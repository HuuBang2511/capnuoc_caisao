<?php
/**
 * Created by PhpStorm.
 * User: MinhDuc
 * Date: 6/24/2020
 * Time: 1:11 AM
 */

namespace app\widgets\apexcharts;

use yii\web\AssetBundle;

class ApexchartsAsset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = __DIR__ . '/assets';
        parent::init();
    }

    public $publishOptions = [
        'forceCopy' => YII_ENV_DEV //dev
    ];

    public $css = [
        'css/apexcharts.css',
    ];

    public $js = [
        'js/apexcharts.js',
    ];

}