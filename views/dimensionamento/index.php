<?php

use app\models\Dimensionamento;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\search\DimensionamentoSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Meus Dimensionamentos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dimensionamento-index">

    <div class="row">
        <div class="col-lg-8 mb-3">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-lg-4 mb-3 text-end">
            <p>
                <?= Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['site/consumo-eletrico'], ['class' => 'btn btn-secondary']) ?>
                <?= Html::a('<i class="fas fa-plus"></i> Novo Dimensionamento', ['create'], ['class' => 'btn btn-success']) ?>
            </p>
        </div>
    </div>

    <p>
        Abaixo estão tabelados os dimensionamentos realizados junto aos parâmetros de entrada fornecidos pelo usuário.
        Para visualizar mais detalhes, outputs da simulação ou editar tais parâmetros, clique no botão "olho" ou "lápis" da simulação desejada.
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="table-responsive">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            // 'filterModel' => $searchModel,
            'columns' => [
                [
                    'class' => ActionColumn::className(),
                    'urlCreator' => function ($action, Dimensionamento $model, $key, $index, $column) {
                        return Url::toRoute([$action, 'id' => $model->id]);
                    }
                ],
                // ['class' => 'yii\grid\SerialColumn'],
                // 'id',
                // 'user_id',
                // 'simulacao_no',
                [
                    'attribute' => 'simulacao_no',
                    'label' => 'ID',
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
                [
                    'class' => ActionColumn::className(),
                    'urlCreator' => function ($action, Dimensionamento $model, $key, $index, $column) {
                        return Url::toRoute([$action, 'id' => $model->id]);
                    }
                ],
            ],
        ]); ?>
    </div>

</div>
