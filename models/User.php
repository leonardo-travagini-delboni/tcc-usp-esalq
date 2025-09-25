<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property int $status
 * @property string $email
 * @property string $auth_key
 * @property string|null $access_token
 * @property int $token_expiration
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string|null $verification_token
 * @property int $is_admin
 * @property int $created_at
 * @property int $updated_at
 * @property float|null $gps_lat
 * @property float|null $gps_lng
 * @property float|null $latitude
 * @property float|null $longitude
 * @property int|null $use_gps
 * @property int|null $mmc
 * 
 * The following properties are specific from forms (ex. password_confirm)
 * @property string $password_confirm
 * @property int $accept_terms
 * @property int $accept_privacy
 * @property string $verifyCode
 * @property Consumo $consumos
 * @property Dimensionamento $dimensionamentos
 */
class User extends ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    self::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => function () {
                    return time();
                },
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'is_admin', 'token_expiration', 'created_at', 'updated_at', 'use_gps', 'mmc'], 'integer'],
            [['email', 'auth_key', 'password_hash', 'is_admin'], 'required'],
            [['email', 'password_hash', 'password_reset_token', 'verification_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['access_token'], 'string', 'max' => 512],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Status',
            'email' => 'E-mail',
            'auth_key' => 'Auth Key',
            'access_token' => 'Access Token',
            'token_expiration' => 'Validade do Token',
            'password_hash' => 'Senha',
            'password_reset_token' => 'Token de Alteração de Senha',
            'verification_token' => 'Token de Verificação',
            'is_admin' => 'Administrador',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
            'gps_lat' => 'Latitude (GPS)',
            'gps_lng' => 'Longitude (GPS)',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'use_gps' => 'Usar GPS',
            'mmc' => 'MMC',
        ];
    }


   /** 
    * Gets query for [[Consumos]]. 
    * 
    * @return \yii\db\ActiveQuery|\app\models\query\ConsumoQuery 
    */ 
    public function getConsumos() 
    { 
        return $this->hasMany(Consumo::class, ['user_id' => 'id']); 
    } 
  
    /** 
     * Gets query for [[Dimensionamentos]]. 
     * 
     * @return \yii\db\ActiveQuery|\app\models\query\DimensionamentoQuery 
     */ 
    public function getDimensionamentos() 
    { 
        return $this->hasMany(Dimensionamento::class, ['user_id' => 'id']); 
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\UserQuery(get_called_class());
    }

    /**
     * Finds an identity by the given ID.
     *
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     *
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface|null the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // Managing Bearer Token for API
        $user = static::findOne(['access_token' => $token]);

        if (!$user) {
            Yii::$app->response->statusCode = 401;
            Yii::$app->response->data = [
                'status' => 'error',
                'message' => 'Token inválido!',
            ];
            return null;
        }

        // Verifica se o token expirou
        if ($user->token_expiration < time()) {
            Yii::$app->response->statusCode = 401;
            Yii::$app->response->data = [
                'status' => 'error',
                'message' => 'Token expirado!',
            ];
            return null;
        }

        // Caso status seja diferente de 10, retorna mensagem de erro
        if ($user->status != 10) {
            Yii::$app->response->statusCode = 401;
            Yii::$app->response->data = [
                'status' => 'error',
                'message' => 'Usuário não verificou a conta por e-mail!',
            ];
            return null;
        }

        // ✅ Atualiza o token de expiração
        $user->token_expiration = time() + Yii::$app->params['user.token_expiration'];
        $user->save(false);
        return $user;
    }

    /**
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string current user auth key
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     * @return bool if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new access token
     */
    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new email verification token
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token) {
        return static::findOne([
            'verification_token' => $token,
            'status' => 9,
        ]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        return static::findOne([
            'password_reset_token' => $token,
            'status' => 10,
        ]);
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne([
            'email' => $email,
            'status' => 9,
        ]);
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Validates password reset token
     *
     * @param string $token password reset token
     * @return bool if password reset token is valid
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * Validates if the session is still valid
     *
     * @return bool if the session is still valid
     */
    public function isSessionValid()
    {
        return $this->token_expiration >= time();
    }

    /**
     * Obtém parâmetros prévios do dimensionamento
     * @return array
     */
    public function getCoordenadasUser()
    {
        $params = [
            'lat_gps' => $this->gps_lat ?? null,
            'lng_gps' => $this->gps_lng ?? null,
            'lat_manual' => $this->latitude ?? null,
            'lng_manual' => $this->longitude ?? null,
            'use_gps' => $this->use_gps ?? null,
        ];
        return $params;
    }
}
