<?php

namespace app\modules\quanly\controllers;

use yii\web\Controller;
// Import Models danh mục để nạp vào bộ lọc (Giữ nguyên như cũ)
use app\modules\quanly\models\danhmuc\DmLoaiong;
use app\modules\quanly\models\danhmuc\DmHieudongho;
use app\modules\quanly\models\danhmuc\DmTinhtrang;
use app\modules\quanly\models\danhmuc\DmSucoNguyennhan;
use app\modules\quanly\models\danhmuc\DmLoaiham;
use app\modules\quanly\models\danhmuc\DmLoaimoinoi;

class MapController extends Controller
{
    public function actionHocau()
    {
        $this->layout = false;
        
        // Lấy dữ liệu danh mục cho bộ lọc (Giữ nguyên)
        $filterData = [
            'loaiong' => DmLoaiong::find()->select(['id', 'ten'])->asArray()->all(),
            'hieudongho' => DmHieudongho::find()->select(['id', 'ten'])->asArray()->all(),
            'tinhtrang' => DmTinhtrang::find()->select(['id', 'ten'])->asArray()->all(),
            'nguyennhan' => DmSucoNguyennhan::find()->select(['id', 'ten'])->asArray()->all(),
            'loaiham' => DmLoaiham::find()->select(['id', 'ten'])->asArray()->all(),
            'loaimoinoi' => DmLoaimoinoi::find()->select(['id', 'ten'])->asArray()->all(),
        ];

        return $this->render('hocau', [
            'filterData' => $filterData
        ]);
    }
}
