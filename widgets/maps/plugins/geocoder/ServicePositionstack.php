<?php
/**
 * Created by PhpStorm.
 * User: MinhDuc
 * Date: 7/30/2020
 * Time: 1:55 PM
 */

namespace app\widgets\maps\plugins\geocoder;


use yii\base\InvalidConfigException;
use yii\web\JsExpression;

class ServicePositionstack extends BaseService
{

    public $serviceUrl = 'https://api.positionstack.com/v1/forward';

    public $apiKey;

    public function init()
    {
        if($this->apiKey === null) {
            throw new InvalidConfigException('"$apiKey" cannot be empty.');
        }
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public function registerAssetBundle($view)
    {
        ServicePositionstackAsset::register($view);
    }

    /**
     * @return \yii\web\JsExpression the javascript code for the geocoder option to be set
     */
    public function getJs()
    {
        return new JsExpression("L.Control.Geocoder.PositionStack('{$this->apiKey}')");
    }
}