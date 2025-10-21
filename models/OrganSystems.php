<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "organ_systems".
 *
 * @property int $id
 * @property string $name
 */
class OrganSystems extends \yii\db\ActiveRecord
{
    public $mcq_count;
    
    public static function tableName()
    {
        return 'organ_systems';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Organ System Name',
        ];
    }
}