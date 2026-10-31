Vue.component('pagination', {
    props: ['current', 'pages'],
    data: function() {
        return {
            page: this.current
        }
    },
    template: `
    <ul class="pagination pagination-pager pagination-rounded justify-content-center my-3">
        <li class="page-item first" :class="page == 1 ? 'disabled' : ''"  @click="page = 1">
            <span class="page-link">⇤</span>
        </li>
        <li class="page-item prev" :class="page == 1 ? 'disabled' : ''"  @click="page = --page < 1 ? 1 : page">
            <span class="page-link">⇠</span>
        </li>
        <li class="page-item next" :class="page == pages ? 'disabled' : ''"  @click="page = ++page > pages ? pages : page">
            <span class="page-link">⇢</span>
        </li>
        <li class="page-item last" :class="page == pages ? 'disabled' : ''" @click="page = pages">
            <span class="page-link">⇥</span>
        </li>
    </ul>`,
    watch: {
        page: function() {
            this.$emit('change', this.page);
        }
    }
})

Vue.component('pagination-summary', {
    props: ['current', 'from', 'to', 'total'],
    template: `
    <h5 class="mb-0">
        Trang {{ current }}: <b>{{ from }}</b> - <b>{{ to }}</b> trong <b>{{ total }}</b> kết quả
    </h5>`
})

Vue.component('delete-modal', {
    props: {
        // deletewarning: String
    },
    template: `<div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="deleteModalLabel">HCDC - Y tế học đường</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5 class="warning-text">Bạn có chắc chắn xóa (bản Tiếng Anh và Tiếng Việt) phiếu thu thập này?</h5>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal" @click="$emit('delete')">Xóa</button>
            </div>
            </div>
        </div>
    </div>`
})

Vue.component('warning-address-modal-en', {
    props: {},
    template: `<div class="modal fade" id="warning-address-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="deleteModalLabel">Deparment of industry and trade</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5 class="warning-text">The factory coordinate has not been determined. Are you sure you want to continue?</h5>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" @click="$emit('save-without-address')">Save</button>
                </div>
            </div>
        </div>
    </div>`,
});

Vue.component('warning-address-modal-vn', {
    props: {},
    template: `<div class="modal fade" id="warning-address-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="deleteModalLabel">HCDC - Y tế học đường</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5 class="warning-text">Tọa độ <b>nhà máy</b> chưa được xác định. Bạn có chắc chắn muốn tiếp tục?</h5>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" @click="$emit('save-without-address')">Lưu</button>
                </div>
            </div>
        </div>
    </div>`,
})

Vue.component('send-survey-modal', {
    props: {
        slug: String,
        completevn: Boolean,
        completeen: Boolean,
        identifyaddress: Boolean,
    },
    data: function() {
        return {
            editVnForm: '/app/survey/edit-vn/' + this.slug,
            editEnForm: '/app/survey/edit-en/' + this.slug
        };
    },
    computed: {
        canSend: function() {
            return this.completevn && this.completeen && this.identifyaddress
        }
    },
    template: `<div class="modal fade" id="send-survey-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="deleteModalLabel">HCDC - Y tế học đường</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4>Trạng thái phiếu thu thập:</h4>
                <div class="loaded-data">
                    <h5><a :href="editEnForm">Biểu mẫu tiếng Anh</a>: 
                        <span class="text-success" v-if="completeen">Hoàn thành</span>
                        <span class="text-danger" v-if="!completeen">Chưa hoàn thành</span>
                        <a :href="editEnForm" class="ml-2"><i class="icon-pencil"></i></a>
                    </h5>
                    <h5><a :href="editVnForm">Biểu mẫu tiếng Việt</a>:
                        <span class="text-success" v-if="completevn">Hoàn thành</span>
                        <span class="text-danger" v-if="!completevn">Chưa hoàn thành</span>    
                        <a :href="editVnForm" class="ml-2"><i class="icon-pencil"></i></a>
                    </h5>
                    <h5><a href="#" @click="$emit('identify-address', slug)">Định vị địa chỉ nhà máy</a>: 
                        <span class="text-success" v-if="identifyaddress">Hoàn thành</span>
                        <span class="text-danger" v-if="!identifyaddress">Chưa hoàn thành</span>  
                        <a href="#" @click="$emit('identify-address', slug)" class="ml-2"><i class="icon-pencil"></i></a> 
                    </h5>
                </div>
                <hr>
                <h5 class="warning-text" v-if="canSend">Bạn có chắc chắn gửi (bản Tiếng Anh và Tiếng Việt) phiếu thu thập này cho HCDC - Y tế học đường?</h5>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal" @click="$emit('send')" v-if="canSend">Gửi</button>
            </div>
            </div>
        </div>
    </div>`,
})

