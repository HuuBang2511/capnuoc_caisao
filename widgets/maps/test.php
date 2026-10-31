<script>jQuery(function ($) {
        var map;
        var marker_doanhnghiep = [];

        function mapInit() {
            map = L.map('w0', {});
            L.tileLayer.wms('https://maps.hcmgis.vn/geoserver/wms', {"layers": "hcm_map:hcm_map"}).addTo(map);
            L.control.layers({
                "HCMGIS": L.tileLayer.wms('https://maps.hcmgis.vn/geoserver/wms', {"layers": "hcm_map:hcm_map"}),
                "OSM": L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    "attribution": "© OpenStreetMap contributors",
                    "maxZoom": 22
                }),
                "GMAP": L.tileLayer('http://{s}.google.com/vt/lyrs=r&x={x}&y={y}&z={z}', {
                    "attribution": "© GoogleMap contributors",
                    "maxZoom": 22,
                    "subdomains": ["mt0", "mt1", "mt2", "mt3"]
                })
            }, {}, {"position": "topright"}).addTo(map);
            L.control.scale({"position": "bottomleft"}).addTo(map);

            var editableLayers = new L.FeatureGroup();
            map.addLayer(editableLayers);

            var drawnItems = new L.FeatureGroup();
            map.addLayer(drawnItems);
            var drawFeatureGroup = L.featureGroup();
            var options = {};
            options = {
                "draw": {
                    "polyline": false,
                    "polygon": false,
                    "marker": false,
                    "circle": true,
                    "rectangle": false
                }
            };
            options.edit = {featureGroup: drawnItems, remove: true};

            var drawControl = new L.Control.Draw(options);


            map.addControl(drawControl);
//
//            var toolbar = new L.Toolbar();
//            toolbar.addToolbar(map);

            map.on('draw:created', function (e) {
                var type = e.layerType,
                    layer = e.layer;

                /* if (type === 'marker') {
                    layer.bindPopup('A popup!');
                } */

                if (type === 'circle') {
                    var center = layer.getLatLng();
                    var radius = layer.getRadius();
                    var merge = {center, radius};

                    var jsonCircle = JSON.stringify(merge);
                    console.log(jsonCircle);

                    $.ajax({
                        type: 'GET',
                        url: '/dongvathoangda_final/web/admin/ban-do/doanhnghiep-geojson-in-circle',
                        data: {'data': jsonCircle},
                        contentType: 'application/json',
                        dataType: 'json',
                        success: function (response) {
                            initPruneCluster(response);
                        },
                        complete: function (data) {
                        }
                    });
                }

                drawnItems.addLayer(layer);
            });

            map.on('draw:deleted', function (e) {


            });


            map.setView([10.7754327, 106.72926793], 13);
        }
        ;
        mapInit();

        function initPruneCluster(response) {
            var pruneCluster = createPruneCluster();
            var data = response.data;
            console.log(response);
            data.features.map(function (item) {
                var marker = new PruneCluster.Marker(item.geo_y, item.geo_x);
                marker.data = item;
                pruneCluster.RegisterMarker(marker);
            })
            marker_doanhnghiep = pruneCluster;
        };

        function createPruneCluster() {
            var pruneCluster = new PruneClusterForLeaflet();
            pruneCluster.PrepareLeafletMarker = function (leafletMarker, data) {
                leafletMarker.on('click', function (e) {
                    var popupid = 'marker-popup-' + data.id;
                    $.ajax({
                        url: '' + '?id=' + data.id,
                        success: function (html) {
                            $('#' + popupid).empty().append(html);
                        }
                    })
                });
            };
            return pruneCluster;
        }

        function initDoanhnghiepGeojson() {
            $.ajax({
                url: '/dongvathoangda_final/web/admin/ban-do/doanhnghiep-geojson',
                dataType: 'json',
                success: function (data) {
                    initPruneCluster(data);
                }
            });
        }

        function initAjaxListFunction(url) {
            var div = $('#doanhnghiep_results');
            $.ajax({
                url: url,
                success: function (html) {
                    div.empty().append(html);
                    initPagAjaxDivList();
                    initClickEvent();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                }
            });
        }

        function initPagAjaxDivList() {
            $('.pagination li a').on('click', function (e) {
                e.preventDefault();
                var _this = $(this);
                var url = _this.attr('href');
                initAjaxListFunction(url);
                return false;
            });
        };

        function initClickEvent() {
            $('.nocgia-item').on('click', function (e) {
                var _this = $(this);
                var point_x = _this.attr('data-point-x');
                var point_y = _this.attr('data-point-y');
                var target = _this.attr('data-target');
                if (typeof point_x !== "undefined" && typeof target !== "undefined") {
                    e.preventDefault();
                    map.setView([point_y, point_x], 16);
                    marker_doanhnghiep[target].openPopup();
                    $.ajax({
                        url: '../ban-do/get-doanhnghiep?id=' + target,
                        success: function (html) {
                            $('#' + 'marker-popup-' + target).empty().append(html);
                        }
                    });
                }
            });
        };

        function initSearchDoanhnghiep() {
            $('#search-box').on('keypress', function (e) {
                if (e.keyCode == 13) {
                    var q = $(this).val();
                    initAjaxListFunction('../ban-do/list-doanhnghiep?q=' + q);
                }
            })
        };

        initPruneCluster('');
        initDoanhnghiepGeojson('');
        initAjaxListFunction('/dongvathoangda_final/web/admin/ban-do/list-doanhnghiep');
        initSearchDoanhnghiep();
    });</script>