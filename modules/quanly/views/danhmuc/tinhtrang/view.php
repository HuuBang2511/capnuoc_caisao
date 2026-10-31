<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\quanly\models\danhmuc\DmTinhtrang */
?>
<div class="dm-tinhtrang-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
    
            'ten',
            'ghichu:ntext',
            
        ],
    ]) ?>

</div>
