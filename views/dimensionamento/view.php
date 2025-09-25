<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Dimensionamento $model */

$this->title = 'Dimensionamento ID: ' . $model->simulacao_no;
$this->params['breadcrumbs'][] = ['label' => 'Meus Dimensionamentos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="dimensionamento-view">

    <div class="row">
        <div class="col-lg-6">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-lg-6 text-end">
            <?= Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['index'], ['class' => 'btn btn-secondary']) ?>
            <?= Html::a('<i class="fas fa-plus"></i> Novo', ['create'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('<i class="fas fa-edit"></i>  Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="fas fa-trash"></i>  Excluir', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Tem certeza que deseja excluir este item?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>

    <p>
        Abaixo estão tabelados os inputs fornecidos pelo usuário durante a criação do cenário de dimensionamento.
        Para visualizar os resultados ou editar tais parâmetros, <a href="/dimensionamento/update?id=<?= $model->id; ?>">clique aqui</a>.
    </p>

    <div class="table-responsive">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                // 'id',
                // 'user_id',
                // 'simulacao_no',
                [
                    'attribute' => 'simulacao_no',
                    'label' => 'Dimensionamento ID',
                    'value' => function ($model) {
                        return $model->simulacao_no;
                    },
                ],
                // 'created_at',
                // 'updated_at',
                'latitude',
                'longitude',
                'consumo_diario_cc_wh',
                'consumo_diario_ca_wh',
                'potencia_instalada_cc_w',
                'potencia_instalada_ca_w',
                'efic_bateria',
                'efic_inversor',
                'efic_gerador',
                'efic_elet',
                // 'painel_id',
                [
                    'attribute' => 'painel_id',
                    'label' => 'Modelo do Painel',
                    'value' => function ($model) {
                        return $model->painel->modelo;
                    },
                ],
                'painel_qtde_total',
                'painel_qtde_serie',
                // 'mppt_id',
                [
                    'attribute' => 'mppt_id',
                    'label' => 'Modelo do MPPT',
                    'value' => function ($model) {
                        return $model->mppt->modelo;
                    },
                ],
                'fator_seguranca',
                // 'bateria_id',
                [
                    'attribute' => 'bateria_id',
                    'label' => 'Modelo da Bateria',
                    'value' => function ($model) {
                        return $model->bateria->modelo;
                    },
                ],
                'profundidade_descarga',
                'dias_autonomia',
                'tensao_nominal_cc',
                [
                    'attribute' => 'created_at',
                    'label' => 'Criado em',
                    'format' => ['date', 'php:d/m/Y H:i:s'],
                ],
                [
                    'attribute' => 'updated_at',
                    'label' => 'Atualizado em',
                    'format' => ['date', 'php:d/m/Y H:i:s'],
                ],
            ],
        ]) ?>
    </div>

    <div class="row">
        <div class="col-lg-12 text-end">
            <?= Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['index'], ['class' => 'btn btn-secondary']) ?>
            <?= Html::a('<i class="fas fa-plus"></i> Novo', ['create'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('<i class="fas fa-edit"></i>  Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="fas fa-trash"></i>  Excluir', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Tem certeza que deseja excluir este item?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>


</div>
