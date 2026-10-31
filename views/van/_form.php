<?php
use yii\helpers\Html;
use kartik\form\ActiveForm;

?>

<div class="van-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'geom')->textInput() ?>

    <?= $form->field($model, 'objectid_1')->textInput() ?>

    <?= $form->field($model, 'objectid')->textInput() ?>

    <?= $form->field($model, 'tinh_trang')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mavan')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vitri')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'covan')->textInput() ?>

    <?= $form->field($model, 'loaivan')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cochiakhoa')->textInput() ?>

    <?= $form->field($model, 'sovong')->textInput() ?>

    <?= $form->field($model, 'chieudong')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'dongmo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ngaylapdat')->textInput() ?>

    <?= $form->field($model, 'ghichu')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lat')->textInput() ?>

    <?= $form->field($model, 'long')->textInput() ?>

    <?= $form->field($model, 'geojson')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'file_dinhkem')->textInput() ?>

    <?= $form->field($model, 'tinhtrang_id')->textInput() ?>

    <?= $form->field($model, 'loaivan_id')->textInput() ?>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
