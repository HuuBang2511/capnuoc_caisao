<?php
/**
 * Created by PhpStorm.
 * User: MinhDuc
 * Date: 7/30/2020
 * Time: 1:47 PM
 */
namespace app\widgets\maps\plugins\geocoder;

use app\widgets\maps\plugins\Plugin;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;

class Geocoder extends Plugin
{

    private $_service;

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        if ($this->_service === null) {
            throw new InvalidConfigException('"service" cannot be empty.');
        }
    }

    /**
     * Sets the service to use for geocoding
     *
     * @param BaseService $service
     */
    public function setService(BaseService $service)
    {
        $this->_service = $service;
    }

    /**
     * @return BaseService
     */
    public function getService()
    {
        return $this->_service;
    }

    public function getPluginName()
    {
        return 'plugin:geocoder';
    }

    public function registerAssetBundle($view)
    {
        GeocoderAsset::register($view);
        return $this;
    }

    public function encode()
    {
        $this->clientOptions = ArrayHelper::merge(
            [
                'showMarker' => true
            ],
            $this->clientOptions
        );

        $this->clientOptions['geocoder'] = $this->getService()->getJs();

        $options = $this->getOptions();
        $name = $this->getName();
        $map = $this->map;

        $js = "new L.Control.Geocoder($options).addTo($map)";

        if (!empty($name)) {
            $js = "var $name = $js;";
        }

        return new JsExpression($js);
    }
}