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

$statusClass = 'secondary';
if ($model->tinhtrangsuco_id == 1) $statusClass = 'danger';
if ($model->tinhtrangsuco_id == 2) $statusClass = 'warning';
if ($model->tinhtrangsuco_id == 3) $statusClass = 'success';
?>

<div class="gd-suco-view container-fluid mt-3">
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white shadow-sm rounded">
        <div>
            <h2 class="h4 fw-bold mb-1 text-primary">
                <i class="fa fa-exclamation-triangle me-2 text-danger"></i><?= Html::encode($model->masuco) ?>
            </h2>
            <div class="d-flex align-items-center">
                <span class="badge bg-<?= $statusClass ?> me-2">
                    <?= (isset($model->tinhtrangsuco) && $model->tinhtrangsuco !== null && isset($model->tinhtrangsuco->ten)) ? $model->tinhtrangsuco->ten : 'N/A' ?>
                </span>
                <small class="text-muted"><i class="fa fa-clock me-1"></i>Phát hiện: <?= $model->n_phathien ?></small>
            </div>
        </div>
        <div class="btn-group shadow-sm">
            <?= Html::a('<i class="fa fa-edit me-1"></i> Cập nhật', ['update', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
            <a href="javascript:history.back()" class="btn btn-light"><i class="fa fa-arrow-left"></i> Quay lại</a>
        </div>
    </div>

    <div class="row align-items-stretch">

        <!-- CỘT TRÁI -->
        <div class="col-xl-5 col-lg-6 d-flex flex-column mb-4">

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold"><i class="fa fa-info-circle text-primary me-2"></i>Thông tin chi tiết sự cố</h5>
                </div>
                <div class="card-body p-0">
                    <?= DetailView::widget([
                        'model' => $model,
                        'options' => ['class' => 'table table-hover mb-0 detail-view-custom'],
                        'attributes' => [
                            'ten',
                            'masuco',
                            'mataisan',
                            [
                                'attribute' => 'loaisuco_id',
                                'label' => 'Loại sự cố',
                                'value' => (isset($model->loaisuco) && $model->loaisuco !== null && isset($model->loaisuco->ten)) ? $model->loaisuco->ten : '',
                            ],
                            'vitri',
                            [
                                'attribute' => 'n_phathien',
                                'label' => 'Thời gian phát hiện',
                                'format' => 'raw',
                                'value' => '<span class="text-danger fw-bold">'.$model->n_phathien.'</span>'
                            ],
                            'n_xuly',
                            'n_hoancong',
                            [
                                'attribute' => 'nguyennhansuco_id',
                                'label' => 'Nguyên nhân sự cố',
                                'value' => (isset($model->nguyennhansuco) && $model->nguyennhansuco !== null && isset($model->nguyennhansuco->ten)) ? $model->nguyennhansuco->ten : '',
                            ],
                            'cachxuly:ntext',
                            'ghichu:ntext',
                            [
                                'attribute' => 'tinhtrang_id',
                                'label' => 'Trạng thái',
                                'format' => 'raw',
                                'value' => (function() use ($model) {
                                    if (isset($model->tinhtrang) && $model->tinhtrang !== null && isset($model->tinhtrang->ten)) {
                                        $colors = ['#1bc5bd', '#ffa800', '#f64e60', '#3699ff', '#8950fc', '#181c32', '#e4e6ef', '#00d4ff'];
                                        $colorIdx = (($model->tinhtrang_id - 1) % count($colors));
                                        $color = $colors[$colorIdx];
                                        return '<span class="badge" style="background-color:' . $color . ';color:#fff;padding:5px 12px;font-size:0.85rem;">' . \yii\helpers\Html::encode($model->tinhtrang->ten) . '</span>';
                                    }
                                    return '<span class="badge bg-secondary" style="padding:5px 12px;font-size:0.85rem;">Chưa cập nhật</span>';
                                })(),
                            ],
                        ],
                    ]) ?>
                </div>
            </div>

            <!-- Card file: flex-grow để lấp đầy chiều cao còn lại -->
            <div class="card border-0 shadow-sm flex-grow-1 d-flex flex-column" style="min-height: 0;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold"><i class="fa fa-camera text-info me-2"></i>Hình ảnh & Hồ sơ hiện trường</h5>
                </div>
                <div class="card-body flex-grow-1 overflow-auto py-2" style="max-height: 420px;">
                    <?php if (!empty($files)) : ?>
                        <!-- Ảnh thumbnail -->
                        <div class="d-flex flex-wrap gap-2 mb-2">
                        <?php foreach ($files as $i => $file) : ?>
                            <?php
                                $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                $dlUrl   = Url::to(['/quanly/hocau/suco/download-file', 'path' => $file['url']]);
                            ?>
                            <?php if ($isImage) : ?>
                                <a href="<?= $dlUrl ?>" target="_blank" class="d-inline-block">
                                    <img src="<?= $dlUrl ?>"
                                         alt="<?= Html::encode($file['name']) ?>"
                                         style="width:140px;height:140px;object-fit:cover;border-radius:8px;border:1px solid #e2e8f0;"
                                         title="<?= Html::encode($file['name']) ?>" />
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </div>
                        <!-- File không phải ảnh -->
                        <div class="list-group list-group-flush">
                        <?php foreach ($files as $i => $file) : ?>
                            <?php
                                $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                $dlUrl   = Url::to(['/quanly/hocau/suco/download-file', 'path' => $file['url']]);
                            ?>
                            <?php if (!$isImage) : ?>
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
                        <div class="text-center py-4 text-muted small">Chưa có ảnh hiện trường</div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- CỘT PHẢI: bản đồ -->
        <div class="col-xl-7 col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold"><i class="fa fa-map-marker-alt text-danger me-2"></i>Vị trí sự cố trên bản đồ</h5>
                    <div class="text-muted small"><?= $model->lat ?>, <?= $model->long ?></div>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 70vh; min-height: 500px; width: 100%;"></div>
                </div>
                <div class="card-footer bg-light p-2 text-center small text-muted">
                    Sử dụng nút <i class="fa fa-location-arrow"></i> trên bản đồ để dẫn đường đến vị trí này.
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
    .detail-view-custom th { background-color: #f8f9fa; width: 40%; font-size: 0.85rem; color: #555; text-transform: uppercase; border-right: 1px solid #eee; }
    .detail-view-custom td { font-size: 0.95rem; }
    .card { border-radius: 12px; overflow: hidden; }
    #map { border: none !important; }
</style>

<script type="module">
    var map = L.map('map').setView([
        <?= ($model->lat != null) ? $model->lat : '10.8231' ?>,
        <?= ($model->long != null) ? $model->long : '106.6297' ?>
    ], 18);

    var layerGMapSatellite = L.tileLayer('http://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    }).addTo(map);

    var layerGmapStreets = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    });

    const suco = L.tileLayer.wms('https://cello.capnuochocaumoi.vn/geoserver/capnuoc_hocau/wms', {
        layers: 'capnuoc_hocau:network_suco',
        format: 'image/png',
        transparent: true,
        maxZoom: 22
    });

    L.control.layers({ "Vệ tinh": layerGMapSatellite, "Địa giới": layerGmapStreets }, { "Sự cố" : suco }).addTo(map);

    var icon = L.icon({
        iconUrl: '<?= Yii::$app->homeUrl ?>images/icons8-map-marker-96.png',
        iconSize: [46, 46],
        iconAnchor: [23, 46],
        popupAnchor: [0, -40],
    });

    <?php if ($model->lat != null && $model->long != null) : ?>
        L.marker([<?= $model->lat ?>, <?= $model->long ?>], {icon: icon})
         .addTo(map)
         .bindPopup("<strong>Sự cố: <?= $model->masuco ?></strong><br><?= $model->vitri ?>")
         .openPopup();
    <?php endif; ?>

    L.control.locate({
        position: 'topleft',
        flyTo: true,
        strings: { title: "Vị trí của tôi" },
        icon: 'fa fa-crosshairs'
    }).addTo(map);

    setTimeout(() => { map.invalidateSize(); }, 400);
</script>