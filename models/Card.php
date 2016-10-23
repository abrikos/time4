<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "card".
 *
 * @property integer $id
 * @property string $number
 * @property integer $percent
 * @property integer $bonus
 * @property integer $delta
 */
class Card extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'card';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['number'], 'string'],
            [['number'], 'unique'],
            [['percent','bonus', 'delta'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'number' => 'Номер карты',
            'percent' => 'Процент',
            'bonusSum' => 'Сумма бонусов',
        ];
    }

	public function getBonuses()
	{
		return $this->hasMany(Bonus::className(), ['card' => 'id'])->orderBy('date desc');
	}

	public function calcBonuses()
	{
		$connection = Yii::$app->getDb();
		$sql = "select sum(price) as s from bonus where card={$this->id} and status=1";
		$command = $connection->createCommand($sql);
		$result = $command->queryAll();
		$this->bonus = $this->delta + $result[0]['s'];
        $this->save();
		return false;
	}

	public function bonusPrice($price)
	{
		return round($price /100 * $this->percent);
	}

	public function getBonusSum()
	{
		return $this->bonus;
	}
}
