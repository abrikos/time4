<?php

namespace app\controllers;

use app\models\Bonus;
use app\models\Card;
use Yii;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\Controller;
use app\models\Shift;
use app\models\Haircut;
use app\models\Material;
use app\models\Master;
use app\models\Administrator;
use app\models\MasterStack;
use app\models\ReserveStack;
use app\models\Expense;
use app\models\Income;
use app\models\Sale;
use yii\web\HttpException;

class SiteController extends Controller
{

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        $shift = Shift::getCurrent();
        $masters = $shift->masters;
        usort($masters, function($a, $b) {
            return ($a->time . $a->id) > ($b->time . $b->id);
        });
        $masterStack = MasterStack::get();
        $reserveStack = ReserveStack::get();
        $haircutTable = $shift->haircutTable;
        $totalTable = $shift->totalTable;
        return $this->render('index', [
            'shift' => $shift,
            'masterStack' => $masterStack,
            'reserveStack' => $reserveStack,
            'masters' => $masters,
            'haircutTable' => $haircutTable,
            'totalTable' => $totalTable
        ]);
    }

    public function actionGetTotalTable()
    {
        $shift = Shift::getCurrent();
        return json_encode($shift->totalTable);
    }

    public function actionCloseShift()
    {
        $shift = Shift::getCurrent();
        if ($shift->administrator_id) {
            $shift->close();
        } else {
            Yii::$app->session->setFlash('shiftHasNoAdmin', 'Перед закрытием смены нужно выбрать администратора!');
        }
        return $this->redirect(['/site']);
    }

    public function actionMasterArrive($id)
    {
        if ($master = Master::findOne(['id' => $id])) {
            $shift = Shift::getCurrent();
            return $shift->addMaster($id);
        }
        return false;
    }

    public function actionMasterLeave($id)
    {
        if ($master = Master::findOne(['id' => $id])) {
            $shift = Shift::getCurrent();
            return $shift->removeMaster($id);
        }
        return false;
    }

    public function actionAddMasterToStack($id)
    {
        if ($master = Master::findOne(['id' => $id])) {
            return MasterStack::add($id);
        }
        return false;
    }

    public function actionRemoveMasterFromStack($id)
    {
        if ($item = MasterStack::findOne(['master_id' => $id, 'removed' => null])) {
            return $item->remove();
        }
        return false;
    }

    public function actionSelectAdmin($id = null)
    {
        if ($id && $admin = Administrator::findOne(['id' => $id])) {
            $shift = Shift::getCurrent();
            $shift->administrator_id = $admin->id;
            $shift->administrator_arrive = time();
            $shift->save();
        }
        $this->redirect(['/']);
    }

    public function actionAddReserve($name, $phone, $time)
    {
        return ReserveStack::add($name, $phone, $time);
    }

    public function actionRemoveReserve($id)
    {
        if ($item = ReserveStack::findOne($id)) {
            return $item->remove();
        }
        return false;
    }

    public function actionGetReserve()
    {
        $items = [];
        $reserveStack = ReserveStack::get();
        foreach ($reserveStack as $reserve) {
            $items[] = [
                'id' => $reserve->id,
                'time' => $reserve->prettyTime,
                'text' => $reserve->text
            ];
        }
        return json_encode($items);
    }

    public function actionAddExpense($name, $amount)
    {
        $shift = Shift::getCurrent();
        return $shift->addExpense($name, $amount);
    }

    public function actionRemoveExpense($id)
    {
        if ($expense = Expense::findOne($id)) {
            return $expense->delete();
        }
        return false;
    }

    public function actionGetExpense()
    {
        $shift = Shift::getCurrent();
        $items = [];
        foreach ($shift->expenses as $expense) {
            $items[] = [
                'id' => $expense->id,
                'name' => $expense->name,
                'amount' => $expense->amount
            ];
        }
        return json_encode($items);
    }

    public function actionAddIncome($name, $amount)
    {
        $shift = Shift::getCurrent();
        return $shift->addIncome($name, $amount);
    }

    public function actionRemoveIncome($id)
    {
        if ($income = Income::findOne($id)) {
            return $income->delete();
        }
        return false;
    }

    public function actionGetIncome()
    {
        $shift = Shift::getCurrent();
        $items = [];
        foreach ($shift->incomes as $income) {
            $items[] = [
                'id' => $income->id,
                'name' => $income->name,
                'amount' => $income->amount
            ];
        }
        return json_encode($items);
    }  

    public function actionAddSale($name, $amount)
    {
        $shift = Shift::getCurrent();
        return $shift->addSale($name, $amount);
    }

    public function actionRemoveSale($id)
    {
        if ($sale = Sale::findOne($id)) {
            return $sale->delete();
        }
        return false;
    }

    public function actionGetSale()
    {
        $shift = Shift::getCurrent();
        $items = [];
        foreach ($shift->sales as $sale) {
            $items[] = [
                'id' => $sale->id,
                'name' => $sale->name,
                'amount' => $sale->amount
            ];
        }
        return json_encode($items);
    }

	public function actionCard($number)
	{

    }

    public function actionGetHaircut($id)
    {
        $haircut = Haircut::find()->where(['id' => $id])->one();
        $result['materials'] = [];
        $result['id'] = $id;
        foreach ($haircut->materials as $material) {
            $result['materials'][] =  $material->attributes;
        }
        $result['note'] = $haircut->note;
        $result['card'] = $haircut->card ? $haircut->card->number : null;
        $result['haircut_bonus'] = $haircut->bonus ? $haircut->bonus->price : null;
        $result['card_bonus'] = $haircut->card ? $haircut->card->bonus : 0;

	    $result['discount'] = $haircut->discount;
	    $result['price'] = $haircut->price;
	    $result['form_hide'] = $result['haircut_bonus'] || $haircut->discount ;
        return json_encode($result);
    }

    public function actionRemoveMaterial($id)
    {
        $material = Material::findOne(['id' => $id]);
        return $material->delete();
    }

    public function actionAddMaterial($id, $name, $price)
    {
        $haircut = Haircut::findOne(['id' => $id]);
        return json_encode($haircut->addMaterial($name, $price));
    }

    public function actionAddHaircut($masterID)
    {
        $shift = Shift::getCurrent();
        $haircut = new Haircut();
        $haircut->shift_id = $shift->id;
        $haircut->master_id = $masterID;
        $haircut->price = 0;
        $haircut->time = time();
        return ($haircut->save() ? json_encode(['haircut'=>$haircut->attributes,'input'=>$haircut->drawInputCell()]) : false);
    }

    public function actionUpdateHaircutprice($id, $price = null){
        $haircut = Haircut::findOne($id);
        if($haircut->bonus_id){
            return Json::encode(['error'=>'Назначен бонус. Редактирование не доступно.','haircut'=>$haircut->attributes]);
        }
        if($haircut->discount){
            return Json::encode(['error'=>'Бонус вычтен. Редактирование не доступно.', 'haircut'=>$haircut->attributes]);
        }
        $haircut->price = $price;
        $haircut->time = time();
        return ($haircut->save() ? Json::encode(['haircut'=>$haircut->attributes]) : false);
    }

    public function actionUpdateHaircut($id, $price = null, $note = null, $card_number=null, $bonus_discount=null)
    {
        if ($haircut = Haircut::findOne($id)) {
            $haircut->note = ( $note !== null ? $note : $haircut->note );
            return ($haircut->save() ? json_encode($haircut->attributes) : false);
        }
    }



    public function actionHaircutCard($id,$card_number,$bonus_discount)
    {
        $haircut = Haircut::findOne($id);
        if(!$haircut) return Json::encode(['status'=>['class'=>'danger', 'message'=>'Нет такой оплаты']]);
        $card = Card::findOne(['number'=>$card_number]);
        if(!$card) return Json::encode(['status'=>['class'=>'danger', 'message'=>'Ошибочный номер карты']]);
        $status=[];




        if($bonus_discount<=$haircut->price && $bonus_discount <= $card->bonus && $bonus_discount>=100){
            $haircut->old_price = $haircut->price;
            $haircut->price = $haircut->price - $bonus_discount;
            $haircut->save();
            //обнуляем израсходованные бонусы
            $connection = Yii::$app->getDb();
            $sql = "update bonus  set status=0 where card={$card->id} and status=1";
            $command = $connection->createCommand($sql);
            $command->execute();
            //Устанавливаем остаток бонусов на карте
            $card->delta = $card->bonus = $card->bonus - $bonus_discount;
            $card->save();
            $status[] = ['class'=>'success','message'=>$bonus_discount . ' бонусов переведено в оплату '  ];
        }


        return Json::encode(['status'=>$status, 'bonus'=>$card->bonus]);
    }

    public function actionUpdateMasterPrepayment($id, $value)
    {
        if ($master = Master::findOne(['id' => $id])) {
            $shift = Shift::getCurrent();
            return $shift->updatePrepayment($id, $value);
        }
        return false;
    }

    public function actionUpdateMasterPenalty($id, $value)
    {
        if ($master = Master::findOne(['id' => $id])) {
            $shift = Shift::getCurrent();
            return $shift->updatePenalty($id, $value);
        }
        return false;
    }

	public function actionMigrate()
	{
		$connection = Yii::$app->getDb();
			$sql = "update card set percent=10";
			$command = $connection->createCommand($sql);
			return $command->execute();


	}
}
