<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Consumo $model */

$this->title = $model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Meu Consumo', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

?>
<div class="consumo-view">

    <div class="row">
        <div class="col-lg-8">
            <h3>
                <?= Html::encode($this->title) ?>
            </h3>
        </div>
        <div class="col-lg-4 text-end">
            <?= Html::a('<i class="fas fa-arrow-left"></i> Meu Consumo', ['index'], ['class' => 'btn btn-secondary']) ?>
            <?= Html::a('<i class="fas fa-pencil-alt"></i> Atualizar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="fas fa-trash-alt"></i> Excluir', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Tem certeza que deseja excluir este item?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>

    <?php if($model->potencia_w > 800): ?>
        <div class="alert alert-danger mt-3">
            <h4>
                Atenção
            </h4>
            <p>
                Equipamentos com potência maior que 800W não são recomendados para sistemas off grid, como por exemplo, chuveiros elétricos, fornos elétricos, etc.
            </p>
        </div>
    <?php endif;?>

    <div class="alert alert-info mt-3">
        <h4>
            Descrição
        </h4>
        <p>
            Consiste em <strong><?= $model->qtde ?></strong> equipamento(s) <strong><?= $model->nome ?></strong> com potência de <strong><?= $model->potencia_w ?>W</strong> cada, totalizando <strong><?= $model->qtde * $model->potencia_w ?>W</strong> de potência instalada. Ele(s) opera(m)
            em corrente <strong><?= $model->tipo_corrente ?></strong> em torno de <strong><?= $model->minutos_por_dia ?> minutos por dia</strong> e <strong><?= $model->dias_por_mes ?> dias por mês</strong>, o que resulta em um consumo aproximado de <strong><?= round($model->qtde * $model->potencia_w * ($model->minutos_por_dia/60) * $model->dias_por_mes / 1000, 2) ?> kWh/mês</strong>, ou então de aproximadamente <strong><?= round($model->qtde * $model->potencia_w * ($model->minutos_por_dia/60) * $model->dias_por_mes * 12 / 1000, 2) ?> kWh/ano</strong>.
        </p>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <h4>
                Detalhes
            </h4>
        </div>
    </div>
    <div class="table-responsive">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                // 'id',
                // 'user_id',
                'nome',
                'qtde',
                'potencia_w',
                // 'minutos_por_dia',
                [
                    'attribute' => 'minutos_por_dia',
                    'value' => function ($model) {
                        return $model->minutos_por_dia . ' minutos por dia (aprox. ' . round($model->minutos_por_dia / 60, 2) . ' horas por dia).';
                    },
                ],
                'dias_por_mes',
                // 'tensao_v',
                [
                    'attribute' => 'tensao_v',
                    'visible' => $model->tensao_v != null,
                ],
                // 'corrente_a',
                [
                    'attribute' => 'corrente_a',
                    'visible' => $model->corrente_a != null,
                ],
                'tipo_corrente',
            ],
        ]) ?>
    </div>
</div>
