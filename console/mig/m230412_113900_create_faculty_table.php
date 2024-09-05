<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%faculty}}`.
 */
class m230412_113900_create_faculty_table extends Migration
{
    /**
     * {@inheritdoc}
     */

    public function safeUp()
    {
        $this->createTable('{{%faculty}}', [
            'id' => $this->primaryKey(),
//            'name' => $this->string(255)->Null(),
            'user_id' => $this->integer()->notNull(),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);
        $this->addForeignKey('mk_faculty_table_user_table', 'faculty', 'user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_faculty_table_user_table', 'faculty');
        $this->dropTable('{{%faculty}}');
    }
}
