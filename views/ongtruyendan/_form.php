<?php
use yii\helpers\Html;
use kartik\form\ActiveForm;

?>

<div class="ongtruyendan-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'geom')->textInput() ?>

    <?= $form->field($model, 'objectid')->textInput() ?>

    <?= $form->field($model, 'tinh_trang')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vatlieu')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'coong')->textInput() ?>

    <?= $form->field($model, 'mavattu')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ngaylapdat')->textInput() ?>

    <?= $form->field($model, 'congtrinh')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'dvtk')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'dvtc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bvhc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ghichu')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shape_leng')->textInput() ?>

    <?= $form->field($model, 'lat')->textInput() ?>

    <?= $form->field($model, 'long')->textInput() ?>

    <?= $form->field($model, 'geojson')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'file_dinhkem')->textInput() ?>

    <?= $form->field($model, 'tinhtrang_id')->textInput() ?>

    <?= $form->field($model, 'loaiong_id')->textInput() ?>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
