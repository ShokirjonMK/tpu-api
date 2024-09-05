<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%document_decree}}`.
 */
class m231220_145232_create_document_decree_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'document_decree';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('document_decree');
        }

        $this->createTable('{{%document_decree}}', [
            'id' => $this->primaryKey(),
            'ids' => $this->string(255)->null()->unique(),
            'command_type' => $this->tinyInteger(1)->defaultValue(0),
            'description' => $this->text()->null(),
            'file' => $this->string(255)->null(),
            'user_id' => $this->integer()->notNull(),
            'sent_time' => $this->integer()->defaultValue(0),
            'type' => $this->integer()->defaultValue(0),
            'is_ok' => $this->integer()->defaultValue(0),
            'is_ok_time' => $this->integer()->defaultValue(0),
            'hr_sent_time' => $this->integer()->defaultValue(0),
            'signature_user_id' => $this->integer()->null(),
            'returned_user_id' => $this->integer()->null(),
            'message' => $this->text()->null(),
            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(0),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_document_decree_table_user_table', 'document_decree', 'user_id', 'users', 'id');
        $this->addForeignKey('mk_document_decree_table_signature_user_table', 'document_decree', 'signature_user_id', 'users', 'id');
        $this->addForeignKey('mk_document_decree_table_returned_user_table', 'document_decree', 'returned_user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%document_decree}}');
    }
}
