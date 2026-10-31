
<?php 
use app\modules\cms\PathConfig;
?>

<div class="page-container login-container" style="min-height:300px">
    <div class="page-content">
        <div class="content-wrapper">
            <div class="content">
                <div class="container-fluid text-center">
                    <h1 class="error-title" style="font-size: 50px;"><?= $name ?></h1>
                    <h3 class="text-semibold content-group">Vui lòng liên hệ quản trị viên để được hỗ trợ!</h3>
                    <div class="d-flex justify-content-center my-3">
                        <a href="<?= \Yii::$app->homeUrl ?>" class="btn btn-primary">Trang chủ <i class="icon-circle-right2 lm-2"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>