<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Coordenada $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="coordenada-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'lat')->textInput() ?>

    <?= $form->field($model, 'long')->textInput() ?>

    <?= $form->field($model, 'mmc')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
