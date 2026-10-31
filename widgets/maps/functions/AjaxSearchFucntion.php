<?php
/**
 * Created by PhpStorm.
 * User: MinhDuc
 * Date: 7/27/2020
 * Time: 8:42 AM
 */

namespace app\widgets\maps\functions;


class GeojsonLayerFunction extends JsFunction
{
    public $listUrl;

    public $itemUrl;

    public $ref_var = null;

    public $configs;

    public function encode()
    {
        $js = "
        function initSearchDoanhnghiep() {
            $('#search-box').on('keypress', function (e) {
                if (e.keyCode == 13) {
                    var q = $(this).val();
                    loadAjaxToDivListNocgia('../ban-do/list-doanhnghiep?q=' + q);
                }
            })
        };
        ";
        return new JsExpression($js);
    }
}