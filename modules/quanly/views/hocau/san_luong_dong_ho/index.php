<?php
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Báo cáo sản lượng theo đồng hồ';
?>
<style>
.sl-wrap { max-width:100%; padding:16px; }
.sl-toolbar { display:flex; align-items:center; gap:10px; margin-bottom:16px;
              background:#fff; border:1px solid #e2e8f0; border-radius:10px;
              padding:12px 16px; flex-wrap:wrap; }
.sl-toolbar label { font-size:.82rem; color:#475569; font-weight:600; }
.sl-toolbar input[type=date] { padding:7px 10px; border:1.5px solid #e2e8f0;
                                border-radius:8px; font-size:.88rem; }
.sl-toolbar button { padding:8px 18px; border:none; border-radius:8px;
                     font-size:.88rem; font-weight:600; cursor:pointer; }
.btn-view  { background:#3b82f6; color:#fff; }
.btn-excel { background:#16a34a; color:#fff; }
.btn-cfg   { background:#f1f5f9; color:#475569; border:1px solid #e2e8f0 !important; }
.btn-back  { background:#f1f5f9; color:#475569; border:1px solid #e2e8f0; margin-left:auto; }
.sl-loading { text-align:center; padding:40px; color:#94a3b8; font-size:.9rem; }
.sl-table-wrap { overflow-x:auto; }
table.sl-tbl { border-collapse:collapse; font-size:.78rem; width:100%; }
table.sl-tbl th { background:#1e3a5f; color:#fff; padding:8px 10px;
                  text-align:center; white-space:nowrap; position:sticky; top:0; z-index:2; }
table.sl-tbl th.col-kh { text-align:left; min-width:160px; position:sticky; left:0; z-index:3; background:#1e3a5f; }
table.sl-tbl th.col-dv { position:sticky; left:0; z-index:3; background:#2d6099; }
table.sl-tbl td { padding:7px 10px; border-bottom:1px solid #f1f5f9; text-align:right; white-space:nowrap; }
table.sl-tbl td.col-kh { text-align:left; font-weight:600; color:#1e3a5f;
                          background:#fff; position:sticky; left:0; z-index:1;
                          border-right:2px solid #e2e8f0; }
table.sl-tbl td.col-dv { font-size:.72rem; color:#64748b; background:#f8fafc;
                          position:sticky; left:0; z-index:1; }
table.sl-tbl tr:hover td { background:#f0f4f8 !important; }
table.sl-tbl tr:hover td.col-kh { background:#e8f0fe !important; }
table.sl-tbl td.col-tong  { font-weight:700; background:#fef9c3; color:#78350f; }
table.sl-tbl td.col-tb    { font-weight:600; background:#eff6ff; color:#1e40af; }
table.sl-tbl td.null-val  { color:#cbd5e1; }
table.sl-tbl tfoot td     { background:#1e3a5f; color:#fff; font-weight:700; padding:8px 10px; }
table.sl-tbl tfoot td.col-kh { background:#2d6099; }
.sl-note { font-size:.75rem; color:#94a3b8; margin-top:8px; padding-left:4px; }
/* Responsive */
@media(max-width:640px){
    .sl-toolbar { gap:8px; }
    table.sl-tbl { font-size:.72rem; }
}
</style>

<div class="sl-wrap">
    <!-- Toolbar -->
    <div class="sl-toolbar">
        <label>Từ ngày:</label>
        <input type="date" id="tu_ngay" value="<?= Html::encode($tu_ngay) ?>" max="<?= date('Y-m-d') ?>">
        <label>Đến ngày:</label>
        <input type="date" id="den_ngay" value="<?= Html::encode($den_ngay) ?>" max="<?= date('Y-m-d') ?>">
        <button class="btn-view" onclick="loadData()">🔍 Xem</button>
        <button class="btn-excel" onclick="xuatExcel()">📥 Xuất Excel</button>
        <button class="btn-cfg" onclick="location.href='<?= Url::to(['nhat-ky/dong-ho-config']) ?>'">⚙️ Cấu hình</button>
        <button class="btn-back" onclick="history.back()">← Quay lại</button>
    </div>

    <!-- Bảng dữ liệu -->
    <div id="sl-content">
        <div class="sl-loading">⏳ Nhấn "Xem" để tải dữ liệu...</div>
    </div>
    <div class="sl-note" id="sl-note" style="display:none;">
        * Sản lượng được tính từ dữ liệu tích lũy SCADA (giá trị MAX – MIN trong ngày).
        Nếu ô trống (—) nghĩa là không có dữ liệu đồng hồ trong ngày đó.
    </div>
</div>

<script>
const API_URL    = '<?= Url::to(['nhat-ky/api-san-luong']) ?>';
const EXCEL_URL  = '<?= Url::to(['nhat-ky/xuat-san-luong']) ?>';

let lastData = null;

function loadData() {
    const tuNgay  = document.getElementById('tu_ngay').value;
    const denNgay = document.getElementById('den_ngay').value;
    if (!tuNgay || !denNgay) { alert('Vui lòng chọn ngày'); return; }
    if (tuNgay > denNgay) { alert('Ngày bắt đầu không được lớn hơn ngày kết thúc'); return; }

    document.getElementById('sl-content').innerHTML = '<div class="sl-loading">⏳ Đang tải dữ liệu từ SCADA...</div>';
    document.getElementById('sl-note').style.display = 'none';

    fetch(API_URL + '?tu_ngay=' + tuNgay + '&den_ngay=' + denNgay)
        .then(r => r.json())
        .then(data => {
            if (!data.success) { showError(data.msg || 'Lỗi tải dữ liệu'); return; }
            lastData = data;
            renderTable(data);
            document.getElementById('sl-note').style.display = 'block';
        })
        .catch(e => showError('Lỗi kết nối: ' + e.message));
}

function renderTable(data) {
    if (!data.rows || data.rows.length === 0) {
        document.getElementById('sl-content').innerHTML =
            '<div class="sl-loading">Không có dữ liệu trong khoảng thời gian này</div>';
        return;
    }

    const ngayList = data.ngay_list;
    let html = '<div class="sl-table-wrap"><table class="sl-tbl"><thead><tr>';
    html += '<th class="col-kh">Khách hàng</th>';
    html += '<th style="min-width:70px">Đ.hồ vào</th>';
    html += '<th style="min-width:70px">Đ.hồ ra</th>';
    html += '<th style="min-width:80px;background:#2d6099">Tổng (m³)</th>';
    html += '<th style="min-width:70px;background:#2d6099">TB/ngày</th>';

    ngayList.forEach(ngay => {
        const d = new Date(ngay);
        const label = (d.getDate()+'').padStart(2,'0') + '/' + ((d.getMonth()+1)+'').padStart(2,'0');
        html += `<th style="min-width:65px">${label}</th>`;
    });
    html += '</tr></thead><tbody>';

    // Tính tổng từng ngày (cho footer)
    const tongNgay = {};
    ngayList.forEach(ngay => { tongNgay[ngay] = null; });
    let tongAll = 0, countAll = 0;
    let tbAll = 0;

    data.rows.forEach(row => {
        html += '<tr>';
        html += `<td class="col-kh">${escHtml(row.ten_kh)}</td>`;
        html += `<td style="font-size:.7rem;color:#64748b;text-align:center">${escHtml(row.dau_vao)}</td>`;
        html += `<td style="font-size:.7rem;color:#64748b;text-align:center">${row.dau_ra !== '—' ? escHtml(row.dau_ra) : '<span style="color:#cbd5e1">—</span>'}</td>`;

        if (row.tong !== null) {
            html += `<td class="col-tong">${fmt(row.tong)}</td>`;
            tongAll += row.tong;
        } else {
            html += `<td class="null-val col-tong">—</td>`;
        }
        html += row.tb_ngay !== null ? `<td class="col-tb">${fmt(row.tb_ngay)}</td>` : `<td class="null-val col-tb">—</td>`;
        if (row.tb_ngay !== null && row.tb_ngay !== undefined) tbAll += row.tb_ngay;

        ngayList.forEach(ngay => {
            const v = row.ngay_data[ngay];
            if (v !== null && v !== undefined) {
                html += `<td>${fmt(v)}</td>`;
                tongNgay[ngay] = (tongNgay[ngay] || 0) + v;
            } else {
                html += `<td class="null-val">—</td>`;
            }
        });
        html += '</tr>';
    });

    // Footer tổng cột
    html += '</tbody><tfoot><tr>';
    html += '<td class="col-kh">TỔNG</td>';
    html += '<td></td><td></td>';
    html += `<td>${fmt(tongAll)}</td>`;
    html += `<td>${fmt(tbAll)}</td>`;
    ngayList.forEach(ngay => {
        html += tongNgay[ngay] !== null ? `<td>${fmt(tongNgay[ngay])}</td>` : '<td>—</td>';
    });
    html += '</tr></tfoot></table></div>';

    document.getElementById('sl-content').innerHTML = html;

    // Tren mobile: tu scroll ngang ve cot ngay gan nhat (phai man hinh)
    requestAnimationFrame(() => {
        const wrap = document.querySelector('.sl-table-wrap');
        if (wrap) wrap.scrollLeft = wrap.scrollWidth;
    });
}

function xuatExcel() {
    const tuNgay  = document.getElementById('tu_ngay').value;
    const denNgay = document.getElementById('den_ngay').value;
    if (!tuNgay || !denNgay) { alert('Vui lòng chọn ngày trước'); return; }
    window.location.href = EXCEL_URL + '?tu_ngay=' + tuNgay + '&den_ngay=' + denNgay;
}

function fmt(v) {
    if (v === null || v === undefined) return '—';
    return Number(v).toLocaleString('vi-VN');
}
function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
function showError(msg) {
    document.getElementById('sl-content').innerHTML =
        `<div class="sl-loading" style="color:#ef4444">❌ ${msg}</div>`;
}

// Auto load khi vào trang
document.addEventListener('DOMContentLoaded', loadData);

// Auto refresh moi 60 giay — chi refresh neu dang o tab nay
setInterval(function() {
    if (!document.hidden) loadData();
}, 60000);
</script>