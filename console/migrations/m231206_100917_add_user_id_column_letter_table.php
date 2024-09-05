<?php

use yii\db\Migration;

/**
 * Class m231206_100917_add_user_id_column_letter_table
 */
class m231206_100917_add_user_id_column_letter_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('letter' , 'user_id', $this->integer()->null());
        $this->addColumn('letter' , 'is_ok', $this->integer()->defaultValue(0));
        $this->addColumn('letter' , 'is_ok_date', $this->integer()->defaultValue(0));
        $this->addColumn('letter' , 'view_type', $this->integer()->defaultValue(0));
        $this->addColumn('letter' , 'view_date', $this->integer()->defaultValue(0));
        $this->addForeignKey('mk_letter_table_user_table', 'letter', 'user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231206_100917_add_user_id_column_letter_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231206_100917_add_user_id_column_letter_table cannot be reverted.\n";

        return false;
    }
    */
}
