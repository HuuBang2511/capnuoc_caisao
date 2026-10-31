<?php
namespace app\widgets\maps\plugins\leafletlocate;

use yii\web\AssetBundle;
use yii\web\View;

class LeafletLocateAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/maps/plugins/leafletlocate/assets';

//    public $jsOptions = ['position' => View::POS_HEAD];

    public $css = [
        'css/leafletlocate.css',
    ];

    public $js = [
        'js/leafletlocate.js',
    ];

    public $depends = [
    ];
}