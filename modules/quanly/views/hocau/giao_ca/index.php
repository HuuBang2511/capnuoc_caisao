<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$tenCa = $ca==1 ? 'Ca Ngày (07h–18h)' : 'Ca Đêm (19h–06h)';
$this->title = 'Sổ giao ca — ' . $tenCa;
?>
<style>
.gc-wrap { max-width:960px; margin:0 auto; padding:16px; }
.gc-card { border:1px solid #e2e8f0; border-radius:12px; padding:20px; margin-bottom:16px; }
.gc-section { font-size:.8rem; font-weight:700; color:#3b82f6; letter-spacing:.05em;
              text-transform:uppercase; margin:16px 0 10px; padding-bottom:6px;
              border-bottom:2px solid #eff6ff; }
.gc-grid  { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.gc-grid3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; }
.gc-field label { font-size:.8rem; color:#64748b; margin-bottom:4px; display:block; }
.gc-field input, .gc-field textarea, .gc-field select {
    width:100%; padding:10px 12px; border:1.5px solid #e2e8f0;
    border-radius:8px; font-size:.95rem; outline:none; box-sizing:border-box; }
.gc-field input:focus, .gc-field textarea:focus { border-color:#3b82f6; }
.gc-calc { font-size:.85rem; color:#10b981; font-weight:600; margin-top:4px; }
.gc-btn  { width:100%; padding:14px; background:#3b82f6; color:#fff;
           border:none; border-radius:10px; font-size:1rem; font-weight:600;
           cursor:pointer; margin-top:8px; }
.ca-switch { display:flex; gap:8px; margin-bottom:12px; }
.ca-switch a { flex:1; text-align:center; padding:10px; border-radius:10px;
               text-decoration:none; font-weight:600; font-size:.9rem;
               background:#f1f5f9; color:#475569; }
.ca-switch a.active { background:#3b82f6; color:#fff; }
/* Thanh lối tắt dùng chung */
.qnav-bar{display:flex;gap:5px;flex-wrap:wrap;align-items:center;padding:8px 10px;
          background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;margin-bottom:10px}
.qnav-label{font-size:.7rem;font-weight:700;color:#94a3b8;white-space:nowrap;margin-right:2px}
.qnav-btn{padding:5px 10px;border-radius:8px;text-decoration:none;font-size:.75rem;font-weight:500;
          background:#fff;color:#475569;border:1px solid #e2e8f0;white-space:nowrap;transition:all .12s}
.qnav-btn:hover{border-color:#3b82f6;color:#3b82f6;background:#eff6ff}
.qnav-active{background:#1e3a5f!important;color:#fff!important;border-color:#1e3a5f!important}
.gc-day-nav a { padding:6px 14px; border-radius:99px; text-decoration:none; font-size:.83rem;
                background:#f1f5f9; color:#475569; }
.gc-day-nav a.active { background:#1e3a5f; color:#fff; }
.gc-day-nav input[type=date] { padding:6px 10px; border:1.5px solid #e2e8f0; border-radius:10px;
                                font-size:.83rem; outline:none; background:#fff; cursor:pointer; }
.gc-day-nav input[type=date]:focus { border-color:#3b82f6; }
.gc-subsection { font-size:.78rem; font-weight:600; color:#64748b; padding:3px 8px;
                 background:#f8fafc; border-radius:5px; margin:8px 0 6px;
                 border-left:2px solid #94a3b8; }
@media(max-width:576px) {
    .gc-grid { grid-template-columns:1fr; }
    .gc-grid3 { grid-template-columns:1fr; }
    .gc-field input { font-size:16px; }
}

/* ===== PRINT ===== */
.print-header{display:none}
@media print{
    .gc-day-nav,.qnav-bar,.ca-switch,.flash-ok,.gc-btn,button[type=submit]{display:none!important}
    .print-header{display:block!important;margin-bottom:8px}

    .gc-wrap{padding:0!important;max-width:100%!important}
    .gc-card{border:1px solid #ccc!important;border-radius:0!important;padding:8px!important;margin-bottom:6px!important}
    .gc-field input,.gc-field textarea{border:1px solid #ccc!important;padding:3px 5px!important;font-size:8pt!important;border-radius:0!important}
    .gc-field label{font-size:7.5pt!important;color:#333!important}
    .gc-section{font-size:7.5pt!important;margin:6px 0 4px!important;padding-bottom:2px!important;border-bottom:1px solid #ddd!important}
    .gc-subsection{font-size:7pt!important;padding:1px 4px!important;margin:3px 0!important}
    .gc-calc{font-size:7pt!important;margin-top:1px!important}
    .gc-grid{gap:6px!important}.gc-grid3{gap:6px!important}
    textarea{height:24px!important;resize:none!important}
    @page{size:A4 portrait;margin:8mm}
    *{-webkit-print-color-adjust:exact!important;print-color-adjust:exact!important}
}
</style>

<div class="gc-wrap">
<!-- PRINT HEADER -->
<div class="print-header">
<table style="width:100%;border:1.5px solid #000;border-collapse:collapse;margin-bottom:6px;font-family:Arial,sans-serif;">
    <tr>
        <td rowspan="3" style="width:14%;border:1px solid #000;padding:4px;text-align:center;vertical-align:middle;">
            <img src="<?= Yii::$app->homeUrl ?>images/logo_tuanloc.jpg" style="height:55px;width:auto;" /><br>
            <span style="font-size:6.5pt;font-weight:700;color:#1e3a5f;">CONG TY CP CAP NUOC<br>HO CAU MOI</span>
        </td>
        <td rowspan="3" style="border:1px solid #000;padding:4px;text-align:center;vertical-align:middle;">
            <div style="font-size:13pt;font-weight:700;letter-spacing:.02em;">SO GIAO CA</div>
            <div style="font-size:8pt;margin-top:3px;color:#334155;"><?= date('d/m/Y', strtotime($ngay)) ?> &mdash; <?= $tenCa ?></div>
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


    <!-- Thanh chọn ngày -->
    <div class="gc-day-nav">
        <?php for ($i = 2; $i >= 0; $i--):
            $d   = date('Y-m-d', strtotime('-' . $i . ' days'));
            $lbl = $i == 0 ? 'Hôm nay' : ($i == 1 ? 'Hôm qua' : date('d/m', strtotime('-' . $i . ' days')));
        ?>
        <a href="<?= Url::to(['nhat-ky/giao-ca', 'ngay' => $d, 'ca' => $ca]) ?>"
           class="<?= $d == $ngay ? 'active' : '' ?>"><?= $lbl ?></a>
        <?php endfor; ?>
        <input type="date" id="gc-date-pick"
               value="<?= Html::encode($ngay) ?>"
               max="<?= date('Y-m-d') ?>"
               onchange="window.location.href='<?= Url::to(['nhat-ky/giao-ca','ca'=>$ca]) ?>&ngay='+this.value" />
    </div>

    <!-- Thanh lối tắt nhập liệu -->
    <div class="qnav-bar">
        <span class="qnav-label">✏ Nhập liệu:</span>
        <a href="<?= Url::to(['nhat-ky/chat-luong-gio','ca'=>1,'ngay'=>$ngay]) ?>"
           class="qnav-btn">🧪 HN Ngày</a>
        <a href="<?= Url::to(['nhat-ky/chat-luong-gio','ca'=>2,'ngay'=>$ngay]) ?>"
           class="qnav-btn">🌙 HN Đêm</a>
        <a href="<?= Url::to(['nhat-ky/giao-ca','ca'=>1,'ngay'=>$ngay]) ?>"
           class="qnav-btn <?= $ca==1?'qnav-active':'' ?>">☀️ VH Ngày</a>
        <a href="<?= Url::to(['nhat-ky/giao-ca','ca'=>2,'ngay'=>$ngay]) ?>"
           class="qnav-btn <?= $ca==2?'qnav-active':'' ?>">🌙 VH Đêm</a>
        <a href="<?= Url::to(['nhat-ky/nuoc-thai-sh','ngay'=>$ngay]) ?>"
           class="qnav-btn">🧫 Nước thải</a>
        <a href="<?= Url::to(['nhat-ky/cln-hang-ngay','ngay'=>$ngay]) ?>"
           class="qnav-btn">📋 CLN ngày</a>
        <a href="<?= Url::to(['nhat-ky/phan-tich-tuan']) ?>"
           class="qnav-btn">📊 CL Tuần</a>
    </div>

    <div class="ca-switch">
        <a href="<?= Url::to(['nhat-ky/giao-ca','ngay'=>$ngay,'ca'=>1]) ?>"
           class="<?= $ca==1?'active':'' ?>">☀️ Ca Ngày</a>
        <a href="<?= Url::to(['nhat-ky/giao-ca','ngay'=>$ngay,'ca'=>2]) ?>"
           class="<?= $ca==2?'active':'' ?>">🌙 Ca Đêm</a>
    </div>

    <div class="gc-card">
        <div style="font-size:1rem;font-weight:700;margin-bottom:16px;">
            📋 Sổ giao ca — <?= date('d/m/Y', strtotime($ngay)) ?> — <?= $tenCa ?>
        </div>

        <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div style="background:#dcfce7;color:#166534;padding:10px 14px;border-radius:8px;margin-bottom:12px;font-size:.88rem;">
            ✓ <?= Yii::$app->session->getFlash('success') ?>
        </div>
        <?php endif; ?>

        <?php if (Yii::$app->session->hasFlash('warning')): ?>
        <div style="background:#fffbeb;color:#92400e;padding:10px 14px;border-radius:8px;margin-bottom:12px;font-size:.88rem;border:1px solid #fde68a;">
            ⚠ <?= Yii::$app->session->getFlash('warning') ?>
        </div>
        <?php endif; ?>

        <?php $form = ActiveForm::begin(['enableClientValidation'=>false]) ?>
        <?= Html::hiddenInput('NkGiaoCa[ngay]', $ngay) ?>
        <?= Html::hiddenInput('NkGiaoCa[ca]', $ca) ?>

        <!-- 1. KHỐI LƯỢNG NƯỚC -->
        <div class="gc-section">1. Khối lượng nước (m³)</div>

        <div class="gc-subsection">Nước cấp (nước sạch)</div>
        <div class="gc-grid" style="margin-bottom:8px;">
            <?php foreach ([
                ['nuoc_cap_dau',  'Nước cấp — Đầu ca'],
                ['nuoc_cap_cuoi', 'Nước cấp — Cuối ca'],
            ] as [$f,$lb]): ?>
            <div class="gc-field">
                <label><?= $lb ?></label>
                <input type="number" name="NkGiaoCa[<?= $f ?>]"
                       value="<?= Html::encode($model->$f) ?>"
                       step="0.1" inputmode="decimal"
                       onchange="calcNuoc()" id="<?= $f ?>" />
            </div>
            <?php endforeach; ?>
        </div>
        <div class="gc-calc" id="calc-nuoc-cap"></div>

        <div class="gc-subsection" style="margin-top:10px;">Nước thô (trạm bơm chính)</div>
        <div class="gc-grid" style="margin-bottom:8px;">
            <?php foreach ([
                ['nuoc_tho_dau',  'Nước thô — Đầu ca'],
                ['nuoc_tho_cuoi', 'Nước thô — Cuối ca'],
            ] as [$f,$lb]): ?>
            <div class="gc-field">
                <label><?= $lb ?></label>
                <input type="number" name="NkGiaoCa[<?= $f ?>]"
                       value="<?= Html::encode($model->$f) ?>"
                       step="0.1" inputmode="decimal"
                       onchange="calcNuoc()" id="<?= $f ?>" />
            </div>
            <?php endforeach; ?>
        </div>
        <div class="gc-calc" id="calc-nuoc-tho"></div>

        <!-- 2. THIẾT BỊ -->
        <div class="gc-section">2. Thiết bị hoạt động</div>
        <div class="gc-grid">
            <?php foreach ([
                ['bom_nt_chay',  'Bơm NT (A/B/C/D)', 'VD: A,C'],
                ['bom_th_chay',  'Bơm TH (A/B)',      'VD: A'],
                ['bom_khi_chay', 'Bơm khí (A/B)',     'VD: A,B'],
            ] as [$f,$lb,$ph]): ?>
            <div class="gc-field">
                <label><?= $lb ?></label>
                <input type="text" name="NkGiaoCa[<?= $f ?>]"
                       value="<?= Html::encode($model->$f) ?>"
                       placeholder="<?= $ph ?>" />
            </div>
            <?php endforeach; ?>
        </div>

        <!-- 3. ĐIỆN -->
        <div class="gc-section">3. Điện (KWh)</div>

        <div class="gc-subsection">Nhà máy</div>
        <div class="gc-grid" style="margin-bottom:8px;">
            <?php foreach ([
                ['dien_nha_may_dau',  'Nhà máy — Đầu ca'],
                ['dien_nha_may_cuoi', 'Nhà máy — Cuối ca'],
            ] as [$f,$lb]): ?>
            <div class="gc-field">
                <label><?= $lb ?></label>
                <input type="number" name="NkGiaoCa[<?= $f ?>]"
                       value="<?= Html::encode($model->$f) ?>"
                       step="1" inputmode="numeric"
                       onchange="calcDien()" id="<?= $f ?>" />
            </div>
            <?php endforeach; ?>
        </div>

        <div class="gc-subsection">Trạm bơm chính</div>
        <div class="gc-grid" style="margin-bottom:8px;">
            <?php foreach ([
                ['dien_tram_bom_dau',  'Trạm bơm — Đầu ca'],
                ['dien_tram_bom_cuoi', 'Trạm bơm — Cuối ca'],
            ] as [$f,$lb]): ?>
            <div class="gc-field">
                <label><?= $lb ?></label>
                <input type="number" name="NkGiaoCa[<?= $f ?>]"
                       value="<?= Html::encode($model->$f) ?>"
                       step="1" inputmode="numeric"
                       onchange="calcDien()" id="<?= $f ?>" />
            </div>
            <?php endforeach; ?>
        </div>

        <div class="gc-subsection">Trạm bơm tăng áp NT5</div>
        <div class="gc-grid" style="margin-bottom:8px;">
            <?php foreach ([
                ['dien_nt5_tang_ap_dau',  'TB Tăng áp NT5 — Đầu ca'],
                ['dien_nt5_tang_ap_cuoi', 'TB Tăng áp NT5 — Cuối ca'],
            ] as [$f,$lb]): ?>
            <div class="gc-field">
                <label><?= $lb ?></label>
                <input type="number" name="NkGiaoCa[<?= $f ?>]"
                       value="<?= Html::encode($model->$f) ?>"
                       step="1" inputmode="numeric"
                       onchange="calcDien()" id="<?= $f ?>" />
            </div>
            <?php endforeach; ?>
        </div>
        <div class="gc-calc" id="calc-dien"></div>

        <!-- 4. CLN ĐẦU/CUỐI CA -->
        <div class="gc-section">4. Kiểm tra chất lượng nước</div>
        <div class="gc-grid">
            <?php
            $qfields = [
                ['ns_ph_dau','pH NS đầu ca','0.01'],  ['ns_ntu_dau','NTU NS đầu ca','0.001'],
                ['clo_du_dau','Clo dư đầu ca','0.01'],
                ['ns_ph_cuoi','pH NS cuối ca','0.01'], ['ns_ntu_cuoi','NTU NS cuối ca','0.001'],
                ['clo_du_cuoi','Clo dư cuối ca','0.01'],
            ];
            foreach ($qfields as [$f,$lb,$st]):
            ?>
            <div class="gc-field">
                <label><?= $lb ?></label>
                <input type="number" name="NkGiaoCa[<?= $f ?>]"
                       value="<?= Html::encode($model->$f) ?>"
                       step="<?= $st ?>" inputmode="decimal" />
            </div>
            <?php endforeach; ?>
        </div>

        <!-- 5. HÓA CHẤT -->
        <div class="gc-section">5. Hóa chất sử dụng</div>
        <div class="gc-grid3">
            <?php foreach ([
                ['pac_kg','PAC (kg)','0.01'],
                ['chlorine_kg','Chlorine (kg)','0.01'],
                ['polymer_kg','Polymer (kg)','0.001'],
            ] as [$f,$lb,$st]): ?>
            <div class="gc-field">
                <label><?= $lb ?></label>
                <input type="number" name="NkGiaoCa[<?= $f ?>]"
                       value="<?= Html::encode($model->$f) ?>"
                       step="<?= $st ?>" inputmode="decimal" />
            </div>
            <?php endforeach; ?>
        </div>

        <!-- 6. SỰ CỐ & GIAO CA -->
        <div class="gc-section">6. Sự cố &amp; Giao ca</div>
        <?php foreach ([
            ['su_co',    'Sự cố trong ca (để trống nếu không có)'],
            ['bien_phap','Biện pháp xử lý'],
            ['ghi_chu',  'Ghi chú bàn giao'],
        ] as [$f,$lb]): ?>
        <div class="gc-field" style="margin-bottom:10px;">
            <label><?= $lb ?></label>
            <textarea name="NkGiaoCa[<?= $f ?>]" rows="2"
                      style="resize:vertical"><?= Html::encode($model->$f) ?></textarea>
        </div>
        <?php endforeach; ?>

        <div class="gc-grid">
            <div class="gc-field">
                <label>Nhân viên giao ca</label>
                <input type="text" name="NkGiaoCa[nhan_vien_giao]"
                       value="<?= Html::encode($model->nhan_vien_giao) ?>" />
            </div>
            <div class="gc-field">
                <label>Nhân viên nhận ca</label>
                <input type="text" name="NkGiaoCa[nhan_vien_nhan]"
                       value="<?= Html::encode($model->nhan_vien_nhan) ?>" />
            </div>
        </div>

        <button type="submit" class="gc-btn">💾 Lưu sổ giao ca</button>
        <?php ActiveForm::end() ?>
    </div>
</div>

<script>
function g(id) { return parseFloat(document.getElementById(id)?.value) || 0; }

function calcNuoc() {
    const cap = g('nuoc_cap_cuoi') - g('nuoc_cap_dau');
    const tho = g('nuoc_tho_cuoi') - g('nuoc_tho_dau');
    const fmt = v => v > 0 ? v.toFixed(1) + ' m³' : '';
    document.getElementById('calc-nuoc-cap').textContent = cap > 0 ? '→ Sản lượng nước cấp: ' + fmt(cap) : '';
    document.getElementById('calc-nuoc-tho').textContent = tho > 0 ? '→ Sản lượng nước thô: ' + fmt(tho) : '';
}

function calcDien() {
    const nm  = g('dien_nha_may_cuoi')      - g('dien_nha_may_dau');
    const tb  = g('dien_tram_bom_cuoi')     - g('dien_tram_bom_dau');
    const nt5 = g('dien_nt5_tang_ap_cuoi')  - g('dien_nt5_tang_ap_dau');
    const el  = document.getElementById('calc-dien');
    const parts = [];
    if (nm  > 0) parts.push('NM: '   + nm  + ' KWh');
    if (tb  > 0) parts.push('TB: '   + tb  + ' KWh');
    if (nt5 > 0) parts.push('NT5: '  + nt5 + ' KWh');
    if (parts.length) {
        const tong = nm + tb + nt5;
        el.textContent = '→ ' + parts.join(' | ') + ' | Tổng: ' + tong + ' KWh';
    } else el.textContent = '';
}

calcNuoc(); calcDien();
</script>