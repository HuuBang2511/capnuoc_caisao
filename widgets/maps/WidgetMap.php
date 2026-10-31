<?php

namespace app\widgets\maps;

use app\widgets\maps\functions\DefaultFunctions;
use app\services\DebugService;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;

class WidgetMap extends Widget
{

    public $initVariables;
    public $initFunctions;
    public $draggableMarker;
    /**
     * @var LeafletMap component holding all configuration
     */
    public $map;
    /**
     * @var string the height of the map. Failing to configure the height of the map, will result in
     * unexpected results.
     */
    public $height = '200px';
    /**
     * @var array the HTML attributes for the widget container tag.
     */
    public $options = [];

    public $styleOptions = [];

    public $tabId = null;
    /**
     * Initializes the widget.
     * This method will register the bootstrap asset bundle. If you override this method,
     * make sure you call the parent implementation first.
     */
    public function init()
    {
        parent::init();
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        if (empty($this->map)) {
            throw new InvalidConfigException(
                "'map' attribute cannot be empty and should be of type LeafLet component."
            );
        }
        if (is_numeric($this->height)) {
            $this->height .= 'px';
        }

        Html::addCssStyle($this->options, $this->styleOptions, false);
    }

    /**
     * Renders the map
     * @return string|void
     */
    public function run()
    {
        echo "\n" . Html::tag('div', '', $this->options);
        $this->registerScript();
    }

    /**
     * Register the script for the map to be rendered according to the configurations on the LeafLet
     * component.
     */
    public function registerScript()
    {
        $name = $this->map->name;
        $view = $this->getView();

        $view->registerJs("var $name;");
        $view->registerJs("var marker_doanhnghiep = [];");

        LeafletMapAsset::register($view);
        $this->map->getPlugins()->registerAssetBundles($view);

        $id = $this->options['id'];
        $js = $this->map->getJs();

        $clientOptions = $this->map->clientOptions;

        // for map load event to fire, we have to postpone setting view, until events are bound
        // see https://github.com/Leaflet/Leaflet/issues/3560
        $lateInitClientOptions['center'] = Json::encode($clientOptions['center']);
        $lateInitClientOptions['zoom'] = $clientOptions['zoom'];
        if (isset($clientOptions['bounds'])) {
            $lateInitClientOptions['bounds'] = $clientOptions['bounds'];
            unset($clientOptions['bounds']);
        }
        unset($clientOptions['center']);
        unset($clientOptions['zoom']);

        $options = empty($clientOptions) ? '{}' : Json::encode($clientOptions, LeafletMap::JSON_OPTIONS);
        array_unshift($js, "$name = L.map('$id', $options);");
        if ($this->map->getTileLayer() !== null) {
            $js[] = $this->map->getTileLayer()->encode();
        }

        $clientEvents = $this->map->clientEvents;

        if (!empty($clientEvents)) {
            foreach ($clientEvents as $event => $handler) {
                $js[] = "$name.on('$event', $handler);";
            }
        }

        if (isset($lateInitClientOptions['bounds'])) {
            $js[] = "$name.fitBounds({$lateInitClientOptions['bounds']});";
        } else {
            $js[] = "$name.setView({$lateInitClientOptions['center']}, {$lateInitClientOptions['zoom']});";
        }



        $view->registerJs("function {$name}Init(){\n" . implode("\n", $js) . "\n};");

        $initJS = "{$name}Init();";

        $view->registerJs($initJS);
        $view->registerJs("function invalidateSizeMap()
    {
        setTimeout(function () {map.invalidateSize()}, 200)
    }");

        if($this->tabId != null){
            $tabId = $this->tabId;
            $view->registerJs("var tab = document.getElementById('{$tabId}');
  var observer = new MutationObserver(function(){
    if(tab.style.display != 'none'){
      {$name}.invalidateSize();
    }
  });
  observer.observe(tab, {attributes: true}); ");
        }


        if($this->draggableMarker != null){
            $this->draggableMarker->map = $name;
            $view->registerJs($this->draggableMarker->encode());
        }


        $functions = $this->map->getFunctions();
        $initFunctionsJS = "";
        if(sizeof($functions) > 0){
            foreach ($functions as $index => $function) {
                $function->map = $name;
                $view->registerJs($function->encode());
                $param = '';
                if (in_array("url", $function->params)) {
                    $param = $function->listUrl;
                }
                $initFunctionsJS .= "init$index('$param');";
            }
            $view->registerJs($initFunctionsJS);
            $view->registerJs('initSearchDoanhnghiep();');
        }

    }
}
