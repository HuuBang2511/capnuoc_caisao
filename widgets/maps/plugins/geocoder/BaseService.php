<?php
/**
 * Created by PhpStorm.
 * User: MinhDuc
 * Date: 7/30/2020
 * Time: 1:54 PM
 */

namespace app\widgets\maps\plugins\geocoder;


use yii\base\BaseObject;

abstract class BaseService extends BaseObject
{
    abstract public function registerAssetBundle($view);

    abstract public function getJs();
}