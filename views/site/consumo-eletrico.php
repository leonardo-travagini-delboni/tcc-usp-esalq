<?php

/** @var yii\web\View $this */

use app\models\Consumo;

use yii\helpers\Html;

$this->title = 'Consumo Elétrico';
$this->params['breadcrumbs'][] = $this->title;

// Criando URLs de avanço e retorno
$url_retorno = '/site/potencial-solar';
$url_avanço = '/dimensionamento/index';

// Extraindo valores importantes:
$consumo = new Consumo();
$equipamentos = $consumo->getEquipamentos();
$consumo_diario = $consumo->getConsumosDiarios();
$potencias = $consumo->getPotencias();
?>
<div class="site-consumo-eletrico">

    <div class="row">
        <div class="col-lg-8 mb-3">
            <h2>Passo 3 - <?= Html::encode($this->title) ?></h2>
            <div class="alert alert-info">
                <p>Calcule o consumo elétrico a ser suprido pelo seu sistema fotovoltaico isolado.</p>
            </div>

            <?php if (empty($equipamentos)) : ?>
                <div class="alert alert-warning mt-3">
                    <h4>
                        Atenção!
                    </h4>
                    <p>
                        Você ainda não cadastrou nenhum equipamento para o cálculo de seu consumo elétrico, portanto não existem resultados disponíveis. <a href="/consumo/create">Clique aqui</a> 
                        para cadastrar um novo equipamento ou <a href="/consumo/index">aqui</a> para ir para a página da relação de todos os seus equipamentos.
                    </p>
                </div>
            <?php else : ?>
                <h4>
                    Valores Totais Estimados
                </h4>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="text-center fw-bold text-white bg-dark">Tipo de Corrente</th>
                                <th class="text-center text-white bg-dark">Potência Instalada<br><small>(W)</small></th>
                                <th class="text-center text-white bg-dark">Consumo Diário<br><small>(kWh/dia)</small></th>
                                <th class="text-center text-white bg-dark">Consumo Mensal<br><small>(kWh/mês)</small></th>
                                <th class="text-center text-white bg-dark">Consumo Anual<br><small>(kWh/ano)</small></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center fw-bold">CC</td>
                                <td class="text-center"><?= $potencias['CC'] ?> W</td>
                                <td class="text-center"><?= round($consumo_diario['CC']/1000, 2) ?> kWh/dia</td>
                                <td class="text-center"><?= round((30*$consumo_diario['CC']/1000), 2) ?> kWh/mês</td>
                                <td class="text-center"><?= round((365*$consumo_diario['CC']/1000), 2) ?> kWh/ano</td>
                            </tr>
                            <tr>
                                <td class="text-center fw-bold">CA</td>
                                <td class="text-center"><?= $potencias['CA'] ?> W</td>
                                <td class="text-center"><?= round($consumo_diario['CA']/1000, 2) ?> kWh/dia</td>
                                <td class="text-center"><?= round((30*$consumo_diario['CA']/1000), 2) ?> kWh/mês</td>
                                <td class="text-center"><?= round((365*$consumo_diario['CA']/1000), 2) ?> kWh/ano</td>
                            </tr>
                            <tr class="table-warning">
                                <td class="text-center fw-bold">Total</td>
                                <td class="text-center fw-bold"><?= $potencias['SISTEMA'] ?> W</td>
                                <td class="text-center fw-bold"><?= round($consumo_diario['SISTEMA']/1000, 2);  ?> kWh/dia</td>
                                <td class="text-center fw-bold"><?= round((30*$consumo_diario['SISTEMA']/1000), 2) ?> kWh/mês</td>
                                <td class="text-center fw-bold"><?= round((365*$consumo_diario['SISTEMA']/1000), 2) ?> kWh/ano</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <div class="row mt-3">
                <div class="col-lg-12 text-center">
                    <a href="<?= $url_retorno ?>" class="btn btn-secondary mt-2"><i class="fas fa-arrow-left"></i> Passo 2 - Potencial Solar</a>
                    <a href="/consumo/index" class="btn btn-primary mt-2"><i class="fas fa-plug"></i> Gerenciar Equipamentos Elétricos</a>
                    <a href="<?= $url_avanço ?>" class="btn btn-success mt-2">Passo 4 - Dimensionamento <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>


        </div>
        <div class="col-lg-4 mb-3">
            <img src="/img/3.png" class="img-fluid" alt="Consumo Elétrico">
        </div>
    </div>




</div>
