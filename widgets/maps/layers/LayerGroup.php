<?php
/**
 * @copyright Copyright (c) 2013-2015 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace app\widgets\maps\layers;


use app\services\DebugService;
use yii\base\Component;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\JsExpression;

class LayerGroup extends Component
{
    use NameTrait;

    public $layerName;
    /**
     * @var string the name of the javascript variable that will hold the reference
     * to the map object.
     */
    public $map;

    /**
     * @var Layer[]
     */
    private $_layers = [];

    /**
     * Adds a layer to the group. If no name given it will be automatically generated.
     *
     * @param Layer $layer
     *
     * @return $this
     * @throws \yii\base\InvalidParamException
     */
    public function addLayer(Layer $layer)
    {
        $layer->map = null;
        $this->layerName = $layer->layerName;
        $this->_layers[$layer->getName(true)] = $layer;
        return $this;
    }

    /**
     * Returns a specific layer. Please note that if the layer didn't have a name, it will be dynamically created. This
     * method works for those that we know the name previously.
     *
     * @param string $name the name of the layer
     *
     * @return mixed
     */
    public function getLayer($name)
    {
        return ArrayHelper::getValue($this->_layers, $name);
    }

    /**
     * Removes a layer with the given name from the group.
     *
     * @param $name
     *
     * @return mixed|null
     */
    public function removeLayer($name)
    {
        return ArrayHelper::remove($this->_layers, $name);
    }

    /**
     * @return Layer[] the added layers
     */
    public function getLayers()
    {
        return $this->_layers;
    }

    /**
     * @return JsExpression
     */
    public function encode()
    {
        $js = [];
        $layers = $this->getLayers();
//        DebugService::dumpdie($layers);
        $name = $this->getName();
        $names = str_replace(array('"', "'"), "", Json::encode(array_keys($layers)));
        $map = $this->map;
        foreach ($layers as $layer) {
            $js[] = $layer->encode();
        }
        $initJs = "L.layerGroup($names)" . ($map !== null ? ".addTo($map);\n" : "");

        if (empty($name)) {
            $js[] = $initJs . ($map !== null ? "" : ";");
        } else {
            $js[] = "var $name = $initJs" . ($map !== null ? "" : ";");
        }
        return new JsExpression(implode("\n", $js));
    }

    /**
     * Returns the initialization
     * @return JsExpression
     */
    public function oneLineEncode()
    {
        $map = $this->map;
        $layers = $this->getLayers();
        $layersJs = [];
        /** @var Layer $layer */
        foreach ($layers as $layer) {
            $layer->name = null;
            $layersJs[] = $layer->encode();
        }
        $js = "L.layerGroup([" . implode(",", $layersJs) . "])" . ($map !== null ? ".addTo($map);" : "");
        return new JsExpression($js);
    }
}
