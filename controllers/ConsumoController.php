<?php

namespace app\controllers;

use app\models\Consumo;
use app\models\search\ConsumoSearch;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

/**
 * ConsumoController implements the CRUD actions for Consumo model.
 */
class ConsumoController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['@'],   // @ means logged in users
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
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
     * Lists all Consumo models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ConsumoSearch();
        // $dataProvider = $searchModel->search($this->request->queryParams);   // ORIGINAL (all data)

        // Usuários podem ver e listar apenas seus próprios equipamentos
        $dataProvider = $searchModel->search(array_merge(
            $this->request->queryParams,
            ['ConsumoSearch' => ['user_id' => Yii::$app->user->id]]
        ));

        Yii::info('Visualizando lista de equipamentos', 'acesso');
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Consumo model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        // Usuários podem ver apenas seus próprios equipamentos
        $model = $this->findModel($id);
        $this->checkAccess($model);

        Yii::info('Visualizando equipamento', 'acesso');
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Consumo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Consumo();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Equipamento adicionado com sucesso.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            Yii::info('Tentativa de acesso a registro inexistente', 'acesso');
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Consumo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // Usuários podem atualizar apenas seus próprios equipamentos
        $this->checkAccess($model);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Equipamento atualizado com sucesso.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        Yii::info('Tentativa de acesso a registro inexistente', 'acesso');
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Consumo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        // $this->findModel($id)->delete();   // ORIGINAL

        // Usuários podem deletar apenas seus próprios equipamentos
        $model = $this->findModel($id);
        $this->checkAccess($model);
        $model->delete();
        Yii::$app->session->setFlash('success', 'Equipamento deletado com sucesso.');

        return $this->redirect(['index']);
    }

    /**
     * Finds the Consumo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Consumo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Consumo::findOne(['id' => $id])) !== null) {
            return $model;
        }

        Yii::info('Tentativa de acesso a registro inexistente', 'acesso');
        throw new NotFoundHttpException('O registro solicitado não foi encontrado.');
    }

    /**
     * Check if the user has access to the model
     * @param Consumo $model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function checkAccess($model)
    {
        if ($model->user_id !== Yii::$app->user->id) {
            Yii::info('Tentativa de acesso a registro de outro usuário', 'acesso');
            throw new ForbiddenHttpException('Você não tem permissão para acessar este registro.');
        }
    }
}
