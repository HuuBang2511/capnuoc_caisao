<?php
namespace app\modules\quanly\components;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Yii;

class ClnTuanExcel
{
    private $thang;
    private $nam;
    private $lichTuan; // array of NkPhanTichTuan

    const C_HEADER  = 'FF1E3A5F';
    const C_NT      = 'FF0369A1';  // xanh đậm — Nước thô
    const C_NS      = 'FF166534';  // xanh lá  — Nước sạch
    const C_ODD     = 'FFF0F4F8';
    const C_WARN    = 'FFFEF3C7';
    const C_BAD     = 'FFFEE2E2';
    const C_WHITE   = 'FFFFFFFF';
    const C_YELLOW  = 'FFFEF9C3';  // hàng BQ

    // QCVN 01-1:2018/BYT — giới hạn NS
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

    // Định nghĩa cột: [field_nt, field_ns, label, unit]
    const COLS = [
        ['nt_do_kiem',     'ns_do_kiem',     'Độ kiềm',     'CaCO3 mg/L'],
        ['nt_do_cung',     'ns_do_cung',     'Độ cứng',     'CaCO3 mg/L'],
        ['nt_clorua',      'ns_clorua',      'Clorua',       'mg/L'],
        ['nt_tss',         'ns_tss',         'TSS',          'mg/L'],
        ['nt_al',          'ns_al',          'Nhôm Al',      'mg/L'],
        ['nt_fe',          'ns_fe',          'Sắt Fe',       'mg/L'],
        ['nt_mn',          'ns_mn',          'Mangan Mn',    'mg/L'],
        ['nt_amoni',       'ns_amoni',       'Amoni NH4+',   'mg/L'],
        ['nt_nitrat',      'ns_nitrat',      'Nitrat NO3-',  'mg/L'],
        ['nt_nitrit',      'ns_nitrit',      'Nitrit NO2-',  'mg/L'],
        ['nt_sulfat',      'ns_sulfat',      'Sulfat',       'mg/L'],
        ['nt_permanganat', 'ns_permanganat', 'Pecmanganat',  ''],
        ['nt_cod',         'ns_cod',         'COD',          'mg/L'],
        ['nt_coliform',    'ns_coliform',    'Coliform',     'VK/100ml'],
        ['nt_florua',      'ns_florua',      'Florua',       'µg/L'],
    ];

    public function __construct($thang, $nam, $lichTuan)
    {
        $this->thang    = (int)$thang;
        $this->nam      = (int)$nam;
        $this->lichTuan = is_array($lichTuan) ? $lichTuan : [];
    }

    public function download()
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setTitle('CL Nước Tuần T' . $this->thang . '/' . $this->nam)
            ->setCompany('Công ty CP Cấp Nước Hồ Cầu Mới');

