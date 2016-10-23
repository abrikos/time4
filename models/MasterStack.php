<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "master_stack".
 *
 * @property integer $id
 * @property integer $master_id
 * @property integer $removed
 */
class MasterStack extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'master_stack';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['master_id', 'removed'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'master_id' => 'Master ID',
            'removed' => 'Removed',
        ];
    }

    public static function get()
    {
        $stack = [];
        $items = static::getActive();
        foreach ($items as $item) {
            $stack[] = Master::findOne(['id' => $item->master_id]);
        }
        return $stack;
    }

    public static function add($id)
    {
        if (static::findOne(['master_id' => $id, 'removed' => null])) {
            return false;
        }
        $item = new MasterStack();
        $item->master_id = $id;
        return $item->save();
    }

    public function remove()
    {
        $this->removed = true;
        return $this->save();
    }

    public static function clear()
    {
        $items = static::getActive();
        $result = true;
        foreach ($items as $item) {
            $result = $result && $item->remove();
        }
        return $result;
    }

    private static function getActive()
    {
        return static::findAll(['removed' => null]);
    }
}
