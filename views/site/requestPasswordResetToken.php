<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \app\models\PasswordResetRequestForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Solicitar Redefinição de Senha';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-request-password-reset">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Por favor, preencha seu e-mail. Um link de redefinição de senha será enviado para lá.</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

                <?= $form->field($model, 'email')->textInput(['autofocus' => true])->label('E-mail*') ?>

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