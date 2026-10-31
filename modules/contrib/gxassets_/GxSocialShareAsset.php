<?php
namespace app\modules\contrib\gxassets_;

class GxSocialShareAsset extends \yii\web\AssetBundle {
    public $sourcePath = '@app/modules/contrib/gxassets/assets/social-share';

    public $css = [
        ''
    ];

    public $js = [
        'https://sp.zalo.me/plugins/sdk.js'
    ];

    public $depends = [
        // '\app\modules\contrib\gxassets\GxJqueryAsset'
    ];

    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}