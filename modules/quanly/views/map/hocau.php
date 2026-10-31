<?php
use app\widgets\maps\LeafletMapAsset;
use app\widgets\maps\plugins\leaflet_measure\LeafletMeasureAsset;
use app\widgets\maps\LeafletDrawAsset;
use app\widgets\maps\plugins\leafletlocate\LeafletLocateAsset;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\Json;

LeafletMapAsset::register($this);
LeafletDrawAsset::register($this);
LeafletMeasureAsset::register($this);
LeafletLocateAsset::register($this);

$geoserverWmsUrl = 'http://cello.capnuochocaumoi.vn/geoserver/capnuoc_hocau/wms';
$geoserverWfsUrl = 'http://cello.capnuochocaumoi.vn/geoserver/capnuoc_hocau/wfs';
$geoserverWmtsUrl = 'http://cello.capnuochocaumoi.vn/geoserver/gwc/service/wmts';

$detailLinks = [
    'network_cocmoc' => Url::to(['/quanly/hocau/cocmoc/view']),
    'network_donghonhamay' => Url::to(['/quanly/hocau/donghonhamay/view']),
    'network_donghotong' => Url::to(['/quanly/hocau/dongho-tong/view']),
    'network_hamkythuat' => Url::to(['/quanly/hocau/hamkythuat/view']),
    'network_moinoi' => Url::to(['/quanly/hocau/moinoi/view']),
    'network_ongphanphoi' => Url::to(['/quanly/hocau/ongphanphoi/view']),
    'network_ongtruyendan' => Url::to(['/quanly/hocau/ongtruyendan/view']),
    'network_ongdansinh' => Url::to(['/quanly/hocau/ongdansinh/view']),
    'network_van' => Url::to(['/quanly/hocau/van/view']),
    'network_suco' => Url::to(['/quanly/hocau/suco/view']),
    'network_hanglangantoan' => Url::to(['/quanly/hocau/hanglangantoan/view']),
    'network_nhamaynuoc' => Url::to(['/quanly/hocau/nhamaynuoc/view']),
];

$this->title = 'Hệ thống GIS Cấp nước Hồ Cầu';
$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->registerCsrfMetaTags() ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        /* =====================================================================
       BIẾN & RESET
    ===================================================================== */
        :root {
            --sidebar-w: 380px;
            --panel-w: 360px;
            --chart-h: 300px;
            --primary: #1a56db;
            --dark: #0a1020;
            --dark2: #0e1830;
            --border: rgba(77, 157, 224, .22);
            --text: #dce8ff;
            --muted: #5a82a8;

            /* Màu áp lực */
            --c-high: #1a6dff;
            --c-med: #22c55e;
            --c-low: #eab308;
            --c-none: #64748b;
            --c-alert: #ef4444;
            --c-offline: #f97316;

            /* Màu nhóm chỉ số */
            --g-ap: #4d9de0;
            --g-flow: #22c55e;
            --g-dien: #f59e0b;
            --g-bom: #a78bfa;
            --g-cln: #06b6d4;
            --g-idx: #f472b6;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            overflow: hidden;
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #0d1117;
        }

        #app {
            display: flex;
            height: 100vh;
            width: 100vw;
            position: relative;
        }

        /* =====================================================================
       SIDEBAR
    ===================================================================== */
        #sidebar {
            width: var(--sidebar-w);
            background: #fff;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 24px rgba(0, 0, 0, .1);
            z-index: 1000;
            transition: transform .3s ease;
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
        }

        #sidebar.collapsed {
            transform: translateX(calc(var(--sidebar-w) * -1));
        }

        .sb-head {
            height: 56px;
            background: var(--primary);
            color: #fff;
            display: flex;
            align-items: center;
            padding: 0 16px;
            font-weight: 700;
            font-size: .95rem;
            gap: 8px;
            flex-shrink: 0;
        }

        .sb-tabs {
            display: flex;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            flex-shrink: 0;
        }

        .sb-tabs button {
            flex: 1;
            border: none;
            background: transparent;
            padding: 10px 4px;
            font-size: .78rem;
            font-weight: 600;
            color: #6c757d;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            transition: all .2s;
        }

        .sb-tabs button.active {
            color: var(--primary);
            background: #fff;
            border-bottom-color: var(--primary);
        }

        .sb-body {
            flex: 1;
            overflow-y: auto;
        }

        .tab-pane {
            display: none;
            padding: 14px;
        }

        .tab-pane.active {
            display: block;
        }

        /* Layer groups */
        .lg {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 8px;
            overflow: hidden;
        }

        .lg-hd {
            background: #f8f9fa;
            padding: 10px 13px;
            cursor: pointer;
            font-weight: 600;
            font-size: .88rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            user-select: none;
        }

        .lg-body {
            padding: 8px 13px;
            border-top: 1px solid #e2e8f0;
        }

        .chk-row {
            display: flex;
            align-items: center;
            margin-bottom: 6px;
            gap: 8px;
            font-size: .88rem;
        }

        .chk-row input {
            width: 1em;
            height: 1em;
            cursor: pointer;
            flex-shrink: 0;
        }

        /* =====================================================================
       MAP
    ===================================================================== */
        #map-wrap {
            flex: 1;
            position: relative;
            height: 100%;
        }

        #map {
            width: 100%;
            height: 100%;
            outline: none;
        }

        #sb-toggle {
            position: absolute;
            top: 14px;
            left: calc(var(--sidebar-w) + 12px);
            z-index: 1001;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #fff;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .2);
            cursor: pointer;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            transition: left .3s ease;
        }

        .fab {
            position: absolute;
            top: 14px;
            right: 14px;
            z-index: 999;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .fab-btn {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: #fff;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .18);
            color: #495057;
            font-size: 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .15s;
        }

        .fab-btn:hover {
            background: #f0f6ff;
            color: var(--primary);
        }

        /* Legend WMS */
        #legend-box {
            position: absolute;
            bottom: 26px;
            left: calc(var(--sidebar-w)+12px);
            z-index: 999;
            background: rgba(255, 255, 255, .97);
            padding: 12px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .15);
            width: 230px;
            max-height: 320px;
            overflow-y: auto;
            transition: left .3s ease;
            display: none;
        }

        /* Popup WMS */
        .leaflet-popup-content-wrapper {
            padding: 0;
            border-radius: 10px;
            overflow: hidden;
        }

        .leaflet-popup-content {
            margin: 0;
            width: 290px !important;
        }

        .pp-head {
            background: var(--primary);
            color: #fff;
            padding: 9px 13px;
            font-weight: 600;
            font-size: .9rem;
        }

        .pp-body {
            max-height: 200px;
            overflow-y: auto;
        }

        .pp-body table td {
            padding: 5px 11px;
            border-bottom: 1px solid #eee;
            font-size: .85rem;
        }

        /* =====================================================================
       IOT HOVER TOOLTIP
    ===================================================================== */
        .iot-tip {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }

        .iot-tip::before,
        .iot-tip::after {
            display: none !important;
        }

        .tip-box {
            background: linear-gradient(135deg, rgba(8, 14, 32, .97), rgba(14, 24, 52, .97));
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 10px 13px;
            min-width: 185px;
            font-family: 'Segoe UI', sans-serif;
            box-shadow: 0 8px 32px rgba(0, 0, 0, .5);
        }

        .tip-title {
            font-size: .78rem;
            font-weight: 700;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 6px;
            padding-bottom: 6px;
            margin-bottom: 6px;
            border-bottom: 1px solid rgba(77, 157, 224, .25);
        }

        .tip-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .tip-row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            font-size: .76rem;
            padding: 2px 0;
        }

        .tip-lbl {
            color: var(--muted);
        }

        .tip-val {
            color: #fff;
            font-weight: 600;
        }

        .tip-val.hi {
            color: #00d4ff;
        }

        .tip-time {
            font-size: .68rem;
            color: #3d5a78;
            margin-top: 5px;
            padding-top: 4px;
            border-top: 1px solid rgba(77, 157, 224, .15);
            text-align: right;
        }

        /* =====================================================================
       IOT DETAIL PANEL
    ===================================================================== */
        #iot-panel {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            width: var(--panel-w);
            background: linear-gradient(180deg, var(--dark) 0%, #080e1c 100%);
            border-left: 1px solid var(--border);
            z-index: 1002;
            display: flex;
            flex-direction: column;
            transform: translateX(100%);
            transition: transform .3s ease;
            box-shadow: -6px 0 36px rgba(0, 0, 0, .45);
        }

        #iot-panel.open {
            transform: translateX(0);
        }

        .ip-head {
            padding: 14px 16px 12px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            background: rgba(26, 86, 219, .1);
            flex-shrink: 0;
        }

        .ip-name {
            color: #fff;
            font-weight: 700;
            font-size: .95rem;
        }

        .ip-sub {
            color: var(--muted);
            font-size: .72rem;
            margin-top: 2px;
        }

        .ip-close {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .1);
            color: var(--muted);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .85rem;
            flex-shrink: 0;
        }

        .ip-close:hover {
            background: rgba(239, 68, 68, .15);
            color: #ef4444;
        }

        .ip-status {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 9px 16px;
            border-bottom: 1px solid rgba(77, 157, 224, .1);
            flex-shrink: 0;
            font-size: .76rem;
        }

        .badge {
            padding: 2px 9px;
            border-radius: 20px;
            font-size: .68rem;
            font-weight: 700;
        }

        .badge-on {
            background: rgba(34, 197, 94, .15);
            color: #22c55e;
            border: 1px solid rgba(34, 197, 94, .3);
        }

        .badge-warn {
            background: rgba(234, 179, 8, .15);
            color: #eab308;
            border: 1px solid rgba(234, 179, 8, .3);
        }

        .badge-off {
            background: rgba(239, 68, 68, .15);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, .3);
        }

        .ip-body {
            flex: 1;
            overflow-y: auto;
            padding: 12px 14px;
        }

        .ip-body::-webkit-scrollbar {
            width: 3px;
        }

        .ip-body::-webkit-scrollbar-thumb {
            background: rgba(77, 157, 224, .3);
            border-radius: 2px;
        }

        .sec-title {
            font-size: .65rem;
            font-weight: 700;
            letter-spacing: .8px;
            text-transform: uppercase;
            color: #3d6080;
            margin: 12px 0 7px;
        }

        .sec-title:first-child {
            margin-top: 0;
        }

        .metric-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 7px;
        }

        .metric {
            background: rgba(255, 255, 255, .04);
            border: 1px solid rgba(77, 157, 224, .12);
            border-radius: 8px;
            padding: 9px 11px;
            cursor: pointer;
            transition: all .18s;
            position: relative;
            overflow: hidden;
        }

        .metric::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--mc, #4d9de0);
            opacity: .4;
            transition: opacity .18s;
        }

        .metric:hover {
            background: rgba(77, 157, 224, .1);
            border-color: rgba(77, 157, 224, .35);
            transform: translateY(-1px);
        }

        .metric:hover::after {
            opacity: 1;
        }

        .metric.wide {
            grid-column: 1/-1;
        }

        .metric-lbl {
            font-size: .68rem;
            color: #4d6d8a;
            margin-bottom: 3px;
            line-height: 1.3;
        }

        .metric-val {
            font-size: 1.05rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
        }

        .metric-unit {
            font-size: .67rem;
            color: #3d5870;
            margin-left: 2px;
        }

        .metric-hint {
            font-size: .62rem;
            color: #2a4a62;
            margin-top: 3px;
            display: flex;
            align-items: center;
            gap: 3px;
        }

        /* =====================================================================
       CHART PANEL
    ===================================================================== */
        #chart-panel {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: var(--chart-h);
            background: linear-gradient(0deg, rgba(5, 10, 22, .99), rgba(8, 14, 32, .98));
            border-top: 1px solid var(--border);
            z-index: 1001;
            display: flex;
            flex-direction: column;
            transform: translateY(100%);
            transition: transform .3s ease, right .3s ease;
            box-shadow: 0 -6px 36px rgba(0, 0, 0, .5);
        }

        #chart-panel.open {
            transform: translateY(0);
        }

        #chart-panel.panel-open {
            right: var(--panel-w);
        }

        .cp-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 16px 8px;
            border-bottom: 1px solid rgba(77, 157, 224, .15);
            flex-shrink: 0;
        }

        .cp-title {
            color: #fff;
            font-weight: 600;
            font-size: .85rem;
            max-width: 55%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .cp-controls {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .rng-btn {
            padding: 3px 9px;
            border-radius: 5px;
            border: 1px solid rgba(77, 157, 224, .22);
            background: transparent;
            color: var(--muted);
            font-size: .7rem;
            cursor: pointer;
            transition: all .15s;
        }

        .rng-btn.active,
        .rng-btn:hover {
            background: rgba(26, 86, 219, .3);
            border-color: var(--primary);
            color: #7ab8ff;
        }

        .cp-close {
            background: transparent;
            border: none;
            color: #3d5870;
            cursor: pointer;
            font-size: .95rem;
        }

        .cp-close:hover {
            color: #ef4444;
        }

        .cp-canvas {
            flex: 1;
            padding: 6px 12px 10px;
            position: relative;
            min-height: 0;
            overflow: hidden;
        }

        .cp-loading {
            position: absolute;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            color: var(--muted);
            font-size: .82rem;
            gap: 8px;
        }

        .cp-loading.show {
            display: flex;
        }

        #iotChart {
            display: block;
            width: 100% !important;
            height: 100% !important;
        }

        /* =====================================================================
       SCADA MAP LEGEND
    ===================================================================== */
        #scada-legend {
            position: absolute;
            bottom: 26px;
            right: 14px;
            z-index: 999;
            background: rgba(8, 14, 32, .93);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 11px 13px;
            min-width: 190px;
            display: none;
            backdrop-filter: blur(8px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, .45);
        }

        .sl-title {
            color: #5a9abf;
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .7px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .sl-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
            font-size: .76rem;
            color: #c8d8ec;
        }

        .sl-dot {
            width: 13px;
            height: 13px;
            border-radius: 50%;
            flex-shrink: 0;
            border: 2px solid rgba(255, 255, 255, .25);
        }

        /* Pulse marker */
        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1
            }

            50% {
                transform: scale(1.7);
                opacity: 0
            }

            100% {
                transform: scale(1);
                opacity: 0
            }
        }

        .pulse-ring {
            animation: pulse 2.2s infinite;
            border-radius: 50%;
        }

        /* Loading */
        #loading {
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, .6);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
        }

        #loading.show {
            display: flex;
        }
    </style>

    <?php $this->head() ?>
