<?php
use app\models\Card;
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use app\models\Master;
use app\models\Administrator;

/* @var $this yii\web\View */

$this->title = 'Тайм';
?>
<div class="site-index">
	<div class="templates" hidden>
		<span class='btn btn-xs btn-default glyphicon glyphicon-pencil haircut-edit-template' style='float: right' data-toggle='modal' data-target='#haircut-modal'></span>
	</div>
    <?php if(Yii::$app->session->hasFlash('shiftHasNoAdmin')): ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
		  	<span aria-hidden="true">&times;</span>
		</button>
            <?= Yii::$app->session->getFlash('shiftHasNoAdmin') ?>
        </div>
    <?php endif; ?>
	<div class="col-sm-9">
		<div class="panel panel-primary">
			<div class="panel-heading">
				Смена №<?= $shift->id ?> от <?= $shift->startedAt ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				Касса: <span class="total-cash"><?= $shift->finalCash ?></span> руб. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				Администратору: <span class="administrator-payment"><?= $shift->administratorPayment ?></span>
				<a href="/site/close-shift" class="btn btn-xs btn-danger" style="float: right" data-confirm="Смена будет закрыта">Закрыть смену</a>
			</div>
			<table id="shift-table" class="panel-body table table-condensed table-bordered">
				<thead><tr>
					<th width="130px" style="max-width: 130px">
						<button class="btn btn-xs btn-warning" data-toggle="modal" data-target="#administrator-modal">
							<?= ($shift->administrator ? '<span class="glyphicon glyphicon-pencil"></span>' : 'Администратор') ?>
						</button>
						<span class="text-uppercase" title="<?= $shift->administratorArriveShort ?>">&nbsp;<?= ($shift->administrator ? $shift->administrator->name : '') ?></span>
					</th>
					<?php foreach ($masters as $master): ?>
						<th width="80px" style="max-width: 80px" title="<?= $master->name ?> - <?= $master->arriveTime ?>/<?= $master->leaveTime ?>" data-master-id="<?= $master->id; ?>">
							<?= $master->name ?> - <?= $master->arriveTime ?>/<?= $master->leaveTime ?>
						</th>
					<?php endforeach ?>
					<th>
						<button class="btn btn-xs btn-primary" data-toggle="modal" data-target="#master-modal">
							<span class="glyphicon glyphicon-plus"></span>
						</button>
					</th>
				</tr></thead>
				<tbody>
					<?php foreach ($haircutTable as $i => $row): ?>
						<tr>
							<td><?= $i + 1 ?></td>
							<?php foreach ($row as $value) {
								echo "<td>$value</td>";
							} ?>
							<td></td>
						</tr>
					<?php endforeach ?>
				</tbody>
			</table>
			<table class="table table-condensed table-bordered table-total">
				<thead>
					<tr>
						<td width="130px" style="max-width: 130px; padding: 0px;"></td>
						<?php for ($i = 0; $i < count($masters); $i++): ?>
							<td width="80px" style="max-width: 80px; padding: 0px;"></td>
						<?php endfor; ?>
						<td style="padding: 0px;"></td>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($totalTable as $id => $row): ?>
						<tr>
							<?php foreach ($row as $value) {
								echo "<td>$value</td>";
							} ?>
							<td></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>


	<div class="col-sm-3">
		<div class="panel panel-primary">
			<div class="panel-heading">
				Продажи
				<button class="btn btn-xs btn-default glyphicon glyphicon-plus" data-toggle="modal" data-target="#sale-modal" style="float: right"></button>
			</div>
			<table class="panel-body table table-condensed sale-table">
				<tbody>
					<?php foreach ($shift->sales as $sale): ?>
						<tr data-sale-id="<?= $sale->id ?>">
							<td><?= $sale->name ?></td>
							<td width="40px"><?= $sale->amount ?></td>
							<td width="20px"><button class="btn btn-xs btn-warning glyphicon glyphicon-remove remove-sale"></button></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<div class="panel panel-primary">
			<div class="panel-heading">
				Доходы
				<button class="btn btn-xs btn-default glyphicon glyphicon-plus" data-toggle="modal" data-target="#income-modal" style="float: right"></button>
			</div>
			<table class="panel-body table table-condensed income-table">
				<tbody>
					<?php foreach ($shift->incomes as $income): ?>
						<tr data-income-id="<?= $income->id ?>">
							<td><?= $income->name ?></td>
							<td width="40px"><?= $income->amount ?></td>
							<td width="20px"><button class="btn btn-xs btn-warning glyphicon glyphicon-remove remove-income"></button></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<div class="panel panel-primary">
			<div class="panel-heading">
				Расходы
				<button class="btn btn-xs btn-default glyphicon glyphicon-plus" data-toggle="modal" data-target="#expense-modal" style="float: right"></button>
			</div>
			<table class="panel-body table table-condensed expense-table">
				<tbody>
					<?php foreach ($shift->expenses as $expense): ?>
						<tr data-expense-id="<?= $expense->id ?>">
							<td><?= $expense->name ?></td>
							<td width="40px"><?= $expense->amount ?></td>
							<td width="20px"><button class="btn btn-xs btn-warning glyphicon glyphicon-remove remove-expense"></button></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		
		<div class="panel panel-primary">
			<div class="panel-heading">Очередь мастеров</div>
			<table class="panel-body table table-condensed">
				<thead>
					<tr>
						<td>
							<button id="add-master-to-stack" class="btn btn-default glyphicon glyphicon-plus"></button>
						</td>
						<td>
							<?= Html::dropDownList('master', null, Master::getNotInStack(), ['class' => 'form-control', 'prompt' => 'Выберите мастера']) ?>
						</td>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($masterStack as $master): ?>
						<tr data-master-id="<?= $master->id ?>">
							<td><button class="btn btn-sm btn-default glyphicon glyphicon-minus remove-master-from-stack"></button></td>
							<td><?= $master->name ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<div class="panel panel-primary">
			<div class="panel-heading">
				Бронирование
				<button class="btn btn-xs btn-default glyphicon glyphicon-plus" data-toggle="modal" data-target="#reserve-modal" style="float: right"></button>
			</div>
			<table class="panel-body table table-condensed reserve-table">
				<tbody>
					<?php foreach ($reserveStack as $reserve): ?>
						<tr data-reserve-id="<?= $reserve->id ?>">
							<td width="40px"><button class="btn btn-xs btn-default glyphicon glyphicon-ok remove-reserve"></button></td>
							<td width="90px"><?= $reserve->prettyTime ?></td>
							<td title="<?= $reserve->text ?>"><?= $reserve->text ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<!-- Modal forms -->

