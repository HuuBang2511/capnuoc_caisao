<style>
#chart-none {
    font-family: Roboto !important;
}

.chartStyle {
    position: relative;
    height: 500px;
    overflow: hidden;
}
</style>

<script>
function getRandomInt(min, max) {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min + 1)) + min;
}
</script>

<div class="modal-body">
    <h4></h4>
    <div class="row pb-5">
        <div class="col-lg-12">
            <h3 class="content-heading">Cơ cấu người dùng</h3>
            <div id="chartCocau" class="chartStyle"></div>
            <script type="module">
            $(document).ready(function() {
                var chartDom = document.getElementById('chartCocau');
                var myChart = echarts.init(chartDom);
                //var option;

                var userChartOption = {
                    title: {
                        text: 'Cơ cấu người dùng nước',
                        left: 'center'
                    },
                    tooltip: {
                        trigger: 'item',
                        formatter: '{b}: {c} hộ ({d}%)'
                    },
                    legend: {
                        orient: 'vertical',
                        left: 'left',
                        top: 'middle'
                    },
                    textStyle: {
                        fontFamily: 'Roboto'
                    },
                    series: [{
                        name: 'Loại người dùng',
                        type: 'pie',
                        radius: '60%',
                        data: [{
                                value: getRandomInt(500, 1500),
                                name: 'Hộ gia đình'
                            },
                            {
                                value: getRandomInt(500, 1500),
                                name: 'Doanh nghiệp'
                            },
                            {
                                value: getRandomInt(500, 1500),
                                name: 'Cơ quan hành chính'
                            },
                            {
                                value: getRandomInt(500, 1500),
                                name: 'Dịch vụ công cộng'
                            }
                        ],
                        emphasis: {
                            itemStyle: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.3)'
                            }
                        }
                    }]
                };



                userChartOption && myChart.setOption(userChartOption);
            });
            </script>

        </div>
    </div>
    <div class="row pb-5">
        <div class="col-lg-12">
            <div class="col-lg-12">
                <h3 class="content-heading">Tỉ lệ thất thoát nước</h3>
                <div id="chartThatthoat" class="chartStyle"></div>
                <script type="module">
                $(document).ready(function() {
                    var chartDom = document.getElementById('chartThatthoat');
                    var myChart = echarts.init(chartDom);
                    //var option;

                    var pieLossOption = {
                        title: {
                            text: 'Tỷ lệ thất thoát nước',
                            left: 'center'
                        },
                        tooltip: {
                            trigger: 'item',
                            formatter: '{b}: {c} m³ ({d}%)'
                        },
                        legend: {
                            orient: 'vertical',
                            left: 'left',
                            top: 'middle'
                        },
                        textStyle: {
                            fontFamily: 'Roboto'
                        },
                        series: [{
                            name: 'Tỷ lệ thất thoát',
                            type: 'pie',
                            radius: '60%',
                            data: [{
                                    value: getRandomInt(3000, 5000),
                                    name: 'Nước cung cấp'
                                },
                                {
                                    value: getRandomInt(200, 500),
                                    name: 'Nước thất thoát'
                                }
                            ],
                            itemStyle: {
                                borderRadius: 8
                            },
                            emphasis: {
                                itemStyle: {
                                    shadowBlur: 10,
                                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                                }
                            }
                        }]
                    };

                    pieLossOption && myChart.setOption(pieLossOption);
                });
                </script>

            </div>
        </div>
    </div>

    <div class="row pb-5">
        <div class="col-lg-12">
            <div class="col-lg-12">
                <h3 class="content-heading">Hóa đơn thu theo tháng</h3>
                <div id="chartHoadon" class="chartStyle"></div>
                <script type="module">
                $(document).ready(function() {
                    var chartDom = document.getElementById('chartHoadon');
                    var myChart = echarts.init(chartDom);
                    //var option;

                    var option = {
                        title: {
                            text: 'Số hóa đơn nước đã thu theo tháng',
                            left: 'center'
                        },
                        tooltip: {
                            trigger: 'axis',
                            formatter: '{b}: {c} hóa đơn'
                        },
                        textStyle: {
                            fontFamily: 'Roboto'
                        },
                        xAxis: {
                            type: 'category',
                            data: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
                                'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12',
                            ]
                        },
                        yAxis: {
                            type: 'value',
                            name: 'Số hóa đơn'
                        },
                        series: [{
                            name: 'Hóa đơn đã thu',
                            type: 'bar',
                            data: [
                                getRandomInt(200, 600),
                                getRandomInt(200, 600),
                                getRandomInt(200, 600),
                                getRandomInt(200, 600),
                                getRandomInt(200, 600),
                                getRandomInt(200, 600),
                                getRandomInt(200, 600),
                                getRandomInt(200, 600),
                                getRandomInt(200, 600),
                                getRandomInt(200, 600),
                                getRandomInt(200, 600),
                                getRandomInt(200, 600)
                            ],
                            itemStyle: {
                                color: '#FF9933'
                            }
                        }]
                    };

                    option && myChart.setOption(option);
                });
                </script>

            </div>
        </div>
    </div>
</div>