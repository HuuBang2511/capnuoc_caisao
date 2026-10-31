<?php
/* @var $this yii\web\View */
use yii\helpers\Url;

$this->title = 'Trung Tâm Điều Hành Hạ Tầng Cấp Nước';

$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js');
?>

<style>
    :root {
        --bg-body: #f4f6f9;
        --card-bg: #ffffff;
        --primary-color: #3699ff;
        --success-color: #1bc5bd;
        --warning-color: #ffa800;
        --danger-color:  #f64e60;
        --info-color:    #8950fc;
        --dark-color:    #181c32;
        --text-muted:    #b5b5c3;
        --sl-blue:   #3699ff;
        --sl-cyan:   #00d4ff;
        --sl-green:  #1bc5bd;
        --sl-amber:  #ffa800;
        --sl-rose:   #f64e60;
    }

    body { background-color:var(--bg-body); font-family:'Segoe UI',sans-serif; }

    /* ── KPI CARDS ─────────────────────────────────────────────── */
    .kpi-row { display:grid; grid-template-columns:repeat(5,1fr); gap:1.5rem; margin-bottom:1.5rem; }
    .kpi-card {
        background:var(--card-bg); border-radius:12px; padding:1.25rem;
        box-shadow:0 0 20px 0 rgba(76,87,125,.02); transition:all .3s ease;
        text-decoration:none; color:inherit; position:relative; overflow:hidden;
        border-bottom:4px solid transparent; display:flex; flex-direction:column;
        justify-content:space-between; height:100%;
    }
    .kpi-card:hover { transform:translateY(-5px); box-shadow:0 10px 30px 0 rgba(76,87,125,.1); }
    .border-blue   { border-bottom-color:var(--primary-color); }
    .border-green  { border-bottom-color:var(--success-color); }
    .border-orange { border-bottom-color:var(--warning-color); }
    .border-red    { border-bottom-color:var(--danger-color); }
    .border-purple { border-bottom-color:var(--info-color); }
    .kpi-icon-wrapper { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px; }
    .kpi-icon { width:45px; height:45px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.4rem; }
    .bg-light-blue   { background:#e1f0ff; color:var(--primary-color); }
    .bg-light-green  { background:#c9f7f5; color:var(--success-color); }
    .bg-light-orange { background:#fff4de; color:var(--warning-color); }
    .bg-light-red    { background:#ffe2e5; color:var(--danger-color); }
    .bg-light-purple { background:#eee5ff; color:var(--info-color); }
    .kpi-value { font-size:1.8rem; font-weight:800; color:var(--dark-color); line-height:1.2; }
    .kpi-title { font-size:.85rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; margin-top:5px; }
    .kpi-unit  { font-size:.8rem; font-weight:500; color:var(--text-muted); }

    /* ── CHART PANEL (GIS - white) ──────────────────────────────── */
    .chart-panel {
        background:var(--card-bg); border-radius:12px; padding:1.5rem;
        box-shadow:0 0 20px 0 rgba(76,87,125,.02);
        height:100%; min-height:380px; display:flex; flex-direction:column;
    }
    .panel-header {
        display:flex; justify-content:space-between; align-items:center;
        margin-bottom:1rem; border-bottom:1px dashed #ebedf3; padding-bottom:.5rem;
    }
    .panel-title { font-weight:700; font-size:1rem; color:var(--dark-color); margin:0; display:flex; align-items:center; }
    .panel-title i { margin-right:8px; opacity:.7; }
    .chart-wrapper { flex-grow:1; position:relative; width:100%; }

    /* ── SẢN LƯỢNG SECTION ──────────────────────────────────────── */
    .sl-section {
        background:#ffffff; border-radius:16px; padding:1.75rem; margin-bottom:1.5rem;
        border:1px solid #e2e8f0; box-shadow:0 0 20px 0 rgba(76,87,125,.08);
    }
    .sl-section-header {
        display:flex; flex-wrap:wrap; gap:10px;
        justify-content:space-between; align-items:flex-start;
        margin-bottom:1.5rem; padding-bottom:1rem; border-bottom:1px solid #e2e8f0;
    }
    .sl-section-title {
        font-size:1rem; font-weight:700; color:#181c32;
        display:flex; align-items:center; gap:10px; margin:0; flex-shrink:0;
    }
    .sl-section-title .icon-badge {
        width:32px; height:32px; border-radius:8px;
        background:linear-gradient(135deg,#3699ff,#00d4ff);
        display:flex; align-items:center; justify-content:center;
        font-size:.85rem; color:#fff; flex-shrink:0;
    }
    .sl-tabs { display:flex; flex-wrap:wrap; gap:5px; }
    .sl-tab {
        padding:6px 14px; border-radius:8px; border:1px solid #e2e8f0;
        background:#f8fafc; color:#64748b; font-size:.78rem; font-weight:600;
        cursor:pointer; transition:all .2s; letter-spacing:.3px;
    }
    .sl-tab:hover, .sl-tab.active { background:#3699ff; border-color:#3699ff; color:#fff; }

    /* KPI mini */
    /* ĐÃ SỬA: grid-template-columns thành 5 cột */
    .sl-kpi-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:12px; margin-bottom:1.5rem; }
    .sl-kpi {
        background:#f8fafc; border:1px solid #e2e8f0;
        border-radius:10px; padding:14px 16px; position:relative; overflow:hidden;
        transition:all .2s; animation:fadeInUp .4s ease both;
    }
    .sl-kpi::before {
        content:''; position:absolute; top:0; left:0; right:0; height:3px;
        background:var(--kpi-color, var(--sl-blue));
    }
    .sl-kpi:hover { background:#eff6ff; border-color:#93c5fd; }
    .sl-kpi:nth-child(1){ animation-delay:.05s; }
    .sl-kpi:nth-child(2){ animation-delay:.1s;  }
    .sl-kpi:nth-child(3){ animation-delay:.15s; }
    .sl-kpi:nth-child(4){ animation-delay:.2s;  }
    .sl-kpi:nth-child(5){ animation-delay:.25s; }
    .sl-kpi-label { font-size:.7rem; color:#64748b; font-weight:600; text-transform:uppercase; letter-spacing:.6px; margin-bottom:6px; }
    .sl-kpi-val   { font-size:1.55rem; font-weight:800; color:#181c32; line-height:1.1; }
    .sl-kpi-unit  { font-size:.72rem; color:#94a3b8; margin-left:3px; font-weight:400; }
    .sl-kpi-sub   { font-size:.68rem; color:#94a3b8; margin-top:3px; }
    .sl-kpi-trend { font-size:.72rem; margin-top:5px; display:flex; align-items:center; gap:4px; }
    .trend-up   { color:#1bc5bd; }
    .trend-down { color:#f64e60; }

    /* Chart cards */
    .sl-chart-grid   { display:grid; grid-template-columns:2fr 1fr; gap:14px; margin-bottom:14px; }
    /* Lớp CSS mới cho bố cục song song trên dưới */
    .sl-chart-stack  { display:flex; flex-direction:column; gap:14px; margin-bottom:14px; }
    .sl-chart-grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px; }
    .sl-card {
        background:#f8fafc; border:1px solid #e2e8f0;
        border-radius:12px; padding:16px; display:flex; flex-direction:column; width: 100%;
    }
    .sl-card-title {
        font-size:.78rem; font-weight:700; color:#3699ff; text-transform:uppercase;
        letter-spacing:.5px; margin-bottom:12px; display:flex; align-items:center; gap:7px;
    }
    .sl-card-title span.dot {
        width:8px; height:8px; border-radius:50%;
        background:var(--dot-color, var(--sl-blue)); flex-shrink:0;
    }
    .sl-canvas-wrap { flex:1; position:relative; min-height:180px; }

    /* ── REALTIME — 5 BLOCKS ────────────────────────────────────── */
    .rt-grid-top {
        display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px; margin-bottom:14px;
    }
    .rt-grid-bottom {
        display:grid; grid-template-columns:1.6fr 1fr; gap:14px;
    }
    .rt-station-card {
        background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px;
        padding:14px; display:flex; flex-direction:column;
    }
    .rt-station-card-title {
        font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.6px;
        margin-bottom:10px; display:flex; align-items:center; gap:8px; padding-bottom:8px;
        border-bottom:1px solid #e2e8f0;
    }
    .rt-station-card-title .dot {
        width:8px; height:8px; border-radius:50%; flex-shrink:0;
        animation:pulse-dot 2s infinite;
    }
    .rt-field-row {
        display:flex; justify-content:space-between; align-items:center;
        padding:5px 0; border-bottom:1px solid #f1f5f9;
    }
    .rt-field-row:last-child { border-bottom:none; }
    .rt-field-label { font-size:.73rem; color:#64748b; font-weight:500; }
    .rt-field-ts    { font-size:.65rem; color:#94a3b8; }
    .rt-field-val   { font-size:.8rem; font-weight:700; }

    /* Bảng 24 trạm */
    .rt-table { width:100%; border-collapse:collapse; font-size:.8rem; }
    .rt-table th {
        padding:7px 10px; text-align:left;
        color:#fff; font-weight:700; font-size:.68rem;
        text-transform:uppercase; letter-spacing:.5px;
        background:#3699ff; border-bottom:1px solid rgba(54,153,255,.15);
    }
    .rt-table td { padding:7px 10px; border-bottom:1px solid #f1f5f9; color:#334155; }
    .rt-table tr:last-child td { border-bottom:none; }
    .rt-table tr:hover td { background:#eff6ff; }
    .rt-table th:first-child { border-radius:6px 0 0 0; }
    .rt-table th:last-child  { border-radius:0 6px 0 0; }
    .ap-badge {
        display:inline-flex; align-items:center; gap:4px;
        padding:2px 7px; border-radius:5px; font-weight:700; font-size:.73rem;
    }
    .ap-high  { background:rgba(54,153,255,.15); color:#3699ff; }
    .ap-med   { background:rgba(27,197,189,.15);  color:#1bc5bd; }
    .ap-low   { background:rgba(255,168,0,.15);   color:#ffa800; }
    .ap-alert { background:rgba(246,78,96,.15);   color:#f64e60; }
    .ap-none  { background:rgba(100,116,139,.12); color:#64748b; }
    .ll-val   { color:#ffa800; font-weight:600; font-size:.75rem; }
    .ll-neg   { color:#f64e60; font-size:.72rem; }

    /* ── GEAR CONFIG DROPDOWN ───────────────────────────────────── */
    .rt-card-header {
        display:flex; justify-content:space-between; align-items:center;
        margin-bottom:10px; padding-bottom:8px; border-bottom:1px solid #e2e8f0;
    }
    .rt-gear-wrap { position:relative; }
    .rt-gear-btn {
        background:none; border:1px solid #e2e8f0; border-radius:6px;
        width:26px; height:26px; display:flex; align-items:center; justify-content:center;
        cursor:pointer; color:#94a3b8; font-size:.75rem; transition:all .2s;
        padding:0;
    }
    .rt-gear-btn:hover { border-color:#3699ff; color:#3699ff; background:#eff6ff; }
    .rt-gear-dropdown {
        display:none; position:absolute; right:0; top:30px; z-index:100;
        background:#fff; border:1px solid #e2e8f0; border-radius:8px;
        padding:8px 0; min-width:170px;
        box-shadow:0 4px 20px rgba(0,0,0,.1);
    }
    .rt-gear-dropdown.open { display:block; }
    .rt-gear-item {
        display:flex; align-items:center; gap:8px;
        padding:6px 14px; font-size:.75rem; color:#334155;
        cursor:pointer; transition:background .15s; white-space:nowrap;
    }
    .rt-gear-item:hover { background:#f8fafc; }
    .rt-gear-item input[type=checkbox] { accent-color:#3699ff; cursor:pointer; }
    .rt-field-row.rt-hidden { display:none; }
    /* custom row: nút xóa */
    .rt-custom-del {
        background:none; border:none; color:#f64e60; cursor:pointer;
        font-size:.7rem; padding:0 0 0 6px; line-height:1; opacity:.6;
        transition:opacity .15s;
    }
    .rt-custom-del:hover { opacity:1; }
    /* add-form trong gear dropdown */
    .rt-gear-add-form {
        padding:8px 12px; border-top:1px solid #f1f5f9; margin-top:4px;
    }
    .rt-gear-add-form input {
        width:100%; border:1px solid #e2e8f0; border-radius:5px;
        padding:4px 7px; font-size:.72rem; color:#334155; margin-bottom:5px;
        outline:none; box-sizing:border-box;
    }
    .rt-gear-add-form input:focus { border-color:#3699ff; }
    .rt-gear-add-btn {
        width:100%; background:#3699ff; color:#fff; border:none;
        border-radius:5px; padding:4px 0; font-size:.72rem; font-weight:600;
        cursor:pointer; transition:background .15s;
    }
    .rt-gear-add-btn:hover { background:#2563eb; }

    /* ── CỘT MỞ RỘNG BẢNG 24 TRẠM ──────────────────────────────── */
    .rt-table th.col-extra,
    .rt-table td.col-extra { text-align:right; }
    .rt-table th.col-hidden,
    .rt-table td.col-hidden { display:none; }
    .rt-tbl-gear-wrap { display:flex; align-items:center; gap:8px; }
    .rt-tbl-gear-btn {
        background:none; border:1px solid #e2e8f0; border-radius:6px;
        padding:3px 7px; cursor:pointer; color:#94a3b8; font-size:.72rem;
        transition:all .2s; white-space:nowrap;
    }
    .rt-tbl-gear-btn:hover { border-color:#3699ff; color:#3699ff; background:#eff6ff; }
    .rt-tbl-dropdown {
        display:none; position:absolute; right:0; top:32px; z-index:200;
        background:#fff; border:1px solid #e2e8f0; border-radius:8px;
        padding:8px 0; min-width:160px;
        box-shadow:0 4px 20px rgba(0,0,0,.1);
    }
    .rt-tbl-dropdown.open { display:block; }

    /* Loading */
    .sl-loading {
        display:flex; align-items:center; justify-content:center;
        min-height:160px; color:#94a3b8; font-size:.85rem; gap:10px;
    }
    .sl-spinner { width:20px; height:20px; border:2px solid #e2e8f0; border-top-color:#3699ff; border-radius:50%; animation:spin .8s linear infinite; }
    @keyframes spin { to { transform:rotate(360deg); } }
    @keyframes fadeInUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
    @keyframes pulse-dot {
        0%,100%{ opacity:1; transform:scale(1); }
        50%    { opacity:.4; transform:scale(1.3); }
    }

    /* ── BẢNG THẤT THOÁT ─────────────────────────────────────────── */
    .tt-section {
        background:#ffffff; border-radius:16px; padding:1.75rem; margin-bottom:1.5rem;
        border:1px solid #e2e8f0; box-shadow:0 0 20px 0 rgba(76,87,125,.08);
    }
    .tt-header {
        display:flex; flex-wrap:wrap; gap:8px;
        justify-content:space-between; align-items:flex-start;
        margin-bottom:1.25rem; padding-bottom:1rem; border-bottom:1px solid #e2e8f0;
    }
    .tt-title {
        font-size:1.05rem; font-weight:700; color:#181c32;
        display:flex; align-items:center; gap:10px; margin:0;
    }
    .tt-title .icon-badge {
        width:34px; height:34px; border-radius:8px;
        background:linear-gradient(135deg,#f64e60,#ff8a00);
        display:flex; align-items:center; justify-content:center;
        font-size:.85rem; color:#fff; flex-shrink:0;
    }
    .tt-controls { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .tt-date-input {
        padding:4px 8px; border-radius:7px; font-size:.75rem;
        border:1px solid rgba(54,153,255,.25); background:rgba(255,255,255,.05);
        color:#334155; outline:none; cursor:pointer; color-scheme:light;
    }
    .tt-date-input:focus { border-color:#3699ff; }
    .tt-report-btn {
        display:inline-flex; align-items:center; gap:6px;
        padding:5px 12px; border-radius:7px; font-size:.75rem; font-weight:600;
        border:1px solid #e2e8f0; background:#f8fafc;
        color:#64748b; cursor:pointer; transition:all .2s; text-decoration:none;
    }
    .tt-report-btn:hover { background:#3699ff; border-color:#3699ff; color:#fff; }
    .tt-report-btn.green { border-color:#1bc5bd; color:#1bc5bd; background:#f0fdf9; }
    .tt-report-btn.green:hover { background:#1bc5bd; border-color:#1bc5bd; color:#fff; }
    .tt-days-btn {
        padding:5px 12px; border-radius:7px; font-size:.75rem; font-weight:600;
        border:1px solid #e2e8f0; background:#f8fafc; color:#64748b; cursor:pointer; transition:all .2s;
    }
    .tt-days-btn.active, .tt-days-btn:hover { background:#3699ff; border-color:#3699ff; color:#fff; }
    .tt-loading {
        display:flex; align-items:center; justify-content:center;
        min-height:120px; color:#94a3b8; font-size:.85rem; gap:8px;
    }
    .tt-wrap { overflow-x:auto; }
    .tt-table { width:100%; border-collapse:separate; border-spacing:0; font-size:.82rem; }
    .tt-table thead tr th {
        padding:10px 14px; text-align:right; font-size:.72rem; font-weight:700;
        color:#fff; text-transform:uppercase; letter-spacing:.5px;
        background:#3699ff; border-bottom:1px solid rgba(54,153,255,.15); white-space:nowrap;
    }
    .tt-table thead tr th:first-child { text-align:left; }
    .tt-table tbody tr td:first-child {
        text-align:left; font-weight:600; color:#334155;
        white-space:nowrap; padding:11px 14px; border-right:1px solid #e2e8f0;
        background:#ffffff; position:sticky; left:0; z-index:2; min-width:160px; max-width:200px;
    }
    .tt-table thead tr th:first-child { position:sticky; left:0; z-index:3; background:#3699ff; }
    .tt-table tbody tr td { padding:11px 14px; text-align:right; border-bottom:1px solid #f1f5f9; color:#334155; }
    .tt-table tbody tr:hover td { background:#eff6ff; }
    .tt-table tbody tr:last-child td { border-bottom:none; }
    .tt-row-raw td:first-child { border-left:3px solid #3699ff; }
    .tt-row-cap td:first-child { border-left:3px solid #1bc5bd; }
    .tt-row-kh  td:first-child { border-left:3px solid #ffa800; }
    .tt-row-nrw td:first-child { border-left:3px solid #f64e60; }
    .tt-row-tl  td:first-child { border-left:3px solid #8950fc; }
    .val-raw  { color:#60a5fa; font-weight:600; }
    .val-cap  { color:#1bc5bd; font-weight:600; }
    .val-kh   { color:#ffa800; font-weight:600; }
    .val-nrw  { color:#f87171; font-weight:600; }
    .val-tl   { font-weight:700; }
    .val-tl-ok   { color:#22c55e; }
    .val-tl-warn { color:#fbbf24; }
    .val-tl-bad  { color:#f87171; }
    .tt-today      { background:#dbeafe !important; }
    .tt-today-head { background:#2563eb !important; color:#fff !important; }
    .tt-sum-col    { background:#eff6ff !important; color:#3699ff !important; font-weight:700; }

    /* ── RESPONSIVE ─────────────────────────────────────────────── */
    /* Cập nhật Responsive cho 5 KPI */
    @media (max-width:1200px) { .kpi-row { grid-template-columns:repeat(3,1fr); } .sl-kpi-grid { grid-template-columns:repeat(3,1fr); } }
    @media (max-width:992px)  { .sl-chart-grid,.sl-chart-grid-3,.rt-grid-top,.rt-grid-bottom { grid-template-columns:1fr; } }
    @media (max-width:768px)  { .kpi-row { grid-template-columns:repeat(2,1fr); } .sl-kpi-grid { grid-template-columns:repeat(2,1fr); } }
    @media (max-width:576px)  { .kpi-row { grid-template-columns:1fr; } }
    @media (max-width:575px) {
        .sl-section,.tt-section { padding:1.25rem 1rem; }
        .sl-tab,.tt-days-btn { padding:4px 10px; font-size:.72rem; }
        .sl-kpi-grid { grid-template-columns:repeat(2,1fr); gap:8px; }
        .sl-kpi-val  { font-size:1.25rem; }
        .tt-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; margin:0 -1rem; padding:0; }
        .tt-table { font-size:.75rem; min-width:420px; }
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark"><i class="fas fa-layer-group text-primary me-2"></i>Dashboard Tổng Hợp</h3>
        <button class="btn btn-sm btn-light text-primary fw-bold shadow-sm" onclick="location.reload()">
            <i class="fas fa-sync me-1"></i> Làm mới
        </button>
    </div>

    <!-- ══ BẢNG THẤT THOÁT NƯỚC ══════════════════════════════════ -->
    <div class="tt-section">
        <div class="tt-header">
            <h4 class="tt-title">
                <span class="icon-badge"><i class="fa-solid fa-droplet-slash"></i></span>
                Bảng Theo Dõi Sản Lượng & Thất Thoát Nước
            </h4>
            <div class="tt-controls">
                <input type="date" id="tt-date-from" class="tt-date-input" title="Từ ngày">
                <span style="color:#94a3b8;font-size:.75rem;">→</span>
                <input type="date" id="tt-date-to" class="tt-date-input" title="Đến ngày" value="<?= date('Y-m-d') ?>">
                <button class="tt-days-btn" onclick="loadTTTableRange()" title="Xem theo ngày chọn">
                    <i class="fa-solid fa-search"></i>
                </button>
                <a href="/quanly/nhat-ky/san-luong-dong-ho" class="tt-report-btn green" title="Sản lượng đồng hồ KH">
                    <i class="fa-solid fa-gauge-high"></i><span>Đồng hồ KH</span>
                </a>
            </div>
        </div>
        <div id="tt-content">
            <div class="tt-loading"><div class="sl-spinner"></div> Đang tải...</div>
        </div>
    </div>

    <!-- ══ SẢN LƯỢNG & VẬN HÀNH SCADA ═══════════════════════════ -->
    <div class="sl-section">
        <div class="sl-section-header">
            <h4 class="sl-section-title">
                <span class="icon-badge"><i class="fa-solid fa-satellite-dish"></i></span>
                Sản Lượng & Vận Hành SCADA
                <span id="sl-live-dot" style="width:8px;height:8px;border-radius:50%;background:#1bc5bd;animation:pulse-dot 2s infinite;" title="Live data"></span>
            </h4>
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                <!-- Bộ lọc khoảng thời gian cho SCADA -->
                <div id="sl-date-filter" class="tt-controls" style="display:flex; align-items:center; gap:8px;">
                    <input type="date" id="sl-date-from" class="tt-date-input" title="Từ ngày">
                    <span style="color:#94a3b8;font-size:.75rem;">→</span>
                    <input type="date" id="sl-date-to" class="tt-date-input" title="Đến ngày" value="<?= date('Y-m-d') ?>">
                    <button class="tt-days-btn" onclick="applySLFilter()" title="Lọc dữ liệu">
                        <i class="fa-solid fa-search"></i>
                    </button>
                </div>
                <div class="sl-tabs">
                    <button class="sl-tab active" onclick="switchSLTab('ngay',this)">Theo Ngày</button>
                    <button class="sl-tab" onclick="switchSLTab('thang',this)">Theo Tháng</button>
                    <button class="sl-tab" onclick="switchSLTab('nam',this)">Theo Năm</button>
                    <button class="sl-tab" onclick="switchSLTab('realtime',this)">Realtime</button>
                </div>
                <a href="/quanly/nhat-ky/bao-cao" class="tt-report-btn" title="Báo cáo hàng ngày">
                    <i class="fa-solid fa-file-excel"></i><span>Báo cáo</span>
                </a>
            </div>
        </div>

        <!-- KPI mini row -->
        <!-- Đã thêm ô số 5 cho Polymer -->
        <div class="sl-kpi-grid" id="sl-kpi-row">
            <div class="sl-kpi" style="--kpi-color:#3699ff;">
                <div class="sl-kpi-label" id="kpi-nuoc-label">Sản lượng nước sạch</div>
                <div class="sl-kpi-val" id="kpi-nuoc">—<span class="sl-kpi-unit">m³</span></div>
                <div class="sl-kpi-trend" id="kpi-nuoc-trend"></div>
            </div>
            <div class="sl-kpi" style="--kpi-color:#ffa800;">
                <div class="sl-kpi-label" id="kpi-dien-label">Điện năng tiêu thụ</div>
                <div class="sl-kpi-val" id="kpi-dien">—<span class="sl-kpi-unit">KWh</span></div>
                <div class="sl-kpi-sub" id="kpi-dien-sub"></div>
            </div>
            <div class="sl-kpi" style="--kpi-color:#1bc5bd;">
                <div class="sl-kpi-label" id="kpi-pac-label">PAC tiêu thụ</div>
                <div class="sl-kpi-val" id="kpi-pac">—<span class="sl-kpi-unit">kg</span></div>
                <div class="sl-kpi-sub" id="kpi-pac-sub"></div>
            </div>
            <div class="sl-kpi" style="--kpi-color:#8950fc;">
                <div class="sl-kpi-label" id="kpi-clo-label">Chlorine tiêu thụ</div>
                <div class="sl-kpi-val" id="kpi-clo">—<span class="sl-kpi-unit">kg</span></div>
                <div class="sl-kpi-sub" id="kpi-clo-sub"></div>
            </div>
            <div class="sl-kpi" style="--kpi-color:#f64e60;">
                <div class="sl-kpi-label" id="kpi-poly-label">Polymer tiêu thụ</div>
                <div class="sl-kpi-val" id="kpi-poly">—<span class="sl-kpi-unit">kg</span></div>
                <div class="sl-kpi-sub" id="kpi-poly-sub"></div>
            </div>
        </div>

        <!-- Chart area -->
        <div id="sl-content">
            <div class="sl-loading"><div class="sl-spinner"></div> Đang tải dữ liệu SCADA...</div>
        </div>
    </div>

    <!-- ══ KPI 5 đối tượng hàng 1 ════════════════════════════════ -->
    <div class="kpi-row">
        <a href="<?= $urlNhaMay ?>" class="kpi-card border-blue">
            <div class="kpi-icon-wrapper">
                <div class="kpi-icon bg-light-blue"><i class="fa-solid fa-industry"></i></div>
                <span class="badge bg-light text-dark">Hoạt động</span>
            </div>
            <div>
                <span class="kpi-value"><?= number_format($cntNhaMay) ?></span>
                <div class="kpi-title">Nhà máy nước</div>
            </div>
        </a>
        <a href="<?= $urlTruyenDan ?>" class="kpi-card border-blue">
            <div class="kpi-icon-wrapper">
                <div class="kpi-icon bg-light-blue"><i class="fa-solid fa-code-branch"></i></div>
                <span class="badge bg-primary text-white">Chính</span>
            </div>
            <div>
                <span class="kpi-value"><?= number_format($lenTruyenDan,1) ?> <span class="kpi-unit">km</span></span>
                <div class="kpi-title">Ống truyền dẫn</div>
            </div>
        </a>
        <a href="<?= $urlPhanPhoi ?>" class="kpi-card border-purple">
            <div class="kpi-icon-wrapper">
                <div class="kpi-icon bg-light-purple"><i class="fa-solid fa-network-wired"></i></div>
                <span class="badge bg-info text-white">Mạng lưới</span>
            </div>
            <div>
                <span class="kpi-value"><?= number_format($lenPhanPhoi,1) ?> <span class="kpi-unit">km</span></span>
                <div class="kpi-title">Ống phân phối</div>
            </div>
        </a>
        <a href="<?= $urlDHTong ?>" class="kpi-card border-green">
            <div class="kpi-icon-wrapper"><div class="kpi-icon bg-light-green"><i class="fa-solid fa-gauge-high"></i></div></div>
            <div>
                <span class="kpi-value"><?= number_format($cntDHTong) ?></span>
                <div class="kpi-title">Đồng hồ tổng</div>
            </div>
        </a>
        <a href="<?= $urlDHKhuVuc ?>" class="kpi-card border-green">
            <div class="kpi-icon-wrapper">
                <div class="kpi-icon bg-light-green"><i class="fa-solid fa-users-viewfinder"></i></div>
                <span class="badge bg-success text-white">Khách hàng</span>
            </div>
            <div>
                <span class="kpi-value"><?= number_format($cntDHKhuVuc) ?></span>
                <div class="kpi-title">Đồng hồ hộ dân</div>
            </div>
        </a>
    </div>

    <!-- ══ KPI 5 đối tượng hàng 2 ════════════════════════════════ -->
    <div class="kpi-row">
        <a href="<?= $urlVan ?>" class="kpi-card border-orange">
            <div class="kpi-icon-wrapper"><div class="kpi-icon bg-light-orange"><i class="fa-solid fa-faucet"></i></div></div>
            <div><span class="kpi-value"><?= number_format($cntVan) ?></span><div class="kpi-title">Van mạng lưới</div></div>
        </a>
        <a href="<?= $urlMoiNoi ?>" class="kpi-card border-orange">
            <div class="kpi-icon-wrapper"><div class="kpi-icon bg-light-orange"><i class="fa-solid fa-link"></i></div></div>
            <div><span class="kpi-value"><?= number_format($cntMoiNoi) ?></span><div class="kpi-title">Điểm mối nối</div></div>
        </a>
        <a href="<?= $urlHam ?>" class="kpi-card border-orange">
            <div class="kpi-icon-wrapper"><div class="kpi-icon bg-light-orange"><i class="fa-solid fa-dungeon"></i></div></div>
            <div><span class="kpi-value"><?= number_format($cntHam) ?></span><div class="kpi-title">Hầm kỹ thuật</div></div>
        </a>
        <a href="<?= $urlCoc ?>" class="kpi-card border-orange">
            <div class="kpi-icon-wrapper"><div class="kpi-icon bg-light-orange"><i class="fa-solid fa-map-pin"></i></div></div>
            <div><span class="kpi-value"><?= number_format($cntCoc) ?></span><div class="kpi-title">Cọc mốc giới</div></div>
        </a>
        <a href="<?= $urlSuCo ?>" class="kpi-card border-red">
            <div class="kpi-icon-wrapper">
                <div class="kpi-icon bg-light-red"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <span class="badge bg-danger text-white">Cảnh báo</span>
            </div>
            <div><span class="kpi-value"><?= number_format($cntSuCo) ?></span><div class="kpi-title">Sự cố chờ xử lý</div></div>
        </a>
    </div>

    <!-- ══ 6 BIỂU ĐỒ GIS ═════════════════════════════════════════ -->
    <div class="row g-4 mb-4">
        <div class="col-lg-4 col-md-6">
            <div class="chart-panel">
                <div class="panel-header"><h5 class="panel-title"><i class="fa-solid fa-chart-bar text-primary"></i> Vật liệu Ống phân phối</h5></div>
                <div class="chart-wrapper"><canvas id="pipeChart"></canvas></div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="chart-panel">
                <div class="panel-header"><h5 class="panel-title"><i class="fa-solid fa-chart-pie text-success"></i> Hãng Đồng hồ</h5></div>
                <div class="chart-wrapper"><canvas id="meterChart"></canvas></div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="chart-panel">
                <div class="panel-header"><h5 class="panel-title"><i class="fa-solid fa-chart-column text-danger"></i> Nguyên nhân sự cố</h5></div>
                <div class="chart-wrapper"><canvas id="incidentChart"></canvas></div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="chart-panel">
                <div class="panel-header"><h5 class="panel-title"><i class="fa-solid fa-gauge text-warning"></i> Tình trạng Van</h5></div>
                <div class="chart-wrapper"><canvas id="valveChart"></canvas></div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="chart-panel">
                <div class="panel-header"><h5 class="panel-title"><i class="fa-solid fa-cubes text-info"></i> Phân loại Hầm kỹ thuật</h5></div>
                <div class="chart-wrapper"><canvas id="hamChart"></canvas></div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="chart-panel">
                <div class="panel-header"><h5 class="panel-title"><i class="fa-solid fa-link text-dark"></i> Loại Mối nối</h5></div>
                <div class="chart-wrapper"><canvas id="moinoiChart"></canvas></div>
            </div>
        </div>
    </div>

    <!-- ══ DANH SÁCH SỰ CỐ ═══════════════════════════════════════ -->
    <div class="row">
        <div class="col-12">
            <div class="chart-panel" style="height:auto;min-height:auto;">
                <div class="panel-header border-0">
                    <h5 class="panel-title"><i class="fa-solid fa-list-check text-danger"></i> Nhật ký sự cố mới nhất</h5>
                    <a href="<?= $urlSuCo ?>" class="btn btn-sm btn-light text-primary fw-bold">Xem tất cả</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Mã SC</th>
                                <th>Sự cố & Vị trí</th>
                                <th>Nguyên nhân</th>
                                <th>Thời gian báo</th>
                                <th>Trạng thái</th>
                                <th class="text-end pe-4">Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentIncidents as $sc): ?>
                            <tr>
                                <td class="ps-4 fw-bold">#<?= isset($sc->masuco) ? $sc->masuco : $sc->id ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= $sc->loaisuco ? $sc->loaisuco->ten : 'Chưa phân loại' ?></div>
                                    <small class="text-muted"><i class="fa-solid fa-location-dot me-1"></i> <?= $sc->vitri ?></small>
                                </td>
                                <td><?= $sc->nguyennhansuco ? $sc->nguyennhansuco->ten : '-' ?></td>
                                <td><?= $sc->n_phathien ? Yii::$app->formatter->asDate(str_replace('/', '-', $sc->n_phathien), 'php:d/m/Y H:i') : '<i class="text-muted" style="font-size: 0.85em;">Chưa nhập ngày phát hiện</i>' ?></td>
                                <td>
                                    <?php
                                        $colors = [
                                            ['bg' => '#c9f7f5', 'text' => '#1bc5bd'], // xanh ngọc
                                            ['bg' => '#fff4de', 'text' => '#ffa800'], // cam
                                            ['bg' => '#ffe2e5', 'text' => '#f64e60'], // đỏ
                                            ['bg' => '#e1f0ff', 'text' => '#3699ff'], // xanh dương
                                            ['bg' => '#d4edda', 'text' => '#28a745'], // xanh lá
                                            ['bg' => '#d1ecf1', 'text' => '#17a2b8'], // cyan
                                            ['bg' => '#eee5ff', 'text' => '#8950fc'], // tím
                                            ['bg' => '#fce8e6', 'text' => '#ea4335'], // đỏ nhạt
                                        ];
                                        if ($sc->tinhtrang !== null && isset($sc->tinhtrang->ten)) {
                                            $ttId = (int)$sc->tinhtrang_id;
                                            $colorIdx = $ttId > 0 ? ($ttId - 1) % count($colors) : 0;
                                            $ttColor = $colors[$colorIdx];
                                            echo '<span class="badge fw-bold px-3 py-2" style="background:' . $ttColor['bg'] . ';color:' . $ttColor['text'] . ';font-size:.78rem;">' . \yii\helpers\Html::encode($sc->tinhtrang->ten) . '</span>';
                                        } else {
                                            echo '<span class="badge fw-bold px-3 py-2" style="background:#f3f6f9;color:#7e8299;font-size:.78rem;">Chưa cập nhật</span>';
                                        }
                                    ?>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="<?= Url::to(['hocau/suco/view','id'=>$sc->id]) ?>" class="btn btn-icon btn-sm btn-light-primary"><i class="fa-solid fa-arrow-right"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    Chart.defaults.font.family = "'Segoe UI', sans-serif";
    Chart.defaults.color       = '#7e8299';
    Chart.defaults.scale.grid.color = '#f3f6f9';

    // ── GIS Charts ───────────────────────────────────────────────
    function handleChartClick(urlBase, paramName, ids) {
        return (e, elements) => {
            if (elements.length > 0) {
                window.location.href = urlBase + '?' + paramName + '=' + ids[elements[0].index];
            }
        };
    }
    new Chart(document.getElementById('pipeChart'), {
        type:'bar',
        data:{ labels:<?= $pipeLabels ?>, datasets:[{label:'Số lượng',data:<?= $pipeValues ?>,backgroundColor:'#3699ff',borderRadius:4,barThickness:20}] },
        options:{ indexAxis:'y',maintainAspectRatio:false,plugins:{legend:{display:false}},onClick:handleChartClick('<?= Url::to(['hocau/ongphanphoi/index']) ?>','OngphanphoiSearch[loaiong_id]',<?= $pipeIds ?>) }
    });
    new Chart(document.getElementById('meterChart'), {
        type:'doughnut',
        data:{ labels:<?= $meterLabels ?>, datasets:[{data:<?= $meterValues ?>,backgroundColor:['#1bc5bd','#3699ff','#8950fc','#ffa800'],borderWidth:0}] },
        options:{ maintainAspectRatio:false,cutout:'70%',plugins:{legend:{position:'right',labels:{boxWidth:10,usePointStyle:true}}},onClick:handleChartClick('<?= Url::to(['hocau/donghonhamay/index']) ?>','DonghonhamaySearch[hieudongho_id]',<?= $meterIds ?>) }
    });
    new Chart(document.getElementById('incidentChart'), {
        type:'bar',
        data:{ labels:<?= $incidentLabels ?>, datasets:[{label:'Vụ việc',data:<?= $incidentValues ?>,backgroundColor:'#f64e60',borderRadius:4}] },
        options:{ maintainAspectRatio:false,plugins:{legend:{display:false}},onClick:handleChartClick('<?= Url::to(['hocau/suco/index']) ?>','SucoSearch[nguyennhansuco_id]',<?= $incidentIds ?>) }
    });
    new Chart(document.getElementById('valveChart'), {
        type:'pie',
        data:{ labels:<?= $valveLabels ?>, datasets:[{data:<?= $valveValues ?>,backgroundColor:['#1bc5bd','#ffa800','#e4e6ef'],borderWidth:0}] },
        options:{ maintainAspectRatio:false,plugins:{legend:{position:'bottom',labels:{usePointStyle:true}}},onClick:handleChartClick('<?= Url::to(['hocau/van/index']) ?>','VanSearch[tinhtrang_id]',<?= $valveIds ?>) }
    });
    new Chart(document.getElementById('hamChart'), {
        type:'polarArea',
        data:{ labels:<?= $hamLabels ?>, datasets:[{data:<?= $hamValues ?>,backgroundColor:['rgba(137,80,252,.7)','rgba(54,153,255,.7)','rgba(27,197,189,.7)'],borderWidth:1}] },
        options:{ maintainAspectRatio:false,scales:{r:{ticks:{display:false}}},plugins:{legend:{position:'right',labels:{boxWidth:10}}},onClick:handleChartClick('<?= Url::to(['hocau/hamkythuat/index']) ?>','HamkythuatSearch[loaiham_id]',<?= $hamIds ?>) }
    });
    new Chart(document.getElementById('moinoiChart'), {
        type:'bar',
        data:{ labels:<?= $moinoiLabels ?>, datasets:[{label:'Số lượng',data:<?= $moinoiValues ?>,backgroundColor:'#181c32',borderRadius:4}] },
        options:{ maintainAspectRatio:false,plugins:{legend:{display:false}},onClick:handleChartClick('<?= Url::to(['hocau/moinoi/index']) ?>','MoinoiSearch[loaimoinoi_id]',<?= $moinoiIds ?>) }
    });

    // ════════════════════════════════════════════════════════════
    // SẢN LƯỢNG SCADA
    // ════════════════════════════════════════════════════════════
    const IOT_BASE = '/iot_api.php';
    const IOT_KEY  = 'SCADA_HOCAU_2024_SECRET_KEY';

    let slCharts = {};
    let curTab   = 'ngay';
    // DB data fetch từ api-van-hanh (điện, hóa chất từ nk_giao_ca)
    let dbCache  = null;

    const C = {
        blue:   '#3699ff', cyan:  '#00d4ff', green: '#1bc5bd',
        amber:  '#ffa800', rose:  '#f64e60', purple:'#8950fc',
        blue2:  'rgba(54,153,255,.18)',  amber2:'rgba(255,168,0,.18)',
        green2: 'rgba(27,197,189,.18)',  purple2:'rgba(137,80,252,.18)',
        teal2:  'rgba(0,212,255,.18)',
    };

    const darkOpts = {
        color: '#5a82a8',
        scale: { grid:{ color:'rgba(54,153,255,.07)' }, ticks:{ color:'#3d6080' } }
    };

    function destroyCharts() {
        Object.values(slCharts).forEach(function(c){ try{c.destroy();}catch(e){} });
        slCharts = {};
    }
    function fmt(n) {
        if (n === null || n === undefined || isNaN(n)) return '—';
        if (n >= 1e6) return (n/1e6).toFixed(2) + 'M';
        if (n >= 1e3) return (n/1e3).toFixed(1) + 'K';
        return parseFloat(n).toFixed(1);
    }
    function fmtN(n) {
        if (!n) return '—';
        return Math.round(n).toLocaleString('vi-VN');
    }
    function cumsum(arr) {
        var s = 0;
        return arr.map(function(v){ s += (v||0); return s; });
    }
    function showEmpty() {
        document.getElementById('sl-content').innerHTML = '<div class="sl-loading" style="color:#3d5a78;"><i class="fa-solid fa-database me-2"></i>Chưa có dữ liệu cho kỳ này</div>';
    }

    // ── Helpers KPI ─────────────────────────────────────────────
    // Cập nhật hàm setKpiLabels có thêm biến l5 cho Polymer
    function setKpiLabels(l1, l2, l3, l4, l5) {
        document.getElementById('kpi-nuoc-label').textContent = l1 || 'Sản lượng nước sạch';
        document.getElementById('kpi-dien-label').textContent = l2 || 'Điện năng tiêu thụ';
        document.getElementById('kpi-pac-label').textContent  = l3 || 'PAC tiêu thụ';
        document.getElementById('kpi-clo-label').textContent  = l4 || 'Chlorine tiêu thụ';
        if(document.getElementById('kpi-poly-label')) {
            document.getElementById('kpi-poly-label').textContent = l5 || 'Polymer tiêu thụ';
        }
    }
    
    // Cập nhật updateKPI có thêm tham số poly và polySub
    function updateKPI(nuoc, dien, pac, clo, poly, dienSub, pacSub, cloSub, polySub) {
        if (nuoc !== null) document.getElementById('kpi-nuoc').innerHTML = nuoc;
        if (dien !== null) document.getElementById('kpi-dien').innerHTML = dien;
        if (pac  !== null) document.getElementById('kpi-pac').innerHTML  = pac;
        if (clo  !== null) document.getElementById('kpi-clo').innerHTML  = clo;
        if (poly !== null && document.getElementById('kpi-poly')) document.getElementById('kpi-poly').innerHTML = poly;
        
        document.getElementById('kpi-dien-sub').textContent = dienSub || '';
        document.getElementById('kpi-pac-sub').textContent  = pacSub  || '';
        document.getElementById('kpi-clo-sub').textContent  = cloSub  || '';
        if(document.getElementById('kpi-poly-sub')) document.getElementById('kpi-poly-sub').textContent = polySub || '';
    }

    // Fetch điện + hóa chất từ nk_giao_ca qua api-van-hanh
    function fetchDbData(callback) {
        if (dbCache !== null) { callback(dbCache); return; }
        var from = document.getElementById('sl-date-from') ? document.getElementById('sl-date-from').value : '';
        var to = document.getElementById('sl-date-to') ? document.getElementById('sl-date-to').value : '';
        var url = '<?= \yii\helpers\Url::to(['/quanly/nhat-ky/api-van-hanh']) ?>';
        if (from && to) {
            var separator = url.indexOf('?') !== -1 ? '&' : '?';
            url += separator + 'tu_ngay=' + encodeURIComponent(from) + '&den_ngay=' + encodeURIComponent(to);
        }
        fetch(url)
            .then(function(r){ return r.json(); })
            .then(function(d){ dbCache = d; callback(d); })
            .catch(function(){ dbCache = {}; callback({}); });
    }

    // ── Merge SCADA data với DB data theo label ngày ─────────────
    // DB trả về: { ngay_data: [{ngay, dien, pac, chlorine, polymer}], thang_data: [...] }
    function mergeWithDb(labels, scadaArr, dbArr, dbKey) {
        if (!dbArr || !dbArr.length) return scadaArr;
        var dbMap = {};
        dbArr.forEach(function(row){ dbMap[row.ngay] = row; });
        return labels.map(function(lbl, i) {
            var scVal = (scadaArr && scadaArr[i]) || 0;
            if (scVal > 0) return scVal; // SCADA có data thì dùng SCADA
            var dbRow = dbMap[lbl];
            return (dbRow && dbRow[dbKey]) ? parseFloat(dbRow[dbKey]) : 0;
        });
    }

    // ── Lọc tháng / năm hiện tại ────────────────────────────────
    function filterByMonth(labels) {
        var now = new Date();
        var ym  = now.getFullYear() + '-' + String(now.getMonth()+1).padStart(2,'0');
        var idx = [];
        labels.forEach(function(l, i){ if(l && l.startsWith(ym)) idx.push(i); });
        return idx;
    }
    function filterByYear(labels) {
        var yr = String(new Date().getFullYear());
        var idx = [];
        labels.forEach(function(l, i){ if(l && l.startsWith(yr)) idx.push(i); });
        return idx;
    }

    // ════════════════════════════════════════════════════════════
    // Tab: Theo Ngày
    // ════════════════════════════════════════════════════════════
    function renderNgay(d, dbData) {
        var dbNgay = (dbData && dbData.ngay_data) ? dbData.ngay_data : [];
        if (!dbNgay.length) { showEmpty(); return; }

        var from = document.getElementById('sl-date-from') ? document.getElementById('sl-date-from').value : '';
        var to = document.getElementById('sl-date-to') ? document.getElementById('sl-date-to').value : '';
        var rows;
        var monthLabel;
        if (from && to) {
            rows = dbNgay.filter(function(r){ return r.ngay && r.ngay >= from && r.ngay <= to; });
            var fmtD = function(str) {
                var p = str.split('-');
                return p[2] + '/' + p[1] + '/' + p[0];
            };
            monthLabel = 'từ ' + fmtD(from) + ' đến ' + fmtD(to);
        } else {
            var now = new Date();
            var ym  = now.getFullYear() + '-' + String(now.getMonth()+1).padStart(2,'0');
            rows = dbNgay.filter(function(r){ return r.ngay && r.ngay.indexOf(ym) === 0; });
            monthLabel = 'tháng ' + (now.getMonth()+1) + '/' + now.getFullYear();
        }
        if (!rows.length) { showEmpty(); return; }

        var labels = rows.map(function(r){ return r.ngay; });
        var nuocM  = rows.map(function(r){ return parseFloat(r.nuoc_sach || 0); });
        var dienM  = rows.map(function(r){ return parseFloat(r.dien      || 0); });
        var pacM   = rows.map(function(r){ return parseFloat(r.pac       || 0); });
        var cloM   = rows.map(function(r){ return parseFloat(r.chlorine  || 0); });
        var polyM  = rows.map(function(r){ return parseFloat(r.polymer   || 0); });

        var totNuoc = nuocM.reduce(function(s,v){return s+(v||0);},0);
        var totDien = dienM.reduce(function(s,v){return s+(v||0);},0);
        var totPac  = pacM.reduce(function(s,v){return s+(v||0);},0);
        var totClo  = cloM.reduce(function(s,v){return s+(v||0);},0);
        var totPoly = polyM.reduce(function(s,v){return s+(v||0);},0); // Đã thêm tổng Poly

        setKpiLabels(
            'Tổng nước sạch ' + monthLabel,
            'Tổng điện năng ' + monthLabel,
            'Tổng PAC ' + monthLabel,
            'Tổng Chlorine ' + monthLabel,
            'Tổng Polymer ' + monthLabel
        );
        var dienNote = totDien > 0 ? '' : '(từ nhật ký vận hành)';
        updateKPI(
            fmtN(totNuoc) + '<span class="sl-kpi-unit">m³</span>',
            fmtN(totDien) + '<span class="sl-kpi-unit">KWh</span>',
            fmtN(totPac)  + '<span class="sl-kpi-unit">kg</span>',
            fmtN(totClo)  + '<span class="sl-kpi-unit">kg</span>',
            fmtN(totPoly) + '<span class="sl-kpi-unit">kg</span>',
            dienNote, '', '', ''
        );

        // Đổi layout .sl-chart-grid sang .sl-chart-stack (chồng lên nhau)
        // và tăng min-height cho canvas để nhìn biểu đồ to, đẹp hơn khi ngang
        document.getElementById('sl-content').innerHTML =
            '<div class="sl-chart-stack">' +
                '<div class="sl-card">' +
                    '<div class="sl-card-title"><span class="dot" style="--dot-color:#3699ff;"></span>' +
                        'Nước sạch & Điện năng theo ngày — ' + monthLabel +
                    '</div>' +
                    '<div class="sl-canvas-wrap" style="min-height:300px;"><canvas id="slCumNgay"></canvas></div>' +
                '</div>' +
                '<div class="sl-card">' +
                    '<div class="sl-card-title"><span class="dot" style="--dot-color:#1bc5bd;"></span>' +
                        'Hóa chất xử lý & Nước sạch theo ngày — ' + monthLabel +
                    '</div>' +
                    '<div class="sl-canvas-wrap" style="min-height:300px;"><canvas id="slHoaChatNgay"></canvas></div>' +
                '</div>' +
            '</div>';

        slCharts.cumNgay = new Chart(document.getElementById('slCumNgay'), {
            type: 'bar',
            data: { labels: labels, datasets: [
                { type: 'bar',  label: 'Điện năng (KWh)', data: dienM,
                  backgroundColor: C.amber2, borderColor: C.amber, borderWidth: 1, borderRadius: 3, yAxisID: 'y1' },
                { type: 'line', label: 'Nước sạch (m³)',  data: nuocM,
                  borderColor: C.blue, backgroundColor: 'transparent', borderWidth: 2.5,
                  tension: .3, pointRadius: 3, pointBackgroundColor: C.blue, yAxisID: 'y' },
            ]},
            options: { ...darkOpts, responsive: true, maintainAspectRatio: false,
                plugins: { legend: { labels: { color: '#5a82a8', usePointStyle: true, boxWidth: 8, font: { size: 11 } } } },
                scales: {
                    x:  { ticks: { color: '#3d6080', maxTicksLimit: 14, font: { size: 10 } }, grid: { display: false } },
                    y:  { ticks: { color: '#3d6080', font: { size: 10 }, callback: function(v){ return fmt(v); } },
                          grid: { color: 'rgba(54,153,255,.07)' },
                          title: { display: true, text: 'm³', color: '#3d6080', font: { size: 10 } } },
                    y1: { position: 'right', ticks: { color: '#c08000', font: { size: 10 }, callback: function(v){ return fmt(v); } },
                          grid: { display: false },
                          title: { display: true, text: 'KWh', color: '#c08000', font: { size: 10 } } },
                }
            }
        });

        slCharts.hcNgay = new Chart(document.getElementById('slHoaChatNgay'), {
            data: { labels: labels, datasets: [
                { type: 'line', label: 'Nước sạch (m³)', data: nuocM, borderColor: C.blue, backgroundColor: 'transparent', borderWidth: 2.5, tension: .3, pointRadius: 3, pointBackgroundColor: C.blue, yAxisID: 'y1' },
                { type: 'bar',  label: 'PAC (kg)',       data: pacM,  backgroundColor: 'rgba(27,197,189,.6)',  borderColor: C.green,  borderWidth: 1, borderRadius: 3, yAxisID: 'y' },
                { type: 'bar',  label: 'Chlorine (kg)',  data: cloM,  backgroundColor: 'rgba(137,80,252,.6)', borderColor: C.purple, borderWidth: 1, borderRadius: 3, yAxisID: 'y' },
                { type: 'bar',  label: 'Polymer (kg)',   data: polyM, backgroundColor: 'rgba(255,168,0,.5)',  borderColor: C.amber,  borderWidth: 1, borderRadius: 3, yAxisID: 'yPoly' },
            ]},
            options: { ...darkOpts, responsive: true, maintainAspectRatio: false,
                plugins: { legend: { labels: { color: '#5a82a8', usePointStyle: true, boxWidth: 8, font: { size: 11 } } } },
                scales: {
                    x: { ticks: { color: '#3d6080', maxTicksLimit: 14, font: { size: 10 } }, grid: { display: false } },
                    y: { 
                        type: 'linear', display: true, position: 'left',
                        ticks: { color: '#3d6080', font: { size: 10 } }, grid: { color: 'rgba(54,153,255,.07)' },
                        title: { display: true, text: 'Hóa chất (kg)', color: '#3d6080', font: { size: 10 } }
                    },
                    y1: {
                        type: 'linear', display: true, position: 'right',
                        ticks: { color: '#3699ff', font: { size: 10 }, callback: function(v){ return fmt(v); } }, grid: { display: false },
                        title: { display: true, text: 'Nước sạch (m³)', color: '#3699ff', font: { size: 10 } }
                    },
                    yPoly: {
                        type: 'linear', display: true, position: 'right',
                        ticks: { color: '#c08000', font: { size: 10 }, callback: function(v){ return fmt(v); } }, grid: { display: false },
                        title: { display: true, text: 'Polymer (kg)', color: '#c08000', font: { size: 10 } }
                    }
                }
            }
        });
    }

    // ════════════════════════════════════════════════════════════
    // Tab: Theo Tháng
    // ════════════════════════════════════════════════════════════
    function renderThang(d, dbData) {
        var dbThang = (dbData && dbData.thang_data) ? dbData.thang_data : [];
        if (!dbThang.length) { showEmpty(); return; }

        var from = document.getElementById('sl-date-from') ? document.getElementById('sl-date-from').value : '';
        var to = document.getElementById('sl-date-to') ? document.getElementById('sl-date-to').value : '';
        var rows;
        var yrLabel;
        if (from && to) {
            var fromM = from.substring(0, 7);
            var toM = to.substring(0, 7);
            rows = dbThang.filter(function(r){ return r.thang && r.thang >= fromM && r.thang <= toM; });
            var fmtM = function(str) {
                var p = str.split('-');
                return p[1] + '/' + p[0];
            };
            yrLabel = 'từ ' + fmtM(fromM) + ' đến ' + fmtM(toM);
        } else {
            var yr = String(new Date().getFullYear());
            rows = dbThang.filter(function(r){ return r.thang && r.thang.indexOf(yr) === 0; });
            yrLabel = 'năm ' + yr;
        }
        rows = rows.sort(function(a,b){ return a.thang < b.thang ? -1 : (a.thang > b.thang ? 1 : 0); });
        if (!rows.length) { showEmpty(); return; }

        var mLabels = rows.map(function(r){ return r.thang; });
        var mNuoc   = rows.map(function(r){ return parseFloat(r.nuoc_sach || 0); });
        var mDien   = rows.map(function(r){ return parseFloat(r.dien      || 0); });
        var mPac    = rows.map(function(r){ return parseFloat(r.pac       || 0); });
        var mClo    = rows.map(function(r){ return parseFloat(r.chlorine  || 0); });
        var mPoly   = rows.map(function(r){ return parseFloat(r.polymer   || 0); });

        var totNuoc = mNuoc.reduce(function(s,v){return s+(v||0);},0);
        var totDien = mDien.reduce(function(s,v){return s+(v||0);},0);
        var totPac  = mPac.reduce(function(s,v){return s+(v||0);},0);
        var totClo  = mClo.reduce(function(s,v){return s+(v||0);},0);
        var totPoly = mPoly.reduce(function(s,v){return s+(v||0);},0); // Đã thêm tổng Poly

        setKpiLabels(
            'Tổng nước sạch ' + yrLabel,
            'Tổng điện năng ' + yrLabel,
            'Tổng PAC ' + yrLabel,
            'Tổng Chlorine ' + yrLabel,
            'Tổng Polymer ' + yrLabel
        );
        updateKPI(
            fmtN(totNuoc) + '<span class="sl-kpi-unit">m³</span>',
            fmtN(totDien) + '<span class="sl-kpi-unit">KWh</span>',
            fmtN(totPac)  + '<span class="sl-kpi-unit">kg</span>',
            fmtN(totClo)  + '<span class="sl-kpi-unit">kg</span>',
            fmtN(totPoly) + '<span class="sl-kpi-unit">kg</span>',
            '', '', '', ''
        );

        // Đổi layout .sl-chart-grid sang .sl-chart-stack
        document.getElementById('sl-content').innerHTML =
            '<div class="sl-chart-stack">' +
                '<div class="sl-card">' +
                    '<div class="sl-card-title"><span class="dot" style="--dot-color:#3699ff;"></span>' +
                        'Nước sạch & Điện năng theo tháng — ' + yrLabel +
                    '</div>' +
                    '<div class="sl-canvas-wrap" style="min-height:300px;"><canvas id="slCumThang"></canvas></div>' +
                '</div>' +
                '<div class="sl-card">' +
                    '<div class="sl-card-title"><span class="dot" style="--dot-color:#1bc5bd;"></span>' +
                        'Hóa chất xử lý & Nước sạch theo tháng — ' + yrLabel +
                    '</div>' +
                    '<div class="sl-canvas-wrap" style="min-height:300px;"><canvas id="slHoaChatThang"></canvas></div>' +
                '</div>' +
            '</div>';

        slCharts.cumThang = new Chart(document.getElementById('slCumThang'), {
            type: 'bar',
            data: { labels: mLabels, datasets: [
                { type: 'bar',  label: 'Điện năng (KWh)', data: mDien,
                  backgroundColor: C.amber2, borderColor: C.amber, borderWidth: 1, borderRadius: 3, yAxisID: 'y1' },
                { type: 'line', label: 'Nước sạch (m³)',  data: mNuoc,
                  borderColor: C.blue, backgroundColor: 'transparent', borderWidth: 2.5,
                  tension: .3, pointRadius: 4, pointBackgroundColor: C.blue, yAxisID: 'y' },
            ]},
            options: { ...darkOpts, responsive: true, maintainAspectRatio: false,
                plugins: { legend: { labels: { color: '#5a82a8', usePointStyle: true, boxWidth: 8, font: { size: 11 } } } },
                scales: {
                    x:  { ticks: { color: '#3d6080', font: { size: 10 } }, grid: { display: false } },
                    y:  { ticks: { color: '#3d6080', font: { size: 10 }, callback: function(v){ return fmt(v); } },
                          grid: { color: 'rgba(54,153,255,.07)' },
                          title: { display: true, text: 'm³', color: '#3d6080', font: { size: 10 } } },
                    y1: { position: 'right', ticks: { color: '#c08000', font: { size: 10 }, callback: function(v){ return fmt(v); } },
                          grid: { display: false },
                          title: { display: true, text: 'KWh', color: '#c08000', font: { size: 10 } } },
                }
            }
        });

        slCharts.hcThang = new Chart(document.getElementById('slHoaChatThang'), {
            data: { labels: mLabels, datasets: [
                { type: 'line', label: 'Nước sạch (m³)', data: mNuoc, borderColor: C.blue, backgroundColor: 'transparent', borderWidth: 2.5, tension: .3, pointRadius: 3, pointBackgroundColor: C.blue, yAxisID: 'y1' },
                { type: 'bar',  label: 'PAC (kg)',       data: mPac,  backgroundColor: 'rgba(27,197,189,.6)',  borderColor: C.green,  borderWidth: 1, borderRadius: 3, yAxisID: 'y' },
                { type: 'bar',  label: 'Chlorine (kg)',  data: mClo,  backgroundColor: 'rgba(137,80,252,.6)', borderColor: C.purple, borderWidth: 1, borderRadius: 3, yAxisID: 'y' },
                { type: 'bar',  label: 'Polymer (kg)',   data: mPoly, backgroundColor: 'rgba(255,168,0,.5)',  borderColor: C.amber,  borderWidth: 1, borderRadius: 3, yAxisID: 'yPoly' },
            ]},
            options: { ...darkOpts, responsive: true, maintainAspectRatio: false,
                plugins: { legend: { labels: { color: '#5a82a8', usePointStyle: true, boxWidth: 8, font: { size: 11 } } } },
                scales: {
                    x: { ticks: { color: '#3d6080', font: { size: 10 } }, grid: { display: false } },
                    y: { 
                        type: 'linear', display: true, position: 'left',
                        ticks: { color: '#3d6080', font: { size: 10 } }, grid: { color: 'rgba(54,153,255,.07)' },
                        title: { display: true, text: 'Hóa chất (kg)', color: '#3d6080', font: { size: 10 } }
                    },
                    y1: {
                        type: 'linear', display: true, position: 'right',
                        ticks: { color: '#3699ff', font: { size: 10 }, callback: function(v){ return fmt(v); } }, grid: { display: false },
                        title: { display: true, text: 'Nước sạch (m³)', color: '#3699ff', font: { size: 10 } }
                    },
                    yPoly: {
                        type: 'linear', display: true, position: 'right',
                        ticks: { color: '#c08000', font: { size: 10 }, callback: function(v){ return fmt(v); } }, grid: { display: false },
                        title: { display: true, text: 'Polymer (kg)', color: '#c08000', font: { size: 10 } }
                    }
                }
            }
        });
    }

    // ════════════════════════════════════════════════════════════
    // Tab: Theo Năm
    // ════════════════════════════════════════════════════════════
    function renderNam(d, dbData) {
        var dbNam = (dbData && dbData.nam_data) ? dbData.nam_data : [];
        if (!dbNam.length) { showEmpty(); return; }

        var from = document.getElementById('sl-date-from') ? document.getElementById('sl-date-from').value : '';
        var to = document.getElementById('sl-date-to') ? document.getElementById('sl-date-to').value : '';
        var rows;
        var yrLabel;
        if (from && to) {
            var fromY = from.substring(0, 4);
            var toY = to.substring(0, 4);
            rows = dbNam.filter(function(r){ return r.nam && r.nam >= fromY && r.nam <= toY; });
            yrLabel = 'từ ' + fromY + ' đến ' + toY;
        } else {
            rows = dbNam;
            yrLabel = 'toàn lịch sử';
        }
        rows = rows.slice().sort(function(a,b){ return a.nam < b.nam ? -1 : (a.nam > b.nam ? 1 : 0); });
        if (!rows.length) { showEmpty(); return; }

        var yLabels = rows.map(function(r){ return r.nam; });
        var yNuoc   = rows.map(function(r){ return parseFloat(r.nuoc_sach || 0); });
        var yDien   = rows.map(function(r){ return parseFloat(r.dien      || 0); });
        var yPac    = rows.map(function(r){ return parseFloat(r.pac       || 0); });
        var yClo    = rows.map(function(r){ return parseFloat(r.chlorine  || 0); });
        var yPoly   = rows.map(function(r){ return parseFloat(r.polymer   || 0); });

        var totNuoc = yNuoc.reduce(function(s,v){return s+(v||0);},0);
        var totDien = yDien.reduce(function(s,v){return s+(v||0);},0);
        var totPacNam = yPac.reduce(function(s,v){return s+(v||0);},0);
        var totCloNam = yClo.reduce(function(s,v){return s+(v||0);},0);
        var totPolyNam= yPoly.reduce(function(s,v){return s+(v||0);},0); // Đã thêm tổng Poly

        setKpiLabels(
            'Tổng nước sạch (' + yrLabel + ')',
            'Tổng điện năng (' + yrLabel + ')',
            'Tổng PAC (' + yrLabel + ')',
            'Tổng Chlorine (' + yrLabel + ')',
            'Tổng Polymer (' + yrLabel + ')'
        );
        updateKPI(
            fmtN(totNuoc)   + '<span class="sl-kpi-unit">m³</span>',
            fmtN(totDien)   + '<span class="sl-kpi-unit">KWh</span>',
            fmt(totPacNam)  + '<span class="sl-kpi-unit">kg</span>',
            fmt(totCloNam)  + '<span class="sl-kpi-unit">kg</span>',
            fmt(totPolyNam) + '<span class="sl-kpi-unit">kg</span>',
            '', '', '', ''
        );

        // Đổi layout thành 2 chart xếp chồng hệt như Ngày và Tháng
        document.getElementById('sl-content').innerHTML =
            '<div class="sl-chart-stack">' +
                '<div class="sl-card">' +
                    '<div class="sl-card-title"><span class="dot" style="--dot-color:#3699ff;"></span>' +
                        'Nước sạch & Điện năng theo năm — ' + (from && to ? 'từ ' + from.substring(0, 4) + ' đến ' + to.substring(0, 4) : 'Toàn bộ dữ liệu') +
                    '</div>' +
                    '<div class="sl-canvas-wrap" style="min-height:300px;"><canvas id="slCumNam"></canvas></div>' +
                '</div>' +
                '<div class="sl-card">' +
                    '<div class="sl-card-title"><span class="dot" style="--dot-color:#1bc5bd;"></span>' +
                        'Hóa chất & Nước sạch theo năm — ' + (from && to ? 'từ ' + from.substring(0, 4) + ' đến ' + to.substring(0, 4) : 'Toàn bộ dữ liệu') +
                    '</div>' +
                    '<div class="sl-canvas-wrap" style="min-height:300px;"><canvas id="slNamHoaChat"></canvas></div>' +
                '</div>' +
            '</div>';

        // Nước sạch + điện theo từng năm
        slCharts.cumNam = new Chart(document.getElementById('slCumNam'), {
            type: 'bar',
            data: { labels: yLabels, datasets: [
                { type: 'bar',  label: 'Điện năng (KWh)', data: yDien,
                  backgroundColor: C.amber2, borderColor: C.amber, borderWidth: 1, borderRadius: 4, yAxisID: 'y1' },
                { type: 'line', label: 'Nước sạch (m³)',  data: yNuoc,
                  borderColor: C.blue, backgroundColor: 'transparent', borderWidth: 2.5,
                  tension: .3, pointRadius: 5, pointBackgroundColor: C.blue, yAxisID: 'y' },
            ]},
            options: { ...darkOpts, responsive: true, maintainAspectRatio: false,
                plugins: { legend: { labels: { color: '#5a82a8', usePointStyle: true, boxWidth: 8, font: { size: 11 } } } },
                scales: {
                    x:  { ticks: { color: '#3d6080', font: { size: 10 } }, grid: { display: false } },
                    y:  { ticks: { color: '#3d6080', font: { size: 10 }, callback: function(v){ return fmt(v); } },
                          grid: { color: 'rgba(54,153,255,.07)' },
                          title: { display: true, text: 'm³', color: '#3d6080', font: { size: 10 } } },
                    y1: { position: 'right', ticks: { color: '#c08000', font: { size: 10 }, callback: function(v){ return fmt(v); } },
                          grid: { display: false },
                          title: { display: true, text: 'KWh', color: '#c08000', font: { size: 10 } } },
                }
            }
        });

        // Hóa chất năm
        slCharts.namHC = new Chart(document.getElementById('slNamHoaChat'), {
            data: { labels: yLabels, datasets: [
                { type: 'line', label: 'Nước (m³)',     data: yNuoc, borderColor: C.blue, backgroundColor: 'transparent', borderWidth: 2.5, tension: .3, pointRadius: 3, pointBackgroundColor: C.blue, yAxisID: 'y1' },
                { type: 'bar',  label: 'PAC (kg)',      data: yPac,  backgroundColor: 'rgba(27,197,189,.5)',  borderColor: C.green,  borderWidth: 1, borderRadius: 3, yAxisID: 'y' },
                { type: 'bar',  label: 'Chlorine (kg)', data: yClo,  backgroundColor: 'rgba(137,80,252,.5)',  borderColor: C.purple, borderWidth: 1, borderRadius: 3, yAxisID: 'y' },
                { type: 'bar',  label: 'Polymer (kg)',  data: yPoly, backgroundColor: 'rgba(255,168,0,.5)',   borderColor: C.amber,  borderWidth: 1, borderRadius: 3, yAxisID: 'yPoly' },
            ]},
            options: { ...darkOpts, responsive: true, maintainAspectRatio: false,
                plugins: { legend: { labels: { color: '#5a82a8', usePointStyle: true, boxWidth: 8, font: { size: 11 } } } },
                scales: { 
                    x: { ticks: { color: '#3d6080', font: { size: 10 } }, grid: { display: false } },
                    y: { 
                        type: 'linear', display: true, position: 'left',
                        ticks: { color: '#3d6080', font: { size: 10 } }, grid: { color: 'rgba(54,153,255,.07)' },
                        title: { display: true, text: 'Hóa chất (kg)', color: '#3d6080', font: { size: 10 } }
                    },
                    y1: {
                        type: 'linear', display: true, position: 'right',
                        ticks: { color: '#3699ff', font: { size: 10 }, callback: function(v){ return fmt(v); } }, grid: { display: false },
                        title: { display: true, text: 'Nước sạch (m³)', color: '#3699ff', font: { size: 10 } }
                    },
                    yPoly: {
                        type: 'linear', display: true, position: 'right',
                        ticks: { color: '#c08000', font: { size: 10 }, callback: function(v){ return fmt(v); } }, grid: { display: false },
                        title: { display: true, text: 'Polymer (kg)', color: '#c08000', font: { size: 10 } }
                    }
                }
            }
        });
    }

    // ════════════════════════════════════════════════════════════
    // Tab: Realtime — 5 blocks
    // Site 60100 = TB Nước Thô | 60000 = Nhà Máy | NT5 = TB TA NT5
    // 24 trạm mạng lưới + chart top 15
    // ════════════════════════════════════════════════════════════
    // ── localStorage helpers — dùng chung cho gear toggle ──────
    var LS_KEY = 'rt_card_cfg';
    function loadCfg() {
        try { return JSON.parse(localStorage.getItem(LS_KEY)) || {}; } catch(e) { return {}; }
    }
    function saveCfg(cfg) {
        try { localStorage.setItem(LS_KEY, JSON.stringify(cfg)); } catch(e) {}
    }

    var LS_TBL = 'rt_tbl_cfg';
    function loadTblCfg() {
        try { return JSON.parse(localStorage.getItem(LS_TBL)) || {}; } catch(e) { return {}; }
    }
    function saveTblCfg(c) {
        try { localStorage.setItem(LS_TBL, JSON.stringify(c)); } catch(e) {}
    }

    // ── Custom channels — DB-backed, localStorage cache ─────────────
    // DB la nguon chinh; localStorage chi la cache tranh fetch lai khi chua doi
    var LS_CUSTOM = 'rt_custom_cfg';
    var RT_CUSTOM_API = '<?= \yii\helpers\Url::to(['/quanly/dashboard/api-rt-custom']) ?>';

    function loadCustom() {
        try {
            var v = JSON.parse(localStorage.getItem(LS_CUSTOM));
            return (v && typeof v === 'object') ? v : {tbn:[], nm:[], nt5:[]};
        } catch(e) { return {tbn:[], nm:[], nt5:[]}; }
    }
    function saveCustomCache(c) {
        try { localStorage.setItem(LS_CUSTOM, JSON.stringify(c)); } catch(e) {}
    }

    // Fetch config tu DB, cap nhat cache, re-render
    function syncCustomFromDB(callback) {
        fetch(RT_CUSTOM_API)
            .then(function(r){ return r.json(); })
            .then(function(data) {
                saveCustomCache(data);
                if (typeof callback === 'function') callback(data);
            })
            .catch(function(e) {
                console.warn('rt_custom sync fail:', e);
                if (typeof callback === 'function') callback(loadCustom());
            });
    }

    function renderRealtime(slData) {
        var trams = (slData && slData.trams) ? slData.trams : [];

        // Gom tất cả custom channel IDs từ 3 card
        var custom = loadCustom();
        var allCustomIds = [];
        ['tbn','nm','nt5'].forEach(function(cid) {
            (custom[cid] || []).forEach(function(item) {
                if (item.id && allCustomIds.indexOf(item.id) === -1) {
                    allCustomIds.push(item.id);
                }
            });
        });

        // Fetch stations + custom channels song song
        var fetchStations = fetch('/iot_api.php?action=stations&key=' + IOT_KEY)
            .then(function(r){ return r.json(); })
            .catch(function(){ return {}; });

        var fetchChan = allCustomIds.length > 0
            ? fetch('/iot_api.php?action=channel_value&channels=' + allCustomIds.join(',') + '&key=' + IOT_KEY)
                .then(function(r){ return r.json(); })
                .catch(function(){ return {}; })
            : Promise.resolve({});

        Promise.all([fetchStations, fetchChan]).then(function(results) {
            renderRealtimeLayout(trams, results[0], results[1] || {});
        });

        // Mỗi card có 1 key trong cfg: { tbn: {ph:1,do_duc:1,luu_luong:1}, nm: {...}, nt5: {...} }
        var CARD_FIELDS = {
            tbn: [
                { key:'ph',        label:'pH' },
                { key:'do_duc',    label:'Độ Đục' },
                { key:'luu_luong', label:'Lưu Lượng' }
            ],
            nm: [
                { key:'do_duc',    label:'Độ Đục' },
                { key:'luu_luong', label:'Lưu Lượng' },
                { key:'ph',        label:'pH' },
                { key:'clo',       label:'Clo' },
                { key:'muc_be',    label:'Mức Bể Chứa' }
            ],
            nt5: [
                { key:'ap_sp',     label:'Áp Suất Cài Đặt' },
                { key:'ap_pv',     label:'Áp Suất Thực Tế' },
                { key:'ll_thuan',  label:'Lưu Lượng Thuận' },
                { key:'ll_nghich', label:'Lưu Lượng Nghịch' },
                { key:'tan_so_1',  label:'Tần Số Bơm 1' },
                { key:'tan_so_2',  label:'Tần Số Bơm 2' },
                { key:'cong_suat', label:'Công Suất' }
            ]
        };

        var cfg = loadCfg();
        // Mặc định: tất cả bật (1) nếu chưa có trong localStorage
        ['tbn','nm','nt5'].forEach(function(cid) {
            if (!cfg[cid]) {
                cfg[cid] = {};
                CARD_FIELDS[cid].forEach(function(f){ cfg[cid][f.key] = 1; });
            }
        });

        // Build gear dropdown HTML cho 1 card
        function buildGear(cardId) {
            var items = CARD_FIELDS[cardId].map(function(f) {
                var checked = cfg[cardId][f.key] !== 0 ? 'checked' : '';
                return '<label class="rt-gear-item">' +
                    '<input type="checkbox" ' + checked + ' data-card="' + cardId + '" data-field="' + f.key + '"> ' +
                    f.label +
                '</label>';
            }).join('');
            // Custom rows trong dropdown (nút xóa)
            var customList = (loadCustom()[cardId] || []).map(function(item, idx) {
                return '<div class="rt-gear-item" style="justify-content:space-between;">' +
                    '<span style="color:#3699ff;">&#x25C6; ' + item.label + '</span>' +
                    '<button class="rt-custom-del" data-card="' + cardId + '" data-idx="' + idx + '" data-chanid="' + item.id + '" title="Xóa">' +
                        '&#10005;' +
                    '</button>' +
                '</div>';
            }).join('');
            // Form thêm thông số
            var addForm =
                '<div class="rt-gear-add-form">' +
                    '<input type="text" placeholder="Channel ID (vd: Wincc01_Level)" class="rt-add-chid" data-card="' + cardId + '">' +
                    '<input type="text" placeholder="Tên hiển thị (vd: Mực Nước Hồ)" class="rt-add-label" data-card="' + cardId + '">' +
                    '<button class="rt-gear-add-btn" onclick="rtAddCustom(this)" data-card="' + cardId + '">&#43; Thêm thông số</button>' +
                '</div>';
            return '<div class="rt-gear-wrap">' +
                '<button class="rt-gear-btn" onclick="rtToggleGear(this)" title="Tùy chỉnh hiển thị">' +
                    '<i class="fa-solid fa-gear"></i>' +
                '</button>' +
                '<div class="rt-gear-dropdown" style="min-width:220px;">' + items + customList + addForm + '</div>' +
            '</div>';
        }

        // Build stationRow có thêm data-card / data-field để ẩn/hiện
        function stationRowCfg(cardId, fieldKey, label, val, unit, color, ts) {
            var isHidden = cfg[cardId][fieldKey] === 0 ? ' rt-hidden' : '';
            var tsStr = ts ? ts.substring(5,16) : '';
            return '<div class="rt-field-row' + isHidden + '" data-card="' + cardId + '" data-field="' + fieldKey + '">' +
                '<span class="rt-field-label">' + label + '</span>' +
                '<div style="text-align:right;">' +
                    '<span class="rt-field-ts">' + tsStr + '</span><br>' +
                    '<span class="rt-field-val" style="color:' + color + ';">' + val + ' <small style="font-weight:400;color:#94a3b8;">' + unit + '</small></span>' +
                '</div>' +
            '</div>';
        }

        function renderRealtimeLayout(trams, stData, customData) {
            customData = customData || {};
            var nm  = stData.nha_may  || {};
            var tbn = stData.tram_bo_nuoc_tho || {};
            var nt5 = stData.nt5      || {};

            // ── Block TB Nước Thô (site 60100) ──
            var blockTBNT =
                '<div class="rt-station-card">' +
                    '<div class="rt-card-header">' +
                        '<div class="rt-station-card-title" style="color:#60a5fa;margin:0;border:none;padding:0;">' +
                            '<span class="dot" style="background:#3699ff;"></span>TB Nước Thô' +
                        '</div>' +
                        buildGear('tbn') +
                    '</div>' +
                    stationRowCfg('tbn','ph',        'pH',         fmtVal(tbn.ph),        'ph',   '#1bc5bd', tbn.ts) +
                    stationRowCfg('tbn','do_duc',    'Độ Đục',     fmtVal(tbn.do_duc),    'ntu',  '#ffa800', tbn.ts) +
                    stationRowCfg('tbn','luu_luong', 'Lưu Lượng',  fmtVal(tbn.luu_luong), 'm³/h', '#3699ff', tbn.ts) +
                    buildCustomRows('tbn', customData) +
                '</div>';

            // ── Block Nhà Máy (site 60000) ──
            var blockNM =
                '<div class="rt-station-card">' +
                    '<div class="rt-card-header">' +
                        '<div class="rt-station-card-title" style="color:#1bc5bd;margin:0;border:none;padding:0;">' +
                            '<span class="dot" style="background:#1bc5bd;"></span>Nhà Máy' +
                        '</div>' +
                        buildGear('nm') +
                    '</div>' +
                    stationRowCfg('nm','do_duc',    'Độ Đục',       fmtVal(nm.do_duc),    'ntu',  '#5a82a8', nm.ts) +
                    stationRowCfg('nm','luu_luong', 'Lưu Lượng',    fmtVal(nm.luu_luong), 'm³/h', '#3699ff', nm.ts) +
                    stationRowCfg('nm','ph',        'pH',            fmtVal(nm.ph),        'ph',   '#1bc5bd', nm.ts) +
                    stationRowCfg('nm','clo',       'Clo',           fmtVal(nm.clo),       'ppm',  '#8950fc', nm.ts) +
                    stationRowCfg('nm','muc_be',    'Mức Bể Chứa',   fmtVal(nm.muc_be),    'm',    '#ffa800', nm.ts) +
                    buildCustomRows('nm', customData) +
                '</div>';

            // ── Block TB TA NT5 ──
            var blockNT5 =
                '<div class="rt-station-card">' +
                    '<div class="rt-card-header">' +
                        '<div class="rt-station-card-title" style="color:#ffa800;margin:0;border:none;padding:0;">' +
                            '<span class="dot" style="background:#ffa800;"></span>TB TA NT5' +
                        '</div>' +
                        buildGear('nt5') +
                    '</div>' +
                    stationRowCfg('nt5','ap_sp',     'Áp Suất Cài Đặt', fmtVal(nt5.ap_sp),     'm',    '#64748b', nt5.ts) +
                    stationRowCfg('nt5','ap_pv',     'Áp Suất Thực Tế', fmtVal(nt5.ap_pv),     'm',    '#3699ff', nt5.ts) +
                    stationRowCfg('nt5','ll_thuan',  'Lưu Lượng Thuận', fmtVal(nt5.ll_thuan),  'm³/h', '#1bc5bd', nt5.ts) +
                    stationRowCfg('nt5','ll_nghich', 'Lưu Lượng Nghịch',fmtVal(nt5.ll_nghich), 'm³/h', '#f64e60', nt5.ts) +
                    stationRowCfg('nt5','tan_so_1',  'Tần Số Bơm 1',    fmtVal(nt5.tan_so_1),  'hz',   '#3699ff', nt5.ts) +
                    stationRowCfg('nt5','tan_so_2',  'Tần Số Bơm 2',    fmtVal(nt5.tan_so_2),  'hz',   '#00d4ff', nt5.ts) +
                    stationRowCfg('nt5','cong_suat', 'Công Suất',        fmtVal(nt5.cong_suat), 'KW',   '#ffa800', nt5.ts) +
                    buildCustomRows('nt5', customData) +
                '</div>';

            // ── Bảng 24 trạm — ap_luc (sort only), ll_thuan, ll_nghich, ap_truoc, ap_sau ──
            // Cột Áp Lực đã bỏ khỏi bảng (chỉ dùng để sort + chart)
            // User toggle LL Thuận / LL Nghịch / AP Trước / AP Sau qua gear
            var TBL_COLS = [
                { key:'ll_thuan',  label:'LL Thuận'  },
                { key:'ll_nghich', label:'LL Nghịch' },
                { key:'ap_truoc',  label:'AP Trước'  },
                { key:'ap_sau',    label:'AP Sau'    },
            ];
            var tblCfg = loadTblCfg();
            TBL_COLS.forEach(function(col) {
                if (tblCfg[col.key] === undefined) tblCfg[col.key] = 1; // mặc định bật
            });

            var sorted = trams.slice().sort(function(a,b){ return b.ap_luc - a.ap_luc; });
            var tableRows = sorted.map(function(t) {
                var llT   = parseFloat(t.luu_luong  || 0);
                var llN   = parseFloat(t.ll_nghich  || 0);
                var apTr  = (t.ap_truoc !== undefined && t.ap_truoc !== null) ? parseFloat(t.ap_truoc)  : null;
                var apSau = (t.ap_sau   !== undefined && t.ap_sau   !== null) ? parseFloat(t.ap_sau)    : null;

                var llTStr  = llT > 0
                    ? '<span class="ll-val">' + llT.toFixed(1) + ' m³/h</span>'
                    : '<span style="color:#94a3b8;">—</span>';
                var llNStr  = llN > 0
                    ? '<span class="ll-neg">▼ ' + llN.toFixed(1) + ' m³/h</span>'
                    : '<span style="color:#94a3b8;">—</span>';
                var apTrStr = apTr !== null
                    ? '<span style="color:#60a5fa;font-weight:600;">' + apTr.toFixed(2) + ' m</span>'
                    : '<span style="color:#94a3b8;">—</span>';
                var apSauStr = apSau !== null
                    ? '<span style="color:#1bc5bd;font-weight:600;">' + apSau.toFixed(2) + ' m</span>'
                    : '<span style="color:#94a3b8;">—</span>';

                var ts = t.timestamp || '';
                return '<tr>' +
                    '<td style="font-weight:600;color:#334155;">' + (t.ten||t.site_id) + '</td>' +
                    '<td class="col-extra' + (tblCfg['ll_thuan']  === 0 ? ' col-hidden' : '') + '" data-col="ll_thuan">'  + llTStr   + '</td>' +
                    '<td class="col-extra' + (tblCfg['ll_nghich'] === 0 ? ' col-hidden' : '') + '" data-col="ll_nghich">' + llNStr   + '</td>' +
                    '<td class="col-extra' + (tblCfg['ap_truoc']  === 0 ? ' col-hidden' : '') + '" data-col="ap_truoc">'  + apTrStr  + '</td>' +
                    '<td class="col-extra' + (tblCfg['ap_sau']    === 0 ? ' col-hidden' : '') + '" data-col="ap_sau">'    + apSauStr + '</td>' +
                    '<td style="font-size:.7rem;color:#94a3b8;">' + ts.substring(5,16) + '</td>' +
                '</tr>';
            }).join('');

            // Build gear dropdown cho bảng trạm
            var tblGearItems = TBL_COLS.map(function(col) {
                var chk = tblCfg[col.key] !== 0 ? 'checked' : '';
                return '<label class="rt-gear-item">' +
                    '<input type="checkbox" ' + chk + ' data-tbl-col="' + col.key + '"> ' +
                    col.label +
                '</label>';
            }).join('');

            var blockTable =
                '<div class="sl-card" style="overflow:hidden;">' +
                    '<div class="rt-card-header" style="margin-bottom:10px;">' +
                        '<div class="sl-card-title" style="margin:0;border:none;padding:0;">' +
                            '<span class="dot" style="--dot-color:#3699ff;"></span>' +
                            'Trạng Thái 24 Trạm SCADA (Realtime)' +
                        '</div>' +
                        '<div class="rt-gear-wrap">' +
                            '<button class="rt-gear-btn" onclick="rtToggleGear(this)" title="Tùy chỉnh cột">' +
                                '<i class="fa-solid fa-gear"></i>' +
                            '</button>' +
                            '<div class="rt-gear-dropdown">' + tblGearItems + '</div>' +
                        '</div>' +
                    '</div>' +
                    '<div style="overflow-x:auto;overflow-y:auto;max-height:380px;">' +
                        '<table class="rt-table" id="rt-tram-table" style="min-width:480px;">' +
                            '<thead><tr>' +
                                '<th style="min-width:150px;">Trạm</th>' +
                                '<th class="col-extra' + (tblCfg['ll_thuan']  === 0 ? ' col-hidden' : '') + '" data-col="ll_thuan">LL Thuận</th>' +
                                '<th class="col-extra' + (tblCfg['ll_nghich'] === 0 ? ' col-hidden' : '') + '" data-col="ll_nghich">LL Nghịch</th>' +
                                '<th class="col-extra' + (tblCfg['ap_truoc']  === 0 ? ' col-hidden' : '') + '" data-col="ap_truoc">AP Trước</th>' +
                                '<th class="col-extra' + (tblCfg['ap_sau']    === 0 ? ' col-hidden' : '') + '" data-col="ap_sau">AP Sau</th>' +
                                '<th>Cập Nhật</th>' +
                            '</tr></thead>' +
                            '<tbody>' + tableRows + '</tbody>' +
                        '</table>' +
                    '</div>' +
                '</div>';

            // ── Chart phân bổ áp lực top 15 ──
            var top15 = sorted.filter(function(t){ return t.ap_luc > 0; }).slice(0,15);
            var blockChart =
                '<div class="sl-card">' +
                    '<div class="sl-card-title"><span class="dot" style="--dot-color:#3699ff;"></span>Phân Bổ Áp Lực (Top 15 Trạm)</div>' +
                    '<div class="sl-canvas-wrap" style="min-height:320px;"><canvas id="slRTBar"></canvas></div>' +
                '</div>';

            document.getElementById('sl-content').innerHTML =
                '<div class="rt-grid-top" style="margin-bottom:14px;">' +
                    blockTBNT + blockNM + blockNT5 +
                '</div>' +
                '<div class="rt-grid-bottom">' +
                    blockTable + blockChart +
                '</div>';

            // Render bar chart
            if (top15.length) {
                slCharts.rtBar = new Chart(document.getElementById('slRTBar'), {
                    type: 'bar',
                    data: { labels: top15.map(function(t){ return t.ten || t.site_id; }), datasets: [{
                        label: 'Áp lực (m)',
                        data: top15.map(function(t){ return t.ap_luc; }),
                        backgroundColor: top15.map(function(t){
                            if(t.ap_luc>=40) return 'rgba(54,153,255,.6)';
                            if(t.ap_luc>=25) return 'rgba(27,197,189,.6)';
                            if(t.ap_luc>=10) return 'rgba(255,168,0,.6)';
                            return 'rgba(246,78,96,.6)';
                        }),
                        borderColor: top15.map(function(t){
                            if(t.ap_luc>=40) return '#3699ff';
                            if(t.ap_luc>=25) return '#1bc5bd';
                            if(t.ap_luc>=10) return '#ffa800';
                            return '#f64e60';
                        }),
                        borderWidth: 1, borderRadius: 4,
                    }]},
                    options: { indexAxis: 'y', ...darkOpts, responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: false },
                                   tooltip: { callbacks: { label: function(c){ return ' ' + c.parsed.x.toFixed(2) + ' m'; } } } },
                        scales: {
                            x: { ticks: { color: '#3d6080', font: { size: 10 } }, grid: { color: 'rgba(54,153,255,.07)' } },
                            y: { ticks: { color: '#334155', font: { size: 9 } }, grid: { display: false } }
                        }
                    }
                });
            }
        }
    }

    function fmtVal(v) {
        if (v === null || v === undefined || v === '') return '—';
        return parseFloat(v).toFixed(2);
    }

    // Build custom channel rows cho 1 card
    function buildCustomRows(cardId, customData) {
        var items = loadCustom()[cardId] || [];
        if (!items.length) return '';
        return items.map(function(item) {
            var ch = customData[item.id] || {};
            var val  = (ch.value !== undefined && ch.value !== null) ? parseFloat(ch.value).toFixed(2) : '—';
            var unit = item.unit || ch.unit || '';
            var ts   = ch.ts   || '';
            var tsStr = ts ? ts.substring(5,16) : '';
            return '<div class="rt-field-row" data-custom-card="' + cardId + '" data-custom-id="' + item.id + '">' +
                '<span class="rt-field-label" style="color:#3699ff;">' + item.label + '</span>' +
                '<div style="text-align:right;">' +
                    '<span class="rt-field-ts">' + tsStr + '</span><br>' +
                    '<span class="rt-field-val" style="color:#60a5fa;">' + val + ' <small style="font-weight:400;color:#94a3b8;">' + unit + '</small></span>' +
                '</div>' +
            '</div>';
        }).join('');
    }

    // Thêm custom channel từ form trong gear dropdown
    window.rtAddCustom = function(btn) {
        var cardId = btn.dataset.card;
        var wrap   = btn.closest('.rt-gear-add-form');
        var chid   = wrap.querySelector('.rt-add-chid').value.trim();
        var label  = wrap.querySelector('.rt-add-label').value.trim();
        if (!chid || !label) { alert('Vui lòng nhập Channel ID và Tên hiển thị'); return; }
        btn.disabled = true;
        btn.textContent = 'Đang lưu...';
        fetch(RT_CUSTOM_API, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action:'add', card_id:cardId, channel_id:chid, label:label})
        })
        .then(function(r){ return r.json(); })
        .then(function(res) {
            btn.disabled = false;
            btn.textContent = '\u002B Thêm thông số';
            if (res.success) {
                wrap.querySelector('.rt-add-chid').value = '';
                wrap.querySelector('.rt-add-label').value = '';
                syncCustomFromDB(function() { loadSLTab('realtime'); });
            } else {
                alert(res.msg || 'Lỗi khi lưu');
            }
        })
        .catch(function() {
            btn.disabled = false;
            btn.textContent = '\u002B Thêm thông số';
            alert('Không kết nối được server');
        });
    };

    // Xóa custom channel
    document.addEventListener('click', function(e) {
        if (!e.target.classList.contains('rt-custom-del')) return;
        var cardId = e.target.dataset.card;
        var chanId = e.target.dataset.chanid;
        if (!chanId) {
            // fallback: lay tu idx neu khong co chanid
            var idx = parseInt(e.target.dataset.idx);
            var c   = loadCustom();
            if (c[cardId] && c[cardId][idx]) chanId = c[cardId][idx].id;
        }
        if (!chanId) return;
        fetch(RT_CUSTOM_API, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action:'delete', card_id:cardId, channel_id:chanId})
        })
        .then(function(r){ return r.json(); })
        .then(function() {
            syncCustomFromDB(function() { loadSLTab('realtime'); });
        })
        .catch(function() { alert('Không kết nối được server'); });
    });

    // ════════════════════════════════════════════════════════════
    // Load & switch tab
    // ════════════════════════════════════════════════════════════
    function loadSLTab(loai) {
        var content = document.getElementById('sl-content');
        content.innerHTML = '<div class="sl-loading"><div class="sl-spinner"></div> Đang tải...</div>';
        destroyCharts();

        if (loai === 'realtime') {
            fetch(IOT_BASE + '?action=sanluong&loai=realtime&key=' + IOT_KEY)
                .then(function(r){ return r.json(); })
                .then(function(slData){ renderRealtime(slData); })
                .catch(function() {
                    content.innerHTML = '<div class="sl-loading" style="color:#f64e60;"><i class="fa-solid fa-circle-exclamation me-2"></i>Không kết nối được SCADA server</div>';
                });
            return;
        }

        var apiLoai = (loai === 'ngay') ? 'ngay' : (loai === 'thang' ? 'thang' : 'nam');

        Promise.all([
            fetch(IOT_BASE + '?action=sanluong&loai=' + apiLoai + '&key=' + IOT_KEY).then(function(r){ return r.json(); }),
            new Promise(function(resolve) { fetchDbData(resolve); })
        ]).then(function(results) {
            var d      = results[0];
            var dbData = results[1];
            if      (loai === 'ngay')  renderNgay(d, dbData);
            else if (loai === 'thang') renderThang(d, dbData);
            else if (loai === 'nam')   renderNam(d, dbData);
        }).catch(function() {
            content.innerHTML = '<div class="sl-loading" style="color:#f64e60;"><i class="fa-solid fa-circle-exclamation me-2"></i>Không kết nối được SCADA server</div>';
        });
    }

    window.switchSLTab = function(loai, btn) {
        curTab = loai;
        document.querySelectorAll('.sl-tab').forEach(function(b){ b.classList.remove('active'); });
        if (btn) btn.classList.add('active');
        
        ['kpi-nuoc','kpi-dien','kpi-pac','kpi-clo','kpi-poly'].forEach(function(id){
            if(document.getElementById(id)) document.getElementById(id).innerHTML = '—';
        });

        // Ẩn/Hiện dòng KPI 5 ô dựa vào loại Tab và bộ lọc ngày tháng
        const kpiRow = document.getElementById('sl-kpi-row');
        const dateFilter = document.getElementById('sl-date-filter');
        if (loai === 'realtime') {
            kpiRow.style.display = 'none'; // Ẩn khi là realtime
            if (dateFilter) dateFilter.style.display = 'none';
        } else {
            kpiRow.style.display = ''; // Khôi phục hiển thị cho các Tab khác
            if (dateFilter) dateFilter.style.display = 'flex';
        }

        // Xóa trống bộ lọc ngày tháng khi chuyển Tab để khôi phục mặc định của tab đó
        if (dateFilter) {
            var fromInput = document.getElementById('sl-date-from');
            var toInput = document.getElementById('sl-date-to');
            if (fromInput) fromInput.value = '';
            if (toInput) toInput.value = '';
            dbCache = null; // Xóa cache dữ liệu DB để tải lại dữ liệu mặc định không lọc
        }

        loadSLTab(loai);
    };

    window.applySLFilter = function() {
        var from = document.getElementById('sl-date-from').value;
        var to = document.getElementById('sl-date-to').value;
        if (!from || !to) { alert('Vui lòng chọn đầy đủ từ ngày và đến ngày'); return; }
        if (from > to) { alert('Từ ngày phải nhỏ hơn hoặc bằng đến ngày'); return; }
        
        dbCache = null; // xóa cache để fetch lại dữ liệu từ DB theo khoảng ngày đã chọn
        loadSLTab(curTab);
    };

    // ── Gear toggle handler ──────────────────────────────────────
    window.rtToggleGear = function(btn) {
        var dd = btn.nextElementSibling;
        dd.classList.toggle('open');
        // Đóng dropdown khác
        document.querySelectorAll('.rt-gear-dropdown.open').forEach(function(el) {
            if (el !== dd) el.classList.remove('open');
        });
    };

    // Đóng dropdown khi click ra ngoài
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.rt-gear-wrap')) {
            document.querySelectorAll('.rt-gear-dropdown.open').forEach(function(el) {
                el.classList.remove('open');
            });
        }
    });

    // Lắng nghe checkbox change — dùng event delegation vì DOM được render động
    document.getElementById('sl-content').addEventListener('change', function(e) {
        var cb = e.target;
        if (cb.type !== 'checkbox') return;

        // Checkbox card TB (3 bảng TB)
        if (cb.dataset.card) {
            var cardId   = cb.dataset.card;
            var fieldKey = cb.dataset.field;
            var cfg2 = loadCfg();
            if (!cfg2[cardId]) cfg2[cardId] = {};
            cfg2[cardId][fieldKey] = cb.checked ? 1 : 0;
            saveCfg(cfg2);
            document.querySelectorAll('.rt-field-row[data-card="' + cardId + '"][data-field="' + fieldKey + '"]').forEach(function(row) {
                if (cb.checked) row.classList.remove('rt-hidden');
                else            row.classList.add('rt-hidden');
            });
        }

        // Checkbox bảng 24 trạm
        if (cb.dataset.tblCol) {
            var colKey = cb.dataset.tblCol;
            var tblC = loadTblCfg();
            tblC[colKey] = cb.checked ? 1 : 0;
            saveTblCfg(tblC);
            // Ẩn/hiện cả th lẫn td theo data-col
            document.querySelectorAll('#rt-tram-table [data-col="' + colKey + '"]').forEach(function(el) {
                if (cb.checked) el.classList.remove('col-hidden');
                else            el.classList.add('col-hidden');
            });
        }
    });

    // Sync custom config tu DB truoc khi render lan dau
    syncCustomFromDB(function() {
        var btnNgay = document.querySelector('.sl-tabs .sl-tab');
        if (btnNgay) {
            switchSLTab('ngay', btnNgay);
        } else {
            switchSLTab('ngay', null);
        }
    });

    setInterval(function() {
        if (!document.hidden) {
            if (curTab === 'ngay' || curTab === 'realtime') loadSLTab(curTab);
        }
    }, 300000);

    // ════════════════════════════════════════════════════════════
    // BẢNG THẤT THOÁT NƯỚC
    // ════════════════════════════════════════════════════════════
    window.ttCurrentDays = 7;
    window.ttMode = 'days';       // 'days' hoặc 'range' — dùng để interval refresh đúng chế độ đang xem
    window.ttRangeFrom = null;
    window.ttRangeTo = null;

    window.loadTTTable = function(days, btn) {
        window.ttCurrentDays = days;
        window.ttMode = 'days';
        document.querySelectorAll('.tt-days-btn').forEach(function(b){ b.classList.remove('active'); });
        if (btn && btn.classList.contains('tt-days-btn')) btn.classList.add('active');
        document.getElementById('tt-content').innerHTML = '<div class="tt-loading"><div class="sl-spinner"></div> Đang tải...</div>';

        fetch(IOT_BASE + '?action=sanluong&loai=thatthoat&key=' + IOT_KEY)
            .then(function(r){ return r.json(); })
            .then(function(data) {
                if (!data.days || !data.days.length) {
                    document.getElementById('tt-content').innerHTML = '<div class="tt-loading" style="color:#3d5a78;">Chưa có dữ liệu</div>';
                    return;
                }
                var rows = data.days.slice(-(days+1)); // N ngày trước + hôm nay = N+1 cột — khớp cách tính của san-luong-dong-ho (tu_ngay = today-N, den_ngay = today)
                renderTTRows(rows, days);
                // Auto scroll về cột phải nhất (ngày mới nhất) — cho mobile
                setTimeout(function() {
                    var wrap = document.querySelector('#tt-content .tt-scroll-wrap');
                    if (!wrap) wrap = document.querySelector('#tt-content table');
                    if (wrap && wrap.parentElement) wrap.parentElement.scrollLeft = wrap.parentElement.scrollWidth;
                }, 50);
            })
            .catch(function() {
                document.getElementById('tt-content').innerHTML = '<div class="tt-loading" style="color:#f64e60;"><i class="fa-solid fa-circle-exclamation me-2"></i>Không kết nối được SCADA server</div>';
            });
    };

    window.loadTTTableRange = function() {
        var from = document.getElementById('tt-date-from').value;
        var to   = document.getElementById('tt-date-to').value;
        if (!from || !to) { alert('Vui lòng chọn đủ từ ngày và đến ngày'); return; }
        if (from > to)    { alert('Từ ngày phải nhỏ hơn đến ngày'); return; }
        window.ttMode      = 'range';
        window.ttRangeFrom = from;
        window.ttRangeTo   = to;
        document.querySelectorAll('.tt-days-btn').forEach(function(b){ b.classList.remove('active'); });
        document.getElementById('tt-content').innerHTML = '<div class="tt-loading"><div class="sl-spinner"></div> Đang tải...</div>';

        var daysNeeded = Math.ceil((new Date() - new Date(from)) / 86400000) + 2;
daysNeeded = Math.max(35, Math.min(daysNeeded, 90));
fetch(IOT_BASE + '?action=sanluong&loai=thatthoat&so_ngay=' + daysNeeded + '&key=' + IOT_KEY)
            .then(function(r){ return r.json(); })
            .then(function(data) {
                if (!data.days || !data.days.length) {
                    document.getElementById('tt-content').innerHTML = '<div class="tt-loading" style="color:#3d5a78;">Chưa có dữ liệu</div>';
                    return;
                }
                var filtered = data.days.filter(function(d) {
                    if (!d.ngay) return false;
                    var iso = d.ngay;
                    if (d.ngay.indexOf('/') !== -1) {
                        var p = d.ngay.split('/');
                        iso = p[2] + '-' + p[1] + '-' + p[0];
                    }
                    return iso >= from && iso <= to;
                });
                if (!filtered.length) {
                    document.getElementById('tt-content').innerHTML = '<div class="tt-loading" style="color:#3d5a78;">Không có dữ liệu trong khoảng này</div>';
                    return;
                }
                renderTTRows(filtered, filtered.length);
            })
            .catch(function() {
                document.getElementById('tt-content').innerHTML = '<div class="tt-loading" style="color:#f64e60;"><i class="fa-solid fa-circle-exclamation me-2"></i>Không kết nối được SCADA server</div>';
            });
    };

    function renderTTRows(rows, days) {
        var today    = new Date();
        var todayStr = today.toLocaleDateString('vi-VN', {day:'2-digit',month:'2-digit',year:'numeric'}).replace(/\//g,'/');
        var todayIso = today.toISOString().slice(0, 10); // YYYY-MM-DD — loai khoi tinh TB

        // rowsForAvg: bo ngay hom nay vi chua chot, tinh vao se sai TB
        var rowsForAvg   = rows.filter(function(d){ return d.ngay !== todayIso; });
        var validRows    = rowsForAvg.filter(function(d){ return d.ti_le !== null && d.ti_le !== undefined && d.nuoc_cap > 0 && d.nuoc_kh > 0; });
        var sumRaw       = rowsForAvg.reduce(function(s,d){ return s+(d.nuoc_tho||0); }, 0);
        var sumCap       = rowsForAvg.reduce(function(s,d){ return s+(d.nuoc_cap||0); }, 0);
        var sumKH        = validRows.reduce(function(s,d){ return s+(d.nuoc_kh||0); }, 0);
        var sumNRW       = validRows.reduce(function(s,d){ return s+(d.that_thoat||0); }, 0);
        var sumCapValid  = validRows.reduce(function(s,d){ return s+(d.nuoc_cap||0); }, 0);
        var avgTL        = sumCapValid > 0 ? sumNRW / sumCapValid * 100 : 0;

        var sumHeadCols =
            '<th style="background:rgba(54,153,255,.12);color:#7ab8ff;white-space:nowrap;text-align:center;">Tổng<br><small style="font-size:.65rem;opacity:.7;">' + days + ' ngày</small></th>' +
            '<th style="background:rgba(54,153,255,.08);color:#5a9fd4;white-space:nowrap;text-align:center;">TB / ngày</th>';

        var headCols = rows.map(function(d, i) {
            var isToday = d.ngay === todayStr || i === rows.length - 1;
            return '<th class="' + (isToday?'tt-today-head':'') + '">' + d.ngay + '</th>';
        }).join('');

        var ROWS_DEF = [
            { key:'nuoc_tho',   label:'Sản lượng nước thô (m³)',    cls:'tt-row-raw', valCls:'val-raw', sum:sumRaw },
            { key:'nuoc_cap',   label:'Nước sạch cấp ra mạng (m³)', cls:'tt-row-cap', valCls:'val-cap', sum:sumCap },
            { key:'nuoc_kh',    label:'Sản lượng khách hàng (m³)',  cls:'tt-row-kh',  valCls:'val-kh',  sum:sumKH  },
            { key:'that_thoat', label:'Lượng nước thất thoát (m³)', cls:'tt-row-nrw', valCls:'val-nrw', sum:sumNRW },
            { key:'ti_le',      label:'Tỷ lệ thất thoát (%)',       cls:'tt-row-tl',  valCls:null,      sum:null   },
        ];

        var bodyRows = '';
        ROWS_DEF.forEach(function(row) {
            var cells = rows.map(function(d, i) {
                var isToday = i === rows.length - 1;
                var val = d[row.key];
                var hasNote = d.note && row.key === 'ti_le';
                var disp;
                if (row.key === 'ti_le') {
                    if (hasNote) {
                        disp = '<span style="color:#4d6d8a;font-size:.72rem;font-style:italic;">' + d.note + '</span>';
                    } else if (val === null || val === undefined) {
                        disp = '<span style="color:#3d5a78;font-size:.75rem;">—</span>';
                    } else {
                        var cls  = val<15?'val-tl-ok': val<=20?'val-tl-warn':'val-tl-bad';
                        var icon = val<15?'↓': val<=20?'→':'↑';
                        disp = '<span class="val-tl ' + cls + '">' + icon + ' ' + val.toFixed(2) + '%</span>';
                    }
                } else {
                    disp = (val > 0) ? val.toLocaleString('vi-VN') : '—';
                }
                var style = (d.note && row.key !== 'ti_le') ? 'opacity:.5;' : '';
                return '<td class="' + (isToday?'tt-today ':'')+  (row.valCls||'') + '" style="' + style + '">' + disp + '</td>';
            }).join('');

            var sumCell;
            if (row.key === 'ti_le') {
                if (validRows.length > 0) {
                    var tlCls  = avgTL<15?'val-tl-ok': avgTL<=20?'val-tl-warn':'val-tl-bad';
                    var note2  = validRows.length < rows.length ? '<br><small style="opacity:.55;font-size:.65rem">' + validRows.length + '/' + rows.length + ' ngày hợp lệ</small>' : '';
                    sumCell = '<td class="tt-sum-col" style="text-align:center;">—</td>' +
                              '<td class="tt-sum-col" style="text-align:center;background:rgba(54,153,255,.06)"><span class="val-tl ' + tlCls + '">' + avgTL.toFixed(2) + '%</span>' + note2 + '</td>';
                } else {
                    sumCell = '<td class="tt-sum-col" style="color:#3d5a78;text-align:center;">—</td><td class="tt-sum-col" style="color:#3d5a78;text-align:center;background:rgba(54,153,255,.06)">—</td>';
                }
            } else {
                var sumVal = (row.key==='nuoc_kh'||row.key==='that_thoat') ? (row.key==='nuoc_kh'?sumKH:sumNRW) : (row.sum||0);
                var cntForAvg = (row.key==='nuoc_kh'||row.key==='that_thoat')
                    ? validRows.length
                    : rowsForAvg.filter(function(d){ return (d[row.key]||0)>0; }).length;
                var tbVal = cntForAvg > 0 ? sumVal / cntForAvg : 0;
                var fmtInt = function(v){ return v>0 ? Math.round(v).toLocaleString('vi-VN') : '—'; };
                sumCell = '<td class="tt-sum-col ' + (row.valCls||'') + '">' + fmtInt(sumVal) + '</td>' +
                          '<td class="tt-sum-col ' + (row.valCls||'') + '" style="background:rgba(54,153,255,.06)">' + fmtInt(tbVal) + '</td>';
            }
            bodyRows += '<tr class="' + row.cls + '"><td>' + row.label + '</td>' + sumCell + cells + '</tr>';
        });

        document.getElementById('tt-content').innerHTML =
            '<div class="tt-wrap"><table class="tt-table">' +
                '<thead><tr>' +
                    '<th style="min-width:220px;text-align:left;">Chỉ tiêu</th>' +
                    sumHeadCols + headCols +
                '</tr></thead>' +
                '<tbody>' + bodyRows + '</tbody>' +
            '</table></div>';
    }

    loadTTTable(7, null);

    setInterval(function() {
        if (!document.hidden) {
            if (window.ttMode === 'range' && window.ttRangeFrom && window.ttRangeTo) {
                window.loadTTTableRange();
            } else if (window.ttCurrentDays) {
                window.loadTTTable(window.ttCurrentDays, null);
            }
        }
    }, 300000);

});
</script>