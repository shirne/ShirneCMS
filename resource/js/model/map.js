
(function(window,$) {


    var apis = {
        'baidu': '//api.map.baidu.com/api?ak=__KEY__&v=1.5&services=false&callback=',
        'google': '//maps.google.com/maps/api/js?key=__KEY__&callback=', //&language=zh
        'tencent': '//map.qq.com/api/js?v=2.exp&key=__KEY__&callback=',
        'gaode': '//webapi.amap.com/maps?v=1.3&key=__KEY__&callback='
    };

    function loadScript(src) {
        var script = document.createElement("script");
        script.type = "text/javascript";
        script.src = src;
        document.body.appendChild(script);
    }

    var mapObj,mapBox,onPick;

    function InitMap(maptype,box,callback,locate) {
        if (mapObj) mapObj.hide();
        mapBox=$(box);
        onPick=callback;

        switch (maptype.toLowerCase()) {
            case 'baidu':
                mapObj = new BaiduMap();
                break;
            case 'google':
                mapObj = new GoogleMap();
                break;
            case 'tencent':
            case 'qq':
                mapObj = new TencentMap();
                break;
            case 'gaode':
                mapObj = new GaodeMap();
                break;
        }
        if (!mapObj) return dialog.warning('不支持该地图类型');
        if(locate){
            if(typeof locate==='string'){
                var loc=locate.split(',');
                locate={
                    lng:parseFloat(loc[0]),
                    lat:parseFloat(loc[1])
                }
            }
            mapObj.locate=locate;
        }
        mapObj.setMap();

        return mapObj;
    }

    function BaseMap(type) {
        if(type !== undefined) {
            var key = 'MAPKEY_' + type.toUpperCase();

            if (!window[key]) {
                throw 'Pls define the ' + key + ' on window.';
            }

            this.mapkey = window[key];
            this.mapType = type;
            this.ishide = false;
            this.isshow = false;
            this.toshow = false;
            this.marker = null;
            this.infoWindow = null;
            this.mapbox = null;
            this.locate = null;
            this.map = null;
        }
    }

    BaseMap.prototype.isAPIReady = function () {
        return false;
    };
    BaseMap.prototype.pointToObject = function () {
    };
    BaseMap.prototype.objectToPoint = function () {
    };
    BaseMap.prototype.setMap = function () {
    };
    BaseMap.prototype.showInfo = function () {
    };
    BaseMap.prototype.getCenter = function () {
    };
    BaseMap.prototype.getAddress = function (rs) {
        return "";
    };
    BaseMap.prototype.getAddressComponent = function (rs) {
        return {};
    };
    BaseMap.prototype.setLocate = function (address) {
    };

    BaseMap.prototype.loadAPI = function () {
        var self = this;
        if (!this.isAPIReady()) {
            this.mapbox = $('<div id="' + this.mapType + 'map" class="mapbox">loading...</div>');
            mapBox.append(this.mapbox);

            //console.log(this.mapType+' maploading...');
            var func = 'mapload' + new Date().getTime();
            window[func] = function () {
                self.setMap();
                delete window[func];
            };
            var apiurl=apis[this.mapType].replace('__KEY__',this.mapkey);
            loadScript(apiurl + func);
            return false;
        } else {
            //console.log(this.mapType + ' maploaded');
            this.mapbox = $('#' + this.mapType + 'map');
            if (this.mapbox.length < 1) {
                this.mapbox = $('<div id="' + this.mapType + 'map" class="mapbox"></div>');
                mapBox.append(this.mapbox);
            }
            return true;
        }
    };
    BaseMap.prototype.setInfoContent = function () {
        if (!this.infoWindow) return;
        var title = '<b>当前位置</b>';
        var addr = '<p style="line-height:1.6em;"></p>';
        if (this.infoWindow.setTitle) {
            this.infoWindow.setTitle(title);
            this.infoWindow.setContent(addr);
        } else {
            var content = '<h3>' + title + '</h3><div style="width:250px">' + addr + '</div>';
            this.infoWindow.setContent(content);
        }
    };
    BaseMap.prototype.showAtCenter=function () {
        var center=this.getCenter();
        this.showInfo(center);
    };
    BaseMap.prototype.showLocationInfo = function (pt, rs) {

        this.showInfo();
        var address=this.getAddress(rs);
        var addressComponent=this.getAddressComponent(rs);
        var locate={};
        if (typeof (pt.lng) === 'function') {
            locate.lng=pt.lng();
            locate.lat=pt.lat();
        } else {
            locate.lng=pt.lng;
            locate.lat=pt.lat;
        }

        onPick(address,locate,addressComponent);
    };
    BaseMap.prototype.show = function () {
        this.ishide = false;
        this.setMap();
        this.showInfo();
    };
    BaseMap.prototype.hide = function () {
        this.ishide = true;
        if (this.infoWindow) {
            this.infoWindow.close();
        }
        if (this.mapbox) {
            $(this.mapbox).remove();
        }
    };


    function BaiduMap() {
        BaseMap.call(this, "baidu");
    }

    BaiduMap.prototype = new BaseMap();
    BaiduMap.prototype.constructor = BaiduMap;
    BaiduMap.prototype.isAPIReady = function () {
        return !!window['BMap'];
    };

    BaiduMap.prototype.setMap = function () {
        var self = this;
        if (this.isshow || this.ishide) return;
        if (!this.loadAPI()) return;

        var map = self.map = new BMap.Map(this.mapbox.attr('id')); //初始化地图
        map.addControl(new BMap.NavigationControl());  //初始化地图控件
        map.addControl(new BMap.ScaleControl());
        map.addControl(new BMap.OverviewMapControl());
        map.enableScrollWheelZoom();

        if(!this.locate)this.locate = this.getCenter()
        var point = this.objectToPoint(this.locate);
        
        map.centerAndZoom(point, 15); //初始化地图中心点
        this.marker = new BMap.Marker(point); //初始化地图标记
        this.marker.enableDragging(); //标记开启拖拽

        var gc = new BMap.Geocoder(); //地址解析类
        //添加标记拖拽监听
        this.marker.addEventListener("dragend", function (e) {
            //获取地址信息
            gc.getLocation(e.point, function (rs) {
                self.showLocationInfo(e.point, rs);
            });
        });

        //添加标记点击监听
        this.marker.addEventListener("click", function (e) {
            gc.getLocation(e.point, function (rs) {
                self.showLocationInfo(e.point, rs);
            });
        });

        map.addOverlay(this.marker); //将标记添加到地图中

        gc.getLocation(point, function (rs) {
            self.showLocationInfo(point, rs);
        });

        this.infoWindow = new BMap.InfoWindow("", {
            width: 250,
            title: ""
        });

        this.isshow = true;
        if (this.toshow) {
            this.showInfo();
            this.toshow = false;
        }
    };
    BaiduMap.prototype.pointToObject = function (point) {
        return {
            lng:point.lng,
            lat:point.lat
        };
    };
    BaiduMap.prototype.objectToPoint = function (object) {
        return new BMap.Point(parseFloat(object.lng),parseFloat(object.lat));
    };
    BaiduMap.prototype.getCenter = function () {
        var center=this.map.getCenter();
        return this.pointToObject(center);
    };

    BaiduMap.prototype.showInfo = function (point) {
        if (this.ishide) return;
        if (!this.isshow) {
            this.toshow = true;
            return;
        }
        if(point){
            this.locate=point;
            this.marker.panTo(new BMap.Point(point.lng,point.lat));
        }
        this.setInfoContent();

        this.marker.openInfoWindow(this.infoWindow);
    };
    BaiduMap.prototype.getAddress = function (rs) {
        var addComp = rs.addressComponents;
        if(addComp) {
            return addComp.province + ", " + addComp.city + ", " + addComp.district + ", " + addComp.street + ", " + addComp.streetNumber;
        }
    };
    BaiduMap.prototype.getAddressComponent = function (rs) {
        return rs.addressComponents;
    };
    BaiduMap.prototype.setLocate = function (address) {
        // 创建地址解析器实例
        var myGeo = new BMap.Geocoder();
        var self = this;
        myGeo.getPoint(address, function (point) {
            if (point) {
                self.map.centerAndZoom(point, 11);
                self.marker.setPosition(point);
                myGeo.getLocation(point, function (rs) {
                    self.showLocationInfo(point, rs);
                });
            } else {
                alert("地址信息不正确，定位失败");
            }
        }, '');
    };


    function GoogleMap() {
        BaseMap.call(this, "google");
        this.infoOpts = {
            width: 250,     //信息窗口宽度
            //   height: 100,     //信息窗口高度
            title: ""  //信息窗口标题
        };
    }

    GoogleMap.prototype = new BaseMap();
    GoogleMap.prototype.constructor = GoogleMap;
    GoogleMap.prototype.isAPIReady = function () {
        return window['google'] && window['google']['maps']
    };
    GoogleMap.prototype.setMap = function () {
        var self = this;
        if (this.isshow || this.ishide) return;
        if (!this.loadAPI()) return;

        //说明地图已切换
        if (this.mapbox.length < 1) return;

        var map = self.map = new google.maps.Map(this.mapbox[0], {
            zoom: 15,
            draggable: true,
            scaleControl: true,
            streetViewControl: true,
            zoomControl: true
        });

        //获取经纬度坐标值
        if(!this.locate)this.locate = this.getCenter()
        var point = this.objectToPoint(this.locate);
        map.panTo(point);
        this.marker = new google.maps.Marker({position: point, map: map, draggable: true});


        var gc = new google.maps.Geocoder();

        this.marker.addListener("dragend", function () {
            point = self.marker.getPosition();
            gc.geocode({'location': point}, function (rs) {
                self.showLocationInfo(point, rs);
            });
        });

        //添加标记点击监听
        this.marker.addListener("click", function () {
            point = self.marker.getPosition();
            gc.geocode({'location': point}, function (rs) {
                self.showLocationInfo(point, rs);
            });
        });

        gc.geocode({'location': point}, function (rs) {
            self.showLocationInfo(point, rs);
        });
        this.infoWindow = new google.maps.InfoWindow({map: map});
        this.infoWindow.setPosition(point);

        this.isshow = true;
        if (this.toshow) {
            this.showInfo();
            this.toshow = false;
        }
    };

    GoogleMap.prototype.pointToObject = function (point) {
        return {
            lng:point.lng,
            lat:point.lat
        };
    };
    GoogleMap.prototype.objectToPoint = function (object) {
        return new google.maps.LatLng(parseFloat(object.lat),parseFloat(object.lng));
    };
    GoogleMap.prototype.getCenter = function () {
        var center=this.map.getCenter();
        return this.pointToObject(center);
    };

    GoogleMap.prototype.showInfo = function (point) {
        if (this.ishide) return;
        if (!this.isshow) {
            this.toshow = true;
            return;
        }
        if(point){
            this.locate=point;
            this.marker.setPosition(this.objectToPoint(point));
        }
        this.infoWindow.setOptions({position: this.marker.getPosition()});
        this.setInfoContent();

    };
    GoogleMap.prototype.getAddress = function (rs, status) {
        if (rs && rs[0]) {
            return rs[0].formatted_address;
        }
    };
    GoogleMap.prototype.getAddressComponent = function (rs, status) {
        var result={};
        if (rs && rs[0]) {
            var comps=rs[0].address_components
            if(comps[0])result.country=comps[0].long_name
            if(comps[1])result.province=comps[1].long_name
            if(comps[2])result.city=comps[2].long_name
            if(comps[3])result.district=comps[3].long_name
            if(comps[4])result.street=comps[4].long_name
            if(comps[5])result.address=comps[5].long_name
        }
        return result;
    };
    GoogleMap.prototype.setLocate = function (address) {
        // 创建地址解析器实例
        var myGeo = new google.maps.Geocoder();
        var self = this;
        myGeo.getPoint(address, function (point) {
            if (point) {
                self.map.centerAndZoom(point, 11);
                self.marker.setPosition(point);
                myGeo.getLocation(point, function (rs) {
                    self.showLocationInfo(point, rs);
                });
            } else {
                alert("地址信息不正确，定位失败");
            }
        }, '');
    };

    function TencentMap() {
        BaseMap.call(this, "tencent");
    }

    TencentMap.prototype = new BaseMap();
    TencentMap.prototype.constructor = TencentMap;
    TencentMap.prototype.isAPIReady = function () {
        return window['qq'] && window['qq']['maps'];
    };
    TencentMap.prototype.pointToObject = function (point) {
        return {
            lng:point.getLng(),
            lat:point.getLat()
        };
    };
    TencentMap.prototype.objectToPoint = function (object) {
        return new qq.maps.LatLng(parseFloat(object.lat),parseFloat(object.lng));
    };
    TencentMap.prototype.setMap = function () {
        var self = this;
        if (this.isshow || this.ishide) return;
        if (!this.loadAPI()) return;


        //初始化地图
        var map = self.map = new qq.maps.Map(this.mapbox[0], {zoom: 15});
        //初始化地图控件
        new qq.maps.ScaleControl({
            align: qq.maps.ALIGN.BOTTOM_LEFT,
            margin: qq.maps.Size(85, 15),
            map: map
        });
        //map.addControl(new BMap.OverviewMapControl());
        //map.enableScrollWheelZoom();

        //获取经纬度坐标值
        if(!this.locate)this.locate = this.getCenter()
        var point = this.objectToPoint(this.locate);
        map.panTo(point); //初始化地图中心点

        //初始化地图标记
        this.marker = new qq.maps.Marker({
            position: point,
            draggable: true,
            map: map
        });
        this.marker.setAnimation(qq.maps.MarkerAnimation.DOWN);

        //地址解析类
        var gc = new qq.maps.Geocoder({
            complete: function (rs) {
                self.showLocationInfo(point, rs);
            }
        });

        qq.maps.event.addListener(this.marker, 'click', function () {
            point = self.marker.getPosition();
            gc.getAddress(point);
        });
        //设置Marker停止拖动事件
        qq.maps.event.addListener(this.marker, 'dragend', function () {
            point = self.marker.getPosition();
            gc.getAddress(point);
        });

        gc.getAddress(point);

        this.infoWindow = new qq.maps.InfoWindow({map: map});

        this.isshow = true;
        if (this.toshow) {
            this.showInfo();
            this.toshow = false;
        }
    };

    TencentMap.prototype.getCenter = function () {
        var center=this.map.getCenter();
        return this.pointToObject(center);
    };

    TencentMap.prototype.showInfo = function (point) {
        if (this.ishide) return;
        if (!this.isshow) {
            this.toshow = true;
            return;
        }
        if(point){
            this.locate=point;
            this.marker.setPosition(this.objectToPoint(point));
        }
        this.infoWindow.setPosition(this.marker.getPosition());
        this.infoWindow.open();
        this.setInfoContent();
    };

    TencentMap.prototype.getAddress = function (rs) {
        if(rs && rs.detail) {
            return rs.detail.address;
        }
        return "";
    };
    
    TencentMap.prototype.getAddressComponent = function (rs) {
        if(rs && rs.detail) {
            var comp = rs.detail.addressComponents
            if(!comp.province){
                comp.province=comp.city;
            }
            if(comp.streetNumber){
                comp.address=comp.streetNumber
            }
            return comp;
        }
        return {};
    };

    TencentMap.prototype.setLocate = function (address) {
        // 创建地址解析器实例
        var self = this;
        var myGeo = new qq.maps.Geocoder({
            complete: function (result) {
                if(result && result.detail && result.detail.location){
                    var point=result.detail.location;
                    self.map.setCenter(point);
                    self.marker.setPosition(point);
                    self.showLocationInfo(point, result);
                }else{
                    alert("地址信息不正确，定位失败");
                }
            },
            error:function(result){
                alert("地址信息不正确，定位失败");
            }
        });
        myGeo.getLocation(address);
    };


    function GaodeMap() {
        BaseMap.call(this, "gaode");
        this.infoOpts = {
            width: 250,     //信息窗口宽度
            //   height: 100,     //信息窗口高度
            title: ""  //信息窗口标题
        };
    }

    GaodeMap.prototype = new BaseMap();
    GaodeMap.prototype.constructor = GaodeMap;
    GaodeMap.prototype.isAPIReady = function () {
        return !!window['AMap']
    };

    GaodeMap.prototype.setMap = function () {
        var self = this;
        if (this.isshow || this.ishide) return;
        if (!this.loadAPI()) return;


        var map = self.map = new AMap.Map(this.mapbox.attr('id'), {
            resizeEnable: true,
            dragEnable: true,
            zoom: 13
        });
        map.plugin(["AMap.ToolBar", "AMap.Scale", "AMap.OverView"], function () {
            map.addControl(new AMap.ToolBar());
            map.addControl(new AMap.Scale());
            map.addControl(new AMap.OverView());
        });

        $('[name=txtLang]').unbind().on('change', function () {
            var lang = $(this).val();
            if (lang) map.setLang(lang);
        }).trigger('change');


        //获取经纬度坐标值
        if(!this.locate)this.locate = this.getCenter()
        var point = this.objectToPoint(this.locate);
        map.setCenter(point);

        this.marker = new AMap.Marker({position: point, map: map}); //初始化地图标记
        this.marker.setDraggable(true); //标记开启拖拽


        this.infoWindow = new AMap.InfoWindow();
        this.infoWindow.open(map, point);

        map.plugin(["AMap.Geocoder"], function () {
            var gc = new AMap.Geocoder(); //地址解析类
            //添加标记拖拽监听
            self.marker.on("dragend", function (e) {
                //获取地址信息
                gc.getAddress(e.lnglat, function (st, rs) {
                    self.showLocationInfo(e.lnglat, rs);
                });
            });

            //添加标记点击监听
            self.marker.on("click", function (e) {
                gc.getAddress(e.lnglat, function (st, rs) {
                    self.showLocationInfo(e.lnglat, rs);
                });
            });

            gc.getAddress(point, function (st, rs) {
                self.showLocationInfo(point, rs);
            });
        });

        this.isshow = true;
        if (this.toshow) {
            this.showInfo();
            this.toshow = false;
        }
    };
    GaodeMap.prototype.pointToObject = function (point) {
        return {
            lng:point.getLng(),
            lat:point.getLat()
        };
    };
    GaodeMap.prototype.objectToPoint = function (object) {
        return new AMap.LngLat(object.lng, object.lat);
    };
    GaodeMap.prototype.getCenter = function () {
        var center=this.map.center();
        return this.pointToObject(center);
    };

    GaodeMap.prototype.showInfo = function (point) {
        if (this.ishide) return;
        if (!this.isshow) {
            this.toshow = true;
            return;
        }
        if(point){
            this.marker.setPosition(this.objectToPoint(point));
        }
        this.setInfoContent();
        this.infoWindow.setPosition(this.marker.getPosition());
    };

    GaodeMap.prototype.getAddress = function (rs) {
        return rs.regeocode.formattedAddress;
    };
    GaodeMap.prototype.getAddressComponent = function (rs) {
        var comp = rs.regeocode.addressComponent
        return comp;
    };

    GaodeMap.prototype.setLocate = function (address) {
        // 创建地址解析器实例
        var myGeo = new AMap.Geocoder();
        var self = this;
        myGeo.getPoint(address, function (point) {
            if (point) {
                self.map.centerAndZoom(point, 11);
                self.marker.setPosition(point);
                myGeo.getLocation(point, function (rs) {
                    self.showLocationInfo(point, rs);
                });
            } else {
                alert("地址信息不正确，定位失败");
            }
        }, '');
    };

    window.InitMap=InitMap;
})(window,jQuery);