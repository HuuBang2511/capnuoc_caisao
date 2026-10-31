<?php
$controller = Yii::$app->controller;
$module = $controller->module;
?>
<style>
    .page-header-dark .nav-main-horizontal .nav-main-link.active {
        background-color: #044fa2;
    }
    .page-header-dark .nav-main-horizontal .nav-main-link:hover {
        background-color: #044fa2;
    }
    .page-header-dark .nav-main-horizontal .nav-main-link>.nav-main-link-icon {
        color: rgba(255,255,255,.4);
    }
    .page-header-dark .nav-main-horizontal .nav-main-link.active {
        color: #fff;
    }
    .page-header-dark .nav-main-horizontal .nav-main-link {
        color: rgba(255,255,255,.75);
    }
    .nav-main-link .nav-main-link-icon{
        color: rgba(255,255,255,.4);
    }
</style>

<div class="page-header-dark bg-header-dark">
<div class="content content-full" style="display: flex; justify-content: space-between; align-items: center; margin: 0 auto; height: 4.25rem;">
    <div id="nav" class="d-flex align-items-center">
        <ul class="nav-main nav-main-horizontal nav-main-hover d-none d-lg-block ms-3">
            <li class="nav-main-item">
                <a id="tai-san-tri-tue" class="nav-main-link" href="<?= Yii::$app->homeUrl.'app/tai-san-tri-tue/index' ?>">
                    <i class="nav-main-link-icon fa fa-certificate"></i>
                    <span class="nav-main-link-name">Tài sản trí tuệ</span>
                </a>
            </li>
            <li class="nav-main-item">
                <a id="tac-gia" class="nav-main-link" href="<?= Yii::$app->homeUrl.'app/tac-gia/index' ?>">
                    <i class="nav-main-link-icon fa fa-user-tie"></i>
                    <span class="nav-main-link-name">Chuyên gia</span>
                </a>
            </li>
            <li class="nav-main-item">
                <a id="doi-tac" class="nav-main-link" href="<?= Yii::$app->homeUrl.'app/don-vi-chuyen-giao/index' ?>">
                    <i class="nav-main-link-icon fa fa-handshake"></i>
                    <span class="nav-main-link-name">Đối tác</span>
                </a>
            </li>
        </ul>
    </div>
    <div>
        <?php if(Yii::$app->user->isGuest):?>
        <a class="btn btn-alt-secondary d-none d-sm-inline-block" href="<?= Yii::$app->homeUrl.'accounts/auth/dang-nhap' ?>">Đăng nhập</a>
        <?php else:?>
        <div class="dropdown d-inline-block">
            <button type="button" class="btn btn btn-alt-secondary dropdown-toggle" id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-fw fa-user d-sm-none"></i>
                <span class="d-none d-sm-inline-block"><?= (!Yii::$app->user->isGuest) ? Yii::$app->user->identity->tendangnhap : 'Chưa đăng nhập' ?></span>
            </button>
            <div class="dropdown-menu dropdown-menu-right p-0" aria-labelledby="page-header-user-dropdown">

                <div class="p-2">
                <a class="dropdown-item"
                       href="<?= Yii::$app->urlManager->createUrl(['quan-ly/dashboard/index']) ?>">
                        <i class="fa fa-cogs mr-1"></i> Quản lý
                    </a>
                    
                    <div role="separator" class="dropdown-divider"></div>
                    <a class="dropdown-item"
                       href="<?= Yii::$app->urlManager->createUrl(['accounts/auth/dang-xuat']) ?>">
                        <i class="far fa-fw fa-arrow-alt-circle-left mr-1"></i> Đăng xuất
                    </a>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-alt-secondary d-lg-none" data-toggle="layout" data-action="sidebar_toggle">
            <i class="fa fa-fw fa-bars"></i>
        </button>
        <?php endif;?>
    </div>
</div>
</div>

<script>
    $(function(){
        var current = location.pathname;
        $('#nav li a').each(function(){
            var $this = $(this);
            // if the current path is like this link, make it active
            if($this.attr('href').indexOf(current) !== -1){
                $this.addClass('active');
            }
        })
    })
</script>