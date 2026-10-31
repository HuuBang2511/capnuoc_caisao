<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'CLN hàng ngày — ' . date('d/m/Y', strtotime($ngay));
// Danh sách giờ theo thứ tự hiển thị
$GIO_ALL = [7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,0,1,2,3,4,5,6];
// Data hiện tại
$gioData = json_decode($model->gio_data ?? '{}', true);
// Jar test arrays
$jar = [
    's' => [
        'pac' => $model->jar_s_pac ? (is_array($model->jar_s_pac) ? $model->jar_s_pac : json_decode($model->jar_s_pac, true)) : array_fill(0,6,null),
        'ntu' => $model->jar_s_ntu ? (is_array($model->jar_s_ntu) ? $model->jar_s_ntu : json_decode($model->jar_s_ntu, true)) : array_fill(0,6,null),
        'ph'  => $model->jar_s_ph  ? (is_array($model->jar_s_ph)  ? $model->jar_s_ph  : json_decode($model->jar_s_ph, true))  : array_fill(0,6,null),
    ],
    'c' => [
        'pac' => $model->jar_c_pac ? (is_array($model->jar_c_pac) ? $model->jar_c_pac : json_decode($model->jar_c_pac, true)) : array_fill(0,6,null),
        'ntu' => $model->jar_c_ntu ? (is_array($model->jar_c_ntu) ? $model->jar_c_ntu : json_decode($model->jar_c_ntu, true)) : array_fill(0,6,null),
        'ph'  => $model->jar_c_ph  ? (is_array($model->jar_c_ph)  ? $model->jar_c_ph  : json_decode($model->jar_c_ph, true))  : array_fill(0,6,null),
    ],
];
foreach (['s','c'] as $k) {
    foreach (['pac','ntu','ph'] as $t) {
        while (count($jar[$k][$t]) < 6) $jar[$k][$t][] = null;
    }
}
?>
<style>
.cln-wrap { max-width:1100px; margin:0 auto; padding:16px; }
.cln-card { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:18px; margin-bottom:16px; }
.cln-title { font-size:1rem; font-weight:700; color:#1e3a5f; margin-bottom:14px; display:flex; align-items:center; gap:8px; }
.cln-section { font-size:.78rem; font-weight:700; color:#3b82f6; letter-spacing:.04em; text-transform:uppercase;
               padding:4px 8px; background:#eff6ff; border-radius:6px; margin:14px 0 10px; border-left:3px solid #3b82f6; }
/* Điều hướng ngày */
.cln-nav { display:flex; gap:8px; margin-bottom:16px; flex-wrap:wrap; }
.cln-nav a { padding:6px 14px; border-radius:99px; text-decoration:none; font-size:.83rem;
             background:#f1f5f9; color:#475569; }
.cln-nav a.active { background:#3b82f6; color:#fff; }
/* Bảng theo giờ */
.tbl-gio { width:100%; border-collapse:collapse; font-size:.75rem; table-layout:fixed; }
.tbl-gio th { background:#1e3a5f; color:#fff; padding:5px 3px; text-align:center;
              white-space:normal; font-weight:600; line-height:1.3; word-break:keep-all; }
.tbl-gio th.sub { background:#2d6099; font-weight:400; font-size:.7rem; }
.tbl-gio td { padding:3px 2px; border-bottom:1px solid #f1f5f9; border-right:1px solid #f1f5f9; text-align:center; }
.tbl-gio tr.ca-dem { background:#f8f9ff; }
.tbl-gio td.gio-label { font-weight:600; color:#475569; background:#f8fafc; width:34px; font-size:.75rem; }
.tbl-gio input[type=number] { width:100%; max-width:60px; min-width:42px; padding:3px 2px;
                               border:1px solid #d1d5db; border-radius:4px;
                               font-size:.8rem; text-align:center; background:transparent;
                               -webkit-appearance:none; box-sizing:border-box; }
.tbl-gio input[type=number]:focus { border-color:#3b82f6; outline:none; background:#fff; }
.tbl-gio input.val-bad  { border-color:#ef4444 !important; background:#fef2f2; }
.tbl-gio input.val-warn { border-color:#f59e0b !important; background:#fffbeb; }
.tbl-gio tr.ca-dem td { border-right-color:#e0e7ff; }
/* Wrapper bảng: scroll chỉ khi không đủ chỗ */
.tbl-gio-wrap { width:100%; overflow-x:auto; -webkit-overflow-scrolling:touch; }
/* Jar test */
.jar-table { width:100%; border-collapse:collapse; font-size:.82rem; }
.jar-table th { background:#f8fafc; padding:7px 10px; border:1px solid #e2e8f0; text-align:center; font-weight:600; }
.jar-table td { padding:6px 8px; border:1px solid #e2e8f0; text-align:center; }
.jar-table td.row-label { font-weight:600; color:#475569; text-align:left; background:#f8fafc; width:100px; }
.jar-table input[type=number] { width:60px; padding:4px 5px; border:1px solid #d1d5db; border-radius:5px;
                                  font-size:.82rem; text-align:center; -webkit-appearance:none; }
.jar-table input:focus { border-color:#3b82f6; outline:none; }
.jar-col-active { background:#eff6ff !important; }
.jar-col-active input { border-color:#3b82f6; background:#fff; }
/* Người trực */
.nguoi-grid { display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; }
.nk-field label { font-size:.8rem; color:#64748b; margin-bottom:4px; display:block; }
.nk-field input { width:100%; padding:9px 11px; border:1.5px solid #e2e8f0; border-radius:8px;
                  font-size:.92rem; outline:none; box-sizing:border-box; }
.nk-field input:focus { border-color:#3b82f6; }
/* Nút */
.btn-save { width:100%; padding:13px; background:#3b82f6; color:#fff; border:none; border-radius:10px;
            font-size:1rem; font-weight:600; cursor:pointer; margin-top:8px; }
.btn-save:active { background:#2563eb; }
.flash-ok { background:#dcfce7; color:#166534; padding:10px 14px; border-radius:8px;
            margin-bottom:12px; font-size:.88rem; }
.bq-row td { font-weight:700 !important; background:#fef9c3 !important; }
@media(max-width:768px) {
    .cln-wrap { padding:10px 6px; }
    .nguoi-grid { grid-template-columns:1fr; }
    .tbl-gio { font-size:.8rem; }
    .tbl-gio input[type=number] { min-width:46px; font-size:.82rem; padding:4px 2px; }
    .tbl-gio td.gio-label { font-size:.8rem; }
/* Thanh lối tắt dùng chung */
.qnav-bar{display:flex;gap:5px;flex-wrap:wrap;align-items:center;padding:8px 10px;
          background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;margin-bottom:10px}
.qnav-label{font-size:.7rem;font-weight:700;color:#94a3b8;white-space:nowrap;margin-right:2px}
.qnav-btn{padding:5px 10px;border-radius:8px;text-decoration:none;font-size:.75rem;font-weight:500;
          background:#fff;color:#475569;border:1px solid #e2e8f0;white-space:nowrap;transition:all .12s}
.qnav-btn:hover{border-color:#3b82f6;color:#3b82f6;background:#eff6ff}
.qnav-active{background:#1e3a5f!important;color:#fff!important;border-color:#1e3a5f!important}
    .jar-table { font-size:.85rem; }
    .jar-table input[type=number] { width:52px; }
}
</style>

<div class="cln-wrap">

    <!-- Điều hướng ngày -->
    <div class="cln-nav">
        <?php for ($i=2;$i>=0;$i--):
            $d = date('Y-m-d', strtotime("-$i days"));
            $lbl = $i==0?'Hôm nay':($i==1?'Hôm qua':date('d/m',strtotime("-$i days")));
        ?>
        <a href="<?= Url::to(['nhat-ky/cln-hang-ngay','ngay'=>$d]) ?>"
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
        <a href="<?= Url::to(['nhat-ky/nuoc-thai-sh','ngay'=>$ngay]) ?>" class="qnav-btn">🧫 Nước thải</a>
        <a href="<?= Url::to(['nhat-ky/cln-hang-ngay','ngay'=>$ngay]) ?>" class="qnav-btn qnav-active">📋 CLN ngày</a>
        <a href="<?= Url::to(['nhat-ky/phan-tich-tuan']) ?>" class="qnav-btn">📊 CL Tuần</a>
    </div>

    <?php if (Yii::$app->session->hasFlash('success_cln')): ?>
    <div class="flash-ok">✓ <?= Yii::$app->session->getFlash('success_cln') ?></div>
    <?php endif; ?>

    <form method="post" id="form-cln-ngay">
        <?= \yii\helpers\Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
        <input type="hidden" name="NkClnHangNgay[ngay]" value="<?= Html::encode($ngay) ?>">

        <!-- ── BẢNG THEO GIỜ ── -->
        <div class="cln-card">
            <div class="cln-title">📋 KẾT QUẢ CHẤT LƯỢNG NƯỚC HÀNG NGÀY — <?= date('d/m/Y', strtotime($ngay)) ?></div>

            <div class="tbl-gio-wrap">
            <table class="tbl-gio" id="tbl-main">
                <thead>
                    <tr>
                        <th rowspan="2">Giờ</th>
                        <th colspan="4">pH</th>
                        <th colspan="4">NTU</th>
                        <th rowspan="2">Clo dư<br><small>mg/L<br>0.2–1.0</small></th>
                        <th colspan="2">Độ màu<br><small>Pt-Co &lt;15</small></th>
                        <th colspan="2">Độ kiềm<br><small>CaCO3</small></th>
                        <th colspan="2">Độ cứng<br><small>CaCO3 &lt;300</small></th>
                        <th colspan="2">Clorua<br><small>mg/L &lt;250</small></th>
                        <th rowspan="2">PAC<br><small>Tỷ trọng</small></th>
                    </tr>
                    <tr class="sub">
                        <th class="sub">NT</th><th class="sub">NL1</th><th class="sub">NL2</th><th class="sub">NS<br>6–8.5</th>
                        <th class="sub">NT</th><th class="sub">NL1<br>&lt;5</th><th class="sub">NL2<br>&lt;5</th><th class="sub">NS<br>&lt;2</th>
                        <th class="sub">NS</th><th class="sub">NT</th>
                        <th class="sub">NS</th><th class="sub">NT</th>
                        <th class="sub">NS<br>&lt;300</th><th class="sub">NT</th>
                        <th class="sub">NS<br>&lt;250</th><th class="sub">NT</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $fields = ['nt_ph','nl1_ph','nl2_ph','ns_ph','nt_ntu','nl1_ntu','nl2_ntu','ns_ntu',
                           'clo_du','ns_do_mau','nt_do_mau','ns_do_kiem','nt_do_kiem',
                           'ns_do_cung','nt_do_cung','ns_clorua','nt_clorua','pac_ty_trong'];
                $steps  = ['nt_ph'=>'0.01','nl1_ph'=>'0.01','nl2_ph'=>'0.01','ns_ph'=>'0.01',
                           'nt_ntu'=>'0.1','nl1_ntu'=>'0.001','nl2_ntu'=>'0.01','ns_ntu'=>'0.001',
                           'clo_du'=>'0.01','ns_do_mau'=>'1','nt_do_mau'=>'1',
                           'ns_do_kiem'=>'0.1','nt_do_kiem'=>'0.1',
                           'ns_do_cung'=>'0.1','nt_do_cung'=>'0.1',
                           'ns_clorua'=>'0.1','nt_clorua'=>'0.1','pac_ty_trong'=>'0.001'];
                // QCVN limits cho JS validation
                $qcvn_js = ['ns_ph'=>[6.0,8.5],'ns_ntu'=>[0,2.0],'clo_du'=>[0.2,1.0],
                            'nl1_ntu'=>[0,5.0],'nl2_ntu'=>[0,5.0],'ns_do_mau'=>[0,15.0],
                            'ns_do_cung'=>[0,300.0],'ns_clorua'=>[0,250.0]];

                foreach ($GIO_ALL as $gio):
                    $isCaDem = in_array($gio, [19,20,21,22,23,0,1,2,3,4,5,6]);
                    $row = $gioData[(string)$gio] ?? [];
                    $gioLabel = $gio === 0 ? '24h' : $gio . 'h';
                ?>
                <tr class="<?= $isCaDem ? 'ca-dem' : '' ?>" id="row-gio-<?= $gio ?>">
                    <td class="gio-label"><?= $gioLabel ?></td>
                    <?php foreach ($fields as $f): ?>
                    <td>
                        <input type="number"
                               name="gio[<?= $gio ?>][<?= $f ?>]"
                               value="<?= Html::encode($row[$f] ?? '') ?>"
                               step="<?= $steps[$f] ?>"
                               inputmode="decimal"
                               data-field="<?= $f ?>"
                               data-gio="<?= $gio ?>"
                               onchange="checkQcvn(this)" />
                    </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
                <!-- Hàng trung bình ca ngày -->
                <tr class="bq-row" id="row-bq-ngay">
                    <td class="gio-label" style="white-space:nowrap">BQ<br>Ngày</td>
                    <?php foreach ($fields as $f): ?>
                    <td id="bq-ngay-<?= $f ?>">—</td>
                    <?php endforeach; ?>
                </tr>
                <!-- Hàng trung bình ca đêm -->
                <tr class="bq-row" id="row-bq-dem">
                    <td class="gio-label" style="white-space:nowrap">BQ<br>Đêm</td>
                    <?php foreach ($fields as $f): ?>
                    <td id="bq-dem-<?= $f ?>">—</td>
                    <?php endforeach; ?>
                </tr>
                </tbody>
            </table>
            </div>
        </div>

        <!-- ── JAR TEST ── -->
        <div class="cln-card">
            <div class="cln-title">🧪 Jar Test PAC</div>

            <!-- Ca sáng -->
            <div class="cln-section">Ca Sáng — Giờ:
                <input type="time" name="NkClnHangNgay[jar_gio_sang]"
                       value="<?= Html::encode($model->jar_gio_sang ?? '08:00') ?>"
                       style="border:1px solid #e2e8f0;border-radius:6px;padding:3px 6px;font-size:.85rem;" />
            </div>
            <div style="overflow-x:auto; margin-bottom:16px;">
            <table class="jar-table" id="jar-sang">
                <thead>
                    <tr>
                        <th class="row-label"></th>
                        <?php for ($i=1;$i<=6;$i++): ?>
                        <th><?= $i ?></th>
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="row-label">PAC (mg/L)</td>
                    <?php for ($i=0;$i<6;$i++): ?>
                    <td><input type="number" step="0.1"
                               name="jar_s_pac[<?= $i ?>]"
                               value="<?= Html::encode($jar['s']['pac'][$i] ?? '') ?>"
                               inputmode="decimal"
                               onchange="updateJarChon('sang')" /></td>
                    <?php endfor; ?>
                </tr>
                <tr>
                    <td class="row-label">Độ đục (NTU)</td>
                    <?php for ($i=0;$i<6;$i++): ?>
                    <td><input type="number" step="0.001"
                               name="jar_s_ntu[<?= $i ?>]"
                               value="<?= Html::encode($jar['s']['ntu'][$i] ?? '') ?>"
                               inputmode="decimal"
                               onchange="updateJarChon('sang')" /></td>
                    <?php endfor; ?>
                </tr>
                <tr>
                    <td class="row-label">pH</td>
                    <?php for ($i=0;$i<6;$i++): ?>
                    <td><input type="number" step="0.01"
                               name="jar_s_ph[<?= $i ?>]"
                               value="<?= Html::encode($jar['s']['ph'][$i] ?? '') ?>"
                               inputmode="decimal" /></td>
                    <?php endfor; ?>
                </tr>
                </tbody>
            </table>
            </div>
            <div style="font-size:.85rem; color:#475569; margin-bottom:4px;">
                Liều chọn (sáng):
                <input type="number" step="0.1" name="NkClnHangNgay[jar_s_chon]"
                       value="<?= Html::encode($model->jar_s_chon ?? '') ?>"
                       id="jar-s-chon"
                       style="width:80px;padding:4px 6px;border:1.5px solid #3b82f6;border-radius:6px;font-weight:700;color:#3b82f6;"
                       inputmode="decimal" />
                mg/L
            </div>

            <!-- Ca chiều -->
            <div class="cln-section">Ca Chiều — Giờ:
                <input type="time" name="NkClnHangNgay[jar_gio_chieu]"
                       value="<?= Html::encode($model->jar_gio_chieu ?? '19:00') ?>"
                       style="border:1px solid #e2e8f0;border-radius:6px;padding:3px 6px;font-size:.85rem;" />
            </div>
            <div style="overflow-x:auto; margin-bottom:16px;">
            <table class="jar-table" id="jar-chieu">
                <thead>
                    <tr>
                        <th class="row-label"></th>
                        <?php for ($i=1;$i<=6;$i++): ?>
                        <th><?= $i ?></th>
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="row-label">PAC (mg/L)</td>
                    <?php for ($i=0;$i<6;$i++): ?>
                    <td><input type="number" step="0.1"
                               name="jar_c_pac[<?= $i ?>]"
                               value="<?= Html::encode($jar['c']['pac'][$i] ?? '') ?>"
                               inputmode="decimal"
                               onchange="updateJarChon('chieu')" /></td>
                    <?php endfor; ?>
                </tr>
                <tr>
                    <td class="row-label">Độ đục (NTU)</td>
                    <?php for ($i=0;$i<6;$i++): ?>
                    <td><input type="number" step="0.001"
                               name="jar_c_ntu[<?= $i ?>]"
                               value="<?= Html::encode($jar['c']['ntu'][$i] ?? '') ?>"
                               inputmode="decimal"
                               onchange="updateJarChon('chieu')" /></td>
                    <?php endfor; ?>
                </tr>
                <tr>
                    <td class="row-label">pH</td>
                    <?php for ($i=0;$i<6;$i++): ?>
                    <td><input type="number" step="0.01"
                               name="jar_c_ph[<?= $i ?>]"
                               value="<?= Html::encode($jar['c']['ph'][$i] ?? '') ?>"
                               inputmode="decimal" /></td>
                    <?php endfor; ?>
                </tr>
                </tbody>
            </table>
            </div>
            <div style="font-size:.85rem; color:#475569; margin-bottom:4px;">
                Liều chọn (chiều):
                <input type="number" step="0.1" name="NkClnHangNgay[jar_c_chon]"
                       value="<?= Html::encode($model->jar_c_chon ?? '') ?>"
                       id="jar-c-chon"
                       style="width:80px;padding:4px 6px;border:1.5px solid #3b82f6;border-radius:6px;font-weight:700;color:#3b82f6;"
                       inputmode="decimal" />
                mg/L
            </div>
        </div>

        <!-- ── NGƯỜI TRỰC / KIỂM TRA ── -->
        <div class="cln-card">
            <div class="cln-title">👤 Người thực hiện</div>
            <div class="nguoi-grid">
                <div class="nk-field">
                    <label>Ca sáng — Người trực</label>
                    <input type="text" name="NkClnHangNgay[nguoi_truc_sang]"
                           value="<?= Html::encode($model->nguoi_truc_sang ?? '') ?>"
                           placeholder="Họ và tên..." />
                </div>
                <div class="nk-field">
                    <label>Ca chiều — Người trực</label>
                    <input type="text" name="NkClnHangNgay[nguoi_truc_chieu]"
                           value="<?= Html::encode($model->nguoi_truc_chieu ?? '') ?>"
                           placeholder="Họ và tên..." />
                </div>
                <div class="nk-field">
                    <label>Người kiểm tra</label>
                    <input type="text" name="NkClnHangNgay[nguoi_kt]"
                           value="<?= Html::encode($model->nguoi_kt ?? '') ?>"
                           placeholder="Họ và tên..." />
                </div>
            </div>
            <div class="nk-field" style="margin-top:10px;">
                <label>Ghi chú</label>
                <input type="text" name="NkClnHangNgay[ghi_chu]"
                       value="<?= Html::encode($model->ghi_chu ?? '') ?>"
                       placeholder="Ghi chú nếu có..." />
            </div>
        </div>

        <button type="submit" class="btn-save">💾 Lưu CLN hàng ngày</button>
    </form>
</div>

<script>
const QCVN_JS = <?= json_encode($qcvn_js ?? ['ns_ph'=>[6.0,8.5],'ns_ntu'=>[0,2.0],'clo_du'=>[0.2,1.0],'nl1_ntu'=>[0,5.0],'nl2_ntu'=>[0,5.0],'ns_do_mau'=>[0,15.0],'ns_do_cung'=>[0,300.0],'ns_clorua'=>[0,250.0]]) ?>;
const GIO_NGAY = [7,8,9,10,11,12,13,14,15,16,17,18];
const GIO_DEM  = [19,20,21,22,23,0,1,2,3,4,5,6];
const FIELDS   = ['nt_ph','nl1_ph','nl2_ph','ns_ph','nt_ntu','nl1_ntu','nl2_ntu','ns_ntu',
                  'clo_du','ns_do_mau','nt_do_mau','ns_do_kiem','nt_do_kiem',
                  'ns_do_cung','nt_do_cung','ns_clorua','nt_clorua','pac_ty_trong'];

function checkQcvn(input) {
    const field = input.dataset.field;
    const v = parseFloat(input.value);
    input.classList.remove('val-bad','val-warn');
    if (isNaN(v) || !QCVN_JS[field]) return;
    const [mn, mx] = QCVN_JS[field];
    if (v < mn || v > mx) { input.classList.add('val-bad'); return; }
    const range = mx - mn;
    if (range > 0 && (v < mn + range*0.05 || v > mx - range*0.05)) input.classList.add('val-warn');
    calcBQ();
}

function calcBQ() {
    ['ngay','dem'].forEach(ca => {
        const gioList = ca === 'ngay' ? GIO_NGAY : GIO_DEM;
        const sums = {}; const counts = {};
        FIELDS.forEach(f => { sums[f] = 0; counts[f] = 0; });
        gioList.forEach(gio => {
            FIELDS.forEach(f => {
                const inp = document.querySelector(`input[name="gio[${gio}][${f}]"]`);
                if (!inp) return;
                const v = parseFloat(inp.value);
                if (!isNaN(v)) { sums[f] += v; counts[f]++; }
            });
        });
        FIELDS.forEach(f => {
            const el = document.getElementById(`bq-${ca}-${f}`);
            if (!el) return;
            el.textContent = counts[f] > 0 ? (sums[f]/counts[f]).toFixed(2) : '—';
        });
    });
}

function updateJarChon(ca) {
    const prefix = ca === 'sang' ? 'jar_s' : 'jar_c';
    const chonEl = document.getElementById(`jar-${ca === 'sang' ? 's' : 'c'}-chon`);
    const tableId = ca === 'sang' ? 'jar-sang' : 'jar-chieu';
    const tbl = document.getElementById(tableId);
    if (!tbl || !chonEl) return;

    // Tìm cột NTU nhỏ nhất → liều PAC tương ứng
    let minNtu = Infinity, minIdx = -1;
    for (let i = 0; i < 6; i++) {
        const pacInp = tbl.querySelector(`input[name="${prefix}_pac[${i}]"]`);
        const ntuInp = tbl.querySelector(`input[name="${prefix}_ntu[${i}]"]`);
        if (!pacInp || !ntuInp) continue;
        const ntu = parseFloat(ntuInp.value);
        if (!isNaN(ntu) && ntu < minNtu) { minNtu = ntu; minIdx = i; }
    }

    // Highlight cột được chọn
    const rows = tbl.querySelectorAll('tbody tr');
    rows.forEach(row => {
        Array.from(row.cells).forEach((cell, idx) => {
            if (idx === 0) return;
            cell.classList.toggle('jar-col-active', idx - 1 === minIdx);
        });
    });

    // Set liều chọn
    if (minIdx >= 0) {
        const pacInp = tbl.querySelector(`input[name="${prefix}_pac[${minIdx}]"]`);
        if (pacInp && pacInp.value) chonEl.value = pacInp.value;
    }
}

// Init
document.addEventListener('DOMContentLoaded', function() {
    calcBQ();
    updateJarChon('sang');
    updateJarChon('chieu');
    // Init validation màu cho các ô đã có data
    document.querySelectorAll('.tbl-gio input[type=number]').forEach(inp => {
        if (inp.value !== '') checkQcvn(inp);
    });
});
</script>
