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
 * @property integer $discount
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
            [['shift_id', 'master_id', 'bonus_id', 'discount'], 'integer']
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

    public function drawInputCell()
    {
        $haircut = $this;
        $isBonus = $haircut->bonus_id ? ' hasBonus ' : '';
        $isDiscount = $haircut->discount ? ' hasDiscount ' : '';
        return "<input value='{$haircut->price}' class='haircut-price-input $isBonus $isDiscount' onfocus='$(this).toggleClass(\"hpi-focused\",1)' onblur='$(this).toggleClass(\"hpi-focused\",0)' onkeyup='restoreHaircutPriceInput(this,event)' data-id='{$haircut->id}' id='haircut-price-{$haircut->id}'/> 
    <span class='btn btn-xs btn-default glyphicon glyphicon-pencil' style='float: right' onclick='haircutDialog({$haircut->id})'></span>
    <div  class='haircut-discount'>
    <span id='haircut-discount-{$haircut->id}'>".($haircut->discount ?$haircut->price - $haircut->discount:'')."</span>
    </div>
";

    }
}