Vue.component('reject-survey-modal', {
    props: {
        rejectmessage: String
    },
    template: `<div class="modal fade" id="reject-survey-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="deleteModalLabel">HCDC - Y tế học đường</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5 class="warning-text">Bạn có chắc chắn từ chối (bản Tiếng Anh và Tiếng Việt) phiếu thu thập này không? Chúng tôi sẽ gửi email tới đơn vị gửi phiếu thu thập này với nội dung: </h5>
                <h6 class="px-2"><i>{{ rejectmessage }}</i></h6>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal" @click="$emit('reject')">Từ chối</button>
            </div>
            </div>
        </div>
    </div>`
})

Vue.component('accept-survey-modal', {
    props: {
        sendingwarning: String
    },
    template: `<div class="modal fade" id="accept-survey-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="deleteModalLabel">HCDC - Y tế học đường</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5 class="warning-text">Bạn có chắc chắn tiếp nhận (bản Tiếng Anh và Tiếng Việt) phiếu thu thập này?</h5>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal" @click="$emit('accept')">Tiếp nhận</button>
            </div>
            </div>
        </div>
    </div>`
})

Vue.component('radio-button', {
    props: {
        value: [Number, String],
        selected: [Number, String],
        label: String,
        readonly: Boolean
    },
    template: `<div class="form-check form-check-inline" :class="readonly ? 'readonly' : ''">
        <label class="form-check-label">
            <div class="uniform-choice" :class="readonly ? 'readonly' : ''">
                <span :class="selected == value ? 'checked' : ''">
                    <input type="radio" class="form-check-input-styled" checked="" data-fouc="" @click="$emit('choose', value)" :disabled="readonly">
                </span>
            </div>
            {{ label }}
        </label>
    </div>`
})

Vue.component('checkbox-button', {
    props: {
        value: [Number, String],
        selected: Array,
        label: String,
        readonly: Boolean
    },
    template: `<div class="form-check form-check-inline" :class="readonly ? 'readonly' : ''">
        <label class="form-check-label">
            <div class="uniform-checker" :class="readonly ? 'readonly' : ''">
                <span :class="selected.includes(value) ? 'checked' : ''">
                    <input type="checkbox" class="form-check-input-styled" checked="" data-fouc="" @click="toggleSelect" :disabled="readonly">
                </span>
            </div>
            {{ label }}
        </label>
    </div>`,
    methods: {
        toggleSelect: function() {
            if(this.selected.includes(this.value)) {
                let index = this.selected.indexOf(this.value)
                this.selected.splice(index, 1)
            } else {
                this.selected.push(this.value)
            }
        }
    }
})

Vue.component('alert-modal', {
    props: ['message', 'url'],
    template: `<div class="modal fade" id="alert-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body py-2">
                    <h6 class="warning-text">{{ message }}</h6>
                </div>
                <div class="modal-footer py-2 bg-light">
                    <a :href="url" class="btn btn-secondary">OK</a>
                </div>
            </div>
        </div>
    </div>`
})

