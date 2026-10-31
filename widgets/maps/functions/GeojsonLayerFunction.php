<?php
/**
 * Created by PhpStorm.
 * User: MinhDuc
 * Date: 6/2/2020
 * Time: 2:42 PM
 */

namespace app\widgets\maps\functions;

use app\services\DebugService;
use yii\web\JsExpression;
use app\widgets\maps\plugins\prunecluster\PruneCluster;

class GeojsonLayerFunction extends JsFunction
{
    public $listUrl;

    public $itemUrl;

    public $ref_var = null;

    public $configs;

    public $pruneclusterName = false;

    public function encode()
    {
        $data = 'data.features';
        $js = "
        function init" . $this->name . "(){
        $.ajax({
            url: '$this->listUrl',
            dataType: 'json',
            success: function (data) { 
        ";
        if ($this->pruneclusterName) {
            $js .= "init$this->pruneclusterName(data);";
        } else {
            $js .= <<< JS
L.geoJSON(data,{
    onEachFeature: function (feature, layer) {
        layer.bindTooltip(feature.properties.ten, {permanent: true, direction:"center"}).openTooltip();
        var popupid = 'polygon-popup-' + feature.properties.id;
        layer.bindPopup("<div id='" + popupid + "'></div>",{minWidth: 300});
        layer.on('click', function () {
            $.ajax({
                url: "$this->itemUrl"  + '?id=' + feature.properties.id,
                success: function (html) {
                    $('#' + popupid).empty().append(html);
                }
            })
        });
    }
}).addTo(map);
JS;
        }

        $js .= "}});}";


        return new JsExpression($js);
    }
}