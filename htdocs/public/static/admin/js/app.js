function del(obj,msg) {
    dialog.confirm(msg,function(){
        location.href=$(obj).attr('href');
    });
    return false;
}

Number.prototype.format=function(fix){
    if(fix===undefined)fix=2;
    var num=this.toFixed(fix);
    var z=num.split('.');
    var format=[],f=z[0].split(''),l=f.length;
    for(var i=0;i<l;i++){
        if(i>0 && i % 3==0){
            format.unshift(',');
        }
        format.unshift(f[l-i-1]);
    }
    return format.join('')+(z.length==2?'.'+z[1]:'');
};
String.prototype.compile=function(data,list){

    if(list){
        var temps=[];
        for(var i in data){
            temps.push(this.compile(data[i]));
        }
        return temps.join("\n");
    }else{
        return this.replace(/\{@([\w\d\.]+)(?:\|([\w\d]+)(?:\s*=\s*([\w\d,\s#]+))?)?\}/g,function(all,m1,func,args){

            if(m1.indexOf('.')>0){
                var keys=m1.split('.'),val=data;
                for(var i=0;i<keys.length;i++){
                    if(val[keys[i]]){
                        val=val[keys[i]];
                    }else{
                        val = '';
                        break;
                    }
                }
                return callfunc(val,func,args);
            }else{
                return data[m1]?callfunc(data[m1],func,args,data):'';
            }
        });
    }
};

function callfunc(val,func,args,thisobj){
    if(!args){
        args=[val];
    }else{
        if(typeof args==='string')args=args.split(',');
        var argidx=args.indexOf('###');
        if(argidx>=0){
            args[argidx]=val;
        }else{
            args=[val].concat(args);
        }
    }
    //console.log(args);
    return window[func]?window[func].apply(thisobj,args):val;
}

function iif(v,m1,m2){
    if(v==='0')v=0;
    return v?m1:m2;
}

var dialogTpl='<div class="modal fade" id="{@id}" tabindex="-1" role="dialog" aria-labelledby="{@id}Label" aria-hidden="true">\n' +
    '    <div class="modal-dialog">\n' +
    '        <div class="modal-content">\n' +
    '            <div class="modal-header">\n' +
    '                <h4 class="modal-title" id="{@id}Label"></h4>\n' +
    '                <button type="button" class="close" data-dismiss="modal">\n' +
    '                    <span aria-hidden="true">&times;</span>\n' +
    '                    <span class="sr-only">Close</span>\n' +
    '                </button>\n' +
    '            </div>\n' +
    '            <div class="modal-body">\n' +
    '            </div>\n' +
    '            <div class="modal-footer">\n' +
    '                <nav class="nav nav-fill"></nav>\n' +
    '            </div>\n' +
    '        </div>\n' +
    '    </div>\n' +
    '</div>';
var dialogIdx=0;
function Dialog(opts){
    if(!opts)opts={};
    //处理按钮
    if(opts.btns!==undefined) {
        if (typeof(opts.btns) == 'string') {
            opts.btns = [opts.btns];
        }
        var dft=-1;
        for (var i = 0; i < opts.btns.length; i++) {
            if(typeof(opts.btns[i])=='string'){
                opts.btns[i]={'text':opts.btns[i]};
            }
            if(opts.btns[i].isdefault){
                dft=i;
            }
        }
        if(dft<0){
            dft=opts.btns.length-1;
            opts.btns[dft].isdefault=true;
        }

        if(!opts.btns[dft]['type']){
            opts.btns[dft]['type']='primary';
        }
        opts.defaultBtn=dft;
    }

    this.options=$.extend({
        'id':'dlgModal'+dialogIdx++,
        'size':'',
        'btns':[
            {'text':'取消','type':'secondary'},
            {'text':'确定','isdefault':true,'type':'primary'}
        ],
        'defaultBtn':1,
        'onsure':null,
        'onshow':null,
        'onshown':null,
        'onhide':null,
        'onhidden':null
    },opts);

    this.box=$(this.options.id);
}
Dialog.prototype.generBtn=function(opt,idx){
    if(opt['type'])opt['class']='btn-outline-'+opt['type'];
    return '<a href="javascript:" class="nav-item btn '+(opt['class']?opt['class']:'btn-outline-secondary')+'" data-index="'+idx+'">'+opt.text+'</a>';
};
Dialog.prototype.show=function(html,title){
    this.box=$('#'+this.options.id);
    if(!title)title='系统提示';
    if(this.box.length<1) {
        $(document.body).append(dialogTpl.compile({'id': this.options.id}));
        this.box=$('#'+this.options.id);
    }else{
        this.box.unbind();
    }

    //this.box.find('.modal-footer .btn-primary').unbind();
    var self=this;
    Dialog.instance=self;

    //生成按钮
    var btns=[];
    for(var i=0;i<this.options.btns.length;i++){
        btns.push(this.generBtn(this.options.btns[i],i));
    }
    this.box.find('.modal-footer .nav').html(btns.join('\n'));

    var dialog=this.box.find('.modal-dialog');
    dialog.removeClass('modal-sm').removeClass('modal-lg');
    if(this.options.size=='sm') {
        dialog.addClass('modal-sm');
    }else if(this.options.size=='lg') {
        dialog.addClass('modal-lg');
    }
    this.box.find('.modal-title').text(title);

    var body=this.box.find('.modal-body');
    body.html(html);
    this.box.on('hide.bs.modal',function(){
        if(self.options.onhide){
            self.options.onhide(body,self.box);
        }
        Dialog.instance=null;
    });
    this.box.on('hidden.bs.modal',function(){
        if(self.options.onhidden){
            self.options.onhidden(body,self.box);
        }
        self.box.remove();
    });
    this.box.on('show.bs.modal',function(){
        if(self.options.onshow){
            self.options.onshow(body,self.box);
        }
    });
    this.box.on('shown.bs.modal',function(){
        if(self.options.onshown){
            self.options.onshown(body,self.box);
        }
    });
    this.box.find('.modal-footer .btn').click(function(){
        var result=true,idx=$(this).data('index');
        if(self.options.btns[idx].click){
            result = self.options.btns[idx].click.apply(this,[body, self.box]);
        }
        if(idx==self.options.defaultBtn) {
            if (self.options.onsure) {
                result = self.options.onsure.apply(this,[body, self.box]);
            }
        }
        if(result!==false){
            self.box.modal('hide');
        }
    });
    this.box.modal('show');
    return this;
};
Dialog.prototype.hide=function(){
    this.box.modal('hide');
    return this;
};

var dialog={
    alert:function(message,callback,title){
        var called=false;
        var iscallback=typeof callback=='function';
        return new Dialog({
            btns:'确定',
            onsure:function(){
                if(iscallback){
                    called=true;
                    return callback(true);
                }
            },
            onhide:function(){
                if(!called && iscallback){
                    callback(false);
                }
            }
        }).show(message,title);
    },
    confirm:function(message,confirm,cancel){
        var called=false;
        return new Dialog({
            'onsure':function(){
                if(typeof confirm=='function'){
                    called=true;
                    return confirm();
                }
            },
            'onhide':function () {
                if(called=false && typeof cancel=='function'){
                    return cancel();
                }
            }
        }).show(message);
    },
    prompt:function(message,callback,cancel){
        var called=false;
        return new Dialog({
            'onshown':function(body){
                body.find('[name=confirm_input]').focus();
            },
            'onsure':function(body){
                var val=body.find('[name=confirm_input]').val();
                if(typeof callback=='function'){
                    var result = callback(val);
                    if(result===true){
                        called=true;
                    }
                    return result;
                }
            },
            'onhide':function () {
                if(called=false && typeof cancel=='function'){
                    return cancel();
                }
            }
        }).show('<input type="text" name="confirm_input" class="form-control" />',message);
    },
    pickUser:function(url,callback,filter){
        var user=null;
        if(!filter)filter={};
        var dlg=new Dialog({
            'onshown':function(body){
                var btn=body.find('.searchbtn');
                var input=body.find('.searchtext');
                var listbox=body.find('.list-group');
                var isloading=false;
                btn.click(function(){
                    if(isloading)return;
                    isloading=true;
                    listbox.html('<span class="list-loading">加载中...</span>');
                    filter['key']=input.val();
                    $.ajax(
                        {
                            url:url,
                            type:'GET',
                            dataType:'JSON',
                            data:filter,
                            success:function(json){
                                isloading=false;
                                if(json.status){
                                    if(json.data && json.data.length) {
                                        listbox.html('<a href="javascript:" data-id="{@id}" class="list-group-item list-group-item-action">[{@id}]&nbsp;<i class="ion-md-person"></i> {@username}&nbsp;&nbsp;&nbsp;<small><i class="ion-md-phone-portrait"></i> {@mobile}</small></a>'.compile(json.data, true));
                                        listbox.find('a.list-group-item').click(function () {
                                            var id = $(this).data('id');
                                            for (var i = 0; i < json.data.length; i++) {
                                                if(json.data[i].id==id){
                                                    user=json.data[i];
                                                    listbox.find('a.list-group-item').removeClass('active');
                                                    $(this).addClass('active');
                                                    break;
                                                }
                                            }
                                        })
                                    }else{
                                        listbox.html('<span class="list-loading"><i class="ion-md-warning"></i> 没有检索到会员</span>');
                                    }
                                }else{
                                    listbox.html('<span class="text-danger"><i class="ion-md-warning"></i> 加载失败</span>');
                                }
                            }
                        }
                    );

                }).trigger('click');
            },
            'onsure':function(body){
                if(!user){
                    toastr.warning('没有选择会员!');
                    return false;
                }
                if(typeof callback=='function'){
                    var result = callback(user);
                    return result;
                }
            }
        }).show('<div class="input-group"><input type="text" class="form-control searchtext" name="keyword" placeholder="根据会员id或名称，电话来搜索"/><div class="input-group-append"><a class="btn btn-outline-secondary searchbtn"><i class="ion-md-search"></i></a></div></div><div class="list-group mt-2"></div>','请搜索并选择会员');
    },
    pickLocate:function(type, callback, locate){
        var settedLocate=null;
        var dlg=new Dialog({
            'size':'lg',
            'onshown':function(body){
                var btn=body.find('.searchbtn');
                var input=body.find('.searchtext');
                var mapbox=body.find('.map');
                var mapinfo=body.find('.mapinfo');
                mapbox.css('height',$(window).height()*.6);
                var isloading=false;
                var map=InitMap('tencent',mapbox,function(address,locate){
                    mapinfo.html(address+'&nbsp;'+locate.lng+','+locate.lat);
                    settedLocate=locate;
                },locate);
                btn.click(function(){
                    var search=input.val();
                    map.setLocate(search);
                });
            },
            'onsure':function(body){
                if(!settedLocate){
                    toastr.warning('没有选择位置!');
                    return false;
                }
                if(typeof callback==='function'){
                    var result = callback(settedLocate);
                    return result;
                }
            }
        }).show('<div class="input-group"><input type="text" class="form-control searchtext" name="keyword" placeholder="填写地址检索位置"/><div class="input-group-append"><a class="btn btn-outline-secondary searchbtn"><i class="ion-md-search"></i></a></div></div>' +
            '<div class="map mt-2"></div>' +
            '<div class="mapinfo mt-2 text-muted">未选择位置</div>','请选择地图位置');
    }
};

jQuery(function($){

    //监控按键
    $(document).on('keydown', function(e){
        if(!Dialog.instance)return;
        var dlg=Dialog.instance;
        if (e.keyCode == 13) {
            dlg.box.find('.modal-footer .btn').eq(dlg.options.defaultBtn).trigger('click');
        }
        //默认已监听关闭
        /*if (e.keyCode == 27) {
         self.hide();
         }*/
    });
});

jQuery.extend(jQuery.fn,{
    tags:function(nm){
        var data=[];
        var tpl='<span class="badge badge-info">{@label}<input type="hidden" name="'+nm+'" value="{@label}"/><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></span>';
        var item=$(this).parents('.form-control');
        var labelgroup=$('<span class="badge-group"></span>');
        var input=this;
        this.before(labelgroup);
        this.on('keyup',function(){
            var val=$(this).val().replace(/，/g,',');
            if(val && val.indexOf(',')>-1){
                var vals=val.split(',');
                for(var i=0;i<vals.length;i++){
                    vals[i]=vals[i].replace(/^\s|\s$/g,'');
                    if(vals[i] && data.indexOf(vals[i])===-1){
                        data.push(vals[i]);
                        labelgroup.append(tpl.compile({label:vals[i]}));
                    }
                }
                input.val('');
            }
        }).on('blur',function(){
            $(this).val($(this).val()+',').trigger('keyup');
        }).trigger('keyup');
        labelgroup.on('click','.close',function(){
            var tag=$(this).parents('.badge').find('input').val();
            var id=data.indexOf(tag);
            if(id)data.splice(id,1);
            $(this).parents('.badge').remove();
        });
        item.click(function(){
            input.focus();
        });
    }
});
//日期组件
if($.fn.datetimepicker) {
    var tooltips= {
        today: '定位当前日期',
        clear: '清除已选日期',
        close: '关闭选择器',
        selectMonth: '选择月份',
        prevMonth: '上个月',
        nextMonth: '下个月',
        selectYear: '选择年份',
        prevYear: '上一年',
        nextYear: '下一年',
        selectDecade: '选择年份区间',
        prevDecade: '上一区间',
        nextDecade: '下一区间',
        prevCentury: '上个世纪',
        nextCentury: '下个世纪'
    };
    var icons={
        time: 'ion-md-time',
        date: 'ion-md-calendar',
        up: 'ion-md-arrow-dropup',
        down: 'ion-md-arrow-dropdown',
        previous: 'ion-md-arrow-dropleft',
        next: 'ion-md-arrow-dropright',
        today: 'ion-md-today',
        clear: 'ion-md-trash',
        close: 'ion-md-close'
    };
    $('.datepicker').datetimepicker({
        icons:icons,
        tooltips:tooltips,
        format: 'YYYY-MM-DD',
        locale: 'zh-cn',
        showClear:true,
        showTodayButton:true,
        showClose:true,
        keepInvalid:true
    });

    $('.date-range').each(function () {
        var from = $(this).find('[name=fromdate],.fromdate'), to = $(this).find('[name=todate],.todate');
        var options = {
            icons:icons,
            tooltips:tooltips,
            format: 'YYYY-MM-DD',
            locale:'zh-cn',
            showClear:true,
            showTodayButton:true,
            showClose:true,
            keepInvalid:true
        };
        from.datetimepicker(options).on('dp.change', function () {
            if (from.val()) {
                to.data('DateTimePicker').minDate(from.val());
            }
        });
        to.datetimepicker(options).on('dp.change', function () {
            if (to.val()) {
                from.data('DateTimePicker').maxDate(to.val());
            }
        });
    });
}

(function(window,$) {
    var apis = {
        'baidu': 'https://api.map.baidu.com/api?ak=rO9tOdEWFfvyGgDkiWqFjxK6&v=1.5&services=false&callback=',
        'google': 'https://maps.google.com/maps/api/js?key=AIzaSyB8lorvl6EtqIWz67bjWBruOhm9NYS1e24&callback=',
        'tencent': 'https://map.qq.com/api/js?v=2.exp&key=7I5BZ-QUE6R-JXLWV-WTVAA-CJMYF-7PBBI&callback=',
        'gaode': 'https://webapi.amap.com/maps?v=1.3&key=3ec311b5db0d597e79422eeb9a6d4449&callback='
    };

    function loadScript(src) {
        var script = document.createElement("script");
        script.type = "text/javascript";
        script.src = src;
        document.body.appendChild(script);
    }

    var mapObj,mapBox,onPick;

    function InitMap(mapkey,box,callback,locate) {
        if (mapObj) mapObj.hide();
        mapBox=$(box);
        onPick=callback;

        switch (mapkey.toLowerCase()) {
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
        if (!mapObj) return toastr.warning('不支持该地图类型');
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
        this.mapType = type;
        this.ishide = false;
        this.isshow = false;
        this.toshow = false;
        this.marker = null;
        this.infoWindow = null;
        this.mapbox = null;
        this.locate = {lng:116.396795,lat:39.933084};
        this.map = null;
    }

    BaseMap.prototype.isAPIReady = function () {
        return false;
    };
    BaseMap.prototype.setMap = function () {
    };
    BaseMap.prototype.showInfo = function () {
    };
    BaseMap.prototype.getAddress = function (rs) {
        return "";
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
            loadScript(apis[this.mapType] + func);
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
    BaseMap.prototype.bindEvents = function () {
        var self = this;
        $('#txtTitle').unbind().blur(function () {
            self.showInfo();
        });
        $('#txtContent').unbind().blur(function () {
            self.showInfo();
        });
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
    BaseMap.prototype.showLocationInfo = function (pt, rs) {

        this.showInfo();
        var address=this.getAddress(rs);
        var locate={};
        if (typeof (pt.lng) === 'function') {
            locate.lng=pt.lng();
            locate.lat=pt.lat();
        } else {
            locate.lng=pt.lng;
            locate.lat=pt.lat;
        }

        onPick(address,locate);
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

        var point = new BMap.Point(this.locate.lng, this.locate.lat);
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

        this.bindEvents();

        this.isshow = true;
        if (this.toshow) {
            this.showInfo();
            this.toshow = false;
        }
    };

    BaiduMap.prototype.showInfo = function () {
        if (this.ishide) return;
        if (!this.isshow) {
            this.toshow = true;
            return;
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
                toastr.warning("地址信息不正确，定位失败");
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
        var point = new google.maps.LatLng(this.locate);
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

        this.bindEvents();

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

    GoogleMap.prototype.showInfo = function () {
        if (this.ishide) return;
        if (!this.isshow) {
            this.toshow = true;
            return;
        }
        this.infoWindow.setOptions({position: this.marker.getPosition()});
        this.setInfoContent();

    };
    GoogleMap.prototype.getAddress = function (rs, status) {
        if (rs && rs[0]) {
            return rs[0].formatted_address;
        }
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
                toastr.warning("地址信息不正确，定位失败");
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
        var point = new qq.maps.LatLng(this.locate.lat, this.locate.lng);
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

        this.bindEvents();

        this.infoWindow = new qq.maps.InfoWindow({map: map});

        this.isshow = true;
        if (this.toshow) {
            this.showInfo();
            this.toshow = false;
        }
    };

    TencentMap.prototype.showInfo = function () {
        if (this.ishide) return;
        if (!this.isshow) {
            this.toshow = true;
            return;
        }
        this.infoWindow.open();
        this.setInfoContent();
        this.infoWindow.setPosition(this.marker.getPosition());
    };

    TencentMap.prototype.getAddress = function (rs) {
        if(rs && rs.detail) {
            return rs.detail.address;
        }
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
                    toastr.warning("地址信息不正确，定位失败");
                }
            },
            error:function(result){
                toastr.warning("地址信息不正确，定位失败");
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
        var point = new AMap.LngLat(this.locate.lng, this.locate.lat);
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

        this.bindEvents();

        this.isshow = true;
        if (this.toshow) {
            this.showInfo();
            this.toshow = false;
        }
    };

    GaodeMap.prototype.showInfo = function () {
        if (this.ishide) return;
        if (!this.isshow) {
            this.toshow = true;
            return;
        }
        this.setInfoContent();
        this.infoWindow.setPosition(this.marker.getPosition());
    };

    GaodeMap.prototype.getAddress = function (rs) {
        return rs.regeocode.formattedAddress;
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
                toastr.warning("地址信息不正确，定位失败");
            }
        }, '');
    };

    window.InitMap=InitMap;
})(window,jQuery);
jQuery(function ($) {
    //高亮当前选中的导航
    var bread = $(".breadcrumb");
    var menu = bread.data('menu');
    if (menu) {
        var link = $('.side-nav a[data-key=' + menu + ']');

        var html = [];
        if (link.length > 0) {
            if (link.is('.menu_top')) {
                html.push('<li class="breadcrumb-item"><a href="javascript:"><i class="' + link.find('i').attr('class') + '"></i>&nbsp;' + link.text() + '</a></li>');
            } else {
                var parent = link.parents('.collapse').eq(0);
                parent.addClass('show');
                link.addClass("active");
                var topmenu = parent.siblings('.card-header').find('a.menu_top');
                html.push('<li class="breadcrumb-item"><a href="javascript:"><i class="' + topmenu.find('i').attr('class') + '"></i>&nbsp;' + topmenu.text() + '</a></li>');
                html.push('<li class="breadcrumb-item"><a href="javascript:">' + link.text() + '</a></li>');
            }
        }
        var title = bread.data('title');
        if (title) {
            html.push('<li class="breadcrumb-item active" aria-current="page">' + title + '</li>');
        }
        bread.html(html.join("\n"));
    }

    //全选、反选按钮
    $('.checkall-btn').click(function (e) {
        var target = $(this).data('target');
        if (!target) target = 'id';
        var ids = $('[name=' + target + ']');
        if ($(this).is('.active')) {
            ids.removeAttr('checked');
        } else {
            ids.attr('checked', true);
        }
    });
    $('.checkreverse-btn').click(function (e) {
        var target = $(this).data('target');
        if (!target) target = 'id';
        var ids = $('[name=' + target + ']');
        for (var i = 0; i < ids.length; i++) {
            if (ids[i].checked) {
                ids.eq(i).removeAttr('checked');
            } else {
                ids.eq(i).attr('checked', true);
            }
        }
    });
    //操作按钮
    $('.action-btn').click(function (e) {
        e.preventDefault();
        var action = $(this).data('action');
        if (!action) {
            return toastr.error('未知操作');
        }
        action = 'action' + action.replace(/^[a-z]/, function (letter) {
            return letter.toUpperCase();
        });
        if (!window[action] || typeof window[action] !== 'function') {
            return toastr.error('未知操作');
        }
        var needChecks = $(this).data('needChecks');
        if (needChecks === undefined) needChecks = true;
        if (needChecks) {
            var target = $(this).data('target');
            if (!target) target = 'id';
            var ids = $('[name=' + target + ']:checked');
            if (ids.length < 1) {
                return toastr.warning('请选择需要操作的项目');
            } else {
                var idchecks = [];
                for (var i = 0; i < ids.length; i++) {
                    idchecks.push(ids.eq(i).val());
                }
                window[action](idchecks);
            }
        } else {
            window[action]();
        }
    });

    //异步显示资料链接
    $('a[rel=ajax]').click(function (e) {
        e.preventDefault();
        var self = $(this);
        var title = $(this).data('title');
        if (!title) title = $(this).text();
        var dlg = new Dialog({
            btns: ['确定'],
            onshow: function (body) {
                $.ajax({
                    url: self.attr('href'),
                    success: function (text) {
                        body.html(text);
                    }
                });
            }
        }).show('<p class="loading">加载中...</p>', title);

    });

    $('.nav-tabs a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    //上传框
    $('.custom-file .custom-file-input').on('change', function () {
        var label = $(this).parents('.custom-file').find('.custom-file-label');
        label.text($(this).val());
    });

    //表单Ajax提交
    $('.btn-primary[type=submit]').click(function (e) {
        var form = $(this).parents('form');
        var btn = this;
        var options = {
            url: $(form).attr('action'),
            type: 'POST',
            dataType: 'JSON',
            success: function (json) {
                if (json.code == 1) {
                    new Dialog({
                        onhidden: function () {
                            if (json.url) {
                                location.href = json.url;
                            } else {
                                location.reload();
                            }
                        }
                    }).show(json.msg);
                } else {
                    toastr.warning(json.msg);
                    $(btn).removeAttr('disabled');
                }
            }
        };
        if (form.attr('enctype') === 'multipart/form-data') {
            if (!FormData) {
                return true;
            }
            options.data = new FormData(form[0]);
            options.cache = false;
            options.processData = false;
            options.contentType = false;
        } else {
            options.data = $(form).serialize();
        }

        e.preventDefault();
        $(this).attr('disabled', true);
        $.ajax(options);
    });

    $('.pickuser').click(function (e) {
        var group = $(this).parents('.input-group');
        var idele = group.find('[name=member_id]');
        var infoele = group.find('[name=member_info]');
        dialog.pickUser($(this).data('url'), function (user) {
            idele.val(user.id);
            infoele.val('[' + user.id + '] ' + user.username + (user.mobile ? (' / ' + user.mobile) : ''));
        }, $(this).data('filter'));
    });
    $('.pick-locate').click(function(e){
        var group=$(this).parents('.input-group');
        var idele=group.find('input[type=text]');
        dialog.pickLocate('qq',function(locate){
            idele.val(locate.lng+','+locate.lat);
        },idele.val());
    });
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNvbW1vbi5qcyIsInRlbXBsYXRlLmpzIiwiZGlhbG9nLmpzIiwianF1ZXJ5LnRhZy5qcyIsImRhdGV0aW1lLmluaXQuanMiLCJtYXAuanMiLCJiYWNrZW5kLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ0xBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQzlEQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ3hUQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUNuQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUMvREE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUMxakJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EiLCJmaWxlIjoiYmFja2VuZC5qcyIsInNvdXJjZXNDb250ZW50IjpbImZ1bmN0aW9uIGRlbChvYmosbXNnKSB7XHJcbiAgICBkaWFsb2cuY29uZmlybShtc2csZnVuY3Rpb24oKXtcclxuICAgICAgICBsb2NhdGlvbi5ocmVmPSQob2JqKS5hdHRyKCdocmVmJyk7XHJcbiAgICB9KTtcclxuICAgIHJldHVybiBmYWxzZTtcclxufSIsIlxyXG5OdW1iZXIucHJvdG90eXBlLmZvcm1hdD1mdW5jdGlvbihmaXgpe1xyXG4gICAgaWYoZml4PT09dW5kZWZpbmVkKWZpeD0yO1xyXG4gICAgdmFyIG51bT10aGlzLnRvRml4ZWQoZml4KTtcclxuICAgIHZhciB6PW51bS5zcGxpdCgnLicpO1xyXG4gICAgdmFyIGZvcm1hdD1bXSxmPXpbMF0uc3BsaXQoJycpLGw9Zi5sZW5ndGg7XHJcbiAgICBmb3IodmFyIGk9MDtpPGw7aSsrKXtcclxuICAgICAgICBpZihpPjAgJiYgaSAlIDM9PTApe1xyXG4gICAgICAgICAgICBmb3JtYXQudW5zaGlmdCgnLCcpO1xyXG4gICAgICAgIH1cclxuICAgICAgICBmb3JtYXQudW5zaGlmdChmW2wtaS0xXSk7XHJcbiAgICB9XHJcbiAgICByZXR1cm4gZm9ybWF0LmpvaW4oJycpKyh6Lmxlbmd0aD09Mj8nLicrelsxXTonJyk7XHJcbn07XHJcblN0cmluZy5wcm90b3R5cGUuY29tcGlsZT1mdW5jdGlvbihkYXRhLGxpc3Qpe1xyXG5cclxuICAgIGlmKGxpc3Qpe1xyXG4gICAgICAgIHZhciB0ZW1wcz1bXTtcclxuICAgICAgICBmb3IodmFyIGkgaW4gZGF0YSl7XHJcbiAgICAgICAgICAgIHRlbXBzLnB1c2godGhpcy5jb21waWxlKGRhdGFbaV0pKTtcclxuICAgICAgICB9XHJcbiAgICAgICAgcmV0dXJuIHRlbXBzLmpvaW4oXCJcXG5cIik7XHJcbiAgICB9ZWxzZXtcclxuICAgICAgICByZXR1cm4gdGhpcy5yZXBsYWNlKC9cXHtAKFtcXHdcXGRcXC5dKykoPzpcXHwoW1xcd1xcZF0rKSg/Olxccyo9XFxzKihbXFx3XFxkLFxccyNdKykpPyk/XFx9L2csZnVuY3Rpb24oYWxsLG0xLGZ1bmMsYXJncyl7XHJcblxyXG4gICAgICAgICAgICBpZihtMS5pbmRleE9mKCcuJyk+MCl7XHJcbiAgICAgICAgICAgICAgICB2YXIga2V5cz1tMS5zcGxpdCgnLicpLHZhbD1kYXRhO1xyXG4gICAgICAgICAgICAgICAgZm9yKHZhciBpPTA7aTxrZXlzLmxlbmd0aDtpKyspe1xyXG4gICAgICAgICAgICAgICAgICAgIGlmKHZhbFtrZXlzW2ldXSl7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhbD12YWxba2V5c1tpXV07XHJcbiAgICAgICAgICAgICAgICAgICAgfWVsc2V7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhbCA9ICcnO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICByZXR1cm4gY2FsbGZ1bmModmFsLGZ1bmMsYXJncyk7XHJcbiAgICAgICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICAgICAgcmV0dXJuIGRhdGFbbTFdP2NhbGxmdW5jKGRhdGFbbTFdLGZ1bmMsYXJncyxkYXRhKTonJztcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pO1xyXG4gICAgfVxyXG59O1xyXG5cclxuZnVuY3Rpb24gY2FsbGZ1bmModmFsLGZ1bmMsYXJncyx0aGlzb2JqKXtcclxuICAgIGlmKCFhcmdzKXtcclxuICAgICAgICBhcmdzPVt2YWxdO1xyXG4gICAgfWVsc2V7XHJcbiAgICAgICAgaWYodHlwZW9mIGFyZ3M9PT0nc3RyaW5nJylhcmdzPWFyZ3Muc3BsaXQoJywnKTtcclxuICAgICAgICB2YXIgYXJnaWR4PWFyZ3MuaW5kZXhPZignIyMjJyk7XHJcbiAgICAgICAgaWYoYXJnaWR4Pj0wKXtcclxuICAgICAgICAgICAgYXJnc1thcmdpZHhdPXZhbDtcclxuICAgICAgICB9ZWxzZXtcclxuICAgICAgICAgICAgYXJncz1bdmFsXS5jb25jYXQoYXJncyk7XHJcbiAgICAgICAgfVxyXG4gICAgfVxyXG4gICAgLy9jb25zb2xlLmxvZyhhcmdzKTtcclxuICAgIHJldHVybiB3aW5kb3dbZnVuY10/d2luZG93W2Z1bmNdLmFwcGx5KHRoaXNvYmosYXJncyk6dmFsO1xyXG59XHJcblxyXG5mdW5jdGlvbiBpaWYodixtMSxtMil7XHJcbiAgICBpZih2PT09JzAnKXY9MDtcclxuICAgIHJldHVybiB2P20xOm0yO1xyXG59IiwiXHJcbnZhciBkaWFsb2dUcGw9JzxkaXYgY2xhc3M9XCJtb2RhbCBmYWRlXCIgaWQ9XCJ7QGlkfVwiIHRhYmluZGV4PVwiLTFcIiByb2xlPVwiZGlhbG9nXCIgYXJpYS1sYWJlbGxlZGJ5PVwie0BpZH1MYWJlbFwiIGFyaWEtaGlkZGVuPVwidHJ1ZVwiPlxcbicgK1xyXG4gICAgJyAgICA8ZGl2IGNsYXNzPVwibW9kYWwtZGlhbG9nXCI+XFxuJyArXHJcbiAgICAnICAgICAgICA8ZGl2IGNsYXNzPVwibW9kYWwtY29udGVudFwiPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJtb2RhbC1oZWFkZXJcIj5cXG4nICtcclxuICAgICcgICAgICAgICAgICAgICAgPGg0IGNsYXNzPVwibW9kYWwtdGl0bGVcIiBpZD1cIntAaWR9TGFiZWxcIj48L2g0PlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgICAgICA8YnV0dG9uIHR5cGU9XCJidXR0b25cIiBjbGFzcz1cImNsb3NlXCIgZGF0YS1kaXNtaXNzPVwibW9kYWxcIj5cXG4nICtcclxuICAgICcgICAgICAgICAgICAgICAgICAgIDxzcGFuIGFyaWEtaGlkZGVuPVwidHJ1ZVwiPiZ0aW1lczs8L3NwYW4+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgICAgICAgICA8c3BhbiBjbGFzcz1cInNyLW9ubHlcIj5DbG9zZTwvc3Bhbj5cXG4nICtcclxuICAgICcgICAgICAgICAgICAgICAgPC9idXR0b24+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgPC9kaXY+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgPGRpdiBjbGFzcz1cIm1vZGFsLWJvZHlcIj5cXG4nICtcclxuICAgICcgICAgICAgICAgICA8L2Rpdj5cXG4nICtcclxuICAgICcgICAgICAgICAgICA8ZGl2IGNsYXNzPVwibW9kYWwtZm9vdGVyXCI+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgICAgIDxuYXYgY2xhc3M9XCJuYXYgbmF2LWZpbGxcIj48L25hdj5cXG4nICtcclxuICAgICcgICAgICAgICAgICA8L2Rpdj5cXG4nICtcclxuICAgICcgICAgICAgIDwvZGl2PlxcbicgK1xyXG4gICAgJyAgICA8L2Rpdj5cXG4nICtcclxuICAgICc8L2Rpdj4nO1xyXG52YXIgZGlhbG9nSWR4PTA7XHJcbmZ1bmN0aW9uIERpYWxvZyhvcHRzKXtcclxuICAgIGlmKCFvcHRzKW9wdHM9e307XHJcbiAgICAvL+WkhOeQhuaMiemSrlxyXG4gICAgaWYob3B0cy5idG5zIT09dW5kZWZpbmVkKSB7XHJcbiAgICAgICAgaWYgKHR5cGVvZihvcHRzLmJ0bnMpID09ICdzdHJpbmcnKSB7XHJcbiAgICAgICAgICAgIG9wdHMuYnRucyA9IFtvcHRzLmJ0bnNdO1xyXG4gICAgICAgIH1cclxuICAgICAgICB2YXIgZGZ0PS0xO1xyXG4gICAgICAgIGZvciAodmFyIGkgPSAwOyBpIDwgb3B0cy5idG5zLmxlbmd0aDsgaSsrKSB7XHJcbiAgICAgICAgICAgIGlmKHR5cGVvZihvcHRzLmJ0bnNbaV0pPT0nc3RyaW5nJyl7XHJcbiAgICAgICAgICAgICAgICBvcHRzLmJ0bnNbaV09eyd0ZXh0JzpvcHRzLmJ0bnNbaV19O1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIGlmKG9wdHMuYnRuc1tpXS5pc2RlZmF1bHQpe1xyXG4gICAgICAgICAgICAgICAgZGZ0PWk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9XHJcbiAgICAgICAgaWYoZGZ0PDApe1xyXG4gICAgICAgICAgICBkZnQ9b3B0cy5idG5zLmxlbmd0aC0xO1xyXG4gICAgICAgICAgICBvcHRzLmJ0bnNbZGZ0XS5pc2RlZmF1bHQ9dHJ1ZTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIGlmKCFvcHRzLmJ0bnNbZGZ0XVsndHlwZSddKXtcclxuICAgICAgICAgICAgb3B0cy5idG5zW2RmdF1bJ3R5cGUnXT0ncHJpbWFyeSc7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIG9wdHMuZGVmYXVsdEJ0bj1kZnQ7XHJcbiAgICB9XHJcblxyXG4gICAgdGhpcy5vcHRpb25zPSQuZXh0ZW5kKHtcclxuICAgICAgICAnaWQnOidkbGdNb2RhbCcrZGlhbG9nSWR4KyssXHJcbiAgICAgICAgJ3NpemUnOicnLFxyXG4gICAgICAgICdidG5zJzpbXHJcbiAgICAgICAgICAgIHsndGV4dCc6J+WPlua2iCcsJ3R5cGUnOidzZWNvbmRhcnknfSxcclxuICAgICAgICAgICAgeyd0ZXh0Jzon56Gu5a6aJywnaXNkZWZhdWx0Jzp0cnVlLCd0eXBlJzoncHJpbWFyeSd9XHJcbiAgICAgICAgXSxcclxuICAgICAgICAnZGVmYXVsdEJ0bic6MSxcclxuICAgICAgICAnb25zdXJlJzpudWxsLFxyXG4gICAgICAgICdvbnNob3cnOm51bGwsXHJcbiAgICAgICAgJ29uc2hvd24nOm51bGwsXHJcbiAgICAgICAgJ29uaGlkZSc6bnVsbCxcclxuICAgICAgICAnb25oaWRkZW4nOm51bGxcclxuICAgIH0sb3B0cyk7XHJcblxyXG4gICAgdGhpcy5ib3g9JCh0aGlzLm9wdGlvbnMuaWQpO1xyXG59XHJcbkRpYWxvZy5wcm90b3R5cGUuZ2VuZXJCdG49ZnVuY3Rpb24ob3B0LGlkeCl7XHJcbiAgICBpZihvcHRbJ3R5cGUnXSlvcHRbJ2NsYXNzJ109J2J0bi1vdXRsaW5lLScrb3B0Wyd0eXBlJ107XHJcbiAgICByZXR1cm4gJzxhIGhyZWY9XCJqYXZhc2NyaXB0OlwiIGNsYXNzPVwibmF2LWl0ZW0gYnRuICcrKG9wdFsnY2xhc3MnXT9vcHRbJ2NsYXNzJ106J2J0bi1vdXRsaW5lLXNlY29uZGFyeScpKydcIiBkYXRhLWluZGV4PVwiJytpZHgrJ1wiPicrb3B0LnRleHQrJzwvYT4nO1xyXG59O1xyXG5EaWFsb2cucHJvdG90eXBlLnNob3c9ZnVuY3Rpb24oaHRtbCx0aXRsZSl7XHJcbiAgICB0aGlzLmJveD0kKCcjJyt0aGlzLm9wdGlvbnMuaWQpO1xyXG4gICAgaWYoIXRpdGxlKXRpdGxlPSfns7vnu5/mj5DnpLonO1xyXG4gICAgaWYodGhpcy5ib3gubGVuZ3RoPDEpIHtcclxuICAgICAgICAkKGRvY3VtZW50LmJvZHkpLmFwcGVuZChkaWFsb2dUcGwuY29tcGlsZSh7J2lkJzogdGhpcy5vcHRpb25zLmlkfSkpO1xyXG4gICAgICAgIHRoaXMuYm94PSQoJyMnK3RoaXMub3B0aW9ucy5pZCk7XHJcbiAgICB9ZWxzZXtcclxuICAgICAgICB0aGlzLmJveC51bmJpbmQoKTtcclxuICAgIH1cclxuXHJcbiAgICAvL3RoaXMuYm94LmZpbmQoJy5tb2RhbC1mb290ZXIgLmJ0bi1wcmltYXJ5JykudW5iaW5kKCk7XHJcbiAgICB2YXIgc2VsZj10aGlzO1xyXG4gICAgRGlhbG9nLmluc3RhbmNlPXNlbGY7XHJcblxyXG4gICAgLy/nlJ/miJDmjInpkq5cclxuICAgIHZhciBidG5zPVtdO1xyXG4gICAgZm9yKHZhciBpPTA7aTx0aGlzLm9wdGlvbnMuYnRucy5sZW5ndGg7aSsrKXtcclxuICAgICAgICBidG5zLnB1c2godGhpcy5nZW5lckJ0bih0aGlzLm9wdGlvbnMuYnRuc1tpXSxpKSk7XHJcbiAgICB9XHJcbiAgICB0aGlzLmJveC5maW5kKCcubW9kYWwtZm9vdGVyIC5uYXYnKS5odG1sKGJ0bnMuam9pbignXFxuJykpO1xyXG5cclxuICAgIHZhciBkaWFsb2c9dGhpcy5ib3guZmluZCgnLm1vZGFsLWRpYWxvZycpO1xyXG4gICAgZGlhbG9nLnJlbW92ZUNsYXNzKCdtb2RhbC1zbScpLnJlbW92ZUNsYXNzKCdtb2RhbC1sZycpO1xyXG4gICAgaWYodGhpcy5vcHRpb25zLnNpemU9PSdzbScpIHtcclxuICAgICAgICBkaWFsb2cuYWRkQ2xhc3MoJ21vZGFsLXNtJyk7XHJcbiAgICB9ZWxzZSBpZih0aGlzLm9wdGlvbnMuc2l6ZT09J2xnJykge1xyXG4gICAgICAgIGRpYWxvZy5hZGRDbGFzcygnbW9kYWwtbGcnKTtcclxuICAgIH1cclxuICAgIHRoaXMuYm94LmZpbmQoJy5tb2RhbC10aXRsZScpLnRleHQodGl0bGUpO1xyXG5cclxuICAgIHZhciBib2R5PXRoaXMuYm94LmZpbmQoJy5tb2RhbC1ib2R5Jyk7XHJcbiAgICBib2R5Lmh0bWwoaHRtbCk7XHJcbiAgICB0aGlzLmJveC5vbignaGlkZS5icy5tb2RhbCcsZnVuY3Rpb24oKXtcclxuICAgICAgICBpZihzZWxmLm9wdGlvbnMub25oaWRlKXtcclxuICAgICAgICAgICAgc2VsZi5vcHRpb25zLm9uaGlkZShib2R5LHNlbGYuYm94KTtcclxuICAgICAgICB9XHJcbiAgICAgICAgRGlhbG9nLmluc3RhbmNlPW51bGw7XHJcbiAgICB9KTtcclxuICAgIHRoaXMuYm94Lm9uKCdoaWRkZW4uYnMubW9kYWwnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgaWYoc2VsZi5vcHRpb25zLm9uaGlkZGVuKXtcclxuICAgICAgICAgICAgc2VsZi5vcHRpb25zLm9uaGlkZGVuKGJvZHksc2VsZi5ib3gpO1xyXG4gICAgICAgIH1cclxuICAgICAgICBzZWxmLmJveC5yZW1vdmUoKTtcclxuICAgIH0pO1xyXG4gICAgdGhpcy5ib3gub24oJ3Nob3cuYnMubW9kYWwnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgaWYoc2VsZi5vcHRpb25zLm9uc2hvdyl7XHJcbiAgICAgICAgICAgIHNlbGYub3B0aW9ucy5vbnNob3coYm9keSxzZWxmLmJveCk7XHJcbiAgICAgICAgfVxyXG4gICAgfSk7XHJcbiAgICB0aGlzLmJveC5vbignc2hvd24uYnMubW9kYWwnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgaWYoc2VsZi5vcHRpb25zLm9uc2hvd24pe1xyXG4gICAgICAgICAgICBzZWxmLm9wdGlvbnMub25zaG93bihib2R5LHNlbGYuYm94KTtcclxuICAgICAgICB9XHJcbiAgICB9KTtcclxuICAgIHRoaXMuYm94LmZpbmQoJy5tb2RhbC1mb290ZXIgLmJ0bicpLmNsaWNrKGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgdmFyIHJlc3VsdD10cnVlLGlkeD0kKHRoaXMpLmRhdGEoJ2luZGV4Jyk7XHJcbiAgICAgICAgaWYoc2VsZi5vcHRpb25zLmJ0bnNbaWR4XS5jbGljayl7XHJcbiAgICAgICAgICAgIHJlc3VsdCA9IHNlbGYub3B0aW9ucy5idG5zW2lkeF0uY2xpY2suYXBwbHkodGhpcyxbYm9keSwgc2VsZi5ib3hdKTtcclxuICAgICAgICB9XHJcbiAgICAgICAgaWYoaWR4PT1zZWxmLm9wdGlvbnMuZGVmYXVsdEJ0bikge1xyXG4gICAgICAgICAgICBpZiAoc2VsZi5vcHRpb25zLm9uc3VyZSkge1xyXG4gICAgICAgICAgICAgICAgcmVzdWx0ID0gc2VsZi5vcHRpb25zLm9uc3VyZS5hcHBseSh0aGlzLFtib2R5LCBzZWxmLmJveF0pO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfVxyXG4gICAgICAgIGlmKHJlc3VsdCE9PWZhbHNlKXtcclxuICAgICAgICAgICAgc2VsZi5ib3gubW9kYWwoJ2hpZGUnKTtcclxuICAgICAgICB9XHJcbiAgICB9KTtcclxuICAgIHRoaXMuYm94Lm1vZGFsKCdzaG93Jyk7XHJcbiAgICByZXR1cm4gdGhpcztcclxufTtcclxuRGlhbG9nLnByb3RvdHlwZS5oaWRlPWZ1bmN0aW9uKCl7XHJcbiAgICB0aGlzLmJveC5tb2RhbCgnaGlkZScpO1xyXG4gICAgcmV0dXJuIHRoaXM7XHJcbn07XHJcblxyXG52YXIgZGlhbG9nPXtcclxuICAgIGFsZXJ0OmZ1bmN0aW9uKG1lc3NhZ2UsY2FsbGJhY2ssdGl0bGUpe1xyXG4gICAgICAgIHZhciBjYWxsZWQ9ZmFsc2U7XHJcbiAgICAgICAgdmFyIGlzY2FsbGJhY2s9dHlwZW9mIGNhbGxiYWNrPT0nZnVuY3Rpb24nO1xyXG4gICAgICAgIHJldHVybiBuZXcgRGlhbG9nKHtcclxuICAgICAgICAgICAgYnRuczon56Gu5a6aJyxcclxuICAgICAgICAgICAgb25zdXJlOmZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgICAgICBpZihpc2NhbGxiYWNrKXtcclxuICAgICAgICAgICAgICAgICAgICBjYWxsZWQ9dHJ1ZTtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gY2FsbGJhY2sodHJ1ZSk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgIG9uaGlkZTpmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICAgICAgaWYoIWNhbGxlZCAmJiBpc2NhbGxiYWNrKXtcclxuICAgICAgICAgICAgICAgICAgICBjYWxsYmFjayhmYWxzZSk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS5zaG93KG1lc3NhZ2UsdGl0bGUpO1xyXG4gICAgfSxcclxuICAgIGNvbmZpcm06ZnVuY3Rpb24obWVzc2FnZSxjb25maXJtLGNhbmNlbCl7XHJcbiAgICAgICAgdmFyIGNhbGxlZD1mYWxzZTtcclxuICAgICAgICByZXR1cm4gbmV3IERpYWxvZyh7XHJcbiAgICAgICAgICAgICdvbnN1cmUnOmZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgICAgICBpZih0eXBlb2YgY29uZmlybT09J2Z1bmN0aW9uJyl7XHJcbiAgICAgICAgICAgICAgICAgICAgY2FsbGVkPXRydWU7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGNvbmZpcm0oKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgJ29uaGlkZSc6ZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAgICAgaWYoY2FsbGVkPWZhbHNlICYmIHR5cGVvZiBjYW5jZWw9PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBjYW5jZWwoKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLnNob3cobWVzc2FnZSk7XHJcbiAgICB9LFxyXG4gICAgcHJvbXB0OmZ1bmN0aW9uKG1lc3NhZ2UsY2FsbGJhY2ssY2FuY2VsKXtcclxuICAgICAgICB2YXIgY2FsbGVkPWZhbHNlO1xyXG4gICAgICAgIHJldHVybiBuZXcgRGlhbG9nKHtcclxuICAgICAgICAgICAgJ29uc2hvd24nOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgYm9keS5maW5kKCdbbmFtZT1jb25maXJtX2lucHV0XScpLmZvY3VzKCk7XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdvbnN1cmUnOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgdmFyIHZhbD1ib2R5LmZpbmQoJ1tuYW1lPWNvbmZpcm1faW5wdXRdJykudmFsKCk7XHJcbiAgICAgICAgICAgICAgICBpZih0eXBlb2YgY2FsbGJhY2s9PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgICAgIHZhciByZXN1bHQgPSBjYWxsYmFjayh2YWwpO1xyXG4gICAgICAgICAgICAgICAgICAgIGlmKHJlc3VsdD09PXRydWUpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBjYWxsZWQ9dHJ1ZTtcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHJlc3VsdDtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgJ29uaGlkZSc6ZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAgICAgaWYoY2FsbGVkPWZhbHNlICYmIHR5cGVvZiBjYW5jZWw9PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBjYW5jZWwoKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLnNob3coJzxpbnB1dCB0eXBlPVwidGV4dFwiIG5hbWU9XCJjb25maXJtX2lucHV0XCIgY2xhc3M9XCJmb3JtLWNvbnRyb2xcIiAvPicsbWVzc2FnZSk7XHJcbiAgICB9LFxyXG4gICAgcGlja1VzZXI6ZnVuY3Rpb24odXJsLGNhbGxiYWNrLGZpbHRlcil7XHJcbiAgICAgICAgdmFyIHVzZXI9bnVsbDtcclxuICAgICAgICBpZighZmlsdGVyKWZpbHRlcj17fTtcclxuICAgICAgICB2YXIgZGxnPW5ldyBEaWFsb2coe1xyXG4gICAgICAgICAgICAnb25zaG93bic6ZnVuY3Rpb24oYm9keSl7XHJcbiAgICAgICAgICAgICAgICB2YXIgYnRuPWJvZHkuZmluZCgnLnNlYXJjaGJ0bicpO1xyXG4gICAgICAgICAgICAgICAgdmFyIGlucHV0PWJvZHkuZmluZCgnLnNlYXJjaHRleHQnKTtcclxuICAgICAgICAgICAgICAgIHZhciBsaXN0Ym94PWJvZHkuZmluZCgnLmxpc3QtZ3JvdXAnKTtcclxuICAgICAgICAgICAgICAgIHZhciBpc2xvYWRpbmc9ZmFsc2U7XHJcbiAgICAgICAgICAgICAgICBidG4uY2xpY2soZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgICAgICAgICBpZihpc2xvYWRpbmcpcmV0dXJuO1xyXG4gICAgICAgICAgICAgICAgICAgIGlzbG9hZGluZz10cnVlO1xyXG4gICAgICAgICAgICAgICAgICAgIGxpc3Rib3guaHRtbCgnPHNwYW4gY2xhc3M9XCJsaXN0LWxvYWRpbmdcIj7liqDovb3kuK0uLi48L3NwYW4+Jyk7XHJcbiAgICAgICAgICAgICAgICAgICAgZmlsdGVyWydrZXknXT1pbnB1dC52YWwoKTtcclxuICAgICAgICAgICAgICAgICAgICAkLmFqYXgoXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHVybDp1cmwsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0eXBlOidHRVQnLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZGF0YVR5cGU6J0pTT04nLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZGF0YTpmaWx0ZXIsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBzdWNjZXNzOmZ1bmN0aW9uKGpzb24pe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlzbG9hZGluZz1mYWxzZTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZihqc29uLnN0YXR1cyl7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmKGpzb24uZGF0YSAmJiBqc29uLmRhdGEubGVuZ3RoKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBsaXN0Ym94Lmh0bWwoJzxhIGhyZWY9XCJqYXZhc2NyaXB0OlwiIGRhdGEtaWQ9XCJ7QGlkfVwiIGNsYXNzPVwibGlzdC1ncm91cC1pdGVtIGxpc3QtZ3JvdXAtaXRlbS1hY3Rpb25cIj5be0BpZH1dJm5ic3A7PGkgY2xhc3M9XCJpb24tbWQtcGVyc29uXCI+PC9pPiB7QHVzZXJuYW1lfSZuYnNwOyZuYnNwOyZuYnNwOzxzbWFsbD48aSBjbGFzcz1cImlvbi1tZC1waG9uZS1wb3J0cmFpdFwiPjwvaT4ge0Btb2JpbGV9PC9zbWFsbD48L2E+Jy5jb21waWxlKGpzb24uZGF0YSwgdHJ1ZSkpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbGlzdGJveC5maW5kKCdhLmxpc3QtZ3JvdXAtaXRlbScpLmNsaWNrKGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgaWQgPSAkKHRoaXMpLmRhdGEoJ2lkJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgZm9yICh2YXIgaSA9IDA7IGkgPCBqc29uLmRhdGEubGVuZ3RoOyBpKyspIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYoanNvbi5kYXRhW2ldLmlkPT1pZCl7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB1c2VyPWpzb24uZGF0YVtpXTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxpc3Rib3guZmluZCgnYS5saXN0LWdyb3VwLWl0ZW0nKS5yZW1vdmVDbGFzcygnYWN0aXZlJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmFkZENsYXNzKCdhY3RpdmUnKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfSlcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfWVsc2V7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBsaXN0Ym94Lmh0bWwoJzxzcGFuIGNsYXNzPVwibGlzdC1sb2FkaW5nXCI+PGkgY2xhc3M9XCJpb24tbWQtd2FybmluZ1wiPjwvaT4g5rKh5pyJ5qOA57Si5Yiw5Lya5ZGYPC9zcGFuPicpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfWVsc2V7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxpc3Rib3guaHRtbCgnPHNwYW4gY2xhc3M9XCJ0ZXh0LWRhbmdlclwiPjxpIGNsYXNzPVwiaW9uLW1kLXdhcm5pbmdcIj48L2k+IOWKoOi9veWksei0pTwvc3Bhbj4nKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICApO1xyXG5cclxuICAgICAgICAgICAgICAgIH0pLnRyaWdnZXIoJ2NsaWNrJyk7XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdvbnN1cmUnOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgaWYoIXVzZXIpe1xyXG4gICAgICAgICAgICAgICAgICAgIHRvYXN0ci53YXJuaW5nKCfmsqHmnInpgInmi6nkvJrlkZghJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgaWYodHlwZW9mIGNhbGxiYWNrPT0nZnVuY3Rpb24nKXtcclxuICAgICAgICAgICAgICAgICAgICB2YXIgcmVzdWx0ID0gY2FsbGJhY2sodXNlcik7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHJlc3VsdDtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLnNob3coJzxkaXYgY2xhc3M9XCJpbnB1dC1ncm91cFwiPjxpbnB1dCB0eXBlPVwidGV4dFwiIGNsYXNzPVwiZm9ybS1jb250cm9sIHNlYXJjaHRleHRcIiBuYW1lPVwia2V5d29yZFwiIHBsYWNlaG9sZGVyPVwi5qC55o2u5Lya5ZGYaWTmiJblkI3np7DvvIznlLXor53mnaXmkJzntKJcIi8+PGRpdiBjbGFzcz1cImlucHV0LWdyb3VwLWFwcGVuZFwiPjxhIGNsYXNzPVwiYnRuIGJ0bi1vdXRsaW5lLXNlY29uZGFyeSBzZWFyY2hidG5cIj48aSBjbGFzcz1cImlvbi1tZC1zZWFyY2hcIj48L2k+PC9hPjwvZGl2PjwvZGl2PjxkaXYgY2xhc3M9XCJsaXN0LWdyb3VwIG10LTJcIj48L2Rpdj4nLCfor7fmkJzntKLlubbpgInmi6nkvJrlkZgnKTtcclxuICAgIH0sXHJcbiAgICBwaWNrTG9jYXRlOmZ1bmN0aW9uKHR5cGUsIGNhbGxiYWNrLCBsb2NhdGUpe1xyXG4gICAgICAgIHZhciBzZXR0ZWRMb2NhdGU9bnVsbDtcclxuICAgICAgICB2YXIgZGxnPW5ldyBEaWFsb2coe1xyXG4gICAgICAgICAgICAnc2l6ZSc6J2xnJyxcclxuICAgICAgICAgICAgJ29uc2hvd24nOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgdmFyIGJ0bj1ib2R5LmZpbmQoJy5zZWFyY2hidG4nKTtcclxuICAgICAgICAgICAgICAgIHZhciBpbnB1dD1ib2R5LmZpbmQoJy5zZWFyY2h0ZXh0Jyk7XHJcbiAgICAgICAgICAgICAgICB2YXIgbWFwYm94PWJvZHkuZmluZCgnLm1hcCcpO1xyXG4gICAgICAgICAgICAgICAgdmFyIG1hcGluZm89Ym9keS5maW5kKCcubWFwaW5mbycpO1xyXG4gICAgICAgICAgICAgICAgbWFwYm94LmNzcygnaGVpZ2h0JywkKHdpbmRvdykuaGVpZ2h0KCkqLjYpO1xyXG4gICAgICAgICAgICAgICAgdmFyIGlzbG9hZGluZz1mYWxzZTtcclxuICAgICAgICAgICAgICAgIHZhciBtYXA9SW5pdE1hcCgndGVuY2VudCcsbWFwYm94LGZ1bmN0aW9uKGFkZHJlc3MsbG9jYXRlKXtcclxuICAgICAgICAgICAgICAgICAgICBtYXBpbmZvLmh0bWwoYWRkcmVzcysnJm5ic3A7Jytsb2NhdGUubG5nKycsJytsb2NhdGUubGF0KTtcclxuICAgICAgICAgICAgICAgICAgICBzZXR0ZWRMb2NhdGU9bG9jYXRlO1xyXG4gICAgICAgICAgICAgICAgfSxsb2NhdGUpO1xyXG4gICAgICAgICAgICAgICAgYnRuLmNsaWNrKGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgICAgICAgICAgdmFyIHNlYXJjaD1pbnB1dC52YWwoKTtcclxuICAgICAgICAgICAgICAgICAgICBtYXAuc2V0TG9jYXRlKHNlYXJjaCk7XHJcbiAgICAgICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgJ29uc3VyZSc6ZnVuY3Rpb24oYm9keSl7XHJcbiAgICAgICAgICAgICAgICBpZighc2V0dGVkTG9jYXRlKXtcclxuICAgICAgICAgICAgICAgICAgICB0b2FzdHIud2FybmluZygn5rKh5pyJ6YCJ5oup5L2N572uIScpO1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIGlmKHR5cGVvZiBjYWxsYmFjaz09PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgICAgIHZhciByZXN1bHQgPSBjYWxsYmFjayhzZXR0ZWRMb2NhdGUpO1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiByZXN1bHQ7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS5zaG93KCc8ZGl2IGNsYXNzPVwiaW5wdXQtZ3JvdXBcIj48aW5wdXQgdHlwZT1cInRleHRcIiBjbGFzcz1cImZvcm0tY29udHJvbCBzZWFyY2h0ZXh0XCIgbmFtZT1cImtleXdvcmRcIiBwbGFjZWhvbGRlcj1cIuWhq+WGmeWcsOWdgOajgOe0ouS9jee9rlwiLz48ZGl2IGNsYXNzPVwiaW5wdXQtZ3JvdXAtYXBwZW5kXCI+PGEgY2xhc3M9XCJidG4gYnRuLW91dGxpbmUtc2Vjb25kYXJ5IHNlYXJjaGJ0blwiPjxpIGNsYXNzPVwiaW9uLW1kLXNlYXJjaFwiPjwvaT48L2E+PC9kaXY+PC9kaXY+JyArXHJcbiAgICAgICAgICAgICc8ZGl2IGNsYXNzPVwibWFwIG10LTJcIj48L2Rpdj4nICtcclxuICAgICAgICAgICAgJzxkaXYgY2xhc3M9XCJtYXBpbmZvIG10LTIgdGV4dC1tdXRlZFwiPuacqumAieaLqeS9jee9rjwvZGl2PicsJ+ivt+mAieaLqeWcsOWbvuS9jee9ricpO1xyXG4gICAgfVxyXG59O1xyXG5cclxualF1ZXJ5KGZ1bmN0aW9uKCQpe1xyXG5cclxuICAgIC8v55uR5o6n5oyJ6ZSuXHJcbiAgICAkKGRvY3VtZW50KS5vbigna2V5ZG93bicsIGZ1bmN0aW9uKGUpe1xyXG4gICAgICAgIGlmKCFEaWFsb2cuaW5zdGFuY2UpcmV0dXJuO1xyXG4gICAgICAgIHZhciBkbGc9RGlhbG9nLmluc3RhbmNlO1xyXG4gICAgICAgIGlmIChlLmtleUNvZGUgPT0gMTMpIHtcclxuICAgICAgICAgICAgZGxnLmJveC5maW5kKCcubW9kYWwtZm9vdGVyIC5idG4nKS5lcShkbGcub3B0aW9ucy5kZWZhdWx0QnRuKS50cmlnZ2VyKCdjbGljaycpO1xyXG4gICAgICAgIH1cclxuICAgICAgICAvL+m7mOiupOW3suebkeWQrOWFs+mXrVxyXG4gICAgICAgIC8qaWYgKGUua2V5Q29kZSA9PSAyNykge1xyXG4gICAgICAgICBzZWxmLmhpZGUoKTtcclxuICAgICAgICAgfSovXHJcbiAgICB9KTtcclxufSk7IiwiXHJcbmpRdWVyeS5leHRlbmQoalF1ZXJ5LmZuLHtcclxuICAgIHRhZ3M6ZnVuY3Rpb24obm0pe1xyXG4gICAgICAgIHZhciBkYXRhPVtdO1xyXG4gICAgICAgIHZhciB0cGw9JzxzcGFuIGNsYXNzPVwiYmFkZ2UgYmFkZ2UtaW5mb1wiPntAbGFiZWx9PGlucHV0IHR5cGU9XCJoaWRkZW5cIiBuYW1lPVwiJytubSsnXCIgdmFsdWU9XCJ7QGxhYmVsfVwiLz48YnV0dG9uIHR5cGU9XCJidXR0b25cIiBjbGFzcz1cImNsb3NlXCIgZGF0YS1kaXNtaXNzPVwiYWxlcnRcIiBhcmlhLWxhYmVsPVwiQ2xvc2VcIj48c3BhbiBhcmlhLWhpZGRlbj1cInRydWVcIj4mdGltZXM7PC9zcGFuPjwvYnV0dG9uPjwvc3Bhbj4nO1xyXG4gICAgICAgIHZhciBpdGVtPSQodGhpcykucGFyZW50cygnLmZvcm0tY29udHJvbCcpO1xyXG4gICAgICAgIHZhciBsYWJlbGdyb3VwPSQoJzxzcGFuIGNsYXNzPVwiYmFkZ2UtZ3JvdXBcIj48L3NwYW4+Jyk7XHJcbiAgICAgICAgdmFyIGlucHV0PXRoaXM7XHJcbiAgICAgICAgdGhpcy5iZWZvcmUobGFiZWxncm91cCk7XHJcbiAgICAgICAgdGhpcy5vbigna2V5dXAnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgIHZhciB2YWw9JCh0aGlzKS52YWwoKS5yZXBsYWNlKC/vvIwvZywnLCcpO1xyXG4gICAgICAgICAgICBpZih2YWwgJiYgdmFsLmluZGV4T2YoJywnKT4tMSl7XHJcbiAgICAgICAgICAgICAgICB2YXIgdmFscz12YWwuc3BsaXQoJywnKTtcclxuICAgICAgICAgICAgICAgIGZvcih2YXIgaT0wO2k8dmFscy5sZW5ndGg7aSsrKXtcclxuICAgICAgICAgICAgICAgICAgICB2YWxzW2ldPXZhbHNbaV0ucmVwbGFjZSgvXlxcc3xcXHMkL2csJycpO1xyXG4gICAgICAgICAgICAgICAgICAgIGlmKHZhbHNbaV0gJiYgZGF0YS5pbmRleE9mKHZhbHNbaV0pPT09LTEpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBkYXRhLnB1c2godmFsc1tpXSk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGxhYmVsZ3JvdXAuYXBwZW5kKHRwbC5jb21waWxlKHtsYWJlbDp2YWxzW2ldfSkpO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIGlucHV0LnZhbCgnJyk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS5vbignYmx1cicsZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgJCh0aGlzKS52YWwoJCh0aGlzKS52YWwoKSsnLCcpLnRyaWdnZXIoJ2tleXVwJyk7XHJcbiAgICAgICAgfSkudHJpZ2dlcigna2V5dXAnKTtcclxuICAgICAgICBsYWJlbGdyb3VwLm9uKCdjbGljaycsJy5jbG9zZScsZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgdmFyIHRhZz0kKHRoaXMpLnBhcmVudHMoJy5iYWRnZScpLmZpbmQoJ2lucHV0JykudmFsKCk7XHJcbiAgICAgICAgICAgIHZhciBpZD1kYXRhLmluZGV4T2YodGFnKTtcclxuICAgICAgICAgICAgaWYoaWQpZGF0YS5zcGxpY2UoaWQsMSk7XHJcbiAgICAgICAgICAgICQodGhpcykucGFyZW50cygnLmJhZGdlJykucmVtb3ZlKCk7XHJcbiAgICAgICAgfSk7XHJcbiAgICAgICAgaXRlbS5jbGljayhmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICBpbnB1dC5mb2N1cygpO1xyXG4gICAgICAgIH0pO1xyXG4gICAgfVxyXG59KTsiLCIvL+aXpeacn+e7hOS7tlxyXG5pZigkLmZuLmRhdGV0aW1lcGlja2VyKSB7XHJcbiAgICB2YXIgdG9vbHRpcHM9IHtcclxuICAgICAgICB0b2RheTogJ+WumuS9jeW9k+WJjeaXpeacnycsXHJcbiAgICAgICAgY2xlYXI6ICfmuIXpmaTlt7LpgInml6XmnJ8nLFxyXG4gICAgICAgIGNsb3NlOiAn5YWz6Zet6YCJ5oup5ZmoJyxcclxuICAgICAgICBzZWxlY3RNb250aDogJ+mAieaLqeaciOS7vScsXHJcbiAgICAgICAgcHJldk1vbnRoOiAn5LiK5Liq5pyIJyxcclxuICAgICAgICBuZXh0TW9udGg6ICfkuIvkuKrmnIgnLFxyXG4gICAgICAgIHNlbGVjdFllYXI6ICfpgInmi6nlubTku70nLFxyXG4gICAgICAgIHByZXZZZWFyOiAn5LiK5LiA5bm0JyxcclxuICAgICAgICBuZXh0WWVhcjogJ+S4i+S4gOW5tCcsXHJcbiAgICAgICAgc2VsZWN0RGVjYWRlOiAn6YCJ5oup5bm05Lu95Yy66Ze0JyxcclxuICAgICAgICBwcmV2RGVjYWRlOiAn5LiK5LiA5Yy66Ze0JyxcclxuICAgICAgICBuZXh0RGVjYWRlOiAn5LiL5LiA5Yy66Ze0JyxcclxuICAgICAgICBwcmV2Q2VudHVyeTogJ+S4iuS4quS4lue6qicsXHJcbiAgICAgICAgbmV4dENlbnR1cnk6ICfkuIvkuKrkuJbnuqonXHJcbiAgICB9O1xyXG4gICAgdmFyIGljb25zPXtcclxuICAgICAgICB0aW1lOiAnaW9uLW1kLXRpbWUnLFxyXG4gICAgICAgIGRhdGU6ICdpb24tbWQtY2FsZW5kYXInLFxyXG4gICAgICAgIHVwOiAnaW9uLW1kLWFycm93LWRyb3B1cCcsXHJcbiAgICAgICAgZG93bjogJ2lvbi1tZC1hcnJvdy1kcm9wZG93bicsXHJcbiAgICAgICAgcHJldmlvdXM6ICdpb24tbWQtYXJyb3ctZHJvcGxlZnQnLFxyXG4gICAgICAgIG5leHQ6ICdpb24tbWQtYXJyb3ctZHJvcHJpZ2h0JyxcclxuICAgICAgICB0b2RheTogJ2lvbi1tZC10b2RheScsXHJcbiAgICAgICAgY2xlYXI6ICdpb24tbWQtdHJhc2gnLFxyXG4gICAgICAgIGNsb3NlOiAnaW9uLW1kLWNsb3NlJ1xyXG4gICAgfTtcclxuICAgICQoJy5kYXRlcGlja2VyJykuZGF0ZXRpbWVwaWNrZXIoe1xyXG4gICAgICAgIGljb25zOmljb25zLFxyXG4gICAgICAgIHRvb2x0aXBzOnRvb2x0aXBzLFxyXG4gICAgICAgIGZvcm1hdDogJ1lZWVktTU0tREQnLFxyXG4gICAgICAgIGxvY2FsZTogJ3poLWNuJyxcclxuICAgICAgICBzaG93Q2xlYXI6dHJ1ZSxcclxuICAgICAgICBzaG93VG9kYXlCdXR0b246dHJ1ZSxcclxuICAgICAgICBzaG93Q2xvc2U6dHJ1ZSxcclxuICAgICAgICBrZWVwSW52YWxpZDp0cnVlXHJcbiAgICB9KTtcclxuXHJcbiAgICAkKCcuZGF0ZS1yYW5nZScpLmVhY2goZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHZhciBmcm9tID0gJCh0aGlzKS5maW5kKCdbbmFtZT1mcm9tZGF0ZV0sLmZyb21kYXRlJyksIHRvID0gJCh0aGlzKS5maW5kKCdbbmFtZT10b2RhdGVdLC50b2RhdGUnKTtcclxuICAgICAgICB2YXIgb3B0aW9ucyA9IHtcclxuICAgICAgICAgICAgaWNvbnM6aWNvbnMsXHJcbiAgICAgICAgICAgIHRvb2x0aXBzOnRvb2x0aXBzLFxyXG4gICAgICAgICAgICBmb3JtYXQ6ICdZWVlZLU1NLUREJyxcclxuICAgICAgICAgICAgbG9jYWxlOid6aC1jbicsXHJcbiAgICAgICAgICAgIHNob3dDbGVhcjp0cnVlLFxyXG4gICAgICAgICAgICBzaG93VG9kYXlCdXR0b246dHJ1ZSxcclxuICAgICAgICAgICAgc2hvd0Nsb3NlOnRydWUsXHJcbiAgICAgICAgICAgIGtlZXBJbnZhbGlkOnRydWVcclxuICAgICAgICB9O1xyXG4gICAgICAgIGZyb20uZGF0ZXRpbWVwaWNrZXIob3B0aW9ucykub24oJ2RwLmNoYW5nZScsIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgaWYgKGZyb20udmFsKCkpIHtcclxuICAgICAgICAgICAgICAgIHRvLmRhdGEoJ0RhdGVUaW1lUGlja2VyJykubWluRGF0ZShmcm9tLnZhbCgpKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pO1xyXG4gICAgICAgIHRvLmRhdGV0aW1lcGlja2VyKG9wdGlvbnMpLm9uKCdkcC5jaGFuZ2UnLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIGlmICh0by52YWwoKSkge1xyXG4gICAgICAgICAgICAgICAgZnJvbS5kYXRhKCdEYXRlVGltZVBpY2tlcicpLm1heERhdGUodG8udmFsKCkpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSk7XHJcbiAgICB9KTtcclxufSIsIlxyXG4oZnVuY3Rpb24od2luZG93LCQpIHtcclxuICAgIHZhciBhcGlzID0ge1xyXG4gICAgICAgICdiYWlkdSc6ICdodHRwczovL2FwaS5tYXAuYmFpZHUuY29tL2FwaT9haz1yTzl0T2RFV0ZmdnlHZ0RraVdxRmp4SzYmdj0xLjUmc2VydmljZXM9ZmFsc2UmY2FsbGJhY2s9JyxcclxuICAgICAgICAnZ29vZ2xlJzogJ2h0dHBzOi8vbWFwcy5nb29nbGUuY29tL21hcHMvYXBpL2pzP2tleT1BSXphU3lCOGxvcnZsNkV0cUlXejY3YmpXQnJ1T2htOU5ZUzFlMjQmY2FsbGJhY2s9JyxcclxuICAgICAgICAndGVuY2VudCc6ICdodHRwczovL21hcC5xcS5jb20vYXBpL2pzP3Y9Mi5leHAma2V5PTdJNUJaLVFVRTZSLUpYTFdWLVdUVkFBLUNKTVlGLTdQQkJJJmNhbGxiYWNrPScsXHJcbiAgICAgICAgJ2dhb2RlJzogJ2h0dHBzOi8vd2ViYXBpLmFtYXAuY29tL21hcHM/dj0xLjMma2V5PTNlYzMxMWI1ZGIwZDU5N2U3OTQyMmVlYjlhNmQ0NDQ5JmNhbGxiYWNrPSdcclxuICAgIH07XHJcblxyXG4gICAgZnVuY3Rpb24gbG9hZFNjcmlwdChzcmMpIHtcclxuICAgICAgICB2YXIgc2NyaXB0ID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudChcInNjcmlwdFwiKTtcclxuICAgICAgICBzY3JpcHQudHlwZSA9IFwidGV4dC9qYXZhc2NyaXB0XCI7XHJcbiAgICAgICAgc2NyaXB0LnNyYyA9IHNyYztcclxuICAgICAgICBkb2N1bWVudC5ib2R5LmFwcGVuZENoaWxkKHNjcmlwdCk7XHJcbiAgICB9XHJcblxyXG4gICAgdmFyIG1hcE9iaixtYXBCb3gsb25QaWNrO1xyXG5cclxuICAgIGZ1bmN0aW9uIEluaXRNYXAobWFwa2V5LGJveCxjYWxsYmFjayxsb2NhdGUpIHtcclxuICAgICAgICBpZiAobWFwT2JqKSBtYXBPYmouaGlkZSgpO1xyXG4gICAgICAgIG1hcEJveD0kKGJveCk7XHJcbiAgICAgICAgb25QaWNrPWNhbGxiYWNrO1xyXG5cclxuICAgICAgICBzd2l0Y2ggKG1hcGtleS50b0xvd2VyQ2FzZSgpKSB7XHJcbiAgICAgICAgICAgIGNhc2UgJ2JhaWR1JzpcclxuICAgICAgICAgICAgICAgIG1hcE9iaiA9IG5ldyBCYWlkdU1hcCgpO1xyXG4gICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgIGNhc2UgJ2dvb2dsZSc6XHJcbiAgICAgICAgICAgICAgICBtYXBPYmogPSBuZXcgR29vZ2xlTWFwKCk7XHJcbiAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgY2FzZSAndGVuY2VudCc6XHJcbiAgICAgICAgICAgIGNhc2UgJ3FxJzpcclxuICAgICAgICAgICAgICAgIG1hcE9iaiA9IG5ldyBUZW5jZW50TWFwKCk7XHJcbiAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgY2FzZSAnZ2FvZGUnOlxyXG4gICAgICAgICAgICAgICAgbWFwT2JqID0gbmV3IEdhb2RlTWFwKCk7XHJcbiAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICB9XHJcbiAgICAgICAgaWYgKCFtYXBPYmopIHJldHVybiB0b2FzdHIud2FybmluZygn5LiN5pSv5oyB6K+l5Zyw5Zu+57G75Z6LJyk7XHJcbiAgICAgICAgaWYobG9jYXRlKXtcclxuICAgICAgICAgICAgaWYodHlwZW9mIGxvY2F0ZT09PSdzdHJpbmcnKXtcclxuICAgICAgICAgICAgICAgIHZhciBsb2M9bG9jYXRlLnNwbGl0KCcsJyk7XHJcbiAgICAgICAgICAgICAgICBsb2NhdGU9e1xyXG4gICAgICAgICAgICAgICAgICAgIGxuZzpwYXJzZUZsb2F0KGxvY1swXSksXHJcbiAgICAgICAgICAgICAgICAgICAgbGF0OnBhcnNlRmxvYXQobG9jWzFdKVxyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIG1hcE9iai5sb2NhdGU9bG9jYXRlO1xyXG4gICAgICAgIH1cclxuICAgICAgICBtYXBPYmouc2V0TWFwKCk7XHJcblxyXG4gICAgICAgIHJldHVybiBtYXBPYmo7XHJcbiAgICB9XHJcblxyXG4gICAgZnVuY3Rpb24gQmFzZU1hcCh0eXBlKSB7XHJcbiAgICAgICAgdGhpcy5tYXBUeXBlID0gdHlwZTtcclxuICAgICAgICB0aGlzLmlzaGlkZSA9IGZhbHNlO1xyXG4gICAgICAgIHRoaXMuaXNzaG93ID0gZmFsc2U7XHJcbiAgICAgICAgdGhpcy50b3Nob3cgPSBmYWxzZTtcclxuICAgICAgICB0aGlzLm1hcmtlciA9IG51bGw7XHJcbiAgICAgICAgdGhpcy5pbmZvV2luZG93ID0gbnVsbDtcclxuICAgICAgICB0aGlzLm1hcGJveCA9IG51bGw7XHJcbiAgICAgICAgdGhpcy5sb2NhdGUgPSB7bG5nOjExNi4zOTY3OTUsbGF0OjM5LjkzMzA4NH07XHJcbiAgICAgICAgdGhpcy5tYXAgPSBudWxsO1xyXG4gICAgfVxyXG5cclxuICAgIEJhc2VNYXAucHJvdG90eXBlLmlzQVBJUmVhZHkgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgfTtcclxuICAgIEJhc2VNYXAucHJvdG90eXBlLnNldE1hcCA9IGZ1bmN0aW9uICgpIHtcclxuICAgIH07XHJcbiAgICBCYXNlTWFwLnByb3RvdHlwZS5zaG93SW5mbyA9IGZ1bmN0aW9uICgpIHtcclxuICAgIH07XHJcbiAgICBCYXNlTWFwLnByb3RvdHlwZS5nZXRBZGRyZXNzID0gZnVuY3Rpb24gKHJzKSB7XHJcbiAgICAgICAgcmV0dXJuIFwiXCI7XHJcbiAgICB9O1xyXG4gICAgQmFzZU1hcC5wcm90b3R5cGUuc2V0TG9jYXRlID0gZnVuY3Rpb24gKGFkZHJlc3MpIHtcclxuICAgIH07XHJcblxyXG4gICAgQmFzZU1hcC5wcm90b3R5cGUubG9hZEFQSSA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICB2YXIgc2VsZiA9IHRoaXM7XHJcbiAgICAgICAgaWYgKCF0aGlzLmlzQVBJUmVhZHkoKSkge1xyXG4gICAgICAgICAgICB0aGlzLm1hcGJveCA9ICQoJzxkaXYgaWQ9XCInICsgdGhpcy5tYXBUeXBlICsgJ21hcFwiIGNsYXNzPVwibWFwYm94XCI+bG9hZGluZy4uLjwvZGl2PicpO1xyXG4gICAgICAgICAgICBtYXBCb3guYXBwZW5kKHRoaXMubWFwYm94KTtcclxuXHJcbiAgICAgICAgICAgIC8vY29uc29sZS5sb2codGhpcy5tYXBUeXBlKycgbWFwbG9hZGluZy4uLicpO1xyXG4gICAgICAgICAgICB2YXIgZnVuYyA9ICdtYXBsb2FkJyArIG5ldyBEYXRlKCkuZ2V0VGltZSgpO1xyXG4gICAgICAgICAgICB3aW5kb3dbZnVuY10gPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgICAgICBzZWxmLnNldE1hcCgpO1xyXG4gICAgICAgICAgICAgICAgZGVsZXRlIHdpbmRvd1tmdW5jXTtcclxuICAgICAgICAgICAgfTtcclxuICAgICAgICAgICAgbG9hZFNjcmlwdChhcGlzW3RoaXMubWFwVHlwZV0gKyBmdW5jKTtcclxuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgIC8vY29uc29sZS5sb2codGhpcy5tYXBUeXBlICsgJyBtYXBsb2FkZWQnKTtcclxuICAgICAgICAgICAgdGhpcy5tYXBib3ggPSAkKCcjJyArIHRoaXMubWFwVHlwZSArICdtYXAnKTtcclxuICAgICAgICAgICAgaWYgKHRoaXMubWFwYm94Lmxlbmd0aCA8IDEpIHtcclxuICAgICAgICAgICAgICAgIHRoaXMubWFwYm94ID0gJCgnPGRpdiBpZD1cIicgKyB0aGlzLm1hcFR5cGUgKyAnbWFwXCIgY2xhc3M9XCJtYXBib3hcIj48L2Rpdj4nKTtcclxuICAgICAgICAgICAgICAgIG1hcEJveC5hcHBlbmQodGhpcy5tYXBib3gpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIHJldHVybiB0cnVlO1xyXG4gICAgICAgIH1cclxuICAgIH07XHJcbiAgICBCYXNlTWFwLnByb3RvdHlwZS5iaW5kRXZlbnRzID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHZhciBzZWxmID0gdGhpcztcclxuICAgICAgICAkKCcjdHh0VGl0bGUnKS51bmJpbmQoKS5ibHVyKGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgc2VsZi5zaG93SW5mbygpO1xyXG4gICAgICAgIH0pO1xyXG4gICAgICAgICQoJyN0eHRDb250ZW50JykudW5iaW5kKCkuYmx1cihmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIHNlbGYuc2hvd0luZm8oKTtcclxuICAgICAgICB9KTtcclxuICAgIH07XHJcbiAgICBCYXNlTWFwLnByb3RvdHlwZS5zZXRJbmZvQ29udGVudCA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICBpZiAoIXRoaXMuaW5mb1dpbmRvdykgcmV0dXJuO1xyXG4gICAgICAgIHZhciB0aXRsZSA9ICc8Yj7lvZPliY3kvY3nva48L2I+JztcclxuICAgICAgICB2YXIgYWRkciA9ICc8cCBzdHlsZT1cImxpbmUtaGVpZ2h0OjEuNmVtO1wiPjwvcD4nO1xyXG4gICAgICAgIGlmICh0aGlzLmluZm9XaW5kb3cuc2V0VGl0bGUpIHtcclxuICAgICAgICAgICAgdGhpcy5pbmZvV2luZG93LnNldFRpdGxlKHRpdGxlKTtcclxuICAgICAgICAgICAgdGhpcy5pbmZvV2luZG93LnNldENvbnRlbnQoYWRkcik7XHJcbiAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgdmFyIGNvbnRlbnQgPSAnPGgzPicgKyB0aXRsZSArICc8L2gzPjxkaXYgc3R5bGU9XCJ3aWR0aDoyNTBweFwiPicgKyBhZGRyICsgJzwvZGl2Pic7XHJcbiAgICAgICAgICAgIHRoaXMuaW5mb1dpbmRvdy5zZXRDb250ZW50KGNvbnRlbnQpO1xyXG4gICAgICAgIH1cclxuICAgIH07XHJcbiAgICBCYXNlTWFwLnByb3RvdHlwZS5zaG93TG9jYXRpb25JbmZvID0gZnVuY3Rpb24gKHB0LCBycykge1xyXG5cclxuICAgICAgICB0aGlzLnNob3dJbmZvKCk7XHJcbiAgICAgICAgdmFyIGFkZHJlc3M9dGhpcy5nZXRBZGRyZXNzKHJzKTtcclxuICAgICAgICB2YXIgbG9jYXRlPXt9O1xyXG4gICAgICAgIGlmICh0eXBlb2YgKHB0LmxuZykgPT09ICdmdW5jdGlvbicpIHtcclxuICAgICAgICAgICAgbG9jYXRlLmxuZz1wdC5sbmcoKTtcclxuICAgICAgICAgICAgbG9jYXRlLmxhdD1wdC5sYXQoKTtcclxuICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICBsb2NhdGUubG5nPXB0LmxuZztcclxuICAgICAgICAgICAgbG9jYXRlLmxhdD1wdC5sYXQ7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICBvblBpY2soYWRkcmVzcyxsb2NhdGUpO1xyXG4gICAgfTtcclxuICAgIEJhc2VNYXAucHJvdG90eXBlLnNob3cgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgdGhpcy5pc2hpZGUgPSBmYWxzZTtcclxuICAgICAgICB0aGlzLnNldE1hcCgpO1xyXG4gICAgICAgIHRoaXMuc2hvd0luZm8oKTtcclxuICAgIH07XHJcbiAgICBCYXNlTWFwLnByb3RvdHlwZS5oaWRlID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHRoaXMuaXNoaWRlID0gdHJ1ZTtcclxuICAgICAgICBpZiAodGhpcy5pbmZvV2luZG93KSB7XHJcbiAgICAgICAgICAgIHRoaXMuaW5mb1dpbmRvdy5jbG9zZSgpO1xyXG4gICAgICAgIH1cclxuICAgICAgICBpZiAodGhpcy5tYXBib3gpIHtcclxuICAgICAgICAgICAgJCh0aGlzLm1hcGJveCkucmVtb3ZlKCk7XHJcbiAgICAgICAgfVxyXG4gICAgfTtcclxuXHJcblxyXG4gICAgZnVuY3Rpb24gQmFpZHVNYXAoKSB7XHJcbiAgICAgICAgQmFzZU1hcC5jYWxsKHRoaXMsIFwiYmFpZHVcIik7XHJcbiAgICB9XHJcblxyXG4gICAgQmFpZHVNYXAucHJvdG90eXBlID0gbmV3IEJhc2VNYXAoKTtcclxuICAgIEJhaWR1TWFwLnByb3RvdHlwZS5jb25zdHJ1Y3RvciA9IEJhaWR1TWFwO1xyXG4gICAgQmFpZHVNYXAucHJvdG90eXBlLmlzQVBJUmVhZHkgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgcmV0dXJuICEhd2luZG93WydCTWFwJ107XHJcbiAgICB9O1xyXG4gICAgQmFpZHVNYXAucHJvdG90eXBlLnNldE1hcCA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICB2YXIgc2VsZiA9IHRoaXM7XHJcbiAgICAgICAgaWYgKHRoaXMuaXNzaG93IHx8IHRoaXMuaXNoaWRlKSByZXR1cm47XHJcbiAgICAgICAgaWYgKCF0aGlzLmxvYWRBUEkoKSkgcmV0dXJuO1xyXG5cclxuICAgICAgICB2YXIgbWFwID0gc2VsZi5tYXAgPSBuZXcgQk1hcC5NYXAodGhpcy5tYXBib3guYXR0cignaWQnKSk7IC8v5Yid5aeL5YyW5Zyw5Zu+XHJcbiAgICAgICAgbWFwLmFkZENvbnRyb2wobmV3IEJNYXAuTmF2aWdhdGlvbkNvbnRyb2woKSk7ICAvL+WIneWni+WMluWcsOWbvuaOp+S7tlxyXG4gICAgICAgIG1hcC5hZGRDb250cm9sKG5ldyBCTWFwLlNjYWxlQ29udHJvbCgpKTtcclxuICAgICAgICBtYXAuYWRkQ29udHJvbChuZXcgQk1hcC5PdmVydmlld01hcENvbnRyb2woKSk7XHJcbiAgICAgICAgbWFwLmVuYWJsZVNjcm9sbFdoZWVsWm9vbSgpO1xyXG5cclxuICAgICAgICB2YXIgcG9pbnQgPSBuZXcgQk1hcC5Qb2ludCh0aGlzLmxvY2F0ZS5sbmcsIHRoaXMubG9jYXRlLmxhdCk7XHJcbiAgICAgICAgbWFwLmNlbnRlckFuZFpvb20ocG9pbnQsIDE1KTsgLy/liJ3lp4vljJblnLDlm77kuK3lv4PngrlcclxuICAgICAgICB0aGlzLm1hcmtlciA9IG5ldyBCTWFwLk1hcmtlcihwb2ludCk7IC8v5Yid5aeL5YyW5Zyw5Zu+5qCH6K6wXHJcbiAgICAgICAgdGhpcy5tYXJrZXIuZW5hYmxlRHJhZ2dpbmcoKTsgLy/moIforrDlvIDlkK/mi5bmi71cclxuXHJcbiAgICAgICAgdmFyIGdjID0gbmV3IEJNYXAuR2VvY29kZXIoKTsgLy/lnLDlnYDop6PmnpDnsbtcclxuICAgICAgICAvL+a3u+WKoOagh+iusOaLluaLveebkeWQrFxyXG4gICAgICAgIHRoaXMubWFya2VyLmFkZEV2ZW50TGlzdGVuZXIoXCJkcmFnZW5kXCIsIGZ1bmN0aW9uIChlKSB7XHJcbiAgICAgICAgICAgIC8v6I635Y+W5Zyw5Z2A5L+h5oGvXHJcbiAgICAgICAgICAgIGdjLmdldExvY2F0aW9uKGUucG9pbnQsIGZ1bmN0aW9uIChycykge1xyXG4gICAgICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKGUucG9pbnQsIHJzKTtcclxuICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIC8v5re75Yqg5qCH6K6w54K55Ye755uR5ZCsXHJcbiAgICAgICAgdGhpcy5tYXJrZXIuYWRkRXZlbnRMaXN0ZW5lcihcImNsaWNrXCIsIGZ1bmN0aW9uIChlKSB7XHJcbiAgICAgICAgICAgIGdjLmdldExvY2F0aW9uKGUucG9pbnQsIGZ1bmN0aW9uIChycykge1xyXG4gICAgICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKGUucG9pbnQsIHJzKTtcclxuICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIG1hcC5hZGRPdmVybGF5KHRoaXMubWFya2VyKTsgLy/lsIbmoIforrDmt7vliqDliLDlnLDlm77kuK1cclxuXHJcbiAgICAgICAgZ2MuZ2V0TG9jYXRpb24ocG9pbnQsIGZ1bmN0aW9uIChycykge1xyXG4gICAgICAgICAgICBzZWxmLnNob3dMb2NhdGlvbkluZm8ocG9pbnQsIHJzKTtcclxuICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgdGhpcy5pbmZvV2luZG93ID0gbmV3IEJNYXAuSW5mb1dpbmRvdyhcIlwiLCB7XHJcbiAgICAgICAgICAgIHdpZHRoOiAyNTAsXHJcbiAgICAgICAgICAgIHRpdGxlOiBcIlwiXHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIHRoaXMuYmluZEV2ZW50cygpO1xyXG5cclxuICAgICAgICB0aGlzLmlzc2hvdyA9IHRydWU7XHJcbiAgICAgICAgaWYgKHRoaXMudG9zaG93KSB7XHJcbiAgICAgICAgICAgIHRoaXMuc2hvd0luZm8oKTtcclxuICAgICAgICAgICAgdGhpcy50b3Nob3cgPSBmYWxzZTtcclxuICAgICAgICB9XHJcbiAgICB9O1xyXG5cclxuICAgIEJhaWR1TWFwLnByb3RvdHlwZS5zaG93SW5mbyA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICBpZiAodGhpcy5pc2hpZGUpIHJldHVybjtcclxuICAgICAgICBpZiAoIXRoaXMuaXNzaG93KSB7XHJcbiAgICAgICAgICAgIHRoaXMudG9zaG93ID0gdHJ1ZTtcclxuICAgICAgICAgICAgcmV0dXJuO1xyXG4gICAgICAgIH1cclxuICAgICAgICB0aGlzLnNldEluZm9Db250ZW50KCk7XHJcblxyXG4gICAgICAgIHRoaXMubWFya2VyLm9wZW5JbmZvV2luZG93KHRoaXMuaW5mb1dpbmRvdyk7XHJcbiAgICB9O1xyXG4gICAgQmFpZHVNYXAucHJvdG90eXBlLmdldEFkZHJlc3MgPSBmdW5jdGlvbiAocnMpIHtcclxuICAgICAgICB2YXIgYWRkQ29tcCA9IHJzLmFkZHJlc3NDb21wb25lbnRzO1xyXG4gICAgICAgIGlmKGFkZENvbXApIHtcclxuICAgICAgICAgICAgcmV0dXJuIGFkZENvbXAucHJvdmluY2UgKyBcIiwgXCIgKyBhZGRDb21wLmNpdHkgKyBcIiwgXCIgKyBhZGRDb21wLmRpc3RyaWN0ICsgXCIsIFwiICsgYWRkQ29tcC5zdHJlZXQgKyBcIiwgXCIgKyBhZGRDb21wLnN0cmVldE51bWJlcjtcclxuICAgICAgICB9XHJcbiAgICB9O1xyXG4gICAgQmFpZHVNYXAucHJvdG90eXBlLnNldExvY2F0ZSA9IGZ1bmN0aW9uIChhZGRyZXNzKSB7XHJcbiAgICAgICAgLy8g5Yib5bu65Zyw5Z2A6Kej5p6Q5Zmo5a6e5L6LXHJcbiAgICAgICAgdmFyIG15R2VvID0gbmV3IEJNYXAuR2VvY29kZXIoKTtcclxuICAgICAgICB2YXIgc2VsZiA9IHRoaXM7XHJcbiAgICAgICAgbXlHZW8uZ2V0UG9pbnQoYWRkcmVzcywgZnVuY3Rpb24gKHBvaW50KSB7XHJcbiAgICAgICAgICAgIGlmIChwb2ludCkge1xyXG4gICAgICAgICAgICAgICAgc2VsZi5tYXAuY2VudGVyQW5kWm9vbShwb2ludCwgMTEpO1xyXG4gICAgICAgICAgICAgICAgc2VsZi5tYXJrZXIuc2V0UG9zaXRpb24ocG9pbnQpO1xyXG4gICAgICAgICAgICAgICAgbXlHZW8uZ2V0TG9jYXRpb24ocG9pbnQsIGZ1bmN0aW9uIChycykge1xyXG4gICAgICAgICAgICAgICAgICAgIHNlbGYuc2hvd0xvY2F0aW9uSW5mbyhwb2ludCwgcnMpO1xyXG4gICAgICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICB0b2FzdHIud2FybmluZyhcIuWcsOWdgOS/oeaBr+S4jeato+ehru+8jOWumuS9jeWksei0pVwiKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0sICcnKTtcclxuICAgIH07XHJcblxyXG5cclxuICAgIGZ1bmN0aW9uIEdvb2dsZU1hcCgpIHtcclxuICAgICAgICBCYXNlTWFwLmNhbGwodGhpcywgXCJnb29nbGVcIik7XHJcbiAgICAgICAgdGhpcy5pbmZvT3B0cyA9IHtcclxuICAgICAgICAgICAgd2lkdGg6IDI1MCwgICAgIC8v5L+h5oGv56qX5Y+j5a695bqmXHJcbiAgICAgICAgICAgIC8vICAgaGVpZ2h0OiAxMDAsICAgICAvL+S/oeaBr+eql+WPo+mrmOW6plxyXG4gICAgICAgICAgICB0aXRsZTogXCJcIiAgLy/kv6Hmga/nqpflj6PmoIfpophcclxuICAgICAgICB9O1xyXG4gICAgfVxyXG5cclxuICAgIEdvb2dsZU1hcC5wcm90b3R5cGUgPSBuZXcgQmFzZU1hcCgpO1xyXG4gICAgR29vZ2xlTWFwLnByb3RvdHlwZS5jb25zdHJ1Y3RvciA9IEdvb2dsZU1hcDtcclxuICAgIEdvb2dsZU1hcC5wcm90b3R5cGUuaXNBUElSZWFkeSA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICByZXR1cm4gd2luZG93Wydnb29nbGUnXSAmJiB3aW5kb3dbJ2dvb2dsZSddWydtYXBzJ11cclxuICAgIH07XHJcbiAgICBHb29nbGVNYXAucHJvdG90eXBlLnNldE1hcCA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICB2YXIgc2VsZiA9IHRoaXM7XHJcbiAgICAgICAgaWYgKHRoaXMuaXNzaG93IHx8IHRoaXMuaXNoaWRlKSByZXR1cm47XHJcbiAgICAgICAgaWYgKCF0aGlzLmxvYWRBUEkoKSkgcmV0dXJuO1xyXG5cclxuICAgICAgICAvL+ivtOaYjuWcsOWbvuW3suWIh+aNolxyXG4gICAgICAgIGlmICh0aGlzLm1hcGJveC5sZW5ndGggPCAxKSByZXR1cm47XHJcblxyXG4gICAgICAgIHZhciBtYXAgPSBzZWxmLm1hcCA9IG5ldyBnb29nbGUubWFwcy5NYXAodGhpcy5tYXBib3hbMF0sIHtcclxuICAgICAgICAgICAgem9vbTogMTUsXHJcbiAgICAgICAgICAgIGRyYWdnYWJsZTogdHJ1ZSxcclxuICAgICAgICAgICAgc2NhbGVDb250cm9sOiB0cnVlLFxyXG4gICAgICAgICAgICBzdHJlZXRWaWV3Q29udHJvbDogdHJ1ZSxcclxuICAgICAgICAgICAgem9vbUNvbnRyb2w6IHRydWVcclxuICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgLy/ojrflj5bnu4/nuqzluqblnZDmoIflgLxcclxuICAgICAgICB2YXIgcG9pbnQgPSBuZXcgZ29vZ2xlLm1hcHMuTGF0TG5nKHRoaXMubG9jYXRlKTtcclxuICAgICAgICBtYXAucGFuVG8ocG9pbnQpO1xyXG4gICAgICAgIHRoaXMubWFya2VyID0gbmV3IGdvb2dsZS5tYXBzLk1hcmtlcih7cG9zaXRpb246IHBvaW50LCBtYXA6IG1hcCwgZHJhZ2dhYmxlOiB0cnVlfSk7XHJcblxyXG5cclxuICAgICAgICB2YXIgZ2MgPSBuZXcgZ29vZ2xlLm1hcHMuR2VvY29kZXIoKTtcclxuXHJcbiAgICAgICAgdGhpcy5tYXJrZXIuYWRkTGlzdGVuZXIoXCJkcmFnZW5kXCIsIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgcG9pbnQgPSBzZWxmLm1hcmtlci5nZXRQb3NpdGlvbigpO1xyXG4gICAgICAgICAgICBnYy5nZW9jb2RlKHsnbG9jYXRpb24nOiBwb2ludH0sIGZ1bmN0aW9uIChycykge1xyXG4gICAgICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKHBvaW50LCBycyk7XHJcbiAgICAgICAgICAgIH0pO1xyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICAvL+a3u+WKoOagh+iusOeCueWHu+ebkeWQrFxyXG4gICAgICAgIHRoaXMubWFya2VyLmFkZExpc3RlbmVyKFwiY2xpY2tcIiwgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICBwb2ludCA9IHNlbGYubWFya2VyLmdldFBvc2l0aW9uKCk7XHJcbiAgICAgICAgICAgIGdjLmdlb2NvZGUoeydsb2NhdGlvbic6IHBvaW50fSwgZnVuY3Rpb24gKHJzKSB7XHJcbiAgICAgICAgICAgICAgICBzZWxmLnNob3dMb2NhdGlvbkluZm8ocG9pbnQsIHJzKTtcclxuICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIHRoaXMuYmluZEV2ZW50cygpO1xyXG5cclxuICAgICAgICBnYy5nZW9jb2RlKHsnbG9jYXRpb24nOiBwb2ludH0sIGZ1bmN0aW9uIChycykge1xyXG4gICAgICAgICAgICBzZWxmLnNob3dMb2NhdGlvbkluZm8ocG9pbnQsIHJzKTtcclxuICAgICAgICB9KTtcclxuICAgICAgICB0aGlzLmluZm9XaW5kb3cgPSBuZXcgZ29vZ2xlLm1hcHMuSW5mb1dpbmRvdyh7bWFwOiBtYXB9KTtcclxuICAgICAgICB0aGlzLmluZm9XaW5kb3cuc2V0UG9zaXRpb24ocG9pbnQpO1xyXG5cclxuICAgICAgICB0aGlzLmlzc2hvdyA9IHRydWU7XHJcbiAgICAgICAgaWYgKHRoaXMudG9zaG93KSB7XHJcbiAgICAgICAgICAgIHRoaXMuc2hvd0luZm8oKTtcclxuICAgICAgICAgICAgdGhpcy50b3Nob3cgPSBmYWxzZTtcclxuICAgICAgICB9XHJcbiAgICB9O1xyXG5cclxuICAgIEdvb2dsZU1hcC5wcm90b3R5cGUuc2hvd0luZm8gPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgaWYgKHRoaXMuaXNoaWRlKSByZXR1cm47XHJcbiAgICAgICAgaWYgKCF0aGlzLmlzc2hvdykge1xyXG4gICAgICAgICAgICB0aGlzLnRvc2hvdyA9IHRydWU7XHJcbiAgICAgICAgICAgIHJldHVybjtcclxuICAgICAgICB9XHJcbiAgICAgICAgdGhpcy5pbmZvV2luZG93LnNldE9wdGlvbnMoe3Bvc2l0aW9uOiB0aGlzLm1hcmtlci5nZXRQb3NpdGlvbigpfSk7XHJcbiAgICAgICAgdGhpcy5zZXRJbmZvQ29udGVudCgpO1xyXG5cclxuICAgIH07XHJcbiAgICBHb29nbGVNYXAucHJvdG90eXBlLmdldEFkZHJlc3MgPSBmdW5jdGlvbiAocnMsIHN0YXR1cykge1xyXG4gICAgICAgIGlmIChycyAmJiByc1swXSkge1xyXG4gICAgICAgICAgICByZXR1cm4gcnNbMF0uZm9ybWF0dGVkX2FkZHJlc3M7XHJcbiAgICAgICAgfVxyXG4gICAgfTtcclxuICAgIEdvb2dsZU1hcC5wcm90b3R5cGUuc2V0TG9jYXRlID0gZnVuY3Rpb24gKGFkZHJlc3MpIHtcclxuICAgICAgICAvLyDliJvlu7rlnLDlnYDop6PmnpDlmajlrp7kvotcclxuICAgICAgICB2YXIgbXlHZW8gPSBuZXcgZ29vZ2xlLm1hcHMuR2VvY29kZXIoKTtcclxuICAgICAgICB2YXIgc2VsZiA9IHRoaXM7XHJcbiAgICAgICAgbXlHZW8uZ2V0UG9pbnQoYWRkcmVzcywgZnVuY3Rpb24gKHBvaW50KSB7XHJcbiAgICAgICAgICAgIGlmIChwb2ludCkge1xyXG4gICAgICAgICAgICAgICAgc2VsZi5tYXAuY2VudGVyQW5kWm9vbShwb2ludCwgMTEpO1xyXG4gICAgICAgICAgICAgICAgc2VsZi5tYXJrZXIuc2V0UG9zaXRpb24ocG9pbnQpO1xyXG4gICAgICAgICAgICAgICAgbXlHZW8uZ2V0TG9jYXRpb24ocG9pbnQsIGZ1bmN0aW9uIChycykge1xyXG4gICAgICAgICAgICAgICAgICAgIHNlbGYuc2hvd0xvY2F0aW9uSW5mbyhwb2ludCwgcnMpO1xyXG4gICAgICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICB0b2FzdHIud2FybmluZyhcIuWcsOWdgOS/oeaBr+S4jeato+ehru+8jOWumuS9jeWksei0pVwiKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0sICcnKTtcclxuICAgIH07XHJcblxyXG4gICAgZnVuY3Rpb24gVGVuY2VudE1hcCgpIHtcclxuICAgICAgICBCYXNlTWFwLmNhbGwodGhpcywgXCJ0ZW5jZW50XCIpO1xyXG4gICAgfVxyXG5cclxuICAgIFRlbmNlbnRNYXAucHJvdG90eXBlID0gbmV3IEJhc2VNYXAoKTtcclxuICAgIFRlbmNlbnRNYXAucHJvdG90eXBlLmNvbnN0cnVjdG9yID0gVGVuY2VudE1hcDtcclxuICAgIFRlbmNlbnRNYXAucHJvdG90eXBlLmlzQVBJUmVhZHkgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgcmV0dXJuIHdpbmRvd1sncXEnXSAmJiB3aW5kb3dbJ3FxJ11bJ21hcHMnXTtcclxuICAgIH07XHJcblxyXG4gICAgVGVuY2VudE1hcC5wcm90b3R5cGUuc2V0TWFwID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHZhciBzZWxmID0gdGhpcztcclxuICAgICAgICBpZiAodGhpcy5pc3Nob3cgfHwgdGhpcy5pc2hpZGUpIHJldHVybjtcclxuICAgICAgICBpZiAoIXRoaXMubG9hZEFQSSgpKSByZXR1cm47XHJcblxyXG5cclxuICAgICAgICAvL+WIneWni+WMluWcsOWbvlxyXG4gICAgICAgIHZhciBtYXAgPSBzZWxmLm1hcCA9IG5ldyBxcS5tYXBzLk1hcCh0aGlzLm1hcGJveFswXSwge3pvb206IDE1fSk7XHJcbiAgICAgICAgLy/liJ3lp4vljJblnLDlm77mjqfku7ZcclxuICAgICAgICBuZXcgcXEubWFwcy5TY2FsZUNvbnRyb2woe1xyXG4gICAgICAgICAgICBhbGlnbjogcXEubWFwcy5BTElHTi5CT1RUT01fTEVGVCxcclxuICAgICAgICAgICAgbWFyZ2luOiBxcS5tYXBzLlNpemUoODUsIDE1KSxcclxuICAgICAgICAgICAgbWFwOiBtYXBcclxuICAgICAgICB9KTtcclxuICAgICAgICAvL21hcC5hZGRDb250cm9sKG5ldyBCTWFwLk92ZXJ2aWV3TWFwQ29udHJvbCgpKTtcclxuICAgICAgICAvL21hcC5lbmFibGVTY3JvbGxXaGVlbFpvb20oKTtcclxuXHJcbiAgICAgICAgLy/ojrflj5bnu4/nuqzluqblnZDmoIflgLxcclxuICAgICAgICB2YXIgcG9pbnQgPSBuZXcgcXEubWFwcy5MYXRMbmcodGhpcy5sb2NhdGUubGF0LCB0aGlzLmxvY2F0ZS5sbmcpO1xyXG4gICAgICAgIG1hcC5wYW5Ubyhwb2ludCk7IC8v5Yid5aeL5YyW5Zyw5Zu+5Lit5b+D54K5XHJcblxyXG4gICAgICAgIC8v5Yid5aeL5YyW5Zyw5Zu+5qCH6K6wXHJcbiAgICAgICAgdGhpcy5tYXJrZXIgPSBuZXcgcXEubWFwcy5NYXJrZXIoe1xyXG4gICAgICAgICAgICBwb3NpdGlvbjogcG9pbnQsXHJcbiAgICAgICAgICAgIGRyYWdnYWJsZTogdHJ1ZSxcclxuICAgICAgICAgICAgbWFwOiBtYXBcclxuICAgICAgICB9KTtcclxuICAgICAgICB0aGlzLm1hcmtlci5zZXRBbmltYXRpb24ocXEubWFwcy5NYXJrZXJBbmltYXRpb24uRE9XTik7XHJcblxyXG4gICAgICAgIC8v5Zyw5Z2A6Kej5p6Q57G7XHJcbiAgICAgICAgdmFyIGdjID0gbmV3IHFxLm1hcHMuR2VvY29kZXIoe1xyXG4gICAgICAgICAgICBjb21wbGV0ZTogZnVuY3Rpb24gKHJzKSB7XHJcbiAgICAgICAgICAgICAgICBzZWxmLnNob3dMb2NhdGlvbkluZm8ocG9pbnQsIHJzKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICBxcS5tYXBzLmV2ZW50LmFkZExpc3RlbmVyKHRoaXMubWFya2VyLCAnY2xpY2snLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIHBvaW50ID0gc2VsZi5tYXJrZXIuZ2V0UG9zaXRpb24oKTtcclxuICAgICAgICAgICAgZ2MuZ2V0QWRkcmVzcyhwb2ludCk7XHJcbiAgICAgICAgfSk7XHJcbiAgICAgICAgLy/orr7nva5NYXJrZXLlgZzmraLmi5bliqjkuovku7ZcclxuICAgICAgICBxcS5tYXBzLmV2ZW50LmFkZExpc3RlbmVyKHRoaXMubWFya2VyLCAnZHJhZ2VuZCcsIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgcG9pbnQgPSBzZWxmLm1hcmtlci5nZXRQb3NpdGlvbigpO1xyXG4gICAgICAgICAgICBnYy5nZXRBZGRyZXNzKHBvaW50KTtcclxuICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgZ2MuZ2V0QWRkcmVzcyhwb2ludCk7XHJcblxyXG4gICAgICAgIHRoaXMuYmluZEV2ZW50cygpO1xyXG5cclxuICAgICAgICB0aGlzLmluZm9XaW5kb3cgPSBuZXcgcXEubWFwcy5JbmZvV2luZG93KHttYXA6IG1hcH0pO1xyXG5cclxuICAgICAgICB0aGlzLmlzc2hvdyA9IHRydWU7XHJcbiAgICAgICAgaWYgKHRoaXMudG9zaG93KSB7XHJcbiAgICAgICAgICAgIHRoaXMuc2hvd0luZm8oKTtcclxuICAgICAgICAgICAgdGhpcy50b3Nob3cgPSBmYWxzZTtcclxuICAgICAgICB9XHJcbiAgICB9O1xyXG5cclxuICAgIFRlbmNlbnRNYXAucHJvdG90eXBlLnNob3dJbmZvID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIGlmICh0aGlzLmlzaGlkZSkgcmV0dXJuO1xyXG4gICAgICAgIGlmICghdGhpcy5pc3Nob3cpIHtcclxuICAgICAgICAgICAgdGhpcy50b3Nob3cgPSB0cnVlO1xyXG4gICAgICAgICAgICByZXR1cm47XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHRoaXMuaW5mb1dpbmRvdy5vcGVuKCk7XHJcbiAgICAgICAgdGhpcy5zZXRJbmZvQ29udGVudCgpO1xyXG4gICAgICAgIHRoaXMuaW5mb1dpbmRvdy5zZXRQb3NpdGlvbih0aGlzLm1hcmtlci5nZXRQb3NpdGlvbigpKTtcclxuICAgIH07XHJcblxyXG4gICAgVGVuY2VudE1hcC5wcm90b3R5cGUuZ2V0QWRkcmVzcyA9IGZ1bmN0aW9uIChycykge1xyXG4gICAgICAgIGlmKHJzICYmIHJzLmRldGFpbCkge1xyXG4gICAgICAgICAgICByZXR1cm4gcnMuZGV0YWlsLmFkZHJlc3M7XHJcbiAgICAgICAgfVxyXG4gICAgfTtcclxuXHJcbiAgICBUZW5jZW50TWFwLnByb3RvdHlwZS5zZXRMb2NhdGUgPSBmdW5jdGlvbiAoYWRkcmVzcykge1xyXG4gICAgICAgIC8vIOWIm+W7uuWcsOWdgOino+aekOWZqOWunuS+i1xyXG4gICAgICAgIHZhciBzZWxmID0gdGhpcztcclxuICAgICAgICB2YXIgbXlHZW8gPSBuZXcgcXEubWFwcy5HZW9jb2Rlcih7XHJcbiAgICAgICAgICAgIGNvbXBsZXRlOiBmdW5jdGlvbiAocmVzdWx0KSB7XHJcbiAgICAgICAgICAgICAgICBpZihyZXN1bHQgJiYgcmVzdWx0LmRldGFpbCAmJiByZXN1bHQuZGV0YWlsLmxvY2F0aW9uKXtcclxuICAgICAgICAgICAgICAgICAgICB2YXIgcG9pbnQ9cmVzdWx0LmRldGFpbC5sb2NhdGlvbjtcclxuICAgICAgICAgICAgICAgICAgICBzZWxmLm1hcC5zZXRDZW50ZXIocG9pbnQpO1xyXG4gICAgICAgICAgICAgICAgICAgIHNlbGYubWFya2VyLnNldFBvc2l0aW9uKHBvaW50KTtcclxuICAgICAgICAgICAgICAgICAgICBzZWxmLnNob3dMb2NhdGlvbkluZm8ocG9pbnQsIHJlc3VsdCk7XHJcbiAgICAgICAgICAgICAgICB9ZWxzZXtcclxuICAgICAgICAgICAgICAgICAgICB0b2FzdHIud2FybmluZyhcIuWcsOWdgOS/oeaBr+S4jeato+ehru+8jOWumuS9jeWksei0pVwiKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgZXJyb3I6ZnVuY3Rpb24ocmVzdWx0KXtcclxuICAgICAgICAgICAgICAgIHRvYXN0ci53YXJuaW5nKFwi5Zyw5Z2A5L+h5oGv5LiN5q2j56Gu77yM5a6a5L2N5aSx6LSlXCIpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSk7XHJcbiAgICAgICAgbXlHZW8uZ2V0TG9jYXRpb24oYWRkcmVzcyk7XHJcbiAgICB9O1xyXG5cclxuXHJcbiAgICBmdW5jdGlvbiBHYW9kZU1hcCgpIHtcclxuICAgICAgICBCYXNlTWFwLmNhbGwodGhpcywgXCJnYW9kZVwiKTtcclxuICAgICAgICB0aGlzLmluZm9PcHRzID0ge1xyXG4gICAgICAgICAgICB3aWR0aDogMjUwLCAgICAgLy/kv6Hmga/nqpflj6Plrr3luqZcclxuICAgICAgICAgICAgLy8gICBoZWlnaHQ6IDEwMCwgICAgIC8v5L+h5oGv56qX5Y+j6auY5bqmXHJcbiAgICAgICAgICAgIHRpdGxlOiBcIlwiICAvL+S/oeaBr+eql+WPo+agh+mimFxyXG4gICAgICAgIH07XHJcbiAgICB9XHJcblxyXG4gICAgR2FvZGVNYXAucHJvdG90eXBlID0gbmV3IEJhc2VNYXAoKTtcclxuICAgIEdhb2RlTWFwLnByb3RvdHlwZS5jb25zdHJ1Y3RvciA9IEdhb2RlTWFwO1xyXG4gICAgR2FvZGVNYXAucHJvdG90eXBlLmlzQVBJUmVhZHkgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgcmV0dXJuICEhd2luZG93WydBTWFwJ11cclxuICAgIH07XHJcblxyXG4gICAgR2FvZGVNYXAucHJvdG90eXBlLnNldE1hcCA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICB2YXIgc2VsZiA9IHRoaXM7XHJcbiAgICAgICAgaWYgKHRoaXMuaXNzaG93IHx8IHRoaXMuaXNoaWRlKSByZXR1cm47XHJcbiAgICAgICAgaWYgKCF0aGlzLmxvYWRBUEkoKSkgcmV0dXJuO1xyXG5cclxuXHJcbiAgICAgICAgdmFyIG1hcCA9IHNlbGYubWFwID0gbmV3IEFNYXAuTWFwKHRoaXMubWFwYm94LmF0dHIoJ2lkJyksIHtcclxuICAgICAgICAgICAgcmVzaXplRW5hYmxlOiB0cnVlLFxyXG4gICAgICAgICAgICBkcmFnRW5hYmxlOiB0cnVlLFxyXG4gICAgICAgICAgICB6b29tOiAxM1xyXG4gICAgICAgIH0pO1xyXG4gICAgICAgIG1hcC5wbHVnaW4oW1wiQU1hcC5Ub29sQmFyXCIsIFwiQU1hcC5TY2FsZVwiLCBcIkFNYXAuT3ZlclZpZXdcIl0sIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgbWFwLmFkZENvbnRyb2wobmV3IEFNYXAuVG9vbEJhcigpKTtcclxuICAgICAgICAgICAgbWFwLmFkZENvbnRyb2wobmV3IEFNYXAuU2NhbGUoKSk7XHJcbiAgICAgICAgICAgIG1hcC5hZGRDb250cm9sKG5ldyBBTWFwLk92ZXJWaWV3KCkpO1xyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICAkKCdbbmFtZT10eHRMYW5nXScpLnVuYmluZCgpLm9uKCdjaGFuZ2UnLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIHZhciBsYW5nID0gJCh0aGlzKS52YWwoKTtcclxuICAgICAgICAgICAgaWYgKGxhbmcpIG1hcC5zZXRMYW5nKGxhbmcpO1xyXG4gICAgICAgIH0pLnRyaWdnZXIoJ2NoYW5nZScpO1xyXG5cclxuXHJcbiAgICAgICAgLy/ojrflj5bnu4/nuqzluqblnZDmoIflgLxcclxuICAgICAgICB2YXIgcG9pbnQgPSBuZXcgQU1hcC5MbmdMYXQodGhpcy5sb2NhdGUubG5nLCB0aGlzLmxvY2F0ZS5sYXQpO1xyXG4gICAgICAgIG1hcC5zZXRDZW50ZXIocG9pbnQpO1xyXG5cclxuICAgICAgICB0aGlzLm1hcmtlciA9IG5ldyBBTWFwLk1hcmtlcih7cG9zaXRpb246IHBvaW50LCBtYXA6IG1hcH0pOyAvL+WIneWni+WMluWcsOWbvuagh+iusFxyXG4gICAgICAgIHRoaXMubWFya2VyLnNldERyYWdnYWJsZSh0cnVlKTsgLy/moIforrDlvIDlkK/mi5bmi71cclxuXHJcblxyXG4gICAgICAgIHRoaXMuaW5mb1dpbmRvdyA9IG5ldyBBTWFwLkluZm9XaW5kb3coKTtcclxuICAgICAgICB0aGlzLmluZm9XaW5kb3cub3BlbihtYXAsIHBvaW50KTtcclxuXHJcbiAgICAgICAgbWFwLnBsdWdpbihbXCJBTWFwLkdlb2NvZGVyXCJdLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIHZhciBnYyA9IG5ldyBBTWFwLkdlb2NvZGVyKCk7IC8v5Zyw5Z2A6Kej5p6Q57G7XHJcbiAgICAgICAgICAgIC8v5re75Yqg5qCH6K6w5ouW5ou955uR5ZCsXHJcbiAgICAgICAgICAgIHNlbGYubWFya2VyLm9uKFwiZHJhZ2VuZFwiLCBmdW5jdGlvbiAoZSkge1xyXG4gICAgICAgICAgICAgICAgLy/ojrflj5blnLDlnYDkv6Hmga9cclxuICAgICAgICAgICAgICAgIGdjLmdldEFkZHJlc3MoZS5sbmdsYXQsIGZ1bmN0aW9uIChzdCwgcnMpIHtcclxuICAgICAgICAgICAgICAgICAgICBzZWxmLnNob3dMb2NhdGlvbkluZm8oZS5sbmdsYXQsIHJzKTtcclxuICAgICAgICAgICAgICAgIH0pO1xyXG4gICAgICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgICAgIC8v5re75Yqg5qCH6K6w54K55Ye755uR5ZCsXHJcbiAgICAgICAgICAgIHNlbGYubWFya2VyLm9uKFwiY2xpY2tcIiwgZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICAgICAgICAgIGdjLmdldEFkZHJlc3MoZS5sbmdsYXQsIGZ1bmN0aW9uIChzdCwgcnMpIHtcclxuICAgICAgICAgICAgICAgICAgICBzZWxmLnNob3dMb2NhdGlvbkluZm8oZS5sbmdsYXQsIHJzKTtcclxuICAgICAgICAgICAgICAgIH0pO1xyXG4gICAgICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgICAgIGdjLmdldEFkZHJlc3MocG9pbnQsIGZ1bmN0aW9uIChzdCwgcnMpIHtcclxuICAgICAgICAgICAgICAgIHNlbGYuc2hvd0xvY2F0aW9uSW5mbyhwb2ludCwgcnMpO1xyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgdGhpcy5iaW5kRXZlbnRzKCk7XHJcblxyXG4gICAgICAgIHRoaXMuaXNzaG93ID0gdHJ1ZTtcclxuICAgICAgICBpZiAodGhpcy50b3Nob3cpIHtcclxuICAgICAgICAgICAgdGhpcy5zaG93SW5mbygpO1xyXG4gICAgICAgICAgICB0aGlzLnRvc2hvdyA9IGZhbHNlO1xyXG4gICAgICAgIH1cclxuICAgIH07XHJcblxyXG4gICAgR2FvZGVNYXAucHJvdG90eXBlLnNob3dJbmZvID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIGlmICh0aGlzLmlzaGlkZSkgcmV0dXJuO1xyXG4gICAgICAgIGlmICghdGhpcy5pc3Nob3cpIHtcclxuICAgICAgICAgICAgdGhpcy50b3Nob3cgPSB0cnVlO1xyXG4gICAgICAgICAgICByZXR1cm47XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHRoaXMuc2V0SW5mb0NvbnRlbnQoKTtcclxuICAgICAgICB0aGlzLmluZm9XaW5kb3cuc2V0UG9zaXRpb24odGhpcy5tYXJrZXIuZ2V0UG9zaXRpb24oKSk7XHJcbiAgICB9O1xyXG5cclxuICAgIEdhb2RlTWFwLnByb3RvdHlwZS5nZXRBZGRyZXNzID0gZnVuY3Rpb24gKHJzKSB7XHJcbiAgICAgICAgcmV0dXJuIHJzLnJlZ2VvY29kZS5mb3JtYXR0ZWRBZGRyZXNzO1xyXG4gICAgfTtcclxuXHJcbiAgICBHYW9kZU1hcC5wcm90b3R5cGUuc2V0TG9jYXRlID0gZnVuY3Rpb24gKGFkZHJlc3MpIHtcclxuICAgICAgICAvLyDliJvlu7rlnLDlnYDop6PmnpDlmajlrp7kvotcclxuICAgICAgICB2YXIgbXlHZW8gPSBuZXcgQU1hcC5HZW9jb2RlcigpO1xyXG4gICAgICAgIHZhciBzZWxmID0gdGhpcztcclxuICAgICAgICBteUdlby5nZXRQb2ludChhZGRyZXNzLCBmdW5jdGlvbiAocG9pbnQpIHtcclxuICAgICAgICAgICAgaWYgKHBvaW50KSB7XHJcbiAgICAgICAgICAgICAgICBzZWxmLm1hcC5jZW50ZXJBbmRab29tKHBvaW50LCAxMSk7XHJcbiAgICAgICAgICAgICAgICBzZWxmLm1hcmtlci5zZXRQb3NpdGlvbihwb2ludCk7XHJcbiAgICAgICAgICAgICAgICBteUdlby5nZXRMb2NhdGlvbihwb2ludCwgZnVuY3Rpb24gKHJzKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKHBvaW50LCBycyk7XHJcbiAgICAgICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgIHRvYXN0ci53YXJuaW5nKFwi5Zyw5Z2A5L+h5oGv5LiN5q2j56Gu77yM5a6a5L2N5aSx6LSlXCIpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSwgJycpO1xyXG4gICAgfTtcclxuXHJcbiAgICB3aW5kb3cuSW5pdE1hcD1Jbml0TWFwO1xyXG59KSh3aW5kb3csalF1ZXJ5KTsiLCJqUXVlcnkoZnVuY3Rpb24gKCQpIHtcclxuICAgIC8v6auY5Lqu5b2T5YmN6YCJ5Lit55qE5a+86IiqXHJcbiAgICB2YXIgYnJlYWQgPSAkKFwiLmJyZWFkY3J1bWJcIik7XHJcbiAgICB2YXIgbWVudSA9IGJyZWFkLmRhdGEoJ21lbnUnKTtcclxuICAgIGlmIChtZW51KSB7XHJcbiAgICAgICAgdmFyIGxpbmsgPSAkKCcuc2lkZS1uYXYgYVtkYXRhLWtleT0nICsgbWVudSArICddJyk7XHJcblxyXG4gICAgICAgIHZhciBodG1sID0gW107XHJcbiAgICAgICAgaWYgKGxpbmsubGVuZ3RoID4gMCkge1xyXG4gICAgICAgICAgICBpZiAobGluay5pcygnLm1lbnVfdG9wJykpIHtcclxuICAgICAgICAgICAgICAgIGh0bWwucHVzaCgnPGxpIGNsYXNzPVwiYnJlYWRjcnVtYi1pdGVtXCI+PGEgaHJlZj1cImphdmFzY3JpcHQ6XCI+PGkgY2xhc3M9XCInICsgbGluay5maW5kKCdpJykuYXR0cignY2xhc3MnKSArICdcIj48L2k+Jm5ic3A7JyArIGxpbmsudGV4dCgpICsgJzwvYT48L2xpPicpO1xyXG4gICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgdmFyIHBhcmVudCA9IGxpbmsucGFyZW50cygnLmNvbGxhcHNlJykuZXEoMCk7XHJcbiAgICAgICAgICAgICAgICBwYXJlbnQuYWRkQ2xhc3MoJ3Nob3cnKTtcclxuICAgICAgICAgICAgICAgIGxpbmsuYWRkQ2xhc3MoXCJhY3RpdmVcIik7XHJcbiAgICAgICAgICAgICAgICB2YXIgdG9wbWVudSA9IHBhcmVudC5zaWJsaW5ncygnLmNhcmQtaGVhZGVyJykuZmluZCgnYS5tZW51X3RvcCcpO1xyXG4gICAgICAgICAgICAgICAgaHRtbC5wdXNoKCc8bGkgY2xhc3M9XCJicmVhZGNydW1iLWl0ZW1cIj48YSBocmVmPVwiamF2YXNjcmlwdDpcIj48aSBjbGFzcz1cIicgKyB0b3BtZW51LmZpbmQoJ2knKS5hdHRyKCdjbGFzcycpICsgJ1wiPjwvaT4mbmJzcDsnICsgdG9wbWVudS50ZXh0KCkgKyAnPC9hPjwvbGk+Jyk7XHJcbiAgICAgICAgICAgICAgICBodG1sLnB1c2goJzxsaSBjbGFzcz1cImJyZWFkY3J1bWItaXRlbVwiPjxhIGhyZWY9XCJqYXZhc2NyaXB0OlwiPicgKyBsaW5rLnRleHQoKSArICc8L2E+PC9saT4nKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH1cclxuICAgICAgICB2YXIgdGl0bGUgPSBicmVhZC5kYXRhKCd0aXRsZScpO1xyXG4gICAgICAgIGlmICh0aXRsZSkge1xyXG4gICAgICAgICAgICBodG1sLnB1c2goJzxsaSBjbGFzcz1cImJyZWFkY3J1bWItaXRlbSBhY3RpdmVcIiBhcmlhLWN1cnJlbnQ9XCJwYWdlXCI+JyArIHRpdGxlICsgJzwvbGk+Jyk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIGJyZWFkLmh0bWwoaHRtbC5qb2luKFwiXFxuXCIpKTtcclxuICAgIH1cclxuXHJcbiAgICAvL+WFqOmAieOAgeWPjemAieaMiemSrlxyXG4gICAgJCgnLmNoZWNrYWxsLWJ0bicpLmNsaWNrKGZ1bmN0aW9uIChlKSB7XHJcbiAgICAgICAgdmFyIHRhcmdldCA9ICQodGhpcykuZGF0YSgndGFyZ2V0Jyk7XHJcbiAgICAgICAgaWYgKCF0YXJnZXQpIHRhcmdldCA9ICdpZCc7XHJcbiAgICAgICAgdmFyIGlkcyA9ICQoJ1tuYW1lPScgKyB0YXJnZXQgKyAnXScpO1xyXG4gICAgICAgIGlmICgkKHRoaXMpLmlzKCcuYWN0aXZlJykpIHtcclxuICAgICAgICAgICAgaWRzLnJlbW92ZUF0dHIoJ2NoZWNrZWQnKTtcclxuICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICBpZHMuYXR0cignY2hlY2tlZCcsIHRydWUpO1xyXG4gICAgICAgIH1cclxuICAgIH0pO1xyXG4gICAgJCgnLmNoZWNrcmV2ZXJzZS1idG4nKS5jbGljayhmdW5jdGlvbiAoZSkge1xyXG4gICAgICAgIHZhciB0YXJnZXQgPSAkKHRoaXMpLmRhdGEoJ3RhcmdldCcpO1xyXG4gICAgICAgIGlmICghdGFyZ2V0KSB0YXJnZXQgPSAnaWQnO1xyXG4gICAgICAgIHZhciBpZHMgPSAkKCdbbmFtZT0nICsgdGFyZ2V0ICsgJ10nKTtcclxuICAgICAgICBmb3IgKHZhciBpID0gMDsgaSA8IGlkcy5sZW5ndGg7IGkrKykge1xyXG4gICAgICAgICAgICBpZiAoaWRzW2ldLmNoZWNrZWQpIHtcclxuICAgICAgICAgICAgICAgIGlkcy5lcShpKS5yZW1vdmVBdHRyKCdjaGVja2VkJyk7XHJcbiAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICBpZHMuZXEoaSkuYXR0cignY2hlY2tlZCcsIHRydWUpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfVxyXG4gICAgfSk7XHJcbiAgICAvL+aTjeS9nOaMiemSrlxyXG4gICAgJCgnLmFjdGlvbi1idG4nKS5jbGljayhmdW5jdGlvbiAoZSkge1xyXG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcclxuICAgICAgICB2YXIgYWN0aW9uID0gJCh0aGlzKS5kYXRhKCdhY3Rpb24nKTtcclxuICAgICAgICBpZiAoIWFjdGlvbikge1xyXG4gICAgICAgICAgICByZXR1cm4gdG9hc3RyLmVycm9yKCfmnKrnn6Xmk43kvZwnKTtcclxuICAgICAgICB9XHJcbiAgICAgICAgYWN0aW9uID0gJ2FjdGlvbicgKyBhY3Rpb24ucmVwbGFjZSgvXlthLXpdLywgZnVuY3Rpb24gKGxldHRlcikge1xyXG4gICAgICAgICAgICByZXR1cm4gbGV0dGVyLnRvVXBwZXJDYXNlKCk7XHJcbiAgICAgICAgfSk7XHJcbiAgICAgICAgaWYgKCF3aW5kb3dbYWN0aW9uXSB8fCB0eXBlb2Ygd2luZG93W2FjdGlvbl0gIT09ICdmdW5jdGlvbicpIHtcclxuICAgICAgICAgICAgcmV0dXJuIHRvYXN0ci5lcnJvcign5pyq55+l5pON5L2cJyk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHZhciBuZWVkQ2hlY2tzID0gJCh0aGlzKS5kYXRhKCduZWVkQ2hlY2tzJyk7XHJcbiAgICAgICAgaWYgKG5lZWRDaGVja3MgPT09IHVuZGVmaW5lZCkgbmVlZENoZWNrcyA9IHRydWU7XHJcbiAgICAgICAgaWYgKG5lZWRDaGVja3MpIHtcclxuICAgICAgICAgICAgdmFyIHRhcmdldCA9ICQodGhpcykuZGF0YSgndGFyZ2V0Jyk7XHJcbiAgICAgICAgICAgIGlmICghdGFyZ2V0KSB0YXJnZXQgPSAnaWQnO1xyXG4gICAgICAgICAgICB2YXIgaWRzID0gJCgnW25hbWU9JyArIHRhcmdldCArICddOmNoZWNrZWQnKTtcclxuICAgICAgICAgICAgaWYgKGlkcy5sZW5ndGggPCAxKSB7XHJcbiAgICAgICAgICAgICAgICByZXR1cm4gdG9hc3RyLndhcm5pbmcoJ+ivt+mAieaLqemcgOimgeaTjeS9nOeahOmhueebricpO1xyXG4gICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgdmFyIGlkY2hlY2tzID0gW107XHJcbiAgICAgICAgICAgICAgICBmb3IgKHZhciBpID0gMDsgaSA8IGlkcy5sZW5ndGg7IGkrKykge1xyXG4gICAgICAgICAgICAgICAgICAgIGlkY2hlY2tzLnB1c2goaWRzLmVxKGkpLnZhbCgpKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIHdpbmRvd1thY3Rpb25dKGlkY2hlY2tzKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgIHdpbmRvd1thY3Rpb25dKCk7XHJcbiAgICAgICAgfVxyXG4gICAgfSk7XHJcblxyXG4gICAgLy/lvILmraXmmL7npLrotYTmlpnpk77mjqVcclxuICAgICQoJ2FbcmVsPWFqYXhdJykuY2xpY2soZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XHJcbiAgICAgICAgdmFyIHNlbGYgPSAkKHRoaXMpO1xyXG4gICAgICAgIHZhciB0aXRsZSA9ICQodGhpcykuZGF0YSgndGl0bGUnKTtcclxuICAgICAgICBpZiAoIXRpdGxlKSB0aXRsZSA9ICQodGhpcykudGV4dCgpO1xyXG4gICAgICAgIHZhciBkbGcgPSBuZXcgRGlhbG9nKHtcclxuICAgICAgICAgICAgYnRuczogWyfnoa7lrponXSxcclxuICAgICAgICAgICAgb25zaG93OiBmdW5jdGlvbiAoYm9keSkge1xyXG4gICAgICAgICAgICAgICAgJC5hamF4KHtcclxuICAgICAgICAgICAgICAgICAgICB1cmw6IHNlbGYuYXR0cignaHJlZicpLFxyXG4gICAgICAgICAgICAgICAgICAgIHN1Y2Nlc3M6IGZ1bmN0aW9uICh0ZXh0KSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGJvZHkuaHRtbCh0ZXh0KTtcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLnNob3coJzxwIGNsYXNzPVwibG9hZGluZ1wiPuWKoOi9veS4rS4uLjwvcD4nLCB0aXRsZSk7XHJcblxyXG4gICAgfSk7XHJcblxyXG4gICAgJCgnLm5hdi10YWJzIGEnKS5jbGljayhmdW5jdGlvbiAoZSkge1xyXG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcclxuICAgICAgICAkKHRoaXMpLnRhYignc2hvdycpO1xyXG4gICAgfSk7XHJcblxyXG4gICAgLy/kuIrkvKDmoYZcclxuICAgICQoJy5jdXN0b20tZmlsZSAuY3VzdG9tLWZpbGUtaW5wdXQnKS5vbignY2hhbmdlJywgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHZhciBsYWJlbCA9ICQodGhpcykucGFyZW50cygnLmN1c3RvbS1maWxlJykuZmluZCgnLmN1c3RvbS1maWxlLWxhYmVsJyk7XHJcbiAgICAgICAgbGFiZWwudGV4dCgkKHRoaXMpLnZhbCgpKTtcclxuICAgIH0pO1xyXG5cclxuICAgIC8v6KGo5Y2VQWpheOaPkOS6pFxyXG4gICAgJCgnLmJ0bi1wcmltYXJ5W3R5cGU9c3VibWl0XScpLmNsaWNrKGZ1bmN0aW9uIChlKSB7XHJcbiAgICAgICAgdmFyIGZvcm0gPSAkKHRoaXMpLnBhcmVudHMoJ2Zvcm0nKTtcclxuICAgICAgICB2YXIgYnRuID0gdGhpcztcclxuICAgICAgICB2YXIgb3B0aW9ucyA9IHtcclxuICAgICAgICAgICAgdXJsOiAkKGZvcm0pLmF0dHIoJ2FjdGlvbicpLFxyXG4gICAgICAgICAgICB0eXBlOiAnUE9TVCcsXHJcbiAgICAgICAgICAgIGRhdGFUeXBlOiAnSlNPTicsXHJcbiAgICAgICAgICAgIHN1Y2Nlc3M6IGZ1bmN0aW9uIChqc29uKSB7XHJcbiAgICAgICAgICAgICAgICBpZiAoanNvbi5jb2RlID09IDEpIHtcclxuICAgICAgICAgICAgICAgICAgICBuZXcgRGlhbG9nKHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgb25oaWRkZW46IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmIChqc29uLnVybCkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxvY2F0aW9uLmhyZWYgPSBqc29uLnVybDtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbG9jYXRpb24ucmVsb2FkKCk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICB9KS5zaG93KGpzb24ubXNnKTtcclxuICAgICAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICAgICAgdG9hc3RyLndhcm5pbmcoanNvbi5tc2cpO1xyXG4gICAgICAgICAgICAgICAgICAgICQoYnRuKS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfTtcclxuICAgICAgICBpZiAoZm9ybS5hdHRyKCdlbmN0eXBlJykgPT09ICdtdWx0aXBhcnQvZm9ybS1kYXRhJykge1xyXG4gICAgICAgICAgICBpZiAoIUZvcm1EYXRhKSB7XHJcbiAgICAgICAgICAgICAgICByZXR1cm4gdHJ1ZTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICBvcHRpb25zLmRhdGEgPSBuZXcgRm9ybURhdGEoZm9ybVswXSk7XHJcbiAgICAgICAgICAgIG9wdGlvbnMuY2FjaGUgPSBmYWxzZTtcclxuICAgICAgICAgICAgb3B0aW9ucy5wcm9jZXNzRGF0YSA9IGZhbHNlO1xyXG4gICAgICAgICAgICBvcHRpb25zLmNvbnRlbnRUeXBlID0gZmFsc2U7XHJcbiAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgb3B0aW9ucy5kYXRhID0gJChmb3JtKS5zZXJpYWxpemUoKTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcclxuICAgICAgICAkKHRoaXMpLmF0dHIoJ2Rpc2FibGVkJywgdHJ1ZSk7XHJcbiAgICAgICAgJC5hamF4KG9wdGlvbnMpO1xyXG4gICAgfSk7XHJcblxyXG4gICAgJCgnLnBpY2t1c2VyJykuY2xpY2soZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICB2YXIgZ3JvdXAgPSAkKHRoaXMpLnBhcmVudHMoJy5pbnB1dC1ncm91cCcpO1xyXG4gICAgICAgIHZhciBpZGVsZSA9IGdyb3VwLmZpbmQoJ1tuYW1lPW1lbWJlcl9pZF0nKTtcclxuICAgICAgICB2YXIgaW5mb2VsZSA9IGdyb3VwLmZpbmQoJ1tuYW1lPW1lbWJlcl9pbmZvXScpO1xyXG4gICAgICAgIGRpYWxvZy5waWNrVXNlcigkKHRoaXMpLmRhdGEoJ3VybCcpLCBmdW5jdGlvbiAodXNlcikge1xyXG4gICAgICAgICAgICBpZGVsZS52YWwodXNlci5pZCk7XHJcbiAgICAgICAgICAgIGluZm9lbGUudmFsKCdbJyArIHVzZXIuaWQgKyAnXSAnICsgdXNlci51c2VybmFtZSArICh1c2VyLm1vYmlsZSA/ICgnIC8gJyArIHVzZXIubW9iaWxlKSA6ICcnKSk7XHJcbiAgICAgICAgfSwgJCh0aGlzKS5kYXRhKCdmaWx0ZXInKSk7XHJcbiAgICB9KTtcclxuICAgICQoJy5waWNrLWxvY2F0ZScpLmNsaWNrKGZ1bmN0aW9uKGUpe1xyXG4gICAgICAgIHZhciBncm91cD0kKHRoaXMpLnBhcmVudHMoJy5pbnB1dC1ncm91cCcpO1xyXG4gICAgICAgIHZhciBpZGVsZT1ncm91cC5maW5kKCdpbnB1dFt0eXBlPXRleHRdJyk7XHJcbiAgICAgICAgZGlhbG9nLnBpY2tMb2NhdGUoJ3FxJyxmdW5jdGlvbihsb2NhdGUpe1xyXG4gICAgICAgICAgICBpZGVsZS52YWwobG9jYXRlLmxuZysnLCcrbG9jYXRlLmxhdCk7XHJcbiAgICAgICAgfSxpZGVsZS52YWwoKSk7XHJcbiAgICB9KTtcclxufSk7Il19
