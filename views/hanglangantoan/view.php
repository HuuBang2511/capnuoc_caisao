<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\quanly\models\hocau\Hanglangantoan */
?>
<div class="hanglangantoan-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'geom',
            'objectid',
            'hanhlang',
            'tinh_trang',
            'ghichu',
            'shape_leng',
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
        ],
    ]) ?>

</div>
