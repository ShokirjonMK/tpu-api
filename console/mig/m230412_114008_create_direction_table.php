<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%direction}}`.
 */
class m230412_114008_create_direction_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%direction}}', [
            'id' => $this->primaryKey(),
            'faculty_id' => $this->integer()->notNull(),
            'kafedra_id' => $this->integer()->Null(),
            'code' => $this->string(255)->notNull(),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);

        $this->addForeignKey('mk_direction_table_faculty_table', 'direction', 'faculty_id', 'faculty', 'id');
//        $this->addForeignKey('mk_direction_table_kafedra_table', 'direction', 'kafedra_id', 'kafedra', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_direction_table_faculty_table', 'direction');
//        $this->dropForeignKey('mk_direction_table_kafedra_table', 'direction');
        $this->dropTable('{{%direction}}');
    }
}
