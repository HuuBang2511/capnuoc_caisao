<?php
namespace app\modules\quanly\controllers;

use Yii;
use app\modules\quanly\base\QuanlyBaseController;
use app\modules\quanly\models\hocau\NkChatLuongGio;
use app\modules\quanly\models\hocau\NkGiaoCa;
use app\modules\quanly\models\hocau\NkNuocThaiSh;
use app\modules\quanly\models\hocau\NkDongHoKhachHang;
use app\modules\quanly\models\hocau\NkClnHangNgay;
use app\modules\quanly\models\hocau\NkPhanTichTuan;
use app\modules\quanly\components\BaoCaoNgayExcel;
use app\modules\quanly\components\SanLuongDongHoExcel;

class NhatKyController extends QuanlyBaseController
{
    /**
     * Cho phep gateway SCADA (192.168.31.6) goi actionApiSanLuong
     * ma khong can session login.
     * Override behaviors() de them guestAllowed cho action nay khi tu IP gateway.
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // Neu request tu SCADA gateway, bo qua access control cho api-san-luong
        $ip = Yii::$app->request->userIP;
        if ($ip === '192.168.31.6') {
            // Xoa tat ca access filter de gateway goi duoc khong can login
            foreach ($behaviors as $name => $behavior) {
                $class = is_array($behavior) ? ($behavior['class'] ?? '') : '';
                if (strpos($class, 'Access') !== false || strpos($class, 'Auth') !== false) {
                    unset($behaviors[$name]);
                }
            }
        }
        return $behaviors;
    }

    public function beforeAction($action)
    {
        // SCADA gateway (192.168.31.6) duoc phep goi api-san-luong khong can login
        // Skip TOAN BO parent::beforeAction (bao gom BaseController auth check)
        if ($action->id === 'api-san-luong'
            && Yii::$app->request->userIP === '192.168.31.6') {
            // Goi Yii\base\Controller::beforeAction de chay behaviors/events
            // nhung KHONG goi BaseController::beforeAction (co auth check)
            return \yii\web\Controller::beforeAction($action);
        }
        return parent::beforeAction($action);
    }

    public function actionBaoCao()
    {
        return $this->render('/hocau/bao_cao/index');
    }

    public function actionChatLuongGio($ngay = null, $ca = null)
    {
        $ngay = $ngay ?? date('Y-m-d');
        if ($ca === null) {
            $ca = (date('H') >= 7 && date('H') < 19) ? 1 : 2;
        }
        $ca = (int)$ca;

        $gioList = $ca == 1
            ? [7,8,9,10,11,12,13,14,15,16,17,18]
            : [19,20,21,22,23,0,1,2,3,4,5,6];

        $allFields = [
            'ns_ph','ns_ntu','nt_ph','nt_ntu','nl1_ph','nl1_ntu','nl2_ph','nl2_ntu',
            'clo_du',
            'ns_clo_nong_do','nt_clo_nong_do','nc_clo_cham','pac_cham',
            'nt_do_mau','ns_do_mau',
            'ns_do_kiem','nt_do_kiem',
            'ns_do_cung','nt_do_cung',
            'ns_clorua','nt_clorua',
            'ngoai_ho_ph','ngoai_ho_ntu',
            'muong_pu_thu_hoi','muong_lang_nl1','muong_pu_ns','dau_be_ns',
            'ho_xi_phong_1_ntu','ho_xi_phong_2_ntu',
            'pac_ty_trong',
        ];

        if (Yii::$app->request->isPost) {
            $post    = Yii::$app->request->post();
            $rows    = $post['rows'] ?? [];
            $nguoi_truc = trim($post['nguoi_truc'] ?? '');
            $nguoi_kt   = trim($post['nguoi_kt'] ?? '');
            $username   = Yii::$app->user->identity->username ?? '';

            // Bảng tính Clo từ POST
            $clo_mat_ban_dau      = (isset($post['clo_mat_ban_dau']) && $post['clo_mat_ban_dau'] !== '') ? (float)$post['clo_mat_ban_dau'] : null;
            $clo_mat_trong_be     = (isset($post['clo_mat_trong_be']) && $post['clo_mat_trong_be'] !== '') ? (float)$post['clo_mat_trong_be'] : null;
            $clo_khoi_luong_cham  = (isset($post['clo_khoi_luong_cham']) && $post['clo_khoi_luong_cham'] !== '') ? (float)$post['clo_khoi_luong_cham'] : null;
            $clo_ll_nuoc_tho      = (isset($post['clo_ll_nuoc_tho']) && $post['clo_ll_nuoc_tho'] !== '') ? (float)$post['clo_ll_nuoc_tho'] : null;
            $clo_du_bq_nhap       = (isset($post['clo_du_bq_nhap']) && $post['clo_du_bq_nhap'] !== '') ? (float)$post['clo_du_bq_nhap'] : null;

            $saved = 0;
            foreach ($gioList as $gio) {
                $rowData = $rows[(string)$gio] ?? [];
                $hasVal = false;
                foreach ($allFields as $f) {
                    if (isset($rowData[$f]) && $rowData[$f] !== '') { $hasVal = true; break; }
                }
                if (!$hasVal) continue;

                $thoi_gian = $ngay . ' ' . ($gio === 0 ? '00' : str_pad($gio, 2, '0', STR_PAD_LEFT)) . ':00:00';
                $model = NkChatLuongGio::findOne(['thoi_gian' => $thoi_gian, 'ca' => $ca]);
                if (!$model) {
                    $model = new NkChatLuongGio();
                    $model->thoi_gian  = $thoi_gian;
                    $model->ca         = $ca;
                    $model->nguoi_nhap = $username;
                }
                foreach ($allFields as $f) {
                    $v = $rowData[$f] ?? null;
                    $model->$f = ($v !== '' && $v !== null) ? (float)$v : null;
                }
                $model->nguoi_truc = $nguoi_truc ?: null;
                $model->nguoi_kt   = $nguoi_kt   ?: null;

                $model->clo_mat_ban_dau      = $clo_mat_ban_dau;
                $model->clo_mat_trong_be     = $clo_mat_trong_be;
                $model->clo_khoi_luong_cham  = $clo_khoi_luong_cham;
                $model->clo_ll_nuoc_tho      = $clo_ll_nuoc_tho;
                $model->clo_du_bq_nhap       = $clo_du_bq_nhap;

                $model->save();
                $saved++;
            }

            $this->saveJarTest($ngay, $ca, $post);
            Yii::$app->session->setFlash('success', 'Đã lưu ' . $saved . ' giờ — ' . date('H:i'));
            return $this->redirect(['chat-luong-gio', 'ngay' => $ngay, 'ca' => $ca]);
        }

        $lichSu = NkChatLuongGio::find()
            ->where(['>=', 'thoi_gian', $ngay . ' 00:00:00'])
            ->andWhere(['<=', 'thoi_gian', $ngay . ' 23:59:59'])
            ->andWhere(['ca' => $ca])
            ->orderBy(['thoi_gian' => SORT_ASC])
            ->all();

        $model = $lichSu[0] ?? new NkChatLuongGio();
        $jarTest = \app\modules\quanly\models\hocau\NkJarTest::findOne(['ngay' => $ngay, 'ca' => $ca]);

        return $this->render('/hocau/chat_luong_gio/index', [
            'model'   => $model,
            'lichSu'  => $lichSu,
            'ngay'    => $ngay,
            'ca'      => $ca,
            'QCVN'    => NkChatLuongGio::QCVN,
            'jarTest' => $jarTest,
        ]);
    }

    /**
     * Hàm lưu Jar Test (đã được fix triệt để vụ empty("0") và lieu_chon rỗng)
     */
    /**
     * Lưu jar test (Bản siêu gỡ lỗi + Chống đạn dấu phẩy)
     */
    private function saveJarTest(string $ngay, int $ca, array $post): void
    {
        $jGio = trim($post['jar_gio'] ?? ($ca == 1 ? '08:00' : '19:00'));
        $pac  = $post['jar_pac'] ?? [];
        $ntu  = $post['jar_ntu'] ?? [];
        $ph   = $post['jar_ph']  ?? [];
        $lieu = $post['jar_lieu_chon'] ?? null;

        // --- BẬT RADAR DEBUG: Hiển thị thẳng ra màn hình ---
        $debugMsg = "🔍 JarTest Nhận: lieu_chon = '" . $lieu . "'";

        // Đổi dấu phẩy thành dấu chấm
        if ($lieu !== null && $lieu !== '') {
            $lieu = str_replace(',', '.', (string)$lieu);
        }
        $debugMsg .= " | Đổi thành = '" . $lieu . "'";

        $hasData = false;
        if ($lieu !== null && $lieu !== '') { 
            $hasData = true; 
        } else {
            for ($i = 0; $i < 6; $i++) {
                if ((isset($pac[$i]) && $pac[$i] !== '') || 
                    (isset($ntu[$i]) && $ntu[$i] !== '') || 
                    (isset($ph[$i]) && $ph[$i] !== '')) { 
                    $hasData = true; break; 
                }
            }
        }

        if (!$hasData) {
            Yii::$app->session->addFlash('error', $debugMsg . ' ❌ LỖI: Server nhận được chuỗi rỗng, không lưu!');
            return;
        }

        $jar = \app\modules\quanly\models\hocau\NkJarTest::findOne(['ngay'=>$ngay,'ca'=>$ca]);
        if (!$jar) {
            $jar = new \app\modules\quanly\models\hocau\NkJarTest();
            $jar->ngay = $ngay;
            $jar->ca   = $ca;
        }
        
        $jar->gio_thu    = $jGio ? $jGio . ':00' : null;
        $jar->lieu_chon  = ($lieu !== null && $lieu !== '') ? (float)$lieu : null;
        $jar->nguoi_nhap = Yii::$app->user->identity->username ?? '';
        
        for ($i = 0; $i < 6; $i++) {
            $col = $i + 1;
            $valPac = isset($pac[$i]) && $pac[$i] !== '' ? str_replace(',', '.', (string)$pac[$i]) : null;
            $valNtu = isset($ntu[$i]) && $ntu[$i] !== '' ? str_replace(',', '.', (string)$ntu[$i]) : null;
            $valPh  = isset($ph[$i])  && $ph[$i]  !== '' ? str_replace(',', '.', (string)$ph[$i])  : null;
            
            $jar->{'pac_'.$col.'_lieu'} = $valPac !== null ? (float)$valPac : null;
            $jar->{'pac_'.$col.'_ntu'}  = $valNtu !== null ? (float)$valNtu : null;
            $jar->{'pac_'.$col.'_ph'}   = $valPh  !== null ? (float)$valPh  : null;
        }
        
        if ($jar->save(false)) {
            Yii::$app->session->addFlash('success', $debugMsg . ' ✅ ĐÃ GHI VÀO DATABASE THÀNH CÔNG!');
        } else {
            Yii::$app->session->addFlash('error', $debugMsg . ' ❌ LỖI DB TỪ CHỐI LƯU!');
        }
    }

