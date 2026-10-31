<?php


namespace app\modules\quanly\base;


use hcmgis\user\controllers\BaseController;
use yii\web\Controller;

class DanhmucBaseController extends BaseController
{
    public $layout = '@app/modules/layouts/main';

    public $title;

    public $url = '';

    public $label = [
        'index' => 'Danh sách',
        'search' => 'Tìm kiếm',
        'create' => 'Thêm mới',
        'update' => 'Cập nhật',
        'view' => 'Thông tin chi tiết',
        'delete' => 'Xóa',
    ];
}