<?php

use kartik\form\ActiveForm;
use app\modules\cms\CMSConfig;
use yii\authclient\widgets\AuthChoice;
?>

<style>
    .navbar,
    footer {
        display: none !important;
    }

    .input-group-addon {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        right: .5rem;
        color: #999;
        font-size: .75rem !important;
    }
</style>
<div class="content d-flex justify-content-center align-items-center page-register">
    <?php $form = ActiveForm::begin([
        'id' => 'register-form'
    ]) ?>
    <div class="card card-body login-form border-top-primary" style="width: 364px;">
        <div class="text-center">
            <a href="<?= Yii::$app->homeUrl ?>" class="mb-2 d-block">
                <img src="<?= Yii::$app->homeUrl ?>resources/images/logo.png" style="max-width: 120px">
            </a>
            <h4 class="font-weight-bold text-uppercase mb-3">HCDC - Y TẾ HỌC ĐƯỜNG</h4>
            <h4 class="font-weight-bold text-uppercase text-primary">ĐĂNG KÝ TÀI KHOẢN MỚI</h4>
        </div>

        <div class="form-group text-left">
            <?= $form->field($model, 'fullname')->textInput(['placeholder' => 'Họ tên'])->label(false) ?>
        </div>

        <div class="form-group text-left">
            <?= $form->field($model, 'username')->textInput(['placeholder' => 'Tên đăng nhập'])->label(false) ?>
        </div>

        <div class="form-group text-left">
            <?= $form->field($model, 'password', [
                'addon' => ['append' => ['content' => '6 - 15 ký tự']]
            ])->textInput(['placeholder' => 'Mật khẩu', 'type' => 'password'])->label(false) ?>
        </div>

        <div class="form-group text-left">
            <?= $form->field($model, 'cpassword')->textInput(['placeholder' => 'Xác nhận mật khẩu', 'type' => 'password'])->label(false) ?>
        </div>

        <div class="form-group text-center">
            <button type="submit" class="btn btn-primary btn-block text-uppercase font-weight-bold">Đăng ký<i class="icon-user-plus ml-2"></i></button>
        </div>

        <div class="content-group">
            <div class="text-center">
                <p class="display-block">Đã có tài khoản? <a href="login" class="font-weight-bold">Đăng nhập</a></p>
            </div>
        </div>

        <h6 class="help-block text-center no-margin"> © 2022 HCDC - Y tế học đường</h6>
    </div>
    <?php ActiveForm::end() ?>
</div>