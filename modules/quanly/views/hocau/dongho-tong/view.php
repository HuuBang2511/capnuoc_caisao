<?php

use yii\bootstrap5\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\crud\CrudAsset;
use yii\widgets\DetailView;
use app\widgets\maps\LeafletMapAsset;
use app\widgets\maps\plugins\leafletlocate\LeafletLocateAsset;

LeafletMapAsset::register($this);
LeafletLocateAsset::register($this);
CrudAsset::register($this);

$requestedAction = Yii::$app->requestedAction;
$controller = $requestedAction->controller;
$label = $controller->label;

$this->title = Yii::t('app', $label[$requestedAction->id].' '.$controller->title);
$this->params['breadcrumbs'][] = ['label' => $label['index'].' '.$controller->title, 'url' => Url::to(['index'])];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="device-view-container mt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 fw-bold mb-1 text-primary"><?= Html::encode($model->madongho) ?></h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item small"><?= $controller->title ?></li>
                    <li class="breadcrumb-item small active">Chi tiết thiết bị</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group shadow-sm">
            <?= Html::a('<i class="fa fa-arrow-left"></i> Quay lại', Url::to(['index']), ['class' => 'btn btn-outline-secondary']) ?>
            <?= Html::a('<i class="fa fa-edit"></i> Cập nhật', ['update', 'id' => $model->id], ['class' => 'btn btn-warning text-white']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="fa fa-cog text-secondary me-2"></i>Thông số kỹ thuật</h5>
                </div>
                <div class="card-body p-0">
                    <?= DetailView::widget([
                        'model' => $model,
                        'options' => ['class' => 'table table-hover mb-0 detail-view'],
                        'attributes' => [
                            'ten',
                            [
                                'attribute' => 'madongho',
                                'captionOptions' => ['style' => 'width: 40%'],
                                'contentOptions' => ['class' => 'fw-bold text-primary'],
                            ],
                            [
                                'label' => 'Hiệu đồng hồ',
                                'value' => (isset($model->hieudongho) && $model->hieudongho !== null && isset($model->hieudongho->ten)) ? $model->hieudongho->ten : 'N/A',
                            ],
                            'sothan',
                            'co',
                            'mavattu',
                            [
                                'label' => 'Tình trạng',
                                'format' => 'raw',
                                'value' => function($model) {
                                    $statusClass = $model->tinhtrang_id == 1 ? 'bg-success' : 'bg-info'; // Tùy biến theo ID
                                    return "<span class='badge $statusClass'>" . ((isset($model->tinhtrang) && $model->tinhtrang !== null && isset($model->tinhtrang->ten)) ? $model->tinhtrang->ten : 'N/A') . "</span>";
                                }
                            ],
                        ],
                    ]) ?>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="fa fa-map-marker-alt text-danger me-2"></i>Thông tin lắp đặt</h5>
                </div>
                <div class="card-body p-0">
                    <?= DetailView::widget([
                        'model' => $model,
                        'options' => ['class' => 'table table-hover mb-0 detail-view'],
                        'attributes' => [
                            'vitri',
                            'khuvuc',
                            'ngaylapdat',
                            'ghichu:ntext',
                        ],
                    ]) ?>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="fa fa-paperclip text-warning me-2"></i>Hình ảnh & Tài liệu</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($files)) : ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($files as $i => $file) : ?>
                            <?php
                                $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                $dlUrl   = Url::to(['/quanly/hocau/dongho-tong/download-file', 'path' => $file['url']]);
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
                        </ul>
                    <?php else : ?>
                        <p class="text-muted small text-center my-2 italic">Không có file đính kèm</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-sm overflow-hidden" style="height: calc(100% - 24px);">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold"><i class="fa fa-globe-asia text-primary me-2"></i>Vị trí thực địa</h5>
                    <div class="small text-muted">Tọa độ: <?= $model->lat ?>, <?= $model->long ?></div>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 75vh; width: 100%; min-height: 600px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php Modal::begin([
    "id" => "ajaxCrudModal",
    "size" => Modal::SIZE_EXTRA_LARGE,
    "footer" => "",
]) ?>
<?php Modal::end(); ?>

<style>
    .detail-view th { background-color: #f8f9fa; color: #495057; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .detail-view td { font-size: 0.95rem; }
    .card { border-radius: 15px; }
    .badge { font-weight: 500; padding: 0.5em 0.8em; }
    #map { z-index: 1; }
</style>

<script type="module">
    const lat = <?= ($model->lat != null) ? $model->lat : '20.473381288809428' ?>;
    const lng = <?= ($model->long != null) ? $model->long : '106.31907196809175' ?>;

    const map = L.map('map', {
        scrollWheelZoom: true
    }).setView([lat, lng], 18);

    const layerGmapStreets = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        maxZoom: 20, subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    }).addTo(map);

    const layerGMapSatellite = L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
        maxZoom: 20, subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    });

    const donghotong = L.tileLayer.wms('https://cello.capnuochocaumoi.vn/geoserver/capnuoc_hocau/wms', {
        layers: 'capnuoc_hocau:network_donghotong',
        format: 'image/png',
        transparent: true,
        maxZoom: 22
    });

    L.control.layers({ "Bản đồ": layerGmapStreets, "Vệ tinh": layerGMapSatellite } , { "Đồng hồ tổng" : donghotong }).addTo(map);

    const icon = L.icon({
        iconUrl: '<?= Yii::$app->homeUrl ?>images/icons8-map-marker-96.png',
        iconSize: [42, 42],
        iconAnchor: [21, 42],
        popupAnchor: [1, -34],
    });

    <?php if ($model->lat != null && $model->long != null) : ?>
        L.marker([lat, lng], { icon: icon })
         .addTo(map)
         .bindPopup("<b>Mã ĐH: <?= $model->madongho ?></b><br><?= $model->vitri ?>")
         .openPopup();
    <?php endif; ?>

    L.control.locate({
        position: 'topleft',
        flyTo: true,
        strings: { title: "Tìm vị trí của tôi" }
    }).addTo(map);

    // Tự động resize bản đồ khi load xong
    setTimeout(() => { map.invalidateSize(); }, 500);
</script>
