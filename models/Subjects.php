<?php

namespace app\models;
use Yii;

/**
 * This is the model class for table "subjects".
 *
 * @property int $id
 * @property string $name
 */
class Subjects extends \yii\db\ActiveRecord
{
    public $mcq_count;
    
    public static function tableName()
    {
        return 'subjects';
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
            'name' => 'Subject Name',
        ];
    }

}