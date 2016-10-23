<?php

namespace app\controllers;

use app\models\Bonus;
use app\models\Card;
use app\models\Haircut;
use Yii;
use yii\helpers\Json;

class HaircutController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionCardChange($id,$cardnum)
    {
        $haircut = Haircut::findOne($id);
        $card = Card::findOne(['number'=>$cardnum]);
        if(!$card) return false;
        if(!$haircut) return false;
        $haircut->card_id = $card->id;
        $haircut->save();
        return Json::encode($card);
    }

    public function actionBonusAdd($id)
    {
        $haircut = Haircut::findOne($id);
        if(!$haircut) return false;
        if($haircut->bonus) return false;
        $card = $haircut->card;
        if(!$card) return false;
        $bonus = new Bonus();
        $bonus->card = $card->id;
        $bonus->date = time();
        $bonus->haircut = $haircut->id;
        $bonus->price = $card->bonusPrice($haircut->price);
        $bonus->status = 1;
        if(!$bonus->save()) throw new HttpException(500,VarDumper::export($bonus->errors));
        $card->calcBonuses();
        $haircut->bonus_id = $bonus->id;
        $haircut->save();
        return Json::encode(['haircut_bonus'=>$bonus->price, 'card_bonus'=>$card->bonus]);
    }

    public function actionDiscountAdd($id,$reduce)
    {
        $haircut = Haircut::findOne($id);
        if(!$haircut) return Json::encode(['status'=>['class'=>'danger', 'message'=>'Нет такой оплаты']]);
        if($haircut->bonus) return Json::encode(['status'=>['class'=>'danger', 'message'=>'Оплата уже присуммирвана к бонусам']]);
        $card = $haircut->card;
        if(!$card) return Json::encode(['status'=>['class'=>'danger', 'message'=>'Ошибочный номер карты']]);

        if($reduce>$haircut->price)
            return Json::encode(['status'=>['class'=>'warning', 'message'=>"Снимаемый бонус больше чем стоимость стрижки ($reduce>{$haircut->price})"]]);
        if($reduce > $card->bonus)
             return Json::encode(['status'=>['class'=>'warning', 'message'=>"Недостаточно бонусов на карте"]]);
        if($reduce < 100)
             return Json::encode(['status'=>['class'=>'warning', 'message'=>"Снимается только более 100 бонусов"]]);
        //$haircut->old_price = $haircut->price;
        $haircut->price = $haircut->price - $reduce;
        $haircut->save();
        //обнуляем израсходованные бонусы
        $connection = Yii::$app->getDb();
        $sql = "update bonus  set status=0 where card={$card->id} and status=1";
        $command = $connection->createCommand($sql);
        $command->execute();
        //Устанавливаем остаток бонусов на карте
        $card->delta = $card->bonus = $card->bonus - $reduce;
        $card->save();
        $status = ['class'=>'success','message'=>$reduce . ' бонусов переведено в оплату '  ];
        return Json::encode(['status'=>$status, 'bonus'=>$card->bonus, 'card'=>$card, 'haircut'=>$haircut]);
    }

}
