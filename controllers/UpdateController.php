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
        return `git -C $path pull`;
    }

    public function actionMigrate()
    {
        `sqlite3 -init add-bonus.sql ..\db\database.db`;
    }

}
