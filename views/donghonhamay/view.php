<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\quanly\models\hocau\Donghonhamay */
?>
<div class="donghonhamay-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'geom',
            'objectid',
            'tinh_trang',
            'madongho',
            'vitri',
            'co',
            'hieu',
            'mavattu',
            'sothan',
            'khuvuc',
            'ghichu',
            'ngaylapdat',
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
            'hieudongho_id',
        ],
    ]) ?>

</div>
