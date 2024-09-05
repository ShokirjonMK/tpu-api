<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%edu_semestr}}`.
 */
class m230417_050513_create_edu_semestr_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $tableName = Yii::$app->db->tablePrefix . 'edu_semestr';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('edu_semestr');
        }

        $this->createTable('{{%edu_semestr}}', [
            'id' => $this->primaryKey(),
            'credit' => $this->double()->null()->defaultValue(0),
            'edu_plan_id' => $this->integer()->notNull(),
            'course_id' => $this->integer()->notNull(),
            'semestr_id' => $this->integer()->notNull(),
            'edu_year_id' => $this->integer()->notNull(),
            'start_date' => $this->dateTime()->null(),
            'end_date' => $this->dateTime()->null(),
            'is_checked' => $this->integer()->null(),
            'type' => $this->integer()->Null()->defaultValue(1)->comment('type 1 - random teshiradi 2 - teacher o`zi tekshiradi id'),

            'optional_subject_count' => $this->integer()->null()->defaultValue(0)->comment('tanlov fanlari soni edu_semestr_subject ga qarab turini sanash'),  // tanlov fanlari soni edu_semestr_subject ga qarab turini sanash
            'required_subject_count' => $this->integer()->null()->defaultValue(0)->comment('majburiy fanlari soni edu_semestr subjectga qarab turini sanash'),  // majburiy fanlari soni edu_semestr subjectga qarab turini sanash

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);

        $this->addForeignKey('mk_edu_semestr_table_edu_plan_table', 'edu_semestr', 'edu_plan_id', 'edu_plan', 'id');
        $this->addForeignKey('mk_edu_semestr_table_course_table', 'edu_semestr', 'course_id', 'course', 'id');
        $this->addForeignKey('mk_edu_semestr_table_semestr_table', 'edu_semestr', 'semestr_id', 'semestr', 'id');
        $this->addForeignKey('mk_edu_semestr_table_edu_year_table', 'edu_semestr', 'edu_year_id', 'edu_year', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_edu_semestr_table_edu_plan_table', 'edu_semestr');
        $this->dropForeignKey('mk_edu_semestr_table_course_table', 'edu_semestr');
        $this->dropForeignKey('mk_edu_semestr_table_semestr_table', 'edu_semestr');
        $this->dropForeignKey('mk_edu_semestr_table_edu_year_table', 'edu_semestr');
        $this->dropTable('{{%edu_semestr}}');
    }
}
