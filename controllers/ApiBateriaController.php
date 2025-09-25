<?php

namespace app\controllers;

use app\models\Bateria;

use Yii;
use yii\rest\ActiveController;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use yii\web\JsonParser;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpBearerAuth;
use yii\web\ForbiddenHttpException;

/**
 * Class ApiBateriaController
 * 
 * @author Leonardo Travagini Delboni <leonardodelboni@gmail.com>
 * @package app\controllers
 */
class ApiBateriaController extends ActiveController 
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
    
        // Configuração do ContentNegotiator para garantir respostas em JSON
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        // Configuração do JsonParser para este controlador
        Yii::$app->request->parsers['application/json'] = JsonParser::class;

        // Configuração dos Métodos HTTP
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

        return $actions;
    }

    /**
     * Authorization checks
     * 
     * @inheritDoc
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        // Forbidden Guest Access
        if (Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException('Você deve estar logado para acessar este recurso.');
        }
    
        // Admin Actions
        if (in_array($action, ['create', 'update', 'delete'])) {
            if (Yii::$app->user->identity->is_admin != 1) {
                throw new ForbiddenHttpException('Apenas administradores podem realizar essa ação.');
            }
        }
    }

    /**
     * @inheritDoc
     */
    public $modelClass = Bateria::class;
}