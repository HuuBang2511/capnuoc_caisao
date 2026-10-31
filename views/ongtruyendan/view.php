<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\quanly\models\hocau\Ongtruyendan */
?>
<div class="ongtruyendan-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'geom',
            'objectid',
            'tinh_trang',
            'vatlieu',
            'coong',
            'mavattu',
            'ngaylapdat',
            'congtrinh',
            'dvtk',
            'dvtc',
            'bvhc',
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
            'loaiong_id',
        ],
    ]) ?>

</div>
