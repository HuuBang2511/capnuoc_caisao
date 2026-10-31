<?php

namespace app\modules\quanly\models\hocau;
use app\modules\quanly\base\QuanlyBaseModel;
use app\modules\quanly\models\danhmuc\DmHieudongho;
use app\modules\quanly\models\danhmuc\DmTinhtrang;
use Yii;

/**
 * This is the model class for table "network_donghonhamay".
 *
 * @property int $id
 * @property string|null $geom
 * @property float|null $objectid
 * @property string|null $tinh_trang
 * @property string|null $madongho
 * @property string|null $vitri
 * @property float|null $co
 * @property string|null $hieu
 * @property string|null $mavattu
 * @property string|null $sothan
 * @property string|null $khuvuc
 * @property string|null $ghichu
 * @property string|null $ngaylapdat
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
 * @property int|null $hieudongho_id
 *
 * @property DmHieudongho $hieudongho
 * @property DmTinhtrang $tinhtrang
 */
class Donghonhamay extends QuanlyBaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'network_donghonhamay';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['geom', 'lat', 'long', 'geojson', 'file_dinhkem', 'ten'], 'string'],
            [['objectid', 'co'], 'number'],
            [['ngaylapdat', 'created_at', 'updated_at'], 'safe'],
            [['status', 'created_by', 'updated_by', 'tinhtrang_id', 'hieudongho_id'], 'default', 'value' => null],
            [['status', 'created_by', 'updated_by', 'tinhtrang_id', 'hieudongho_id'], 'integer'],
            [['tinh_trang'], 'string', 'max' => 10],
            [['madongho'], 'string', 'max' => 20],
            [['vitri', 'ghichu'], 'string', 'max' => 50],
            [['hieu', 'mavattu', 'sothan', 'khuvuc'], 'string', 'max' => 25],
            [['hieudongho_id'], 'exist', 'skipOnError' => true, 'targetClass' => DmHieudongho::className(), 'targetAttribute' => ['hieudongho_id' => 'id']],
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
            'madongho' => 'Mã đồng hồ',
            'vitri' => 'Vị trí',
            'co' => 'Cỡ',
            'hieu' => 'Hiệu',
            'mavattu' => 'Mã vật tư',
            'sothan' => 'Số thân',
            'khuvuc' => 'Khu vực',
            'ghichu' => 'Ghi chú',
            'ngaylapdat' => 'Ngày lắp đặt',
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
            'hieudongho_id' => 'Hiệu đồng hồ',
            'ten' => 'Tên',
        ];
    }

    /**
     * Gets query for [[Hieudongho]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHieudongho()
    {
        return $this->hasOne(DmHieudongho::className(), ['id' => 'hieudongho_id']);
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
