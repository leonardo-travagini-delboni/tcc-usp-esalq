<?php

namespace app\controllers;

use app\models\Consumo;

use Yii;
use yii\rest\ActiveController;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use yii\web\JsonParser;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpBearerAuth;
use yii\web\ForbiddenHttpException;
use yii\data\ActiveDataProvider;

/**
 * Class ApiConsumoController
 * 
 * @author Leonardo Travagini Delboni <leonardodelboni@gmail.com>
 * @package app\controllers
 */
class ApiConsumoController extends ActiveController
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
    
        // JSON Response
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        // JSON Parser
        Yii::$app->request->parsers['application/json'] = JsonParser::class;

        // Method Verbs
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'index' => ['GET'],
                'view' => ['GET'],
                'create' => ['POST'],
                'update' => ['PUT', 'PATCH'],
                'delete' => ['DELETE'],
            ],
        ];

        // Authenticator - Bearer Token
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'only' => ['index', 'view', 'create', 'update', 'delete'],
        ];
    
        return $behaviors;
    }

    /**
     * Actions for the controller
     * 
     * @inheritDoc
     * @return array
     */
    public function actions()
    {
        $actions = parent::actions();

        // Index
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        $actions['index']['checkAccess'] = [$this, 'checkAccess'];

        // Create
        unset($actions['create']);

        // Update
        unset($actions['update']);

        return $actions;
    }

    /**
     * Prepare the data provider for the index action
     * 
     * @inheritDoc
     */
    public function prepareDataProvider()
    {
        $query = Consumo::find()->where(['user_id' => Yii::$app->user->id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
    
        return $dataProvider;
    }

    /**
     * Authorization checks
     * 
     * @inheritDoc
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        // Guests
        if (Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException('Você deve estar logado para acessar este recurso.');
        }

        // Speficic Model Actions Rules (only the owner can access them)
        if ($action === 'view' || $action === 'update' || $action === 'delete') {
            if ($model !== null && $model->user_id !== Yii::$app->user->id) {
                throw new ForbiddenHttpException('Você não tem permissão para acessar este recurso.');
            }
        }
    }


    /**
     * Overwriting CREATE action to set user_id from token
     * 
     * @return array|\yii\db\ActiveRecord
     */
    public function actionCreate()
    {
        $model = new Consumo();
        $data = Yii::$app->request->bodyParams;
    
        // Carrega os dados do body, exceto user_id
        $model->load($data, '');
        
        // Força o user_id a ser o do token (usuário logado)
        $model->user_id = Yii::$app->user->id;
    
        if ($model->save()) {
            Yii::$app->response->statusCode = 201;
            return $model;
        }
    
        Yii::$app->response->statusCode = 422;
        return $model->getErrors();
    }

    /**
     * Overwriting UPDATE action to set user_id from token
     * 
     * @param int $id
     * @return array|\yii\db\ActiveRecord
     */
    public function actionUpdate($id)
    {
        $model = Consumo::findOne($id);

        if (!$model) {
            Yii::$app->response->statusCode = 404;
            return ['error' => 'Registro não encontrado'];
        }

        // Verifica se o usuário tem acesso a esse item (mesmo do checkAccess)
        if ($model->user_id !== Yii::$app->user->id) {
            throw new ForbiddenHttpException('Você não tem permissão para editar este item.');
        }

        // Carrega os dados, MAS IGNORA user_id
        $data = Yii::$app->request->bodyParams;
        unset($data['user_id']);

        $model->load($data, '');

        if ($model->save()) {
            return $model;
        }

        Yii::$app->response->statusCode = 422;
        return $model->getErrors();
    }

    /**
     * @inheritDoc
     */
    public $modelClass = Consumo::class;
}