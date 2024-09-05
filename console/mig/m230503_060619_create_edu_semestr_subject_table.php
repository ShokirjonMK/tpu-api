<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%edu_semestr_subject}}`.
 */
class m230503_060619_create_edu_semestr_subject_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%subject_type}}', [
            'id' => $this->primaryKey(),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);

        $this->createTable('{{%edu_semestr_subject}}', [
            'id' => $this->primaryKey(),

            'edu_semestr_id' => $this->integer()->notNull(),
            'subject_id' => $this->integer()->notNull(),
            'subject_type_id' => $this->integer()->Null(),
            'faculty_id' => $this->integer()->Null(),
            'direction_id' => $this->integer()->Null(),

            'credit' => $this->double()->Null(),
            'auditory_time' => $this->double()->Null(),
            'all_ball_yuklama' => $this->integer()->Null(),
            'is_checked' => $this->integer()->Null(),
            'max_ball' => $this->integer()->Null(),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),

            'faculty_id'=>$this->integer()->Null(),
            'direction_id'=>$this->integer()->Null(),

        ]);

        $this->addForeignKey('mk_edu_semestr_subject_table_edu_semestr_table', 'edu_semestr_subject', 'edu_semestr_id', 'edu_semestr', 'id');
        $this->addForeignKey('mk_edu_semestr_subject_table_subject_table', 'edu_semestr_subject', 'subject_id', 'subject', 'id');
        $this->addForeignKey('mk_edu_semestr_subject_table_subject_type_table', 'edu_semestr_subject', 'subject_type_id', 'subject_type', 'id');
        $this->addForeignKey('mk_edu_semestr_subject_table_faculty_table', 'edu_semestr_subject', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('mk_edu_semestr_subject_table_direction_table', 'edu_semestr_subject', 'direction_id', 'direction', 'id');
    }

    /**
     * {@inheritdoc}
     */

    public function safeDown()
    {
        $this->dropForeignKey('mk_edu_semestr_subject_table_edu_semestr_table', 'edu_semestr_subject');
        $this->dropForeignKey('mk_edu_semestr_subject_table_subject_table', 'edu_semestr_subject');
        $this->dropForeignKey('mk_edu_semestr_subject_table_subject_type_table', 'edu_semestr_subject');
        $this->dropForeignKey('mk_edu_semestr_subject_table_faculty_table', 'edu_semestr_subject');
        $this->dropForeignKey('mk_edu_semestr_subject_table_direction_table', 'edu_semestr_subject');
        $this->dropTable('{{%edu_semestr_subject}}');
        $this->dropTable('{{%subject_type}}');
    }
}
