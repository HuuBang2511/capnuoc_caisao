<?php
namespace app\modules\contrib\gxassets_;

class GxVuetifyAsset extends \yii\web\AssetBundle {
    public $sourcePath = '@app/modules/contrib/gxassets/assets/vuetify';

    public $css = [
        'https://cdn.jsdelivr.net/npm/@mdi/font@4.x/css/materialdesignicons.min.css',
        'https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css'
    ];

    public $js = [
        'https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js'
    ];

    public $depends = [
        '\app\modules\contrib\gxassets\GxVueAsset',
    ];

    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}