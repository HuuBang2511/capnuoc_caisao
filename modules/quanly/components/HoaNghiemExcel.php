<?php
/**
 * HoaNghiemExcel.php
 * Xuất báo cáo hoá nghiệm hoàn chỉnh 100% tất cả các cột của Web
 * Đặt tại: modules\quanly\components\HoaNghiemExcel.php
 */

namespace app\modules\quanly\components;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class HoaNghiemExcel
{
    private $ngay;
    private $chatLuong; 
    private $clnNgay;   

    // QCVN 01-1:2018/BYT
    const QCVN_PH_MIN  = 6.5;
    const QCVN_PH_MAX  = 8.5;
    const QCVN_NTU_MAX = 2.0;
    const QCVN_CLO_MIN = 0.2;
    const QCVN_CLO_MAX = 1.0;

    const COLOR_HEADER_DARK  = 'FF1E3A5F'; 
    const COLOR_HEADER_MID   = 'FF2E6DA4'; 
    const COLOR_HEADER_LIGHT = 'FFD9E8F5'; 
    const COLOR_FAIL         = 'FFFFCCCC'; 

    public function __construct($ngay, $chatLuong, $clnNgay)
    {
        $this->ngay      = $ngay;
        $this->chatLuong = $chatLuong ? $chatLuong : array();
        $this->clnNgay   = $clnNgay;
    }

    public function download()
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setTitle('Hoá Nghiệm ' . $this->ngay)
            ->setCreator('WebGIS Cấp Nước Hồ Cầu Mới');

        $this->buildSheet1($spreadsheet);
        $this->buildSheet2($spreadsheet);

        $writer   = new Xlsx($spreadsheet);
        $filename = 'hoa_nghiem_' . str_replace('-', '', $this->ngay) . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    private function buildSheet1($spreadsheet)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Theo Giờ');
        $ngayFmt = date('d/m/Y', strtotime($this->ngay));

        // Thiết kế khung trục từ cột A -> Cột AH (34 cột dữ liệu toàn diện)
        $sheet->mergeCells('A1:AH1');
        $sheet->setCellValue('A1', 'CÔNG TY CP CẤP NƯỚC HỒ CẦU MỚI');
        $this->styleTitle($sheet, 'A1:AH1', 12, true);

        $sheet->mergeCells('A2:AH2');
        $sheet->setCellValue('A2', 'NHẬT KÝ PHÂN TÍCH HOÁ NGHIỆM THEO GIỜ');
        $this->styleTitle($sheet, 'A2:AH2', 13, true, self::COLOR_HEADER_DARK);

        $sheet->mergeCells('A3:AH3');
        $sheet->setCellValue('A3', 'Ngày: ' . $ngayFmt);
        $this->styleTitle($sheet, 'A3:AH3', 11, false);

        $row = 5;
        $sheet->setCellValue('A' . $row, 'STT');
        $sheet->mergeCells('B' . $row . ':B' . ($row + 1));
        $sheet->setCellValue('B' . $row, 'Thời gian');
        $sheet->mergeCells('C' . $row . ':C' . ($row + 1));
        $sheet->setCellValue('C' . $row, 'Ca');

        $sheet->mergeCells('D' . $row . ':E' . $row);
        $sheet->setCellValue('D' . $row, 'NƯỚC SẠCH (NS)');

        $sheet->mergeCells('F' . $row . ':G' . $row);
        $sheet->setCellValue('F' . $row, 'NƯỚC THÔ (NT)');

        $sheet->mergeCells('H' . $row . ':I' . $row);
        $sheet->setCellValue('H' . $row, 'NƯỚC LẮNG 1');

        $sheet->mergeCells('J' . $row . ':K' . $row);
        $sheet->setCellValue('J' . $row, 'NƯỚC LẮNG 2');

        $sheet->mergeCells('L' . $row . ':L' . ($row + 1));
        $sheet->setCellValue('L' . $row, "Clor dư\nTB/PS");

