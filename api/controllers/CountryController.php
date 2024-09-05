<?php

namespace api\controllers;

use common\models\model\Countries;
use Yii;
use base\ResponseStatus;

class CountryController extends ApiActiveController
{
    public $modelClass = 'api\resources\Country';

    public function actions()
    {
        return [];
    }


    public $table_name = 'country';
    public $controller_name = 'Country';

    public function actionIndex($lang)
    {
        $model = new Countries();

        $query = $model->find()

            ->andFilterWhere(['like', 'name', Yii::$app->request->get('query')]);

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }


    public function actionView($lang, $id)
    {
        $model = Countries::find()
            ->andWhere(['id' => $id])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }


}
