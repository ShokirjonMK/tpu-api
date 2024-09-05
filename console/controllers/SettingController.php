<?php

namespace console\controllers;

use api\resources\BaseGet;
use api\resources\SemestrUpdate;
use common\models\model\AttendReason;
use common\models\model\EduPlan;
use common\models\model\EduSemestr;
use common\models\model\EduSemestrExamsType;
use common\models\model\EduSemestrSubject;
use common\models\model\FinalExam;
use common\models\model\FinalExamGroup;
use common\models\model\Group;
use common\models\model\MarkHistory;
use common\models\model\PasswordEncrypts;
use common\models\model\Student;
use common\models\model\StudentAttend;
use common\models\model\StudentGroup;
use common\models\model\StudentMark;
use common\models\model\StudentMarkHistory;
use common\models\model\StudentSemestrSubject;
use common\models\model\StudentSemestrSubjectVedomst;
use common\models\model\StudentTopicPermission;
use common\models\model\SubjectVedomst;
use common\models\model\TeacherAccess;
use common\models\model\Timetable;
use common\models\model\TimetableAttend;
use common\models\model\TimetableDate;
use common\models\model\TimetableReason;
use common\models\model\TimetableStudent;
use common\models\model\Translate;
use common\models\model\UserAccess;
use common\models\model\UserAccessType;
use common\models\Profile;
use common\models\User;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yii;
use yii\console\Controller;
use yii\db\Expression;
use yii\helpers\Console;
use yii\helpers\Inflector;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use yii\web\Response;

class SettingController extends Controller
{
    public function actionStdGroupKey()
    {
        $studentGroups = StudentGroup::find()
            ->where(['is_deleted' => 0 , 'semestr_key' => null])
            ->all();
        foreach ($studentGroups as $studentGroup) {
            $micTime = (int) round(microtime(true) * 1000);
            $startKey = Yii::$app->security->generateRandomString(15);
            $endKey = Yii::$app->security->generateRandomString(10);
            $rand = rand(10000 , 99999);
            $studentGroup->semestr_key = $startKey.$rand.$micTime.$endKey;
            $studentGroup->save(false);
        }
    }



    public function actionVedBug()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $finalExams = FinalExam::find()
            ->where([
                'status' => 7,
                'vedomst' => 2,
                'is_deleted' => 0
            ])->all();

        $data = [];

