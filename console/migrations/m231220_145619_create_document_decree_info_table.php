<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%document_decree_info}}`.
 */
class m231220_145619_create_document_decree_info_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'document_decree_info';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('document_decree_info');
        }

        $this->createTable('{{%document_decree_info}}', [
            'id' => $this->primaryKey(),
            'document_decree_id' => $this->integer()->null(),
            'user_id' => $this->integer()->null(),
            'view_time' => $this->integer()->null(),
            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_document_decree_info_table_user_table', 'document_decree_info', 'user_id', 'users', 'id');
        $this->addForeignKey('mk_document_decree_info_table_document_decree_table', 'document_decree_info', 'document_decree_id', 'document_decree', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%document_decree_info}}');
    }
}
