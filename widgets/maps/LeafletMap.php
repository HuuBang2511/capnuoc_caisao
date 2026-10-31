<?php
/**
 * Created by PhpStorm.
 * User: MinhDuc
 * Date: 5/28/2020
 * Time: 1:05 PM
 */
namespace app\widgets\maps;

use app\widgets\maps\controls\Control;
use app\widgets\maps\functions\Functions;
use app\widgets\maps\functions\JsFunction;
use app\widgets\maps\layers\Layer;
use app\widgets\maps\layers\LayerGroup;
use app\widgets\maps\layers\Polygon;
use app\widgets\maps\layers\TileLayer;
use app\widgets\maps\plugins\Plugin;
use app\widgets\maps\plugins\PluginManager;
use app\widgets\maps\types\LatLng;
use app\services\DebugService;
use yii\base\Component;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;

class LeafletMap extends Component
{
    // JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK
    const JSON_OPTIONS = 352;

    /**
     * @var integer a counter used to generate [[name]] for layers.
     * @internal
     */
    public static $counter = 0;
    /**
     * @var string the prefix to the automatically generated object names.
     * @see [[generateName()]]
     */
    public static $autoNamePrefix = 'l';
    /**
     * @var string the name to give to the variable. The name of the map specified on the
     * [[TileLayer]] component overrides this one.
     */
    public $name = 'map';
    /**
     * @var int the zoom level of the map
     */
    public $zoom = 13;
    /**
     * @var array the options for the underlying LeafLetJs JS component.
     * Please refer to the LeafLetJs api reference for possible
     * [options](http://leafletjs.com/reference.html).
     */
    public $clientOptions = [];
    public $styleOptions = [];
    /**
     * @var array the event handlers for the underlying LeafletJs JS plugin.
     * Please refer to the LeafLetJs js api object options for possible events.
     */
    public $clientEvents = [];
    /**
     * @var Layer[] holding ui layers (do not confuse with map layers, these are markers, popups, polygons, etc)
     */
    private $_layers = [];
    /**
     * @var LayerGroup[] holding layer groups
     */
    private $_layerGroups = [];
    /**
     * @var LatLng sets the center of the map
     */
    private $_center;

    /**
     * Returns the center of the map.
     * @return LatLng center of the map.
     */

    public $markerName = 'marker';

    public function getCenter()
    {
        return $this->_center;
    }

    /**
     * Sets the center of the map.
     *
     * @param LatLng $value center of the map.
     */
    public function setCenter(LatLng $value)
    {
        $this->_center = $value;
    }

    /**
     * @var Control[] holding controls to be added to the map.
     */
    private $_controls = [];

    /**
     * @param Control[] $controls
     *
     * @throws \yii\base\InvalidParamException
     */
    public function setControls(array $controls)
    {
        foreach ($controls as $control) {
            if (!($control instanceof Control)) {
                throw new InvalidParamException("All controls must be of type Control.");
            }
        }
        $this->_controls = $controls;
    }

    /**
     * @return Control[]
     */
    public function getControls()
    {
        return $this->_controls;
    }

    /**
     * @param Control $control
     */
    public function addControl(Control $control)
    {
        $this->_controls[] = $control;
    }

    /**
     * @var \dosamigos\leaflet\layers\TileLayer
     */
    private $_tileLayer;

    /**
     * @param \dosamigos\leaflet\layers\TileLayer $tileLayer
     *
     * @return static the component itself
     */
    public function setTileLayer(TileLayer $tileLayer)
    {
        if (!empty($tileLayer->map) && strcmp($tileLayer->map, $this->name) !== 0) {
            $this->name = $tileLayer->map;
        }
        if (empty($tileLayer->map)) {
            $tileLayer->map = $this->name;
        }
        $this->_tileLayer = $tileLayer;

        return $this;
    }

    /**
     * @return \dosamigos\leaflet\layers\TileLayer
     */
    public function getTileLayer()
    {
        return $this->_tileLayer;
    }

    /**
     * @var array holds the js script lines to be registered.
     */
    private $_js = [];

    /**
     * @param string|array $js custom javascript code to be registered.
     * *Warning*: This method overrides any previous settings.
     *
     * @return static the component itself
     */
    public function setJs($js)
    {
        $this->_js = is_array($js) ? $js : [$js];
        return $this;
    }

    /**
     * @param string $js appends javascript code to be registered.
     *
     * @return static the component itself
     */
    public function appendJs($js)
    {
        $this->_js[] = $js;
        return $this;
    }

