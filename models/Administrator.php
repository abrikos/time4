<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "administrator".
 *
 * @property integer $id
 * @property string $name
 */
class Administrator extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'administrator';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
        ];
    }

    public function getShifts()
    {
        return $this->hasMany(Shift::className(), ['administrator_id' => 'id']);
    }

    public static function getList()
    {
        $list = [];
        $administrators = Administrator::find()->all();
        return ArrayHelper::map($administrators, 'id', 'name');
    }
}
