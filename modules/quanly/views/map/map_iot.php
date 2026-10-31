<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Bản đồ thiết bị IoT</title>

    <!-- Thư viện CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        
        /* Cài đặt cơ bản */
        html, body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: #111827; /* gray-900 */
            overflow: hidden;
            height: 100%;
        }

        /* Trình bao bọc bản đồ */
        #iot-map-wrapper {
            position: relative;
            width: 100%;
            height: 100vh;
        }
        #map {
            height: 100%;
            width: 100%;
            background-color: #111827;
        }

        /* Lớp phủ tải và spinner */
        .loader-overlay {
            position: absolute;
            inset: 0;
            background-color: rgba(17, 24, 39, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 2000;
            transition: opacity 0.3s ease;
            pointer-events: none;
            opacity: 0;
        }
        .loader-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }
        .loader {
            border: 4px solid #374151; /* gray-700 */
            border-top: 4px solid #3b82f6; /* blue-500 */
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Tiêu đề trang */
        .page-title {
            position: fixed;
            top: 1rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1001;
            background: rgba(31, 41, 55, 0.8);
            backdrop-filter: blur(8px);
            padding: 0.5rem 1.5rem;
            border-radius: 9999px; /* Fully rounded */
            border: 1px solid #374151;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* Tùy chỉnh Popup của Leaflet */
        .leaflet-popup-content-wrapper {
            background-color: #1f2937; /* gray-800 */
            color: #e5e7eb; /* gray-200 */
            border-radius: 12px;
            border: 1px solid #4b5563;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            padding: 0; /* Reset padding mặc định */
            overflow: hidden;
        }
        .leaflet-popup-content {
            margin: 0 !important;
            width: 340px !important;
        }
        .leaflet-popup-tip {
            background: #1f2937;
            border: 1px solid #4b5563;
        }
        a.leaflet-popup-close-button {
            color: #9ca3af !important; /* gray-400 */
            font-size: 20px !important;
            top: 8px !important;
            right: 8px !important;
        }
        a.leaflet-popup-close-button:hover {
            color: #e5e7eb !important; /* gray-200 */
        }
        
        /* Nội dung Popup */
        .popup-header {
            padding: 16px;
            background-color: #111827; /* Darker header */
            border-bottom: 1px solid #374151;
        }
        .popup-title {
            font-weight: 700;
            font-size: 1.125rem;
            color: white;
            margin-bottom: 4px;
        }
        .popup-subtitle {
             font-size: 0.875rem;
             color: #9ca3af;
        }

        .popup-body {
            padding: 16px;
        }
        .data-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            border-bottom: 1px solid #374151;
            font-size: 0.875rem;
        }
        .data-row:last-child {
            border-bottom: none;
        }
        .data-label {
            color: #9ca3af; /* gray-400 */
            font-weight: 500;
        }
        .data-value {
            color: #e5e7eb; /* gray-200 */
            font-weight: 600;
            text-align: right;
        }

        /* Vùng chứa biểu đồ trong Popup */
        .chart-wrapper {
            padding: 16px;
            background-color: rgba(17, 24, 39, 0.5); /* Semi-transparent dark background for chart */
            border-top: 1px solid #374151;
        }
        .chart-container {
            position: relative;
            height: 200px;
            width: 100%;
        }
        .chart-message-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background: rgba(31, 41, 55, 0.8);
            z-index: 10;
            border-radius: 8px;
        }

        /* Biểu tượng Marker tùy chỉnh */
        .custom-marker-icon {
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            border: 3px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .custom-marker-icon i {
            color: white;
            font-size: 16px;
        }
        .custom-marker-icon:hover {
            transform: scale(1.15) translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.4);
        }
        .prv-marker { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .qtcln-marker { background: linear-gradient(135deg, #10b981, #059669); }
        
    </style>

    <!-- Thư viện JavaScript -->
    <!-- Load Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <!-- Load Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div id="iot-map-wrapper">
        <div class="page-title">
            <i class="fa-solid fa-map-location-dot mr-2"></i> Bản đồ thiết bị IoT
        </div>
        
        <div id="loader-overlay" class="loader-overlay active">
            <div class="flex flex-col items-center">
                <div class="loader mb-4"></div>
                <p class="text-gray-300 font-medium">Đang tải dữ liệu...</p>
            </div>
        </div>
        
        <div id="map"></div>
        
        <!-- Error Toast -->
        <div id="error-container" class="hidden fixed bottom-5 left-5 right-5 md:left-auto md:right-5 md:max-w-md bg-red-900/90 border-l-4 border-red-500 text-red-100 p-4 rounded shadow-lg z-[9999] backdrop-blur-sm flex items-start transition-all duration-300 ease-in-out transform translate-y-10 opacity-0">
            <div class="flex-shrink-0 mr-3">
                <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
            </div>
            <div class="flex-grow">
                <h3 class="font-bold mb-1">Đã xảy ra lỗi</h3>
                <p id="error-message" class="text-sm opacity-90"></p>
            </div>
            <button onclick="App.utils.hideError()" class="ml-4 text-red-300 hover:text-white transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <script>
        const App = {
            // Cấu hình ứng dụng
            config: {
                apiBaseUrl: 'https://iot-apis.saigonvalve.vn/v1',
                map: {
                    initialView: [16.0471, 108.2068], // Mặc định: Đà Nẵng (trung tâm VN để dễ nhìn nếu chưa load đc vị trí)
                    initialZoom: 6,
                },
                credentials: {
                    emailOrUsername: "demo2",
                    password: "Demo@@22"
                }
            },

            // Trạng thái global
            state: {
                map: null,
                deviceLayerGroup: null,
                chartInstance: null,
                accessToken: null,
            },

            // UI Elements
            ui: {
                loaderOverlay: null,
                errorContainer: null,
                errorMessage: null,
            },

            /**
             * Khởi tạo ứng dụng
             */
            async init() {
                console.log("App initializing...");
                this.ui.loaderOverlay = document.getElementById('loader-overlay');
                this.ui.errorContainer = document.getElementById('error-container');
                this.ui.errorMessage = document.getElementById('error-message');

                this.map.init();

                try {
                    // 1. Đăng nhập để lấy Token
                    console.log("Đang đăng nhập...");
                    const loginData = await this.api.login();
                    this.state.accessToken = loginData.accessToken;
                    console.log("Đăng nhập thành công.");

                    // 3. Lấy danh sách thiết bị
                    // Không cần roleId nữa
                    console.log(`Đang lấy danh sách thiết bị...`);
                    const devices = await this.api.getDevices();
                    console.log(`Đã tìm thấy ${devices.length} thiết bị.`);

                    if (!devices || devices.length === 0) {
                        this.utils.showError("Không tìm thấy thiết bị nào cho tài khoản này.", false);
                    } else {
                        // 4. Hiển thị lên bản đồ
                        this.map.addDevicesToMap(devices);
                    }

                } catch (error) {
                    console.error("Lỗi khởi tạo nghiêm trọng:", error);
                    this.utils.showError(`Không thể tải dữ liệu: ${error.message}. Vui lòng kiểm tra kết nối mạng hoặc liên hệ admin.`, false);
                } finally {
                    this.utils.hideLoader();
                }
            },
            
            // --- Tầng API ---
            api: {
                async login() {
                    try {
                        const response = await fetch(`${App.config.apiBaseUrl}/auth/login`, {
                            method: "POST",
                            headers: { "Content-Type": "application/json" },
                            body: JSON.stringify(App.config.credentials)
                        });
                        if (!response.ok) {
                            if (response.status === 401) throw new Error("Sai tên đăng nhập hoặc mật khẩu.");
                            throw new Error(`Lỗi máy chủ (${response.status})`);
                        }
                        return await response.json();
                    } catch (e) {
                         throw new Error(`Lỗi kết nối đăng nhập: ${e.message}`);
                    }
                },

                async getDevices() {
                    // CẬP NHẬT: Bỏ roleId khỏi query string
                    const url = `${App.config.apiBaseUrl}/device`;
                    console.log(`Calling API: ${url}`);
                    
                    const response = await fetch(url, {
                        headers: { Authorization: `Bearer ${App.state.accessToken}` }
                    });
                    if (!response.ok) throw new Error(`Không thể lấy danh sách thiết bị (${response.status})`);
                    const data = await response.json();
                    return Array.isArray(data.data) ? data.data : [];
                },
                
                async getDeviceData(device) {
                    // CẬP NHẬT: Đổi endpoint 'vga-data' thành 'prv-data' vì khả năng cao endpoint cũ sai tên.
                    const endpoint = (device.dataType === 'PRV') ? 'prv-data' : 'carbonate-hardness-data';

                    // CẬP NHẬT QUAN TRỌNG: Thử lấy ID từ nhiều trường khác nhau để chắc chắn
                    const actualDeviceId = device.id || device.deviceId || device.device_id;

                    if (!actualDeviceId) {
                         console.error("[DEBUG] Không tìm thấy ID thiết bị trong đối tượng:", device);
                         throw new Error("Thiết bị không có ID hợp lệ.");
                    }
                    
                    const url = `${App.config.apiBaseUrl}/${endpoint}?deviceId=${actualDeviceId}&sort=DESC&perPage=20&page=1`;
                    console.log(`[DEBUG] Đang gọi API lấy dữ liệu biểu đồ: ${url}`);
                    
                    const response = await fetch(url, { 
                        headers: { Authorization: `Bearer ${App.state.accessToken}` } 
                    });
                    
                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error(`[DEBUG] API lỗi ${response.status}: ${errorText}`);
                        throw new Error(`Không thể tải dữ liệu lịch sử (Mã lỗi: ${response.status})`);
                    }
                    const result = await response.json();
                    // Đảo ngược mảng để hiển thị trên biểu đồ từ cũ -> mới (trái -> phải)
                    return (Array.isArray(result.data) ? result.data : []).reverse();
                }
            },

            // --- Tầng Bản Đồ ---
            map: {
                init() {
                    // Khởi tạo map
                    App.state.map = L.map('map', {
                        zoomControl: false // Sẽ add lại ở vị trí khác nếu muốn
                    }).setView(App.config.map.initialView, App.config.map.initialZoom);

                    // Thêm nút zoom ở góc phải dưới cho dễ thao tác một tay trên mobile
                    L.control.zoom({ position: 'bottomright' }).addTo(App.state.map);

                    // Các lớp bản đồ nền
                    const dark = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', { 
                        attribution: '&copy; OpenStreetMap, &copy; CartoDB',
                        maxZoom: 20
                    }).addTo(App.state.map); // Mặc định chọn Dark theme cho ngầu

                    const googleHybrid = L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}',{
                        maxZoom: 20,
                        subdomains:['mt0','mt1','mt2','mt3'],
                        attribution: 'Google Maps'
                    });

                    const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap'
                    });

                    // Layer Group cho markers
                    App.state.deviceLayerGroup = L.markerClusterGroup ? L.markerClusterGroup() : L.featureGroup();
                    App.state.map.addLayer(App.state.deviceLayerGroup);

                    // Điều khiển lớp bản đồ
                    const baseMaps = { "Bản đồ tối": dark, "Vệ tinh Hybrid": googleHybrid, "Bản đồ sáng": osm };
                    L.control.layers(baseMaps, null, { position: 'topright' }).addTo(App.state.map);

                    // QUAN TRỌNG: Sự kiện đóng popup để dọn dẹp biểu đồ
                    App.state.map.on('popupclose', function(e) {
                        if (App.state.chartInstance) {
                            // console.log("Dọn dẹp biểu đồ cũ...");
                            App.state.chartInstance.destroy();
                            App.state.chartInstance = null;
                        }
                    });
                },

                addDevicesToMap(devices) {
                    let validDeviceCount = 0;
                    devices.forEach(device => {
                        // Fallback linh hoạt cho cấu trúc dữ liệu latitude/longitude
                        const latStr = device.device?.latitude || device.latitude;
                        const lngStr = device.device?.longitude || device.longitude;
                        const lat = parseFloat(latStr);
                        const lng = parseFloat(lngStr);

                        // Bỏ qua nếu tọa độ không hợp lệ
                        if (isNaN(lat) || isNaN(lng) || lat === 0 || lng === 0) {
                            // CẬP NHẬT: Thử lấy ID để log cho chính xác
                            const logId = device.id || device.deviceId || 'unknown';
                            console.warn(`Thiết bị ID ${logId} có tọa độ không hợp lệ: [${latStr}, ${lngStr}]`);
                            return;
                        }
                        
                        validDeviceCount++;

                        // Xác định loại thiết bị (Normalized)
                        const deviceName = (device.device?.name || device.name || '').toLowerCase();
                        const isPrv = deviceName.includes('prv') || (device.dataType && device.dataType.toUpperCase() === 'PRV');
                        device.dataType = isPrv ? 'PRV' : 'QTCLN'; // Gán chuẩn loại dữ liệu để dùng sau này

                        // Tạo icon tùy chỉnh
                        const icon = L.divIcon({
                            html: `<i class="fa-solid ${isPrv ? 'fa-faucet-drip' : 'fa-flask'}"></i>`,
                            className: `custom-marker-icon ${isPrv ? 'prv-marker' : 'qtcln-marker'}`,
                            iconSize: [40, 40], // Kích thước lớn hơn chút cho dễ bấm trên mobile
                            iconAnchor: [20, 20],
                            popupAnchor: [0, -25]
                        });

                        const marker = L.marker([lat, lng], { icon: icon });
                        
                        // Bind popup và gắn sự kiện mở
                        marker.bindPopup(() => App.popup.createSkeleton(device), { 
                            minWidth: 340,
                            maxWidth: 340,
                            closeButton: true,
                            autoPanPadding: [20, 20]
                        });
                        marker.on('popupopen', (e) => App.popup.onOpen(e, device));
                        
                        App.state.deviceLayerGroup.addLayer(marker);
                    });

                    // Zoom bản đồ để thấy tất cả thiết bị
                    if (validDeviceCount > 0) {
                         const bounds = App.state.deviceLayerGroup.getBounds();
                         if (bounds.isValid()) {
                             App.state.map.fitBounds(bounds, { padding: [50, 50], maxZoom: 15 });
                         }
                    } else {
                        App.utils.showError("Không có thiết bị nào có tọa độ hợp lệ để hiển thị.");
                    }
                }
            },
            
            // --- Tầng Popup & Hiển thị chi tiết ---
            popup: {
                // Tạo khung HTML tĩnh trước khi có dữ liệu
                createSkeleton(device) {
                    const deviceName = device.device?.name || device.name || 'Thiết bị không tên';
                    // CẬP NHẬT: Lấy ID hiển thị cho đúng
                    const deviceId = device.id || device.deviceId || 'N/A';
                    const typeLabel = device.dataType === 'PRV' ? 'Van giảm áp (PRV)' : 'Quan trắc chất lượng nước';
                    
                    // Dùng ID làm định danh duy nhất cho các element trong popup
                    const uniqueId = deviceId.toString().replace(/[^a-zA-Z0-9]/g, '');

                    return `
                        <div class="popup-header">
                            <div class="popup-title">${deviceName}</div>
                            <div class="popup-subtitle">${typeLabel} <span class="opacity-50 mx-1">|</span> ID: ${deviceId}</div>
                        </div>
                        
                        <div id="popup-details-${uniqueId}" class="popup-body">
                            <!-- Placeholder khi đang tải -->
                            <div class="flex items-center justify-center py-4 space-x-2 text-blue-400">
                                <div class="w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin"></div>
                                <span class="text-sm">Đang tải dữ liệu mới nhất...</span>
                            </div>
                        </div>

                        <div class="chart-wrapper">
                            <div class="chart-container">
                                <div class="chart-message-overlay">
                                    <div class="loader" style="width: 30px; height: 30px; border-width: 3px;"></div>
                                </div>
                                <canvas id="chart-${uniqueId}"></canvas>
                            </div>
                        </div>
                    `;
                },

                // Khi popup mở ra thì mới gọi API lấy dữ liệu chi tiết
                onOpen(e, device) {
                    const popupNode = e.popup.getElement();
                    const deviceId = device.id || device.deviceId || 'N/A';
                    const uniqueId = deviceId.toString().replace(/[^a-zA-Z0-9]/g, '');

                    // Tìm canvas trong popup vừa mở
                    const canvas = popupNode.querySelector(`#chart-${uniqueId}`);
                    const messageOverlay = popupNode.querySelector('.chart-message-overlay');
                    const detailsContainer = popupNode.querySelector(`#popup-details-${uniqueId}`);

                    if (!canvas || !detailsContainer) return;

                    // Gọi hàm render chính
                    App.chart.fetchAndRender(device, canvas, messageOverlay, detailsContainer);
                },

                // Cập nhật phần text chi tiết sau khi có data
                updateDetailsBox(container, device, latestData) {
                    if (!latestData) {
                        container.innerHTML = '<div class="text-center text-gray-500 py-2">Không có dữ liệu cảm biến.</div>';
                        return;
                    }

                    const ts = new Date(+latestData.ts);
                    const timeStr = ts.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
                    const dateStr = ts.toLocaleDateString('vi-VN');

                    // Sử dụng helper để format số an toàn
                    const fmt = App.utils.safeToFixed;
                    const fmtLocale = App.utils.safeLocaleString;

                    let html = '';
                    if (device.dataType === 'PRV') {
                         html += `
                            <div class="data-row"><span class="data-label">Áp lực trước</span><span class="data-value text-blue-400">${fmt(latestData.pressureBeforeValve, 2)} bar</span></div>
                            <div class="data-row"><span class="data-label">Áp lực sau</span><span class="data-value text-red-400">${fmt(latestData.pressureAfterValve, 2)} bar</span></div>
                            <div class="data-row"><span class="data-label">Lưu lượng</span><span class="data-value text-orange-400">${fmt(latestData.waterflow, 2)} m³/h</span></div>
                            <div class="data-row"><span class="data-label">Tổng lưu lượng</span><span class="data-value">${fmtLocale(latestData.Q_TONG)} m³</span></div>
                         `;
                    } else {
                        // QTCLN
                         html += `
                            <div class="grid grid-cols-2 gap-x-4">
                                <div class="data-row"><span class="data-label">pH</span><span class="data-value text-green-400">${fmt(latestData.ph, 1)}</span></div>
                                <div class="data-row"><span class="data-label">COD</span><span class="data-value text-pink-400">${fmt(latestData.chemicalOxygenDemand, 1)}</span></div>
                                <div class="data-row"><span class="data-label">Amoni</span><span class="data-value text-yellow-400">${fmt(latestData.amoni, 2)}</span></div>
                                <div class="data-row"><span class="data-label">TSS</span><span class="data-value text-purple-400">${fmt(latestData.totalSuspendedSolids, 1)}</span></div>
                                <div class="data-row col-span-2"><span class="data-label">Oxy hòa tan (DO)</span><span class="data-value text-indigo-400">${fmt(latestData.dissolvedOxygen, 2)} mg/L</span></div>
                            </div>
                         `;
                    }

                    html += `<div class="mt-3 pt-2 border-t border-gray-700 text-xs text-gray-500 flex justify-between">
                        <span>Cập nhật cuối:</span>
                        <span>${timeStr} - ${dateStr}</span>
                    </div>`;

                    container.innerHTML = html;
                }
            },
            
            // --- Tầng Biểu đồ ---
            chart: {
                async fetchAndRender(device, canvasEl, overlayEl, detailsEl) {
                    try {
                        // CẬP NHẬT: Log đối tượng thiết bị để debug
                        console.log("[DEBUG] Đang vẽ biểu đồ cho thiết bị:", device);
                        const data = await App.api.getDeviceData(device);

                        if (!data || data.length === 0) {
                             overlayEl.innerHTML = '<span class="text-gray-400 text-sm">Chưa có dữ liệu lịch sử</span>';
                             App.popup.updateDetailsBox(detailsEl, device, null);
                             return;
                        }

                        // Ẩn overlay loading
                        overlayEl.style.display = 'none';

                        // Cập nhật thông tin mới nhất vào bảng chi tiết
                        const latestData = data[data.length - 1];
                        App.popup.updateDetailsBox(detailsEl, device, latestData);

                        // Chuẩn bị dữ liệu vẽ biểu đồ
                        // Chỉ lấy tối đa 20 điểm dữ liệu gần nhất để biểu đồ thoáng
                        const chartData = data.slice(-20); 
                        const labels = chartData.map(d => new Date(+d.ts).toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'}));
                        
                        let datasets = [];
                        if (device.dataType === 'PRV') {
                            datasets = [
                                { label: 'Trước (bar)', data: chartData.map(d => d.pressureBeforeValve), borderColor: '#60a5fa', backgroundColor: 'rgba(96, 165, 250, 0.1)', tension: 0.3, borderWidth: 2, pointRadius: 0, pointHitRadius: 10 },
                                { label: 'Sau (bar)', data: chartData.map(d => d.pressureAfterValve), borderColor: '#f87171', backgroundColor: 'rgba(248, 113, 113, 0.1)', tension: 0.3, borderWidth: 2, pointRadius: 0, pointHitRadius: 10 },
                                // Flow thường có đơn vị khác nên có thể cần trục Y thứ 2, nhưng tạm thời vẽ chung
                                { label: 'Lưu lượng', data: chartData.map(d => d.waterflow), borderColor: '#fb923c', borderDash: [5, 5], tension: 0.3, borderWidth: 1.5, pointRadius: 0 }
                            ];
                        } else {
                            datasets = [
                                { label: 'pH', data: chartData.map(d => d.ph), borderColor: '#34d399', tension: 0.3, borderWidth: 2, pointRadius: 0 },
                                { label: 'COD', data: chartData.map(d => d.chemicalOxygenDemand), borderColor: '#f472b6', tension: 0.3, borderWidth: 2, pointRadius: 0, hidden: true }, // Ẩn bớt cho đỡ rối
                                { label: 'DO', data: chartData.map(d => d.dissolvedOxygen), borderColor: '#818cf8', tension: 0.3, borderWidth: 2, pointRadius: 0 }
                            ];
                        }

                        // Đảm bảo hủy biểu đồ cũ nếu có (dù đã xử lý ở popupclose nhưng cẩn thận vẫn hơn)
                        if (App.state.chartInstance) {
                            App.state.chartInstance.destroy();
                        }

                        // Vẽ biểu đồ mới
                        App.state.chartInstance = new Chart(canvasEl, {
                            type: 'line',
                            data: { labels, datasets },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                interaction: {
                                    mode: 'index',
                                    intersect: false,
                                },
                                scales: {
                                    x: {
                                        ticks: { color: '#6b7280', maxRotation: 0, font: {size: 10} },
                                        grid: { color: 'rgba(107, 114, 128, 0.1)' }
                                    },
                                    y: {
                                        beginAtZero: true,
                                        ticks: { color: '#6b7280', font: {size: 10} },
                                        grid: { color: 'rgba(107, 114, 128, 0.1)' }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        position: 'top',
                                        align: 'end',
                                        labels: { color: '#9ca3af', boxWidth: 12, padding: 10, font: {size: 11} }
                                    },
                                    tooltip: {
                                        mode: 'index',
                                        intersect: false,
                                        backgroundColor: 'rgba(17, 24, 39, 0.95)',
                                        titleColor: '#e5e7eb',
                                        bodyColor: '#e5e7eb',
                                        borderColor: '#374151',
                                        borderWidth: 1
                                    }
                                }
                            }
                        });

                    } catch (error) {
                        console.error("Chart render error:", error);
                        overlayEl.innerHTML = `<span class="text-red-400 text-sm px-4 text-center">Lỗi tải dữ liệu: ${error.message}</span>`;
                    }
                }
            },
            
            // --- Các tiện ích ---
            utils: {
                // Hàm format số an toàn (tránh lỗi khi value là string)
                safeToFixed(value, decimals) {
                    const num = Number(value);
                    return isNaN(num) ? '--' : num.toFixed(decimals);
                },
                // Hàm format số theo locale Việt Nam an toàn
                safeLocaleString(value) {
                    const num = Number(value);
                    return isNaN(num) ? '--' : num.toLocaleString('vi-VN');
                },

                showLoader() { 
                    App.ui.loaderOverlay.classList.remove('hidden');
                    // Trigger reflow để transition hoạt động
                    void App.ui.loaderOverlay.offsetWidth; 
                    App.ui.loaderOverlay.classList.add('active');
                },
                hideLoader() { 
                    App.ui.loaderOverlay.classList.remove('active');
                    setTimeout(() => App.ui.loaderOverlay.classList.add('hidden'), 300); 
                },
                showError(message, autoHide = true) {
                    App.ui.errorMessage.textContent = message;
                    App.ui.errorContainer.classList.remove('hidden', 'translate-y-10', 'opacity-0');
                    
                    if (this.errorTimeout) clearTimeout(this.errorTimeout);
                    if (autoHide) {
                       this.errorTimeout = setTimeout(() => this.hideError(), 6000);
                    }
                },
                hideError() { 
                    App.ui.errorContainer.classList.add('translate-y-10', 'opacity-0');
                    setTimeout(() => App.ui.errorContainer.classList.add('hidden'), 300);
                }
            }
        };

        // Khởi chạy khi DOM sẵn sàng
        document.addEventListener('DOMContentLoaded', () => App.init());
    </script>
</body>
</html>