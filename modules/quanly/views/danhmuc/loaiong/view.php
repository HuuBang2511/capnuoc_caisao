<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\quanly\models\danhmuc\DmLoaiong */
?>
<div class="dm-loaiong-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
    
            'ten',
    
        ],
    ]) ?>

</div>
