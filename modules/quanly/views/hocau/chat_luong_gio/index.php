<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\quanly\models\hocau\NkChatLuongGio;

$this->title = 'Nhật ký phân tích hàng ngày — ' . date('d/m/Y', strtotime($ngay));
$tenCa   = $ca == 1 ? 'Ca 1: Từ 07h đến 18h' : 'Ca 2: Từ 19h đến 06h';
$gioList = $ca == 1
    ? [7,8,9,10,11,12,13,14,15,16,17,18]
    : [19,20,21,22,23,0,1,2,3,4,5,6];

// Gom dữ liệu đã có theo giờ
$dataByGio = [];
foreach ($lichSu as $r) {
    $h = (int)date('H', strtotime($r->thoi_gian));
    $dataByGio[$h] = $r;
}

// Tất cả fields theo thứ tự cột chuẩn UI
$allFields = [
    'ns_ph','ns_ntu',
    'nt_ph','nt_ntu',
    'nl1_ph','nl1_ntu',
    'nl2_ph','nl2_ntu',
    'clo_du',
    'ns_clo_nong_do','nt_clo_nong_do','nc_clo_cham','pac_cham',
    'nt_do_mau','ns_do_mau',
    'ns_do_kiem','nt_do_kiem',
    'ns_do_cung','nt_do_cung',
    'ns_clorua','nt_clorua',
    'ngoai_ho_ph','ngoai_ho_ntu',
    'muong_pu_thu_hoi','muong_lang_nl1','muong_pu_ns','dau_be_ns',
    'ho_xi_phong_1_ntu','ho_xi_phong_2_ntu',
    'pac_ty_trong',
];

// Tính BQ cho TẤT CẢ fields — PHP 7 compatible
$bq = [];
foreach ($allFields as $f) {
    $sum = 0; $cnt = 0;
    foreach ($gioList as $g) {
        $r = isset($dataByGio[$g]) ? $dataByGio[$g] : null;
        if ($r !== null && $r->$f !== null) {
            $sum += (float)$r->$f;
            $cnt++;
        }
    }
    $bq[$f] = $cnt > 0 ? round($sum / $cnt, 2) : null;
}

$steps = [
    'ns_ph'=>'0.01','ns_ntu'=>'0.001',
    'nt_ph'=>'0.01','nt_ntu'=>'0.1',
    'nl1_ph'=>'0.01','nl1_ntu'=>'0.001',
    'nl2_ph'=>'0.01','nl2_ntu'=>'0.01',
    'clo_du'=>'0.01',
    'ns_clo_nong_do'=>'0.01','nt_clo_nong_do'=>'0.01',
    'nc_clo_cham'=>'0.01','pac_cham'=>'0.1',
    'nt_do_mau'=>'1','ns_do_mau'=>'1',
    'ns_do_kiem'=>'1','nt_do_kiem'=>'1',
    'ns_do_cung'=>'1','nt_do_cung'=>'1',
    'ns_clorua'=>'1','nt_clorua'=>'1',
    'ngoai_ho_ph'=>'0.01','ngoai_ho_ntu'=>'0.01',
    'muong_pu_thu_hoi'=>'0.01','muong_lang_nl1'=>'0.01',
    'muong_pu_ns'=>'0.01','dau_be_ns'=>'0.01',
    'ho_xi_phong_1_ntu'=>'0.001','ho_xi_phong_2_ntu'=>'0.001',
    'pac_ty_trong'=>'0.001',
];
$qcvnFields = ['ns_ph','ns_ntu','clo_du','nl1_ntu','nl2_ntu','ns_do_mau','ns_do_cung','ns_clorua'];

// Jar test
$jPac  = $jarTest ? $jarTest->getPacLieuArr() : array_fill(0, 6, null);
$jNtu  = $jarTest ? $jarTest->getPacNtuArr()  : array_fill(0, 6, null);
$jPh   = $jarTest ? $jarTest->getPacPhArr()   : array_fill(0, 6, null);
$jMin  = $jarTest ? $jarTest->getMinNtuIndex() : -1;
$jGio  = $jarTest ? date('H:i', strtotime($jarTest->gio_thu)) : ($ca == 1 ? '08:00' : '19:00');
$jChon = $jarTest ? $jarTest->lieu_chon : null;

// Khởi tạo các giá trị bảng tính Clo từ Model dòng đầu tiên nếu có
$valMatBanDau    = ($model && $model->clo_mat_ban_dau !== null) ? $model->clo_mat_ban_dau : 0.6;
$valMatTrongBe   = ($model && $model->clo_mat_trong_be !== null) ? $model->clo_mat_trong_be : 0.1;
$valKhoiLuong    = ($model && $model->clo_khoi_luong_cham !== null) ? $model->clo_khoi_luong_cham : 3.0;
$valLlNuocTho    = ($model && $model->clo_ll_nuoc_tho !== null) ? $model->clo_ll_nuoc_tho : 4500.0;
$valCloDuBqNhap  = ($model && $model->clo_du_bq_nhap !== null) ? $model->clo_du_bq_nhap : null;

