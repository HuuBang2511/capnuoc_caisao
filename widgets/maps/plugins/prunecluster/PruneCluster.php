<?php
/**
 * Created by PhpStorm.
 * User: MinhDuc
 * Date: 5/29/2020
 * Time: 3:46 PM
 */

namespace app\widgets\maps\plugins\prunecluster;


use app\widgets\maps\plugins\Plugin;
use app\widgets\maps\layers\Marker;
use yii\web\JsExpression;

class PruneCluster extends Plugin
{
    public $url = false;

    public $listUrl = false;
    public $itemUrl = false;

    public $var = false;

    public $data = false;

    public $pluginName;

    public $mapObject;

    public $homeUrl = "../";

    private $_markers = [];

    /**
     * @return array the markers added
     */
    public function getMarkers()
    {
        return $this->_markers;
    }

    /**
     * Returns the name of the plugin
     * @return string
     */
    public function getPluginName()
    {
        return 'plugin:prunecluster';
    }

    /**
     * Registers plugin asset bundle
     *
     * @param \yii\web\View $view
     *
     * @return static the plugin
     * @codeCoverageIgnore
     */
    public function registerAssetBundle($view)
    {
        PruneClusterAsset::register($view);
        return $this;
    }

    /**
     * @param Marker $marker
     *
     * @return static the plugin
     */

    public function addMarker(Marker $marker)
    {
        $marker->name = $marker->map = null;
        $this->_markers[] = $marker;
        return $this;
    }

    /**
     * Returns the javascript ready code for the object to render
     * @return \yii\web\JsExpression|string
     */
    public function encodePlugin()
    {
        $markers = $this->getMarkers();
        if (empty($markers) && $this->url == false) {
            return "";
        }
        $js = [];
        $options = $this->getOptions();
        $name = !is_null($this->pluginName) ? $this->pluginName : $this->getName(true);
        $map = $this->map;
        $js[] = "var $name = new PruneClusterForLeaflet();";
        if ($this->listUrl) {
            $js[] = "
                $name.Cluster.Size = 20;
                $.ajax({
                    url: '{$this->listUrl}', 
                    dataType: 'json',
                    jsonpCallback: 'getJson',
                    success: function(response){
                        L.geoJson(response, {
                            onEachFeature: function (feature, layer) {
                                var marker = new PruneCluster.Marker(feature.properties.geo_y, feature.properties.geo_x);
                                marker.data = feature.properties;
                                
                                $name.PrepareLeafletMarker = function(marker, data) {
                                    marker.data = data;
                                    var popupid = 'marker-popup-' + data.id;
                                    marker.bindPopup('<div id=\"' + popupid + '\"></div>',{minWidth: 300});
                                    marker.on('click', function () {
                                        $.ajax({
                                            url: {$this->itemUrl} + '?id=' + data.id,
                                            success: function (html) {
                                                $('#' + popupid).empty().append(html);
                                            }
                                        })
                                    });
                                    $this->var['marker-' + data.id] = marker;
                                };
                                $name.RegisterMarker(marker);
                            }
                        })
                    }
                                  
                });
                console.log($this->var);  ";
        }

        $js[] = "$map.addLayer($name);";
        $js = [1];
        return new JsExpression(implode("\n", $js));
    }

    public function encode()
    {
        $js = [1];
        $options = $this->getOptions();
        $name = !is_null($this->pluginName) ? $this->pluginName : $this->getName(true);
        $js[] = "var $name = new PruneClusterForLeaflet();";
        $js[] = "\t"."pruneCluster.PrepareLeafletMarker = function(leafletMarker, data) {";
        $js[] = "\t\t"."leafletMarker.on('click', function(e) {";
        $js[] = "\t\t\t"."var popupid = 'marker-popup-' + data.id;";
        $js[] = "\t\t\t"."leafletMarker.bindPopup('<div id=\"' + popupid + '\"></div>');";
        $js[] = "\t\t\t"."$.ajax({";
        $js[] = "\t\t\t\t"."url: '$this->itemUrl' + '?id=' + data.id,";
        $js[] = "\t\t\t\t"."success: function (html) {";
        $js[] = "\t\t\t\t\t"."$('#' + popupid).empty().append(html);";
        $js[] = "\t\t\t\t"."}";
        $js[] = "\t\t\t"."})";
        $js[] = "\t\t"."});";
        $js[] = "\t"."};";
        $js[] = "\t"."return $name;";
        return new JsExpression(implode("\n", $js));
    }
}