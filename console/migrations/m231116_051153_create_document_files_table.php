<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%document_files}}`.
 */
class m231116_051153_create_document_files_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'document_files';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('document_files');
        }

        $this->createTable('{{%document_files}}', [
            'id' => $this->primaryKey(),
            'document_id' => $this->integer()->notNull(),
            'title' => $this->string(255)->null(),
            'description' => $this->text()->null(),
            'file' => $this->string()->null(),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ],$tableOptions);
        $this->addForeignKey('mk_document_files_table_document_table', 'document_files', 'document_id', 'document', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%document_files}}');
    }
}
