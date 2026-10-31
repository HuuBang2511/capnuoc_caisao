<?php

use kartik\form\ActiveForm;
use app\modules\contrib\gxassets_\GxLaddaAsset;

GxLaddaAsset::register($this);
?>

<style>
    .navbar,
    footer {
        display: none !important;
    }
</style>

<div class="content d-flex justify-content-center align-items-center">
    <?php ActiveForm::begin(['id' => 'reset-password-form']); ?>
    <div class="card border-top-primary mb-0" style="width: 364px;">
        <div class="card-body">
            <div class="text-center">
                <a href="<?= Yii::$app->homeUrl ?>" class="mb-2 d-block">
                    <img src="<?= Yii::$app->homeUrl ?>resources/images/logo.png" style="max-width: 120px">
                </a>
                <h4 class="font-weight-bold text-uppercase mb-3">HCDC - Y TẾ HỌC ĐƯỜNG</h4>
                <h4 class="font-weight-bold text-uppercase text-primary">THAY ĐỔI MẬT KHẨU</h4>
            </div>

            <div class="form-group form-group-feedback form-group-feedback-left">
                <input type="password" class="form-control form-control" name="AuthUser[password]" placeholder="Mật khẩu mới" autocomplete="password">
                <div class="form-control-feedback form-control-feedback">
                    <i class="icon-lock"></i>
                </div>
            </div>

            <div class="form-group form-group-feedback form-group-feedback-left">
                <input type="password" class="form-control form-control" name="AuthUser[cpassword]" placeholder="Xác nhận mật khẩu mới" autocomplete="cpassword">
                <div class="form-control-feedback form-control-feedback">
                    <i class="icon-lock"></i>
                </div>
            </div>

            <div class="form-group">
                <input type="hidden" name="token" value="<?= $token ?>">
                <input type="hidden" name="auth" value="<?= $auth ?>">
                <button type="button" class="btn btn-primary btn-block text-uppercase font-weight-bold" id="btn-reset-password" @click="resetPassword">XÁC NHẬN</button>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script>
    $(function() {
        var vm = new Vue({
            el: '#reset-password-form',
            data: {},
            methods: {
                resetPassword: function(e) {
                    e.preventDefault();
                    let api = '<?= Yii::$app->homeUrl . 'site/set-new-password' ?>',
                        ladda = Ladda.create($('#btn-reset-password')[0]);

                    ladda.start();
                    $.ajax({
                        data: $('#reset-password-form').serialize(),
                        url: api,
                        type: 'POST',
                        success: function(resp) {
                            if (resp.status) {
                                window.location.assign('<?= Yii::$app->homeUrl . 'site/login' ?>');
                            } else {
                                toastMessage('error', resp.message);
                            }
                            ladda.stop();
                        },
                        error: function(msg) {
                            console.log(msg);
                        }
                    });
                }
            }
        })

        $('#reset-password-form').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>