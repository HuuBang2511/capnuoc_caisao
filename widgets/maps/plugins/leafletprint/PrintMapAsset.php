<?php
namespace app\widgets\maps\plugins\leafletprint;

use yii\web\AssetBundle;
use yii\web\View;

class PrintMapAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/maps/plugins/leafletprint/assets';

//    public $jsOptions = ['position' => View::POS_HEAD];

    public $css = [
    ];

    public $js = [
        'js/leaflet.browser.print.min.js',
    ];

    public $depends = [
    ];
}