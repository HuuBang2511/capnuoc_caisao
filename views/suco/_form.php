<?php
use yii\helpers\Html;
use kartik\form\ActiveForm;

?>

<div class="suco-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'geom')->textInput() ?>

    <?= $form->field($model, 'objectid_1')->textInput() ?>

    <?= $form->field($model, 'objectid')->textInput() ?>

    <?= $form->field($model, 'tinh_trang')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'masuco')->textInput() ?>

    <?= $form->field($model, 'vitri')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'loai')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'n_phathien')->textInput() ?>

    <?= $form->field($model, 'd_phathien')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'n_xuly')->textInput() ?>

    <?= $form->field($model, 'd_xuly')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'n_hoancong')->textInput() ?>

    <?= $form->field($model, 'nguyennhan')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cachxuly')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mataisan')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ghichu')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lat')->textInput() ?>

    <?= $form->field($model, 'long')->textInput() ?>

    <?= $form->field($model, 'geojson')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'file_dinhkem')->textInput() ?>

    <?= $form->field($model, 'loaisuco_id')->textInput() ?>

    <?= $form->field($model, 'nguyennhansuco_id')->textInput() ?>

    <?= $form->field($model, 'tinhtrangsuco_id')->textInput() ?>

    <?= $form->field($model, 'tinhtrang_id')->textInput() ?>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