</head>

<body>
    <?php $this->beginBody() ?>

    <div id="app">
        <!-- ═══ SIDEBAR ═══ -->
        <div id="sidebar">
            <div class="sb-head">
                <i class="fa-solid fa-droplet"></i>
                <a href="<?= Url::home() ?>" style="color:#fff;text-decoration:none;">QUẢN LÝ CẤP NƯỚC HỒ CẦU</a>
            </div>
            <div class="sb-tabs">
                <button class="active" onclick="switchTab(this,'t-layers')"><i
                        class="fa-solid fa-layer-group me-1"></i>Lớp</button>
                <button onclick="switchTab(this,'t-filter')"><i class="fa-solid fa-filter me-1"></i>Lọc</button>
                <button onclick="switchTab(this,'t-download')"><i class="fa-solid fa-download me-1"></i>Tải</button>
                <button onclick="switchTab(this,'t-search')"><i class="fa-solid fa-search me-1"></i>Tìm</button>
            </div>
            <div class="sb-body">
                <!-- TAB: LAYERS -->
                <div id="t-layers" class="tab-pane active">
                    <div class="mb-3">
                        <label class="form-label text-uppercase fw-bold text-muted" style="font-size:.75rem;">Bản đồ
                            nền</label>
                        <select id="basemap-sel" class="form-select form-select-sm" onchange="setBasemap(this.value)">
                            <option value="google_road" selected>Google Maps (Giao thông)</option>
                            <option value="hocau_base">Bản đồ Quy hoạch Hồ Cầu</option>
                            <option value="google_satellite">Google Maps (Vệ tinh)</option>
                            <option value="osm">OpenStreetMap</option>
                            <option value="none">Không nền</option>
                        </select>
                    </div>
                    <?php
                    $layerGroups = [
                        'Mạng Lưới Đường Ống' => [
                            ['id' => 'truyendan', 'layer' => 'capnuoc_hocau:network_ongtruyendan', 'label' => 'Ống nước thô', 'checked' => true, 'z' => 20],
                            ['id' => 'phanphoi', 'layer' => 'capnuoc_hocau:network_ongphanphoi', 'label' => 'Ống phân phối', 'checked' => true, 'z' => 20],
                            ['id' => 'dansinh', 'layer' => 'capnuoc_hocau:network_ongdansinh', 'label' => 'Ống dân sinh', 'checked' => true, 'z' => 20],
                            ['id' => 'moinoi', 'layer' => 'capnuoc_hocau:network_moinoi', 'label' => 'Mối nối', 'checked' => false, 'z' => 30],
                        ],
                        'Thiết Bị & Đồng Hồ' => [
                            ['id' => 'van', 'layer' => 'capnuoc_hocau:network_van', 'label' => 'Van', 'checked' => true, 'z' => 30],
                            ['id' => 'dhtong', 'layer' => 'capnuoc_hocau:network_donghotong', 'label' => 'Đồng hồ tổng', 'checked' => true, 'z' => 30],
                            ['id' => 'dhnhamay', 'layer' => 'capnuoc_hocau:network_donghonhamay', 'label' => 'Đồng hồ nhà máy', 'checked' => false, 'z' => 30],
                        ],
                        'Công Trình & Khác' => [
                            ['id' => 'nhamay', 'layer' => 'capnuoc_hocau:network_nhamaynuoc', 'label' => 'Nhà máy nước', 'checked' => true, 'z' => 10],
                            ['id' => 'base_ranhnhamay', 'layer' => 'capnuoc_hocau:base_ranhnhamay', 'label' => 'Ranh giới nhà máy nước', 'checked' => true, 'z' => 5],
                            ['id' => 'ham', 'layer' => 'capnuoc_hocau:network_hamkythuat', 'label' => 'Hầm kỹ thuật', 'checked' => false, 'z' => 10],
                            ['id' => 'hanhlang', 'layer' => 'capnuoc_hocau:network_hanglangantoan', 'label' => 'Hành lang an toàn', 'checked' => false, 'z' => 10],
                            ['id' => 'cocmoc', 'layer' => 'capnuoc_hocau:network_cocmoc', 'label' => 'Cọc mốc', 'checked' => false, 'z' => 30],
                            ['id' => 'suco', 'layer' => 'capnuoc_hocau:network_suco', 'label' => 'Điểm sự cố', 'checked' => true, 'z' => 40],
                        ],
                    ];
                    foreach ($layerGroups as $gname => $layers):
                        $gid = 'lg-' . md5($gname);
                        ?>
                        <div class="lg">
                            <div class="lg-hd" onclick="toggleLG('<?= $gid ?>')">
                                <span><?= $gname ?></span>
                                <i class="fa-solid fa-chevron-up lg-icon-<?= $gid ?>"></i>
                            </div>
                            <div class="lg-body" id="<?= $gid ?>">
                                <?php foreach ($layers as $l): ?>
                                    <div class="chk-row">
                                        <input type="checkbox" class="wms-chk" id="<?= $l['id'] ?>" data-layer="<?= $l['layer'] ?>"
                                            data-label="<?= $l['label'] ?>" data-z="<?= $l['z'] ?>" <?= $l['checked'] ? 'checked' : '' ?>>
                                        <label for="<?= $l['id'] ?>"><?= $l['label'] ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- SCADA IoT -->
                    <div class="lg">
                        <div class="lg-hd" onclick="toggleLG('lg-scada')"
                            style="background:linear-gradient(135deg,#f0fff4,#e8f5e9);">
                            <span style="color:#166534;font-weight:700;">
                                <i class="fa-solid fa-satellite-dish me-1" style="color:#16a34a;"></i>
                                SCADA Realtime
                            </span>
                            <div style="display:flex;align-items:center;gap:6px;">
                                <span id="iot-dot"
                                    style="width:8px;height:8px;border-radius:50%;background:#94a3b8;transition:background .3s;"></span>
                                <i class="fa-solid fa-chevron-down lg-icon-lg-scada"></i>
                            </div>
                        </div>
                        <div class="lg-body" id="lg-scada" style="display:none;">
                            <div class="chk-row mb-2">
                                <input type="checkbox" id="iot-toggle">
                                <label for="iot-toggle" style="font-weight:600;">Hiển thị trạm SCADA</label>
                            </div>
                            <div class="chk-row">
                                <input type="checkbox" id="iot-label" checked>
                                <label for="iot-label" style="color:#6c757d;font-size:.82rem;">Hiện tên trạm</label>
                            </div>
                            <div
                                style="background:#f8f9fa;border-radius:6px;padding:8px 10px;margin-top:8px;font-size:.76rem;color:#6c757d;">
                                <div style="display:flex;justify-content:space-between;"><span>Tổng trạm:</span><b
                                        id="iot-count">0</b></div>
                                <div style="display:flex;justify-content:space-between;margin-top:3px;"><span>Cập
                                        nhật:</span><b id="iot-upd">--:--</b></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB: FILTER -->
                <div id="t-filter" class="tab-pane">
                    <div class="alert alert-light border mb-3" style="font-size:.82rem;">
                        <i class="fa-solid fa-filter me-1"></i> Chọn lớp để lọc theo tiêu chí.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:.8rem;">Lớp dữ liệu</label>
                        <select class="form-select form-select-sm" id="filter-layer" onchange="onFilterChange()">
                            <option value="">-- Chọn đối tượng --</option>
                        </select>
                    </div>
                    <div id="filter-crit" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="font-size:.8rem;" id="filter-crit-label">Tiêu
                                chí</label>
                            <select class="form-select form-select-sm" id="filter-value"></select>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button class="btn btn-sm btn-primary" onclick="applyFilter()"><i
                                class="fa-solid fa-check me-1"></i>Áp dụng</button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="clearFilter()"><i
                                class="fa-solid fa-rotate-left me-1"></i>Xóa lọc</button>
                    </div>
                </div>

                <!-- TAB: DOWNLOAD -->
                <div id="t-download" class="tab-pane">
                    <div class="alert alert-light border mb-3" style="font-size:.82rem;">
                        <i class="fa-solid fa-download me-1"></i> Tải xuống dữ liệu GIS từ GeoServer.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:.8rem;">Chọn lớp</label>
                        <select class="form-select form-select-sm" id="dl-layer">
                            <option value="">-- Chọn --</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:.8rem;">Định dạng</label>
                        <select class="form-select form-select-sm" id="dl-fmt">
                            <option value="shape-zip">Shapefile (.zip)</option>
                            <option value="application/vnd.google-earth.kml+xml">Google Earth (.kml)</option>
                            <option value="application/json">GeoJSON (.json)</option>
                            <option value="csv">CSV (.csv)</option>
                        </select>
                    </div>
                    <div class="d-grid">
                        <button class="btn btn-sm btn-success" onclick="doDownload()">
                            <i class="fa-solid fa-cloud-arrow-down me-1"></i>Tải xuống
                        </button>
                    </div>
                </div>

                <!-- TAB: SEARCH -->
                <div id="t-search" class="tab-pane">
                    <div class="input-group mb-2">
                        <input type="text" class="form-control form-control-sm" id="search-kw"
                            placeholder="Nhập mã, tên...">
                        <button class="btn btn-sm btn-primary" onclick="doSearch()"><i
                                class="fa-solid fa-magnifying-glass"></i></button>
                    </div>
                    <select class="form-select form-select-sm mb-2" id="search-layer">
                        <option value="capnuoc_hocau:network_donghonhamay">Đồng hồ nhà máy</option>
                        <option value="capnuoc_hocau:network_van">Van</option>
                        <option value="capnuoc_hocau:network_suco">Sự cố</option>
                    </select>
                    <div id="search-res"
                        style="min-height:100px;border:1px solid #dee2e6;border-radius:6px;background:#f9fafb;">
                        <div style="padding:12px;text-align:center;color:#aaa;font-size:.82rem;">Kết quả tìm kiếm...
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button id="sb-toggle" onclick="toggleSidebar()"><i class="fa-solid fa-bars"></i></button>

        <!-- ═══ MAP ═══ -->
        <div id="map-wrap">
            <div class="fab">
                <button class="fab-btn" onclick="toggleLegend()" title="Chú giải"><i
                        class="fa-solid fa-list"></i></button>
                <button class="fab-btn" onclick="map.locate&&map.locate({setView:true,maxZoom:16})"
                    title="Vị trí tôi"><i class="fa-solid fa-location-crosshairs"></i></button>
                <button class="fab-btn" onclick="map.setView(defaultCenter,defaultZoom)" title="Reset"><i
                        class="fa-solid fa-house"></i></button>
            </div>

            <div id="legend-box">
                <div
                    style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;padding-bottom:8px;border-bottom:1px solid #eee;">
                    <span style="font-weight:700;font-size:.8rem;text-transform:uppercase;">Chú giải</span>
                    <i class="fa-solid fa-xmark" style="cursor:pointer;color:#aaa;"
                        onclick="document.getElementById('legend-box').style.display='none'"></i>
                </div>
                <div id="legend-content"></div>
            </div>

            <!-- SCADA Color Legend -->
            <div id="scada-legend">
                <div class="sl-title"><i class="fa-solid fa-satellite-dish me-1"></i>Áp lực (m)</div>
                <div class="sl-row"><span class="sl-dot"
                        style="background:var(--c-high);color:var(--c-high);box-shadow:0 0 5px currentColor;"></span>≥
                    25m — Cao</div>
                <div class="sl-row"><span class="sl-dot"
                        style="background:var(--c-med);color:var(--c-med);box-shadow:0 0 5px currentColor;"></span>15 –
                    25m</div>
                <div class="sl-row"><span class="sl-dot"
                        style="background:var(--c-low);color:var(--c-low);box-shadow:0 0 5px currentColor;"></span>1 –
                    15m — Thấp</div>
                <div class="sl-row"><span class="sl-dot"
                        style="background:var(--c-none);color:var(--c-none);box-shadow:0 0 5px currentColor;"></span>Không
                    có kênh</div>
                <div class="sl-row"><span class="sl-dot"
                        style="background:var(--c-alert);color:var(--c-alert);box-shadow:0 0 5px currentColor;"></span>Cảnh
                    báo</div>
                <div class="sl-row"><span class="sl-dot"
                        style="background:var(--c-offline);color:var(--c-offline);box-shadow:0 0 5px currentColor;"></span>Mất
                    tín hiệu (&gt;12h)</div>
            </div>

            <div id="map"></div>

            <!-- IOT DETAIL PANEL -->
            <div id="iot-panel">
                <div class="ip-head">
                    <div>
                        <div class="ip-name" id="ip-name">—</div>
                        <div class="ip-sub" id="ip-sub">—</div>
                    </div>
                    <button class="ip-close" onclick="closePanel()"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="ip-status">
                    <span class="badge badge-on" id="ip-badge">ONLINE</span>
                    <span style="color:var(--muted);font-size:.73rem;" id="ip-time"></span>
                </div>
                <div class="ip-body" id="ip-body"></div>
            </div>

            <!-- CHART PANEL -->
            <div id="chart-panel">
                <div class="cp-head">
                    <div class="cp-title" id="cp-title">Biểu đồ</div>
                    <div class="cp-controls">
                        <button class="rng-btn active" onclick="setRange('1h',this)">1H</button>
                        <button class="rng-btn" onclick="setRange('6h',this)">6H</button>
                        <button class="rng-btn" onclick="setRange('24h',this)">24H</button>
                        <button class="rng-btn" onclick="setRange('7d',this)">7N</button>
                        <button class="rng-btn" onclick="setRange('30d',this)">30N</button>
                        <button class="rng-btn" onclick="downloadChartExcel()" id="btn-dl-excel" title="Tải xuống Excel"
                            style="border-color:rgba(34,197,94,.3);color:#22c55e;">
                            <i class="fa-solid fa-file-excel me-1"></i>Excel
                        </button>
                        <button class="cp-close" onclick="closeChart()"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                </div>
                <div class="cp-canvas">
                    <div class="cp-loading" id="cp-loading"><i class="fa-solid fa-spinner fa-spin"></i> Đang tải...
                    </div>
                    <canvas id="iotChart"></canvas>
                </div>
            </div>
        </div>

        <div id="loading">
            <div
                style="background:#fff;padding:14px 20px;border-radius:8px;box-shadow:0 4px 20px rgba(0,0,0,.2);display:flex;align-items:center;gap:12px;">
                <div class="spinner-border text-primary" style="width:1.5rem;height:1.5rem;"></div><span
                    style="font-weight:600;color:var(--primary);">Đang xử lý...</span>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ================================================================
        // CONFIG
        // ================================================================
        const GEO_WMS = '<?= $geoserverWmsUrl ?>';
        const GEO_WFS = '<?= $geoserverWfsUrl ?>';
        const GEO_WMTS = '<?= $geoserverWmtsUrl ?>';
        const DET_LINKS = <?= Json::encode($detailLinks) ?>;
        const IOT_KEY = 'SCADA_HOCAU_2024_SECRET_KEY';
        const IOT_BASE = '/iot_api.php';
        const defaultCenter = [10.737202, 106.915000];
        const defaultZoom = 14;

        // ================================================================
        // FILTER CONFIG (dựa trên schema thực)
        // ================================================================
        const FILTER_CFG = {
            'capnuoc_hocau:network_ongphanphoi': { field: 'loaiong_id', label: 'Chất liệu ống', options: <?= Json::encode($filterData['loaiong'] ?? []) ?> },
            'capnuoc_hocau:network_donghonhamay': { field: 'hieudongho_id', label: 'Hiệu đồng hồ', options: <?= Json::encode($filterData['hieudongho'] ?? []) ?> },
            'capnuoc_hocau:network_van': { field: 'tinhtrang_id', label: 'Trạng thái van', options: <?= Json::encode($filterData['tinhtrang'] ?? []) ?> },
            'capnuoc_hocau:network_suco': { field: 'nguyennhansuco_id', label: 'Nguyên nhân sự cố', options: <?= Json::encode($filterData['nguyennhan'] ?? []) ?> },
            'capnuoc_hocau:network_hamkythuat': { field: 'loaiham_id', label: 'Loại hầm', options: <?= Json::encode($filterData['loaiham'] ?? []) ?> },
            'capnuoc_hocau:network_moinoi': { field: 'loaimoinoi_id', label: 'Kiểu mối nối', options: <?= Json::encode($filterData['loaimoinoi'] ?? []) ?> },
        };

        // ================================================================
        // NHÓM HIỂN THỊ CHỈ SỐ
        // key = tên nhóm (group) từ DB, value = config hiển thị
        // ================================================================
        const GROUP_CFG = {
            ap_suat: { label: 'Áp Suất', color: '#4d9de0', icon: 'fa-gauge-high' },
            luu_luong: { label: 'Lưu Lượng', color: '#22c55e', icon: 'fa-water' },
            dien: { label: 'Điện', color: '#f59e0b', icon: 'fa-bolt' },
            bom: { label: 'Tần Số Bơm', color: '#a78bfa', icon: 'fa-circle-notch' },
            chat_luong_nuoc: { label: 'Chất Lượng Nước', color: '#06b6d4', icon: 'fa-flask' },
        };

        // Field -> màu hiển thị
        const FIELD_COLOR = {
            ap_luc: '#00d4ff',
            ap_suat_cai_dat: '#7ab8ff',
            ap_luc_truoc_van: '#5bc4ff',
            ap_luc_sau_van: '#00d4ff',
            luu_luong_thuan: '#4ade80',
            luu_luong_nghich: '#f87171',
            cong_suat: '#fbbf24',
            chi_so_dien: '#fde68a',
            tan_so_bom_1: '#c4b5fd',
            tan_so_bom_2: '#ddd6fe',
            ph: '#67e8f9',
            clo: '#a5f3fc',
            do_duc: '#fcd34d',
            muc_be: '#86efac',
        };

        // Fields hiển thị trong tooltip hover (theo thứ tự ưu tiên)
        const TOOLTIP_PRIORITY = ['ap_luc', 'ap_luc_sau_van', 'luu_luong_thuan', 'cong_suat', 'tan_so_bom_1'];

        // ================================================================
        // STATE
        // ================================================================
        let map, wmsLayers = {};
        let iotLayerGroup, iotMarkers = {};
        let showLabels = true;
        let chartInst = null;
        let curChartCid = null, curChartRange = '1h';
        let curChartLabel = '', curChartUnit = '', curChartColor = '#4d9de0';

        // ================================================================
        // INIT
        // ================================================================
        document.addEventListener('DOMContentLoaded', () => {
            initMap();
            initWmsLayers();
            populateFilterList();
            populateDownloadList();

            iotLayerGroup = L.layerGroup();

            document.getElementById('iot-toggle').addEventListener('change', function () {
                if (this.checked) { map.addLayer(iotLayerGroup); document.getElementById('scada-legend').style.display = 'block'; }
                else { map.removeLayer(iotLayerGroup); document.getElementById('scada-legend').style.display = 'none'; closePanel(); }
            });
            document.getElementById('iot-label').addEventListener('change', function () {
                showLabels = this.checked; rebuildMarkerIcons();
            });

            setTimeout(() => { fetchIot(); setInterval(fetchIot, 30000); }, 1500);
        });

        // ================================================================
        // MAP
        // ================================================================
        function initMap() {
            map = L.map('map', { zoomControl: false, attributionControl: false }).setView(defaultCenter, defaultZoom);
            L.control.zoom({ position: 'bottomright' }).addTo(map);
            L.control.scale({ imperial: false, position: 'bottomright' }).addTo(map);
            setBasemap('google_road');
            map.on('click', onMapClick);
        }

        function setBasemap(t) {
            if (window._basemap) map.removeLayer(window._basemap);
            let layer;
            if (t === 'hocau_base') {
                layer = L.tileLayer(GEO_WMTS + '?service=WMTS&request=GetTile&version=1.0.0&layer=capnuoc_hocau:base_thuadat&style=&tilematrixset=EPSG:900913&format=image/png&tilematrix=EPSG:900913:{z}&tilerow={y}&tilecol={x}', { maxZoom: 22 });
            } else if (t.startsWith('google')) {
                const s = t === 'google_satellite' ? 's,h' : 'm';
                layer = L.tileLayer(`https://{s}.google.com/vt/lyrs=${s}&x={x}&y={y}&z={z}`, { maxZoom: 22, subdomains: ['mt0', 'mt1', 'mt2', 'mt3'] });
            } else if (t === 'osm') {
                layer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 });
            }
            if (layer && t !== 'none') { layer.addTo(map); layer.bringToBack(); window._basemap = layer; }
        }

        function initWmsLayers() {
            // Van, moi noi, coc moc chi hien tu zoom 15+ de tranh ban do bi roi
            const LAYER_MIN_ZOOM = {
                'capnuoc_hocau:network_van': 15,
                'capnuoc_hocau:network_moinoi': 15,
                'capnuoc_hocau:network_cocmoc': 16,
                'capnuoc_hocau:network_hamkythuat': 15,
            };
            document.querySelectorAll('.wms-chk').forEach(chk => {
                const name = chk.dataset.layer, z = parseInt(chk.dataset.z) || 10;
                const minZ = LAYER_MIN_ZOOM[name] || 1;
                const layer = L.tileLayer.wms(GEO_WMS, { layers: name, format: 'image/png', transparent: true, version: '1.1.0', tiled: true, maxZoom: 22, minZoom: minZ, zIndex: z });
                wmsLayers[name] = layer;
                if (chk.checked) layer.addTo(map);
                chk.addEventListener('change', function () { this.checked ? layer.addTo(map) : map.removeLayer(layer); updateLegend(); });
            });
            updateLegend();
        }

        // ================================================================
        // IOT COLOR
        // ================================================================
        function iotColor(d) {
            if (d.last_update) {
                const h = (Date.now() - new Date(d.last_update).getTime()) / 3600000;
                if (h > 12) return { fill: '#f97316', glow: '#f97316', status: 'offline', label: 'Mất tín hiệu' };
            }
            const ch = d.channels || {};
            const apField = ch['ap_luc'] ?? ch['ap_luc_sau_van'] ?? ch['ap_luc_truoc_van'] ?? null;
            if (!apField && !d.ap_luc) {
                // Kiểm tra xem có kênh nào không
                if (Object.keys(ch).length === 0) return { fill: '#64748b', glow: '#64748b', status: 'none', label: 'Không có kênh' };
            }
            const ap = parseFloat(apField?.value ?? d.ap_luc ?? 0) || 0;
            if (ap === 0 && !apField) return { fill: '#64748b', glow: '#94a3b8', status: 'none', label: 'Không có kênh' };
            if (ap >= 25) return { fill: '#1a6dff', glow: '#60a5fa', status: 'high', label: 'Áp lực cao ≥25m' };
            if (ap >= 15) return { fill: '#22c55e', glow: '#4ade80', status: 'med', label: 'Áp lực 15–25m' };
            if (ap >= 1) return { fill: '#eab308', glow: '#fde047', status: 'low', label: 'Áp lực 1–15m' };
            return { fill: '#ef4444', glow: '#f87171', status: 'alert', label: 'Cảnh báo' };
        }

        function createIcon(d) {
            const ci = iotColor(d);
            const lbl = showLabels
                ? `<div style="position:absolute;top:-17px;left:50%;transform:translateX(-50%);white-space:nowrap;font-size:9.5px;font-weight:700;color:#fff;text-shadow:0 1px 3px rgba(0,0,0,.95),0 0 8px rgba(0,0,0,.8);pointer-events:none;">${esc(d.ten_tram || d.ma_tram)}</div>`
                : '';
            return L.divIcon({
                html: `<div style="position:relative;width:20px;height:20px;">${lbl}<div style="position:absolute;top:0;left:0;width:20px;height:20px;border-radius:50%;background:${ci.glow};opacity:.3;" class="pulse-ring"></div><div style="width:20px;height:20px;border-radius:50%;background:${ci.fill};border:2.5px solid rgba(255,255,255,.85);box-shadow:0 2px 8px rgba(0,0,0,.4),0 0 14px ${ci.fill}55;position:relative;z-index:1;"></div></div>`,
                className: '', iconSize: [20, 20], iconAnchor: [10, 10]
            });
        }

        // ================================================================
        // IOT FETCH & RENDER
        // ================================================================
        function fetchIot() {
            fetch(IOT_BASE + '?action=get&key=' + IOT_KEY, { headers: { 'Authorization': 'Bearer ' + IOT_KEY } })
                .then(r => r.json())
                .then(data => {
                    const keys = Object.keys(data);
                    document.getElementById('iot-count').textContent = keys.length;
                    document.getElementById('iot-upd').textContent = new Date().toLocaleTimeString('vi-VN');
                    document.getElementById('iot-dot').style.background = keys.length ? '#22c55e' : '#94a3b8';

                    keys.forEach(k => renderMarker(k, data[k]));
                    Object.keys(iotMarkers).forEach(k => {
                        if (!data[k]) { iotLayerGroup.removeLayer(iotMarkers[k]); delete iotMarkers[k]; }
                    });
                })
                .catch(() => { document.getElementById('iot-dot').style.background = '#ef4444'; });
        }

        function renderMarker(sid, d) {
            if (!d.lat || !d.lng) return;
            const ll = [parseFloat(d.lat), parseFloat(d.lng)];
            const icon = createIcon(d);

            if (iotMarkers[sid]) {
                iotMarkers[sid].setLatLng(ll).setIcon(icon);
                iotMarkers[sid]._d = d;
                iotMarkers[sid].setTooltipContent(buildTooltip(d));
            } else {
                const m = L.marker(ll, { icon, zIndexOffset: 1000 });
                m._d = d;
                m.bindTooltip(buildTooltip(d), { permanent: false, direction: 'right', className: 'iot-tip', offset: [14, 0], opacity: 1 });
                m.on('click', e => { L.DomEvent.stopPropagation(e); openPanel(sid, m._d); });
                m.addTo(iotLayerGroup);
                iotMarkers[sid] = m;
            }
        }

        function rebuildMarkerIcons() {
            Object.values(iotMarkers).forEach(m => { if (m._d) m.setIcon(createIcon(m._d)); });
        }

        // ================================================================
        // TOOLTIP HTML
        // ================================================================
        function buildTooltip(d) {
            const ci = iotColor(d);
            const ch = d.channels || {};
            let rows = '';

            // Hiển thị các field theo thứ tự ưu tiên
            TOOLTIP_PRIORITY.forEach(fk => {
                const chanObj = ch[fk];
                const val = chanObj?.value ?? d[fk];
                if (val == null || val === '' || (val === 0 && !chanObj)) return;
                const label = chanObj?.label || fk;
                const unit = chanObj?.unit || '';
                const hi = fk === 'ap_luc' || fk === 'ap_luc_sau_van' ? ' hi' : '';
                rows += `<div class="tip-row"><span class="tip-lbl">${esc(label)}</span><span class="tip-val${hi}">${fmt(val)} <small style="color:#3d5870;">${unit}</small></span></div>`;
            });

            if (!rows) rows = `<div class="tip-row"><span class="tip-lbl" style="color:#3a5a78;">Chưa có dữ liệu</span></div>`;

            return `<div class="tip-box">
        <div class="tip-title">
            <span class="tip-dot" style="background:${ci.fill};box-shadow:0 0 6px ${ci.glow};"></span>
            ${esc(d.ten_tram || d.ma_tram)}
        </div>
        ${rows}
        <div class="tip-time"><i class="fa-regular fa-clock me-1"></i>${fmtTs(d.last_update || d.timestamp)}</div>
    </div>`;
        }

        // ================================================================
        // DETAIL PANEL
        // ================================================================
        function openPanel(sid, d) {
            const ci = iotColor(d);
            document.getElementById('ip-name').textContent = d.ten_tram || sid;
            document.getElementById('ip-sub').textContent = `Mã trạm: ${sid} | ${d.loai || ''}`;

            const badge = document.getElementById('ip-badge');
            badge.className = 'badge ' + (ci.status === 'offline' ? 'badge-off' : ci.status === 'alert' ? 'badge-warn' : 'badge-on');
            badge.textContent = ci.status === 'offline' ? 'MẤT TÍN HIỆU' : ci.status === 'alert' ? 'CẢNH BÁO' : 'ONLINE';
            document.getElementById('ip-time').textContent = d.last_update ? 'Cập nhật: ' + fmtTs(d.last_update) : '';

            const ch = d.channels || {};
            // Gom channel theo nhóm
            const groups = {};
            Object.entries(ch).forEach(([fk, chanObj]) => {
                const grp = chanObj.group || 'khac';
                if (!groups[grp]) groups[grp] = [];
                groups[grp].push({ fk, ...chanObj });
            });

            let html = '';
            const grpOrder = ['ap_suat', 'luu_luong', 'dien', 'bom', 'chat_luong_nuoc'];
            [...grpOrder, ...Object.keys(groups).filter(g => !grpOrder.includes(g))].forEach(grp => {
                if (!groups[grp]) return;
                const gcfg = GROUP_CFG[grp];
                const grpLabel = gcfg ? `<i class="fa-solid ${gcfg.icon} me-1" style="color:${gcfg.color};"></i>${gcfg.label}` : grp;
                html += `<div class="sec-title">${grpLabel}</div><div class="metric-grid">`;
                groups[grp].forEach(c => {
                    const col = FIELD_COLOR[c.fk] || gcfg?.color || '#4d9de0';
                    const wide = ['san_luong_ngay', 'san_luong_thang', 'san_luong_nam', 'chi_so_dien'].includes(c.fk) ? ' wide' : '';
                    html += `
                <div class="metric${wide}" style="--mc:${col};" onclick="openChart('${c.channel_id}','${esc(c.label)}','${esc(c.unit)}','${col}')">
                    <div class="metric-lbl">${esc(c.label)}</div>
                    <div class="metric-val">${fmt(c.value)}<span class="metric-unit">${esc(c.unit)}</span></div>
                    <div class="metric-hint"><i class="fa-solid fa-chart-line" style="font-size:.58rem;"></i>Xem lịch sử</div>
                </div>`;
                });
                html += '</div>';
            });

            if (!html) html = `<div style="color:var(--muted);text-align:center;padding:24px;font-size:.82rem;">Chưa có dữ liệu chi tiết</div>`;
            document.getElementById('ip-body').innerHTML = html;
            document.getElementById('iot-panel').classList.add('open');
            document.getElementById('chart-panel').classList.add('panel-open');
        }

        function closePanel() {
            document.getElementById('iot-panel').classList.remove('open');
            document.getElementById('chart-panel').classList.remove('panel-open');
            closeChart();
        }

        // ================================================================
        // CHART
        // ================================================================
        function openChart(channelId, label, unit, color) {
            if (!channelId) return;
            curChartCid = channelId;
            curChartLabel = label;
            curChartUnit = unit;
            curChartColor = color || '#4d9de0';
            document.getElementById('cp-title').textContent = label;
            document.getElementById('chart-panel').classList.add('open');
            loadChart();
        }

        function closeChart() {
            document.getElementById('chart-panel').classList.remove('open');
            if (chartInst) { chartInst.destroy(); chartInst = null; }
        }

        function setRange(r, btn) {
            curChartRange = r;
            document.querySelectorAll('.rng-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            loadChart();
        }

        function loadChart() {
            if (!curChartCid) return;
            document.getElementById('cp-loading').classList.add('show');
            if (chartInst) { chartInst.destroy(); chartInst = null; }

            const url = `${IOT_BASE}?action=history&channel_id=${encodeURIComponent(curChartCid)}&range=${curChartRange}&key=${IOT_KEY}`;
            fetch(url, { headers: { 'Authorization': 'Bearer ' + IOT_KEY } })
                .then(r => r.json())
                .then(data => {
                    document.getElementById('cp-loading').classList.remove('show');
                    renderChart(data.labels || [], data.values || []);
                })
                .catch(() => {
                    document.getElementById('cp-loading').classList.remove('show');
                    renderChart([], []);
                });
        }

        function renderChart(labels, values) {
            const ctx = document.getElementById('iotChart').getContext('2d');
            const grad = ctx.createLinearGradient(0, 0, 0, 220);
            grad.addColorStop(0, hexAlpha(curChartColor, .38));
            grad.addColorStop(1, hexAlpha(curChartColor, .02));

            chartInst = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels.map(t => fmtTsShort(t)),
                    datasets: [{
                        label: `${curChartLabel} (${curChartUnit})`,
                        data: values,
                        borderColor: curChartColor,
                        backgroundColor: grad,
                        borderWidth: 1.8,
                        pointRadius: labels.length > 80 ? 0 : 2.5,
                        pointHoverRadius: 5,
                        pointBackgroundColor: curChartColor,
                        tension: 0.35, fill: true,
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(8,14,32,.95)',
                            titleColor: '#7ab8ff', bodyColor: '#fff',
                            borderColor: 'rgba(77,157,224,.3)', borderWidth: 1, padding: 10,
                            callbacks: { label: c => ` ${fmt(c.parsed.y)} ${curChartUnit}` }
                        }
                    },
                    scales: {
                        x: { ticks: { color: '#3d6080', font: { size: 9 }, maxTicksLimit: 8 }, grid: { color: 'rgba(77,157,224,.06)' } },
                        y: { ticks: { color: '#3d6080', font: { size: 9 } }, grid: { color: 'rgba(77,157,224,.09)' } }
                    }
                }
            });
        }

        // ================================================================
        // WMS FEATURE INFO
        // ================================================================

        // Mapping ten cot -> nhan tieng Viet (dung chung cho popup)
        const FIELD_LABELS = {
            'id': 'Mã số', 'objectid': 'Mã đối tượng', 'objectid_1': 'Mã đối tượng',
            'masuco': 'Mã sự cố', 'mavattu': 'Mã vật tư', 'ten': 'Tên',
            'vitri': 'Vị trí', 'ghichu': 'Ghi chú', 'mota': 'Mô tả',
            'created_at': 'Ngày tạo', 'updated_at': 'Ngày cập nhật',
            'ngaylapdat': 'Ngày lắp đặt', 'ngayghinhan': 'Ngày ghi nhận',
            'vatlieu': 'Vật liệu', 'coong': 'Cỡ ống (mm)', 'shape_leng': 'Chiều dài (m)',
            'shape_length': 'Chiều dài (m)', 'chieudai': 'Chiều dài',
            'congtrinh': 'Công trình', 'dvtk': 'Đơn vị thiết kế',
            'dvtc': 'Đơn vị thi công', 'bvhc': 'Bản vẽ hoàn công',
            'loaiong': 'Loại ống', 'loaiong_id': 'Loại ống',
            'tinhtrang': 'Tình trạng', 'tinhtrang_id': 'Tình trạng',
            'shd': 'Số hiệu đồng hồ', 'ten_khach_hang': 'Tên khách hàng',
            'dia_chi': 'Địa chỉ', 'hieudongho': 'Hiệu đồng hồ', 'hieudongho_id': 'Hiệu đồng hồ',
            'chiso': 'Chỉ số', 'ngaydoc': 'Ngày đọc',
            'loaivan': 'Loại van', 'loaivan_id': 'Loại van',
            'dongmo': 'Trạng thái đóng/mở', 'lydoghi': 'Lý do ghi nhận',
            'nguyennhan': 'Nguyên nhân', 'nguyennhansuco_id': 'Nguyên nhân sự cố',
            'loaisuco': 'Loại sự cố', 'loaisuco_id': 'Loại sự cố',
            'mucdo': 'Mức độ', 'trangthai': 'Trạng thái',
            'loaiham': 'Loại hầm', 'loaiham_id': 'Loại hầm', 'kichthuoc': 'Kích thước',
            'loaimoinoi': 'Loại mối nối', 'loaimoinoi_id': 'Loại mối nối',
            'tendonvi': 'Tên đơn vị', 'diachi': 'Địa chỉ', 'congsuattkm': 'Công suất TKM',
            'congsuatthietke': 'Công suất thiết kế', 'namxaydung': 'Năm xây dựng',
            'chieurong': 'Chiều rộng (m)', 'loaimoc': 'Loại mốc', 'ky_hieu': 'Ký hiệu',
            'status': 'Trạng thái', 'pipesize': 'Cỡ ống', 'dma_in': 'DMA In', 'dma_out': 'DMA Out',
        };
        function getFieldLabel(k) {
            return FIELD_LABELS[k.toLowerCase()] || (k.charAt(0).toUpperCase() + k.slice(1).replace(/_/g, ' '));
        }

        // Build rows HTML cho 1 feature
        const SKIP_FIELDS = new Set(['geom', 'the_geom', 'geometry', 'geojson', 'bbox', 'coordinates',
            'type', 'shape_leng', 'shape_area', 'st_area', 'st_length', 'shape_length',
            'lat', 'long', 'lng', 'x', 'y', 'objectid_1', 'objectid', 'gid', 'id_0', 'id', 'status',
            'created_by', 'updated_by', 'file_dinhkem', 'tinh_trang']);
        // Ghi chu: an 'id' va 'objectid' khoi bang vi GeoServer FID moi la PK dung
        // Cac field nay hien trong badge "ID: X" o tieu de popup thay vi trong bang

        function buildFeatureRows(props) {
            let rows = '';
            for (const k in props) {
                const v = props[k];
                if (v != null && v !== '' && !SKIP_FIELDS.has(k.toLowerCase())) {
                    rows += `<tr>
                <td style="font-weight:600;color:#555;white-space:nowrap;padding:5px 10px 5px 12px;font-size:.82rem;background:#fafafa;border-right:1px solid #f0f0f0;">${getFieldLabel(k)}</td>
                <td style="padding:5px 12px;font-size:.85rem;">${v}</td>
            </tr>`;
                }
            }
            return rows;
        }

        // Lay ID dung de link den trang chi tiet
        // GeoServer FID suffix = PostgreSQL primary key (xac nhan thuc te)
        // Vi du: fid="network_ongphanphoi.38" -> PK=38, con props.id=18 la field khac
        function getFeatureId(props, fid) {
            // Uu tien GeoServer FID suffix
            if (fid && fid.includes('.')) {
                const fidNum = parseInt(fid.split('.').pop());
                if (fidNum && fidNum > 0) return fidNum;
            }
            // Fallback: props.objectid > props.id (tranh dung props.id vi co the la field khac)
            if (props.objectid && props.objectid != 0) return props.objectid;
            if (props.id && props.id != 0) return props.id;
            return null;
        }

        function onMapClick(e) {
            const active = Array.from(document.querySelectorAll('.wms-chk:checked')).map(c => c.dataset.layer);
            if (!active.length) return;
            document.body.style.cursor = 'wait';

            // Dung buffer_pixel lon hon de bat duoc features gan vung click
            const p = {
                request: 'GetFeatureInfo', service: 'WMS', srs: 'EPSG:4326', version: '1.1.0',
                format: 'image/png',
                bbox: map.getBounds().toBBoxString(),
                height: map.getSize().y, width: map.getSize().x,
                layers: active.join(','), query_layers: active.join(','),
                info_format: 'application/json',
                feature_count: 10,   // Lay nhieu hon de hien danh sach
                x: Math.round(e.containerPoint.x),
                y: Math.round(e.containerPoint.y),
                BUFFER: 8,           // Buffer pixel de bat diem/line de hon
            };

            fetch(GEO_WMS + L.Util.getParamString(p, GEO_WMS, true))
                .then(r => r.json())
                .then(data => {
                    document.body.style.cursor = 'default';
                    if (!data.features?.length) return;

                    const features = data.features;

                    if (features.length === 1) {
                        // Chi co 1 doi tuong: hien luon
                        showFeaturePopup(e.latlng, features[0]);
                    } else {
                        // Nhieu doi tuong: hien danh sach de user chon
                        showFeatureListPopup(e.latlng, features);
                    }
                })
                .catch(() => { document.body.style.cursor = 'default'; });
        }

        // Popup chi tiet 1 doi tuong
        function showFeaturePopup(latlng, f) {
            const props = f.properties;
            const rows = buildFeatureRows(props);

            // Lay layer key tu feature id (vi du: "network_ongphanphoi.58" -> "network_ongphanphoi")
            let btn = '';
            if (f.id && f.id.includes('.')) {
                const layerKey = f.id.split('.')[0];
                const oid = getFeatureId(props, f.id);  // GeoServer FID suffix = PostgreSQL PK
                if (DET_LINKS[layerKey] && oid) {
                    btn = `<div style="padding:8px 12px;border-top:1px solid #eee;text-align:right;">
                <a href="${DET_LINKS[layerKey]}?id=${oid}" target="_blank"
                   class="btn btn-sm btn-primary" style="font-size:.8rem;">
                    <i class="fa-solid fa-circle-info me-1"></i>Xem chi tiết
                </a>
            </div>`;
                }
            }

            // Tieu de: lay ten layer dep hon
            const layerName = f.id ? f.id.split('.')[0].replace('network_', '').replace(/_/g, ' ') : 'Đối tượng';
            const title = layerName.charAt(0).toUpperCase() + layerName.slice(1);

            L.popup({ maxWidth: 320, maxHeight: 400 })
                .setLatLng(latlng)
                .setContent(`
        <div class="pp-head" style="display:flex;justify-content:space-between;align-items:center;">
            <span>${title}</span>
            ${getFeatureId(props, f.id) ? `<small style="opacity:.7;font-size:.72rem;">ID: ${getFeatureId(props, f.id)}</small>` : ''}
        </div>
        <div class="pp-body">
            <table style="width:100%;border-collapse:collapse;">${rows}</table>
            ${btn}
        </div>`)
                .openOn(map);
        }

        // Popup danh sach nhieu doi tuong de user chon
        function showFeatureListPopup(latlng, features) {
            // Luu cache toan bo features theo index tuyen tinh
            window._pendingFeatures = features;
            window._pendingLatlng = latlng;

            // Nhom theo layer de hien thi dep hon
            const byLayer = {};
            features.forEach((f, globalIdx) => {
                const lk = f.id ? f.id.split('.')[0] : 'unknown';
                if (!byLayer[lk]) byLayer[lk] = [];
                byLayer[lk].push({ f, globalIdx });
            });

            let listHtml = '';
            for (const lk in byLayer) {
                const layerLabel = lk.replace('network_', '').replace(/_/g, ' ');
                const label = layerLabel.charAt(0).toUpperCase() + layerLabel.slice(1);
                listHtml += `<div style="font-size:.7rem;font-weight:700;color:#aaa;text-transform:uppercase;
                                 letter-spacing:.6px;padding:8px 12px 3px;background:#fafafa;">${label}</div>`;

                byLayer[lk].forEach(({ f, globalIdx }) => {
                    const props = f.properties;
                    const oid = getFeatureId(props, f.id);
                    const desc = props.vatlieu || props.ten_khach_hang || props.vitri
                        || props.mavattu || props.masuco || props.tendonvi
                        || props.loaivan || props.loaiham
                        || (oid ? 'ID: ' + oid : 'Đối tượng');
                    const sub = props.coong ? props.coong + ' mm'
                        : props.shd ? 'SHĐ: ' + props.shd
                            : props.ngaylapdat ? props.ngaylapdat
                                : oid ? 'ID: ' + oid : '';

                    // Dung data-gidx thay vi onclick voi latlng de tranh HTML attribute escaping
                    listHtml += `<div class="pp-feature-item" data-gidx="${globalIdx}"
                     style="padding:9px 12px;border-bottom:1px solid #f3f3f3;cursor:pointer;
                            display:flex;align-items:center;gap:9px;">
                    <i class="fa-solid fa-circle-dot" style="color:#3699ff;font-size:.65rem;flex-shrink:0;"></i>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:.85rem;font-weight:600;color:#222;
                                    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${desc}</div>
                        ${sub ? `<div style="font-size:.73rem;color:#aaa;">${sub}</div>` : ''}
                    </div>
                    <i class="fa-solid fa-chevron-right" style="color:#ccc;font-size:.65rem;flex-shrink:0;"></i>
                </div>`;
                });
            }

            const popup = L.popup({ maxWidth: 300, className: 'pp-list-popup' })
                .setLatLng(latlng)
                .setContent(`
            <div class="pp-head" style="display:flex;align-items:center;gap:7px;">
                <i class="fa-solid fa-layer-group" style="font-size:.85rem;opacity:.8;"></i>
                ${features.length} đối tượng tại vị trí này
            </div>
            <div class="pp-body" style="max-height:320px;overflow-y:auto;padding:0;">
                ${listHtml}
            </div>`)
                .openOn(map);

            // Gan event sau khi popup duoc them vao DOM
            // Dung MutationObserver hoac setTimeout ngan de dam bao DOM da render
            setTimeout(() => {
                document.querySelectorAll('.pp-feature-item').forEach(el => {
                    el.addEventListener('mouseenter', () => el.style.background = '#f0f7ff');
                    el.addEventListener('mouseleave', () => el.style.background = '');
                    el.addEventListener('click', () => {
                        const gidx = parseInt(el.dataset.gidx);
                        const feat = window._pendingFeatures?.[gidx];
                        if (feat) showFeaturePopup(window._pendingLatlng, feat);
                    });
                });
            }, 80);
        }

        // ================================================================
        // SIDEBAR / UI
        // ================================================================
        function switchTab(btn, id) {
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.sb-tabs button').forEach(b => b.classList.remove('active'));
            document.getElementById(id).classList.add('active');
            btn.classList.add('active');
        }
        function toggleSidebar() {
            const sb = document.getElementById('sidebar');
            const btn = document.getElementById('sb-toggle');
            const lb = document.getElementById('legend-box');
            sb.classList.toggle('collapsed');
            const c = sb.classList.contains('collapsed');
            btn.style.left = c ? '12px' : 'calc(var(--sidebar-w) + 12px)';
            lb.style.left = c ? '12px' : 'calc(var(--sidebar-w) + 12px)';
        }
        function toggleLG(id) {
            const body = document.getElementById(id);
            const icon = document.querySelector(`.lg-icon-${id}`);
            const show = body.style.display === 'none';
            body.style.display = show ? 'block' : 'none';
            icon.className = `fa-solid ${show ? 'fa-chevron-up' : 'fa-chevron-down'} lg-icon-${id}`;
        }
        function toggleLegend() {
            const box = document.getElementById('legend-box');
            box.style.display = box.style.display === 'none' ? 'block' : 'none';
        }
        function updateLegend() {
            const content = document.getElementById('legend-content');
            content.innerHTML = '';
            document.querySelectorAll('.wms-chk:checked').forEach(chk => {
                const url = `${GEO_WMS}?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=${chk.dataset.layer}`;
                const div = document.createElement('div');
                div.style.cssText = 'display:flex;align-items:center;margin-bottom:6px;gap:8px;font-size:.82rem;';
                div.innerHTML = `<img src="${url}" style="width:20px;flex-shrink:0;"><span>${chk.dataset.label}</span>`;
                content.appendChild(div);
            });
        }
        function showLoading(v) { document.getElementById('loading').classList.toggle('show', v); }

        // ================================================================
        // FILTER
        // ================================================================
        function populateFilterList() {
            const sel = document.getElementById('filter-layer');
            sel.innerHTML = '<option value="">-- Chọn đối tượng --</option>';
            document.querySelectorAll('.wms-chk').forEach(chk => {
                if (FILTER_CFG[chk.dataset.layer]) {
                    sel.add(new Option(chk.dataset.label, chk.dataset.layer));
                }
            });
        }
        function onFilterChange() {
            const v = document.getElementById('filter-layer').value;
            const c = document.getElementById('filter-crit');
            const s = document.getElementById('filter-value');
            s.innerHTML = '';
            if (!v || !FILTER_CFG[v]) { c.style.display = 'none'; return; }
            c.style.display = 'block';
            document.getElementById('filter-crit-label').textContent = FILTER_CFG[v].label;
            s.add(new Option('-- Tất cả --', ''));
            (FILTER_CFG[v].options || []).forEach(o => s.add(new Option(o.ten, o.id)));
        }
        function applyFilter() {
            const lname = document.getElementById('filter-layer').value;
            const val = document.getElementById('filter-value').value;
            if (!lname) { alert('Vui lòng chọn lớp!'); return; }
            showLoading(true);
            const layer = wmsLayers[lname];
            if (layer) {
                if (val) layer.setParams({ cql_filter: `${FILTER_CFG[lname].field} = ${val}` });
                else { delete layer.wmsParams.cql_filter; layer.redraw(); }
                const chk = document.querySelector(`.wms-chk[data-layer="${lname}"]`);
                if (chk && !chk.checked) { chk.checked = true; layer.addTo(map); updateLegend(); }
            }
            setTimeout(() => showLoading(false), 500);
        }
        function clearFilter() {
            document.getElementById('filter-layer').value = '';
            document.getElementById('filter-crit').style.display = 'none';
            Object.values(wmsLayers).forEach(l => { if (l.wmsParams?.cql_filter) { delete l.wmsParams.cql_filter; l.redraw(); } });
            alert('Đã xóa bộ lọc.');
        }

        // ================================================================
        // DOWNLOAD
        // ================================================================
        function populateDownloadList() {
            const sel = document.getElementById('dl-layer');
            document.querySelectorAll('.wms-chk').forEach(chk => sel.add(new Option(chk.dataset.label, chk.dataset.layer)));
        }
        function doDownload() {
            const layer = document.getElementById('dl-layer').value;
            const fmt = document.getElementById('dl-fmt').value;
            if (!layer) { alert('Vui lòng chọn lớp!'); return; }
            const params = new URLSearchParams({
                service: 'WFS',
                version: '1.0.0',
                request: 'GetFeature',
                typeName: layer,
                outputFormat: fmt
            });
            window.location.assign(`${GEO_WFS}?${params.toString()}`);
        }

        // ================================================================
        // SEARCH
        // ================================================================
        const SEARCH_CFG = {
            'capnuoc_hocau:network_donghonhamay': { fields: ['objectid', 'shd', 'ten_khach_hang', 'dia_chi'], display: p => `<b>${p.ten_khach_hang || 'N/A'}</b><br><small>${p.dia_chi || ''}</small>` },
            'capnuoc_hocau:network_van': { fields: ['objectid', 'vitri', 'lydoghi'], display: p => `<b>Van: ${p.objectid}</b><br><small>${p.vitri || ''}</small>` },
            'capnuoc_hocau:network_suco': { fields: ['masuco', 'vitri', 'nguyennhan', 'ghichu'], display: p => `<b>SC: ${p.masuco || p.id}</b><br><small>${p.vitri || ''}</small>` },
            'default': { fields: ['objectid'], display: p => `<b>${p.objectid || 'Đối tượng'}</b>` },
        };
        function doSearch() {
            const kw = document.getElementById('search-kw').value.trim();
            const sl = document.getElementById('search-layer').value;
            const rs = document.getElementById('search-res');
            if (!kw) { alert('Nhập từ khóa!'); return; }
            showLoading(true); rs.innerHTML = '';
            const cfg = SEARCH_CFG[sl] || SEARCH_CFG['default'];
            const inner = cfg.fields.map(f => `<PropertyIsLike wildCard="*" singleChar="." escapeChar="!" matchCase="false"><PropertyName>${f}</PropertyName><Literal>*${kw}*</Literal></PropertyIsLike>`).join('');
            const filter = cfg.fields.length > 1 ? `<Filter xmlns="http://www.opengis.net/ogc"><Or>${inner}</Or></Filter>` : `<Filter xmlns="http://www.opengis.net/ogc">${inner}</Filter>`;
            fetch(`${GEO_WFS}?service=WFS&version=1.1.0&request=GetFeature&typeName=${sl}&outputFormat=application/json&filter=${encodeURIComponent(filter)}&maxFeatures=10`)
                .then(r => r.json())
                .then(data => {
                    showLoading(false);
                    if (data.features?.length) {
                        data.features.forEach(f => {
                            const d = document.createElement('div');
                            d.style.cssText = 'padding:9px 11px;border-bottom:1px solid #eee;cursor:pointer;font-size:.83rem;';
                            d.innerHTML = `<i class="fa-solid fa-location-dot text-primary me-2"></i>${cfg.display(f.properties)}`;
                            d.onmouseover = () => d.style.background = '#f0f7ff';
                            d.onmouseout = () => d.style.background = '';
                            d.onclick = () => {
                                const l = L.geoJSON(f, { pointToLayer: (ft, ll) => L.circleMarker(ll, { radius: 8, fillColor: '#ff0000', color: '#fff', weight: 2, fillOpacity: .8 }), style: { color: '#ff0000', weight: 4 } });
                                map.fitBounds(l.getBounds(), { maxZoom: 18 }); l.addTo(map).bindPopup(cfg.display(f.properties)).openPopup();
                                setTimeout(() => map.removeLayer(l), 6000);
                            };
                            rs.appendChild(d);
                        });
                    } else {
                        rs.innerHTML = '<div style="padding:14px;text-align:center;color:#aaa;font-size:.82rem;">Không tìm thấy kết quả.</div>';
                    }
                })
                .catch(() => { showLoading(false); rs.innerHTML = '<div style="padding:14px;text-align:center;color:red;font-size:.82rem;">Lỗi kết nối!</div>'; });
        }


        // ================================================================
        // DOWNLOAD CHART DATA AS EXCEL (CSV)
        // ================================================================
        function downloadChartExcel() {
            if (!chartInst || !curChartCid) return;

            const labels = chartInst.data.labels || [];
            const values = chartInst.data.datasets[0]?.data || [];

            if (!labels.length) {
                alert('Chưa có dữ liệu để tải xuống');
                return;
            }

            // Tao CSV content
            const tenTram = document.getElementById('ip-name')?.textContent || 'tram';
            const tenKenh = curChartLabel || 'du_lieu';
            const donVi = curChartUnit || '';
            const range = curChartRange || '24h';

            let csv = '\uFEFF'; // BOM UTF-8 de Excel doc dung tieng Viet
            csv += 'Th\u1EDDi gian,Gi\u00E1 tr\u1ECB (' + donVi + ')\n';
            labels.forEach((lbl, i) => {
                const val = values[i] !== undefined && values[i] !== null ? values[i] : '';
                csv += '"' + lbl + '",' + val + '\n';
            });

            // Tao file blob va download
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            const filename = [tenTram, tenKenh, range].join('_').replace(/[^a-zA-Z0-9_\u00C0-\u024F]/g, '_') + '.csv';

            a.href = url;
            a.download = filename;
            a.style.display = 'none';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }
        // ================================================================
        // UTILITIES
        // ================================================================
        function fmt(v) {
            const n = parseFloat(v);
            if (isNaN(n)) return String(v);
            if (Math.abs(n) >= 1e6) return (n / 1e6).toFixed(3) + 'M';
            if (Math.abs(n) >= 1e3) return n.toLocaleString('vi-VN');
            return n % 1 === 0 ? String(n) : n.toFixed(2);
        }
        function fmtTs(ts) {
            if (!ts) return '—';
            const d = new Date(ts);
            return isNaN(d) ? ts : d.toLocaleString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        }
        function fmtTsShort(ts) {
            if (!ts) return '';
            const d = new Date(ts);
            return isNaN(d) ? ts : d.toLocaleString('vi-VN', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' });
        }
        function esc(s) { return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
        function hexAlpha(hex, a) {
            const r = parseInt(hex.slice(1, 3), 16), g = parseInt(hex.slice(3, 5), 16), b = parseInt(hex.slice(5, 7), 16);
            return `rgba(${r},${g},${b},${a})`;
        }
    </script>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>
