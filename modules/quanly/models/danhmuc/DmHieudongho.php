<?php

namespace app\modules\quanly\models\danhmuc;

use Yii;

/**
 * This is the model class for table "dm_hieudongho".
 *
 * @property int $id
 * @property string $ten
 * @property string|null $ghichu
 * @property string|null $created_at
 * @property string|null $created_by
 * @property string|null $updated_at
 * @property string|null $updated_by
 *
 * @property NetworkDonghonhamay[] $networkDonghonhamays
 */
class DmHieudongho extends \app\modules\quanly\base\QuanlyBaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dm_hieudongho';
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
     * Gets query for [[NetworkDonghonhamays]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNetworkDonghonhamays()
    {
        return $this->hasMany(NetworkDonghonhamay::className(), ['hieudongho_id' => 'id']);
    }
}
