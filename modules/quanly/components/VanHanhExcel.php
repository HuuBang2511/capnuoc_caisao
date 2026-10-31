<?php
/**
 * VanHanhExcel.php
 * Xuất báo cáo vận hành (sổ giao ca ngày + đêm + dữ liệu SCADA)
 * Đặt tại: modules\quanly\components\VanHanhExcel.php
 */

namespace app\modules\quanly\components;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class VanHanhExcel
{
    private $ngay;
    private $giaoCa;    // array of NkGiaoCa (ca 1 + ca 2)
    private $scadaData; // array|null — dữ liệu SCADA ngày (từ iot_api)

    const COLOR_HEADER_DARK  = 'FF1E3A5F';
    const COLOR_HEADER_MID   = 'FF2E6DA4';
    const COLOR_HEADER_LIGHT = 'FFD9E8F5';
    const COLOR_CA_NGAY      = 'FFFFF8E1'; // vàng nhạt — ca ngày
    const COLOR_CA_DEM       = 'FFE8EAF6'; // tím nhạt  — ca đêm

    public function __construct($ngay, $giaoCa, $scadaData = null)
    {
        $this->ngay     = $ngay;
        $this->giaoCa   = $giaoCa   ? $giaoCa   : array();
        $this->scadaData = $scadaData ? $scadaData : array();
    }

    public function download()
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setTitle('Vận Hành ' . $this->ngay)
            ->setCreator('WebGIS Cấp Nước Hồ Cầu Mới');

        $this->buildSheetGiaoCa($spreadsheet);
        $this->buildSheetSanLuong($spreadsheet);

        $writer   = new Xlsx($spreadsheet);
        $filename = 'van_hanh_' . str_replace('-', '', $this->ngay) . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    /** Sheet 1 — Sổ giao ca */
    private function buildSheetGiaoCa($spreadsheet)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Giao Ca');

        $ngayFmt = date('d/m/Y', strtotime($this->ngay));

        // Tiêu đề
        $sheet->mergeCells('A1:L1');
        $sheet->setCellValue('A1', 'CÔNG TY CP CẤP NƯỚC HỒ CẦU MỚI');
        $this->styleTitle($sheet, 'A1', 12, true);

        $sheet->mergeCells('A2:L2');
        $sheet->setCellValue('A2', 'SỔ GIAO CA VẬN HÀNH — Ngày ' . $ngayFmt);
        $this->styleTitle($sheet, 'A2', 13, true, self::COLOR_HEADER_DARK);

        // Lấy ca ngày và ca đêm
        $caNgay = null;
        $caDem  = null;
        foreach ($this->giaoCa as $gc) {
            if ($gc->ca == 1) $caNgay = $gc;
            if ($gc->ca == 2) $caDem  = $gc;
        }

        $row = 4;
        $row = $this->renderCaBlock($sheet, $row, $caNgay, 1, $ngayFmt);
        $row += 1;
        $row = $this->renderCaBlock($sheet, $row, $caDem, 2, $ngayFmt);