        // Nhóm châm hoá chất
        $sheet->mergeCells('M' . $row . ':P' . $row);
        $sheet->setCellValue('M' . $row, 'CLO / PAC CHÂM');

        $sheet->mergeCells('Q' . $row . ':R' . $row);
        $sheet->setCellValue('Q' . $row, 'ĐỘ MÀU');

        $sheet->mergeCells('S' . $row . ':T' . $row);
        $sheet->setCellValue('S' . $row, 'ĐỘ KIỀM');

        $sheet->mergeCells('U' . $row . ':V' . $row);
        $sheet->setCellValue('U' . $row, 'ĐỘ CỨNG');

        $sheet->mergeCells('W' . $row . ':X' . $row);
        $sheet->setCellValue('W' . $row, 'CLORUA');

        $sheet->mergeCells('Y' . $row . ':Z' . $row);
        $sheet->setCellValue('Y' . $row, 'NGOÀI HỒ');

        $sheet->mergeCells('AA' . $row . ':AF' . $row);
        $sheet->setCellValue('AA' . $row, 'MƯƠNG / BỂ (殘餘氯)');

        $sheet->mergeCells('AG' . $row . ':AG' . ($row + 1));
        $sheet->setCellValue('AG' . $row, "PAC Pha\nTỷ Trọng");

        $sheet->mergeCells('AH' . $row . ':AH' . ($row + 1));
        $sheet->setCellValue('AH' . $row, 'Ghi chú');

        $row2 = $row + 1;
        $sheet->setCellValue('D' . $row2, 'pH'); $sheet->setCellValue('E' . $row2, 'NTU');
        $sheet->setCellValue('F' . $row2, 'pH'); $sheet->setCellValue('G' . $row2, 'NTU');
        $sheet->setCellValue('H' . $row2, 'pH'); $sheet->setCellValue('I' . $row2, 'NTU (<5)');
        $sheet->setCellValue('J' . $row2, 'pH'); $sheet->setCellValue('K' . $row2, 'NTU (<5)');
        $sheet->setCellValue('M' . $row2, 'NC Nồng độ'); $sheet->setCellValue('N' . $row2, 'NT Nồng độ');
        $sheet->setCellValue('O' . $row2, 'Clo châm'); $sheet->setCellValue('P' . $row2, 'PAC châm');
        $sheet->setCellValue('Q' . $row2, 'NT RW'); $sheet->setCellValue('R' . $row2, 'NS (<15)');
        $sheet->setCellValue('S' . $row2, 'NS'); $sheet->setCellValue('T' . $row2, 'NT');
        $sheet->setCellValue('U' . $row2, 'NS (<300)'); $sheet->setCellValue('V' . $row2, 'NT');
        $sheet->setCellValue('W' . $row2, 'NS (<250)'); $sheet->setCellValue('X' . $row2, 'NT');
        $sheet->setCellValue('Y' . $row2, 'pH'); $sheet->setCellValue('Z' . $row2, 'NTU');
        
        $sheet->setCellValue('AA' . $row2, 'Mương PƯ'); $sheet->setCellValue('AB' . $row2, 'Lắng NL1');
        $sheet->setCellValue('AC' . $row2, 'Mương NS'); $sheet->setCellValue('AD' . $row2, 'Đầu bể NS');
        $sheet->setCellValue('AE' . $row2, 'Xi phông 1'); $sheet->setCellValue('AF' . $row2, 'Xi phông 2');

        $this->styleHeaderGroup($sheet, 'D' . $row . ':E' . $row, self::COLOR_HEADER_MID);
        $this->styleHeaderGroup($sheet, 'F' . $row . ':G' . $row, self::COLOR_HEADER_MID);
        $this->styleHeaderGroup($sheet, 'H' . $row . ':I' . $row, self::COLOR_HEADER_MID);
        $this->styleHeaderGroup($sheet, 'J' . $row . ':K' . $row, self::COLOR_HEADER_MID);
        $this->styleHeaderGroup($sheet, 'M' . $row . ':P' . $row, self::COLOR_HEADER_MID);
        $this->styleHeaderGroup($sheet, 'Q' . $row . ':AF' . $row, self::COLOR_HEADER_MID);
        $this->styleHeaderGroup($sheet, 'A' . $row . ':AH' . $row2, self::COLOR_HEADER_LIGHT);

