<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use yii\base\InvalidConfigException;
//use app\services\NotificationService;

/**
 * This is the model class for table "book".
 *
 * @property int $id
 * @property string $title
 * @property int $year
 * @property string|null $description
 * @property string|null $isbn
 * @property string|null $cover_path
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Author[] $authors
 * @property BookAuthor[] $bookAuthors
 */
class Book extends ActiveRecord
{
    /** @var UploadedFile|null */
    public $coverFile;
    public array $authorIds = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'book';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['title', 'year', 'authorIds'], 'required'],
            [['year'], 'integer', 'min' => 1500, 'max' => (int)date('Y') + 1,
                'tooSmall' => 'Год выпуска не может быть меньше {min}.',
                'tooBig'   => 'Год выпуска не может быть больше {max}.',
            ],
            [['description'], 'string'],
            [['title', 'cover_path'], 'string', 'max' => 255],
            [['isbn'], 'filter', 'filter' => 'trim'],
            [['isbn'], 'string', 'max' => 13],
            ['isbn', 'match',
                'pattern' => '/^\d{10}(\d{3})?$/',
                'message' => 'ISBN должен состоять из 10 или 13 цифр без пробелов и дефисов.',
            ],
            [['authorIds'], 'each', 'rule' => ['integer']],
            [['coverFile'], 'file',
                'skipOnEmpty' => true,
                'extensions' => 'png, jpg, jpeg',
                'maxSize' => 5 * 1024 * 1024
            ],
        ];
    }

    /**
     * @throws InvalidConfigException
     */
    public function afterFind(): void
    {
        parent::afterFind();

        $this->authorIds = $this->getAuthors()->select('id')->column();
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'year' => 'Год выпуска',
            'description' => 'Описание',
            'isbn' => 'ISBN',
            'cover_path' => 'Путь до файла обложки',
            'coverFile' => 'Файл обложки',
        ];
    }

    /**
     * Gets query for [[Authors]].
     *
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getAuthors(): ActiveQuery
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])
            ->viaTable('book_author', ['book_id' => 'id']);
    }

    /**
     * Gets query for [[BookAuthors]].
     *
     * @return ActiveQuery
     */
    public function getBookAuthors(): ActiveQuery
    {
        return $this->hasMany(BookAuthor::class, ['book_id' => 'id']);
    }

    public static function getAuthorsList(): array
    {
        return ArrayHelper::map(Author::find()->all(), 'id', 'full_name');
    }

    /**
     * @throws Exception|InvalidConfigException
     */
    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);

//        // Обновляем связи авторов
//        if (!empty($this->authorIds)) {
//            BookAuthor::deleteAll(['book_id' => $this->id]);
//            foreach ($this->authorIds as $authorId) {
//                $ba = new BookAuthor();
//                $ba->book_id = $this->id;
//                $ba->author_id = $authorId;
//                $ba->save(false);
//            }
//        }
//
//        // Появилась новая книга от автора, шлем SMS подписчикам
//        if ($insert) {
//            NotificationService::notifyNewBook($this);
//        }
    }

    /**
     * @throws Exception
     */
    public function syncAuthors(array $authorIds): void
    {
        BookAuthor::deleteAll(['book_id' => $this->id]);

        foreach ($authorIds as $authorId) {
            (new BookAuthor([
                'book_id' => $this->id,
                'author_id' => $authorId,
            ]))->save(false);
        }
    }

    /**
     * @throws Exception
     */
    public function handleCoverUpload(?string $oldCoverPath = null): void
    {
        if (!$this->coverFile instanceof UploadedFile) {
            // Если обложка не загружена, вернуть старый путь
            if ($oldCoverPath !== null) {
                $this->cover_path = $oldCoverPath;
                $this->save(false, ['cover_path']);
            }
            return;
        }

        $uploadDir = \Yii::getAlias('@webroot/uploads/covers');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = 'book_' . $this->id . '.' . $this->coverFile->extension;
        $filePath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

        if ($this->coverFile->saveAs($filePath)) {
            // Удаляем старую обложку, если была
            if ($oldCoverPath && file_exists(\Yii::getAlias('@webroot/' . $oldCoverPath))) {
                @unlink(\Yii::getAlias('@webroot/' . $oldCoverPath));
            }

            $this->cover_path = 'uploads/covers/' . $fileName;
            $this->save(false, ['cover_path']);
        }
    }
}
