<?php
namespace app\modules\quanly\models\hocau;
use app\modules\quanly\base\QuanlyBaseModel;

/**
 * Model cho bảng nk_cln_hang_ngay
 * Lưu toàn bộ CLN theo giờ trong 1 ngày dạng JSONB
 * + Jar test 2 lần/ngày + người trực/kiểm tra
 */
class NkClnHangNgay extends QuanlyBaseModel
{
    // Danh sách giờ ca ngày (7h–18h) và ca đêm (19h–6h)
    const GIO_CA_NGAY = [7,8,9,10,11,12,13,14,15,16,17,18];
    const GIO_CA_DEM  = [19,20,21,22,23,0,1,2,3,4,5,6];
    const GIO_ALL     = [7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,0,1,2,3,4,5,6];

    // Các chỉ tiêu theo giờ — key => [label, unit, qcvn_min, qcvn_max]
    const CHI_TIEU = [
        'nt_ph'       => ['NT pH',        '',        null, null],
        'nl1_ph'      => ['NL1 pH',       '',        null, null],
        'nl2_ph'      => ['NL2 pH',       '',        null, null],
        'ns_ph'       => ['NS pH',        '',        6.0,  8.5],
        'nt_ntu'      => ['NT NTU',       'NTU',     null, null],
        'nl1_ntu'     => ['NL1 NTU',      'NTU',     null, 5.0],
        'nl2_ntu'     => ['NL2 NTU',      'NTU',     null, 5.0],
        'ns_ntu'      => ['NS NTU',       'NTU',     null, 2.0],
        'clo_du'      => ['Clo dư',       'mg/L',    0.2,  1.0],
        'ns_do_mau'   => ['NS Độ màu',    'Pt-Co',   null, 15.0],
        'nt_do_mau'   => ['NT Độ màu',    'Pt-Co',   null, null],
        'ns_do_kiem'  => ['NS Độ kiềm',   'CaCO3',   null, null],
        'nt_do_kiem'  => ['NT Độ kiềm',   'CaCO3',   null, null],
        'ns_do_cung'  => ['NS Độ cứng',   'CaCO3',   null, 300.0],
        'nt_do_cung'  => ['NT Độ cứng',   'CaCO3',   null, null],
        'ns_clorua'   => ['NS Clorua',    'mg/L',    null, 250.0],
        'nt_clorua'   => ['NT Clorua',    'mg/L',    null, null],
        'pac_ty_trong'=> ['PAC Tỷ trọng', '',        null, null],
    ];

    public static function tableName() { return 'nk_cln_hang_ngay'; }

    public function rules()
    {
        return [
            [['ngay'], 'required'],
            ['ngay', 'date', 'format'=>'php:Y-m-d'],
            ['gio_data', 'string'],
            [['jar_gio_sang','jar_gio_chieu'], 'string'],
            [['jar_s_pac','jar_s_ntu','jar_s_ph',
              'jar_c_pac','jar_c_ntu','jar_c_ph'], 'safe'],
            [['jar_s_chon','jar_c_chon'], 'number', 'min'=>0],
            [['nguoi_truc_sang','nguoi_truc_chieu','nguoi_kt',
              'nguoi_nhap','ghi_chu'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'ngay'              => 'Ngày',
            'gio_data'          => 'Dữ liệu theo giờ',
            'jar_gio_sang'      => 'Giờ Jar test sáng',
            'jar_gio_chieu'     => 'Giờ Jar test chiều',
            'jar_s_chon'        => 'Liều PAC chọn (sáng)',
            'jar_c_chon'        => 'Liều PAC chọn (chiều)',
            'nguoi_truc_sang'   => 'Người trực ca sáng',
            'nguoi_truc_chieu'  => 'Người trực ca chiều',
            'nguoi_kt'          => 'Người kiểm tra',
            'ghi_chu'           => 'Ghi chú',
        ];
    }

    /**
     * Lấy dữ liệu một giờ cụ thể
     */
    public function getGioRow(int $gio): array
    {
        $data = json_decode($this->gio_data ?? '{}', true);
        return $data[(string)$gio] ?? [];
    }

    /**
     * Set dữ liệu một giờ
     */
    public function setGioRow(int $gio, array $vals): void
    {
        $data = json_decode($this->gio_data ?? '{}', true);
        $data[(string)$gio] = $vals;
        $this->gio_data = json_encode($data);
    }

    /**
     * Tính trung bình các chỉ tiêu trong ca
     */
    public function trungBinhCa(int $ca): array
    {
        $gioList = $ca === 1 ? self::GIO_CA_NGAY : self::GIO_CA_DEM;
        $data    = json_decode($this->gio_data ?? '{}', true);
        $sums    = []; $counts = [];
        foreach ($gioList as $g) {
            $row = $data[(string)$g] ?? [];
            foreach ($row as $k => $v) {
                if ($v !== null && $v !== '') {
                    $sums[$k]   = ($sums[$k]   ?? 0) + (float)$v;
                    $counts[$k] = ($counts[$k] ?? 0) + 1;
                }
            }
        }
        $avgs = [];
        foreach ($sums as $k => $s) {
            $avgs[$k] = $counts[$k] > 0 ? round($s / $counts[$k], 2) : null;
        }
        return $avgs;
    }

    /**
     * Kiểm tra status của 1 giá trị so QCVN
     */
    public static function checkStatus(string $field, $val): string
    {
        if ($val === null || $val === '') return '';
        $ct = self::CHI_TIEU[$field] ?? null;
        if (!$ct) return '';
        [$label, $unit, $mn, $mx] = $ct;
        $v = (float)$val;
        if ($mn !== null && $v < $mn) return 'bad';
        if ($mx !== null && $v > $mx) return 'bad';
        return 'ok';
    }
}