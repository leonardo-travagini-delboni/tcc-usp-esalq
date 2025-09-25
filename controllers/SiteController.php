<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\helpers\Html;

use app\models\LoginForm;
use app\models\CadastroForm;
use app\models\PasswordResetRequestForm;
use app\models\ResetPasswordForm;
use app\models\VerifyEmailForm;
use app\models\ResendVerificationEmailForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout', 'minhas-coordenadas', 'potencial-solar', 'consumo-eletrico', 'dimensionamento'],
                        'allow' => true,
                        'roles' => ['@'],   // only authenticated users
                    ],
                    [
                        'actions' => ['request-password-reset', 'resend-verification-email'],
                        'allow' => true,
                        'roles' => ['?'], // only guests
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
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
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'minLength' => 3,
                'maxLength' => 3,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $this->layout = 'main';
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            Yii::info('Usuário já logado', 'login');
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            if (Yii::$app->user->identity->status == 9) {
                Yii::$app->user->logout();
                Yii::$app->session->setFlash('error', 'Você precisa validar seu e-mail antes de logar. ' . Html::a('Reenviar e-mail de verificação!', ['site/resend-verification-email']));
                return $this->redirect(['site/login']);
            }
    
            Yii::info('Usuário logado', 'login');
            return $this->redirect(['site/index']);
        }

        $model->password = '';
        Yii::info('Usuário não logado', 'login');
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::info('Usuário deslogado', 'login');
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * Displays politica-de-privacidade page.
     *
     * @return string
     */
    public function actionPoliticaDePrivacidade()
    {
        return $this->render('politica-de-privacidade');
    }

    /**
     * Displays termos-de-uso page.
     *
     * @return string
     */
    public function actionTermosDeUso()
    {
        return $this->render('termos-de-uso');
    }

    /**
     * Displays minhas-coordenadas page.
     *
     * @return string
     */
    public function actionMinhasCoordenadas()
    {
        return $this->render('minhas-coordenadas');
    }

    /**
     * Displays potencial-solar page.
     *
     * @return string
     */
    public function actionPotencialSolar()
    {
        return $this->render('potencial-solar');
    }

    /**
     * Displays consumo-eletrico page.
     *
     * @return string
     */
    public function actionConsumoEletrico()
    {
        return $this->render('consumo-eletrico');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionCadastro()
    {
        $model = new CadastroForm();
        Yii::info('DEBUG - Iniciando o Cadastro.', __METHOD__);
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::info('DEBUG - Cadastro realizado com sucesso.', __METHOD__);
            Yii::$app->session->setFlash('success', 'Cadastrado com sucesso. Enviamos um e-mail de confirmação para você. Não esqueça de verificar sua caixa de spam.');
            return $this->goHome();
        }

        Yii::info('DEBUG - Erro ao cadastrar.', __METHOD__);
        return $this->render('cadastro', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Enviamos as próximas instruções para o seu e-mail. Não se esqueça de verificar o spam também.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Desculpe, não foi possível redefinir a senha para o e-mail fornecido.');
        }
        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            Yii::info('DEBUG - Iniciando o ResetPassword.', __METHOD__);
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            Yii::info('DEBUG - Token inválido. Erro: ' . $e->getMessage(), __METHOD__);
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'Nova senha cadastrada com sucesso.');
            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return yii\web\Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if (($user = $model->verifyEmail()) && Yii::$app->user->login($user)) {
            Yii::$app->session->setFlash('success', 'Seu e-mail foi confirmado com sucesso. Você já está logado(a)!');
            return $this->goHome();
        }

        Yii::$app->session->setFlash('error', 'Desculpe, não foi possível verificar seu e-mail com o token fornecido.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'E-mail reenviado com sucesso. Por favor, verifique sua caixa de entrada.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Desculpe, não foi possível reenviar o e-mail de verificação.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }

    /**
     * Atualiza a localização do usuário
     *
     * @return array
     */
    public function actionAtualizarGps()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
    
        if (Yii::$app->user->isGuest) {
            return ['success' => false, 'message' => 'Usuário não autenticado'];
        }
    
        $lat = Yii::$app->request->post('latitude');
        $lng = Yii::$app->request->post('longitude');
    
        if ($lat && $lng) {
            $user = Yii::$app->user->identity;
            $user->gps_lat = $lat;
            $user->gps_lng = $lng;
            $user->use_gps = 0;
            $user->save(false);
    
            return ['success' => true, 'message' => 'Localização atualizada com sucesso'];
        }
    
        return ['success' => false, 'message' => 'Dados inválidos'];
    }

    /**
     * Usar a localização do GPS
     *
     * @return yii\web\Response
     */
    public function actionUsarGps($route = 'site/potencial-solar', $use_gps = 1)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        $user = Yii::$app->user->identity;
        $user->use_gps = $use_gps;
        $user->save(false);

        return $this->redirect([$route]);
    }

    /**
     * Atualiza as coordenadas do usuário manualmente
     *
     * @param float|null $latitude
     * @param float|null $longitude
     * @param string $rota_final
     * @return yii\web\Response
     */
    public function actionAtualizarCoordenadasManuais($latitude = null, $longitude = null, $rota_final = null, $use_gps=0)
    {
        // Limites geográficos do Brasil
        $minLat = -33.7000;
        $maxLat = 5.3000;
        $minLong = -73.9000;
        $maxLong = -32.4000;
    
        // Validação dos parâmetros recebidos
        if ($latitude === null || $longitude === null || $rota_final === null) {
            Yii::$app->session->setFlash('error', 'Todos os parâmetros são obrigatórios.');
            return $this->redirect(['/site/potencial-solar']);
        }
    
        // Conversão para float
        $latitude = floatval($latitude);
        $longitude = floatval($longitude);
    
        // Verificação se estão dentro dos limites do Brasil
        if (
            $latitude < $minLat || $latitude > $maxLat ||
            $longitude < $minLong || $longitude > $maxLong
        ) {
            Yii::$app->session->setFlash('error', 'Coordenadas fora dos limites permitidos no Brasil.');
            return $this->redirect(['/site/minhas-coordenadas']);
        }
    
        // Atualização do usuário logado
        $user = Yii::$app->user->identity;
        $user->latitude = $latitude;
        $user->longitude = $longitude;
        $user->use_gps = $use_gps;
    
        if ($user->save()) {
            return $this->redirect([$rota_final]);
        } else {
            Yii::$app->session->setFlash('error', 'Erro ao salvar coordenadas.');
            return $this->redirect(['/site/minhas-coordenadas']);
        }
    }
    
}
