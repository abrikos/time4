<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Master */

$this->title = 'Изменение: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Мастера', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="master-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
