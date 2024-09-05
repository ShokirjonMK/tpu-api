<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%semestr}}`.
 */
class m230417_050451_create_semestr_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%semestr}}', [
            'id' => $this->primaryKey(),
//            'name'=> $this->string(255)->null(),
            'course_id' => $this->integer()->notNull(),
            'type' => $this->integer()->null(),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);
        $this->addForeignKey('mk_semestr_table_course_table', 'semestr', 'course_id', 'course', 'id');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_semestr_table_course_table', 'semestr');
        $this->dropTable('{{%semestr}}');
    }
}