        foreach ($finalExams as $model) {
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
                                if ($ball >= $persentControlBall && $yak >= $persentExamBall) {
                                    $t = StudentSemestrSubject::findOne([
                                        'id' => $item->student_semestr_subject_id
                                    ]);
                                    if ($t->closed == 0) {
                                        $data[] = "3 - ".$item->id." - ".$item->edu_year_id. " - " .$subject->id." - ".$t->all_ball." - ".$ball." - ".$yak;
                                    }
                                }
                            } elseif ($subject->type == 1) {
                                if ($yak >= $persentExamBall) {
                                    $t = StudentSemestrSubject::findOne([
                                        'id' => $item->student_semestr_subject_id
                                    ]);
                                    if ($t->closed == 0) {
                                        $data[] = "3 - ".$item->id." - ".$item->edu_year_id. " - " .$subject->id." - ".$t->all_ball." - ".$ball." - ".$yak;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if (count($errors) == 0) {
            $transaction->commit();
        }
        dd($data);
    }




    public function actionSetFinal()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $finalExams = FinalExam::find()
            ->where([
                'status' => 7,
                'vedomst' => 2,
                'is_deleted' => 0
            ])->all();

        $data = [];

        foreach ($finalExams as $model) {
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
                                            $t = StudentSemestrSubject::findOne([
                                                'id' => $item->student_semestr_subject_id
                                            ]);
                                            if ($t->closed == 0) {
                                                $data[] = "1 - ".$item->id." - ".$item->edu_year_id." - ".$t->all_ball." - ".$ball." - ".$yak;
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
                                                $t = StudentSemestrSubject::findOne([
                                                    'id' => $item->student_semestr_subject_id
                                                ]);
                                                if ($t->closed == 0) {
                                                    $data[] = "2 - ".$item->id." - ".$item->edu_year_id." - ".$t->all_ball." - ".$ball." - ".$yak;
                                                }
                                            }
                                        }
                                    }
                                }
                            } elseif ($subject->type == 1) {
                                if ($yak < $persentExamBall) {
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
                                            $t = StudentSemestrSubject::findOne([
                                                'id' => $item->student_semestr_subject_id
                                            ]);
                                            if ($t->closed == 0) {
                                                $data[] = "3 - ".$item->id." - ".$item->edu_year_id." - ".$t->all_ball." - ".$ball." - ".$yak;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        dd($data);

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
    }









    public function actionSubjectBug1()
    {
        $semSubjects = StudentSemestrSubject::find()
            ->where([
                'edu_form_id' => 1,
                'status' => 1,
                'is_deleted' => 0
            ])
//            ->andWhere(['=' , 'semestr_id' , 1])
            ->all();
        $data = [];
        foreach ($semSubjects as $semSubject) {
            $subject = $semSubject->eduSemestrSubject->noFilterSubject;
            if ($subject->semestr_id != $semSubject->semestr_id) {
                $semSubject->is_deleted = 60;
                $semSubject->status = 60;
                $semSubject->save(false);
                $data[] = $semSubject->id.' -- '.$semSubject->all_ball;
            }
        }
        dd($data);
    }


    public function actionSubjectDel()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $eduSemestrs = EduSemestrSubject::find()
            ->where([
                'is_deleted' => 1
            ])->all();

        foreach ($eduSemestrs as $eduSemestr) {
            $semSubjects = StudentSemestrSubject::find()
                ->where([
                    'edu_semestr_subject_id' => $eduSemestr->id,
                ])->all();
            if (count($semSubjects) > 0) {
                foreach ($semSubjects as $semSubject) {
                    $semSubject->is_deleted = 61;
                    $semSubject->save(false);

                    $vedoms = StudentSemestrSubjectVedomst::find()
                        ->where([
                            'student_semestr_subject_id' => $semSubject->id,
                            'is_deleted' => 0
                        ])->all();
                    if (count($vedoms) > 0) {
                        foreach ($vedoms as $vedom) {
                            $vedom->is_deleted = 61;
                            $vedom->save(false);
                            $marks = StudentMark::find()
                                ->where([
                                    'student_semestr_subject_vedomst_id' => $vedom->id,
                                    'is_deleted' => 0
                                ])
                                ->all();
                            if (count($marks) > 0) {
                                foreach ($marks as $mark) {
                                    $mark->is_deleted = 61;
                                    $mark->save(false);
                                }
                            }
                        }
                    }
                }
            }
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
    }


    public function actionIkromAka()
    {
        $time = 'ik001dd1111';
        $data = StudentSemestrSubject::find()
            ->where([
                'all_ball' => 0,
                'status' => 1,
                'is_deleted' => 0
            ])
            ->andWhere(['<' , 'edu_year_id' , 7])
            ->andWhere(['<>' , 'edu_form_id' , 2])
            ->all();

        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $sheet->setCellValue('A1', 'Student Id');
        $sheet->setCellValue('B1', 'Student full name');
        $sheet->setCellValue('C1', 'Semestr');
        $sheet->setCellValue('D1', 'Guruh');
        $sheet->setCellValue('E1', 'Ball');
        $sheet->setCellValue('F1', 'Fan');
        $sheet->setCellValue('G1', 'Fakultet');
        $sheet->setCellValue('H1', 'Yonalish');
        $sheet->setCellValue('I1', 'SubjectId');

        // Populate the spreadsheet with data
        $row = 2; // Start from the second row

        foreach ($data as $item) {
            echo $item->id."\n";
            $student = $item->student;
            $group_name = '';
            if ($student->group_id != null) {
                $group = Group::findOne($student->group_id);
                $group_name = $group->unical_name;
            }
            $profile = $student->profile;
            $sbj = '';
            $subject = $item->eduSemestrSubject->noFilterSubject;
            if ($item->eduSemestrSubject != null) {
                $sbj = $item->eduSemestrSubject->id;
            }
            $subject_name = '';
            $faculty_name = '';
            $direction_name = '';
            $translate = Translate::findOne([
                'table_name' => 'subject',
                'language' => 'uz',
                'model_id' => $subject->id,
                'is_deleted' => 0
            ]);
            if ($translate) {
                $subject_name = $translate->name;
            }

            $fc = Translate::findOne([
                'table_name' => 'faculty',
                'language' => 'uz',
                'model_id' => $item->faculty_id,
                'is_deleted' => 0
            ]);
            if ($fc) {
                $faculty_name = $fc->name;
            }

            $dr = Translate::findOne([
                'table_name' => 'direction',
                'language' => 'uz',
                'model_id' => $item->faculty_id,
                'is_deleted' => 0
            ]);
            if ($dr) {
                $direction_name = $dr->name;
            }
            $full_name = $profile->last_name.' '.$profile->first_name.' '.$profile->middle_name;
            $sheet->setCellValue('A' . $row, $item->student_id);
            $sheet->setCellValue('B' . $row, $full_name);
            $sheet->setCellValue('C' . $row, $item->semestr_id);
            $sheet->setCellValue('D' . $row, $group_name);
            $sheet->setCellValue('E' . $row, $item->all_ball);
            $sheet->setCellValue('F' . $row, $subject_name);
            $sheet->setCellValue('G' . $row, $faculty_name);
            $sheet->setCellValue('H' . $row, $direction_name);
            $sheet->setCellValue('I' . $row, $sbj);
            $row++;
        }

        if (!file_exists(\Yii::getAlias('@api/web/storage/export'))) {
            mkdir(\Yii::getAlias('@api/web/storage/export'), 0777, true);
        }

        $filePath = Yii::getAlias('@api/web/storage/export/'.$time.'.xlsx');

        // Create a writer to save the file
        $writer = new Xlsx($spreadsheet);

        // Save the file to the specified path
        $writer->save($filePath);
    }


    public function actionIkromAka2()
    {
        $time = 'newik001';
        $data = StudentSemestrSubject::find()
            ->where([
                'all_ball' => 0,
                'edu_form_id' => [1 , 3],
                'status' => 1,
                'is_deleted' => 0
            ])
            ->andWhere(['>' , 'edu_year_id' , 6])
            ->all();

        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $sheet->setCellValue('A1', 'Student Id');
        $sheet->setCellValue('B1', 'Student full name');
        $sheet->setCellValue('C1', 'Semestr');
        $sheet->setCellValue('D1', 'Guruh');
        $sheet->setCellValue('E1', 'Ball');
        $sheet->setCellValue('F1', 'Fan');
        $sheet->setCellValue('G1', 'Fakultet');
        $sheet->setCellValue('H1', 'Yonalish');

        // Populate the spreadsheet with data
        $row = 2; // Start from the second row

        foreach ($data as $item) {
            echo $item->id."\n";
            $student = $item->student;
            $group_name = '';
            if ($student->group_id != null) {
                $group = Group::findOne($student->group_id);
                $group_name = $group->unical_name;
            }
            $profile = $student->profile;
            $subject = $item->eduSemestrSubject->noFilterSubject;
            $subject_name = '';
            $faculty_name = '';
            $direction_name = '';
            $translate = Translate::findOne([
                'table_name' => 'subject',
                'language' => 'uz',
                'model_id' => $subject->id,
                'is_deleted' => 0
            ]);
            if ($translate) {
                $subject_name = $translate->name;
            }

            $fc = Translate::findOne([
                'table_name' => 'faculty',
                'language' => 'uz',
                'model_id' => $item->faculty_id,
                'is_deleted' => 0
            ]);
            if ($fc) {
                $faculty_name = $fc->name;
            }

            $dr = Translate::findOne([
                'table_name' => 'direction',
                'language' => 'uz',
                'model_id' => $item->faculty_id,
                'is_deleted' => 0
            ]);
            if ($dr) {
                $direction_name = $dr->name;
            }
            $full_name = $profile->last_name.' '.$profile->first_name.' '.$profile->middle_name;
            $sheet->setCellValue('A' . $row, $item->student_id);
            $sheet->setCellValue('B' . $row, $full_name);
            $sheet->setCellValue('C' . $row, $item->semestr_id);
            $sheet->setCellValue('D' . $row, $group_name);
            $sheet->setCellValue('E' . $row, $item->all_ball);
            $sheet->setCellValue('F' . $row, $subject_name);
            $sheet->setCellValue('G' . $row, $faculty_name);
            $sheet->setCellValue('H' . $row, $direction_name);
            $row++;
        }

        if (!file_exists(\Yii::getAlias('@api/web/storage/export'))) {
            mkdir(\Yii::getAlias('@api/web/storage/export'), 0777, true);
        }

        $filePath = Yii::getAlias('@api/web/storage/export/'.$time.'.xlsx');

        // Create a writer to save the file
        $writer = new Xlsx($spreadsheet);

        // Save the file to the specified path
        $writer->save($filePath);
    }




    public function actionFt()
    {
        $students = Student::find()
            ->where([
                'status' => 5
            ])->all();
        foreach ($students as $student) {
            $semSubjects = StudentSemestrSubject::find()
                ->where([
                    'student_id' => $student->id,
                    'is_deleted' => 1
                ])->all();
            if (count($semSubjects) > 0) {
                foreach ($semSubjects as $subject) {
                    $vedoms = StudentSemestrSubjectVedomst::find()
                        ->where([
                            'student_semestr_subject_id' => $subject->id,
                            'student_id' => $student->id,
                        ])
                        ->all();
                    if (count($vedoms) > 0) {
                        foreach ($vedoms as $vedom) {
                            $vedom->is_deleted = 1;
                            $vedom->save(false);
                            $marks = StudentMark::find()
                                ->where([
                                    'student_semestr_subject_vedomst_id' => $vedom->id,
                                    'student_id' => $student->id,
                                ])->all();
                            foreach ($marks as $mark) {
                                $mark->is_deleted = 1;
                                $mark->save(false);
                            }
                        }
                    }
                }
            }
        }
    }


    public function actionMarkHis()
    {
        $studentMarks = StudentMark::find()
            ->where([
                'faculty_id' => 3,
                'is_deleted' => 0,
                'updated_by' => 0
            ])->all();

        $data = [];
        foreach ($studentMarks as $studentMark) {
            $getMarkHistory = MarkHistory::find()
                ->where([
                    'student_mark_id' => $studentMark->id,
                ])
                ->orderBy('update_time desc')
                ->one();

            if ($getMarkHistory) {
                if ($studentMark->ball != $getMarkHistory->ball) {
                    $studentMark->ball = $getMarkHistory->ball;
                    $studentMark->save(false);
                }
            }
        }
    }


    public function actionTy()
    {
        $query = StudentSemestrSubject::find()
            ->where([
                'is_deleted' => 1
            ])
            ->all();

        $data = [];
        foreach ($query as $item) {
            $subq = StudentSemestrSubjectVedomst::find()
                ->where([
                    'student_semestr_subject_id' => $item->id,
                ])
                ->all();
            if (count($subq) > 0) {

                foreach ($subq as $t) {
                    $marks = StudentMark::find()
                        ->where([
                            'student_semestr_subject_vedomst_id' => $t->id,
                            'is_deleted' => 0
                        ])
                        ->all();
                    if (count($marks) > 0) {
                        foreach ($marks as $m) {
                            $m->is_deleted = 1;
                            $m->save(false);
                        }
                    }
                }

//                $wwww = StudentSemestrSubject::findOne([
//                    'student_id' => $item->student_id,
//                    'edu_semestr_subject_id' => $item->edu_semestr_subject_id,
//                    'status' => 1,
//                    'is_deleted' => 0
//                ]);
//
//                if ($wwww != null) {
//                    $wwww->is_deleted = 12;
//                    $wwww->save(false);
//
//                    $subq2 = StudentSemestrSubjectVedomst::find()
//                        ->where([
//                            'student_semestr_subject_id' => $wwww->id,
//                            'is_deleted' => 0
//                        ])
//                        ->all();
//
//                    foreach ($subq2 as $value) {
//                        $value->is_deleted = 1;
//                        $value->save(false);
//
//                        $marks = StudentMark::find()
//                            ->where([
//                                'student_semestr_subject_vedomst_id' => $value->id,
//                                'is_deleted' => 0
//                            ])
//                            ->all();
//                        if (count($marks) > 0) {
//                            foreach ($marks as $m) {
//                                $m->is_deleted = 1;
//                                $m->save(false);
//                            }
//                        }
//
//                    }
//                }

//                $item->is_deleted = 0;
//                $item->save(false);

            }
        }
        dd($data);
    }

    public function actionIds()
    {
        $query = Timetable::find()
            ->where([
                'edu_form_id' => 2,
                'status' => 1,
                'is_deleted' => 0
            ])->all();

        foreach ($query as $item) {
            $q = TimetableDate::find()
                ->where([
                    'ids_id' => $item->ids,
                    'status' => 1,
                    'is_deleted' => 0
                ])
                ->orderBy('date asc')
                ->all();
            if (count($q) > 1) {
                $a = 1;
                foreach ($q as $value) {
                    if ($a != 1) {
                        $value->is_deleted = 1;
                        $value->status = 0;
                        $value->save(false);
                        TimetableAttend::updateAll(['is_deleted' => 7] , ['timetable_date_id' => $value->id]);
                    }
                    $a++;
                }
            }
        }
    }


    public function actionUserArole()
    {
        $model = new \api\resources\User();

        $query = $model->find()
            ->with(['profile'])
            ->join('LEFT JOIN', 'profile', 'profile.user_id = users.id')
            ->join('LEFT JOIN', 'auth_assignment', 'auth_assignment.user_id = users.id')
            ->andWhere(['users.deleted' => 0])
            ->groupBy('profile.user_id')
            ->andWhere(['not in', 'auth_assignment.item_name', ['student' , 'admin']])
            ->all();


        foreach ($query as $item) {
            $roles = current_user_roles_array($item->id);
            if ($roles != null) {
                foreach ($roles as $role) {
                    if ($role == 'dean') {
                        $userAccess = UserAccess::findOne([
                            'user_id' => $item->id,
                            'user_access_type_id' => 1,
                            'is_leader' => 1,
                            'status' => 1,
                            'is_deleted' => 0
                        ]);
                        if ($userAccess) {
                            $userAccess->role_name = 'dean';
                            $userAccess->save(false);
                        }
                    } elseif ($role == 'mudir') {
                        $userAccess = UserAccess::findOne([
                            'user_id' => $item->id,
                            'user_access_type_id' => 2,
                            'is_leader' => 1,
                            'status' => 1,
                            'is_deleted' => 0
                        ]);
                        if ($userAccess) {
                            $userAccess->role_name = 'mudir';
                            $userAccess->save(false);
                        }
                    } elseif ($role == 'teacher') {
                        $userAccess = UserAccess::findOne([
                            'user_id' => $item->id,
                            'user_access_type_id' => 2,
                            'is_leader' => 0,
                            'status' => 1,
                            'is_deleted' => 0
                        ]);
                        if ($userAccess) {
                            $userAccess->role_name = 'teacher';
                            $userAccess->save(false);
                        }
                    } elseif ($role == 'tutor') {
                        $userAccess = UserAccess::findOne([
                            'user_id' => $item->id,
                            'user_access_type_id' => 1,
                            'is_leader' => 0,
                            'status' => 1,
                            'is_deleted' => 0
                        ]);
                        if ($userAccess) {
                            $userAccess->role_name = 'tutor';
                            $userAccess->save(false);
                        }
                    } elseif ($role == 'dean_deputy') {
                        $userAccess = UserAccess::findOne([
                            'user_id' => $item->id,
                            'user_access_type_id' => 1,
                            'is_leader' => 0,
                            'status' => 1,
                            'is_deleted' => 0
                        ]);
                        if ($userAccess) {
                            $userAccess->role_name = 'dean_deputy';
                            $userAccess->save(false);
                        }
                    }
                }
            }
        }
    }









    public function actionEndVedomst()
    {
        $studentSemestrSubjects = StudentSemestrSubject::find()
            ->where([
                'edu_year_id' => 7,
                'all_ball' => 0,
                'closed' => 0
            ])
            ->all();

        $data = [];
        foreach ($studentSemestrSubjects as $studentSemestrSubject) {
               $studentVedoms = StudentSemestrSubjectVedomst::find()
                   ->where([
                       'student_semestr_subject_id' => $studentSemestrSubject->id,
                       'status' => 1,
                       'is_deleted' => 0,
                       'passed' => 1
                   ])
                   ->orderBy('vedomst desc')
                   ->one();
               if ($studentVedoms) {
                   $studentSemestrSubject->all_ball = $studentVedoms->ball;
                   $studentSemestrSubject->closed = 1;
                   $studentSemestrSubject->save(false);
               }
        }

    }



    public function actionControlBall()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $time = 'bug_mark';

        $vedoms = StudentSemestrSubjectVedomst::find()
            ->where([
                'edu_year_id' => 8,
                'status' => 1,
                'is_deleted' => 0
            ])->all();

        $data = [];
        foreach ($vedoms as $vedom) {
               $studentMarks = StudentMark::find()
                   ->where([
                       'student_semestr_subject_vedomst_id' => $vedom->id,
                       'is_deleted' => 0,
                       'status' => 2,
                       'exam_type_id' => [1, 2, 5],
                   ])->all();

               if (count($studentMarks) > 0) {
                   foreach ($studentMarks as $studentMark) {
                       $userId = $studentMark->updated_by;

                        $roles = current_user_roles_array($userId);

                        if ($roles != null) {
                            if (count($roles) > 0) {
                                foreach ($roles as $role) {
                                    if ($role == 'dean' || $role == 'dean_deputy') {
                                        $data[] = $studentMark->id;
                                    }
                                }
                            }
                        }
                   }
               }
        }


        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
    }


    public function actionExport11()
    {
        $time = time();

        $filter = ['student.status' => 10 , 'student.edu_form_id' => 2];
        $data = BaseGet::studentGet($filter);

        if (count($data) == 0) {
            echo ":( \n"; die;
        }

        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $sheet->setCellValue('A1', 'Passport PIN');

        // Populate the spreadsheet with data
        $row = 2; // Start from the second row
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item->profile->passport_pin);
            $row++;
        }

        if (!file_exists(\Yii::getAlias('@api/web/storage/export'))) {
            mkdir(\Yii::getAlias('@api/web/storage/export'), 0777, true);
        }

        $filePath = Yii::getAlias('@api/web/storage/export/'.$time.'.xlsx');

        // Create a writer to save the file
        $writer = new Xlsx($spreadsheet);

        // Save the file to the specified path
        $writer->save($filePath);
    }

    public function actionStdErr()
    {
        $inputFileName = __DIR__ . '/excels/tba.xlsx';
        $spreadsheet = IOFactory::load($inputFileName);
        $data = $spreadsheet->getActiveSheet()->toArray();

        foreach ($data as $key => $row) {

            if ($key != 0) {
                $jshr = $row[0];
                $seria = $row[1];
                $number = $row[2];
                if ($jshr == null) {
                    break;
                }

                $profile = Profile::findOne([
                    'passport_pin' => $jshr
                ]);
                if ($profile) {
                    $profile->passport_serial = $seria;
                    $profile->passport_number = $number;
                    $profile->save(false);
                }
            }
        }
    }


    public function actionGrName()
    {
        $groups = Group::find()
            ->all();
        foreach ($groups as $group) {
            $string = $group->unical_name;
            $string = str_replace('А', "A", $string);
            $string = str_replace('С', "C", $string);
            $string = str_replace('Т', "T", $string);
            $string = str_replace(' ', '', $string);
            $group->unical_name = $string;
            $group->save(false);
        }
    }


    public function actionRemoveTimetable()
    {
        $timeTableDates = TimetableDate::find()
            ->where([
                'faculty_id' => 2,
                'edu_form_id' => 1,
                'is_deleted' => 0
            ])
            ->andWhere(['>' , 'date' , '2024-05-19'])
            ->all();

        foreach ($timeTableDates as $timeTableDate) {
            TimetableAttend::updateAll(['is_deleted' => 5] , ['timetable_date_id' => $timeTableDate->id]);
            $timeTableDate->is_deleted = 5;
            $timeTableDate->save(false);
        }
    }

    public function actionSubjectSill($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $eduSemestrSubject = EduSemestrSubject::findOne($id);

        $marks = StudentMark::find()
            ->where([
                'edu_semestr_subject_id' => $eduSemestrSubject->id,
                'is_deleted' => 0
            ])->all();

        foreach ($marks as $mark) {
            $isExamType = EduSemestrExamsType::findOne([
                'id' => $mark->edu_semestr_exams_type_id,
                'status' => 1,
                'is_deleted' => 0
            ]);
            if (!$isExamType) {
                $mark->is_deleted = 1;
            } else {
                $mark->max_ball = $isExamType->max_ball;
            }
            $mark->save(false);
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
    }

    public function actionDomla($userId , $date)
    {
        $timeTableDates = TimetableDate::find()
            ->where([
                'user_id' => $userId,
                'attend_status' => 1,
                'status' => 1,
                'is_deleted' => 0
            ])
            ->andWhere(['<' , 'date' , $date])
            ->all();

        $data = [];
        if (count($timeTableDates) > 0) {
            foreach ($timeTableDates as $timeTableDate) {
                $data[] = date("Y-m-d H:i:s" , $timeTableDate->updated_at);
            }
            dd($data);
        } else {
            echo ":( \n";
        }
    }

    public function actionSirtqi()
    {
        $data = [];
        $timeTables = Timetable::find()
            ->where([
                'edu_form_id' => 2,
                'status' => 1,
                'is_deleted' => 0
            ])
            ->all();
        foreach ($timeTables as $timeTable) {
            if (count($timeTable->timeTableDate) > 1) {
                $data[] = $timeTable->id;
            }
        }
        
        dd($data);
    }

    public function actionEduPlanSubjectUpdate()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $eduSemestrSubjects = EduSemestrSubject::find()
            ->where(['is_deleted' => 0])
            ->all();

        foreach ($eduSemestrSubjects as $subject) {
            $studentGroups = StudentGroup::find()
                ->where([
                    'edu_semestr_id' => $subject->edu_semestr_id,
                    'status' => 1,
                    'is_deleted' => 0
                ])->all();

            foreach ($studentGroups as $model) {

                $eduSemestrSubjectExamTypes = $subject->eduSemestrExamsTypes;
                $studentSemestrSubject = new StudentSemestrSubject();
                $studentSemestrSubject->edu_plan_id = $model->edu_plan_id;
                $studentSemestrSubject->edu_semestr_id = $model->edu_semestr_id;
                $studentSemestrSubject->edu_semestr_subject_id = $subject->id;
                $studentSemestrSubject->student_id = $model->student_id;
                $studentSemestrSubject->student_user_id = $model->student->user_id;
                $studentSemestrSubject->faculty_id = $model->faculty_id;
                $studentSemestrSubject->direction_id = $model->direction_id;
                $studentSemestrSubject->edu_form_id = $model->edu_form_id;
                $studentSemestrSubject->edu_year_id = $model->edu_year_id;
                $studentSemestrSubject->course_id = $model->eduSemestr->course_id;
                $studentSemestrSubject->semestr_id = $model->semestr_id;
                if (!$studentSemestrSubject->validate()) {
                    $errors[] = [ 'studentSemestrSubject'];
                } else {
                    $studentSemestrSubject->save(false);
                    $studentVedomst = new StudentSemestrSubjectVedomst();
                    $studentVedomst->student_semestr_subject_id = $studentSemestrSubject->id;
                    $studentVedomst->subject_id = $subject->subject_id;
                    $studentVedomst->edu_year_id = $model->edu_year_id;
                    $studentVedomst->semestr_id = $model->semestr_id;
                    $studentVedomst->student_id = $model->student_id;
                    $studentVedomst->student_user_id = $studentSemestrSubject->student_user_id;
                    $studentVedomst->group_id = $model->group_id;
                    $studentVedomst->vedomst = 1;
                    if (!$studentVedomst->validate()) {
                        $errors[] = ['student Vedomst validate'];
                    } else {
                        $studentVedomst->save(false);
                        foreach ($eduSemestrSubjectExamTypes as $eduSemestrSubjectExamType) {
                            $studentMark = new StudentMark();
                            $studentMark->edu_semestr_exams_type_id = $eduSemestrSubjectExamType->id;
                            $studentMark->exam_type_id = $eduSemestrSubjectExamType->exams_type_id;
                            $studentMark->group_id = $model->group_id;
                            $studentMark->student_id = $model->student_id;
                            $studentMark->student_user_id = $studentVedomst->student_user_id;
                            $studentMark->max_ball = $eduSemestrSubjectExamType->max_ball;
                            $studentMark->edu_semestr_subject_id = $subject->id;
                            $studentMark->subject_id = $subject->subject_id;
                            $studentMark->edu_plan_id = $model->edu_plan_id;
                            $studentMark->edu_semestr_id = $model->edu_semestr_id;
                            $studentMark->faculty_id = $model->faculty_id;
                            $studentMark->direction_id = $model->direction_id;
                            $studentMark->semestr_id = $model->semestr_id;
                            $studentMark->course_id = $studentSemestrSubject->course_id;
                            $studentMark->vedomst = 1;
                            $studentMark->student_semestr_subject_vedomst_id = $studentVedomst->id;
                            if (!$studentMark->validate()) {
                                $errors[] = ['Student Mark Validate Errors'];
                            } else {
                                $studentMark->save(false);
                            }
                        }
                    }
                }
            }
        }


        if (count($errors) == 0) {
            $transaction->commit();
            echo "1111";
        } else {
            echo "2222";
        }
    }

    public function actionOldStudentGroup()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $students = Student::find()
            ->where([
                'is_deleted' => 0,
                'status' => 10
            ])
            ->all();

        foreach ($students as $student) {
            $descStudentGroup = StudentGroup::find()
                ->where(['student_id' => $student->id])
                ->orderBy('semestr_id desc')
                ->one();

            for ($i = 1; $i <= $descStudentGroup->semestr_id; $i++) {

                $isStudentGroup = StudentGroup::findOne([
                    'student_id' => $student->id,
                    'semestr_id' => $i,
                    'status' => 1,
                    'is_deleted' => 0
                ]);

                if ($isStudentGroup) {

                    $eduSemestr = EduSemestr::findOne([
                        'edu_plan_id' => $isStudentGroup->edu_plan_id,
                        'semestr_id' => $i,
                        'is_deleted' => 0
                    ]);
                    $eduSemestrSubjects = $eduSemestr->eduSemestrSubjects;

                    $result = self::studentSemestrSubject($isStudentGroup , $eduSemestrSubjects);
                    if (!$result['is_ok']) {
                        foreach ($result['errors'] as $err) {
                            $errors[] = $err;
                        }
                    }

                } else {

//                    $eduSemestr = EduSemestr::findOne([
//                        'edu_plan_id' => $descStudentGroup->edu_plan_id,
//                        'semestr_id' => $i,
//                        'is_deleted' => 0
//                    ]);
//                    $eduSemestrSubjects = $eduSemestr->eduSemestrSubjects;
//
//                    $studentGroup = new StudentGroup();
//                    $studentGroup->student_id = $student->id;
//                    $studentGroup->group_id = $student->group_id;
//                    $studentGroup->edu_year_id = $eduSemestr->edu_year_id;
//                    $studentGroup->edu_plan_id = $eduSemestr->edu_plan_id;
//                    $studentGroup->edu_semestr_id = $eduSemestr->id;
//                    $studentGroup->edu_form_id = $eduSemestr->edu_form_id;
//                    $studentGroup->semestr_id = $eduSemestr->semestr_id;
//                    $studentGroup->course_id = $eduSemestr->course_id;
//                    $studentGroup->faculty_id = $eduSemestr->faculty_id;
//                    $studentGroup->direction_id = $eduSemestr->direction_id;
//                    $studentGroup->save(false);
//
//                    $result = self::studentSemestrSubject($studentGroup , $eduSemestrSubjects);
//                    if (!$result['is_ok']) {
//                        foreach ($result['errors'] as $err) {
//                            $errors[] = $err;
//                        }
//                    }

                }

            }

        }



        if (count($errors) == 0) {
            $transaction->commit();
            echo "1111";
        } else {
            $transaction->rollBack();
            echo "2222 \n";
        }
    }



    public static function studentSemestrSubject($model , $eduSemestrSubjects)
    {
        $errors = [];
        foreach ($eduSemestrSubjects as $subject) {

            $isStudentSubject = StudentSemestrSubject::findOne([
                'edu_semestr_subject_id' => $subject->id,
                'student_id' => $model->student_id,
                'is_deleted' => 0
            ]);

            $eduSemestrSubjectExamTypes = $subject->eduSemestrExamsTypes;

            if (!$isStudentSubject) {
                $studentSemestrSubject = new StudentSemestrSubject();
                $studentSemestrSubject->edu_plan_id = $model->edu_plan_id;
                $studentSemestrSubject->edu_semestr_id = $model->edu_semestr_id;
                $studentSemestrSubject->edu_semestr_subject_id = $subject->id;
                $studentSemestrSubject->student_id = $model->student_id;
                $studentSemestrSubject->student_user_id = $model->student->user_id;
                $studentSemestrSubject->faculty_id = $model->faculty_id;
                $studentSemestrSubject->direction_id = $model->direction_id;
                $studentSemestrSubject->edu_form_id = $model->edu_form_id;
                $studentSemestrSubject->edu_year_id = $model->edu_year_id;
                $studentSemestrSubject->course_id = $model->eduSemestr->course_id;
                $studentSemestrSubject->semestr_id = $model->semestr_id;
                if (!$studentSemestrSubject->validate()) {
                    $errors[] = [ 'studentSemestrSubject'];
                } else {
                    $studentSemestrSubject->save(false);
                    $studentVedomst = new StudentSemestrSubjectVedomst();
                    $studentVedomst->student_semestr_subject_id = $studentSemestrSubject->id;
                    $studentVedomst->subject_id = $subject->subject_id;
                    $studentVedomst->edu_year_id = $model->edu_year_id;
                    $studentVedomst->semestr_id = $model->semestr_id;
                    $studentVedomst->student_id = $model->student_id;
                    $studentVedomst->student_user_id = $studentSemestrSubject->student_user_id;
                    $studentVedomst->group_id = $model->group_id;
                    $studentVedomst->vedomst = 1;
                    if (!$studentVedomst->validate()) {
                        $errors[] = ['student Vedomst validate'];
                    } else {
                        $studentVedomst->save(false);
                        foreach ($eduSemestrSubjectExamTypes as $eduSemestrSubjectExamType) {
                            $studentMark = new StudentMark();
                            $studentMark->edu_semestr_exams_type_id = $eduSemestrSubjectExamType->id;
                            $studentMark->exam_type_id = $eduSemestrSubjectExamType->exams_type_id;
                            $studentMark->group_id = $model->group_id;
                            $studentMark->student_id = $model->student_id;
                            $studentMark->student_user_id = $studentVedomst->student_user_id;
                            $studentMark->max_ball = $eduSemestrSubjectExamType->max_ball;
                            $studentMark->edu_semestr_subject_id = $subject->id;
                            $studentMark->subject_id = $subject->subject_id;
                            $studentMark->edu_plan_id = $model->edu_plan_id;
                            $studentMark->edu_semestr_id = $model->edu_semestr_id;
                            $studentMark->faculty_id = $model->faculty_id;
                            $studentMark->direction_id = $model->direction_id;
                            $studentMark->semestr_id = $model->semestr_id;
                            $studentMark->course_id = $studentSemestrSubject->course_id;
                            $studentMark->vedomst = 1;
                            $studentMark->student_semestr_subject_vedomst_id = $studentVedomst->id;
                            if (!$studentMark->validate()) {
                                $errors[] = ['Student Mark Validate Errors'];
                            } else {
                                $studentMark->save(false);
                            }
                        }
                    }
                }

            } else {

                $queryVedomst = StudentSemestrSubjectVedomst::findOne([
                    'subject_id' => $subject->subject_id,
                    'student_semestr_subject_id' => $isStudentSubject->id,
                    'is_deleted' => 0,
                    'vedomst' => 1,
                ]);

                foreach ($eduSemestrSubjectExamTypes as $eduSemestrSubjectExamType) {
                    $qStudentMark = StudentMark::findOne([
                        'student_semestr_subject_vedomst_id' => $queryVedomst->id,
                        'edu_semestr_exams_type_id' => $eduSemestrSubjectExamType->id,
                        'is_deleted' => 0,
                        'vedomst' => 1
                    ]);
                    if (!$qStudentMark) {
                        $studentMark = new StudentMark();
                        $studentMark->edu_semestr_exams_type_id = $eduSemestrSubjectExamType->id;
                        $studentMark->exam_type_id = $eduSemestrSubjectExamType->exams_type_id;
                        $studentMark->group_id = $model->group_id;
                        $studentMark->student_id = $model->student_id;
                        $studentMark->student_user_id = $queryVedomst->student_user_id;
                        $studentMark->max_ball = $eduSemestrSubjectExamType->max_ball;
                        $studentMark->edu_semestr_subject_id = $subject->id;
                        $studentMark->subject_id = $subject->subject_id;
                        $studentMark->edu_plan_id = $model->edu_plan_id;
                        $studentMark->edu_semestr_id = $model->edu_semestr_id;
                        $studentMark->faculty_id = $model->faculty_id;
                        $studentMark->direction_id = $model->direction_id;
                        $studentMark->semestr_id = $model->semestr_id;
                        $studentMark->course_id = $isStudentSubject->course_id;
                        $studentMark->vedomst = 1;
                        $studentMark->student_semestr_subject_vedomst_id = $queryVedomst->id;
                        if (!$studentMark->validate()) {
                            $errors[] = ['Student Mark Validate Errors'];
                        } else {
                            $studentMark->save(false);
                        }
                    }
                }


            }

        }
        if (count($errors) == 0) {
            return ['is_ok' => true];
        }
        return ['is_ok' => false , 'errors' => $errors];
    }


    public function actionEduPlanSubjectDelete()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $eduSemestrSubjects = EduSemestrSubject::find()
            ->where(['id' => [1543]])
            ->all();

        foreach ($eduSemestrSubjects as $subject) {

            $stdSubs = StudentSemestrSubject::find()
                ->where([
                    'edu_semestr_id' => $subject->edu_semestr_id, 'edu_semestr_subject_id' => $subject->id, 'is_deleted' => 0
                ])->all();

            foreach ($stdSubs as $v1) {
                $v1->is_deleted = 1;
                $v1->save(false);

                $stdVedoms = StudentSemestrSubjectVedomst::find()
                    ->where([
                        'student_semestr_subject_id' => $v1->id, 'is_deleted' => 0
                    ])->all();

                foreach ($stdVedoms as $t) {
                    $t->is_deleted = 1;
                    $t->save(false);
                    StudentMark::updateAll(['is_deleted' => 1] , ['student_semestr_subject_vedomst_id' => $t->id, 'is_deleted' => 0]);
                }
            }
        }

        if (count($errors) == 0) {
            $transaction->commit();
            echo "1111";
        } else {
            echo "2222";
        }
    }


    public function actionStdAt()
    {
        $studentA = TimetableAttend::find()
            ->where(['student_id' => 1771])
            ->all();

        foreach ($studentA as $v) {
            $v->timetable_reason_id = null;
            $v->reason = 0;
            $v->save(false);
        }
    }


    public function actionSemestrKey()
    {
        $students = StudentGroup::find()
            ->where([
                'status' => 1,
                'is_deleted' => 0
            ])
            ->all();
        foreach ($students as $student) {
            $student->update(false);
        }
    }

    public function actionStdAttend()
    {
        $students = Student::find()
            ->where(['status' => 10 , 'is_deleted' => 0])
            ->all();
        foreach ($students as $student) {
            $query = TimetableAttend::find()
                ->where([
                    'student_id' => $student->id,
                    'status' => 1,
                    'is_deleted' => 0
                ])->all();
            if (count($query) > 0) {
                foreach ($query as $value) {
                    if ($value->group_id != $student->group_id) {
                        $value->status = 0;
                        $value->is_deleted = 1;
                        $value->save(false);
                    }
                }
            }
        }
    }

    public function actionStdType()
    {
        $students = Student::find()
            ->where(['status' => 10 , 'is_deleted' => 0])
            ->all();

        $data = 1;
        foreach ($students as $student) {

            $timeTables = Timetable::find()
                ->where([
                    'group_id' => $student->group_id,
                    'edu_year_id' => $student->edu_year_id,
                    'two_group' => 1,
                    'group_type' => 1,
                    'status' => 1,
                    'is_deleted' => 0
                ])->all();

            if (count($timeTables) > 0) {
                foreach ($timeTables as $timeTable) {
                    $query = TimetableStudent::findOne([
                        'ids_id' => $timeTable->ids,
                        'group_id' => $student->group_id,
                        'student_id' => $student->id,
                        'status' => 1,
                        'is_deleted' => 0
                    ]);
                    if (!$query) {
                        $newTimetableStudent = new TimetableStudent();
                        $newTimetableStudent->ids_id = $timeTable->ids;
                        $newTimetableStudent->group_id = $student->group_id;
                        $newTimetableStudent->student_id = $student->id;
                        $newTimetableStudent->student_user_id = $student->user_id;
                        $newTimetableStudent->group_type = 1;
                        $newTimetableStudent->save(false);
                        $data++;
                    }
                }
            }
        }

        dd($data);

    }


    public function actionMstd()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $students = Student::find()
            ->where([
                'is_deleted' => 0,
                'status' => 10
            ])
            ->all();
        
        foreach ($students as $student) {
            $querys = StudentSemestrSubjectVedomst::find()
                ->where([
                    'student_id' => $student->id,
                    'edu_year_id' => $student->edu_year_id,
                    'status' => 1,
                    'is_deleted' => 0
                ])->all();
            if (count($querys) > 0) {
                foreach ($querys as $query) {
                    if ($query->group_id != $student->group_id) {
                        $query->group_id = $student->group_id;
                        $query->save(false);

                        $studentMarks = $query->studentMark;
                        foreach ($studentMarks as $studentMark) {
                            $studentMark->group_id = $student->group_id;
                            $studentMark->save(false);
                        }

                    }
                }
            }
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
    }

    public function actionAtStd($facultyId , $date , $padaId)
    {
        if ($padaId != null) {
            $query = TimetableAttend::find()
                ->where([
                    'faculty_id' => $facultyId,
                    'date' => $date,
                    'para_id' => $padaId
                ])->groupBy('student_id')->count();
        } else {
            $query = TimetableAttend::find()
                ->where([
                    'faculty_id' => $facultyId,
                    'date' => $date
                ])->groupBy('student_id')->count();
        }

        echo $query."\n";
    }


    public function actionAtPara()
    {
        $query = TimetableAttend::find()
            ->all();
        foreach ($query as $t) {
            $tdate = $t->timeTableDate;
            $t->para_id = $tdate->para_id;
            $t->save(false);
        }
    }

    public function actionAsd()
    {
        $timeTableDates = TimetableDate::find()
            ->where([
                'status' => 1,
                'is_deleted' => 1
            ])->all();
        foreach ($timeTableDates as $timeTableDate) {
            $timeTableDate->status = 0;
            $timeTableDate->save(false);
        }
    }

    public function actionHoliday()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $startDate = "2024-05-09";
