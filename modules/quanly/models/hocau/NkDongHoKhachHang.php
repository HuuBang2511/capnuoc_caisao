<?php
namespace app\modules\quanly\models\hocau;
use app\modules\quanly\base\QuanlyBaseModel;

class NkDongHoKhachHang extends QuanlyBaseModel
{
    public static function tableName() { return 'nk_dong_ho_khach_hang'; }

    public function rules()
    {
        return [
            [['ten_kh','channel_dau_vao'], 'required'],
            ['ten_kh',          'string',  'max'=>200],
            ['ghi_chu',         'string'],
            ['don_vi',          'string',  'max'=>20],
            ['thu_tu',          'integer', 'min'=>0],
            ['active',          'boolean'],
            ['channel_dau_vao', 'string'],  // JSON array string
            ['channel_dau_ra',  'string'],  // JSON array string
        ];
    }

    public function attributeLabels()
    {
        return [
            'ten_kh'          => 'Tên khách hàng',
            'thu_tu'          => 'Thứ tự',
            'channel_dau_vao' => 'Đồng hồ đầu vào (Channel IDs)',
            'channel_dau_ra'  => 'Đồng hồ đầu ra / trừ đi',
            'don_vi'          => 'Đơn vị',
            'ghi_chu'         => 'Ghi chú',
            'active'          => 'Đang sử dụng',
        ];
    }

    /**
     * Lấy mảng channelId đầu vào
     */
    public function getChannelDauVaoArr(): array
    {
        $arr = json_decode($this->channel_dau_vao ?? '[]', true);
        // Giu nguyen string (ho tro ca so '60007' lan chuoi 'NT5_D200')
        return is_array($arr) ? array_values(array_filter(array_map('strval', $arr))) : [];
    }

    /**
     * Lấy mảng channelId đầu ra (trừ đi)
     */
    public function getChannelDauRaArr(): array
    {
        $arr = json_decode($this->channel_dau_ra ?? '[]', true);
        return is_array($arr) ? array_values(array_filter(array_map('strval', $arr))) : [];
    }

    /**
     * Label đồng hồ đầu vào để hiển thị
     */
    public function getLabelDauVao(): string
    {
        $ids = $this->getChannelDauVaoArr();
        return count($ids) ? implode(', ', $ids) : '—';
    }

    /**
     * Label đồng hồ đầu ra để hiển thị
     */
    public function getLabelDauRa(): string
    {
        $ids = $this->getChannelDauRaArr();
        return count($ids) ? implode(', ', $ids) : '—';
    }

    /**
     * Lấy tất cả khách hàng đang active, sắp xếp theo thu_tu
     */
    public static function getActive(): array
    {
        return static::find()
            ->where(['active' => true])
            ->orderBy(['thu_tu' => SORT_ASC, 'id' => SORT_ASC])
            ->all();
    }
}