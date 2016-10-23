<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Master */

$this->title = 'Создание';
$this->params['breadcrumbs'][] = ['label' => 'Мастера', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="master-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
