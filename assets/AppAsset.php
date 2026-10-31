<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'custom/custom.css',
        'fonts/fa/css/all.css',
//        'fonts/si/css/simple-line-icons.css',
    ];
    public $js = [
//        'js/site.js',
//        'js/vue-component.js',
    ];
    public $depends = [
//        'yii\web\YiiAsset',
//        'app\modules\contrib\gxassets\GxBootstrapAsset',
//        'app\modules\contrib\gxassets\GxJqueryAsset',
//        'app\modules\contrib\gxassets\GxDashMixAsset',
    ];
}
