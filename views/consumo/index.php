<?php

use app\models\Consumo;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\search\ConsumoSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Meu Consumo Elétrico';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="consumo-index">

    <div class="row">
        <div class="col-lg-4">
            <h1>
                <?= Html::encode($this->title) ?>
            </h1>
        </div>
        <div class="col-lg-8 text-end">
            <?= Html::a('<i class="fas fa-plus"></i> Adicionar Equipamento', ['create'], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="fas fa-bolt"></i> Calcular Consumo', ['site/consumo-eletrico'], ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="alert alert-info">
                <p>
                    Nessa página você adiciona todos os equipamentos elétricos a serem supridos em sua simulação. <strong>Dispositivos com potências maiores que 800W não são recomendadados para sistemas off grid, como por exemplo, chuveiros elétricos, fornos elétricos, secadores de cabelo, entre outros.</strong> Se possível, evitá-los.
                </p>
            </div>
        </div>
    </div>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="table-responsive">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            // 'filterModel' => $searchModel,
            'columns' => [
                [
                    'class' => ActionColumn::className(),
                    'urlCreator' => function ($action, Consumo $model, $key, $index, $column) {
                        return Url::toRoute([$action, 'id' => $model->id]);
                    }
                ],
                ['class' => 'yii\grid\SerialColumn'],
                // 'id',
                // 'user_id',
                'nome',
                'qtde',
                'potencia_w',
                'minutos_por_dia',
                'dias_por_mes',
                // 'tensao_v',
                // 'corrente_a',
                'tipo_corrente',
                [
                    'attribute' => 'potencia_w',
                    'format' => 'raw',
                    'label' => 'Alerta',
                    'value' => function ($model) {
                        if ($model->potencia_w > 800) {
                            return '<div class="alert alert-danger">Cuidado!</div>';
                        }
                        return '<div class="alert alert-success">OK!</div>';
                    }
                ],
                [
                    'class' => ActionColumn::className(),
                    'urlCreator' => function ($action, Consumo $model, $key, $index, $column) {
                        return Url::toRoute([$action, 'id' => $model->id]);
                    }
                ],
            ],
        ]); ?>
    </div>

    <hr>
    
</div>
