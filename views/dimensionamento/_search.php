<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\search\DimensionamentoSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="dimensionamento-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'simulacao_no') ?>

    <?= $form->field($model, 'created_at') ?>

    <?= $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'latitude') ?>

    <?php // echo $form->field($model, 'longitude') ?>

    <?php // echo $form->field($model, 'consumo_diario_cc_wh') ?>

    <?php // echo $form->field($model, 'consumo_diario_ca_wh') ?>

    <?php // echo $form->field($model, 'potencia_instalada_cc_w') ?>

    <?php // echo $form->field($model, 'potencia_instalada_ca_w') ?>

    <?php // echo $form->field($model, 'efic_bateria') ?>

    <?php // echo $form->field($model, 'efic_inversor') ?>

    <?php // echo $form->field($model, 'efic_gerador') ?>

    <?php // echo $form->field($model, 'efic_elet') ?>

    <?php // echo $form->field($model, 'painel_id') ?>

    <?php // echo $form->field($model, 'painel_qtde_total') ?>

    <?php // echo $form->field($model, 'painel_qtde_serie') ?>

    <?php // echo $form->field($model, 'mppt_id') ?>

    <?php // echo $form->field($model, 'fator_seguranca') ?>

    <?php // echo $form->field($model, 'bateria_id') ?>

    <?php // echo $form->field($model, 'profundidade_descarga') ?>

    <?php // echo $form->field($model, 'dias_autonomia') ?>

    <?php // echo $form->field($model, 'tensao_nominal_cc') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