    /**
     * @return array the queued javascript code to be registered.
     * *Warning*: This method does not include map initialization.
     */
    public function getJs()
    {
        $js = [];
        foreach ($this->getLayers() as $layer) {

            if ($layer instanceof Polygon) {
                $layerJs = $layer->encode();
                $insertAtTheBottom = $layer->insertAtTheBottom ? 'true' : 'false';
                $js[] = "$this->name.addLayer($layerJs, $insertAtTheBottom);";
                continue;
            }
            $layer->map = $this->name;
            $js[] = $layer->encode();
        }
        $groups = $this->getEncodedLayerGroups($this->getLayerGroups());
        $controls = $this->getEncodedControls($this->getControls());
        $plugins = $this->getEncodedPlugins($this->getPlugins()->getInstalledPlugins());
        $js = ArrayHelper::merge($js, $groups);
        $js = ArrayHelper::merge($js, $controls);
        $js = ArrayHelper::merge($js, $plugins);
        $js = ArrayHelper::merge($js, $this->_js);
        return $js;
    }

    /**
     * @var PluginManager
     */
    private $_plugins;

    /**
     * @return PluginManager
     */
    public function getPlugins()
    {
        return $this->_plugins;
    }

    /**
     * Installs a plugin
     *
     * @param Plugin $plugin
     */
    public function installPlugin(Plugin $plugin)
    {
        $plugin->map = $this->name;
        $this->getPlugins()->install($plugin);
    }

    public function addVariable($var){
        $js = "var $var = []";
        $this->appendJs(new JsExpression($js));
    }

    /**
     * Removes an installed plugin
     *
     * @param $plugin
     *
     * @return mixed|null
     */
    public function removePlugin($plugin)
    {
        return $this->getPlugins()->remove($plugin);
    }

    /**
     * @var Control[] holding controls to be added to the map.
     */
    private $_functions = [];

    public function setFunctions(array $controls)
    {
        foreach ($controls as $control) {
            if (!($control instanceof Control)) {
                throw new InvalidParamException("All controls must be of type Control.");
            }
        }
        $this->_functions = $controls;
    }

    public function getFunctions()
    {
        return $this->_functions;
    }

    public function addFunction(JsFunction $function)
    {
        $this->_functions[$function->name] = $function;
    }

    public function getEncodedFunctions($functions)
    {
        return $this->getEncodedObjects($functions);
    }

    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();
        if (empty($this->center) || empty($this->zoom)) {
            throw new InvalidConfigException("'center' and/or 'zoom' attributes cannot be empty.");
        }
        $this->_plugins = new PluginManager();
        $this->clientOptions['center'] = $this->center->toArray(true);
        $this->clientOptions['zoom'] = $this->zoom;
    }

    /**
     * Helper method to render the widget. It is also possible to use the widget directly:
     * ```
     * echo Map::widget(['leafLet' => $leafLetObject, ...]);
     * ```
     *
     * @param array $config
     *
     * @return string
     */
    public function widget($config = [])
    {
        ob_start();
        ob_implicit_flush(false);
        $config['map'] = $this;
        $widget = new WidgetMap($config,$this->styleOptions);
        $out = $widget->run();
        return ob_get_clean() . $out;
    }

    /**
     * @param Layer $layer the layer script to add to the js script code. It could be any object extending from [[Layer]]
     * component (markers, polylines, popup, etc)
     *
     * @return static the component itself
     */
    public function addLayer(Layer $layer)
    {
        $this->_layers[] = $layer;
        return $this;
    }

    /**
     * @return Layer[] the stored layers
     */
    public function getLayers()
    {
        return $this->_layers;
    }

    /**
     * @param LayerGroup $group sets a layer group
     *
     * @return static the component itself
     */
    public function addLayerGroup(LayerGroup $group)
    {
        $this->_layerGroups[] = $group;
        return $this;
    }

    /**
     * @return layers\LayerGroup[] all stored layer groups
     */
    public function getLayerGroups()
    {
        return $this->_layerGroups;
    }

    /**
     * Clears all stored layer groups
     * @return static the component itself
     */
    public function clearLayerGroups()
    {
        $this->_layerGroups = [];
        return $this;
    }

    /**
     * @param Control[] $controls
     *
     * @return array
     */
    public function getEncodedControls($controls)
    {
        return $this->getEncodedObjects($controls);
    }

    /**
     * @param LayerGroup[] $groups
     *
     * @return array
     */
    public function getEncodedLayerGroups($groups)
    {
        return $this->getEncodedObjects($groups);
    }

    /**
     * @param Plugin[] $plugins
     *
     * @return array
     */
    public function getEncodedPlugins($plugins)
    {
        return $this->getEncodedObjects($plugins);
    }

    /**
     * @return string
     */
    public static function generateName()
    {
        return self::$autoNamePrefix . self::$counter++;
    }

    /**
     * @param $objects
     *
     * @return array
     */
    protected function getEncodedObjects($objects)
    {
        $js = [];
        foreach ((array)$objects as $object) {
            if (property_exists($object, 'map')) {
                $object->map = $this->name;
            }
            $js[] = method_exists($object, 'encode') ? $object->encode() : null;
        }
        return array_filter($js);
    }

}


