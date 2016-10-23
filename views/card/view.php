<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Card */

$this->title = $model->number;
$this->params['breadcrumbs'][] = ['label' => 'Cards', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'percent',
	        'bonusSum'
        ],
    ]) ?>

</div>
<h3>Полученные бонусы</h3>
<table class="table">
	<tr>
		<th>Дата</th>
		<th>Сумма</th>
		<th>Бонус</th>
		<th>Статус</th>
		<th>Мастер</th>
	</tr>
<?php
foreach ($model->bonuses as $bonuse) {
	print '<tr>';
	print '<td>'. $bonuse->dateH.'</td>';
	print '<td>'. $bonuse->haircut0->price.'</td>';
	print '<td>'. $bonuse->price.'</td>';
	print '<td>'. ($bonuse->status?'Активно':'Использовано').'</td>';
	print '<td>'. $bonuse->haircut0->master->name.'</td>';
	print '</tr>';
}
?>
</table>

