<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m250123_191224_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Importing params
        $params = require __DIR__ . '/../config/params.php';

        // Table Settings
        $this->createTable('{{%user}}', [
            // user main params
            'id' => $this->primaryKey(),
            'status' => $this->smallInteger()->notNull()->defaultValue(0), // 0 = inactive, 1 = active, 9 = deleted
            'email' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'access_token' => $this->string(512),
            'token_expiration' => $this->integer()->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'verification_token' => $this->string()->defaultValue(null),
            'is_admin' => $this->integer()->notNull(),  // 0 = guest, 1 = admin
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            // user extra params
            'gps_lat' => $this->decimal(10, 4)->notNull()->defaultValue(-23.5568),
            'gps_lng' => $this->decimal(10, 4)->notNull()->defaultValue(-46.6538),
            'latitude' => $this->decimal(10, 4)->defaultValue(-23.5568),
            'longitude' => $this->decimal(10, 4)->defaultValue(-46.6538),
            'use_gps' => $this->smallInteger()->defaultValue(0), // 0 = false, 1 = true
            'mmc' => $this->integer(),
        ]);

        // Create Admin User
        $this->insert('{{%user}}', [
            'id' => 1,
            'status' => 10,
            'email' => $_ENV['ADMIN_EMAIL'],
            'auth_key' => Yii::$app->security->generateRandomString(),
            'access_token' => Yii::$app->security->generateRandomString(),
            'token_expiration' => time() + $params['user.token_expiration'],
            'password_hash' => Yii::$app->security->generatePasswordHash($_ENV['ADMIN_PASSWORD']),
            'password_reset_token' => null,
            'verification_token' => null,
            'is_admin' => 1,
            'created_at' => time(),
            'updated_at' => time(),
            // default params:
            'gps_lat' => -15.7723,
            'gps_lng' => -47.8700,
            'latitude' => -15.7723,
            'longitude' => -47.8700,
            'use_gps' => 0,
            'mmc' => null,
        ]);

        // Create Common User
        $this->insert('{{%user}}', [
            'id' => 2,
            'status' => 10,
            'email' => $_ENV['COMMON_EMAIL'],
            'auth_key' => Yii::$app->security->generateRandomString(),
            'access_token' => Yii::$app->security->generateRandomString(),
            'token_expiration' => time() + $params['user.token_expiration'],
            'password_hash' => Yii::$app->security->generatePasswordHash($_ENV['COMMON_PASSWORD']),
            'password_reset_token' => null,
            'verification_token' => null,
            'is_admin' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            // default params:
            'gps_lat' => -15.7723,
            'gps_lng' => -47.8700,
            'latitude' => -15.7723,
            'longitude' => -47.8700,
            'use_gps' => 0,
            'mmc' => null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop table
        $this->dropTable('{{%user}}');
    }
}
