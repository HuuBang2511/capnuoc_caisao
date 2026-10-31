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
    <?php ActiveForm::begin(['id' => 'forgot-password-form']); ?>
    <div class="card border-top-primary mb-0" style="width: 364px;">
        <div class="card-body">
            <div class="text-center">
                <a href="<?= Yii::$app->homeUrl ?>" class="mb-2 d-block">
                    <img src="<?= Yii::$app->homeUrl ?>resources/images/logo.png" style="max-width: 120px">
                </a>
                <h4 class="font-weight-bold text-uppercase mb-3">HCDC - Y TẾ HỌC ĐƯỜNG</h4>
                <h4 class="font-weight-bold text-uppercase text-primary">QUÊN MẬT KHẨU</h4>
            </div>

            <div class="form-group form-group-feedback form-group-feedback-left">
                <input type="text" class="form-control form-control" name="email" placeholder="Email đã đăng ký tài khoản">
                <div class="form-control-feedback form-control-feedback">
                    <i class="icon-mail5"></i>
                </div>
            </div>

            <div class="form-group">
                <button type="button" class="btn btn-primary btn-block text-uppercase font-weight-bold" id="btn-forgot-password" @click="confirmEmail">XÁC NHẬN</button>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script>
    $(function() {
        var vm = new Vue({
            el: '#forgot-password-form',
            data: {
                waitingTime: <?= $waitingTime ?>,
            },
            methods: {
                confirmEmail: function(e) {
                    e.preventDefault();
                    let ladda = Ladda.create($('#btn-forgot-password')[0]);

                    ladda.start();
                    $.ajax({
                        data: $('#forgot-password-form').serialize(),
                        type: 'POST',
                        success: function(resp) {
                            if (resp.status) {
                                toastMessage('success', resp.message);
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

        $('#forgot-password-form').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>