<?php
namespace app\modules\quanly\models\hocau;
use app\modules\quanly\base\QuanlyBaseModel;

/**
 * Model nk_chat_luong_gio — khớp đúng Excel BM 01.01
 * Đầy đủ tất cả cột theo giờ và bảng tính toán Clo
 */
class NkChatLuongGio extends QuanlyBaseModel
{
    const QCVN = [
        'ns_ph'  => ['min' => 6.5, 'max' => 8.5,  'unit' => ''],
        'ns_ntu' => ['min' => 0,   'max' => 2.0,  'unit' => 'NTU'],
        'clo_du' => ['min' => 0.2, 'max' => 1.0,  'unit' => 'mg/L'],
        'nl1_ntu'=> ['min' => 0,   'max' => 5.0,  'unit' => 'NTU'], // Đã sửa thành < 5 theo yêu cầu
        'nl2_ntu'=> ['min' => 0,   'max' => 5.0,  'unit' => 'NTU'],
        'ns_do_mau'  => ['min' => 0, 'max' => 15.0, 'unit' => 'Pt-Co'],
        'ns_do_cung' => ['min' => 0, 'max' => 300.0, 'unit' => 'CaCO3'],
        'ns_clorua'  => ['min' => 0, 'max' => 250.0, 'unit' => 'mg/L'],
    ];

    public static function tableName() { return 'nk_chat_luong_gio'; }

    public function rules()
    {
        return [
            [['thoi_gian', 'ca'], 'required'],
            ['ca', 'in', 'range' => [1, 2]],
            [['ns_ph','nt_ph','nl1_ph','nl2_ph','ngoai_ho_ph'], 'number','min'=>0,'max'=>14],
            [['ns_ntu','nt_ntu','nl1_ntu','nl2_ntu',
              'ho_xi_phong_1_ntu','ho_xi_phong_2_ntu','ngoai_ho_ntu'], 'number','min'=>0],
            ['clo_du', 'number','min'=>0,'max'=>5],
            [['ns_clo_nong_do','nt_clo_nong_do','nc_clo_cham','pac_cham'], 'number','min'=>0],
            [['muong_pu_thu_hoi','muong_lang_nl1','muong_pu_ns','dau_be_ns'], 'number','min'=>0,'max'=>5],
            ['pac_ty_trong', 'number','min'=>0],
            [['ns_do_mau','nt_do_mau'], 'number','min'=>0],
            [['ns_do_kiem','nt_do_kiem','ns_do_cung','nt_do_cung','ns_clorua','nt_clorua'], 'number', 'min'=>0],
            [['clo_mat_ban_dau','clo_mat_trong_be','clo_khoi_luong_cham','clo_ll_nuoc_tho'], 'number', 'min'=>0],
            [['nguoi_truc','nguoi_kt','nguoi_nhap','ghi_chu'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'thoi_gian'       => 'Thời gian',
            'ca'              => 'Ca',
            'ns_ph'           => 'NS pH',
            'ns_ntu'          => 'NS NTU (<0.4)',
            'ns_do_mau'       => 'NS Độ màu (Pt-Co <15)',
            'ns_do_kiem'      => 'NS Độ kiềm (CaCO3)',
            'ns_do_cung'      => 'NS Độ cứng (CaCO3 <300)',
            'ns_clorua'       => 'NS Clorua (mg/L <250)',
            'nt_ph'           => 'NT pH',
            'nt_ntu'          => 'NT NTU',
            'nt_do_mau'       => 'NT Độ màu (Pt-Co)',
            'nt_do_kiem'      => 'NT Độ kiềm (CaCO3)',
            'nt_do_cung'      => 'NT Độ cứng (CaCO3)',
            'nt_clorua'       => 'NT Clorua (mg/L)',
            'nl1_ph'          => 'NL1 pH',
            'nl1_ntu'         => 'NL1 NTU (<5)',
            'nl2_ph'          => 'NL2 pH',
            'nl2_ntu'         => 'NL2 NTU (<5)',
            'clo_du'          => 'Clor dư TB/PS (0.2–1.0)',
            'ns_clo_nong_do'  => 'Nước cấp nồng độ clo (ppm)',
            'nt_clo_nong_do'  => 'Nước thô nồng độ clo (ppm)',
            'nc_clo_cham'     => 'Nồng độ clo châm nước cấp (ppm)',
            'pac_cham'        => 'Nồng độ PAC châm (mg/L)',
            'ngoai_ho_ph'     => 'Nước ngoài hồ pH',
            'ngoai_ho_ntu'    => 'Nước ngoài hồ NTU',
            'muong_pu_thu_hoi'=> 'Mương PƯ (thu hồi) Clor dư',
            'muong_lang_nl1'  => 'Mương lắng NL1 Clor dư',
            'muong_pu_ns'     => 'Mương PƯ NS Clor dư',
            'dau_be_ns'       => 'Đầu bể NS Clor dư',
            'ho_xi_phong_1_ntu' => 'Hố xi phông 1 NTU',
            'ho_xi_phong_2_ntu' => 'Hố xi phông 2 NTU',
            'pac_ty_trong'    => 'PAC Pha Tỷ trọng',
            'nguoi_truc'      => 'Người trực',
            'nguoi_kt'        => 'Người kiểm tra',
        ];
    }

    public function getStatus(string $field): string
    {
        if (!isset(self::QCVN[$field]) || $this->$field === null) return 'ok';
        $q = self::QCVN[$field];
        $v = (float)$this->$field;
        if ($v < $q['min'] || $v > $q['max']) return 'bad';
        $range = $q['max'] - $q['min'];
        if ($range > 0 && ($v < $q['min'] + $range*0.05 || $v > $q['max'] - $range*0.05)) return 'warn';
        return 'ok';
    }
}