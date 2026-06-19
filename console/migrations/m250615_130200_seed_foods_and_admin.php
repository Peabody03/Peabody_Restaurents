<?php

declare(strict_types=1);

use yii\db\Migration;

class m250615_130200_seed_foods_and_admin extends Migration
{
    public function safeUp(): void
    {
        $now = time();
        $foods = require __DIR__ . '/data/food_seed.php';

        foreach ($foods as $menuType => $items) {
            foreach ($items as $item) {
                [$name, $price, $description] = $item;
                $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
                $this->insert('{{%food}}', [
                    'food_name' => $name,
                    'menu_type' => $menuType,
                    'price' => $price,
                    'image' => 'foods/' . $menuType . '/' . trim($slug, '-') . '.jpg',
                    'description' => $description,
                    'is_available' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $exists = (new \yii\db\Query())
            ->from('{{%user}}')
            ->where(['username' => 'admin'])
            ->exists($this->db);

        if (!$exists) {
            $security = Yii::$app->security;
            $this->insert('{{%user}}', [
                'username' => 'admin',
                'email' => 'admin@peabody.com',
                'phone' => '+255700000000',
                'auth_key' => $security->generateRandomString(),
                'password_hash' => $security->generatePasswordHash('Admin@12345'),
                'status' => 10,
                'role' => 'admin',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            $this->update('{{%user}}', ['role' => 'admin'], ['username' => 'admin']);
        }
    }

    public function safeDown(): void
    {
        $this->delete('{{%food}}');
        $this->update('{{%user}}', ['role' => 'customer'], ['username' => 'admin']);
    }
}
