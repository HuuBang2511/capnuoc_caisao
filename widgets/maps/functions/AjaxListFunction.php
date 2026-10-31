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

class AjaxListFunction extends JsFunction
{
    public $listUrl;

    public $itemUrl;

    public $url;

    public $target;

    public $ref_var;

    public $configs;

    public function encode()
    {
        $params = implode(',', $this->params);
        $js = "
        function init" . $this->name . "($params){
            var div = $('$this->target');
            $.ajax({
                url: url,
                success: function (html) { 
                    div.empty().append(html);
                    initPagAjaxDivList();
                    initClickEvent();                               
        ";

        $js .= "},
        error: function (xhr, ajaxOptions, thrownError) {
        alert(xhr.status);
        alert(thrownError);
      }
        });}";

        $js .= $this->generatePagination();
        $js .= $this->generateClickEvent();
        $js .= $this->generateSearch();

        return new JsExpression($js);
    }

    private function generateSearch()
    {
        $js = "
        function initSearchDoanhnghiep() {
                                $('#search-box').on('keypress', function (e) {
                                    if (e.keyCode == 13) {
                                        var q = $(this).val();
                                        init$this->name('../ban-do/list-doanhnghiep?q=' + q);
                                    }
                                })
                            };
        ";
        return new JsExpression($js);
    }

    private function generatePagination()
    {
        $js = "
        function initPagAjaxDivList() {
            $('.pagination li a').on('click', function (e) {
                e.preventDefault();
                var _this = $(this);
                var url = _this.attr('href');
                init$this->name(url);
                return false;
            });
        };       
        ";
        return new JsExpression($js);
    }

    private function generateClickEvent()
    {
        $js = "
        function initClickEvent() {
        $('.nocgia-item').on('click', function (e) {
            var _this = $(this);
            var point_x = _this.attr('data-point-x');
            var point_y = _this.attr('data-point-y');
            var target = _this.attr('data-target');
            console.log(marker_doanhnghiep);
            if(typeof point_x !== \"undefined\" && typeof target !== \"undefined\"){
                e.preventDefault();
                $this->map.setView([point_y, point_x],16);
                
                marker_doanhnghiep[target].openPopup();
                $.ajax({
                    url: '../ban-do/get-doanhnghiep?id=' + target,
                    success: function (html) {
                        $('#' + 'marker-popup-' + target).empty().append(html);
                    }
                });
            }
        });
        };
        ";
        return new JsExpression($js);
    }
}