    public function actionGiaoCa($ngay = null, $ca = null)
    {
        $ngay = $ngay ?? date('Y-m-d');
        $ca   = $ca   ?? ((date('H') >= 7 && date('H') < 19) ? 1 : 2);

        $model = NkGiaoCa::findOne(['ngay' => $ngay, 'ca' => $ca]);
        if (!$model) {
            $model = new NkGiaoCa();
            $model->ngay       = $ngay;
            $model->ca         = $ca;
            $model->nguoi_nhap = Yii::$app->user->identity->username ?? '';
        }

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            if ($model->save()) {
                // Cảnh báo nhập liệu bất thường — không chặn lưu, chỉ nhắc kiểm tra lại
                // Chỉ xét nguồn nào ĐÃ nhập đủ cả Đầu ca lẫn Cuối ca (bỏ qua nếu 1 trong 2 còn trống)
                $NGUONG_CANH_BAO_DIEN_KWH = 20000; // KWh/ca/nguồn — điều chỉnh nếu cần
                $capNguon = array(
                    'Nhà máy'  => array($model->dien_nha_may_dau,     $model->dien_nha_may_cuoi),
                    'Trạm bơm' => array($model->dien_tram_bom_dau,    $model->dien_tram_bom_cuoi),
                    'NT5'      => array($model->dien_nt5_tang_ap_dau, $model->dien_nt5_tang_ap_cuoi),
                );
                $canhBao = array();
                foreach ($capNguon as $ten => $cap) {
                    list($dau, $cuoi) = $cap;
                    if ($dau === null || $dau === '' || $cuoi === null || $cuoi === '') {
                        continue; // chưa nhập đủ, chưa đánh giá được — bỏ qua
                    }
                    $d = (float)$cuoi - (float)$dau;
                    if ($d < 0) {
                        $canhBao[] = $ten . ': Cuối ca nhỏ hơn Đầu ca (' . number_format($d) . ') — kiểm tra lại';
                    } elseif ($d > $NGUONG_CANH_BAO_DIEN_KWH) {
                        $canhBao[] = $ten . ': chênh lệch ' . number_format($d) . ' KWh — bất thường, kiểm tra lại số đã nhập';
                    }
                }
                if ($canhBao) {
                    Yii::$app->session->setFlash('warning', 'Đã lưu sổ giao ca, nhưng phát hiện số liệu điện bất thường: ' . implode(' | ', $canhBao));
                } else {
                    Yii::$app->session->setFlash('success', 'Đã lưu sổ giao ca');
                }
                return $this->redirect(['giao-ca', 'ngay' => $ngay, 'ca' => $ca]);
            }
        }

