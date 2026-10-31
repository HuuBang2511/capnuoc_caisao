<?php
namespace app\modules\quanly\components;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Fill, Border, Alignment};
use Yii;

class BaoCaoNgayExcel
{
    private $ngay;
    private $scada;
    private $chatLuong;
    private $giaoCa;   // array of NkGiaoCa

    const C_HEADER  = 'FF1E3A5F';
    const C_SUBHEAD = 'FF2D6099';
    const C_ODD     = 'FFF0F4F8';
    const C_OK      = 'FFD1FAE5';
    const C_WARN    = 'FFFEF3C7';
    const C_BAD     = 'FFFEE2E2';
    const C_WHITE   = 'FFFFFFFF';

    public function __construct($ngay, $scada, $chatLuong, $giaoCa)
    {
        $this->ngay      = $ngay;
        $this->scada     = $scada;
        $this->chatLuong = is_array($chatLuong) ? $chatLuong : [];
        // BUG FIX #3: đảm bảo giaoCa luôn là array
        $this->giaoCa    = is_array($giaoCa) ? $giaoCa : ($giaoCa ? [$giaoCa] : []);
    }

    public function download()
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setTitle('Báo cáo ngày ' . $this->ngay)
            ->setCompany('Công ty CP Cấp Nước Hồ Cầu Mới');

