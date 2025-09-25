<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception$exception */

use yii\helpers\Html;

$this->title = 'Erro: ' . $name;
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <p>
        O erro acima ocorreu enquanto o servidor Web estava processando sua solicitação.
    </p>
    <p>
        Por favor, entre em contato conosco se você acha que é um erro do servidor. Obrigado.
    </p>

    <a href="/site/index" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar ao Site</a>

</div>
