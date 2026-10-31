<?php

use yii\helpers\Html;
use kartik\date\DatePicker;
use kartik\form\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;
use kartik\select2\Select2;
use yii\widgets\MaskedInput;
use kartik\depdrop\DepDrop;
use app\widgets\maps\LeafletMapAsset;
use kartik\file\FileInput;

LeafletMapAsset::register($this);

$requestedAction = Yii::$app->requestedAction;
$controller = $requestedAction->controller;
$label = $controller->label;

$actionId = $controller->action->id;
$this->title = Yii::t('app', ($controller->label[$actionId] ?? $actionId) . ' ' . $controller->title);
$this->params['breadcrumbs'][] = ['label' => ($controller->label['search'] ?? 'Search') . ' ' . $controller->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<link rel="stylesheet" href="https://unpkg.com/leaflet.locatecontrol/dist/L.Control.Locate.min.css" />
<script src="https://unpkg.com/leaflet.locatecontrol/dist/L.Control.Locate.min.js"></script>

<?php 
    $file = [];
    $fileConfig = [];
    if($model->file_dinhkem != null){
        // Xử lý an toàn: phòng trường hợp file_dinhkem đã bị decode thành array từ trước
        $file_arr = is_string($model->file_dinhkem) ? json_decode($model->file_dinhkem, true) : $model->file_dinhkem;
        if(is_array($file_arr)){
            foreach($file_arr as $i => $item){
                $file[] = Url::to(['download-file', 'path' => $item]);
                
                // Cấu hình URL gọi AJAX để xóa file
                $fileConfig[] = [
                    'caption' => basename($item),
                    'url' => Url::to(['delete-file', 'id' => $model->id]),
                    'key' => $item,
                ];
            }
        }
    }
?>

<?php $form = ActiveForm::begin([
    'fieldConfig' => [
        'errorOptions' => ['encode' => false],
    ],
]) ?>

<div class="block block-themed">
    <div class="block-header">
        <h3 class="block-title">
            <?= ($model->isNewRecord) ? 'Thêm mới' : 'Cập nhật' ?>
        </h3>
    </div>

    <div class="block-content">

        <div class="row mt-3">
            <div class="col-lg-12">
                <?= $form->field($model, 'ten')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-lg-3">
                <?= $form->field($model, 'masuco')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'mataisan')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'n_phathien')->widget(DatePicker::className(), [
                        'pluginOptions' => [
                            'format' => 'dd/mm/yyyy',
                            'autoclose' => true,
                        ],
                        'options' => ['placeholder' => 'Ngày phát hiện'],
                ]) ?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'n_xuly')->widget(DatePicker::className(), [
                        'pluginOptions' => [
                            'format' => 'dd/mm/yyyy',
                            'autoclose' => true,
                        ],
                        'options' => ['placeholder' => 'Ngày xử lý'],
                ]) ?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'n_hoancong')->widget(DatePicker::className(), [
                        'pluginOptions' => [
                            'format' => 'dd/mm/yyyy',
                            'autoclose' => true,
                        ],
                        'options' => ['placeholder' => 'Ngày hoàn công'],
                ]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3">
                <?= $form->field($model, 'nguyennhansuco_id')->widget(Select2::className(), [
                        'data' => ArrayHelper::map($categories['nguyennhansuco'], 'id', 'ten'),
                        'options' => ['placeholder' => 'Nguyên nhân sự cố' ],
                        'pluginOptions' => ['allowClear' => true],
                ]) ?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'tinhtrangsuco_id')->widget(Select2::className(), [
                        'data' => ArrayHelper::map($categories['tinhtrangsuco'], 'id', 'ten'),
                        'options' => ['placeholder' => 'Tình trạng sự cố' ],
                        'pluginOptions' => ['allowClear' => true],
                ]) ?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'loaisuco_id')->widget(Select2::className(), [
                        'data' => ArrayHelper::map($categories['loaisuco'], 'id', 'ten'),
                        'options' => ['placeholder' => 'Loại sự cố' ],
                        'pluginOptions' => ['allowClear' => true],
                ]) ?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'tinhtrang_id')->widget(Select2::className(), [
                        'data' => ArrayHelper::map($categories['tinhtrang'], 'id', 'ten'),
                        'options' => ['placeholder' => 'Trạng thái' ],
                        'pluginOptions' => ['allowClear' => true],
                ]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <?= $form->field($model, 'vitri')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

         <div class="row">
            <div class="col-lg-12">
                <?= $form->field($model, 'cachxuly')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-12">
                <?= $form->field($model, 'ghichu')->textArea(['rows' => 3]) ?>
            </div>
        </div>

        <div class="row">
            <div class="tab-pane" id="filedinhkem-view">
                <div class="row px-3">
                    <?php if($model->isNewRecord): ?>
                    <div class="col-lg-12">
                        <?= $form->field($filedinhkem, 'fileupload')->widget(FileInput::className(), [
                                    'options'=>['multiple'=>true],
                                    'pluginOptions' => [
                                        'initialPreviewAsData' => true,
                                        'showPreview' => true,
                                        'showCaption' => true,
                                        'showRemove' => true,
                                        'showUpload' => false,
                                    ]
                                ])->label('File đính kèm');
                            ?>
                    </div>
                    <?php else: ?>
                        <?php if($model->file_dinhkem != null): ?>
                        <div class="col-lg-12">
                            <?= $form->field($filedinhkem, 'fileupload')->widget(FileInput::className(), [
                                        'options'=>['multiple'=>true],
                                        'pluginOptions' => [
                                            'overwriteInitial' => false,
                                        'initialPreviewConfig' => $fileConfig,
                                            'initialPreview' => $file,
                                            'initialPreviewAsData' => true,
                                            'initialPreviewFileType' => 'pdf', // Thay đổi linh hoạt nếu cần thiết
                                            'showPreview' => true,
                                            'showCaption' => true,
                                            'showRemove' => true,
                                            'showUpload' => false,
                                        ]
                                    ])->label('File đính kèm');
                            ?>
                        </div>
                        <?php else: ?>
                        <div class="col-lg-12">
                            <?= $form->field($filedinhkem, 'fileupload')->widget(FileInput::className(), [
                                        'options'=>['multiple'=>true],
                                        'pluginOptions' => [
                                            'initialPreviewAsData' => true,
                                            'showPreview' => true,
                                            'showCaption' => true,
                                            'showRemove' => true,
                                            'showUpload' => false,
                                        ]
                                    ])->label('File đính kèm');
                                ?>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-lg-6">
                <?= $form->field($model, 'lat')->input('text', ['id' => 'geox-input']) ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'long')->input('text', ['id' => 'geoy-input']) ?>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-lg-12">
                <div id="map" style="height: 600px"></div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-lg-12 pb-3">
                <?= Html::submitButton('Lưu', ['class' => 'btn btn-primary', 'id' => 'submitButton']) ?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

