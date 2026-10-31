<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\quanly\models\hocau\Suco */
?>
<div class="suco-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'geom',
            'objectid_1',
            'objectid',
            'tinh_trang',
            'masuco',
            'vitri',
            'loai',
            'n_phathien',
            'd_phathien',
            'n_xuly',
            'd_xuly',
            'n_hoancong',
            'nguyennhan',
            'cachxuly',
            'mataisan',
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
            'loaisuco_id',
            'nguyennhansuco_id',
            'tinhtrangsuco_id',
            'tinhtrang_id',
        ],
    ]) ?>

</div>
