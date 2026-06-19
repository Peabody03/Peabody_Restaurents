<?php

declare(strict_types=1);

use yii\db\Migration;

class m250615_130000_add_role_to_user_table extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('{{%user}}', 'role', $this->string(20)->notNull()->defaultValue('customer')->after('status'));
        $this->createIndex('idx-user-role', '{{%user}}', 'role');
    }

    public function safeDown(): void
    {
        $this->dropIndex('idx-user-role', '{{%user}}');
        $this->dropColumn('{{%user}}', 'role');
    }
}
