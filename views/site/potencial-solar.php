<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use app\models\Coordenada;

$this->title = 'Potencial Solar';
$this->params['breadcrumbs'][] = $this->title;

// Extraindo parametros do user
$lat_gps = Yii::$app->user->identity->gps_lat ?? null;
$lng_gps = Yii::$app->user->identity->gps_lng ?? null;
$lat_manual = Yii::$app->user->identity->latitude ?? null;
$lng_manual = Yii::$app->user->identity->longitude ?? null;

// Obtém o MMC do ponto mais próximo para GPS
if($lat_gps && $lng_gps) {
    $pontoProximoGPS = Coordenada::findOne(Coordenada::getPontoProximo($lat_gps, $lng_gps));
    $lat_pontoProximoGPS = $pontoProximoGPS->lat;
    $long_pontoProximoGPS = $pontoProximoGPS->long;
    $distanciaGPS = Coordenada::getDistancia($lat_gps, $lng_gps, $lat_pontoProximoGPS, $long_pontoProximoGPS);
    $mmcGPS = Coordenada::getMmc($lat_gps, $lng_gps);
}

// Obtém o MMC do ponto mais próximo para Manual
if($lat_manual && $lng_manual) {
    $pontoProximoManual = Coordenada::findOne(Coordenada::getPontoProximo($lat_manual, $lng_manual));
    $lat_pontoProximoManual = $pontoProximoManual->lat;
    $long_pontoProximoManual = $pontoProximoManual->long;
    $distanciaManual = Coordenada::getDistancia($lat_manual, $lng_manual, $lat_pontoProximoManual, $long_pontoProximoManual);
    $mmcManual = Coordenada::getMmc($lat_manual, $lng_manual);
}

// Limites do Brasil
$minLat = -33.7000;
$maxLat = 5.3000;
$minLong = -73.9000;
$maxLong = -32.4000;

?>
<div class="site-potencial-solar">

    <div class="row">
        <div class="col-lg-8 mb-3">
            <h1>Passo 2 - <?= Html::encode($this->title) ?></h1>

            <?php if ($lat_gps && $lng_gps): ?>
                <div class="alert alert-info">
                    <div class="row">
                        <div class="col-lg-8">
                            <h4><strong>Sua Localização Geográfica</strong></h4>
                            Suas últimas coordenadas são 
                            <strong>
                                (<?= Html::encode($lat_gps) ?>, <?= Html::encode($lng_gps) ?>)
                            </strong>
                            cujo ponto mais próximo é o <strong><?= Html::encode($pontoProximoGPS->id) ?> (
                                <?= Html::encode($lat_pontoProximoGPS) ?>, <?= Html::encode($long_pontoProximoGPS) ?>)
                            distando <?= Html::encode(number_format($distanciaGPS, 2)) ?> km </strong>.
                            Portanto, o Potencial Solar pelo Método do Mês Crítico é de: <strong><?= Html::encode($mmcGPS) ?> [W.h/m².dia]</strong>
                        </div>
                        <div class="col-lg-4 text-end">
                            <div class="row">
                                <a class="btn btn-secondary mt-2" href="/site/minhas-coordenadas">
                                    <i class="fas fa-sync-alt"></i>
                                    Atualizar Localização
                                </a>
                                <a class="btn btn-info mt-2" href="https://www.google.com/maps/search/?api=1&query=<?= Html::encode($lat_gps) ?>,<?= Html::encode($lng_gps) ?>" target="_blank">
                                    <i class="fas fa-map-marked-alt"></i>
                                    Ver no Mapa
                                </a>
                                <a href="/site/usar-gps?route=site/consumo-eletrico&use_gps=1" class="btn btn-success mt-2">
                                    <i class="fas fa-sun"></i> Usar essas Coordenadas <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <p>
                    ou então...
                </p>
            <?php endif; ?>

            <?php if ($lat_manual && $lng_manual): ?>
                <div class="alert alert-info">
                    <div class="row">
                        <div class="col-lg-8">
                            <h4><strong>Suas Coordenadas Manuais</strong></h4>
                            Suas últimas coordenadas são
                            <strong>
                                (<?= Html::encode($lat_manual) ?>, <?= Html::encode($lng_manual) ?>)
                            </strong>
                            cujo ponto mais próximo é o <strong><?= Html::encode($pontoProximoManual->id) ?>
                            (<?= Html::encode($lat_pontoProximoManual) ?>, <?= Html::encode($long_pontoProximoManual) ?>).
                            distando <?= Html::encode(number_format($distanciaManual, 2)) ?> km </strong>.
                            Portanto, o Potencial Solar pelo Método do Mês Crítico é de: <strong><?= Html::encode($mmcManual) ?> [W.h/m².dia]</strong>
                        </div>
                        <div class="col-lg-4 text-end">
                            <div class="row">
                                <small style="text-align: center; padding: 10px;">Atualize inserindo novos dados...</small>
                                <a class="btn btn-info mt-2" href="https://www.google.com/maps/search/?api=1&query=<?= Html::encode($lat_manual) ?>,<?= Html::encode($lng_manual) ?>" target="_blank">
                                    <i class="fas fa-map-marked-alt"></i>
                                    Ver no Mapa
                                </a>
                                <a href="/site/usar-gps?route=site/consumo-eletrico&use_gps=0" class="btn btn-success mt-2">
                                    <i class="fas fa-sun"></i> 
                                    Usar essas Coordenadas 
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <p>
                    ou então...
                </p>
            <?php endif; ?>

            <h3><strong>Insira Novas Coordenadas Manuais</strong></h3>

            <form action="/site/atualizar-coordenadas-manuais" method="get">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="hidden" name="rota_final" value="/site/potencial-solar">
                            <label for="latitude">Latitude</label>
                            <input type="text" name="latitude" id="latitude" class="form-control" required>
                            <small class="form-text text-muted">
                                Limites do Brasil: <?= number_format($minLat,4); ?> e <?= number_format($maxLat,4); ?>. Exemplo: -23.5505
                            </small>
                            <br><br>
                            <label for="longitude">Longitude</label>
                            <input type="text" name="longitude" id="longitude" class="form-control" required>
                            <small class="form-text text-muted">
                                Limites do Brasil: <?= number_format($minLong,4); ?> e <?= number_format($maxLong,4); ?>. Exemplo: -46.6333
                            </small>
                        </div>
                    </div>
                </div>
                <a href="/site/minhas-coordenadas" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Minha Localização
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sun"></i>
                    Calcular Potencial Solar
                </button>
            </form>

        </div>
        <div class="col-lg-4 mb-3">
            <img src="/img/2.png" class="img-fluid" alt="Potencial Solar">
        </div>
    </div>

</div>
