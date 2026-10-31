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

<div class="gd-hamkythuat-view container-fluid mt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 fw-bold mb-1 text-primary"><i class="fa fa-door-open me-2"></i><?= Html::encode($model->ten) ?></h2>
            <div class="text-muted small">Chi tiết thông tin hầm kỹ thuật</div>
        </div>
        <div class="btn-group shadow-sm">
            <?= Html::a('<i class="fa fa-list me-1"></i> Danh sách', Url::to(['index']), ['class' => 'btn btn-outline-secondary']) ?>
            <?= Html::a('<i class="fa fa-edit me-1"></i> Cập nhật', ['update', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-7 order-lg-2 mb-4">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold"><i class="fa fa-map-marked-alt text-success me-2"></i>Sơ đồ mặt bằng GeoJSON</h5>
                    <div class="badge bg-soft-info text-info border border-info px-3">Tự động căn chỉnh</div>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 65vh; min-height: 500px; width: 100%;"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5 order-lg-1 mb-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="fa fa-info-circle text-primary me-2"></i>Thông số đo đạc</h5>
                </div>
                <div class="card-body p-0">
                    <?= DetailView::widget([
                        'model' => $model,
                        'options' => ['class' => 'table table-hover mb-0 detail-view-table'],
                        'attributes' => [
                            [
                                'attribute' => 'ten',
                                'label' => 'Tên gọi',
                                'contentOptions' => ['class' => 'fw-bold'],
                            ],
                            'ma',
                            [
                                'attribute' => 'shape_area',
                                'label' => 'Diện tích (m²)',
                                'value' => function($model) {
                                    return number_format($model->shape_area, 2) . ' m²';
                                }
                            ],
                            [
                                'attribute' => 'shape_leng',
                                'label' => 'Chu vi/Chiều dài (m)',
                                'value' => function($model) {
                                    return number_format($model->shape_leng, 2) . ' m';
                                }
                            ],
                            [
                                'label' => 'Loại hầm/nhà máy',
                                'format' => 'raw',
                                'value' => function($model) {
                                    $text = (isset($model->loainhamay) && $model->loainhamay !== null && isset($model->loainhamay->ten)) ? $model->loainhamay->ten : 'N/A';
                                    return '<span class="badge bg-soft-primary text-primary border border-primary px-3">' . $text . '</span>';
                                }
                            ],
                            'ghichu:ntext',
                        ],
                    ]) ?>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold"><i class="fa fa-file-pdf text-danger me-2"></i>Tài liệu thiết kế</h5>
                </div>
                <div class="card-body py-2">
                    <?php if (!empty($files)) : ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($files as $i => $file) : ?>
                            <?php
                                $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                $dlUrl   = Url::to(['/quanly/hocau/nhamaynuoc/download-file', 'path' => $file['url']]);
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
                        <div class="text-center py-4 text-muted small italic">
                            <i class="fa fa-ghost fa-2x mb-2 opacity-25"></i><br>Không có file đính kèm
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-soft-info { background-color: rgba(13, 202, 240, 0.1); }
    .bg-soft-primary { background-color: rgba(13, 110, 253, 0.1); }
    .detail-view-table th { background-color: #f8f9fa; width: 40%; color: #555; font-size: 0.85rem; text-transform: uppercase; }
    .card { border-radius: 12px; }
    #map { z-index: 1; border-radius: 0 0 12px 12px; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var center = [10.804291919691535, 106.69527258767485];
        var map = L.map('map').setView(center, 14);

        var baseMaps = {
            "Bản đồ Google": L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                maxZoom: 22, subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            }).addTo(map),
            "Ảnh vệ tinh": L.tileLayer('http://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                maxZoom: 22, subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            })
        };

        var overlayers = {
            "Nhà máy nước": L.tileLayer.wms('https://cello.capnuochocaumoi.vn/geoserver/capnuoc_hocau/wms', {
                layers: 'capnuoc_hocau:network_nhamaynuoc',
                format: 'image/png',
                transparent: true,
                maxZoom: 22
            })
        };

        L.control.layers(baseMaps, overlayers).addTo(map);

        <?php if($model->geojson != null) :?>
            try {
                var geoData = {
                    "type": "Feature",
                    "properties": {"name": "<?= $model->ten ?>"},
                    "geometry": <?= $model->geojson ?>
                };

                var geoLayer = L.geoJSON(geoData, {
                    style: function(feature) {
                        return {
                            color: "#e74c3c",
                            weight: 3,
                            fillColor: "#e74c3c",
                            fillOpacity: 0.3
                        };
                    }
                }).addTo(map);

                // Gắn popup thông tin vào Polygon
                geoLayer.bindPopup("<strong><?= $model->ten ?></strong><br>Diện tích: <?= number_format($model->shape_area, 2) ?> m²");

                var bounds = geoLayer.getBounds();
                if (bounds.isValid()) {
                    map.fitBounds(bounds, { padding: [50, 50] });
                }
            } catch (e) {
                console.error('Lỗi GeoJSON:', e);
            }
        <?php endif;?>

        // Fix lỗi mảng trắng khi container thay đổi
        setTimeout(function(){ map.invalidateSize(); }, 400);
    });
</script>
