<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;
    public $verifyCode;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\app\models\User',
                'filter' => ['status' => 10],
                'message' => 'Não há usuário cadastrado com este e-mail.',
            ],
            ['verifyCode', 'captcha', 'message' => 'Código de verificação inválido.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => 'E-mail*',
            'verifyCode' => 'Anti-Spam*',
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne([
            'status' => 10,
            'email' => $this->email,
        ]);

        if (!$user) {
            Yii::info('Erro de SendMail - Usuário não encontrado.', __METHOD__);
            return false;
        }
        
        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
            if (!$user->save()) {
                Yii::error('Erro ao salvar token de redefinição de senha.', __METHOD__);
                return false;
            }
        }

        try {
            return Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
                    ['user' => $user]
                )
                ->setFrom([Yii::$app->params['smtp']['email'] => Yii::$app->params['smtp']['username']])
                ->setTo($this->email)
                ->setSubject('Redefinição de Senha para ' . Yii::$app->name)
                ->send();
        } catch (\Throwable $th) {
            Yii::error('Erro ao enviar e-mail de redefinição de senha: ' . $th->getMessage(), __METHOD__);
            return false;
        }
    }
}