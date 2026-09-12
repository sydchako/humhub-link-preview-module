<?php

use yii\db\Migration;

class m260912_120000_create_sytvil_link_preview_cache extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%sytvil_link_preview}}', [
            'id' => $this->primaryKey(),
            'url' => $this->text()->notNull(),
            'url_hash' => $this->string(64)->notNull(),
            'title' => $this->string(300)->null(),
            'description' => $this->text()->null(),
            'image_url' => $this->text()->null(),
            'site_name' => $this->string(150)->null(),
            'content_type' => $this->string(150)->null(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'error_message' => $this->string(500)->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'expires_at' => $this->integer()->notNull(),
        ]);
        $this->createIndex('ux_sytvil_link_preview_hash', '{{%sytvil_link_preview}}', 'url_hash', true);
        $this->createIndex('ix_sytvil_link_preview_expires', '{{%sytvil_link_preview}}', 'expires_at');
    }

    public function safeDown()
    {
        $this->dropTable('{{%sytvil_link_preview}}');
    }
}
