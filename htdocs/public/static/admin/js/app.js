function del(obj,msg) {
    dialog.confirm(msg,function(){
        location.href=$(obj).attr('href');
    });
    return false;
}

function randomString(len, charSet) {
    charSet = charSet || 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var str = '',allLen=charSet.length;
    for (var i = 0; i < len; i++) {
        var randomPoz = Math.floor(Math.random() * allLen);
        str += charSet.substring(randomPoz,randomPoz+1);
    }
    return str;
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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNvbW1vbi5qcyIsInRlbXBsYXRlLmpzIiwiZGlhbG9nLmpzIiwianF1ZXJ5LnRhZy5qcyIsImRhdGV0aW1lLmluaXQuanMiLCJtYXAuanMiLCJiYWNrZW5kLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDZkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDOURBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDeFRBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ25DQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQy9EQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQzFqQkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSIsImZpbGUiOiJiYWNrZW5kLmpzIiwic291cmNlc0NvbnRlbnQiOlsiZnVuY3Rpb24gZGVsKG9iaixtc2cpIHtcclxuICAgIGRpYWxvZy5jb25maXJtKG1zZyxmdW5jdGlvbigpe1xyXG4gICAgICAgIGxvY2F0aW9uLmhyZWY9JChvYmopLmF0dHIoJ2hyZWYnKTtcclxuICAgIH0pO1xyXG4gICAgcmV0dXJuIGZhbHNlO1xyXG59XHJcblxyXG5mdW5jdGlvbiByYW5kb21TdHJpbmcobGVuLCBjaGFyU2V0KSB7XHJcbiAgICBjaGFyU2V0ID0gY2hhclNldCB8fCAnQUJDREVGR0hJSktMTU5PUFFSU1RVVldYWVphYmNkZWZnaGlqa2xtbm9wcXJzdHV2d3h5ejAxMjM0NTY3ODknO1xyXG4gICAgdmFyIHN0ciA9ICcnLGFsbExlbj1jaGFyU2V0Lmxlbmd0aDtcclxuICAgIGZvciAodmFyIGkgPSAwOyBpIDwgbGVuOyBpKyspIHtcclxuICAgICAgICB2YXIgcmFuZG9tUG96ID0gTWF0aC5mbG9vcihNYXRoLnJhbmRvbSgpICogYWxsTGVuKTtcclxuICAgICAgICBzdHIgKz0gY2hhclNldC5zdWJzdHJpbmcocmFuZG9tUG96LHJhbmRvbVBveisxKTtcclxuICAgIH1cclxuICAgIHJldHVybiBzdHI7XHJcbn0iLCJcclxuTnVtYmVyLnByb3RvdHlwZS5mb3JtYXQ9ZnVuY3Rpb24oZml4KXtcclxuICAgIGlmKGZpeD09PXVuZGVmaW5lZClmaXg9MjtcclxuICAgIHZhciBudW09dGhpcy50b0ZpeGVkKGZpeCk7XHJcbiAgICB2YXIgej1udW0uc3BsaXQoJy4nKTtcclxuICAgIHZhciBmb3JtYXQ9W10sZj16WzBdLnNwbGl0KCcnKSxsPWYubGVuZ3RoO1xyXG4gICAgZm9yKHZhciBpPTA7aTxsO2krKyl7XHJcbiAgICAgICAgaWYoaT4wICYmIGkgJSAzPT0wKXtcclxuICAgICAgICAgICAgZm9ybWF0LnVuc2hpZnQoJywnKTtcclxuICAgICAgICB9XHJcbiAgICAgICAgZm9ybWF0LnVuc2hpZnQoZltsLWktMV0pO1xyXG4gICAgfVxyXG4gICAgcmV0dXJuIGZvcm1hdC5qb2luKCcnKSsoei5sZW5ndGg9PTI/Jy4nK3pbMV06JycpO1xyXG59O1xyXG5TdHJpbmcucHJvdG90eXBlLmNvbXBpbGU9ZnVuY3Rpb24oZGF0YSxsaXN0KXtcclxuXHJcbiAgICBpZihsaXN0KXtcclxuICAgICAgICB2YXIgdGVtcHM9W107XHJcbiAgICAgICAgZm9yKHZhciBpIGluIGRhdGEpe1xyXG4gICAgICAgICAgICB0ZW1wcy5wdXNoKHRoaXMuY29tcGlsZShkYXRhW2ldKSk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHJldHVybiB0ZW1wcy5qb2luKFwiXFxuXCIpO1xyXG4gICAgfWVsc2V7XHJcbiAgICAgICAgcmV0dXJuIHRoaXMucmVwbGFjZSgvXFx7QChbXFx3XFxkXFwuXSspKD86XFx8KFtcXHdcXGRdKykoPzpcXHMqPVxccyooW1xcd1xcZCxcXHMjXSspKT8pP1xcfS9nLGZ1bmN0aW9uKGFsbCxtMSxmdW5jLGFyZ3Mpe1xyXG5cclxuICAgICAgICAgICAgaWYobTEuaW5kZXhPZignLicpPjApe1xyXG4gICAgICAgICAgICAgICAgdmFyIGtleXM9bTEuc3BsaXQoJy4nKSx2YWw9ZGF0YTtcclxuICAgICAgICAgICAgICAgIGZvcih2YXIgaT0wO2k8a2V5cy5sZW5ndGg7aSsrKXtcclxuICAgICAgICAgICAgICAgICAgICBpZih2YWxba2V5c1tpXV0pe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB2YWw9dmFsW2tleXNbaV1dO1xyXG4gICAgICAgICAgICAgICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB2YWwgPSAnJztcclxuICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgcmV0dXJuIGNhbGxmdW5jKHZhbCxmdW5jLGFyZ3MpO1xyXG4gICAgICAgICAgICB9ZWxzZXtcclxuICAgICAgICAgICAgICAgIHJldHVybiBkYXRhW20xXT9jYWxsZnVuYyhkYXRhW20xXSxmdW5jLGFyZ3MsZGF0YSk6Jyc7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KTtcclxuICAgIH1cclxufTtcclxuXHJcbmZ1bmN0aW9uIGNhbGxmdW5jKHZhbCxmdW5jLGFyZ3MsdGhpc29iail7XHJcbiAgICBpZighYXJncyl7XHJcbiAgICAgICAgYXJncz1bdmFsXTtcclxuICAgIH1lbHNle1xyXG4gICAgICAgIGlmKHR5cGVvZiBhcmdzPT09J3N0cmluZycpYXJncz1hcmdzLnNwbGl0KCcsJyk7XHJcbiAgICAgICAgdmFyIGFyZ2lkeD1hcmdzLmluZGV4T2YoJyMjIycpO1xyXG4gICAgICAgIGlmKGFyZ2lkeD49MCl7XHJcbiAgICAgICAgICAgIGFyZ3NbYXJnaWR4XT12YWw7XHJcbiAgICAgICAgfWVsc2V7XHJcbiAgICAgICAgICAgIGFyZ3M9W3ZhbF0uY29uY2F0KGFyZ3MpO1xyXG4gICAgICAgIH1cclxuICAgIH1cclxuICAgIC8vY29uc29sZS5sb2coYXJncyk7XHJcbiAgICByZXR1cm4gd2luZG93W2Z1bmNdP3dpbmRvd1tmdW5jXS5hcHBseSh0aGlzb2JqLGFyZ3MpOnZhbDtcclxufVxyXG5cclxuZnVuY3Rpb24gaWlmKHYsbTEsbTIpe1xyXG4gICAgaWYodj09PScwJyl2PTA7XHJcbiAgICByZXR1cm4gdj9tMTptMjtcclxufSIsIlxyXG52YXIgZGlhbG9nVHBsPSc8ZGl2IGNsYXNzPVwibW9kYWwgZmFkZVwiIGlkPVwie0BpZH1cIiB0YWJpbmRleD1cIi0xXCIgcm9sZT1cImRpYWxvZ1wiIGFyaWEtbGFiZWxsZWRieT1cIntAaWR9TGFiZWxcIiBhcmlhLWhpZGRlbj1cInRydWVcIj5cXG4nICtcclxuICAgICcgICAgPGRpdiBjbGFzcz1cIm1vZGFsLWRpYWxvZ1wiPlxcbicgK1xyXG4gICAgJyAgICAgICAgPGRpdiBjbGFzcz1cIm1vZGFsLWNvbnRlbnRcIj5cXG4nICtcclxuICAgICcgICAgICAgICAgICA8ZGl2IGNsYXNzPVwibW9kYWwtaGVhZGVyXCI+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgICAgIDxoNCBjbGFzcz1cIm1vZGFsLXRpdGxlXCIgaWQ9XCJ7QGlkfUxhYmVsXCI+PC9oND5cXG4nICtcclxuICAgICcgICAgICAgICAgICAgICAgPGJ1dHRvbiB0eXBlPVwiYnV0dG9uXCIgY2xhc3M9XCJjbG9zZVwiIGRhdGEtZGlzbWlzcz1cIm1vZGFsXCI+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgICAgICAgICA8c3BhbiBhcmlhLWhpZGRlbj1cInRydWVcIj4mdGltZXM7PC9zcGFuPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgICAgICAgICAgPHNwYW4gY2xhc3M9XCJzci1vbmx5XCI+Q2xvc2U8L3NwYW4+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgICAgIDwvYnV0dG9uPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgIDwvZGl2PlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJtb2RhbC1ib2R5XCI+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgPC9kaXY+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgPGRpdiBjbGFzcz1cIm1vZGFsLWZvb3RlclwiPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgICAgICA8bmF2IGNsYXNzPVwibmF2IG5hdi1maWxsXCI+PC9uYXY+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgPC9kaXY+XFxuJyArXHJcbiAgICAnICAgICAgICA8L2Rpdj5cXG4nICtcclxuICAgICcgICAgPC9kaXY+XFxuJyArXHJcbiAgICAnPC9kaXY+JztcclxudmFyIGRpYWxvZ0lkeD0wO1xyXG5mdW5jdGlvbiBEaWFsb2cob3B0cyl7XHJcbiAgICBpZighb3B0cylvcHRzPXt9O1xyXG4gICAgLy/lpITnkIbmjInpkq5cclxuICAgIGlmKG9wdHMuYnRucyE9PXVuZGVmaW5lZCkge1xyXG4gICAgICAgIGlmICh0eXBlb2Yob3B0cy5idG5zKSA9PSAnc3RyaW5nJykge1xyXG4gICAgICAgICAgICBvcHRzLmJ0bnMgPSBbb3B0cy5idG5zXTtcclxuICAgICAgICB9XHJcbiAgICAgICAgdmFyIGRmdD0tMTtcclxuICAgICAgICBmb3IgKHZhciBpID0gMDsgaSA8IG9wdHMuYnRucy5sZW5ndGg7IGkrKykge1xyXG4gICAgICAgICAgICBpZih0eXBlb2Yob3B0cy5idG5zW2ldKT09J3N0cmluZycpe1xyXG4gICAgICAgICAgICAgICAgb3B0cy5idG5zW2ldPXsndGV4dCc6b3B0cy5idG5zW2ldfTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICBpZihvcHRzLmJ0bnNbaV0uaXNkZWZhdWx0KXtcclxuICAgICAgICAgICAgICAgIGRmdD1pO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfVxyXG4gICAgICAgIGlmKGRmdDwwKXtcclxuICAgICAgICAgICAgZGZ0PW9wdHMuYnRucy5sZW5ndGgtMTtcclxuICAgICAgICAgICAgb3B0cy5idG5zW2RmdF0uaXNkZWZhdWx0PXRydWU7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICBpZighb3B0cy5idG5zW2RmdF1bJ3R5cGUnXSl7XHJcbiAgICAgICAgICAgIG9wdHMuYnRuc1tkZnRdWyd0eXBlJ109J3ByaW1hcnknO1xyXG4gICAgICAgIH1cclxuICAgICAgICBvcHRzLmRlZmF1bHRCdG49ZGZ0O1xyXG4gICAgfVxyXG5cclxuICAgIHRoaXMub3B0aW9ucz0kLmV4dGVuZCh7XHJcbiAgICAgICAgJ2lkJzonZGxnTW9kYWwnK2RpYWxvZ0lkeCsrLFxyXG4gICAgICAgICdzaXplJzonJyxcclxuICAgICAgICAnYnRucyc6W1xyXG4gICAgICAgICAgICB7J3RleHQnOiflj5bmtognLCd0eXBlJzonc2Vjb25kYXJ5J30sXHJcbiAgICAgICAgICAgIHsndGV4dCc6J+ehruWumicsJ2lzZGVmYXVsdCc6dHJ1ZSwndHlwZSc6J3ByaW1hcnknfVxyXG4gICAgICAgIF0sXHJcbiAgICAgICAgJ2RlZmF1bHRCdG4nOjEsXHJcbiAgICAgICAgJ29uc3VyZSc6bnVsbCxcclxuICAgICAgICAnb25zaG93JzpudWxsLFxyXG4gICAgICAgICdvbnNob3duJzpudWxsLFxyXG4gICAgICAgICdvbmhpZGUnOm51bGwsXHJcbiAgICAgICAgJ29uaGlkZGVuJzpudWxsXHJcbiAgICB9LG9wdHMpO1xyXG5cclxuICAgIHRoaXMuYm94PSQodGhpcy5vcHRpb25zLmlkKTtcclxufVxyXG5EaWFsb2cucHJvdG90eXBlLmdlbmVyQnRuPWZ1bmN0aW9uKG9wdCxpZHgpe1xyXG4gICAgaWYob3B0Wyd0eXBlJ10pb3B0WydjbGFzcyddPSdidG4tb3V0bGluZS0nK29wdFsndHlwZSddO1xyXG4gICAgcmV0dXJuICc8YSBocmVmPVwiamF2YXNjcmlwdDpcIiBjbGFzcz1cIm5hdi1pdGVtIGJ0biAnKyhvcHRbJ2NsYXNzJ10/b3B0WydjbGFzcyddOididG4tb3V0bGluZS1zZWNvbmRhcnknKSsnXCIgZGF0YS1pbmRleD1cIicraWR4KydcIj4nK29wdC50ZXh0Kyc8L2E+JztcclxufTtcclxuRGlhbG9nLnByb3RvdHlwZS5zaG93PWZ1bmN0aW9uKGh0bWwsdGl0bGUpe1xyXG4gICAgdGhpcy5ib3g9JCgnIycrdGhpcy5vcHRpb25zLmlkKTtcclxuICAgIGlmKCF0aXRsZSl0aXRsZT0n57O757uf5o+Q56S6JztcclxuICAgIGlmKHRoaXMuYm94Lmxlbmd0aDwxKSB7XHJcbiAgICAgICAgJChkb2N1bWVudC5ib2R5KS5hcHBlbmQoZGlhbG9nVHBsLmNvbXBpbGUoeydpZCc6IHRoaXMub3B0aW9ucy5pZH0pKTtcclxuICAgICAgICB0aGlzLmJveD0kKCcjJyt0aGlzLm9wdGlvbnMuaWQpO1xyXG4gICAgfWVsc2V7XHJcbiAgICAgICAgdGhpcy5ib3gudW5iaW5kKCk7XHJcbiAgICB9XHJcblxyXG4gICAgLy90aGlzLmJveC5maW5kKCcubW9kYWwtZm9vdGVyIC5idG4tcHJpbWFyeScpLnVuYmluZCgpO1xyXG4gICAgdmFyIHNlbGY9dGhpcztcclxuICAgIERpYWxvZy5pbnN0YW5jZT1zZWxmO1xyXG5cclxuICAgIC8v55Sf5oiQ5oyJ6ZKuXHJcbiAgICB2YXIgYnRucz1bXTtcclxuICAgIGZvcih2YXIgaT0wO2k8dGhpcy5vcHRpb25zLmJ0bnMubGVuZ3RoO2krKyl7XHJcbiAgICAgICAgYnRucy5wdXNoKHRoaXMuZ2VuZXJCdG4odGhpcy5vcHRpb25zLmJ0bnNbaV0saSkpO1xyXG4gICAgfVxyXG4gICAgdGhpcy5ib3guZmluZCgnLm1vZGFsLWZvb3RlciAubmF2JykuaHRtbChidG5zLmpvaW4oJ1xcbicpKTtcclxuXHJcbiAgICB2YXIgZGlhbG9nPXRoaXMuYm94LmZpbmQoJy5tb2RhbC1kaWFsb2cnKTtcclxuICAgIGRpYWxvZy5yZW1vdmVDbGFzcygnbW9kYWwtc20nKS5yZW1vdmVDbGFzcygnbW9kYWwtbGcnKTtcclxuICAgIGlmKHRoaXMub3B0aW9ucy5zaXplPT0nc20nKSB7XHJcbiAgICAgICAgZGlhbG9nLmFkZENsYXNzKCdtb2RhbC1zbScpO1xyXG4gICAgfWVsc2UgaWYodGhpcy5vcHRpb25zLnNpemU9PSdsZycpIHtcclxuICAgICAgICBkaWFsb2cuYWRkQ2xhc3MoJ21vZGFsLWxnJyk7XHJcbiAgICB9XHJcbiAgICB0aGlzLmJveC5maW5kKCcubW9kYWwtdGl0bGUnKS50ZXh0KHRpdGxlKTtcclxuXHJcbiAgICB2YXIgYm9keT10aGlzLmJveC5maW5kKCcubW9kYWwtYm9keScpO1xyXG4gICAgYm9keS5odG1sKGh0bWwpO1xyXG4gICAgdGhpcy5ib3gub24oJ2hpZGUuYnMubW9kYWwnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgaWYoc2VsZi5vcHRpb25zLm9uaGlkZSl7XHJcbiAgICAgICAgICAgIHNlbGYub3B0aW9ucy5vbmhpZGUoYm9keSxzZWxmLmJveCk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIERpYWxvZy5pbnN0YW5jZT1udWxsO1xyXG4gICAgfSk7XHJcbiAgICB0aGlzLmJveC5vbignaGlkZGVuLmJzLm1vZGFsJyxmdW5jdGlvbigpe1xyXG4gICAgICAgIGlmKHNlbGYub3B0aW9ucy5vbmhpZGRlbil7XHJcbiAgICAgICAgICAgIHNlbGYub3B0aW9ucy5vbmhpZGRlbihib2R5LHNlbGYuYm94KTtcclxuICAgICAgICB9XHJcbiAgICAgICAgc2VsZi5ib3gucmVtb3ZlKCk7XHJcbiAgICB9KTtcclxuICAgIHRoaXMuYm94Lm9uKCdzaG93LmJzLm1vZGFsJyxmdW5jdGlvbigpe1xyXG4gICAgICAgIGlmKHNlbGYub3B0aW9ucy5vbnNob3cpe1xyXG4gICAgICAgICAgICBzZWxmLm9wdGlvbnMub25zaG93KGJvZHksc2VsZi5ib3gpO1xyXG4gICAgICAgIH1cclxuICAgIH0pO1xyXG4gICAgdGhpcy5ib3gub24oJ3Nob3duLmJzLm1vZGFsJyxmdW5jdGlvbigpe1xyXG4gICAgICAgIGlmKHNlbGYub3B0aW9ucy5vbnNob3duKXtcclxuICAgICAgICAgICAgc2VsZi5vcHRpb25zLm9uc2hvd24oYm9keSxzZWxmLmJveCk7XHJcbiAgICAgICAgfVxyXG4gICAgfSk7XHJcbiAgICB0aGlzLmJveC5maW5kKCcubW9kYWwtZm9vdGVyIC5idG4nKS5jbGljayhmdW5jdGlvbigpe1xyXG4gICAgICAgIHZhciByZXN1bHQ9dHJ1ZSxpZHg9JCh0aGlzKS5kYXRhKCdpbmRleCcpO1xyXG4gICAgICAgIGlmKHNlbGYub3B0aW9ucy5idG5zW2lkeF0uY2xpY2spe1xyXG4gICAgICAgICAgICByZXN1bHQgPSBzZWxmLm9wdGlvbnMuYnRuc1tpZHhdLmNsaWNrLmFwcGx5KHRoaXMsW2JvZHksIHNlbGYuYm94XSk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIGlmKGlkeD09c2VsZi5vcHRpb25zLmRlZmF1bHRCdG4pIHtcclxuICAgICAgICAgICAgaWYgKHNlbGYub3B0aW9ucy5vbnN1cmUpIHtcclxuICAgICAgICAgICAgICAgIHJlc3VsdCA9IHNlbGYub3B0aW9ucy5vbnN1cmUuYXBwbHkodGhpcyxbYm9keSwgc2VsZi5ib3hdKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH1cclxuICAgICAgICBpZihyZXN1bHQhPT1mYWxzZSl7XHJcbiAgICAgICAgICAgIHNlbGYuYm94Lm1vZGFsKCdoaWRlJyk7XHJcbiAgICAgICAgfVxyXG4gICAgfSk7XHJcbiAgICB0aGlzLmJveC5tb2RhbCgnc2hvdycpO1xyXG4gICAgcmV0dXJuIHRoaXM7XHJcbn07XHJcbkRpYWxvZy5wcm90b3R5cGUuaGlkZT1mdW5jdGlvbigpe1xyXG4gICAgdGhpcy5ib3gubW9kYWwoJ2hpZGUnKTtcclxuICAgIHJldHVybiB0aGlzO1xyXG59O1xyXG5cclxudmFyIGRpYWxvZz17XHJcbiAgICBhbGVydDpmdW5jdGlvbihtZXNzYWdlLGNhbGxiYWNrLHRpdGxlKXtcclxuICAgICAgICB2YXIgY2FsbGVkPWZhbHNlO1xyXG4gICAgICAgIHZhciBpc2NhbGxiYWNrPXR5cGVvZiBjYWxsYmFjaz09J2Z1bmN0aW9uJztcclxuICAgICAgICByZXR1cm4gbmV3IERpYWxvZyh7XHJcbiAgICAgICAgICAgIGJ0bnM6J+ehruWumicsXHJcbiAgICAgICAgICAgIG9uc3VyZTpmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICAgICAgaWYoaXNjYWxsYmFjayl7XHJcbiAgICAgICAgICAgICAgICAgICAgY2FsbGVkPXRydWU7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGNhbGxiYWNrKHRydWUpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICBvbmhpZGU6ZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgICAgIGlmKCFjYWxsZWQgJiYgaXNjYWxsYmFjayl7XHJcbiAgICAgICAgICAgICAgICAgICAgY2FsbGJhY2soZmFsc2UpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkuc2hvdyhtZXNzYWdlLHRpdGxlKTtcclxuICAgIH0sXHJcbiAgICBjb25maXJtOmZ1bmN0aW9uKG1lc3NhZ2UsY29uZmlybSxjYW5jZWwpe1xyXG4gICAgICAgIHZhciBjYWxsZWQ9ZmFsc2U7XHJcbiAgICAgICAgcmV0dXJuIG5ldyBEaWFsb2coe1xyXG4gICAgICAgICAgICAnb25zdXJlJzpmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICAgICAgaWYodHlwZW9mIGNvbmZpcm09PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgICAgIGNhbGxlZD10cnVlO1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBjb25maXJtKCk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdvbmhpZGUnOmZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgIGlmKGNhbGxlZD1mYWxzZSAmJiB0eXBlb2YgY2FuY2VsPT0nZnVuY3Rpb24nKXtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gY2FuY2VsKCk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS5zaG93KG1lc3NhZ2UpO1xyXG4gICAgfSxcclxuICAgIHByb21wdDpmdW5jdGlvbihtZXNzYWdlLGNhbGxiYWNrLGNhbmNlbCl7XHJcbiAgICAgICAgdmFyIGNhbGxlZD1mYWxzZTtcclxuICAgICAgICByZXR1cm4gbmV3IERpYWxvZyh7XHJcbiAgICAgICAgICAgICdvbnNob3duJzpmdW5jdGlvbihib2R5KXtcclxuICAgICAgICAgICAgICAgIGJvZHkuZmluZCgnW25hbWU9Y29uZmlybV9pbnB1dF0nKS5mb2N1cygpO1xyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAnb25zdXJlJzpmdW5jdGlvbihib2R5KXtcclxuICAgICAgICAgICAgICAgIHZhciB2YWw9Ym9keS5maW5kKCdbbmFtZT1jb25maXJtX2lucHV0XScpLnZhbCgpO1xyXG4gICAgICAgICAgICAgICAgaWYodHlwZW9mIGNhbGxiYWNrPT0nZnVuY3Rpb24nKXtcclxuICAgICAgICAgICAgICAgICAgICB2YXIgcmVzdWx0ID0gY2FsbGJhY2sodmFsKTtcclxuICAgICAgICAgICAgICAgICAgICBpZihyZXN1bHQ9PT10cnVlKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgY2FsbGVkPXRydWU7XHJcbiAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiByZXN1bHQ7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdvbmhpZGUnOmZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgIGlmKGNhbGxlZD1mYWxzZSAmJiB0eXBlb2YgY2FuY2VsPT0nZnVuY3Rpb24nKXtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gY2FuY2VsKCk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS5zaG93KCc8aW5wdXQgdHlwZT1cInRleHRcIiBuYW1lPVwiY29uZmlybV9pbnB1dFwiIGNsYXNzPVwiZm9ybS1jb250cm9sXCIgLz4nLG1lc3NhZ2UpO1xyXG4gICAgfSxcclxuICAgIHBpY2tVc2VyOmZ1bmN0aW9uKHVybCxjYWxsYmFjayxmaWx0ZXIpe1xyXG4gICAgICAgIHZhciB1c2VyPW51bGw7XHJcbiAgICAgICAgaWYoIWZpbHRlcilmaWx0ZXI9e307XHJcbiAgICAgICAgdmFyIGRsZz1uZXcgRGlhbG9nKHtcclxuICAgICAgICAgICAgJ29uc2hvd24nOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgdmFyIGJ0bj1ib2R5LmZpbmQoJy5zZWFyY2hidG4nKTtcclxuICAgICAgICAgICAgICAgIHZhciBpbnB1dD1ib2R5LmZpbmQoJy5zZWFyY2h0ZXh0Jyk7XHJcbiAgICAgICAgICAgICAgICB2YXIgbGlzdGJveD1ib2R5LmZpbmQoJy5saXN0LWdyb3VwJyk7XHJcbiAgICAgICAgICAgICAgICB2YXIgaXNsb2FkaW5nPWZhbHNlO1xyXG4gICAgICAgICAgICAgICAgYnRuLmNsaWNrKGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgICAgICAgICAgaWYoaXNsb2FkaW5nKXJldHVybjtcclxuICAgICAgICAgICAgICAgICAgICBpc2xvYWRpbmc9dHJ1ZTtcclxuICAgICAgICAgICAgICAgICAgICBsaXN0Ym94Lmh0bWwoJzxzcGFuIGNsYXNzPVwibGlzdC1sb2FkaW5nXCI+5Yqg6L295LitLi4uPC9zcGFuPicpO1xyXG4gICAgICAgICAgICAgICAgICAgIGZpbHRlclsna2V5J109aW5wdXQudmFsKCk7XHJcbiAgICAgICAgICAgICAgICAgICAgJC5hamF4KFxyXG4gICAgICAgICAgICAgICAgICAgICAgICB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB1cmw6dXJsLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdHlwZTonR0VUJyxcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGRhdGFUeXBlOidKU09OJyxcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGRhdGE6ZmlsdGVyLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc3VjY2VzczpmdW5jdGlvbihqc29uKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpc2xvYWRpbmc9ZmFsc2U7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYoanNvbi5zdGF0dXMpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZihqc29uLmRhdGEgJiYganNvbi5kYXRhLmxlbmd0aCkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbGlzdGJveC5odG1sKCc8YSBocmVmPVwiamF2YXNjcmlwdDpcIiBkYXRhLWlkPVwie0BpZH1cIiBjbGFzcz1cImxpc3QtZ3JvdXAtaXRlbSBsaXN0LWdyb3VwLWl0ZW0tYWN0aW9uXCI+W3tAaWR9XSZuYnNwOzxpIGNsYXNzPVwiaW9uLW1kLXBlcnNvblwiPjwvaT4ge0B1c2VybmFtZX0mbmJzcDsmbmJzcDsmbmJzcDs8c21hbGw+PGkgY2xhc3M9XCJpb24tbWQtcGhvbmUtcG9ydHJhaXRcIj48L2k+IHtAbW9iaWxlfTwvc21hbGw+PC9hPicuY29tcGlsZShqc29uLmRhdGEsIHRydWUpKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxpc3Rib3guZmluZCgnYS5saXN0LWdyb3VwLWl0ZW0nKS5jbGljayhmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyIGlkID0gJCh0aGlzKS5kYXRhKCdpZCcpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGZvciAodmFyIGkgPSAwOyBpIDwganNvbi5kYXRhLmxlbmd0aDsgaSsrKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmKGpzb24uZGF0YVtpXS5pZD09aWQpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdXNlcj1qc29uLmRhdGFbaV07XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBsaXN0Ym94LmZpbmQoJ2EubGlzdC1ncm91cC1pdGVtJykucmVtb3ZlQ2xhc3MoJ2FjdGl2ZScpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5hZGRDbGFzcygnYWN0aXZlJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbGlzdGJveC5odG1sKCc8c3BhbiBjbGFzcz1cImxpc3QtbG9hZGluZ1wiPjxpIGNsYXNzPVwiaW9uLW1kLXdhcm5pbmdcIj48L2k+IOayoeacieajgOe0ouWIsOS8muWRmDwvc3Bhbj4nKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBsaXN0Ym94Lmh0bWwoJzxzcGFuIGNsYXNzPVwidGV4dC1kYW5nZXJcIj48aSBjbGFzcz1cImlvbi1tZC13YXJuaW5nXCI+PC9pPiDliqDovb3lpLHotKU8L3NwYW4+Jyk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgKTtcclxuXHJcbiAgICAgICAgICAgICAgICB9KS50cmlnZ2VyKCdjbGljaycpO1xyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAnb25zdXJlJzpmdW5jdGlvbihib2R5KXtcclxuICAgICAgICAgICAgICAgIGlmKCF1c2VyKXtcclxuICAgICAgICAgICAgICAgICAgICB0b2FzdHIud2FybmluZygn5rKh5pyJ6YCJ5oup5Lya5ZGYIScpO1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIGlmKHR5cGVvZiBjYWxsYmFjaz09J2Z1bmN0aW9uJyl7XHJcbiAgICAgICAgICAgICAgICAgICAgdmFyIHJlc3VsdCA9IGNhbGxiYWNrKHVzZXIpO1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiByZXN1bHQ7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS5zaG93KCc8ZGl2IGNsYXNzPVwiaW5wdXQtZ3JvdXBcIj48aW5wdXQgdHlwZT1cInRleHRcIiBjbGFzcz1cImZvcm0tY29udHJvbCBzZWFyY2h0ZXh0XCIgbmFtZT1cImtleXdvcmRcIiBwbGFjZWhvbGRlcj1cIuagueaNruS8muWRmGlk5oiW5ZCN56ew77yM55S16K+d5p2l5pCc57SiXCIvPjxkaXYgY2xhc3M9XCJpbnB1dC1ncm91cC1hcHBlbmRcIj48YSBjbGFzcz1cImJ0biBidG4tb3V0bGluZS1zZWNvbmRhcnkgc2VhcmNoYnRuXCI+PGkgY2xhc3M9XCJpb24tbWQtc2VhcmNoXCI+PC9pPjwvYT48L2Rpdj48L2Rpdj48ZGl2IGNsYXNzPVwibGlzdC1ncm91cCBtdC0yXCI+PC9kaXY+Jywn6K+35pCc57Si5bm26YCJ5oup5Lya5ZGYJyk7XHJcbiAgICB9LFxyXG4gICAgcGlja0xvY2F0ZTpmdW5jdGlvbih0eXBlLCBjYWxsYmFjaywgbG9jYXRlKXtcclxuICAgICAgICB2YXIgc2V0dGVkTG9jYXRlPW51bGw7XHJcbiAgICAgICAgdmFyIGRsZz1uZXcgRGlhbG9nKHtcclxuICAgICAgICAgICAgJ3NpemUnOidsZycsXHJcbiAgICAgICAgICAgICdvbnNob3duJzpmdW5jdGlvbihib2R5KXtcclxuICAgICAgICAgICAgICAgIHZhciBidG49Ym9keS5maW5kKCcuc2VhcmNoYnRuJyk7XHJcbiAgICAgICAgICAgICAgICB2YXIgaW5wdXQ9Ym9keS5maW5kKCcuc2VhcmNodGV4dCcpO1xyXG4gICAgICAgICAgICAgICAgdmFyIG1hcGJveD1ib2R5LmZpbmQoJy5tYXAnKTtcclxuICAgICAgICAgICAgICAgIHZhciBtYXBpbmZvPWJvZHkuZmluZCgnLm1hcGluZm8nKTtcclxuICAgICAgICAgICAgICAgIG1hcGJveC5jc3MoJ2hlaWdodCcsJCh3aW5kb3cpLmhlaWdodCgpKi42KTtcclxuICAgICAgICAgICAgICAgIHZhciBpc2xvYWRpbmc9ZmFsc2U7XHJcbiAgICAgICAgICAgICAgICB2YXIgbWFwPUluaXRNYXAoJ3RlbmNlbnQnLG1hcGJveCxmdW5jdGlvbihhZGRyZXNzLGxvY2F0ZSl7XHJcbiAgICAgICAgICAgICAgICAgICAgbWFwaW5mby5odG1sKGFkZHJlc3MrJyZuYnNwOycrbG9jYXRlLmxuZysnLCcrbG9jYXRlLmxhdCk7XHJcbiAgICAgICAgICAgICAgICAgICAgc2V0dGVkTG9jYXRlPWxvY2F0ZTtcclxuICAgICAgICAgICAgICAgIH0sbG9jYXRlKTtcclxuICAgICAgICAgICAgICAgIGJ0bi5jbGljayhmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICAgICAgICAgIHZhciBzZWFyY2g9aW5wdXQudmFsKCk7XHJcbiAgICAgICAgICAgICAgICAgICAgbWFwLnNldExvY2F0ZShzZWFyY2gpO1xyXG4gICAgICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdvbnN1cmUnOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgaWYoIXNldHRlZExvY2F0ZSl7XHJcbiAgICAgICAgICAgICAgICAgICAgdG9hc3RyLndhcm5pbmcoJ+ayoeaciemAieaLqeS9jee9riEnKTtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICBpZih0eXBlb2YgY2FsbGJhY2s9PT0nZnVuY3Rpb24nKXtcclxuICAgICAgICAgICAgICAgICAgICB2YXIgcmVzdWx0ID0gY2FsbGJhY2soc2V0dGVkTG9jYXRlKTtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gcmVzdWx0O1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkuc2hvdygnPGRpdiBjbGFzcz1cImlucHV0LWdyb3VwXCI+PGlucHV0IHR5cGU9XCJ0ZXh0XCIgY2xhc3M9XCJmb3JtLWNvbnRyb2wgc2VhcmNodGV4dFwiIG5hbWU9XCJrZXl3b3JkXCIgcGxhY2Vob2xkZXI9XCLloavlhpnlnLDlnYDmo4DntKLkvY3nva5cIi8+PGRpdiBjbGFzcz1cImlucHV0LWdyb3VwLWFwcGVuZFwiPjxhIGNsYXNzPVwiYnRuIGJ0bi1vdXRsaW5lLXNlY29uZGFyeSBzZWFyY2hidG5cIj48aSBjbGFzcz1cImlvbi1tZC1zZWFyY2hcIj48L2k+PC9hPjwvZGl2PjwvZGl2PicgK1xyXG4gICAgICAgICAgICAnPGRpdiBjbGFzcz1cIm1hcCBtdC0yXCI+PC9kaXY+JyArXHJcbiAgICAgICAgICAgICc8ZGl2IGNsYXNzPVwibWFwaW5mbyBtdC0yIHRleHQtbXV0ZWRcIj7mnKrpgInmi6nkvY3nva48L2Rpdj4nLCfor7fpgInmi6nlnLDlm77kvY3nva4nKTtcclxuICAgIH1cclxufTtcclxuXHJcbmpRdWVyeShmdW5jdGlvbigkKXtcclxuXHJcbiAgICAvL+ebkeaOp+aMiemUrlxyXG4gICAgJChkb2N1bWVudCkub24oJ2tleWRvd24nLCBmdW5jdGlvbihlKXtcclxuICAgICAgICBpZighRGlhbG9nLmluc3RhbmNlKXJldHVybjtcclxuICAgICAgICB2YXIgZGxnPURpYWxvZy5pbnN0YW5jZTtcclxuICAgICAgICBpZiAoZS5rZXlDb2RlID09IDEzKSB7XHJcbiAgICAgICAgICAgIGRsZy5ib3guZmluZCgnLm1vZGFsLWZvb3RlciAuYnRuJykuZXEoZGxnLm9wdGlvbnMuZGVmYXVsdEJ0bikudHJpZ2dlcignY2xpY2snKTtcclxuICAgICAgICB9XHJcbiAgICAgICAgLy/pu5jorqTlt7Lnm5HlkKzlhbPpl61cclxuICAgICAgICAvKmlmIChlLmtleUNvZGUgPT0gMjcpIHtcclxuICAgICAgICAgc2VsZi5oaWRlKCk7XHJcbiAgICAgICAgIH0qL1xyXG4gICAgfSk7XHJcbn0pOyIsIlxyXG5qUXVlcnkuZXh0ZW5kKGpRdWVyeS5mbix7XHJcbiAgICB0YWdzOmZ1bmN0aW9uKG5tKXtcclxuICAgICAgICB2YXIgZGF0YT1bXTtcclxuICAgICAgICB2YXIgdHBsPSc8c3BhbiBjbGFzcz1cImJhZGdlIGJhZGdlLWluZm9cIj57QGxhYmVsfTxpbnB1dCB0eXBlPVwiaGlkZGVuXCIgbmFtZT1cIicrbm0rJ1wiIHZhbHVlPVwie0BsYWJlbH1cIi8+PGJ1dHRvbiB0eXBlPVwiYnV0dG9uXCIgY2xhc3M9XCJjbG9zZVwiIGRhdGEtZGlzbWlzcz1cImFsZXJ0XCIgYXJpYS1sYWJlbD1cIkNsb3NlXCI+PHNwYW4gYXJpYS1oaWRkZW49XCJ0cnVlXCI+JnRpbWVzOzwvc3Bhbj48L2J1dHRvbj48L3NwYW4+JztcclxuICAgICAgICB2YXIgaXRlbT0kKHRoaXMpLnBhcmVudHMoJy5mb3JtLWNvbnRyb2wnKTtcclxuICAgICAgICB2YXIgbGFiZWxncm91cD0kKCc8c3BhbiBjbGFzcz1cImJhZGdlLWdyb3VwXCI+PC9zcGFuPicpO1xyXG4gICAgICAgIHZhciBpbnB1dD10aGlzO1xyXG4gICAgICAgIHRoaXMuYmVmb3JlKGxhYmVsZ3JvdXApO1xyXG4gICAgICAgIHRoaXMub24oJ2tleXVwJyxmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICB2YXIgdmFsPSQodGhpcykudmFsKCkucmVwbGFjZSgv77yML2csJywnKTtcclxuICAgICAgICAgICAgaWYodmFsICYmIHZhbC5pbmRleE9mKCcsJyk+LTEpe1xyXG4gICAgICAgICAgICAgICAgdmFyIHZhbHM9dmFsLnNwbGl0KCcsJyk7XHJcbiAgICAgICAgICAgICAgICBmb3IodmFyIGk9MDtpPHZhbHMubGVuZ3RoO2krKyl7XHJcbiAgICAgICAgICAgICAgICAgICAgdmFsc1tpXT12YWxzW2ldLnJlcGxhY2UoL15cXHN8XFxzJC9nLCcnKTtcclxuICAgICAgICAgICAgICAgICAgICBpZih2YWxzW2ldICYmIGRhdGEuaW5kZXhPZih2YWxzW2ldKT09PS0xKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgZGF0YS5wdXNoKHZhbHNbaV0pO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBsYWJlbGdyb3VwLmFwcGVuZCh0cGwuY29tcGlsZSh7bGFiZWw6dmFsc1tpXX0pKTtcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICBpbnB1dC52YWwoJycpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkub24oJ2JsdXInLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgICQodGhpcykudmFsKCQodGhpcykudmFsKCkrJywnKS50cmlnZ2VyKCdrZXl1cCcpO1xyXG4gICAgICAgIH0pLnRyaWdnZXIoJ2tleXVwJyk7XHJcbiAgICAgICAgbGFiZWxncm91cC5vbignY2xpY2snLCcuY2xvc2UnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgIHZhciB0YWc9JCh0aGlzKS5wYXJlbnRzKCcuYmFkZ2UnKS5maW5kKCdpbnB1dCcpLnZhbCgpO1xyXG4gICAgICAgICAgICB2YXIgaWQ9ZGF0YS5pbmRleE9mKHRhZyk7XHJcbiAgICAgICAgICAgIGlmKGlkKWRhdGEuc3BsaWNlKGlkLDEpO1xyXG4gICAgICAgICAgICAkKHRoaXMpLnBhcmVudHMoJy5iYWRnZScpLnJlbW92ZSgpO1xyXG4gICAgICAgIH0pO1xyXG4gICAgICAgIGl0ZW0uY2xpY2soZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgaW5wdXQuZm9jdXMoKTtcclxuICAgICAgICB9KTtcclxuICAgIH1cclxufSk7IiwiLy/ml6XmnJ/nu4Tku7ZcclxuaWYoJC5mbi5kYXRldGltZXBpY2tlcikge1xyXG4gICAgdmFyIHRvb2x0aXBzPSB7XHJcbiAgICAgICAgdG9kYXk6ICflrprkvY3lvZPliY3ml6XmnJ8nLFxyXG4gICAgICAgIGNsZWFyOiAn5riF6Zmk5bey6YCJ5pel5pyfJyxcclxuICAgICAgICBjbG9zZTogJ+WFs+mXremAieaLqeWZqCcsXHJcbiAgICAgICAgc2VsZWN0TW9udGg6ICfpgInmi6nmnIjku70nLFxyXG4gICAgICAgIHByZXZNb250aDogJ+S4iuS4quaciCcsXHJcbiAgICAgICAgbmV4dE1vbnRoOiAn5LiL5Liq5pyIJyxcclxuICAgICAgICBzZWxlY3RZZWFyOiAn6YCJ5oup5bm05Lu9JyxcclxuICAgICAgICBwcmV2WWVhcjogJ+S4iuS4gOW5tCcsXHJcbiAgICAgICAgbmV4dFllYXI6ICfkuIvkuIDlubQnLFxyXG4gICAgICAgIHNlbGVjdERlY2FkZTogJ+mAieaLqeW5tOS7veWMuumXtCcsXHJcbiAgICAgICAgcHJldkRlY2FkZTogJ+S4iuS4gOWMuumXtCcsXHJcbiAgICAgICAgbmV4dERlY2FkZTogJ+S4i+S4gOWMuumXtCcsXHJcbiAgICAgICAgcHJldkNlbnR1cnk6ICfkuIrkuKrkuJbnuqonLFxyXG4gICAgICAgIG5leHRDZW50dXJ5OiAn5LiL5Liq5LiW57qqJ1xyXG4gICAgfTtcclxuICAgIHZhciBpY29ucz17XHJcbiAgICAgICAgdGltZTogJ2lvbi1tZC10aW1lJyxcclxuICAgICAgICBkYXRlOiAnaW9uLW1kLWNhbGVuZGFyJyxcclxuICAgICAgICB1cDogJ2lvbi1tZC1hcnJvdy1kcm9wdXAnLFxyXG4gICAgICAgIGRvd246ICdpb24tbWQtYXJyb3ctZHJvcGRvd24nLFxyXG4gICAgICAgIHByZXZpb3VzOiAnaW9uLW1kLWFycm93LWRyb3BsZWZ0JyxcclxuICAgICAgICBuZXh0OiAnaW9uLW1kLWFycm93LWRyb3ByaWdodCcsXHJcbiAgICAgICAgdG9kYXk6ICdpb24tbWQtdG9kYXknLFxyXG4gICAgICAgIGNsZWFyOiAnaW9uLW1kLXRyYXNoJyxcclxuICAgICAgICBjbG9zZTogJ2lvbi1tZC1jbG9zZSdcclxuICAgIH07XHJcbiAgICAkKCcuZGF0ZXBpY2tlcicpLmRhdGV0aW1lcGlja2VyKHtcclxuICAgICAgICBpY29uczppY29ucyxcclxuICAgICAgICB0b29sdGlwczp0b29sdGlwcyxcclxuICAgICAgICBmb3JtYXQ6ICdZWVlZLU1NLUREJyxcclxuICAgICAgICBsb2NhbGU6ICd6aC1jbicsXHJcbiAgICAgICAgc2hvd0NsZWFyOnRydWUsXHJcbiAgICAgICAgc2hvd1RvZGF5QnV0dG9uOnRydWUsXHJcbiAgICAgICAgc2hvd0Nsb3NlOnRydWUsXHJcbiAgICAgICAga2VlcEludmFsaWQ6dHJ1ZVxyXG4gICAgfSk7XHJcblxyXG4gICAgJCgnLmRhdGUtcmFuZ2UnKS5lYWNoKGZ1bmN0aW9uICgpIHtcclxuICAgICAgICB2YXIgZnJvbSA9ICQodGhpcykuZmluZCgnW25hbWU9ZnJvbWRhdGVdLC5mcm9tZGF0ZScpLCB0byA9ICQodGhpcykuZmluZCgnW25hbWU9dG9kYXRlXSwudG9kYXRlJyk7XHJcbiAgICAgICAgdmFyIG9wdGlvbnMgPSB7XHJcbiAgICAgICAgICAgIGljb25zOmljb25zLFxyXG4gICAgICAgICAgICB0b29sdGlwczp0b29sdGlwcyxcclxuICAgICAgICAgICAgZm9ybWF0OiAnWVlZWS1NTS1ERCcsXHJcbiAgICAgICAgICAgIGxvY2FsZTonemgtY24nLFxyXG4gICAgICAgICAgICBzaG93Q2xlYXI6dHJ1ZSxcclxuICAgICAgICAgICAgc2hvd1RvZGF5QnV0dG9uOnRydWUsXHJcbiAgICAgICAgICAgIHNob3dDbG9zZTp0cnVlLFxyXG4gICAgICAgICAgICBrZWVwSW52YWxpZDp0cnVlXHJcbiAgICAgICAgfTtcclxuICAgICAgICBmcm9tLmRhdGV0aW1lcGlja2VyKG9wdGlvbnMpLm9uKCdkcC5jaGFuZ2UnLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIGlmIChmcm9tLnZhbCgpKSB7XHJcbiAgICAgICAgICAgICAgICB0by5kYXRhKCdEYXRlVGltZVBpY2tlcicpLm1pbkRhdGUoZnJvbS52YWwoKSk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KTtcclxuICAgICAgICB0by5kYXRldGltZXBpY2tlcihvcHRpb25zKS5vbignZHAuY2hhbmdlJywgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICBpZiAodG8udmFsKCkpIHtcclxuICAgICAgICAgICAgICAgIGZyb20uZGF0YSgnRGF0ZVRpbWVQaWNrZXInKS5tYXhEYXRlKHRvLnZhbCgpKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pO1xyXG4gICAgfSk7XHJcbn0iLCJcclxuKGZ1bmN0aW9uKHdpbmRvdywkKSB7XHJcbiAgICB2YXIgYXBpcyA9IHtcclxuICAgICAgICAnYmFpZHUnOiAnaHR0cHM6Ly9hcGkubWFwLmJhaWR1LmNvbS9hcGk/YWs9ck85dE9kRVdGZnZ5R2dEa2lXcUZqeEs2JnY9MS41JnNlcnZpY2VzPWZhbHNlJmNhbGxiYWNrPScsXHJcbiAgICAgICAgJ2dvb2dsZSc6ICdodHRwczovL21hcHMuZ29vZ2xlLmNvbS9tYXBzL2FwaS9qcz9rZXk9QUl6YVN5Qjhsb3J2bDZFdHFJV3o2N2JqV0JydU9obTlOWVMxZTI0JmNhbGxiYWNrPScsXHJcbiAgICAgICAgJ3RlbmNlbnQnOiAnaHR0cHM6Ly9tYXAucXEuY29tL2FwaS9qcz92PTIuZXhwJmtleT03STVCWi1RVUU2Ui1KWExXVi1XVFZBQS1DSk1ZRi03UEJCSSZjYWxsYmFjaz0nLFxyXG4gICAgICAgICdnYW9kZSc6ICdodHRwczovL3dlYmFwaS5hbWFwLmNvbS9tYXBzP3Y9MS4zJmtleT0zZWMzMTFiNWRiMGQ1OTdlNzk0MjJlZWI5YTZkNDQ0OSZjYWxsYmFjaz0nXHJcbiAgICB9O1xyXG5cclxuICAgIGZ1bmN0aW9uIGxvYWRTY3JpcHQoc3JjKSB7XHJcbiAgICAgICAgdmFyIHNjcmlwdCA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoXCJzY3JpcHRcIik7XHJcbiAgICAgICAgc2NyaXB0LnR5cGUgPSBcInRleHQvamF2YXNjcmlwdFwiO1xyXG4gICAgICAgIHNjcmlwdC5zcmMgPSBzcmM7XHJcbiAgICAgICAgZG9jdW1lbnQuYm9keS5hcHBlbmRDaGlsZChzY3JpcHQpO1xyXG4gICAgfVxyXG5cclxuICAgIHZhciBtYXBPYmosbWFwQm94LG9uUGljaztcclxuXHJcbiAgICBmdW5jdGlvbiBJbml0TWFwKG1hcGtleSxib3gsY2FsbGJhY2ssbG9jYXRlKSB7XHJcbiAgICAgICAgaWYgKG1hcE9iaikgbWFwT2JqLmhpZGUoKTtcclxuICAgICAgICBtYXBCb3g9JChib3gpO1xyXG4gICAgICAgIG9uUGljaz1jYWxsYmFjaztcclxuXHJcbiAgICAgICAgc3dpdGNoIChtYXBrZXkudG9Mb3dlckNhc2UoKSkge1xyXG4gICAgICAgICAgICBjYXNlICdiYWlkdSc6XHJcbiAgICAgICAgICAgICAgICBtYXBPYmogPSBuZXcgQmFpZHVNYXAoKTtcclxuICAgICAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgICAgICBjYXNlICdnb29nbGUnOlxyXG4gICAgICAgICAgICAgICAgbWFwT2JqID0gbmV3IEdvb2dsZU1hcCgpO1xyXG4gICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgIGNhc2UgJ3RlbmNlbnQnOlxyXG4gICAgICAgICAgICBjYXNlICdxcSc6XHJcbiAgICAgICAgICAgICAgICBtYXBPYmogPSBuZXcgVGVuY2VudE1hcCgpO1xyXG4gICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgIGNhc2UgJ2dhb2RlJzpcclxuICAgICAgICAgICAgICAgIG1hcE9iaiA9IG5ldyBHYW9kZU1hcCgpO1xyXG4gICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIGlmICghbWFwT2JqKSByZXR1cm4gdG9hc3RyLndhcm5pbmcoJ+S4jeaUr+aMgeivpeWcsOWbvuexu+WeiycpO1xyXG4gICAgICAgIGlmKGxvY2F0ZSl7XHJcbiAgICAgICAgICAgIGlmKHR5cGVvZiBsb2NhdGU9PT0nc3RyaW5nJyl7XHJcbiAgICAgICAgICAgICAgICB2YXIgbG9jPWxvY2F0ZS5zcGxpdCgnLCcpO1xyXG4gICAgICAgICAgICAgICAgbG9jYXRlPXtcclxuICAgICAgICAgICAgICAgICAgICBsbmc6cGFyc2VGbG9hdChsb2NbMF0pLFxyXG4gICAgICAgICAgICAgICAgICAgIGxhdDpwYXJzZUZsb2F0KGxvY1sxXSlcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICBtYXBPYmoubG9jYXRlPWxvY2F0ZTtcclxuICAgICAgICB9XHJcbiAgICAgICAgbWFwT2JqLnNldE1hcCgpO1xyXG5cclxuICAgICAgICByZXR1cm4gbWFwT2JqO1xyXG4gICAgfVxyXG5cclxuICAgIGZ1bmN0aW9uIEJhc2VNYXAodHlwZSkge1xyXG4gICAgICAgIHRoaXMubWFwVHlwZSA9IHR5cGU7XHJcbiAgICAgICAgdGhpcy5pc2hpZGUgPSBmYWxzZTtcclxuICAgICAgICB0aGlzLmlzc2hvdyA9IGZhbHNlO1xyXG4gICAgICAgIHRoaXMudG9zaG93ID0gZmFsc2U7XHJcbiAgICAgICAgdGhpcy5tYXJrZXIgPSBudWxsO1xyXG4gICAgICAgIHRoaXMuaW5mb1dpbmRvdyA9IG51bGw7XHJcbiAgICAgICAgdGhpcy5tYXBib3ggPSBudWxsO1xyXG4gICAgICAgIHRoaXMubG9jYXRlID0ge2xuZzoxMTYuMzk2Nzk1LGxhdDozOS45MzMwODR9O1xyXG4gICAgICAgIHRoaXMubWFwID0gbnVsbDtcclxuICAgIH1cclxuXHJcbiAgICBCYXNlTWFwLnByb3RvdHlwZS5pc0FQSVJlYWR5ID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgIH07XHJcbiAgICBCYXNlTWFwLnByb3RvdHlwZS5zZXRNYXAgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICB9O1xyXG4gICAgQmFzZU1hcC5wcm90b3R5cGUuc2hvd0luZm8gPSBmdW5jdGlvbiAoKSB7XHJcbiAgICB9O1xyXG4gICAgQmFzZU1hcC5wcm90b3R5cGUuZ2V0QWRkcmVzcyA9IGZ1bmN0aW9uIChycykge1xyXG4gICAgICAgIHJldHVybiBcIlwiO1xyXG4gICAgfTtcclxuICAgIEJhc2VNYXAucHJvdG90eXBlLnNldExvY2F0ZSA9IGZ1bmN0aW9uIChhZGRyZXNzKSB7XHJcbiAgICB9O1xyXG5cclxuICAgIEJhc2VNYXAucHJvdG90eXBlLmxvYWRBUEkgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgdmFyIHNlbGYgPSB0aGlzO1xyXG4gICAgICAgIGlmICghdGhpcy5pc0FQSVJlYWR5KCkpIHtcclxuICAgICAgICAgICAgdGhpcy5tYXBib3ggPSAkKCc8ZGl2IGlkPVwiJyArIHRoaXMubWFwVHlwZSArICdtYXBcIiBjbGFzcz1cIm1hcGJveFwiPmxvYWRpbmcuLi48L2Rpdj4nKTtcclxuICAgICAgICAgICAgbWFwQm94LmFwcGVuZCh0aGlzLm1hcGJveCk7XHJcblxyXG4gICAgICAgICAgICAvL2NvbnNvbGUubG9nKHRoaXMubWFwVHlwZSsnIG1hcGxvYWRpbmcuLi4nKTtcclxuICAgICAgICAgICAgdmFyIGZ1bmMgPSAnbWFwbG9hZCcgKyBuZXcgRGF0ZSgpLmdldFRpbWUoKTtcclxuICAgICAgICAgICAgd2luZG93W2Z1bmNdID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAgICAgc2VsZi5zZXRNYXAoKTtcclxuICAgICAgICAgICAgICAgIGRlbGV0ZSB3aW5kb3dbZnVuY107XHJcbiAgICAgICAgICAgIH07XHJcbiAgICAgICAgICAgIGxvYWRTY3JpcHQoYXBpc1t0aGlzLm1hcFR5cGVdICsgZnVuYyk7XHJcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAvL2NvbnNvbGUubG9nKHRoaXMubWFwVHlwZSArICcgbWFwbG9hZGVkJyk7XHJcbiAgICAgICAgICAgIHRoaXMubWFwYm94ID0gJCgnIycgKyB0aGlzLm1hcFR5cGUgKyAnbWFwJyk7XHJcbiAgICAgICAgICAgIGlmICh0aGlzLm1hcGJveC5sZW5ndGggPCAxKSB7XHJcbiAgICAgICAgICAgICAgICB0aGlzLm1hcGJveCA9ICQoJzxkaXYgaWQ9XCInICsgdGhpcy5tYXBUeXBlICsgJ21hcFwiIGNsYXNzPVwibWFwYm94XCI+PC9kaXY+Jyk7XHJcbiAgICAgICAgICAgICAgICBtYXBCb3guYXBwZW5kKHRoaXMubWFwYm94KTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICByZXR1cm4gdHJ1ZTtcclxuICAgICAgICB9XHJcbiAgICB9O1xyXG4gICAgQmFzZU1hcC5wcm90b3R5cGUuYmluZEV2ZW50cyA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICB2YXIgc2VsZiA9IHRoaXM7XHJcbiAgICAgICAgJCgnI3R4dFRpdGxlJykudW5iaW5kKCkuYmx1cihmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIHNlbGYuc2hvd0luZm8oKTtcclxuICAgICAgICB9KTtcclxuICAgICAgICAkKCcjdHh0Q29udGVudCcpLnVuYmluZCgpLmJsdXIoZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICBzZWxmLnNob3dJbmZvKCk7XHJcbiAgICAgICAgfSk7XHJcbiAgICB9O1xyXG4gICAgQmFzZU1hcC5wcm90b3R5cGUuc2V0SW5mb0NvbnRlbnQgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgaWYgKCF0aGlzLmluZm9XaW5kb3cpIHJldHVybjtcclxuICAgICAgICB2YXIgdGl0bGUgPSAnPGI+5b2T5YmN5L2N572uPC9iPic7XHJcbiAgICAgICAgdmFyIGFkZHIgPSAnPHAgc3R5bGU9XCJsaW5lLWhlaWdodDoxLjZlbTtcIj48L3A+JztcclxuICAgICAgICBpZiAodGhpcy5pbmZvV2luZG93LnNldFRpdGxlKSB7XHJcbiAgICAgICAgICAgIHRoaXMuaW5mb1dpbmRvdy5zZXRUaXRsZSh0aXRsZSk7XHJcbiAgICAgICAgICAgIHRoaXMuaW5mb1dpbmRvdy5zZXRDb250ZW50KGFkZHIpO1xyXG4gICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgIHZhciBjb250ZW50ID0gJzxoMz4nICsgdGl0bGUgKyAnPC9oMz48ZGl2IHN0eWxlPVwid2lkdGg6MjUwcHhcIj4nICsgYWRkciArICc8L2Rpdj4nO1xyXG4gICAgICAgICAgICB0aGlzLmluZm9XaW5kb3cuc2V0Q29udGVudChjb250ZW50KTtcclxuICAgICAgICB9XHJcbiAgICB9O1xyXG4gICAgQmFzZU1hcC5wcm90b3R5cGUuc2hvd0xvY2F0aW9uSW5mbyA9IGZ1bmN0aW9uIChwdCwgcnMpIHtcclxuXHJcbiAgICAgICAgdGhpcy5zaG93SW5mbygpO1xyXG4gICAgICAgIHZhciBhZGRyZXNzPXRoaXMuZ2V0QWRkcmVzcyhycyk7XHJcbiAgICAgICAgdmFyIGxvY2F0ZT17fTtcclxuICAgICAgICBpZiAodHlwZW9mIChwdC5sbmcpID09PSAnZnVuY3Rpb24nKSB7XHJcbiAgICAgICAgICAgIGxvY2F0ZS5sbmc9cHQubG5nKCk7XHJcbiAgICAgICAgICAgIGxvY2F0ZS5sYXQ9cHQubGF0KCk7XHJcbiAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgbG9jYXRlLmxuZz1wdC5sbmc7XHJcbiAgICAgICAgICAgIGxvY2F0ZS5sYXQ9cHQubGF0O1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgb25QaWNrKGFkZHJlc3MsbG9jYXRlKTtcclxuICAgIH07XHJcbiAgICBCYXNlTWFwLnByb3RvdHlwZS5zaG93ID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHRoaXMuaXNoaWRlID0gZmFsc2U7XHJcbiAgICAgICAgdGhpcy5zZXRNYXAoKTtcclxuICAgICAgICB0aGlzLnNob3dJbmZvKCk7XHJcbiAgICB9O1xyXG4gICAgQmFzZU1hcC5wcm90b3R5cGUuaGlkZSA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICB0aGlzLmlzaGlkZSA9IHRydWU7XHJcbiAgICAgICAgaWYgKHRoaXMuaW5mb1dpbmRvdykge1xyXG4gICAgICAgICAgICB0aGlzLmluZm9XaW5kb3cuY2xvc2UoKTtcclxuICAgICAgICB9XHJcbiAgICAgICAgaWYgKHRoaXMubWFwYm94KSB7XHJcbiAgICAgICAgICAgICQodGhpcy5tYXBib3gpLnJlbW92ZSgpO1xyXG4gICAgICAgIH1cclxuICAgIH07XHJcblxyXG5cclxuICAgIGZ1bmN0aW9uIEJhaWR1TWFwKCkge1xyXG4gICAgICAgIEJhc2VNYXAuY2FsbCh0aGlzLCBcImJhaWR1XCIpO1xyXG4gICAgfVxyXG5cclxuICAgIEJhaWR1TWFwLnByb3RvdHlwZSA9IG5ldyBCYXNlTWFwKCk7XHJcbiAgICBCYWlkdU1hcC5wcm90b3R5cGUuY29uc3RydWN0b3IgPSBCYWlkdU1hcDtcclxuICAgIEJhaWR1TWFwLnByb3RvdHlwZS5pc0FQSVJlYWR5ID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHJldHVybiAhIXdpbmRvd1snQk1hcCddO1xyXG4gICAgfTtcclxuICAgIEJhaWR1TWFwLnByb3RvdHlwZS5zZXRNYXAgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgdmFyIHNlbGYgPSB0aGlzO1xyXG4gICAgICAgIGlmICh0aGlzLmlzc2hvdyB8fCB0aGlzLmlzaGlkZSkgcmV0dXJuO1xyXG4gICAgICAgIGlmICghdGhpcy5sb2FkQVBJKCkpIHJldHVybjtcclxuXHJcbiAgICAgICAgdmFyIG1hcCA9IHNlbGYubWFwID0gbmV3IEJNYXAuTWFwKHRoaXMubWFwYm94LmF0dHIoJ2lkJykpOyAvL+WIneWni+WMluWcsOWbvlxyXG4gICAgICAgIG1hcC5hZGRDb250cm9sKG5ldyBCTWFwLk5hdmlnYXRpb25Db250cm9sKCkpOyAgLy/liJ3lp4vljJblnLDlm77mjqfku7ZcclxuICAgICAgICBtYXAuYWRkQ29udHJvbChuZXcgQk1hcC5TY2FsZUNvbnRyb2woKSk7XHJcbiAgICAgICAgbWFwLmFkZENvbnRyb2wobmV3IEJNYXAuT3ZlcnZpZXdNYXBDb250cm9sKCkpO1xyXG4gICAgICAgIG1hcC5lbmFibGVTY3JvbGxXaGVlbFpvb20oKTtcclxuXHJcbiAgICAgICAgdmFyIHBvaW50ID0gbmV3IEJNYXAuUG9pbnQodGhpcy5sb2NhdGUubG5nLCB0aGlzLmxvY2F0ZS5sYXQpO1xyXG4gICAgICAgIG1hcC5jZW50ZXJBbmRab29tKHBvaW50LCAxNSk7IC8v5Yid5aeL5YyW5Zyw5Zu+5Lit5b+D54K5XHJcbiAgICAgICAgdGhpcy5tYXJrZXIgPSBuZXcgQk1hcC5NYXJrZXIocG9pbnQpOyAvL+WIneWni+WMluWcsOWbvuagh+iusFxyXG4gICAgICAgIHRoaXMubWFya2VyLmVuYWJsZURyYWdnaW5nKCk7IC8v5qCH6K6w5byA5ZCv5ouW5ou9XHJcblxyXG4gICAgICAgIHZhciBnYyA9IG5ldyBCTWFwLkdlb2NvZGVyKCk7IC8v5Zyw5Z2A6Kej5p6Q57G7XHJcbiAgICAgICAgLy/mt7vliqDmoIforrDmi5bmi73nm5HlkKxcclxuICAgICAgICB0aGlzLm1hcmtlci5hZGRFdmVudExpc3RlbmVyKFwiZHJhZ2VuZFwiLCBmdW5jdGlvbiAoZSkge1xyXG4gICAgICAgICAgICAvL+iOt+WPluWcsOWdgOS/oeaBr1xyXG4gICAgICAgICAgICBnYy5nZXRMb2NhdGlvbihlLnBvaW50LCBmdW5jdGlvbiAocnMpIHtcclxuICAgICAgICAgICAgICAgIHNlbGYuc2hvd0xvY2F0aW9uSW5mbyhlLnBvaW50LCBycyk7XHJcbiAgICAgICAgICAgIH0pO1xyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICAvL+a3u+WKoOagh+iusOeCueWHu+ebkeWQrFxyXG4gICAgICAgIHRoaXMubWFya2VyLmFkZEV2ZW50TGlzdGVuZXIoXCJjbGlja1wiLCBmdW5jdGlvbiAoZSkge1xyXG4gICAgICAgICAgICBnYy5nZXRMb2NhdGlvbihlLnBvaW50LCBmdW5jdGlvbiAocnMpIHtcclxuICAgICAgICAgICAgICAgIHNlbGYuc2hvd0xvY2F0aW9uSW5mbyhlLnBvaW50LCBycyk7XHJcbiAgICAgICAgICAgIH0pO1xyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICBtYXAuYWRkT3ZlcmxheSh0aGlzLm1hcmtlcik7IC8v5bCG5qCH6K6w5re75Yqg5Yiw5Zyw5Zu+5LitXHJcblxyXG4gICAgICAgIGdjLmdldExvY2F0aW9uKHBvaW50LCBmdW5jdGlvbiAocnMpIHtcclxuICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKHBvaW50LCBycyk7XHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIHRoaXMuaW5mb1dpbmRvdyA9IG5ldyBCTWFwLkluZm9XaW5kb3coXCJcIiwge1xyXG4gICAgICAgICAgICB3aWR0aDogMjUwLFxyXG4gICAgICAgICAgICB0aXRsZTogXCJcIlxyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICB0aGlzLmJpbmRFdmVudHMoKTtcclxuXHJcbiAgICAgICAgdGhpcy5pc3Nob3cgPSB0cnVlO1xyXG4gICAgICAgIGlmICh0aGlzLnRvc2hvdykge1xyXG4gICAgICAgICAgICB0aGlzLnNob3dJbmZvKCk7XHJcbiAgICAgICAgICAgIHRoaXMudG9zaG93ID0gZmFsc2U7XHJcbiAgICAgICAgfVxyXG4gICAgfTtcclxuXHJcbiAgICBCYWlkdU1hcC5wcm90b3R5cGUuc2hvd0luZm8gPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgaWYgKHRoaXMuaXNoaWRlKSByZXR1cm47XHJcbiAgICAgICAgaWYgKCF0aGlzLmlzc2hvdykge1xyXG4gICAgICAgICAgICB0aGlzLnRvc2hvdyA9IHRydWU7XHJcbiAgICAgICAgICAgIHJldHVybjtcclxuICAgICAgICB9XHJcbiAgICAgICAgdGhpcy5zZXRJbmZvQ29udGVudCgpO1xyXG5cclxuICAgICAgICB0aGlzLm1hcmtlci5vcGVuSW5mb1dpbmRvdyh0aGlzLmluZm9XaW5kb3cpO1xyXG4gICAgfTtcclxuICAgIEJhaWR1TWFwLnByb3RvdHlwZS5nZXRBZGRyZXNzID0gZnVuY3Rpb24gKHJzKSB7XHJcbiAgICAgICAgdmFyIGFkZENvbXAgPSBycy5hZGRyZXNzQ29tcG9uZW50cztcclxuICAgICAgICBpZihhZGRDb21wKSB7XHJcbiAgICAgICAgICAgIHJldHVybiBhZGRDb21wLnByb3ZpbmNlICsgXCIsIFwiICsgYWRkQ29tcC5jaXR5ICsgXCIsIFwiICsgYWRkQ29tcC5kaXN0cmljdCArIFwiLCBcIiArIGFkZENvbXAuc3RyZWV0ICsgXCIsIFwiICsgYWRkQ29tcC5zdHJlZXROdW1iZXI7XHJcbiAgICAgICAgfVxyXG4gICAgfTtcclxuICAgIEJhaWR1TWFwLnByb3RvdHlwZS5zZXRMb2NhdGUgPSBmdW5jdGlvbiAoYWRkcmVzcykge1xyXG4gICAgICAgIC8vIOWIm+W7uuWcsOWdgOino+aekOWZqOWunuS+i1xyXG4gICAgICAgIHZhciBteUdlbyA9IG5ldyBCTWFwLkdlb2NvZGVyKCk7XHJcbiAgICAgICAgdmFyIHNlbGYgPSB0aGlzO1xyXG4gICAgICAgIG15R2VvLmdldFBvaW50KGFkZHJlc3MsIGZ1bmN0aW9uIChwb2ludCkge1xyXG4gICAgICAgICAgICBpZiAocG9pbnQpIHtcclxuICAgICAgICAgICAgICAgIHNlbGYubWFwLmNlbnRlckFuZFpvb20ocG9pbnQsIDExKTtcclxuICAgICAgICAgICAgICAgIHNlbGYubWFya2VyLnNldFBvc2l0aW9uKHBvaW50KTtcclxuICAgICAgICAgICAgICAgIG15R2VvLmdldExvY2F0aW9uKHBvaW50LCBmdW5jdGlvbiAocnMpIHtcclxuICAgICAgICAgICAgICAgICAgICBzZWxmLnNob3dMb2NhdGlvbkluZm8ocG9pbnQsIHJzKTtcclxuICAgICAgICAgICAgICAgIH0pO1xyXG4gICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgdG9hc3RyLndhcm5pbmcoXCLlnLDlnYDkv6Hmga/kuI3mraPnoa7vvIzlrprkvY3lpLHotKVcIik7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9LCAnJyk7XHJcbiAgICB9O1xyXG5cclxuXHJcbiAgICBmdW5jdGlvbiBHb29nbGVNYXAoKSB7XHJcbiAgICAgICAgQmFzZU1hcC5jYWxsKHRoaXMsIFwiZ29vZ2xlXCIpO1xyXG4gICAgICAgIHRoaXMuaW5mb09wdHMgPSB7XHJcbiAgICAgICAgICAgIHdpZHRoOiAyNTAsICAgICAvL+S/oeaBr+eql+WPo+WuveW6plxyXG4gICAgICAgICAgICAvLyAgIGhlaWdodDogMTAwLCAgICAgLy/kv6Hmga/nqpflj6Ppq5jluqZcclxuICAgICAgICAgICAgdGl0bGU6IFwiXCIgIC8v5L+h5oGv56qX5Y+j5qCH6aKYXHJcbiAgICAgICAgfTtcclxuICAgIH1cclxuXHJcbiAgICBHb29nbGVNYXAucHJvdG90eXBlID0gbmV3IEJhc2VNYXAoKTtcclxuICAgIEdvb2dsZU1hcC5wcm90b3R5cGUuY29uc3RydWN0b3IgPSBHb29nbGVNYXA7XHJcbiAgICBHb29nbGVNYXAucHJvdG90eXBlLmlzQVBJUmVhZHkgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgcmV0dXJuIHdpbmRvd1snZ29vZ2xlJ10gJiYgd2luZG93Wydnb29nbGUnXVsnbWFwcyddXHJcbiAgICB9O1xyXG4gICAgR29vZ2xlTWFwLnByb3RvdHlwZS5zZXRNYXAgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgdmFyIHNlbGYgPSB0aGlzO1xyXG4gICAgICAgIGlmICh0aGlzLmlzc2hvdyB8fCB0aGlzLmlzaGlkZSkgcmV0dXJuO1xyXG4gICAgICAgIGlmICghdGhpcy5sb2FkQVBJKCkpIHJldHVybjtcclxuXHJcbiAgICAgICAgLy/or7TmmI7lnLDlm77lt7LliIfmjaJcclxuICAgICAgICBpZiAodGhpcy5tYXBib3gubGVuZ3RoIDwgMSkgcmV0dXJuO1xyXG5cclxuICAgICAgICB2YXIgbWFwID0gc2VsZi5tYXAgPSBuZXcgZ29vZ2xlLm1hcHMuTWFwKHRoaXMubWFwYm94WzBdLCB7XHJcbiAgICAgICAgICAgIHpvb206IDE1LFxyXG4gICAgICAgICAgICBkcmFnZ2FibGU6IHRydWUsXHJcbiAgICAgICAgICAgIHNjYWxlQ29udHJvbDogdHJ1ZSxcclxuICAgICAgICAgICAgc3RyZWV0Vmlld0NvbnRyb2w6IHRydWUsXHJcbiAgICAgICAgICAgIHpvb21Db250cm9sOiB0cnVlXHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIC8v6I635Y+W57uP57qs5bqm5Z2Q5qCH5YC8XHJcbiAgICAgICAgdmFyIHBvaW50ID0gbmV3IGdvb2dsZS5tYXBzLkxhdExuZyh0aGlzLmxvY2F0ZSk7XHJcbiAgICAgICAgbWFwLnBhblRvKHBvaW50KTtcclxuICAgICAgICB0aGlzLm1hcmtlciA9IG5ldyBnb29nbGUubWFwcy5NYXJrZXIoe3Bvc2l0aW9uOiBwb2ludCwgbWFwOiBtYXAsIGRyYWdnYWJsZTogdHJ1ZX0pO1xyXG5cclxuXHJcbiAgICAgICAgdmFyIGdjID0gbmV3IGdvb2dsZS5tYXBzLkdlb2NvZGVyKCk7XHJcblxyXG4gICAgICAgIHRoaXMubWFya2VyLmFkZExpc3RlbmVyKFwiZHJhZ2VuZFwiLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIHBvaW50ID0gc2VsZi5tYXJrZXIuZ2V0UG9zaXRpb24oKTtcclxuICAgICAgICAgICAgZ2MuZ2VvY29kZSh7J2xvY2F0aW9uJzogcG9pbnR9LCBmdW5jdGlvbiAocnMpIHtcclxuICAgICAgICAgICAgICAgIHNlbGYuc2hvd0xvY2F0aW9uSW5mbyhwb2ludCwgcnMpO1xyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgLy/mt7vliqDmoIforrDngrnlh7vnm5HlkKxcclxuICAgICAgICB0aGlzLm1hcmtlci5hZGRMaXN0ZW5lcihcImNsaWNrXCIsIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgcG9pbnQgPSBzZWxmLm1hcmtlci5nZXRQb3NpdGlvbigpO1xyXG4gICAgICAgICAgICBnYy5nZW9jb2RlKHsnbG9jYXRpb24nOiBwb2ludH0sIGZ1bmN0aW9uIChycykge1xyXG4gICAgICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKHBvaW50LCBycyk7XHJcbiAgICAgICAgICAgIH0pO1xyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICB0aGlzLmJpbmRFdmVudHMoKTtcclxuXHJcbiAgICAgICAgZ2MuZ2VvY29kZSh7J2xvY2F0aW9uJzogcG9pbnR9LCBmdW5jdGlvbiAocnMpIHtcclxuICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKHBvaW50LCBycyk7XHJcbiAgICAgICAgfSk7XHJcbiAgICAgICAgdGhpcy5pbmZvV2luZG93ID0gbmV3IGdvb2dsZS5tYXBzLkluZm9XaW5kb3coe21hcDogbWFwfSk7XHJcbiAgICAgICAgdGhpcy5pbmZvV2luZG93LnNldFBvc2l0aW9uKHBvaW50KTtcclxuXHJcbiAgICAgICAgdGhpcy5pc3Nob3cgPSB0cnVlO1xyXG4gICAgICAgIGlmICh0aGlzLnRvc2hvdykge1xyXG4gICAgICAgICAgICB0aGlzLnNob3dJbmZvKCk7XHJcbiAgICAgICAgICAgIHRoaXMudG9zaG93ID0gZmFsc2U7XHJcbiAgICAgICAgfVxyXG4gICAgfTtcclxuXHJcbiAgICBHb29nbGVNYXAucHJvdG90eXBlLnNob3dJbmZvID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIGlmICh0aGlzLmlzaGlkZSkgcmV0dXJuO1xyXG4gICAgICAgIGlmICghdGhpcy5pc3Nob3cpIHtcclxuICAgICAgICAgICAgdGhpcy50b3Nob3cgPSB0cnVlO1xyXG4gICAgICAgICAgICByZXR1cm47XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHRoaXMuaW5mb1dpbmRvdy5zZXRPcHRpb25zKHtwb3NpdGlvbjogdGhpcy5tYXJrZXIuZ2V0UG9zaXRpb24oKX0pO1xyXG4gICAgICAgIHRoaXMuc2V0SW5mb0NvbnRlbnQoKTtcclxuXHJcbiAgICB9O1xyXG4gICAgR29vZ2xlTWFwLnByb3RvdHlwZS5nZXRBZGRyZXNzID0gZnVuY3Rpb24gKHJzLCBzdGF0dXMpIHtcclxuICAgICAgICBpZiAocnMgJiYgcnNbMF0pIHtcclxuICAgICAgICAgICAgcmV0dXJuIHJzWzBdLmZvcm1hdHRlZF9hZGRyZXNzO1xyXG4gICAgICAgIH1cclxuICAgIH07XHJcbiAgICBHb29nbGVNYXAucHJvdG90eXBlLnNldExvY2F0ZSA9IGZ1bmN0aW9uIChhZGRyZXNzKSB7XHJcbiAgICAgICAgLy8g5Yib5bu65Zyw5Z2A6Kej5p6Q5Zmo5a6e5L6LXHJcbiAgICAgICAgdmFyIG15R2VvID0gbmV3IGdvb2dsZS5tYXBzLkdlb2NvZGVyKCk7XHJcbiAgICAgICAgdmFyIHNlbGYgPSB0aGlzO1xyXG4gICAgICAgIG15R2VvLmdldFBvaW50KGFkZHJlc3MsIGZ1bmN0aW9uIChwb2ludCkge1xyXG4gICAgICAgICAgICBpZiAocG9pbnQpIHtcclxuICAgICAgICAgICAgICAgIHNlbGYubWFwLmNlbnRlckFuZFpvb20ocG9pbnQsIDExKTtcclxuICAgICAgICAgICAgICAgIHNlbGYubWFya2VyLnNldFBvc2l0aW9uKHBvaW50KTtcclxuICAgICAgICAgICAgICAgIG15R2VvLmdldExvY2F0aW9uKHBvaW50LCBmdW5jdGlvbiAocnMpIHtcclxuICAgICAgICAgICAgICAgICAgICBzZWxmLnNob3dMb2NhdGlvbkluZm8ocG9pbnQsIHJzKTtcclxuICAgICAgICAgICAgICAgIH0pO1xyXG4gICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgdG9hc3RyLndhcm5pbmcoXCLlnLDlnYDkv6Hmga/kuI3mraPnoa7vvIzlrprkvY3lpLHotKVcIik7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9LCAnJyk7XHJcbiAgICB9O1xyXG5cclxuICAgIGZ1bmN0aW9uIFRlbmNlbnRNYXAoKSB7XHJcbiAgICAgICAgQmFzZU1hcC5jYWxsKHRoaXMsIFwidGVuY2VudFwiKTtcclxuICAgIH1cclxuXHJcbiAgICBUZW5jZW50TWFwLnByb3RvdHlwZSA9IG5ldyBCYXNlTWFwKCk7XHJcbiAgICBUZW5jZW50TWFwLnByb3RvdHlwZS5jb25zdHJ1Y3RvciA9IFRlbmNlbnRNYXA7XHJcbiAgICBUZW5jZW50TWFwLnByb3RvdHlwZS5pc0FQSVJlYWR5ID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHJldHVybiB3aW5kb3dbJ3FxJ10gJiYgd2luZG93WydxcSddWydtYXBzJ107XHJcbiAgICB9O1xyXG5cclxuICAgIFRlbmNlbnRNYXAucHJvdG90eXBlLnNldE1hcCA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICB2YXIgc2VsZiA9IHRoaXM7XHJcbiAgICAgICAgaWYgKHRoaXMuaXNzaG93IHx8IHRoaXMuaXNoaWRlKSByZXR1cm47XHJcbiAgICAgICAgaWYgKCF0aGlzLmxvYWRBUEkoKSkgcmV0dXJuO1xyXG5cclxuXHJcbiAgICAgICAgLy/liJ3lp4vljJblnLDlm75cclxuICAgICAgICB2YXIgbWFwID0gc2VsZi5tYXAgPSBuZXcgcXEubWFwcy5NYXAodGhpcy5tYXBib3hbMF0sIHt6b29tOiAxNX0pO1xyXG4gICAgICAgIC8v5Yid5aeL5YyW5Zyw5Zu+5o6n5Lu2XHJcbiAgICAgICAgbmV3IHFxLm1hcHMuU2NhbGVDb250cm9sKHtcclxuICAgICAgICAgICAgYWxpZ246IHFxLm1hcHMuQUxJR04uQk9UVE9NX0xFRlQsXHJcbiAgICAgICAgICAgIG1hcmdpbjogcXEubWFwcy5TaXplKDg1LCAxNSksXHJcbiAgICAgICAgICAgIG1hcDogbWFwXHJcbiAgICAgICAgfSk7XHJcbiAgICAgICAgLy9tYXAuYWRkQ29udHJvbChuZXcgQk1hcC5PdmVydmlld01hcENvbnRyb2woKSk7XHJcbiAgICAgICAgLy9tYXAuZW5hYmxlU2Nyb2xsV2hlZWxab29tKCk7XHJcblxyXG4gICAgICAgIC8v6I635Y+W57uP57qs5bqm5Z2Q5qCH5YC8XHJcbiAgICAgICAgdmFyIHBvaW50ID0gbmV3IHFxLm1hcHMuTGF0TG5nKHRoaXMubG9jYXRlLmxhdCwgdGhpcy5sb2NhdGUubG5nKTtcclxuICAgICAgICBtYXAucGFuVG8ocG9pbnQpOyAvL+WIneWni+WMluWcsOWbvuS4reW/g+eCuVxyXG5cclxuICAgICAgICAvL+WIneWni+WMluWcsOWbvuagh+iusFxyXG4gICAgICAgIHRoaXMubWFya2VyID0gbmV3IHFxLm1hcHMuTWFya2VyKHtcclxuICAgICAgICAgICAgcG9zaXRpb246IHBvaW50LFxyXG4gICAgICAgICAgICBkcmFnZ2FibGU6IHRydWUsXHJcbiAgICAgICAgICAgIG1hcDogbWFwXHJcbiAgICAgICAgfSk7XHJcbiAgICAgICAgdGhpcy5tYXJrZXIuc2V0QW5pbWF0aW9uKHFxLm1hcHMuTWFya2VyQW5pbWF0aW9uLkRPV04pO1xyXG5cclxuICAgICAgICAvL+WcsOWdgOino+aekOexu1xyXG4gICAgICAgIHZhciBnYyA9IG5ldyBxcS5tYXBzLkdlb2NvZGVyKHtcclxuICAgICAgICAgICAgY29tcGxldGU6IGZ1bmN0aW9uIChycykge1xyXG4gICAgICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKHBvaW50LCBycyk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgcXEubWFwcy5ldmVudC5hZGRMaXN0ZW5lcih0aGlzLm1hcmtlciwgJ2NsaWNrJywgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICBwb2ludCA9IHNlbGYubWFya2VyLmdldFBvc2l0aW9uKCk7XHJcbiAgICAgICAgICAgIGdjLmdldEFkZHJlc3MocG9pbnQpO1xyXG4gICAgICAgIH0pO1xyXG4gICAgICAgIC8v6K6+572uTWFya2Vy5YGc5q2i5ouW5Yqo5LqL5Lu2XHJcbiAgICAgICAgcXEubWFwcy5ldmVudC5hZGRMaXN0ZW5lcih0aGlzLm1hcmtlciwgJ2RyYWdlbmQnLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIHBvaW50ID0gc2VsZi5tYXJrZXIuZ2V0UG9zaXRpb24oKTtcclxuICAgICAgICAgICAgZ2MuZ2V0QWRkcmVzcyhwb2ludCk7XHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIGdjLmdldEFkZHJlc3MocG9pbnQpO1xyXG5cclxuICAgICAgICB0aGlzLmJpbmRFdmVudHMoKTtcclxuXHJcbiAgICAgICAgdGhpcy5pbmZvV2luZG93ID0gbmV3IHFxLm1hcHMuSW5mb1dpbmRvdyh7bWFwOiBtYXB9KTtcclxuXHJcbiAgICAgICAgdGhpcy5pc3Nob3cgPSB0cnVlO1xyXG4gICAgICAgIGlmICh0aGlzLnRvc2hvdykge1xyXG4gICAgICAgICAgICB0aGlzLnNob3dJbmZvKCk7XHJcbiAgICAgICAgICAgIHRoaXMudG9zaG93ID0gZmFsc2U7XHJcbiAgICAgICAgfVxyXG4gICAgfTtcclxuXHJcbiAgICBUZW5jZW50TWFwLnByb3RvdHlwZS5zaG93SW5mbyA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICBpZiAodGhpcy5pc2hpZGUpIHJldHVybjtcclxuICAgICAgICBpZiAoIXRoaXMuaXNzaG93KSB7XHJcbiAgICAgICAgICAgIHRoaXMudG9zaG93ID0gdHJ1ZTtcclxuICAgICAgICAgICAgcmV0dXJuO1xyXG4gICAgICAgIH1cclxuICAgICAgICB0aGlzLmluZm9XaW5kb3cub3BlbigpO1xyXG4gICAgICAgIHRoaXMuc2V0SW5mb0NvbnRlbnQoKTtcclxuICAgICAgICB0aGlzLmluZm9XaW5kb3cuc2V0UG9zaXRpb24odGhpcy5tYXJrZXIuZ2V0UG9zaXRpb24oKSk7XHJcbiAgICB9O1xyXG5cclxuICAgIFRlbmNlbnRNYXAucHJvdG90eXBlLmdldEFkZHJlc3MgPSBmdW5jdGlvbiAocnMpIHtcclxuICAgICAgICBpZihycyAmJiBycy5kZXRhaWwpIHtcclxuICAgICAgICAgICAgcmV0dXJuIHJzLmRldGFpbC5hZGRyZXNzO1xyXG4gICAgICAgIH1cclxuICAgIH07XHJcblxyXG4gICAgVGVuY2VudE1hcC5wcm90b3R5cGUuc2V0TG9jYXRlID0gZnVuY3Rpb24gKGFkZHJlc3MpIHtcclxuICAgICAgICAvLyDliJvlu7rlnLDlnYDop6PmnpDlmajlrp7kvotcclxuICAgICAgICB2YXIgc2VsZiA9IHRoaXM7XHJcbiAgICAgICAgdmFyIG15R2VvID0gbmV3IHFxLm1hcHMuR2VvY29kZXIoe1xyXG4gICAgICAgICAgICBjb21wbGV0ZTogZnVuY3Rpb24gKHJlc3VsdCkge1xyXG4gICAgICAgICAgICAgICAgaWYocmVzdWx0ICYmIHJlc3VsdC5kZXRhaWwgJiYgcmVzdWx0LmRldGFpbC5sb2NhdGlvbil7XHJcbiAgICAgICAgICAgICAgICAgICAgdmFyIHBvaW50PXJlc3VsdC5kZXRhaWwubG9jYXRpb247XHJcbiAgICAgICAgICAgICAgICAgICAgc2VsZi5tYXAuc2V0Q2VudGVyKHBvaW50KTtcclxuICAgICAgICAgICAgICAgICAgICBzZWxmLm1hcmtlci5zZXRQb3NpdGlvbihwb2ludCk7XHJcbiAgICAgICAgICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKHBvaW50LCByZXN1bHQpO1xyXG4gICAgICAgICAgICAgICAgfWVsc2V7XHJcbiAgICAgICAgICAgICAgICAgICAgdG9hc3RyLndhcm5pbmcoXCLlnLDlnYDkv6Hmga/kuI3mraPnoa7vvIzlrprkvY3lpLHotKVcIik7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgIGVycm9yOmZ1bmN0aW9uKHJlc3VsdCl7XHJcbiAgICAgICAgICAgICAgICB0b2FzdHIud2FybmluZyhcIuWcsOWdgOS/oeaBr+S4jeato+ehru+8jOWumuS9jeWksei0pVwiKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pO1xyXG4gICAgICAgIG15R2VvLmdldExvY2F0aW9uKGFkZHJlc3MpO1xyXG4gICAgfTtcclxuXHJcblxyXG4gICAgZnVuY3Rpb24gR2FvZGVNYXAoKSB7XHJcbiAgICAgICAgQmFzZU1hcC5jYWxsKHRoaXMsIFwiZ2FvZGVcIik7XHJcbiAgICAgICAgdGhpcy5pbmZvT3B0cyA9IHtcclxuICAgICAgICAgICAgd2lkdGg6IDI1MCwgICAgIC8v5L+h5oGv56qX5Y+j5a695bqmXHJcbiAgICAgICAgICAgIC8vICAgaGVpZ2h0OiAxMDAsICAgICAvL+S/oeaBr+eql+WPo+mrmOW6plxyXG4gICAgICAgICAgICB0aXRsZTogXCJcIiAgLy/kv6Hmga/nqpflj6PmoIfpophcclxuICAgICAgICB9O1xyXG4gICAgfVxyXG5cclxuICAgIEdhb2RlTWFwLnByb3RvdHlwZSA9IG5ldyBCYXNlTWFwKCk7XHJcbiAgICBHYW9kZU1hcC5wcm90b3R5cGUuY29uc3RydWN0b3IgPSBHYW9kZU1hcDtcclxuICAgIEdhb2RlTWFwLnByb3RvdHlwZS5pc0FQSVJlYWR5ID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHJldHVybiAhIXdpbmRvd1snQU1hcCddXHJcbiAgICB9O1xyXG5cclxuICAgIEdhb2RlTWFwLnByb3RvdHlwZS5zZXRNYXAgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgdmFyIHNlbGYgPSB0aGlzO1xyXG4gICAgICAgIGlmICh0aGlzLmlzc2hvdyB8fCB0aGlzLmlzaGlkZSkgcmV0dXJuO1xyXG4gICAgICAgIGlmICghdGhpcy5sb2FkQVBJKCkpIHJldHVybjtcclxuXHJcblxyXG4gICAgICAgIHZhciBtYXAgPSBzZWxmLm1hcCA9IG5ldyBBTWFwLk1hcCh0aGlzLm1hcGJveC5hdHRyKCdpZCcpLCB7XHJcbiAgICAgICAgICAgIHJlc2l6ZUVuYWJsZTogdHJ1ZSxcclxuICAgICAgICAgICAgZHJhZ0VuYWJsZTogdHJ1ZSxcclxuICAgICAgICAgICAgem9vbTogMTNcclxuICAgICAgICB9KTtcclxuICAgICAgICBtYXAucGx1Z2luKFtcIkFNYXAuVG9vbEJhclwiLCBcIkFNYXAuU2NhbGVcIiwgXCJBTWFwLk92ZXJWaWV3XCJdLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIG1hcC5hZGRDb250cm9sKG5ldyBBTWFwLlRvb2xCYXIoKSk7XHJcbiAgICAgICAgICAgIG1hcC5hZGRDb250cm9sKG5ldyBBTWFwLlNjYWxlKCkpO1xyXG4gICAgICAgICAgICBtYXAuYWRkQ29udHJvbChuZXcgQU1hcC5PdmVyVmlldygpKTtcclxuICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgJCgnW25hbWU9dHh0TGFuZ10nKS51bmJpbmQoKS5vbignY2hhbmdlJywgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICB2YXIgbGFuZyA9ICQodGhpcykudmFsKCk7XHJcbiAgICAgICAgICAgIGlmIChsYW5nKSBtYXAuc2V0TGFuZyhsYW5nKTtcclxuICAgICAgICB9KS50cmlnZ2VyKCdjaGFuZ2UnKTtcclxuXHJcblxyXG4gICAgICAgIC8v6I635Y+W57uP57qs5bqm5Z2Q5qCH5YC8XHJcbiAgICAgICAgdmFyIHBvaW50ID0gbmV3IEFNYXAuTG5nTGF0KHRoaXMubG9jYXRlLmxuZywgdGhpcy5sb2NhdGUubGF0KTtcclxuICAgICAgICBtYXAuc2V0Q2VudGVyKHBvaW50KTtcclxuXHJcbiAgICAgICAgdGhpcy5tYXJrZXIgPSBuZXcgQU1hcC5NYXJrZXIoe3Bvc2l0aW9uOiBwb2ludCwgbWFwOiBtYXB9KTsgLy/liJ3lp4vljJblnLDlm77moIforrBcclxuICAgICAgICB0aGlzLm1hcmtlci5zZXREcmFnZ2FibGUodHJ1ZSk7IC8v5qCH6K6w5byA5ZCv5ouW5ou9XHJcblxyXG5cclxuICAgICAgICB0aGlzLmluZm9XaW5kb3cgPSBuZXcgQU1hcC5JbmZvV2luZG93KCk7XHJcbiAgICAgICAgdGhpcy5pbmZvV2luZG93Lm9wZW4obWFwLCBwb2ludCk7XHJcblxyXG4gICAgICAgIG1hcC5wbHVnaW4oW1wiQU1hcC5HZW9jb2RlclwiXSwgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICB2YXIgZ2MgPSBuZXcgQU1hcC5HZW9jb2RlcigpOyAvL+WcsOWdgOino+aekOexu1xyXG4gICAgICAgICAgICAvL+a3u+WKoOagh+iusOaLluaLveebkeWQrFxyXG4gICAgICAgICAgICBzZWxmLm1hcmtlci5vbihcImRyYWdlbmRcIiwgZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICAgICAgICAgIC8v6I635Y+W5Zyw5Z2A5L+h5oGvXHJcbiAgICAgICAgICAgICAgICBnYy5nZXRBZGRyZXNzKGUubG5nbGF0LCBmdW5jdGlvbiAoc3QsIHJzKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKGUubG5nbGF0LCBycyk7XHJcbiAgICAgICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgfSk7XHJcblxyXG4gICAgICAgICAgICAvL+a3u+WKoOagh+iusOeCueWHu+ebkeWQrFxyXG4gICAgICAgICAgICBzZWxmLm1hcmtlci5vbihcImNsaWNrXCIsIGZ1bmN0aW9uIChlKSB7XHJcbiAgICAgICAgICAgICAgICBnYy5nZXRBZGRyZXNzKGUubG5nbGF0LCBmdW5jdGlvbiAoc3QsIHJzKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKGUubG5nbGF0LCBycyk7XHJcbiAgICAgICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgfSk7XHJcblxyXG4gICAgICAgICAgICBnYy5nZXRBZGRyZXNzKHBvaW50LCBmdW5jdGlvbiAoc3QsIHJzKSB7XHJcbiAgICAgICAgICAgICAgICBzZWxmLnNob3dMb2NhdGlvbkluZm8ocG9pbnQsIHJzKTtcclxuICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIHRoaXMuYmluZEV2ZW50cygpO1xyXG5cclxuICAgICAgICB0aGlzLmlzc2hvdyA9IHRydWU7XHJcbiAgICAgICAgaWYgKHRoaXMudG9zaG93KSB7XHJcbiAgICAgICAgICAgIHRoaXMuc2hvd0luZm8oKTtcclxuICAgICAgICAgICAgdGhpcy50b3Nob3cgPSBmYWxzZTtcclxuICAgICAgICB9XHJcbiAgICB9O1xyXG5cclxuICAgIEdhb2RlTWFwLnByb3RvdHlwZS5zaG93SW5mbyA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICBpZiAodGhpcy5pc2hpZGUpIHJldHVybjtcclxuICAgICAgICBpZiAoIXRoaXMuaXNzaG93KSB7XHJcbiAgICAgICAgICAgIHRoaXMudG9zaG93ID0gdHJ1ZTtcclxuICAgICAgICAgICAgcmV0dXJuO1xyXG4gICAgICAgIH1cclxuICAgICAgICB0aGlzLnNldEluZm9Db250ZW50KCk7XHJcbiAgICAgICAgdGhpcy5pbmZvV2luZG93LnNldFBvc2l0aW9uKHRoaXMubWFya2VyLmdldFBvc2l0aW9uKCkpO1xyXG4gICAgfTtcclxuXHJcbiAgICBHYW9kZU1hcC5wcm90b3R5cGUuZ2V0QWRkcmVzcyA9IGZ1bmN0aW9uIChycykge1xyXG4gICAgICAgIHJldHVybiBycy5yZWdlb2NvZGUuZm9ybWF0dGVkQWRkcmVzcztcclxuICAgIH07XHJcblxyXG4gICAgR2FvZGVNYXAucHJvdG90eXBlLnNldExvY2F0ZSA9IGZ1bmN0aW9uIChhZGRyZXNzKSB7XHJcbiAgICAgICAgLy8g5Yib5bu65Zyw5Z2A6Kej5p6Q5Zmo5a6e5L6LXHJcbiAgICAgICAgdmFyIG15R2VvID0gbmV3IEFNYXAuR2VvY29kZXIoKTtcclxuICAgICAgICB2YXIgc2VsZiA9IHRoaXM7XHJcbiAgICAgICAgbXlHZW8uZ2V0UG9pbnQoYWRkcmVzcywgZnVuY3Rpb24gKHBvaW50KSB7XHJcbiAgICAgICAgICAgIGlmIChwb2ludCkge1xyXG4gICAgICAgICAgICAgICAgc2VsZi5tYXAuY2VudGVyQW5kWm9vbShwb2ludCwgMTEpO1xyXG4gICAgICAgICAgICAgICAgc2VsZi5tYXJrZXIuc2V0UG9zaXRpb24ocG9pbnQpO1xyXG4gICAgICAgICAgICAgICAgbXlHZW8uZ2V0TG9jYXRpb24ocG9pbnQsIGZ1bmN0aW9uIChycykge1xyXG4gICAgICAgICAgICAgICAgICAgIHNlbGYuc2hvd0xvY2F0aW9uSW5mbyhwb2ludCwgcnMpO1xyXG4gICAgICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICB0b2FzdHIud2FybmluZyhcIuWcsOWdgOS/oeaBr+S4jeato+ehru+8jOWumuS9jeWksei0pVwiKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0sICcnKTtcclxuICAgIH07XHJcblxyXG4gICAgd2luZG93LkluaXRNYXA9SW5pdE1hcDtcclxufSkod2luZG93LGpRdWVyeSk7IiwialF1ZXJ5KGZ1bmN0aW9uICgkKSB7XHJcbiAgICAvL+mrmOS6ruW9k+WJjemAieS4reeahOWvvOiIqlxyXG4gICAgdmFyIGJyZWFkID0gJChcIi5icmVhZGNydW1iXCIpO1xyXG4gICAgdmFyIG1lbnUgPSBicmVhZC5kYXRhKCdtZW51Jyk7XHJcbiAgICBpZiAobWVudSkge1xyXG4gICAgICAgIHZhciBsaW5rID0gJCgnLnNpZGUtbmF2IGFbZGF0YS1rZXk9JyArIG1lbnUgKyAnXScpO1xyXG5cclxuICAgICAgICB2YXIgaHRtbCA9IFtdO1xyXG4gICAgICAgIGlmIChsaW5rLmxlbmd0aCA+IDApIHtcclxuICAgICAgICAgICAgaWYgKGxpbmsuaXMoJy5tZW51X3RvcCcpKSB7XHJcbiAgICAgICAgICAgICAgICBodG1sLnB1c2goJzxsaSBjbGFzcz1cImJyZWFkY3J1bWItaXRlbVwiPjxhIGhyZWY9XCJqYXZhc2NyaXB0OlwiPjxpIGNsYXNzPVwiJyArIGxpbmsuZmluZCgnaScpLmF0dHIoJ2NsYXNzJykgKyAnXCI+PC9pPiZuYnNwOycgKyBsaW5rLnRleHQoKSArICc8L2E+PC9saT4nKTtcclxuICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgIHZhciBwYXJlbnQgPSBsaW5rLnBhcmVudHMoJy5jb2xsYXBzZScpLmVxKDApO1xyXG4gICAgICAgICAgICAgICAgcGFyZW50LmFkZENsYXNzKCdzaG93Jyk7XHJcbiAgICAgICAgICAgICAgICBsaW5rLmFkZENsYXNzKFwiYWN0aXZlXCIpO1xyXG4gICAgICAgICAgICAgICAgdmFyIHRvcG1lbnUgPSBwYXJlbnQuc2libGluZ3MoJy5jYXJkLWhlYWRlcicpLmZpbmQoJ2EubWVudV90b3AnKTtcclxuICAgICAgICAgICAgICAgIGh0bWwucHVzaCgnPGxpIGNsYXNzPVwiYnJlYWRjcnVtYi1pdGVtXCI+PGEgaHJlZj1cImphdmFzY3JpcHQ6XCI+PGkgY2xhc3M9XCInICsgdG9wbWVudS5maW5kKCdpJykuYXR0cignY2xhc3MnKSArICdcIj48L2k+Jm5ic3A7JyArIHRvcG1lbnUudGV4dCgpICsgJzwvYT48L2xpPicpO1xyXG4gICAgICAgICAgICAgICAgaHRtbC5wdXNoKCc8bGkgY2xhc3M9XCJicmVhZGNydW1iLWl0ZW1cIj48YSBocmVmPVwiamF2YXNjcmlwdDpcIj4nICsgbGluay50ZXh0KCkgKyAnPC9hPjwvbGk+Jyk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9XHJcbiAgICAgICAgdmFyIHRpdGxlID0gYnJlYWQuZGF0YSgndGl0bGUnKTtcclxuICAgICAgICBpZiAodGl0bGUpIHtcclxuICAgICAgICAgICAgaHRtbC5wdXNoKCc8bGkgY2xhc3M9XCJicmVhZGNydW1iLWl0ZW0gYWN0aXZlXCIgYXJpYS1jdXJyZW50PVwicGFnZVwiPicgKyB0aXRsZSArICc8L2xpPicpO1xyXG4gICAgICAgIH1cclxuICAgICAgICBicmVhZC5odG1sKGh0bWwuam9pbihcIlxcblwiKSk7XHJcbiAgICB9XHJcblxyXG4gICAgLy/lhajpgInjgIHlj43pgInmjInpkq5cclxuICAgICQoJy5jaGVja2FsbC1idG4nKS5jbGljayhmdW5jdGlvbiAoZSkge1xyXG4gICAgICAgIHZhciB0YXJnZXQgPSAkKHRoaXMpLmRhdGEoJ3RhcmdldCcpO1xyXG4gICAgICAgIGlmICghdGFyZ2V0KSB0YXJnZXQgPSAnaWQnO1xyXG4gICAgICAgIHZhciBpZHMgPSAkKCdbbmFtZT0nICsgdGFyZ2V0ICsgJ10nKTtcclxuICAgICAgICBpZiAoJCh0aGlzKS5pcygnLmFjdGl2ZScpKSB7XHJcbiAgICAgICAgICAgIGlkcy5yZW1vdmVBdHRyKCdjaGVja2VkJyk7XHJcbiAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgaWRzLmF0dHIoJ2NoZWNrZWQnLCB0cnVlKTtcclxuICAgICAgICB9XHJcbiAgICB9KTtcclxuICAgICQoJy5jaGVja3JldmVyc2UtYnRuJykuY2xpY2soZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICB2YXIgdGFyZ2V0ID0gJCh0aGlzKS5kYXRhKCd0YXJnZXQnKTtcclxuICAgICAgICBpZiAoIXRhcmdldCkgdGFyZ2V0ID0gJ2lkJztcclxuICAgICAgICB2YXIgaWRzID0gJCgnW25hbWU9JyArIHRhcmdldCArICddJyk7XHJcbiAgICAgICAgZm9yICh2YXIgaSA9IDA7IGkgPCBpZHMubGVuZ3RoOyBpKyspIHtcclxuICAgICAgICAgICAgaWYgKGlkc1tpXS5jaGVja2VkKSB7XHJcbiAgICAgICAgICAgICAgICBpZHMuZXEoaSkucmVtb3ZlQXR0cignY2hlY2tlZCcpO1xyXG4gICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgaWRzLmVxKGkpLmF0dHIoJ2NoZWNrZWQnLCB0cnVlKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH1cclxuICAgIH0pO1xyXG4gICAgLy/mk43kvZzmjInpkq5cclxuICAgICQoJy5hY3Rpb24tYnRuJykuY2xpY2soZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XHJcbiAgICAgICAgdmFyIGFjdGlvbiA9ICQodGhpcykuZGF0YSgnYWN0aW9uJyk7XHJcbiAgICAgICAgaWYgKCFhY3Rpb24pIHtcclxuICAgICAgICAgICAgcmV0dXJuIHRvYXN0ci5lcnJvcign5pyq55+l5pON5L2cJyk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIGFjdGlvbiA9ICdhY3Rpb24nICsgYWN0aW9uLnJlcGxhY2UoL15bYS16XS8sIGZ1bmN0aW9uIChsZXR0ZXIpIHtcclxuICAgICAgICAgICAgcmV0dXJuIGxldHRlci50b1VwcGVyQ2FzZSgpO1xyXG4gICAgICAgIH0pO1xyXG4gICAgICAgIGlmICghd2luZG93W2FjdGlvbl0gfHwgdHlwZW9mIHdpbmRvd1thY3Rpb25dICE9PSAnZnVuY3Rpb24nKSB7XHJcbiAgICAgICAgICAgIHJldHVybiB0b2FzdHIuZXJyb3IoJ+acquefpeaTjeS9nCcpO1xyXG4gICAgICAgIH1cclxuICAgICAgICB2YXIgbmVlZENoZWNrcyA9ICQodGhpcykuZGF0YSgnbmVlZENoZWNrcycpO1xyXG4gICAgICAgIGlmIChuZWVkQ2hlY2tzID09PSB1bmRlZmluZWQpIG5lZWRDaGVja3MgPSB0cnVlO1xyXG4gICAgICAgIGlmIChuZWVkQ2hlY2tzKSB7XHJcbiAgICAgICAgICAgIHZhciB0YXJnZXQgPSAkKHRoaXMpLmRhdGEoJ3RhcmdldCcpO1xyXG4gICAgICAgICAgICBpZiAoIXRhcmdldCkgdGFyZ2V0ID0gJ2lkJztcclxuICAgICAgICAgICAgdmFyIGlkcyA9ICQoJ1tuYW1lPScgKyB0YXJnZXQgKyAnXTpjaGVja2VkJyk7XHJcbiAgICAgICAgICAgIGlmIChpZHMubGVuZ3RoIDwgMSkge1xyXG4gICAgICAgICAgICAgICAgcmV0dXJuIHRvYXN0ci53YXJuaW5nKCfor7fpgInmi6npnIDopoHmk43kvZznmoTpobnnm64nKTtcclxuICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgIHZhciBpZGNoZWNrcyA9IFtdO1xyXG4gICAgICAgICAgICAgICAgZm9yICh2YXIgaSA9IDA7IGkgPCBpZHMubGVuZ3RoOyBpKyspIHtcclxuICAgICAgICAgICAgICAgICAgICBpZGNoZWNrcy5wdXNoKGlkcy5lcShpKS52YWwoKSk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICB3aW5kb3dbYWN0aW9uXShpZGNoZWNrcyk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICB3aW5kb3dbYWN0aW9uXSgpO1xyXG4gICAgICAgIH1cclxuICAgIH0pO1xyXG5cclxuICAgIC8v5byC5q2l5pi+56S66LWE5paZ6ZO+5o6lXHJcbiAgICAkKCdhW3JlbD1hamF4XScpLmNsaWNrKGZ1bmN0aW9uIChlKSB7XHJcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xyXG4gICAgICAgIHZhciBzZWxmID0gJCh0aGlzKTtcclxuICAgICAgICB2YXIgdGl0bGUgPSAkKHRoaXMpLmRhdGEoJ3RpdGxlJyk7XHJcbiAgICAgICAgaWYgKCF0aXRsZSkgdGl0bGUgPSAkKHRoaXMpLnRleHQoKTtcclxuICAgICAgICB2YXIgZGxnID0gbmV3IERpYWxvZyh7XHJcbiAgICAgICAgICAgIGJ0bnM6IFsn56Gu5a6aJ10sXHJcbiAgICAgICAgICAgIG9uc2hvdzogZnVuY3Rpb24gKGJvZHkpIHtcclxuICAgICAgICAgICAgICAgICQuYWpheCh7XHJcbiAgICAgICAgICAgICAgICAgICAgdXJsOiBzZWxmLmF0dHIoJ2hyZWYnKSxcclxuICAgICAgICAgICAgICAgICAgICBzdWNjZXNzOiBmdW5jdGlvbiAodGV4dCkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBib2R5Lmh0bWwodGV4dCk7XHJcbiAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS5zaG93KCc8cCBjbGFzcz1cImxvYWRpbmdcIj7liqDovb3kuK0uLi48L3A+JywgdGl0bGUpO1xyXG5cclxuICAgIH0pO1xyXG5cclxuICAgICQoJy5uYXYtdGFicyBhJykuY2xpY2soZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XHJcbiAgICAgICAgJCh0aGlzKS50YWIoJ3Nob3cnKTtcclxuICAgIH0pO1xyXG5cclxuICAgIC8v5LiK5Lyg5qGGXHJcbiAgICAkKCcuY3VzdG9tLWZpbGUgLmN1c3RvbS1maWxlLWlucHV0Jykub24oJ2NoYW5nZScsIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICB2YXIgbGFiZWwgPSAkKHRoaXMpLnBhcmVudHMoJy5jdXN0b20tZmlsZScpLmZpbmQoJy5jdXN0b20tZmlsZS1sYWJlbCcpO1xyXG4gICAgICAgIGxhYmVsLnRleHQoJCh0aGlzKS52YWwoKSk7XHJcbiAgICB9KTtcclxuXHJcbiAgICAvL+ihqOWNlUFqYXjmj5DkuqRcclxuICAgICQoJy5idG4tcHJpbWFyeVt0eXBlPXN1Ym1pdF0nKS5jbGljayhmdW5jdGlvbiAoZSkge1xyXG4gICAgICAgIHZhciBmb3JtID0gJCh0aGlzKS5wYXJlbnRzKCdmb3JtJyk7XHJcbiAgICAgICAgdmFyIGJ0biA9IHRoaXM7XHJcbiAgICAgICAgdmFyIG9wdGlvbnMgPSB7XHJcbiAgICAgICAgICAgIHVybDogJChmb3JtKS5hdHRyKCdhY3Rpb24nKSxcclxuICAgICAgICAgICAgdHlwZTogJ1BPU1QnLFxyXG4gICAgICAgICAgICBkYXRhVHlwZTogJ0pTT04nLFxyXG4gICAgICAgICAgICBzdWNjZXNzOiBmdW5jdGlvbiAoanNvbikge1xyXG4gICAgICAgICAgICAgICAgaWYgKGpzb24uY29kZSA9PSAxKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgbmV3IERpYWxvZyh7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIG9uaGlkZGVuOiBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAoanNvbi51cmwpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBsb2NhdGlvbi5ocmVmID0ganNvbi51cmw7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxvY2F0aW9uLnJlbG9hZCgpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgfSkuc2hvdyhqc29uLm1zZyk7XHJcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgICAgIHRvYXN0ci53YXJuaW5nKGpzb24ubXNnKTtcclxuICAgICAgICAgICAgICAgICAgICAkKGJ0bikucmVtb3ZlQXR0cignZGlzYWJsZWQnKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH07XHJcbiAgICAgICAgaWYgKGZvcm0uYXR0cignZW5jdHlwZScpID09PSAnbXVsdGlwYXJ0L2Zvcm0tZGF0YScpIHtcclxuICAgICAgICAgICAgaWYgKCFGb3JtRGF0YSkge1xyXG4gICAgICAgICAgICAgICAgcmV0dXJuIHRydWU7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgb3B0aW9ucy5kYXRhID0gbmV3IEZvcm1EYXRhKGZvcm1bMF0pO1xyXG4gICAgICAgICAgICBvcHRpb25zLmNhY2hlID0gZmFsc2U7XHJcbiAgICAgICAgICAgIG9wdGlvbnMucHJvY2Vzc0RhdGEgPSBmYWxzZTtcclxuICAgICAgICAgICAgb3B0aW9ucy5jb250ZW50VHlwZSA9IGZhbHNlO1xyXG4gICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgIG9wdGlvbnMuZGF0YSA9ICQoZm9ybSkuc2VyaWFsaXplKCk7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XHJcbiAgICAgICAgJCh0aGlzKS5hdHRyKCdkaXNhYmxlZCcsIHRydWUpO1xyXG4gICAgICAgICQuYWpheChvcHRpb25zKTtcclxuICAgIH0pO1xyXG5cclxuICAgICQoJy5waWNrdXNlcicpLmNsaWNrKGZ1bmN0aW9uIChlKSB7XHJcbiAgICAgICAgdmFyIGdyb3VwID0gJCh0aGlzKS5wYXJlbnRzKCcuaW5wdXQtZ3JvdXAnKTtcclxuICAgICAgICB2YXIgaWRlbGUgPSBncm91cC5maW5kKCdbbmFtZT1tZW1iZXJfaWRdJyk7XHJcbiAgICAgICAgdmFyIGluZm9lbGUgPSBncm91cC5maW5kKCdbbmFtZT1tZW1iZXJfaW5mb10nKTtcclxuICAgICAgICBkaWFsb2cucGlja1VzZXIoJCh0aGlzKS5kYXRhKCd1cmwnKSwgZnVuY3Rpb24gKHVzZXIpIHtcclxuICAgICAgICAgICAgaWRlbGUudmFsKHVzZXIuaWQpO1xyXG4gICAgICAgICAgICBpbmZvZWxlLnZhbCgnWycgKyB1c2VyLmlkICsgJ10gJyArIHVzZXIudXNlcm5hbWUgKyAodXNlci5tb2JpbGUgPyAoJyAvICcgKyB1c2VyLm1vYmlsZSkgOiAnJykpO1xyXG4gICAgICAgIH0sICQodGhpcykuZGF0YSgnZmlsdGVyJykpO1xyXG4gICAgfSk7XHJcbiAgICAkKCcucGljay1sb2NhdGUnKS5jbGljayhmdW5jdGlvbihlKXtcclxuICAgICAgICB2YXIgZ3JvdXA9JCh0aGlzKS5wYXJlbnRzKCcuaW5wdXQtZ3JvdXAnKTtcclxuICAgICAgICB2YXIgaWRlbGU9Z3JvdXAuZmluZCgnaW5wdXRbdHlwZT10ZXh0XScpO1xyXG4gICAgICAgIGRpYWxvZy5waWNrTG9jYXRlKCdxcScsZnVuY3Rpb24obG9jYXRlKXtcclxuICAgICAgICAgICAgaWRlbGUudmFsKGxvY2F0ZS5sbmcrJywnK2xvY2F0ZS5sYXQpO1xyXG4gICAgICAgIH0saWRlbGUudmFsKCkpO1xyXG4gICAgfSk7XHJcbn0pOyJdfQ==
