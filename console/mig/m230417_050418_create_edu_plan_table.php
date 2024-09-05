<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%edu_plan}}`.
 */
class m230417_050418_create_edu_plan_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%edu_plan}}', [
            'id' => $this->primaryKey(),
            'spring_start' => $this->string()->Null(),
            'spring_end' => $this->string()->Null(),
            'fall_start' => $this->string()->Null(),
            'fall_end' => $this->string()->Null(),
            'course' => $this->integer()->notNull(),
            'semestr' => $this->integer()->notNull(),
            'edu_year_id' => $this->integer()->notNull(),
            'faculty_id' => $this->integer()->notNull(),
            'direction_id' => $this->integer()->notNull(),
            'edu_type_id' => $this->integer()->notNull(),
            'edu_form_id' => $this->integer()->Null()->comment('ta-lim shakli'),
            'type' => $this->integer()->notNull(),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);

        $this->addForeignKey('mk_edu_plan_table_edu_year_table', 'edu_plan', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('mk_edu_plan_table_faculty_table', 'edu_plan', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('mk_edu_plan_table_direction_table', 'edu_plan', 'direction_id', 'direction', 'id');
        $this->addForeignKey('mk_edu_plan_table_edu_type_table', 'edu_plan', 'edu_type_id', 'edu_type', 'id');
        $this->addForeignKey('mk_edu_plan_table_edu_form_table', 'edu_plan', 'edu_form_id', 'edu_form', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_edu_plan_table_edu_year_table', 'edu_plan');
        $this->dropForeignKey('mk_edu_plan_table_faculty_table', 'edu_plan');
        $this->dropForeignKey('mk_edu_plan_table_direction_table', 'edu_plan');
        $this->dropForeignKey('mk_edu_plan_table_edu_type_table', 'edu_plan');
        $this->dropForeignKey('mk_edu_plan_table_edu_form_table', 'edu_plan');
        $this->dropTable('{{%edu_plan}}');
    }
}
