<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%profile}}`.
 */
class m230501_060156_create_profile_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%profile}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'checked' => $this->double()->defaultValue(0),
            'checked_full' => $this->double()->defaultValue(0),
            'image' => $this->string(255)->null(),

            'first_name' => $this->string(255)->null(),
            'last_name' => $this->string(255)->null(),
            'middle_name' => $this->string(255)->null(),
            'passport_serial' => $this->string()->null(),
            'passport_number' => $this->string()->null(),
            'passport_pinip' => $this->string(14)->null(),
            'passport_issued_date' => $this->date()->null(),
            'passport_given_date' => $this->date()->null(),
            'passport_given_by' => $this->string(255)->null(),
            'birthday' => $this->date()->null(),
            'gender' => $this->tinyInteger(1)->null(),
            'phone' => $this->string(50)->null(),
            'phone_secondary' => $this->string(50)->null(),
            'passport_file' => $this->string(255)->null(),

            'countries_id' => $this->integer()->null(),
            'region_id' => $this->integer()->null(),
            'area_id' => $this->integer()->null(),

            'permanent_countries_id' => $this->integer()->null(),
            'permanent_region_id' => $this->integer()->null(),
            'permanent_area_id' => $this->integer()->null(),


            'permanent_address' => $this->string()->null(),
            'address' => $this->text()->null(),
            'description' => $this->text()->null(),

            'is_foreign' => $this->tinyInteger(1)->null(),

            'citizenship_id' => $this->integer()->null(),
            'telegram_chat_id' => $this->integer()->null(),
            'diploma_type_id' => $this->integer()->null(),
            'degree_id' => $this->integer()->null(),
            'academic_degree_id' => $this->integer()->null(),
            'degree_info_id' => $this->integer()->null(),
            'partiya_id' => $this->integer()->null(),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->notNull(),
            'updated_at'=>$this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),

        ]);

        $this->insert('{{%profile}}', [
            'user_id' => 4,
            'first_name'=>'blackmoon',
            'last_name'=>'blackmoon_uz',
            'middle_name'=>'blackmoonuz',
            'created_at'=>time(),
            'updated_at'=>time(),
        ]);

        $this->insert('{{%profile}}', [
            'user_id' => 1,
            'first_name'=>'ShokirjonMK',
            'last_name'=>'ShokirjonMK_uz',
            'middle_name'=>'ShokirjonMKuz',
            'created_at'=>time(),
            'updated_at'=>time(),
        ]);

        $this->insert('{{%profile}}', [
            'user_id' => 5,
            'first_name'=>'Iqboljon',
            'last_name'=>'Uraimov',
            'middle_name'=>'Anvarjon o\'g\'li',
            'created_at'=>time(),
            'updated_at'=>time(),
        ]);

        $this->addForeignKey('mk_profile_table_users_table', 'profile', 'user_id', 'users', 'id');
        $this->addForeignKey('mk_profile_table_countries_table', 'profile', 'countries_id', 'countries', 'id');
        $this->addForeignKey('mk_profile_table_region_table', 'profile', 'region_id', 'region', 'id');
//        $this->addForeignKey('mk_profile_table_area_table', 'profile', 'area_id', 'area', 'id');

        $this->addForeignKey('mk_profile_table_permanent_countries_table', 'profile', 'permanent_countries_id', 'countries', 'id');
        $this->addForeignKey('mk_profile_table_permanent_region_table', 'profile', 'permanent_region_id', 'region', 'id');
//        $this->addForeignKey('mk_profile_table_permanent_area_table', 'profile', 'permanent_area_id', 'area', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_profile_table_user_table', 'profile');
        $this->dropForeignKey('mk_profile_table_country_table', 'profile');
        $this->dropForeignKey('mk_profile_table_region_table', 'profile');
//        $this->dropForeignKey('mk_profile_table_area_table', 'profile');

        $this->dropForeignKey('mk_profile_table_permanent_country_table', 'profile');
        $this->dropForeignKey('mk_profile_table_permanent_region_table', 'profile');
//        $this->dropForeignKey('mk_profile_table_permanent_area_table', 'profile');
        $this->dropTable('{{%profile}}');
    }
}
