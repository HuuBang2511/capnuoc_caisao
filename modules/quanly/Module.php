<?php
namespace app\modules\quanly;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\quanly\controllers';

    public function init()
    {
        parent::init();

        \Yii::$app->urlManager->addRules([
            // ── Nhật ký vận hành ──────────────────────────────────
            'quanly/nhat-ky/bao-cao'              => 'quanly/nhat-ky/bao-cao',
            'quanly/nhat-ky/chat-luong-gio'       => 'quanly/nhat-ky/chat-luong-gio',
            'quanly/nhat-ky/giao-ca'              => 'quanly/nhat-ky/giao-ca',
            'quanly/nhat-ky/api-cln'              => 'quanly/nhat-ky/api-cln',
            'quanly/nhat-ky/xuat-bao-cao-ngay'   => 'quanly/nhat-ky/xuat-bao-cao-ngay',

            // ── Nước thải sinh hoạt (MỚI) ─────────────────────────
            'quanly/nhat-ky/nuoc-thai-sh'         => 'quanly/nhat-ky/nuoc-thai-sh',

            // ── Sản lượng đồng hồ (MỚI) ──────────────────────────
            'quanly/nhat-ky/san-luong-dong-ho'    => 'quanly/nhat-ky/san-luong-dong-ho',
            'quanly/nhat-ky/dong-ho-config'       => 'quanly/nhat-ky/dong-ho-config',
            'quanly/nhat-ky/dong-ho-save'         => 'quanly/nhat-ky/dong-ho-save',
            'quanly/nhat-ky/dong-ho-delete'       => 'quanly/nhat-ky/dong-ho-delete',
            'quanly/nhat-ky/api-san-luong'        => 'quanly/nhat-ky/api-san-luong',
            'quanly/nhat-ky/xuat-san-luong'       => 'quanly/nhat-ky/xuat-san-luong',
        ], false);
    }
}