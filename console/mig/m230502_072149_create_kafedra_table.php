<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%kafedra}}`.
 */
class m230502_072149_create_kafedra_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%kafedra}}', [
            'id' => $this->primaryKey(),
            'faculty_id' => $this->integer()->notNull(),
            'direction_id' => $this->integer()->notNull(),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->notNull(),
            'updated_at'=>$this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);

        $this->addForeignKey('mk_kafedra_table_faculty_table', 'kafedra', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('mk_kafedra_table_direction_table', 'kafedra', 'direction_id', 'direction', 'id');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_kafedra_table_faculty_table', 'kafedra');
        $this->dropForeignKey('mk_kafedra_table_direction_table', 'kafedra');
        $this->dropTable('{{%kafedra}}');
    }
}
