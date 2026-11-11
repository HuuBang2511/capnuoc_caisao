<?php

namespace app\modules\quanly\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\BadRequestHttpException;
// Import Models danh mục để nạp vào bộ lọc (Giữ nguyên như cũ)
use app\modules\quanly\models\danhmuc\DmLoaiong;
use app\modules\quanly\models\danhmuc\DmHieudongho;
use app\modules\quanly\models\danhmuc\DmTinhtrang;
use app\modules\quanly\models\danhmuc\DmSucoNguyennhan;
use app\modules\quanly\models\danhmuc\DmLoaiham;
use app\modules\quanly\models\danhmuc\DmLoaimoinoi;

class MapController extends Controller
{
    public function actionHocau()
    {
        $this->layout = false;
        
        // Lấy dữ liệu danh mục cho bộ lọc (Giữ nguyên)
        $filterData = [
            'loaiong' => DmLoaiong::find()->select(['id', 'ten'])->asArray()->all(),
            'hieudongho' => DmHieudongho::find()->select(['id', 'ten'])->asArray()->all(),
            'tinhtrang' => DmTinhtrang::find()->select(['id', 'ten'])->asArray()->all(),
            'nguyennhan' => DmSucoNguyennhan::find()->select(['id', 'ten'])->asArray()->all(),
            'loaiham' => DmLoaiham::find()->select(['id', 'ten'])->asArray()->all(),
            'loaimoinoi' => DmLoaimoinoi::find()->select(['id', 'ten'])->asArray()->all(),
        ];

        return $this->render('hocau', [
            'filterData' => $filterData
        ]);
    }

    public function actionCaiSao()
    {
        $this->layout = false;
        
        // Lấy dữ liệu danh mục cho bộ lọc (Giữ nguyên)
        $filterData = [
            'loaiong' => DmLoaiong::find()->select(['id', 'ten'])->asArray()->all(),
            'hieudongho' => DmHieudongho::find()->select(['id', 'ten'])->asArray()->all(),
            'tinhtrang' => DmTinhtrang::find()->select(['id', 'ten'])->asArray()->all(),
            'nguyennhan' => DmSucoNguyennhan::find()->select(['id', 'ten'])->asArray()->all(),
            'loaiham' => DmLoaiham::find()->select(['id', 'ten'])->asArray()->all(),
            'loaimoinoi' => DmLoaimoinoi::find()->select(['id', 'ten'])->asArray()->all(),
        ];

        return $this->render('caisao', [
            'filterData' => $filterData
        ]);
    }

    // ================================================================
    // ĐỒNG HỒ NƯỚC THÔNG MINH (api.donghonuocthongminh.vn)
    // Proxy phía server: giữ tài khoản/token ở backend, trả JSON cùng định dạng lớp IoT trên map.
    // ================================================================

    /** Các kênh hiển thị: field API => [nhãn, đơn vị, nhóm] */
    private static $DHN_CHANNELS = [
        'short_l'  => ['Lưu lượng tức thời', 'm³/h', 'luu_luong'],
        'amount'   => ['Tổng lưu lượng', 'm³', 'luu_luong'],
        'p_amount' => ['Tổng lưu lượng xuôi', 'm³', 'luu_luong'],
        'r_amount' => ['Tổng lưu lượng ngược', 'm³', 'luu_luong'],
        'pressure' => ['Áp lực', 'bar', 'ap_suat'],
        'bat'      => ['Pin', 'V', 'dien'],
    ];

    /**
     * GET /quanly/map/dhn-latest
     * Trả về số liệu mới nhất của các đồng hồ khai báo trong config/donghonuoc.php
     */
    public function actionDhnLatest()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $cfg = $this->dhnConfig();

