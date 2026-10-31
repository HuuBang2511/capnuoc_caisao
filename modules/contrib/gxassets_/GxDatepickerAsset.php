<?php
namespace app\modules\contrib\gxassets_;

class GxDatepickerAsset extends \yii\web\AssetBundle {
    public $sourcePath = '@app/modules/contrib/gxassets/assets/datepicker';

    public $css = [];

    public $js = [
        'datepicker.js'
    ];

    public $depends = [
        '\app\modules\contrib\gxassets\GxVueAsset',
    ];

    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}