<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_access_type}}`.
 */
class m230413_053002_create_user_access_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_access_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->text()->notNull(),
            'table_name' => $this->text()->notNull(),
            'url' => $this->text()->notNull(),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),

        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_access_type}}');
    }
}
