<?php

namespace api\controllers;

use Yii;
use api\resources\User;
use api\resources\Password;
use base\ResponseStatus;
use common\models\model\AuthChild;
use common\models\model\EncryptPass;
use common\models\model\Keys;
use common\models\model\PasswordEncrypts;

class PasswordController extends ApiActiveController
{
    public $modelClass = 'api\resources\Password';

    // public $modelClass;

    public function actions()
    {
        return [];
    }

    public function actionIndex($lang)
    {
        $data = new Password();
        $data = $data->decryptThisUser();
//        return $data;
        return $this->response(1, _e('Success'), $data);
    }

    public function actionUpdate($lang, $id)
    {
        $post = Yii::$app->request->post();

        $passwordNew =  $post['new_password'] ?? null;
        $passwordOld =  $post['old_password'] ?? null;
        $passwordRe =  $post['re_password'] ?? null;
        $data = new Password();

        if (isRole('admin')) {
            $data = $data->decryptThisUser($id);
        } else {
            $data = $data->decryptThisUser(current_user_id());
        }

        if ($passwordNew != $passwordRe || $passwordNew == $passwordOld) {
            return $this->response(0, _e('Error sending password.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        if (($data['password'] == $passwordOld) || isRole('admin')) {

            if (strlen($passwordNew) >= 6) {

                if ($passwordRe == $passwordNew) {
                    if (isRole('admin') && current_user_id() != $id) {
                        $model = User::findOne($id);
                        $model->savePassword($passwordNew, $id);
                        $model->is_changed = User::PASSWORD_NO_CHANED;
                    } else {
                        $model = User::findOne(current_user_id());
                        $model->savePassword($passwordNew, current_user_id());
                        $model->is_changed = User::PASSWORD_CHANED;
                    }
                    //**parolni shifrlab saqlaymiz */
                    // $model->savePassword($passwordNew, current_user_id());
                    //**** */
                    $model->password_hash = \Yii::$app->security->generatePasswordHash($passwordNew);
                    $model->change_password_type = 1;

                    if ($model->save(false)) {
                        return $this->response(1, _e('Password successfully changed!'), null, null, ResponseStatus::OK);
                    } else {
                        return $this->response(0, _e('There is an error occurred while changing password!'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
                    }
                } else {
                    return $this->response(0, _e('Passwords are not same.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
                }
            } else {
                return $this->response(0, _e('The password must be at least 6 characters.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        } else {
            return $this->response(0, _e('Old password incorrect.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
    }

    public function actionView($lang, $id)
    {
        $user_id = $id;
        if (current_user_id() != $user_id) {


            if (!(isRole('admin') || isRole('edu_admin'))) {
                return $this->response(0, _e('You can not get.'), null, null, ResponseStatus::NOT_FOUND);
            }

            $user = User::findOne(current_user_id());
            $viewTeacher = User::findOne($user_id);

            if ($user->attach_role == 'mudir') {
                if ($viewTeacher->attach_role == 'teacher') {
                    return $this->response(0, _e('You can not get.'), null, null, ResponseStatus::NOT_FOUND);
                }
            }

            $isChild =
                AuthChild::find()
                    ->where(['parent' => $user->attach_role])
                    ->andWhere(['in', 'child', current_user_roles_array($user_id)])
                    ->all();

            $isChildTwo =
                AuthChild::find()
                    ->where(['child' => $user->attach_role])
                    ->andWhere(['in' , 'parent' , current_user_roles_array($user_id)])
                    ->all();

            if ((count($isChild) == 0 || count($isChildTwo) > 0) && !isRole('admin')) return $this->response(0, _e('You can not get.'), null, null, ResponseStatus::NOT_FOUND);
            
        }
        $data = new Password();
        $data = $data->decryptThisUser($user_id);

        return $this->response(1, _e('Success.'), $data, null, ResponseStatus::OK);
        return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
    }
}
