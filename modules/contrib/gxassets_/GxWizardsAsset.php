<?php
namespace app\modules\contrib\gxassets_;

class GxWizardsAsset extends \yii\web\AssetBundle {
    public $sourcePath = '@app/modules/contrib/gxassets/assets/wizards';

    public $css = [
        ''
    ];

    public $js = [
        'wizards.js'
    ];

    public $depends = [
        '\app\modules\contrib\gxassets\GxJqueryAsset'
    ];

    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}