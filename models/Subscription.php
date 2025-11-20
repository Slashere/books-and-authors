<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "subscription".
 *
 * @property int $id
 * @property int $author_id
 * @property string $phone
 * @property int $created_at
 *
 * @property Author $author
 */
class Subscription extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'subscription';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => null,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['author_id', 'phone'], 'required'],
            [['author_id'], 'integer'],
            [['phone'], 'string', 'max' => 20],
            [['phone'], 'match', 'pattern' => '/^\+?\d{7,20}$/', 'message' => 'Введите корректный номер телефона.'],
            [['author_id', 'phone'], 'unique', 'targetAttribute' => ['author_id', 'phone'], 'message' => 'Вы уже подписаны на этого автора.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'phone' => 'Номер телефона',
        ];
    }

    /**
     * Gets query for [[Author]].
     *
     * @return ActiveQuery
     */
    public function getAuthor(): ActiveQuery
    {
        return $this->hasOne(Author::class, ['id' => 'author_id']);
    }

}
