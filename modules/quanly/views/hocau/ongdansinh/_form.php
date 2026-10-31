<?php

use app\modules\APPConfig;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\form\ActiveForm;
use app\widgets\maps\LeafletMapAsset;
use app\widgets\maps\plugins\leaflet_measure\LeafletMeasureAsset;
use app\widgets\maps\LeafletDrawAsset;
use yii\helpers\Json;
use kartik\file\FileInput;

LeafletMapAsset::register($this);
LeafletDrawAsset::register($this);
LeafletMeasureAsset::register($this);

/**
 * @var yii\web\View $this
 * @var app\modules\quanly\models\capnuocgd\GdOngcai $model
 * @var kartik\form\ActiveForm $form
 */

// 1. Gán biến gọn gàng hơn
$controller = Yii::$app->controller;
$actionId = $controller->action->id;
$this->title = Yii::t('app', ($controller->label[$actionId] ?? $actionId) . ' ' . $controller->title);
$this->params['breadcrumbs'][] = ['label' => ($controller->label['search'] ?? 'Search') . ' ' . $controller->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// 2. Chuẩn bị dữ liệu GeoJSON để truyền sang JavaScript một cách an toàn
$geojson_data = !empty($model->geojson) ? Json::encode($model->geojson) : 'null';
?>

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

<div class="gd-ongcai-form">
    <div class="block block-themed">
        <div class="block-header">
            <h2 class="block-title"><?= Html::encode($this->title) ?></h2>
        </div>
        <div class="block-content">
            <?php $form = ActiveForm::begin(); ?>
            <div class="row pb-2">
                <div class="col-lg-12">
                    <!-- 3. Giao diện bản đồ và trường ẩn -->
                    <div id="map" style="height: 500px; width: 100%; border-radius: 4px; z-index: 1;"></div>
                    <?= $form->field($model, 'geojson')->hiddenInput(['id' => 'geojson'])->label(false) ?>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-lg-12">
                    <?= $form->field($model, 'ten')->textInput(['maxlength' => true]) ?>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-lg-12">
                    <?= $form->field($model, 'ma')->textInput(['maxlength' => true]) ?>
                </div>
            </div>

            <div class="row">
                
                <div class="col-lg-3">
                    <?= $form->field($model, 'vatlieu')->textInput() ?>
                </div>
                <div class="col-lg-3">
                    <?= $form->field($model, 'coong')->textInput() ?>
                </div>
                <div class="col-lg-3">
                    <?= $form->field($model, 'tinhtrang_id')->widget(Select2::className(), [
                        'data' => ArrayHelper::map($categories['tinhtrang'], 'id', 'ten'),
                        'options' => ['placeholder' => 'Tình trạng' ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]) ?>
                </div>
            </div>

            <div class="row">
                <div class="tab-pane" id="filedinhkem-view">
                    <div class="row px-3">
                        <?php if($model->isNewRecord): ?>
                        <div class="col-lg-12">
                            <?= $form->field($filedinhkem, 'fileupload')->widget(FileInput::className(), [
                                        'options'=>[
                                            'multiple'=>true
                                        ],
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
                                        'options'=>[
                                            'multiple'=>true
                                        ],
                                        'pluginOptions' => [
                                            'overwriteInitial' => false,
                                        'initialPreviewConfig' => $fileConfig,
                                            'initialPreview' => $file,
                                            'initialPreviewAsData' => true,
                                            'initialPreviewFileType' => 'pdf',
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
                                        'options'=>[
                                            'multiple'=>true
                                        ],
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
                <div class="form-group col-lg-12">
                    <?= Html::submitButton('Lưu', ['class' => 'btn btn-primary float-left']) ?>
                    <?= Html::button('Quay lại', ['class' => 'btn btn-light float-right', 'type' => 'button', 'onclick' => "history.back()"]) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
// 4. Quản lý toàn bộ script bằng registerJs
$js = <<<JS
// --- KHỞI TẠO BẢN ĐỒ VÀ CÁC LỚP NỀN ---
const defaultCenter = [10.737202088, 106.915000047];
const map = L.map('map').setView(defaultCenter, 16);

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
    "Ống dân sinh": L.tileLayer.wms('https://cello.capnuochocaumoi.vn/geoserver/capnuoc_hocau/wms', {
    layers: 'capnuoc_hocau:network_ongdansinh',
    format: 'image/png',
    transparent: true,
    maxZoom: 22 // Đặt maxZoom là 22
})
}

// --- THÊM CÁC LỚP WMS MẶC ĐỊNH ---
const wmsDefaultOptions = {
    format: 'image/png',
    transparent: true,
    maxZoom: 22,
};


// Chỉ thêm bảng điều khiển cho lớp nền
L.control.layers(baseLayers, overlayers).addTo(map);


// --- CẤU HÌNH LEAFLET.DRAW ---

// Lớp để chứa các đối tượng được vẽ/chỉnh sửa
const editableLayers = new L.FeatureGroup();
map.addLayer(editableLayers);

// Cấu hình công cụ vẽ cho Polyline
const drawOptions = {
    position: 'topleft',
    draw: {
        polyline: {
            shapeOptions: { color: '#f357a1', weight: 10 }
        },
        polygon: false,
        rectangle: false,
        circle: false,
        marker: false,
        circlemarker: false
    },
    edit: {
        featureGroup: editableLayers,
        remove: true
    }
};

const drawControl = new L.Control.Draw(drawOptions);
map.addControl(drawControl);

// Hàm cập nhật trường input ẩn với dữ liệu GeoJSON
function updateGeoJsonInput() {
    const layers = editableLayers.getLayers();
    if (layers.length === 0) {
        $('#geojson').val('');
        return;
    }
    // Chỉ lấy geometry của đối tượng đầu tiên (giả sử chỉ vẽ 1 đường)
    const geoJsonGeometry = layers[0].toGeoJSON().geometry;
    $('#geojson').val(JSON.stringify(geoJsonGeometry));
}

// Tải dữ liệu GeoJSON đã có khi mở form
let initialGeoJson = {$geojson_data};
if (initialGeoJson) {
    // Dữ liệu từ server là một chuỗi JSON, cần parse lại
    const geoJsonFeature = {
        "type": "Feature",
        "properties": {},
        "geometry": JSON.parse(initialGeoJson)
    };
    
    L.geoJSON(geoJsonFeature, {
        onEachFeature: function (feature, layer) {
            editableLayers.addLayer(layer);
        }
    });

    // Zoom tới vùng đã vẽ
    if (editableLayers.getLayers().length > 0) {
        map.fitBounds(editableLayers.getBounds());
    }
}


// Xử lý sự kiện khi một đối tượng được VẼ XONG
map.on(L.Draw.Event.CREATED, function (e) {
    const layer = e.layer;
    // Xóa các đối tượng cũ đi để đảm bảo chỉ có 1 đối tượng trên bản đồ
    editableLayers.clearLayers();
    editableLayers.addLayer(layer);
    updateGeoJsonInput();
});

// Xử lý sự kiện khi một đối tượng được CHỈNH SỬA
map.on(L.Draw.Event.EDITED, function (e) {
    updateGeoJsonInput();
});

// Xử lý sự kiện khi một đối tượng được XÓA
map.on(L.Draw.Event.DELETED, function (e) {
    updateGeoJsonInput();
});

JS;
$this->registerJs($js, \yii\web\View::POS_READY);
?>
