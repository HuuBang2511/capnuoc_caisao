jQuery(function ($) {
    var map,markers;
    function map_init(){
        map = L.map('w0', {});
        L.tileLayer.wms('http://pcd.hcmgis.vn/geoserver/ows?', {"layers":"hcm_map:hcm_map"}).addTo(map);
        L.control.layers({"HCMGIS":L.tileLayer.wms('https://maps.hcmgis.vn/geoserver/wms', {"layers":"hcm_map:hcm_map"}),"OSM":L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {"attribution":"© OpenStreetMap contributors","maxZoom":22}),"GMAP":L.tileLayer('http://{s}.google.com/vt/lyrs=r&x={x}&y={y}&z={z}', {"attribution":"© GoogleMap contributors","maxZoom":22,"subdomains":["mt0","mt1","mt2","mt3"]})}, {"Ranh thửa":L.layerGroup([L.tileLayer.wms('http://localhost:8080/geoserver/geoserver_p11q3/wms?', {"layers":"geoserver_p11q3:ranh_thua","transparent":true,"format":"image/png8","maxZoom":22})])}, {"position":"topright"}).addTo(map);
        L.control.scale({"position":"bottomleft"}).addTo(map);
        map.setView([10.7865386,106.66955], 13);}
    map_init();
    function initGeojsonLayer()
    {
        $.ajax({
            url: '/p11q3/web/ban-do/site/noicutru-geojson',
            dataType: 'json',
            success: function (data) {
                var prunecluster = new PruneClusterForLeaflet();
                prunecluster.PrepareLeafletMarker = function(leafletMarker, data){
                    leafletMarker.on('click', function(e){
                        var popupid = 'marker-popup-' + data.id;
                        leafletMarker.bindPopup('<div id="' + popupid + '"></div>');
                        $.ajax({
                            url: '/p11q3/web/ban-do/site/get-noicutru' + data.id,
                            success: function (html) {
                                $('#' + popupid).empty().append(html);
                            }
                        })
                    })
                };
                var data = data.features;
                data.map(function(item){
                    var marker = new PruneCluster.Marker(item.geo_y, item.geo_x);
                    marker.data = item;
                    prunecluster.RegisterMarker(marker);
                });

                markers = prunecluster;

            }})
        map.invalidateSize();
    };
    initGeojsonLayer();
});