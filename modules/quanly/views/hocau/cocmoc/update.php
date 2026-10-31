<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\quanly\models\hocau\Cocmoc */
?>
<div class="cocmoc-update">

    <?= $this->render('_form', [
        'model' => $model,
        'filedinhkem' => $filedinhkem,
        'categories' => $categories,
    ]) ?>

</div>
