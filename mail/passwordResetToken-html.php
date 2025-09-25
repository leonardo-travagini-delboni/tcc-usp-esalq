<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\User $user */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>
<div class="password-reset">
    <p>Olá <?= Html::encode($user->email) ?>,</p>

    <p>Clique no link abaixo para redefinir sua senha:</p>

    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>

    <p>Se você não solicitou uma redefinição de senha, apenas ignore este e-mail.</p>

    <p>
        Atenciosamente,<br>
        Dimensionamento Solar Off Grid
    </p>
    
</div>
