<?php

/** @var yii\web\View $this */
/** @var app\models\User $user */

$verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $user->verification_token]);
?>
Prezado(a) usuário(a) <?= $user->email ?>,

Para concluir seu cadastro em nosso site, por favor, clique no link abaixo:

<?= $verifyLink ?>

Atenciosamente,
Dimensionamento Solar Off Grid
