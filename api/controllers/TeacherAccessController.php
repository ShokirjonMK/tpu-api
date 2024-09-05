<?php

namespace api\controllers;

use common\models\AuthAssignment;
use common\models\model\AuthChild;
use common\models\model\Department;
use common\models\model\EduSemestr;
use common\models\model\EduSemestrSubject;
use common\models\model\EduSemestrSubjectCategoryTime;
use common\models\model\Faculty;
use common\models\model\FinalExam;
use common\models\model\Kafedra;
use common\models\model\TeacherAccess;
use common\models\model\UserAccess;
use Yii;
use api\resources\Job;
use base\ResponseStatus;
use common\models\JobInfo;
use common\models\model\Profile;
use common\models\model\Semestr;
use common\models\model\TimeTable1;
use common\models\User;

class TeacherAccessController extends ApiActiveController
{
    public $modelClass = 'api\resources\TeacherAccess';

    public function actions()
    {
        return [];
    }

    public $table_name = 'teacher_access';
    public $controller_name = 'TeacherAccess';

    public function actionContent($lang)
    {
        $model = new TeacherAccess();

        $query = $model->find()
            ->with(['teacher'])
            ->andWhere(['is_deleted' => 0]);

        if (isRole(('teacher') && (!isRole('mudir')))) {
            $query->andWhere(['user_id' => current_user_id()]);
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionFree($lang)
    {
        $get = Yii::$app->request->get();

        $errors = [];
        if (empty($get['edu_semestr_subject_category_time'])) {
            $errors[] = ['edu_semestr_subject_category_time' => _e('Edu semestr subject category time Id is required')];
        }

        if (count($errors) > 0) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $errors, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        $categoryTime = EduSemestrSubjectCategoryTime::findOne($get['edu_semestr_subject_category_time']);
        $subjectid = $categoryTime->eduSemestrSubject->subject_id;

        $model = new TeacherAccess();
        $query = $model->find()
            ->where([
                'is_lecture' => $categoryTime->subject_category_id,
                'subject_id' => $subjectid,
                'is_deleted' => 0,
                'status' => 1
            ])->groupBy('user_id');

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionFreeExam()
    {
        $errors = [];
        if (empty(Yii::$app->request->get('faculty_id'))) {
            $errors[] = ['faculty_id' => _e('Faculty Id is required.')];
        }
        if (empty(Yii::$app->request->get('date'))) {
            $errors[] = ['date' => _e('Date is required.')];
        }
        if (empty(Yii::$app->request->get('para_id'))) {
            $errors[] = ['para_id' => _e('Para Id is required.')];
        }

        if (count($errors) > 0) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $errors, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        $date = Yii::$app->request->get('date');
        $para = Yii::$app->request->get('para_id');

        $model = new User();

        $query = $model->find()
            ->with(['profile'])
            ->join('LEFT JOIN', 'profile', 'profile.user_id = users.id')
            ->join('LEFT JOIN', 'auth_assignment', 'auth_assignment.user_id = users.id')
            ->andWhere(['users.deleted' => 0])
            ->groupBy('profile.user_id')
            ->andWhere(['not in', 'auth_assignment.item_name', ['admin' , currentRole()]])
            ->andFilterWhere(['like', 'username', Yii::$app->request->get('query')]);


        if (!isRole('admin')) {
            $userIds = AuthAssignment::find()
                ->select('user_id')
                ->where([
                    'in', 'auth_assignment.item_name',
                    AuthChild::find()->select('child')->where([
                        'in', 'parent', currentRole()
                    ])
                ]);
            $query->andFilterWhere([
                'in', 'users.id', $userIds
            ]);
        }

//        $query->andFilterWhere([
//            'in', 'users.id', UserAccess::find()->select('user_id')->where([
//                'table_id' => Yii::$app->request->get('faculty_id'),
//                'user_access_type_id' => Faculty::USER_ACCESS_TYPE_ID,
//                'is_deleted' => 0,
//                'status' => 1,
//            ])
//        ])->orFilterWhere([
//            'in', 'users.id', UserAccess::find()->select('user_id')->where([
//                'table_id' => Kafedra::find()->select('id')->where([
//                    'faculty_id' => Yii::$app->request->get('faculty_id'),
//                    'status' => 1,
//                    'is_deleted' => 0,
//                ]),
//                'user_access_type_id' => Kafedra::USER_ACCESS_TYPE_ID,
//                'is_deleted' => 0,
//                'status' => 1,
//            ])
//        ]);


        $filter = '{"role_name":["tutor","teacher"]}';
        $filter = json_decode(str_replace("'", "", $filter));
        //  Filter from Profile
        $profile = new Profile();
        if (isset($filter)) {
            foreach ($filter as $attribute => $value) {
                $attributeMinus = explode('-', $attribute);
                if (isset($attributeMinus[1])) {
                    if ($attributeMinus[1] == 'role_name') {
                        if (is_array($value)) {
                            $query = $query->andWhere(['not in', 'auth_assignment.item_name', $value]);
                        }
                    }
                }
                if ($attribute == 'role_name') {
                    if (is_array($value)) {
                        $query = $query->andWhere(['in', 'auth_assignment.item_name', $value]);
                    } else {
                        $query = $query->andFilterWhere(['like', 'auth_assignment.item_name', '%' . $value . '%', false]);
                    }
                }
                if (in_array($attribute, $profile->attributes())) {
                    $query = $query->andFilterWhere(['profile.' . $attribute => $value]);
                }
            }
        }


        $query->andFilterWhere(['not in' , 'users.id' , FinalExam::find()
            ->select('user_id')
            ->where(['date' => date("Y-m-d" , strtotime($date)) , 'para_id' => $para, 'is_deleted' => 0])]);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionIndex($lang)
    {

        $model = new TeacherAccess();

        $query = $model->find()
            ->groupBy('user_id')
            ->where([$this->table_name . '.is_deleted' => 0])
            ->join('INNER JOIN', 'profile', 'profile.user_id = ' . $this->table_name . '.user_id')
            ->join('INNER JOIN', 'users', 'users.id = ' . $this->table_name . '.user_id');

        $query->andWhere(['users.status' => User::STATUS_ACTIVE, 'deleted' => 0]);

        //  Filter from Profile 
        $profile = new Profile();

        if (isset($filter)) {
            foreach ($filter as $attribute => $id) {
                if (in_array($attribute, $profile->attributes())) {
                    $query = $query->andFilterWhere(['profile.' . $attribute => $id]);
                }
            }
        }

        $queryfilter = Yii::$app->request->get('filter-like');
        $queryfilter = json_decode(str_replace("'", "", $queryfilter));
        if (isset($queryfilter)) {
            foreach ($queryfilter as $attributeq => $word) {
                if (in_array($attributeq, $profile->attributes())) {
                    $query = $query->andFilterWhere(['like', 'profile.' . $attributeq, '%' . $word . '%', false]);
                }
            }
        }
        // ***

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }


    public function actionGet($lang)
    {

        $model = new TeacherAccess();

        $query = $model->find()
            ->where([$this->table_name . '.is_deleted' => 0 , $this->table_name . '.status' => 1]);

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }


    public function actionCreate($lang)
    {
        $model = new TeacherAccess();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = TeacherAccess::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('TeacherAccess successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = User::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();
        $result = TeacherAccess::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('TeacherAccess successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = TeacherAccess::find()
            ->andWhere(['id' => $id])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = TeacherAccess::findOne($id);

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $model->is_deleted = 1;
        $model->update(false);

        return $this->response(1, _e('TeacherAccess succesfully removed.'), null, null, ResponseStatus::OK);
    }
}
