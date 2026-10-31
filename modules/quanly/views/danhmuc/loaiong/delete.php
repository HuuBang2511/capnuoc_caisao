<?php
use yii\helpers\Html;
use kartik\form\ActiveForm;

?>

<div class="dm-loaiong-form">

    <?php $form = ActiveForm::begin(); ?>

    <h4>Xóa danh mục "<?= $model->ten ?>"</h4>

    <?php ActiveForm::end(); ?>

</div>

