<?php
/**
 * Created by PhpStorm.
 * User: MinhDuc
 * Date: 6/5/2020
 * Time: 3:13 PM
 */

namespace app\widgets\gridview;


class Module extends \kartik\base\Module {
    /**
     * The module name for Krajee gridview
     */
    const MODULE = "gridview";

    /**
     * @var string a random salt that will be used to generate a hash string for export configuration.
     */
    public $exportEncryptSalt = 'SET_A_SALT_FOR_YII2_GRID';

    /**
     * @var string|array the action (url) used for downloading exported file
     */
    public $downloadAction;

    /**
     * @inheritdoc
     */
    protected $_msgCat = 'kvgrid';
}