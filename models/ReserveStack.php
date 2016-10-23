<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reserve".
 *
 * @property integer $id
 * @property string $name
 * @property string $phone
 * @property string $time
 */
class ReserveStack extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reserve_stack';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'phone'], 'string'],
            [['removed'], 'integer'],
            [['time'], 'safe']
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
            'phone' => 'Phone',
            'time' => 'Time',
            'removed' => 'Removed'
        ];
    }

    public function getPrettyTime()
    {
        return date("d/m H:i", $this->time);
    }

    public function getText()
    {
        return "{$this->phone} ({$this->name})";
    }

    public static function get()
    {
        return static::getActive();
    }

    private static function getActive()
    {
        return static::find()->where(['removed' => null])->orderBy(['time' => SORT_ASC])->all();
    }

    public static function add($name, $phone, $time)
    {
        $item = new ReserveStack();
        $item->name = $name;
        $item->phone = $phone;
        $item->time = strtotime($time);
        return ($item->save() ? $item->id : false);
    }

    public function remove()
    {
        $this->removed = true;
        return $this->save();
    }
}
