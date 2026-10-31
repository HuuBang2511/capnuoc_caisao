<?php
/**
 * Created by PhpStorm.
 * User: MinhDuc
 * Date: 12/2/2019
 * Time: 9:48 AM
 */

namespace app\widgets\maps\layers;

use dosamigos\leaflet\layers\Layer;
use yii\base\InvalidConfigException;
use yii\web\JsExpression;

class LeafletTileLayer extends Layer
{
    const WMS = 'wms';

    public $urlTemplate;

    public $service;

    public $layerName = null;

    public function init()
    {
        parent::init();
        if (empty($this->urlTemplate)) {
            throw new InvalidConfigException("'urlTemplate' cannot be empty.");
        }
    }

    /**
     * @return \yii\web\JsExpression the marker constructor string
     */
    public function encode()
    {
        $options = $this->getOptions();
        $name = $this->getName();
        $map = $this->map;
        $js = "L.tileLayer". (($this->service != null) ? '.'.$this->service : '')  ."('$this->urlTemplate', $options)" . ($map !== null ? ".addTo($map);" : "");
        if (!empty($name)) {
            $js = "var $name = $js" . ($map !== null ? "" : ";");
            $js .= $this->getEvents();
        }

        return new JsExpression($js);
    }
}
