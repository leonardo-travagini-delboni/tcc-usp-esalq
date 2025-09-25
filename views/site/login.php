<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Por favor, preencha os seguintes campos para acessar o sistema:</p>

    <div class="row">
        <div class="col-lg-8">

            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}",
                    'labelOptions' => ['class' => 'col-lg-1 col-form-label mr-lg-3'],
                    'inputOptions' => ['class' => 'col-lg-3 form-control'],
                    'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
                ],
            ]); ?>

            <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

            <?= $form->field($model, 'password')->passwordInput() ?>

            <?= $form->field($model, 'verifyCode')->widget(\yii\captcha\Captcha::class, [
                    'template' => '
                    <div class="row">
                        <div class="col-lg-4">
                            {image} 
                            <button id="refresh-captcha" type="button" class="btn btn-link p-0 m-0" aria-label="Nova imagem" title="Gerar nova imagem"><i class="fas fa-sync-alt"></i></button>
                        </div>
                        <div class="col-lg-8">{input}</div>
                    </div>',
                ])->label('Verificação*');
            ?>

            <?= $form->field($model, 'rememberMe')->hiddenInput(['value' => 1])->label(false) ?>

            <div class="row">
                <div class="col-lg-3 text-center mt-2">
                    <?= Html::a('<i class="fas fa-lock"></i> Esqueceu a Senha?', ['site/request-password-reset'], ['class' => 'btn btn-secondary']) ?>
                </div>
                <div class="col-lg-3 text-center mt-2">
                    <?= Html::a('<i class="fas fa-key"></i> Reenviar Token', ['site/resend-verification-email'], ['class' => 'btn btn-warning']) ?>
                </div>
                <div class="col-lg-3 text-center mt-2">
                    <?= Html::a('<i class="fas fa-id-card"></i> Não tem Cadastro?', ['site/cadastro'], ['class' => 'btn btn-primary']) ?>
                </div>
                <div class="col-lg-3 text-center mt-2">
                    <?= Html::submitButton('<i class="fas fa-user-check"></i> Fazer o Login <i class="fas fa-arrow-right"></i>', ['class' => 'btn btn-success', 'name' => 'login-button']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
        <div class="col-lg-1"></div>
        <div class="col-lg-3 mt-2">
            <img src="/img/lock.png" class="img-fluid" alt="Login">
        </div>
    </div>
</div>

<?php
    $this->registerJs("
        $('#refresh-captcha').on('click', function(e){
            e.preventDefault();
            $('#" . Html::getInputId($model, 'verifyCode') . "-image').yiiCaptcha('refresh');
        });
    ");
?>