        return $this->render('/hocau/giao_ca/index', [
            'model' => $model,
            'ngay'  => $ngay,
            'ca'    => (int)$ca,
        ]);
    }

    public function actionNuocThaiSh($ngay = null)
    {
        $ngay = $ngay ?? date('Y-m-d');

        $model = NkNuocThaiSh::findOne(['ngay' => $ngay]);
        if (!$model) {
            $model = new NkNuocThaiSh();
            $model->ngay       = $ngay;
            $model->nguoi_nhap = Yii::$app->user->identity->username ?? '';
        }

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Đã lưu kết quả nước thải ngày ' . date('d/m/Y', strtotime($ngay)));
                return $this->redirect(['nuoc-thai-sh', 'ngay' => $ngay]);
            }
        }

        $lichSu = NkNuocThaiSh::find()
            ->where(['>=', 'ngay', date('Y-m-d', strtotime('-90 days'))])
            ->orderBy(['ngay' => SORT_DESC])
            ->all();

        return $this->render('/hocau/nuoc_thai_sh/index', [
            'model'  => $model,
            'lichSu' => $lichSu,
            'ngay'   => $ngay,
            'QCVN'   => NkNuocThaiSh::QCVN,
        ]);
    }

    public function actionClnHangNgay($ngay = null)
    {
        $ngay = $ngay ?? date('Y-m-d');

        $model = NkClnHangNgay::findOne(['ngay' => $ngay]);
        if (!$model) {
            $model = new NkClnHangNgay();
            $model->ngay       = $ngay;
            $model->gio_data   = '{}';
            $model->nguoi_nhap = Yii::$app->user->identity->username ?? '';
        }

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            $model->load($post);

            $gioPost = $post['gio'] ?? [];
            $gioData = json_decode($model->gio_data ?? '{}', true);
            $GIO_ALL = NkClnHangNgay::GIO_ALL;
            foreach ($GIO_ALL as $gio) {
                $row = $gioPost[(string)$gio] ?? [];
                $vals = [];
                foreach ($row as $k => $v) {
                    $vals[$k] = ($v !== '' && $v !== null) ? (float)$v : null;
                }
                $hasVal = false;
                foreach ($vals as $_v) { if ($_v !== null) { $hasVal = true; break; } }
                if ($hasVal) {
                    $gioData[(string)$gio] = $vals;
                }
            }
            $model->gio_data = json_encode($gioData);

            foreach (['s','c'] as $k) {
                $prefix = 'jar_' . $k;
                foreach (['pac','ntu','ph'] as $t) {
                    $arr = $post[$prefix . '_' . $t] ?? [];
                    $arrVals = array_values($arr);
                    $mapped = [];
                    foreach ($arrVals as $_av) {
                        $mapped[] = ($_av !== '' && $_av !== null) ? (float)$_av : null;
                    }
                    $model->{'jar_' . $k . '_' . $t} = json_encode($mapped);
                }
            }

            $model->updated_at = date('Y-m-d H:i:s');
            if ($model->save()) {
                Yii::$app->session->setFlash('success_cln', 'Đã lưu CLN hàng ngày ' . date('d/m/Y', strtotime($ngay)));
                return $this->redirect(['cln-hang-ngay', 'ngay' => $ngay]);
            }
        }

        return $this->render('/hocau/cln_hang_ngay/index', [
            'model' => $model,
            'ngay'  => $ngay,
        ]);
    }

    public function actionPhanTichTuan($thang = null, $nam = null)
    {
        $thang = (int)(isset($thang) ? $thang : date('m'));
        $nam   = (int)(isset($nam)   ? $nam   : date('Y'));

        $lichTuan = NkPhanTichTuan::find()
            ->where(['thang' => $thang, 'nam' => $nam])
            ->orderBy(['tuan_so' => SORT_ASC, 'ngay_pt' => SORT_ASC])
            ->all();

        if (Yii::$app->request->isPost) {
            $post     = Yii::$app->request->post();
            $rows     = isset($post['rows']) ? $post['rows'] : [];
            $nguoi_pt = trim(isset($post['nguoi_pt']) ? $post['nguoi_pt'] : '');
            $nguoi_kt = trim(isset($post['nguoi_kt']) ? $post['nguoi_kt'] : '');
            $username = Yii::$app->user->identity->username;

            $numFields = [
                'nt_do_kiem','nt_do_cung','nt_clorua','nt_tss',
                'nt_al','nt_fe','nt_mn','nt_amoni','nt_nitrat','nt_nitrit',
                'nt_sulfat','nt_permanganat','nt_cod','nt_florua',
                'ns_do_kiem','ns_do_cung','ns_clorua','ns_tss',
                'ns_al','ns_fe','ns_mn','ns_amoni','ns_nitrat','ns_nitrit',
                'ns_sulfat','ns_permanganat','ns_cod','ns_florua',
            ];

            $savedCount    = 0;
            $skippedNoDate = 0;
            $failedRows    = [];

            foreach ($rows as $tuanSo => $tuanRows) {
                if (!is_array($tuanRows)) continue;

                foreach ($tuanRows as $ri => $rowData) {
                    if (!is_array($rowData)) continue;
                    $ngayPt = isset($rowData['ngay_pt']) ? trim($rowData['ngay_pt']) : '';
                    if (!$ngayPt) { $skippedNoDate++; continue; }

                    $recId = isset($rowData['id']) && $rowData['id'] ? (int)$rowData['id'] : null;
                    $rec   = null;
                    if ($recId) {
                        $rec = NkPhanTichTuan::findOne($recId);
                    }
                    if (!$rec) {
                        $rec = NkPhanTichTuan::findOne(['ngay_pt' => $ngayPt]);
                    }
                    if (!$rec) {
                        $rec = new NkPhanTichTuan();
                        $rec->ngay_pt = $ngayPt;
                    } else {
                        $rec->ngay_pt = $ngayPt;
                    }

                    $rec->tuan_so    = (int)$tuanSo;
                    $rec->thang      = $thang;
                    $rec->nam        = $nam;
                    $rec->nguoi_pt   = $nguoi_pt ?: null;
                    $rec->nguoi_kt   = $nguoi_kt ?: null;
                    $rec->nguoi_nhap = $username;

                    foreach ($numFields as $f) {
                        $v = isset($rowData[$f]) ? $rowData[$f] : null;
                        $rec->$f = ($v !== '' && $v !== null) ? (float)$v : null;
                    }
                    foreach (['nt_coliform','ns_coliform'] as $cf) {
                        $v = isset($rowData[$cf]) ? $rowData[$cf] : null;
                        $rec->$cf = ($v !== '' && $v !== null) ? (int)$v : null;
                    }

                    if ($rec->save(false)) {
                        $savedCount++;
                    } else {
                        $failedRows[] = 'Tuần ' . $tuanSo . ' - dòng ' . ($ri + 1);
                    }
                }
            }

            if ($savedCount > 0 && empty($failedRows)) {
                Yii::$app->session->setFlash('success_tuan', 'Đã lưu ' . $savedCount . ' dòng — CL nước tháng ' . $thang . '/' . $nam);
            } elseif ($savedCount > 0) {
                Yii::$app->session->setFlash('warning_tuan', 'Đã lưu ' . $savedCount . ' dòng, nhưng LỖI ở: ' . implode(', ', $failedRows));
            } else {
                Yii::$app->session->setFlash('error_tuan',
                    'KHÔNG có dòng nào được lưu! ' . $skippedNoDate . ' dòng bị bỏ qua do CHƯA nhập "Ngày PT" — vui lòng chọn ngày cho từng dòng rồi bấm Lưu lại.');
            }
            return $this->redirect(['phan-tich-tuan', 'thang' => $thang, 'nam' => $nam]);
        }

        $firstRec = isset($lichTuan[0]) ? $lichTuan[0] : null;
        $nguoi_pt = ($firstRec !== null && $firstRec->nguoi_pt) ? $firstRec->nguoi_pt : '';
        $nguoi_kt = ($firstRec !== null && $firstRec->nguoi_kt) ? $firstRec->nguoi_kt : '';

        return $this->render('/hocau/phan_tich_tuan/index', [
            'lichTuan' => $lichTuan,
            'thang'    => $thang,
            'nam'      => $nam,
            'nguoi_pt' => $nguoi_pt,
            'nguoi_kt' => $nguoi_kt,
        ]);
    }

    public function actionSanLuongDongHo($tu_ngay = null, $den_ngay = null)
    {
        $den_ngay = $den_ngay ?? date('Y-m-d');
        $tu_ngay  = $tu_ngay  ?? date('Y-m-d', strtotime('-7 days'));
        $khachHang = NkDongHoKhachHang::getActive();
        return $this->render('/hocau/san_luong_dong_ho/index', [
            'tu_ngay'   => $tu_ngay,
            'den_ngay'  => $den_ngay,
            'khachHang' => $khachHang,
        ]);
    }

    public function actionDongHoConfig()
    {
        $danhSach = NkDongHoKhachHang::find()
            ->orderBy(['thu_tu' => SORT_ASC, 'id' => SORT_ASC])
            ->all();
        return $this->render('/hocau/san_luong_dong_ho/config', ['danhSach' => $danhSach]);
    }

    public function actionDongHoSave()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (!Yii::$app->request->isPost) return ['success'=>false,'msg'=>'Invalid'];

        $post = Yii::$app->request->post();
        $id   = $post['id'] ?? null;
        $model = $id ? NkDongHoKhachHang::findOne($id) : new NkDongHoKhachHang();
        if (!$model) return ['success'=>false,'msg'=>'Không tìm thấy bản ghi'];

        $dvao = $post['channel_dau_vao'] ?? [];
        $dra  = $post['channel_dau_ra']  ?? [];
        if (is_string($dvao)) $dvao = array_filter(array_map('trim', explode(',', $dvao)));
        if (is_string($dra))  $dra  = array_filter(array_map('trim', explode(',', $dra)));

        $sanitize = function($ids) {
            return array_values(array_filter(array_map(function($id) {
                $id = trim($id);
                return preg_match('/^[a-zA-Z0-9_]+$/', $id) ? $id : null;
            }, $ids)));
        };
        $model->ten_kh          = $post['ten_kh'] ?? '';
        $model->thu_tu          = (int)($post['thu_tu'] ?? 0);
        $model->channel_dau_vao = json_encode($sanitize($dvao));
        $model->channel_dau_ra  = json_encode($sanitize($dra));
        $model->don_vi          = $post['don_vi'] ?? 'm³';
        $model->ghi_chu         = $post['ghi_chu'] ?? null;
        $model->active          = (bool)($post['active'] ?? true);
        $model->updated_at      = date('Y-m-d H:i:s');

        if ($model->save()) return ['success'=>true,'id'=>$model->id];
        return ['success'=>false,'msg'=>implode(', ', $model->getFirstErrors())];
    }

    public function actionDongHoDelete($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = NkDongHoKhachHang::findOne($id);
        if (!$model) return ['success'=>false,'msg'=>'Không tìm thấy'];
        $model->delete();
        return ['success'=>true];
    }

    public function actionApiCln($ngay = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $ngay = $ngay ?? date('Y-m-d');

        $cln = NkChatLuongGio::find()
            ->where(['>=', 'thoi_gian', $ngay . ' 00:00:00'])
            ->andWhere(['<=', 'thoi_gian', $ngay . ' 23:59:59'])
            ->orderBy('thoi_gian')
            ->all();

        $rows = [];
        foreach ($cln as $r) {
            $rows[] = [
                'gio'     => date('H:i', strtotime($r->thoi_gian)),
                'ca'      => $r->ca,
                'ns_ph'   => $r->ns_ph,  'ns_ntu'  => $r->ns_ntu,
                'nt_ph'   => $r->nt_ph,  'nt_ntu'  => $r->nt_ntu,
                'nl1_ph'  => $r->nl1_ph, 'nl1_ntu' => $r->nl1_ntu,
                'nl2_ph'  => $r->nl2_ph, 'nl2_ntu' => $r->nl2_ntu,
                'clo_du'  => $r->clo_du,
            ];
        }

        $ca_ngay = NkGiaoCa::findOne(['ngay' => $ngay, 'ca' => 1]);
        $ca_dem  = NkGiaoCa::findOne(['ngay' => $ngay, 'ca' => 2]);
        $clnNgay = NkClnHangNgay::findOne(['ngay' => $ngay]);

        return [
            'count'      => count($rows),
            'rows'       => $rows,
            'ca_ngay'    => $ca_ngay ? ['sl_cap' => $ca_ngay->getSanLuongCap()] : null,
            'ca_dem'     => $ca_dem  ? ['sl_cap' => $ca_dem->getSanLuongCap()]  : null,
            'cln_ngay'   => $clnNgay ? ['id' => $clnNgay->id] : null,
        ];
    }

    public function actionApiNuocThai($ngay = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $ngay = $ngay ?? date('Y-m-d');
        $rec  = NkNuocThaiSh::findOne(['ngay' => $ngay]);
        return [
            'has_data' => $rec !== null,
            'data'     => $rec ? [
                'ph'       => $rec->ph,
                'tss'      => $rec->tss,
                'amoni'    => $rec->amoni,
                'nitrat'   => $rec->nitrat,
                'coliform' => $rec->coliform,
                'nguoi_th' => $rec->nguoi_th,
            ] : null,
        ];
    }

    public function actionApiSanLuong($tu_ngay = null, $den_ngay = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $den_ngay = $den_ngay ?? date('Y-m-d');
        $tu_ngay  = $tu_ngay  ?? date('Y-m-d', strtotime('-7 days'));

        if (!strtotime($tu_ngay) || !strtotime($den_ngay)) {
            return ['success'=>false,'msg'=>'Ngày không hợp lệ'];
        }

        $khachHang = NkDongHoKhachHang::getActive();
        if (empty($khachHang)) return ['success'=>true,'rows'=>[],'ngay_list'=>[]];

        $allChannels = [];
        foreach ($khachHang as $kh) {
            foreach ($kh->getChannelDauVaoArr() as $cid) $allChannels[$cid] = true;
            foreach ($kh->getChannelDauRaArr()  as $cid) $allChannels[$cid] = true;
        }
        $channelIds = array_keys($allChannels);

        $ngayList = [];
        $d = strtotime($tu_ngay);
        $dEnd = strtotime($den_ngay);
        while ($d <= $dEnd) {
            $ngayList[] = date('Y-m-d', $d);
            $d = strtotime('+1 day', $d);
        }
        if (empty($ngayList)) return ['success'=>true,'rows'=>[],'ngay_list'=>[]];

        $scadaData = $this->fetchScadaSanLuong($channelIds, $tu_ngay, $den_ngay);

        $rows = [];
        foreach ($khachHang as $kh) {
            $dvaoIds = $kh->getChannelDauVaoArr();
            $draIds  = $kh->getChannelDauRaArr();

            $rowData = [
                'id'        => $kh->id,
                'ten_kh'    => $kh->ten_kh,
                'dau_vao'   => $kh->getLabelDauVao(),
                'dau_ra'    => $kh->getLabelDauRa(),
                'don_vi'    => $kh->don_vi,
                'ngay_data' => [],
                'tong'      => null,
                'tb_ngay'   => null,
            ];

            $tongTong  = 0;
            $countNgay = 0;

            foreach ($ngayList as $ngay) {
                $vao = 0;
                foreach ($dvaoIds as $cid) {
                    $v = $scadaData[$cid][$ngay] ?? null;
                    if ($v !== null) $vao += $v;
                }
                $ra = 0;
                foreach ($draIds as $cid) {
                    $v = $scadaData[$cid][$ngay] ?? null;
                    if ($v !== null) $ra += $v;
                }
                $hasData = false;
                foreach ($dvaoIds as $cid) {
                    if (isset($scadaData[$cid][$ngay])) { $hasData = true; break; }
                }
                $sl = $hasData ? max(0, $vao - $ra) : null;
                $rowData['ngay_data'][$ngay] = $sl;
                if ($sl !== null) { $tongTong += $sl; $countNgay++; }
            }

            // Option B: ca Tong va TB deu bo ngay hom nay (chua chot)
            $today = date('Y-m-d');
            $tongChot = 0;
            $countNgayForAvg = 0;
            foreach ($ngayList as $ngay) {
                if ($ngay !== $today && $rowData['ngay_data'][$ngay] !== null) {
                    $tongChot += $rowData['ngay_data'][$ngay];
                    $countNgayForAvg++;
                }
            }
            $rowData['tong']    = $countNgayForAvg > 0 ? round($tongChot) : null;
            $rowData['tb_ngay'] = $countNgayForAvg > 0 ? round($tongChot / $countNgayForAvg) : null;
            $rows[] = $rowData;
        }

        return ['success' => true, 'rows' => $rows, 'ngay_list' => $ngayList];
    }

    private function fetchScadaSanLuong(array $channelIds, string $tu_ngay, string $den_ngay): array
    {
        $params = http_build_query([
            'key'      => 'SCADA_HOCAU_2024_SECRET_KEY',
            'channels' => implode(',', $channelIds),
            'tu_ngay'  => $tu_ngay,
            'den_ngay' => $den_ngay,
        ]);
        $ctx  = stream_context_create(['http' => ['timeout' => 15]]);
        $json = @file_get_contents('http://192.168.31.6:5001/sanluong_dong_ho?' . $params, false, $ctx);
        if (!$json) return [];
        $data = json_decode($json, true);
        if (empty($data['success']) || !isset($data['data'])) return [];
        $result = [];
        foreach (($data['data'] ?? []) as $channelId => $ngayData) {
            $result[(string)$channelId] = $ngayData;
        }
        return $result;
    }

    public function actionApiVanHanh($tu_ngay = null, $den_ngay = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $db = Yii::$app->db;

        $params = [];
        if ($tu_ngay && $den_ngay) {
            $whereNgay = "ngay >= :tu_ngay AND ngay <= :den_ngay";
            $params = [':tu_ngay' => $tu_ngay, ':den_ngay' => $den_ngay];
        } else {
            $whereNgay = "ngay >= CURRENT_DATE - INTERVAL '365 days' AND ngay <= CURRENT_DATE";
        }

        $sqlNgay = "
            SELECT ngay::text AS ngay,
                   SUM( CASE WHEN dien_nha_may_cuoi IS NOT NULL AND dien_nha_may_dau IS NOT NULL
                             THEN dien_nha_may_cuoi - dien_nha_may_dau ELSE 0 END
                      + CASE WHEN dien_tram_bom_cuoi IS NOT NULL AND dien_tram_bom_dau IS NOT NULL
                             THEN dien_tram_bom_cuoi - dien_tram_bom_dau ELSE 0 END
                      + CASE WHEN dien_nt5_tang_ap_cuoi IS NOT NULL AND dien_nt5_tang_ap_dau IS NOT NULL
                             THEN dien_nt5_tang_ap_cuoi - dien_nt5_tang_ap_dau ELSE 0 END ) AS dien,
                   SUM( CASE WHEN nuoc_cap_cuoi IS NOT NULL AND nuoc_cap_dau IS NOT NULL
                             THEN nuoc_cap_cuoi - nuoc_cap_dau ELSE 0 END ) AS nuoc_sach,
                   SUM(COALESCE(pac_kg,      0)) AS pac,
                   SUM(COALESCE(chlorine_kg, 0)) AS chlorine,
                   SUM(COALESCE(polymer_kg,  0)) AS polymer
            FROM nk_giao_ca
            WHERE $whereNgay
            GROUP BY ngay
            ORDER BY ngay ASC
        ";

        if ($tu_ngay && $den_ngay) {
            $whereThang = "ngay >= :tu_ngay AND ngay <= :den_ngay";
        } else {
            $whereThang = "ngay >= DATE_TRUNC('month', CURRENT_DATE) - INTERVAL '23 months'";
        }

        $sqlThang = "
            SELECT TO_CHAR(ngay,'YYYY-MM') AS thang,
                   SUM( CASE WHEN dien_nha_may_cuoi IS NOT NULL AND dien_nha_may_dau IS NOT NULL
                             THEN dien_nha_may_cuoi - dien_nha_may_dau ELSE 0 END
                      + CASE WHEN dien_tram_bom_cuoi IS NOT NULL AND dien_tram_bom_dau IS NOT NULL
                             THEN dien_tram_bom_cuoi - dien_tram_bom_dau ELSE 0 END
                      + CASE WHEN dien_nt5_tang_ap_cuoi IS NOT NULL AND dien_nt5_tang_ap_dau IS NOT NULL
                             THEN dien_nt5_tang_ap_cuoi - dien_nt5_tang_ap_dau ELSE 0 END ) AS dien,
                   SUM( CASE WHEN nuoc_cap_cuoi IS NOT NULL AND nuoc_cap_dau IS NOT NULL
                             THEN nuoc_cap_cuoi - nuoc_cap_dau ELSE 0 END ) AS nuoc_sach,
                   SUM(COALESCE(pac_kg,      0)) AS pac,
                   SUM(COALESCE(chlorine_kg, 0)) AS chlorine,
                   SUM(COALESCE(polymer_kg,  0)) AS polymer
            FROM nk_giao_ca
            WHERE $whereThang
            GROUP BY TO_CHAR(ngay,'YYYY-MM')
            ORDER BY thang ASC
        ";

        if ($tu_ngay && $den_ngay) {
            $whereNam = "WHERE ngay >= :tu_ngay AND ngay <= :den_ngay";
        } else {
            $whereNam = "";
        }

        $sqlNam = "
            SELECT EXTRACT(YEAR FROM ngay)::text AS nam,
                   SUM( CASE WHEN dien_nha_may_cuoi IS NOT NULL AND dien_nha_may_dau IS NOT NULL
                             THEN dien_nha_may_cuoi - dien_nha_may_dau ELSE 0 END
                      + CASE WHEN dien_tram_bom_cuoi IS NOT NULL AND dien_tram_bom_dau IS NOT NULL
                             THEN dien_tram_bom_cuoi - dien_tram_bom_dau ELSE 0 END
                      + CASE WHEN dien_nt5_tang_ap_cuoi IS NOT NULL AND dien_nt5_tang_ap_dau IS NOT NULL
                             THEN dien_nt5_tang_ap_cuoi - dien_nt5_tang_ap_dau ELSE 0 END ) AS dien,
                   SUM( CASE WHEN nuoc_cap_cuoi IS NOT NULL AND nuoc_cap_dau IS NOT NULL
                             THEN nuoc_cap_cuoi - nuoc_cap_dau ELSE 0 END ) AS nuoc_sach,
                   SUM(COALESCE(pac_kg,      0)) AS pac,
                   SUM(COALESCE(chlorine_kg, 0)) AS chlorine,
                   SUM(COALESCE(polymer_kg,  0)) AS polymer
            FROM nk_giao_ca
            $whereNam
            GROUP BY EXTRACT(YEAR FROM ngay)
            ORDER BY nam ASC
        ";

        $cast = function($rows, $textKey) {
            $out = array();
            foreach ($rows as $row) {
                $r = array();
                foreach ($row as $k => $v) {
                    $r[$k] = ($k === $textKey) ? $v : round(floatval($v), 2);
                }
                $out[] = $r;
            }
            return $out;
        };

        return array(
            'ngay_data'  => $cast($db->createCommand($sqlNgay)->bindValues($params)->queryAll(),  'ngay'),
            'thang_data' => $cast($db->createCommand($sqlThang)->bindValues($params)->queryAll(), 'thang'),
            'nam_data'   => $cast($db->createCommand($sqlNam)->bindValues($params)->queryAll(),   'nam'),
        );
    }

    public function actionXuatHoaNghiem($ngay = null)
    {
        $ngay = $ngay ?? date('Y-m-d');
        $chatLuong = NkChatLuongGio::find()
            ->where(['>=', 'thoi_gian', $ngay . ' 00:00:00'])
            ->andWhere(['<=', 'thoi_gian', $ngay . ' 23:59:59'])
            ->orderBy('thoi_gian')->all();
        $clnNgay = NkClnHangNgay::findOne(['ngay' => $ngay]);

        $builder = new \app\modules\quanly\components\HoaNghiemExcel($ngay, $chatLuong, $clnNgay);
        $builder->download();
    }

    public function actionXuatVanHanh($ngay = null)
    {
        $ngay = $ngay ?? date('Y-m-d');
        $giaoCa = NkGiaoCa::findAll(['ngay' => $ngay]);
        $scadaData = $this->getScadaForNgay($ngay);
        $builder = new \app\modules\quanly\components\VanHanhExcel($ngay, $giaoCa, $scadaData);
        $builder->download();
    }

    public function actionXuatNuocThai($ngay = null)
    {
        $ngay = $ngay ?? date('Y-m-d');
        $nuocThai = NkNuocThaiSh::findOne(['ngay' => $ngay]);
        $builder = new \app\modules\quanly\components\NuocThaiExcel($ngay, $nuocThai);
        $builder->download();
    }

    public function actionXuatClnTuan($thang = null, $nam = null)
    {
        $thang = (int)($thang ?? date('m'));
        $nam   = (int)($nam   ?? date('Y'));
        $lichTuan = NkPhanTichTuan::find()
            ->where(['thang' => $thang, 'nam' => $nam])
            ->orderBy(['tuan_so' => SORT_ASC])->all();
        $builder = new \app\modules\quanly\components\ClnTuanExcel($thang, $nam, $lichTuan);
        $builder->download();
    }

    public function actionXuatBaoCaoNgay($ngay = null)
    {
        $ngay = $ngay ?? date('Y-m-d');
        $chatLuong = NkChatLuongGio::find()
            ->where(['>=', 'thoi_gian', $ngay . ' 00:00:00'])
            ->andWhere(['<=', 'thoi_gian', $ngay . ' 23:59:59'])
            ->orderBy('thoi_gian')->all();
        $giaoCa = NkGiaoCa::findAll(['ngay' => $ngay]);
        $scadaData = $this->getScadaForNgay($ngay);
        $builder = new BaoCaoNgayExcel($ngay, $scadaData, $chatLuong, $giaoCa);
        $builder->download();
    }

    public function actionXuatSanLuong($tu_ngay = null, $den_ngay = null)
    {
        $den_ngay = $den_ngay ?? date('Y-m-d');
        $tu_ngay  = $tu_ngay  ?? date('Y-m-d', strtotime('-7 days'));
        $apiResult = $this->actionApiSanLuong($tu_ngay, $den_ngay);
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $builder = new SanLuongDongHoExcel($tu_ngay, $den_ngay, $apiResult['rows'] ?? [], $apiResult['ngay_list'] ?? []);
        $builder->download();
    }

    private function getScadaForNgay(string $ngay): array
    {
        $scadaData = [];
        $json = @file_get_contents(
            'http://192.168.31.11/iot_api.php?action=sanluong&loai=thatthoat&key=SCADA_HOCAU_2024_SECRET_KEY'
        );
        if ($json) {
            $data = json_decode($json, true);
            foreach (($data['days'] ?? []) as $d) {
                $ngayScada = $d['ngay'] ?? '';
                $ngayISO   = strpos($ngayScada, '/') !== false
                    ? date('Y-m-d', strtotime(str_replace('/', '-', $ngayScada)))
                    : $ngayScada;
                if ($ngayISO === $ngay) { $scadaData = $d; break; }
            }
        }
        return $scadaData;
    }
}