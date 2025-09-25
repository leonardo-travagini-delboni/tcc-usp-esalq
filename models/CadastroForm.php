<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;

/**
 * Signup form
 */
class CadastroForm extends Model
{
    public $email;
    public $password;
    public $password_confirm;
    public $acceptTerms;
    public $acceptPrivacy;
    public $verifyCode;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required', 'message' => 'Campo obrigatório.'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255, 'message' => 'E-mail muito longo (máx 255 caracteres).'],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => 'E-mail já cadastrado.'],

            ['password', 'required', 'message' => 'Campo obrigatório*'],
            ['password', 'string', 'min' => 6, 'max' => 255, 'message' => 'Senha muito curta (mín 6 caracteres).'],

            ['password_confirm', 'required', 'message' => 'Campo obrigatório.'],
            ['password_confirm', 'compare', 'compareAttribute' => 'password', 'message' => 'As senhas não conferem.'],

            ['acceptTerms', 'required', 'requiredValue' => 1, 'message' => 'Você deve aceitar os termos de uso.'],
            ['acceptPrivacy', 'required', 'requiredValue' => 1, 'message' => 'Você deve aceitar a política de privacidade.'],

            ['verifyCode', 'captcha', 'message' => 'Código de verificação inválido.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => 'E-mail*',
            'password' => 'Senha*',
            'password_confirm' => 'Confirmação*',
            'acceptTerms' => 'Termos de Uso*',
            'acceptPrivacy' => 'Política de Privacidade*',
            'verifyCode' => 'Verificação*',
        ];
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function signup()
    {
        if (!$this->validate()) {
            // return null;
            return false;
        }

        Yii::info('Iniciando o processo de cadastro.', __METHOD__);
        
        $user = new User();
        $user->status = 9;                      // 9 = STATUS_INACTIVE, 10 = STATUS_ACTIVE
        $user->email = $this->email;
        $user->generateAuthKey();
        $user->generateAccessToken();
        $user->token_expiration = time() + Yii::$app->params['user.token_expiration'];
        $user->setPassword($this->password);
        $user->password_reset_token = null;
        $user->generateEmailVerificationToken();
        $user->is_admin = 0;
        $user->gps_lat = -23.5568;          // Avenida Paulista, SP-SP
        $user->gps_lng = -46.6538;          // Avenida Paulista, SP-SP
        $user->latitude = -23.5568;         // Avenida Paulista, SP-SP
        $user->longitude = -46.6538;        // Avenida Paulista, SP-SP
        $user->use_gps = 1;                 // 1 = GPS, 0 = MANUAL
        $user->mmc = null;
        return $user->save() && $this->sendEmail($user);
    }

    /**
     * Sends confirmation email to user
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail($user)
    {
        try {
            Yii::info('Enviando e-mail de confirmação.', __METHOD__);
            return Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                    ['user' => $user]
                )
                ->setFrom([Yii::$app->params['smtp']['email'] => Yii::$app->params['smtp']['username']])
                ->setTo($this->email)
                ->setSubject('Dimensionamento Solar Off-grid - Novo Cadastro')
                ->send();
        } catch (\Throwable $th) {
            Yii::error('Erro ao enviar e-mail de confirmação: ' . $th->getMessage(), __METHOD__);
            return false;
        }
    }
    
}
