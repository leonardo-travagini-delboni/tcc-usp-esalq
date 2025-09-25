<?php

/** @var yii\web\View $this */

use app\models\Coordenada;

use yii\helpers\Html;

$this->title = 'Minha Localização';
$this->params['breadcrumbs'][] = $this->title;

// Extraindo parametros do user
$lat_gps = Yii::$app->user->identity->gps_lat ?? null;
$lng_gps = Yii::$app->user->identity->gps_lng ?? null;
$lat_manual = Yii::$app->user->identity->latitude ?? null;
$lng_manual = Yii::$app->user->identity->longitude ?? null;
$use_gps = Yii::$app->user->identity->use_gps ?? 0;

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


?>
<div class="site-minhas-coordenadas">

    <div class="row">
        <div class="col-lg-8 mb-3">

            <h2>Passo 1 - <?= Html::encode($this->title) ?></h2>

            <?php if ($lat_gps && $lng_gps): ?>

                <hr>
                <div class="alert alert-info">
                    <div class="row">
                        <div class="col-lg-6">
                            Sua última localização geográfica é:
                            <strong>
                                <?= Html::encode($lat_gps) ?>, <?= Html::encode($lng_gps) ?>.
                            </strong>
                            cujo ponto mais próximo é o <strong><?= Html::encode($pontoProximoGPS->id) ?></strong>, com coordenadas
                            <strong>
                                <?= Html::encode($lat_pontoProximoGPS) ?>, <?= Html::encode($long_pontoProximoGPS) ?>.
                            </strong>
                            ou seja, <strong>distando <?= Html::encode(number_format($distanciaGPS, 2)) ?> km </strong> de suas coordenadas aproximadamente.
                            Portanto, o Potencial Solar pelo Método do Mês Crítico é de: <strong><?= Html::encode($mmcGPS) ?> [W.h/m².dia]</strong>
                        </div>
                        <div class="col-lg-6 text-end">
                            <div class="row">
                                <button onclick="getLocation()" class="btn btn-primary mt-2">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Atualizar Minha Localização
                                </button>
                                <a href="/site/usar-gps?route=site/potencial-solar&use_gps=0" class="btn btn-dark mt-2">
                                    <i class="fas fa-sun"></i> 
                                    Inserir Manualmente
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                                <a class="btn btn-info mt-2" href="https://www.google.com/maps/search/?api=1&query=<?= Html::encode($lat_gps) ?>,<?= Html::encode($lng_gps) ?>" target="_blank">
                                    <i class="fas fa-map-marked-alt"></i>
                                    Ver no Mapa
                                </a>
                                <a href="/site/usar-gps?route=site/consumo-eletrico" class="btn btn-success mt-2">
                                    <i class="fas fa-sun"></i> Ir para Consumo Elétrico <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>

            <?php else : ?>

                <p>Por favor, clique no botão abaixo para obter a sua localização:</p>
                <div class="row">
                    <div class="col-lg-4 text-left">
                        <a href="/site/index" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            Voltar ao Site
                        </a>
                    </div>
                    <div class="col-lg-4 text-left">
                        <button onclick="getLocation()" class="btn btn-primary">
                            <i class="fas fa-map-marker-alt"></i>
                            Extrair Minha Localização
                        </button>
                    </div>
                    <div class="col-lg-4 text-left">
                        <a href="/site/usar-gps?route=site/potencial-solar&use_gps=0" class="btn btn-dark">
                            Inserir Manualmente
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

            <?php endif; ?>

            <p id="demo"></p>
        </div>
        <div class="col-lg-4 mb-3">
            <img src="/img/1.png" class="img-fluid" alt="Minhas Coordenadas">
        </div>
    </div>





</div>

<script>

    function getLocation() {
        var demo = document.getElementById("demo");
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showError);
        } else {
            demo.innerHTML = "Geolocalização não é suportada por este navegador.";
        }
    }

    function showPosition(position) {
        var demo = document.getElementById("demo");
        var latitude = position.coords.latitude.toFixed(4);
        var longitude = position.coords.longitude.toFixed(4);

        // Enviar coordenadas para o backend via AJAX
        fetch('/site/atualizar-gps', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-Token': yii.getCsrfToken()
            },
            body: 'latitude=' + latitude + '&longitude=' + longitude
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                demo.innerHTML = `
                    <hr>
                    <p>
                        Portanto, sua localização mais atualiza é:
                    </p>
                    <div class='row py-2'>
                        <div class='col-lg-4 text-left'>
                            <strong>Latitude: ${latitude}</strong>
                        </div>
                        <div class='col-lg-4 text-left'>
                            <strong>Longitude: ${longitude}</strong>
                        </div>
                        <div class='col-lg-4 text-left'>
                            <a class='btn btn-info' href='https://www.google.com/maps/search/?api=1&query=${latitude},${longitude}' target='_blank'>
                                <i class='fas fa-map-marked-alt'></i> Ver no Google Maps
                            </a>
                        </div>
                    </div>
                    <hr>
                    <p>Escolha a seguir a forma como deseja <strong>Calcular o Potencial Solar:</strong></p>
                    <div class='row py-3'>
                        <div class='col-lg-4 text-left'>
                            <a class='btn btn-dark' href='/site/potencial-solar'>
                                <i class='fas fa-sun'></i> Para Outra Localização <i class='fas fa-arrow-right'></i>
                            </a>
                        </div>
                        <div class='col-lg-4 text-left'>
                            <a class='btn btn-success' href='/site/usar-gps'>
                                <i class='fas fa-sun'></i> Para Essa Localização <i class='fas fa-arrow-right'></i>
                            </a>
                        </div>
                    </div>
                `;
            } else {
                demo.innerHTML = "Erro ao salvar localização: " + data.message;
            }
        });
    }


    function showError(error) {
        var demo = document.getElementById("demo");
        switch(error.code) {
            case error.PERMISSION_DENIED:
                demo.innerHTML = "Usuário negou a solicitação de Geolocalização. Por favor, permita ao navegador obter a sua localização.";
                break;
            case error.POSITION_UNAVAILABLE:
                demo.innerHTML = "Informação de localização não está disponível. Por favor, tente novamente.";
                break;
            case error.TIMEOUT:
                demo.innerHTML = "A solicitação para obter a localização expirou. Por favor, tente novamente.";
                break;
            case error.UNKNOWN_ERROR:
                demo.innerHTML = "Ocorreu um erro desconhecido.";
                break;
        }
    }
</script>