<?php

/* @var $this yii\web\View */
/* @var $model \frontend\models\EditProfileForm */


use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Edit profile';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .edit_form{
        width: 30%;
    }
    @media screen and (max-width: 800px){
        .edit_form{
            width: 100%;
        }
    }
</style>
<div class="site-profile">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>This is your profile page. You can see and edit your username and email, or you can request a new password.<br>Be careful, after changing your username, you'll have to use the <i>new</i> username to login!</p>

    <?php $form = ActiveForm::begin(['id' => 'edit-form']); ?>
    <div class="edit_form">
        <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'value' => $model->username]) ?>
        <?= $form->field($model, 'email')->textInput(['value' => $model->email]) ?>
    </div>
        <br>
        Registration time:
        <?= Yii::$app->formatter->format($model->reg_time,'datetime') ?>
        <br>
        <br>
        Last login:
        <?= Yii::$app->formatter->format($model->last_login,'datetime') ?>
        <br>
        <br>
        Last update:
        <?= Yii::$app->formatter->format($model->update_time,'datetime') ?>

        <br>
        <br>
        <a href="send-password-email">Click here to resend a password reset email.</a> <br><br>

    <div class="form-group">
        <?= Html::submitButton('Edit', ['class' => 'btn btn-primary', 'name' => 'edit-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
