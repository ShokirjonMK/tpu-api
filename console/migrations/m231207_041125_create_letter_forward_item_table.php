<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%letter_forward_item}}`.
 */
class m231207_041125_create_letter_forward_item_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'letter_forward_item';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('letter_forward_item');
        }

        $this->createTable('{{%letter_forward_item}}', [
            'id' => $this->primaryKey(),
            'letter_id' => $this->integer()->notNull(),
            'letter_forward_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'description' => $this->text()->null(),
            'title' => $this->string(255)->null(),
            'view_type' => $this->tinyInteger(1)->defaultValue(0),
            'view_date' => $this->integer()->defaultValue(0),
            'sent_date' => $this->integer()->defaultValue(0),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(0),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_letter_forward_item_table_letter_table', 'letter_forward_item', 'letter_id', 'letter', 'id');
        $this->addForeignKey('mk_letter_forward_item_table_letter_forward_table', 'letter_forward_item', 'letter_forward_id', 'letter_forward', 'id');
        $this->addForeignKey('mk_letter_forward_item_table_user_table', 'letter_forward_item', 'user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%letter_forward_item}}');
    }
}
