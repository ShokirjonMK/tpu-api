<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%letter_body}}`.
 */
class m231203_122958_create_letter_outgoing_body_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'letter_outgoing_body';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('letter_outgoing_body');
        }

        $this->createTable('{{%letter_outgoing_body}}', [
            'id' => $this->primaryKey(),
            'letter_outgoing_id' => $this->integer()->notNull(),
            'body' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext'),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_letter_body_table_letter_outgoing_table', 'letter_outgoing_body', 'letter_outgoing_id', 'letter_outgoing', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%letter_body}}');
    }
}
