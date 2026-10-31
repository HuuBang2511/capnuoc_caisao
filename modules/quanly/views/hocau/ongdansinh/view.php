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
            <i class="fa fa-project-diagram me-2"></i><?= Html::encode($this->title) ?>
        </h2>
        <div class="btn-group shadow-sm">
            <?= Html::a('<i class="fa fa-edit me-1"></i> Cập nhật', ['update', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
            <?= Html::a('<i class="fa fa-list me-1"></i> Danh sách', ['index'], ['class' => 'btn btn-light']) ?>
            <button class="btn btn-secondary" onclick="history.back()"><i class="fa fa-arrow-left"></i></button>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold text-success"><i class="fa fa-map-marked-alt me-2"></i>Sơ đồ tuyến ống thực địa</h5>
                    <span class="badge bg-light text-dark border">WMS: Ống phân phối</span>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 500px; width: 100%;"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-7 col-lg-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold text-primary px-2">Thông tin kỹ thuật</h5>
                </div>
                <div class="card-body p-0">
                    <?= DetailView::widget([
                        'model' => $model,
                        'options' => ['class' => 'table table-hover mb-0 detail-view-custom'],
                        'attributes' => [
                            'ten',
                            'ma',
                            'vatlieu',
                            'coong',
                            [
                                'label' => 'Tình trạng',
                                'format' => 'raw',
                                'value' => function($model) {
                                    $tinhtrang = (isset($model->tinhtrang) && $model->tinhtrang !== null && isset($model->tinhtrang->ten)) ? $model->tinhtrang->ten : 'N/A';
                                    return Html::tag('span', $tinhtrang, ['class' => 'badge bg-success']);
                                }
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-xl-5 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="fa fa-folder-open text-warning me-2"></i>Hồ sơ hoàn công & Bản vẽ</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($files)) : ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($files as $i => $file) : ?>
                            <?php
                                $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                $dlUrl   = Url::to(['/quanly/hocau/ongdansinh/download-file', 'path' => $file['url']]);
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
                        <div class="text-center py-5">
                            <i class="fa fa-file-excel fa-3x text-light mb-3"></i>
                            <p class="text-muted">Chưa có file đính kèm</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .detail-view-custom th { background-color: #f8f9fa; width: 35%; font-size: 0.9rem; color: #495057; border-right: 1px solid #eee; }
    .detail-view-custom td { font-size: 0.95rem; vertical-align: middle; }
    .card { border-radius: 12px; }
    #map { z-index: 1; border-radius: 0 0 12px 12px; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var center = [10.737202088, 106.915000047];
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
            "Ống dân sinh": L.tileLayer.wms('https://cello.capnuochocaumoi.vn/geoserver/capnuoc_hocau/wms', {
                layers: 'capnuoc_hocau:network_ongdansinh',
                format: 'image/png',
                transparent: true,
                maxZoom: 22
            })
        };

        L.control.layers(baseMaps, overlayers).addTo(map);

        <?php if($model->geojson != null) :?>
            try {
                var geoLayer = L.geoJSON(<?= $model->geojson ?>, {
                    style: {
                        color: "#007bff",
                        weight: 6,
                        opacity: 0.8
                    }
                }).addTo(map);
                
                var bounds = geoLayer.getBounds();
                map.fitBounds(bounds, { padding: [30, 30] });
                
                // Thêm tooltip thông tin ống
                geoLayer.bindTooltip("Ống cái: <?= $model->coong ?> mm", { sticky: true });
            } catch (e) {
                console.error("Lỗi GeoJSON:", e);
            }
        <?php endif;?>
        
        // Đảm bảo map render đúng kích thước sau khi load
        setTimeout(() => { map.invalidateSize(); }, 500);
    });
</script>
