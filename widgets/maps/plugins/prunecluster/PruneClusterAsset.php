<?php
/**
 * @copyright Copyright (c) 2013-2015 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace app\widgets\maps\plugins\prunecluster;


use yii\web\AssetBundle;

/**
 * MarkerClusterAsset
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\leaflet\plugins\markercluster
 */
class PruneClusterAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/maps/assets';

    public $css = [
        'css/LeafletStyleSheet.css',
    ];

    public $js = [
        'js/PruneCluster.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'app\widgets\maps\LeafLetMapAsset',
    ];

//    public $js = [
//        'https://unpkg.com/leaflet.markercluster@1.0.0/dist/leaflet.markercluster.js'
//    ];

//    public function init()
//    {
//        $this->sourcePath = __DIR__ . '/assets';
//        $this->js = YII_DEBUG ? ['js/leaflet.markercluster-src.js'] : ['js/leaflet.markercluster.js'];
//    }
}
