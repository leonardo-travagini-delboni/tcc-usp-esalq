<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Dimensionamento $model */

$this->title = 'Nova Simulação';
$this->params['breadcrumbs'][] = ['label' => 'Dimensionar', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Novo'];
?>
<div class="dimensionamento-create">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0"><?= Html::encode($this->title) ?></h2>
        <?= Html::a('<i class="fas fa-arrow-left"></i>', 
            ['dimensionamento/index'],
            ['class' => 'btn btn-secondary']) ?>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
