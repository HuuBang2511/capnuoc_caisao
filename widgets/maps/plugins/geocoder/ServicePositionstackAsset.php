<?php
/**
 * Created by PhpStorm.
 * User: MinhDuc
 * Date: 7/30/2020
 * Time: 1:57 PM
 */

namespace app\widgets\maps\plugins\geocoder;

use yii\web\AssetBundle;

class ServicePositionstackAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/maps/plugins/geocoder/assets';

    public $js = [
        'js/positionstack.js'
    ];

    public $depends = [
        'app\widgets\maps\plugins\geocoder\GeoCoderAsset',
    ];
}