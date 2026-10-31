<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\quanly\models\hocau\Nhamaynuoc */

?>
<div class="nhamaynuoc-create">
    <?= $this->render('_form', [
        'model' => $model,
        'filedinhkem' => $filedinhkem,
        'categories' => $categories,
    ]) ?>
</div>
