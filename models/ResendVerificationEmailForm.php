<?php

namespace app\models;

use Yii;
use app\models\User;
use yii\base\Model;


class ResendVerificationEmailForm extends Model
{
    /**
     * @var string
     */
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
                'filter' => ['status' => 9],
                'message' => 'Não há usuário cadastrado com este e-mail ou o e-mail já foi verificado.',
            ],
            ['verifyCode', 'captcha', 'captchaAction' => 'site/captcha', 'message' => 'Código de verificação inválido.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'email' => 'E-mail*',
            'verifyCode' => 'Anti-Spam*',
        ];
    }

    /**
     * Sends confirmation email to user
     *
     * @return bool whether the email was sent
     */
    public function sendEmail()
    {
        $user = User::findOne([
            'email' => $this->email,
            'status' => 9,
        ]);

        if ($user === null) {
            Yii::info('Erro de SendMail - Usuário não encontrado.', __METHOD__);
            return false;
        }


        Yii::info('Re-enviando e-mail de confirmação.', __METHOD__);
        try {
            return Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                    ['user' => $user]
                )
                ->setFrom([Yii::$app->params['smtp']['email'] => Yii::$app->params['smtp']['username']])
                ->setTo($this->email)
                ->setSubject('Dimensionamento Solar Off-grid - Reenvio de Token de Confirmação')
                ->send();
        } catch (\Throwable $th) {
            Yii::error('Erro ao enviar e-mail de confirmação: ' . $th->getMessage(), __METHOD__);
            return false;
        }
    }
}