Vue.component('upload-image', {
    props: {
        inputname: String,
        label: String,
        path: String,
    },
    data: function() {
        return {
            chooseImage: this.path ? true : false,
            progressUpload: this.path ? 100 : 0,
            cpath: this.path,
            completeUpload: false,
            fullpath: this.path == undefined ? null : '/uploads/' + this.path
        }
    },
    template: `<div class="file-upload-wrap p-2" style="height: 250px; background: rgba(0, 0, 0, .2); border-radius: .1875rem; border: 1.5px dashed rgba(0, 0, 0, .3);" v-cloak>
        <div class="file-upload h-100 d-flex flex-column justify-content-center align-items-center position-relative" v-if="!chooseImage">
            <h5 class="mb-0 file-upload-title">{{ label }}</h5>
            <h6>(jpg, jpeg, png)</h6>
            <input type="file" accept=".jpg, .jpeg, .png" class="position-absolute top-0 w-100 h-100 opacity-0 cursor-pointer" @change="readImageInfo">
        </div>
        <div class="file-uploaded image-wrap h-100 d-flex flex-column justify-content-center align-items-center position-relative" v-else>
            <div class="d-flex position-relative my-2 w-50" v-if="progressUpload >= 0 && fullpath == null">
                <div class="progress progress-image w-100" style="height: 1rem;">
                    <div class="progress-bar progress-bar-striped bg-primary" :style="'width: ' + progressUpload + '%'">
                        <span>{{ progressUpload }}%</span>
                    </div>
                </div>
            </div>   
            <div class="h-100 w-100 d-flex flex-column justify-content-center align-items-center" v-if="fullpath != null">
                <img class="image" :src="fullpath" alt="Y tế học đường" style="max-height:80%;max-width:90%;object-fit:contain;">
                <a href="#" class="delete-file cursor-pointer text-danger my-1" @click="removeImage">
                    <i class="icon-cancel-circle2 mr-2"></i>Remove Photo
                </a>
            </div>
            <p class="mb-0" v-else>Đang xử lý...</p>
        </div>
        <input type="hidden" :name="inputname" v-model="cpath">
    </div>
    `,
    methods: {
        readImageInfo: function(e) {
            var _this = this,
                input = e.target,
                api = '/app/file/upload';

            this.uploadImage(input.files, api, (resp) => {
                if (resp.fails.length > 0) {
                    toastMessage('error', 'Không thể tải lên hình ảnh: ' + resp.fails[0]);
                }
                if (resp.successes.length > 0) {
                    _this.cpath = resp.successes[0].path;
                    _this.fullpath = '/uploads/' + _this.cpath;
                    _this.$emit('uploaded', _this.cpath);
                }
            })
        },

        uploadImage: function(files, api, callback) {
            var _this = this;
            var form = new FormData();
            var file = files[0];

            if (['image/jpeg', 'image/jpg', 'image/png'].indexOf(file.type) == -1) {
                toastMessage('error', file.name + ': Không đúng định dạng được hỗ trợ: jpg/jpeg/png')
            } else if (file.size > 10485760) {
                toastMessage('error', file.name + ': Vượt quá kích thước tệp tối đa: 10MB')
            } else {
                form.append('Files[]', file, file.name);
                _this.chooseImage = true
            }

            if(form.has('Files[]')) {
                var xhr = new XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(evt){
                    if (evt.lengthComputable) {
                        var percent = (evt.loaded / evt.total) * 100;
                        console.log(percent);
                        _this.progressUpload = Math.round(percent)
                    }
                }, false)
                xhr.addEventListener('load', function(evt) {
                    if(this.status == 200) {
                        var resp = JSON.parse(this.response)
                        callback(resp)
                    }
                }, false);
                xhr.addEventListener('error', function(evt) {
                    _this.progressUpload = -1
                }, false);

                xhr.open('POST', api);
                xhr.send(form);
            }
        },

        removeImage: function(e) {
            e.preventDefault();
            // this.deleteFile(this.cpath);
            this.cpath = null;
            this.fullpath = null;
            this.chooseImage = false;
            this.progressUpload = 0;

            this.$emit('deleted');
        },

        deleteFile: function(file) {
            var api = '/app/file/delete',
                data = {
                    file: file
                }
            sendAjax(api, data, function(resp) {})
        },
    }
})

Vue.component('image-viewer', {
    props: {
        label: String,
        path: String
    },
    template: `<div class="file-upload-wrap p-2" style="height: 250px; background: rgba(0, 0, 0, .2); border-radius: .1875rem; border: 1.5px dashed rgba(0, 0, 0, .3);" v-cloak>   
        <div class="file-uploaded image-wrap h-100">
            <div class="h-100 w-100 d-flex flex-column justify-content-center align-items-center">
                <img class="image" :src="'/uploads/' + path" alt="Y tế học đường" style="max-height:80%;max-width:90%;object-fit:contain;" v-if="path">
                <h6 class="mt-2 mb-0">{{ label }}</h6>
            </div>
        </div>
    </div>`,
})

