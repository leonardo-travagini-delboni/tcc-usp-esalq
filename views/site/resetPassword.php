<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \app\models\ResetPasswordForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Redefinir Senha';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-reset-password">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Por favor, escolha sua nova senha:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>

                <?= $form->field($model, 'password')->passwordInput(['autofocus' => true])->label('Nova Senha*') ?>

                <?= $form->field($model, 'password_repeat')->passwordInput()->label('Confirmar Senha*') ?>

                <?= $form->field($model, 'verifyCode')->widget(\yii\captcha\Captcha::class, [
                        'template' => '
                        <div class="row">
                            <div class="col-lg-4">
                                {image} 
                                <button id="refresh-captcha" type="button" class="btn btn-link p-0 m-0" aria-label="Nova imagem" title="Gerar nova imagem"><i class="fas fa-sync-alt"></i></button>
                            </div>
                            <div class="col-lg-8">{input}</div>
                        </div>',
                    ])->label('Código de Verificação*');
                ?>

                <div class="row">
                    <div class="col-lg-6 text-center">
                        <?= Html::a('<i class="fas fa-arrow-left"></i> Voltar ao Site', ['site/index'], ['class' => 'btn btn-secondary']) ?>
                    </div>
                    <div class="col-lg-6 text-center">
                        <?= Html::submitButton('Confirmar e Prosseguir <i class="fas fa-arrow-right"></i>', ['class' => 'btn btn-success']) ?>
                    </div>
                </div>

            <?php ActiveForm::end(); ?>
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