<?php

use yii\helpers\Url;
use yii\widgets\DetailView;
use app\widgets\maps\LeafletMapAsset;
use yii\helpers\Html;

LeafletMapAsset::register($this);

$requestedAction = Yii::$app->requestedAction;
$controller = $requestedAction->controller;
$label = $controller->label;

$this->title = Yii::t('app', $label[$requestedAction->id] . ' ' . $controller->title);
$this->params['breadcrumbs'][] = ['label' => $label['search'] . ' ' . $controller->title, 'url' => $controller->url];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="gd-ongcai-view container-fluid mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 fw-bold mb-0 text-primary">
            <i class="fa fa-layer-group me-2"></i><?= Html::encode($this->title) ?>
        </h2>
        <div class="btn-group shadow-sm">
            <?= Html::a('<i class="fa fa-list me-1"></i> Danh sách', Url::to(['index']), ['class' => 'btn btn-outline-secondary']) ?>
            <?= Html::a('<i class="fa fa-edit me-1"></i> Cập nhật', ['update', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold"><i class="fa fa-map-marked-alt text-success me-2"></i>Bản đồ thực địa</h5>
                    <span class="badge bg-soft-info text-info border border-info px-2">GeoJSON Layer</span>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 60vh; min-height: 500px; width: 100%;"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="fa fa-info-circle text-primary me-2"></i>Thông tin chung</h5>
                </div>
                <div class="card-body p-0 text-break">
                    <?= DetailView::widget([
                        'model' => $model,
                        'options' => ['class' => 'table table-hover mb-0 detail-view-table'],
                        'attributes' => [
                            'ten',
                            'ma',
                            [
                                'attribute' => 'hanhlang',
                                'label' => 'Hành lang',
                                'captionOptions' => ['style' => 'width: 35%'],
                            ],
                            [
                                'label' => 'Tình trạng',
                                'format' => 'raw',
                                'value' => function($model) {
                                    $text = ($model->tinhtrang_id != null) ? $model->tinhtrang->ten : 'N/A';
                                    return '<span class="badge bg-soft-primary text-primary px-3 border border-primary">' . $text . '</span>';
                                }
                            ],
                            'ghichu:ntext',
                        ],
                    ]) ?>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold"><i class="fa fa-paperclip text-warning me-2"></i>File đính kèm</h5>
                </div>
                <div class="card-body py-2">
                    <?php if (!empty($files)) : ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($files as $i => $file) : ?>
                            <?php
                                $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                $dlUrl   = Url::to(['/quanly/hocau/hanglangantoan/download-file', 'path' => $file['url']]);
                            ?>
                            <?php if ($isImage) : ?>
                                <a href="<?= $dlUrl ?>" target="_blank" class="d-inline-block me-2 mb-2">
                                    <img src="<?= $dlUrl ?>"
                                         alt="<?= Html::encode($file['name']) ?>"
                                         style="width:80px;height:80px;object-fit:cover;border-radius:6px;border:1px solid #e2e8f0;"
                                         title="<?= Html::encode($file['name']) ?>" />
                                </a>
                            <?php else : ?>
                                <a href="<?= $dlUrl ?>" target="_blank"
                                   class="list-group-item list-group-item-action border-0 px-0 d-flex align-items-center">
                                    <div class="bg-light rounded p-2 me-3 text-center" style="width:40px;">
                                        <?php
                                            if (in_array($ext, ['pdf']))            echo '<i class="fa fa-file-pdf text-danger"></i>';
                                            elseif (in_array($ext, ['doc','docx'])) echo '<i class="fa fa-file-word text-primary"></i>';
                                            elseif (in_array($ext, ['xls','xlsx'])) echo '<i class="fa fa-file-excel text-success"></i>';
                                            elseif (in_array($ext, ['zip','rar','7z'])) echo '<i class="fa fa-file-archive text-warning"></i>';
                                            elseif (in_array($ext, ['dwg']))        echo '<i class="fa fa-file-code text-info"></i>';
                                            else                                    echo '<i class="fa fa-file text-muted"></i>';
                                        ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="small fw-medium text-dark"><?= Html::encode($file['name']) ?></div>
                                    </div>
                                    <i class="fa fa-external-link-alt text-muted small"></i>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="text-center py-4 text-muted small opacity-75">
                            <i class="fa fa-folder-open fa-2x mb-2"></i><br>Không có tài liệu
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-soft-primary { background-color: rgba(13, 110, 253, 0.1); }
    .bg-soft-info { background-color: rgba(13, 202, 240, 0.1); }
    .detail-view-table th { background-color: #f9f9fb; font-size: 0.85rem; text-transform: uppercase; color: #666; vertical-align: middle; }
    .card { border-radius: 12px; }
    #map { z-index: 1; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tọa độ trung tâm mặc định
        var center = [10.737202088, 106.915000047];

        // Khởi tạo Map
        var map = L.map('map').setView(center, 14);

        // Các lớp nền
        var baseMaps = {
            "Bản đồ Google": L.tileLayer('http://{s}.google.com/vt/lyrs=r&x={x}&y={y}&z={z}', {
                maxZoom: 22,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            }).addTo(map),
            "Ảnh vệ tinh": L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
                maxZoom: 22,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            }),
        };

        // Lớp WMS
        var overlayers = {
            "Hành lang an toàn": L.tileLayer.wms('https://cello.capnuochocaumoi.vn/geoserver/capnuoc_hocau/wms', {
                layers: 'capnuoc_hocau:network_hanglangantoan',
                format: 'image/png',
                transparent: true,
                maxZoom: 22
            })
        };

        L.control.layers(baseMaps, overlayers).addTo(map);

        <?php if($model->geojson != null) :?>
            var geoData = {
                "type": "Feature",
                "properties": {"name": "Hành lang mục tiêu"},
                "geometry": <?= $model->geojson ?>
            };

            var geoLayer = L.geoJSON(geoData, {
                style: function (feature) {
                    return {
                        color: "#ff4d4d",
                        weight: 3,
                        opacity: 0.8,
                        fillOpacity: 0.2
                    };
                }
            }).addTo(map);

            // Zoom vào vùng GeoJSON
            var bounds = geoLayer.getBounds();
            map.fitBounds(bounds, { padding: [30, 30] });
        <?php endif;?>
        
        // Fix lỗi render nếu map bị mờ
        setTimeout(function(){ map.invalidateSize(); }, 400);
    });
</script>
