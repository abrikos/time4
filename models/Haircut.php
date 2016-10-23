<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "haircut".
 *
 * @property string $price
 * @property integer $shift_id
 * @property integer $master_id
 * @property integer $id
 * @property integer $bonus_id
 * @property integer $original_price
 */
class Haircut extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'haircut';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['price'], 'number'],
            [['shift_id', 'master_id', 'bonus_id', 'original_price'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'price' => 'Price',
            'shift_id' => 'Shift ID',
            'master_id' => 'Master ID',
            'bonus_id' => 'Bonus ID',
            'id' => 'ID',
        ];
    }

    public function getShortTime()
    {
        return date("H:i", $this->time);
    }

    public function getMaterials()
    {
        return $this->hasMany(Material::className(), ['haircut_id' => 'id']);
    }

    public function getBonus()
    {
        return $this->hasOne(Bonus::className(), ['haircut' => 'id']);
    }
    public function getCard()
    {
        return $this->hasOne(Card::className(), ['id'=>'card_id']);
    }
    public function getMaster()
    {
        return $this->hasOne(Master::className(), ['id' => 'master_id']);
    }

    public function addMaterial($name, $price)
    {
        $material = new Material();
        $material->name = $name;
        $material->price = $price;
        $material->haircut_id = $this->id;
        $material->save();
        return $material->attributes;
    }
}