        $sheet->mergeCells('A' . $row . ':A' . $row2);

        // ===== TIẾN HÀNH ĐỔ DỮ LIỆU =====
        $dataRow  = $row2 + 1;
        $stt      = 1;
        foreach ($this->chatLuong as $rec) {
            $sheet->setCellValue('A' . $dataRow, $stt);
            $sheet->setCellValue('B' . $dataRow, date('H:i', strtotime($rec->thoi_gian)));
            $sheet->setCellValue('C' . $dataRow, ($rec->ca == 1) ? 'Ngày' : 'Đêm');
            
            $sheet->setCellValue('D' . $dataRow, $this->val($rec->ns_ph));
            $sheet->setCellValue('E' . $dataRow, $this->val($rec->ns_ntu));
            $sheet->setCellValue('F' . $dataRow, $this->val($rec->nt_ph));
            $sheet->setCellValue('G' . $dataRow, $this->val($rec->nt_ntu));
            $sheet->setCellValue('H' . $dataRow, $this->val($rec->nl1_ph));
            $sheet->setCellValue('I' . $dataRow, $this->val($rec->nl1_ntu));
            $sheet->setCellValue('J' . $dataRow, $this->val($rec->nl2_ph));
            $sheet->setCellValue('K' . $dataRow, $this->val($rec->nl2_ntu));
            $sheet->setCellValue('L' . $dataRow, $this->val($rec->clo_du));
            
            $sheet->setCellValue('M' . $dataRow, $this->val($rec->ns_clo_nong_do));
            $sheet->setCellValue('N' . $dataRow, $this->val($rec->nt_clo_nong_do));
            $sheet->setCellValue('O' . $dataRow, $this->val($rec->nc_clo_cham));
            $sheet->setCellValue('P' . $dataRow, $this->val($rec->pac_cham));
            
            $sheet->setCellValue('Q' . $dataRow, $this->val($rec->nt_do_mau));
            $sheet->setCellValue('R' . $dataRow, $this->val($rec->ns_do_mau));
            
            $sheet->setCellValue('S' . $dataRow, $this->val($rec->ns_do_kiem));
            $sheet->setCellValue('T' . $dataRow, $this->val($rec->nt_do_kiem));
            $sheet->setCellValue('U' . $dataRow, $this->val($rec->ns_do_cung));
            $sheet->setCellValue('V' . $dataRow, $this->val($rec->nt_do_cung));
            $sheet->setCellValue('W' . $dataRow, $this->val($rec->ns_clorua));
            $sheet->setCellValue('X' . $dataRow, $this->val($rec->nt_clorua));
            
            $sheet->setCellValue('Y' . $dataRow, $this->val($rec->ngoai_ho_ph));
            $sheet->setCellValue('Z' . $dataRow, $this->val($rec->ngoai_ho_ntu));
            
            $sheet->setCellValue('AA' . $dataRow, $this->val($rec->muong_pu_thu_hoi));
            $sheet->setCellValue('AB' . $dataRow, $this->val($rec->muong_lang_nl1));
            $sheet->setCellValue('AC' . $dataRow, $this->val($rec->muong_pu_ns));
            $sheet->setCellValue('AD' . $dataRow, $this->val($rec->dau_be_ns));
            $sheet->setCellValue('AE' . $dataRow, $this->val($rec->ho_xi_phong_1_ntu));
            $sheet->setCellValue('AF' . $dataRow, $this->val($rec->ho_xi_phong_2_ntu));
            
            $sheet->setCellValue('AG' . $dataRow, $this->val($rec->pac_ty_trong));
            $sheet->setCellValue('AH' . $dataRow, isset($rec->ghi_chu) ? $rec->ghi_chu : '');

            // Highlight đỏ QCVN
            $this->highlightQcvn($sheet, $rec->ns_ph,  'D' . $dataRow, self::QCVN_PH_MIN,  self::QCVN_PH_MAX);
            $this->highlightQcvn($sheet, $rec->ns_ntu, 'E' . $dataRow, null,               self::QCVN_NTU_MAX);
            $this->highlightQcvn($sheet, $rec->clo_du, 'L' . $dataRow, self::QCVN_CLO_MIN, self::QCVN_CLO_MAX);
            $this->highlightQcvn($sheet, $rec->nl1_ntu, 'I' . $dataRow, null, 5.0);
            $this->highlightQcvn($sheet, $rec->nl2_ntu, 'K' . $dataRow, null, 5.0);
            $this->highlightQcvn($sheet, $rec->ns_do_mau, 'R' . $dataRow, null, 15.0);
            $this->highlightQcvn($sheet, $rec->ns_do_cung, 'U' . $dataRow, null, 300.0);
            $this->highlightQcvn($sheet, $rec->ns_clorua,  'W' . $dataRow, null, 250.0);

            $this->styleDataRow($sheet, 'A' . $dataRow . ':AH' . $dataRow, ($stt % 2 == 0));
            $stt++; $dataRow++;
        }

