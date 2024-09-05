<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%room}}`.
 */
class m230411_093643_create_room_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%room}}', [
            'id' => $this->primaryKey(),
            'building_id' => $this->integer()->notNull(),
            // 'name' => $this->string(255)->notNull(),
            'capacity' => $this->integer()->notNull(),
            
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);

        $this->addForeignKey('mk_room_table_building_table', 'room', 'building_id', 'building', 'id');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_room_table_building_table', 'room');
        $this->dropTable('{{%room}}');
    }
}
