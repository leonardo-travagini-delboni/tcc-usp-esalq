<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\web\JsonParser;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpBearerAuth;
use yii\web\UnauthorizedHttpException;
use app\models\CadastroForm;
use app\models\LoginForm;
use app\models\User;

class ApiSiteController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => ['application/json' => Response::FORMAT_JSON],
        ];

        Yii::$app->request->parsers['application/json'] = JsonParser::class;

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'signup' => ['POST'],
                'login' => ['POST'],
                'logout' => ['POST'],
            ],
        ];

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'only' => ['logout'],
        ];

        return $behaviors;
    }

    /**
     * Cadastro de usuário via API
     */
    public function actionSignup()
    {
        $model = new CadastroForm();

        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->errorHandler->errorAction = null;

            $post = Yii::$app->request->post();
            $post['acceptTerms'] = 1;
            $post['acceptPrivacy'] = 1;
            $model->detachBehavior('captcha');
            $model->verifyCode = 'testme'; // previne erro de campo vazio

            if ($model->load($post, '')){

                // Remove qualquer validador de captcha (sem erro!)
                foreach ($model->validators as $i => $validator) {
                    if (in_array('verifyCode', $validator->attributes, true)) {
                        $model->validators->offsetUnset($i);
                    }
                }

                if ($model->signup()) {
                    $user = User::findOne(['email' => $model->email]);

                    if (!$user) {
                        return ['success' => false, 'message' => 'Usuário não encontrado após cadastro.'];
                    }

                    return [
                        'success' => true,
                        'message' => 'Usuário cadastrado com sucesso! Verifique seu e-mail para ativar sua conta.',
                        // 'access_token' => $user->access_token,
                    ];
                }
            }
            return ['success' => false, 'errors' => $model->getErrors()];


        } catch (\Throwable $th) {
            return [
                'success' => false,
                'error' => $th->getMessage(),
                // 'trace' => $th->getTraceAsString(),
            ];
        }
    }

    /**
     * Login via API
     */
    public function actionLogin()
    {
        $model = new LoginForm();

        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->errorHandler->errorAction = null;

            // Ignora captcha se for necessário
            $post = Yii::$app->request->post();
            $model->detachBehavior('captcha');
            $model->verifyCode = 'testme'; // previne erro de campo vazio
    
            if ($model->load($post, '')) {

                // Remove qualquer validador de captcha (sem erro!)
                foreach ($model->validators as $i => $validator) {
                    if (in_array('verifyCode', $validator->attributes, true)) {
                        $model->validators->offsetUnset($i);
                    }
                }
                
                if($model->login()){
                    $user = Yii::$app->user->identity;
    
                    if ($user->status == 9) {
                        Yii::$app->user->logout();
                        throw new UnauthorizedHttpException('Você precisa validar seu e-mail antes de logar.');
                    }
        
                    return [
                        'success' => true,
                        'access_token' => $user->access_token,
                    ];
                }
            }
    
            return ['success' => false, 'errors' => $model->getErrors()];
    


        } catch (\Throwable $th) {
            return [
                'success' => false,
                'error' => $th->getMessage(),
                // 'trace' => $th->getTraceAsString(),
            ];
        }
    }

    /**
     * Logout via API
     */
    public function actionLogout()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->errorHandler->errorAction = null;
        
        try {
            /** @var User $user */
            $user = Yii::$app->user->identity;

            $user->access_token = null;
            $user->token_expiration = null;
            $user->save(false);

            Yii::$app->user->logout();

            return ['success' => true, 'message' => 'Logout realizado com sucesso.'];

        } catch (\Throwable $th) {
            return [
                'success' => false,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ];
        }
    }

    /**
     * Atualiza a expiração do token a cada requisição autenticada
     */
    public function beforeAction($action)
    {
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity;

            if ($user->token_expiration < time()) {
                $user->access_token = null;
                $user->token_expiration = null;
                $user->save(false);
                Yii::$app->user->logout();
                throw new UnauthorizedHttpException('Sessão expirada. Faça login novamente.');
            }

            $user->token_expiration = time() + Yii::$app->params['user.token_expiration'];
            $user->save(false);
        }

        return parent::beforeAction($action);
    }
}
