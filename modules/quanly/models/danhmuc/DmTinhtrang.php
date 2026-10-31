<?php

namespace app\modules\quanly\models\danhmuc;

use Yii;

/**
 * This is the model class for table "dm_tinhtrang".
 *
 * @property int $id
 * @property string $ten
 * @property string|null $ghichu
 * @property string|null $created_at
 * @property string|null $created_by
 * @property string|null $updated_at
 * @property string|null $updated_by
 *
 * @property NetworkCocmoc[] $networkCocmocs
 * @property NetworkDonghonhamay[] $networkDonghonhamays
 * @property NetworkDonghotong[] $networkDonghotongs
 * @property NetworkGhichu[] $networkGhichus
 * @property NetworkHamkythuat[] $networkHamkythuats
 * @property NetworkHanglangantoan[] $networkHanglangantoans
 * @property NetworkMoinoi[] $networkMoinois
 * @property NetworkOngphanphoi[] $networkOngphanphois
 * @property NetworkOngtruyendan[] $networkOngtruyendans
 * @property NetworkSuco[] $networkSucos
 * @property NetworkVan[] $networkVans
 */
class DmTinhtrang extends \app\modules\quanly\base\QuanlyBaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dm_tinhtrang';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ten'], 'required'],
            [['ghichu'], 'string'],
            [['created_at', 'updated_at', 'status'], 'safe'],
            [['ten'], 'string', 'max' => 255],
            [['created_by', 'updated_by'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ten' => 'Tên',
            'ghichu' => 'Ghi chú',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[NetworkCocmocs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNetworkCocmocs()
    {
        return $this->hasMany(NetworkCocmoc::className(), ['tinhtrang_id' => 'id']);
    }

    /**
     * Gets query for [[NetworkDonghonhamays]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNetworkDonghonhamays()
    {
        return $this->hasMany(NetworkDonghonhamay::className(), ['tinhtrang_id' => 'id']);
    }

    /**
     * Gets query for [[NetworkDonghotongs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNetworkDonghotongs()
    {
        return $this->hasMany(NetworkDonghotong::className(), ['tinhtrang_id' => 'id']);
    }

    /**
     * Gets query for [[NetworkGhichus]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNetworkGhichus()
    {
        return $this->hasMany(NetworkGhichu::className(), ['tinhtrang_id' => 'id']);
    }

    /**
     * Gets query for [[NetworkHamkythuats]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNetworkHamkythuats()
    {
        return $this->hasMany(NetworkHamkythuat::className(), ['tinhtrang_id' => 'id']);
    }

    /**
     * Gets query for [[NetworkHanglangantoans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNetworkHanglangantoans()
    {
        return $this->hasMany(NetworkHanglangantoan::className(), ['tinhtrang_id' => 'id']);
    }

    /**
     * Gets query for [[NetworkMoinois]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNetworkMoinois()
    {
        return $this->hasMany(NetworkMoinoi::className(), ['tinhtrang_id' => 'id']);
    }

    /**
     * Gets query for [[NetworkOngphanphois]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNetworkOngphanphois()
    {
        return $this->hasMany(NetworkOngphanphoi::className(), ['tinhtrang_id' => 'id']);
    }

    /**
     * Gets query for [[NetworkOngtruyendans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNetworkOngtruyendans()
    {
        return $this->hasMany(NetworkOngtruyendan::className(), ['tinhtrang_id' => 'id']);
    }

    /**
     * Gets query for [[NetworkSucos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNetworkSucos()
    {
        return $this->hasMany(NetworkSuco::className(), ['tinhtrang_id' => 'id']);
    }

    /**
     * Gets query for [[NetworkVans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNetworkVans()
    {
        return $this->hasMany(NetworkVan::className(), ['tinhtrang_id' => 'id']);
    }
}
