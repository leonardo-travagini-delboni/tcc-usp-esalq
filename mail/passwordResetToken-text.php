<?php

/** @var yii\web\View $this */
/** @var app\models\User $user */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>
Olá <?= $user->email ?>,

Clique no link abaixo para redefinir sua senha:

<?= $resetLink ?>

Se você não solicitou uma redefinição de senha, ignore este e-mail.

Atenciosamente,
Dimensionamento Solar Off Grid
