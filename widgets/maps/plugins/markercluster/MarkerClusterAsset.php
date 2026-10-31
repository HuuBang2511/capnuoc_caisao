<?php
namespace app\widgets\maps\plugins\markercluster;

use yii\web\AssetBundle;
use yii\web\View;

class MarkerClusterAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/maps/plugins/markercluster/assets';

//    public $jsOptions = ['position' => View::POS_HEAD];

    public $css = [
        'css/MarkerCluster.css',
        'css/MarkerCluster.Default.css',
    ];

    public $js = [
        'js/leaflet.markercluster.js',
    ];

    public $depends = [
    ];
}