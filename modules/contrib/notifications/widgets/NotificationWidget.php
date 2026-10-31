<?php
 namespace app\modules\contrib\notifications\widgets;

use yii\base\Widget;

class NotificationWidget extends Widget {
     public function run() {
         return $this->render('notificationWidget');
     }
 }