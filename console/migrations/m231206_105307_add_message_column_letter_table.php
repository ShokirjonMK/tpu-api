<?php

use yii\db\Migration;

/**
 * Class m231206_105307_add_message_column_letter_table
 */
class m231206_105307_add_message_column_letter_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('letter' , 'message' , $this->text()->null());
        $this->addColumn('letter' , 'sent_time' , $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */

    public function safeDown()
    {
        echo "m231206_105307_add_message_column_letter_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231206_105307_add_message_column_letter_table cannot be reverted.\n";

        return false;
    }
    */
}
