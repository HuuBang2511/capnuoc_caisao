<?php
namespace app\widgets\maps\plugins\fullscreen;

use yii\web\AssetBundle;
use yii\web\View;

class FullScreenAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/maps/plugins/fullscreen/assets';

//    public $jsOptions = ['position' => View::POS_HEAD];

    public $css = [
        'css/leaflet.fullscreen.css',
    ];

    public $js = [
        'js/Leaflet.fullscreen.min.js',
    ];

    public $depends = [
    ];
}