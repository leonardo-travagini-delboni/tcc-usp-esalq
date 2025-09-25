<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\User $user */

$verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $user->verification_token]);
?>
<div class="verify-email">
    <p>Prezado(a) usuário(a) <?= Html::encode($user->email) ?>,</p>

    <p>Para concluir seu cadastro em nosso site, por favor, clique no link abaixo:</p>

    <p><?= Html::a(Html::encode($verifyLink), $verifyLink) ?></p>

    <p>Atenciosamente,</p>
    <p>Dimensionamento Solar Off Grid</p>
</div>
