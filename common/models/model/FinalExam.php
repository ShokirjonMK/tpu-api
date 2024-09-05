<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use common\models\model\Student;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%exam}}".
 *
 * @property int $id
 * @property int $group_id
 * @property int|null $start_time
 * @property int|null $finish_time
 * @property float|null $max_ball
 * @property int|null $duration
 * @property string|null $question
 * @property string|null $file
 * @property int|null $course_id
 * @property int|null $semestr_id
 * @property int $edu_year_id
 * @property int $language_id
 * @property int $edu_plan_id
 * @property int $edu_semestr_id
 * @property int $edu_semester_subject_id
 * @property int|null $faculty_id
 * @property int|null $direction_id
 * @property int|null $type
 * @property int|null $status
 * @property int|null $order
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Course $course
 * @property Direction $direction
 * @property EduPlan $eduPlan
 * @property EduSemestr $eduSemester
 * @property EduYear $eduYear
 * @property ExamControlStudent[] $examControlStudents
 * @property Faculty $faculty
 * @property Language $language
 * @property Semestr $semester
 * @property Subject $subject
 * @property SubjectCategory $subjectCategory
 * @property TeacherAccess $teacher_access_id
 * @property User $teacherUser
 * @property TimeTable1 $timeTable
 */
class FinalExam extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const EXAM = 3;


    const STATUS_DEFAULT = 3;
    const STATUS_CHARGE = 4;
    const STATUS_MUDIR = 5;
    const STATUS_DEAN = 6;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'final_exam';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'vedomst',
                    'edu_semestr_subject_id',
                    'user_id',
                    'exam_form_type',
                    'date',
                    'para_id',
                    'room_id',
                ], 'required'
            ],
            [
                [
                    'vedomst',
                    'edu_semestr_subject_id',
                    'user_id',
                    'exam_form_type',
                    'para_id',
                    'course_id',
                    'semestr_id',
                    'edu_year_id',
                    'subject_id',
                    'edu_plan_id',
                    'edu_semestr_id',
                    'edu_semestr_exams_type_id',
                    'exams_type_id',
                    'edu_form_id',
                    'faculty_id',
                    'direction_id',
                    'exam_type',
                    'exam_form_type',
                    'para_id',
                    'building_id',
                    'room_id',
                    'status',
                    'order',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted'
                ], 'integer'
            ],
            [['date'], 'date', 'format' => 'yyyy-mm-dd'],
