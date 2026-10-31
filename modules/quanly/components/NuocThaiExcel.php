<?php
/**
 * NuocThaiExcel.php
 * Xuất báo cáo nước thải sinh hoạt — 1 record theo ngày
 * Đặt tại: modules\quanly\components\NuocThaiExcel.php
 */

namespace app\modules\quanly\components;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use app\modules\quanly\models\hocau\NkNuocThaiSh;

class NuocThaiExcel
{
    private $ngay;
    private $nuocThai; // NkNuocThaiSh|null

    const COLOR_HEADER_DARK  = 'FF1E3A5F';
    const COLOR_HEADER_LIGHT = 'FFD9E8F5';
    const COLOR_FAIL         = 'FFFFCCCC';

    public function __construct($ngay, $nuocThai)
    {
        $this->ngay     = $ngay;
        $this->nuocThai = $nuocThai;
    }

    public function download()
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setTitle('Nước Thải SH ' . $this->ngay)
            ->setCreator('WebGIS Cấp Nước Hồ Cầu Mới');

        $this->buildSheet($spreadsheet->getActiveSheet());

        $writer   = new Xlsx($spreadsheet);
        $filename = 'nuoc_thai_sh_' . str_replace('-', '', $this->ngay) . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    private function buildSheet($sheet)
    {
        $sheet->setTitle('Nước Thải SH');
        $ngayFmt = date('d/m/Y', strtotime($this->ngay));
        $qcvn    = NkNuocThaiSh::QCVN;
        $rec     = $this->nuocThai;

        // Tiêu đề
        $sheet->mergeCells('A1:D1');
        $sheet->setCellValue('A1', 'CÔNG TY CP CẤP NƯỚC HỒ CẦU MỚI');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12)->setName('Arial');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:D2');
        $sheet->setCellValue('A2', 'KẾT QUẢ PHÂN TÍCH NƯỚC THẢI SINH HOẠT');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(13)->setName('Arial');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2')->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB(self::COLOR_HEADER_DARK);
        $sheet->getStyle('A2')->getFont()->getColor()->setARGB('FFFFFFFF');

        $sheet->mergeCells('A3:D3');
        $sheet->setCellValue('A3', 'QCVN 14:2008/BTNMT — Cột B    |    Ngày: ' . $ngayFmt);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3')->getFont()->setSize(11)->setName('Arial');

        // Header bảng
        $row = 5;
        $headers = array('A' => 'Chỉ tiêu', 'B' => 'Kết quả', 'C' => 'QCVN cột B', 'D' => 'Đánh giá');
        foreach ($headers as $col => $label) {
            $sheet->setCellValue($col . $row, $label);
        }
        $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true)->setName('Arial');
        $sheet->getStyle('A' . $row . ':D' . $row)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A' . $row . ':D' . $row)->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB(self::COLOR_HEADER_LIGHT);
        $sheet->getRowDimension($row)->setRowHeight(22);

        // Các chỉ tiêu — ngưỡng lấy từ NkNuocThaiSh::QCVN
        $chiTieu = array(
            array('field' => 'ph',       'label' => 'pH',                 'unit' => '',           'qcvn_str' => $qcvn['ph']['min'] . ' – ' . $qcvn['ph']['max']),
            array('field' => 'tss',      'label' => 'TSS',                'unit' => 'mg/L',       'qcvn_str' => '≤ ' . $qcvn['tss']['max']),
            array('field' => 'amoni',    'label' => 'Amoni (NH₄⁺-N)',     'unit' => 'mg/L',       'qcvn_str' => '≤ ' . $qcvn['amoni']['max']),
            array('field' => 'nitrat',   'label' => 'Nitrat (NO₃⁻-N)',    'unit' => 'mg/L',       'qcvn_str' => '≤ ' . $qcvn['nitrat']['max']),
            array('field' => 'coliform', 'label' => 'Coliform',           'unit' => 'MPN/100mL',  'qcvn_str' => '≤ ' . number_format($qcvn['coliform']['max'])),
        );

        $row++;
        foreach ($chiTieu as $idx => $ct) {
            $field = $ct['field'];
            $val   = ($rec !== null && $rec->$field !== null) ? $rec->$field : '';

            $danhGia = '';
            $fail    = false;
            if ($rec !== null && $rec->$field !== null) {
                $q = $qcvn[$field];
                $v = (float)$rec->$field;
                if ($v < $q['min'] || $v > $q['max']) {
                    $fail    = true;
                    $danhGia = 'VƯỢT QCVN';
                } else {
                    $danhGia = 'Đạt';
                }
            }

            $labelFull = $ct['label'];
            if ($ct['unit']) $labelFull .= ' (' . $ct['unit'] . ')';

            $sheet->setCellValue('A' . $row, $labelFull);
            $sheet->setCellValue('B' . $row, $val);
            $sheet->setCellValue('C' . $row, $ct['qcvn_str']);
            $sheet->setCellValue('D' . $row, $danhGia);

            $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setName('Arial')->setSize(11);
            $sheet->getStyle('B' . $row . ':D' . $row)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            if ($fail) {
                $sheet->getStyle('B' . $row . ':D' . $row)->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB(self::COLOR_FAIL);
                $sheet->getStyle('D' . $row)->getFont()->getColor()->setARGB('FF990000');
                $sheet->getStyle('D' . $row)->getFont()->setBold(true);
            } elseif ($idx % 2 == 0) {
                $sheet->getStyle('A' . $row . ':D' . $row)->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF5F9FF');
            }

            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;
        }

        // Border
        $sheet->getStyle('A5:D' . ($row - 1))->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A5:D' . ($row - 1))->getBorders()->getOutline()
            ->setBorderStyle(Border::BORDER_MEDIUM);

        // Thông báo nếu chưa có dữ liệu
        if ($rec === null) {
            $sheet->mergeCells('A' . $row . ':D' . $row);
            $sheet->setCellValue('A' . $row, 'Chưa có dữ liệu nước thải ngày ' . $ngayFmt);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A' . $row)->getFont()->setItalic(true)->setName('Arial');
            $row++;
        }

        // Ghi chú
        $row++;
        $sheet->setCellValue('A' . $row, 'Ghi chú:');
        $sheet->mergeCells('B' . $row . ':D' . $row);
        $sheet->setCellValue('B' . $row, ($rec !== null && $rec->ghi_chu) ? $rec->ghi_chu : '');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setName('Arial');
        $sheet->getRowDimension($row)->setRowHeight(20);

        // Người thực hiện / kiểm tra
        $row += 2;
        $sheet->setCellValue('A' . $row, 'Người thực hiện:');
        $sheet->setCellValue('B' . $row, ($rec !== null && $rec->nguoi_th) ? $rec->nguoi_th : '');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setName('Arial');

        $row++;
        $sheet->setCellValue('A' . $row, 'Người kiểm tra:');
        $sheet->setCellValue('B' . $row, ($rec !== null && $rec->nguoi_kt) ? $rec->nguoi_kt : '');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setName('Arial');

        // Ký tên
        $row += 2;
        $sheet->setCellValue('A' . $row, 'Người lập báo cáo');
        $sheet->setCellValue('D' . $row, 'Trưởng phòng KT');
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true)->setName('Arial');

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(26);
        $sheet->getColumnDimension('B')->setWidth(16);
        $sheet->getColumnDimension('C')->setWidth(16);
        $sheet->getColumnDimension('D')->setWidth(18);
    }
}