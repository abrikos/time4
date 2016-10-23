<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "master".
 *
 * @property integer $id
 * @property string $name
 */
class Master extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'master';
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

    public function getHaircuts($shiftID = null)
    {
        $shift = ($shiftID ? Shift::findOne(['id' => $shiftID]) : Shift::getCurrent());
        return $this->hasMany(Haircut::className(), ['master_id' => 'id'])->andWhere(['shift_id' => $shift->id]);
    }

    public function getMaterials($shiftID = null)
    {
        $shift = ($shiftID ? Shift::findOne(['id' => $shiftID]) : Shift::getCurrent());
        return $this->hasMany(Material::className(), ['haircut_id' => 'id'])
            ->viaTable(Haircut::tableName(), ['master_id' => 'id'], function($query) use($shift) {
                $query->andWhere(['shift_id' => $shift->id]);
            });
    }

    public static function getNotInStack($stack = null)
    {
        $masters = Master::find()->all();
        $stack = ($stack ? : MasterStack::get());
        $list = ArrayHelper::map($masters, 'id', 'name');
        foreach ($stack as $item) {
            unset($list[$item->id]);
        }
        return $list;
    }

    public static function getNotInShift()
    {
        $masters = Master::find()->all();
        $shift = Shift::getCurrent();
        return static::getNotInStack($shift->masters);
    }

    public function getShift($id = null)
    {
        $shift = ($id ? Shift::findOne(['id' => $id]) : Shift::getCurrent());
        return MasterToShift::findOne(['master_id' => $this->id, 'shift_id' => $shift->id]);
    }

    public function getArriveTime($shiftID = null)
    {
        return $this->shift->arriveShortTime;
    }

    public function getLeaveTime($shiftID = null)
    {
        return $this->shift->leaveShortTime;
    }

    public function getTime($shiftID = null)
    {
        return $this->shift->arrive_time;
    }

    public function getPrepayment($shiftID = null)
    {
        return $this->shift->prepayment;
    }

    public function getPenalty($shiftID = null)
    {
        return $this->shift->penalty;
    }
}
