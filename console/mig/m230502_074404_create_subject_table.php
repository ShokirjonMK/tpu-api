<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subject}}`.
 */
class m230502_074404_create_subject_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%subject}}', [
            'id' => $this->primaryKey(),
            'kafedra_id' => $this->integer()->notNull(),
            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->notNull(),
            'updated_at'=>$this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'semestr_id' => $this->integer()->Null()->comment('fanga smester'),
            'parent_id' => $this->integer()->Null()->comment('fanga parent'),
        ]);

        $this->addForeignKey('mk_subject_table_kafedra_table', 'subject', 'kafedra_id', 'kafedra', 'id');
        $this->addForeignKey('mk_subject_table_semestr_table', 'subject', 'semestr_id', 'semestr', 'id');
//        $this->addForeignKey('mk_subject_table_parent_table', 'subject', 'parent_id', 'subject', 'id');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_subject_table_kafedra_table', 'subject');
        $this->dropForeignKey('mk_subject_table_semestr_table', 'subject');
//        $this->dropForeignKey('mk_subject_table_parent_table', 'subject');
        $this->dropTable('{{%subject}}');
    }
}
