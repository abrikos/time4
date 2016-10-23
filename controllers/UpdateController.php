<?php

namespace app\controllers;

use Yii;
use yii\helpers\Json;

class UpdateController extends \yii\web\Controller
{
    public function actionIndex()
    {


        return $this->render('index');
    }


    public function actionPull()
    {
        $path = dirname(__FILE__) . '/..';
        $result = `git -C $path pull`;
        return "<pre>$result</pre>";
    }

    public function actionMigrate()
    {
        $result = `sqlite3 -init add-bonus.sql ..\db\database.db`;
        return "<pre>$result</pre>";
    }

}
