<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Consumo $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="consumo-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->hiddenInput(['value' => Yii::$app->user->id])->label(false) ?>

    <div class="row">
        <div class="col-lg-8">
            <?= $form->field($model, 'nome')->textInput(['maxlength' => true])->label('Nome do Equipamento (obrigatório)')->hint('Ex. Lâmpadas LED 127Vcc para Sala de Reunião') ?>
        </div>
        <div class="col-lg-4">
            <?= $form->field($model, 'qtde')->textInput(['type' => 'number', 'min' => 0])->label('Quantidade (obrigatório)')->hint('Ex. 10') ?>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-4">
            <?= $form->field($model, 'potencia_w')->textInput(['type' => 'number', 'step' => '0.01'])->label('Potência (W) (obrigatório)')->hint('Ex. 9.5') ?>
        </div>
        <div class="col-lg-4">
            <?= $form->field($model, 'tensao_v')->textInput(['type' => 'number', 'step' => '0.01'])->label('Tensão (V) (opcional)')->hint('Ex. 127') ?>
        </div>
        <div class="col-lg-4">
            <?= $form->field($model, 'corrente_a')->textInput(['type' => 'number', 'step' => '0.01'])->label('Corrente (A) (opcional)')->hint('Ex. 1.45') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <?= $form->field($model, 'tipo_corrente')->dropDownList(['CC' => 'CC', 'CA' => 'CA'])->label('Tipo de Corrente (obrigatório)')->hint('CC = Corrente Contínua, CA = Corrente Alternada') ?>
        </div>
        <div class="col-lg-4">
            <?= $form->field($model, 'minutos_por_dia')->textInput(['type' => 'number', 'min' => 1, 'max' => 1440])->label('Minutos por Dia (obrigatório)')->hint('Ex. 120') ?>
        </div>
        <div class="col-lg-4">
            <?= $form->field($model, 'dias_por_mes')->textInput(['type' => 'number', 'min' => 1, 'max' => 31])->label('Dias por Mês (obrigatório)')->hint('Ex. 30') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 text-end">
            <?= Html::a('<i class="fas fa-arrow-left"></i> Meu Consumo', ['index'], ['class' => 'btn btn-secondary']) ?>
            <?php if (!$model->isNewRecord) : ?>
                <?= Html::a('<i class="fas fa-eye"></i> Ver Equipamento', ['view', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?php endif; ?>
            <?= Html::submitButton('Salvar e Prosseguir <i class="fas fa-arrow-right"></i>', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
