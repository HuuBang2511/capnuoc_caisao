<?php
/**
 * /**
 * @copyright Copyright (c) 2013-2015 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */

namespace app\widgets\maps\functions;


use yii\base\Component;
use yii\web\JsExpression;


class DefaultFunctions extends Component
{

    public $name;

    private $_functions = [];
    /**
     * @var float Sets the radius of a circle. Units are in meters.
     */
    public $js;

    /**
     * Returns the javascript ready code for the object to render
     * @return JsExpression
     */
    public function encode()
    {

        return new JsExpression($this->js);
    }

    public function initSearch($target){
        $js = "function initSearch.$this->name {";
        $js .= "
            $('#$target').on('keypress', function (e) {
                if (e.keyCode == 13) {
                    var q = $(this).val();
                    loadAjaxToDivListNocgia('../ban-do/list-noc-gia?q=' + q);
                }
            })
        ";
        $js .= "}";
        return new JsExpression($js);
    }

    public function initFunctions(){
        $js = "$(document).ready(function () {";
        if($this->_functions != null){
            foreach($this->_functions as $i => $function){
                $js .= "init".$function->name."();";
            }
        }

        $js .= "})";

        return $js;
    }

    public function loadAjaxToDivList($name, $target_div, $url){
        $js = "function loadAjaxToDivList$name () {";

        $js .= "var div = $('#$target_div');";
        $js .= "
            $.ajax({
                url: $url,
                success: function (html) {
                    div.empty().append(html);
                    $('.pagination li a').on('click', function (e) {
                        e.preventDefault();
                        var _this = $(this);
                        var url = _this.attr('href');
                        loadAjaxToDivList$name(url);
                        return false;
                    });
                }
            });
        ";
        $js .= "}";
        return $js;
    }

    public function initPageAjaxDivList($name){
        $js = "";
    }

}
