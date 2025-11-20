<?php

namespace app\services;

use Throwable;
use Yii;
use yii\web\UploadedFile;
use yii\db\Exception;
use app\models\Book;

class BookService
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    /**
     * Создание книги.
     *
     * @param Book $model
     * @param array $data
     * @return Book|null
     * @throws Exception|Throwable
     */
    public function create(Book $model, array $data): ?Book
    {
        if (!$model->load($data)) {
            return null;
        }

        $model->coverFile = UploadedFile::getInstance($model, 'coverFile');

        if (!$model->validate()) {
            return null;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->save(false)) {
                $transaction->rollBack();
                return null;
            }

            if (!empty($model->authorIds)) {
                $model->syncAuthors($model->authorIds);
            }

            $model->handleCoverUpload(null);

            // Появилась новая книга от автора, шлем SMS подписчикам
            $this->notificationService->notifyNewBook($model);

            $transaction->commit();
            return $model;
        } catch (Throwable $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    /**
     * Обновление книги.
     *
     * @param Book $model
     * @param array $data
     * @param string|null $oldCoverPath
     * @return Book|null
     * @throws Exception|Throwable
     */
    public function update(Book $model, array $data, ?string $oldCoverPath): ?Book
    {
        if (!$model->load($data)) {
            return null;
        }

        $model->coverFile = UploadedFile::getInstance($model, 'coverFile');

        if (!$model->validate()) {
            return null;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->save(false)) {
                $transaction->rollBack();
                return null;
            }

            if (!empty($model->authorIds)) {
                $model->syncAuthors($model->authorIds);
            }

            $model->handleCoverUpload($oldCoverPath);

            $transaction->commit();
            return $model;
        } catch (Throwable $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }
}
