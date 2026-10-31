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

<div class="gd-view-container mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 fw-bold mb-0 text-primary">
            <i class="fa fa-info-circle me-2"></i><?= Html::encode($this->title) ?>
        </h2>
        <div class="btn-group shadow-sm">
            <?= Html::a('<i class="fa fa-arrow-left"></i> Quay lại', Url::to(['index']), ['class' => 'btn btn-outline-secondary']) ?>
            <?= Html::a('<i class="fa fa-edit"></i> Cập nhật', ['update', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0"><i class="fa fa-list-alt text-info me-2"></i>Thông tin chung</h5>
                </div>
                <div class="card-body p-0">
                    <?= DetailView::widget([
                        'model' => $model,
                        'options' => ['class' => 'table table-hover mb-0 detail-view'],
                        'attributes' => [
                            [
                                'attribute' => 'vitri',
                                'label' => 'Vị trí',
                                'captionOptions' => ['style' => 'width: 35%'],
                            ],
                            'ten',
                            'ma',
                            [
                                'label' => 'Tình trạng',
                                'format' => 'raw',
                                'value' => function($model){
                                    $text = ($model->tinhtrang_id != null) ? $model->tinhtrang->ten : 'N/A';
                                    return '<span class="badge bg-soft-info text-info border border-info px-2 py-1">' . $text . '</span>';
                                }
                            ],
                            'ghichu:ntext',
                            'lat',
                            'long',
                        ],
                    ]) ?>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0"><i class="fa fa-paperclip text-warning me-2"></i>Tài liệu đính kèm</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($files)) : ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless align-middle mb-0">
                                <thead>
                                    <tr class="text-muted small uppercase">
                                        <th width="40">#</th>
                                        <th>Tên file</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($files as $i => $file) : ?>
                            <?php
                                $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                $dlUrl   = Url::to(['/quanly/hocau/cocmoc/download-file', 'path' => $file['url']]);
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
                                </tbody>
                            </table>
                        </div>
                    <?php else : ?>
                        <div class="text-center py-3">
                            <i class="fa fa-folder-open text-light fa-2x mb-2"></i>
                            <p class="text-muted small mb-0">Không có file đính kèm</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="fa fa-map-marked-alt text-danger me-2"></i>Bản đồ vị trí</h5>
                    <span class="badge bg-light text-dark fw-normal border">Zoom: 18x</span>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 65vh; width: 100%; min-height: 500px; border-bottom-left-radius: .35rem; border-bottom-right-radius: .35rem;"></div>
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
    .bg-soft-info { background-color: rgba(13, 202, 240, 0.1); }
    .detail-view th { background-color: #fcfcfc; color: #666; font-weight: 600; font-size: 0.9rem; }
    .card { border-radius: 12px; overflow: hidden; }
    #map { border: none !important; }
</style>

<script type="module">
    // Khởi tạo tọa độ
    const lat = <?= ($model->lat != null) ? $model->lat : '20.473381288809428' ?>;
    const lng = <?= ($model->long != null) ? $model->long : '106.31907196809175' ?>;

    const map = L.map('map').setView([lat, lng], 18);

    // Lớp nền
    const streets = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        maxZoom: 20, subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    }).addTo(map);

    const satellite = L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        maxZoom: 20, subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    });

    const cocmoc = L.tileLayer.wms('https://cello.capnuochocaumoi.vn/geoserver/capnuoc_hocau/wms', {
        layers: 'capnuoc_hocau:network_cocmoc',
        format: 'image/png',
        transparent: true,
        maxZoom: 22
    });

    L.control.layers({ "Bản đồ": streets, "Vệ tinh": satellite }, { "Cọc mốc" : cocmoc }).addTo(map);

    // Marker
    const icon = L.icon({
        iconUrl: '<?= Yii::$app->homeUrl ?>images/icons8-map-marker-96.png',
        iconSize: [40, 40],
        iconAnchor: [20, 40],
    });

    <?php if ($model->lat != null && $model->long != null) : ?>
        L.marker([lat, lng], { icon: icon }).addTo(map);
    <?php endif; ?>

    // Định vị
    L.control.locate({
        position: 'topleft',
        flyTo: true,
        strings: { title: "Vị trí của tôi" },
        locateOptions: { enableHighAccuracy: true }
    }).addTo(map);

    // Fix render
    setTimeout(() => { map.invalidateSize(); }, 300);
</script>