Vue.component('map-picker', {
    props: {
        lat: [String, Number],
        lng: [String, Number]
    },
    data: function() {
        return {
            map: null,
            layers: {
                base: [],
                overlay: []
            },
            icons: {},
            controls: {},
            MARKER: null,
            latVal: this.lat ? this.lat : 10.762622,
            lngVal: this.lng ? this.lng : 106.660172
        }
    },
    mounted: function() {
        let _this = this;
        _this.$nextTick(function() {
            this.map = L.map('factory-address-map', {
                minZoom: 0,
                maxZoom: 22
            }).setView([this.latVal, this.lngVal], 14);
            this.initControl();
            this.initExtends();
            this.registerEventInvalidateMaps();
        });
    },
    watch: {
        latVal: function() {
            let _this = this
            _this.MARKER.setLatLng([_this.latVal, _this.lngVal]);
            _this.$emit('changelat', _this.latVal);
        },

        lngVal: function() {
            let _this = this
            _this.MARKER.setLatLng([_this.latVal, _this.lngVal]);
            _this.$emit('changelng', _this.lngVal);
        }
    },
    methods: {
        initControl: function() {
            let _this = this
            _this.initBaseLayer();
            _this.initHstsLayer();
            _this.initSearchPlaceControl();
            _this.initLocationGPS();
        },

        initHstsLayer: function() {
            let _this = this
            _this.layers.overlay['hstsLayer'] = L.tileLayer.wms('https://wmsv1.hcmgis.vn/geoserver/geodb/wms', {
                layers: 'layers=geodb:vietnam_hoangsa_truongsa_group',
                format: 'image/png',
                transparent: true,
                minZoom: 0,
                maxZoom: 22,
            }).addTo(_this.map)
        },
    
        initExtends: function() {
            let _this = this
            _this.initDragMarker([_this.latVal, _this.lngVal]);
            _this.initClickToMapEvent();
        },
    
        initBaseLayer: function() {
            let _this = this
            _this.layers.base['Google Maps'] = L.tileLayer('http://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                minZoom: 0,
                maxZoom: 22,
                attribution: 'Google Maps'
            }).addTo(_this.map)
        },

        initLocationGPS: function() {
            let _this = this
            _this.controls.locate = L.control.locate({
                iconElementTag: 'i',
                icon: 'icon-target',
                iconLoading: 'icon-spinner3 spinner'
            });
            _this.map.on('locationfound', function(e) {
                _this.latVal = e.latlng.lat
                _this.lngVal = e.latlng.lng
            })
            _this.map.addControl(_this.controls.locate);
        },
    
        initSearchPlaceControl: function() {
            let _this = this;
            let placeControlId = '_searchplacecontrol';
            let placeControlItemsId = '_placecontrolitems';
    
            let placeControl = $('#' + placeControlId);
            let placeControlItems = $('#' + placeControlItemsId)
            placeControl.on('input', function(e) {
                $.ajax({
                    url: 'https://places.demo.api.here.com/places/v1/discover/search?app_id=zSfLmO4akpNNRkXp0CG9&app_code=Qx4lDVRUvipDhgpvpMjFFg&at=10.7974,106.7348&q=' + placeControl.val(),
                    success: function(e) {
                        let items = e.results.items;
                        placeControlResults = e.results;
                        placeControlItems.empty();
                        for(let i=0; i<items.length; i++) {
                            let item = items[i];
                            let placeItemHtml = "<div class='place-item' style='padding: 5px; cursor: pointer' data-idx='"+ i +"'>"+ item.title +"</div>";
                            placeControlItems.append(placeItemHtml);
                        }
                        placeControlItems.on('click', '.place-item', function(event) {
                            let idx = $(this).attr('data-idx');
                            let item = placeControlResults.items[idx];
                            if (_this.MARKER != undefined) {
                                _this.latVal = item.position[0];
                                _this.lngVal = item.position[1];
                            };
                            _this.map.setView(item.position, 16);
                            $('#' + placeControlItemsId).empty();
                        })
                    }
                })
            })
        },
    
        initDragMarker: function(coords) {
            let _this = this
            _this.MARKER = L.marker(coords, {
                draggable: true
            }).bindPopup('<p>Move the marker or manually enter in the <b>Lat</b> and <b>Lng</b> below to update your image coordinates</p>');
            _this.MARKER.addTo(_this.map);
            _this.map.setView(coords);
            _this.initBindingMarkerAndGeometryInput();
        },
    
        initClickToMapEvent: function() {
            let _this = this
            this.map.on('click', function(e) {
                if (_this.MARKER != undefined) {
                    _this.latVal = e.latlng.lat
                    _this.lngVal = e.latlng.lng
                };
            })
        },
    
        initBindingMarkerAndGeometryInput: function() {
            let _this = this;
            _this.MARKER.on('dragend', function(e) {
                let latlng = e.target._latlng;
                _this.latVal = latlng.lat
                _this.lngVal = latlng.lng
            })
        },

        registerEventInvalidateMaps: function() {
            let _this = this;
            $('#map-picker-modal').on('shown.bs.modal', () => {
                _this.map.invalidateSize()
            })
        },
    },
    template: `
    <div class="modal fade" id="map-picker-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="deleteModalLabel">HCDC - Y tế học đường</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="factory-address-map" style="height: 400px">
                        <div class="row m-0 position-relative overflow-hidden h-100">
                            <div id="factory-address-map" class="col-12 p-0 h-100" style="z-index: 99"></div>
                            <div id="_placecontrolcontainer" class="position-absolute" style="width: 250px; top: 12px; left: 58px; z-index: 1000; background: #fff">
                                <input id="_searchplacecontrol" placeholder="Address" class="form-control px-2">
                                <div id="_placecontrolitems" class="place-items"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" @click="$emit('pick-location', latVal, lngVal)">OK</button>
                </div>
            </div>
        </div>
    </div>`,
});

