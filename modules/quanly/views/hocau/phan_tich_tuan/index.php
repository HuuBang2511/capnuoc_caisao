<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'CL Nước Tuần — Tháng ' . $thang . '/' . $nam;

function getWeeksPT($m, $y) {
    $first = mktime(0,0,0,$m,1,$y);
    $last  = mktime(0,0,0,$m+1,0,$y);
    $weeks = []; $w = 1; $d = $first;
    while ($d <= $last && $w <= 5) {
        $end = min($d + 6*86400, $last);
        $weeks[$w] = ['start'=>date('Y-m-d',$d),'end'=>date('Y-m-d',$end)];
        $d = $end + 86400; $w++;
    }
    return $weeks;
}
$weeks = getWeeksPT($thang, $nam);

// Map tuan_so => array of records
$tuanMap = [];
foreach ($lichTuan as $r) {
    $tuanMap[$r->tuan_so][] = $r;
}

// Cột bảng — [field_nt, field_ns, label, qcvn_ns_max, step]
$COLS = [
    ['nt_do_kiem',     'ns_do_kiem',     'Độ kiềm<br><small>Alkalinity<br>CaCO3 mg/L</small>', null,  '0.1'],
    ['nt_do_cung',     'ns_do_cung',     'Độ cứng<br><small>Hardness<br>CaCO3 mg/L</small>',   300,   '0.1'],
    ['nt_clorua',      'ns_clorua',      'Clorua<br><small>Chloride mg/L</small>',               250,   '0.1'],
    ['nt_tss',         'ns_tss',         'TSS<br><small>mg/L</small>',                           null,  '0.01'],
    ['nt_al',          'ns_al',          'Nhôm<br><small>Al mg/L</small>',                       0.2,   '0.001'],
    ['nt_fe',          'ns_fe',          'Sắt<br><small>Fe mg/L</small>',                        0.3,   '0.001'],
    ['nt_mn',          'ns_mn',          'Mangan<br><small>Mn mg/L</small>',                     0.1,   '0.001'],
    ['nt_amoni',       'ns_amoni',       'Amoni<br><small>NH4+ mg/L</small>',                    3.0,   '0.001'],
    ['nt_nitrat',      'ns_nitrat',      'Nitrat<br><small>NO3- mg/L</small>',                   50.0,  '0.001'],
    ['nt_nitrit',      'ns_nitrit',      'Nitrit<br><small>NO2- mg/L</small>',                   3.0,   '0.001'],
    ['nt_sulfat',      'ns_sulfat',      'Sulfat<br><small>SO4 mg/L</small>',                    250,   '0.1'],
    ['nt_permanganat', 'ns_permanganat', 'Pecmanganat',                                          2.0,   '0.001'],
    ['nt_cod',         'ns_cod',         'COD<br><small>mg/L</small>',                           null,  '0.1'],
    ['nt_coliform',    'ns_coliform',    'Coliform<br><small>VK/100ml</small>',                  0,     '1'],
    ['nt_florua',      'ns_florua',      'Florua<br><small>µg/L</small>',                        1.5,   '0.001'],
];

