<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Cadastro';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Insira as informações a seguir:</p>

    <div class="row">
        <div class="col-lg-8">

            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

            <?= $form->field($model, 'password')->passwordInput() ?>

            <?= $form->field($model, 'password_confirm')->passwordInput()->label('Confirme a Senha*') ?>

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

            <div class="row">
                <div class="col-lg-3 mt-2">
                    <?= $form->field($model, 'acceptTerms')->checkbox()->label('Termos de Uso* ' . Html::a('<i class="fas fa-external-link-alt"></i>', ['site/termos-de-uso'], ['target' => '_blank'])) ?>
                </div>
                <div class="col-lg-3 mt-2">
                    <?= $form->field($model, 'acceptPrivacy')->checkbox()->label('Polít. de Privacidade* ' . Html::a('<i class="fas fa-external-link-alt"></i>', ['site/politica-de-privacidade'], ['target' => '_blank'])) ?>
                </div>
                <div class="col-lg-3 text-center mt-2">
                    <?= Html::a('Sou cadastrado', ['site/login'], ['class' => 'btn btn-secondary']) ?>
                </div>
                <div class="col-lg-3 text-center mt-2">
                    <?= Html::submitButton('Cadastrar-se <i class="fas fa-arrow-right"></i>', ['class' => 'btn btn-success', 'name' => 'cadastro-button']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
        <div class="col-lg-4 mt-2">
            <img src="/img/user.png" class="img-fluid" alt="Cadastro">
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