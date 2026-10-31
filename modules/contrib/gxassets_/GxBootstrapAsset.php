<?php
namespace app\modules\contrib\gxassets_;

use yii\bootstrap4\BootstrapAsset;


class GxBootstrapAsset extends \yii\web\AssetBundle {
    public $sourcePath = __DIR__ . '/assets/bootstrap';

    public $css = [
        'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css',
    ];

    public $js = [
        'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js',
        'https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js',
        'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js',
    ];


    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}