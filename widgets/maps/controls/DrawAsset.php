<?php
/**
 * @copyright Copyright (c) 2015 David J Eddy
 * @link http://davidjeddy.com
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace app\widgets\maps\controls;

use yii\web\AssetBundle;

/**    
 *
 * @author David J Eddy <me@davidjeddy.com>
 * @link http://www.davidjeddy.com/
 * @link https://github.com/davidjeddy
 * @package davidjeddy\leaflet\plugins\draw
 */
class DrawAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/maps/assets';
    public $css        = ['css/leaflet.draw.css'];
    public $depends    = ['app\widgets\maps\LeafLetMapAsset'];
    public $js         = ['js/leaflet.draw.js'];


    public function init()
    {
//        $this->js = YII_DEBUG ? ['leaflet.draw-src.js'] : ['leaflet.draw.js'];
    }
}
