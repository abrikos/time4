<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "material".
 *
 * @property string $name
 * @property string $price
 * @property integer $haircut_id
 * @property integer $id
 */
class Material extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'material';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
            [['price'], 'integer'],
            [['haircut_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Name' => 'Name',
            'haircut_id' => 'Haircut ID',
            'id' => 'ID',
        ];
    }

    public function getHaircut()
    {
        return $this->hasOne(Haircut::className(), ['id' => 'haircut_id']);
    }
}
