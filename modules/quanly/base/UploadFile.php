<?php

namespace app\modules\quanly\base;

use yii\base\Model;

class UploadFile extends Model
{
    public $fileupload;
    public $imageupload;
    public $type;

    public function rules()
    {
        return [
            [['fileupload'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10, 'maxSize' => 1024 * 1024 * 500],
            [['imageupload'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png,jpg,jpeg', 'maxFiles' => 10, 'maxSize' => 1024 * 1024 * 50],
            [['type'], 'string'],
        ];
    }

    public function uploadFile($path, $file)
    {
        if (file_exists($path)) {
            $file->saveAs($path . $file->baseName . '.' . $file->extension);
            return true;
        } else {
            mkdir($path, 0777, true);
            $file->saveAs($path . $file->baseName . '.' . $file->extension);
            return true;
        }
    }
}