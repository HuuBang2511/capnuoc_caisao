<?php
/**
 * /**
 * @copyright Copyright (c) 2013-2015 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */

namespace app\widgets\maps\functions;


use app\widgets\maps\plugins\prunecluster\PruneCluster;
use app\services\DebugService;
use yii\base\Component;
use yii\web\JsExpression;


abstract class JsFunction extends Component
{

    public $name;
    public $map;

    public $params = [];

    private $_functions = [];
    /**
     * @var float Sets the radius of a circle. Units are in meters.
     */
    public $js;

    public $configs = [];

    /**
     * Returns the javascript ready code for the object to render
     * @return JsExpression
     */
    abstract public function encode();


}
