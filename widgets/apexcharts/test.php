<?php
/**
 * Created by PhpStorm.
 * User: MinhDuc
 * Date: 6/24/2020
 * Time: 9:06 AM
 */

?>

<script>
    $(document).ready(function () {
        var optionschartNVCT = {
            series: <?= json_encode($seriesNVCT, JSON_UNESCAPED_UNICODE)?>,
            chart: {
                type: 'bar',
                height: 700
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    dataLabels: {
                        position: 'top',
                    },
                }
            },
            colors: <?= json_encode($colors) ?>,
            dataLabels: {
                enabled: true,
                style: {
                    fontSize: '12px',
                    colors: ['#fff']
                }
            },
            stroke: {
                show: true,
                width: 1,
                colors: ['#fff']
            },
            xaxis: {
                categories: <?= json_encode($years)?>
            },
            legend: {
                position: 'bottom',
            }
        };

        var chartNVCT = new ApexCharts(document.querySelector("#chartNVCT"), optionschartNVCT);
        chartNVCT.render();

        var optionschartKQNC = {
            series: <?= json_encode($seriesKQNC, JSON_UNESCAPED_UNICODE)?>,
            chart: {
                type: 'bar',
                height: 700
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    dataLabels: {
                        position: 'top',
                    },
                }
            },
            colors: <?= json_encode($colors) ?>,
            dataLabels: {
                enabled: true,
                style: {
                    fontSize: '12px',
                    colors: ['#fff']
                }
            },
            stroke: {
                show: true,
                width: 1,
                colors: ['#fff']
            },
            xaxis: {
                categories: <?= json_encode($years)?>
            },
            legend: {
                position: 'bottom',
            }
        };

        var chartKQNC = new ApexCharts(document.querySelector("#chartKQNC"), optionschartKQNC);
        chartKQNC.render();


        var chartKQLoaiOptions = {
            series: <?= json_encode(array_values($dataLoai), JSON_UNESCAPED_UNICODE)?>,
            chart: {
                type: 'bar',
                height: 350,
                stacked: true,
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                },
            },
            stroke: {
                width: 1,
                colors: ['#fff']
            },
            xaxis: {
                categories: <?= json_encode($years)?>,

            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " Ket qua"
                    }
                }
            },
            fill: {
                opacity: 1
            },
            legend: {
                position: 'top',
                horizontalAlign: 'left',
                offsetX: 40
            }
        };

        var chartKQLoai = new ApexCharts(document.querySelector("#chartKQLoai"), chartKQLoaiOptions);
        chartKQLoai.render();

        var pieKQLoaioptions = {
            series: <?= json_encode($pieData)?>,
            chart: {
                width: 380,
                type: 'pie',
            },
            labels: <?= json_encode($pieLabel, JSON_UNESCAPED_UNICODE)?>,
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        var pieKQLoai = new ApexCharts(document.querySelector("#pieKQLoai"), pieKQLoaioptions);
        pieKQLoai.render();
    });
</script>
