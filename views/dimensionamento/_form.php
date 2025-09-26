<?php

use app\models\Coordenada;
use app\models\Consumo;
use app\models\Painel;
use app\models\Bateria;
use app\models\Mppt;

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\BadRequestHttpException;

/** @var yii\web\View $this */
/** @var app\models\Dimensionamento $model */
/** @var yii\widgets\ActiveForm $form */

// Coordenadas do User:
$lat_gps = Yii::$app->user->identity->gps_lat ?? null;
$lng_gps = Yii::$app->user->identity->gps_lng ?? null;
$lat_manual = Yii::$app->user->identity->latitude ?? null;
$lng_manual = Yii::$app->user->identity->longitude ?? null;
$potencial_solar_gps = round(Coordenada::getMmc($lat_gps, $lng_gps), 2);
$potencial_solar_manual = round(Coordenada::getMmc($lat_manual, $lng_manual), 2);
$use_gps = Yii::$app->user->identity->use_gps ?? null;
$hsp_gps = round(Coordenada::getMmc($lat_gps, $lng_gps) / 1000, 0);
$hsp_manual = round(Coordenada::getMmc($lat_manual, $lng_manual) / 1000, 0);
if ($use_gps == 1) {
    if($model->isNewRecord){
        $lat_final = $lat_gps;
        $lng_final = $lng_gps;
    } else{
        $lat_final = $model->latitude;
        $lng_final = $model->longitude;
    }
    $potencial_solar_final = $potencial_solar_gps;
    $hsp_final = $hsp_gps;
} elseif ($use_gps == 0) {
    if($model->isNewRecord){
        $lat_final = $lat_manual;
        $lng_final = $lng_manual;
    } else{
        $lat_final = $model->latitude;
        $lng_final = $model->longitude;
    }
    $potencial_solar_final = $potencial_solar_manual;
    $hsp_final = $hsp_manual;
} else {
    // Lançar erro
    Yii::error('Erro ao obter coordenadas do usuário', 'coordenadas');
    throw new BadRequestHttpException('Coordenadas inválidas. Por favor, refaça as etapas 1 e 2 do dimensionamento,');
}

// Potencias e Consumos do User:
$consumo = new Consumo();
$consumo_diario = $consumo->getConsumosDiarios();
$potencias = $consumo->getPotencias();

// Paineis Solares:
$paineis = Painel::find()->select(['modelo', 'id'])->indexBy('id')->column();

// Baterias:
$baterias = Bateria::find()->select(['modelo', 'id'])->indexBy('id')->column();

// MPPTs:
$mppts = Mppt::find()->select(['modelo', 'id'])->indexBy('id')->column();

// Valores padronizados:
$std_efic_painel = 0.75;
$std_efic_bateria = 0.86;
$std_efic_inversor = 0.90;
$std_efic_elet = 0.90;
$std_fator_segurança = 1.25;
$std_dias_autonomia = 2;
$std_profundidade_descarga = 0.80;
$std_tensao_nominal_cc = 48;

// Valores padrão para o cenário de validação:
// $std_painel = Painel::find()->where(['id' => 5])->one();
// $std_mppt = Mppt::find()->where(['id' => 2])->one();
// $std_bateria = Bateria::find()->where(['id' => 3])->one();
// $std_qtde_painel_total = 70;
// $std_qtde_painel_serie = 10;

$std_painel = Painel::find()->where(['id' => 1])->one();
$std_mppt = Mppt::find()->where(['id' => 1])->one();
$std_bateria = Bateria::find()->where(['id' => 1])->one();
$std_qtde_painel_total = 1;
$std_qtde_painel_serie = 1;

// Caso CREATE:
if ($model->isNewRecord) {
    $model->user_id = Yii::$app->user->id;
    $model->simulacao_no = $model->getUltimoSimulacaoNo() + 1;
    $model->created_at = time();
    $model->updated_at = time();
    $model->consumo_diario_cc_wh = $consumo_diario['CC'];
    $model->consumo_diario_ca_wh = $consumo_diario['CA'];
    $model->potencia_instalada_cc_w = $potencias['CC'];
    $model->potencia_instalada_ca_w = $potencias['CA'];
    // arredondando todos os 4 itens com duas casas decimais
    $model->consumo_diario_cc_wh = round($model->consumo_diario_cc_wh, 2);
    $model->consumo_diario_ca_wh = round($model->consumo_diario_ca_wh, 2);
    $model->potencia_instalada_cc_w = round($model->potencia_instalada_cc_w, 2);
    $model->potencia_instalada_ca_w = round($model->potencia_instalada_ca_w, 2);
    // fator de segurança padrão
    $model->mppt_id = $std_mppt->id;
    $model->fator_seguranca = $std_fator_segurança;
    // valores padrao iniciais
    $model->painel_id = $std_painel->id;
    $model->painel_qtde_total = $std_qtde_painel_total;
    $model->painel_qtde_serie = $std_qtde_painel_serie;
    // componentes da bateria
    $model->bateria_id = $std_bateria->id;
    $model->tensao_nominal_cc = $std_tensao_nominal_cc;
    $model->dias_autonomia = $std_dias_autonomia;
    $model->profundidade_descarga = $std_profundidade_descarga;
} else {
    // Caso UPDATE:
    $model->updated_at = time();
}

function tooltip($text) {
    return '<span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" data-bs-placement="top" title="' . htmlspecialchars($text) . '">
                <i class="fas fa-info-circle text-primary"></i>
            </span>';
}

?>

