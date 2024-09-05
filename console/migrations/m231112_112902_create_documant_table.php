<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%documant}}`.
 */
class m231112_112902_create_documant_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'document';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('document');
        }

        $this->createTable('{{%document}}', [
            'id' => $this->primaryKey(),

            'document_type_id' => $this->integer()->notNull(),
            'document_weight_id' => $this->integer()->null(),

            'title' => $this->string(255)->null(),
            'description' => $this->text()->null(),
            'file' => $this->string(255)->null(),
            'start_date' => $this->integer()->null(),
            'end_date' => $this->integer()->null(),

            'doc_number' => $this->string(255)->null(),
            'access_doc_number' => $this->string(255)->null(),
            'registr_number' => $this->string(255)->null(),

            'user_id' => $this->integer()->null(),
            'qr_type' => $this->tinyInteger()->defaultValue(0),
            'coming_type' => $this->tinyInteger()->defaultValue(0),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_document_table_document_type_table', 'document', 'document_type_id', 'document_type', 'id');
        $this->addForeignKey('mk_document_table_document_weight_table', 'document', 'document_weight_id', 'document_weight', 'id');
        $this->addForeignKey('mk_document_table_user_table', 'document', 'user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%documant}}');
    }
}
