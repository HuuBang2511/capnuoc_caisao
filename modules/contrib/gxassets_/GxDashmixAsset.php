<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\modules\contrib\gxassets_;

use yii\web\AssetBundle;
class GxDashmixAsset extends AssetBundle
{
//    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];

    public $sourcePath = __DIR__ . '/assets/dashmix';

    public $css = [
        'css/dashmix.min.css',
        'css/all.min.css',
        'css/custom.css',
    ];
    public $js = [
        'js/dashmix.app.min.js',
    ];
}
