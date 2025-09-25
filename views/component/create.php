<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Component $model */

$this->title = 'Create Component';
$this->params['breadcrumbs'][] = ['label' => 'Components', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="component-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
