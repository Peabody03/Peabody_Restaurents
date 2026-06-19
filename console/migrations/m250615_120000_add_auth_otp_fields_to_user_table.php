<?php

declare(strict_types=1);

use yii\db\Migration;

class m250615_120000_add_auth_otp_fields_to_user_table extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('{{%user}}', 'phone', $this->string(20)->notNull()->defaultValue('')->after('email'));
        $this->addColumn('{{%user}}', 'verification_otp_hash', $this->string()->null()->after('verification_token'));
        $this->addColumn('{{%user}}', 'verification_otp_expires_at', $this->integer()->null()->after('verification_otp_hash'));
        $this->addColumn('{{%user}}', 'password_reset_otp_hash', $this->string()->null()->after('password_reset_token'));
        $this->addColumn('{{%user}}', 'password_reset_otp_expires_at', $this->integer()->null()->after('password_reset_otp_hash'));

        $this->createIndex('idx-user-phone', '{{%user}}', 'phone');
    }

    public function safeDown(): void
    {
        $this->dropIndex('idx-user-phone', '{{%user}}');
        $this->dropColumn('{{%user}}', 'password_reset_otp_expires_at');
        $this->dropColumn('{{%user}}', 'password_reset_otp_hash');
        $this->dropColumn('{{%user}}', 'verification_otp_expires_at');
        $this->dropColumn('{{%user}}', 'verification_otp_hash');
        $this->dropColumn('{{%user}}', 'phone');
    }
}
