<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_access}}`.
 */
class m230413_060240_create_user_access_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_access}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),  //*
            'is_leader' => $this->integer()->notNull(),
            'table_name' => $this->text()->Null(),
            'role_name' => $this->text()->Null(),
            'table_id' => $this->integer()->notNull(),
            'user_access_type_id' => $this->integer()->notNull(),  //*
//            'work_type' => $this->integer()->notNull(),
//            'work_rate_id' => $this->integer()->notNull(), //*
//            'job_title_id' => $this->integer()->notNull(), //*
            'tabel_number' => $this->string(50)->notNull(),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),

        ]);

        $this->addForeignKey('mk_user_access_table_users_table', 'user_access', 'user_id', 'users', 'id');
        $this->addForeignKey('mk_user_access_table_user_access_type_table', 'user_access', 'user_access_type_id', 'user_access_type', 'id');
//        $this->addForeignKey('mk_user_access_table_work_rate_table', 'user_access', 'work_rate_id', 'work_rate', 'id');
//        $this->addForeignKey('mk_user_access_table_job_title_table', 'user_access', 'job_title_id', 'job_title', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_user_access_table_users_table', 'user_access');
        $this->dropForeignKey('mk_user_access_table_user_access_type_table', 'user_access');
//        $this->dropForeignKey('mk_user_access_table_work_rate_table', 'user_access');
//        $this->dropForeignKey('mk_user_access_table_job_title_table', 'user_access');
        $this->dropTable('{{%user_access}}');
    }
}
