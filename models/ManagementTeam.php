<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "management_team".
 *
 * @property int $id
 * @property string $name
 * @property string $role
 * @property string $email
 * @property string|null $image
 * @property string $created_at
 */
class ManagementTeam extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'management_team';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['image'], 'default', 'value' => null],
            [['name', 'role', 'email'], 'required'],
            [['created_at'], 'safe'],
            [['name', 'email', 'image'], 'string', 'max' => 255],
            [['role'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'role' => 'Role',
            'email' => 'Email',
            'image' => 'Image',
            'created_at' => 'Created At',
        ];
    }

}