Vue.component('map-viewer', {
    props: {
        lat: [String, Number],
        lng: [String, Number]
    },
    data: function() {
        return {
            map: null,
            layers: {
                base: [],
                overlay: []
            },
            icons: {},
            controls: {},
            MARKER: null,
            latVal: this.lat ? this.lat : 10.762622,
            lngVal: this.lng ? this.lng : 106.660172
        }
    },
    mounted: function() {
        let _this = this;
        _this.$nextTick(function() {
            this.map = L.map('factory-address-map', {
                minZoom: 0,
                maxZoom: 22
            }).setView([this.latVal, this.lngVal], 14);
            this.initControl();
            this.initMarker([_this.latVal, _this.lngVal]);
            this.registerEventInvalidateMaps();
        });
    },
    methods: {
        initControl: function() {
            let _this = this
            _this.initBaseLayer();
            _this.initHstsLayer();
        },

        initHstsLayer: function() {
            let _this = this
            _this.layers.overlay['hstsLayer'] = L.tileLayer.wms('https://wmsv1.hcmgis.vn/geoserver/geodb/wms', {
                layers: 'layers=geodb:vietnam_hoangsa_truongsa_group',
                format: 'image/png',
                transparent: true,
                minZoom: 0,
                maxZoom: 22,
            }).addTo(_this.map)
        },
    
        initBaseLayer: function() {
            let _this = this
            _this.layers.base['Google Maps'] = L.tileLayer('http://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                minZoom: 0,
                maxZoom: 22,
                attribution: 'Google Maps'
            }).addTo(_this.map)
        },
    
        initMarker: function(coords) {
            let _this = this
            _this.MARKER = L.marker(coords);
            _this.MARKER.addTo(_this.map);
            _this.map.setView(coords);
        },

        registerEventInvalidateMaps: function() {
            let _this = this;
            $('#map-picker-modal').on('shown.bs.modal', () => {
                _this.map.invalidateSize()
            })
        },
    },
    template: `
    <div class="modal fade" id="map-picker-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="deleteModalLabel">HCDC - Y tế học đường</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                
                    <div class="factory-address-map" style="height: 400px">
                        <div class="row m-0 position-relative overflow-hidden h-100">
                            <div id="factory-address-map" class="col-12 p-0 h-100" style="z-index: 99"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>`,
})

