<?php

namespace app\modules\quanly\controllers\hocau;

use Yii;
use app\modules\quanly\models\hocau\Suco;
use app\modules\quanly\models\hocau\SucoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use app\modules\quanly\base\QuanlyBaseController;
use app\modules\quanly\base\UploadFile;
use yii\web\UploadedFile;
use app\modules\services\CategoriesService;

/**
 * SucoController implements the CRUD actions for Suco model.
 */
class SucoController extends QuanlyBaseController
{
    public $title = "Sự cố";
    
    // CẤU HÌNH ĐƯỜNG DẪN NAS TẠI ĐÂY
    private $nasBasePath = '\\\\192.168.31.8\\Gis-Data\\';

    public function actionIndex()
    {
        $searchModel = new SucoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'categories' => CategoriesService::getCategories(),
        ]);
    }

    public function actionView($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);

        if($model->file_dinhkem != null){
            $filedinhkem = json_decode($model->file_dinhkem);
            $files = [];
            foreach($filedinhkem as $i => $item){
                $filename = basename($item); 
                $filename = str_replace(' ', '_', $filename);

                $files[$i]['url'] = $item;
                $files[$i]['name'] = $filename;
            }
        }else{
            $files = null;
        }

        return $this->render('view', [
            'model' => $this->findModel($id),
            'files' => $files,
        ]);
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new Suco();
        $filedinhkem = new UploadFile();

        if($model->load($request->post()) && $model->save() && $filedinhkem->load($request->post())){
            $filedinhkem->fileupload = UploadedFile::getInstances($filedinhkem, 'fileupload');

            if($filedinhkem->fileupload != null){
                $file = [];
                if ($model->file_dinhkem != null) {
                    $existingFiles = json_decode($model->file_dinhkem, true);
                    if (is_array($existingFiles)) {
                        $file = $existingFiles;
                    }
                }
                // Thư mục lưu tương đối trong DB
                $relativePath = 'uploads/suco/' . $model->id . '/';
                // Thư mục vật lý trên NAS
                $physicalDir = $this->nasBasePath . str_replace('/', '\\', $relativePath);

                if (!is_dir($physicalDir)) {
                    mkdir($physicalDir, 0777, true);
                }

                foreach($filedinhkem->fileupload as $i => $item){
                    if(strpos($item->name, "'") !== false){
                        $item->name = str_replace("'","_",$item->name);
                    }
                    
                    $fileName = $item->baseName . '.' . $item->extension;
                    $physicalPath = $physicalDir . $fileName;
                    $dbPath = $relativePath . $fileName;

                    // Lưu trực tiếp vào NAS
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
            'model' => $model,
            'filedinhkem' => $filedinhkem,
            'categories' => CategoriesService::getCategories(),
        ]);
    }

    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $filedinhkem = new UploadFile();

        if($model->load($request->post()) && $model->save() && $filedinhkem->load($request->post())){
            $filedinhkem->fileupload = UploadedFile::getInstances($filedinhkem, 'fileupload');

            if($filedinhkem->fileupload != null){
                $file = [];
                // Lấy lại file cũ nếu có để nối thêm file mới
                if ($model->file_dinhkem != null) {
                    $existingFiles = json_decode($model->file_dinhkem, true);
                    if (is_array($existingFiles)) {
                        $file = $existingFiles;
                    }
                }
                $relativePath = 'uploads/suco/' . $model->id . '/';
                $physicalDir = $this->nasBasePath . str_replace('/', '\\', $relativePath);

                if (!is_dir($physicalDir)) {
                    mkdir($physicalDir, 0777, true);
                }

                foreach($filedinhkem->fileupload as $i => $item){
                    if(strpos($item->name, "'") !== false){
                        $item->name = str_replace("'","_",$item->name);
                    }
                    
                    $fileName = $item->baseName . '.' . $item->extension;
                    $physicalPath = $physicalDir . $fileName;
                    $dbPath = $relativePath . $fileName;

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
            'model' => $model,
            'filedinhkem' => $filedinhkem,
            'categories' => CategoriesService::getCategories(),
        ]);
    }

    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $model->status = 0;

        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Xóa sự cố #" . $id,
                    'content' => $this->renderAjax('delete', ['model' => $model]),
                    'footer' => Html::button('Đóng', ['class' => 'btn btn-light float-right', 'data-bs-dismiss' => "modal"]) .
                                Html::button('Xóa', ['class' => 'btn btn-danger float-left', 'type' => "submit"])
                ];
            } else if ($request->isPost && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "Xóa sự cố thành công #" . $id,
                    'content' => '<span class="text-success">Xóa thành công</span>',
                    'footer' => Html::button('Close', ['class' => 'btn btn-light float-right', 'data-bs-dismiss' => "modal"])
                ];
            } else {
                return [
                    'title' => "Update #" . $id,
                    'content' => $this->renderAjax('delete', ['model' => $model]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-light float-right', 'data-bs-dismiss' => "modal"]) .
                                Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                ];
            }
        }
    }

    /**
     * ACTION MỚI: Proxy để đọc file từ NAS và trả về Web Browser
     */
    public function actionDownloadFile($path)
    {
        // Xóa các ký tự nguy hiểm đề phòng path traversal
        $cleanPath = str_replace(['..\\', '../', '..'], '', $path);
        
        $fullPath = $this->nasBasePath . str_replace('/', '\\', $cleanPath);

        if (file_exists($fullPath)) {
            // 'inline' => true giúp mở ảnh/pdf trực tiếp trên trình duyệt, false sẽ ép tải về
            return Yii::$app->response->sendFile($fullPath, basename($fullPath), ['inline' => true]);
        }
        
        throw new NotFoundHttpException('File không tồn tại trên hệ thống NAS.');
    }

    protected function findModel($id)
    {
        if (($model = Suco::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
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