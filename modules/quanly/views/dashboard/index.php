<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\echarts\EChartAsset;
use yii\bootstrap5\Modal;

EChartAsset::register($this);

// Đăng ký tài nguyên
$this->registerJsFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerCssFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', ['position' => \yii\web\View::POS_HEAD]);
?>

<style>
.dashboard-container {
    background-color: #f8f9fa;
    padding: 20px;
    min-height: 100vh;
}

.block-themed {
    transition: transform 0.3s, box-shadow 0.3s;
    border-radius: 10px;
    overflow: hidden;
}

.block-themed:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.chart-container {
    height: 300px;
    width: 100%;
}

#map {
    height: 400px;
    border-radius: 10px;
}
</style>

<div class="dashboard-container">
    <h1 class="mb-4 text-primary">Bảng Điều Khiển Cấp Thoát Nước</h1>

    <!-- Thẻ Thống Kê -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="block block-themed stat-card text-center">
                <div class="block-header bg-primary-dark d-flex align-items-center justify-content-center">
                    <i class="fas fa-tint text-white fa-2x me-2"></i>
                    <h3 class="block-title fs-4 fw-bold text-white">Van</h3>
                </div>
                <div class="block-content p-4">
                    <div class="fs-1 fw-bold text-primary"><?= $thongke['van_mangluoi'] ?></div>
                    <a href="<?= Url::to(['capnuocgd/gd-vanphanphoi/index']) ?>"
                        class="btn btn-outline-primary mt-2">Xem chi tiết</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="block block-themed stat-card text-center">
                <div class="block-header bg-success d-flex align-items-center justify-content-center">
                    <i class="fas fa-gauge text-white fa-2x me-2"></i>
                    <h3 class="block-title fs-4 fw-bold text-white">Đồng hồ tổng</h3>
                </div>
                <div class="block-content p-4">
                    <div class="fs-1 fw-bold text-success"><?= $thongke['nhamay_nuoc'] ?></div>
                    <a href="<?= Url::to(['capnuocgd/gd-dongho-tong-gd/index']) ?>"
                        class="btn btn-outline-success mt-2">Xem chi tiết</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="block block-themed stat-card text-center">
                <div class="block-header bg-warning d-flex align-items-center justify-content-center">
                    <i class="fas fa-gauge text-white fa-2x me-2"></i>
                    <h3 class="block-title fs-4 fw-bold text-white">Đồng hồ Khách hàng</h3>
                </div>
                <div class="block-content p-4">
                    <div class="fs-1 fw-bold text-warning"><?= $thongke['dongho_kh'] ?></div>
                    <a href="<?= Url::to(['capnuocgd/gd-dongho-kh-gd/index']) ?>"
                        class="btn btn-outline-warning mt-2">Xem chi tiết</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="block block-themed stat-card text-center">
                <div class="block-header bg-info d-flex align-items-center justify-content-center">
                    <i class="fas fa-road text-white fa-2x me-2"></i>
                    <h3 class="block-title fs-4 fw-bold text-white">Số Km Ống cái</h3>
                </div>
                <div class="block-content p-4">
                    <div class="fs-1 fw-bold text-info"><?= $thongke['ong_phanphoi'] ?></div>
                    <a href="<?= Url::to(['capnuocgd/gd-ongnganh/index']) ?>" class="btn btn-outline-info mt-2">Xem chi
                        tiết</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="block block-themed stat-card text-center">
                <div class="block-header bg-danger d-flex align-items-center justify-content-center">
                    <i class="fas fa-exclamation-triangle text-white fa-2x me-2"></i>
                    <h3 class="block-title fs-4 fw-bold text-white">Sự cố điểm bể</h3>
                </div>
                <div class="block-content p-4">
                    <div class="fs-1 fw-bold text-danger"><?= $thongke['suco'] ?></div>
                    <a href="<?= Url::to(['capnuocgd/gd-suco/index']) ?>" class="btn btn-outline-danger mt-2">Xem chi
                        tiết</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="block block-themed stat-card text-center">
                <div class="block-header bg-secondary d-flex align-items-center justify-content-center">
                    <i class="fas fa-fire-extinguisher text-white fa-2x me-2"></i>
                    <h3 class="block-title fs-4 fw-bold text-white">Trụ PCCC</h3>
                </div>
                <div class="block-content p-4">
                    <div class="fs-1 fw-bold text-secondary"><?= $thongke['pccc'] ?></div>
                    <a href="<?= Url::to(['capnuocgd/gd-tramcuuhoa/index']) ?>"
                        class="btn btn-outline-secondary mt-2">Xem chi tiết</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="block block-themed stat-card text-center">
                <div class="block-header bg-primary d-flex align-items-center justify-content-center">
                    <i class="fas fa-industry text-white fa-2x me-2"></i>
                    <h3 class="block-title fs-4 fw-bold text-white">Trạm bơm</h3>
                </div>
                <div class="block-content p-4">
                    <div class="fs-1 fw-bold text-primary"><?= $thongke['trambom'] ?></div>
                    <a href="<?= Url::to(['capnuocgd/gd-trambom/index']) ?>" class="btn btn-outline-primary mt-2">Xem
                        chi tiết</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="block block-themed stat-card text-center">
                <div class="block-header bg-dark d-flex align-items-center justify-content-center">
                    <i class="fas fa-wrench text-white fa-2x me-2"></i>
                    <h3 class="block-title fs-4 fw-bold text-white">Hầm kỹ thuật</h3>
                </div>
                <div class="block-content p-4">
                    <div class="fs-1 fw-bold text-dark"><?= $thongke['ham'] ?></div>
                    <a href="<?= Url::to(['capnuocgd/gd-hamkythuat/index']) ?>" class="btn btn-outline-dark mt-2">Xem
                        chi tiết</a>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row mt-4">
        <div class="col-12 col-xl-6">
            <div class="block block-themed">
                <div class="block-header bg-primary">
                    <h3 class="block-title">Tiêu thụ Nước Hàng Tháng</h3>
                </div>
                <div class="block-content">
                    <canvas id="water-consumption-chart" class="chart-container"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="block block-themed">
                <div class="block-header bg-danger">
                    <h3 class="block-title">Sự cố Rò rỉ Theo Thời Gian</h3>
                </div>
                <div class="block-content">
                    <canvas id="leak-incidents-chart" class="chart-container"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="block block-themed">
                <div class="block-header">
                    <h3 class="block-title">DMA</h3>
                </div>
                <div class="block-content">
                    <div class="row">
                        <div id="geomap" class="charts" _echarts_instance_="ec_16dd532"
                            style="height: 70vh;width: 100%"></div>
                    </div>
                    <script>
                    $(document).ready(function() {
                        var chartDom = document.getElementById('geomap');
                        var myChart = echarts.init(chartDom);
                        var option;

                        myChart.showLoading();
                        $.get('<?= Yii::$app->urlManager->createUrl('quanly/dashboard/geojson')?>',
                            function(geoJson) {
                                myChart.hideLoading();
                                echarts.registerMap('HK', geoJson);

                                function pieChart(data, center, radius) {
                                    return {
                                        type: 'pie',
                                        coordinateSystem: 'geo',
                                        textStyle: {
                                            fontFamily: 'Roboto',
                                        },
                                        tooltip: {
                                            //trigger: 'item',
                                            formatter: '{b}: {c} ({d}%)',
                                            textStyle: {
                                                fontFamily: 'Roboto'
                                            }
                                        },
                                        label: {
                                            show: false
                                        },
                                        labelLine: {
                                            show: false
                                        },
                                        animationDuration: 0,
                                        radius,
                                        center,
                                        data
                                    };


                                }
                                option = {
                                    title: {
                                        text: 'Bản đồ DMA',

                                        textStyle: {
                                            fontFamily: 'Roboto'
                                        }
                                    },
                                    textStyle: {
                                        fontFamily: 'Roboto'
                                    },
                                    tooltip: {
                                        trigger: 'item',
                                        showDelay: 0,
                                        transitionDuration: 0.2,
                                        textStyle: {
                                            fontFamily: 'Roboto'
                                        }
                                    },
                                    toolbox: {
                                        show: true,
                                        orient: 'vertical',
                                        left: 'right',
                                        top: 'center',
                                        feature: {
                                            dataView: {
                                                readOnly: false
                                            },
                                            restore: {},
                                            saveAsImage: {},
                                            dataZoom: {}
                                        },
                                        // feature: {
                                        //     saveAsImage: {}
                                        // }
                                    },
                                    visualMap: {
                                        min: 0,
                                        max: 150,
                                        text: ['Cao', 'Thấp'],
                                        calculable: true,
                                        inRange: {
                                            color: ['lightskyblue', 'yellow', 'orangered']
                                        }
                                    },
                                    series: [{
                                        name: 'Số van',
                                        type: 'map',
                                        label: {
                                            show: true,
                                        },
                                        map: 'HK',
                                        roam: true,
                                        data: <?= json_encode($sovanDma, JSON_UNESCAPED_UNICODE)?>,

                                    }]
                                }
                                myChart.setOption(option);
                            });

                        option && myChart.setOption(option);

                        myChart.on('click', function(params) {
                            // Print name in console
                            console.log(params);
                            //Dashmix.layout('side_overlay_open');
                            // $.get(url).then(function(data){
                            //     $('#content-side').empty().html(data);
                            // });
                            //var a = document.createElement('a');
                            var url =
                                '<?= Yii::$app->urlManager->createUrl('quanly/dashboard/chitietdma?id=')?>' +
                                params.data.id;
                            $('#modal-content').load(url);
                            $('#ajax-modal').modal('show'); // Show the modal
                        });


                    });

                    function chartInit() {
                        $(".charts").each(function() {
                            var id = $(this).attr('_echarts_instance_');
                            window.echarts.getInstanceById(id).resize();
                        });
                    }
                    </script>
                </div>
            </div>
        </div>
    </div>

