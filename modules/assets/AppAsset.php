<?php


namespace app\modules\assets;


use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{

    public $sourcePath = '@app/modules/assets';

    public $css = [
        'css/site.css',
    ];
    public $js = [
    ];

    public $depends = [
        'app\modules\contrib\gxassets_\GxBootstrapAsset',
        'app\modules\contrib\gxassets_\GxDashmixAsset',
    ];
}