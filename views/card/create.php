<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Card */

$this->title = 'Новая карта';
$this->params['breadcrumbs'][] = ['label' => 'Бонусные карты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
