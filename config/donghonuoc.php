<?php
/**
 * Cấu hình API Đồng hồ nước thông minh (donghonuocthongminh.vn)
 * dùng cho lớp "Đồng hồ lưu lượng" trên bản đồ Cái Sao.
 *
 * - customer_id: UUID khách hàng trên hệ thống donghonuocthongminh (tham số customer_id của API).
 * - lat/lng: tọa độ hiển thị trên bản đồ. Tọa độ trên API của các điểm này đang sai/trống
 *   nên khai báo tại đây. CÁC TỌA ĐỘ DƯỚI ĐÂY LÀ TẠM (khu vực Mỹ Thới - Long Xuyên), cần cập nhật đúng vị trí.
 */
return [
    'base_url' => 'https://api.donghonuocthongminh.vn',
    'username' => 'capnuocangiang',
    'password' => 'Abcd@1234',
    'timezone' => 'Asia/Ho_Chi_Minh',

    'stations' => [
        [
            'customer_id' => 'B89B9480-6BDA-F111-9AC7-B083FEDEBCF7',
            'logger' => '026023120036',
            'dongho' => '624170300057',
            'ten' => 'D80 Cầu Sơn Trắng (Cái Sao)',
            'lat' => 10.3585,
            'lng' => 105.4625,
        ],
        [
            'customer_id' => 'DE19C742-3724-F111-9AC5-B083FEDEBCF7',
            'logger' => '026023120032',
            'dongho' => '23-H-300039',
            'ten' => 'Logger D40 - Đầu hẻm gần cống số 1, nhánh PV Cái Sao',
            'lat' => 10.3560,
            'lng' => 105.4655,
        ],
        [
            'customer_id' => '48BC67DF-DE8E-F011-9AB5-B083FEDEBCF7',
            'logger' => '026023120050',
            'dongho' => '23G300059',
            'ten' => 'D100 (lưu động) PV Cái Sao',
            'lat' => 10.3540,
            'lng' => 105.4685,
        ],
    ],
];
