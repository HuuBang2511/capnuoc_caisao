<?php

namespace app\modules\quanly\models\hocau;
use app\modules\quanly\base\QuanlyBaseModel;
use app\modules\quanly\models\danhmuc\DmLoaimoinoi;
use app\modules\quanly\models\danhmuc\DmTinhtrang;
use Yii;

/**
 * This is the model class for table "network_moinoi".
 *
 * @property int $id
 * @property string|null $geom
 * @property int|null $objectid_1
 * @property int|null $objectid
 * @property string|null $tinh_trang
 * @property string|null $loaimoinoi
 * @property string|null $kichthuoc
 * @property float|null $x
 * @property float|null $y
 * @property float|null $z
 * @property string|null $vattu
 * @property string|null $mavitri
 * @property string|null $ghichu
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
 * @property int|null $loaimoinoi_id
 *
 * @property DmLoaimoinoi $loaimoinoi0
 * @property DmTinhtrang $tinhtrang
 */
class Moinoi extends QuanlyBaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'network_moinoi';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['geom', 'lat', 'long', 'geojson', 'file_dinhkem', 'ten', 'ma'], 'string'],
            [['objectid_1', 'objectid', 'status', 'created_by', 'updated_by', 'tinhtrang_id', 'loaimoinoi_id'], 'default', 'value' => null],
            [['objectid_1', 'objectid', 'status', 'created_by', 'updated_by', 'tinhtrang_id', 'loaimoinoi_id'], 'integer'],
            [['x', 'y', 'z'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['tinh_trang'], 'string', 'max' => 10],
            [['loaimoinoi', 'kichthuoc', 'mavitri'], 'string', 'max' => 50],
            [['vattu'], 'string', 'max' => 20],
            [['ghichu'], 'string', 'max' => 200],
            [['loaimoinoi_id'], 'exist', 'skipOnError' => true, 'targetClass' => DmLoaimoinoi::className(), 'targetAttribute' => ['loaimoinoi_id' => 'id']],
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
            'tinh_trang' => 'Tinh Trang',
            'loaimoinoi' => 'Loaimoinoi',
            'kichthuoc' => 'Kích thước',
            'x' => 'X',
            'y' => 'Y',
            'z' => 'Z',
            'vattu' => 'Vật tư',
            'mavitri' => 'Mã vị trí',
            'ghichu' => 'Ghi chú',
            'lat' => 'Lat',
            'long' => 'Long',
            'geojson' => 'Geojson',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'file_dinhkem' => 'File Dinhkem',
            'tinhtrang_id' => 'Tình trạng',
            'loaimoinoi_id' => 'Loại mối nối',
            'ten' => 'Tên',
            'ma' => 'Mã mối nối',
        ];
    }

    /**
     * Gets query for [[Loaimoinoi0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLoaimoinoi0()
    {
        return $this->hasOne(DmLoaimoinoi::className(), ['id' => 'loaimoinoi_id']);
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