        // Ký tên cuối trang
        $row += 2;
        $sheet->setCellValue('A' . $row, 'Người giao ca');
        $sheet->setCellValue('E' . $row, 'Người nhận ca');
        $sheet->setCellValue('I' . $row, 'Người lập báo cáo');
        foreach (array('A', 'E', 'I') as $c) {
            $sheet->getStyle($c . $row)->getFont()->setBold(true);
            $sheet->getStyle($c . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Column widths
        $wArr = array('A' => 22, 'B' => 14, 'C' => 14, 'D' => 14, 'E' => 14, 'F' => 14,
                      'G' => 14, 'H' => 14, 'I' => 14, 'J' => 14, 'K' => 14, 'L' => 22);
        foreach ($wArr as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }
    }

    /** Render block thông tin 1 ca */
    private function renderCaBlock($sheet, $startRow, $gc, $caNum, $ngayFmt)
    {
        $caLabel = ($caNum == 1) ? 'CA NGÀY' : 'CA ĐÊM';
        $caColor = ($caNum == 1) ? self::COLOR_CA_NGAY : self::COLOR_CA_DEM;

        // Header ca
        $sheet->mergeCells('A' . $startRow . ':L' . $startRow);
        $sheet->setCellValue('A' . $startRow, $caLabel . ' — ' . $ngayFmt);
        $this->styleHeaderGroup($sheet, 'A' . $startRow . ':L' . $startRow, self::COLOR_HEADER_MID);

        $row = $startRow + 1;

        // Nhân sự
        $sheet->setCellValue('A' . $row, 'Người giao:');
        $sheet->setCellValue('B' . $row, $gc ? $this->val($gc->nhan_vien_giao) : '');
        $sheet->setCellValue('E' . $row, 'Người nhận:');
        $sheet->setCellValue('F' . $row, $gc ? $this->val($gc->nhan_vien_nhan) : '');
        $sheet->setCellValue('I' . $row, 'Người nhập:');
        $sheet->setCellValue('J' . $row, $gc ? $this->val($gc->nguoi_nhap) : '');
        $this->styleLabelValueRow($sheet, $row);
        $row++;

        // ===== Sản lượng =====
        $sheet->mergeCells('A' . $row . ':L' . $row);
        $sheet->setCellValue('A' . $row, 'I. SẢN LƯỢNG NƯỚC');
        $this->styleSubHeader($sheet, 'A' . $row . ':L' . $row, $caColor);
        $row++;

        // Header bảng SL
        $slHeaders = array(
            'A' => 'Hạng mục', 'B' => 'Đồng hồ đầu ca', 'C' => 'Đồng hồ cuối ca',
            'D' => 'Sản lượng (m³)',
        );
        foreach ($slHeaders as $col => $label) {
            $sheet->setCellValue($col . $row, $label);
        }
        $this->styleHeaderGroup($sheet, 'A' . $row . ':D' . $row, self::COLOR_HEADER_LIGHT);
        $row++;

        $slItems = array(
            array('Nước sạch cấp ra',  $gc ? $gc->nuoc_cap_dau  : '', $gc ? $gc->nuoc_cap_cuoi  : ''),
            array('Nước thô bơm lên',  $gc ? $gc->nuoc_tho_dau  : '', $gc ? $gc->nuoc_tho_cuoi  : ''),
        );
        foreach ($slItems as $item) {
            $sheet->setCellValue('A' . $row, $item[0]);
            $sheet->setCellValue('B' . $row, $this->val($item[1]));
            $sheet->setCellValue('C' . $row, $this->val($item[2]));
            // Công thức tính sản lượng
            $dau  = 'B' . $row;
            $cuoi = 'C' . $row;
            if ($this->val($item[1]) !== '' && $this->val($item[2]) !== '') {
                $sheet->setCellValue('D' . $row, '=IF(AND(' . $cuoi . '<>"", ' . $dau . '<>""), ' . $cuoi . '-' . $dau . ', "")');
            } else {
                $sheet->setCellValue('D' . $row, '');
            }
            $this->styleDataRow($sheet, 'A' . $row . ':D' . $row, false);
            $this->applyBorderAll($sheet, 'A' . $row . ':D' . $row);
            $row++;
        }

        // ===== Điện năng =====
        $sheet->mergeCells('A' . $row . ':L' . $row);
        $sheet->setCellValue('A' . $row, 'II. ĐIỆN NĂNG TIÊU THỤ');
        $this->styleSubHeader($sheet, 'A' . $row . ':L' . $row, $caColor);
        $row++;

        $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true);
        $sheet->setCellValue('A' . $row, 'Hạng mục');
        $sheet->setCellValue('B' . $row, 'Đồng hồ đầu ca');
        $sheet->setCellValue('C' . $row, 'Đồng hồ cuối ca');
        $sheet->setCellValue('D' . $row, 'Tiêu thụ (kWh)');
        $this->styleHeaderGroup($sheet, 'A' . $row . ':D' . $row, self::COLOR_HEADER_LIGHT);
        $row++;

        $dienItems = array(
            array('Nhà máy xử lý',   $gc ? $gc->dien_nha_may_dau   : '', $gc ? $gc->dien_nha_may_cuoi   : ''),
            array('Trạm bơm',        $gc ? $gc->dien_tram_bom_dau  : '', $gc ? $gc->dien_tram_bom_cuoi  : ''),
            array('NT5 tăng áp',     $gc ? $gc->dien_nt5_tang_ap_dau : '', $gc ? $gc->dien_nt5_tang_ap_cuoi : ''),
        );
        foreach ($dienItems as $item) {
            $sheet->setCellValue('A' . $row, $item[0]);
            $sheet->setCellValue('B' . $row, $this->val($item[1]));
            $sheet->setCellValue('C' . $row, $this->val($item[2]));
            $dau  = 'B' . $row;
            $cuoi = 'C' . $row;
            if ($this->val($item[1]) !== '' && $this->val($item[2]) !== '') {
                $sheet->setCellValue('D' . $row, '=IF(AND(' . $cuoi . '<>"", ' . $dau . '<>""), ' . $cuoi . '-' . $dau . ', "")');
            } else {
                $sheet->setCellValue('D' . $row, '');
            }
            $this->styleDataRow($sheet, 'A' . $row . ':D' . $row, false);
            $this->applyBorderAll($sheet, 'A' . $row . ':D' . $row);
            $row++;
        }

        // ===== Hoá chất =====
        $sheet->mergeCells('A' . $row . ':L' . $row);
        $sheet->setCellValue('A' . $row, 'III. HOÁ CHẤT SỬ DỤNG');
        $this->styleSubHeader($sheet, 'A' . $row . ':L' . $row, $caColor);
        $row++;

        $hcItems = array(
            array('PAC (kg)',        $gc ? $this->val($gc->pac_kg)      : ''),
            array('Chlorine (kg)',   $gc ? $this->val($gc->chlorine_kg) : ''),
            array('Polymer (kg)',    $gc ? $this->val($gc->polymer_kg)  : ''),
        );
        $sheet->setCellValue('A' . $row, 'Hoá chất');
        $sheet->setCellValue('B' . $row, 'Lượng dùng');
        $this->styleHeaderGroup($sheet, 'A' . $row . ':B' . $row, self::COLOR_HEADER_LIGHT);
        $row++;
        foreach ($hcItems as $item) {
            $sheet->setCellValue('A' . $row, $item[0]);
            $sheet->setCellValue('B' . $row, $item[1]);
            $this->styleDataRow($sheet, 'A' . $row . ':B' . $row, false);
            $this->applyBorderAll($sheet, 'A' . $row . ':B' . $row);
            $row++;
        }

        // ===== Chất lượng nước đầu/cuối ca =====
        $sheet->mergeCells('A' . $row . ':L' . $row);
        $sheet->setCellValue('A' . $row, 'IV. CHẤT LƯỢNG NƯỚC ĐẦU / CUỐI CA');
        $this->styleSubHeader($sheet, 'A' . $row . ':L' . $row, $caColor);
        $row++;

        $sheet->setCellValue('A' . $row, 'Chỉ tiêu');
        $sheet->setCellValue('B' . $row, 'Đầu ca');
        $sheet->setCellValue('C' . $row, 'Cuối ca');
        $sheet->setCellValue('D' . $row, 'QCVN 01-1:2018');
        $this->styleHeaderGroup($sheet, 'A' . $row . ':D' . $row, self::COLOR_HEADER_LIGHT);
        $row++;

        $clnItems = array(
            array('pH',            $gc ? $this->val($gc->ns_ph_dau)  : '', $gc ? $this->val($gc->ns_ph_cuoi)  : '', '6.5 – 8.5'),
            array('NTU',           $gc ? $this->val($gc->ns_ntu_dau) : '', $gc ? $this->val($gc->ns_ntu_cuoi) : '', '< 2.0'),
            array('Clo dư (mg/L)', $gc ? $this->val($gc->clo_du_dau) : '', $gc ? $this->val($gc->clo_du_cuoi) : '', '0.2 – 1.0'),
        );
        foreach ($clnItems as $idx => $item) {
            $sheet->setCellValue('A' . $row, $item[0]);
            $sheet->setCellValue('B' . $row, $item[1]);
            $sheet->setCellValue('C' . $row, $item[2]);
            $sheet->setCellValue('D' . $row, $item[3]);
            $this->styleDataRow($sheet, 'A' . $row . ':D' . $row, ($idx % 2 == 1));
            $this->applyBorderAll($sheet, 'A' . $row . ':D' . $row);
            $row++;
        }

        // ===== Thiết bị vận hành =====
        $sheet->mergeCells('A' . $row . ':L' . $row);
        $sheet->setCellValue('A' . $row, 'V. THIẾT BỊ VẬN HÀNH');
        $this->styleSubHeader($sheet, 'A' . $row . ':L' . $row, $caColor);
        $row++;

        $tbItems = array(
            array('Bơm NT chạy',  $gc ? $this->val($gc->bom_nt_chay)  : ''),
            array('Bơm TH chạy',  $gc ? $this->val($gc->bom_th_chay)  : ''),
            array('Bơm khí chạy', $gc ? $this->val($gc->bom_khi_chay) : ''),
        );
        $sheet->setCellValue('A' . $row, 'Thiết bị');
        $sheet->setCellValue('B' . $row, 'Trạng thái / Số lượng');
        $this->styleHeaderGroup($sheet, 'A' . $row . ':B' . $row, self::COLOR_HEADER_LIGHT);
        $row++;
        foreach ($tbItems as $item) {
            $sheet->setCellValue('A' . $row, $item[0]);
            $sheet->setCellValue('B' . $row, $item[1]);
            $this->styleDataRow($sheet, 'A' . $row . ':B' . $row, false);
            $this->applyBorderAll($sheet, 'A' . $row . ':B' . $row);
            $row++;
        }

        // ===== Sự cố =====
        $sheet->mergeCells('A' . $row . ':L' . $row);
        $sheet->setCellValue('A' . $row, 'VI. SỰ CỐ & BIỆN PHÁP XỬ LÝ');
        $this->styleSubHeader($sheet, 'A' . $row . ':L' . $row, $caColor);
        $row++;

        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->mergeCells('G' . $row . ':L' . $row);
        $sheet->setCellValue('A' . $row, 'Sự cố: ' . ($gc ? $this->val($gc->su_co) : ''));
        $sheet->setCellValue('G' . $row, 'Biện pháp: ' . ($gc ? $this->val($gc->bien_phap) : ''));
        $sheet->getStyle('A' . $row . ':L' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(40);
        $row++;

        $sheet->mergeCells('A' . $row . ':L' . $row);
        $sheet->setCellValue('A' . $row, 'Ghi chú: ' . ($gc ? $this->val($gc->ghi_chu) : ''));
        $sheet->getStyle('A' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(30);
        $row++;

        return $row;
    }

    /** Sheet 2 — Sản lượng SCADA tổng hợp */
    private function buildSheetSanLuong($spreadsheet)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Sản Lượng SCADA');

        $ngayFmt = date('d/m/Y', strtotime($this->ngay));

        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'TỔNG HỢP SẢN LƯỢNG SCADA — ' . $ngayFmt);
        $this->styleTitle($sheet, 'A1', 13, true, self::COLOR_HEADER_DARK);

        $row = 3;
        $headers = array('A' => 'Hạng mục', 'B' => 'Giá trị', 'C' => 'Đơn vị', 'D' => 'Ghi chú');
        foreach ($headers as $col => $label) {
            $sheet->setCellValue($col . $row, $label);
        }
        $this->styleHeaderGroup($sheet, 'A' . $row . ':D' . $row, self::COLOR_HEADER_LIGHT);
        $row++;

        if (!empty($this->scadaData)) {
            $nuocSach = isset($this->scadaData['nuoc_sach']) ? $this->scadaData['nuoc_sach'] : '';
            $nuocTho  = isset($this->scadaData['nuoc_tho'])  ? $this->scadaData['nuoc_tho']  : '';
            $nuocKh   = isset($this->scadaData['nuoc_kh'])   ? $this->scadaData['nuoc_kh']   : '';
            $thatThoat = isset($this->scadaData['that_thoat']) ? $this->scadaData['that_thoat'] : '';
            $tyLe      = isset($this->scadaData['ty_le_that_thoat']) ? $this->scadaData['ty_le_that_thoat'] : '';

            $scadaItems = array(
                array('Nước sạch cấp ra (SCADA)', $nuocSach, 'm³', 'Index Logger 60000_05'),
                array('Nước thô bơm lên (SCADA)', $nuocTho,  'm³', 'Index Logger 60100_03'),
                array('Tổng sản lượng khách hàng', $nuocKh,  'm³', '20 channels _02'),
                array('Thất thoát', $thatThoat, 'm³', 'Nước sạch - Khách hàng'),
                array('Tỷ lệ thất thoát', $tyLe, '%', ''),
            );
        } else {
            $scadaItems = array(
                array('Nước sạch cấp ra (SCADA)', '', 'm³', 'Index Logger 60000_05'),
                array('Nước thô bơm lên (SCADA)', '', 'm³', 'Index Logger 60100_03'),
                array('Tổng sản lượng khách hàng', '', 'm³', '20 channels _02'),
                array('Thất thoát', '', 'm³', 'Nước sạch - Khách hàng'),
                array('Tỷ lệ thất thoát', '', '%', ''),
            );
        }

        foreach ($scadaItems as $idx => $item) {
            $sheet->setCellValue('A' . $row, $item[0]);
            $sheet->setCellValue('B' . $row, $this->val($item[1]));
            $sheet->setCellValue('C' . $row, $item[2]);
            $sheet->setCellValue('D' . $row, $item[3]);
            $this->styleDataRow($sheet, 'A' . $row . ':D' . $row, ($idx % 2 == 1));
            $this->applyBorderAll($sheet, 'A' . $row . ':D' . $row);
            $row++;
        }

        $sheet->getStyle('B4:B' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');

        // Note chốt giờ
        $row++;
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $sheet->setCellValue('A' . $row, '* Dữ liệu SCADA chốt lúc 5:00 sáng. Lệch ~1h so với số liệu khách hàng (chốt 6:00) là bình thường.');
        $sheet->getStyle('A' . $row)->getFont()->setItalic(true)->setSize(9);

        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(16);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(30);
    }

    // ===== HELPERS =====

    private function val($v)
    {
        return ($v !== null && $v !== '') ? $v : '';
    }

    private function styleTitle($sheet, $cell, $size, $bold, $bgColor = null)
    {
        $style = $sheet->getStyle($cell);
        $style->getFont()->setSize($size)->setBold($bold)->setName('Arial');
        $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        if ($bgColor) {
            $style->getFill()->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB($bgColor);
            $style->getFont()->getColor()->setARGB('FFFFFFFF');
        }
    }

    private function styleHeaderGroup($sheet, $range, $bgColor)
    {
        $style = $sheet->getStyle($range);
        $style->getFont()->setBold(true)->setSize(10)->setName('Arial');
        $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
        $style->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB($bgColor);
        if ($bgColor !== self::COLOR_HEADER_LIGHT) {
            $style->getFont()->getColor()->setARGB('FFFFFFFF');
        }
    }

    private function styleSubHeader($sheet, $range, $bgColor)
    {
        $style = $sheet->getStyle($range);
        $style->getFont()->setBold(true)->setSize(10)->setName('Arial');
        $style->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $style->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB($bgColor);
    }

    private function styleLabelValueRow($sheet, $row)
    {
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $sheet->getStyle('E' . $row)->getFont()->setBold(true);
        $sheet->getStyle('I' . $row)->getFont()->setBold(true);
    }

    private function styleDataRow($sheet, $range, $alt)
    {
        $style = $sheet->getStyle($range);
        $style->getFont()->setName('Arial')->setSize(10);
        $style->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        if ($alt) {
            $style->getFill()->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFF5F9FF');
        }
    }

    private function applyBorderAll($sheet, $range)
    {
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    }
}