Vue.component('map-identify-location', {
    props: {
        lat: [String, Number],
        lng: [String, Number],
        id: [String, Number]
    },
    data: function() {
        return {
            map: null,
            layers: {
                base: [],
                overlay: []
            },
            icons: {},
            controls: {},
            MARKER: null,
            latVal: this.lat ? this.lat : 10.762622,
            lngVal: this.lng ? this.lng : 106.660172
        }
    },
    mounted: function() {
        let _this = this;
        _this.$nextTick(function() {
            _this.map = L.map('map-identify-location-' + _this.id, {
                minZoom: 0,
                maxZoom: 22
            }).setView([_this.latVal, _this.lngVal], 6);
            _this.initControl();
            _this.initExtends();
            _this.$emit('changelat', _this.latVal);
            _this.$emit('changelng', _this.lngVal);
            
        });
    },
    watch: {
        latVal: function() {
            let _this = this
            _this.MARKER.setLatLng([_this.latVal, _this.lngVal]);
            _this.$emit('changelat', _this.latVal);
        },

        lngVal: function() {
            let _this = this
            _this.MARKER.setLatLng([_this.latVal, _this.lngVal]);
            _this.$emit('changelng', _this.lngVal);
        }
    },
    methods: {
        initControl: function() {
            let _this = this
            _this.initBaseLayer();
            _this.initHstsLayer();
            _this.initSearchPlaceControl();
            _this.initLocationGPS();
        },

        initHstsLayer: function() {
            let _this = this
            _this.layers.overlay['hstsLayer'] = L.tileLayer.wms('https://wmsv1.hcmgis.vn/geoserver/geodb/wms', {
                layers: 'layers=geodb:vietnam_hoangsa_truongsa_group',
                format: 'image/png',
                transparent: true,
                minZoom: 0,
                maxZoom: 22,
            }).addTo(_this.map)
        },

        initLocationGPS: function() {
            let _this = this
            _this.controls.locate = L.control.locate({
                iconElementTag: 'i',
                icon: 'icon-target',
                iconLoading: 'icon-spinner3 spinner'
            });
            _this.map.on('locationfound', function(e) {
                _this.latVal = e.latlng.lat
                _this.lngVal = e.latlng.lng
            })
            _this.map.addControl(_this.controls.locate);
        },
    
        initExtends: function() {
            let _this = this
            _this.initDragMarker([_this.latVal, _this.lngVal]);
            _this.initClickToMapEvent();
        },
    
        initBaseLayer: function() {
            let _this = this
            _this.layers.base['Google Maps'] = L.tileLayer('http://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                minZoom: 0,
                maxZoom: 22,
                attribution: 'Google Maps'
            }).addTo(_this.map)
        },
    
        initSearchPlaceControl: function() {
            let _this = this;
            let placeControlId = '_searchplacecontrol-' + this.id;
            let placeControlItemsId = '_placecontrolitems-' + this.id;
    
            let placeControl = $('#' + placeControlId);
            let placeControlItems = $('#' + placeControlItemsId)
            placeControl.on('input', function(e) {
                $.ajax({
                    url: 'https://places.demo.api.here.com/places/v1/discover/search?app_id=zSfLmO4akpNNRkXp0CG9&app_code=Qx4lDVRUvipDhgpvpMjFFg&at=10.7974,106.7348&q=' + placeControl.val(),
                    success: function(e) {
                        let items = e.results.items;
                        placeControlResults = e.results;
                        placeControlItems.empty();
                        for(let i=0; i<items.length; i++) {
                            let item = items[i];
                            let placeItemHtml = "<div class='place-item' style='padding: 5px; cursor: pointer' data-idx='"+ i +"'>"+ item.title +"</div>";
                            placeControlItems.append(placeItemHtml);
                        }
                        placeControlItems.on('click', '.place-item', function(event) {
                            let idx = $(this).attr('data-idx');
                            let item = placeControlResults.items[idx];
                            if (_this.MARKER != undefined) {
                                _this.latVal = item.position[0];
                                _this.lngVal = item.position[1];
                            };
                            _this.map.setView(item.position, 16);
                            $('#' + placeControlItemsId).empty();
                        })
                    }
                })
            })
        },
    
        initDragMarker: function(coords) {
            let _this = this
            _this.MARKER = L.marker(coords, {
                draggable: true
            }).bindPopup('<p>Move the marker or manually enter in the <b>Lat</b> and <b>Lng</b> below to update your image coordinates</p>');
            _this.MARKER.addTo(_this.map);
            _this.map.setView(coords, 16);
            _this.initBindingMarkerAndGeometryInput();
        },
    
        initClickToMapEvent: function() {
            let _this = this
            this.map.on('click', function(e) {
                if (_this.MARKER != undefined) {
                    _this.latVal = e.latlng.lat
                    _this.lngVal = e.latlng.lng
                };

                $('#_placecontrolitems-' + _this.id).empty();
            })
        },
    
        initBindingMarkerAndGeometryInput: function() {
            let _this = this;
            _this.MARKER.on('dragend', function(e) {
                let latlng = e.target._latlng;
                _this.latVal = latlng.lat
                _this.lngVal = latlng.lng

                $('#_placecontrolitems-' + _this.id).empty();
            })
        }
    },
    template: `<div class="map-identify-location" style="height: 400px">
        <div class="row m-0 position-relative overflow-hidden h-100" style="border-radius: .1875rem">
            <div :id="'map-identify-location-' + id" class="col-12 p-0 h-100" style="z-index: 99"></div>
            <div :id="'_placecontrolcontainer-' + id" class="position-absolute" style="width: 250px; top: 12px; left: 58px; z-index: 1000; background: #fff">
                <input :id="'_searchplacecontrol-' + id" placeholder="Nhập vào địa chỉ cần tìm..." class="form-control px-2">
                <div :id="'_placecontrolitems-' + id" class="place-items"></div>
            </div>
        </div>
    </div>`,
})

