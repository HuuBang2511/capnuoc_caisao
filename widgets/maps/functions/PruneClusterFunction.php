<?php
/**
 * Created by PhpStorm.
 * User: MinhDuc
 * Date: 6/2/2020
 * Time: 3:09 PM
 */

namespace app\widgets\maps\functions;

use app\services\DebugService;
use yii\web\JsExpression;
use app\widgets\maps\plugins\prunecluster\PruneCluster;

class PruneClusterFunction extends JsFunction
{
    public $listUrl;

    public $itemUrl;

    public $url;

    public $target;

    public $ref_var;

    public $configs;

    public function encode()
    {
        $js = "function init$this->name(response){\n";
        $js .= "\t" . "var pruneCluster = createPruneCluster();\n";
        $js .= "\t" . "var data = response.features;\n";
        $js .= "\t" . "if(Array.isArray(data)){\n";
        $js .= "\t\t" . "data.map(function (item)  {\n";
        $js .= "\t\t\t" . "var marker = new PruneCluster.Marker(item.geo_y, item.geo_x);\n";
        $js .= "\t\t\t" . "marker.data = item;\n";
        $js .= "\t\t\t" . "pruneCluster.RegisterMarker(marker);\n";
        $js .= "\t\t" . "})\n";
        $js .= "\t};\n";
        $js .= "\t" . "$this->ref_var = pruneCluster;";
        $js .= "\t" . "$this->map.addLayer($this->ref_var);\n";
        $js .= "};\n";

        $pruneCluster = new PruneCluster([
            'name' => 'pruneCluster',
            'listUrl' => $this->listUrl,
            'itemUrl' => $this->itemUrl,
            'var' => $this->ref_var,
            'map' => $this->map,
        ]);

        $js .= "function createPruneCluster(){\n";
        $js .= "\t" . $pruneCluster->encode();
        $js .= "\n}";

        return new JsExpression($js);
    }

}