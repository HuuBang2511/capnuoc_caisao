<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\quanly\models\hocau\Van */
?>
<div class="van-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'geom',
            'objectid_1',
            'objectid',
            'tinh_trang',
            'mavan',
            'vitri',
            'covan',
            'loaivan',
            'cochiakhoa',
            'sovong',
            'chieudong',
            'dongmo',
            'ngaylapdat',
            'ghichu',
            'lat',
            'long',
            'geojson',
            'status',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'file_dinhkem',
            'tinhtrang_id',
            'loaivan_id',
        ],
    ]) ?>

</div>