        $this->buildSheet1($spreadsheet->getActiveSheet());
        $this->buildSheet2($spreadsheet->createSheet());
        $this->buildSheet3($spreadsheet->createSheet());

        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'BaoCaoNgay_' . $this->ngay . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        (new Xlsx($spreadsheet))->save('php://output');
        Yii::$app->end();
    }

    // ── Helpers ───────────────────────────────────────────────
    /**
     * BUG FIX #3: getGiaoCa luôn iterate đúng array
     */
    private function getGiaoCa($ca)
    {
        foreach ($this->giaoCa as $gc) {
            if ((int)$gc->ca === (int)$ca) return $gc;
        }
        return null;
    }

    // ── SHEET 1: SẢN XUẤT ────────────────────────────────────
    private function buildSheet1($sheet)
    {
        $sheet->setTitle('Sản xuất');
        $ngay_vn = date('d/m/Y', strtotime($this->ngay));
        $thu_vn  = ['CN','Thứ 2','Thứ 3','Thứ 4','Thứ 5','Thứ 6','Thứ 7'][date('w', strtotime($this->ngay))];
        $s = $this->scada ?? [];

        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'CÔNG TY CỔ PHẦN CẤP NƯỚC HỒ CẦU MỚI');
        $sheet->mergeCells('A2:H2');
        $sheet->setCellValue('A2', 'BÁO CÁO SẢN XUẤT HÀNG NGÀY');
        $sheet->mergeCells('A3:H3');
        $sheet->setCellValue('A3', "$thu_vn, ngày $ngay_vn");

        $this->styleHeader($sheet, 'A1:H1', 14, self::C_HEADER, 'FFFFFFFF');
        $this->styleHeader($sheet, 'A2:H2', 12, self::C_SUBHEAD, 'FFFFFFFF');
        $this->styleCenter($sheet, 'A3:H3');

        $row = 5;
        $this->writeRow($sheet, $row, ['STT','Chỉ tiêu','Đvt','Ca Ngày','Ca Đêm','Tổng ngày','Tháng lũy kế','Ghi chú'], true);
        $row++;

        $ca_ngay = $this->getGiaoCa(1);
        $ca_dem  = $this->getGiaoCa(2);

        $nuoc_tho = $s['nuoc_tho'] ?? 0;
        $nuoc_cap = $s['nuoc_cap'] ?? 0;

        $sl_tho_ngay = ($ca_ngay && $ca_ngay->nuoc_tho_cuoi !== null && $ca_ngay->nuoc_tho_dau !== null)
            ? ($ca_ngay->nuoc_tho_cuoi - $ca_ngay->nuoc_tho_dau)
            : ($nuoc_tho > 0 ? round($nuoc_tho / 2) : '—');

        $sl_cap_ngay = ($ca_ngay && $ca_ngay->nuoc_cap_cuoi !== null && $ca_ngay->nuoc_cap_dau !== null)
            ? ($ca_ngay->nuoc_cap_cuoi - $ca_ngay->nuoc_cap_dau)
            : ($nuoc_cap > 0 ? round($nuoc_cap / 2) : '—');

        $sl_tho_dem = ($ca_dem && $ca_dem->nuoc_tho_cuoi !== null && $ca_dem->nuoc_tho_dau !== null)
            ? ($ca_dem->nuoc_tho_cuoi - $ca_dem->nuoc_tho_dau) : '—';
        $sl_cap_dem = ($ca_dem && $ca_dem->nuoc_cap_cuoi !== null && $ca_dem->nuoc_cap_dau !== null)
            ? ($ca_dem->nuoc_cap_cuoi - $ca_dem->nuoc_cap_dau) : '—';

        $data = [
            [1,'SL Nước thô bơm vào','m³', $sl_tho_ngay, $sl_tho_dem, $s['nuoc_tho']??'—','—',''],
            [2,'SL Nước sạch cấp ra','m³', $sl_cap_ngay, $sl_cap_dem, $s['nuoc_cap']??'—','—',''],
            [3,'SL KH đồng hồ lớn', 'm³', '—','—', $s['nuoc_kh']??'—','—',''],
            [4,'Nước thất thoát',    'm³', '—','—', $s['that_thoat']??'—','—',''],
            [5,'Tỷ lệ thất thoát',   '%',  '—','—', isset($s['ti_le'])&&$s['ti_le']!==null?number_format($s['ti_le'],2).'%':'—','—',''],
        ];

        foreach ($data as $i => $d) {
            $this->writeRow($sheet, $row, $d, false, $i%2==0?self::C_ODD:self::C_WHITE);
            $row++;
        }

        $row++;
        $this->writeRow($sheet, $row, ['','Hóa chất sử dụng','','Ca Ngày','Ca Đêm','Tổng','',''], true, self::C_SUBHEAD, 'FFFFFFFF');
        $row++;

        foreach ([['PAC','pac_kg'],['Chlorine','chlorine_kg'],['Polymer','polymer_kg']] as $i => $hc) {
            $d = $ca_ngay && $ca_ngay->{$hc[1]} !== null ? $ca_ngay->{$hc[1]} : '—';
            $e = $ca_dem  && $ca_dem->{$hc[1]}  !== null ? $ca_dem->{$hc[1]}  : '—';
            $t = is_numeric($d) && is_numeric($e) ? $d+$e : '—';
            $this->writeRow($sheet, $row, ['',($i+1).'. '.$hc[0],'kg',$d,$e,$t,'',''], false, $i%2==0?self::C_ODD:self::C_WHITE);
            $row++;
        }

        $row++;
        $this->writeRow($sheet, $row, ['','Điện tiêu thụ','KWh','Ca Ngày','Ca Đêm','Tổng','Định mức',''], true, self::C_SUBHEAD, 'FFFFFFFF');
        $row++;
        foreach ([['Điện nhà máy','dien_nha_may'],['Điện trạm bơm','dien_tram_bom']] as $i => $el) {
            $f = $el[1];
            $d = ($ca_ngay && $ca_ngay->{$f.'_cuoi'} !== null && $ca_ngay->{$f.'_dau'} !== null)
               ? $ca_ngay->{$f.'_cuoi'} - $ca_ngay->{$f.'_dau'} : '—';
            $e = ($ca_dem  && $ca_dem->{$f.'_cuoi'} !== null && $ca_dem->{$f.'_dau'} !== null)
               ? $ca_dem->{$f.'_cuoi'}  - $ca_dem->{$f.'_dau'}  : '—';
            $t = is_numeric($d)&&is_numeric($e) ? $d+$e : '—';
            $this->writeRow($sheet, $row, ['',($i+1).'. '.$el[0],'KWh',$d,$e,$t,'',''], false, $i%2==0?self::C_ODD:self::C_WHITE);
            $row++;
        }

        $row++;
        $sheet->setCellValue("A$row", 'Mực nước hồ chứa:');
        $sheet->setCellValue("C$row", ($s['level_lake']??'—') . ' m');

        foreach (['A'=>8,'B'=>35,'C'=>8,'D'=>12,'E'=>12,'F'=>14,'G'=>14,'H'=>20] as $c=>$w)
            $sheet->getColumnDimension($c)->setWidth($w);
    }

    // ── SHEET 2: CHẤT LƯỢNG NƯỚC ─────────────────────────────
    private function buildSheet2($sheet)
    {
        $sheet->setTitle('Chất lượng nước');
        $ngay_vn = date('d/m/Y', strtotime($this->ngay));

        $sheet->mergeCells('A1:K1');
        $sheet->setCellValue('A1', 'NHẬT KÝ PHÂN TÍCH CHẤT LƯỢNG NƯỚC — ' . $ngay_vn);
        $this->styleHeader($sheet, 'A1:K1', 12, self::C_HEADER, 'FFFFFFFF');

        $sheet->mergeCells('A2:K2');
        $sheet->setCellValue('A2', 'QCVN 01-1:2018/BYT: pH 6.5–8.5 | Độ đục < 2 NTU | Clo dư 0.2–1.0 mg/L');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(9);
        $sheet->getStyle('A2')->getFont()->getColor()->setARGB('FF64748B');

        $row = 4;
        $this->writeRow($sheet, $row,
            ['Giờ','Ca','NS-pH','NS-NTU','NT-pH','NT-NTU','Lắng1-pH','Lắng1-NTU','Lắng2-pH','Lắng2-NTU','Clo dư'],
            true);
        $row++;

        $qcvn = ['ns_ph'=>[6.5,8.5],'ns_ntu'=>[0,2.0],'clo_du'=>[0.2,1.0]];

        foreach ($this->chatLuong as $i => $r) {
            $vals = [
                date('H:i', strtotime($r->thoi_gian)),
                $r->ca==1?'Ngày':'Đêm',
                $r->ns_ph, $r->ns_ntu, $r->nt_ph, $r->nt_ntu,
                $r->nl1_ph, $r->nl1_ntu, $r->nl2_ph, $r->nl2_ntu,
                $r->clo_du
            ];
            $this->writeRow($sheet, $row, $vals, false, $i%2==0?self::C_ODD:self::C_WHITE);

            foreach (['ns_ph'=>'C','ns_ntu'=>'D','clo_du'=>'K'] as $field=>$col) {
                $v = $r->$field;
                if ($v === null) continue;
                [$mn,$mx] = $qcvn[$field];
                $fv = (float)$v;
                if ($fv < $mn || $fv > $mx) {
                    $sheet->getStyle("{$col}{$row}")->getFill()
                          ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(self::C_BAD);
                } elseif ($fv < $mn+($mx-$mn)*0.05 || $fv > $mx-($mx-$mn)*0.05) {
                    $sheet->getStyle("{$col}{$row}")->getFill()
                          ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(self::C_WARN);
                }
            }
            $row++;
        }

        if (empty($this->chatLuong)) {
            $sheet->mergeCells("A{$row}:K{$row}");
            $sheet->setCellValue("A{$row}", '— Chưa có dữ liệu cho ngày này —');
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        foreach (['A'=>8,'B'=>8,'C'=>8,'D'=>9,'E'=>8,'F'=>9,'G'=>11,'H'=>12,'I'=>11,'J'=>12,'K'=>13] as $c=>$w)
            $sheet->getColumnDimension($c)->setWidth($w);
    }

    // ── SHEET 3: GIAO CA — BUG FIX #3 ───────────────────────
    private function buildSheet3($sheet)
    {
        $sheet->setTitle('Giao ca');
        $ngay_vn = date('d/m/Y', strtotime($this->ngay));

        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A1', 'SỔ GIAO CA — ' . $ngay_vn);
        $this->styleHeader($sheet, 'A1:E1', 12, self::C_HEADER, 'FFFFFFFF');

        $row = 3;
        foreach ([1=>'CA NGÀY (07h–18h)', 2=>'CA ĐÊM (19h–06h)'] as $ca=>$label) {
            $gc = $this->getGiaoCa($ca);  // null nếu chưa nhập

            // Tiêu đề ca
            $sheet->mergeCells("A{$row}:E{$row}");
            $sheet->setCellValue("A{$row}", $label);
            $this->styleHeader($sheet, "A{$row}:E{$row}", 10, self::C_SUBHEAD, 'FFFFFFFF');
            $row++;

            if ($gc === null) {
                // Chưa có dữ liệu — hiển thị rõ ràng
                $sheet->mergeCells("A{$row}:E{$row}");
                $sheet->setCellValue("A{$row}", 'Chưa có dữ liệu giao ca');
                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A{$row}")->getFont()->setItalic(true);
                $sheet->getStyle("A{$row}")->getFont()->getColor()->setARGB('FF94A3B8');
                $row += 2;
                continue;
            }

            // Bảng chỉ số
            $sheet->setCellValue("A{$row}", 'Chỉ tiêu');
            $sheet->setCellValue("B{$row}", 'Đầu ca');
            $sheet->setCellValue("C{$row}", 'Cuối ca');
            $sheet->setCellValue("D{$row}", 'Tổng ca');
            $this->styleRow($sheet, "A{$row}:D{$row}", true, self::C_ODD);
            $row++;

            $nuoc_tho_total = ($gc->nuoc_tho_cuoi !== null && $gc->nuoc_tho_dau !== null)
                ? ($gc->nuoc_tho_cuoi - $gc->nuoc_tho_dau) : '—';
            $dien_nm = ($gc->dien_nha_may_cuoi !== null && $gc->dien_nha_may_dau !== null)
                ? ($gc->dien_nha_may_cuoi - $gc->dien_nha_may_dau) : '—';

            $items = [
                ['Nước cấp (m³)',   $gc->nuoc_cap_dau ?? '—', $gc->nuoc_cap_cuoi ?? '—', $gc->getSanLuongCap() ?? '—'],
                ['Nước thô (m³)',   $gc->nuoc_tho_dau ?? '—', $gc->nuoc_tho_cuoi ?? '—', $nuoc_tho_total],
                ['Điện NM (KWh)',   $gc->dien_nha_may_dau ?? '—', $gc->dien_nha_may_cuoi ?? '—', $dien_nm],
            ];

            foreach ($items as $i=>$it) {
                $sheet->fromArray($it, null, "A{$row}");
                if ($i%2==0) $sheet->getStyle("A{$row}:D{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(self::C_ODD);
                $row++;
            }

            $row++;
            $sheet->setCellValue("A{$row}",
                'Bơm NT: '.($gc->bom_nt_chay ?? '—')
                .' | Bơm TH: '.($gc->bom_th_chay ?? '—'));
            $sheet->mergeCells("A{$row}:D{$row}"); $row++;

            $sheet->setCellValue("A{$row}",
                'PAC: '.($gc->pac_kg ?? '—').' kg'
                .' | Chlorine: '.($gc->chlorine_kg ?? '—').' kg'
                .' | Polymer: '.($gc->polymer_kg ?? '—').' kg');
            $sheet->mergeCells("A{$row}:D{$row}"); $row++;

            if (!empty($gc->su_co)) {
                $sheet->setCellValue("A{$row}", '⚠ Sự cố: '.$gc->su_co);
                $sheet->mergeCells("A{$row}:D{$row}");
                $sheet->getStyle("A{$row}")->getFont()->getColor()->setARGB('FFDC2626');
                $row++;
                if (!empty($gc->bien_phap)) {
                    $sheet->setCellValue("A{$row}", '→ Biện pháp: '.$gc->bien_phap);
                    $sheet->mergeCells("A{$row}:D{$row}"); $row++;
                }
            }

            $sheet->setCellValue("A{$row}",
                'NV giao: '.($gc->nhan_vien_giao ?? '—')
                .'   |   NV nhận: '.($gc->nhan_vien_nhan ?? '—'));
            $sheet->mergeCells("A{$row}:D{$row}"); $row += 2;
        }

        foreach (['A'=>30,'B'=>14,'C'=>14,'D'=>14,'E'=>15] as $c=>$w)
            $sheet->getColumnDimension($c)->setWidth($w);
    }

    // ── Style helpers ─────────────────────────────────────────
    private function writeRow($sheet, $row, $data, $bold=false, $bg=null, $color=null)
    {
        $cols = range('A', chr(ord('A') + count($data) - 1));
        foreach ($data as $i=>$val)
            $sheet->setCellValue($cols[$i].$row, $val ?? '');
        $this->styleRow($sheet, 'A'.$row.':'.$cols[count($data)-1].$row, $bold, $bg, $color);
    }

    private function styleRow($sheet, $range, $bold=false, $bg=null, $color=null)
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

    private function styleHeader($sheet, $range, $size=11, $bg=null, $color=null)
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