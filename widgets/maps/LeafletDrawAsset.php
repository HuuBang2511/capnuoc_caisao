<?php

namespace app\widgets\maps;


use yii\web\AssetBundle;
use yii\web\View;


class LeafletDrawAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/maps/assets';

    public $jsOptions = ['position' => View::POS_HEAD];

    public $css = [

//        'css/leaflet.draw.css',
        'https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css',




    ];

    public $js = [

//        'js/leaflet.draw.js'
        'https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js',
    ];

    public $depends = [
        // 'app\modules\contrib\appassets\GxBootstrapAsset',
    ];
}
