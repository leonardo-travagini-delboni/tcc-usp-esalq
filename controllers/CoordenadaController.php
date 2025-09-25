<?php

namespace app\controllers;

use Yii;
use app\models\Coordenada;
use app\models\search\CoordenadaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * CoordenadaController implements the CRUD actions for Coordenada model.
 */
class CoordenadaController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['@'],   // @ means logged in users
                            'matchCallback' => function ($rule, $action) {
                                // only admin can access this controller
                                return Yii::$app->user->identity->is_admin == 1;
                            }
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Check if user is logged in and if the session is still valid
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        if (!Yii::$app->user->isGuest) {
            // Se sessão expirou, faz logout
            if (!Yii::$app->user->identity->isSessionValid()) {
                Yii::$app->user->logout();
                return $this->redirect(['site/login']);
            }
    
            // Atualiza a validade da sessão
            Yii::$app->user->identity->token_expiration = time() + Yii::$app->params['user.token_expiration'];
            Yii::$app->user->identity->save(false); // false para não validar novamente
        }
    
        return parent::beforeAction($action);
    }

    /**
     * Lists all Coordenada models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CoordenadaSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Coordenada model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Coordenada model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Coordenada();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Coordenada model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Coordenada model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Coordenada model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Coordenada the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Coordenada::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