<div class="modal fade" tabindex="-1" role="dialog" id="administrator-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Выберите администратора</h4>
			</div>
			<div class="modal-body">
				<?= Html::dropDownList(
					'administrator',
					$shift->administrator_id,
					Administrator::getList(),
					[
						'class' => 'form-control',
						'prompt' => 'Выберите администратора',
						'onChange' => '$(this).closest(".modal-body").siblings(".modal-footer").children("a").attr("href", "site/select-admin?id=" + $(this).val());'
					]); ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
				<?= Html::a('Выбрать', ['site/select-admin', 'id' => $shift->administrator_id], ['class' => 'btn btn-primary']) ?>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="master-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Выберите мастера</h4>
			</div>
			<div class="modal-body">
				<?= Html::dropDownList('master', null, Master::getNotInShift(), ['class' => 'form-control', 'prompt' => 'Выберите мастера']); ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
				<button type="button" class="btn btn-primary" id="master-arrive">Добавить</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="reserve-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Бронирование</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<input type="text" name="name" class="form-control" placeholder="Имя" onFocus="$(this).parent().removeClass('has-error')">
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<input type="text" name="phone" class="form-control" placeholder="Телефон" onFocus="$(this).parent().removeClass('has-error')">
					</div>
					<div class="col-md-6">
                        <input id="bronetime" name="time" placeholder="Время" />
                        <script>
                            $(function () {
                                $('#bronetime').datetimepicker({value:'<?=date('d-m-Y H:i')?>', format: "d-m-Y H:i"});
                                $.datetimepicker.setLocale('ru');
                            })
                        </script>
						<!--?= DateTimePicker::widget([
						    'name' => 'time',
						    'options' => ['placeholder' => 'Время'],
						    'type' => DateTimePicker::TYPE_INPUT,
						    'value' => date('d-m-Y H:i'),
						    'readonly' => true,
						    'pluginOptions' => [
						        'autoclose' => true,
						        'format' => 'dd-mm-yyyy H:ii',
						    ]
						]); ?-->
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
				<button type="button" class="btn btn-primary" id="add-reserve">Добавить</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="sale-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Продажи</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-9">
						<input type="text" name="name" class="form-control" placeholder="Название" onFocus="$(this).parent().removeClass('has-error')">
					</div>
					<div class="col-md-3">
						<input type="text" name="amount" class="form-control" placeholder="Сумма" onFocus="$(this).parent().removeClass('has-error')">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
				<button type="button" class="btn btn-primary" id="add-sale">Добавить</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="income-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Доходы</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-9">
						<input type="text" name="name" class="form-control" placeholder="Название" onFocus="$(this).parent().removeClass('has-error')">
					</div>
					<div class="col-md-3">
						<input type="text" name="amount" class="form-control" placeholder="Сумма" onFocus="$(this).parent().removeClass('has-error')">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
				<button type="button" class="btn btn-primary" id="add-income">Добавить</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="expense-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Расходы</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-9">
						<input type="text" name="name" class="form-control" placeholder="Название" onFocus="$(this).parent().removeClass('has-error')">
					</div>
					<div class="col-md-3">
						<input type="text" name="amount" class="form-control" placeholder="Сумма" onFocus="$(this).parent().removeClass('has-error')">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
				<button type="button" class="btn btn-primary" id="add-expense">Добавить</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="haircut-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Детальный просмотр</h4>
			</div>
			<div class="modal-body">
				<div style="text-align: center">
					<img src="/images/ajax-loading.gif" alt="Загрузка..." width="50px">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
			</div>
		</div>
	</div>
</div>

<script type="text/x-tmpl" id="bonus-form">
<?=$this->render('bonus-form')?>
</script>
<datalist id="card_list" onclick="console.log(this)">
<?php foreach (Card::find()->all() as $card):?>
<option><?=$card->number?></option>
<?php endforeach?>
</datalist>
