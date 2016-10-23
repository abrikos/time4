<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bonus".
 *
 * @property integer $id
 * @property integer $card
 * @property integer $date
 * @property integer $price
 * @property integer $haircut
 * @property integer $status
 */
class Bonus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bonus';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['card', 'date', 'price', 'haircut', 'status'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'card' => 'Card',
            'date' => 'Date',
            'price' => 'Price',
            'haircut' => 'Haircut',
        ];
    }

	public function getHaircut0()
	{
		return $this->hasOne(Haircut::className(),['id'=>'haircut']);
    }

	public function getCard0()
	{
		return $this->hasOne(Card::className(),['id'=>'card']);
    }

	public function getDateH()
	{
		return date('d-m-Y H:i:s', $this->date);
    }

}
