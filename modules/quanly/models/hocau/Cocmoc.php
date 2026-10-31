<?php

namespace app\modules\quanly\models\hocau;
use app\modules\quanly\base\QuanlyBaseModel;
use app\modules\quanly\models\danhmuc\DmLoaiham;
use app\modules\quanly\models\danhmuc\DmTinhtrang;
use Yii;

/**
 * This is the model class for table "network_cocmoc".
 *
 * @property int $id
 * @property string|null $geom
 * @property int|null $objectid
 * @property string|null $loai
 * @property string|null $vitri
 * @property int|null $status
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int|null $created_by
 * @property int|null $update_by
 * @property string|null $geojson
 * @property string|null $lat
 * @property string|null $long
 * @property string|null $file_dinhkem
 * @property int|null $tinhtrang_id
 *
 * @property DmTinhtrang $tinhtrang
 */
class Cocmoc extends QuanlyBaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'network_cocmoc';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['geom', 'geojson', 'lat', 'long', 'file_dinhkem', 'ten', 'ma'], 'string'],
            [['objectid', 'status', 'created_by', 'update_by', 'tinhtrang_id'], 'default', 'value' => null],
            [['objectid', 'status', 'created_by', 'update_by', 'tinhtrang_id'], 'integer'],
            [['created_at', 'updated_at', 'ghichu'], 'safe'],
            [['loai', 'vitri'], 'string', 'max' => 50],
            [['tinhtrang_id'], 'exist', 'skipOnError' => true, 'targetClass' => DmTinhtrang::className(), 'targetAttribute' => ['tinhtrang_id' => 'id']],
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
            'objectid' => 'Objectid',
            'loai' => 'Loại',
            'vitri' => 'Vị trí',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'update_by' => 'Update By',
            'geojson' => 'Geojson',
            'lat' => 'Lat',
            'long' => 'Long',
            'file_dinhkem' => 'File đính kèm',
            'tinhtrang_id' => 'Tình trạng',
            'ten' => 'Tên',
            'ma' => 'Mã cọc mốc',
            'ghichu' => 'Ghi chú',
        ];
    }

    /**
     * Gets query for [[Tinhtrang]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTinhtrang()
    {
        return $this->hasOne(DmTinhtrang::className(), ['id' => 'tinhtrang_id']);
    }
}
