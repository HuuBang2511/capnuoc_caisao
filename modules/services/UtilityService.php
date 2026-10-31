<?php
/**
 * Created by PhpStorm.
 * User: MinhDuc
 * Date: 7/6/2016
 * Time: 9:17 PM
 */

namespace app\modules\services;

use DOMStringExtend;
use yii\helpers\VarDumper;

class UtilityService {

    public static function alert($content){
        \Yii::$app->session->addFlash($content,true);
        return true;
    }

    public static  function paramValidate($id){
        if (!isset($id) || $id == null ||is_numeric($id) == false) {
            return false;
        } else {
            return true;
        }

    }

    public static function utf8convert($str) {

        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ằ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );
        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/", $nonUnicode, $str);
        }

        return $str;
    }

    public static function convertDateFromDb($date){
        if($date != null){
            return date('d/m/Y',strtotime($date));
        } else {
            return '';
        }
    }

    public static function convertDateFromMaskedInput($date){
        if($date != null){
            return date('Y-m-d',strtotime(str_replace('/','-',$date)));
        } else {
            return '';
        }
    }

    public static function getRelatedInfo($model = null){
        if($model != null){
            return $model->ten;
        } else {
            return '';
        }
    }

}