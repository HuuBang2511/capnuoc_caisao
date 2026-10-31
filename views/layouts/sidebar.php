<?php

use yii\helpers\Url;

$sidebar = Yii::$app->controller->module->params['adminSidebar'];

?>
<nav id="sidebar" aria-label="Main Navigation">
    <!-- Side Header -->
    <div class="bg-header-dark">
        <div class="content-header bg-white-10">
            <!-- Logo -->
            <a class="font-w600 text-white tracking-wide" href="/">
                <?php if (isset(Yii::$app->params['logo'])) : ?>
                    <img src="<?= Yii::$app->homeUrl ?>/resources/images/logo_hpn.png" alt="logo" width="50%" class="logo-default">
                <?php else : ?>
                    <span class="">
                        <?= isset(Yii::$app->params['siteName']) ? Yii::$app->params['siteName'] : 'siteName' ?>
                    </span>
                <?php endif ?>


            </a>
            <!-- END Logo -->

            <!-- Options -->
            <div>


                <!-- Close Sidebar, Visible only on mobile screens -->
                <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                <a class="d-lg-none text-white ml-2" data-toggle="layout" data-action="sidebar_close" href="javascript:void(0)">
                    <i class="fa fa-times-circle"></i>
                </a>
                <!-- END Close Sidebar -->
            </div>
            <!-- END Options -->
        </div>
    </div>
    <!-- END Side Header -->

    <!-- Sidebar Scrolling -->
    <div class="js-sidebar-scroll">
        <!-- Side Navigation -->
        <div class="content-side">
            <ul class="nav-main">
                <?php foreach ($sidebar as $group) : ?>
                    <li class="nav-main-heading"><?= $group['name'] ?></li>
                    <li class="nav-main-item">
                        <?php foreach ($group['items'] as $item) : ?>
                            <a class="nav-main-link" aria-haspopup="true" aria-expanded="false" href="<?= Url::to([$item['url']]) ?>">
                                <i class="nav-main-link-icon fa <?= $item['icon'] ?>"></i>
                                <span class="nav-main-link-name"><?= $item['name'] ?></span>
                            </a>

                        <?php endforeach ?>
                        <!-- <ul class="nav-main-submenu">
                            <li class="nav-main-item">
                                <a class="nav-main-link" href="be_blocks_styles.html">
                                    <span class="nav-main-link-name">Styles</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link" href="be_blocks_options.html">
                                    <span class="nav-main-link-name">Options</span>
                                </a>
                            </li>

                        </ul> -->
                    </li>
                <?php endforeach ?>





            </ul>
        </div>
        <!-- END Side Navigation -->
    </div>
    <!-- END Sidebar Scrolling -->
</nav>
<!-- END Sidebar -->