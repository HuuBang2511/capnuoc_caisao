<?php
/**
 * iot_api.php v5.2
 * Topology:
 *   192.168.31.6  = SCADA server (Python chay o day)
 *   192.168.31.11 = WebGIS server (file nay nam o day)
 *   192.168.31.10 = DB WebGIS (PostgreSQL)
 *   192.168.31.4  = DB SCADA (SQL Server)
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Authorization, Content-Type');

$API_KEY         = 'SCADA_HOCAU_2024_SECRET_KEY';
$RUNTIME_FILE    = dirname(__DIR__) . '/runtime/iot_realtime.json';
$PYTHON_HIST_URL = 'http://192.168.31.6:5001/history';
$PYTHON_BASE_URL = 'http://192.168.31.6:5001';

// ── Xac thuc ────────────────────────────────────────────────────
function checkAuth($key) {
    $h    = function_exists('getallheaders') ? getallheaders() : [];
    $auth = isset($h['Authorization']) ? $h['Authorization'] : '';
    if (!$auth && isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $auth = $_SERVER['HTTP_AUTHORIZATION'];
    }
    if ($auth === 'Bearer ' . $key) return true;
    if (isset($_GET['key']) && $_GET['key'] === $key) return true;
    return false;
}

if (!checkAuth($API_KEY)) {
    http_response_code(403);
    echo json_encode(array('status' => 'error', 'message' => 'Sai API Key'));
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

// ================================================================
// action=update — SCADA gateway POST du lieu len
// ================================================================
if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data  = json_decode($input, true);

    if (!$data || empty($data['ma_tram'])) {
        http_response_code(400);
        echo json_encode(array('status' => 'error', 'message' => 'Thieu ma_tram'));
        exit;
    }

    $dir = dirname($RUNTIME_FILE);
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $current = array();
    if (file_exists($RUNTIME_FILE)) {
        $raw     = file_get_contents($RUNTIME_FILE);
        $current = json_decode($raw, true);
        if (!$current) $current = array();
    }

    $data['last_update']       = date('Y-m-d H:i:s');
    $current[$data['ma_tram']] = $data;
    file_put_contents($RUNTIME_FILE, json_encode($current, JSON_UNESCAPED_UNICODE));

    echo json_encode(array('status' => 'success'));
    exit;
}

// ================================================================
// action=get — Tra toan bo du lieu realtime
// ================================================================
if ($action === 'get') {
    echo file_exists($RUNTIME_FILE)
        ? file_get_contents($RUNTIME_FILE)
        : '{}';
    exit;
}

// ================================================================
// action=history — Proxy sang Python history server
// ================================================================
if ($action === 'history') {
    $channel_id = trim(isset($_GET['channel_id']) ? $_GET['channel_id'] : '');
    $range      = isset($_GET['range']) ? $_GET['range'] : '24h';

    if (!$channel_id) {
        http_response_code(400);
        echo json_encode(array('status' => 'error', 'message' => 'Thieu channel_id'));
        exit;
    }

    $url = $PYTHON_HIST_URL
         . '?channel_id=' . urlencode($channel_id)
         . '&range='      . urlencode($range)
         . '&key='        . urlencode($API_KEY);

    $ctx      = stream_context_create(array('http' => array('method' => 'GET', 'timeout' => 10, 'ignore_errors' => true)));
    $response = @file_get_contents($url, false, $ctx);

    echo $response !== false ? $response
        : json_encode(array(
            'labels' => array(), 'values' => array(), 'count' => 0,
            'error'  => 'Khong ket noi duoc history server (192.168.31.6:5001).',
          ), JSON_UNESCAPED_UNICODE);
    exit;
}

// ================================================================
// action=sites — Danh sach tram + kenh
// ================================================================
if ($action === 'sites') {
    $data = array();
    if (file_exists($RUNTIME_FILE)) {
        $raw  = file_get_contents($RUNTIME_FILE);
        $decoded = json_decode($raw, true);
        if ($decoded) $data = $decoded;
    }

    $result = array();
    foreach ($data as $sid => $d) {
        $channels_out = array();
        $channels_raw = isset($d['channels']) ? $d['channels'] : array();
        foreach ($channels_raw as $ck => $c) {
            $channels_out[$ck] = array(
                'label'      => isset($c['label'])      ? $c['label']      : '',
                'unit'       => isset($c['unit'])       ? $c['unit']       : '',
                'group'      => isset($c['group'])      ? $c['group']      : '',
                'channel_id' => isset($c['channel_id']) ? $c['channel_id'] : '',
            );
        }
        $result[$sid] = array(
            'ten_tram' => isset($d['ten_tram']) ? $d['ten_tram'] : $sid,
            'lat'      => isset($d['lat'])      ? $d['lat']      : 0,
            'lng'      => isset($d['lng'])      ? $d['lng']      : 0,
            'loai'     => isset($d['loai'])     ? $d['loai']     : 'dong_ho',
            'channels' => $channels_out,
        );
    }
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

// ================================================================
// action=sanluong — Proxy sang Python
// ================================================================
if ($action === 'sanluong') {
    $loai    = isset($_GET['loai'])    ? $_GET['loai']    : 'ngay';
    $so_ngay = isset($_GET['so_ngay']) ? (int)$_GET['so_ngay'] : 35;
    $so_ngay = max(35, min($so_ngay, 90));
    $url  = $PYTHON_BASE_URL . '/sanluong'
          . '?loai='    . urlencode($loai)
          . '&so_ngay=' . $so_ngay
          . '&key='     . urlencode($API_KEY);

    $ctx      = stream_context_create(array('http' => array('method' => 'GET', 'timeout' => 8, 'ignore_errors' => true)));
    $response = @file_get_contents($url, false, $ctx);

    echo $response !== false ? $response
        : json_encode(array('status' => 'error', 'message' => 'History server chua chay'));
    exit;
}

// ================================================================
// action=stations — Proxy sang Python: realtime 3 tram chinh
// Tra ve: TB Nuoc Tho (60100), Nha May (60000), TB TA NT5 (NT5)
// ================================================================
if ($action === 'stations') {
    $url = $PYTHON_BASE_URL . '/stations?key=' . urlencode($API_KEY);

    $ctx      = stream_context_create(array('http' => array('method' => 'GET', 'timeout' => 8, 'ignore_errors' => true)));
    $response = @file_get_contents($url, false, $ctx);

    if ($response !== false && $response !== '') {
        echo $response;
    } else {
        echo json_encode(array(
            'nha_may'          => array(),
            'tram_bo_nuoc_tho' => array(),
            'nt5'              => array(),
            'error'            => 'Gateway timeout hoac chua update route /stations',
        ), JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// ================================================================
// action=channel_value — Proxy sang Python: lay LastValue cua channel bat ky
// Params: channels=id1,id2,... (ngan cach bang dau phay)
// Response: { "id1": {value, unit, name, ts}, ... }
// ================================================================
if ($action === 'channel_value') {
    $channels_str = trim(isset($_GET['channels']) ? $_GET['channels'] : '');

    if (empty($channels_str)) {
        http_response_code(400);
        echo json_encode(array('status' => 'error', 'message' => 'Thieu tham so channels'));
        exit;
    }

    $rawIds     = explode(',', $channels_str);
    $channelIds = array();
    foreach ($rawIds as $id) {
        $id = trim($id);
        if (preg_match('/^[a-zA-Z0-9_]+$/', $id) && $id !== '') {
            $channelIds[] = $id;
        }
    }
    if (empty($channelIds)) {
        http_response_code(400);
        echo json_encode(array('status' => 'error', 'message' => 'Channel IDs khong hop le'));
        exit;
    }

    $url = $PYTHON_BASE_URL . '/channel_value'
         . '?channels=' . urlencode(implode(',', $channelIds))
         . '&key='      . urlencode($API_KEY);

    $ctx      = stream_context_create(array('http' => array('method' => 'GET', 'timeout' => 8, 'ignore_errors' => true)));
    $response = @file_get_contents($url, false, $ctx);

    if ($response !== false && $response !== '') {
        echo $response;
    } else {
        echo json_encode(array('error' => 'Gateway timeout'), JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// ================================================================
// action=sanluong_dong_ho — Proxy sang Python
// ================================================================
if ($action === 'sanluong_dong_ho') {
    $channels_str = trim(isset($_GET['channels']) ? $_GET['channels'] : '');
    $tu_ngay      = trim(isset($_GET['tu_ngay'])  ? $_GET['tu_ngay']  : '');
    $den_ngay     = trim(isset($_GET['den_ngay']) ? $_GET['den_ngay'] : '');

    if (empty($channels_str)) {
        http_response_code(400);
        echo json_encode(array('success' => false, 'msg' => 'Thieu tham so channels'));
        exit;
    }

    $rawIds     = explode(',', $channels_str);
    $channelIds = array();
    foreach ($rawIds as $id) {
        $id = trim($id);
        if (preg_match('/^[a-zA-Z0-9_]+$/', $id) && $id !== '') {
            $channelIds[] = $id;
        }
    }
    if (empty($channelIds)) {
        http_response_code(400);
        echo json_encode(array('success' => false, 'msg' => 'Channel IDs khong hop le'));
        exit;
    }

    $tu  = $tu_ngay  ? strtotime($tu_ngay)  : 0;
    $den = $den_ngay ? strtotime($den_ngay) : 0;
    if (!$tu || !$den) {
        http_response_code(400);
        echo json_encode(array('success' => false, 'msg' => 'Ngay khong hop le (dinh dang Y-m-d)'));
        exit;
    }
    if ($tu > $den) {
        http_response_code(400);
        echo json_encode(array('success' => false, 'msg' => 'tu_ngay phai nho hon hoac bang den_ngay'));
        exit;
    }
    if ($den - $tu > 31 * 86400) {
        http_response_code(400);
        echo json_encode(array('success' => false, 'msg' => 'Khoang thoi gian toi da 31 ngay'));
        exit;
    }

    $url = $PYTHON_BASE_URL . '/sanluong_dong_ho'
         . '?channels=' . urlencode(implode(',', $channelIds))
         . '&tu_ngay='  . urlencode(date('Y-m-d', $tu))
         . '&den_ngay=' . urlencode(date('Y-m-d', $den))
         . '&key='      . urlencode($API_KEY);

    $ctx      = stream_context_create(array('http' => array('method' => 'GET', 'timeout' => 15, 'ignore_errors' => true)));
    $response = @file_get_contents($url, false, $ctx);

    if ($response === false || $response === null) {
        echo json_encode(fetchSanLuongDirect($channelIds, date('Y-m-d', $tu), date('Y-m-d', $den)),
                         JSON_UNESCAPED_UNICODE);
    } else {
        echo $response;
    }
    exit;
}

// ================================================================
// Fallback SQL Server truc tiep cho sanluong_dong_ho
// ================================================================
function fetchSanLuongDirect(array $channelIds, $tu_ngay, $den_ngay)
{
    try {
        $drivers = PDO::getAvailableDrivers();
        if (in_array('sqlsrv', $drivers)) {
            $dsn = 'sqlsrv:Server=192.168.31.4;Database=viwater';
        } elseif (in_array('odbc', $drivers)) {
            $dsn = 'odbc:Driver={ODBC Driver 17 for SQL Server};Server=192.168.31.4;Database=viwater';
        } else {
            return array('success' => false, 'msg' => 'PHP khong co PDO SQL Server driver.');
        }

        $conn = new PDO($dsn, 'viwater', '123456',
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 10));

        $result = array();
        foreach ($channelIds as $cid) {
            $result[$cid] = array();
            $tableName    = 't_Data_Logger_' . $cid;

            $stmtChk = $conn->prepare(
                "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES
                 WHERE TABLE_NAME = ? AND TABLE_TYPE = 'BASE TABLE'"
            );
            $stmtChk->execute(array($tableName));
            if ((int)$stmtChk->fetchColumn() === 0) continue;

            $sql = "
                SELECT
                    CONVERT(VARCHAR(10), TimeStamp, 120) AS ngay,
                    MAX(CAST(Value AS FLOAT)) - MIN(CAST(Value AS FLOAT)) AS san_luong
                FROM [{$tableName}]
                WHERE TimeStamp >= :tu
                  AND TimeStamp <= :den
                  AND TimeStamp < '2030-01-01'
                  AND Value IS NOT NULL
                GROUP BY CONVERT(VARCHAR(10), TimeStamp, 120)
                ORDER BY ngay
            ";

            $stmt = $conn->prepare($sql);
            $stmt->execute(array(':tu' => $tu_ngay . ' 00:00:00', ':den' => $den_ngay . ' 23:59:59'));

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $sl = $row['san_luong'];
                if ($sl !== null && (float)$sl >= 0) {
                    $result[$cid][$row['ngay']] = round((float)$sl);
                }
            }
        }

        return array('success' => true, 'data' => $result, 'source' => 'direct_sql');

    } catch (Exception $e) {
        return array('success' => false, 'msg' => 'Loi ket noi SQL Server: ' . $e->getMessage());
    }
}

// ================================================================
// action=rt_custom — GET/POST cau hinh custom channels Realtime
// Proxy sang DashboardController::actionApiRtCustom
// ================================================================
if ($action === 'rt_custom') {
    $method = $_SERVER['REQUEST_METHOD'];
    $webgis_url = 'http://192.168.31.11/quanly/dashboard/api-rt-custom';

    if ($method === 'GET') {
        $ctx = stream_context_create(array('http' => array(
            'method'        => 'GET',
            'timeout'       => 8,
            'ignore_errors' => true,
            'header'        => 'Cookie: ' . (isset($_SERVER['HTTP_COOKIE']) ? $_SERVER['HTTP_COOKIE'] : '') . "\r\n",
        )));
        $response = @file_get_contents($webgis_url, false, $ctx);
        echo $response !== false ? $response
            : json_encode(array('tbn' => array(), 'nm' => array(), 'nt5' => array()));
    } else {
        // POST: forward body len controller
        $raw = file_get_contents('php://input');
        $ctx = stream_context_create(array('http' => array(
            'method'        => 'POST',
            'timeout'       => 8,
            'ignore_errors' => true,
            'header'        => "Content-Type: application/json\r\n"
                             . 'Cookie: ' . (isset($_SERVER['HTTP_COOKIE']) ? $_SERVER['HTTP_COOKIE'] : '') . "\r\n",
            'content'       => $raw,
        )));
        $response = @file_get_contents($webgis_url, false, $ctx);
        echo $response !== false ? $response
            : json_encode(array('success' => false, 'msg' => 'Khong ket noi duoc WebGIS server'));
    }
    exit;
}

// ================================================================
// Fallback
// ================================================================
http_response_code(404);
echo json_encode(array('status' => 'error', 'message' => 'Action khong hop le: ' . htmlspecialchars($action)));