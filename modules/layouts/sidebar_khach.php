<?php
$controller = Yii::$app->controller;
$module = $controller->module;

use \app\modules\base\Constant;
use yii\helpers\Url;
use \app\modules\APPConfig;

$url = implode('/', [$module->id, $controller->id]);

$map = APPConfig::$CONFIG['map'];
$user_id = Yii::$app->user->id;
//dd(Yii::$app->user->identity->is_admin);
//dd(\hcmgis\user\services\AuthService::can($user_id,'quanly.hocsinh.index'));
?>

<nav id="sidebar" aria-label="Main Navigation">

    <div class="bg-gray-lighter">
        <div class="content-header bg-primary-darker">
            <a class="font-w600 text-white tracking-wide logo-default" href="http://localhost/luanvany/web/user/auth/login">
                <img src="https://cdn.thuvienphapluat.vn/phap-luat/2022/TD/220702/nuoc-sinh-hoat.png"
                     alt="logo"
                     class="logo-default py-2">
            </a>
            <div>
                <a class="d-lg-none text-white ml-2" data-toggle="layout" data-action="sidebar_close"
                   href="javascript:void(0)">
                    <i class="fa fa-times-circle text-primary-darker"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="js-sidebar-scroll" data-simplebar="init">
        <div class="simplebar-wrapper" style="margin: 0px;">
            <div class="simplebar-height-auto-observer-wrapper">
                <div class="simplebar-height-auto-observer"></div>
            </div>
            <div class="simplebar-mask">
                <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                    <div class="simplebar-content-wrapper" style="height: 100%; overflow: hidden scroll;">
                        <div class="simplebar-content" style="padding: 0px;">
                            <div class="content-side">
                                <ul class="nav-main">
                                    <li class="nav-main-item">
                                        <a class="nav-main-link" href="http://localhost/luanvany/web/user/auth/login">
                                            <i class="nav-main-link-icon fa fa-globe"></i>
                                            <span class="nav-main-link-name">Trang chủ</span>
                                        </a>
                                    </li>

                                    <li class="nav-main-heading">Map</li>

                                    <li class="nav-main-item">
                                        <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu"
                                           aria-haspopup="true" aria-expanded="true" href="#">
                                            <i class="nav-main-link-icon fa fa-list"></i>
                                            <span class="nav-main-link-name">Map</span>
                                        </a>
                                        <ul class="nav-main-submenu">
                                            <?php foreach ($map as $navchild) : ?>

                                                    <li class="nav-main-item <?= ($url == $navchild['url']) ? 'active' : '' ?>">
                                                        <a class="nav-main-link"
                                                           href="<?= Yii::$app->urlManager->createUrl([$navchild['url']]) ?>">
                                                            <span class="nav-main-link-name"><?= $navchild['name'] ?></span>
                                                        </a>
                                                    </li>

                                            <?php endforeach; ?>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="simplebar-placeholder" style="width: auto; height: 760px;"></div>
        </div>
        <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
            <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
        </div>
        <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
            <div class="simplebar-scrollbar"
                 style="height: 190px; transform: translate3d(0px, 0px, 0px); display: block;"></div>
        </div>
    </div>
</nav>

<script>
    $(document).ready(function () {
        $('li.active').parent().parent().addClass('open');
        $('li.active a').addClass('active');
    })
</script>