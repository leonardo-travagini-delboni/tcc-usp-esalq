<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\search\ConsumoSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="consumo-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'nome') ?>

    <?= $form->field($model, 'qtde') ?>

    <?= $form->field($model, 'potencia_w') ?>

    <?= $form->field($model, 'minutos_por_dia') ?>

    <?= $form->field($model, 'dias_por_mes') ?>

    <?php // echo $form->field($model, 'tensao_v') ?>

    <?php // echo $form->field($model, 'corrente_a') ?>

    <?php // echo $form->field($model, 'tipo_corrente') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
