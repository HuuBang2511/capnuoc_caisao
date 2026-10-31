<?php


namespace app\modules\services;


class ImportService
{
//    public static $hinhthucdieutri = DmHinhthucDieutri::find()->all();

    public static function getDmGioitinh($gioitinh)
    {
        switch ($gioitinh) {
            case 'NAM' :
                return 1;
            case 'NU' :
                return 2;
            default :
                return 3;
        }
    }
    public static function getDmTrangthaihs($trangthaihs)
    {
        switch ($trangthaihs) {
            case 'DANG HOC' :
                return 1;
            case 'NGHI HOC' :
                return 2;
            default :
                return 3;
        }
    }

    public static function getDmloaibenh($loaibenh)
    {
        switch ($loaibenh) {
            case 'SXH' :
                return 1;
            case 'TCM' :
                return 2;
            case 'SOI' :
                return 3;
            case 'Rubella' :
                return 4;
            case 'CUM' :
                return 5;
            case 'TIEUCHAY' :
                return 6;
            case 'COVID' :
                return 7;
            default :
                return 8;
        }
    }

}