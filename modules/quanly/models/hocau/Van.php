<?php

namespace app\modules\quanly\models\hocau;
use app\modules\quanly\base\QuanlyBaseModel;
use app\modules\quanly\models\danhmuc\DmTinhtrang;
use app\modules\quanly\models\danhmuc\DmLoaivan;
use Yii;

/**
 * This is the model class for table "network_van".
 *
 * @property int $id
 * @property string|null $geom
 * @property int|null $objectid_1
 * @property int|null $objectid
 * @property string|null $tinh_trang
 * @property string|null $mavan
 * @property string|null $vitri
 * @property int|null $covan
 * @property string|null $loaivan
 * @property int|null $cochiakhoa
 * @property int|null $sovong
 * @property string|null $chieudong
 * @property string|null $dongmo
 * @property string|null $ngaylapdat
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
 * @property int|null $loaivan_id
 *
 * @property DmLoaivan $loaivan0
 * @property DmTinhtrang $tinhtrang
 */
class Van extends QuanlyBaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'network_van';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['geom', 'lat', 'long', 'geojson', 'file_dinhkem', 'ten'], 'string'],
            [['objectid_1', 'objectid', 'covan', 'cochiakhoa', 'sovong', 'status', 'created_by', 'updated_by', 'tinhtrang_id', 'loaivan_id'], 'default', 'value' => null],
            [['objectid_1', 'objectid', 'covan', 'cochiakhoa', 'sovong', 'status', 'created_by', 'updated_by', 'tinhtrang_id', 'loaivan_id'], 'integer'],
            [['ngaylapdat', 'created_at', 'updated_at'], 'safe'],
            [['tinh_trang'], 'string', 'max' => 10],
            [['mavan'], 'string', 'max' => 25],
            [['vitri', 'loai_van'], 'string', 'max' => 50],
            [['chieudong', 'dongmo'], 'string', 'max' => 1],
            [['ghichu'], 'string', 'max' => 200],
            [['loaivan_id'], 'exist', 'skipOnError' => true, 'targetClass' => DmLoaivan::className(), 'targetAttribute' => ['loaivan_id' => 'id']],
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
            'mavan' => 'Mã van',
            'vitri' => 'Vị trí',
            'covan' => 'Cỡ van',
            'loai_van' => 'Loaivan',
            'cochiakhoa' => 'Cỡ chìa khóa',
            'sovong' => 'Số vòng',
            'chieudong' => 'Chiều đóng',
            'dongmo' => 'Đóng mở',
            'ngaylapdat' => 'Ngày lắp đặt',
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
            'loaivan_id' => 'Loại van',
            'ten' => 'Tên',

        ];
    }

    /**
     * Gets query for [[Loaivan0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLoaivan()
    {
        return $this->hasOne(DmLoaivan::className(), ['id' => 'loaivan_id']);
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
