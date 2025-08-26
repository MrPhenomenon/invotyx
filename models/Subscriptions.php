<?php

namespace app\models;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use Yii;

/**
 * This is the model class for table "subscriptions".
 *
 * @property int $id
 * @property string $name
 * @property float $price
 * @property int $duration_days
 * @property string|null $features_json
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Payments[] $payments
 * @property UserSubscriptions[] $userSubscriptions
 */
class Subscriptions extends ActiveRecord
{
    /**
     * @var array An array to hold the features for form input.
     */
    public $features_array;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subscriptions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'price', 'duration_days'], 'required'],
            [['price'], 'number'],
            [['duration_days'], 'integer'],
            [['created_at', 'updated_at', 'features_array'], 'safe'], // Make features_array safe
            [['name'], 'string', 'max' => 255],
            // Remove 'features_json' from rules if it's there, as we handle it via the array.
        ];
    }

    /**
     * After finding a record, decode the JSON string into the array property.
     */
    public function afterFind()
    {
        parent::afterFind();
        try {
            $decoded = Json::decode($this->features_json);
            $this->features_array = is_array($decoded) ? $decoded : [];
        } catch (\Throwable $e) {
            $this->features_array = [];
        }
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (is_string($this->features_array)) {
                $this->features_array = array_map('trim', explode("\n", $this->features_array));
            }

            $filtered = is_array($this->features_array) ? array_filter($this->features_array) : [];
            $this->features_json = !empty($filtered) ? Json::encode(array_values($filtered)) : null;

            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Plan Name',
            'price' => 'Price',
            'duration_days' => 'Duration (Days)',
            'features_json' => 'Features',
            'features_array' => 'Features', // Label for the form
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    // ... (relations) ...
}
