<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%letter_forward}}`.
 */
class m231126_070943_create_letter_forward_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'letter_forward';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('letter_forward');
        }

        $this->createTable('{{%letter_forward}}', [
            'id' => $this->primaryKey(),
            'letter_id' => $this->integer()->notNull(),
            'description' => $this->text()->null(),
            'start_date' => $this->integer()->notNull(),
            'end_date' => $this->integer()->null(),
            'sent_date' => $this->integer()->null(),
            'user_id' => $this->integer()->notNull(),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(0),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_letter_forward_table_letter_table', 'letter_forward', 'letter_id', 'letter', 'id');
        $this->addForeignKey('mk_letter_forward_table_users_table', 'letter_forward', 'user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */

    public function safeDown()
    {
        $this->dropTable('{{%letter_forward}}');
    }
}
