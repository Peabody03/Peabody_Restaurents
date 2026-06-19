<?php

declare(strict_types=1);

namespace common\components;

use Yii;
use yii\caching\CacheInterface;

/**
 * Protects login against brute-force attempts using cache-backed counters.
 */
class LoginRateLimiter
{
    private const CACHE_PREFIX = 'login_attempts_';

    public function __construct(
        private readonly CacheInterface $cache,
        private readonly int $maxAttempts,
        private readonly int $lockoutDuration,
    ) {
    }

    public static function create(): self
    {
        $params = Yii::$app->params;

        return new self(
            Yii::$app->cache,
            (int) ($params['user.loginMaxAttempts'] ?? 5),
            (int) ($params['user.loginLockoutDuration'] ?? 900),
        );
    }

    public function isBlocked(string $username, string $ip): bool
    {
        return $this->getAttempts($username, $ip) >= $this->maxAttempts;
    }

    public function getRemainingLockoutSeconds(string $username, string $ip): int
    {
        $key = $this->buildKey($username, $ip);
        $expiresAt = $this->cache->get($key . '_expires');

        if ($expiresAt === false) {
            return 0;
        }

        return max(0, (int) $expiresAt - time());
    }

    public function recordFailure(string $username, string $ip): void
    {
        $key = $this->buildKey($username, $ip);
        $attempts = (int) $this->cache->get($key) + 1;

        $this->cache->set($key, $attempts, $this->lockoutDuration);
        $this->cache->set($key . '_expires', time() + $this->lockoutDuration, $this->lockoutDuration);
    }

    public function clear(string $username, string $ip): void
    {
        $key = $this->buildKey($username, $ip);
        $this->cache->delete($key);
        $this->cache->delete($key . '_expires');
    }

    private function getAttempts(string $username, string $ip): int
    {
        return (int) $this->cache->get($this->buildKey($username, $ip));
    }

    private function buildKey(string $username, string $ip): string
    {
        return self::CACHE_PREFIX . hash('sha256', strtolower(trim($username)) . '|' . $ip);
    }
}
