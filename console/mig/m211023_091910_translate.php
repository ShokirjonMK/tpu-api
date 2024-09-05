<?php

use yii\db\Migration;

/**
 * Class m211023_091910_translate
 */
class m211023_091910_translate extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('translate', [
        'id' => $this->primaryKey(),
        'model_id'=>$this->integer()->Null(),
        'name'=>$this->string(255)->notNull(),
        'description'=>$this->text()->Null(),
        'table_name'=>$this->string(255)->notNull(),
        'language'=>$this->string(2)->notNull(),


        'order'=>$this->tinyInteger(1)->defaultValue(1),
        'status' => $this->tinyInteger(1)->defaultValue(1),
        'created_at'=>$this->integer()->Null(),
        'updated_at'=>$this->integer()->Null(),
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
        $this->dropTable('translate');
    }

}