<script>
var map = L.map('map').setView([
    <?= ($model->lat != null) ? $model->lat : 10.737202088 ?>,
    <?= ($model->long != null) ? $model->long : 106.915000047 ?>
], 16);

var googleMap = L.tileLayer('http://{s}.google.com/vt/lyrs=r&x={x}&y={y}&z={z}', {
    maxZoom: 24,
    subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
}).addTo(map);

var vetinh = L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
    maxZoom: 24,
    subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
});

const baseLayers = {
    "Bản đồ Google": L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        maxZoom: 22,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    }).addTo(map),
    "Ảnh vệ tinh": L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
        maxZoom: 22,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
    }),
};

const overlayers = {
    "Sự cố": L.tileLayer.wms('https://cello.capnuochocaumoi.vn/geoserver/capnuoc_hocau/wms', {
    layers: 'capnuoc_hocau:network_suco',
    format: 'image/png',
    transparent: true,
    maxZoom: 22 
}),
    "Ống phân phối": L.tileLayer.wms('https://cello.capnuochocaumoi.vn/geoserver/capnuoc_hocau/wms', {
        layers: 'capnuoc_hocau:network_ongphanphoi',
        format: 'image/png',
        transparent: true,
        maxZoom: 22
    }),
    "Ống truyền dẫn": L.tileLayer.wms('https://cello.capnuochocaumoi.vn/geoserver/capnuoc_hocau/wms', {
        layers: 'capnuoc_hocau:network_ongtruyendan',
        format: 'image/png',
        transparent: true,
        maxZoom: 22
    }),
    "Ống dân sinh": L.tileLayer.wms('https://cello.capnuochocaumoi.vn/geoserver/capnuoc_hocau/wms', {
        layers: 'capnuoc_hocau:network_ongdansinh',
        format: 'image/png',
        transparent: true,
        maxZoom: 22
    })
}

L.control.layers(baseLayers, overlayers).addTo(map);

var icon = L.icon({
    iconUrl: '<?= Yii::$app->homeUrl ?>images/icons8-map-marker-96.png',
    iconSize: [40, 40],
    iconAnchor: [20, 20],
    popupAnchor: [0, -48],
});

let lastLatLng = null;
let isManualPosition = false;

const marker = new L.marker([<?= ($model->lat != null) ? $model->lat : 10.737202088 ?>,
    <?= ($model->long != null) ? $model->long : 106.915000047 ?>
], {
    'draggable': 'true',
    'icon': icon,
}).addTo(map);

marker.on('dragend', function(event) {
    const position = event.target.getLatLng();
    isManualPosition = true; 
    $('#geoy-input').val(position.lng);
    $('#geox-input').val(position.lat);
    map.panTo(position);
});

const locateControl = L.control.locate({
    position: 'topleft',
    flyTo: true,
    keepCurrentZoomLevel: true,
    drawCircle: false,
    showPopup: false,
    strings: { title: "Định vị vị trí của bạn" },
    icon: 'fa fa-location-arrow',
    locateOptions: { enableHighAccuracy: true, maxZoom: 18, watch: false },
    clickBehavior: { inView: 'stop', outOfView: 'setView', inViewNotFollowing: 'setView' }
}).addTo(map);

setTimeout(() => {
    const btn = document.querySelector('.leaflet-control-locate a');
    if (btn) {
        const handleLocate = function(e) {
            e.preventDefault();
            isManualPosition = false;
            map.locate({ setView: true, maxZoom: 18, enableHighAccuracy: true, watch: false });
        };
        btn.addEventListener('click', handleLocate);
        btn.addEventListener('touchstart', handleLocate);
    }
}, 1000);

map.on("locationfound", function(e) {
    if (isManualPosition) return; 

    const current = L.latLng(e.latitude, e.longitude);
    if (!lastLatLng || current.distanceTo(lastLatLng) > 5) {
        lastLatLng = current;
        $('#geoy-input').val(e.latitude);
        $('#geox-input').val(e.longitude);
        marker.setLatLng(current);
        map.setView(current, 18);
    }

    if (!isManualPosition) {
        const current = L.latLng(e.latitude, e.longitude);
        lastLatLng = current;

        $('#geoy-input').val(e.latitude);
        $('#geox-input').val(e.longitude);
        marker.setLatLng(current);
        map.setView(current, 18);
    }
});
</script>
