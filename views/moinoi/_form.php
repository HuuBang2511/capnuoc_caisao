<?php
use yii\helpers\Html;
use kartik\form\ActiveForm;

?>

<div class="moinoi-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'geom')->textInput() ?>

    <?= $form->field($model, 'objectid_1')->textInput() ?>

    <?= $form->field($model, 'objectid')->textInput() ?>

    <?= $form->field($model, 'tinh_trang')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'loaimoinoi')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'kichthuoc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'x')->textInput() ?>

    <?= $form->field($model, 'y')->textInput() ?>

    <?= $form->field($model, 'z')->textInput() ?>

    <?= $form->field($model, 'vattu')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mavitri')->textInput(['maxlength' => true]) ?>

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

    <?= $form->field($model, 'loaimoinoi_id')->textInput() ?>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
