<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "edu_semestr_subject".
 *
 * @property int $id
 * @property int $edu_semestr_id
 * @property int $subject_id
 * @property int $subject_type_id
 * @property float $credit
 * @property int $all_ball_yuklama
 * @property int $is_checked
 * @property int $max_ball
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property EduSemestrExamsType[] $eduSemestrExamsTypes
 * @property EduSemestr $eduSemestr
 * @property Subject $subject
 * @property SubjectType $subjectType
 * @property EduSemestrSubjectCategoryTime[] $eduSemestrSubjectCategoryTimes
 */
class EduSemestrSubject extends \yii\db\ActiveRecord
{
    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

//    const REQUIRED = 1;
//    const OPTIONAL = 2;

    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'edu_semestr_subject';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [
                [
                    'edu_semestr_id',
                    'subject_id'
                ], 'required'
            ],
            //    [['edu_semestr_id', 'subject_id', 'subject_type_id', 'credit', 'all_ball_yuklama', 'is_checked', 'max_ball'], 'required'],
            [
                [
                    'type',
                    'edu_semestr_id',
                    'faculty_id',
                    'direction_id',
                    'subject_id',
                    'subject_type_id',
                    'all_ball_yuklama',
                    'is_checked',
                    'max_ball',
                    'order',
                    'status',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted'
                ], 'integer'
            ],
            [['credit', 'auditory_time'], 'double'],
            [['edu_semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestr::className(), 'targetAttribute' => ['edu_semestr_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['subject_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectType::className(), 'targetAttribute' => ['subject_type_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'faculty_id' => 'Faculty Id',
            'direction_id' => 'Direction Id',
            'edu_semestr_id' => 'Edu Semestr ID',
            'subject_id' => 'Subject ID',
            'subject_type_id' => 'Subject Type ID',
            'credit' => 'Credit',
            'auditory_time' => 'auditory_time',
            'all_ball_yuklama' => 'All Ball Yuklama',
            'is_checked' => 'Is Checked',
            'max_ball' => 'Max Ball',
            'order' => _e('Order'),
            'status' => _e('Status'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'is_deleted' => _e('Is Deleted'),
        ];
    }


    public function fields()
    {
        $fields = [
            'id',
            'type',
            'edu_semestr_id',
            'subject_id',
            'subject_type_id',
            'auditory_time',
            'credit',
            'all_ball_yuklama',
            'is_checked',
            'max_ball',
            'order',
            'status',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields = [
            'faculty',
            'direction',
            'eduSemestrExamsTypes',
            'markExamsType',
            'eduSemestr',
            'subject',
            'subjectType',
            'eduSemestrSubjectCategoryTimes',

            'studentSubjectSelection',
            'selection',
            'studentMark',
            'allHour',
            'categoryAllHour',
            'subjectVedomst',
            'noFilterSubject',

            'timeTableTeacher',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getTimeTableTeacher() {
        $teacher = TeacherAccess::find()
            ->where(['is_deleted' => 0])
            ->andWhere(['in' , 'user_id' , TimeTable1::find()
                ->select('user_id')
                ->where([
                    'subject_id' => $this->subject_id,
                    'is_deleted' => 0,
                    'status' => 1,
                ])])
            ->all();
        return $teacher;
    }

    public function getStudentSubjectSelection()
    {
        if (isRole('student')) {
            return $this->hasOne(StudentSubjectSelection::className(), ['edu_semestr_subject_id' => 'id'])->onCondition(['user_id' => current_user_id()]);
        }
        return $this->hasOne(StudentSubjectSelection::className(), ['edu_semestr_subject_id' => 'id']);
    }

    public function getSelection()
    {
        if (isRole('student')) {
            return $this->hasOne(StudentSubjectSelection::className(), ['edu_semestr_subject_id' => 'id'])->onCondition(['user_id' => current_user_id()]);
        }
        return $this->hasOne(StudentSubjectSelection::className(), ['edu_semestr_subject_id' => 'id']);
    }

    /**
     * Gets query for [[faculty_id]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFaculty()
    {
        return $this->hasOne(Faculty::className(), ['faculty_id' => 'id']);
    }

    public function getStudentMark()
    {
        if (isRole('student')) {
            return $this->hasMany(StudentMark::className(), ['edu_semestr_subject_id' => 'id'])->where(['is_deleted' => 0,'student_id' => current_student()->id]);
        } else {
            return $this->hasMany(StudentMark::className(), ['edu_semestr_subject_id' => 'id'])->where(['is_deleted' => 0]);
        }
    }

    /**
     * Gets query for [[direction_id]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getDirection()
    {
        return $this->hasOne(Direction::className(), ['direction_id' => 'id']);
    }

    /**
     * Gets query for [[EduSemestrExamsTypes]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getEduSemestrExamsTypes()
    {
        return $this->hasMany(EduSemestrExamsType::className(), ['edu_semestr_subject_id' => 'id'])->where(['status' => 1, 'is_deleted' => 0]);
    }

    public function getMarkExamsType()
    {
        if (isRole('teacher')) {
            $teacherAccessSubjectIds = TeacherAccess::find()
                ->select('subject_id')
                ->where(['user_id' => current_user_id(), 'is_deleted' => 0]);

            $timeTables = TimetableDate::find()
                ->select('edu_semestr_subject_id')
                ->where([
                    'user_id' => current_user_id(),
                    'group_id' => Yii::$app->request->get('group_id'),
                    'status' => 1,
                    'is_deleted' => 0,
                ])
                ->andWhere(['in' , 'subject_id' , $teacherAccessSubjectIds])
                ->andFilterWhere(['edu_year_id' => activeYearId()])->all();

            $lecture = false;
            $notLecture = false;
            if (count($timeTables) > 0) {
                foreach ($timeTables as $timeTable) {
                    if ($timeTable->subject_category_id == 1) {
                        $lecture = true;
                    } else {
                        $notLecture = true;
                    }
                }
            }

            if ($lecture && $notLecture) {
                return $this->hasMany(EduSemestrExamsType::className(), ['edu_semestr_subject_id' => 'id'])->where(['status' => 1, 'is_deleted' => 0]);
            } elseif ($lecture) {
                return $this->hasMany(EduSemestrExamsType::className(), ['edu_semestr_subject_id' => 'id'])
                    ->where(['status' => 1, 'is_deleted' => 0])
                    ->andWhere(['in' , 'exams_type_id' , [1 , 3]]);
            } elseif ($notLecture) {
                return $this->hasMany(EduSemestrExamsType::className(), ['edu_semestr_subject_id' => 'id'])
                    ->where(['status' => 1, 'is_deleted' => 0])
                    ->andWhere(['not in' , 'exams_type_id' , [1 , 3]]);
            } else {
                return null;
            }

        } elseif (isRole('admin')) {
            return $this->hasMany(EduSemestrExamsType::className(), ['edu_semestr_subject_id' => 'id'])->where(['status' => 1, 'is_deleted' => 0]);
        } else {
            return null;
        }

    }



    /**
     * Gets query for [[EduSemestr]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getEduSemestr()
    {
        return $this->hasOne(EduSemestr::className(), ['id' => 'edu_semestr_id']);
    }

    /**
     * Gets query for [[Subject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id'])->onCondition(['is_deleted' => 0]);
    }

    public function getNoFilterSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
    }

    /**
     * Gets query for [[SubjectType]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getSubjectType()
    {
        return $this->hasOne(SubjectType::className(), ['id' => 'subject_type_id']);
    }

    public function getSubjectVedomst()
    {
        return $this->hasMany(SubjectVedomst::className(), ['edu_semestr_subject_id' => 'id'])->where(['is_deleted' => 0]);
    }

    /**
     * Gets query for [[EduSemestrSubjectCategoryTimes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduSemestrSubjectCategoryTimes()
    {
        return $this->hasMany(EduSemestrSubjectCategoryTime::className(), ['edu_semestr_subject_id' => 'id'])->where(['status' => 1, 'is_deleted' => 0]);
    }

    public function getHours()
    {
        $hours = $this->eduSemestrSubjectCategoryTimes;
        $clock = 0;
        if (count($hours) > 0) {
            foreach ($hours as $category) {
                if ($category->subject_category_id != 6) {
                    $clock = $clock + ($category->hours / 2);
                }
            }
        }
        return $clock;
    }

    public function getAllHour()
    {
        $categoryTimes = $this->eduSemestrSubjectCategoryTimes;
        $all_hour = 0;
        if (count($categoryTimes) > 0) {
            foreach ($categoryTimes as $categoryTime) {
                if (!($categoryTime->subject_category_id == 6 || $categoryTime->subject_category_id == 5)) {
                    $all_hour = $all_hour + $categoryTime->hours;
                }
            }
        }
        return $all_hour;
    }

    public function getCategoryAllHour()
    {
        $categoryTimes = $this->eduSemestrSubjectCategoryTimes;
        $all_hour = 0;
        if (count($categoryTimes) > 0) {
            foreach ($categoryTimes as $categoryTime) {
                $all_hour = $all_hour + $categoryTime->hours;
            }
        }
        return $all_hour;
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }
        $EduSemestrSubject = EduSemestrSubject::findOne([
            'edu_semestr_id' => $model->edu_semestr_id,
            'subject_id' => $post['subject_id'] ?? null,
            'status' => 1,
            'is_deleted' => 0,
        ]);
        if (isset($EduSemestrSubject)) {
            $errors[] = _e('This Edu Subject already exists in This Semester');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->save(false)) {

            $eduSemestr = $model->eduSemestr;

            $eduYearId = $eduSemestr->edu_year_id;
            if ($eduYearId == 7 || $eduYearId == 8) {
//                $errors[] = _e('You cannot add or edit a subject to this year.');
//                $transaction->rollBack();
//                return simplify_errors($errors);
            }

            $subjectSillabus = Subject::findOne(['id' => $post['subject_id'] ?? null]);
            $all_ball_yuklama = 0;
            $max_ball = 0;

            $new = new SubjectVedomst();
            $new->edu_semestr_subject_id = $model->id;
            $new->edu_semestr_id = $model->edu_semestr_id;
            $new->edu_plan_id = $model->eduSemestr->eduPlan->id;
            $new->type = 1;
            $new->status = 0;
            $new->save(false);

            $model->faculty_id = $model->eduSemestr->eduPlan->faculty_id;
            $model->direction_id = $model->eduSemestr->eduPlan->direction_id;
            $model->update(false);

            if (isset($subjectSillabus)) {

                if (isset($subjectSillabus->edu_semestr_subject_category_times)) {
                    $EduSemestrSubjectCategoryTimes = json_decode(str_replace("'", "", $subjectSillabus->edu_semestr_subject_category_times));
                    foreach ($EduSemestrSubjectCategoryTimes as $subjectCatId => $subjectCatValues) {
                        if (SubjectCategory::find()->where(['id' => $subjectCatId])->exists()) {
                            $EduSemestrSubjectCategoryTime1 = new EduSemestrSubjectCategoryTime();
                            $EduSemestrSubjectCategoryTime1->edu_semestr_subject_id = $model->id;
                            $EduSemestrSubjectCategoryTime1->subject_category_id = $subjectCatId;
                            $EduSemestrSubjectCategoryTime1->hours = $subjectCatValues;
                            $EduSemestrSubjectCategoryTime1->edu_semestr_id = $model->edu_semestr_id;
                            $EduSemestrSubjectCategoryTime1->subject_id = $model->subject_id;
                            $EduSemestrSubjectCategoryTime1->save(false);
                            $all_ball_yuklama = $all_ball_yuklama + $subjectCatValues;
                        }
                    }
                }

                if (isset($subjectSillabus->edu_semestr_exams_types)) {
                    $EduSemestrExamType = json_decode(str_replace("'", "", $subjectSillabus->edu_semestr_exams_types));
                    foreach ($EduSemestrExamType as $examsTypeId => $examsTypeMaxBal) {
                        if (ExamsType::find()->where(['id' => $examsTypeId])->exists()) {
                            $EduSemestrExamsType1 = new EduSemestrExamsType();
                            $EduSemestrExamsType1->edu_semestr_subject_id = $model->id;
                            $EduSemestrExamsType1->exams_type_id = $examsTypeId;
                            $EduSemestrExamsType1->max_ball = $examsTypeMaxBal;
                            $EduSemestrExamsType1->save(false);
                            $max_ball = $max_ball + $examsTypeMaxBal;
                        }
                    }
                }
                $model->all_ball_yuklama = $all_ball_yuklama;
                $model->max_ball = $max_ball;
                $model->subject_type_id = $subjectSillabus->subject_type_id;
                $model->credit = $subjectSillabus->credit;
                $model->auditory_time = $subjectSillabus->auditory_time;
            }

            $model->update(false);
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function createItemss($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
        }
        $EduSemestrSubject = EduSemestrSubject::findOne([
            'edu_semestr_id' => $model->edu_semestr_id,
            'subject_id' => $post['subject_id'] ?? null,
        ]);

        if (isset($EduSemestrSubject)) {
            $errors[] = _e('This Edu Subject already exists in This Semester');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->save()) {

            $subjectSillabus = SubjectSillabus::findOne(['subject_id' => $post['subject_id'] ?? null]);
            $all_ball_yuklama = 0;
            $max_ball = 0;

            $model->faculty_id = $model->eduSemestr->eduPlan->faculty_id;
            $model->direction_id = $model->eduSemestr->eduPlan->direction_id;
            $model->update();

            if (isset($subjectSillabus)) {

                if (isset($subjectSillabus->edu_semestr_subject_category_times)) {
                    $EduSemestrSubjectCategoryTimes = json_decode(str_replace("'", "", $subjectSillabus->edu_semestr_subject_category_times));
                    foreach ($EduSemestrSubjectCategoryTimes as $subjectCatId => $subjectCatValues) {
                        $EduSemestrSubjectCategoryTime1 = new EduSemestrSubjectCategoryTime();
                        $EduSemestrSubjectCategoryTime1->edu_semestr_subject_id = $model->id;
                        $EduSemestrSubjectCategoryTime1->subject_category_id = $subjectCatId;
                        $EduSemestrSubjectCategoryTime1->hours = $subjectCatValues;
                        $EduSemestrSubjectCategoryTime1->edu_semestr_id = $model->edu_semestr_id;
                        $EduSemestrSubjectCategoryTime1->subject_id = $model->subject_id;
                        $EduSemestrSubjectCategoryTime1->save();
                        $all_ball_yuklama = $all_ball_yuklama + $subjectCatValues;
                    }
                }

                if (isset($subjectSillabus->edu_semestr_exams_types)) {
                    $EduSemestrExamType = json_decode(str_replace("'", "", $subjectSillabus->edu_semestr_exams_types));
                    foreach ($EduSemestrExamType as $examsTypeId => $examsTypeMaxBal) {
                        $EduSemestrExamsType1 = new EduSemestrExamsType();
                        $EduSemestrExamsType1->edu_semestr_subject_id = $model->id;
                        $EduSemestrExamsType1->exams_type_id = $examsTypeId;
                        $EduSemestrExamsType1->max_ball = $examsTypeMaxBal;
                        $EduSemestrExamsType1->save();
                        $max_ball = $max_ball + $examsTypeMaxBal;

                        /** imtihonlar  imtixon turlari bo'yicha avto yaralishi  */
                        // $newExam = new Exam();
                        // $newExam->faculty_id = $model->eduSemestr->eduPlan->faculty_id;
                        // $newExam->direction_id = $model->eduSemestr->eduPlan->direction_id;
                        // $newExam->exam_type_id = $examsTypeId;
                        // $newExam->edu_semestr_subject_id = $model->id;
                        // //
                        // $newExam->type = $model->eduSemestr->type ?? 1;

                        // $newExam->start = date("Y-m-d H:i:s");
                        // $newExam->finish = date("Y-m-d H:i:s");
                        // $newExam->max_ball = $examsTypeMaxBal;
                        // $newExam->min_ball = $examsTypeMaxBal;
                        // $newExam->status = Exam::STATUS_INACTIVE;
                        // $newExam->save();
                        /** imtihonlar  imtixon turlari bo'yicha avto yaralishi  */
                    }
                }
                $model->all_ball_yuklama = $all_ball_yuklama;
                $model->max_ball = $max_ball;
                $model->subject_type_id = $subjectSillabus->subject_type_id;
                $model->credit = $subjectSillabus->credit;
                $model->auditory_time = $subjectSillabus->auditory_time;
            }

            $model->update();
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        if ($model->save(false)) {

            $all_ball_yuklama = 0;
            $max_ball = 0;
            $auditory_time = 0;

            if (isset($post['SubjectCategory'])) {
                $SubjectCategory = json_decode(str_replace("'", "", $post['SubjectCategory']));
                if (isset($SubjectCategory)) {
                    EduSemestrSubjectCategoryTime::deleteAll(['edu_semestr_subject_id' => $model->id]);
                    foreach ($SubjectCategory as $subjectCatId => $subjectCatValues) {
                        if (SubjectCategory::find()->where(['id' => $subjectCatId])->exists()) {

                            $EduSemestrSubjectCategoryTime = new EduSemestrSubjectCategoryTime();
                            $EduSemestrSubjectCategoryTime->edu_semestr_subject_id = $model->id;
                            $EduSemestrSubjectCategoryTime->subject_category_id = $subjectCatId;
                            $EduSemestrSubjectCategoryTime->hours = $subjectCatValues;
                            $EduSemestrSubjectCategoryTime->edu_semestr_id = $model->edu_semestr_id;
                            $EduSemestrSubjectCategoryTime->subject_id = $model->subject_id;
                            $EduSemestrSubjectCategoryTime->save();
                            $auditory_time += $subjectCatValues;
                            $all_ball_yuklama = $all_ball_yuklama + $subjectCatValues;

                        }
                    }
                    $model->auditory_time = $auditory_time;
                    $model->all_ball_yuklama = $all_ball_yuklama;
                }
            }

            if ($model->auditory_time != $model->credit * Subject::CREDIT_TIME) {
                $errors[] = _e("Total hours do not equal credit hours.");
            }

            if (isset($post['EduSemestrExamType'])) {
                $EduSemestrExamType = json_decode(str_replace("'", "", $post['EduSemestrExamType']));
                EduSemestrExamsType::updateAll(['status' => 0], ['edu_semestr_subject_id' => $model->id]);
                foreach ($EduSemestrExamType as $examsTypeId1 => $examsTypeMaxBal1) {
                    if (ExamsType::find()->where(['id' => $examsTypeId1,])->exists()) {
                        $queryExamType = EduSemestrExamsType::findOne([
                            'edu_semestr_subject_id' => $model->id,
                            'exams_type_id' => $examsTypeId1,
                        ]);
                        if (isset($queryExamType)) {
                            $queryExamType->max_ball = $examsTypeMaxBal1;
                            $queryExamType->status = 1;
                            $queryExamType->save(false);
                            $max_ball = $max_ball + $examsTypeMaxBal1;
                        } else {
                            $EduSemestrExamsType = new EduSemestrExamsType();
                            $EduSemestrExamsType->edu_semestr_subject_id = $model->id;
                            $EduSemestrExamsType->exams_type_id = $examsTypeId1;
                            $EduSemestrExamsType->max_ball = $examsTypeMaxBal1;
                            $EduSemestrExamsType->save(false);
                            $max_ball = $max_ball + $examsTypeMaxBal1;
                        }

                    }
                }
                $model->max_ball = $max_ball;
            }

            if (count($errors) == 0) {
                $model->update(false);
                $transaction->commit();
                return true;
            }
            $transaction->rollBack();
            return simplify_errors($errors);
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function deleteItem($model)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $timeTable = TimeTable1::find()
            ->where([
                'edu_semestr_subject_id' => $model->id,
                'subject_id' => $model->subject_id,
                'is_deleted' => 0
            ])->count();
        if ($timeTable > 0) {
            $errors[] = _e("You cannot delete a subject!");
        } else {
            EduSemestrSubjectCategoryTime::updateAll(['status' => 0 , 'is_deleted' => 1], ['edu_semestr_subject_id' => $model->id]);
            EduSemestrExamsType::updateAll(['status' => 0 , 'is_deleted' => 1], ['edu_semestr_subject_id' => $model->id]);
            $model->is_deleted = 1;
            $model->save(false);
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = current_user_id();
        } else {
            $this->updated_by = current_user_id();
        }
        return parent::beforeSave($insert);
    }
}