//            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => Group::className(), 'targetAttribute' => ['group_id' => 'id']],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']],
            [['edu_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']],
            [['edu_semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestr::className(), 'targetAttribute' => ['edu_semestr_id' => 'id']],
            [['edu_semestr_subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestrSubject::className(), 'targetAttribute' => ['edu_semestr_subject_id' => 'id']],
            [['edu_semestr_exams_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestrExamsType::className(), 'targetAttribute' => ['edu_semestr_exams_type_id' => 'id']],
            [['exams_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamsType::className(), 'targetAttribute' => ['exams_type_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['edu_form_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduForm::className(), 'targetAttribute' => ['edu_form_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semestr_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['language_id' => 'id']],
            [['para_id'], 'exist', 'skipOnError' => true, 'targetClass' => Para::className(), 'targetAttribute' => ['para_id' => 'id']],
            [['building_id'], 'exist', 'skipOnError' => true, 'targetClass' => Building::className(), 'targetAttribute' => ['building_id' => 'id']],
            [['room_id'], 'exist', 'skipOnError' => true, 'targetClass' => Room::className(), 'targetAttribute' => ['room_id' => 'id']],
        ];
    }

    public function fields()
    {
        $fields =  [
            'id',
            'vedomst',
            'edu_semestr_subject_id',
            'user_id',
            'exam_form_type',
            'para_id',
            'course_id',
            'semestr_id',
            'edu_year_id',
            'subject_id',
            'edu_plan_id',
            'edu_semestr_id',
            'edu_semestr_exams_type_id',
            'exams_type_id',
            'edu_form_id',
            'faculty_id',
            'direction_id',
            'exam_type',
            'exam_form_type',
            'para_id',
            'building_id',
            'room_id',
            'para_id',
            'date',

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
        $extraFields =  [
            'eduSemestrSubject',
            'para',
            'room',
            'groups',
            'direction',
            'faculty',
            'user',
            'examsType',
            'eduSemestrExamsType',
            'eduForm',
            'building',
            'eduPlan',
            'eduSemestr',
            'studentMark',
            'subject',
            'eduYear',

            'finalExamConfirm',

            'course',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getEduYear()
    {
        return $this->hasOne(EduYear::className(), ['id' => 'edu_year_id']);
    }

    public function getEduPlan()
    {
        return $this->hasOne(EduPlan::className(), ['id' => 'edu_plan_id']);
    }

    public function getEduSemestr()
    {
        return $this->hasOne(EduSemestr::className(), ['id' => 'edu_semestr_id']);
    }

    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
    }

    public function getEduSemestrSubject()
    {
        return $this->hasOne(EduSemestrSubject::className(), ['id' => 'edu_semestr_subject_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getGroups()
    {
        return $this->hasMany(FinalExamGroup::className(), ['final_exam_id' => 'id'])->where(['status' =>1 , 'is_deleted' => 0]);
    }

    public function getDirection()
    {
        return $this->hasOne(Direction::className(), ['id' => 'direction_id']);
    }

    public function getFaculty()
    {
        return $this->hasOne(Faculty::className(), ['id' => 'faculty_id']);
    }

    public function getEduForm()
    {
        return $this->hasOne(Faculty::className(), ['id' => 'faculty_id']);
    }

    public function getExamsType()
    {
        return $this->hasOne(ExamsType::className(), ['id' => 'exams_type_id']);
    }

    public function getEduSemestrExamsType()
    {
        return $this->hasOne(EduSemestrExamsType::className(), ['id' => 'edu_semestr_exams_type_id']);
    }

    public function getCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'course_id']);
    }

    public function getRoom()
    {
        return $this->hasOne(Room::className(), ['id' => 'room_id']);
    }

    public function getBuilding()
    {
        return $this->hasOne(Building::className(), ['id' => 'building_id']);
    }

    public function getPara()
    {
        return $this->hasOne(Para::className(), ['id' => 'para_id']);
    }

    public function getFinalExamConfirm()
    {
        return $this->hasMany(FinalExamConfirm::className(), ['final_exam_id' => 'id'])->where(['is_deleted' => 0, 'status' => 1]);
    }

    public function getStudentMark()
    {
        return StudentMark::find()
            ->where([
                'vedomst' => $this->vedomst,
                'edu_semestr_subject_id' => $this->edu_semestr_subject_id,
                'edu_semestr_exams_type_id' => $this->edu_semestr_exams_type_id,
                'is_deleted' => 0
            ])
            ->andWhere(['in' , 'group_id' , FinalExamGroup::find()
                    ->select('group_id')
                    ->where([
                        'final_exam_id' => $this->id,
                        'status' => 1,
                        'is_deleted' => 0
                    ])
                ])
            ->all();
    }


    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $model->exam_form_type = 1;
        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        // Xonani shu vaqtda bandligini tekshiradi
        $subjectIsExam = $model->isRoom($model);
        if ($subjectIsExam) {
            $errors[] = ['room_id' => _e('This room is occupied.')];
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $eduSemestrSubject = $model->eduSemestrSubject;
        $eduSemestr = $eduSemestrSubject->eduSemestr;
        $eduSemestrExamsType = EduSemestrExamsType::findOne([
            'edu_semestr_subject_id' => $eduSemestrSubject->id,
            'exams_type_id' => self::EXAM,
            'status' => 1,
            'is_deleted' => 0
        ]);
        if ($eduSemestrExamsType == null) {
            $errors[] = _e('There is no Final Exam in the Edu Plan.');
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        if (isset($eduSemestr)) {
            $model->subject_id = $eduSemestrSubject->subject_id;
            $model->edu_plan_id = $eduSemestr->edu_plan_id;
            $model->edu_semestr_id = $eduSemestr->id;
            $model->edu_year_id = $eduSemestr->edu_year_id;
            $model->faculty_id = $eduSemestr->eduPlan->faculty_id;
            $model->direction_id = $eduSemestr->eduPlan->direction_id;
            $model->course_id = $eduSemestr->course_id;
            $model->semestr_id = $eduSemestr->semestr_id;
            $model->edu_form_id = $eduSemestr->edu_form_id;
            $model->building_id = $model->room->building_id;
            $model->edu_semestr_exams_type_id = $eduSemestrExamsType->id;
            $model->exams_type_id = $eduSemestrExamsType->exams_type_id;
            if (!$model->validate()) {
                $errors[] = $model->errors;
                $transaction->rollBack();
                return simplify_errors($errors);
            } else {
                $model->save(false);
                if (isset($post['groups'])) {
                    $groups = json_decode($post['groups']);
                    foreach ($groups as $group) {
                        $eduPlanId = Group::findOne($group)->edu_plan_id;
                        if ($model->edu_plan_id == $eduPlanId) {
                            $finalExamGroup = new FinalExamGroup();
                            $finalExamGroup->final_exam_id = $model->id;
                            $finalExamGroup->group_id = $group;
                            $finalExamGroup->edu_semestr_subject_id = $model->edu_semestr_subject_id;
                            $finalExamGroup->subject_id = $model->subject_id;
                            $finalExamGroup->edu_semestr_id = $model->edu_semestr_id;
                            $finalExamGroup->edu_plan_id = $model->edu_plan_id;
                            $finalExamGroup->vedomst = $model->vedomst;
                            $finalExamGroup->date = $model->date;
                            $finalExamGroup->para_id = $model->para_id;
                            $finalExamGroup->user_id = $model->user_id;

                            // Guruh uchun uchbu fandan shu vedoms uchun imtixon yaratilganmi yoki yoq shuni tekshiradi
                            $subjectIsExam = $model->subjectIsExam($finalExamGroup);
                            if ($subjectIsExam) {
                                $errors[] = ['vedomst' => _e('This group has been given an examination from this discipline.')];
                                $transaction->rollBack();
                                return simplify_errors($errors);
                            }

                            // Guruh uchun shu kun shu parada boshqa imtixoni bormi yoki yoq shuni tekshiradi
                            $subjectIsExam = $model->isGroupExam($finalExamGroup);
                            if ($subjectIsExam) {
                                $errors[] = ['para_id' => _e('At this time there is a group exam.')];
                                $transaction->rollBack();
                                return simplify_errors($errors);
                            }

                            if (!$finalExamGroup->validate()) {
                                $errors[] = $finalExamGroup->errors;
                                $transaction->rollBack();
                                return simplify_errors($errors);
                            } else {
                                $finalExamGroup->save(false);
                            }
                        } else {
                            $errors[] = $eduPlanId . _e(' Error sending group.');
                        }
                    }

                } else {
                    $errors[] = _e('Groups data not found.');
                }
            }
        } else {
            $errors[] = _e('Edu Semestr not found.');
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public function subjectIsExam($model)
    {
        $query = FinalExamGroup::find()
            ->where([
                'vedomst' => $model->vedomst,
                'edu_semestr_subject_id' => $model->edu_semestr_subject_id,
                'group_id' => $model->group_id,
                'is_deleted' => 0
            ])
            ->andWhere(['<>' , 'id' , $model->id ?? 0])
            ->exists();
        if ($query) {
            return true;
        }
        return false;
    }

    public function isGroupExam($model)
    {
        $query = FinalExamGroup::find()
            ->where([
                'group_id' => $model->group_id,
                'date' => $model->finalExam->date,
                'para_id' => $model->finalExam->para_id,
                'is_deleted' => 0
            ])
            ->andWhere(['<>' , 'id' , $model->id ?? 0])
            ->exists();
        if ($query) {
            return true;
        }
        return false;
    }

    public function isRoom($model)
    {
        $query = FinalExam::find()
            ->where([
                'date' => $model->date,
                'room_id' => $model->room_id,
                'para_id' => $model->para_id,
                'is_deleted' => 0
            ])
            ->andWhere(['<>' , 'id' , $model->id ?? 0])
            ->exists();
        if ($query) {
            return true;
        }
        return false;
    }

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        // Xonani shu vaqtda bandligini tekshiradi
        $subjectIsExam = $model->isRoom($model);
        if ($subjectIsExam) {
            $errors[] = ['room_id' => _e('This room is occupied.')];
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $eduSemestrSubject = $model->eduSemestrSubject;
        $eduSemestr = $eduSemestrSubject->eduSemestr;
        $eduSemestrExamsType = EduSemestrExamsType::findOne([
            'edu_semestr_subject_id' => $eduSemestrSubject->id,
            'exams_type_id' => self::EXAM,
            'status' => 1,
            'is_deleted' => 0
        ]);
        if ($eduSemestrExamsType == null) {
            $errors[] = _e('There is no Final Exam in the Edu Plan.');
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        if (isset($eduSemestr)) {
            $model->subject_id = $eduSemestrSubject->subject_id;
            $model->edu_plan_id = $eduSemestr->edu_plan_id;
            $model->edu_semestr_id = $eduSemestr->id;
            $model->edu_year_id = $eduSemestr->edu_year_id;
            $model->faculty_id = $eduSemestr->eduPlan->faculty_id;
            $model->direction_id = $eduSemestr->eduPlan->direction_id;
            $model->course_id = $eduSemestr->course_id;
            $model->semestr_id = $eduSemestr->semestr_id;
            $model->edu_form_id = $eduSemestr->edu_form_id;
            $model->building_id = $model->room->building_id;
            $model->edu_semestr_exams_type_id = $eduSemestrExamsType->id;
            $model->exams_type_id = $eduSemestrExamsType->exams_type_id;
            if (!$model->validate()) {
                $errors[] = $model->errors;
                $transaction->rollBack();
                return simplify_errors($errors);
            } else {
                $model->save(false);
                if (isset($post['groups'])) {
                    $groups = json_decode($post['groups']);
                    FinalExamGroup::updateAll(['status' => 0 , 'is_deleted' => 1], ['final_exam_id' => $model->id]);
                    foreach ($groups as $group) {
                        $gr = Group::findOne($group);
                        if ($model->edu_plan_id == $gr->edu_plan_id) {
                            $query = FinalExamGroup::findOne([
                                'final_exam_id' => $model->id,
                                'group_id' => $gr->id,
                                'status' => 0,
                                'is_deleted' => 1
                            ]);
                            if ($query == null) {
                                $finalExamGroup = new FinalExamGroup();
                                $finalExamGroup->final_exam_id = $model->id;
                                $finalExamGroup->group_id = $gr->id;
                                $finalExamGroup->edu_semestr_subject_id = $model->edu_semestr_subject_id;
                                $finalExamGroup->subject_id = $model->subject_id;
                                $finalExamGroup->edu_semestr_id = $model->edu_semestr_id;
                                $finalExamGroup->edu_plan_id = $model->edu_plan_id;
                                $finalExamGroup->vedomst = $model->vedomst;
                                $finalExamGroup->date = $model->date;
                                $finalExamGroup->para_id = $model->para_id;
                                $finalExamGroup->user_id = $model->user_id;

                                // Guruh uchun uchbu fandan shu vedoms uchun imtixon yaratilganmi yoki yoq shuni tekshiradi
                                $subjectIsExam = $model->subjectIsExam($finalExamGroup);
                                if ($subjectIsExam) {
                                    $errors[] = ['vedomst' => _e('This group has been given an examination from this discipline.')];
                                    $transaction->rollBack();
                                    return simplify_errors($errors);
                                }

                                // Guruh uchun shu kun shu parada boshqa imtixoni bormi yoki yoq shuni tekshiradi
                                $subjectIsExam = $model->isGroupExam($finalExamGroup);
                                if ($subjectIsExam) {
                                    $errors[] = ['para_id' => _e('At this time there is a group exam.')];
                                    $transaction->rollBack();
                                    return simplify_errors($errors);
                                }

                                if (!$finalExamGroup->validate()) {
                                    $errors[] = $finalExamGroup->errors;
                                    $transaction->rollBack();
                                    return simplify_errors($errors);
                                } else {
                                    $finalExamGroup->save(false);
                                }
                            } else {
                                $query->final_exam_id = $model->id;
                                $query->group_id = $gr->id;
                                $query->edu_semestr_subject_id = $model->edu_semestr_subject_id;
                                $query->subject_id = $model->subject_id;
                                $query->edu_semestr_id = $model->edu_semestr_id;
                                $query->edu_plan_id = $model->edu_plan_id;
                                $query->vedomst = $model->vedomst;
                                $query->date = $model->date;
                                $query->para_id = $model->para_id;
                                $query->user_id = $model->user_id;
                                $query->status = 1;
                                $query->is_deleted = 0;

                                // Guruh uchun uchbu fandan shu vedoms uchun imtixon yaratilganmi yoki yoq shuni tekshiradi
                                $subjectIsExam = $model->subjectIsExam($query);
                                if ($subjectIsExam) {
                                    $errors[] = ['vedomst' => _e('This group has been given an examination from this discipline.')];
                                    $transaction->rollBack();
                                    return simplify_errors($errors);
                                }

                                // Guruh uchun shu kun shu parada boshqa imtixoni bormi yoki yoq shuni tekshiradi
                                $subjectIsExam = $model->isGroupExam($query);
                                if ($subjectIsExam) {
                                    $errors[] = ['para_id' => _e('At this time there is a group exam.')];
                                    $transaction->rollBack();
                                    return simplify_errors($errors);
                                }

                                if (!$query->validate()) {
                                    $errors[] = $query->errors;
                                    $transaction->rollBack();
                                    return simplify_errors($errors);
                                } else {
                                    $query->update(false);
                                }
                            }
                        } else {
                            $errors[] = $gr->edu_plan_id . _e(' Error sending group.');
                        }
                    }
                } else {
                    $errors[] = _e('Groups data not found.');
                }
            }
        } else {
            $errors[] = _e('Edu Semestr not found.');
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function confirm($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (isset($post['status'])) {
            if ($post['status'] == 1 || $post['status'] == 2) {
                $model->status = $post['status'];
                $model->update(false);
            } else {
                $errors[] = _e('Status value sent incorrectly.');
            }
        } else {
            $errors[] = _e('Status value not found.');
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function confirmTwo($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (isset($post['status'])) {
            if ($post['status'] == 2 || $post['status'] == 3) {
                $model->status = $post['status'];
                $model->update(false);
            } else {
                $errors[] = _e('Status value sent incorrectly.');
            }
        } else {
            $errors[] = _e('Status value not found.');
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function inCharge($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $query = FinalExamConfirm::findOne([
            'final_exam_id' => $model->id,
            'user_id' => $model->user_id,
            'type' => 1,
            'status' => 1,
            'is_deleted' => 0
        ]);
        if ($query) {
            $query->status = 0;
            $query->update(false);
        }

        $newModel = new FinalExamConfirm();
        $newModel->final_exam_id = $model->id;
        $newModel->user_id = $model->user_id;
        $newModel->role_name = currentRole();
        $newModel->date = time();
        $newModel->type = 1;
        $newModel->save(false);

        if (isset($post['status'])) {
            if ($post['status'] == 3) {
                $model->status = $post['status'];
                $model->update(false);
                $newModel->order = $post['status'];
                $newModel->update(false);
            } elseif ($post['status'] == 4) {
                $getStudentMarks = $model->studentMark;
                if (count($getStudentMarks) > 0) {
                    foreach ($getStudentMarks as $getStudentMark) {
                        if ($getStudentMark->status != 2) {
                            $errors[] = _e('There is an ungraded student!');
                            $transaction->rollBack();
                            return simplify_errors($errors);
                        }
                    }
                }
                $model->status = $post['status'];
                $model->update(false);
                $newModel->order = $post['status'];
                $newModel->update(false);
            } else {
                $errors[] = _e('Status value sent incorrectly.');
            }
        } else {
            $errors[] = _e('Status value not found.');
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function confirmMudir($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $query = FinalExamConfirm::findOne([
            'final_exam_id' => $model->id,
            'user_id' => $model->user_id,
            'type' => 2,
            'is_deleted' => 0
        ]);
        if ($query) {
            $query->status = 0;
            $query->update(false);
        }
        $kafedra = get_mudir();
        $newModel = new FinalExamConfirm();
        $newModel->final_exam_id = $model->id;
        $newModel->user_id = $kafedra->user_id;
        $newModel->role_name = currentRole();
        $newModel->date = time();
        $newModel->type = 2;
        $newModel->save(false);

        if (isset($post['status'])) {
            if ($post['status'] == 4 || $post['status'] == 5) {
                $model->status = $post['status'];
                $model->update(false);
                $newModel->order = $post['status'];
                $newModel->update(false);
            } else {
                $errors[] = _e('Status value sent incorrectly.');
            }
        } else {
            $errors[] = _e('Status value not found.');
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function confirmDean($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $time = time();

        $query = FinalExamConfirm::findOne([
            'final_exam_id' => $model->id,
            'user_id' => $model->user_id,
            'type' => 3,
            'is_deleted' => 0
        ]);
        if ($query) {
            $query->status = 0;
            $query->update(false);
        }

        $dean = get_dean();
        $qr_info = examConfirm($model);
        $newModel = new FinalExamConfirm();
        $newModel->final_exam_id = $model->id;
        $newModel->user_id = $dean->user_id;
        $newModel->role_name = currentRole();
        $newModel->date = $time;
        $newModel->type = 3;
        $newModel->qr_code = qrCodeMK($qr_info);
        $newModel->save(false);

        if (isset($post['status'])) {
            if ($post['status'] == 5 || $post['status'] == 6) {
                $model->status = $post['status'];
                $model->update(false);
                $newModel->order = $post['status'];
                $newModel->update(false);
            } else {
                $errors[] = _e('Status value sent incorrectly.');
            }
        } else {
            $errors[] = _e('Status value not found.');
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function confirmLast($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (isset($post['status'])) {
            if ($post['status'] == 6 || $post['status'] == 7) {
                $model->status = $post['status'];
                $model->update(false);
                if ($model->status == 7) {
                    $finalExamGroups = $model->groups;
                    $subject = $model->eduSemestrSubject;
                    if (count($finalExamGroups) > 0) {
                        foreach ($finalExamGroups as $finalExamGroup) {
                            $group = $finalExamGroup->group;
                            $examBall = 0;
                            $controlBall = 0;
                            $examCategorys = EduSemestrExamsType::find()
                                ->where([
                                    'edu_semestr_subject_id' => $finalExamGroup->edu_semestr_subject_id,
                                    'status' => 1,
                                    'is_deleted' => 0
                                ])->all();

                            if (count($examCategorys) > 0) {
                                foreach ($examCategorys as $examCategory) {
                                    if ($examCategory->exams_type_id != 3) {
                                        $controlBall = $controlBall + $examCategory->max_ball;
                                    } else {
                                        $examBall = $examCategory->max_ball;
                                    }
                                }
                            }

                            $persentExamBall = (int)(($examBall * 60) / 100);
                            $persentControlBall = (int)(($controlBall * 60) / 100);

                            if (isset($group)) {
                                $studentVedomst = StudentSemestrSubjectVedomst::find()
                                    ->where([
                                        'group_id' => $group->id,
                                        'subject_id' => $finalExamGroup->subject_id,
                                        'vedomst' => $model->vedomst,
                                        'status' => 1,
                                        'is_deleted' => 0
                                    ])->all();

                                foreach ($studentVedomst as $item) {
                                    $marks = StudentMark::find()
                                        ->where([
                                            'student_semestr_subject_vedomst_id' => $item->id,
                                            'is_deleted' => 0
                                        ])->all();

                                    $ball = 0;
                                    $yak = 0;

                                    if (count($marks) > 0) {
                                        foreach ($marks as $mark) {
                                            if ($mark->exam_type_id != 3) {
                                                $ball = $ball + $mark->ball;
                                            } else {
                                                $yak = $mark->ball;
                                            }
                                        }
                                    }

                                    if ($subject->type == 0) {

                                        if ($ball < $persentControlBall || $yak < $persentExamBall) {
                                            $item->ball = 0;
                                            $item->passed = 2;
                                            $item->update(false);
                                            if ($item->vedomst < 3) {
                                                $ved = (int)$item->vedomst + 1;
                                                $query = StudentSemestrSubjectVedomst::findOne([
                                                    'student_id' => $item->student_id,
                                                    'student_semestr_subject_id' => $item->student_semestr_subject_id,
                                                    'vedomst' => $ved,
                                                    'is_deleted' => 0,
                                                    'status' => 1
                                                ]);
                                                if (!$query) {
                                                    $new = new StudentSemestrSubjectVedomst();
                                                    $new->student_semestr_subject_id = $item->student_semestr_subject_id;
                                                    $new->subject_id = $item->subject_id;
                                                    $new->edu_year_id = $item->edu_year_id;
                                                    $new->semestr_id = $item->semestr_id;
                                                    $new->student_id = $item->student_id;
                                                    $new->student_user_id = $item->student_user_id;
                                                    $new->group_id = $item->student->group_id;
                                                    $new->vedomst = $ved;
                                                    $new->save(false);

                                                    foreach ($marks as $value) {
                                                        $newMark = new StudentMark();
                                                        $newMark->edu_semestr_exams_type_id = $value->edu_semestr_exams_type_id;
                                                        $newMark->exam_type_id = $value->exam_type_id;
                                                        $newMark->group_id = $value->group_id;
                                                        $newMark->student_id = $value->student_id;
                                                        $newMark->student_user_id = $value->student_user_id;
                                                        $newMark->max_ball = $value->max_ball;
                                                        if ($value->exam_type_id != 3) {
                                                            $newMark->ball = $value->ball;
                                                        } else {
                                                            $newMark->ball = 0;
                                                        }
                                                        $newMark->edu_semestr_subject_id = $value->edu_semestr_subject_id;
                                                        $newMark->subject_id = $value->subject_id;
                                                        $newMark->edu_plan_id = $value->edu_plan_id;
                                                        $newMark->edu_semestr_id = $value->edu_semestr_id;
                                                        $newMark->faculty_id = $value->faculty_id;
                                                        $newMark->direction_id = $value->direction_id;
                                                        $newMark->semestr_id = $value->semestr_id;
                                                        $newMark->course_id = $value->course_id;
                                                        $newMark->vedomst = $new->vedomst;
                                                        $newMark->student_semestr_subject_vedomst_id = $new->id;
                                                        $newMark->save(false);
                                                    }
                                                }
                                            }
                                        } else {
                                            $attend = TimetableAttend::find()
                                                ->where([
                                                    'student_id' => $item->student_id,
                                                    'subject_id' => $item->subject_id,
                                                    'reason' => 0,
                                                    'status' => 1,
                                                    'is_deleted' => 0
                                                ])->count();

                                            $subjectHour = $finalExamGroup->eduSemestrSubject->allHour / 2;
                                            $attendPercent = ($subjectHour * 25) / 100;

                                            if ($attend  > $attendPercent) {
                                                $markExam = StudentMark::findOne([
                                                    'student_semestr_subject_vedomst_id' => $item->id,
                                                    'student_id' => $item->student_id,
                                                    'exam_type_id' => 3,
                                                    'is_deleted' => 0
                                                ]);
                                                $markExam->ball = 0;
                                                $markExam->save(false);
                                                StudentMark::markHistory($markExam);

                                                $item->ball = 0;
                                                $item->passed = 2;
                                                $item->update(false);
                                                if ($item->vedomst < 3) {
                                                    $ved = (int)$item->vedomst + 1;
                                                    $query = StudentSemestrSubjectVedomst::findOne([
                                                        'student_id' => $item->student_id,
                                                        'student_semestr_subject_id' => $item->student_semestr_subject_id,
                                                        'vedomst' => $ved,
                                                        'is_deleted' => 0,
                                                        'status' => 1
                                                    ]);
                                                    if (!$query) {
                                                        $new = new StudentSemestrSubjectVedomst();
                                                        $new->student_semestr_subject_id = $item->student_semestr_subject_id;
                                                        $new->subject_id = $item->subject_id;
                                                        $new->edu_year_id = $item->edu_year_id;
                                                        $new->semestr_id = $item->semestr_id;
                                                        $new->student_id = $item->student_id;
                                                        $new->student_user_id = $item->student_user_id;
                                                        $new->group_id = $item->student->group_id;
                                                        $new->vedomst = $ved;
                                                        $new->save(false);

                                                        foreach ($marks as $value) {
                                                            $newMark = new StudentMark();
                                                            $newMark->edu_semestr_exams_type_id = $value->edu_semestr_exams_type_id;
                                                            $newMark->exam_type_id = $value->exam_type_id;
                                                            $newMark->group_id = $value->group_id;
                                                            $newMark->student_id = $value->student_id;
                                                            $newMark->student_user_id = $value->student_user_id;
                                                            $newMark->max_ball = $value->max_ball;
                                                            if ($value->exam_type_id != 3) {
                                                                $newMark->ball = $value->ball;
                                                            } else {
                                                                $newMark->ball = 0;
                                                            }
                                                            $newMark->edu_semestr_subject_id = $value->edu_semestr_subject_id;
                                                            $newMark->subject_id = $value->subject_id;
                                                            $newMark->edu_plan_id = $value->edu_plan_id;
                                                            $newMark->edu_semestr_id = $value->edu_semestr_id;
                                                            $newMark->faculty_id = $value->faculty_id;
                                                            $newMark->direction_id = $value->direction_id;
                                                            $newMark->semestr_id = $value->semestr_id;
                                                            $newMark->course_id = $value->course_id;
                                                            $newMark->vedomst = $new->vedomst;
                                                            $newMark->student_semestr_subject_vedomst_id = $new->id;
                                                            $newMark->save(false);
                                                        }

                                                    }
                                                }

                                            } else {
                                                $item->ball = $ball + $yak;
                                                $item->passed = 1;
                                                $item->update(false);
                                                $studentSemestrSubject = $item->studentSemestrSubject;
                                                $studentSemestrSubject->all_ball = $item->ball;
                                                $studentSemestrSubject->closed = 1;
                                                $studentSemestrSubject->update(false);
                                            }

                                        }
                                    } elseif ($subject->type == 1) {
                                        if ($yak < $persentExamBall) {
                                            $item->ball = 0;
                                            $item->passed = 2;
                                            $item->update(false);
                                            if ($item->vedomst < 3) {
                                                $ved = $item->vedomst + 1;
                                                $query = StudentSemestrSubjectVedomst::findOne([
                                                    'student_id' => $item->student_id,
                                                    'student_semestr_subject_id' => $item->student_semestr_subject_id,
                                                    'vedomst' => $ved,
                                                    'is_deleted' => 0,
                                                    'status' => 1
                                                ]);
                                                if (!$query) {
                                                    $new = new StudentSemestrSubjectVedomst();
                                                    $new->student_semestr_subject_id = $item->student_semestr_subject_id;
                                                    $new->subject_id = $item->subject_id;
                                                    $new->edu_year_id = $item->edu_year_id;
                                                    $new->semestr_id = $item->semestr_id;
                                                    $new->student_id = $item->student_id;
                                                    $new->student_user_id = $item->student_user_id;
                                                    $new->group_id = $item->student->group_id;
                                                    $new->vedomst = $ved;
                                                    $new->save(false);

                                                    foreach ($marks as $value) {
                                                        $newMark = new StudentMark();
                                                        $newMark->edu_semestr_exams_type_id = $value->edu_semestr_exams_type_id;
                                                        $newMark->exam_type_id = $value->exam_type_id;
                                                        $newMark->group_id = $value->group_id;
                                                        $newMark->student_id = $value->student_id;
                                                        $newMark->student_user_id = $value->student_user_id;
                                                        $newMark->max_ball = $value->max_ball;
                                                        if ($value->exam_type_id != 3) {
                                                            $newMark->ball = $value->ball;
                                                        } else {
                                                            $newMark->ball = 0;
                                                        }
                                                        $newMark->edu_semestr_subject_id = $value->edu_semestr_subject_id;
                                                        $newMark->subject_id = $value->subject_id;
                                                        $newMark->edu_plan_id = $value->edu_plan_id;
                                                        $newMark->edu_semestr_id = $value->edu_semestr_id;
                                                        $newMark->faculty_id = $value->faculty_id;
                                                        $newMark->direction_id = $value->direction_id;
                                                        $newMark->semestr_id = $value->semestr_id;
                                                        $newMark->course_id = $value->course_id;
                                                        $newMark->vedomst = $new->vedomst;
                                                        $newMark->student_semestr_subject_vedomst_id = $new->id;
                                                        $newMark->save(false);
                                                    }
                                                }
                                            }
                                        } else {

                                            $item->ball = $ball + $yak;
                                            $item->passed = 1;
                                            $item->update(false);
                                            $studentSemestrSubject = $item->studentSemestrSubject;
                                            $studentSemestrSubject->all_ball = $item->ball;
                                            $studentSemestrSubject->closed = 1;
                                            $studentSemestrSubject->update(false);

                                        }
                                    } else {
                                        $errors[] = _e('Type value error.');
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $errors[] = _e('Information cannot be changed.');
                }
            } else {
                $errors[] = _e('Status value sent incorrectly.');
            }
        } else {
            $errors[] = _e('Status value not found.');
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }


    public static function allConfirm($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        foreach ($model as $item) {
            $item->status = 7;
            $item->update(false);
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }


    public static function examFinish($model , $post) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];



        if (count($errors) == 0) {
            $model->save(false);
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }


    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = Current_user_id();
        } else {
            $this->updated_by = Current_user_id();
        }

        return parent::beforeSave($insert);
    }


    public function deleteFile($oldFile = NULL)
    {
        if (isset($oldFile)) {
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
        return true;
    }

}
