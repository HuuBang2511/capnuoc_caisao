<?php
namespace app\modules\contrib\gxassets_;

class GxVueSelectAsset extends \yii\web\AssetBundle {
    public $sourcePath = '@app/modules/contrib/gxassets/assets/vue-select';

    public $css = [
        'vue-select.css'
    ];

    public $js = [
        'vue-select.js'
    ];

    public $depends = [
        '\app\modules\contrib\gxassets\GxVueAsset',
    ];

    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}