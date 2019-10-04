<?php

/* @var $this yii\web\View */
/* @var $model \backend\models\EditProfile */


use yii\helpers\Html;
use common\models\User;
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
    <h1><?= Html::encode($this->title).' - '.$model->username ?></h1>

    <?php $form = ActiveForm::begin(['id' => 'edit-form']); ?>
    <div class="edit_form">
        <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'value' => $model->username]) ?>
        <?= $form->field($model, 'email')->textInput(['value' => $model->email]) ?>
    </div>
    <br>
    <?php
        echo $form->field($model, 'adminBool')->checkbox(['checked' => $model->adminBool ? true : false])->label("Admin");
    ?>
    <?php
        echo $form->field($model, 'statusBool')->checkbox(['checked' => $model->statusBool ? true : false])->label("Active status");
    ?>
    <div class="form-group">
        <?= Html::submitButton('Edit', ['class' => 'btn btn-primary', 'name' => 'edit-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
