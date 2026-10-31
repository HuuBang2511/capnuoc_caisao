<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\quanly\models\danhmuc\DmSucoTinhtrang */
?>
<div class="dm-suco-tinhtrang-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
    
            'ten',
            'ghichu:ntext',
            
        ],
    ]) ?>

</div>
