<?php
namespace app\widgets\maps\plugins\leaflet_measure;

use yii\web\AssetBundle;
use yii\web\View;

class LeafletMeasureAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/maps/plugins/leaflet_measure/';

    public $jsOptions = ['position' => View::POS_HEAD];

    public $css = [
        'css/leaflet-measure.css',
    ];

    public $js = [
        'js/leafletmeasure.js',
    ];

    public $depends = [
    ];
}