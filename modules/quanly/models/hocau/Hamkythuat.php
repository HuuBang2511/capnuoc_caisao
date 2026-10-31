<?php

namespace app\modules\quanly\models\hocau;
use app\modules\quanly\base\QuanlyBaseModel;
use app\modules\quanly\models\danhmuc\DmLoaiham;
use app\modules\quanly\models\danhmuc\DmTinhtrang;
use Yii;

/**
 * This is the model class for table "network_hamkythuat".
 *
 * @property int $id
 * @property string|null $geom
 * @property int|null $objectid_1
 * @property int|null $objectid
 * @property string|null $tinh_trang
 * @property string|null $maham
 * @property string|null $loai_ham
 * @property string|null $kichthuoc
 * @property string|null $vatlieu
 * @property string|null $sonap
 * @property string|null $vitri
 * @property string|null $ngaylapdat
 * @property string|null $dvtk
 * @property string|null $dvtc
 * @property string|null $bvhc
 * @property string|null $ghichu
 * @property float|null $shape_leng
 * @property float|null $shape_area
 * @property string|null $geojson
 * @property int|null $status
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property string|null $file_dinhkem
 * @property int|null $tinhtrang_id
 * @property int|null $loaiham_id
 *
 * @property DmLoaiham $loaiham
 * @property DmTinhtrang $tinhtrang
 */
class Hamkythuat extends QuanlyBaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'network_hamkythuat';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['geom', 'geojson', 'file_dinhkem', 'ten'], 'string'],
            [['objectid_1', 'objectid', 'status', 'created_by', 'updated_by', 'tinhtrang_id', 'loaiham_id'], 'default', 'value' => null],
            [['objectid_1', 'objectid', 'status', 'created_by', 'updated_by', 'tinhtrang_id', 'loaiham_id'], 'integer'],
            [['ngaylapdat', 'created_at', 'updated_at'], 'safe'],
            [['shape_leng', 'shape_area'], 'number'],
            [['tinh_trang', 'kichthuoc', 'vatlieu', 'sonap', 'vitri', 'dvtk', 'dvtc', 'bvhc'], 'string', 'max' => 50],
            [['maham'], 'string', 'max' => 25],
            [['loai_ham'], 'string', 'max' => 10],
            [['ghichu'], 'string', 'max' => 200],
            [['loaiham_id'], 'exist', 'skipOnError' => true, 'targetClass' => DmLoaiham::className(), 'targetAttribute' => ['loaiham_id' => 'id']],
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
            'objectid_1' => 'Objectid 1',
            'objectid' => 'Objectid',
            'tinh_trang' => 'Tình trạng',
            'maham' => 'Mã hầm',
            'loai_ham' => 'Loại hầm',
            'kichthuoc' => 'Kích thước',
            'vatlieu' => 'Vật liệu',
            'sonap' => 'Số nắp',
            'vitri' => 'Vị trí',
            'ngaylapdat' => 'Ngày lắp đặt',
            'dvtk' => 'DVTK',
            'dvtc' => 'DVTC',
            'bvhc' => 'BVHC',
            'ghichu' => 'Ghi chú',
            'shape_leng' => 'Shape Leng',
            'shape_area' => 'Shape Area',
            'geojson' => 'Geojson',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'file_dinhkem' => 'File đính kèm',
            'tinhtrang_id' => 'Tình trạng',
            'loaiham_id' => 'Loại hầm',
            'ten' => 'Tên',
        ];
    }

    /**
     * Gets query for [[Loaiham]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLoaiham()
    {
        return $this->hasOne(DmLoaiham::className(), ['id' => 'loaiham_id']);
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
