<?php

namespace app\modules\quanly\models\danhmuc;

use Yii;

/**
 * This is the model class for table "dm_loaiong".
 *
 * @property int $id
 * @property string|null $ten
 * @property int|null $status
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property NetworkOngphanphoi[] $networkOngphanphois
 * @property NetworkOngtruyendan[] $networkOngtruyendans
 */
class DmLoaiong extends \app\modules\quanly\base\QuanlyBaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dm_loaiong';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ten'], 'string'],
            [['status', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['status', 'created_by', 'updated_by'], 'integer'],
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
            'ten' => 'Tên',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[NetworkOngphanphois]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNetworkOngphanphois()
    {
        return $this->hasMany(NetworkOngphanphoi::className(), ['loaiong_id' => 'id']);
    }

    /**
     * Gets query for [[NetworkOngtruyendans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNetworkOngtruyendans()
    {
        return $this->hasMany(NetworkOngtruyendan::className(), ['loaiong_id' => 'id']);
    }
}
