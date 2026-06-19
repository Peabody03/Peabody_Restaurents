<?php

declare(strict_types=1);

use yii\db\Migration;

class m250615_130100_create_restaurant_tables extends Migration
{
    public function safeUp(): void
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%food}}', [
            'id' => $this->primaryKey(),
            'food_name' => $this->string(255)->notNull(),
            'menu_type' => $this->string(20)->notNull(),
            'price' => $this->decimal(12, 2)->notNull()->defaultValue(0),
            'image' => $this->string(500)->null(),
            'description' => $this->text()->null(),
            'is_available' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->createIndex('idx-food-menu_type', '{{%food}}', 'menu_type');
        $this->createIndex('idx-food-available', '{{%food}}', 'is_available');

        $this->createTable('{{%order}}', [
            'id' => $this->primaryKey(),
            'order_number' => $this->string(32)->notNull()->unique(),
            'user_id' => $this->integer()->notNull(),
            'status' => $this->string(30)->notNull()->defaultValue('pending'),
            'payment_method' => $this->string(30)->null(),
            'payment_status' => $this->string(30)->notNull()->defaultValue('unpaid'),
            'delivery_type' => $this->string(30)->notNull()->defaultValue('pickup'),
            'subtotal' => $this->decimal(12, 2)->notNull()->defaultValue(0),
            'tax' => $this->decimal(12, 2)->notNull()->defaultValue(0),
            'discount' => $this->decimal(12, 2)->notNull()->defaultValue(0),
            'total' => $this->decimal(12, 2)->notNull()->defaultValue(0),
            'notes' => $this->text()->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addForeignKey('fk-order-user', '{{%order}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->createIndex('idx-order-status', '{{%order}}', 'status');
        $this->createIndex('idx-order-created', '{{%order}}', 'created_at');

        $this->createTable('{{%order_item}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'food_id' => $this->integer()->notNull(),
            'food_name' => $this->string(255)->notNull(),
            'quantity' => $this->integer()->notNull()->defaultValue(1),
            'unit_price' => $this->decimal(12, 2)->notNull(),
            'total_price' => $this->decimal(12, 2)->notNull(),
        ], $tableOptions);
        $this->addForeignKey('fk-order_item-order', '{{%order_item}}', 'order_id', '{{%order}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-order_item-food', '{{%order_item}}', 'food_id', '{{%food}}', 'id', 'RESTRICT', 'CASCADE');

        $this->createTable('{{%cart_item}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'food_id' => $this->integer()->notNull(),
            'quantity' => $this->integer()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addForeignKey('fk-cart_item-user', '{{%cart_item}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-cart_item-food', '{{%cart_item}}', 'food_id', '{{%food}}', 'id', 'CASCADE', 'CASCADE');
        $this->createIndex('idx-cart_item-user_food', '{{%cart_item}}', ['user_id', 'food_id'], true);

        $this->createTable('{{%reservation}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->null(),
            'guest_name' => $this->string(255)->notNull(),
            'guest_email' => $this->string(255)->notNull(),
            'guest_phone' => $this->string(20)->notNull(),
            'guests' => $this->integer()->notNull()->defaultValue(2),
            'reservation_date' => $this->date()->notNull(),
            'reservation_time' => $this->time()->notNull(),
            'status' => $this->string(30)->notNull()->defaultValue('pending'),
            'notes' => $this->text()->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addForeignKey('fk-reservation-user', '{{%reservation}}', 'user_id', '{{%user}}', 'id', 'SET NULL', 'CASCADE');

        $this->createTable('{{%uploaded_image}}', [
            'id' => $this->primaryKey(),
            'filename' => $this->string(255)->notNull(),
            'original_name' => $this->string(255)->notNull(),
            'path' => $this->string(500)->notNull(),
            'mime_type' => $this->string(100)->notNull(),
            'size' => $this->integer()->notNull(),
            'width' => $this->integer()->null(),
            'height' => $this->integer()->null(),
            'uploaded_by' => $this->integer()->null(),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addForeignKey('fk-uploaded_image-user', '{{%uploaded_image}}', 'uploaded_by', '{{%user}}', 'id', 'SET NULL', 'CASCADE');
        $this->createIndex('idx-uploaded_image-created', '{{%uploaded_image}}', 'created_at');
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%uploaded_image}}');
        $this->dropTable('{{%reservation}}');
        $this->dropTable('{{%cart_item}}');
        $this->dropTable('{{%order_item}}');
        $this->dropTable('{{%order}}');
        $this->dropTable('{{%food}}');
    }
}