// Chia cột cho 2 trang in
$COLS_P1 = array_slice($COLS, 0, 8);  // Độ kiềm → Amoni
$COLS_P2 = array_slice($COLS, 8);      // Nitrat → Florua
?>
<style>
.pt-wrap{max-width:100%;padding:12px 8px}
.pt-nav{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:8px}
.pt-nav a{padding:6px 14px;border-radius:99px;text-decoration:none;font-size:.82rem;background:#f1f5f9;color:#475569}
.pt-nav a.active{background:#3b82f6;color:#fff}
.qnav-bar{display:flex;gap:5px;flex-wrap:wrap;align-items:center;padding:8px 10px;
          background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;margin-bottom:10px}
.qnav-label{font-size:.7rem;font-weight:700;color:#94a3b8;white-space:nowrap;margin-right:2px}
.qnav-btn{padding:5px 10px;border-radius:8px;text-decoration:none;font-size:.75rem;font-weight:500;
          background:#fff;color:#475569;border:1px solid #e2e8f0;white-space:nowrap;transition:all .12s}
.qnav-btn:hover{border-color:#3b82f6;color:#3b82f6;background:#eff6ff}
.qnav-active{background:#1e3a5f!important;color:#fff!important;border-color:#1e3a5f!important}
.pt-card{border:1px solid #e2e8f0;border-radius:10px;padding:14px;margin-bottom:14px;background:#fff}
.pt-title{font-size:.95rem;font-weight:700;color:#1e3a5f;margin-bottom:12px}
.pt-tbl{width:100%;border-collapse:collapse;font-size:.7rem;table-layout:auto}
.pt-tbl th{padding:4px 3px;text-align:center;border:1px solid #cbd5e1;white-space:normal;font-weight:600;line-height:1.2}
.pt-tbl th.h-main{background:#1e3a5f;color:#fff}
.pt-tbl th.h-nt{background:#0369a1;color:#fff}
.pt-tbl th.h-ns{background:#166534;color:#fff}
.pt-tbl td{padding:2px 2px;border:1px solid #e2e8f0;text-align:center;vertical-align:middle}
.pt-tbl td.tuan-cell{background:#f8fafc;font-weight:700;font-size:.72rem;padding:4px 5px;white-space:nowrap;vertical-align:middle}
.pt-tbl td.ngay-cell{background:#f8fafc;padding:2px 3px}
.pt-tbl td.nt-col{background:#f0f9ff}
.pt-tbl td.ns-col{background:#f0fdf4}
.pt-tbl tr.bq-row td{background:#fffbeb;font-weight:700}
.pt-tbl tr.row-2 td.nt-col{background:#e0f2fe}
.pt-tbl tr.row-2 td.ns-col{background:#dcfce7}
.pt-tbl tr.row-3 td.nt-col{background:#bae6fd}
.pt-tbl tr.row-3 td.ns-col{background:#bbf7d0}
.pt-tbl input[type=number]{width:56px;padding:2px 2px;border:1px solid #d1d5db;border-radius:3px;font-size:.7rem;text-align:center;-webkit-appearance:none;background:transparent;box-sizing:border-box}
.pt-tbl input[type=date]{width:96px;padding:2px 2px;border:1px solid #d1d5db;border-radius:3px;font-size:.7rem;background:transparent}
.pt-tbl input:focus{border-color:#3b82f6;outline:none;background:#fff}
.pt-tbl input.bad{border-color:#ef4444!important;background:#fef2f2}
.tbl-wrap{width:100%;overflow-x:auto}
.nguoi-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.nk-field label{font-size:.78rem;color:#64748b;margin-bottom:3px;display:block}
.nk-field input{width:100%;padding:8px 10px;border:1.5px solid #e2e8f0;border-radius:7px;font-size:.9rem;outline:none;box-sizing:border-box}
.nk-field input:focus{border-color:#3b82f6}
.btn-save{width:100%;padding:13px;background:#3b82f6;color:#fff;border:none;border-radius:10px;font-size:1rem;font-weight:600;cursor:pointer;margin-top:8px}
.flash-ok{background:#dcfce7;color:#166534;padding:9px 14px;border-radius:8px;margin-bottom:12px;font-size:.85rem}

/* ===== PRINT ===== */
.print-header{display:none}
.print-block{display:none}  /* Các bảng print-only — ẩn trên màn hình */
@media print{
    .qnav-bar,.nav-thang,.pt-nav,.flash-ok,button[type=submit],input[type=submit]{display:none!important}
    .print-header{display:block!important;margin-bottom:6px}
    /* Ẩn bảng gốc có overflow-x scroll, hiện các bảng print */
    .tbl-wrap{display:none!important}
    .print-block{display:block!important}
    /* Ngắt trang trước bảng thứ 2 */
    .print-page-2{page-break-before:always}
    @page{size:A4 landscape;margin:8mm}
    *{-webkit-print-color-adjust:exact!important;print-color-adjust:exact!important}
    .pt-wrap{padding:0!important}
    .pt-card{padding:4px!important;margin-bottom:4px!important;border-radius:0!important;border:none!important;box-shadow:none!important}
    .pt-title{display:none!important}
    /* Style bảng khi in */
    .print-tbl{width:100%;border-collapse:collapse}
    .print-tbl th{padding:3px 4px;text-align:center;border:1px solid #94a3b8;white-space:normal;font-weight:700;line-height:1.2;font-size:7pt}
    .print-tbl th small{font-size:5.5pt}
    .print-tbl th.h-main{background:#1e3a5f;color:#fff}
    .print-tbl th.h-nt{background:#0369a1;color:#fff}
    .print-tbl th.h-ns{background:#166534;color:#fff}
    .print-tbl td{padding:3px 4px;border:1px solid #cbd5e1;text-align:center;vertical-align:middle;font-size:7.5pt}
    .print-tbl td.tuan-cell{background:#f8fafc!important;font-weight:700;font-size:7pt;padding:3px 5px;white-space:nowrap}
    .print-tbl td.ngay-cell{background:#f8fafc!important;font-size:7pt;padding:2px 4px}
    .print-tbl td.nt-col{background:#f0f9ff!important}
    .print-tbl td.ns-col{background:#f0fdf4!important}
    .print-tbl tr.bq-row td{background:#fffbeb!important;font-weight:700}
    .print-tbl tr.bq-month td{background:#fef9c3!important;font-weight:700}
    .print-tbl tr.row-2 td.nt-col{background:#e0f2fe!important}
    .print-tbl tr.row-2 td.ns-col{background:#dcfce7!important}
    .print-tbl tr.row-3 td.nt-col{background:#bae6fd!important}
    .print-tbl tr.row-3 td.ns-col{background:#bbf7d0!important}
    /* Người thực hiện */
    .nguoi-grid{display:grid!important;grid-template-columns:1fr 1fr!important;gap:20px!important;margin-top:12px}
    .nk-field input{font-size:9pt!important;border:none!important;border-bottom:1.5px solid #000!important;border-radius:0!important;padding:3px 4px!important;width:100%!important;background:transparent!important}
    .nk-field label{font-size:7.5pt!important;color:#64748b}
}
</style>

<div class="pt-wrap">
<!-- PRINT HEADER (ẩn trên màn hình, hiện khi in) -->
<div class="print-header">
<table style="width:100%;border:1.5px solid #000;border-collapse:collapse;margin-bottom:6px;font-family:Arial,sans-serif;">
    <tr>
        <td rowspan="3" style="width:14%;border:1px solid #000;padding:4px;text-align:center;vertical-align:middle;">
            <img src="<?= Yii::$app->homeUrl ?>images/logo_tuanloc.jpg" style="height:55px;width:auto;" /><br>
            <span style="font-size:6.5pt;font-weight:700;color:#1e3a5f;">CONG TY CP CAP NUOC<br>HO CAU MOI</span>
        </td>
        <td rowspan="3" style="border:1px solid #000;padding:4px;text-align:center;vertical-align:middle;">
            <div style="font-size:13pt;font-weight:700;letter-spacing:.02em;">PHAN TICH CHAT LUONG NUOC TUAN</div>
            <div style="font-size:8pt;margin-top:3px;color:#334155;"><?= 'Thang ' . $thang . '/' . $nam ?></div>
        </td>
        <td style="border:1px solid #000;padding:3px 8px;font-size:8pt;width:18%;">Ma so:</td>
        <td style="border:1px solid #000;padding:3px 8px;font-size:8pt;width:18%;"></td>
    </tr>
    <tr>
        <td style="border:1px solid #000;padding:3px 8px;font-size:8pt;">Ngay ban hanh:</td>
        <td style="border:1px solid #000;padding:3px 8px;font-size:8pt;"></td>
    </tr>
    <tr>
        <td style="border:1px solid #000;padding:3px 8px;font-size:8pt;">Ngay sua doi:</td>
        <td style="border:1px solid #000;padding:3px 8px;font-size:8pt;"></td>
    </tr>
</table>
</div>


    <div class="pt-nav">
        <?php for ($i=3;$i>=0;$i--):
            $m = (int)date('m') - $i; $y = (int)date('Y');
            if ($m <= 0) { $m += 12; $y--; }
        ?>
        <a href="<?= Url::to(['nhat-ky/phan-tich-tuan','thang'=>$m,'nam'=>$y]) ?>"
           class="<?= ($m==$thang&&$y==$nam)?'active':'' ?>">T<?= $m ?>/<?= $y ?></a>
        <?php endfor; ?>
    </div>

    <!-- Thanh lối tắt nhập liệu -->
    <?php $todayNav = date('Y-m-d'); ?>
    <div class="qnav-bar">
        <span class="qnav-label">✏ Nhập liệu:</span>
        <a href="<?= Url::to(['nhat-ky/chat-luong-gio','ca'=>1,'ngay'=>$todayNav]) ?>" class="qnav-btn">🧪 HN Ngày</a>
        <a href="<?= Url::to(['nhat-ky/chat-luong-gio','ca'=>2,'ngay'=>$todayNav]) ?>" class="qnav-btn">🌙 HN Đêm</a>
        <a href="<?= Url::to(['nhat-ky/giao-ca','ca'=>1,'ngay'=>$todayNav]) ?>" class="qnav-btn">☀️ VH Ngày</a>
        <a href="<?= Url::to(['nhat-ky/giao-ca','ca'=>2,'ngay'=>$todayNav]) ?>" class="qnav-btn">🌙 VH Đêm</a>
        <a href="<?= Url::to(['nhat-ky/nuoc-thai-sh','ngay'=>$todayNav]) ?>" class="qnav-btn">🧫 Nước thải</a>
        <a href="<?= Url::to(['nhat-ky/cln-hang-ngay','ngay'=>$todayNav]) ?>" class="qnav-btn">📋 CLN ngày</a>
        <a href="<?= Url::to(['nhat-ky/phan-tich-tuan']) ?>" class="qnav-btn qnav-active">📊 CL Tuần</a>
    </div>

    <?php if (Yii::$app->session->hasFlash('success_tuan')): ?>
    <div class="flash-ok">✓ <?= Yii::$app->session->getFlash('success_tuan') ?></div>
    <?php endif; ?>
    <?php if (Yii::$app->session->hasFlash('warning_tuan')): ?>
    <div class="flash-ok" style="background:#fef9c3;color:#854d0e;">⚠ <?= Yii::$app->session->getFlash('warning_tuan') ?></div>
    <?php endif; ?>
    <?php if (Yii::$app->session->hasFlash('error_tuan')): ?>
    <div class="flash-ok" style="background:#fee2e2;color:#991b1b;">✗ <?= Yii::$app->session->getFlash('error_tuan') ?></div>
    <?php endif; ?>

    <form method="post" id="form-pt">
        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
        <input type="hidden" name="thang" value="<?= $thang ?>">
        <input type="hidden" name="nam"   value="<?= $nam ?>">

        <div class="pt-card">
            <div class="pt-title">
                KẾT QUẢ CHẤT LƯỢNG NƯỚC HÀNG THÁNG (WEEKLY WATER TEST RESULT)
                — Tháng <?= $thang ?> Năm <?= $nam ?>
                <span style="float:right;font-size:.72rem;color:#94a3b8;font-weight:400">BM.01.02</span>
            </div>

            <!-- ====================================================
                 BẢNG GỐC — hiện trên màn hình (scrollable), ẩn khi in
                 ==================================================== -->
            <div class="tbl-wrap">
            <table class="pt-tbl">
                <thead>
                    <tr>
                        <th class="h-main" rowspan="2" style="min-width:70px">Tuần</th>
                        <th class="h-main" rowspan="2" style="min-width:100px">Ngày PT</th>
                        <?php foreach ($COLS as $c): ?>
                        <th class="h-nt"><?= $c[2] ?><br><small>NT</small></th>
                        <th class="h-ns"><?= $c[2] ?><br><small>NS<?= $c[3]!==null?' ≤'.$c[3]:'' ?></small></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($weeks as $tuanSo => $weekInfo):
                    $recs = isset($tuanMap[$tuanSo]) ? $tuanMap[$tuanSo] : [];
                    while (count($recs) < 3) $recs[] = null;
                    $recs = array_slice($recs, 0, 3);
                    $s = date('d/m', strtotime($weekInfo['start']));
                    $e = date('d/m', strtotime($weekInfo['end']));
                    $tuanBqSums = []; $tuanBqCnts = [];
                    foreach ($COLS as $ci => $c) { $tuanBqSums[$ci] = [0, 0]; $tuanBqCnts[$ci] = [0, 0]; }
                    foreach ($recs as $rec) {
                        if (!$rec) continue;
                        foreach ($COLS as $ci => $c) {
                            list($fnt,$fns) = $c;
                            if ($rec->$fnt !== null) { $tuanBqSums[$ci][0] += $rec->$fnt; $tuanBqCnts[$ci][0]++; }
                            if ($rec->$fns !== null) { $tuanBqSums[$ci][1] += $rec->$fns; $tuanBqCnts[$ci][1]++; }
                        }
                    }
                ?>
                <?php foreach ($recs as $ri => $rec): $rowClass = 'row-' . ($ri + 1); ?>
                <tr class="<?= $rowClass ?>">
                    <?php if ($ri === 0): ?>
                    <td class="tuan-cell" rowspan="3">
                        Tuần <?= $tuanSo ?><br>
                        <span style="font-size:.65rem;font-weight:400;color:#94a3b8"><?= $s ?>–<?= $e ?></span>
                    </td>
                    <?php endif; ?>
                    <td class="ngay-cell">
                        <input type="date" name="rows[<?= $tuanSo ?>][<?= $ri ?>][ngay_pt]"
                               value="<?= Html::encode($rec !== null ? $rec->ngay_pt : '') ?>" />
                        <?php if ($rec !== null): ?>
                        <input type="hidden" name="rows[<?= $tuanSo ?>][<?= $ri ?>][id]" value="<?= $rec->id ?>" />
                        <?php endif; ?>
                    </td>
                    <?php foreach ($COLS as $ci => $c):
                        list($fnt,$fns,$label,$qc,$step) = $c;
                        $vnt = ($rec !== null) ? $rec->$fnt : null;
                        $vns = ($rec !== null) ? $rec->$fns : null;
                        $badNs = ($qc !== null && $vns !== null && ($qc == 0 ? (float)$vns > 0 : (float)$vns > $qc));
                    ?>
                    <td class="nt-col">
                        <input type="number" name="rows[<?= $tuanSo ?>][<?= $ri ?>][<?= $fnt ?>]"
                               value="<?= $vnt !== null ? Html::encode($vnt) : '' ?>"
                               step="<?= $step ?>" inputmode="decimal" />
                    </td>
                    <td class="ns-col">
                        <input type="number" name="rows[<?= $tuanSo ?>][<?= $ri ?>][<?= $fns ?>]"
                               value="<?= $vns !== null ? Html::encode($vns) : '' ?>"
                               step="<?= $step ?>" inputmode="decimal"
                               class="<?= $badNs ? 'bad' : '' ?>"
                               onchange="chkQc(this,<?= json_encode($qc) ?>)" />
                    </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
                <!-- BQ tuần -->
                <tr class="bq-row">
                    <td colspan="2" style="font-size:.68rem;background:#fffbeb">TB Tuần <?= $tuanSo ?></td>
                    <?php foreach ($COLS as $ci => $c):
                        $bqNt = $tuanBqCnts[$ci][0] > 0 ? round($tuanBqSums[$ci][0]/$tuanBqCnts[$ci][0],3) : null;
                        $bqNs = $tuanBqCnts[$ci][1] > 0 ? round($tuanBqSums[$ci][1]/$tuanBqCnts[$ci][1],3) : null;
                    ?>
                    <td class="nt-col"><?= $bqNt !== null ? $bqNt : '—' ?></td>
                    <td class="ns-col"><?= $bqNs !== null ? $bqNs : '—' ?></td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
                <!-- BQ tháng -->
                <?php
                $monthSums = []; $monthCnts = [];
                foreach ($COLS as $ci => $c) { $monthSums[$ci] = [0,0]; $monthCnts[$ci] = [0,0]; }
                foreach ($lichTuan as $r) {
                    foreach ($COLS as $ci => $c) {
                        list($fnt,$fns) = $c;
                        if ($r->$fnt !== null) { $monthSums[$ci][0] += $r->$fnt; $monthCnts[$ci][0]++; }
                        if ($r->$fns !== null) { $monthSums[$ci][1] += $r->$fns; $monthCnts[$ci][1]++; }
                    }
                }
                ?>
                <tr class="bq-row" style="border-top:2px solid #cbd5e1">
                    <td colspan="2" style="font-weight:700;font-size:.75rem;background:#fef9c3">TB Tháng <?= $thang ?></td>
                    <?php foreach ($COLS as $ci => $c):
                        $bqNt = $monthCnts[$ci][0] > 0 ? round($monthSums[$ci][0]/$monthCnts[$ci][0],3) : null;
                        $bqNs = $monthCnts[$ci][1] > 0 ? round($monthSums[$ci][1]/$monthCnts[$ci][1],3) : null;
                    ?>
                    <td class="nt-col"><?= $bqNt !== null ? $bqNt : '—' ?></td>
                    <td class="ns-col"><?= $bqNs !== null ? $bqNs : '—' ?></td>
                    <?php endforeach; ?>
                </tr>
                </tbody>
            </table>
            </div><!-- /.tbl-wrap -->

            <?php
            // ============================================================
            // TÍNH BQ THÁNG CHO TỪNG NHÓM CỘT (dùng cho 2 bảng print)
            // ============================================================
            $bqThangP1 = []; $bqThangP2 = [];
            foreach ($COLS_P1 as $ci => $c) {
                list($fnt,$fns) = $c;
                $sNt=0;$cNt=0;$sNs=0;$cNs=0;
                foreach ($lichTuan as $r) {
                    if ($r->$fnt !== null) { $sNt+=$r->$fnt; $cNt++; }
                    if ($r->$fns !== null) { $sNs+=$r->$fns; $cNs++; }
                }
                $bqThangP1[$ci] = [
                    $cNt>0 ? round($sNt/$cNt,3) : null,
                    $cNs>0 ? round($sNs/$cNs,3) : null,
                ];
            }
            foreach ($COLS_P2 as $ci => $c) {
                list($fnt,$fns) = $c;
                $sNt=0;$cNt=0;$sNs=0;$cNs=0;
                foreach ($lichTuan as $r) {
                    if ($r->$fnt !== null) { $sNt+=$r->$fnt; $cNt++; }
                    if ($r->$fns !== null) { $sNs+=$r->$fns; $cNs++; }
                }
                $bqThangP2[$ci] = [
                    $cNt>0 ? round($sNt/$cNt,3) : null,
                    $cNs>0 ? round($sNs/$cNs,3) : null,
                ];
            }

            // Helper render 1 bảng print
            function renderPrintBlock($colsSlice, $bqThang, $weeks, $tuanMap, $thang, $nam, $pageClass) {
                $pageLabel = ($pageClass === '') ? '1/2' : '2/2';
            ?>
            <div class="print-block <?= $pageClass ?>">
                <div style="font-size:8pt;font-weight:700;color:#1e3a5f;margin-bottom:4px;border-bottom:1.5px solid #1e3a5f;padding-bottom:3px;">
                    KẾT QUẢ CHẤT LƯỢNG NƯỚC HÀNG THÁNG (WEEKLY WATER TEST RESULT)
                    — Tháng <?= $thang ?> Năm <?= $nam ?>
                    <span style="margin-left:8px;font-weight:400;font-size:7pt;color:#64748b;">Trang <?= $pageLabel ?></span>
                    <span style="float:right;font-size:7pt;color:#94a3b8;font-weight:400">BM.01.02</span>
                </div>
                <table class="print-tbl">
                    <thead>
                        <tr>
                            <th class="h-main" rowspan="2" style="min-width:55px">Tuần</th>
                            <th class="h-main" rowspan="2" style="min-width:68px">Ngày PT</th>
                            <?php foreach ($colsSlice as $c): ?>
                            <th class="h-nt"><?= $c[2] ?><br><small>NT</small></th>
                            <th class="h-ns"><?= $c[2] ?><br><small>NS<?= $c[3]!==null?' ≤'.$c[3]:'' ?></small></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($weeks as $tuanSo => $weekInfo):
                        $recs = isset($tuanMap[$tuanSo]) ? $tuanMap[$tuanSo] : [];
                        while (count($recs) < 3) $recs[] = null;
                        $recs = array_slice($recs, 0, 3);
                        $s = date('d/m', strtotime($weekInfo['start']));
                        $e = date('d/m', strtotime($weekInfo['end']));
                        // BQ tuần cho slice này
                        $tBqS=[]; $tBqC=[];
                        foreach ($colsSlice as $ci=>$c) { $tBqS[$ci]=[0,0]; $tBqC[$ci]=[0,0]; }
                        foreach ($recs as $rec) {
                            if (!$rec) continue;
                            foreach ($colsSlice as $ci=>$c) {
                                list($fnt,$fns)=$c;
                                if ($rec->$fnt!==null){$tBqS[$ci][0]+=$rec->$fnt;$tBqC[$ci][0]++;}
                                if ($rec->$fns!==null){$tBqS[$ci][1]+=$rec->$fns;$tBqC[$ci][1]++;}
                            }
                        }
                    ?>
                    <?php foreach ($recs as $ri => $rec): $rowClass = 'row-'.($ri+1); ?>
                    <tr class="<?= $rowClass ?>">
                        <?php if ($ri === 0): ?>
                        <td class="tuan-cell" rowspan="3">
                            Tuần <?= $tuanSo ?><br>
                            <span style="font-size:5.5pt;font-weight:400;color:#94a3b8"><?= $s ?>–<?= $e ?></span>
                        </td>
                        <?php endif; ?>
                        <td class="ngay-cell"><?= ($rec&&$rec->ngay_pt) ? date('d/m/Y',strtotime($rec->ngay_pt)) : '' ?></td>
                        <?php foreach ($colsSlice as $ci=>$c):
                            list($fnt,$fns,$label,$qc,$step)=$c;
                            $vnt=($rec!==null)?$rec->$fnt:null;
                            $vns=($rec!==null)?$rec->$fns:null;
                            $badNs=($qc!==null&&$vns!==null&&($qc==0?(float)$vns>0:(float)$vns>$qc));
                        ?>
                        <td class="nt-col"><?= $vnt!==null?$vnt:'' ?></td>
                        <td class="ns-col"<?= $badNs?' style="color:#dc2626;font-weight:700"':'' ?>><?= $vns!==null?$vns:'' ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                    <!-- BQ tuần -->
                    <tr class="bq-row">
                        <td colspan="2" style="font-size:6.5pt;background:#fffbeb">TB Tuần <?= $tuanSo ?></td>
                        <?php foreach ($colsSlice as $ci=>$c):
                            $bNt=$tBqC[$ci][0]>0?round($tBqS[$ci][0]/$tBqC[$ci][0],3):null;
                            $bNs=$tBqC[$ci][1]>0?round($tBqS[$ci][1]/$tBqC[$ci][1],3):null;
                        ?>
                        <td class="nt-col"><?= $bNt!==null?$bNt:'—' ?></td>
                        <td class="ns-col"><?= $bNs!==null?$bNs:'—' ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                    <!-- BQ tháng -->
                    <tr class="bq-month" style="border-top:2px solid #94a3b8">
                        <td colspan="2" style="font-size:7pt;font-weight:700;background:#fef9c3">TB Tháng <?= $thang ?></td>
                        <?php foreach ($colsSlice as $ci=>$c): ?>
                        <td class="nt-col"><?= $bqThang[$ci][0]!==null?$bqThang[$ci][0]:'—' ?></td>
                        <td class="ns-col"><?= $bqThang[$ci][1]!==null?$bqThang[$ci][1]:'—' ?></td>
                        <?php endforeach; ?>
                    </tr>
                    </tbody>
                </table>
            </div>
            <?php } // end renderPrintBlock

            renderPrintBlock($COLS_P1, $bqThangP1, $weeks, $tuanMap, $thang, $nam, '');
            renderPrintBlock($COLS_P2, $bqThangP2, $weeks, $tuanMap, $thang, $nam, 'print-page-2');
            ?>

        </div><!-- /.pt-card -->

        <div class="pt-card">
            <div style="font-size:.88rem;font-weight:700;color:#334155;margin-bottom:10px;">👤 Người thực hiện</div>
            <div class="nguoi-grid">
                <div class="nk-field">
                    <label>Người thực hiện phân tích</label>
                    <input type="text" name="nguoi_pt"
                           value="<?= Html::encode($nguoi_pt) ?>" placeholder="Họ và tên..." />
                </div>
                <div class="nk-field">
                    <label>Người kiểm tra (Checked by)</label>
                    <input type="text" name="nguoi_kt"
                           value="<?= Html::encode($nguoi_kt) ?>" placeholder="Họ và tên..." />
                </div>
            </div>
        </div>

        <button type="submit" class="btn-save">💾 Lưu tháng <?= $thang ?>/<?= $nam ?></button>
    </form>
</div>

<script>
function chkQc(inp, qcMax) {
    inp.classList.remove('bad');
    if (qcMax === null || inp.value === '') return;
    var v = parseFloat(inp.value);
    if (isNaN(v)) return;
    if (qcMax === 0 ? v > 0 : v > qcMax) inp.classList.add('bad');
}
</script>