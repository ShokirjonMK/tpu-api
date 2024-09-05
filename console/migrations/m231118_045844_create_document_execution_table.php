<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%document_execution}}`.
 */
class m231118_045844_create_document_execution_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'document_execution';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('document_execution');
        }

        $this->createTable('{{%document_execution}}', [
            'id' => $this->primaryKey(),
            'document_id' => $this->integer()->notNull(),

            'title' => $this->string(255)->null(),
            'description' => $this->text()->null(),
            'start_date' => $this->integer()->null(),
            'end_date' => $this->integer()->null(),
            'file' => $this->string(255)->null(),

            'document_type_id' => $this->integer()->null(),
            'document_weight_id' => $this->integer()->null(),
            'doc_number' => $this->string(255)->null(),
            'access_doc_number' => $this->string(255)->null(),
            'registr_number' => $this->string(255)->null(),

            'user_id' => $this->integer()->null(),
            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_document_execution_table_document_table', 'document_execution', 'document_id', 'document', 'id');
        $this->addForeignKey('mk_document_execution_table_user_table', 'document_execution', 'user_id', 'users', 'id');
        $this->addForeignKey('mk_document_execution_table_document_type_table', 'document_execution', 'document_type_id', 'document_type', 'id');
        $this->addForeignKey('mk_document_execution_table_document_weight_table', 'document_execution', 'document_weight_id', 'document_weight', 'id');
    }

    /**
     * {@inheritdoc}
     */

    public function safeDown()
    {
        $this->dropTable('{{%document_execution}}');
    }
}
