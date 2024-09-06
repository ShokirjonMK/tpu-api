<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $tableName = Yii::$app->db->tablePrefix . 'users';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('users');
        }

        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'verification_token' => $this->string()->defaultValue(null),
            'access_token' => $this->string(100)->defaultValue(null),
            'access_token_time' => $this->integer()->null(),
            'email' => $this->string()->null()->unique(),
            'template' => $this->string(255)->Null(),
            'layout' => $this->string(255)->Null(),
            'view' => $this->string(255)->Null(),
            'meta' => $this->json(),
            'status' => $this->smallInteger()->null(),
            'status_n' => $this->smallInteger()->notNull()->defaultValue(10),
            'deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'cacheable' => $this->tinyInteger()->notNull()->defaultValue(0),
            'searchable' => $this->tinyInteger()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_changed' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);


        // $tableName = Yii::$app->db->tablePrefix . 'profile';
        // if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
        //     $this->dropTable('profile');
        // }

        //        $this->createTable('{{%profile}}', [
        //
        //            'id' => $this->primaryKey(),
        //            'user_id' => $this->integer()->null(),
        //            'checked' => $this->double()->defaultValue(0),
        //            'checked_full' => $this->double()->defaultValue(0),
        //            'image' => $this->string(255)->null(),
        //
        //            'first_name' => $this->string(255)->null(),
        //            'last_name' => $this->string(255)->null(),
        //            'middle_name' => $this->string(255)->null(),
        //            'passport_serial' => $this->string()->null(),
        //            'passport_number' => $this->string()->null(),
        //            'passport_pinip' => $this->string(14)->null(),
        //            'passport_issued_date' => $this->date()->null(),
        //            'passport_given_date' => $this->date()->null(),
        //            'passport_given_by' => $this->string(255)->null(),
        //            'birthday' => $this->date()->null(),
        //            'gender' => $this->tinyInteger(1)->null(),
        //            'phone' => $this->string(50)->null(),
        //            'phone_secondary' => $this->string(50)->null(),
        //            'passport_file' => $this->string(255)->null(),
        //
        //            'country_id' => $this->integer()->null(),
        //            'region_id' => $this->integer()->null(),
        //            'area_id' => $this->integer()->null(),
        //
        //            'permanent_country_id' => $this->integer()->null(),
        //            'permanent_region_id' => $this->integer()->null(),
        //            'permanent_area_id' => $this->integer()->null(),
        //
        //            'permanent_address' => $this->string()->null(),
        //            'address' => $this->text()->null(),
        //            'description' => $this->text()->null(),
        //
        //            'is_foreign' => $this->tinyInteger(1)->null(),
        //
        //            'citizenship_id' => $this->integer()->null(),
        //            'telegram_chat_id' => $this->integer()->null(),
        //            'diploma_type_id' => $this->integer()->null(),
        //            'degree_id' => $this->integer()->null(),
        //            'academic_degree_id' => $this->integer()->null(),
        //            'degree_info_id' => $this->integer()->null(),
        //            'partiya_id' => $this->integer()->null(),
        //
        //
        //
        //        ], $tableOptions);



        //        $this->createTable('{{%employee}}', [
        //            'id' => $this->primaryKey(),
        //            'user_id' => $this->integer(),
        //            'department_id' => $this->integer()->null(),
        //            'job_id' => $this->integer()->null(),
        //
        //            'inps' => $this->string()->null(),
        //            'scientific_work' => $this->text()->null(),
        //            'languages' => $this->string()->null(),
        //            'lang_certs' => $this->string()->null(),
        //            'rate' => $this->decimal(10, 2)->null(),
        //            'rank_id' => $this->integer()->null(),
        //            'science_degree_id' => $this->integer()->null(),
        //            'scientific_title_id' => $this->integer()->null(),
        //            'special_title_id' => $this->integer()->null(),
        //            'reception_time' => $this->string()->null(),
        //            'out_staff' => $this->tinyInteger(1)->null(),
        //            'basic_job' => $this->tinyInteger(1)->null(),
        //
        //            'is_convicted' => $this->tinyInteger(1)->null(),
        //            'party_membership' => $this->tinyInteger(1)->null(),
        //            'awords' => $this->string()->null(),
        //            'depuities' => $this->string()->null(),
        //            'military_rank' => $this->string()->null(),
        //            'disability_group' => $this->tinyInteger(1)->null(),
        //            'family_status' => $this->tinyInteger(1)->null(),
        //            'children' => $this->string()->null(),
        //            'other_info' => $this->text()->null(),
        //
        //        ], $tableOptions);

        // $this->createTable('{{%student}}', [
        //     'id' => $this->primaryKey(),
        //     'user_id' => $this->integer(),
        //     'department_id' => $this->integer()->null(),
        //     'education_direction_id' => $this->integer()->null(),
        //     'basis_of_learning' => $this->integer()->null(),
        //     'education_type' => $this->tinyInteger()->null(),

        //     'diploma_number' => $this->string()->null(),
        //     'diploma_date' => $this->timestamp()->null(),

        //     'type_of_residence' => $this->tinyInteger(1)->null(),
        //     'landlord_info' => $this->text()->null(),
        //     'student_live_with' => $this->text()->null(),
        //     'other_info' => $this->text()->null(),

        // ], $tableOptions);


        // inserting data

        //$this->insert('{{%profile}}', [
        //    'user_id' => 4,
        //    'first_name'=>'blackmoon',
        //    'last_name'=>'blackmoon_uz',
        //    'middle_name'=>'blackmoonuz',
        //]);
        //
        //$this->insert('{{%profile}}', [
        //    'user_id' => 1,
        //    'first_name'=>'ShokirjonMK',
        //    'last_name'=>'ShokirjonMK_uz',
        //    'middle_name'=>'ShokirjonMKuz',
        //]);

        $this->insert('{{%users}}', [
            'username' => 'ShokirjonMK',
            'auth_key' => \Yii::$app->security->generateRandomString(20),
            'password_hash' => \Yii::$app->security->generatePasswordHash("12300123"),
            'password_reset_token' => null,
            'access_token' => \Yii::$app->security->generateRandomString(),
            'access_token_time' => time(),
            'email' => 'mkshokirjon@gmail.com',
            'template' => '',
            'layout' => '',
            'view' => '',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert('{{%users}}', [
            'username' => 'blackmoon',
            'auth_key' => \Yii::$app->security->generateRandomString(20),
            'password_hash' => \Yii::$app->security->generatePasswordHash("blackmoonuz"),
            'password_reset_token' => null,
            'access_token' => \Yii::$app->security->generateRandomString(),
            'access_token_time' => time(),
            'email' => 'blackmoonuz@mail.ru',
            'template' => '',
            'layout' => '',
            'view' => '',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);


        $this->insert('{{%users}}', [
            'username' => '10ikbol16',
            'auth_key' => \Yii::$app->security->generateRandomString(20),
            'password_hash' => \Yii::$app->security->generatePasswordHash("ikboljon2oo1"),
            'password_reset_token' => null,
            'access_token' => \Yii::$app->security->generateRandomString(),
            'access_token_time' => time(),
            'email' => 'ikboljon@gmail.uz',
            'template' => '',
            'layout' => '',
            'view' => '',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert('{{%users}}', [
            'username' => 'ahror',
            'auth_key' => \Yii::$app->security->generateRandomString(20),
            'password_hash' => \Yii::$app->security->generatePasswordHash("ahroruz"),
            'password_reset_token' => null,
            'access_token' => \Yii::$app->security->generateRandomString(),
            'access_token_time' => time(),
            'email' => 'a-user@asd.uz',
            'template' => '',
            'layout' => '',
            'view' => '',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert('{{%users}}', [
            'username' => 'testAdmin',
            'auth_key' => \Yii::$app->security->generateRandomString(20),
            'password_hash' => \Yii::$app->security->generatePasswordHash("testAdminuz"),
            'password_reset_token' => null,
            'access_token' => \Yii::$app->security->generateRandomString(),
            'access_token_time' => time(),
            'email' => 'testAdmin@asd.uz',
            'template' => '',
            'layout' => '',
            'view' => '',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert('{{%users}}', [
            'username' => 'suAdmin',
            'auth_key' => \Yii::$app->security->generateRandomString(20),
            'password_hash' => \Yii::$app->security->generatePasswordHash("suAdminuz"),
            'password_reset_token' => null,
            'access_token' => \Yii::$app->security->generateRandomString(),
            'access_token_time' => time(),
            'email' => 'suAdmin@asd.uz',
            'template' => '',
            'layout' => '',
            'view' => '',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

    }

    public function down()
    {
        $this->dropTable('{{%users}}');
    }
}
