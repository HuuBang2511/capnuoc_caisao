<?php
/**
 * Created by PhpStorm.
 * User: Duc
 * Date: 9/24/2021
 * Time: 10:43 AM
 */

namespace app\widgets\crud;


use yii\web\AssetBundle;

class CrudAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/crud/assets';

    public $css = [
        'ajaxcrud.css'
    ];

    public $depends = [
        'kartik\grid\GridViewAsset',
    ];

    public function init() {
        $this->js = YII_DEBUG ? [
            'ModalRemote.js',
            'ajaxcrud.js',
        ]:[
            'ModalRemote.min.js',
            'ajaxcrud.min.js',
        ];
        parent::init();
    }
}