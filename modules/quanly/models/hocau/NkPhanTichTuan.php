<?php
namespace app\modules\quanly\models\hocau;
use yii\db\ActiveRecord;

/**
 * Model nk_phan_tich_tuan — khớp đúng Excel BM 01.02
 * Extends ActiveRecord trực tiếp (không qua QuanlyBaseModel)
 * để tránh UtilityService::convertDateFromMaskedInput() làm hỏng ngay_pt.
 */
class NkPhanTichTuan extends ActiveRecord
{
    // QCVN 01-1:2018/BYT — giới hạn nước sạch (NS)
    const QCVN_NS = [
        'ns_do_cung'     => 300,
        'ns_clorua'      => 250,
        'ns_sulfat'      => 250,
        'ns_permanganat' => 2.0,
        'ns_coliform'    => 0,
        'ns_florua'      => 1.5,
        'ns_al'          => 0.2,
        'ns_fe'          => 0.3,
        'ns_mn'          => 0.1,
        'ns_amoni'       => 3.0,
        'ns_nitrat'      => 50.0,
        'ns_nitrit'      => 3.0,
    ];

    public static function tableName() { return 'nk_phan_tich_tuan'; }

    public function rules()
    {
        return [
            [['ngay_pt'], 'required'],
            ['ngay_pt', 'date', 'format' => 'php:Y-m-d'],
            [['tuan_so'], 'integer', 'min' => 1, 'max' => 5],
            [['thang'],   'integer', 'min' => 1, 'max' => 12],
            [['nam'],     'integer', 'min' => 2000, 'max' => 2099],
            // Nước thô (NT)
            [['nt_do_kiem','nt_do_cung','nt_clorua','nt_tss',
               'nt_sulfat','nt_cod','nt_florua',
               'nt_al','nt_fe','nt_mn',
               'nt_amoni','nt_nitrat','nt_nitrit'], 'number', 'min' => 0],
            ['nt_permanganat', 'number', 'min' => 0],
            ['nt_coliform',    'integer', 'min' => 0],
            // Nước sạch (NS)
            [['ns_do_kiem','ns_do_cung','ns_clorua','ns_tss',
               'ns_sulfat','ns_cod','ns_florua',
               'ns_al','ns_fe','ns_mn',
               'ns_amoni','ns_nitrat','ns_nitrit'], 'number', 'min' => 0],
            ['ns_permanganat', 'number', 'min' => 0],
            ['ns_coliform',    'integer', 'min' => 0],
            [['ghi_chu','nguoi_pt','nguoi_nhap','nguoi_kt'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'ngay_pt'        => 'Ngày phân tích',
            'tuan_so'        => 'Tuần số',
            'nt_do_kiem'     => 'NT Độ kiềm (CaCO3 mg/L)',
            'nt_do_cung'     => 'NT Độ cứng (CaCO3 mg/L)',
            'nt_clorua'      => 'NT Clorua (mg/L)',
            'nt_tss'         => 'NT TSS (mg/L)',
            'nt_al'          => 'NT Nhôm Al (mg/L)',
            'nt_fe'          => 'NT Sắt Fe (mg/L)',
            'nt_mn'          => 'NT Mangan Mn (mg/L)',
            'nt_amoni'       => 'NT Amoni NH4+ (mg/L)',
            'nt_nitrat'      => 'NT Nitrat NO3- (mg/L)',
            'nt_nitrit'      => 'NT Nitrit NO2- (mg/L)',
            'nt_sulfat'      => 'NT Sulfat (mg/L)',
            'nt_permanganat' => 'NT Pecmanganat',
            'nt_cod'         => 'NT COD (mg/L)',
            'nt_coliform'    => 'NT Coliform (VK/100ml)',
            'nt_florua'      => 'NT Florua (µg/L)',
            'ns_do_kiem'     => 'NS Độ kiềm (CaCO3 mg/L)',
            'ns_do_cung'     => 'NS Độ cứng (≤300 CaCO3 mg/L)',
            'ns_clorua'      => 'NS Clorua (≤250 mg/L)',
            'ns_tss'         => 'NS TSS (mg/L)',
            'ns_al'          => 'NS Nhôm Al (≤0.2 mg/L)',
            'ns_fe'          => 'NS Sắt Fe (≤0.3 mg/L)',
            'ns_mn'          => 'NS Mangan Mn (≤0.1 mg/L)',
            'ns_amoni'       => 'NS Amoni NH4+ (≤3 mg/L)',
            'ns_nitrat'      => 'NS Nitrat NO3- (≤50 mg/L)',
            'ns_nitrit'      => 'NS Nitrit NO2- (≤3 mg/L)',
            'ns_sulfat'      => 'NS Sulfat (mg/L)',
            'ns_permanganat' => 'NS Pecmanganat (≤2)',
            'ns_cod'         => 'NS COD (mg/L)',
            'ns_coliform'    => 'NS Coliform (VK/100ml)',
            'ns_florua'      => 'NS Florua (µg/L)',
            'nguoi_pt'       => 'Người thực hiện',
            'nguoi_kt'       => 'Người kiểm tra',
        ];
    }

    public function getNsStatus(string $field): string
    {
        if (!isset(self::QCVN_NS[$field]) || $this->$field === null) return 'ok';
        return (float)$this->$field > self::QCVN_NS[$field] ? 'bad' : 'ok';
    }
}