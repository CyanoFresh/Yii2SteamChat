<?php

use yii\db\Schema;
use yii\db\Migration;

class m151228_152421_create_user_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'steamid' => $this->string()->notNull()->unique(),
            'username' => $this->string()->notNull(),
            'profile_url' => $this->string()->notNull(),
            'avatar' => $this->string()->notNull(),
            'avatar_md' => $this->string()->notNull(),
            'avatar_lg' => $this->string()->notNull(),
            'auth_key' => $this->string(32)->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
