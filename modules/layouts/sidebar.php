<?php
$controller = Yii::$app->controller;
$module = $controller->module;

use \app\modules\base\Constant;
use yii\helpers\Url;
use \app\modules\APPConfig;

$url = implode('/', [$module->id, $controller->id]);

$adminSidebar = APPConfig::$CONFIG['adminSidebar'];
// $vientham = APPConfig::$CONFIG['vientham'];
$danhmuc = APPConfig::$CONFIG['danhmuc'];
$map = APPConfig::$CONFIG['map'];
// $aphu = APPConfig::$CONFIG['aphu'];
$giadinh = APPConfig::$CONFIG['giadinh'];
$user_id = Yii::$app->user->id;
//dd(Yii::$app->user->identity->is_admin);
//dd(\hcmgis\user\services\AuthService::can($user_id,'quanly.hocsinh.index'));
?>

<nav id="sidebar" aria-label="Main Navigation">

    <div class="bg-gray-lighter">
        <div class="content-header bg-primary-darker">
            <a class="font-w600 text-white tracking-wide logo-default" href="<?= Yii::$app->homeUrl ?>">
                <img src="<?= Yii::$app->homeUrl ?>resources/images/logo_tuanloc.jpg"
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
                                        <a class="nav-main-link" href="<?= Yii::$app->homeUrl ?>">
                                            <i class="nav-main-link-icon fa fa-globe"></i>
                                            <span class="nav-main-link-name">Thống kê tài sản</span>
                                        </a>
                                    </li>
                                    <li class="nav-main-heading">Quản trị hệ thống</li>
                                    <?php foreach ($adminSidebar as $navchild) : ?>
                                    <?php if (\hcmgis\user\services\AuthService::can($user_id, $navchild['key']) or Yii::$app->user->identity->is_admin == true) { ?>
                                        <li class="nav-main-item">
                                            <a class="nav-main-link"
                                               href="<?= Yii::$app->urlManager->createUrl([$navchild['url']]) ?>">
                                                <i class="nav-main-link-icon <?= $navchild['icon'] ?>"></i>
                                                <span class="nav-main-link-name"><?= $navchild['name'] ?></span>
                                            </a>
                                        </li>
                                        <?php } ?>
                                    <?php endforeach; ?>
                                    <li class="nav-main-heading">Quản lý thông tin</li>
                                    <li class="nav-main-item" id="giadinh">
                                        <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu"
                                           aria-haspopup="true" aria-expanded="true" href="#">
                                            <i class="nav-main-link-icon fa fa-list"></i>
                                            <span class="nav-main-link-name">Quản lý hệ thống cấp nước</span>
                                        </a>
                                        <ul class="nav-main-submenu">
                                            <?php foreach ($giadinh as $navchild) : ?>
                                                <?php if (\hcmgis\user\services\AuthService::can($user_id, $navchild['key']) or Yii::$app->user->identity->is_admin == true) { ?>
                                                    <li class="nav-main-item <?= ($url == $navchild['url']) ? 'active' : '' ?>">
                                                        <a class="nav-main-link"
                                                           href="<?= Yii::$app->urlManager->createUrl([$navchild['url']]) ?>">
                                                            <span class="nav-main-link-name"><?= $navchild['name'] ?></span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                            <?php endforeach; ?>
                                        </ul>
                                    </li>
                                    <li class="nav-main-heading">Bản đồ thông minh</li>

                                    <li class="nav-main-item">
                                        <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu"
                                           aria-haspopup="true" aria-expanded="true" href="#">
                                            <i class="nav-main-link-icon fa fa-list"></i>
                                            <span class="nav-main-link-name">Bản đồ vị trí</span>
                                        </a>
                                        <ul class="nav-main-submenu">
                                            <?php foreach ($map as $navchild) : ?>
                                                <?php if (\hcmgis\user\services\AuthService::can($user_id, $navchild['key']) or Yii::$app->user->identity->is_admin == true) { ?>
                                                    <li class="nav-main-item <?= ($url == $navchild['url']) ? 'active' : '' ?>">
                                                        <a class="nav-main-link"
                                                           href="<?= Yii::$app->urlManager->createUrl([$navchild['url']]) ?>">
                                                            <span class="nav-main-link-name"><?= $navchild['name'] ?></span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                            <?php endforeach; ?>
                                        </ul>
                                    </li>
                                    <li class="nav-main-heading">Danh mục</li>

                                    <li class="nav-main-item" id="danhmuc">
                                        <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu"
                                           aria-haspopup="true" aria-expanded="true" href="#">
                                            <i class="nav-main-link-icon fa fa-list"></i>
                                            <span class="nav-main-link-name">Quản lý danh mục</span>
                                        </a>
                                        <ul class="nav-main-submenu">
                                            <?php foreach ($danhmuc as $navchild) : ?>
                                            <?php if (\hcmgis\user\services\AuthService::can($user_id, $navchild['key']) or Yii::$app->user->identity->is_admin == true) { ?>
                                                <li class="nav-main-item <?= ($url == $navchild['url']) ? 'active' : '' ?>">
                                                    <a class="nav-main-link"
                                                       href="<?= Yii::$app->urlManager->createUrl([$navchild['url']]) ?>">
                                                        <span class="nav-main-link-name"><?= $navchild['name'] ?></span>
                                                    </a>
                                                </li>
                                                <?php } ?>
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