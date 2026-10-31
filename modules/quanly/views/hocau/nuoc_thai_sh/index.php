<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->title = 'Nước thải sinh hoạt';
?>
<style>
.nts-wrap { max-width:700px; margin:0 auto; padding:16px; }
.nts-card { border:1px solid #e2e8f0; border-radius:12px; padding:20px; margin-bottom:16px; }
.nts-title { font-size:1rem; font-weight:700; color:#1e3a5f; margin-bottom:16px; display:flex; align-items:center; gap:8px; }
.nts-grid  { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.nts-field label { font-size:.8rem; color:#64748b; margin-bottom:4px; display:block; }
.nts-field input { width:100%; padding:10px 12px; border:1.5px solid #e2e8f0;
                   border-radius:8px; font-size:.95rem; outline:none; box-sizing:border-box; }
.nts-field input:focus { border-color:#3b82f6; }
.nts-field input.val-bad  { border-color:#ef4444; background:#fef2f2; }
.nts-field input.val-warn { border-color:#f59e0b; background:#fffbeb; }
.nts-field input.val-ok   { border-color:#22c55e; }
.nts-hint { font-size:.7rem; color:#94a3b8; margin-top:2px; min-height:14px; }
.nts-hint.bad  { color:#ef4444; }
.nts-hint.warn { color:#f59e0b; }
.nts-btn { width:100%; padding:13px; background:#3b82f6; color:#fff; border:none;
           border-radius:10px; font-size:1rem; font-weight:600; cursor:pointer; margin-top:8px; }
.nav-day { display:flex; gap:8px; margin-bottom:8px; flex-wrap:wrap; }
.nav-day a { padding:6px 14px; border-radius:99px; text-decoration:none;
             font-size:.83rem; background:#f1f5f9; color:#475569; }
.nav-day a.active { background:#3b82f6; color:#fff; }
.qnav-bar{display:flex;gap:5px;flex-wrap:wrap;align-items:center;padding:8px 10px;
          background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;margin-bottom:10px}
.qnav-label{font-size:.7rem;font-weight:700;color:#94a3b8;white-space:nowrap;margin-right:2px}
.qnav-btn{padding:5px 10px;border-radius:8px;text-decoration:none;font-size:.75rem;font-weight:500;
          background:#fff;color:#475569;border:1px solid #e2e8f0;white-space:nowrap;transition:all .12s}
.qnav-btn:hover{border-color:#3b82f6;color:#3b82f6;background:#eff6ff}
.qnav-active{background:#1e3a5f!important;color:#fff!important;border-color:#1e3a5f!important}
.nts-table { width:100%; border-collapse:collapse; font-size:.8rem; }
.nts-table th { background:#f8fafc; padding:8px; text-align:center; border-bottom:2px solid #e2e8f0; }
.nts-table td { padding:8px; border-bottom:1px solid #f1f5f9; text-align:center; }
.nts-badge { display:inline-block; padding:2px 8px; border-radius:99px; font-size:.72rem; }
.nts-badge.ok   { background:#dcfce7; color:#166534; }
.nts-badge.warn { background:#fef9c3; color:#854d0e; }
.nts-badge.bad  { background:#fee2e2; color:#991b1b; }
@media(max-width:576px){ .nts-grid { grid-template-columns:1fr; } }

/* ===== PRINT ===== */
.print-header{display:none}
@media print{
    .nav-day,.qnav-bar,.flash-ok,.nts-btn,button[type=submit]{display:none!important}
    .print-header{display:block!important;margin-bottom:8px}

    .nts-wrap{padding:0!important;max-width:100%!important}
    .nts-card{border:1px solid #ccc!important;border-radius:0!important;padding:8px!important;margin-bottom:6px!important}
    .nts-field input{border:1px solid #ccc!important;padding:3px 5px!important;font-size:8pt!important;border-radius:0!important}
    .nts-field label{font-size:7.5pt!important}
    .nts-hint{font-size:6.5pt!important}
    .nts-grid{gap:8px!important}
    .nts-table{font-size:7.5pt!important}
    .nts-table th,.nts-table td{padding:4px!important}
    .nts-title{font-size:9pt!important;margin-bottom:6px!important}
    @page{size:A4 portrait;margin:8mm}
    *{-webkit-print-color-adjust:exact!important;print-color-adjust:exact!important}
}
</style>

<div class="nts-wrap">
<!-- PRINT HEADER -->
<div class="print-header">
<table style="width:100%;border:1.5px solid #000;border-collapse:collapse;margin-bottom:6px;font-family:Arial,sans-serif;">
    <tr>
        <td rowspan="3" style="width:14%;border:1px solid #000;padding:4px;text-align:center;vertical-align:middle;">
            <img src="<?= Yii::$app->homeUrl ?>images/logo_tuanloc.jpg" style="height:55px;width:auto;" /><br>
            <span style="font-size:6.5pt;font-weight:700;color:#1e3a5f;">CONG TY CP CAP NUOC<br>HO CAU MOI</span>
        </td>
        <td rowspan="3" style="border:1px solid #000;padding:4px;text-align:center;vertical-align:middle;">
            <div style="font-size:13pt;font-weight:700;letter-spacing:.02em;">NUOC THAI SINH HOAT</div>
            <div style="font-size:8pt;margin-top:3px;color:#334155;"><?= date('d/m/Y', strtotime($ngay)) ?></div>
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

    <div class="nav-day">
        <?php for ($i=2;$i>=0;$i--):
            $d = date('Y-m-d', strtotime("-$i days"));
            $lbl = $i==0?'Hôm nay':($i==1?'Hôm qua':date('d/m',strtotime("-$i days")));
        ?>
        <a href="<?= Url::to(['nhat-ky/nuoc-thai-sh','ngay'=>$d]) ?>"
           class="<?= $d==$ngay?'active':'' ?>"><?= $lbl ?></a>
        <?php endfor; ?>
    </div>

    <!-- Thanh lối tắt nhập liệu -->
    <div class="qnav-bar">
        <span class="qnav-label">✏ Nhập liệu:</span>
        <a href="<?= Url::to(['nhat-ky/chat-luong-gio','ca'=>1,'ngay'=>$ngay]) ?>" class="qnav-btn">🧪 HN Ngày</a>
        <a href="<?= Url::to(['nhat-ky/chat-luong-gio','ca'=>2,'ngay'=>$ngay]) ?>" class="qnav-btn">🌙 HN Đêm</a>
        <a href="<?= Url::to(['nhat-ky/giao-ca','ca'=>1,'ngay'=>$ngay]) ?>" class="qnav-btn">☀️ VH Ngày</a>
        <a href="<?= Url::to(['nhat-ky/giao-ca','ca'=>2,'ngay'=>$ngay]) ?>" class="qnav-btn">🌙 VH Đêm</a>
        <a href="<?= Url::to(['nhat-ky/nuoc-thai-sh','ngay'=>$ngay]) ?>" class="qnav-btn qnav-active">🧫 Nước thải</a>
        <a href="<?= Url::to(['nhat-ky/cln-hang-ngay','ngay'=>$ngay]) ?>" class="qnav-btn">📋 CLN ngày</a>
        <a href="<?= Url::to(['nhat-ky/phan-tich-tuan']) ?>" class="qnav-btn">📊 CL Tuần</a>
    </div>

    <div class="nts-card">
        <div class="nts-title">🧫 Nước thải sinh hoạt — <?= date('d/m/Y', strtotime($ngay)) ?></div>

        <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div style="background:#dcfce7;color:#166534;padding:10px 14px;border-radius:8px;margin-bottom:12px;font-size:.88rem;">
            ✓ <?= Yii::$app->session->getFlash('success') ?>
        </div>
        <?php endif; ?>

        <?php $form = ActiveForm::begin(['enableClientValidation'=>false]) ?>
        <?= Html::hiddenInput('NkNuocThaiSh[ngay]', $ngay) ?>

        <div class="nts-grid" style="margin-bottom:12px;">
            <?php
            $fields = [
                ['ph',       'pH',               '5.0–9.0',          '0.01'],
                ['tss',      'TSS (mg/L)',        '≤ 50',             '0.1'],
                ['amoni',    'Amoni NH₄⁺ (mg/L)', '≤ 5',             '0.001'],
                ['nitrat',   'Nitrat NO₃⁻ (mg/L)','≤ 30',            '0.001'],
                ['coliform', 'Coliform (MPN/100mL)','≤ 3.000',        '1'],
            ];
            foreach ($fields as [$f,$lb,$qc,$st]):
                $status = $model->getStatus($f);
            ?>
            <div class="nts-field">
                <label><?= $lb ?> <span style="color:#94a3b8;font-weight:400;"><?= $qc ?></span></label>
                <input type="number"
                       name="NkNuocThaiSh[<?= $f ?>]"
                       value="<?= Html::encode($model->$f) ?>"
                       class="val-<?= $status ?>"
                       step="<?= $st ?>"
                       inputmode="decimal"
                       onchange="checkNts(this,'<?= $f ?>')" />
                <div class="nts-hint <?= $status ?>" id="hint-<?= $f ?>">
                    <?= $status=='bad'?'⚠ Vượt ngưỡng QCVN 14':'' ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Người thực hiện + kiểm tra -->
        <div style="font-size:.78rem;font-weight:600;color:#475569;padding:4px 8px;background:#f1f5f9;border-radius:6px;border-left:3px solid #3b82f6;margin:12px 0 10px;">
            👤 Người thực hiện
        </div>
        <div class="nts-grid" style="margin-bottom:12px;">
            <div class="nts-field">
                <label>Người thực hiện</label>
                <input type="text" name="NkNuocThaiSh[nguoi_th]"
                       value="<?= Html::encode($model->nguoi_th ?? '') ?>"
                       placeholder="Họ và tên..." />
            </div>
            <div class="nts-field">
                <label>Người kiểm tra</label>
                <input type="text" name="NkNuocThaiSh[nguoi_kt]"
                       value="<?= Html::encode($model->nguoi_kt ?? '') ?>"
                       placeholder="Họ và tên..." />
            </div>
        </div>

        <div class="nts-field" style="margin-bottom:12px;">
            <label>Ghi chú</label>
            <input type="text" name="NkNuocThaiSh[ghi_chu]"
                   value="<?= Html::encode($model->ghi_chu) ?>"
                   placeholder="Ghi chú nếu có..." />
        </div>

        <button type="submit" class="nts-btn">💾 Lưu kết quả nước thải</button>
        <?php ActiveForm::end() ?>
    </div>

    <!-- Lịch sử -->
    <?php if ($lichSu): ?>
    <div class="nts-card">
        <div class="nts-title" style="margin-bottom:12px;">📊 Lịch sử 3 tháng gần nhất</div>
        <div style="overflow-x:auto;">
        <table class="nts-table">
            <thead>
                <tr>
                    <th>Ngày</th><th>pH</th><th>TSS</th>
                    <th>Amoni</th><th>Nitrat</th><th>Coliform</th>
                    <th>Người TH</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($lichSu as $r): ?>
                <tr>
                    <td><?= Html::encode($r->ngay) ?></td>
                    <?php foreach (['ph','tss','amoni','nitrat','coliform'] as $f):
                        $st = $r->getStatus($f);
                    ?>
                    <td>
                        <?php if ($r->$f !== null): ?>
                        <span class="nts-badge <?= $st ?>"><?= $r->$f ?></span>
                        <?php else: ?>—<?php endif; ?>
                    </td>
                    <?php endforeach; ?>
                    <td style="font-size:.75rem"><?= Html::encode($r->nguoi_th ?? '—') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
const QCVN_NTS = <?= json_encode($QCVN) ?>;
function checkNts(input, field) {
    if (!QCVN_NTS[field]) return;
    const v = parseFloat(input.value);
    const q = QCVN_NTS[field];
    const hint = document.getElementById('hint-' + field);
    input.className = 'nts-field input';
    if (hint) { hint.className = 'nts-hint'; hint.textContent = ''; }
    if (isNaN(v)) return;
    if (v < q.min || v > q.max) {
        input.classList.add('val-bad');
        if (hint) { hint.classList.add('bad'); hint.textContent = '⚠ Vượt QCVN 14'; }
    } else {
        const range = q.max - q.min;
        if (range > 0 && v > q.max - range*0.1) {
            input.classList.add('val-warn');
            if (hint) { hint.classList.add('warn'); hint.textContent = 'Gần ngưỡng'; }
        } else {
            input.classList.add('val-ok');
        }
    }
}
</script>
