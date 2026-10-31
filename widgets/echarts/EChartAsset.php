<?php


namespace app\widgets\echarts;


use yii\web\AssetBundle;

class EChartAsset extends AssetBundle
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
//        'css/apexcharts.css',
    ];

    public $js = [
        'js/echarts.min.js',
    ];
}