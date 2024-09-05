<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%letter}}`.
 */
class m231126_045214_create_letter_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'letter';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('letter');
        }

        $this->createTable('{{%letter}}', [
            'id' => $this->primaryKey(),
            'document_weight_id' => $this->integer()->null(),
            'important_level_id' => $this->integer()->null(),
            'description' => $this->text()->notNull(),
            'file' => $this->string(255)->null(),
            'type' => $this->integer()->defaultValue(0),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(0),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_letter_table_document_weight_table', 'letter', 'document_weight_id', 'document_weight', 'id');
        $this->addForeignKey('mk_letter_table_important_level_table', 'letter', 'important_level_id', 'important_level', 'id');
    }

    /**
     * {@inheritdoc}
     */

    public function safeDown()
    {
        $this->dropTable('{{%letter}}');
    }
}
