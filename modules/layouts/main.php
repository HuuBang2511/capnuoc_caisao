<?php

/**
 * Created by PhpStorm.
 * User: Duc
 * Date: 7/3/2021
 * Time: 9:00 PM
 */

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use \app\modules\assets\AppAsset;

AppAsset::register($this);
$this->title = 'GIS Cấp nước';
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <link rel="shortcut icon" href="<?= Yii::$app->homeUrl ?>resources/images/favicon.ico" type="image/x-icon">
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body>
    <?php $this->beginBody() ?>
    <div id="page-container" class="sidebar-o enable-page-overlay side-scroll page-header-fixed side-trans-enabled">
        <div id="page-overlay"></div>
        <?php include('sidebar.php'); ?>
        <?php include('header.php'); ?>
        <main id="main-container">
            <div class="content">
                <?php if (isset($this->params['breadcrumbs'])) : ?>
                    <div class="block">
                        <div class="col-lg-12">
                            <nav aria-label="breadcrumb">
                                <?=
                                Breadcrumbs::widget([
                                    'tag' => 'ol',
                                    'homeLink' => [
                                        'label' => Html::encode(Yii::t('yii', 'Tổng quan')),
                                        'url' => Yii::$app->urlManager->createUrl('quanly/dashboard/index'),
                                        'encode' => false,
                                    ],
                                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                                    'itemTemplate' => "<li class='breadcrumb-item'>{link}</li>",
                                    'activeItemTemplate' => "<li class='active breadcrumb-item'>{link}</li>",
                                    'options' => [
                                        'class' => 'breadcrumb custom-breadcrumb',
                                    ]
                                ])
                                ?>
                            </nav>
                        </div>
                    </div>
                <?php endif; ?>
                <?= $content ?>
            </div>
        </main>

        <?php include('footer.php'); ?>
    </div>



    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>