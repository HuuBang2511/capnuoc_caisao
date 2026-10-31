<?php

namespace app\modules\quanly\models\hocau;
use app\modules\quanly\base\QuanlyBaseModel;
use app\modules\quanly\models\danhmuc\DmTinhtrang;
use Yii;

/**
 * This is the model class for table "network_ongdansinh".
 *
 * @property int $id
 * @property string|null $geom
 * @property int|null $id1
 * @property int|null $status
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property string|null $lat
 * @property string|null $long
 * @property string|null $geojson
 * @property string|null $file_dinhkem
 * @property string|null $vatlieu
 * @property int|null $coong
 * @property int|null $tinhtrang_id
 */
class Ongdansinh extends QuanlyBaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'network_ongdansinh';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['geom', 'lat', 'long', 'geojson', 'file_dinhkem', 'vatlieu', 'ten', 'ma'], 'string'],
            [['id1', 'status', 'created_by', 'updated_by', 'coong', 'tinhtrang_id'], 'default', 'value' => null],
            [['id1', 'status', 'created_by', 'updated_by', 'coong', 'tinhtrang_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'geom' => 'Geom',
            'id1' => 'Id1',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'lat' => 'Lat',
            'long' => 'Long',
            'geojson' => 'Geojson',
            'file_dinhkem' => 'File đính kèm',
            'vatlieu' => 'Vật liệu',
            'coong' => 'Cỡ ống',
            'tinhtrang_id' => 'Tình trạng',
            'ten' => 'Tên',
            'ma' => 'Mã ống',
        ];
    }

    public function getTinhtrang()
    {
        return $this->hasOne(DmTinhtrang::className(), ['id' => 'tinhtrang_id']);
    }
}
