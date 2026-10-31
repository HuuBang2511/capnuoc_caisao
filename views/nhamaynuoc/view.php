<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\quanly\models\hocau\Nhamaynuoc */
?>
<div class="nhamaynuoc-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'geom',
            'objectid',
            'loai',
            'shape_leng',
            'shape_area',
            'file_dinhkem',
            'lat',
            'long',
            'geojson',
            'status',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'loainhamay_id',
        ],
    ]) ?>

</div>
