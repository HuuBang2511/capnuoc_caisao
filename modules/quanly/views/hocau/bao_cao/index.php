<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Báo cáo nội bộ hàng ngày';
?>
<style>
.bc-wrap { max-width:960px; margin:0 auto; padding:20px 16px; }
.bc-hero  { background:linear-gradient(135deg,#1e3a5f,#2d6099);
            border-radius:16px; padding:28px 24px; margin-bottom:24px; color:#fff; }
.bc-hero h1 { font-size:1.2rem; font-weight:700; margin:0 0 4px; }
.bc-hero p  { font-size:.85rem; opacity:.75; margin:0; }

/* Chọn ngày */
.bc-date-row { display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin-bottom:16px; }
.bc-date-row input[type=date] {
    padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:10px;
    font-size:.95rem; outline:none; background:#fff; }
.bc-date-row input[type=date]:focus { border-color:#3b82f6; }

/* Nút */
.bc-btn { padding:9px 16px; border-radius:10px; font-size:.85rem; font-weight:600;
          cursor:pointer; text-decoration:none; display:inline-flex;
          align-items:center; gap:6px; border:none; white-space:nowrap; }
.bc-btn:hover { opacity:.88; }
.bc-btn-blue   { background:#3b82f6; color:#fff; }
.bc-btn-green  { background:#16a34a; color:#fff; }
.bc-btn-teal   { background:#0891b2; color:#fff; }
.bc-btn-violet { background:#7c3aed; color:#fff; }
.bc-btn-orange { background:#ea580c; color:#fff; }
.bc-btn-rose   { background:#e11d48; color:#fff; }
.bc-btn-slate  { background:#fff; color:#475569; border:1.5px solid #e2e8f0 !important; }
.bc-btn-slate:hover { border-color:#3b82f6 !important; color:#3b82f6; }

.bc-btn-group { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:12px; }
.bc-btn-group-label { font-size:.7rem; font-weight:700; color:#94a3b8; letter-spacing:.05em;
                      text-transform:uppercase; margin-bottom:6px; margin-top:4px; }
.bc-divider { border:none; border-top:1px solid #f1f5f9; margin:16px 0; }

/* Shortcuts ngày */
.bc-shortcuts { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:20px; }
.bc-shortcut  { padding:6px 14px; border-radius:99px; font-size:.82rem; font-weight:500;
                background:#f1f5f9; color:#475569; cursor:pointer; border:none;
                text-decoration:none; }
.bc-shortcut:hover, .bc-shortcut.active { background:#3b82f6; color:#fff; }

/* Status grid — 8 cards */
.bc-status-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:10px; margin-bottom:20px; }
.bc-status-card { border:1px solid #e2e8f0; border-radius:12px; padding:14px; }
.bc-status-card .label { font-size:.73rem; color:#94a3b8; margin-bottom:3px; }
.bc-status-card .value { font-size:.95rem; font-weight:700; color:#1e3a5f; }
.bc-status-card .sub   { font-size:.75rem; color:#64748b; margin-top:2px; }
.bc-status-card.has-data  { border-left:4px solid #22c55e; }
.bc-status-card.no-data   { border-left:4px solid #f59e0b; }
.bc-status-card.auto-data { border-left:4px solid #3b82f6; }

/* Preview bảng */
.bc-preview { border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; margin-bottom:20px; }
.bc-preview-head { background:#f8fafc; padding:12px 16px; font-weight:600; font-size:.9rem;
                   color:#334155; border-bottom:1px solid #e2e8f0;
                   display:flex; justify-content:space-between; align-items:center; }
.bc-table { width:100%; border-collapse:collapse; font-size:.85rem; }
.bc-table th { background:#f8fafc; padding:8px 12px; text-align:left;
               border-bottom:2px solid #e2e8f0; white-space:nowrap; font-weight:600; color:#475569; }
.bc-table td { padding:9px 12px; border-bottom:1px solid #f1f5f9; }
.bc-table tr:last-child td { border-bottom:none; }
.bc-table tr:hover td { background:#f8fafc; }
.bc-badge { display:inline-block; padding:2px 9px; border-radius:99px; font-size:.75rem; }
.bc-badge.ok   { background:#dcfce7; color:#166534; }
.bc-badge.warn { background:#fef9c3; color:#854d0e; }
.bc-badge.bad  { background:#fee2e2; color:#991b1b; }
.bc-badge.auto { background:#dbeafe; color:#1e40af; }
.bc-badge.miss { background:#f1f5f9; color:#94a3b8; }

/* Tháng/năm picker cho tuần */
.bc-month-row { display:flex; gap:8px; align-items:center; flex-wrap:wrap; margin-bottom:12px; }
.bc-month-row select { padding:8px 12px; border:1.5px solid #e2e8f0; border-radius:8px;
                       font-size:.9rem; outline:none; background:#fff; }
.bc-month-row select:focus { border-color:#3b82f6; }

@media(max-width:576px) {
    .bc-status-grid { grid-template-columns:1fr; }
    .bc-date-row    { flex-direction:column; align-items:stretch; }
    .bc-btn-group   { flex-direction:column; }
    .bc-btn         { justify-content:center; }
}
</style>

<div class="bc-wrap">

    <!-- Hero -->
    <div class="bc-hero">
        <h1>📊 Báo cáo & Nhật ký vận hành</h1>
        <p>Sản xuất · Chất lượng nước · Giao ca · Hoá nghiệm · Nước thải · CL Nước Tuần</p>
    </div>

    <!-- Chọn ngày -->
    <div class="bc-date-row">
        <input type="date" id="input-ngay"
               value="<?= date('Y-m-d') ?>"
               max="<?= date('Y-m-d') ?>"
               onchange="loadPreview(this.value)" />
    </div>

    <!-- ── XUẤT BÁO CÁO ── -->
    <div class="bc-btn-group-label">📤 Xuất báo cáo</div>
    <div class="bc-btn-group">
        <button class="bc-btn bc-btn-violet" id="btn-xuat-hoa-nghiem" onclick="xuatFile('hoa-nghiem', this)">
            ⬇ Hoá nghiệm
        </button>
        <button class="bc-btn bc-btn-blue" id="btn-xuat-van-hanh" onclick="xuatFile('van-hanh', this)">
            ⬇ Vận hành
        </button>
        <button class="bc-btn bc-btn-teal" id="btn-xuat-nuoc-thai" onclick="xuatFile('nuoc-thai', this)">
            ⬇ Nước thải SH
        </button>
    </div>
    <!-- CL Nước Tuần: chọn tháng/năm -->
    <div class="bc-month-row">
        <span style="font-size:.82rem;color:#64748b;font-weight:600;">📊 CL Nước Tuần:</span>
        <select id="sel-thang">
            <?php for ($m=1;$m<=12;$m++): ?>
            <option value="<?= $m ?>" <?= $m==(int)date('m')?'selected':'' ?>>Tháng <?= $m ?></option>
            <?php endfor; ?>
        </select>
        <select id="sel-nam">
            <?php for ($y=date('Y')-1;$y<=date('Y');$y++): ?>
            <option value="<?= $y ?>" <?= $y==(int)date('Y')?'selected':'' ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>
        <button class="bc-btn bc-btn-orange" onclick="xuatClnTuan(this)">⬇ Xuất CL Nước Tuần</button>
    </div>

    <hr class="bc-divider">

    <!-- ── NHẬP LIỆU HÀNG NGÀY (7 tab) ── -->
    <div class="bc-btn-group-label">✏ Nhập liệu hàng ngày</div>
    <div class="bc-btn-group">
        <a id="btn-hoa-nghiem-ngay" href="#" class="bc-btn bc-btn-slate">
            🧪 Giao ca HN Ngày
        </a>
        <a id="btn-hoa-nghiem-dem" href="#" class="bc-btn bc-btn-slate">
            🌙 Giao ca HN Đêm
        </a>
        <a id="btn-giao-ca-ngay" href="#" class="bc-btn bc-btn-slate">
            ☀️ Giao ca VH Ngày
        </a>
        <a id="btn-giao-ca-dem" href="#" class="bc-btn bc-btn-slate">
            🌙 Giao ca VH Đêm
        </a>
        <a id="btn-nuoc-thai" href="#" class="bc-btn bc-btn-slate">
            🧫 Nước thải SH
        </a>
        <a id="btn-cln-ngay" href="#" class="bc-btn bc-btn-slate">
            📋 CLN hàng ngày
        </a>
    </div>
    <!-- CL Nước Tuần: link riêng với chọn tháng -->
    <div class="bc-btn-group">
        <a id="btn-phan-tich-tuan" href="<?= Url::to(['nhat-ky/phan-tich-tuan']) ?>" class="bc-btn bc-btn-slate">
            📊 CL Nước Tuần
        </a>
    </div>

    <hr class="bc-divider">

    <!-- ── CẤU HÌNH ── -->
    <div class="bc-btn-group-label">⚙ Cấu hình</div>
    <div class="bc-btn-group">
        <a href="<?= Url::to(['nhat-ky/dong-ho-config']) ?>" class="bc-btn bc-btn-slate">
            ⚙️ Cấu hình đồng hồ KH
        </a>
        <a href="<?= Url::to(['nhat-ky/san-luong-dong-ho']) ?>" class="bc-btn bc-btn-green">
            📈 Sản lượng đồng hồ
        </a>
    </div>

    <hr class="bc-divider">

    <!-- Shortcuts ngày -->
    <div class="bc-shortcuts">
        <?php for ($i = 0; $i < 7; $i++):
            $d = date('Y-m-d', strtotime("-$i days"));
            $lbl = $i==0?'Hôm nay':($i==1?'Hôm qua':date('d/m', strtotime("-$i days")));
        ?>
        <a class="bc-shortcut <?= $i==0?'active':'' ?>"
           href="javascript:void(0)"
           onclick="setNgay('<?= $d ?>',this)"><?= $lbl ?></a>
        <?php endfor; ?>
    </div>

    <!-- Tình trạng dữ liệu — 8 cards -->
    <div id="status-grid" class="bc-status-grid">
        <!-- SCADA -->
        <div class="bc-status-card auto-data">
            <div class="label">Sản lượng SCADA</div>
            <div class="value" id="st-scada">Đang tải...</div>
            <div class="sub" id="st-scada-sub">Tự động từ SCADA</div>
        </div>
        <!-- CLN hàng ngày -->
        <div class="bc-status-card" id="card-cln-ngay">
            <div class="label">CLN hàng ngày</div>
            <div class="value" id="st-cln-ngay">—</div>
            <div class="sub"><a id="link-cln-ngay" href="#" style="color:#3b82f6;text-decoration:none;">Nhập tay →</a></div>
        </div>
        <!-- Giao ca HN ngày -->
        <div class="bc-status-card" id="card-hn-ngay">
            <div class="label">Giao ca Hoá nghiệm Ngày</div>
            <div class="value" id="st-hn-ngay">—</div>
            <div class="sub"><a id="link-hn-ngay" href="#" style="color:#3b82f6;text-decoration:none;">Nhập tay →</a></div>
        </div>
        <!-- Giao ca HN đêm -->
        <div class="bc-status-card" id="card-hn-dem">
            <div class="label">Giao ca Hoá nghiệm Đêm</div>
            <div class="value" id="st-hn-dem">—</div>
            <div class="sub"><a id="link-hn-dem" href="#" style="color:#3b82f6;text-decoration:none;">Nhập tay →</a></div>
        </div>
        <!-- Giao ca VH ngày -->
        <div class="bc-status-card" id="card-ca-ngay">
            <div class="label">Sổ giao ca Vận hành Ngày</div>
            <div class="value" id="st-ca-ngay">—</div>
            <div class="sub"><a id="link-ca-ngay" href="#" style="color:#3b82f6;text-decoration:none;">Nhập tay →</a></div>
        </div>
        <!-- Giao ca VH đêm -->
        <div class="bc-status-card" id="card-ca-dem">
            <div class="label">Sổ giao ca Vận hành Đêm</div>
            <div class="value" id="st-ca-dem">—</div>
            <div class="sub"><a id="link-ca-dem" href="#" style="color:#3b82f6;text-decoration:none;">Nhập tay →</a></div>
        </div>
        <!-- Nước thải SH -->
        <div class="bc-status-card" id="card-nuoc-thai">
            <div class="label">Nước thải SH</div>
            <div class="value" id="st-nuoc-thai">—</div>
            <div class="sub"><a id="link-nuoc-thai" href="#" style="color:#3b82f6;text-decoration:none;">Nhập tay →</a></div>
        </div>
        <!-- CLN Hoá nghiệm (nk_chat_luong_gio) -->
        <div class="bc-status-card" id="card-cln-gio">
            <div class="label">CLN theo giờ (HN)</div>
            <div class="value" id="st-cln-gio">—</div>
            <div class="sub">Nhập tay</div>
        </div>
    </div>

    <!-- Preview sản lượng SCADA -->
    <div class="bc-preview">
        <div class="bc-preview-head">
            <span>Sơ lược sản lượng</span>
            <span id="preview-ngay" style="color:#94a3b8;font-size:.8rem;font-weight:400"></span>
        </div>
        <div style="overflow-x:auto;">
        <table class="bc-table" id="tbl-sanluong">
            <thead>
                <tr>
                    <th>Chỉ tiêu</th><th>Giá trị</th><th>Trạng thái</th>
                </tr>
            </thead>
            <tbody id="tbody-sanluong">
                <tr><td colspan="3" style="text-align:center;color:#94a3b8;padding:20px">Đang tải...</td></tr>
            </tbody>
        </table>
        </div>
    </div>

    <!-- Preview CLN theo giờ -->
    <div class="bc-preview" id="preview-cln" style="display:none">
        <div class="bc-preview-head">
            <span>CLN theo giờ hôm nay</span>
            <a id="link-cln-full" href="#"
               style="font-size:.8rem;color:#3b82f6;font-weight:400;text-decoration:none;">
               Xem / Nhập đầy đủ →
            </a>
        </div>
        <div style="overflow-x:auto;">
        <table class="bc-table">
            <thead>
                <tr>
                    <th>Giờ</th><th>Ca</th>
                    <th>NS pH</th><th>NS NTU</th>
                    <th>Clo dư</th><th>NT pH</th><th>NT NTU</th>
                </tr>
            </thead>
            <tbody id="tbody-cln"></tbody>
        </table>
        </div>
    </div>
</div>

<script>
const SCADA_KEY = 'SCADA_HOCAU_2024_SECRET_KEY';
const URL_HN_NGAY  = '<?= Url::to(['nhat-ky/chat-luong-gio', 'ca'=>1]) ?>';
const URL_HN_DEM   = '<?= Url::to(['nhat-ky/chat-luong-gio', 'ca'=>2]) ?>';
const URL_GC1      = '<?= Url::to(['nhat-ky/giao-ca', 'ca'=>1]) ?>';
const URL_GC2      = '<?= Url::to(['nhat-ky/giao-ca', 'ca'=>2]) ?>';
const URL_NT       = '<?= Url::to(['nhat-ky/nuoc-thai-sh']) ?>';
const URL_CLN_NGAY = '<?= Url::to(['nhat-ky/cln-hang-ngay']) ?>';
const URL_PT_TUAN  = '<?= Url::to(['nhat-ky/phan-tich-tuan']) ?>';
const URL_EXCEL_HN = '<?= Url::to(['nhat-ky/xuat-hoa-nghiem']) ?>';
const URL_EXCEL_VH = '<?= Url::to(['nhat-ky/xuat-van-hanh']) ?>';
const URL_EXCEL_NT = '<?= Url::to(['nhat-ky/xuat-nuoc-thai']) ?>';
const URL_EXCEL_TN = '<?= Url::to(['nhat-ky/xuat-cln-tuan']) ?>';
const URL_API_CLN  = '<?= Url::to(['nhat-ky/api-cln']) ?>';

let currentNgay = document.getElementById('input-ngay').value;

function setNgay(ngay, el) {
    document.querySelectorAll('.bc-shortcut').forEach(e => e.classList.remove('active'));
    if (el) el.classList.add('active');
    document.getElementById('input-ngay').value = ngay;
    loadPreview(ngay);
}

function loadPreview(ngay) {
    currentNgay = ngay;
    document.getElementById('preview-ngay').textContent = formatNgayVN(ngay);
    updateBtnLinks(ngay);
    loadScada(ngay);
    loadCLN(ngay);
}

function updateBtnLinks(ngay) {
    const map = [
        ['btn-hoa-nghiem-ngay', URL_HN_NGAY + '&ngay=' + ngay],
        ['btn-hoa-nghiem-dem',  URL_HN_DEM  + '&ngay=' + ngay],
        ['btn-giao-ca-ngay',    URL_GC1     + '&ngay=' + ngay],
        ['btn-giao-ca-dem',     URL_GC2     + '&ngay=' + ngay],
        ['btn-nuoc-thai',       URL_NT      + '?ngay='  + ngay],
        ['btn-cln-ngay',        URL_CLN_NGAY + '?ngay=' + ngay],
        ['link-cln-full',       URL_HN_NGAY + '&ngay=' + ngay],
        ['link-cln-ngay',       URL_CLN_NGAY + '?ngay=' + ngay],
        ['link-hn-ngay',        URL_HN_NGAY + '&ngay=' + ngay],
        ['link-hn-dem',         URL_HN_DEM  + '&ngay=' + ngay],
        ['link-ca-ngay',        URL_GC1     + '&ngay=' + ngay],
        ['link-ca-dem',         URL_GC2     + '&ngay=' + ngay],
        ['link-nuoc-thai',      URL_NT      + '?ngay='  + ngay],
    ];
    map.forEach(([id, url]) => {
        const el = document.getElementById(id);
        if (el) el.href = url;
    });
}

function formatNgayVN(y) {
    let d;
    if (y && y.includes('-')) d = new Date(y + 'T00:00:00');
    else if (y && y.includes('/')) { const p=y.split('/'); d=new Date(p[2]+'-'+p[1]+'-'+p[0]+'T00:00:00'); }
    else return y;
    const thu = ['CN','T2','T3','T4','T5','T6','T7'][d.getDay()];
    return `${thu}, ${String(d.getDate()).padStart(2,'0')}/${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()}`;
}

function toIso(ngay) {
    if (!ngay) return '';
    if (/^\d{4}-\d{2}-\d{2}/.test(ngay)) return ngay.slice(0,10);
    if (/^\d{2}\/\d{2}\/\d{4}/.test(ngay)) { const p=ngay.split('/'); return p[2]+'-'+p[1]+'-'+p[0]; }
    return ngay;
}

function setCard(id, val, color, hasData) {
    const el = document.getElementById(id);
    if (!el) return;
    el.textContent = val;
    el.style.color = color;
    const card = document.getElementById('card-' + id.replace('st-',''));
    if (card) {
        card.classList.remove('has-data','no-data');
        card.classList.add(hasData ? 'has-data' : 'no-data');
    }
}

function loadScada(ngay) {
    const url = '/iot_api.php?action=sanluong&loai=thatthoat&key=' + SCADA_KEY;
    const ngayIso = toIso(ngay);
    const stEl = document.getElementById('st-scada');
    stEl.textContent = 'Đang tải...';
    stEl.style.color = '#94a3b8';

    fetch(url)
        .then(r => r.json())
        .then(data => {
            const days = (data.days || []).filter(d => d && d.ngay);
            if (!days.length) { stEl.textContent='— Không có dữ liệu'; stEl.style.color='#94a3b8'; renderSanLuong(null); return; }
            const daysNorm = days.map(d => ({...d, _iso: toIso(d.ngay)}));
            let day = daysNorm.find(d => d._iso === ngayIso);
            if (day) {
                stEl.textContent = '✓ Có dữ liệu';
                stEl.style.color = '#16a34a';
            } else {
                day = daysNorm[daysNorm.length-1];
                const lbl = formatNgayVN(day._iso);
                stEl.textContent = '↩ ' + lbl;
                stEl.style.color = '#f59e0b';
                const subEl = document.getElementById('st-scada-sub');
                if (subEl) subEl.textContent = 'Dữ liệu mới nhất có sẵn';
                const pv = document.getElementById('preview-ngay');
                if (pv) pv.textContent = lbl + ' (mới nhất có dữ liệu)';
            }
            renderSanLuong(day);
        })
        .catch(() => { stEl.textContent='✗ Lỗi kết nối SCADA'; stEl.style.color='#ef4444'; renderSanLuong(null); });
}

function renderSanLuong(d) {
    const tbody = document.getElementById('tbody-sanluong');
    if (!d) { tbody.innerHTML='<tr><td colspan="3" style="text-align:center;color:#94a3b8;padding:16px">Không có dữ liệu SCADA</td></tr>'; return; }
    const fmt = v => v!=null ? Number(v).toLocaleString('vi-VN') : '—';
    const badge = (v,type) => {
        if (v==null||v===0) return '<span class="bc-badge miss">—</span>';
        if (type==='auto') return '<span class="bc-badge auto">Tự động</span>';
        if (type==='tl') { const cls=v<10?'ok':v<20?'warn':'bad'; return `<span class="bc-badge ${cls}">${v.toFixed(2)}%</span>`; }
        return '<span class="bc-badge ok">✓</span>';
    };
    const rows = [
        ['Nước thô bơm vào (m³)', fmt(d.nuoc_tho),  badge(d.nuoc_tho,'auto')],
        ['Nước sạch cấp ra (m³)', fmt(d.nuoc_cap),  badge(d.nuoc_cap,'auto')],
        ['Sản lượng KH (m³)',     fmt(d.nuoc_kh),   badge(d.nuoc_kh,d.nuoc_kh>0?'auto':'miss')],
        ['Thất thoát (m³)',       fmt(d.that_thoat),badge(d.that_thoat,'auto')],
        ['Tỷ lệ thất thoát', d.ti_le!=null?d.ti_le.toFixed(2)+'%':'—', d.ti_le!=null?badge(d.ti_le,'tl'):'<span class="bc-badge miss">Chưa đủ KH</span>'],
    ];
    tbody.innerHTML = rows.map(([a,b,c],i)=>
        `<tr style="${i%2?'':'background:#f8fafc'}"><td style="font-weight:500">${a}</td><td>${b}</td><td>${c}</td></tr>`
    ).join('');
}

function loadCLN(ngay) {
    fetch(`${URL_API_CLN}?ngay=${ngay}`)
        .then(r => r.json())
        .then(data => {
            const count = data.count || 0;

            // CLN theo giờ (HN)
            setCard('st-cln-gio', count>0 ? `✓ ${count} lần nhập` : '— Chưa nhập',
                    count>0 ? '#16a34a' : '#f59e0b', count>0);

            // CLN hàng ngày
            const hasClnNgay = !!data.cln_ngay;
            setCard('st-cln-ngay', hasClnNgay ? '✓ Đã nhập' : '— Chưa nhập',
                    hasClnNgay ? '#16a34a' : '#f59e0b', hasClnNgay);

            // Giao ca HN ngày = ca=1 trong nk_chat_luong_gio
            const cln_ca1 = (data.rows||[]).filter(r=>r.ca===1);
            const hasHnNgay = cln_ca1.length > 0;
            setCard('st-hn-ngay', hasHnNgay ? `✓ ${cln_ca1.length} lần nhập` : '— Chưa nhập',
                    hasHnNgay ? '#16a34a' : '#f59e0b', hasHnNgay);

            // Giao ca HN đêm = ca=2
            const cln_ca2 = (data.rows||[]).filter(r=>r.ca===2);
            const hasHnDem = cln_ca2.length > 0;
            setCard('st-hn-dem', hasHnDem ? `✓ ${cln_ca2.length} lần nhập` : '— Chưa nhập',
                    hasHnDem ? '#16a34a' : '#f59e0b', hasHnDem);

            // Giao ca VH ngày/đêm
            const hasCaNgay = !!data.ca_ngay;
            setCard('st-ca-ngay', hasCaNgay ? '✓ Đã nhập' : '— Chưa nhập',
                    hasCaNgay ? '#16a34a' : '#f59e0b', hasCaNgay);
            const hasCaDem = !!data.ca_dem;
            setCard('st-ca-dem', hasCaDem ? '✓ Đã nhập' : '— Chưa nhập',
                    hasCaDem ? '#16a34a' : '#f59e0b', hasCaDem);

            // Preview CLN
            if (count > 0) {
                renderCLN(data.rows||[]);
                document.getElementById('preview-cln').style.display = 'block';
            } else {
                document.getElementById('preview-cln').style.display = 'none';
            }

            // Nước thải: gọi riêng vì api-cln không có field này
            loadNuocThaiStatus(ngay);
        })
        .catch(() => {});
}

function loadNuocThaiStatus(ngay) {
    // Kiểm tra nước thải qua API đơn giản
    fetch(`<?= Url::to(['nhat-ky/api-nuoc-thai']) ?>?ngay=${ngay}`)
        .then(r => r.json())
        .then(d => {
            const has = !!d.has_data;
            setCard('st-nuoc-thai', has ? '✓ Đã nhập' : '— Chưa nhập',
                    has ? '#16a34a' : '#f59e0b', has);
        })
        .catch(() => {
            const el = document.getElementById('st-nuoc-thai');
            if (el) { el.textContent = '—'; el.style.color = '#94a3b8'; }
        });
}

function renderCLN(rows) {
    const qcvn = { ns_ph:[6.5,8.5], ns_ntu:[0,2.0], clo_du:[0.2,1.0] };
    function badge(v,field) {
        if (v==null) return '—';
        const q=qcvn[field]; if (!q) return v;
        const cls=(v<q[0]||v>q[1])?'bad':((v<q[0]+0.1||v>q[1]-0.1)?'warn':'ok');
        return `<span class="bc-badge ${cls}">${v}</span>`;
    }
    document.getElementById('tbody-cln').innerHTML = rows.map((r,i)=>
        `<tr style="${i%2?'':'background:#f8fafc'}">
            <td>${r.gio}</td><td>${r.ca==1?'Ngày':'Đêm'}</td>
            <td>${badge(r.ns_ph,'ns_ph')}</td><td>${badge(r.ns_ntu,'ns_ntu')}</td>
            <td>${badge(r.clo_du,'clo_du')}</td>
            <td>${r.nt_ph??'—'}</td><td>${r.nt_ntu??'—'}</td>
        </tr>`
    ).join('');
}

function xuatFile(loai, btn) {
    const orig = btn.textContent;
    btn.textContent = '⏳ Đang tạo...';
    btn.disabled = true;
    const urls = { 'hoa-nghiem': URL_EXCEL_HN, 'van-hanh': URL_EXCEL_VH, 'nuoc-thai': URL_EXCEL_NT };
    window.location.href = urls[loai] + '?ngay=' + currentNgay;
    setTimeout(() => { btn.textContent = orig; btn.disabled = false; }, 3500);
}

function xuatClnTuan(btn) {
    const thang = document.getElementById('sel-thang').value;
    const nam   = document.getElementById('sel-nam').value;
    const orig = btn.textContent;
    btn.textContent = '⏳ Đang tạo...';
    btn.disabled = true;
    window.location.href = URL_EXCEL_TN + '?thang=' + thang + '&nam=' + nam;
    setTimeout(() => { btn.textContent = orig; btn.disabled = false; }, 3500);
}

// Init
loadPreview(currentNgay);

// Auto refresh 60s khi đang xem hôm nay
setInterval(function() {
    if (!document.hidden) {
        const today = new Date().toISOString().slice(0,10);
        if (currentNgay === today) { loadScada(currentNgay); loadCLN(currentNgay); }
    }
}, 60000);
</script>
