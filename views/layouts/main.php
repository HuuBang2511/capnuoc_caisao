<?php
/* @var $this View */
/* @var $content string */

use app\modules\contrib\widgets\FlashMessageWidget;
use hcmgis\user\assets\UserAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

$siteName = ArrayHelper::getValue(Yii::$app->params, 'siteName', 'siteName');
UserAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <link rel="shortcut icon" href="<?= Yii::$app->homeUrl ?>resources/images/favicon.ico" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="192x192" href="<?= Yii::$app->homeUrl ?>resources/images/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= Yii::$app->homeUrl ?>resources/images/favicon.ico">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= $siteName ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <script>
        // TODO: redirect http to https
        if (window.location.protocol == 'http:' && window.location.hostname != 'localhost') {
            window.location.assign('https://' + window.location.hostname + window.location.pathname + window.location.search);
        }
    </script>
    <?php $this->head() ?>

</head>

<body cz-shortcut-listen="true" infinite-wrapper>

    <?php $this->beginBody() ?>

    <div id="page-container" class=" sidebar-o page-header-dark enable-page-overlay side-scroll page-header-fixed main-content-narrow">
        <?= $this->render('sidebar') ?>
        <?= $this->render('header') ?>
        <main id="main-container">
            <!-- Hero -->
            <div class="content">
                <?php if (isset($this->params['breadcrumbs'])) : ?>
                    <div class="block">
                        <div class="col-lg-12">
                            <nav aria-label="breadcrumb">
                                <?=
                                Breadcrumbs::widget([
                                    'tag' => 'ol',
                                    'homeLink' => [
                                        'label' => Html::encode(Yii::t('yii', 'Quản lý')),
                                        'url' => ['/user'],
                                        'encode' => false,
                                    ],
                                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                                    'itemTemplate' => "<li class='breadcrumb-item'>{link}</li>",
                                    'activeItemTemplate' => "<li class='active breadcrumb-item'>{link}</li>",
                                    'options' => [
                                        'class' => 'breadcrumb breadcrumb-alt',
                                    ]
                                ])
                                ?>
                            </nav>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                <?php endif; ?>
                <?= FlashMessageWidget::widget() ?>
                <div class="mb-3"></div>
                <?= $content ?>
            </div>
        </main>
        <?= $this->render('footer'); ?>

    </div>
    <?php $this->endBody() ?>

</body>

</html>
<?php $this->endPage(); ?>