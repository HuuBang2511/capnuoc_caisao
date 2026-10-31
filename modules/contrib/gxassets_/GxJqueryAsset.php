<?php

namespace app\modules\contrib\gxassets_;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;


class GxJqueryAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/contrib/gxassets/assets/jquery';

    public $css = [
    ];

    public $js = [
//       'jquery.min.js',
        'https://code.jquery.com/jquery-3.6.1.min.js'
    ];

    public $depends = [
        //'yii\web\JqueryAsset'
    ];

    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}