<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%letter_outgoing}}`.
 */
class m231128_045619_create_letter_outgoing_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'letter_outgoing';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('letter_outgoing');
        }

        $this->createTable('{{%letter_outgoing}}', [
            'id' => $this->primaryKey(),
            'letter_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'description' => $this->text()->null(),
            'file' => $this->string(255)->null(),

            'output_number' => $this->string(255)->unique()->null(),
            'access_number' => $this->string(255)->unique()->null(),

            'view_type' => $this->integer()->defaultValue(0),
            'view_date' => $this->integer()->defaultValue(0),
            'is_ok' => $this->tinyInteger(1)->defaultValue(0),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(0),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_letter_outgoing_table_letter_table', 'letter_outgoing', 'letter_id', 'letter', 'id');
        $this->addForeignKey('mk_letter_outgoing_table_users_table', 'letter_outgoing', 'user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%letter_outgoing}}');
    }
}
