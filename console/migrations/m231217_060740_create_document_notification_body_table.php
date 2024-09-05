<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%document_notification_body}}`.
 */
class m231217_060740_create_document_notification_body_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'document_notification_body';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('document_notification_body');
        }

        $this->createTable('{{%document_notification_body}}', [
            'id' => $this->primaryKey(),
            'document_notification_id' => $this->integer()->null(),
            'name_user' => $this->string(255)->null(),
            'name_signature' => $this->string(255)->null(),
            'body' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext'),
            'qr_code_signature' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext'),
            'qr_code_user' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext'),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_document_notification_body_table_document_notification_table', 'document_notification_body', 'document_notification_id', 'document_notification', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%document_notification_body}}');
    }
}