        $tableRange = 'A' . ($row) . ':AH' . ($dataRow - 1);
        $this->applyBorderAll($sheet, $tableRange);
        $sheet->freezePane('A' . ($row2 + 1));
    }

    private function buildSheet2($spreadsheet)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Tổng Hợp Ngày');
        $ngayFmt = date('d/m/Y', strtotime($this->ngay));

        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'TỔNG HỢP CHẤT LƯỢNG NƯỚC & BẢNG TÍNH CLO — ' . $ngayFmt);
        $this->styleTitle($sheet, 'A1:G1', 13, true, self::COLOR_HEADER_DARK);

        // Đổ dữ liệu Bảng tính toán lượng Clo từ Data của Ca hiện tại xuống cuối Excel
        $row = 3;
        $sheet->mergeCells('A' . $row . ':G' . $row);
        $sheet->setCellValue('A' . $row, 'BẢNG THEO DÕI NỒNG ĐỘ CLO TIÊU THỤ THEO CA');
        $this->styleHeaderGroup($sheet, 'A' . $row . ':G' . $row, self::COLOR_HEADER_MID);

        $row++;
        $sheet->setCellValue('A' . $row, 'Thông số cấu phần');
        $sheet->setCellValue('B' . $row, 'Giá trị nhập');
        $sheet->setCellValue('C' . $row, 'Đơn vị');
        $sheet->setCellValue('D' . $row, 'Kết quả tính toán');
        $sheet->setCellValue('E' . $row, 'Hàm số áp dụng');
        $this->styleHeaderGroup($sheet, 'A' . $row . ':E' . $row, self::COLOR_HEADER_LIGHT);

        // Lấy data dòng đầu làm mẫu cho bảng tính clo
        $rec = isset($this->chatLuong[0]) ? $this->chatLuong[0] : null;
        $mBD = $rec ? (float)$rec->clo_mat_ban_dau : 0.6;
        $mTB = $rec ? (float)$rec->clo_mat_trong_be : 0.1;
        $kLC = $rec ? (float)$rec->clo_khoi_luong_cham : 3.0;
        $lNT = $rec ? (float)$rec->clo_ll_nuoc_tho : 4500.0;

        // Tính Clo dư BQ từ mảng
        $sumC = 0; $cntC = 0;
        foreach($this->chatLuong as $_r) {
            if($_r->clo_du !== null) { $sumC += $_r->clo_du; $cntC++; }
        }
        $cloDuBq = $cntC > 0 ? ($sumC / $cntC) : 0.0;

        $cloNuocTho = $lNT > 0 ? (($kLC / $lNT) * 1000) : 0.0;
        $cloChamNc  = $cloDuBq + $mBD + $mTB;

        $rowsClo = [
            ['Lượng clo mất đi ban đầu', $mBD, 'mg/L', '—', 'Hằng số thực địa'],
            ['Lượng clo mất trong bể', $mTB, 'mg/L', '—', 'Hằng số thực địa'],
            ['Khối lượng Châm Clo', $kLC, 'kg/h', '—', 'Đồng hồ châm thực tế'],
            ['Lưu lượng Nước Thô', $lNT, 'm³/h', '—', 'Thiết bị lưu lượng SCADA'],
            ['Lượng Clo dư bình quân ca', round($cloDuBq,2), 'mg/L', '—', 'Tự động tính từ lưới dữ liệu'],
            ['Nước thô Nồng độ clo', '—', 'mg/L', round($cloNuocTho,2), '(Khối lượng châm / LL nước thô) * 1000'],
            ['Nồng độ clo châm nước cấp', '—', 'mg/L', round($cloChamNc,2), 'Clo dư BQ + Mất ban đầu + Mất trong bể'],
        ];

        foreach ($rowsClo as $rC) {
            $row++;
            $sheet->setCellValue('A' . $row, $rC[0]);
            $sheet->setCellValue('B' . $row, $rC[1]);
            $sheet->setCellValue('C' . $row, $rC[2]);
            $sheet->setCellValue('D' . $row, $rC[3]);
            $sheet->setCellValue('E' . $row, $rC[4]);
            $this->styleDataRow($sheet, 'A' . $row . ':E' . $row, false);
            if(strpos($rC[0], 'Nồng độ') !== false || strpos($rC[0], 'Nước thô') !== false) {
                $sheet->getStyle('D' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE6F4EA');
                $sheet->getStyle('D' . $row)->getFont()->setBold(true);
            }
        }
        $this->applyBorderAll($sheet, 'A5:E' . $row);

        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(40);
    }

    private function val($v) { return ($v !== null && $v !== '') ? $v : ''; }

    private function toArr($v)
    {
        if ($v === null || $v === '') return array();
        if (is_array($v)) return $v;
        if (is_object($v)) return (array)$v;
        $decoded = json_decode($v, true);
        return is_array($decoded) ? $decoded : array();
    }

    private function styleTitle($sheet, $range, $size, $bold, $bgColor = null)
    {
        $style = $sheet->getStyle($range);
        $style->getFont()->setSize($size)->setBold($bold)->setName('Arial');
        $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        if ($bgColor) {
            $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($bgColor);
            $style->getFont()->getColor()->setARGB('FFFFFFFF');
        }
    }

    private function styleHeaderGroup($sheet, $range, $bgColor)
    {
        $style = $sheet->getStyle($range);
        $style->getFont()->setBold(true)->setSize(10)->setName('Arial');
        $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
        $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($bgColor);
        if ($bgColor !== self::COLOR_HEADER_LIGHT) { $style->getFont()->getColor()->setARGB('FFFFFFFF'); }
    }

    private function styleDataRow($sheet, $range, $alt)
    {
        $style = $sheet->getStyle($range);
        $style->getFont()->setName('Arial')->setSize(10);
        $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        if ($alt) { $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF5F9FF'); }
    }

    private function applyBorderAll($sheet, $range)
    {
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    }

    private function highlightQcvn($sheet, $val, $cell, $min, $max)
    {
        if ($val === null || $val === '') return;
        $fail = false;
        if ($min !== null && $val < $min) $fail = true;
        if ($max !== null && $val > $max) $fail = true;
        if ($fail) {
            $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(self::COLOR_FAIL);
            $sheet->getStyle($cell)->getFont()->getColor()->setARGB('FF990000');
        }
    }
}