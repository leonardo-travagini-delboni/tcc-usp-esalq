<?php

/** @var yii\web\View $this */

$this->title = 'Home';
?>
<div class="site-index">

    <?php if (Yii::$app->user->isGuest) : ?>
        <div class="jumbotron text-center bg-transparent mt-5 mb-5">
            <h1 class="display-5">Bem-vindo(a)!</h1>
            <p class="lead">Dimensionamento Fotovoltaico Off Grid para todo o Brasil</p>
            <p>
                <a class="btn btn-lg btn-success" href="/site/cadastro">Cadastrar-se</a>
                <a class="btn btn-lg btn-success px-3" href="/site/login">Fazer Login</a>
            </p>
        </div>
    <?php else : ?>
        <div class="jumbotron text-center mt-4 mb-4">
            <h1 class="display-5">Dimensionamento Solar Off Grid</h1>
        </div>
    <?php endif; ?>

    <div class="body-content">

        <?php if (Yii::$app->user->isGuest) : ?>

            <div class="row">
                <div class="col-lg-3 mb-3">
                    <h2>Passo 1</h2>
                    <p>Caso não saiba suas coordenadas, o sistema obtém a localização diretamente do navegador de seu dispositivo.</p>
                    <img src="/img/1.png" class="img-fluid" alt="Minhas Coordenadas">
                </div>
                <div class="col-lg-3 mb-3">
                    <h2>Passo 2</h2>
                    <p>Com as coordenadas geográficas em mãos, calcule o respectivo potencial solar, através do Método do Mês Crítico.</p>
                    <img src="/img/2.png" class="img-fluid" alt="Potencial Solar">
                </div>
                <div class="col-lg-3 mb-3">
                    <h2>Passo 3</h2>
                    <p>Então calcule o consumo elétrico a ser suprido pelo seu sistema fotovoltaico off grid (isolado da rede elétrica).</p>
                    <img src="/img/3.png" class="img-fluid" alt="Consumo Elétrico">
                </div>
                <div class="col-lg-3">
                    <h2>Passo 4</h2>
                    <p>Por fim, dimensione o seu sistema fotovoltaico autônomo com controlador-inversor MPPT para todo o Brasil.</p>
                    <img src="/img/4.png" class="img-fluid" alt="Dimensionamento">
                </div>
            </div>
            <p aling="center" class="text-center">
                Para acessar todas as funcionalidades, por favor acesse o sistema ou realize seu cadastro na plataforma gratuitamente.
            </p>

        <?php else : ?>

            <div class="alert alert-dark text-justify" role="alert">
                <div class="row">
                    <div class="col-lg-3 mb-3">
                        <h2>Passo 1</h2>
                        <p>Obtenha as coordenadas geográficas da simulação. Caso não saiba, clique abaixo para extrair sua localização atual.</p>
                        <img src="/img/1.png" class="img-fluid" alt="Minhas Coordenadas">
                        <p class="text-center mt-3">
                            <a class="btn btn-outline-secondary" href="/site/minhas-coordenadas">
                                <i class="fas fa-map-marker-alt"></i>
                                Minhas Coordenadas
                            </a>
                        </p>
                    </div>
                    <div class="col-lg-3 mb-3">
                        <h2>Passo 2</h2>
                        <p>Com as coordenadas geográficas em mãos, calcule o respectivo potencial solar, através do Método do Mês Crítico.</p>
                        <img src="/img/2.png" class="img-fluid" alt="Potencial Solar">
                        <p class="text-center mt-3">
                            <a class="btn btn-outline-secondary" href="/site/potencial-solar">
                                <i class="fas fa-sun"></i>
                                Potencial Solar
                            </a>
                        </p>
                    </div>
                    <div class="col-lg-3 mb-3">
                        <h2>Passo 3</h2>
                        <p>Então calcule o consumo elétrico a ser suprido pelo seu sistema fotovoltaico off grid (isolado da rede elétrica).</p>
                        <img src="/img/3.png" class="img-fluid" alt="Consumo Elétrico">
                        <p class="text-center mt-3">
                            <a class="btn btn-outline-secondary" href="/site/consumo-eletrico">
                                <i class="fas fa-bolt"></i>
                                Consumo Elétrico
                            </a>
                        </p>
                    </div>
                    <div class="col-lg-3">
                        <h2>Passo 4</h2>
                        <p>Por fim, dimensione o seu sistema fotovoltaico autônomo com controlador-inversor MPPT para todo o Brasil.</p>
                        <img src="/img/4.png" class="img-fluid" alt="Dimensionamento">
                        <p class="text-center mt-3">
                            <a class="btn btn-outline-secondary" href="/dimensionamento/index">
                                <i class="fas fa-calculator"></i>
                                Meus Dimensionamentos
                            </a>
                        </p>
                    </div>
                </div>
            </div>

        <?php endif; ?>



    </div>
</div>
