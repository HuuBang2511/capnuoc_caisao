<?php
namespace app\modules\services;

use app\modules\danhmuc\models\DmKtvhxh;
use app\modules\danhmuc\models\DmTongiao;

use app\modules\quanly\models\danhmuc\DmHieudongho;
use app\modules\quanly\models\danhmuc\DmLoaiham;
use app\modules\quanly\models\danhmuc\DmLoaimoinoi;
use app\modules\quanly\models\danhmuc\DmLoainhamay;
use app\modules\quanly\models\danhmuc\DmLoaiong;
use app\modules\quanly\models\danhmuc\DmLoaivan;
use app\modules\quanly\models\danhmuc\DmSucoLoai;
use app\modules\quanly\models\danhmuc\DmSucoNguyennhan;
use app\modules\quanly\models\danhmuc\DmSucoTinhtrang;
use app\modules\quanly\models\danhmuc\DmTinhtrang;


class CategoriesService
{

    public static function getCategories()
    {
        $categories = [];
        $categories['hieudongho'] = DmHieudongho::find()->where(['status'=>1])->orderBy('ten')->asArray()->all();
        $categories['loaiham'] = DmLoaiham::find()->where(['status'=>1])->orderBy('ten')->asArray()->all();
        $categories['loaimoinoi'] = DmLoaimoinoi::find()->where(['status'=>1])->orderBy('ten')->asArray()->all();
        $categories['loainhamay'] = DmLoainhamay::find()->where(['status'=>1])->orderBy('ten')->asArray()->all();
        $categories['loaiong'] = DmLoaiong::find()->where(['status'=>1])->orderBy('ten')->asArray()->all();
        $categories['loaivan'] = DmLoaivan::find()->where(['status'=>1])->orderBy('ten')->asArray()->all();
        $categories['loaisuco'] = DmSucoLoai::find()->where(['status'=>1])->orderBy('ten')->asArray()->all();
        $categories['nguyennhansuco'] = DmSucoNguyennhan::find()->where(['status'=>1])->orderBy('ten')->asArray()->all();
        $categories['tinhtrangsuco'] = DmSucoTinhtrang::find()->where(['status'=>1])->orderBy('ten')->asArray()->all();
        $categories['tinhtrang'] = DmTinhtrang::find()->where(['status'=>1])->orderBy('ten')->asArray()->all();

        return $categories;
    }

    public static function getDanhmuc_suco() {
        $categories = [];
        $categories['dm_suco_bienphapxuly'] = GdDmSucoBienphapxuly::find()->select(['id','ten', 'ma'])->where(['status'=>1])->orderBy('ten')->asArray()->all();
        $categories['dm_suco_ketcaumatduong'] = GdDmSucoKetcaumatduong::find()->select(['id','ten', 'ma'])->where(['status'=>1])->orderBy('ten')->asArray()->all();
        $categories['dm_suco_nguyennhan'] = GdDmSucoNguyennhan::find()->select(['id','ten', 'ma'])->where(['status'=>1])->orderBy('ten')->asArray()->all();
        $categories['dm_xulysuco'] = GdDmXulysuco::find()->select(['id','ten'])->where(['status'=>1])->orderBy('ten')->asArray()->all();
        return $categories;
    }

}