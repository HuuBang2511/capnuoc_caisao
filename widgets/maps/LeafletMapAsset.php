<?php

namespace app\widgets\maps;


use yii\web\AssetBundle;
use yii\web\View;


class LeafletMapAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/maps/assets';

    public $jsOptions = ['position' => View::POS_HEAD];

    public $css = [
        'css/leaflet.css',
        'css/leaflet.defaultextent.css',
    ];

    public $js = [
        'js/leaflet-src.js',
        // 'js/jquery.geocomplete.js',
    ];

    public $depends = [
        // 'app\modules\contrib\appassets\GxBootstrapAsset',
    ];
}
