<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Consumo $model */

$this->title = 'Adicionar Equipamento';
$this->params['breadcrumbs'][] = ['label' => 'Meu Consumo', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="consumo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
