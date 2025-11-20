<?php

namespace app\controllers;

use Yii;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Author;
use app\models\Subscription;
use yii\web\Response;

class SubscriptionController extends Controller
{
    /**
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionCreate(int $author_id): Response|string
    {
        $author = Author::findOne($author_id);
        if ($author === null) {
            throw new NotFoundHttpException('Автор не найден.');
        }

        $model = new Subscription();
        $model->author_id = $author_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Вы подписались на новые книги автора.');
            return $this->redirect(['author/view', 'id' => $author_id]);
        }

        return $this->render('create', [
            'model' => $model,
            'author' => $author,
        ]);
    }
}