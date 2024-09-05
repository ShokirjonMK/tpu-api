<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%qr_letter_outgoing}}`.
 */
class m231203_050943_create_qr_letter_outgoing_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'qr_letter_outgoing';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('qr_letter_outgoing');
        }

        $this->createTable('{{%qr_letter_outgoing}}', [
            'id' => $this->primaryKey(),
            'letter_outgoing_id' => $this->integer()->notNull(),
            'letter_id' => $this->integer()->null(),
            'qr_code' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext'),
            'url' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext'),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_qr_letter_outgoing_table_letter_outgoing_table', 'qr_letter_outgoing', 'letter_outgoing_id', 'letter_outgoing', 'id');
        $this->addForeignKey('mk_qr_letter_outgoing_table_letter_table', 'qr_letter_outgoing', 'letter_id', 'letter', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%qr_letter_outgoing}}');
    }
}
