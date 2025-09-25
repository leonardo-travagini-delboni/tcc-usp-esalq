<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user
 *
 */
class LoginForm extends Model
{   
    public $email;
    public $password;
    public $rememberMe = true;
    public $verifyCode;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // email and password are both required
            [['email', 'password'], 'required', 'message' => 'Campo obrigatório*'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            ['verifyCode', 'captcha', 'captchaAction' => 'site/captcha', 'message' => 'Código de verificação inválido.'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'email' => 'E-mail',
            'password' => 'Senha',
            'rememberMe' => 'Lembrar-me',
            'verifyCode' => 'Verificação',
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Credenciais inválidas.');
            }
        }
    }

    /**
     * Logs in a user using the provided email and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {

            // Atualizando token e expiration_token (apenas quando login é ambos)
            $user = $this->getUser();
            $user->access_token = Yii::$app->security->generateRandomString();  // GERA UM NOVO TOKEN APENAS NO LOGIN
            $user->token_expiration = time() + Yii::$app->params['user.token_expiration'];
            $user->save(false);

            return Yii::$app->user->login($user, $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }

    /**
     * Finds user by [[email]]
     *
     * @return User|null
     */
    public function getUser()
    {
        return User::findOne(['email' => $this->email]);
    }
}
