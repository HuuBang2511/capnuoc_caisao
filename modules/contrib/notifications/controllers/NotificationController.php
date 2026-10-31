<?php 

namespace app\modules\contrib\notifications\controllers;

use app\modules\contrib\notifications\services\NotificationService;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class NotificationController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionGetUnreadNotifications() {
        $notifications = NotificationService::GetUnreadNotifications();
        $notifications = ArrayHelper::toArray($notifications);
        return $this->asJson($notifications);
    }

    public function actionRead() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $id = $request->post('id');
            $url = NotificationService::Read($id);
            return $this->asJson($url);
        }

        throw new NotFoundHttpException();
    }

    public function actionReadAll() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $readall = NotificationService::ReadAll();
            return $this->asJson($readall);
        }

        throw new NotFoundHttpException();
    }
}