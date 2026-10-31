<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\quanly\models\danhmuc\DmSucoLoai */
?>
<div class="dm-suco-loai-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
    
            'ten',
            'ghichu:ntext',
            
        ],
    ]) ?>

</div>
