<?php
use yii\helpers\Html;
use kartik\form\ActiveForm;

?>

<div class="nhamaynuoc-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'geom')->textInput() ?>

    <?= $form->field($model, 'objectid')->textInput() ?>

    <?= $form->field($model, 'loai')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shape_leng')->textInput() ?>

    <?= $form->field($model, 'shape_area')->textInput() ?>

    <?= $form->field($model, 'file_dinhkem')->textInput() ?>

    <?= $form->field($model, 'lat')->textInput() ?>

    <?= $form->field($model, 'long')->textInput() ?>

    <?= $form->field($model, 'geojson')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'loainhamay_id')->textInput() ?>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
