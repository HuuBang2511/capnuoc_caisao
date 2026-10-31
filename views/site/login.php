<?php

use app\assets\AppAsset;
use app\modules\contrib\gxassets_\GxLimitlessTemplateAsset;
use yii\bootstrap\ActiveForm;
use yii\authclient\widgets\AuthChoice;
?>

<style>
    .navbar,
    footer {
        display: none !important;
    }
</style>

<div class="content d-flex justify-content-center align-items-center page-login flex-column">
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
    ]); ?>
    <div class="card card-body login-form border-top-primary" style="width: 364px;">
        <div class="text-center">
            <a href="<?= Yii::$app->homeUrl ?>" class="mb-2 d-block">
                <img src="<?= Yii::$app->homeUrl ?>resources/images/logo.png" style="max-width: 120px">
            </a>
            <h4 class="font-weight-bold text-uppercase mb-3">HCDC - Y TẾ HỌC ĐƯỜNG</h4>
            <h4 class="font-weight-bold text-uppercase text-primary">ĐĂNG NHẬP</h4>
        </div>

        <div class="form-group text-left">
            <?= $form->field($model, 'username')->textInput(['placeholder' => 'Tên đăng nhập'])->label(false) ?>
        </div>

        <div class="form-group text-left">
            <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Mật khẩu'])->label(false) ?>
        </div>

        <div class="form-group d-flex align-items-center">
            <a href="<?=Yii::$app->homeUrl . 'site/forgot-password' ?>" class="ml-auto">Quên mật khẩu?</a>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block text-uppercase font-weight-bold">Đăng nhập<i class="icon-circle-right2 ml-2"></i></button>
        </div>

        <div class="content-group">
            <div class="text-center">
                <p class="display-block">Không có tài khoản? <a href="register" class="font-weight-bold">Đăng ký</a></p>
            </div>
        </div>

        <h6 class="help-block text-center no-margin"> © 2022 HCDC - Y tế học đường</h6>
    </div>
    <?php ActiveForm::end(); ?>
</div>