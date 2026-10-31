<?php
/**
 *
 * Draw.php
 *
 *
 * 
 * @copyright Copyright (c) 2015 David J Eddy
 * @link http://davidjeddy.com
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */

namespace app\widgets\maps\controls;

use app\services\DebugService;
use app\widgets\maps\plugins\Plugin;
use yii\web\JsExpression;
use yii\helpers\Json;


/**
 * Draw adds the ability to place line, shapes, and markers to your leaflet maps
 * 
 * @author David J Eddy <me@davidjeddy.com>
 * @link http://www.davidjeddy.com/
 * @link https://github.com/davidjeddy
 * @package davidjeddy\leaflet\plugins\draw
 */
class Draw extends Plugin
{
    /**
     * @var string the name of the javascript variable that will hold the reference
     * to the map object.
     */
    public $map = 'map';
    /**
     * @var array the options for the underlying LeafLetJs JS component.
     * Please refer to the LeafLetJs api reference for possible
     * [options](http://leafletjs.com/reference.html).
     */

    public $url = null;
    public $successResponse;
    public $options = null;
    public $functions = null;

    /* get/set methods */

    /**
     * Returns the plugin name
     * @return string
     */
    public function getPluginName()
    {

        return 'leaflet:draw';
    }

    /**
     * Returns the processed js options
     * @return array
     */
    public function getOptions()
    {
        return ($this->options
            ? json::encode($this->options)
            : '{}'
        );
    }

    /* non get/set methods */

    /**
     * Returns the javascript ready code for the object to render
     * @return \yii\web\JsExpression
     */
    public function encode()
    {
//        $this->options['edit'] = ['featureGroup' => 'drawnItems'];

        $options = $this->getOptions();
//        DebugService::dumpdie($options);
        $js = "
            var editableLayers = new L.FeatureGroup();
            {$this->map}.addLayer(editableLayers);

            var drawnItems = new L.FeatureGroup();  
            {$this->map}.addLayer(drawnItems);
            var drawFeatureGroup = L.featureGroup();
            var options = {};
            options = $options; 
            options.edit = { featureGroup: drawnItems, remove: true };
            
            var drawControl = new L.Control.Draw(options);
            
            
            {$this->map}.addControl(drawControl);
//            
//            var toolbar = new L.Toolbar();
//            toolbar.addToolbar({$this->map});

            {$this->map}.on('draw:created', function (e) {
                var type = e.layerType,
                    layer = e.layer;

                /* if (type === 'marker') {
                    layer.bindPopup('A popup!');
                } */
                
                if(type === 'circle'){
                    var center = layer.getLatLng();
                    var radius = layer.getRadius();
                    var merge = {center,radius};
                    
                    var jsonCircle = JSON.stringify(merge);
                                        console.log(jsonCircle);

                    $.ajax({
                        type: 'GET',
                        url: '$this->url',
                        data: {'data': jsonCircle},
                        contentType: 'application/json',
                        dataType: 'json',
                        success: function(response){
                        console.log(response);
                            {$this->map}.removeLayer(marker_doanhnghiep);
                            init$this->successResponse(response);
                        },
                        complete: function(data){
                        }
                    });
                }

                drawnItems.addLayer(layer);
            });
            
            {$this->map}.on('draw:deleted', function (e) {
                {$this->map}.removeLayer(marker_doanhnghiep);
                initDoanhnghiepGeojson();
                
            });
            
        ";
        return new JsExpression($js);
    }

    /**
     * Registers plugin asset bundle
     * @param \yii\web\View $view
     * @return mixed
     * @codeCoverageIgnore
     */
    public function registerAssetBundle($view)
    {
        DrawAsset::register($view);
        return $this;
    }
}
