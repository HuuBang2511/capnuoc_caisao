<?php

namespace app\modules\quanly\models\hocau;
use app\modules\quanly\base\QuanlyBaseModel;
use app\modules\quanly\models\danhmuc\DmTinhtrang;
use app\modules\quanly\models\danhmuc\DmLoaiong;
use Yii;

/**
 * This is the model class for table "network_ongtruyendan".
 *
 * @property int $id
 * @property string|null $geom
 * @property int|null $objectid
 * @property string|null $tinh_trang
 * @property string|null $vatlieu
 * @property int|null $coong
 * @property string|null $mavattu
 * @property string|null $ngaylapdat
 * @property string|null $congtrinh
 * @property string|null $dvtk
 * @property string|null $dvtc
 * @property string|null $bvhc
 * @property string|null $ghichu
 * @property float|null $shape_leng
 * @property string|null $lat
 * @property string|null $long
 * @property string|null $geojson
 * @property int|null $status
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property string|null $file_dinhkem
 * @property int|null $tinhtrang_id
 * @property int|null $loaiong_id
 *
 * @property DmLoaiong $loaiong
 * @property DmTinhtrang $tinhtrang
 */
class Ongtruyendan extends QuanlyBaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'network_ongtruyendan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['geom', 'lat', 'long', 'geojson', 'file_dinhkem', 'ten', 'ma'], 'string'],
            [['objectid', 'coong', 'status', 'created_by', 'updated_by', 'tinhtrang_id', 'loaiong_id'], 'default', 'value' => null],
            [['objectid', 'coong', 'status', 'created_by', 'updated_by', 'tinhtrang_id', 'loaiong_id'], 'integer'],
            [['ngaylapdat', 'created_at', 'updated_at'], 'safe'],
            [['shape_leng'], 'number'],
            [['tinh_trang', 'vatlieu'], 'string', 'max' => 10],
            [['mavattu'], 'string', 'max' => 25],
            [['congtrinh', 'dvtk', 'dvtc', 'bvhc'], 'string', 'max' => 50],
            [['ghichu'], 'string', 'max' => 200],
            [['loaiong_id'], 'exist', 'skipOnError' => true, 'targetClass' => DmLoaiong::className(), 'targetAttribute' => ['loaiong_id' => 'id']],
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
            'tinh_trang' => 'Tinh Trang',
            'vatlieu' => 'Vật liệu',
            'coong' => 'Cỡ ống',
            'mavattu' => 'Mã vật tư',
            'ngaylapdat' => 'Ngày lắp đặt',
            'congtrinh' => 'Công trình',
            'dvtk' => 'DVTK',
            'dvtc' => 'DVTC',
            'bvhc' => 'BVHC',
            'ghichu' => 'Ghi chú',
            'shape_leng' => 'Shape Leng',
            'lat' => 'Lat',
            'long' => 'Long',
            'geojson' => 'Geojson',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'file_dinhkem' => 'File đinh kèm',
            'tinhtrang_id' => 'Tình trạng',
            'loaiong_id' => 'Loại ống',
            'ten' => 'Tên',
            'ma' => 'Mã ống',
        ];
    }

    /**
     * Gets query for [[Loaiong]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLoaiong()
    {
        return $this->hasOne(DmLoaiong::className(), ['id' => 'loaiong_id']);
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
