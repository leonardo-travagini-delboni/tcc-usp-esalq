<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Consumo $model */

$this->title = 'Atualizar Consumo: ' . $model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Meu Consumo', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nome, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Atualizar';
?>
<div class="consumo-update">

    <h4><?= Html::encode($this->title) ?></h4>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
