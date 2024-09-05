<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%commands}}`.
 */
class m231030_121633_create_commands_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'commands';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('commands');
        }

        $this->createTable('{{%commands}}', [
            'id' => $this->primaryKey(),
            'commands_name' => $this->string(255)->null(),
            'faculty_id' => $this->integer()->notNull(),
            'commands_type_id' => $this->integer()->notNull(),
            'commands_date' => $this->date()->notNull(),
            'commands_number' => $this->string(255)->notNull(),
            'commands_file' => $this->string(255)->null(),

            'commands_target' => $this->text()->null(),
            'commands_summary' => $this->text()->null(),

            'description' => $this->text()->null(),
            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ] , $tableOptions);
        $this->addForeignKey('mk_commands_table_faculty_table', 'commands', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('mk_commands_table_commands_type_table', 'commands', 'commands_type_id', 'commands_type', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%commands}}');
    }
}
