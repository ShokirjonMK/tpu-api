<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%new_user}}`.
 */
class m230502_130823_create_new_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->insert('{{%users}}', [
            'username' => 'Azizxon',
            'auth_key' => \Yii::$app->security->generateRandomString(20),
            'password_hash' => \Yii::$app->security->generatePasswordHash("Azizxonuz"),
            'password_reset_token' => null,
            'access_token' => \Yii::$app->security->generateRandomString(),
            'access_token_time' => time(),
            'email' => 'Azizxonuz@mail.ru',
            'template' => '',
            'layout' => '',
            'view' => '',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $auth = Yii::$app->authManager;
        $admin = $auth->createRole('admin');
        $auth->assign($admin, 7);


        $this->insert('{{%profile}}', [
            'user_id' => 7,
            'first_name'=>'Azizxon',
            'last_name'=>'Azizxon',
            'middle_name'=>'Azizxon',
            'created_at'=>time(),
            'updated_at'=>time(),
        ]);


    }

}
