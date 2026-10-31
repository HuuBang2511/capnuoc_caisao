<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\quanly\models\hocau\Moinoi */
?>
<div class="moinoi-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'geom',
            'objectid_1',
            'objectid',
            'tinh_trang',
            'loaimoinoi',
            'kichthuoc',
            'x',
            'y',
            'z',
            'vattu',
            'mavitri',
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
            'loaimoinoi_id',
        ],
    ]) ?>

</div>
