<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "master_to_shift".
 *
 * @property integer $master_id
 * @property integer $shift_id
 * @property integer $arrive_time
 * @property integer $leave_time
 */
class MasterToShift extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'master_to_shift';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['master_id', 'shift_id', 'prepayment'], 'integer'],
            [['prepayment', 'penalty'], 'default', 'value' => 0],
            [['arrive_time', 'leave_time'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'master_id' => 'Master ID',
            'shift_id' => 'Shift ID',
            'arrive_time' => 'Time',
            'leave_time' => 'Time'
        ];
    }

    public function getArriveShortTime()
    {
        return date("H:i", $this->arrive_time);
    }

    public function getLeaveShortTime()
    {
        return ( $this->leave_time ? date("H:i", $this->leave_time) : '--:--' );
    }
}
