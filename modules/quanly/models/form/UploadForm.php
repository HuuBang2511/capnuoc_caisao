<?php


namespace app\modules\quanly\models\form;


use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $file;

    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xlsx, xls'],
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $this->file->saveAs('resources/uploads/' . $this->file->baseName . '.' . $this->file->extension);
            return 'resources/uploads/' . $this->file->baseName . '.' . $this->file->extension;
        } else {
            return false;
        }
    }
}