<div class="dimensionamento-form">

    <hr>

    <h3>Coordenadas e Potencial Solar</h3>
    <p>Equivalente às etapas 1 e 2 da aplicação</p>
    <div class="row py-2">
        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-1" style="background-color: #f8f9fa;">
                    <thead>
                        <tr>
                            <th class="text-center fw-bold text-white bg-dark">Tipo de Coordenadas</th>
                            <th class="text-center text-white bg-dark">Localização Geográfica</th>
                            <th class="text-center text-white bg-dark">Inseridas Manualmente</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center fw-bold">Latitude</td>
                            <td class="text-center"><?= $lat_gps ?> </td>
                            <td class="text-center"><?= $lat_manual ?> </td>
                        </tr>
                        <tr>
                            <td class="text-center fw-bold">Longitude</td>
                            <td class="text-center"><?= $lng_gps ?> </td>
                            <td class="text-center"><?= $lng_manual ?> </td>
                        </tr>
                        <tr>
                            <td style="text-align: center; font-weight: bold;">Potencial Solar</td>
                            <td style="text-align: center;"><?= $potencial_solar_gps ?> kWh/m²/dia</td>
                            <td style="text-align: center;"><?= $potencial_solar_manual ?> kWh/m²/dia</td>
                        </tr>
                        <tr>
                            <td style="text-align: center; font-weight: bold;">HSP</td>
                            <td style="text-align: center;"><?= $hsp_gps; ?> horas/dia</td>
                            <td style="text-align: center;"><?= $hsp_manual; ?> horas/dia</td>
                        </tr>
                        <tr>
                            <td style="text-align: center; font-weight: bold;">Ver no Mapa</td>
                            <td style="text-align: center;">
                                <a href="https://www.google.com/maps/search/?api=1&query=<?= $lat_gps ?>,<?= $lng_gps ?>" target="_blank" class="btn btn-info">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Mapa
                                </a>
                            </td>
                            <td style="text-align: center;">
                                <a href="https://www.google.com/maps/search/?api=1&query=<?= $lat_manual ?>,<?= $lng_manual ?>" target="_blank" class="btn btn-info">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Mapa
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center; font-weight: bold;">Atualizar</td>
                            <td style="text-align: center;">
                                <a href="/site/usar-gps?route=site/minhas-coordenadas&use_gps=1" class="btn btn-dark">
                                    <i class="fas fa-street-view"></i>
                                    Atualizar
                                </a>
                            </td>
                            <td style="text-align: center;">
                                <a href="/site/usar-gps?route=site/potencial-solar&use_gps=0" class="btn btn-dark">
                                    <i class="fas fa-street-view"></i>
                                    Atualizar
                                </a>
                            </td>
                        </tr>
                        <tr class="table-warning">
                            <td class="text-center fw-bold">Escolha Atual</td>
                            <td style="text-align: center;">
                                <?php if($use_gps == 1): ?>
                                    <button type="button" class="btn btn-success" disabled>
                                        <i class="fas fa-check"></i>
                                        JÁ SELECIONADA
                                    </button>
                                <?php else: ?>
                                    <a href="/site/usar-gps?route=dimensionamento/create&use_gps=1" class="btn btn-danger">
                                        <i class="fas fa-sync-alt"></i>
                                        ESCOLHER ESSA
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <?php if($use_gps == 0): ?>
                                    <button type="button" class="btn btn-success" disabled>
                                        <i class="fas fa-check"></i>
                                        JÁ SELECIONADA
                                    </button>
                                <?php else: ?>
                                    <a href="/site/usar-gps?route=dimensionamento/create&use_gps=0" class="btn btn-danger">
                                        <i class="fas fa-sync-alt"></i>
                                        ESCOLHER ESSA
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <?php $form = ActiveForm::begin(); ?>

    <h3>Consumo Elétrico Estimado</h3>
    <p>Equivale à etapa 3 dessa aplicação</p>

    <div class="row">
        <?php if ($model->isNewRecord): ?>
            <p>Conforme os equipamentos cadastrados atualmente na etapa anterior...</p>
        <?php else: ?>
            <p><strong>Atenção!</strong> Os dados abaixo são referente aos equipamentos cadastrados 
            atualmente na etapa 3 e não no momento da criação desse dimensionamento. Para alterar os
            valores retorne à etapa 3, ou então insira manualmente os dados logo abaixo.
        </p>
        <?php endif; ?>
        <div class="row py-2">
            <div class="col-lg-9">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-1" style="background-color: #f8f9fa;">
                    <thead>
                        <tr>
                            <th class="text-center fw-bold text-white bg-dark">Tipo de Corrente</th>
                            <th class="text-center text-white bg-dark">Potência Instalada<br><small>(W)</small></th>
                            <th class="text-center text-white bg-dark">Consumo Diário<br><small>(Wh/dia)</small></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center fw-bold">CC</td>
                            <td class="text-center"><?= $potencias['CC'] ?> W</td>
                            <td class="text-center"><?= round($consumo_diario['CC'], 2) ?> Wh/dia</td>
                        </tr>
                        <tr>
                            <td class="text-center fw-bold">CA</td>
                            <td class="text-center"><?= $potencias['CA'] ?> W</td>
                            <td class="text-center"><?= round($consumo_diario['CA'], 2) ?> Wh/dia</td>
                        </tr>
                        <tr class="table-warning">
                            <td class="text-center fw-bold">Total</td>
                            <td class="text-center fw-bold"><?= $potencias['SISTEMA'] ?> W</td>
                            <td class="text-center fw-bold"><?= round($consumo_diario['SISTEMA'], 2);  ?> Wh/dia</td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>
            <div class="col-lg-3 text-end">
                <div class="row px-2">
                    <p class="text-center">
                        Funcionalidades disponíveis:
                    </p>
                    <a href="/site/consumo-eletrico" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Voltar para Etapa 3
                    </a>
                    <a href="/consumo/index" class="btn btn-primary">
                        <i class="fas fa-plug"></i>
                        Gerenciar Equipamentos
                    </a>
                    <a href="/consumo/create" class="btn btn-success">
                        <i class="fas fa-plus"></i>
                        Cadastrar Equipamento
                    </a>
                </div>
            </div>
        </div>
        <p class="py-2">Ou então, <u>caso queira ajustar manualmente os dados</u>, utilize os campos abaixo... </p>
        <div class="alert alert-dark">
            <h4>Parâmetros Finais</h4>
            <div class="row">
                <div class="col-lg-3">
                    <?= $form->field($model, 'consumo_diario_cc_wh')->textInput([
                        'value' => $model->consumo_diario_cc_wh, 
                        'id' => 'consumo-cc', 
                        'maxlength' => true,
                        'type' => 'number',
                        'step' => '0.01',
                        'min' => '0.00',
                        ])  
                        ?>
                </div>
                <div class="col-lg-3">
                    <?= $form->field($model, 'potencia_instalada_cc_w')->textInput([
                        'value' => $model->potencia_instalada_cc_w, 
                        'id' => 'potencia-cc', 
                        'maxlength' => true,
                        'type' => 'number',
                        'step' => '0.01',
                        'min' => '0.00',
                        ])  
                        ?>
                </div>
                <div class="col-lg-3">
                    <?= $form->field($model, 'consumo_diario_ca_wh')->textInput([
                        'value' => $model->consumo_diario_ca_wh, 
                        'id' => 'consumo-ca', 
                        'maxlength' => true,
                        'type' => 'number',
                        'step' => '0.01',
                        'min' => '0.00',
                        ]) 
                        ?>
                </div>
                <div class="col-lg-3">
                    <?= $form->field($model, 'potencia_instalada_ca_w')->textInput([
                        'value' => $model->potencia_instalada_ca_w, 
                        'id' => 'potencia-ca', 
                        'maxlength' => true,
                        'type' => 'number',
                        'step' => '0.01',
                        'min' => '0.00',
                        ]) 
                        ?>
                </div>
            </div>
            <hr>
            <div class="row text-center align-items-center" style="justify-content: center;">
                <div class="col-lg-6">
                    <p><strong>Potência Instalada Total:</strong> <span id="potencia-total-value">-</span> [W]</p>
                </div>
                <div class="col-lg-6">
                    <p><strong>Consumo Diário Total:</strong> <span id="consumo-total-value">-</span> [W.h/dia]</p>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <h2>Etapa 4 - Demais Parâmetros para o Dimensionamento</h2>

    <?php // Parâmetros ocultos ao usuário ?>
    <?= $form->field($model, 'user_id')->hiddenInput(['value' => $model->user_id])->label(false) ?>
    <?= $form->field($model, 'simulacao_no')->hiddenInput(['value' => $model->simulacao_no])->label(false) ?>
    <?= $form->field($model, 'latitude')->hiddenInput(['value' => $lat_final])->label(false) ?>
    <?= $form->field($model, 'longitude')->hiddenInput(['value' => $lng_final])->label(false) ?>

    <div class="alert alert-dark">
        <h4>Coeficientes de Eficiência</h4>
        <div class="row">
            <div class="col-lg-3">
                <?php if($model->isNewRecord): ?>
                    <?= $form->field($model, 'efic_gerador')->input('number', [
                        'id' => 'efic-painel',
                        'step' => '0.01',
                        'min' => '0.00',
                        'max' => '1.00',
                        'value' => $std_efic_painel,
                    ])->label('Eficiência do Painel')->hint('Padrão: ' . $std_efic_painel . ' (entre 0.00 e 1.00)') ?>
                <?php else: ?>
                    <?= $form->field($model, 'efic_gerador')->input('number', [
                        'id' => 'efic-painel',
                        'step' => '0.01',
                        'min' => '0.00',
                        'max' => '1.00',
                    ])->label('Eficiência do Painel')->hint('Padrão: ' . $std_efic_painel . ' (entre 0.00 e 1.00)') ?>
                <?php endif; ?>
            </div>
            <div class="col-lg-3">
                <?php if($model->isNewRecord): ?>
                    <?= $form->field($model, 'efic_bateria')->input('number', [
                        'id' => 'efic-bateria',
                        'step' => '0.01',
                        'min' => '0.00',
                        'max' => '1.00',
                        'value' => $std_efic_bateria,
                    ])->label('Eficiência da Bateria')->hint('Padrão: ' . $std_efic_bateria . ' (entre 0.00 e 1.00)') ?>
                <?php else: ?>
                    <?= $form->field($model, 'efic_bateria')->input('number', [
                        'id' => 'efic-bateria',
                        'step' => '0.01',
                        'min' => '0.00',
                        'max' => '1.00',
                    ])->label('Eficiência da Bateria')->hint('Padrão: ' . $std_efic_bateria . ' (entre 0.00 e 1.00)') ?>
                <?php endif; ?>
            </div>
            <div class="col-lg-3">
                <?php if($model->isNewRecord): ?>
                    <?= $form->field($model, 'efic_inversor')->input('number', [
                        'id' => 'efic-inversor',
                        'step' => '0.01',
                        'min' => '0.00',
                        'max' => '1.00',
                        'value' => $std_efic_inversor,
                    ])->label('Eficiência do MPPT')->hint('Padrão: ' . $std_efic_inversor . ' (entre 0.00 e 1.00)') ?>
                <?php else: ?>
                    <?= $form->field($model, 'efic_inversor')->input('number', [
                        'id' => 'efic-inversor',
                        'step' => '0.01',
                        'min' => '0.00',
                        'max' => '1.00',
                    ])->label('Eficiência do MPPT')->hint('Padrão: ' . $std_efic_inversor . ' (entre 0.00 e 1.00)') ?>
                <?php endif; ?>
            </div>
            <div class="col-lg-3">
                <?php if($model->isNewRecord): ?>
                    <?= $form->field($model, 'efic_elet')->input('number', [
                        'id' => 'efic-elet',
                        'step' => '0.01',
                        'min' => '0.00',
                        'max' => '1.00',
                        'value' => $std_efic_elet,
                    ])->label('Eficiência Geral (Fios, etc.)')->hint('Padrão: ' . $std_efic_elet . ' (entre 0.00 e 1.00)') ?>
                <?php else: ?>
                    <?= $form->field($model, 'efic_elet')->input('number', [
                        'id' => 'efic-elet',
                        'step' => '0.01',
                        'min' => '0.00',
                        'max' => '1.00',
                    ])->label('Eficiência Geral (Fios, etc.)')->hint('Padrão: ' . $std_efic_elet . ' (entre 0.00 e 1.00)') ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="row text-center align-items-center" style="justify-content: center;">
            <div class="row">
                <div class="col-lg-4">
                    <p>
                        <?= tooltip('Energia consumida diariamente em corrente contínua (Lcc)') ?>
                        <strong>Lcc:</strong> 
                        <span id="lcc-value">-</span> [W.h/dia]
                    </p>
                </div>
                <div class="col-lg-4">
                    <p>
                        <?= tooltip('Energia consumida diariamente em corrente alternada (Lca)') ?>
                        <strong>Lca:</strong> 
                        <span id="lca-value">-</span> [W.h/dia]
                    </p>
                </div>
                <div class="col-lg-4">
                    <p>
                        <?= tooltip('Energia ativa diária total do sistema (L)') ?>
                        <strong>L:</strong> 
                        <span id="ltotal-value">-</span> [W.h/dia]
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-dark">
        <h4>Controlador-Inversor MPPT</h4>
        <div class="row">
            <div class="col-lg-6">
                <?= $form->field($model, 'mppt_id')->dropDownList(
                    $mppts,
                    [
                        'prompt' => 'Selecione um MPPT',
                        'id' => 'mppt-id',
                        ]
                )->label('Modelo') ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'fator_seguranca')->textInput([
                    'value' => $model->fator_seguranca,
                    'type' => 'number',
                    'step' => '0.01',
                    'min' => '1.00',
                    'max' => '10.00',
                    'id' => 'fator-seguranca',
                    'maxlength' => true,
                ])->label('Fator de Segurança')->hint('Padrão: ' . $std_fator_segurança . ' (entre 1.00 e 10.00)') ?>
            </div>
        </div>
        <div class="row text-center align-items-center" style="justify-content: center;">
            <div class="row">
                <div class="col-lg-4">
                    <p>
                        <?= tooltip('Mínima tensão de operação do controlador MPPT (Vmppt,min)') ?>
                        <strong>Vmppt,min:</strong> 
                        <span id="mppt-vmpptmin-value">-</span> [Vcc]
                    </p>
                </div>
                <div class="col-lg-4">
                    <p>
                        <?= tooltip('Máxima tensão de operação do controlador MPPT (Vmppt,max)') ?>
                        <strong>Vmppt,max:</strong> <span id="mppt-vmpptmax-value">-</span> [Vca]
                    </p>
                </div>
                <div class="col-lg-4">
                    <p>
                        <?= tooltip('Corrente máxima de operação do controlador MPPT (Ictl)') ?>
                        <strong>Ictl:</strong> <span id="mppt-ictl-value">-</span> [A]
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-dark">
        <h4>Painel Solar</h4>
        <div class="row">
            <div class="col-lg-12">
                <?= $form->field($model, 'painel_id')->dropDownList(
                    $paineis,
                    [
                        'prompt' => 'Selecione um Painel',
                        'id' => 'painel-id',
                    ]
                )->label('Modelo do Painel Solar') ?>
            </div>
        </div>
        <div class="row text-center align-items-center" style="justify-content: center;">
            <div class="row">
                <div class="col-lg-4 text-center">
                    <p>
                        <?= tooltip('Comprimento do painel (C)') ?>
                        <strong>C:</strong> <span id="painel-c-value">-</span> [m]
                    </p>
                </div>
                <div class="col-lg-4 text-center">
                    <p>
                        <?= tooltip('Largura do painel (L)') ?>
                        <strong>L:</strong> <span id="painel-l-value">-</span> [m]
                    </p>
                </div>
                <div class="col-lg-4 text-center">
                    <p>
                        <strong>Área Mínima de Painéis:</strong> <span id="painel-areamin-value">-</span> [m²]
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 text-center">
                    <p>
                        <?= tooltip('Potência máxima do painel (Pmax)') ?>
                        <strong>Pmax:</strong> <span id="painel-pmax-value">-</span> [W]
                    </p>
                </div>
                <div class="col-lg-4 text-center">
                    <p>
                        <?= tooltip('Corrente máxima do painel (Imax)') ?>
                        <strong>Imax:</strong> <span id="painel-imax-value">-</span> [A]
                    </p>
                </div>
                <div class="col-lg-4 text-center">
                    <p>
                        <?= tooltip('Tensão máxima do painel (Vmax)') ?>
                        <strong>Vmax:</strong> <span id="painel-vmax-value">-</span> [V]
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 text-center">
                    <p>
                        <?= tooltip('Tensão de circuito aberto do painel (Voc)') ?>
                        <strong>Voc:</strong> <span id="painel-voc-value">-</span> [V]
                    </p>
                </div>
                <div class="col-lg-4 text-center">
                    <p>
                        <?= tooltip('Corrente de curto-circuito do painel (Icc)') ?>
                        <strong>Icc:</strong> <span id="painel-icc-value">-</span> [A]
                    </p>
                </div>
                <div class="col-lg-4 text-center">
                    <p>
                        <?= tooltip('Temperatura mínima de operação do painel (Tmin,oper)') ?>
                        <strong>Tmin,oper:</strong> <span id="painel-tminoper-value">-</span> [ºC]
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 text-center">
                    <p>
                        <?= tooltip('Temperatura máxima de operação do painel (Tmax,oper)') ?>
                        <strong>Tmax,oper:</strong> <span id="painel-tmaxoper-value">-</span> [ºC]
                    </p>
                </div>
                <div class="col-lg-4 text-center">
                    <p>
                        <?= tooltip('Coeficiente de temperatura do painel (Beta)') ?>
                        <strong>Beta:</strong> <span id="painel-beta-value">-</span> [1/ºC]
                    </p>
                </div>
                <div class="col-lg-4 text-center">
                    <p>
                        <?= tooltip('Potência necessária dos painéis inicialmente (Pm,a priori)') ?>
                        <strong>Pm (a priori):</strong> <span id="painel-pmwp-value">-</span> [Wp]
                    </p>
                </div>
            </div>
        </div>
        <hr>
        <h5>Total de Painéis</h5>
        <div class="row align-items-center" style="justify-content: center;">
            <div class="col-lg-3">
                <?= $form->field($model, 'painel_qtde_total')->textInput(
                    [
                        'placeholder' => 'Total de Paineis',
                        'value' => $model->painel_qtde_total,
                        'id' => 'painel-qtde-total',
                        'maxlength' => true,
                        'type' => 'number',
                        'step' => '1',
                        'min' => '1',
                    ]
                )->label('Quantidade Total de Paineis') ?>
            </div>
            <div class="col-lg-9 text-center">
                <div class="row align-items-center" style="justify-content: center;">
                    <div class="col-lg-4 text-center">
                        <div class="alert alert-warning">
                            <p><strong>Sugestão:</strong> <span id="sugestao-painel-qtde">-</span> Painéis</p>
                        </div>
                    </div>
                    <div class="col-lg-4 text-center">
                        <p><strong>Qtde Mínima:</strong> <span id="painel-qtdemin-value">-</span> Painéis</p>
                    </div>
                    <div class="col-lg-4 text-center">
                        <p><strong>Check:</strong> <span id="check-painel-total">-</span></p>
                    </div>
                </div>
            </div>
        </div>
        <p>Portanto...</p>
        <div class="row align-items-center" style="justify-content: center;">
            <div class="col-lg-3 text-center">
                <p>
                    <?= tooltip('Potência total final dos painéis (Pm)') ?>
                    <strong>Pm (final):</strong> <span id="painel-pmwp-final-value">-</span> [Wp]
                </p>
            </div>
            <div class="col-lg-3 text-center">
                <p>
                    <?= tooltip('Área total final dos painéis (A)') ?>
                    <strong>Área (final):</strong> <span id="painel-area-final-value">-</span> [m²]
                </p>
            </div>
            <div class="col-lg-3 text-center">
                <p>
                    <?= tooltip('Tensão máxima de operação do painel corrigido pela temperatura mínima local (VmpTmin)') ?>
                    <strong>VmpTmin:</strong> <span id="painel-vmptmin-value">-</span> [V]
                </p>
            </div>
            <div class="col-lg-3 text-center">
                <p>
                    <?= tooltip('Tensão máxima de operação do painel corrigido pela temperatura máxima local (VmpTmax)') ?>
                    <strong>VmpTmax:</strong> <span id="painel-vmptmax-value">-</span> [V]
                </p>
            </div>
        </div>

        <hr>

        <h5>Painéis em Série</h5>
        <div class="row align-items-center" style="justify-content: center;">
            <div class="col-lg-3">
                <?= $form->field($model, 'painel_qtde_serie')->textInput(
                    [
                        'placeholder' => 'Total em Série',
                        'value' => $model->painel_qtde_serie,
                        'id' => 'painel-qtde-serie',
                        'maxlength' => true,
                        'type' => 'number',
                        'step' => '1',
                        'min' => '1',
                    ]
                )->label('Quantidade de Painéis em Série') ?>
            </div>
            <div class="col-lg-9 text-center">
                <div class="row align-items-center" style="justify-content: center;">
                    <div class="col-lg-3 text-center">
                        <div class="alert alert-warning">
                            <p><strong>Sugestão:</strong> <span id="painel-serie-sugestao">-</span> Painéis</p>
                        </div>
                    </div>
                    <div class="col-lg-3 text-center">
                        <p><strong>Mínimo:</strong> <span id="painel-serie-min-value">-</span> Painéis</p>
                    </div>
                    <div class="col-lg-3 text-center">
                        <p><strong>Máximo:</strong> <span id="painel-serie-max-value">-</span> Painéis</p>
                    </div>
                    <div class="col-lg-3 text-center">
                        <p><strong>Check:</strong> <span id="painel-serie-check">-</span></p>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <h5>Painéis em Paralelo</h5>
        <div class="row align-items-center" style="justify-content: center;">
            <div class="col-lg-4 text-center">
                <p><strong>Quantidade em Paralelo:</strong> <span id="painel-paralelo-qtde-value">-</span> Painéis</p>
            </div>
            <div class="col-lg-4 text-center">
                <p>
                    <strong>Icurto-circuito:</strong> <span id="painel-paralelo-icurtocircuito-value">-</span> [A]
                </p>
            </div>
            <div class="col-lg-4 text-center">
                <p><strong>Check:</strong> <span id="painel-paralelo-check">-</span></p>
            </div>
        </div>
    </div>

    <div class="alert alert-dark">
        <h4>Bateria</h4>
        <div class="row">
            <div class="col-lg-3">
                <?= $form->field($model, 'bateria_id')->dropDownList(
                    $baterias,
                    [
                        'prompt' => 'Selecione uma Bateria',
                        'id' => 'bateria-id',
                        ]
                )->label('Modelo') ?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'profundidade_descarga')->textInput([
                    'value' => $model->profundidade_descarga,
                    'type' => 'number',
                    'step' => '0.01',
                    'min' => '0.00',
                    'max' => '1.00',
                    'id' => 'profundidade-descarga',
                    'maxlength' => true,
                ])->label('Profundidade de Descarga')->hint('Padrão: ' . $std_profundidade_descarga . ' (entre 0.00 e 1.00)') ?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'dias_autonomia')->textInput(
                    [
                        'value' => $model->dias_autonomia,
                        'type' => 'number',
                        'step' => '1',
                        'min' => '1',
                        'max' => '365',
                        'id' => 'dias-autonomia',
                        'maxlength' => true,
                    ]
                )->label('Dias de Autonomia')->hint('Padrão: ' . $std_dias_autonomia . ' (entre 1 e 365)'
                ) ?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'tensao_nominal_cc')->dropDownList(
                    [
                        12 => '12v',
                        24 => '24v',
                        36 => '36v',
                        48 => '48v',
                    ],
                    [
                        'id' => 'tensao-nominal-cc',
                        'disabled' => true,
                    ]
                )->label('Tensão Nominal CC')->hint('Padrão é 48 [V].') ?>
            </div>
        </div>
        <div class="row text-center align-items-center py-2" style="justify-content: center;">
            <div class="col-lg-3">
                <p><strong>Altura:</strong> <span id="bateria-altura-value">-</span> [m]</p>
            </div>
            <div class="col-lg-3">
                <p><strong>Comprimento:</strong> <span id="bateria-comprimento-value">-</span> [m]</p>
            </div>
            <div class="col-lg-3">
                <p><strong>Largura:</strong> <span id="bateria-largura-value">-</span> [m]</p>
            </div>
            <div class="col-lg-3">
                <p><strong>Tensão Nominal:</strong> <span id="bateria-tensaonominal-value">-</span> [Ah]</p>
            </div>
        </div>
        <div class="row text-center align-items-center" style="justify-content: center;">
            <div class="col-lg-4">
                <p>
                    <?= tooltip('Capacidade da bateria unitária para o regime de descarga em 20 horas') ?>
                    <strong>CBIc20,bat:</strong> <span id="bateria-cbic20bat-value">-</span> [Ah]
                </p>
            </div>
            <div class="col-lg-4">
                <p>
                    <?= tooltip('Capacidade total necessária da bateria para o regime de descarga em 20 horas') ?>
                    <strong>CBIc20:</strong> <span id="bateria-cbic20-value">-</span> [Ah]
                </p>
            </div>
            <div class="col-lg-4">
                <p>
                    <strong>Área Baterias:</strong> <span id="bateria-aream2-value">-</span> [m²]
                </p>
            </div>
        </div>
        <div class="row text-center align-items-center" style="justify-content: center;">
            <div class="col-lg-4">
                <p><strong>Total:</strong> <span id="bateria-qtdetotal-value">-</span> Baterias</p>
            </div>
            <div class="col-lg-4">
                <p><strong>Em Série:</strong> <span id="bateria-qtdeserie-value">-</span> Baterias</p>
            </div>
            <div class="col-lg-4">
                <p><strong>Em Paralelo</strong> <span id="bateria-qtdeparalelo-value">-</span> Baterias</p>
            </div>
        </div>
    </div>

    <div class="alert alert-dark">
        <h4>Verificações finais...</h4>
        <div class="row py-2">
            <p>Retomando o controlador-inversor...</p> 
        </div>
        <div class="row text-center align-items-center" style="justify-content: center;">
            <div class="row">
                <div class="col-lg-4">
                    <p><strong>Corrente Máxima MPPT:</strong> <span id="mppt-imax-value">-</span> [A]</p>
                </div>
                <div class="col-lg-4">
                    <p><strong>Quantidade de MPPT em Paralelo:</strong> <span id="mppt-qtdeparalelo-value">-</span> MPPTs</p>
                </div>
                <div class="col-lg-4">
                    <p>
                        <?= tooltip('Tensão de circuito aberto do painel corrigido pela temperatura mínima local (Voc,tmin)') ?>
                        <strong>Voc,tmin</strong> <span id="mppt-voctmin-value">-</span> [V]
                    </p>
                </div>
            </div>
        </div>
        <hr>
        <h5 class="text-center">
            Primeira Condição de Existência: 
            <strong>Npaineis,serie * Voc,tmin < Vcmax</strong>
            <?= tooltip('A tensão de circuito aberto do painel corrigido pela temperatura mínima local (Voc,tmin), dada a quantidade de painéis em uma "string", deve ser menor que a tensão máxima de entrada do inversor (Vcmax)') ?>
        </h5>
        <p>Portanto...</p>
        <div class="row text-center align-items-center py-2" style="justify-content: center;">
            <div class="row">
                <div class="col-lg-4">
                    <p>
                        <strong>Npaineis,série * Voc,tmin:</strong> 
                        <span id="mppt-fator1-check1-value">-</span> [V]
                    </p>
                </div>
                <div class="col-lg-4">
                    <p><strong>Vcmax:</strong> <span id="mppt-vcmax-value">-</span> [Vca]</p>
                </div>
                <div class="col-lg-4">
                    <p><strong>Check</strong> <span id="check-primeira-existencia-value">-</span></p>
                </div>
            </div>
        </div>
        <hr>
        <h5 class="text-center">
            Segunda Condição de Existência:
            <strong>Pinversor[W] >= Pinstalada [W]</strong>
            <?= tooltip('A potência do inversor deve ser maior ou igual à potência total instalada no sistema') ?>
        </h5>
        <p>Portanto...</p>
        <div class="row text-center align-items-center py-2" style="justify-content: center;">
            <div class="row">
                <div class="col-lg-4">
                    <p><strong>Pinversor</strong> <span id="mppt-pinvmax-value">-</span> [W]</p>
                </div>
                <div class="col-lg-4">
                    <p><strong>Pinstalada</strong> <span id="mppt-pinstalada-value">-</span> [W]</p>
                </div>
                <div class="col-lg-4">
                    <p><strong>Check:</strong> <span id="check-segunda-existencia-value">-</span></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 text-center">
            <?= Html::a('<i class="fas fa-arrow-left"></i> Voltar ', ['/dimensionamento/index'], ['class' => 'btn btn-secondary']) ?>
            <?= Html::submitButton('Salvar e Prosseguir <i class="fas fa-arrow-right"></i>', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
    // Inicializar tooltips do Bootstrap
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    document.addEventListener('DOMContentLoaded', function () {

        // INPUTS DO FORMS
        const consumoCCInput = document.getElementById('consumo-cc');
        const consumoCAInput = document.getElementById('consumo-ca');
        const potenciaCCInput = document.getElementById('potencia-cc');
        const potenciaCAInput = document.getElementById('potencia-ca');
        const eficPainelInput = document.getElementById('efic-painel');
        const eficBateriaInput = document.getElementById('efic-bateria');
        const eficInversorInput = document.getElementById('efic-inversor');
        const eficEletInput = document.getElementById('efic-elet');
        const mpptIdInput = document.getElementById('mppt-id');
        const fatorSegurancaInput = document.getElementById('fator-seguranca');
        const painelIdInput = document.getElementById('painel-id');
        const painelQtdeTotalInput = document.getElementById('painel-qtde-total');
        const painelQtdeSerieInput = document.getElementById('painel-qtde-serie');
        const bateriaIdInput = document.getElementById('bateria-id');
        const profundidadeDescargaInput = document.getElementById('profundidade-descarga');
        const diasAutonomiaInput = document.getElementById('dias-autonomia');
        const tensaoNominalCCInput = document.getElementById('tensao-nominal-cc');

        //  OUTPUTS DO FORM
        const lccValue = document.getElementById('lcc-value');
        const lcaValue = document.getElementById('lca-value');
        const ltotalValue = document.getElementById('ltotal-value');
        const mpptModeloValue = document.getElementById('mppt-modelo-value');
        const mpptVmpptminValue = document.getElementById('mppt-vmpptmin-value');
        const mpptVmpptmaxValue = document.getElementById('mppt-vmpptmax-value');
        const mpptIctlValue = document.getElementById('mppt-ictl-value');
        const painelCValue = document.getElementById('painel-c-value');
        const painelLValue = document.getElementById('painel-l-value');
        const painelPmaxValue = document.getElementById('painel-pmax-value');
        const painelImaxValue = document.getElementById('painel-imax-value');
        const painelVmaxValue = document.getElementById('painel-vmax-value');
        const painelVocValue = document.getElementById('painel-voc-value');
        const painelIccValue = document.getElementById('painel-icc-value');
        const painelTminoperValue = document.getElementById('painel-tminoper-value');
        const painelTmaxoperValue = document.getElementById('painel-tmaxoper-value');
        const painelBetaValue = document.getElementById('painel-beta-value');
        const painelPmwpValue = document.getElementById('painel-pmwp-value');
        const painelQtdeminValue = document.getElementById('painel-qtdemin-value');
        const painelAreaminValue = document.getElementById('painel-areamin-value');
        const potenciaTotalValue = document.getElementById('potencia-total-value');
        const consumoTotalValue = document.getElementById('consumo-total-value');
        const sugestaoPainelQtdeValue = document.getElementById('sugestao-painel-qtde');
        const checkPainelTotalValue = document.getElementById('check-painel-total');

        const painelPmwpFinalValue = document.getElementById('painel-pmwp-final-value');
        const painelAreaFinalValue = document.getElementById('painel-area-final-value');
        const painelVmptminValue = document.getElementById('painel-vmptmin-value');
        const painelVmptmaxValue = document.getElementById('painel-vmptmax-value');

        const painelSerieSugestaoValue = document.getElementById('painel-serie-sugestao');
        const painelSerieMinValue = document.getElementById('painel-serie-min-value');
        const painelSerieMaxValue = document.getElementById('painel-serie-max-value');
        const checkPainelSerieValue = document.getElementById('painel-serie-check');

        const painelParaleloQtdeValue = document.getElementById('painel-paralelo-qtde-value');
        const checkPainelParaleloValue = document.getElementById('painel-paralelo-check');
        const painelParaleloIcurtocircuitoValue = document.getElementById('painel-paralelo-icurtocircuito-value');

        const bateriaAlturaValue = document.getElementById('bateria-altura-value');
        const bateriaComprimentoValue = document.getElementById('bateria-comprimento-value');
        const bateriaLarguraValue = document.getElementById('bateria-largura-value');
        const bateriaTensaonominalValue = document.getElementById('bateria-tensaonominal-value');
        const bateriaCbiC20batValue = document.getElementById('bateria-cbic20bat-value');
        const bateriaCbiC20Value = document.getElementById('bateria-cbic20-value');
        const bateriaAream2Value = document.getElementById('bateria-aream2-value');
        const bateriaQtdeTotalValue = document.getElementById('bateria-qtdetotal-value');
        const bateriaQtdeSerieValue = document.getElementById('bateria-qtdeserie-value');
        const bateriaQtdeParaleloValue = document.getElementById('bateria-qtdeparalelo-value');

        const mpptImaxValue = document.getElementById('mppt-imax-value');
        const mpptQtdeParaleloValue = document.getElementById('mppt-qtdeparalelo-value');
        const mpptVoctminValue = document.getElementById('mppt-voctmin-value');

        const mpptFator1Check1Value = document.getElementById('mppt-fator1-check1-value');
        const mpptVcmaxValue = document.getElementById('mppt-vcmax-value');
        const checkPrimeiraExistenciaValue = document.getElementById('check-primeira-existencia-value');

        const mpptPinvmaxValue = document.getElementById('mppt-pinvmax-value');
        const mpptPinstaladaValue = document.getElementById('mppt-pinstalada-value');
        const checkSegundaExistenciaValue = document.getElementById('check-segunda-existencia-value');

        // Funcao de update
        function calcularValores() {

            // INPUTS DO FORMS
            const consumoCC = parseFloat(consumoCCInput.value) || 0;
            const consumoCA = parseFloat(consumoCAInput.value) || 0;
            const potenciaCC = parseFloat(potenciaCCInput.value) || 0;
            const potenciaCA = parseFloat(potenciaCAInput.value) || 0;
            const eficPainel = parseFloat(eficPainelInput.value) || 0;
            const eficBateria = parseFloat(eficBateriaInput.value) || 0;
            const eficInversor = parseFloat(eficInversorInput.value) || 0;
            const fatorSeguranca = parseFloat(fatorSegurancaInput.value) || 0;
            const mpptId = parseInt(mpptIdInput.value) || 0;
            const eficElet = parseFloat(eficEletInput.value) || 0;
            const painelId = parseInt(painelIdInput.value) || 0;
            const painelQtdeTotal = parseInt(painelQtdeTotalInput.value) || 0;
            const painelQtdeSerie = parseInt(painelQtdeSerieInput.value) || 0;
            const bateriaId = parseInt(bateriaIdInput.value) || 0;
            const profundidadeDescarga = parseFloat(profundidadeDescargaInput.value) || 0;
            const diasAutonomia = parseInt(diasAutonomiaInput.value) || 0;
            const tensaoNominalCC = parseInt(tensaoNominalCCInput.value) || 0;

            // CALCULOS
            const potenciaTotal = potenciaCC + potenciaCA;
            const consumoTotal = consumoCC + consumoCA;

            const lcc = eficBateria > 0 ? (consumoCC / eficBateria).toFixed(2) : '-';
            const lca = (eficBateria > 0 && eficInversor > 0) ? (consumoCA / (eficBateria * eficInversor)).toFixed(2) : '-';
            const ltotal = (parseFloat(lcc) + parseFloat(lca)).toFixed(2);

            // Parametros do MPPT
            const mppt = <?= json_encode(Mppt::find()->indexBy('id')->asArray()->all()) ?>;
            const mpptEscolhido = mppt[mpptId] || null;
            const mpptModelo = mpptEscolhido ? mpptEscolhido.modelo : '-';
            const mpptVmpptminv = mpptEscolhido ? mpptEscolhido.vmppt_min_v : '-';
            const mpptVmpptmaxv = mpptEscolhido ? mpptEscolhido.vmppt_max_v : '-';
            const mpptIctl = mpptEscolhido ? mpptEscolhido.ictl_a : '-';
            const mpptPinvmax = mpptEscolhido ? mpptEscolhido.p_inv_max : '-';

            // Parametros do PAINEL
            const painel = <?= json_encode(Painel::find()->indexBy('id')->asArray()->all()) ?>;
            const painelEscolhido = painel[painelId] || null;
            const painelC = painelEscolhido ? painelEscolhido.comprimento_m : '-';
            const painelL = painelEscolhido ? painelEscolhido.largura_m : '-';
            const painelPmax = painelEscolhido ? painelEscolhido.pmax_w : '-';
            const painelImax = painelEscolhido ? painelEscolhido.imax_a : '-';
            const painelVmax = painelEscolhido ? painelEscolhido.vmax_v : '-';
            const painelVoc = painelEscolhido ? painelEscolhido.voc_v : '-';
            const painelIcc = painelEscolhido ? painelEscolhido.icc_a : '-';
            const painelTminoper = painelEscolhido ? painelEscolhido.tmin_oper_celsius : '-';
            const painelTmaxoper = painelEscolhido ? painelEscolhido.tmax_oper_celsius : '-';
            const painelBeta = painelEscolhido ? painelEscolhido.beta_1_sobre_celsius : '-';

            // Cálculos para o Painel
            const painelPmwp = parseFloat(ltotal) / (<?= json_encode($hsp_final) ?> * eficPainel * eficElet);
            const painelQtdemin = Math.ceil(painelPmwp / painelPmax);
            const painelAreamin = painelQtdemin * painelC * painelL;
            sugestaoPainelQtde = painelQtdemin;
            if (painelQtdemin % 2 !== 0) {
                sugestaoPainelQtde = painelQtdemin + 1;
            }
            // O check painel deve ser um span-button de success se atende as condicoes ou de danger se nao
            if (painelQtdemin <= painelQtdeTotal) {
                if(painelQtdeTotal % 2 !== 0) {
                    checkPainelTotalValue.textContent = 'QTDE ÍMPAR!';
                    checkPainelTotalValue.classList.add('btn', 'btn-danger');
                    checkPainelTotalValue.classList.remove('btn-success');
                } else {
                    checkPainelTotalValue.textContent = 'OK!';
                    checkPainelTotalValue.classList.add('btn', 'btn-success');
                    checkPainelTotalValue.classList.remove('btn-danger');
                }
            } else {
                checkPainelTotalValue.textContent = 'INSUFICIENTE!';
                checkPainelTotalValue.classList.add('btn', 'btn-danger');
                checkPainelTotalValue.classList.remove('btn-success');
            }

            painelPmwpFinal = painelQtdeTotal * painelPmax;
            painelAreaFinal = painelQtdeTotal * painelC * painelL;
            painelVmptmin = painelVmax * (1 + painelBeta * (painelTminoper - 25));
            painelVmptmax = painelVmax * (1 + painelBeta * (painelTmaxoper - 25));

            painelSerieMin = Math.ceil(mpptVmpptminv / painelVmptmax);
            painelSerieMax = Math.floor(mpptVmpptmaxv / painelVmptmin);
            razaoSerie = Math.floor((painelSerieMax + painelSerieMin) / 2);

            if(razaoSerie % 2 !== 0){
                razaoSerie = razaoSerie - 1;
            } else if (razaoSerie % 2 === 0){
                razaoSerie = razaoSerie;
            }
            painelSerieSugestao = razaoSerie;

            // if(painelQtdeTotal <= razaoSerie){
            //     painelSerieSugestao = painelQtdeTotal;
            // } else {
            //     painelSerieSugestao = razaoSerie;
            // }

            // painelSerieSugestao = razaoSerie;
            if (painelSerieMin <= painelQtdeSerie && painelQtdeSerie <= painelSerieMax) {
                checkPainelSerieValue.textContent = 'OK!';
                checkPainelSerieValue.classList.add('btn', 'btn-success');
                checkPainelSerieValue.classList.remove('btn-danger');
            } else {
                checkPainelSerieValue.textContent = 'Fora dos Limites!';
                checkPainelSerieValue.classList.add('btn', 'btn-danger');
                checkPainelSerieValue.classList.remove('btn-success');
            }

            painelParaleloQtde = (painelQtdeTotal / painelQtdeSerie);
            painelParaleloIcurtocircuito = painelIcc * painelParaleloQtde;
            if (painelParaleloQtde > 1) {
                if( painelParaleloQtde % 1 !== 0) {
                checkPainelParaleloValue.textContent = 'Qtde deve ser Inteiro!';
                checkPainelParaleloValue.classList.add('btn', 'btn-danger');
                checkPainelParaleloValue.classList.remove('btn-success');
                } else {
                    checkPainelParaleloValue.textContent = 'OK!';
                    checkPainelParaleloValue.classList.add('btn', 'btn-success');
                    checkPainelParaleloValue.classList.remove('btn-danger');
                }
            } else if (painelParaleloQtde === 1) {
                checkPainelParaleloValue.textContent = 'OK!';
                checkPainelParaleloValue.classList.add('btn', 'btn-success');
                checkPainelParaleloValue.classList.remove('btn-danger');
            } else {
                checkPainelParaleloValue.textContent = 'Erro!';
                checkPainelParaleloValue.classList.add('btn', 'btn-danger');
                checkPainelParaleloValue.classList.remove('btn-success');
            }
            
            // Cálculo da Bateria
            const bateria = <?= json_encode(Bateria::find()->indexBy('id')->asArray()->all()) ?>;
            const bateriaEscolhida = bateria[bateriaId] || null;
            const bateriaAltura = bateriaEscolhida ? bateriaEscolhida.h_m : '-';
            const bateriaEspessura = bateriaEscolhida ? bateriaEscolhida.w_m : '-';
            const bateriaLargura = bateriaEscolhida ? bateriaEscolhida.d_m : '-';
            const bateriaCbiC20BatAh = bateriaEscolhida ? bateriaEscolhida.cbi_c20_bat_ah : '-';
            const bateriaVocBatV = bateriaEscolhida ? bateriaEscolhida.voc_bat_v : '-';
            const bateriaCbiC20 = ((ltotal * diasAutonomia) / (profundidadeDescarga * bateriaVocBatV));
            const bateriaQtdeParalelo = Math.ceil(bateriaCbiC20 / bateriaCbiC20BatAh);
            const bateriaQtdeSerie = (bateriaVocBatV / tensaoNominalCC).toFixed(0) || '-';
            const bateriaQtdeTotal = bateriaQtdeParalelo * bateriaQtdeSerie;
            const bateriaAream2 = bateriaEscolhida ? (bateriaAltura * bateriaLargura * bateriaQtdeTotal) : '-';

            mpptImax = (fatorSeguranca * painelParaleloIcurtocircuito);
            mpptQtdeParalelo = Math.ceil(mpptImax / mpptIctl);
            mpptVoctmin = painelVoc * (1 + painelBeta * (painelTminoper - 25));
            mpptVcmax = mpptEscolhido ? parseFloat(mpptEscolhido.vc_max_v).toFixed(2) : '-';
            mpptFator1Check1 = parseFloat(painelQtdeSerie * mpptVoctmin).toFixed(2);
            if (mpptFator1Check1 < mpptVcmax) {
                checkPrimeiraExistenciaValue.textContent = 'OK!';
                checkPrimeiraExistenciaValue.classList.add('btn', 'btn-success');
                checkPrimeiraExistenciaValue.classList.remove('btn-danger');
            } else {
                checkPrimeiraExistenciaValue.textContent = 'NÃO ATENDE!';
                checkPrimeiraExistenciaValue.classList.add('btn', 'btn-danger');
                checkPrimeiraExistenciaValue.classList.remove('btn-success');
            }

            if (mpptPinvmax >= potenciaCA) {
                checkSegundaExistenciaValue.textContent = 'OK!';
                checkSegundaExistenciaValue.classList.add('btn', 'btn-success');
                checkSegundaExistenciaValue.classList.remove('btn-danger');
            } else {
                checkSegundaExistenciaValue.textContent = 'NÃO ATENDE! - Potência CA Maior que o Inversor';
                checkSegundaExistenciaValue.classList.add('btn', 'btn-danger');
                checkSegundaExistenciaValue.classList.remove('btn-success');
            }


            <?php if($_ENV['IS_PRODUCTION'] !=='true'): ?>
            
                // Debugando
                console.log('######### NOVO CÁLCULO #########');
                console.log('Consumo CC:', consumoCC);
                console.log('Consumo CA:', consumoCA);
                console.log('Consumo Total:', consumoTotal);
                console.log('Potência CC:', potenciaCC);
                console.log('Potência CA:', potenciaCA);
                console.log('Potência Total:', potenciaTotal);
                console.log('Eficiência Painel:', eficPainel);
                console.log('Eficiência Bateria:', eficBateria);
                console.log('Eficiência Inversor:', eficInversor);
                console.log('Eficiência Elet:', eficElet);
                console.log('Fator de Segurança:', fatorSeguranca);
                console.log('MPPT Escolhido:', mpptEscolhido);
                console.log('MPPT Vmppt_min_v:', mpptVmpptminv);
                console.log('MPPT Vmppt_max_v:', mpptVmpptmaxv);
                console.log('MPPT Ictl:', mpptIctl);
                console.log('Painel Escolhido:', painelEscolhido);
                console.log('Painel C:', painelC);
                console.log('Painel L:', painelL);
                console.log('Painel Pmax:', painelPmax);
                console.log('Painel Imax:', painelImax);
                console.log('Painel Vmax:', painelVmax);
                console.log('Painel Voc:', painelVoc);
                console.log('Painel Icc:', painelIcc);
                console.log('Painel Tmin,oper:', painelTminoper);
                console.log('Painel Tmax,oper:', painelTmaxoper);
                console.log('Painel Beta:', painelBeta);
                console.log('Painel Pmwp:', painelPmwp);
                console.log('Painel Qtde Mínima:', painelQtdemin);
                console.log('Painel Área Mínima:', painelAreamin);
                console.log('Sugestão de Painel Qtde:', sugestaoPainelQtde);
                console.log('Check Painel Total:', painelQtdeTotal);

                console.log('Painel Pmwp Final:', painelPmwpFinal);
                console.log('Painel Área Final:', painelAreaFinal);
                console.log('Painel VmpTmin:', painelVmptmin);
                console.log('Painel VmpTmax:', painelVmptmax);

                console.log('Painel Qtde em Série:', painelQtdeSerie);
                console.log('Painel Série Sugestão:', painelSerieSugestao);
                console.log('Painel Série Mínima:', painelSerieMin);
                console.log('Painel Série Máxima:', painelSerieMax);
                console.log('Check Painel Série:', painelQtdeSerie);

                console.log('Painel Qtde em Paralelo:', painelParaleloQtde);
                console.log('Icurto-circuito:', painelParaleloIcurtocircuito);
                console.log('Check Painel Paralelo:', checkPainelParaleloValue);

                console.log('Bateria Escolhida:', bateriaEscolhida);
                console.log('Bateria Altura:', bateriaAltura);
                console.log('Bateria Espessura:', bateriaEspessura);
                console.log('Bateria Largura:', bateriaLargura);
                console.log('Bateria Cbi C20 Bat Ah:', bateriaCbiC20BatAh);
                console.log('Bateria Voc Bat V:', bateriaVocBatV);
                console.log('Fator de Segurança:', fatorSeguranca);
                console.log('Profundidade de Descarga:', profundidadeDescarga);
                console.log('Dias de Autonomia:', diasAutonomia);
                console.log('Tensão Nominal CC:', tensaoNominalCC);

                console.log('Bateria Cbi C20:', bateriaCbiC20);
                console.log('Bateria Área m²:', bateriaAream2);
                console.log('Bateria Qtde Total:', bateriaQtdeTotal);
                console.log('Bateria Qtde em Série:', bateriaQtdeSerie);
                console.log('Bateria Qtde em Paralelo:', bateriaQtdeParalelo);

                console.log('MPPT Imax:', mpptImax);
                console.log('MPPT Qtde em Paralelo:', mpptQtdeParalelo);
                console.log('MPPT Vmppt_min:', mpptVmpptminv);

                console.log('MPPT Fator 1 Check 1:', parseFloat(mpptFator1Check1));
                console.log('MPPT Vcmax:', parseFloat(mpptVmpptmaxv));
                console.log('Check Primeira Existência:', checkPrimeiraExistenciaValue);

                console.log('Pinversor:', mpptPinvmax);
                console.log('Pinstalada:', potenciaCA);
                console.log('Check Segunda Existência:', checkSegundaExistenciaValue);

                console.log('################################');
            <?php endif; ?>

            // ATUALIZA DADOS NA INTERFACE
            potenciaTotalValue.textContent = potenciaTotal.toFixed(2);
            consumoTotalValue.textContent = consumoTotal.toFixed(2);

            lccValue.textContent = lcc;
            lcaValue.textContent = lca;
            ltotalValue.textContent = ltotal;

            mpptVmpptminValue.textContent = mpptVmpptminv;
            mpptVmpptmaxValue.textContent = mpptVmpptmaxv;
            mpptIctlValue.textContent = mpptIctl;

            painelCValue.textContent = painelC;
            painelLValue.textContent = painelL;

            painelPmaxValue.textContent = painelPmax;
            painelImaxValue.textContent = painelImax;
            painelVmaxValue.textContent = painelVmax;

            painelVocValue.textContent = painelVoc;
            painelIccValue.textContent = painelIcc;
            painelTminoperValue.textContent = painelTminoper;

            painelTmaxoperValue.textContent = painelTmaxoper;
            painelBetaValue.textContent = painelBeta;

            if (painelPmwpValue) painelPmwpValue.textContent = painelPmwp.toFixed(2);
            painelQtdeminValue.textContent = painelQtdemin;
            if (painelAreaminValue) painelAreaminValue.textContent = painelAreamin.toFixed(2);

            sugestaoPainelQtdeValue.textContent = sugestaoPainelQtde;

            painelPmwpFinalValue.textContent = painelPmwpFinal.toFixed(2);
            painelAreaFinalValue.textContent = painelAreaFinal.toFixed(2);
            painelVmptminValue.textContent = painelVmptmin.toFixed(2);
            painelVmptmaxValue.textContent = painelVmptmax.toFixed(2);

            painelSerieSugestaoValue.textContent = painelSerieSugestao;
            painelSerieMinValue.textContent = painelSerieMin;
            painelSerieMaxValue.textContent = painelSerieMax;

            painelParaleloQtdeValue.textContent = painelParaleloQtde;
            painelParaleloIcurtocircuitoValue.textContent = painelParaleloIcurtocircuito;

            bateriaAlturaValue.textContent = bateriaAltura;
            bateriaComprimentoValue.textContent = bateriaEspessura;
            bateriaLarguraValue.textContent = bateriaLargura;
            bateriaTensaonominalValue.textContent = tensaoNominalCC;
            bateriaCbiC20batValue.textContent = bateriaCbiC20BatAh;
            bateriaCbiC20Value.textContent = bateriaCbiC20;
            bateriaAream2Value.textContent = bateriaAream2;
            bateriaQtdeTotalValue.textContent = bateriaQtdeTotal;
            bateriaQtdeSerieValue.textContent = bateriaQtdeSerie;
            bateriaQtdeParaleloValue.textContent = bateriaQtdeParalelo;

            mpptImaxValue.textContent = mpptImax.toFixed(2);
            mpptQtdeParaleloValue.textContent = mpptQtdeParalelo;
            mpptVoctminValue.textContent = mpptVoctmin.toFixed(2);

            mpptFator1Check1Value.textContent = mpptFator1Check1;
            mpptVcmaxValue.textContent = mpptVmpptmaxv;
            checkPrimeiraExistenciaValue.textContent = checkPrimeiraExistenciaValue.textContent;

            mpptPinvmaxValue.textContent = mpptPinvmax;
            mpptPinstaladaValue.textContent = potenciaCA;
            checkSegundaExistenciaValue.textContent = checkSegundaExistenciaValue.textContent;
        }

        // INPUTS DO FORMS
        consumoCCInput.addEventListener('input', calcularValores);
        consumoCAInput.addEventListener('input', calcularValores);

        potenciaCCInput.addEventListener('input', calcularValores);
        potenciaCAInput.addEventListener('input', calcularValores);

        eficPainelInput.addEventListener('input', calcularValores);
        eficBateriaInput.addEventListener('input', calcularValores);
        eficInversorInput.addEventListener('input', calcularValores);
        eficEletInput.addEventListener('input', calcularValores);

        mpptIdInput.addEventListener('input', calcularValores);
        fatorSegurancaInput.addEventListener('input', calcularValores);

        painelIdInput.addEventListener('input', calcularValores);
        painelQtdeTotalInput.addEventListener('input', calcularValores);
        painelQtdeSerieInput.addEventListener('input', calcularValores);

        bateriaIdInput.addEventListener('input', calcularValores);
        profundidadeDescargaInput.addEventListener('input', calcularValores);
        diasAutonomiaInput.addEventListener('input', calcularValores);
        tensaoNominalCCInput.addEventListener('input', calcularValores);

        // Calcula os valores iniciais
        calcularValores();
    });
</script>