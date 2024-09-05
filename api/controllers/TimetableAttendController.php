<?php

namespace api\controllers;

use common\models\model\EduPlan;
use common\models\model\Group;
use common\models\model\TeacherAccess;
use common\models\model\Timetable;
use common\models\model\TimeTable1;
use common\models\model\TimetableAttend;
use common\models\model\TimetableDate;
use common\models\model\TimeTableGroup;
use Yii;
use base\ResponseStatus;
use common\models\model\EduSemestr;
use common\models\model\Kafedra;
use common\models\model\Student;
use common\models\model\Subject;
use yii\db\Expression;

class TimetableAttendController extends ApiActiveController
{
    public $modelClass = 'common\models\model\TimetableAttend';

    public $table_name = 'timetable_attend';

    public $controller_name = 'TimetableAttend';

    public function actions()
    {
        return [];
    }


    public function actionIndex()
    {
        $model = new TimetableAttend();

        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 0]);

        if (isRole('student')) {
            $query->andFilterWhere(['student_id' => current_student()->id]);
        }

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
        $post = Yii::$app->request->post();
        $result = TimetableAttend::createItem($post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully saved.'), null, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

}
