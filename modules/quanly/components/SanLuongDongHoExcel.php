<?php
namespace app\modules\quanly\components;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Fill, Border, Alignment};
use Yii;

class SanLuongDongHoExcel
{
    private $tu_ngay;
    private $den_ngay;
    private $rows;
    private $ngay_list;

    const C_HEADER  = 'FF1E3A5F';
    const C_SUBHEAD = 'FF2D6099';
    const C_ODD     = 'FFF0F4F8';
    const C_TONG    = 'FFFEF3C7';
    const C_TB      = 'FFEFF6FF';
    const C_WHITE   = 'FFFFFFFF';

    public function __construct($tu_ngay, $den_ngay, $rows, $ngay_list)
    {
        $this->tu_ngay  = $tu_ngay;
        $this->den_ngay = $den_ngay;
        $this->rows     = $rows     ?: [];
        $this->ngay_list = $ngay_list ?: [];
    }

    public function download()
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setTitle('Sản lượng đồng hồ')
            ->setCompany('Công ty CP Cấp Nước Hồ Cầu Mới');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sản lượng');

        $this->buildSheet($sheet);

        $filename = 'SanLuong_DongHo_'
            . str_replace('-','', $this->tu_ngay)
            . '_'
            . str_replace('-','', $this->den_ngay)
            . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        (new Xlsx($spreadsheet))->save('php://output');
        Yii::$app->end();
    }

    private function buildSheet($sheet)
    {
        $ngayCount = count($this->ngay_list);
        $totalCols = 5 + $ngayCount;  // STT, KH, vào, ra, tổng, TB, ngày1..N
        $lastCol   = $this->colLetter($totalCols - 1);

        // ── Tiêu đề ──
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A1', 'CÔNG TY CP CẤP NƯỚC HỒ CẦU MỚI');
        $this->styleHdr($sheet, "A1:{$lastCol}1", 13, self::C_HEADER, 'FFFFFFFF');

        $sheet->mergeCells("A2:{$lastCol}2");
        $tu_vn  = date('d/m/Y', strtotime($this->tu_ngay));
        $den_vn = date('d/m/Y', strtotime($this->den_ngay));
        $sheet->setCellValue('A2', "BẢNG SẢN LƯỢNG NƯỚC THEO ĐỒNG HỒ — Từ $tu_vn đến $den_vn");
        $this->styleHdr($sheet, "A2:{$lastCol}2", 11, self::C_SUBHEAD, 'FFFFFFFF');

        // ── Header bảng ──
        $headerRow = 4;
        $headers = ['STT', 'Khách hàng', 'Đ.hồ vào', 'Đ.hồ ra', 'Tổng (m³)', 'TB/ngày'];
        foreach ($this->ngay_list as $ngay) {
            $d = new \DateTime($ngay);
            $headers[] = $d->format('d/m');
        }

        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col.$headerRow, $h);
            $col++;
        }
        $this->styleHdr($sheet, "A{$headerRow}:{$lastCol}{$headerRow}", 10, self::C_HEADER, 'FFFFFFFF');
        $sheet->getRowDimension($headerRow)->setRowHeight(24);

        // ── Dữ liệu ──
        $row = $headerRow + 1;
        $tongNgay = array_fill_keys($this->ngay_list, null);
        $tongAll  = null;

        foreach ($this->rows as $i => $kh) {
            $bg = $i % 2 === 0 ? self::C_ODD : self::C_WHITE;

            $sheet->setCellValue('A'.$row, $i + 1);
            $sheet->setCellValue('B'.$row, $kh['ten_kh'] ?? '');
            $sheet->setCellValue('C'.$row, $kh['dau_vao'] ?? '');
            $sheet->setCellValue('D'.$row, $kh['dau_ra'] !== '—' ? $kh['dau_ra'] : '');

            if ($kh['tong'] !== null) {
                $sheet->setCellValue('E'.$row, $kh['tong']);
                $tongAll = ($tongAll ?? 0) + $kh['tong'];
            } else {
                $sheet->setCellValue('E'.$row, '');
            }

            $sheet->setCellValue('F'.$row, $kh['tb_ngay'] !== null ? $kh['tb_ngay'] : '');

            $colIdx = 6; // G = index 6
            foreach ($this->ngay_list as $ngay) {
                $v = $kh['ngay_data'][$ngay] ?? null;
                $colLtr = $this->colLetter($colIdx);
                $sheet->setCellValue($colLtr.$row, $v !== null ? $v : '');
                if ($v !== null) $tongNgay[$ngay] = ($tongNgay[$ngay] ?? 0) + $v;
                $colIdx++;
            }

            // Style dòng
            $this->styleRow($sheet, "A{$row}:{$lastCol}{$row}", false, $bg);
            // Tổng màu vàng
            $sheet->getStyle("E{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(self::C_TONG);
            $sheet->getStyle("E{$row}")->getFont()->setBold(true);
            // TB màu xanh
            $sheet->getStyle("F{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(self::C_TB);

            // Căn trái cột tên KH
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("C{$row}")->getFont()->setSize(8);
            $sheet->getStyle("D{$row}")->getFont()->setSize(8);

            $row++;
        }

        // ── Footer tổng ──
        $sheet->setCellValue("A{$row}", '');
        $sheet->setCellValue("B{$row}", 'TỔNG CỘNG');
        $sheet->setCellValue("E{$row}", $tongAll ?? '');

        $colIdx = 6;
        foreach ($this->ngay_list as $ngay) {
            $colLtr = $this->colLetter($colIdx);
            $sheet->setCellValue($colLtr.$row, $tongNgay[$ngay] ?? '');
            $colIdx++;
        }
        $this->styleRow($sheet, "A{$row}:{$lastCol}{$row}", true, self::C_HEADER, 'FFFFFFFF');

        // ── Ghi chú ──
        $row += 2;
        $sheet->setCellValue("A{$row}", '* Sản lượng tính từ dữ liệu tích lũy SCADA (MAX–MIN trong ngày). Ô trống = không có dữ liệu.');
        $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setItalic(true)->setSize(8);
        $sheet->getStyle("A{$row}")->getFont()->getColor()->setARGB('FF94A3B8');

        // ── Column widths ──
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(26);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(16);
        $sheet->getColumnDimension('E')->setWidth(13);
        $sheet->getColumnDimension('F')->setWidth(11);
        $colIdx = 6;
        foreach ($this->ngay_list as $_) {
            $sheet->getColumnDimension($this->colLetter($colIdx))->setWidth(8);
            $colIdx++;
        }

        // Freeze panes: cố định 2 cột đầu + header
        $sheet->freezePane('G'.($headerRow+1));
    }

    private function styleHdr($sheet, $range, $size=10, $bg=null, $color=null)
    {
        $s = $sheet->getStyle($range);
        $s->getFont()->setBold(true)->setSize($size);
        $s->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
          ->setVertical(Alignment::VERTICAL_CENTER);
        if ($bg)    $s->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($bg);
        if ($color) $s->getFont()->getColor()->setARGB($color);
    }

    private function styleRow($sheet, $range, $bold=false, $bg=null, $color=null)
    {
        $s = $sheet->getStyle($range);
        $s->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
          ->setVertical(Alignment::VERTICAL_CENTER);
        $s->getBorders()->getAllBorders()
          ->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFE2E8F0');
        if ($bold)  $s->getFont()->setBold(true);
        if ($bg)    $s->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($bg);
        if ($color) $s->getFont()->getColor()->setARGB($color);
    }

    /** Convert 0-based column index to Excel letter (A, B, ..., Z, AA, AB...) */
    private function colLetter(int $idx): string
    {
        $letter = '';
        $idx++;
        while ($idx > 0) {
            $mod = ($idx - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $idx = (int)(($idx - $mod) / 26);
        }
        return $letter;
    }
}