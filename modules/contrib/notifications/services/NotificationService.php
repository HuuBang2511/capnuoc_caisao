<?php

namespace app\modules\contrib\notifications\services;

use app\modules\contrib\notifications\models\Notification;
use DateTime;
use Exception;
use Yii;
use yii\db\Query;

class NotificationService
{
    public static $ICON = [
        'SUCCESS' => 'icon-checkmark2',
        'ERROR' => 'icon-cross3',
        'DEFAULT' => 'icon-cloud'
    ];

    public static $COLOR = [
        'SUCCESS' => 'success',
        'ERROR' => 'danger',
        'DEFAULT' => 'primary'
    ];

    public static $HOME = '/';

    public static $TYPE = [
        'UNREAD' => 1,
        'READED' => 0
    ];
    
    public static function GetNotificationById($id) {
        $userid = Yii::$app->user->id;
        $notification = Notification::find()->where(['and', ['id' => $id], ['destination_user' => $userid]])->one();
        return $notification;
    }

    public static function GetUnreadNotifications() {
        $userid = Yii::$app->user->id;
        $notifications = Notification::find()->where(['and', ['read_at' => NULL], ['destination_user' => $userid]])->orderBy('created_at desc')->all();
        return $notifications;
    }

    public static function GetNotifications() {
        $userid = Yii::$app->user->id;
        $notifications = Notification::find()->where(['destination_user' => $userid])->orderBy('read_at desc, created_at desc')->all();
        return $notifications;
    }

    public static function TimeNow() {
        $time = new DateTime();
        return $time->format('Y-m-d H:i:s');
    }

    public static function Send($subject, $destination_user, $destination_url = null, $icon = null, $color = null, $note = null)
    {
        try {
            $notification = new Notification([
                'subject' => $subject,
                'destination_user' => $destination_user,
                'destination_url' => $destination_url ? $destination_url : self::$HOME,
                'icon' => $icon ? $icon : self::$ICON['DEFAULT'],
                'color' => $color ? $color : self::$COLOR['DEFAULT'],
                'note' => $note
            ]);
    
            if($notification->save()) return $notification->destination_url;
        } catch(Exception $e) {
            return false;
        }
        
        return false;
    }

    public static function Read($id)
    {
        try {
            $notification = self::GetNotificationById($id);
            $notification->read_at = self::TimeNow();
            if($notification->save()) return $notification->destination_url;
        } catch(Exception $e) {
            return false;
        }

        return false;
    }

    public static function Unread($id)
    {
        try {
            $notification = self::GetNotificationById($id);
            $notification->read_at = NULL;
            if($notification->save()) return $notification->destination_url;
        } catch(Exception $e) {
            return false;
        }
        
        return false;
    }

    public static function ReadAll()
    {
        try {
            $notifications = self::GetUnreadNotifications();
            foreach($notifications as $n) {
                $n->read_at = self::TimeNow();
                $n->save();
            }

            return true;
        } catch(Exception $e) {
            return false;
        }
        
        return false;
    }

    public static function Delete($id) {
        try {
            $notification = self::GetNotificationById($id);
            if($notification->delete()) return true;
        } catch(Exception $e) {
            return false;
        }
        
        return false;
    }
}