        return Yii::$app->cache->getOrSet('dhn_latest_v1', function () use ($cfg) {
            $tz = new \DateTimeZone($cfg['timezone']);
            $today = new \DateTime('now', $tz);
            $yesterday = (clone $today)->modify('-1 day');
            $out = [];

            foreach ($cfg['stations'] as $st) {
                $item = [
                    'source' => 'dhn',
                    'ma_tram' => $st['logger'],
                    'ten_tram' => $st['ten'],
                    'loai' => 'Đồng hồ lưu lượng - ĐH: ' . $st['dongho'],
                    'lat' => $st['lat'],
                    'lng' => $st['lng'],
                    'last_update' => null,
                    'channels' => [],
                ];
                try {
                    $rows = $this->dhnFetchFlow($st['customer_id'], $yesterday->format('Y-m-d'), $today->format('Y-m-d'));
                } catch (\Throwable $e) {
                    Yii::warning('DHN latest ' . $st['customer_id'] . ': ' . $e->getMessage(), __METHOD__);
                    $item['error'] = 'Không lấy được dữ liệu';
                    $out[$st['logger']] = $item;
                    continue;
                }
                if ($rows) {
                    $last = end($rows);
                    $item['last_update'] = $last['rec_date_time'];
                    foreach (self::$DHN_CHANNELS as $f => [$label, $unit, $group]) {
                        if (!isset($last[$f]) || $last[$f] === null) continue;
                        $item['channels'][$f] = [
                            'label' => $label, 'unit' => $unit, 'group' => $group,
                            'value' => $last[$f],
                            'channel_id' => 'dhn|' . $st['customer_id'] . '|' . $f,
                        ];
                    }
                    // Sản lượng từ 0h hôm nay = chỉ số cuối - chỉ số đầu tiên trong ngày
                    $todayStr = $today->format('Y-m-d');
                    foreach ($rows as $r) {
                        if (($r['cv_rec_date_time'] ?? '') === $todayStr && isset($r['amount'])) {
                            $item['channels']['san_luong_ngay'] = [
                                'label' => 'Sản lượng từ 0h', 'unit' => 'm³', 'group' => 'luu_luong',
                                'value' => round($last['amount'] - $r['amount'], 3),
                                'channel_id' => 'dhn|' . $st['customer_id'] . '|short_l',
                            ];
                            break;
                        }
                    }
                }
                $out[$st['logger']] = $item;
            }
            return $out;
        }, 300);
    }

    /**
     * GET /quanly/map/dhn-history?channel_id=dhn|<customer_uuid>|<field>&range=24h
     * Trả về {labels:[ISO time], values:[]} như iot_api history.
     */
    public function actionDhnHistory($channel_id, $range = '24h')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $cfg = $this->dhnConfig();

        $parts = explode('|', (string)$channel_id);
        if (count($parts) !== 3 || $parts[0] !== 'dhn' || !isset(self::$DHN_CHANNELS[$parts[2]])) {
            throw new BadRequestHttpException('channel_id không hợp lệ');
        }
        [, $customerId, $field] = $parts;
        if (!in_array($customerId, array_column($cfg['stations'], 'customer_id'), true)) {
            throw new BadRequestHttpException('Đồng hồ không được khai báo');
        }

        // Dữ liệu theo giờ: khung 1H quá ít điểm -> hiển thị 6H
        $hoursMap = ['1h' => 6, '6h' => 6, '24h' => 24, '7d' => 168, '30d' => 720];
        $hours = $hoursMap[$range] ?? 24;

        $tz = new \DateTimeZone($cfg['timezone']);
        $now = new \DateTime('now', $tz);
        $from = (clone $now)->modify("-{$hours} hours");

        $cacheKey = 'dhn_hist_' . md5($customerId . $from->format('Y-m-d') . $now->format('Y-m-d'));
        $rows = Yii::$app->cache->getOrSet($cacheKey, function () use ($customerId, $from, $now) {
            return $this->dhnFetchFlow($customerId, $from->format('Y-m-d'), $now->format('Y-m-d'));
        }, 300);

        $labels = $values = [];
        $fromTs = $from->getTimestamp();
        foreach ($rows as $r) {
            if (strtotime($r['rec_date_time']) < $fromTs || !isset($r[$field])) continue;
            $labels[] = $r['rec_date_time'];
            $values[] = $r[$field];
        }
        return ['labels' => $labels, 'values' => $values];
    }

    private function dhnConfig()
    {
        return require Yii::getAlias('@app/config/donghonuoc.php');
    }

    /** Lấy dữ liệu lưu lượng theo giờ (đã sắp xếp theo thời gian tăng dần) */
    private function dhnFetchFlow($customerId, $startDate, $endDate)
    {
        $res = $this->dhnRequest('/water-flows/water-flow-by-hour', [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'customer_id' => $customerId,
        ]);
        $rows = $res['data']['data'] ?? [];
        usort($rows, function ($a, $b) {
            return strcmp($a['rec_date_time'], $b['rec_date_time']);
        });
        return $rows;
    }

    /** GET có Bearer token; tự đăng nhập lại 1 lần nếu token hết hạn (401) */
    private function dhnRequest($path, array $query, $retry = true)
    {
        $cfg = $this->dhnConfig();
        $token = $this->dhnToken();
        [$code, $body] = $this->dhnHttp('GET', $cfg['base_url'] . $path . '?' . http_build_query($query), null, $token);

        if ($code === 401 && $retry) {
            Yii::$app->cache->delete('dhn_token');
            return $this->dhnRequest($path, $query, false);
        }
        if ($code !== 200) {
            throw new \RuntimeException("HTTP $code: " . substr($body, 0, 200));
        }
        return json_decode($body, true) ?: [];
    }

    private function dhnToken()
    {
        $token = Yii::$app->cache->get('dhn_token');
        if ($token) return $token;

        $cfg = $this->dhnConfig();
        [$code, $body] = $this->dhnHttp('POST', $cfg['base_url'] . '/auth/login', json_encode([
            'username' => $cfg['username'],
            'password' => $cfg['password'],
        ]));
        $data = json_decode($body, true);
        if ($code !== 200 && $code !== 201 || empty($data['access_token'])) {
            throw new \RuntimeException("Đăng nhập API đồng hồ nước thất bại (HTTP $code)");
        }
        $token = $data['access_token'];

        // Lấy hạn token từ JWT (exp), trừ hao 10 phút; mặc định 1 ngày
        $ttl = 86400;
        $payload = json_decode(base64_decode(strtr(explode('.', $token)[1] ?? '', '-_', '+/')), true);
        if (!empty($payload['exp'])) {
            $ttl = max(60, min($ttl, $payload['exp'] - time() - 600));
        }
        Yii::$app->cache->set('dhn_token', $token, $ttl);
        return $token;
    }

    private function dhnHttp($method, $url, $jsonBody = null, $token = null)
    {
        $headers = ['Accept: application/json'];
        if ($jsonBody !== null) $headers[] = 'Content-Type: application/json';
        if ($token) $headers[] = 'Authorization: Bearer ' . $token;

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30,
        ]);
        if ($jsonBody !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);
        $body = curl_exec($ch);
        if ($body === false) {
            $err = curl_error($ch);
            curl_close($ch);
            throw new \RuntimeException('cURL: ' . $err);
        }
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return [$code, $body];
    }

}
