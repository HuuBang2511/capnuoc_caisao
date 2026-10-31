<?php

namespace app\widgets\maps\layers;

use app\services\DebugService;
use app\widgets\maps\types\LatLng;
use app\widgets\maps\types\Icon;
use yii\base\InvalidConfigException;
use yii\web\JsExpression;

/**
 * Marker is used to put a marker on the map
 *
 * @see http://leafletjs.com/reference.html#circle
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\leaflet\layers
 */
/**
 * @property string $name
 * @property LatLng $latLng
 * @property string $popupContent
 * @property bool $openPopup
 */
class Marker extends Layer
{
    use LatLngTrait;
    use PopupTrait;

    /**
     * Sets the marker's icon
     *
     * @param Icon $icon
     */
    public function setIcon($icon) //Icon - if you force the icon as type, the makimarker won't work...:(
    {
        $this->clientOptions['icon'] = $icon;
    }

    /**
     * @return Icon
     */
    public function getIcon()
    {
        return isset($this->clientOptions['icon']) ? $this->clientOptions['icon'] : null;
    }

    /**
     * Initializes the marker.
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (empty($this->latLng)) {
            throw new InvalidConfigException("'latLng' attribute cannot be empty.");
        }
    }

    /**
     * @return \yii\web\JsExpression the marker constructor string
     */
    public function encode()
    {
        $latLon = $this->getLatLng()->toArray(true);
        $options = $this->getOptions();
        $name = $this->name;
        $map = $this->map;
        $js = $this->bindPopupContent("L.marker($latLon, $options)") . ($map !== null ? ".addTo($map)" : "");
        if (!empty($name)) {
            $js = "var $name = $js;";
        }
        $js .= $this->getEvents() . ($map !== null && empty($name)? ";" : "");
        return new JsExpression($js);
    }
}