$nguoiTruc = ($model && $model->nguoi_truc) ? $model->nguoi_truc : '';
$nguoiKt   = ($model && $model->nguoi_kt)   ? $model->nguoi_kt   : '';
?>
<style>
.hn-wrap{max-width:100%;padding:12px 8px}
.hn-nav{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:8px}
.hn-nav a{padding:6px 14px;border-radius:99px;text-decoration:none;font-size:.82rem;background:#f1f5f9;color:#475569}
.hn-nav a.active{background:#3b82f6;color:#fff}
.hn-nav input[type=date]{padding:6px 10px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:.83rem;outline:none;background:#fff;cursor:pointer}
.hn-nav input[type=date]:focus{border-color:#3b82f6}
.qnav-bar{display:flex;gap:5px;flex-wrap:wrap;align-items:center;padding:8px 10px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;margin-bottom:10px}
.qnav-label{font-size:.7rem;font-weight:700;color:#94a3b8;white-space:nowrap;margin-right:2px}
.qnav-btn{padding:5px 10px;border-radius:8px;text-decoration:none;font-size:.75rem;font-weight:500;background:#fff;color:#475569;border:1px solid #e2e8f0;white-space:nowrap;transition:all .12s}
.qnav-btn:hover{border-color:#3b82f6;color:#3b82f6;background:#eff6ff}
.qnav-active{background:#1e3a5f!important;color:#fff!important;border-color:#1e3a5f!important}
.hn-ca-sw{display:flex;gap:8px;margin-bottom:12px}
.hn-ca-sw a{flex:1;text-align:center;padding:9px;border-radius:10px;font-size:.88rem;font-weight:600;text-decoration:none;background:#f1f5f9;color:#475569}
.hn-ca-sw a.active{background:#1e3a5f;color:#fff}
.hn-shortcuts{display:flex;gap:5px;flex-wrap:wrap;padding:10px 12px;background:#f8fafc;border-radius:10px;margin-bottom:12px;align-items:center}
.hn-shortcuts-label{font-size:.72rem;color:#94a3b8;font-weight:600;margin-right:4px;white-space:nowrap}
.hn-shortcut-btn{padding:4px 10px;border-radius:99px;border:1.5px solid #e2e8f0;background:#fff;color:#475569;font-size:.75rem;font-weight:600;cursor:pointer;transition:all .15s}
.hn-shortcut-btn:hover{border-color:#3b82f6;color:#3b82f6;background:#eff6ff}
.hn-shortcut-btn.filled{background:#dcfce7;border-color:#16a34a;color:#166534}
.hn-shortcut-btn.current{background:#3b82f6;border-color:#3b82f6;color:#fff}
.hn-ca-header{background:#1e3a5f;color:#fff;border-radius:10px 10px 0 0;padding:10px 16px;font-weight:700;font-size:.9rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:6px}
.hn-card{border:1px solid #e2e8f0;border-radius:0 0 10px 10px;overflow-x:auto;margin-bottom:16px}
.hn-tbl{width:100%;border-collapse:collapse;font-size:.72rem}
.hn-tbl th{padding:4px;text-align:center;border:1px solid #cbd5e1;white-space:normal;font-weight:600;line-height:1.2}
.hn-tbl th.g0{background:#1e3a5f;color:#fff}
.hn-tbl th.g1{background:#2d6099;color:#fff}
.hn-tbl th.g2{background:#0369a1;color:#fff}
.hn-tbl th.g3{background:#047857;color:#fff}
.hn-tbl th.g4{background:#065f46;color:#fff}
.hn-tbl th.g5{background:#92400e;color:#fff}
.hn-tbl th.g6{background:#6b21a8;color:#fff}
.hn-tbl th.g7{background:#be185d;color:#fff}
.hn-tbl th.g8{background:#155e75;color:#fff}
.hn-tbl th.g9{background:#166534;color:#fff}
.hn-tbl td{padding:2px 3px;border:1px solid #e2e8f0;text-align:center}
.hn-tbl tr.bq-row td{background:#fef9c3;font-weight:700}
.hn-tbl td.gio-cell{font-weight:600;background:#f8fafc;padding:3px 6px;white-space:nowrap}
.hn-tbl input[type=number]{width:52px;padding:2px 3px;border:1px solid #d1d5db;border-radius:3px;font-size:.72rem;text-align:center;-webkit-appearance:none;background:transparent}
.hn-tbl input:focus{border-color:#3b82f6;outline:none;background:#fff}
.hn-tbl input.bad{border-color:#ef4444!important;background:#fef2f2}
.hn-tbl input.warn{border-color:#f59e0b!important;background:#fffbeb}
.jar-card{border:1px solid #e2e8f0;border-radius:10px;padding:14px;margin-bottom:16px}
.jar-title{font-size:.85rem;font-weight:700;color:#1e3a5f;margin-bottom:10px}
.jar-tbl{border-collapse:collapse;font-size:.82rem}
.jar-tbl th{background:#f8fafc;padding:6px 10px;border:1px solid #e2e8f0;text-align:center;font-weight:600}
.jar-tbl td{padding:5px 7px;border:1px solid #e2e8f0;text-align:center}
.jar-tbl td.rl{font-weight:600;color:#475569;text-align:left;background:#f8fafc;min-width:110px}
.jar-tbl input[type=number]{width:62px;padding:3px 5px;border:1px solid #d1d5db;border-radius:4px;font-size:.82rem;text-align:center;-webkit-appearance:none}
.jar-tbl input:focus{border-color:#3b82f6;outline:none}
.jar-active{background:#eff6ff!important}
.jar-active input{border-color:#3b82f6}

/* CSS Bảng tính Clo mới */
.cl-calc-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px}
.cl-calc-table{width:100%;border-collapse:collapse;font-size:.82rem}
.cl-calc-table td{padding:6px 10px;border:1px solid #e2e8f0;text-align:left}
.cl-calc-table input[type=number]{width:80px;padding:4px 6px;border:1px solid #cbd5e1;border-radius:4px;text-align:center;font-weight:600}
.cl-calc-output{font-weight:700;color:#1e3a5f;background:#f1f5f9;text-align:center!important;font-size:.88rem}

.nguoi-card{border:1px solid #e2e8f0;border-radius:10px;padding:14px;margin-bottom:16px}
.ng-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.nk-field label{font-size:.78rem;color:#64748b;margin-bottom:3px;display:block}
.nk-field input{width:100%;padding:8px 10px;border:1.5px solid #e2e8f0;border-radius:7px;font-size:.9rem;outline:none;box-sizing:border-box}
.nk-field input:focus{border-color:#3b82f6}
.btn-save{width:100%;padding:13px;background:#3b82f6;color:#fff;border:none;border-radius:10px;font-size:1rem;font-weight:600;cursor:pointer}
.flash-ok{background:#dcfce7;color:#166534;padding:9px 14px;margin-bottom:0;font-size:.85rem}
@media(max-width:768px){.cl-calc-grid{grid-template-columns:1fr}.ng-grid{grid-template-columns:1fr}}

/* ===== PRINT ===== */
.print-header{display:none}
@media print{
    .hn-nav,.qnav-bar,.hn-ca-sw,.hn-shortcuts,.flash-ok,button[type=submit],input[type=submit]{display:none!important}
    .print-header{display:block!important;margin-bottom:6px}
    .hn-wrap{padding:0}
    .hn-ca-header{border-radius:0;margin-bottom:4px}
    .hn-card{border-radius:0;overflow:visible!important}
    .cl-calc-grid{grid-template-columns:1fr 1fr}
    .nguoi-card{border-radius:0}
    *{font-size:7pt!important}
    .hn-tbl{font-size:6.5pt!important}
    th,td{padding:2px 3px!important}
    input[type=number],input[type=text]{width:40px!important;font-size:6pt!important;padding:1px!important;border:none!important;background:transparent!important}
    @page{size:A4 landscape;margin:8mm}
    *{-webkit-print-color-adjust:exact!important;print-color-adjust:exact!important}
}
</style>

<div class="hn-wrap">
<!-- PRINT HEADER -->
<div class="print-header">
<table style="width:100%;border:1.5px solid #000;border-collapse:collapse;margin-bottom:6px;font-family:Arial,sans-serif;">
    <tr>
        <td rowspan="3" style="width:14%;border:1px solid #000;padding:4px;text-align:center;vertical-align:middle;">
            <img src="<?= Yii::$app->homeUrl ?>images/logo_tuanloc.jpg" style="height:55px;width:auto;" /><br>
            <span style="font-size:6.5pt;font-weight:700;color:#1e3a5f;">CONG TY CP CAP NUOC<br>HO CAU MOI</span>
        </td>
        <td rowspan="3" style="border:1px solid #000;padding:4px;text-align:center;vertical-align:middle;">
            <div style="font-size:13pt;font-weight:700;letter-spacing:.02em;">NHAT KY PHAN TICH HANG NGAY</div>
            <div style="font-size:8pt;margin-top:3px;color:#334155;"><?= $tenCa ?> — <?= date('d/m/Y', strtotime($ngay)) ?></div>
        </td>
        <td style="border:1px solid #000;padding:3px 8px;font-size:8pt;width:18%;">Ma so:</td>
        <td style="border:1px solid #000;padding:3px 8px;font-size:8pt;width:18%;"></td>
    </tr>
    <tr>
        <td style="border:1px solid #000;padding:3px 8px;font-size:8pt;">Nguoi truc ca:</td>
        <td style="border:1px solid #000;padding:3px 8px;font-size:8pt;"><?= Html::encode($nguoiTruc) ?></td>
    </tr>
    <tr>
        <td style="border:1px solid #000;padding:3px 8px;font-size:8pt;">Nguoi kiem tra:</td>
        <td style="border:1px solid #000;padding:3px 8px;font-size:8pt;"><?= Html::encode($nguoiKt) ?></td>
    </tr>
</table>
</div>

    <div class="hn-nav">
        <?php for ($i = 2; $i >= 0; $i--):
            $d   = date('Y-m-d', strtotime('-' . $i . ' days'));
            $lbl = $i == 0 ? 'Hôm nay' : ($i == 1 ? 'Hôm qua' : date('d/m', strtotime('-' . $i . ' days')));
        ?>
        <a href="<?= Url::to(['nhat-ky/chat-luong-gio', 'ngay' => $d, 'ca' => $ca]) ?>"
           class="<?= $d == $ngay ? 'active' : '' ?>"><?= $lbl ?></a>
        <?php endfor; ?>
        <input type="date" id="hn-date-pick" value="<?= Html::encode($ngay) ?>" max="<?= date('Y-m-d') ?>" onchange="window.location.href='<?= Url::to(['nhat-ky/chat-luong-gio','ca'=>$ca]) ?>&ngay='+this.value" />
    </div>

    <div class="qnav-bar">
        <span class="qnav-label">✏ Nhập liệu:</span>
        <a href="<?= Url::to(['nhat-ky/chat-luong-gio','ca'=>1,'ngay'=>$ngay]) ?>" class="qnav-btn <?= (strpos(Yii::$app->request->url,'chat-luong-gio')!==false && $ca==1)?'qnav-active':'' ?>">🧪 HN Ngày</a>
        <a href="<?= Url::to(['nhat-ky/chat-luong-gio','ca'=>2,'ngay'=>$ngay]) ?>" class="qnav-btn <?= (strpos(Yii::$app->request->url,'chat-luong-gio')!==false && $ca==2)?'qnav-active':'' ?>">🌙 HN Đêm</a>
        <a href="<?= Url::to(['nhat-ky/giao-ca','ca'=>1,'ngay'=>$ngay]) ?>" class="qnav-btn">☀️ VH Ngày</a>
        <a href="<?= Url::to(['nhat-ky/giao-ca','ca'=>2,'ngay'=>$ngay]) ?>" class="qnav-btn">🌙 VH Đêm</a>
        <a href="<?= Url::to(['nhat-ky/nuoc-thai-sh','ngay'=>$ngay]) ?>" class="qnav-btn">🧫 Nước thải</a>
        <a href="<?= Url::to(['nhat-ky/cln-hang-ngay','ngay'=>$ngay]) ?>" class="qnav-btn">📋 CLN ngày</a>
        <a href="<?= Url::to(['nhat-ky/phan-tich-tuan']) ?>" class="qnav-btn">📊 CL Tuần</a>
    </div>

    <div class="hn-ca-sw">
        <a href="<?= Url::to(['nhat-ky/chat-luong-gio', 'ngay' => $ngay, 'ca' => 1]) ?>" class="<?= $ca == 1 ? 'active' : '' ?>">☀️ Ca 1: 07h–18h</a>
        <a href="<?= Url::to(['nhat-ky/chat-luong-gio', 'ngay' => $ngay, 'ca' => 2]) ?>" class="<?= $ca == 2 ? 'active' : '' ?>">🌙 Ca 2: 19h–06h</a>
    </div>

    <div class="hn-shortcuts" id="shortcuts-bar">
        <span class="hn-shortcuts-label">Đến giờ:</span>
        <?php foreach ($gioList as $gio):
            $gLbl = ($gio === 0 ? '24' : $gio) . 'h';
            $hasFilled = isset($dataByGio[$gio]);
        ?>
        <button type="button" class="hn-shortcut-btn <?= $hasFilled ? 'filled' : '' ?>" onclick="jumpToGio(<?= $gio ?>)" id="sc-<?= $gio ?>"><?= $gLbl ?></button>
        <?php endforeach; ?>
        <button type="button" class="hn-shortcut-btn" style="margin-left:auto;background:#f8fafc;color:#3b82f6;border-color:#3b82f6;" onclick="jumpToJar()">🧪 Jar test</button>
    </div>

    <div class="hn-ca-header">
        <span>NHẬT KÝ PHÂN TÍCH HÀNG NGÀY — <?= $tenCa ?> — <?= date('d/m/Y', strtotime($ngay)) ?></span>
        <span style="font-size:.82rem;opacity:.85;">Người trực: <?= Html::encode($nguoiTruc ? $nguoiTruc : '—') ?></span>
    </div>

    <?php if (Yii::$app->session->hasFlash('success')): 
        $okFlash = Yii::$app->session->getFlash('success');
        $okText  = is_array($okFlash) ? implode('<br>✓ ', $okFlash) : $okFlash;
    ?>
    <div class="flash-ok">✓ <?= $okText ?></div>
    <?php endif; ?>
    
    <?php if (Yii::$app->session->hasFlash('error')): 
        $errFlash = Yii::$app->session->getFlash('error');
        $errText  = is_array($errFlash) ? implode('<br>❌ ', $errFlash) : $errFlash;
    ?>
    <div class="flash-ok" style="background:#fef2f2;color:#991b1b;border:1px solid #fca5a5;margin-top:8px;">❌ <?= $errText ?></div>
    <?php endif; ?>

    <form method="post" id="form-hn">
        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
        <?= Html::hiddenInput('ca_submit', $ca) ?>
        <?= Html::hiddenInput('ngay_submit', $ngay) ?>

        <div class="hn-card">
        <table class="hn-tbl">
            <thead>
                <tr>
                    <th class="g0" rowspan="3">Giờ</th>
                    <th class="g1" colspan="2">Nước Sạch<br><small>NS / TW</small></th>
                    <th class="g2" colspan="2">Nước Thô<br><small>NT / RW</small></th>
                    <th class="g3" colspan="2">Nước Lắng 1<br><small>NL1</small></th>
                    <th class="g4" colspan="2">Nước Lắng 2<br><small>NL2</small></th>
                    <th class="g5" rowspan="3">Clor dư<br>TB/PS<br><small>0.2–1.0<br>mg/L</small></th>
                    <th class="g6" colspan="4">Clo / PAC châm</th>
                    <th class="g7" colspan="2">Độ màu<br><small>Pt-Co</small></th>
                    <th class="g2" colspan="2">Độ kiềm<br><small>CaCO3</small></th>
                    <th class="g2" colspan="2">Độ cứng<br><small>CaCO3 &lt;300</small></th>
                    <th class="g2" colspan="2">Clorua<br><small>mg/L &lt;250</small></th>
                    <th class="g8" colspan="2">Nước ngoài hồ</th>
                    <th class="g9" colspan="6">Mương / Bể — Clor dư Residual Cl</th>
                    <th class="g0" rowspan="3">PAC Pha<br>Tỷ trọng</th>
                </tr>
                <tr>
                    <th class="g1" rowspan="2">pH<br><small>6.5–8.5</small></th>
                    <th class="g1" rowspan="2">NTU<br><small>&lt;0.4</small></th>
                    <th class="g2" rowspan="2">pH</th>
                    <th class="g2" rowspan="2">NTU</th>
                    <th class="g3" rowspan="2">pH</th>
                    <th class="g3" rowspan="2">NTU<br><small>&lt;5</small></th>
                    <th class="g4" rowspan="2">pH</th>
                    <th class="g4" rowspan="2">NTU<br><small>&lt;5</small></th>
                    <th class="g6" rowspan="2">NC<br>nồng độ<br>clo (ppm)</th>
                    <th class="g6" rowspan="2">NT<br>nồng độ<br>clo (ppm)</th>
                    <th class="g6" rowspan="2">Clo châm<br>NC (ppm)</th>
                    <th class="g6" rowspan="2">PAC châm<br>(mg/L)</th>
                    <th class="g7" rowspan="2">NT RW</th>
                    <th class="g7" rowspan="2">NS<br><small>&lt;15</small></th>
                    <th class="g2" rowspan="2">NS</th>
                    <th class="g2" rowspan="2">NT</th>
                    <th class="g2" rowspan="2">NS<br><small>&lt;300</small></th>
                    <th class="g2" rowspan="2">NT</th>
                    <th class="g2" rowspan="2">NS<br><small>&lt;250</small></th>
                    <th class="g2" rowspan="2">NT</th>
                    <th class="g8" rowspan="2">pH</th>
                    <th class="g8" rowspan="2">NTU</th>
                    <th class="g9" rowspan="2">Mương PƯ<br>(Thu hồi)</th>
                    <th class="g9" rowspan="2">Mương<br>lắng NL1</th>
                    <th class="g9" rowspan="2">Mương<br>PƯ NS</th>
                    <th class="g9" rowspan="2">Đầu bể NS</th>
                    <th class="g9" rowspan="2">Hố xi<br>phông 1<br>NTU</th>
                    <th class="g9" rowspan="2">Hố xi<br>phông 2<br>NTU</th>
                </tr>
                <tr></tr>
            </thead>
            <tbody>
            <?php foreach ($gioList as $gio):
                $rec  = isset($dataByGio[$gio]) ? $dataByGio[$gio] : null;
                $gLbl = ($gio === 0 ? '24' : $gio) . 'h';
            ?>
            <tr id="row-<?= $gio ?>">
                <td class="gio-cell"><?= $gLbl ?></td>
                <?php foreach ($allFields as $f):
                    $v   = ($rec !== null && $rec->$f !== null) ? $rec->$f : null;
                    $cls = '';
                    if ($v !== null && in_array($f, $qcvnFields) && isset(NkChatLuongGio::QCVN[$f])) {
                        $q = NkChatLuongGio::QCVN[$f];
                        if ((float)$v < $q['min'] || (float)$v > $q['max']) $cls = 'bad';
                    }
                ?>
                <td>
                    <input type="number" name="rows[<?= $gio ?>][<?= $f ?>]" value="<?= $v !== null ? Html::encode($v) : '' ?>" step="<?= isset($steps[$f]) ? $steps[$f] : '0.01' ?>" inputmode="decimal" <?= $cls ? 'class="' . $cls . '"' : '' ?> onchange="checkVal(this,'<?= $f ?>')" />
                </td>
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
            <tr class="bq-row">
                <td class="gio-cell">BQ</td>
                <?php foreach ($allFields as $f): ?>
                <td id="bq-<?= $f ?>"><?= (isset($bq[$f]) && $bq[$f] !== null) ? $bq[$f] : '—' ?></td>
                <?php endforeach; ?>
            </tr>
            </tbody>
        </table>
        </div>

        <div class="cl-calc-grid">
            <div class="jar-card" style="margin-bottom:0;">
                <div class="jar-title">🧪 Jar Test PAC — Giờ: <input type="time" name="jar_gio" value="<?= Html::encode($jGio) ?>" style="border:1px solid #e2e8f0;border-radius:5px;padding:3px 7px;font-size:.82rem;" /></div>
                <div style="overflow-x:auto;">
                <table class="jar-tbl">
                    <thead>
                        <tr>
                            <th class="rl"></th>
                            <?php for ($i = 1; $i <= 6; $i++): ?><th><?= $i ?></th><?php endfor; ?>
                            <th style="background:#eff6ff;color:#1e40af;min-width:90px;">Liều chọn</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $jarDef = [
                        ['pac','PAC (mg/L)','0.1',$jPac],
                        ['ntu','Độ đục (NTU)','0.001',$jNtu],
                        ['ph','pH','0.01',$jPh],
                    ];
                    foreach ($jarDef as $jd):
                        list($jkey,$jlabel,$jstep,$jarArr) = $jd;
                    ?>
                    <tr>
                        <td class="rl"><?= $jlabel ?></td>
                        <?php for ($i = 0; $i < 6; $i++):
                            $isMin = ($jkey === 'ntu' && $i === $jMin && $jMin >= 0);
                        ?>
                        <td class="<?= $isMin ? 'jar-active' : '' ?>" id="jc-<?= $i ?>-<?= $jkey ?>">
                            <input type="number" step="<?= $jstep ?>" name="jar_<?= $jkey ?>[<?= $i ?>]" value="<?= (isset($jarArr[$i]) && $jarArr[$i] !== null) ? Html::encode($jarArr[$i]) : '' ?>" inputmode="decimal" onchange="pickJar()" />
                        </td>
                        <?php endfor; ?>
                        <td style="background:#eff6ff;">
                            <?php if ($jkey === 'pac'): ?>
                            <input type="text" name="jar_lieu_chon" id="jar-lieu-chon" value="<?= $jChon !== null ? Html::encode($jChon) : '' ?>" style="width:60px;padding:3px 5px;border:1.5px solid #3b82f6;border-radius:5px;font-weight:700;color:#3b82f6;" inputmode="decimal" /> mg/L
                            <?php else: ?>
                            <span id="jar-prev-<?= $jkey ?>">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            </div>

            <div class="jar-card" style="margin-bottom:0; background:#fff;">
                <div class="jar-title" style="color:#047857;">📈 Bảng Tính Toán Nồng Độ Clo (mg/L)</div>
                <table class="cl-calc-table">
                    <tr>
                        <td>Lượng clo mất đi ban đầu</td>
                        <td><input type="number" step="0.01" name="clo_mat_ban_dau" id="clo_mat_ban_dau" value="<?= Html::encode($valMatBanDau) ?>" oninput="runCloCalculation()" /></td>
                        <td rowspan="4" style="background:#eff6ff; font-weight:600; text-align:center; vertical-align:middle; color:#1e3a5f; width:70px;">SẠCH</td>
                    </tr>
                    <tr>
                        <td>Lượng clo mất trong bể</td>
                        <td><input type="number" step="0.01" name="clo_mat_trong_be" id="clo_mat_trong_be" value="<?= Html::encode($valMatTrongBe) ?>" oninput="runCloCalculation()" /></td>
                    </tr>
                    <tr style="background:#fef9c3;">
                        <td>Lượng Clo dư bình quân (Đo được)</td>
                        <td><input type="number" step="0.01" name="clo_du_bq_nhap" id="inp-clo-du-bq" value="<?= $valCloDuBqNhap !== null ? Html::encode($valCloDuBqNhap) : '' ?>" inputmode="decimal" oninput="runCloCalculation()" style="width:80px;padding:4px 6px;border:1px solid #cbd5e1;border-radius:4px;text-align:center;font-weight:600;" /></td>
                    </tr>
                    <tr style="background:#eff6ff;">
                        <td style="font-weight:600; color:#1e40af;">Nồng độ clo châm nước cấp</td>
                        <td id="out-clo-cham-nc" class="cl-calc-output" style="border:2px solid #3b82f6;">0.00</td>
                    </tr>
                    <tr style="border-top:2px solid #cbd5e1;">
                        <td>Khối lượng Châm (kg/h)</td>
                        <td><input type="number" step="0.1" name="clo_khoi_luong_cham" id="clo_khoi_luong_cham" value="<?= Html::encode($valKhoiLuong) ?>" oninput="runCloCalculation()" /></td>
                        <td rowspan="3" style="background:#f0fdf4; font-weight:600; text-align:center; vertical-align:middle; color:#166534;">THÔ</td>
                    </tr>
                    <tr>
                        <td>LL Nước Thô (m³/h)</td>
                        <td><input type="number" step="1" name="clo_ll_nuoc_tho" id="clo_ll_nuoc_tho" value="<?= Html::encode($valLlNuocTho) ?>" oninput="runCloCalculation()" /></td>
                    </tr>
                    <tr style="background:#ecfdf5;">
                        <td style="font-weight:600; color:#065f46;">Nước thô Nồng độ clo</td>
                        <td id="out-clo-nuoc-tho" class="cl-calc-output">0.00</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="nguoi-card" style="margin-top:16px;">
            <div style="font-size:.85rem;font-weight:700;color:#334155;margin-bottom:12px;">👤 Người thực hiện</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">

                <div style="text-align:center;">
                    <div style="font-size:.78rem;color:#64748b;margin-bottom:4px;">Người trực ca <?= $ca == 1 ? 'sáng' : 'đêm' ?></div>
                    <input type="text" name="nguoi_truc" value="<?= Html::encode($nguoiTruc) ?>"
                           placeholder="Họ và tên..."
                           style="width:100%;padding:6px 10px;border:none;border-bottom:1.5px solid #94a3b8;
                                  border-radius:0;font-size:.9rem;outline:none;text-align:center;
                                  background:transparent;box-sizing:border-box;" />
                    <div style="font-size:.7rem;color:#94a3b8;margin-top:28px;">(Ký, ghi rõ họ tên)</div>
                </div>

                <div style="text-align:center;">
                    <div style="font-size:.78rem;color:#64748b;margin-bottom:4px;">Người kiểm tra (Checked by)</div>
                    <input type="text" name="nguoi_kt" value="<?= Html::encode($nguoiKt) ?>"
                           placeholder="Họ và tên..."
                           style="width:100%;padding:6px 10px;border:none;border-bottom:1.5px solid #94a3b8;
                                  border-radius:0;font-size:.9rem;outline:none;text-align:center;
                                  background:transparent;box-sizing:border-box;" />
                    <div style="font-size:.7rem;color:#94a3b8;margin-top:28px;">(Ký, ghi rõ họ tên)</div>
                </div>

            </div>
        </div>

        <button type="submit" class="btn-save">💾 Lưu nhật ký phân tích & Bảng tính Clo</button>
    </form>
</div>

<script>
var QCVN_JS = {
    ns_ph:{min:6.5,max:8.5},ns_ntu:{min:0,max:0.4},
    nl1_ntu:{min:0,max:5.0},nl2_ntu:{min:0,max:5.0}, // Sửa thành 5.0
    clo_du:{min:0.2,max:1.0},ns_do_mau:{min:0,max:15.0},
    ns_do_cung:{min:0,max:300.0}, ns_clorua:{min:0,max:250.0}
};
var GIO_LIST  = <?= json_encode($gioList) ?>;
var BQ_FIELDS = ['ns_ph','ns_ntu','nt_ph','nt_ntu','nl1_ph','nl1_ntu','nl2_ph','nl2_ntu',
                 'clo_du','ns_clo_nong_do','nt_clo_nong_do','nc_clo_cham','pac_cham',
                 'nt_do_mau','ns_do_mau',
                 'ns_do_kiem','nt_do_kiem','ns_do_cung','nt_do_cung','ns_clorua','nt_clorua',
                 'ngoai_ho_ph','ngoai_ho_ntu','muong_pu_thu_hoi','muong_lang_nl1',
                 'muong_pu_ns','dau_be_ns','ho_xi_phong_1_ntu','ho_xi_phong_2_ntu'];

function checkVal(inp, field) {
    inp.classList.remove('bad','warn');
    if (!QCVN_JS[field] || inp.value === '') return;
    var v = parseFloat(inp.value), q = QCVN_JS[field];
    if (v < q.min || v > q.max) { inp.classList.add('bad'); }
    else {
        var r = q.max - q.min;
        if (r > 0 && (v < q.min + r*0.05 || v > q.max - r*0.05)) inp.classList.add('warn');
    }
    calcBQ();
    runCloCalculation(); // Tính lại Clo khi có giờ thay đổi
}

function calcBQ() {
    for (var fi = 0; fi < BQ_FIELDS.length; fi++) {
        var f = BQ_FIELDS[fi], sum = 0, cnt = 0;
        for (var gi = 0; gi < GIO_LIST.length; gi++) {
            var inp = document.querySelector('input[name="rows['+GIO_LIST[gi]+']['+f+']"]');
            var v = parseFloat(inp ? inp.value : '');
            if (!isNaN(v)) { sum += v; cnt++; }
        }
        var el = document.getElementById('bq-'+f);
        if (el) el.textContent = cnt > 0 ? (sum/cnt).toFixed(2) : '—';
    }
}

// HÀM TÍNH TOÁN CLO TỰ ĐỘNG THEO ẢNH MẪU
function runCloCalculation() {
    var matBanDau   = parseFloat(document.getElementById('clo_mat_ban_dau').value) || 0;
    var matTrongBe  = parseFloat(document.getElementById('clo_mat_trong_be').value) || 0;
    var khoiLuong   = parseFloat(document.getElementById('clo_khoi_luong_cham').value) || 0;
    var llNuocTho   = parseFloat(document.getElementById('clo_ll_nuoc_tho').value) || 0;
    
    // Lấy Clo dư BQ từ input nhập tay
    var cloDuBqInp = document.getElementById('inp-clo-du-bq');
    var cloDuBq    = cloDuBqInp ? (parseFloat(cloDuBqInp.value) || 0) : 0;

    // 1. Nước thô Nồng độ clo = (Khối lượng châm / LL Nước thô) * 1000
    var cloNuocTho = 0;
    if (llNuocTho > 0) {
        cloNuocTho = (khoiLuong / llNuocTho) * 1000;
    }
    document.getElementById('out-clo-nuoc-tho').textContent = cloNuocTho.toFixed(2);

    // 2. Nồng độ clo châm nước cấp = Clo dư bình quân + mất ban đầu + mất trong bể
    var cloChamNc = cloDuBq + matBanDau + matTrongBe;
    document.getElementById('out-clo-cham-nc').textContent = cloChamNc.toFixed(2);
}

var _jarLieuUserEdited = <?= ($jChon !== null && $jChon !== '') ? 'true' : 'false' ?>;
function pickJar() {
    var minNtu = Infinity, minIdx = -1;
    for (var i = 0; i < 6; i++) {
        var inp = document.querySelector('input[name="jar_ntu['+i+']"]');
        var v = parseFloat(inp ? inp.value : '');
        if (!isNaN(v) && v < minNtu) { minNtu = v; minIdx = i; }
    }
    for (var i = 0; i < 6; i++) {
        ['pac','ntu','ph'].forEach(function(k) {
            var cell = document.getElementById('jc-'+i+'-'+k);
            if (!cell) return;
            if (i === minIdx) cell.classList.add('jar-active');
            else cell.classList.remove('jar-active');
        });
    }
    if (minIdx >= 0) {
        var pac = document.querySelector('input[name="jar_pac['+minIdx+']"]');
        var chon = document.getElementById('jar-lieu-chon');
        if (pac && pac.value !== "" && chon && !_jarLieuUserEdited) chon.value = pac.value;
        var pvNtu = document.getElementById('jar-prev-ntu');
        var pvPh  = document.getElementById('jar-prev-ph');
        var nInp  = document.querySelector('input[name="jar_ntu['+minIdx+']"]');
        var pInp  = document.querySelector('input[name="jar_ph['+minIdx+']"]');
        if (pvNtu && nInp) pvNtu.textContent = nInp.value || '—';
        if (pvPh  && pInp) pvPh.textContent  = pInp.value || '—';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    pickJar();
    calcBQ();
    runCloCalculation();

    var chonInp = document.getElementById('jar-lieu-chon');
    if (chonInp) {
        chonInp.addEventListener('input', function() { _jarLieuUserEdited = true; });
        chonInp.addEventListener('change', function() {
            if (this.value === '') _jarLieuUserEdited = false;
        });
    }

    var nowH = new Date().getHours();
    var curBtn = document.getElementById('sc-' + nowH);
    if (curBtn) curBtn.classList.add('current');

    document.querySelectorAll('.hn-tbl input[type=number]').forEach(function(inp) {
        inp.addEventListener('change', function() {
            var match = this.name.match(/rows\[(\d+)\]/);
            if (!match) return;
            var gio = parseInt(match[1]);
            var btn = document.getElementById('sc-' + gio);
            if (!btn) return;
            var row = document.querySelectorAll('input[name^="rows['+gio+']"]');
            var hasVal = false;
            row.forEach(function(r) { if (r.value !== '') hasVal = true; });
            if (hasVal) btn.classList.add('filled');
            else btn.classList.remove('filled');
        });
    });
});

function jumpToGio(gio) {
    var row = document.getElementById('row-' + gio);
    if (!row) return;
    row.scrollIntoView({behavior:'smooth', block:'center'});
    var firstInp = row.querySelector('input[type=number]');
    if (firstInp) { setTimeout(function() { firstInp.focus(); }, 400); }
    row.style.transition = 'background .2s';
    row.style.background = '#eff6ff';
    setTimeout(function() { row.style.background = ''; }, 1500);
}
function jumpToJar() {
    var jarCard = document.querySelector('.jar-card');
    if (jarCard) jarCard.scrollIntoView({behavior:'smooth', block:'start'});
}
</script>