<?php
/**
 * Created by PhpStorm.
 * User: MinhDuc
 * Date: 6/4/2020
 * Time: 11:19 AM
 */
?>

<script>

    jQuery(function ($) {
        function map_init() {
            var map = L.map('map', {});
            L.tileLayer.wms('https://maps.hcmgis.vn/geoserver/wms', {"layers": "hcm_map:hcm_map"}).addTo(map);
            L.control.layers({
                "HCMGIS": L.tileLayer.wms('https://maps.hcmgis.vn/geoserver/wms', {"layers": "hcm_map:hcm_map"}),
                "GMAP": L.tileLayer('http://{s}.google.com/vt/lyrs=r&x={x}&y={y}&z={z}', {
                    "attribution": "© GoogleMap contributors",
                    "maxZoom": 22,
                    "subdomains": ["mt0", "mt1", "mt2", "mt3"]
                }),
                "HereMap": L.tileLayer('https://2.base.maps.ls.hereapi.com/maptile/2.1/maptile/newest/normal.day/{z}/{x}/{y}/512/png8?apiKey=jDRuakJSjCcRxLWuLkH_z1_mhggHUL5v5d-HonUxnYQ&ppi=320', {
                    "attribution": "©  HERE 2020",
                    "maxZoom": 22
                })
            }, {
                "Long Thới": L.layerGroup([L.tileLayer.wms('http://nhknhabe.hcmgis.vn/geo113/nhanhokhau_nhabe/wms?', {
                    "layers": "nhanhokhau_nhabe:ranhthua_longthoi",
                    "transparent": true,
                    "format": "image/png8",
                    "maxZoom": 22
                })]),
                "Nhơn Đức": L.layerGroup([L.tileLayer.wms('http://nhknhabe.hcmgis.vn/geo113/nhanhokhau_nhabe/wms?', {
                    "layers": "nhanhokhau_nhabe:ranhthua_nhonduc",
                    "transparent": true,
                    "format": "image/png8",
                    "maxZoom": 22
                })]),
                "Phước Lộc": L.layerGroup([L.tileLayer.wms('http://nhknhabe.hcmgis.vn/geo113/nhanhokhau_nhabe/wms?', {
                    "layers": "nhanhokhau_nhabe:ranhthua_phuocloc",
                    "transparent": true,
                    "format": "image/png8",
                    "maxZoom": 22
                })])
            }, {"position": "topright"}).addTo(map);
            L.control.scale({"position": "bottomleft"}).addTo(map);

            function initNocGiaGeojson() {
                $.ajax({
                    url: '/nhanhokhau_nhabe_dev/web/admin/ban-do/noc-gia-geojson',
                    dataType: 'json',
                    success: function (data) {
                        var pruneCluster = new PruneClusterForLeaflet();

                        data.features.map(function (item) {
                            var marker = new PruneCluster.Marker(item.geo_y, item.geo_x);
                            marker.data = item;
                            pruneCluster.RegisterMarker(marker);
                        });

                        pruneCluster.PrepareLeafletMarker = function (leafletMarker, data) {
                            var popupid = 'marker-popup-' + data.id;
                            leafletMarker.bindPopup('<div id="' + popupid + '"></div>');
                            leafletMarker.on('click', function () {
                                var popupid = 'marker-popup-' + data.id;
                                //  mapZoomAndPanTo(data.geo_y, data.geo_x);
                                $.ajax({
                                    url: '/nhanhokhau_nhabe_dev/web/admin/ban-do/get-noc-gia' + '?id=' + data.id,
                                    success: function (html) {
                                        $('#' + popupid).empty().append(html);
                                    }
                                })
                            });

                            marker_nocgia['marker-' + data.id] = leafletMarker;
                        }


                        map.addLayer(pruneCluster);
                    }
                });
            }

            function initAjaxListNocGia(url) {
                var div = $('#nocgia_results');
                $.ajax({
                    url: url,
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        div.empty().append(html);
//                    initPagAjaxDivList();
//                    initNocgiaClickEvent();
                    }
                });
            }

            function initPagAjaxDivList() {
                $('.pagination li a').on('click', function (e) {
                    e.preventDefault();
                    var _this = $(this);
                    var url = _this.attr('href');
                    initAjaxListNocGia(url);
                    return false;
                });
            };

            map.setView([10.6554327, 106.7226793], 13);
            initNocGiaGeojson();
            initAjaxListNocGia();
        }
        ;

        var marker_nocgia = [];
        map_init();

    });

</script>
