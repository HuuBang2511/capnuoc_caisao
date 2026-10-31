<?php

namespace app\modules\contrib\notifications\models;

use Yii;

/**
 * This is the model class for table "notification".
 *
 * @property int $id
 * @property string $subject
 * @property string $read_at
 * @property string $created_at
 * @property string $icon
 * @property string $color
 * @property string $destination_url
 * @property int $destination_user
 * @property string $note
 */
class Notification extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notification';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['read_at', 'created_at'], 'safe'],
            [['destination_user'], 'default', 'value' => null],
            [['destination_user'], 'integer'],
            [['subject', 'icon', 'color', 'destination_url', 'note'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'subject' => Yii::t('app', 'Subject'),
            'read_at' => Yii::t('app', 'Read At'),
            'created_at' => Yii::t('app', 'Created At'),
            'icon' => Yii::t('app', 'Icon'),
            'color' => Yii::t('app', 'Color'),
            'destination_url' => Yii::t('app', 'Destination Url'),
            'destination_user' => Yii::t('app', 'Destination User'),
            'note' => Yii::t('app', 'Note'),
        ];
    }
}