        $this->buildSheet($spreadsheet->getActiveSheet());
        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'CLNuocTuan_T' . $this->thang . '_' . $this->nam . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        (new Xlsx($spreadsheet))->save('php://output');
        Yii::$app->end();
    }

    private function buildSheet($sheet)
    {
        $sheet->setTitle('CL Nước Tuần');
        $nCols   = count(self::COLS); // 15 chỉ tiêu
        $nColsXL = 2 + $nCols * 2;   // Tuần + Ngày PT + (NT+NS)*15 = 32 cột
        $lastCol = $this->colLetter($nColsXL);

        // ── TIÊU ĐỀ ───────────────────────────────────────────
        $sheet->mergeCells('A1:' . $lastCol . '1');
        $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN CẤP NƯỚC HỒ CẦU MỚI');
        $this->styleHeader($sheet, 'A1:' . $lastCol . '1', 13, self::C_HEADER, 'FFFFFFFF');

        $sheet->mergeCells('A2:' . $lastCol . '2');
        $sheet->setCellValue('A2', 'KẾT QUẢ CHẤT LƯỢNG NƯỚC HÀNG THÁNG (WEEKLY WATER TEST RESULT)');
        $this->styleHeader($sheet, 'A2:' . $lastCol . '2', 12, 'FF2D6099', 'FFFFFFFF');

        $sheet->mergeCells('A3:' . $lastCol . '3');
        $sheet->setCellValue('A3', 'Tháng ' . $this->thang . ' Năm ' . $this->nam . '           BM.01.02');
        $this->styleCenter($sheet, 'A3:' . $lastCol . '3');
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);

        // ── HEADER BẢNG (3 dòng gộp) ──────────────────────────
        $row = 5;

        // Row 5 — nhóm lớn
        $sheet->setCellValue('A' . $row, 'Tuần');
        $sheet->mergeCells('A' . $row . ':A' . ($row + 2));
        $sheet->setCellValue('B' . $row, 'Ngày PT');
        $sheet->mergeCells('B' . $row . ':B' . ($row + 2));

        $col = 3; // bắt đầu từ cột C
        foreach (self::COLS as $def) {
            list($fnt, $fns, $label, $unit) = $def;
            $colLetter  = $this->colLetter($col);
            $colLetter2 = $this->colLetter($col + 1);
            $sheet->setCellValue($colLetter . $row, $label . ($unit ? "\n" . $unit : ''));
            $sheet->mergeCells($colLetter . $row . ':' . $colLetter2 . $row);
            $col += 2;
        }
        $this->styleHeader($sheet, 'A' . $row . ':' . $lastCol . $row, 9, self::C_HEADER, 'FFFFFFFF');
        $sheet->getRowDimension($row)->setRowHeight(28);

        // Row 6 — NT / NS
        $row++;
        $col = 3;
        foreach (self::COLS as $def) {
            $colLetter  = $this->colLetter($col);
            $colLetter2 = $this->colLetter($col + 1);
            $sheet->setCellValue($colLetter  . $row, 'NT');
            $sheet->setCellValue($colLetter2 . $row, 'NS');
            $col += 2;
        }
        $this->styleRow($sheet, 'A' . $row . ':' . $lastCol . $row, true);
        foreach (range(3, $nColsXL) as $c) {
            $ltr = $this->colLetter($c);
            $color = ($c % 2 === 1) ? self::C_NT : self::C_NS;
            $sheet->getStyle($ltr . $row)->getFill()
                  ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);
            $sheet->getStyle($ltr . $row)->getFont()->getColor()->setARGB('FFFFFFFF');
        }
        $sheet->getStyle('A' . $row . ':B' . $row)->getFill()
              ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(self::C_HEADER);

        // Row 7 — QCVN limits
        $row++;
        $sheet->setCellValue('A' . $row, 'QCVN');
        $col = 3;
        foreach (self::COLS as $def) {
            list($fnt, $fns, $label, $unit) = $def;
            $colLetter2 = $this->colLetter($col + 1);
            $qc = isset(self::QCVN_NS[$fns]) ? '≤' . self::QCVN_NS[$fns] : '';
            $sheet->setCellValue($colLetter2 . $row, $qc);
            $col += 2;
        }
        $this->styleRow($sheet, 'A' . $row . ':' . $lastCol . $row, false, 'FFEFF6FF');
        $sheet->getStyle('A' . $row . ':B' . $row)->getFill()
              ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(self::C_HEADER);
        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->getColor()->setARGB('FFFFFFFF');
        $row++;

        // ── DỮ LIỆU TỪNG TUẦN ────────────────────────────────
        // Map tuan_so => array of records (có thể nhiều bản ghi/tuần)
        $tuanMap = [];
        foreach ($this->lichTuan as $r) {
            $tuanMap[$r->tuan_so][] = $r;
        }

        // Tính số tuần trong tháng
        $weeks = $this->getWeeks($this->thang, $this->nam);

        $sums  = array_fill(0, count(self::COLS) * 2, 0);
        $cnts  = array_fill(0, count(self::COLS) * 2, 0);
        $bgIdx = 0;

        foreach ($weeks as $tuanSo => $weekInfo) {
            $recs = isset($tuanMap[$tuanSo]) ? $tuanMap[$tuanSo] : [];
            // Đảm bảo luôn có đúng 3 hàng
            while (count($recs) < 3) $recs[] = null;
            $recs = array_slice($recs, 0, 3);

            $startRow = $row;
            foreach ($recs as $ri => $rec) {
                $bg = ($bgIdx % 2 === 0) ? self::C_ODD : self::C_WHITE;

                // Cột A: Tuần (merge 3 hàng)
                if ($ri === 0) {
                    $endMerge = $row + 2;
                    $sheet->mergeCells('A' . $row . ':A' . $endMerge);
                    $tuanLbl = 'Tuần ' . $tuanSo . "\n"
                        . date('d/m', strtotime($weekInfo['start']))
                        . '–' . date('d/m', strtotime($weekInfo['end']));
                    $sheet->setCellValue('A' . $row, $tuanLbl);
                    $sheet->getStyle('A' . $row)->getAlignment()
                          ->setWrapText(true)->setVertical(Alignment::VERTICAL_CENTER)
                          ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A' . $row)->getFill()
                          ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(self::C_ODD);
                    $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(8);
                }

                // Cột B: Ngày PT
                $ngayVal = $rec ? $rec->ngay_pt : '';
                if ($ngayVal) {
                    $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($ngayVal)));
                } else {
                    $sheet->setCellValue('B' . $row, '');
                }

                // Các cột chỉ tiêu
                $col = 3;
                $ci  = 0;
                foreach (self::COLS as $def) {
                    list($fnt, $fns) = $def;
                    $colNt = $this->colLetter($col);
                    $colNs = $this->colLetter($col + 1);

                    $vnt = ($rec && $rec->$fnt !== null) ? $rec->$fnt : null;
                    $vns = ($rec && $rec->$fns !== null) ? $rec->$fns : null;

                    $sheet->setCellValue($colNt . $row, $vnt !== null ? $vnt : '');
                    $sheet->setCellValue($colNs . $row, $vns !== null ? $vns : '');

                    // Highlight NS vượt QCVN
                    if ($vns !== null && isset(self::QCVN_NS[$fns])) {
                        $qc = self::QCVN_NS[$fns];
                        if ($qc == 0 ? (float)$vns > 0 : (float)$vns > $qc) {
                            $sheet->getStyle($colNs . $row)->getFill()
                                  ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(self::C_BAD);
                        }
                    }

                    // Cộng vào sum cho BQ
                    if ($vnt !== null) { $sums[$ci*2]   += (float)$vnt; $cnts[$ci*2]++;   }
                    if ($vns !== null) { $sums[$ci*2+1] += (float)$vns; $cnts[$ci*2+1]++; }
                    $ci++;
                    $col += 2;
                }

                $this->styleRow($sheet, 'A' . $row . ':' . $lastCol . $row, false, $bg);
                $sheet->getRowDimension($row)->setRowHeight(16);
                $row++;
            }
            $bgIdx++;
        }

        // ── HÀNG TRUNG BÌNH ──────────────────────────────────
        $sheet->setCellValue('A' . $row, 'Trung bình');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $col = 3;
        $ci  = 0;
        foreach (self::COLS as $def) {
            $colNt = $this->colLetter($col);
            $colNs = $this->colLetter($col + 1);
            $bqNt  = $cnts[$ci*2]   > 0 ? round($sums[$ci*2]   / $cnts[$ci*2],   3) : '';
            $bqNs  = $cnts[$ci*2+1] > 0 ? round($sums[$ci*2+1] / $cnts[$ci*2+1], 3) : '';
            $sheet->setCellValue($colNt . $row, $bqNt);
            $sheet->setCellValue($colNs . $row, $bqNs);
            $ci++; $col += 2;
        }
        $this->styleRow($sheet, 'A' . $row . ':' . $lastCol . $row, true, self::C_YELLOW);
        $row += 2;

        // ── KÝ TÊN ────────────────────────────────────────────
        $nguoi_pt = !empty($this->lichTuan) ? ($this->lichTuan[0]->nguoi_pt ?? '') : '';
        $nguoi_kt = !empty($this->lichTuan) ? ($this->lichTuan[0]->nguoi_kt ?? '') : '';

        $sheet->setCellValue('A' . $row, 'Người thực hiện');
        $sheet->setCellValue($this->colLetter((int)round($nColsXL * 0.6)) . $row, 'Người kiểm tra');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $sheet->getStyle($this->colLetter((int)round($nColsXL * 0.6)) . $row)->getFont()->setBold(true);
        $row++;
        $sheet->setCellValue('A' . $row, $nguoi_pt);
        $sheet->setCellValue($this->colLetter((int)round($nColsXL * 0.6)) . $row, $nguoi_kt);

        // ── COLUMN WIDTHS ─────────────────────────────────────
        $sheet->getColumnDimension('A')->setWidth(12);
        $sheet->getColumnDimension('B')->setWidth(11);
        for ($c = 3; $c <= $nColsXL; $c++) {
            $sheet->getColumnDimension($this->colLetter($c))->setWidth(8);
        }

        // Wrap text header
        $sheet->getStyle('A5:' . $lastCol . '5')->getAlignment()->setWrapText(true);
    }

    // ── HELPERS ───────────────────────────────────────────────

    private function getWeeks($thang, $nam)
    {
        $first = mktime(0, 0, 0, $thang, 1, $nam);
        $last  = mktime(0, 0, 0, $thang + 1, 0, $nam);
        $weeks = []; $w = 1; $d = $first;
        while ($d <= $last && $w <= 5) {
            $end = min($d + 6 * 86400, $last);
            $weeks[$w] = ['start' => date('Y-m-d', $d), 'end' => date('Y-m-d', $end)];
            $d = $end + 86400;
            $w++;
        }
        return $weeks;
    }

    /**
     * Số cột (1-based) → chữ cái Excel: 1=A, 27=AA, ...
     */
    private function colLetter($n)
    {
        $letter = '';
        while ($n > 0) {
            $n--;
            $letter = chr(65 + ($n % 26)) . $letter;
            $n = (int)floor($n / 26);
        }
        return $letter;
    }

    private function styleRow($sheet, $range, $bold = false, $bg = null, $color = null)
    {
        $s = $sheet->getStyle($range);
        $s->getAlignment()
          ->setHorizontal(Alignment::HORIZONTAL_CENTER)
          ->setVertical(Alignment::VERTICAL_CENTER);
        $s->getBorders()->getAllBorders()
          ->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFE2E8F0');
        if ($bold)  $s->getFont()->setBold(true);
        if ($bg)    $s->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($bg);
        if ($color) $s->getFont()->getColor()->setARGB($color);
    }

    private function styleHeader($sheet, $range, $size = 11, $bg = null, $color = null)
    {
        $s = $sheet->getStyle($range);
        $s->getFont()->setBold(true)->setSize($size);
        $s->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
          ->setVertical(Alignment::VERTICAL_CENTER);
        if ($bg)    $s->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($bg);
        if ($color) $s->getFont()->getColor()->setARGB($color);
    }

    private function styleCenter($sheet, $range)
    {
        $sheet->getStyle($range)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }
}