<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "management_team".
 *
 * @property int $id
 * @property string $name
 * @property string $role
 * @property string $email
 * @property string $password
 * @property string $auth_key
 * @property string|null $image
 * @property string $created_at
 */
class ManagementTeam extends \yii\db\ActiveRecord implements IdentityInterface
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
            [['name', 'role', 'email', 'password'], 'required'],
            [['created_at'], 'safe'],
            [['name', 'email', 'password', 'image'], 'string', 'max' => 255],
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
            'password' => 'Password',
            'image' => 'Image',
            'created_at' => 'Created At',
        ];
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null; // or implement token login if needed
    }

    public static function findByName($username)
    {
        return static::findOne(['name' => $username]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    // public function getRole()
    // {
    //     return $this->role;
    // }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }
}
