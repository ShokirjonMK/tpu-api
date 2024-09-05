<?php

namespace api\controllers;

use common\models\model\SubjectContent;
use common\models\model\SubjectTopic;
use Yii;
use base\ResponseStatus;


class SubjectContentController extends ApiActiveController
{
    public $modelClass = 'api\resources\SubjectTopic';

    public function actions()
    {
        return [];
    }

    public $table_name = 'subject_content';
    public $controller_name = 'SubjectContent';

    public function actionTypes($lang , $key = null)
    {
        $model = new SubjectContent();
        return $model->typesArray($key);
    }

    public function actionTrash($lang)
    {
        $model = new SubjectContent();

        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 1]);

        if (isRole('teacher') && !isRole('mudir')) {
            $query->andWhere([$this->table_name . '.created_by' => current_user_id()]);
        }

        if (Yii::$app->request->get('user_id') != null) {
            $query->andWhere([$this->table_name . '.created_by' => Yii::$app->request->get('user_id')]);
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data = $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionIndex($lang)
    {
        $model = new SubjectContent();

        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 0]);

        if (isRole('teacher') && (!isRole('mudir') && !isRole('contenter'))) {
            $query->andWhere([$this->table_name . '.created_by' => current_user_id()]);
        }

        if (Yii::$app->request->get('user_id') != null) {
            $query->andWhere([$this->table_name . '.created_by' => Yii::$app->request->get('user_id')]);
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data = $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new SubjectContent();
        $post = Yii::$app->request->post();

        $this->load($model, $post);

        $result = SubjectContent::createItem($model, $post);
        // return $result;
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = SubjectContent::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();

        if (isset($post['order'])) {
            if ($post['order'] <= 0) {
                return $this->response(0, _e('The minimum value of the order should not be less than zero'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
            } else {
                if ($post['order'] < $model->order) {
                    $orderUpdate = SubjectContent::find()->where([
                        'between', 'order', $post['order'], $model->order-1
                    ])
                        ->andWhere([ 'subject_topic_id' => $model->subject_topic_id , 'is_deleted' => 0 ])
                        ->all();
                    if (isset($orderUpdate)) {
                        foreach ($orderUpdate as $orderItem) {
                            $orderItem->order = $orderItem->order + 1;
                            $orderItem->save(false);
                        }
                    }
                }
                if ($post['order'] > $model->order) {
                    $orderUpdate = SubjectContent::find()->where([
                        'between', 'order', $model->order+1, $post['order']
                    ])
                        ->andWhere([ 'subject_topic_id' => $model->subject_topic_id ,'is_deleted' => 0])
                        ->all();
                    if (isset($orderUpdate)) {
                        foreach ($orderUpdate as $orderItem) {
                            $orderItem->order = $orderItem->order - 1;
                            $orderItem->save(false);
                        }
                    }
                }
            }
        }

        $this->load($model, $post);
        $result = SubjectContent::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = SubjectContent::find()
            ->andWhere(['id' => $id])
            //            ->andWhere(['is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }


        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = SubjectContent::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {
            $model->is_deleted = 1;
            if ($model->save()) {
                $order = SubjectContent::find()->where([
                    '>' , 'order', $model->order
                ])
                    ->andWhere(['subject_topic_id'=> $model->subject_topic_id])
                    ->all();
                foreach ($order as $order_item) {
                    $order_item->order = $order_item->order - 1;
                    $order_item->save();
                }
            }

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }

    public function actionTrashDelete($lang, $id)
    {
        $model = SubjectContent::find()
            ->andWhere(['id' => $id, 'is_deleted' => 1])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {
            $model->is_deleted = 1;
            if ($model->save()) {
                $order = SubjectContent::find()->where([
                    '>' , 'order', $model->order
                ])
                    ->andWhere(['subject_topic_id' => $model->subject_topic_id])
                    ->all();
                foreach ($order as $order_item) {
                    $order_item->order = $order_item->order - 1;
                    $order_item->save(false);
                }
            }

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }

    public function actionOrder($lang)
    {
        $post = Yii::$app->request->post();

        $result = SubjectContent::orderCorrector($post);
        if (!is_array($result)) {
            return $this->response(1, _e('Content successfully ordered.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
