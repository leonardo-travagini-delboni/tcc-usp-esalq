<?php

namespace app\controllers;

use app\models\Consumo;
use app\models\Dimensionamento;
use app\models\search\DimensionamentoSearch;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

/**
 * DimensionamentoController implements the CRUD actions for Dimensionamento model.
 */
class DimensionamentoController extends Controller
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
     * Lists all Dimensionamento models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new DimensionamentoSearch();
        // $dataProvider = $searchModel->search($this->request->queryParams); // ORIGINAL (all data)

        // Usuários podem ver e listar apenas seus próprios dimensionamentos
        $dataProvider = $searchModel->search(array_merge(
            $this->request->queryParams,
            ['DimensionamentoSearch' => ['user_id' => Yii::$app->user->id]]
        ));

        Yii::info('Visualizando lista de dimensionamentos', 'dimensionamento');
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Dimensionamento model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        // Usuários podem ver apenas seus próprios dimensionamentos
        $model = $this->findModel($id);
        $this->checkAccess($model);

        Yii::info('Visualizando dimensionamento', 'acesso');
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Dimensionamento model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Dimensionamento();

        if ($this->request->isPost) {

            // Coordenadas do User:
            $lat_gps = Yii::$app->user->identity->gps_lat ?? null;
            $lng_gps = Yii::$app->user->identity->gps_lng ?? null;
            $lat_manual = Yii::$app->user->identity->latitude ?? null;
            $lng_manual = Yii::$app->user->identity->longitude ?? null;

            // Checagem inicial (se todos são nulos, então redireciona para site/minhas-coordenadas com setFlash)
            if (is_null($lat_gps) && is_null($lng_gps) && is_null($lat_manual) && is_null($lng_manual)) {
                Yii::$app->session->setFlash('error', 'Antes de dimensionar você precisa fornecer suas coordenadas ou informá-las manualmente.');
                return $this->redirect(['site/minhas-coordenadas']);
            }

            // if ($model->load($this->request->post()) && $model->save()) {
            //     Yii::$app->session->setFlash('success', 'Dimensionamento criado com sucesso.');
            //     return $this->redirect(['view', 'id' => $model->id]);
            // }
            
            if ($model->load($this->request->post())) {
                Yii::info('Dados carregados no modelo: ' . json_encode($model->attributes), 'dimensionamento');
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Dimensionamento criado com sucesso.');
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    Yii::error('Erro ao salvar o modelo: ' . json_encode($model->errors), 'dimensionamento');
                }
            } else {
                Yii::error('Dados não carregados no modelo.', 'dimensionamento');
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
     * Updates an existing Dimensionamento model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // Usuários podem editar apenas seus próprios dimensionamentos
        $this->checkAccess($model);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Dimensionamento atualizado com sucesso.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Dimensionamento model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        // $this->findModel($id)->delete(); // ORIGINAL

        // Usuários podem deletar apenas seus próprios dimensionamentos
        $model = $this->findModel($id);
        $this->checkAccess($model);
        $model->delete();
        Yii::$app->session->setFlash('success', 'Dimensionamento deletado com sucesso.');

        return $this->redirect(['index']);
    }

    /**
     * Finds the Dimensionamento model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Dimensionamento the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Dimensionamento::findOne(['id' => $id])) !== null) {
            return $model;
        }

        Yii::info('Tentativa de acesso a registro inexistente', 'acesso');
        throw new NotFoundHttpException('O registro solicitado não foi encontrado.');
    }

    /**
     * Check if the user has access to the model
     * @param Dimensionamento $model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function checkAccess($model)
    {
        if ($model->user_id !== Yii::$app->user->id) {
            Yii::info('Tentativa de acesso a registro de outro usuário', 'acesso');
            throw new ForbiddenHttpException('Você não tem permissão para acessar este registro.');
        }
    }    

    /**
     * Overwrite the consumption and power values on the current update
     * @param int $id ID
     * @return \yii\web\Response
     */
    public function overwriteConsumoOnCurrentUpdate($id)
    {
        try {
            // Extraindo parametros Necessarios:
            $dimensionamento = Dimensionamento::findOne($id);
            $consumo_atual = Consumo::findOne(['user_id' => Yii::$app->user->id]);
            $consumos_atuais = $consumo_atual->getConsumosDiarios();
            $consumo_atual_cc = $consumos_atuais['CC'];
            $consumo_atual_ca = $consumos_atuais['CA'];
            $potencias_atuais = $consumo_atual->getPotencias();
            $potencia_atual_cc = $potencias_atuais['CC'];
            $potencia_atual_ca = $potencias_atuais['CA'];
    
            // Sobrescrevendo valores antigos com os atuais
            $dimensionamento->consumo_diario_cc_wh = $consumo_atual_cc;
            $dimensionamento->consumo_diario_ca_wh = $consumo_atual_ca;
            $dimensionamento->potencia_instalada_cc_w = $potencia_atual_cc;
            $dimensionamento->potencia_instalada_ca_w = $potencia_atual_ca;
            $dimensionamento->save(false);

            // Registrando e redirecionando
            Yii::info('Consumo e Potência atualizados com sucesso', 'dimensionamento');
            Yii::$app->session->setFlash('success', 'Valores de consumo e ptência atualizados com sucesso.');
            return $this->redirect(['view', 'id' => $dimensionamento->id]);

        } catch (\Throwable $th) {

            // Captura a exceção e define a variável $th
            Yii::error('Erro ao tentar atualizar consumo e potência: ' . $th->getMessage(), 'dimensionamento');
            Yii::$app->session->setFlash('error', 'Erro ao tentar atualizar consumo e potência.');
            return $this->redirect(['view', 'id' => $dimensionamento->id]);
        }


    }
}
