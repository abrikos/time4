<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sale".
 *
 * @property integer $id
 * @property string $name
 * @property integer $amount
 * @property integer $shift_id
 */
class Sale extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sale';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
            [['amount', 'shift_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'amount' => 'Amount',
            'shift_id' => 'Shift ID',
        ];
    }
}