//        $endDate = "2024-05-12";

        $timeTableDates = TimetableDate::find()
            ->where([
                'status' => 1,
                'is_deleted' => 0
            ])
            ->andWhere(['=' , 'date' , $startDate])
//            ->andWhere(['edu_plan_id' => [57, 58, 59]])
//            ->andWhere(['between', 'date', new Expression('DATE(:start_date)'), new Expression('DATE(:end_date)')])
//            ->params([':start_date' => $startDate, ':end_date' => $endDate])
            ->all();

        if (count($timeTableDates) > 0) {
            foreach ($timeTableDates as $timeTableDate) {
                $query = TimetableDate::find()
                    ->where([
                        'ids_id' => $timeTableDate->ids_id,
                        'group_id' => $timeTableDate->group_id,
                        'para_id' => $timeTableDate->para_id,
                        'group_type' => $timeTableDate->group_type,
                        'two_group' => $timeTableDate->two_group,
                        'status' => 1,
                        'is_deleted' => 0
                    ])->orderBy('date desc')
                    ->one();
                if ($query) {
                    $dateFrom = new \DateTime($query->date);
                    if ($query->type == 0) {
                        $dateFrom->modify('+1 week');
                    } else {
                        $dateFrom->modify('+2 week');
                    }

                    $new = new TimetableDate();
                    $new->timetable_id = $query->timetable_id;
                    $new->ids_id = $query->ids_id;
                    $new->date = $dateFrom->format('Y-m-d');
                    $new->building_id = $query->building_id;
                    $new->room_id = $query->room_id;
                    $new->week_id = $query->week_id;
                    $new->para_id = $query->para_id;
                    $new->group_id = $query->group_id;
                    $new->edu_semestr_subject_id = $query->edu_semestr_subject_id;
                    $new->teacher_access_id = $query->teacher_access_id;
                    $new->user_id = $query->user_id;
                    $new->subject_id = $query->subject_id;
                    $new->subject_category_id = $query->subject_category_id;
                    $new->edu_plan_id = $query->edu_plan_id;
                    $new->edu_semestr_id = $query->edu_semestr_id;
                    $new->edu_form_id  = $query->edu_form_id;
                    $new->edu_year_id  = $query->edu_year_id;
                    $new->edu_type_id  = $query->edu_type_id;
                    $new->faculty_id  = $query->faculty_id;
                    $new->direction_id  = $query->direction_id;
                    $new->semestr_id  = $query->semestr_id;
                    $new->course_id  = $query->course_id;
                    $new->group_type  = $query->group_type;
                    $new->two_group  = $query->two_group;
                    $new->type = $query->type;
                    $new->save(false);

                    $timeTableDate->status = 0;
                    $timeTableDate->is_deleted = 1;
                    $timeTableDate->save(false);
                }
            }
        }


        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
    }


    public function actionReason()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $atReasons = TimetableReason::find()->all();

        foreach ($atReasons as $atReason) {
            $dates = TimetableAttend::find()
                ->where([
                    'student_id' => $atReason->student_id,
                    'status' => 1,
                    'is_deleted' => 0
                ])->all();

            if (count($dates) > 0) {
                foreach ($dates as $date) {
                    $paraTime = strtotime($date->date. " ".$date->timeTableDate->para->start_time);
                    if (strtotime($atReason->start) <= $paraTime && strtotime($atReason->end) >= $paraTime) {
                        $date->timetable_reason_id = $atReason->id;
                        $date->reason = 1;
                        $date->save(false);
                    }
                }
            }
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
    }


    public function actionEduYear()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $students= Student::find()
            ->where(['status' => 10, 'is_deleted' => 0 , 'edu_form_id' => 2])
            ->all();

        foreach ($students as $student) {
            $eduSemestr = $student->eduPlan->activeSemestr;
            $student->edu_year_id = $eduSemestr->edu_year_id;
            $student->course_id = $eduSemestr->course_id;
            $student->save(false);
            $studentGroup = StudentGroup::findOne([
                'student_id' => $student->id,
                'status' => 1,
                'is_deleted' => 0
            ]);
            if ($studentGroup->edu_year_id != 8) {
                StudentMark::deleteAll(['student_id' => $student->id]);
                StudentSemestrSubjectVedomst::deleteAll(['student_id' => $student->id]);
                StudentSemestrSubject::deleteAll(['student_id' => $student->id]);

                $studentGroup->edu_semestr_id = $eduSemestr->id;
                $studentGroup->semestr_id = $eduSemestr->semestr_id;
                $studentGroup->course_id = $eduSemestr->course_id;
                $studentGroup->edu_year_id = $eduSemestr->edu_year_id;
                $studentGroup->save(false);

                $eduSemestrSubject = $eduSemestr->eduSemestrSubjects;

                $result = SemestrUpdate::new($studentGroup , $eduSemestrSubject);
                if (!$result['is_ok']) {
                    $errors[] = $studentGroup->id . " Student Subject create errors!";
                    echo $studentGroup->id . " Student Subject create errors! \n";
                    break;
                }
            }
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
    }


    public function actionStudentForm()
    {
        $students = Student::find()
            ->where([
                'edu_form_id' => 2
            ])->all();



        foreach ($students as $student) {
            $eduPlan = $student->eduPlan->activeSemestr;
            $student->course_id = $eduPlan->course_id;
            $student->save(false);
        }
    }


    public function actionTimetableDateDelete()
    {
        $timeA = TimetableDate::find()
            ->where(['date' => '2024-06-18' , 'status' => 1, 'is_deleted' => 0])
            ->all();

        foreach ($timeA as $item) {
            $item->is_deleted = 31;
            $item->save(false);
        }
    }

    public function actionBug()
    {
        $timetables = Timetable::find()
            ->where([
                'status' => 1,
                'is_deleted' => 0
            ])
            ->groupBy('ids')
            ->all();

        $data = [];
        foreach ($timetables as $timetable) {
            $querys = Timetable::find()
                ->where([
                    'ids' => $timetable->ids,
                    'status' => 1,
                    'is_deleted' => 0
                ])->all();
            if (count($querys) > 1) {
                foreach ($querys as $query) {
                    if (!($query->subject_id == $timetable->subject_id && $query->subject_category_id == $timetable->subject_category_id)) {
                        $data[] = $query->id;
                    } else {
                        $subQuery = Timetable::find()
                            ->where([
                                'ids' => $timetable->ids,
                                'status' => 1,
                                'is_deleted' => 0
                            ])
                            ->andWhere(['<>' , 'subject_category_id' , 1])
                            ->all();
                        if (count($subQuery) > 1) {
                            if (count($subQuery) == 2) {
                                $t = false;
                                $r = false;
                                foreach ($subQuery as $v) {
                                    if ($v->group_type == 1) {
                                        $t = true;
                                    }
                                    if ($v->group_type == 2) {
                                        $r = true;
                                    }
                                }
                                if (!($t && $r)) {
                                    $data[] = $query->id;
                                }
                            } else {
                                $data[] = $query->id;
                            }
                        }
                    }
                }
            }
        }

        dd($data);
    }












    public function actionStd()
    {
        $timetables = Timetable::find()
            ->where([
                'two_group' => 1,
                'group_type' => 2,
                'status' => 1,
                'is_deleted' => 0
            ])->all();

        foreach ($timetables as $timetable) {
            $stds = TimetableStudent::find()
                ->where(['ids_id' => $timetable->ids])->all();
            if (count($stds) > 0) {
                foreach ($stds as $std) {
                    $std->is_deleted = 1;
                    $std->save(false);
                }
            }
        }
    }

    public function actionTime()
    {
        $querys = Timetable::find()
            ->where(['two_group' => 1, 'status' => 1, 'is_deleted' => 0 , 'group_type' => 1])
            ->all();
        foreach ($querys as $query) {
            $students = StudentGroup::find()->where([
                'edu_semestr_id' => $query->edu_semestr_id,
                'group_id' => $query->group_id,
                'is_deleted' => 0
            ])->all();
            if (count($students) > 0) {
                foreach ($students as $student) {
                    $is = TimetableStudent::findOne([
                        'student_id' => $student->student_id,
                        'ids_id' => $query->ids,
                        'status' => 1,
                        'is_deleted' => 0
                    ]);
                    if (!$is) {
                        $new = new TimetableStudent();
                        $new->ids_id = $query->ids;
                        $new->group_id = $student->group_id;
                        $new->student_id = $student->student_id;
                        $new->student_user_id = $student->student->user_id;
                        $new->save(false);
                    }
                }
            }
        }
    }

    public function actionStudentDel()
    {
        $students = Student::find()
            ->where([
                'is_deleted' => 1
            ])
            ->all();
        foreach ($students as $student) {
            $profiles = Profile::find()->where(['user_id' => $student->user_id])->all();
            if (count($profiles) > 0) {
                foreach ($profiles as $profile) {
                    $profile->delete();
                }
            }

            $pass = PasswordEncrypts::find()->where(['user_id' => $student->user_id])->all();
            if (count($pass) > 0) {
                foreach ($pass as $pas) {
                    $pas->delete();
                }
            }
            $studentMarks = StudentMark::find()->where(['student_id' => $student->id])->all();
            if (count($studentMarks) > 0) {
                foreach ($studentMarks as $studentMark) {
                    $studentMark->delete();
                }
            }

            $studentVedomsts = StudentSemestrSubjectVedomst::find()->where(['student_id' => $student->id])->all();
            if (count($studentVedomsts) > 0) {
                foreach ($studentVedomsts as $studentVedomst) {
                    $studentVedomst->delete();
                }
            }
            $studentSemestrs = StudentSemestrSubject::find()->where(['student_id' => $student->id])->all();
            if (count($studentSemestrs) > 0) {
                foreach ($studentSemestrs as $studentSemestr) {
                    $studentSemestr->delete();
                }
            }
            $attens = StudentAttend::find()->where(['student_id' => $student->id])->all();
            if (count($attens) > 0) {
                foreach ($attens as $atten) {
                    $atten->delete();
                }
            }

            $attens = StudentGroup::find()->where(['student_id' => $student->id])->all();
            if (count($attens) > 0) {
                foreach ($attens as $atten) {
                    $atten->delete();
                }
            }
            $attens = StudentTopicPermission::find()->where(['student_id' => $student->id])->all();
            if (count($attens) > 0) {
                foreach ($attens as $atten) {
                    $atten->delete();
                }
            }

            $user = User::findOne($student->user_id);
            $student->delete();
            $user->delete();
        }
    }

    public function actionStudentVedomst()
    {
        $students = Student::find()
            ->where(['status' => 10])
            ->all();
        foreach ($students as $student) {
            $studentSemestrSubjects = EduSemestrSubject::find()
                ->where(['edu_semestr_id' => $student->activeSemestr->id , 'status' => 1, 'is_deleted' => 0])->all();

        }
    }

    public function actionQ()
    {
        $students = Student::find()
            ->where(['<>' , 'course_id' , 1])
            ->all();
        $data = [];
        foreach ($students as $student) {
            $studentGr = StudentGroup::find()->where(['student_id' => $student->id])->count();
            if ($studentGr != 2) {
                $data[] = $student->id;
            }
        }
        dd($data);
    }

    public function actionF()
    {
        $students = StudentSemestrSubject::find()
            ->where(['course_id' => 2, 'semestr_id' => 4, 'created_by' => 0])
            ->all();
        foreach ($students as $student) {
            $studentVed = StudentSemestrSubjectVedomst::find()
                ->where([
                    'student_semestr_subject_id' => $student->id,
                ])
                ->all();
            foreach ($studentVed as $item) {
                $studentMark = StudentMark::find()
                    ->where([
                        'student_semestr_subject_vedomst_id' => $item->id,
                    ])
                    ->all();
                if (count($studentMark) > 0) {
                    foreach ($studentMark as $v) {
                        $v->delete();
                    }
                }
                $item->delete();
            }
            $student->delete();
        }
    }

    public function actionW()
    {
        $timetables = Timetable::find()
            ->where([
                'status' => 1,
                'is_deleted' => 0,
                'two_group' => 1,
                'group_type' => 1
            ])->all();
        foreach ($timetables as $timetable) {
            $students = StudentGroup::find()
                ->where([
                    'edu_semestr_id' => $timetable->edu_semestr_id,
                    'group_id' => $timetable->group_id,
                    'status' => 1,
                    'is_deleted' => 0
                ])->all();
            if (count($students) > 0) {
                foreach ($students as $student) {
                    $new = new TimetableStudent();
                    $new->ids_id = $timetable->ids;
                    $new->group_id = $timetable->group_id;
                    $new->student_id = $student->student_id;
                    $new->student_user_id = $student->student->user_id;
                    $new->group_type = 1;
                    $new->save(false);
                }
            }
        }
    }


    public function actionFinalIsDeleted()
    {
        $q = FinalExam::find()->where(['is_deleted' => 1])->all();
        foreach ($q as $v) {
            FinalExamGroup::updateAll(['is_deleted' => 1], ['final_exam_id' => $v->id]);
        }
    }


    public function actionFgs()
    {
        $query = FinalExamGroup::find()->all();
        foreach ($query as $item) {
            $item->subject_id = $item->eduSemestrSubject->subject_id;
            $item->save(false);
        }
    }

    public function actionSubjectVedomst1()
    {
        $subjects = EduSemestrSubject::find()
            ->where([
                'status' => 1,
                'is_deleted' => 0,
            ])
            ->all();
        foreach ($subjects as $subject) {
            for ($i = 1; $i <= 3; $i++) {
                $vedomst = SubjectVedomst::findOne([
                    'edu_semestr_subject_id' => $subject->id,
                    'edu_semestr_id' => $subject->edu_semestr_id,
                    'edu_plan_id' => $subject->eduSemestr->edu_plan_id,
                    'type' => $i
                ]);
                if (!$vedomst) {
                    $new = new SubjectVedomst();
                    $new->edu_semestr_subject_id = $subject->id;
                    $new->edu_semestr_id = $subject->edu_semestr_id;
                    $new->edu_plan_id = $subject->eduSemestr->edu_plan_id;
                    $new->type = $i;
                    $new->save(false);
                }
            }
        }
    }

    public function actionStudentSubject()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $subjects = EduSemestrSubject::find()
            ->where(['status' => 1, 'is_deleted' => 0])
            ->all();

        foreach ($subjects as $subject) {
            $groups = $subject->eduSemestr->eduPlan->group;
            $eduSemestrExamsTypes = $subject->eduSemestrExamsTypes;
            if (count($groups) > 0) {
                foreach ($groups as $group) {
                    $students = $group->student;
                    if (count($students) > 0) {
                        foreach ($students as $student) {
                            $query = StudentSemestrSubject::find()
                                ->where([
                                    'edu_semestr_subject_id' => $subject->id,
                                    'student_id' => $student->id,
                                    'is_deleted' => 0
                                ])->exists();
                            if (!$query) {
                                $new = new StudentSemestrSubject();
                                $new->edu_semestr_subject_id = $subject->id;
                                $new->edu_semestr_id = $subject->edu_semestr_id;
                                $new->edu_plan_id = $subject->eduSemestr->edu_plan_id;
                                $new->student_id = $student->id;
                                $new->student_user_id = $student->user_id;
                                $new->faculty_id = $subject->eduSemestr->faculty_id;
                                $new->direction_id = $subject->eduSemestr->direction_id;
                                $new->edu_form_id = $subject->eduSemestr->edu_form_id;
                                $new->edu_year_id = $subject->eduSemestr->edu_year_id;
                                $new->course_id = $subject->eduSemestr->course_id;
                                $new->semestr_id = $subject->eduSemestr->semestr_id;
                                if (!$new->validate()) {
                                    $errors[] = $new->errors;
                                    dd($errors);
                                } else {
                                    $new->save(false);
                                    $newStdVedomst = new StudentSemestrSubjectVedomst();
                                    $newStdVedomst->student_semestr_subject_id = $new->id;
                                    $newStdVedomst->subject_id  = $subject->subject_id;
                                    $newStdVedomst->edu_year_id  = $new->edu_year_id;
                                    $newStdVedomst->semestr_id  = $new->semestr_id;
                                    $newStdVedomst->student_id  = $student->id;
                                    $newStdVedomst->student_user_id  = $student->user_id;
                                    $newStdVedomst->group_id  = $student->group_id;
                                    $newStdVedomst->vedomst  = 1;
                                    $newStdVedomst->save(false);

                                    if (count($eduSemestrExamsTypes) > 0) {
                                        foreach ($eduSemestrExamsTypes as $eduSemestrExamsType) {
                                            $queryStudentMark = StudentMark::findOne([
                                                'edu_semestr_exams_type_id' => $eduSemestrExamsType->id,
                                                'student_id' => $student->id,
                                                'is_deleted' => 0
                                            ]);
                                            if (!$queryStudentMark) {
                                                $newStudentMark = new StudentMark();
                                                $newStudentMark->edu_semestr_exams_type_id = $eduSemestrExamsType->id;
                                                $newStudentMark->exam_type_id = $eduSemestrExamsType->exams_type_id;
                                                $newStudentMark->group_id = $student->group_id;
                                                $newStudentMark->student_id = $student->id;
                                                $newStudentMark->student_user_id = $student->user_id;
                                                $newStudentMark->ball = 0;
                                                $newStudentMark->max_ball = $eduSemestrExamsType->max_ball;
                                                $newStudentMark->edu_semestr_subject_id = $subject->id;
                                                $newStudentMark->subject_id = $subject->subject_id;
                                                $newStudentMark->edu_plan_id = $subject->eduSemestr->edu_plan_id;
                                                $newStudentMark->edu_semestr_id = $subject->edu_semestr_id;
                                                $newStudentMark->faculty_id = $subject->eduSemestr->faculty_id;
                                                $newStudentMark->direction_id = $subject->eduSemestr->direction_id;
                                                $newStudentMark->semestr_id = $subject->eduSemestr->semestr_id;
                                                $newStudentMark->course_id = $subject->eduSemestr->course_id;
                                                $newStudentMark->vedomst = 1;
                                                $newStudentMark->status = 1;
                                                $newStudentMark->student_semestr_subject_vedomst_id = $newStdVedomst->id;
                                                $newStudentMark->save(false);
                                            } else {
                                                $queryStudentMark->vedomst = 1;
                                                $queryStudentMark->student_semestr_subject_vedomst_id = $newStdVedomst->id;
                                                $queryStudentMark->save(false);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        dd($errors);
    }

    public function actionStudentMarkDelete()
    {
        $query1 = StudentMarkHistory::find()
            ->all();
        foreach ($query1 as $v) {
            $v->delete();
        }

        $query = StudentMark::find()
            ->where(['<>' , 'is_deleted', 0])
            ->all();
        foreach ($query as $item) {
            $item->delete();
        }
    }

    public function actionStudentMarkVedomstDelete()
    {
        $query = StudentMark::find()
            ->where([
                'exam_type_id' => 3,
                'vedomst' => [2,3]
            ])
            ->all();

        foreach ($query as $v) {
            $v->delete();
        }
    }

    public function actionStudentMarkVedomstNullDelete()
    {
        $query = StudentMark::find()
            ->where([
                'vedomst' => null
            ])
            ->all();

        foreach ($query as $v) {
            $v->delete();
        }
    }

    public function actionStudentMarkIssed()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $subjects = EduSemestrSubject::find()
            ->where(['status' => 1, 'is_deleted' => 0])
            ->all();
        foreach ($subjects as $subject) {
            $groups = $subject->eduSemestr->eduPlan->group;

            $examTypes = EduSemestrExamsType::find()
                ->where([
                    'edu_semestr_subject_id' => $subject->id,
                    'status' => 1,
                    'is_deleted' => 0
                ])
                ->all();
            if (count($groups) > 0) {
                foreach ($groups as $group) {
                    $students = $group->student;
                    if (count($students) > 0) {
                        foreach ($students as $student) {
                            $studentMark = StudentMark::find()
                                ->where([
                                    'edu_semestr_subject_id' => $subject->id,
                                    'student_id' => $student->id,
                                    'is_deleted' => 0,
                                    'vedomst' => 1
                                ])
                                ->all();
                            if (count($examTypes) != count($studentMark)) {
                                $errors[] = $student->id."--".$subject->id;
                            }
                        }
                    }
                }
            }
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        dd($errors);
    }

    public function actionStudentMarkBall()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $studentMark = StudentMark::find()
            ->where([
                'exam_type_id' => 3,
                'is_deleted' => 0,
                'vedomst' => 1
            ])->all();
        foreach ($studentMark as $value) {
            if ($value->ball > 0) {
                $value->attend = 1;
                $value->status = 2;
                $value->save(false);
            } else {
                $value->attend = 0;
                $value->status = 2;
                $value->save(false);
            }
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        dd($errors);
    }

    public function actionMarkBall()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $qq = StudentMark::find()
            ->where([
                'vedomst' => 2
            ])
            ->all();
        foreach ($qq as $r) {
            $r->delete();
        }

        $subjects = EduSemestrSubject::find()
            ->where(['status' => 1, 'is_deleted' => 0])
            ->all();
        foreach ($subjects as $subject) {
            $groups = $subject->eduSemestr->eduPlan->group;
            $examTypes = EduSemestrExamsType::find()
                ->where([
                    'edu_semestr_subject_id' => $subject->id,
                    'status' => 1,
                    'is_deleted' => 0
                ])
                ->all();
            if (count($groups) > 0) {
                foreach ($groups as $group) {
                    $students = $group->student;
                    if (count($students) > 0) {
                        foreach ($students as $student) {
                            $yak = 0;
                            $ball = 0;
                            $studentMark = StudentMark::find()
                                ->where([
                                    'edu_semestr_subject_id' => $subject->id,
                                    'student_id' => $student->id,
                                    'is_deleted' => 0,
                                    'vedomst' => 1
                                ])
                                ->all();
                            foreach ($studentMark as $value) {
                                if ($value->exam_type_id != 3) {
                                    $ball = $ball + $value->ball;
                                } else {
                                    $yak = $value->ball;
                                }
                            }

                            $studentMark1 = StudentMark::find()
                                ->where([
                                    'edu_semestr_subject_id' => $subject->id,
                                    'exam_type_id' => 3,
                                    'student_id' => $student->id,
                                    'is_deleted' => 0,
                                    'vedomst' => 1
                                ])->one();
                            if ($ball > 41 && $yak > 17) {
                                $studentMark1->passed = 1;
                                $studentMark1->save(false);
                            } else {
                                $studentMark1->passed = 2;
                                $studentMark1->save(false);
                                foreach ($examTypes as $examType) {
                                    if ($examType->exams_type_id == 3) {
                                        $mark = new StudentMark();
                                        $mark->edu_semestr_exams_type_id = $examType->id;
                                        $mark->exam_type_id = $examType->exams_type_id;
                                        $mark->group_id = $student->group_id;
                                        $mark->student_id = $student->id;
                                        $mark->student_user_id = $student->user_id;
                                        $mark->ball = 0;
                                        $mark->max_ball = $examType->max_ball;
                                        $mark->edu_semestr_subject_id = $subject->id;
                                        $mark->subject_id = $subject->subject_id;
                                        $mark->edu_semestr_id = $subject->edu_semestr_id;
                                        $mark->edu_plan_id = $subject->eduSemestr->edu_plan_id;
                                        $mark->faculty_id = $subject->eduSemestr->faculty_id;
                                        $mark->direction_id = $subject->eduSemestr->direction_id;
                                        $mark->semestr_id = $subject->eduSemestr->semestr_id;
                                        $mark->course_id = $subject->eduSemestr->course_id;
                                        $mark->vedomst = 2;
                                        $mark->save(false);
                                    } else {
                                        $studentMark2 = StudentMark::findOne([
                                            'edu_semestr_subject_id' => $subject->id,
                                            'exam_type_id' => $examType->exams_type_id,
                                            'student_id' => $student->id,
                                            'is_deleted' => 0,
                                            'vedomst' => 1
                                        ]);
                                        $mark = new StudentMark();
                                        $mark->edu_semestr_exams_type_id = $studentMark2->edu_semestr_exams_type_id;
                                        $mark->exam_type_id = $studentMark2->exam_type_id;
                                        $mark->group_id = $studentMark2->group_id;
                                        $mark->student_id = $studentMark2->student_id;
                                        $mark->student_user_id = $studentMark2->student_user_id;
                                        $mark->ball = $studentMark2->ball;
                                        $mark->max_ball = $studentMark2->max_ball;
                                        $mark->edu_semestr_subject_id = $studentMark2->edu_semestr_subject_id;
                                        $mark->subject_id = $studentMark2->subject_id;
                                        $mark->edu_plan_id = $studentMark2->edu_plan_id;
                                        $mark->edu_semestr_id = $studentMark2->edu_semestr_id;
                                        $mark->faculty_id = $studentMark2->faculty_id;
                                        $mark->direction_id = $studentMark2->direction_id;
                                        $mark->semestr_id = $studentMark2->semestr_id;
                                        $mark->course_id = $studentMark2->course_id;
                                        $mark->vedomst = 2;
                                        $mark->save(false);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }


        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        dd($errors);
    }
}