<?php

namespace app\controllers;

use Throwable;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\models\Book;
use app\models\BookSearch;
use app\services\BookService;

/**
 * BookController implements the CRUD actions for Book model.
 */
class BookController extends Controller
{

    public function __construct($id, $module, private BookService $bookService, $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    /**
     * @inheritDoc
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create','update','delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // авторизованный пользователь
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Book models.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new BookSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Book model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Book model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     * @throws Exception
     * @throws Throwable
     */
    public function actionCreate(): Response|string
    {
        $model = new Book();

        $authorsList = Book::getAuthorsList();

        if ($this->request->isPost) {
            $saved = $this->bookService->create($model, $this->request->post());
            if ($saved !== null) {
                return $this->redirect(['view', 'id' => $saved->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'authorsList' => $authorsList,
        ]);
    }

    /**
     * Updates an existing Book model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException|Exception if the model cannot be found
     * @throws Throwable
     */
    public function actionUpdate(int $id): Response|string
    {
        $model = $this->findModel($id);
        $authorsList = Book::getAuthorsList();
        $oldCover = $model->cover_path;

        if ($this->request->isPost) {
            $saved = $this->bookService->update($model, $this->request->post(), $oldCover);
            if ($saved !== null) {
                return $this->redirect(['view', 'id' => $saved->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'authorsList' => $authorsList,
        ]);
    }

    /**
     * Deletes an existing Book model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete(int $id): Response
    {
        $this->findModel($id)->delete();

        \Yii::$app->session->setFlash('success', 'Книга удалена.');
        return $this->redirect(['index']);
    }

    /**
     * Finds the Book model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Book the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): Book
    {
        if (($model = Book::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
