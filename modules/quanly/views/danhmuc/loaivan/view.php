<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\quanly\models\danhmuc\DmLoaivan */
?>
<div class="dm-loaivan-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
    
            'ten',
            'ghichu:ntext',
            
        ],
    ]) ?>

</div>
