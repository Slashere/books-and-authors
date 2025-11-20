<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "book_author".
 *
 * @property int $book_id
 * @property int $author_id
 *
 * @property Author $author
 * @property Book $book
 */
class BookAuthor extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'book_author';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['book_id', 'author_id'], 'required'],
            [['book_id', 'author_id'], 'integer'],
            [['book_id', 'author_id'], 'unique', 'targetAttribute' => ['book_id', 'author_id']],
            [['book_id'], 'exist', 'skipOnError' => true, 'targetClass' => Book::class, 'targetAttribute' => ['book_id' => 'id']],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Author::class, 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'book_id' => 'ID Книги',
            'author_id' => 'ID Автора',
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

    /**
     * Gets query for [[Book]].
     *
     * @return ActiveQuery
     */
    public function getBook(): ActiveQuery
    {
        return $this->hasOne(Book::class, ['id' => 'book_id']);
    }

}