</div>


<style>
#chartdiv {
    max-width: 100%;
    height: 800px;
    background-color: #212327;
}
</style>





<script>
// Đảm bảo Chart.js đã tải trước khi khởi tạo
document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo Bản đồ
    // var map = L.map('map').setView([10.7769, 106.7009], 13);
    // L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    //     attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    // }).addTo(map);
    // L.marker([10.7769, 106.7009]).addTo(map).bindPopup('Trạm Bơm Chính');
    // L.marker([10.7800, 106.7100]).addTo(map).bindPopup('Van');
    // L.marker([10.7700, 106.6900]).addTo(map).bindPopup('Sự cố Rò rỉ');

    // Biểu đồ Tiêu thụ Nước
    const ctx = document.getElementById('water-consumption-chart').getContext('2d');
    if (ctx) {
        const waterConsumptionChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Th1', 'Th2', 'Th3', 'Th4', 'Th5', 'Th6', 'Th7', 'Th8', 'Th9', 'Th10', 'Th11',
                    'Th12'
                ],
                datasets: [{
                    label: 'Tiêu thụ Nước (m³)',
                    data: [12000, 15000, 13000, 17000, 16000, 18000, 20000, 19000, 21000, 22000,
                        23000, 24000
                    ],
                    fill: true,
                    backgroundColor: 'rgba(0, 123, 255, 0.2)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Tiêu thụ Nước Hàng Tháng',
                        font: {
                            size: 16
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Khối lượng (m³)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Tháng'
                        }
                    }
                }
            }
        });
    }

    // Biểu đồ Sự cố Rò rỉ
    const ctx2 = document.getElementById('leak-incidents-chart').getContext('2d');
    if (ctx2) {
        const leakIncidentsChart = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: ['Th1', 'Th2', 'Th3', 'Th4', 'Th5', 'Th6', 'Th7', 'Th8', 'Th9', 'Th10', 'Th11',
                    'Th12'
                ],
                datasets: [{
                    label: 'Sự cố Rò rỉ',
                    data: [5, 3, 8, 2, 6, 4, 7, 3, 5, 2, 4, 6],
                    backgroundColor: 'rgba(220, 53, 69, 0.5)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Sự cố Rò rỉ Theo Thời Gian',
                        font: {
                            size: 16
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Số sự cố'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Tháng'
                        }
                    }
                }
            }
        });
    }
});
</script>

<!-- Modal -->
<?php
    Modal::begin([
        'id' => 'ajax-modal',
        'title' => '<h5></h5>',
        'size' => Modal::SIZE_LARGE,
        'footer' => '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>',
    ]);
    ?>

<div id="modal-content">
    <p>Loading...</p>
</div>

<?php Modal::end(); ?>
