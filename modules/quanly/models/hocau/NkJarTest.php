<?php
namespace app\modules\quanly\models\hocau;
use app\modules\quanly\base\QuanlyBaseModel;

/**
 * Model nk_jar_test
 * Schema thực tế (sau migration):
 *   id, ngay, gio_thu, do_duc_nt
 *   pac_1_lieu..pac_6_lieu, pac_1_ntu..pac_6_ntu, pac_1_ph..pac_6_ph
 *   lieu_chon, ca, nguoi_kt, nguoi_nhap, ghi_chu, created_at
 * Unique: (ngay, gio_thu)
 */
class NkJarTest extends QuanlyBaseModel
{
    public static function tableName()
    {
        return 'nk_jar_test';
    }

    public function rules()
    {
        $numFields = [];
        for ($i = 1; $i <= 6; $i++) {
            $numFields[] = 'pac_' . $i . '_lieu';
            $numFields[] = 'pac_' . $i . '_ntu';
            $numFields[] = 'pac_' . $i . '_ph';
        }

        return [
            [['ngay', 'gio_thu'], 'required'],
            ['ngay',    'date',   'format' => 'php:Y-m-d'],
            ['gio_thu', 'string'],
            ['ca',      'in',     'range' => [1, 2]],
            ['do_duc_nt', 'number', 'min' => 0],
            ['lieu_chon', 'number', 'min' => 0],
            [$numFields,  'number', 'min' => 0],
            [['ghi_chu', 'nguoi_nhap', 'nguoi_kt'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'ngay'      => 'Ngày',
            'gio_thu'   => 'Giờ thu mẫu',
            'ca'        => 'Ca (1=Sáng, 2=Chiều)',
            'do_duc_nt' => 'Độ đục NT (NTU)',
            'lieu_chon' => 'Liều PAC chọn (mg/L)',
            'nguoi_kt'  => 'Người kiểm tra',
            'nguoi_nhap'=> 'Người nhập',
            'ghi_chu'   => 'Ghi chú',
        ];
    }

    /**
     * Trả về index (0-based) của mẫu có NTU nhỏ nhất
     * Dùng để highlight cột được chọn
     */
    public function getMinNtuIndex(): int
    {
        $minNtu = PHP_FLOAT_MAX;
        $minIdx = 0;
        for ($i = 1; $i <= 6; $i++) {
            $f = 'pac_' . $i . '_ntu';
            if ($this->$f !== null && (float)$this->$f < $minNtu) {
                $minNtu = (float)$this->$f;
                $minIdx = $i - 1;
            }
        }
        return $minIdx;
    }

    /**
     * Lấy mảng liều PAC [1..6]
     */
    public function getPacLieuArr(): array
    {
        $arr = [];
        for ($i = 1; $i <= 6; $i++) {
            $arr[] = $this->{'pac_' . $i . '_lieu'};
        }
        return $arr;
    }

    /**
     * Lấy mảng NTU [1..6]
     */
    public function getPacNtuArr(): array
    {
        $arr = [];
        for ($i = 1; $i <= 6; $i++) {
            $arr[] = $this->{'pac_' . $i . '_ntu'};
        }
        return $arr;
    }

    /**
     * Lấy mảng pH [1..6]
     */
    public function getPacPhArr(): array
    {
        $arr = [];
        for ($i = 1; $i <= 6; $i++) {
            $arr[] = $this->{'pac_' . $i . '_ph'};
        }
        return $arr;
    }
}