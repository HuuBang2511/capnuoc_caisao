<?php
namespace app\modules\contrib\gxassets_;

class GxFancyTreeAsset extends \yii\web\AssetBundle {
    public $sourcePath = '@app/modules/contrib/gxassets/assets/fancytree';

    public $css = [
        'ui.fancytree.min.css'
    ];

    public $js = [
        'jquery.fancytree-all-deps.min.js',
    ];

    public $depends = [
        '\app\modules\contrib\gxassets\GxJqueryAsset'
    ];

    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}