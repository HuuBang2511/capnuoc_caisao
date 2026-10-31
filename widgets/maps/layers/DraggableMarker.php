<?php
/**
 * @copyright Copyright (c) 2013-2015 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */

namespace app\widgets\maps\layers;

use app\services\DebugService;
use app\widgets\maps\types\Icon;
use yii\base\InvalidConfigException;
use yii\web\JsExpression;

class DraggableMarker extends Layer
{
    use LatLngTrait;
    use PopupTrait;

    public $center;
    public $marker;
    public $inputX;
    public $inputY;
    public $selectCoordinate;
    public $toVN2000 = false;

    /**
     * Sets the marker's icon
     *
     * @param Icon $icon
     */
    public function setIcon($icon) //Icon - if you force the icon as type, the makimarker won't work...:(
    {
        $this->clientOptions['icon'] = $icon;
    }

    /**
     * @return Icon
     */
    public function getIcon()
    {
        return isset($this->clientOptions['icon']) ? $this->clientOptions['icon'] : null;
    }

    /**
     * Initializes the marker.
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (empty($this->center)) {
            throw new InvalidConfigException("'center' attribute cannot be empty.");
        }

        if (empty($this->inputX) || empty($this->inputY)) {
            throw new InvalidConfigException("'inputX' or 'inputY' cannot be empty.");
        }

        if (empty($this->name)) {
            $this->name = 'draggableMarker';
        }
    }

    /**
     * @return \yii\web\JsExpression the marker constructor string
     */
    public function encode()
    {
        $name = $this->name;
        if($this->getIcon() != null){
            $icon = $this->icon->encode()->expression;
        }

        $inputX = $this->inputX;
        $inputY = $this->inputY;
        $select = $this->selectCoordinate;
        $toVN2000 = $this->toVN2000;

        $map = $this->map;
        $center = json_encode($this->center);
        $js = "var $name;
function initDraggableMarker(){
    $name = L.marker($center, { 'draggable': true,});
    $map.addLayer($name);        
    $name.on('dragend', function(e) {        
        updateLatLng(e.target._latlng.lat, e.target._latlng.lng);                                           
    });
};

function initBindingInput() {
    var lat = $('$inputY').val();
    var lng = $('$inputX').val();
    console.log(lat);
    console.log(lng);
    $('$inputX').keyup(function(){
        updateMarker(lat, $('$inputX').val());
    });
    $('$inputY').keyup(function(){
        updateMarker($('$inputY').val(), lng);
    });
            
}

function updateMarker(lat, lng) {
    
    $name.setLatLng(L.latLng(lat, lng));
    $map.flyTo($name.getLatLng(),15);        
}

function updateLatLng(lat, lng){
    var latlngs = [lng, lat];
    $('$inputY').val(latlngs[1]);
    $('$inputX').val(latlngs[0]); 
    
}

function initSearchInput(){
    $(\"#searchButton\").click(function () {
            var query = $(\".searchMap\").val();
            $.ajax({
                url: 'http://api.positionstack.com/v1/forward',
                data: {
                    access_key: 'e3aaa709ed7a3c1582bdfe4d30102055',
                    query: query,
                    country: 'VN',
                    region: 'HoChiMinh',
                    limit: 1
                }
            }).done(function (data) {
                if(data != 'undefined'){
                    var lat = data['data'][0]['latitude'];
                    var long = data['data'][0]['longitude'];
                
                    $(\"#can-geo_x\").val(long);
                    $(\"#can-geo_y\").val(lat);
                    if(lat != 'undefined' && long != 'undefined'){
                        updateMarker(lat, long);
                    }
                }
            });
    });
}

initDraggableMarker();
initBindingInput();
initSearchInput();
";
//        DebugService::dumpdie($js);
        return new JsExpression($js);
    }
}
