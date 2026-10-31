<?php

namespace app\modules\quanly\controllers\hocau;

use Yii;
use app\modules\quanly\models\hocau\Nhamaynuoc;
use app\modules\quanly\models\hocau\NhamaynuocSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use app\modules\quanly\base\QuanlyBaseController;
use app\modules\quanly\base\UploadFile;
use yii\web\UploadedFile;
use app\modules\services\CategoriesService;

class NhamaynuocController extends QuanlyBaseController
{
    public $title = "Nhà máy nước";

    private $nasBasePath = '\\\\192.168.31.8\\Gis-Data\\';

    public function actionIndex()
    {
        $searchModel = new NhamaynuocSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'categories'   => CategoriesService::getCategories(),
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);

        if ($model->file_dinhkem != null) {
            $filedinhkem = json_decode($model->file_dinhkem);
            $files = [];
            foreach ($filedinhkem as $i => $item) {
                $filename = basename($item);
                $filename = str_replace(' ', '_', $filename);
                $files[$i]['url']  = $item;
                $files[$i]['name'] = $filename;
            }
        } else {
            $files = null;
        }

        return $this->render('view', [
            'model' => $model,
            'files' => $files,
        ]);
    }

    public function actionCreate()
    {
        $request     = Yii::$app->request;
        $model       = new Nhamaynuoc();
        $filedinhkem = new UploadFile();

        if ($model->load($request->post()) && $model->save() && $filedinhkem->load($request->post())) {
            $filedinhkem->fileupload = UploadedFile::getInstances($filedinhkem, 'fileupload');

            if ($filedinhkem->fileupload != null) {
                $file        = [];
                if ($model->file_dinhkem != null) {
                    $existingFiles = json_decode($model->file_dinhkem, true);
                    if (is_array($existingFiles)) {
                        $file = $existingFiles;
                    }
                }
                $relative    = 'uploads/nhamaynuoc/' . $model->id . '/';
                $physicalDir = $this->nasBasePath . str_replace('/', '\\', $relative);

                if (!is_dir($physicalDir)) {
                    mkdir($physicalDir, 0777, true);
                }

                foreach ($filedinhkem->fileupload as $item) {
                    if (strpos($item->name, "'") !== false) {
                        $item->name = str_replace("'", '_', $item->name);
                    }
                    $fileName     = $item->baseName . '.' . $item->extension;
                    $physicalPath = $physicalDir . $fileName;
                    $dbPath       = $relative . $fileName;

                    if ($item->saveAs($physicalPath)) {
                        $file[] = $dbPath;
                    }
                }

                $model->file_dinhkem = json_encode($file);
                $model->save();
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model'       => $model,
            'filedinhkem' => $filedinhkem,
            'categories'  => CategoriesService::getCategories(),
        ]);
    }

    public function actionUpdate($id)
    {
        $request     = Yii::$app->request;
        $model       = $this->findModel($id);
        $filedinhkem = new UploadFile();

        if ($model->load($request->post()) && $model->save() && $filedinhkem->load($request->post())) {
            $filedinhkem->fileupload = UploadedFile::getInstances($filedinhkem, 'fileupload');

            if ($filedinhkem->fileupload != null) {
                $file        = [];
                $relative    = 'uploads/nhamaynuoc/' . $model->id . '/';
                $physicalDir = $this->nasBasePath . str_replace('/', '\\', $relative);

                if (!is_dir($physicalDir)) {
                    mkdir($physicalDir, 0777, true);
                }

                foreach ($filedinhkem->fileupload as $item) {
                    if (strpos($item->name, "'") !== false) {
                        $item->name = str_replace("'", '_', $item->name);
                    }
                    $fileName     = $item->baseName . '.' . $item->extension;
                    $physicalPath = $physicalDir . $fileName;
                    $dbPath       = $relative . $fileName;

                    if ($item->saveAs($physicalPath)) {
                        $file[] = $dbPath;
                    }
                }

                $model->file_dinhkem = json_encode($file);
                $model->save();
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model'       => $model,
            'filedinhkem' => $filedinhkem,
            'categories'  => CategoriesService::getCategories(),
        ]);
    }

    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $model   = $this->findModel($id);
        $model->status = 0;

        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title'   => "Xóa Nhà máy nước #" . $id,
                    'content' => $this->renderAjax('delete', ['model' => $model]),
                    'footer'  => Html::button('Đóng', ['class' => 'btn btn-light float-right', 'data-bs-dismiss' => "modal"]) .
                                 Html::button('Xóa',  ['class' => 'btn btn-danger float-left',  'type' => "submit"]),
                ];
            } elseif ($request->isPost && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title'       => "Xóa Nhà máy nước thành công #" . $id,
                    'content'     => '<span class="text-success">Xóa thành công</span>',
                    'footer'      => Html::button('Close', ['class' => 'btn btn-light float-right', 'data-bs-dismiss' => "modal"]),
                ];
            } else {
                return [
                    'title'   => "Update #" . $id,
                    'content' => $this->renderAjax('delete', ['model' => $model]),
                    'footer'  => Html::button('Close', ['class' => 'btn btn-light float-right', 'data-bs-dismiss' => "modal"]) .
                                 Html::button('Save',  ['class' => 'btn btn-primary', 'type' => "submit"]),
                ];
            }
        }
    }

    public function actionDownloadFile($path)
    {
        $cleanPath = str_replace(['..\\', '../', '..'], '', $path);
        $fullPath  = $this->nasBasePath . str_replace('/', '\\', $cleanPath);

        if (file_exists($fullPath)) {
            return Yii::$app->response->sendFile($fullPath, basename($fullPath), ['inline' => true]);
        }

        throw new NotFoundHttpException('File không tồn tại trên hệ thống NAS.');
    }

    protected function findModel($id)
    {
        if (($model = Nhamaynuoc::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionDeleteFile($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $path = Yii::$app->request->post('key');
        $model = $this->findModel($id);
        
        if ($model->file_dinhkem != null) {
            $files = json_decode($model->file_dinhkem, true);
            if (is_array($files)) {
                // Lọc bỏ file đã bị bấm X khỏi mảng
                $files = array_filter($files, function($v) use ($path) {
                    return $v !== $path;
                });
                
                // Lưu lại mảng mới vào DB (bypass validation để chạy nhanh)
                $model->file_dinhkem = json_encode(array_values($files));
                $model->save(false);
                
                // Xóa file vật lý trên NAS
                $cleanPath = str_replace(['..\\', '../', '..'], '', $path);
                $fullPath  = $this->nasBasePath . str_replace('/', '\\', $cleanPath);
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
                
                // Trả về JSON rỗng theo đúng chuẩn của Kartik FileInput để nó xóa hình trên UI
                return []; 
            }
        }
        return ['error' => 'Lỗi xóa file'];
    }
}
