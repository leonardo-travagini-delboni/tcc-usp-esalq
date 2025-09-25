<?php

namespace app\models;

use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;

use app\models\User;

/**
 * Password reset form
 */
class ResetPasswordForm extends Model
{
    public $password;
    public $password_repeat;
    public $verifyCode;

    /**
     * @var \app\models\User
     */
    private $_user;


    /**
     * Creates a form model given a token.
     *
     * @param string $token
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws InvalidArgumentException if token is empty or not valid
     */
    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            Yii::info('Erro de ResetPassword - Token inválido.', __METHOD__);
            throw new InvalidArgumentException('A senha não pode ser resetada sem um token.');
        }
        $this->_user = User::findByPasswordResetToken($token);
        if (!$this->_user) {
            Yii::info('Erro de ResetPassword - Usuário não encontrado.', __METHOD__);
            throw new InvalidArgumentException('Token inválido ou expirado.');
        }
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => 6, 'message' => 'A senha deve conter no mínimo 6 caracteres.'],
            ['password_repeat', 'required'],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => 'As senhas não conferem.'],
            ['verifyCode', 'captcha', 'message' => 'Código de verificação incorreto.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'password' => 'Nova Senha*',
            'verifyCode' => 'Anti-Spam*',
        ];
    }

    /**
     * Resets password.
     *
     * @return bool if password was reset.
     */
    public function resetPassword()
    {
        Yii::info('DEBUG - Resetando a senha.', __METHOD__);
        $user = $this->_user;
        Yii::info('DEBUG - Usuário encontrado.', __METHOD__);
        $user->setPassword($this->password);
        Yii::info('DEBUG - Senha setada.', __METHOD__);
        $user->removePasswordResetToken();
        Yii::info('DEBUG - Token removido.', __METHOD__);
        $user->generateAuthKey();
        Yii::info('DEBUG - Senha resetada com sucesso.', __METHOD__);
        return $user->save(false);
    }
}