Vue.component('identify-address-modal', {
    props: {
        url: String
    },
    mounted: function() {
        let _this = this;
        $('#identify-address-modal').on('shown.bs.modal', () => {
            _this.selectUrl();
        });
    },
    template: `<div class="modal fade" id="identify-address-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="shareModalLabel">HCDC - Y tế học đường</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="zalo-share-button" :data-href="url" data-oaid="534475732021789422" data-layout="4" data-color="blue" data-customize="false"></div>
                </div>
                <div class="form-group">
                    <label for="">Sao chép liên kết vào thiết bị di động để cập nhật vị trí chính xác của nhà máy:</label>
                    <input type="text" :value="url" class="form-control" id="identify-url">
                </div>
                <div class="form-group">
                    <label for="">Hoặc truy cập ngay:</label>
                    <a :href="url">Định vị nhà máy</a>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal" @click="copyIdentifyAddressLink">Sao chép</button>
            </div>
            </div>
        </div>
    </div>`,
    methods: {
        copyIdentifyAddressLink: function() {
            this.selectUrl();
            document.execCommand("copy");
            toastMessage('success', 'Đã sao chép liên kết');
        },

        selectUrl: function() {
            var url = document.getElementById('identify-url');
            url.select();
            url.setSelectionRange(0, 99999);
        }
    }
})

Vue.component('delete-user-modal', {
    props: {
        user: Object
    },
    template: `<div class="modal fade" id="delete-user-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="deleteModalLabel">HCDC - Y tế học đường</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5 class="warning-text">Bạn có chắc chắn xóa người dùng {{ user.fullname }} ({{ user.username }})?</h5>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" @click="$emit('delete')">Xóa</button>
                </div>
            </div>
        </div>
    </div>`
})

Vue.component('confirm-email-modal', {
    props: {
        user: Object
    },
    template: `<div class="modal fade" id="confirm-email-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="confirmModalLabel">HCDC - Y tế học đường</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5 class="warning-text">Bạn có chắc chắn xác thực người dùng {{ user.fullname }} ({{ user.username }})?</h5>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" @click="$emit('confirm')">Xác nhận</button>
                </div>
            </div>
        </div>
    </div>`
})