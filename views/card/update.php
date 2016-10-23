<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Card */

$this->title = 'Карта: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Бонусные карты', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="card-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
