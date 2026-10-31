<?php
/**
 * Created by PhpStorm.
 * User: MinhDuc
 * Date: 7/30/2020
 * Time: 1:51 PM
 */

namespace app\widgets\maps\plugins\geocoder;


use yii\web\AssetBundle;

class GeocoderAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/maps/plugins/geocoder/assets';

    public $css = [
//        'css/l.geocoder.css'
    ];

    public $js = [
        'js/positionstack.js'
    ];

    public $depends = [
        'app\widgets\maps\LeafLetMapAsset',
    ];
}