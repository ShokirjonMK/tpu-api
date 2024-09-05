<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%edu_year}}`.
 */
class m230417_043722_create_edu_year_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%edu_year}}', [
            'id' => $this->primaryKey(),
//            'name'=>$this->string(255)->null(), 
            'year' => $this->integer()->notNull(),
            'type' => $this->integer()->notNull(),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%edu_year}}');
    }
}
