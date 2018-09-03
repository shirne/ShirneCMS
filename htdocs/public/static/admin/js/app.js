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

function copy_obj(arr){
    return JSON.parse(JSON.stringify(arr));
}

function isObjectValueEqual(a, b) {
    if(!a && !b)return true;
    if(!a || !b)return false;

    // Of course, we can do it use for in
    // Create arrays of property names
    var aProps = Object.getOwnPropertyNames(a);
    var bProps = Object.getOwnPropertyNames(b);

    // If number of properties is different,
    // objects are not equivalent
    if (aProps.length != bProps.length) {
        return false;
    }

    for (var i = 0; i < aProps.length; i++) {
        var propName = aProps[i];

        // If values of same property are not equal,
        // objects are not equivalent
        if (a[propName] !== b[propName]) {
            return false;
        }
    }

    // If we made it this far, objects
    // are considered equivalent
    return true;
}

function array_combine(a,b) {
    var obj={};
    for(var i=0;i<a.length;i++){
        if(b.length<i+1)break;
        obj[a[i]]=b[i];
    }
    return obj;
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
                    if(val[keys[i]]!==undefined){
                        val=val[keys[i]];
                    }else{
                        val = '';
                        break;
                    }
                }
                return callfunc(val,func,args);
            }else{
                return data[m1]!==undefined?callfunc(data[m1],func,args,data):'';
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
    tags:function(nm,onupdate){
        var data=[];
        var tpl='<span class="badge badge-info">{@label}<input type="hidden" name="'+nm+'" value="{@label}"/><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></span>';
        var item=$(this).parents('.form-control');
        var labelgroup=$('<span class="badge-group"></span>');
        var input=this;
        this.before(labelgroup);
        this.on('keyup',function(){
            var val=$(this).val().replace(/，/g,',');
            var updated=false;
            if(val && val.indexOf(',')>-1){
                var vals=val.split(',');
                for(var i=0;i<vals.length;i++){
                    vals[i]=vals[i].replace(/^\s|\s$/g,'');
                    if(vals[i] && data.indexOf(vals[i])===-1){
                        data.push(vals[i]);
                        labelgroup.append(tpl.compile({label:vals[i]}));
                        updated=true;
                    }
                }
                input.val('');
                if(updated && onupdate)onupdate(data);
            }
        }).on('blur',function(){
            var val=$(this).val();
            if(val) {
                $(this).val(val + ',').trigger('keyup');
            }
        }).trigger('blur');
        labelgroup.on('click','.close',function(){
            var tag=$(this).parents('.badge').find('input').val();
            var id=data.indexOf(tag);
            if(id)data.splice(id,1);
            $(this).parents('.badge').remove();
            if(onupdate)onupdate(data);
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
    $('.datepicker').each(function(){
        var config=$.extend({
            icons:icons,
            tooltips:tooltips,
            format: 'YYYY-MM-DD',
            locale: 'zh-cn',
            showClear:true,
            showTodayButton:true,
            showClose:true,
            keepInvalid:true
        },$(this).data());
        $(this).datetimepicker(config);
    });

    $('.date-range').each(function () {
        var from = $(this).find('[name=fromdate],.fromdate'), to = $(this).find('[name=todate],.todate');
        var options = $.extend({
            icons:icons,
            tooltips:tooltips,
            format: 'YYYY-MM-DD',
            locale:'zh-cn',
            showClear:true,
            showTodayButton:true,
            showClose:true,
            keepInvalid:true
        },$(this).data());
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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNvbW1vbi5qcyIsInRlbXBsYXRlLmpzIiwiZGlhbG9nLmpzIiwianF1ZXJ5LnRhZy5qcyIsImRhdGV0aW1lLmluaXQuanMiLCJtYXAuanMiLCJiYWNrZW5kLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUMxREE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDOURBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDeFRBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDMUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDbEVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDMWpCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBIiwiZmlsZSI6ImJhY2tlbmQuanMiLCJzb3VyY2VzQ29udGVudCI6WyJmdW5jdGlvbiBkZWwob2JqLG1zZykge1xyXG4gICAgZGlhbG9nLmNvbmZpcm0obXNnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgbG9jYXRpb24uaHJlZj0kKG9iaikuYXR0cignaHJlZicpO1xyXG4gICAgfSk7XHJcbiAgICByZXR1cm4gZmFsc2U7XHJcbn1cclxuXHJcbmZ1bmN0aW9uIHJhbmRvbVN0cmluZyhsZW4sIGNoYXJTZXQpIHtcclxuICAgIGNoYXJTZXQgPSBjaGFyU2V0IHx8ICdBQkNERUZHSElKS0xNTk9QUVJTVFVWV1hZWmFiY2RlZmdoaWprbG1ub3BxcnN0dXZ3eHl6MDEyMzQ1Njc4OSc7XHJcbiAgICB2YXIgc3RyID0gJycsYWxsTGVuPWNoYXJTZXQubGVuZ3RoO1xyXG4gICAgZm9yICh2YXIgaSA9IDA7IGkgPCBsZW47IGkrKykge1xyXG4gICAgICAgIHZhciByYW5kb21Qb3ogPSBNYXRoLmZsb29yKE1hdGgucmFuZG9tKCkgKiBhbGxMZW4pO1xyXG4gICAgICAgIHN0ciArPSBjaGFyU2V0LnN1YnN0cmluZyhyYW5kb21Qb3oscmFuZG9tUG96KzEpO1xyXG4gICAgfVxyXG4gICAgcmV0dXJuIHN0cjtcclxufVxyXG5cclxuZnVuY3Rpb24gY29weV9vYmooYXJyKXtcclxuICAgIHJldHVybiBKU09OLnBhcnNlKEpTT04uc3RyaW5naWZ5KGFycikpO1xyXG59XHJcblxyXG5mdW5jdGlvbiBpc09iamVjdFZhbHVlRXF1YWwoYSwgYikge1xyXG4gICAgaWYoIWEgJiYgIWIpcmV0dXJuIHRydWU7XHJcbiAgICBpZighYSB8fCAhYilyZXR1cm4gZmFsc2U7XHJcblxyXG4gICAgLy8gT2YgY291cnNlLCB3ZSBjYW4gZG8gaXQgdXNlIGZvciBpblxyXG4gICAgLy8gQ3JlYXRlIGFycmF5cyBvZiBwcm9wZXJ0eSBuYW1lc1xyXG4gICAgdmFyIGFQcm9wcyA9IE9iamVjdC5nZXRPd25Qcm9wZXJ0eU5hbWVzKGEpO1xyXG4gICAgdmFyIGJQcm9wcyA9IE9iamVjdC5nZXRPd25Qcm9wZXJ0eU5hbWVzKGIpO1xyXG5cclxuICAgIC8vIElmIG51bWJlciBvZiBwcm9wZXJ0aWVzIGlzIGRpZmZlcmVudCxcclxuICAgIC8vIG9iamVjdHMgYXJlIG5vdCBlcXVpdmFsZW50XHJcbiAgICBpZiAoYVByb3BzLmxlbmd0aCAhPSBiUHJvcHMubGVuZ3RoKSB7XHJcbiAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgfVxyXG5cclxuICAgIGZvciAodmFyIGkgPSAwOyBpIDwgYVByb3BzLmxlbmd0aDsgaSsrKSB7XHJcbiAgICAgICAgdmFyIHByb3BOYW1lID0gYVByb3BzW2ldO1xyXG5cclxuICAgICAgICAvLyBJZiB2YWx1ZXMgb2Ygc2FtZSBwcm9wZXJ0eSBhcmUgbm90IGVxdWFsLFxyXG4gICAgICAgIC8vIG9iamVjdHMgYXJlIG5vdCBlcXVpdmFsZW50XHJcbiAgICAgICAgaWYgKGFbcHJvcE5hbWVdICE9PSBiW3Byb3BOYW1lXSkge1xyXG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICAgICAgfVxyXG4gICAgfVxyXG5cclxuICAgIC8vIElmIHdlIG1hZGUgaXQgdGhpcyBmYXIsIG9iamVjdHNcclxuICAgIC8vIGFyZSBjb25zaWRlcmVkIGVxdWl2YWxlbnRcclxuICAgIHJldHVybiB0cnVlO1xyXG59XHJcblxyXG5mdW5jdGlvbiBhcnJheV9jb21iaW5lKGEsYikge1xyXG4gICAgdmFyIG9iaj17fTtcclxuICAgIGZvcih2YXIgaT0wO2k8YS5sZW5ndGg7aSsrKXtcclxuICAgICAgICBpZihiLmxlbmd0aDxpKzEpYnJlYWs7XHJcbiAgICAgICAgb2JqW2FbaV1dPWJbaV07XHJcbiAgICB9XHJcbiAgICByZXR1cm4gb2JqO1xyXG59IiwiXHJcbk51bWJlci5wcm90b3R5cGUuZm9ybWF0PWZ1bmN0aW9uKGZpeCl7XHJcbiAgICBpZihmaXg9PT11bmRlZmluZWQpZml4PTI7XHJcbiAgICB2YXIgbnVtPXRoaXMudG9GaXhlZChmaXgpO1xyXG4gICAgdmFyIHo9bnVtLnNwbGl0KCcuJyk7XHJcbiAgICB2YXIgZm9ybWF0PVtdLGY9elswXS5zcGxpdCgnJyksbD1mLmxlbmd0aDtcclxuICAgIGZvcih2YXIgaT0wO2k8bDtpKyspe1xyXG4gICAgICAgIGlmKGk+MCAmJiBpICUgMz09MCl7XHJcbiAgICAgICAgICAgIGZvcm1hdC51bnNoaWZ0KCcsJyk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIGZvcm1hdC51bnNoaWZ0KGZbbC1pLTFdKTtcclxuICAgIH1cclxuICAgIHJldHVybiBmb3JtYXQuam9pbignJykrKHoubGVuZ3RoPT0yPycuJyt6WzFdOicnKTtcclxufTtcclxuU3RyaW5nLnByb3RvdHlwZS5jb21waWxlPWZ1bmN0aW9uKGRhdGEsbGlzdCl7XHJcblxyXG4gICAgaWYobGlzdCl7XHJcbiAgICAgICAgdmFyIHRlbXBzPVtdO1xyXG4gICAgICAgIGZvcih2YXIgaSBpbiBkYXRhKXtcclxuICAgICAgICAgICAgdGVtcHMucHVzaCh0aGlzLmNvbXBpbGUoZGF0YVtpXSkpO1xyXG4gICAgICAgIH1cclxuICAgICAgICByZXR1cm4gdGVtcHMuam9pbihcIlxcblwiKTtcclxuICAgIH1lbHNle1xyXG4gICAgICAgIHJldHVybiB0aGlzLnJlcGxhY2UoL1xce0AoW1xcd1xcZFxcLl0rKSg/OlxcfChbXFx3XFxkXSspKD86XFxzKj1cXHMqKFtcXHdcXGQsXFxzI10rKSk/KT9cXH0vZyxmdW5jdGlvbihhbGwsbTEsZnVuYyxhcmdzKXtcclxuXHJcbiAgICAgICAgICAgIGlmKG0xLmluZGV4T2YoJy4nKT4wKXtcclxuICAgICAgICAgICAgICAgIHZhciBrZXlzPW0xLnNwbGl0KCcuJyksdmFsPWRhdGE7XHJcbiAgICAgICAgICAgICAgICBmb3IodmFyIGk9MDtpPGtleXMubGVuZ3RoO2krKyl7XHJcbiAgICAgICAgICAgICAgICAgICAgaWYodmFsW2tleXNbaV1dIT09dW5kZWZpbmVkKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgdmFsPXZhbFtrZXlzW2ldXTtcclxuICAgICAgICAgICAgICAgICAgICB9ZWxzZXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgdmFsID0gJyc7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIHJldHVybiBjYWxsZnVuYyh2YWwsZnVuYyxhcmdzKTtcclxuICAgICAgICAgICAgfWVsc2V7XHJcbiAgICAgICAgICAgICAgICByZXR1cm4gZGF0YVttMV0hPT11bmRlZmluZWQ/Y2FsbGZ1bmMoZGF0YVttMV0sZnVuYyxhcmdzLGRhdGEpOicnO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSk7XHJcbiAgICB9XHJcbn07XHJcblxyXG5mdW5jdGlvbiBjYWxsZnVuYyh2YWwsZnVuYyxhcmdzLHRoaXNvYmope1xyXG4gICAgaWYoIWFyZ3Mpe1xyXG4gICAgICAgIGFyZ3M9W3ZhbF07XHJcbiAgICB9ZWxzZXtcclxuICAgICAgICBpZih0eXBlb2YgYXJncz09PSdzdHJpbmcnKWFyZ3M9YXJncy5zcGxpdCgnLCcpO1xyXG4gICAgICAgIHZhciBhcmdpZHg9YXJncy5pbmRleE9mKCcjIyMnKTtcclxuICAgICAgICBpZihhcmdpZHg+PTApe1xyXG4gICAgICAgICAgICBhcmdzW2FyZ2lkeF09dmFsO1xyXG4gICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICBhcmdzPVt2YWxdLmNvbmNhdChhcmdzKTtcclxuICAgICAgICB9XHJcbiAgICB9XHJcbiAgICAvL2NvbnNvbGUubG9nKGFyZ3MpO1xyXG4gICAgcmV0dXJuIHdpbmRvd1tmdW5jXT93aW5kb3dbZnVuY10uYXBwbHkodGhpc29iaixhcmdzKTp2YWw7XHJcbn1cclxuXHJcbmZ1bmN0aW9uIGlpZih2LG0xLG0yKXtcclxuICAgIGlmKHY9PT0nMCcpdj0wO1xyXG4gICAgcmV0dXJuIHY/bTE6bTI7XHJcbn0iLCJcclxudmFyIGRpYWxvZ1RwbD0nPGRpdiBjbGFzcz1cIm1vZGFsIGZhZGVcIiBpZD1cIntAaWR9XCIgdGFiaW5kZXg9XCItMVwiIHJvbGU9XCJkaWFsb2dcIiBhcmlhLWxhYmVsbGVkYnk9XCJ7QGlkfUxhYmVsXCIgYXJpYS1oaWRkZW49XCJ0cnVlXCI+XFxuJyArXHJcbiAgICAnICAgIDxkaXYgY2xhc3M9XCJtb2RhbC1kaWFsb2dcIj5cXG4nICtcclxuICAgICcgICAgICAgIDxkaXYgY2xhc3M9XCJtb2RhbC1jb250ZW50XCI+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgPGRpdiBjbGFzcz1cIm1vZGFsLWhlYWRlclwiPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgICAgICA8aDQgY2xhc3M9XCJtb2RhbC10aXRsZVwiIGlkPVwie0BpZH1MYWJlbFwiPjwvaDQ+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgICAgIDxidXR0b24gdHlwZT1cImJ1dHRvblwiIGNsYXNzPVwiY2xvc2VcIiBkYXRhLWRpc21pc3M9XCJtb2RhbFwiPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgICAgICAgICAgPHNwYW4gYXJpYS1oaWRkZW49XCJ0cnVlXCI+JnRpbWVzOzwvc3Bhbj5cXG4nICtcclxuICAgICcgICAgICAgICAgICAgICAgICAgIDxzcGFuIGNsYXNzPVwic3Itb25seVwiPkNsb3NlPC9zcGFuPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgICAgICA8L2J1dHRvbj5cXG4nICtcclxuICAgICcgICAgICAgICAgICA8L2Rpdj5cXG4nICtcclxuICAgICcgICAgICAgICAgICA8ZGl2IGNsYXNzPVwibW9kYWwtYm9keVwiPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgIDwvZGl2PlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJtb2RhbC1mb290ZXJcIj5cXG4nICtcclxuICAgICcgICAgICAgICAgICAgICAgPG5hdiBjbGFzcz1cIm5hdiBuYXYtZmlsbFwiPjwvbmF2PlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgIDwvZGl2PlxcbicgK1xyXG4gICAgJyAgICAgICAgPC9kaXY+XFxuJyArXHJcbiAgICAnICAgIDwvZGl2PlxcbicgK1xyXG4gICAgJzwvZGl2Pic7XHJcbnZhciBkaWFsb2dJZHg9MDtcclxuZnVuY3Rpb24gRGlhbG9nKG9wdHMpe1xyXG4gICAgaWYoIW9wdHMpb3B0cz17fTtcclxuICAgIC8v5aSE55CG5oyJ6ZKuXHJcbiAgICBpZihvcHRzLmJ0bnMhPT11bmRlZmluZWQpIHtcclxuICAgICAgICBpZiAodHlwZW9mKG9wdHMuYnRucykgPT0gJ3N0cmluZycpIHtcclxuICAgICAgICAgICAgb3B0cy5idG5zID0gW29wdHMuYnRuc107XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHZhciBkZnQ9LTE7XHJcbiAgICAgICAgZm9yICh2YXIgaSA9IDA7IGkgPCBvcHRzLmJ0bnMubGVuZ3RoOyBpKyspIHtcclxuICAgICAgICAgICAgaWYodHlwZW9mKG9wdHMuYnRuc1tpXSk9PSdzdHJpbmcnKXtcclxuICAgICAgICAgICAgICAgIG9wdHMuYnRuc1tpXT17J3RleHQnOm9wdHMuYnRuc1tpXX07XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgaWYob3B0cy5idG5zW2ldLmlzZGVmYXVsdCl7XHJcbiAgICAgICAgICAgICAgICBkZnQ9aTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH1cclxuICAgICAgICBpZihkZnQ8MCl7XHJcbiAgICAgICAgICAgIGRmdD1vcHRzLmJ0bnMubGVuZ3RoLTE7XHJcbiAgICAgICAgICAgIG9wdHMuYnRuc1tkZnRdLmlzZGVmYXVsdD10cnVlO1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgaWYoIW9wdHMuYnRuc1tkZnRdWyd0eXBlJ10pe1xyXG4gICAgICAgICAgICBvcHRzLmJ0bnNbZGZ0XVsndHlwZSddPSdwcmltYXJ5JztcclxuICAgICAgICB9XHJcbiAgICAgICAgb3B0cy5kZWZhdWx0QnRuPWRmdDtcclxuICAgIH1cclxuXHJcbiAgICB0aGlzLm9wdGlvbnM9JC5leHRlbmQoe1xyXG4gICAgICAgICdpZCc6J2RsZ01vZGFsJytkaWFsb2dJZHgrKyxcclxuICAgICAgICAnc2l6ZSc6JycsXHJcbiAgICAgICAgJ2J0bnMnOltcclxuICAgICAgICAgICAgeyd0ZXh0Jzon5Y+W5raIJywndHlwZSc6J3NlY29uZGFyeSd9LFxyXG4gICAgICAgICAgICB7J3RleHQnOifnoa7lrponLCdpc2RlZmF1bHQnOnRydWUsJ3R5cGUnOidwcmltYXJ5J31cclxuICAgICAgICBdLFxyXG4gICAgICAgICdkZWZhdWx0QnRuJzoxLFxyXG4gICAgICAgICdvbnN1cmUnOm51bGwsXHJcbiAgICAgICAgJ29uc2hvdyc6bnVsbCxcclxuICAgICAgICAnb25zaG93bic6bnVsbCxcclxuICAgICAgICAnb25oaWRlJzpudWxsLFxyXG4gICAgICAgICdvbmhpZGRlbic6bnVsbFxyXG4gICAgfSxvcHRzKTtcclxuXHJcbiAgICB0aGlzLmJveD0kKHRoaXMub3B0aW9ucy5pZCk7XHJcbn1cclxuRGlhbG9nLnByb3RvdHlwZS5nZW5lckJ0bj1mdW5jdGlvbihvcHQsaWR4KXtcclxuICAgIGlmKG9wdFsndHlwZSddKW9wdFsnY2xhc3MnXT0nYnRuLW91dGxpbmUtJytvcHRbJ3R5cGUnXTtcclxuICAgIHJldHVybiAnPGEgaHJlZj1cImphdmFzY3JpcHQ6XCIgY2xhc3M9XCJuYXYtaXRlbSBidG4gJysob3B0WydjbGFzcyddP29wdFsnY2xhc3MnXTonYnRuLW91dGxpbmUtc2Vjb25kYXJ5JykrJ1wiIGRhdGEtaW5kZXg9XCInK2lkeCsnXCI+JytvcHQudGV4dCsnPC9hPic7XHJcbn07XHJcbkRpYWxvZy5wcm90b3R5cGUuc2hvdz1mdW5jdGlvbihodG1sLHRpdGxlKXtcclxuICAgIHRoaXMuYm94PSQoJyMnK3RoaXMub3B0aW9ucy5pZCk7XHJcbiAgICBpZighdGl0bGUpdGl0bGU9J+ezu+e7n+aPkOekuic7XHJcbiAgICBpZih0aGlzLmJveC5sZW5ndGg8MSkge1xyXG4gICAgICAgICQoZG9jdW1lbnQuYm9keSkuYXBwZW5kKGRpYWxvZ1RwbC5jb21waWxlKHsnaWQnOiB0aGlzLm9wdGlvbnMuaWR9KSk7XHJcbiAgICAgICAgdGhpcy5ib3g9JCgnIycrdGhpcy5vcHRpb25zLmlkKTtcclxuICAgIH1lbHNle1xyXG4gICAgICAgIHRoaXMuYm94LnVuYmluZCgpO1xyXG4gICAgfVxyXG5cclxuICAgIC8vdGhpcy5ib3guZmluZCgnLm1vZGFsLWZvb3RlciAuYnRuLXByaW1hcnknKS51bmJpbmQoKTtcclxuICAgIHZhciBzZWxmPXRoaXM7XHJcbiAgICBEaWFsb2cuaW5zdGFuY2U9c2VsZjtcclxuXHJcbiAgICAvL+eUn+aIkOaMiemSrlxyXG4gICAgdmFyIGJ0bnM9W107XHJcbiAgICBmb3IodmFyIGk9MDtpPHRoaXMub3B0aW9ucy5idG5zLmxlbmd0aDtpKyspe1xyXG4gICAgICAgIGJ0bnMucHVzaCh0aGlzLmdlbmVyQnRuKHRoaXMub3B0aW9ucy5idG5zW2ldLGkpKTtcclxuICAgIH1cclxuICAgIHRoaXMuYm94LmZpbmQoJy5tb2RhbC1mb290ZXIgLm5hdicpLmh0bWwoYnRucy5qb2luKCdcXG4nKSk7XHJcblxyXG4gICAgdmFyIGRpYWxvZz10aGlzLmJveC5maW5kKCcubW9kYWwtZGlhbG9nJyk7XHJcbiAgICBkaWFsb2cucmVtb3ZlQ2xhc3MoJ21vZGFsLXNtJykucmVtb3ZlQ2xhc3MoJ21vZGFsLWxnJyk7XHJcbiAgICBpZih0aGlzLm9wdGlvbnMuc2l6ZT09J3NtJykge1xyXG4gICAgICAgIGRpYWxvZy5hZGRDbGFzcygnbW9kYWwtc20nKTtcclxuICAgIH1lbHNlIGlmKHRoaXMub3B0aW9ucy5zaXplPT0nbGcnKSB7XHJcbiAgICAgICAgZGlhbG9nLmFkZENsYXNzKCdtb2RhbC1sZycpO1xyXG4gICAgfVxyXG4gICAgdGhpcy5ib3guZmluZCgnLm1vZGFsLXRpdGxlJykudGV4dCh0aXRsZSk7XHJcblxyXG4gICAgdmFyIGJvZHk9dGhpcy5ib3guZmluZCgnLm1vZGFsLWJvZHknKTtcclxuICAgIGJvZHkuaHRtbChodG1sKTtcclxuICAgIHRoaXMuYm94Lm9uKCdoaWRlLmJzLm1vZGFsJyxmdW5jdGlvbigpe1xyXG4gICAgICAgIGlmKHNlbGYub3B0aW9ucy5vbmhpZGUpe1xyXG4gICAgICAgICAgICBzZWxmLm9wdGlvbnMub25oaWRlKGJvZHksc2VsZi5ib3gpO1xyXG4gICAgICAgIH1cclxuICAgICAgICBEaWFsb2cuaW5zdGFuY2U9bnVsbDtcclxuICAgIH0pO1xyXG4gICAgdGhpcy5ib3gub24oJ2hpZGRlbi5icy5tb2RhbCcsZnVuY3Rpb24oKXtcclxuICAgICAgICBpZihzZWxmLm9wdGlvbnMub25oaWRkZW4pe1xyXG4gICAgICAgICAgICBzZWxmLm9wdGlvbnMub25oaWRkZW4oYm9keSxzZWxmLmJveCk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHNlbGYuYm94LnJlbW92ZSgpO1xyXG4gICAgfSk7XHJcbiAgICB0aGlzLmJveC5vbignc2hvdy5icy5tb2RhbCcsZnVuY3Rpb24oKXtcclxuICAgICAgICBpZihzZWxmLm9wdGlvbnMub25zaG93KXtcclxuICAgICAgICAgICAgc2VsZi5vcHRpb25zLm9uc2hvdyhib2R5LHNlbGYuYm94KTtcclxuICAgICAgICB9XHJcbiAgICB9KTtcclxuICAgIHRoaXMuYm94Lm9uKCdzaG93bi5icy5tb2RhbCcsZnVuY3Rpb24oKXtcclxuICAgICAgICBpZihzZWxmLm9wdGlvbnMub25zaG93bil7XHJcbiAgICAgICAgICAgIHNlbGYub3B0aW9ucy5vbnNob3duKGJvZHksc2VsZi5ib3gpO1xyXG4gICAgICAgIH1cclxuICAgIH0pO1xyXG4gICAgdGhpcy5ib3guZmluZCgnLm1vZGFsLWZvb3RlciAuYnRuJykuY2xpY2soZnVuY3Rpb24oKXtcclxuICAgICAgICB2YXIgcmVzdWx0PXRydWUsaWR4PSQodGhpcykuZGF0YSgnaW5kZXgnKTtcclxuICAgICAgICBpZihzZWxmLm9wdGlvbnMuYnRuc1tpZHhdLmNsaWNrKXtcclxuICAgICAgICAgICAgcmVzdWx0ID0gc2VsZi5vcHRpb25zLmJ0bnNbaWR4XS5jbGljay5hcHBseSh0aGlzLFtib2R5LCBzZWxmLmJveF0pO1xyXG4gICAgICAgIH1cclxuICAgICAgICBpZihpZHg9PXNlbGYub3B0aW9ucy5kZWZhdWx0QnRuKSB7XHJcbiAgICAgICAgICAgIGlmIChzZWxmLm9wdGlvbnMub25zdXJlKSB7XHJcbiAgICAgICAgICAgICAgICByZXN1bHQgPSBzZWxmLm9wdGlvbnMub25zdXJlLmFwcGx5KHRoaXMsW2JvZHksIHNlbGYuYm94XSk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9XHJcbiAgICAgICAgaWYocmVzdWx0IT09ZmFsc2Upe1xyXG4gICAgICAgICAgICBzZWxmLmJveC5tb2RhbCgnaGlkZScpO1xyXG4gICAgICAgIH1cclxuICAgIH0pO1xyXG4gICAgdGhpcy5ib3gubW9kYWwoJ3Nob3cnKTtcclxuICAgIHJldHVybiB0aGlzO1xyXG59O1xyXG5EaWFsb2cucHJvdG90eXBlLmhpZGU9ZnVuY3Rpb24oKXtcclxuICAgIHRoaXMuYm94Lm1vZGFsKCdoaWRlJyk7XHJcbiAgICByZXR1cm4gdGhpcztcclxufTtcclxuXHJcbnZhciBkaWFsb2c9e1xyXG4gICAgYWxlcnQ6ZnVuY3Rpb24obWVzc2FnZSxjYWxsYmFjayx0aXRsZSl7XHJcbiAgICAgICAgdmFyIGNhbGxlZD1mYWxzZTtcclxuICAgICAgICB2YXIgaXNjYWxsYmFjaz10eXBlb2YgY2FsbGJhY2s9PSdmdW5jdGlvbic7XHJcbiAgICAgICAgcmV0dXJuIG5ldyBEaWFsb2coe1xyXG4gICAgICAgICAgICBidG5zOifnoa7lrponLFxyXG4gICAgICAgICAgICBvbnN1cmU6ZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgICAgIGlmKGlzY2FsbGJhY2spe1xyXG4gICAgICAgICAgICAgICAgICAgIGNhbGxlZD10cnVlO1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBjYWxsYmFjayh0cnVlKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgb25oaWRlOmZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgICAgICBpZighY2FsbGVkICYmIGlzY2FsbGJhY2spe1xyXG4gICAgICAgICAgICAgICAgICAgIGNhbGxiYWNrKGZhbHNlKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLnNob3cobWVzc2FnZSx0aXRsZSk7XHJcbiAgICB9LFxyXG4gICAgY29uZmlybTpmdW5jdGlvbihtZXNzYWdlLGNvbmZpcm0sY2FuY2VsKXtcclxuICAgICAgICB2YXIgY2FsbGVkPWZhbHNlO1xyXG4gICAgICAgIHJldHVybiBuZXcgRGlhbG9nKHtcclxuICAgICAgICAgICAgJ29uc3VyZSc6ZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgICAgIGlmKHR5cGVvZiBjb25maXJtPT0nZnVuY3Rpb24nKXtcclxuICAgICAgICAgICAgICAgICAgICBjYWxsZWQ9dHJ1ZTtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gY29uZmlybSgpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAnb25oaWRlJzpmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgICAgICBpZihjYWxsZWQ9ZmFsc2UgJiYgdHlwZW9mIGNhbmNlbD09J2Z1bmN0aW9uJyl7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGNhbmNlbCgpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkuc2hvdyhtZXNzYWdlKTtcclxuICAgIH0sXHJcbiAgICBwcm9tcHQ6ZnVuY3Rpb24obWVzc2FnZSxjYWxsYmFjayxjYW5jZWwpe1xyXG4gICAgICAgIHZhciBjYWxsZWQ9ZmFsc2U7XHJcbiAgICAgICAgcmV0dXJuIG5ldyBEaWFsb2coe1xyXG4gICAgICAgICAgICAnb25zaG93bic6ZnVuY3Rpb24oYm9keSl7XHJcbiAgICAgICAgICAgICAgICBib2R5LmZpbmQoJ1tuYW1lPWNvbmZpcm1faW5wdXRdJykuZm9jdXMoKTtcclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgJ29uc3VyZSc6ZnVuY3Rpb24oYm9keSl7XHJcbiAgICAgICAgICAgICAgICB2YXIgdmFsPWJvZHkuZmluZCgnW25hbWU9Y29uZmlybV9pbnB1dF0nKS52YWwoKTtcclxuICAgICAgICAgICAgICAgIGlmKHR5cGVvZiBjYWxsYmFjaz09J2Z1bmN0aW9uJyl7XHJcbiAgICAgICAgICAgICAgICAgICAgdmFyIHJlc3VsdCA9IGNhbGxiYWNrKHZhbCk7XHJcbiAgICAgICAgICAgICAgICAgICAgaWYocmVzdWx0PT09dHJ1ZSl7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGNhbGxlZD10cnVlO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gcmVzdWx0O1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAnb25oaWRlJzpmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgICAgICBpZihjYWxsZWQ9ZmFsc2UgJiYgdHlwZW9mIGNhbmNlbD09J2Z1bmN0aW9uJyl7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGNhbmNlbCgpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkuc2hvdygnPGlucHV0IHR5cGU9XCJ0ZXh0XCIgbmFtZT1cImNvbmZpcm1faW5wdXRcIiBjbGFzcz1cImZvcm0tY29udHJvbFwiIC8+JyxtZXNzYWdlKTtcclxuICAgIH0sXHJcbiAgICBwaWNrVXNlcjpmdW5jdGlvbih1cmwsY2FsbGJhY2ssZmlsdGVyKXtcclxuICAgICAgICB2YXIgdXNlcj1udWxsO1xyXG4gICAgICAgIGlmKCFmaWx0ZXIpZmlsdGVyPXt9O1xyXG4gICAgICAgIHZhciBkbGc9bmV3IERpYWxvZyh7XHJcbiAgICAgICAgICAgICdvbnNob3duJzpmdW5jdGlvbihib2R5KXtcclxuICAgICAgICAgICAgICAgIHZhciBidG49Ym9keS5maW5kKCcuc2VhcmNoYnRuJyk7XHJcbiAgICAgICAgICAgICAgICB2YXIgaW5wdXQ9Ym9keS5maW5kKCcuc2VhcmNodGV4dCcpO1xyXG4gICAgICAgICAgICAgICAgdmFyIGxpc3Rib3g9Ym9keS5maW5kKCcubGlzdC1ncm91cCcpO1xyXG4gICAgICAgICAgICAgICAgdmFyIGlzbG9hZGluZz1mYWxzZTtcclxuICAgICAgICAgICAgICAgIGJ0bi5jbGljayhmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICAgICAgICAgIGlmKGlzbG9hZGluZylyZXR1cm47XHJcbiAgICAgICAgICAgICAgICAgICAgaXNsb2FkaW5nPXRydWU7XHJcbiAgICAgICAgICAgICAgICAgICAgbGlzdGJveC5odG1sKCc8c3BhbiBjbGFzcz1cImxpc3QtbG9hZGluZ1wiPuWKoOi9veS4rS4uLjwvc3Bhbj4nKTtcclxuICAgICAgICAgICAgICAgICAgICBmaWx0ZXJbJ2tleSddPWlucHV0LnZhbCgpO1xyXG4gICAgICAgICAgICAgICAgICAgICQuYWpheChcclxuICAgICAgICAgICAgICAgICAgICAgICAge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdXJsOnVybCxcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHR5cGU6J0dFVCcsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBkYXRhVHlwZTonSlNPTicsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBkYXRhOmZpbHRlcixcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHN1Y2Nlc3M6ZnVuY3Rpb24oanNvbil7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaXNsb2FkaW5nPWZhbHNlO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmKGpzb24uc3RhdHVzKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYoanNvbi5kYXRhICYmIGpzb24uZGF0YS5sZW5ndGgpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxpc3Rib3guaHRtbCgnPGEgaHJlZj1cImphdmFzY3JpcHQ6XCIgZGF0YS1pZD1cIntAaWR9XCIgY2xhc3M9XCJsaXN0LWdyb3VwLWl0ZW0gbGlzdC1ncm91cC1pdGVtLWFjdGlvblwiPlt7QGlkfV0mbmJzcDs8aSBjbGFzcz1cImlvbi1tZC1wZXJzb25cIj48L2k+IHtAdXNlcm5hbWV9Jm5ic3A7Jm5ic3A7Jm5ic3A7PHNtYWxsPjxpIGNsYXNzPVwiaW9uLW1kLXBob25lLXBvcnRyYWl0XCI+PC9pPiB7QG1vYmlsZX08L3NtYWxsPjwvYT4nLmNvbXBpbGUoanNvbi5kYXRhLCB0cnVlKSk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBsaXN0Ym94LmZpbmQoJ2EubGlzdC1ncm91cC1pdGVtJykuY2xpY2soZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHZhciBpZCA9ICQodGhpcykuZGF0YSgnaWQnKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBmb3IgKHZhciBpID0gMDsgaSA8IGpzb24uZGF0YS5sZW5ndGg7IGkrKykge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZihqc29uLmRhdGFbaV0uaWQ9PWlkKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHVzZXI9anNvbi5kYXRhW2ldO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbGlzdGJveC5maW5kKCdhLmxpc3QtZ3JvdXAtaXRlbScpLnJlbW92ZUNsYXNzKCdhY3RpdmUnKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykuYWRkQ2xhc3MoJ2FjdGl2ZScpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9KVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9ZWxzZXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxpc3Rib3guaHRtbCgnPHNwYW4gY2xhc3M9XCJsaXN0LWxvYWRpbmdcIj48aSBjbGFzcz1cImlvbi1tZC13YXJuaW5nXCI+PC9pPiDmsqHmnInmo4DntKLliLDkvJrlkZg8L3NwYW4+Jyk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9ZWxzZXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbGlzdGJveC5odG1sKCc8c3BhbiBjbGFzcz1cInRleHQtZGFuZ2VyXCI+PGkgY2xhc3M9XCJpb24tbWQtd2FybmluZ1wiPjwvaT4g5Yqg6L295aSx6LSlPC9zcGFuPicpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgICk7XHJcblxyXG4gICAgICAgICAgICAgICAgfSkudHJpZ2dlcignY2xpY2snKTtcclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgJ29uc3VyZSc6ZnVuY3Rpb24oYm9keSl7XHJcbiAgICAgICAgICAgICAgICBpZighdXNlcil7XHJcbiAgICAgICAgICAgICAgICAgICAgdG9hc3RyLndhcm5pbmcoJ+ayoeaciemAieaLqeS8muWRmCEnKTtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICBpZih0eXBlb2YgY2FsbGJhY2s9PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgICAgIHZhciByZXN1bHQgPSBjYWxsYmFjayh1c2VyKTtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gcmVzdWx0O1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkuc2hvdygnPGRpdiBjbGFzcz1cImlucHV0LWdyb3VwXCI+PGlucHV0IHR5cGU9XCJ0ZXh0XCIgY2xhc3M9XCJmb3JtLWNvbnRyb2wgc2VhcmNodGV4dFwiIG5hbWU9XCJrZXl3b3JkXCIgcGxhY2Vob2xkZXI9XCLmoLnmja7kvJrlkZhpZOaIluWQjeensO+8jOeUteivneadpeaQnOe0olwiLz48ZGl2IGNsYXNzPVwiaW5wdXQtZ3JvdXAtYXBwZW5kXCI+PGEgY2xhc3M9XCJidG4gYnRuLW91dGxpbmUtc2Vjb25kYXJ5IHNlYXJjaGJ0blwiPjxpIGNsYXNzPVwiaW9uLW1kLXNlYXJjaFwiPjwvaT48L2E+PC9kaXY+PC9kaXY+PGRpdiBjbGFzcz1cImxpc3QtZ3JvdXAgbXQtMlwiPjwvZGl2PicsJ+ivt+aQnOe0ouW5tumAieaLqeS8muWRmCcpO1xyXG4gICAgfSxcclxuICAgIHBpY2tMb2NhdGU6ZnVuY3Rpb24odHlwZSwgY2FsbGJhY2ssIGxvY2F0ZSl7XHJcbiAgICAgICAgdmFyIHNldHRlZExvY2F0ZT1udWxsO1xyXG4gICAgICAgIHZhciBkbGc9bmV3IERpYWxvZyh7XHJcbiAgICAgICAgICAgICdzaXplJzonbGcnLFxyXG4gICAgICAgICAgICAnb25zaG93bic6ZnVuY3Rpb24oYm9keSl7XHJcbiAgICAgICAgICAgICAgICB2YXIgYnRuPWJvZHkuZmluZCgnLnNlYXJjaGJ0bicpO1xyXG4gICAgICAgICAgICAgICAgdmFyIGlucHV0PWJvZHkuZmluZCgnLnNlYXJjaHRleHQnKTtcclxuICAgICAgICAgICAgICAgIHZhciBtYXBib3g9Ym9keS5maW5kKCcubWFwJyk7XHJcbiAgICAgICAgICAgICAgICB2YXIgbWFwaW5mbz1ib2R5LmZpbmQoJy5tYXBpbmZvJyk7XHJcbiAgICAgICAgICAgICAgICBtYXBib3guY3NzKCdoZWlnaHQnLCQod2luZG93KS5oZWlnaHQoKSouNik7XHJcbiAgICAgICAgICAgICAgICB2YXIgaXNsb2FkaW5nPWZhbHNlO1xyXG4gICAgICAgICAgICAgICAgdmFyIG1hcD1Jbml0TWFwKCd0ZW5jZW50JyxtYXBib3gsZnVuY3Rpb24oYWRkcmVzcyxsb2NhdGUpe1xyXG4gICAgICAgICAgICAgICAgICAgIG1hcGluZm8uaHRtbChhZGRyZXNzKycmbmJzcDsnK2xvY2F0ZS5sbmcrJywnK2xvY2F0ZS5sYXQpO1xyXG4gICAgICAgICAgICAgICAgICAgIHNldHRlZExvY2F0ZT1sb2NhdGU7XHJcbiAgICAgICAgICAgICAgICB9LGxvY2F0ZSk7XHJcbiAgICAgICAgICAgICAgICBidG4uY2xpY2soZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgICAgICAgICB2YXIgc2VhcmNoPWlucHV0LnZhbCgpO1xyXG4gICAgICAgICAgICAgICAgICAgIG1hcC5zZXRMb2NhdGUoc2VhcmNoKTtcclxuICAgICAgICAgICAgICAgIH0pO1xyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAnb25zdXJlJzpmdW5jdGlvbihib2R5KXtcclxuICAgICAgICAgICAgICAgIGlmKCFzZXR0ZWRMb2NhdGUpe1xyXG4gICAgICAgICAgICAgICAgICAgIHRvYXN0ci53YXJuaW5nKCfmsqHmnInpgInmi6nkvY3nva4hJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgaWYodHlwZW9mIGNhbGxiYWNrPT09J2Z1bmN0aW9uJyl7XHJcbiAgICAgICAgICAgICAgICAgICAgdmFyIHJlc3VsdCA9IGNhbGxiYWNrKHNldHRlZExvY2F0ZSk7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHJlc3VsdDtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLnNob3coJzxkaXYgY2xhc3M9XCJpbnB1dC1ncm91cFwiPjxpbnB1dCB0eXBlPVwidGV4dFwiIGNsYXNzPVwiZm9ybS1jb250cm9sIHNlYXJjaHRleHRcIiBuYW1lPVwia2V5d29yZFwiIHBsYWNlaG9sZGVyPVwi5aGr5YaZ5Zyw5Z2A5qOA57Si5L2N572uXCIvPjxkaXYgY2xhc3M9XCJpbnB1dC1ncm91cC1hcHBlbmRcIj48YSBjbGFzcz1cImJ0biBidG4tb3V0bGluZS1zZWNvbmRhcnkgc2VhcmNoYnRuXCI+PGkgY2xhc3M9XCJpb24tbWQtc2VhcmNoXCI+PC9pPjwvYT48L2Rpdj48L2Rpdj4nICtcclxuICAgICAgICAgICAgJzxkaXYgY2xhc3M9XCJtYXAgbXQtMlwiPjwvZGl2PicgK1xyXG4gICAgICAgICAgICAnPGRpdiBjbGFzcz1cIm1hcGluZm8gbXQtMiB0ZXh0LW11dGVkXCI+5pyq6YCJ5oup5L2N572uPC9kaXY+Jywn6K+36YCJ5oup5Zyw5Zu+5L2N572uJyk7XHJcbiAgICB9XHJcbn07XHJcblxyXG5qUXVlcnkoZnVuY3Rpb24oJCl7XHJcblxyXG4gICAgLy/nm5HmjqfmjInplK5cclxuICAgICQoZG9jdW1lbnQpLm9uKCdrZXlkb3duJywgZnVuY3Rpb24oZSl7XHJcbiAgICAgICAgaWYoIURpYWxvZy5pbnN0YW5jZSlyZXR1cm47XHJcbiAgICAgICAgdmFyIGRsZz1EaWFsb2cuaW5zdGFuY2U7XHJcbiAgICAgICAgaWYgKGUua2V5Q29kZSA9PSAxMykge1xyXG4gICAgICAgICAgICBkbGcuYm94LmZpbmQoJy5tb2RhbC1mb290ZXIgLmJ0bicpLmVxKGRsZy5vcHRpb25zLmRlZmF1bHRCdG4pLnRyaWdnZXIoJ2NsaWNrJyk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIC8v6buY6K6k5bey55uR5ZCs5YWz6ZetXHJcbiAgICAgICAgLyppZiAoZS5rZXlDb2RlID09IDI3KSB7XHJcbiAgICAgICAgIHNlbGYuaGlkZSgpO1xyXG4gICAgICAgICB9Ki9cclxuICAgIH0pO1xyXG59KTsiLCJcclxualF1ZXJ5LmV4dGVuZChqUXVlcnkuZm4se1xyXG4gICAgdGFnczpmdW5jdGlvbihubSxvbnVwZGF0ZSl7XHJcbiAgICAgICAgdmFyIGRhdGE9W107XHJcbiAgICAgICAgdmFyIHRwbD0nPHNwYW4gY2xhc3M9XCJiYWRnZSBiYWRnZS1pbmZvXCI+e0BsYWJlbH08aW5wdXQgdHlwZT1cImhpZGRlblwiIG5hbWU9XCInK25tKydcIiB2YWx1ZT1cIntAbGFiZWx9XCIvPjxidXR0b24gdHlwZT1cImJ1dHRvblwiIGNsYXNzPVwiY2xvc2VcIiBkYXRhLWRpc21pc3M9XCJhbGVydFwiIGFyaWEtbGFiZWw9XCJDbG9zZVwiPjxzcGFuIGFyaWEtaGlkZGVuPVwidHJ1ZVwiPiZ0aW1lczs8L3NwYW4+PC9idXR0b24+PC9zcGFuPic7XHJcbiAgICAgICAgdmFyIGl0ZW09JCh0aGlzKS5wYXJlbnRzKCcuZm9ybS1jb250cm9sJyk7XHJcbiAgICAgICAgdmFyIGxhYmVsZ3JvdXA9JCgnPHNwYW4gY2xhc3M9XCJiYWRnZS1ncm91cFwiPjwvc3Bhbj4nKTtcclxuICAgICAgICB2YXIgaW5wdXQ9dGhpcztcclxuICAgICAgICB0aGlzLmJlZm9yZShsYWJlbGdyb3VwKTtcclxuICAgICAgICB0aGlzLm9uKCdrZXl1cCcsZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgdmFyIHZhbD0kKHRoaXMpLnZhbCgpLnJlcGxhY2UoL++8jC9nLCcsJyk7XHJcbiAgICAgICAgICAgIHZhciB1cGRhdGVkPWZhbHNlO1xyXG4gICAgICAgICAgICBpZih2YWwgJiYgdmFsLmluZGV4T2YoJywnKT4tMSl7XHJcbiAgICAgICAgICAgICAgICB2YXIgdmFscz12YWwuc3BsaXQoJywnKTtcclxuICAgICAgICAgICAgICAgIGZvcih2YXIgaT0wO2k8dmFscy5sZW5ndGg7aSsrKXtcclxuICAgICAgICAgICAgICAgICAgICB2YWxzW2ldPXZhbHNbaV0ucmVwbGFjZSgvXlxcc3xcXHMkL2csJycpO1xyXG4gICAgICAgICAgICAgICAgICAgIGlmKHZhbHNbaV0gJiYgZGF0YS5pbmRleE9mKHZhbHNbaV0pPT09LTEpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBkYXRhLnB1c2godmFsc1tpXSk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGxhYmVsZ3JvdXAuYXBwZW5kKHRwbC5jb21waWxlKHtsYWJlbDp2YWxzW2ldfSkpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB1cGRhdGVkPXRydWU7XHJcbiAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgaW5wdXQudmFsKCcnKTtcclxuICAgICAgICAgICAgICAgIGlmKHVwZGF0ZWQgJiYgb251cGRhdGUpb251cGRhdGUoZGF0YSk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS5vbignYmx1cicsZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgdmFyIHZhbD0kKHRoaXMpLnZhbCgpO1xyXG4gICAgICAgICAgICBpZih2YWwpIHtcclxuICAgICAgICAgICAgICAgICQodGhpcykudmFsKHZhbCArICcsJykudHJpZ2dlcigna2V5dXAnKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLnRyaWdnZXIoJ2JsdXInKTtcclxuICAgICAgICBsYWJlbGdyb3VwLm9uKCdjbGljaycsJy5jbG9zZScsZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgdmFyIHRhZz0kKHRoaXMpLnBhcmVudHMoJy5iYWRnZScpLmZpbmQoJ2lucHV0JykudmFsKCk7XHJcbiAgICAgICAgICAgIHZhciBpZD1kYXRhLmluZGV4T2YodGFnKTtcclxuICAgICAgICAgICAgaWYoaWQpZGF0YS5zcGxpY2UoaWQsMSk7XHJcbiAgICAgICAgICAgICQodGhpcykucGFyZW50cygnLmJhZGdlJykucmVtb3ZlKCk7XHJcbiAgICAgICAgICAgIGlmKG9udXBkYXRlKW9udXBkYXRlKGRhdGEpO1xyXG4gICAgICAgIH0pO1xyXG4gICAgICAgIGl0ZW0uY2xpY2soZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgaW5wdXQuZm9jdXMoKTtcclxuICAgICAgICB9KTtcclxuICAgIH1cclxufSk7IiwiLy/ml6XmnJ/nu4Tku7ZcclxuaWYoJC5mbi5kYXRldGltZXBpY2tlcikge1xyXG4gICAgdmFyIHRvb2x0aXBzPSB7XHJcbiAgICAgICAgdG9kYXk6ICflrprkvY3lvZPliY3ml6XmnJ8nLFxyXG4gICAgICAgIGNsZWFyOiAn5riF6Zmk5bey6YCJ5pel5pyfJyxcclxuICAgICAgICBjbG9zZTogJ+WFs+mXremAieaLqeWZqCcsXHJcbiAgICAgICAgc2VsZWN0TW9udGg6ICfpgInmi6nmnIjku70nLFxyXG4gICAgICAgIHByZXZNb250aDogJ+S4iuS4quaciCcsXHJcbiAgICAgICAgbmV4dE1vbnRoOiAn5LiL5Liq5pyIJyxcclxuICAgICAgICBzZWxlY3RZZWFyOiAn6YCJ5oup5bm05Lu9JyxcclxuICAgICAgICBwcmV2WWVhcjogJ+S4iuS4gOW5tCcsXHJcbiAgICAgICAgbmV4dFllYXI6ICfkuIvkuIDlubQnLFxyXG4gICAgICAgIHNlbGVjdERlY2FkZTogJ+mAieaLqeW5tOS7veWMuumXtCcsXHJcbiAgICAgICAgcHJldkRlY2FkZTogJ+S4iuS4gOWMuumXtCcsXHJcbiAgICAgICAgbmV4dERlY2FkZTogJ+S4i+S4gOWMuumXtCcsXHJcbiAgICAgICAgcHJldkNlbnR1cnk6ICfkuIrkuKrkuJbnuqonLFxyXG4gICAgICAgIG5leHRDZW50dXJ5OiAn5LiL5Liq5LiW57qqJ1xyXG4gICAgfTtcclxuICAgIHZhciBpY29ucz17XHJcbiAgICAgICAgdGltZTogJ2lvbi1tZC10aW1lJyxcclxuICAgICAgICBkYXRlOiAnaW9uLW1kLWNhbGVuZGFyJyxcclxuICAgICAgICB1cDogJ2lvbi1tZC1hcnJvdy1kcm9wdXAnLFxyXG4gICAgICAgIGRvd246ICdpb24tbWQtYXJyb3ctZHJvcGRvd24nLFxyXG4gICAgICAgIHByZXZpb3VzOiAnaW9uLW1kLWFycm93LWRyb3BsZWZ0JyxcclxuICAgICAgICBuZXh0OiAnaW9uLW1kLWFycm93LWRyb3ByaWdodCcsXHJcbiAgICAgICAgdG9kYXk6ICdpb24tbWQtdG9kYXknLFxyXG4gICAgICAgIGNsZWFyOiAnaW9uLW1kLXRyYXNoJyxcclxuICAgICAgICBjbG9zZTogJ2lvbi1tZC1jbG9zZSdcclxuICAgIH07XHJcbiAgICAkKCcuZGF0ZXBpY2tlcicpLmVhY2goZnVuY3Rpb24oKXtcclxuICAgICAgICB2YXIgY29uZmlnPSQuZXh0ZW5kKHtcclxuICAgICAgICAgICAgaWNvbnM6aWNvbnMsXHJcbiAgICAgICAgICAgIHRvb2x0aXBzOnRvb2x0aXBzLFxyXG4gICAgICAgICAgICBmb3JtYXQ6ICdZWVlZLU1NLUREJyxcclxuICAgICAgICAgICAgbG9jYWxlOiAnemgtY24nLFxyXG4gICAgICAgICAgICBzaG93Q2xlYXI6dHJ1ZSxcclxuICAgICAgICAgICAgc2hvd1RvZGF5QnV0dG9uOnRydWUsXHJcbiAgICAgICAgICAgIHNob3dDbG9zZTp0cnVlLFxyXG4gICAgICAgICAgICBrZWVwSW52YWxpZDp0cnVlXHJcbiAgICAgICAgfSwkKHRoaXMpLmRhdGEoKSk7XHJcbiAgICAgICAgJCh0aGlzKS5kYXRldGltZXBpY2tlcihjb25maWcpO1xyXG4gICAgfSk7XHJcblxyXG4gICAgJCgnLmRhdGUtcmFuZ2UnKS5lYWNoKGZ1bmN0aW9uICgpIHtcclxuICAgICAgICB2YXIgZnJvbSA9ICQodGhpcykuZmluZCgnW25hbWU9ZnJvbWRhdGVdLC5mcm9tZGF0ZScpLCB0byA9ICQodGhpcykuZmluZCgnW25hbWU9dG9kYXRlXSwudG9kYXRlJyk7XHJcbiAgICAgICAgdmFyIG9wdGlvbnMgPSAkLmV4dGVuZCh7XHJcbiAgICAgICAgICAgIGljb25zOmljb25zLFxyXG4gICAgICAgICAgICB0b29sdGlwczp0b29sdGlwcyxcclxuICAgICAgICAgICAgZm9ybWF0OiAnWVlZWS1NTS1ERCcsXHJcbiAgICAgICAgICAgIGxvY2FsZTonemgtY24nLFxyXG4gICAgICAgICAgICBzaG93Q2xlYXI6dHJ1ZSxcclxuICAgICAgICAgICAgc2hvd1RvZGF5QnV0dG9uOnRydWUsXHJcbiAgICAgICAgICAgIHNob3dDbG9zZTp0cnVlLFxyXG4gICAgICAgICAgICBrZWVwSW52YWxpZDp0cnVlXHJcbiAgICAgICAgfSwkKHRoaXMpLmRhdGEoKSk7XHJcbiAgICAgICAgZnJvbS5kYXRldGltZXBpY2tlcihvcHRpb25zKS5vbignZHAuY2hhbmdlJywgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICBpZiAoZnJvbS52YWwoKSkge1xyXG4gICAgICAgICAgICAgICAgdG8uZGF0YSgnRGF0ZVRpbWVQaWNrZXInKS5taW5EYXRlKGZyb20udmFsKCkpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSk7XHJcbiAgICAgICAgdG8uZGF0ZXRpbWVwaWNrZXIob3B0aW9ucykub24oJ2RwLmNoYW5nZScsIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgaWYgKHRvLnZhbCgpKSB7XHJcbiAgICAgICAgICAgICAgICBmcm9tLmRhdGEoJ0RhdGVUaW1lUGlja2VyJykubWF4RGF0ZSh0by52YWwoKSk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KTtcclxuICAgIH0pO1xyXG59IiwiXHJcbihmdW5jdGlvbih3aW5kb3csJCkge1xyXG4gICAgdmFyIGFwaXMgPSB7XHJcbiAgICAgICAgJ2JhaWR1JzogJ2h0dHBzOi8vYXBpLm1hcC5iYWlkdS5jb20vYXBpP2FrPXJPOXRPZEVXRmZ2eUdnRGtpV3FGanhLNiZ2PTEuNSZzZXJ2aWNlcz1mYWxzZSZjYWxsYmFjaz0nLFxyXG4gICAgICAgICdnb29nbGUnOiAnaHR0cHM6Ly9tYXBzLmdvb2dsZS5jb20vbWFwcy9hcGkvanM/a2V5PUFJemFTeUI4bG9ydmw2RXRxSVd6NjdialdCcnVPaG05TllTMWUyNCZjYWxsYmFjaz0nLFxyXG4gICAgICAgICd0ZW5jZW50JzogJ2h0dHBzOi8vbWFwLnFxLmNvbS9hcGkvanM/dj0yLmV4cCZrZXk9N0k1QlotUVVFNlItSlhMV1YtV1RWQUEtQ0pNWUYtN1BCQkkmY2FsbGJhY2s9JyxcclxuICAgICAgICAnZ2FvZGUnOiAnaHR0cHM6Ly93ZWJhcGkuYW1hcC5jb20vbWFwcz92PTEuMyZrZXk9M2VjMzExYjVkYjBkNTk3ZTc5NDIyZWViOWE2ZDQ0NDkmY2FsbGJhY2s9J1xyXG4gICAgfTtcclxuXHJcbiAgICBmdW5jdGlvbiBsb2FkU2NyaXB0KHNyYykge1xyXG4gICAgICAgIHZhciBzY3JpcHQgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KFwic2NyaXB0XCIpO1xyXG4gICAgICAgIHNjcmlwdC50eXBlID0gXCJ0ZXh0L2phdmFzY3JpcHRcIjtcclxuICAgICAgICBzY3JpcHQuc3JjID0gc3JjO1xyXG4gICAgICAgIGRvY3VtZW50LmJvZHkuYXBwZW5kQ2hpbGQoc2NyaXB0KTtcclxuICAgIH1cclxuXHJcbiAgICB2YXIgbWFwT2JqLG1hcEJveCxvblBpY2s7XHJcblxyXG4gICAgZnVuY3Rpb24gSW5pdE1hcChtYXBrZXksYm94LGNhbGxiYWNrLGxvY2F0ZSkge1xyXG4gICAgICAgIGlmIChtYXBPYmopIG1hcE9iai5oaWRlKCk7XHJcbiAgICAgICAgbWFwQm94PSQoYm94KTtcclxuICAgICAgICBvblBpY2s9Y2FsbGJhY2s7XHJcblxyXG4gICAgICAgIHN3aXRjaCAobWFwa2V5LnRvTG93ZXJDYXNlKCkpIHtcclxuICAgICAgICAgICAgY2FzZSAnYmFpZHUnOlxyXG4gICAgICAgICAgICAgICAgbWFwT2JqID0gbmV3IEJhaWR1TWFwKCk7XHJcbiAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgY2FzZSAnZ29vZ2xlJzpcclxuICAgICAgICAgICAgICAgIG1hcE9iaiA9IG5ldyBHb29nbGVNYXAoKTtcclxuICAgICAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgICAgICBjYXNlICd0ZW5jZW50JzpcclxuICAgICAgICAgICAgY2FzZSAncXEnOlxyXG4gICAgICAgICAgICAgICAgbWFwT2JqID0gbmV3IFRlbmNlbnRNYXAoKTtcclxuICAgICAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgICAgICBjYXNlICdnYW9kZSc6XHJcbiAgICAgICAgICAgICAgICBtYXBPYmogPSBuZXcgR2FvZGVNYXAoKTtcclxuICAgICAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgIH1cclxuICAgICAgICBpZiAoIW1hcE9iaikgcmV0dXJuIHRvYXN0ci53YXJuaW5nKCfkuI3mlK/mjIHor6XlnLDlm77nsbvlnosnKTtcclxuICAgICAgICBpZihsb2NhdGUpe1xyXG4gICAgICAgICAgICBpZih0eXBlb2YgbG9jYXRlPT09J3N0cmluZycpe1xyXG4gICAgICAgICAgICAgICAgdmFyIGxvYz1sb2NhdGUuc3BsaXQoJywnKTtcclxuICAgICAgICAgICAgICAgIGxvY2F0ZT17XHJcbiAgICAgICAgICAgICAgICAgICAgbG5nOnBhcnNlRmxvYXQobG9jWzBdKSxcclxuICAgICAgICAgICAgICAgICAgICBsYXQ6cGFyc2VGbG9hdChsb2NbMV0pXHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgbWFwT2JqLmxvY2F0ZT1sb2NhdGU7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIG1hcE9iai5zZXRNYXAoKTtcclxuXHJcbiAgICAgICAgcmV0dXJuIG1hcE9iajtcclxuICAgIH1cclxuXHJcbiAgICBmdW5jdGlvbiBCYXNlTWFwKHR5cGUpIHtcclxuICAgICAgICB0aGlzLm1hcFR5cGUgPSB0eXBlO1xyXG4gICAgICAgIHRoaXMuaXNoaWRlID0gZmFsc2U7XHJcbiAgICAgICAgdGhpcy5pc3Nob3cgPSBmYWxzZTtcclxuICAgICAgICB0aGlzLnRvc2hvdyA9IGZhbHNlO1xyXG4gICAgICAgIHRoaXMubWFya2VyID0gbnVsbDtcclxuICAgICAgICB0aGlzLmluZm9XaW5kb3cgPSBudWxsO1xyXG4gICAgICAgIHRoaXMubWFwYm94ID0gbnVsbDtcclxuICAgICAgICB0aGlzLmxvY2F0ZSA9IHtsbmc6MTE2LjM5Njc5NSxsYXQ6MzkuOTMzMDg0fTtcclxuICAgICAgICB0aGlzLm1hcCA9IG51bGw7XHJcbiAgICB9XHJcblxyXG4gICAgQmFzZU1hcC5wcm90b3R5cGUuaXNBUElSZWFkeSA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICB9O1xyXG4gICAgQmFzZU1hcC5wcm90b3R5cGUuc2V0TWFwID0gZnVuY3Rpb24gKCkge1xyXG4gICAgfTtcclxuICAgIEJhc2VNYXAucHJvdG90eXBlLnNob3dJbmZvID0gZnVuY3Rpb24gKCkge1xyXG4gICAgfTtcclxuICAgIEJhc2VNYXAucHJvdG90eXBlLmdldEFkZHJlc3MgPSBmdW5jdGlvbiAocnMpIHtcclxuICAgICAgICByZXR1cm4gXCJcIjtcclxuICAgIH07XHJcbiAgICBCYXNlTWFwLnByb3RvdHlwZS5zZXRMb2NhdGUgPSBmdW5jdGlvbiAoYWRkcmVzcykge1xyXG4gICAgfTtcclxuXHJcbiAgICBCYXNlTWFwLnByb3RvdHlwZS5sb2FkQVBJID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHZhciBzZWxmID0gdGhpcztcclxuICAgICAgICBpZiAoIXRoaXMuaXNBUElSZWFkeSgpKSB7XHJcbiAgICAgICAgICAgIHRoaXMubWFwYm94ID0gJCgnPGRpdiBpZD1cIicgKyB0aGlzLm1hcFR5cGUgKyAnbWFwXCIgY2xhc3M9XCJtYXBib3hcIj5sb2FkaW5nLi4uPC9kaXY+Jyk7XHJcbiAgICAgICAgICAgIG1hcEJveC5hcHBlbmQodGhpcy5tYXBib3gpO1xyXG5cclxuICAgICAgICAgICAgLy9jb25zb2xlLmxvZyh0aGlzLm1hcFR5cGUrJyBtYXBsb2FkaW5nLi4uJyk7XHJcbiAgICAgICAgICAgIHZhciBmdW5jID0gJ21hcGxvYWQnICsgbmV3IERhdGUoKS5nZXRUaW1lKCk7XHJcbiAgICAgICAgICAgIHdpbmRvd1tmdW5jXSA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgIHNlbGYuc2V0TWFwKCk7XHJcbiAgICAgICAgICAgICAgICBkZWxldGUgd2luZG93W2Z1bmNdO1xyXG4gICAgICAgICAgICB9O1xyXG4gICAgICAgICAgICBsb2FkU2NyaXB0KGFwaXNbdGhpcy5tYXBUeXBlXSArIGZ1bmMpO1xyXG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgLy9jb25zb2xlLmxvZyh0aGlzLm1hcFR5cGUgKyAnIG1hcGxvYWRlZCcpO1xyXG4gICAgICAgICAgICB0aGlzLm1hcGJveCA9ICQoJyMnICsgdGhpcy5tYXBUeXBlICsgJ21hcCcpO1xyXG4gICAgICAgICAgICBpZiAodGhpcy5tYXBib3gubGVuZ3RoIDwgMSkge1xyXG4gICAgICAgICAgICAgICAgdGhpcy5tYXBib3ggPSAkKCc8ZGl2IGlkPVwiJyArIHRoaXMubWFwVHlwZSArICdtYXBcIiBjbGFzcz1cIm1hcGJveFwiPjwvZGl2PicpO1xyXG4gICAgICAgICAgICAgICAgbWFwQm94LmFwcGVuZCh0aGlzLm1hcGJveCk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgcmV0dXJuIHRydWU7XHJcbiAgICAgICAgfVxyXG4gICAgfTtcclxuICAgIEJhc2VNYXAucHJvdG90eXBlLmJpbmRFdmVudHMgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgdmFyIHNlbGYgPSB0aGlzO1xyXG4gICAgICAgICQoJyN0eHRUaXRsZScpLnVuYmluZCgpLmJsdXIoZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICBzZWxmLnNob3dJbmZvKCk7XHJcbiAgICAgICAgfSk7XHJcbiAgICAgICAgJCgnI3R4dENvbnRlbnQnKS51bmJpbmQoKS5ibHVyKGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgc2VsZi5zaG93SW5mbygpO1xyXG4gICAgICAgIH0pO1xyXG4gICAgfTtcclxuICAgIEJhc2VNYXAucHJvdG90eXBlLnNldEluZm9Db250ZW50ID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIGlmICghdGhpcy5pbmZvV2luZG93KSByZXR1cm47XHJcbiAgICAgICAgdmFyIHRpdGxlID0gJzxiPuW9k+WJjeS9jee9rjwvYj4nO1xyXG4gICAgICAgIHZhciBhZGRyID0gJzxwIHN0eWxlPVwibGluZS1oZWlnaHQ6MS42ZW07XCI+PC9wPic7XHJcbiAgICAgICAgaWYgKHRoaXMuaW5mb1dpbmRvdy5zZXRUaXRsZSkge1xyXG4gICAgICAgICAgICB0aGlzLmluZm9XaW5kb3cuc2V0VGl0bGUodGl0bGUpO1xyXG4gICAgICAgICAgICB0aGlzLmluZm9XaW5kb3cuc2V0Q29udGVudChhZGRyKTtcclxuICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICB2YXIgY29udGVudCA9ICc8aDM+JyArIHRpdGxlICsgJzwvaDM+PGRpdiBzdHlsZT1cIndpZHRoOjI1MHB4XCI+JyArIGFkZHIgKyAnPC9kaXY+JztcclxuICAgICAgICAgICAgdGhpcy5pbmZvV2luZG93LnNldENvbnRlbnQoY29udGVudCk7XHJcbiAgICAgICAgfVxyXG4gICAgfTtcclxuICAgIEJhc2VNYXAucHJvdG90eXBlLnNob3dMb2NhdGlvbkluZm8gPSBmdW5jdGlvbiAocHQsIHJzKSB7XHJcblxyXG4gICAgICAgIHRoaXMuc2hvd0luZm8oKTtcclxuICAgICAgICB2YXIgYWRkcmVzcz10aGlzLmdldEFkZHJlc3MocnMpO1xyXG4gICAgICAgIHZhciBsb2NhdGU9e307XHJcbiAgICAgICAgaWYgKHR5cGVvZiAocHQubG5nKSA9PT0gJ2Z1bmN0aW9uJykge1xyXG4gICAgICAgICAgICBsb2NhdGUubG5nPXB0LmxuZygpO1xyXG4gICAgICAgICAgICBsb2NhdGUubGF0PXB0LmxhdCgpO1xyXG4gICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgIGxvY2F0ZS5sbmc9cHQubG5nO1xyXG4gICAgICAgICAgICBsb2NhdGUubGF0PXB0LmxhdDtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIG9uUGljayhhZGRyZXNzLGxvY2F0ZSk7XHJcbiAgICB9O1xyXG4gICAgQmFzZU1hcC5wcm90b3R5cGUuc2hvdyA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICB0aGlzLmlzaGlkZSA9IGZhbHNlO1xyXG4gICAgICAgIHRoaXMuc2V0TWFwKCk7XHJcbiAgICAgICAgdGhpcy5zaG93SW5mbygpO1xyXG4gICAgfTtcclxuICAgIEJhc2VNYXAucHJvdG90eXBlLmhpZGUgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgdGhpcy5pc2hpZGUgPSB0cnVlO1xyXG4gICAgICAgIGlmICh0aGlzLmluZm9XaW5kb3cpIHtcclxuICAgICAgICAgICAgdGhpcy5pbmZvV2luZG93LmNsb3NlKCk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIGlmICh0aGlzLm1hcGJveCkge1xyXG4gICAgICAgICAgICAkKHRoaXMubWFwYm94KS5yZW1vdmUoKTtcclxuICAgICAgICB9XHJcbiAgICB9O1xyXG5cclxuXHJcbiAgICBmdW5jdGlvbiBCYWlkdU1hcCgpIHtcclxuICAgICAgICBCYXNlTWFwLmNhbGwodGhpcywgXCJiYWlkdVwiKTtcclxuICAgIH1cclxuXHJcbiAgICBCYWlkdU1hcC5wcm90b3R5cGUgPSBuZXcgQmFzZU1hcCgpO1xyXG4gICAgQmFpZHVNYXAucHJvdG90eXBlLmNvbnN0cnVjdG9yID0gQmFpZHVNYXA7XHJcbiAgICBCYWlkdU1hcC5wcm90b3R5cGUuaXNBUElSZWFkeSA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICByZXR1cm4gISF3aW5kb3dbJ0JNYXAnXTtcclxuICAgIH07XHJcbiAgICBCYWlkdU1hcC5wcm90b3R5cGUuc2V0TWFwID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHZhciBzZWxmID0gdGhpcztcclxuICAgICAgICBpZiAodGhpcy5pc3Nob3cgfHwgdGhpcy5pc2hpZGUpIHJldHVybjtcclxuICAgICAgICBpZiAoIXRoaXMubG9hZEFQSSgpKSByZXR1cm47XHJcblxyXG4gICAgICAgIHZhciBtYXAgPSBzZWxmLm1hcCA9IG5ldyBCTWFwLk1hcCh0aGlzLm1hcGJveC5hdHRyKCdpZCcpKTsgLy/liJ3lp4vljJblnLDlm75cclxuICAgICAgICBtYXAuYWRkQ29udHJvbChuZXcgQk1hcC5OYXZpZ2F0aW9uQ29udHJvbCgpKTsgIC8v5Yid5aeL5YyW5Zyw5Zu+5o6n5Lu2XHJcbiAgICAgICAgbWFwLmFkZENvbnRyb2wobmV3IEJNYXAuU2NhbGVDb250cm9sKCkpO1xyXG4gICAgICAgIG1hcC5hZGRDb250cm9sKG5ldyBCTWFwLk92ZXJ2aWV3TWFwQ29udHJvbCgpKTtcclxuICAgICAgICBtYXAuZW5hYmxlU2Nyb2xsV2hlZWxab29tKCk7XHJcblxyXG4gICAgICAgIHZhciBwb2ludCA9IG5ldyBCTWFwLlBvaW50KHRoaXMubG9jYXRlLmxuZywgdGhpcy5sb2NhdGUubGF0KTtcclxuICAgICAgICBtYXAuY2VudGVyQW5kWm9vbShwb2ludCwgMTUpOyAvL+WIneWni+WMluWcsOWbvuS4reW/g+eCuVxyXG4gICAgICAgIHRoaXMubWFya2VyID0gbmV3IEJNYXAuTWFya2VyKHBvaW50KTsgLy/liJ3lp4vljJblnLDlm77moIforrBcclxuICAgICAgICB0aGlzLm1hcmtlci5lbmFibGVEcmFnZ2luZygpOyAvL+agh+iusOW8gOWQr+aLluaLvVxyXG5cclxuICAgICAgICB2YXIgZ2MgPSBuZXcgQk1hcC5HZW9jb2RlcigpOyAvL+WcsOWdgOino+aekOexu1xyXG4gICAgICAgIC8v5re75Yqg5qCH6K6w5ouW5ou955uR5ZCsXHJcbiAgICAgICAgdGhpcy5tYXJrZXIuYWRkRXZlbnRMaXN0ZW5lcihcImRyYWdlbmRcIiwgZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICAgICAgLy/ojrflj5blnLDlnYDkv6Hmga9cclxuICAgICAgICAgICAgZ2MuZ2V0TG9jYXRpb24oZS5wb2ludCwgZnVuY3Rpb24gKHJzKSB7XHJcbiAgICAgICAgICAgICAgICBzZWxmLnNob3dMb2NhdGlvbkluZm8oZS5wb2ludCwgcnMpO1xyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgLy/mt7vliqDmoIforrDngrnlh7vnm5HlkKxcclxuICAgICAgICB0aGlzLm1hcmtlci5hZGRFdmVudExpc3RlbmVyKFwiY2xpY2tcIiwgZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICAgICAgZ2MuZ2V0TG9jYXRpb24oZS5wb2ludCwgZnVuY3Rpb24gKHJzKSB7XHJcbiAgICAgICAgICAgICAgICBzZWxmLnNob3dMb2NhdGlvbkluZm8oZS5wb2ludCwgcnMpO1xyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgbWFwLmFkZE92ZXJsYXkodGhpcy5tYXJrZXIpOyAvL+Wwhuagh+iusOa3u+WKoOWIsOWcsOWbvuS4rVxyXG5cclxuICAgICAgICBnYy5nZXRMb2NhdGlvbihwb2ludCwgZnVuY3Rpb24gKHJzKSB7XHJcbiAgICAgICAgICAgIHNlbGYuc2hvd0xvY2F0aW9uSW5mbyhwb2ludCwgcnMpO1xyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICB0aGlzLmluZm9XaW5kb3cgPSBuZXcgQk1hcC5JbmZvV2luZG93KFwiXCIsIHtcclxuICAgICAgICAgICAgd2lkdGg6IDI1MCxcclxuICAgICAgICAgICAgdGl0bGU6IFwiXCJcclxuICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgdGhpcy5iaW5kRXZlbnRzKCk7XHJcblxyXG4gICAgICAgIHRoaXMuaXNzaG93ID0gdHJ1ZTtcclxuICAgICAgICBpZiAodGhpcy50b3Nob3cpIHtcclxuICAgICAgICAgICAgdGhpcy5zaG93SW5mbygpO1xyXG4gICAgICAgICAgICB0aGlzLnRvc2hvdyA9IGZhbHNlO1xyXG4gICAgICAgIH1cclxuICAgIH07XHJcblxyXG4gICAgQmFpZHVNYXAucHJvdG90eXBlLnNob3dJbmZvID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIGlmICh0aGlzLmlzaGlkZSkgcmV0dXJuO1xyXG4gICAgICAgIGlmICghdGhpcy5pc3Nob3cpIHtcclxuICAgICAgICAgICAgdGhpcy50b3Nob3cgPSB0cnVlO1xyXG4gICAgICAgICAgICByZXR1cm47XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHRoaXMuc2V0SW5mb0NvbnRlbnQoKTtcclxuXHJcbiAgICAgICAgdGhpcy5tYXJrZXIub3BlbkluZm9XaW5kb3codGhpcy5pbmZvV2luZG93KTtcclxuICAgIH07XHJcbiAgICBCYWlkdU1hcC5wcm90b3R5cGUuZ2V0QWRkcmVzcyA9IGZ1bmN0aW9uIChycykge1xyXG4gICAgICAgIHZhciBhZGRDb21wID0gcnMuYWRkcmVzc0NvbXBvbmVudHM7XHJcbiAgICAgICAgaWYoYWRkQ29tcCkge1xyXG4gICAgICAgICAgICByZXR1cm4gYWRkQ29tcC5wcm92aW5jZSArIFwiLCBcIiArIGFkZENvbXAuY2l0eSArIFwiLCBcIiArIGFkZENvbXAuZGlzdHJpY3QgKyBcIiwgXCIgKyBhZGRDb21wLnN0cmVldCArIFwiLCBcIiArIGFkZENvbXAuc3RyZWV0TnVtYmVyO1xyXG4gICAgICAgIH1cclxuICAgIH07XHJcbiAgICBCYWlkdU1hcC5wcm90b3R5cGUuc2V0TG9jYXRlID0gZnVuY3Rpb24gKGFkZHJlc3MpIHtcclxuICAgICAgICAvLyDliJvlu7rlnLDlnYDop6PmnpDlmajlrp7kvotcclxuICAgICAgICB2YXIgbXlHZW8gPSBuZXcgQk1hcC5HZW9jb2RlcigpO1xyXG4gICAgICAgIHZhciBzZWxmID0gdGhpcztcclxuICAgICAgICBteUdlby5nZXRQb2ludChhZGRyZXNzLCBmdW5jdGlvbiAocG9pbnQpIHtcclxuICAgICAgICAgICAgaWYgKHBvaW50KSB7XHJcbiAgICAgICAgICAgICAgICBzZWxmLm1hcC5jZW50ZXJBbmRab29tKHBvaW50LCAxMSk7XHJcbiAgICAgICAgICAgICAgICBzZWxmLm1hcmtlci5zZXRQb3NpdGlvbihwb2ludCk7XHJcbiAgICAgICAgICAgICAgICBteUdlby5nZXRMb2NhdGlvbihwb2ludCwgZnVuY3Rpb24gKHJzKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKHBvaW50LCBycyk7XHJcbiAgICAgICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgIHRvYXN0ci53YXJuaW5nKFwi5Zyw5Z2A5L+h5oGv5LiN5q2j56Gu77yM5a6a5L2N5aSx6LSlXCIpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSwgJycpO1xyXG4gICAgfTtcclxuXHJcblxyXG4gICAgZnVuY3Rpb24gR29vZ2xlTWFwKCkge1xyXG4gICAgICAgIEJhc2VNYXAuY2FsbCh0aGlzLCBcImdvb2dsZVwiKTtcclxuICAgICAgICB0aGlzLmluZm9PcHRzID0ge1xyXG4gICAgICAgICAgICB3aWR0aDogMjUwLCAgICAgLy/kv6Hmga/nqpflj6Plrr3luqZcclxuICAgICAgICAgICAgLy8gICBoZWlnaHQ6IDEwMCwgICAgIC8v5L+h5oGv56qX5Y+j6auY5bqmXHJcbiAgICAgICAgICAgIHRpdGxlOiBcIlwiICAvL+S/oeaBr+eql+WPo+agh+mimFxyXG4gICAgICAgIH07XHJcbiAgICB9XHJcblxyXG4gICAgR29vZ2xlTWFwLnByb3RvdHlwZSA9IG5ldyBCYXNlTWFwKCk7XHJcbiAgICBHb29nbGVNYXAucHJvdG90eXBlLmNvbnN0cnVjdG9yID0gR29vZ2xlTWFwO1xyXG4gICAgR29vZ2xlTWFwLnByb3RvdHlwZS5pc0FQSVJlYWR5ID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHJldHVybiB3aW5kb3dbJ2dvb2dsZSddICYmIHdpbmRvd1snZ29vZ2xlJ11bJ21hcHMnXVxyXG4gICAgfTtcclxuICAgIEdvb2dsZU1hcC5wcm90b3R5cGUuc2V0TWFwID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHZhciBzZWxmID0gdGhpcztcclxuICAgICAgICBpZiAodGhpcy5pc3Nob3cgfHwgdGhpcy5pc2hpZGUpIHJldHVybjtcclxuICAgICAgICBpZiAoIXRoaXMubG9hZEFQSSgpKSByZXR1cm47XHJcblxyXG4gICAgICAgIC8v6K+05piO5Zyw5Zu+5bey5YiH5o2iXHJcbiAgICAgICAgaWYgKHRoaXMubWFwYm94Lmxlbmd0aCA8IDEpIHJldHVybjtcclxuXHJcbiAgICAgICAgdmFyIG1hcCA9IHNlbGYubWFwID0gbmV3IGdvb2dsZS5tYXBzLk1hcCh0aGlzLm1hcGJveFswXSwge1xyXG4gICAgICAgICAgICB6b29tOiAxNSxcclxuICAgICAgICAgICAgZHJhZ2dhYmxlOiB0cnVlLFxyXG4gICAgICAgICAgICBzY2FsZUNvbnRyb2w6IHRydWUsXHJcbiAgICAgICAgICAgIHN0cmVldFZpZXdDb250cm9sOiB0cnVlLFxyXG4gICAgICAgICAgICB6b29tQ29udHJvbDogdHJ1ZVxyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICAvL+iOt+WPlue7j+e6rOW6puWdkOagh+WAvFxyXG4gICAgICAgIHZhciBwb2ludCA9IG5ldyBnb29nbGUubWFwcy5MYXRMbmcodGhpcy5sb2NhdGUpO1xyXG4gICAgICAgIG1hcC5wYW5Ubyhwb2ludCk7XHJcbiAgICAgICAgdGhpcy5tYXJrZXIgPSBuZXcgZ29vZ2xlLm1hcHMuTWFya2VyKHtwb3NpdGlvbjogcG9pbnQsIG1hcDogbWFwLCBkcmFnZ2FibGU6IHRydWV9KTtcclxuXHJcblxyXG4gICAgICAgIHZhciBnYyA9IG5ldyBnb29nbGUubWFwcy5HZW9jb2RlcigpO1xyXG5cclxuICAgICAgICB0aGlzLm1hcmtlci5hZGRMaXN0ZW5lcihcImRyYWdlbmRcIiwgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICBwb2ludCA9IHNlbGYubWFya2VyLmdldFBvc2l0aW9uKCk7XHJcbiAgICAgICAgICAgIGdjLmdlb2NvZGUoeydsb2NhdGlvbic6IHBvaW50fSwgZnVuY3Rpb24gKHJzKSB7XHJcbiAgICAgICAgICAgICAgICBzZWxmLnNob3dMb2NhdGlvbkluZm8ocG9pbnQsIHJzKTtcclxuICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIC8v5re75Yqg5qCH6K6w54K55Ye755uR5ZCsXHJcbiAgICAgICAgdGhpcy5tYXJrZXIuYWRkTGlzdGVuZXIoXCJjbGlja1wiLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIHBvaW50ID0gc2VsZi5tYXJrZXIuZ2V0UG9zaXRpb24oKTtcclxuICAgICAgICAgICAgZ2MuZ2VvY29kZSh7J2xvY2F0aW9uJzogcG9pbnR9LCBmdW5jdGlvbiAocnMpIHtcclxuICAgICAgICAgICAgICAgIHNlbGYuc2hvd0xvY2F0aW9uSW5mbyhwb2ludCwgcnMpO1xyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgdGhpcy5iaW5kRXZlbnRzKCk7XHJcblxyXG4gICAgICAgIGdjLmdlb2NvZGUoeydsb2NhdGlvbic6IHBvaW50fSwgZnVuY3Rpb24gKHJzKSB7XHJcbiAgICAgICAgICAgIHNlbGYuc2hvd0xvY2F0aW9uSW5mbyhwb2ludCwgcnMpO1xyXG4gICAgICAgIH0pO1xyXG4gICAgICAgIHRoaXMuaW5mb1dpbmRvdyA9IG5ldyBnb29nbGUubWFwcy5JbmZvV2luZG93KHttYXA6IG1hcH0pO1xyXG4gICAgICAgIHRoaXMuaW5mb1dpbmRvdy5zZXRQb3NpdGlvbihwb2ludCk7XHJcblxyXG4gICAgICAgIHRoaXMuaXNzaG93ID0gdHJ1ZTtcclxuICAgICAgICBpZiAodGhpcy50b3Nob3cpIHtcclxuICAgICAgICAgICAgdGhpcy5zaG93SW5mbygpO1xyXG4gICAgICAgICAgICB0aGlzLnRvc2hvdyA9IGZhbHNlO1xyXG4gICAgICAgIH1cclxuICAgIH07XHJcblxyXG4gICAgR29vZ2xlTWFwLnByb3RvdHlwZS5zaG93SW5mbyA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICBpZiAodGhpcy5pc2hpZGUpIHJldHVybjtcclxuICAgICAgICBpZiAoIXRoaXMuaXNzaG93KSB7XHJcbiAgICAgICAgICAgIHRoaXMudG9zaG93ID0gdHJ1ZTtcclxuICAgICAgICAgICAgcmV0dXJuO1xyXG4gICAgICAgIH1cclxuICAgICAgICB0aGlzLmluZm9XaW5kb3cuc2V0T3B0aW9ucyh7cG9zaXRpb246IHRoaXMubWFya2VyLmdldFBvc2l0aW9uKCl9KTtcclxuICAgICAgICB0aGlzLnNldEluZm9Db250ZW50KCk7XHJcblxyXG4gICAgfTtcclxuICAgIEdvb2dsZU1hcC5wcm90b3R5cGUuZ2V0QWRkcmVzcyA9IGZ1bmN0aW9uIChycywgc3RhdHVzKSB7XHJcbiAgICAgICAgaWYgKHJzICYmIHJzWzBdKSB7XHJcbiAgICAgICAgICAgIHJldHVybiByc1swXS5mb3JtYXR0ZWRfYWRkcmVzcztcclxuICAgICAgICB9XHJcbiAgICB9O1xyXG4gICAgR29vZ2xlTWFwLnByb3RvdHlwZS5zZXRMb2NhdGUgPSBmdW5jdGlvbiAoYWRkcmVzcykge1xyXG4gICAgICAgIC8vIOWIm+W7uuWcsOWdgOino+aekOWZqOWunuS+i1xyXG4gICAgICAgIHZhciBteUdlbyA9IG5ldyBnb29nbGUubWFwcy5HZW9jb2RlcigpO1xyXG4gICAgICAgIHZhciBzZWxmID0gdGhpcztcclxuICAgICAgICBteUdlby5nZXRQb2ludChhZGRyZXNzLCBmdW5jdGlvbiAocG9pbnQpIHtcclxuICAgICAgICAgICAgaWYgKHBvaW50KSB7XHJcbiAgICAgICAgICAgICAgICBzZWxmLm1hcC5jZW50ZXJBbmRab29tKHBvaW50LCAxMSk7XHJcbiAgICAgICAgICAgICAgICBzZWxmLm1hcmtlci5zZXRQb3NpdGlvbihwb2ludCk7XHJcbiAgICAgICAgICAgICAgICBteUdlby5nZXRMb2NhdGlvbihwb2ludCwgZnVuY3Rpb24gKHJzKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKHBvaW50LCBycyk7XHJcbiAgICAgICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgIHRvYXN0ci53YXJuaW5nKFwi5Zyw5Z2A5L+h5oGv5LiN5q2j56Gu77yM5a6a5L2N5aSx6LSlXCIpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSwgJycpO1xyXG4gICAgfTtcclxuXHJcbiAgICBmdW5jdGlvbiBUZW5jZW50TWFwKCkge1xyXG4gICAgICAgIEJhc2VNYXAuY2FsbCh0aGlzLCBcInRlbmNlbnRcIik7XHJcbiAgICB9XHJcblxyXG4gICAgVGVuY2VudE1hcC5wcm90b3R5cGUgPSBuZXcgQmFzZU1hcCgpO1xyXG4gICAgVGVuY2VudE1hcC5wcm90b3R5cGUuY29uc3RydWN0b3IgPSBUZW5jZW50TWFwO1xyXG4gICAgVGVuY2VudE1hcC5wcm90b3R5cGUuaXNBUElSZWFkeSA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICByZXR1cm4gd2luZG93WydxcSddICYmIHdpbmRvd1sncXEnXVsnbWFwcyddO1xyXG4gICAgfTtcclxuXHJcbiAgICBUZW5jZW50TWFwLnByb3RvdHlwZS5zZXRNYXAgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgdmFyIHNlbGYgPSB0aGlzO1xyXG4gICAgICAgIGlmICh0aGlzLmlzc2hvdyB8fCB0aGlzLmlzaGlkZSkgcmV0dXJuO1xyXG4gICAgICAgIGlmICghdGhpcy5sb2FkQVBJKCkpIHJldHVybjtcclxuXHJcblxyXG4gICAgICAgIC8v5Yid5aeL5YyW5Zyw5Zu+XHJcbiAgICAgICAgdmFyIG1hcCA9IHNlbGYubWFwID0gbmV3IHFxLm1hcHMuTWFwKHRoaXMubWFwYm94WzBdLCB7em9vbTogMTV9KTtcclxuICAgICAgICAvL+WIneWni+WMluWcsOWbvuaOp+S7tlxyXG4gICAgICAgIG5ldyBxcS5tYXBzLlNjYWxlQ29udHJvbCh7XHJcbiAgICAgICAgICAgIGFsaWduOiBxcS5tYXBzLkFMSUdOLkJPVFRPTV9MRUZULFxyXG4gICAgICAgICAgICBtYXJnaW46IHFxLm1hcHMuU2l6ZSg4NSwgMTUpLFxyXG4gICAgICAgICAgICBtYXA6IG1hcFxyXG4gICAgICAgIH0pO1xyXG4gICAgICAgIC8vbWFwLmFkZENvbnRyb2wobmV3IEJNYXAuT3ZlcnZpZXdNYXBDb250cm9sKCkpO1xyXG4gICAgICAgIC8vbWFwLmVuYWJsZVNjcm9sbFdoZWVsWm9vbSgpO1xyXG5cclxuICAgICAgICAvL+iOt+WPlue7j+e6rOW6puWdkOagh+WAvFxyXG4gICAgICAgIHZhciBwb2ludCA9IG5ldyBxcS5tYXBzLkxhdExuZyh0aGlzLmxvY2F0ZS5sYXQsIHRoaXMubG9jYXRlLmxuZyk7XHJcbiAgICAgICAgbWFwLnBhblRvKHBvaW50KTsgLy/liJ3lp4vljJblnLDlm77kuK3lv4PngrlcclxuXHJcbiAgICAgICAgLy/liJ3lp4vljJblnLDlm77moIforrBcclxuICAgICAgICB0aGlzLm1hcmtlciA9IG5ldyBxcS5tYXBzLk1hcmtlcih7XHJcbiAgICAgICAgICAgIHBvc2l0aW9uOiBwb2ludCxcclxuICAgICAgICAgICAgZHJhZ2dhYmxlOiB0cnVlLFxyXG4gICAgICAgICAgICBtYXA6IG1hcFxyXG4gICAgICAgIH0pO1xyXG4gICAgICAgIHRoaXMubWFya2VyLnNldEFuaW1hdGlvbihxcS5tYXBzLk1hcmtlckFuaW1hdGlvbi5ET1dOKTtcclxuXHJcbiAgICAgICAgLy/lnLDlnYDop6PmnpDnsbtcclxuICAgICAgICB2YXIgZ2MgPSBuZXcgcXEubWFwcy5HZW9jb2Rlcih7XHJcbiAgICAgICAgICAgIGNvbXBsZXRlOiBmdW5jdGlvbiAocnMpIHtcclxuICAgICAgICAgICAgICAgIHNlbGYuc2hvd0xvY2F0aW9uSW5mbyhwb2ludCwgcnMpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIHFxLm1hcHMuZXZlbnQuYWRkTGlzdGVuZXIodGhpcy5tYXJrZXIsICdjbGljaycsIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgcG9pbnQgPSBzZWxmLm1hcmtlci5nZXRQb3NpdGlvbigpO1xyXG4gICAgICAgICAgICBnYy5nZXRBZGRyZXNzKHBvaW50KTtcclxuICAgICAgICB9KTtcclxuICAgICAgICAvL+iuvue9rk1hcmtlcuWBnOatouaLluWKqOS6i+S7tlxyXG4gICAgICAgIHFxLm1hcHMuZXZlbnQuYWRkTGlzdGVuZXIodGhpcy5tYXJrZXIsICdkcmFnZW5kJywgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICBwb2ludCA9IHNlbGYubWFya2VyLmdldFBvc2l0aW9uKCk7XHJcbiAgICAgICAgICAgIGdjLmdldEFkZHJlc3MocG9pbnQpO1xyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICBnYy5nZXRBZGRyZXNzKHBvaW50KTtcclxuXHJcbiAgICAgICAgdGhpcy5iaW5kRXZlbnRzKCk7XHJcblxyXG4gICAgICAgIHRoaXMuaW5mb1dpbmRvdyA9IG5ldyBxcS5tYXBzLkluZm9XaW5kb3coe21hcDogbWFwfSk7XHJcblxyXG4gICAgICAgIHRoaXMuaXNzaG93ID0gdHJ1ZTtcclxuICAgICAgICBpZiAodGhpcy50b3Nob3cpIHtcclxuICAgICAgICAgICAgdGhpcy5zaG93SW5mbygpO1xyXG4gICAgICAgICAgICB0aGlzLnRvc2hvdyA9IGZhbHNlO1xyXG4gICAgICAgIH1cclxuICAgIH07XHJcblxyXG4gICAgVGVuY2VudE1hcC5wcm90b3R5cGUuc2hvd0luZm8gPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgaWYgKHRoaXMuaXNoaWRlKSByZXR1cm47XHJcbiAgICAgICAgaWYgKCF0aGlzLmlzc2hvdykge1xyXG4gICAgICAgICAgICB0aGlzLnRvc2hvdyA9IHRydWU7XHJcbiAgICAgICAgICAgIHJldHVybjtcclxuICAgICAgICB9XHJcbiAgICAgICAgdGhpcy5pbmZvV2luZG93Lm9wZW4oKTtcclxuICAgICAgICB0aGlzLnNldEluZm9Db250ZW50KCk7XHJcbiAgICAgICAgdGhpcy5pbmZvV2luZG93LnNldFBvc2l0aW9uKHRoaXMubWFya2VyLmdldFBvc2l0aW9uKCkpO1xyXG4gICAgfTtcclxuXHJcbiAgICBUZW5jZW50TWFwLnByb3RvdHlwZS5nZXRBZGRyZXNzID0gZnVuY3Rpb24gKHJzKSB7XHJcbiAgICAgICAgaWYocnMgJiYgcnMuZGV0YWlsKSB7XHJcbiAgICAgICAgICAgIHJldHVybiBycy5kZXRhaWwuYWRkcmVzcztcclxuICAgICAgICB9XHJcbiAgICB9O1xyXG5cclxuICAgIFRlbmNlbnRNYXAucHJvdG90eXBlLnNldExvY2F0ZSA9IGZ1bmN0aW9uIChhZGRyZXNzKSB7XHJcbiAgICAgICAgLy8g5Yib5bu65Zyw5Z2A6Kej5p6Q5Zmo5a6e5L6LXHJcbiAgICAgICAgdmFyIHNlbGYgPSB0aGlzO1xyXG4gICAgICAgIHZhciBteUdlbyA9IG5ldyBxcS5tYXBzLkdlb2NvZGVyKHtcclxuICAgICAgICAgICAgY29tcGxldGU6IGZ1bmN0aW9uIChyZXN1bHQpIHtcclxuICAgICAgICAgICAgICAgIGlmKHJlc3VsdCAmJiByZXN1bHQuZGV0YWlsICYmIHJlc3VsdC5kZXRhaWwubG9jYXRpb24pe1xyXG4gICAgICAgICAgICAgICAgICAgIHZhciBwb2ludD1yZXN1bHQuZGV0YWlsLmxvY2F0aW9uO1xyXG4gICAgICAgICAgICAgICAgICAgIHNlbGYubWFwLnNldENlbnRlcihwb2ludCk7XHJcbiAgICAgICAgICAgICAgICAgICAgc2VsZi5tYXJrZXIuc2V0UG9zaXRpb24ocG9pbnQpO1xyXG4gICAgICAgICAgICAgICAgICAgIHNlbGYuc2hvd0xvY2F0aW9uSW5mbyhwb2ludCwgcmVzdWx0KTtcclxuICAgICAgICAgICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICAgICAgICAgIHRvYXN0ci53YXJuaW5nKFwi5Zyw5Z2A5L+h5oGv5LiN5q2j56Gu77yM5a6a5L2N5aSx6LSlXCIpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICBlcnJvcjpmdW5jdGlvbihyZXN1bHQpe1xyXG4gICAgICAgICAgICAgICAgdG9hc3RyLndhcm5pbmcoXCLlnLDlnYDkv6Hmga/kuI3mraPnoa7vvIzlrprkvY3lpLHotKVcIik7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KTtcclxuICAgICAgICBteUdlby5nZXRMb2NhdGlvbihhZGRyZXNzKTtcclxuICAgIH07XHJcblxyXG5cclxuICAgIGZ1bmN0aW9uIEdhb2RlTWFwKCkge1xyXG4gICAgICAgIEJhc2VNYXAuY2FsbCh0aGlzLCBcImdhb2RlXCIpO1xyXG4gICAgICAgIHRoaXMuaW5mb09wdHMgPSB7XHJcbiAgICAgICAgICAgIHdpZHRoOiAyNTAsICAgICAvL+S/oeaBr+eql+WPo+WuveW6plxyXG4gICAgICAgICAgICAvLyAgIGhlaWdodDogMTAwLCAgICAgLy/kv6Hmga/nqpflj6Ppq5jluqZcclxuICAgICAgICAgICAgdGl0bGU6IFwiXCIgIC8v5L+h5oGv56qX5Y+j5qCH6aKYXHJcbiAgICAgICAgfTtcclxuICAgIH1cclxuXHJcbiAgICBHYW9kZU1hcC5wcm90b3R5cGUgPSBuZXcgQmFzZU1hcCgpO1xyXG4gICAgR2FvZGVNYXAucHJvdG90eXBlLmNvbnN0cnVjdG9yID0gR2FvZGVNYXA7XHJcbiAgICBHYW9kZU1hcC5wcm90b3R5cGUuaXNBUElSZWFkeSA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICByZXR1cm4gISF3aW5kb3dbJ0FNYXAnXVxyXG4gICAgfTtcclxuXHJcbiAgICBHYW9kZU1hcC5wcm90b3R5cGUuc2V0TWFwID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHZhciBzZWxmID0gdGhpcztcclxuICAgICAgICBpZiAodGhpcy5pc3Nob3cgfHwgdGhpcy5pc2hpZGUpIHJldHVybjtcclxuICAgICAgICBpZiAoIXRoaXMubG9hZEFQSSgpKSByZXR1cm47XHJcblxyXG5cclxuICAgICAgICB2YXIgbWFwID0gc2VsZi5tYXAgPSBuZXcgQU1hcC5NYXAodGhpcy5tYXBib3guYXR0cignaWQnKSwge1xyXG4gICAgICAgICAgICByZXNpemVFbmFibGU6IHRydWUsXHJcbiAgICAgICAgICAgIGRyYWdFbmFibGU6IHRydWUsXHJcbiAgICAgICAgICAgIHpvb206IDEzXHJcbiAgICAgICAgfSk7XHJcbiAgICAgICAgbWFwLnBsdWdpbihbXCJBTWFwLlRvb2xCYXJcIiwgXCJBTWFwLlNjYWxlXCIsIFwiQU1hcC5PdmVyVmlld1wiXSwgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICBtYXAuYWRkQ29udHJvbChuZXcgQU1hcC5Ub29sQmFyKCkpO1xyXG4gICAgICAgICAgICBtYXAuYWRkQ29udHJvbChuZXcgQU1hcC5TY2FsZSgpKTtcclxuICAgICAgICAgICAgbWFwLmFkZENvbnRyb2wobmV3IEFNYXAuT3ZlclZpZXcoKSk7XHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgICQoJ1tuYW1lPXR4dExhbmddJykudW5iaW5kKCkub24oJ2NoYW5nZScsIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgdmFyIGxhbmcgPSAkKHRoaXMpLnZhbCgpO1xyXG4gICAgICAgICAgICBpZiAobGFuZykgbWFwLnNldExhbmcobGFuZyk7XHJcbiAgICAgICAgfSkudHJpZ2dlcignY2hhbmdlJyk7XHJcblxyXG5cclxuICAgICAgICAvL+iOt+WPlue7j+e6rOW6puWdkOagh+WAvFxyXG4gICAgICAgIHZhciBwb2ludCA9IG5ldyBBTWFwLkxuZ0xhdCh0aGlzLmxvY2F0ZS5sbmcsIHRoaXMubG9jYXRlLmxhdCk7XHJcbiAgICAgICAgbWFwLnNldENlbnRlcihwb2ludCk7XHJcblxyXG4gICAgICAgIHRoaXMubWFya2VyID0gbmV3IEFNYXAuTWFya2VyKHtwb3NpdGlvbjogcG9pbnQsIG1hcDogbWFwfSk7IC8v5Yid5aeL5YyW5Zyw5Zu+5qCH6K6wXHJcbiAgICAgICAgdGhpcy5tYXJrZXIuc2V0RHJhZ2dhYmxlKHRydWUpOyAvL+agh+iusOW8gOWQr+aLluaLvVxyXG5cclxuXHJcbiAgICAgICAgdGhpcy5pbmZvV2luZG93ID0gbmV3IEFNYXAuSW5mb1dpbmRvdygpO1xyXG4gICAgICAgIHRoaXMuaW5mb1dpbmRvdy5vcGVuKG1hcCwgcG9pbnQpO1xyXG5cclxuICAgICAgICBtYXAucGx1Z2luKFtcIkFNYXAuR2VvY29kZXJcIl0sIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgdmFyIGdjID0gbmV3IEFNYXAuR2VvY29kZXIoKTsgLy/lnLDlnYDop6PmnpDnsbtcclxuICAgICAgICAgICAgLy/mt7vliqDmoIforrDmi5bmi73nm5HlkKxcclxuICAgICAgICAgICAgc2VsZi5tYXJrZXIub24oXCJkcmFnZW5kXCIsIGZ1bmN0aW9uIChlKSB7XHJcbiAgICAgICAgICAgICAgICAvL+iOt+WPluWcsOWdgOS/oeaBr1xyXG4gICAgICAgICAgICAgICAgZ2MuZ2V0QWRkcmVzcyhlLmxuZ2xhdCwgZnVuY3Rpb24gKHN0LCBycykge1xyXG4gICAgICAgICAgICAgICAgICAgIHNlbGYuc2hvd0xvY2F0aW9uSW5mbyhlLmxuZ2xhdCwgcnMpO1xyXG4gICAgICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgICAgIH0pO1xyXG5cclxuICAgICAgICAgICAgLy/mt7vliqDmoIforrDngrnlh7vnm5HlkKxcclxuICAgICAgICAgICAgc2VsZi5tYXJrZXIub24oXCJjbGlja1wiLCBmdW5jdGlvbiAoZSkge1xyXG4gICAgICAgICAgICAgICAgZ2MuZ2V0QWRkcmVzcyhlLmxuZ2xhdCwgZnVuY3Rpb24gKHN0LCBycykge1xyXG4gICAgICAgICAgICAgICAgICAgIHNlbGYuc2hvd0xvY2F0aW9uSW5mbyhlLmxuZ2xhdCwgcnMpO1xyXG4gICAgICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgICAgIH0pO1xyXG5cclxuICAgICAgICAgICAgZ2MuZ2V0QWRkcmVzcyhwb2ludCwgZnVuY3Rpb24gKHN0LCBycykge1xyXG4gICAgICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKHBvaW50LCBycyk7XHJcbiAgICAgICAgICAgIH0pO1xyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICB0aGlzLmJpbmRFdmVudHMoKTtcclxuXHJcbiAgICAgICAgdGhpcy5pc3Nob3cgPSB0cnVlO1xyXG4gICAgICAgIGlmICh0aGlzLnRvc2hvdykge1xyXG4gICAgICAgICAgICB0aGlzLnNob3dJbmZvKCk7XHJcbiAgICAgICAgICAgIHRoaXMudG9zaG93ID0gZmFsc2U7XHJcbiAgICAgICAgfVxyXG4gICAgfTtcclxuXHJcbiAgICBHYW9kZU1hcC5wcm90b3R5cGUuc2hvd0luZm8gPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgaWYgKHRoaXMuaXNoaWRlKSByZXR1cm47XHJcbiAgICAgICAgaWYgKCF0aGlzLmlzc2hvdykge1xyXG4gICAgICAgICAgICB0aGlzLnRvc2hvdyA9IHRydWU7XHJcbiAgICAgICAgICAgIHJldHVybjtcclxuICAgICAgICB9XHJcbiAgICAgICAgdGhpcy5zZXRJbmZvQ29udGVudCgpO1xyXG4gICAgICAgIHRoaXMuaW5mb1dpbmRvdy5zZXRQb3NpdGlvbih0aGlzLm1hcmtlci5nZXRQb3NpdGlvbigpKTtcclxuICAgIH07XHJcblxyXG4gICAgR2FvZGVNYXAucHJvdG90eXBlLmdldEFkZHJlc3MgPSBmdW5jdGlvbiAocnMpIHtcclxuICAgICAgICByZXR1cm4gcnMucmVnZW9jb2RlLmZvcm1hdHRlZEFkZHJlc3M7XHJcbiAgICB9O1xyXG5cclxuICAgIEdhb2RlTWFwLnByb3RvdHlwZS5zZXRMb2NhdGUgPSBmdW5jdGlvbiAoYWRkcmVzcykge1xyXG4gICAgICAgIC8vIOWIm+W7uuWcsOWdgOino+aekOWZqOWunuS+i1xyXG4gICAgICAgIHZhciBteUdlbyA9IG5ldyBBTWFwLkdlb2NvZGVyKCk7XHJcbiAgICAgICAgdmFyIHNlbGYgPSB0aGlzO1xyXG4gICAgICAgIG15R2VvLmdldFBvaW50KGFkZHJlc3MsIGZ1bmN0aW9uIChwb2ludCkge1xyXG4gICAgICAgICAgICBpZiAocG9pbnQpIHtcclxuICAgICAgICAgICAgICAgIHNlbGYubWFwLmNlbnRlckFuZFpvb20ocG9pbnQsIDExKTtcclxuICAgICAgICAgICAgICAgIHNlbGYubWFya2VyLnNldFBvc2l0aW9uKHBvaW50KTtcclxuICAgICAgICAgICAgICAgIG15R2VvLmdldExvY2F0aW9uKHBvaW50LCBmdW5jdGlvbiAocnMpIHtcclxuICAgICAgICAgICAgICAgICAgICBzZWxmLnNob3dMb2NhdGlvbkluZm8ocG9pbnQsIHJzKTtcclxuICAgICAgICAgICAgICAgIH0pO1xyXG4gICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgdG9hc3RyLndhcm5pbmcoXCLlnLDlnYDkv6Hmga/kuI3mraPnoa7vvIzlrprkvY3lpLHotKVcIik7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9LCAnJyk7XHJcbiAgICB9O1xyXG5cclxuICAgIHdpbmRvdy5Jbml0TWFwPUluaXRNYXA7XHJcbn0pKHdpbmRvdyxqUXVlcnkpOyIsImpRdWVyeShmdW5jdGlvbiAoJCkge1xyXG4gICAgLy/pq5jkuq7lvZPliY3pgInkuK3nmoTlr7zoiKpcclxuICAgIHZhciBicmVhZCA9ICQoXCIuYnJlYWRjcnVtYlwiKTtcclxuICAgIHZhciBtZW51ID0gYnJlYWQuZGF0YSgnbWVudScpO1xyXG4gICAgaWYgKG1lbnUpIHtcclxuICAgICAgICB2YXIgbGluayA9ICQoJy5zaWRlLW5hdiBhW2RhdGEta2V5PScgKyBtZW51ICsgJ10nKTtcclxuXHJcbiAgICAgICAgdmFyIGh0bWwgPSBbXTtcclxuICAgICAgICBpZiAobGluay5sZW5ndGggPiAwKSB7XHJcbiAgICAgICAgICAgIGlmIChsaW5rLmlzKCcubWVudV90b3AnKSkge1xyXG4gICAgICAgICAgICAgICAgaHRtbC5wdXNoKCc8bGkgY2xhc3M9XCJicmVhZGNydW1iLWl0ZW1cIj48YSBocmVmPVwiamF2YXNjcmlwdDpcIj48aSBjbGFzcz1cIicgKyBsaW5rLmZpbmQoJ2knKS5hdHRyKCdjbGFzcycpICsgJ1wiPjwvaT4mbmJzcDsnICsgbGluay50ZXh0KCkgKyAnPC9hPjwvbGk+Jyk7XHJcbiAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICB2YXIgcGFyZW50ID0gbGluay5wYXJlbnRzKCcuY29sbGFwc2UnKS5lcSgwKTtcclxuICAgICAgICAgICAgICAgIHBhcmVudC5hZGRDbGFzcygnc2hvdycpO1xyXG4gICAgICAgICAgICAgICAgbGluay5hZGRDbGFzcyhcImFjdGl2ZVwiKTtcclxuICAgICAgICAgICAgICAgIHZhciB0b3BtZW51ID0gcGFyZW50LnNpYmxpbmdzKCcuY2FyZC1oZWFkZXInKS5maW5kKCdhLm1lbnVfdG9wJyk7XHJcbiAgICAgICAgICAgICAgICBodG1sLnB1c2goJzxsaSBjbGFzcz1cImJyZWFkY3J1bWItaXRlbVwiPjxhIGhyZWY9XCJqYXZhc2NyaXB0OlwiPjxpIGNsYXNzPVwiJyArIHRvcG1lbnUuZmluZCgnaScpLmF0dHIoJ2NsYXNzJykgKyAnXCI+PC9pPiZuYnNwOycgKyB0b3BtZW51LnRleHQoKSArICc8L2E+PC9saT4nKTtcclxuICAgICAgICAgICAgICAgIGh0bWwucHVzaCgnPGxpIGNsYXNzPVwiYnJlYWRjcnVtYi1pdGVtXCI+PGEgaHJlZj1cImphdmFzY3JpcHQ6XCI+JyArIGxpbmsudGV4dCgpICsgJzwvYT48L2xpPicpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHZhciB0aXRsZSA9IGJyZWFkLmRhdGEoJ3RpdGxlJyk7XHJcbiAgICAgICAgaWYgKHRpdGxlKSB7XHJcbiAgICAgICAgICAgIGh0bWwucHVzaCgnPGxpIGNsYXNzPVwiYnJlYWRjcnVtYi1pdGVtIGFjdGl2ZVwiIGFyaWEtY3VycmVudD1cInBhZ2VcIj4nICsgdGl0bGUgKyAnPC9saT4nKTtcclxuICAgICAgICB9XHJcbiAgICAgICAgYnJlYWQuaHRtbChodG1sLmpvaW4oXCJcXG5cIikpO1xyXG4gICAgfVxyXG5cclxuICAgIC8v5YWo6YCJ44CB5Y+N6YCJ5oyJ6ZKuXHJcbiAgICAkKCcuY2hlY2thbGwtYnRuJykuY2xpY2soZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICB2YXIgdGFyZ2V0ID0gJCh0aGlzKS5kYXRhKCd0YXJnZXQnKTtcclxuICAgICAgICBpZiAoIXRhcmdldCkgdGFyZ2V0ID0gJ2lkJztcclxuICAgICAgICB2YXIgaWRzID0gJCgnW25hbWU9JyArIHRhcmdldCArICddJyk7XHJcbiAgICAgICAgaWYgKCQodGhpcykuaXMoJy5hY3RpdmUnKSkge1xyXG4gICAgICAgICAgICBpZHMucmVtb3ZlQXR0cignY2hlY2tlZCcpO1xyXG4gICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgIGlkcy5hdHRyKCdjaGVja2VkJywgdHJ1ZSk7XHJcbiAgICAgICAgfVxyXG4gICAgfSk7XHJcbiAgICAkKCcuY2hlY2tyZXZlcnNlLWJ0bicpLmNsaWNrKGZ1bmN0aW9uIChlKSB7XHJcbiAgICAgICAgdmFyIHRhcmdldCA9ICQodGhpcykuZGF0YSgndGFyZ2V0Jyk7XHJcbiAgICAgICAgaWYgKCF0YXJnZXQpIHRhcmdldCA9ICdpZCc7XHJcbiAgICAgICAgdmFyIGlkcyA9ICQoJ1tuYW1lPScgKyB0YXJnZXQgKyAnXScpO1xyXG4gICAgICAgIGZvciAodmFyIGkgPSAwOyBpIDwgaWRzLmxlbmd0aDsgaSsrKSB7XHJcbiAgICAgICAgICAgIGlmIChpZHNbaV0uY2hlY2tlZCkge1xyXG4gICAgICAgICAgICAgICAgaWRzLmVxKGkpLnJlbW92ZUF0dHIoJ2NoZWNrZWQnKTtcclxuICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgIGlkcy5lcShpKS5hdHRyKCdjaGVja2VkJywgdHJ1ZSk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9XHJcbiAgICB9KTtcclxuICAgIC8v5pON5L2c5oyJ6ZKuXHJcbiAgICAkKCcuYWN0aW9uLWJ0bicpLmNsaWNrKGZ1bmN0aW9uIChlKSB7XHJcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xyXG4gICAgICAgIHZhciBhY3Rpb24gPSAkKHRoaXMpLmRhdGEoJ2FjdGlvbicpO1xyXG4gICAgICAgIGlmICghYWN0aW9uKSB7XHJcbiAgICAgICAgICAgIHJldHVybiB0b2FzdHIuZXJyb3IoJ+acquefpeaTjeS9nCcpO1xyXG4gICAgICAgIH1cclxuICAgICAgICBhY3Rpb24gPSAnYWN0aW9uJyArIGFjdGlvbi5yZXBsYWNlKC9eW2Etel0vLCBmdW5jdGlvbiAobGV0dGVyKSB7XHJcbiAgICAgICAgICAgIHJldHVybiBsZXR0ZXIudG9VcHBlckNhc2UoKTtcclxuICAgICAgICB9KTtcclxuICAgICAgICBpZiAoIXdpbmRvd1thY3Rpb25dIHx8IHR5cGVvZiB3aW5kb3dbYWN0aW9uXSAhPT0gJ2Z1bmN0aW9uJykge1xyXG4gICAgICAgICAgICByZXR1cm4gdG9hc3RyLmVycm9yKCfmnKrnn6Xmk43kvZwnKTtcclxuICAgICAgICB9XHJcbiAgICAgICAgdmFyIG5lZWRDaGVja3MgPSAkKHRoaXMpLmRhdGEoJ25lZWRDaGVja3MnKTtcclxuICAgICAgICBpZiAobmVlZENoZWNrcyA9PT0gdW5kZWZpbmVkKSBuZWVkQ2hlY2tzID0gdHJ1ZTtcclxuICAgICAgICBpZiAobmVlZENoZWNrcykge1xyXG4gICAgICAgICAgICB2YXIgdGFyZ2V0ID0gJCh0aGlzKS5kYXRhKCd0YXJnZXQnKTtcclxuICAgICAgICAgICAgaWYgKCF0YXJnZXQpIHRhcmdldCA9ICdpZCc7XHJcbiAgICAgICAgICAgIHZhciBpZHMgPSAkKCdbbmFtZT0nICsgdGFyZ2V0ICsgJ106Y2hlY2tlZCcpO1xyXG4gICAgICAgICAgICBpZiAoaWRzLmxlbmd0aCA8IDEpIHtcclxuICAgICAgICAgICAgICAgIHJldHVybiB0b2FzdHIud2FybmluZygn6K+36YCJ5oup6ZyA6KaB5pON5L2c55qE6aG555uuJyk7XHJcbiAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICB2YXIgaWRjaGVja3MgPSBbXTtcclxuICAgICAgICAgICAgICAgIGZvciAodmFyIGkgPSAwOyBpIDwgaWRzLmxlbmd0aDsgaSsrKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgaWRjaGVja3MucHVzaChpZHMuZXEoaSkudmFsKCkpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgd2luZG93W2FjdGlvbl0oaWRjaGVja3MpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgd2luZG93W2FjdGlvbl0oKTtcclxuICAgICAgICB9XHJcbiAgICB9KTtcclxuXHJcbiAgICAvL+W8guatpeaYvuekuui1hOaWmemTvuaOpVxyXG4gICAgJCgnYVtyZWw9YWpheF0nKS5jbGljayhmdW5jdGlvbiAoZSkge1xyXG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcclxuICAgICAgICB2YXIgc2VsZiA9ICQodGhpcyk7XHJcbiAgICAgICAgdmFyIHRpdGxlID0gJCh0aGlzKS5kYXRhKCd0aXRsZScpO1xyXG4gICAgICAgIGlmICghdGl0bGUpIHRpdGxlID0gJCh0aGlzKS50ZXh0KCk7XHJcbiAgICAgICAgdmFyIGRsZyA9IG5ldyBEaWFsb2coe1xyXG4gICAgICAgICAgICBidG5zOiBbJ+ehruWumiddLFxyXG4gICAgICAgICAgICBvbnNob3c6IGZ1bmN0aW9uIChib2R5KSB7XHJcbiAgICAgICAgICAgICAgICAkLmFqYXgoe1xyXG4gICAgICAgICAgICAgICAgICAgIHVybDogc2VsZi5hdHRyKCdocmVmJyksXHJcbiAgICAgICAgICAgICAgICAgICAgc3VjY2VzczogZnVuY3Rpb24gKHRleHQpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgYm9keS5odG1sKHRleHQpO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIH0pO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkuc2hvdygnPHAgY2xhc3M9XCJsb2FkaW5nXCI+5Yqg6L295LitLi4uPC9wPicsIHRpdGxlKTtcclxuXHJcbiAgICB9KTtcclxuXHJcbiAgICAkKCcubmF2LXRhYnMgYScpLmNsaWNrKGZ1bmN0aW9uIChlKSB7XHJcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xyXG4gICAgICAgICQodGhpcykudGFiKCdzaG93Jyk7XHJcbiAgICB9KTtcclxuXHJcbiAgICAvL+S4iuS8oOahhlxyXG4gICAgJCgnLmN1c3RvbS1maWxlIC5jdXN0b20tZmlsZS1pbnB1dCcpLm9uKCdjaGFuZ2UnLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgdmFyIGxhYmVsID0gJCh0aGlzKS5wYXJlbnRzKCcuY3VzdG9tLWZpbGUnKS5maW5kKCcuY3VzdG9tLWZpbGUtbGFiZWwnKTtcclxuICAgICAgICBsYWJlbC50ZXh0KCQodGhpcykudmFsKCkpO1xyXG4gICAgfSk7XHJcblxyXG4gICAgLy/ooajljZVBamF45o+Q5LqkXHJcbiAgICAkKCcuYnRuLXByaW1hcnlbdHlwZT1zdWJtaXRdJykuY2xpY2soZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICB2YXIgZm9ybSA9ICQodGhpcykucGFyZW50cygnZm9ybScpO1xyXG4gICAgICAgIHZhciBidG4gPSB0aGlzO1xyXG4gICAgICAgIHZhciBvcHRpb25zID0ge1xyXG4gICAgICAgICAgICB1cmw6ICQoZm9ybSkuYXR0cignYWN0aW9uJyksXHJcbiAgICAgICAgICAgIHR5cGU6ICdQT1NUJyxcclxuICAgICAgICAgICAgZGF0YVR5cGU6ICdKU09OJyxcclxuICAgICAgICAgICAgc3VjY2VzczogZnVuY3Rpb24gKGpzb24pIHtcclxuICAgICAgICAgICAgICAgIGlmIChqc29uLmNvZGUgPT0gMSkge1xyXG4gICAgICAgICAgICAgICAgICAgIG5ldyBEaWFsb2coe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBvbmhpZGRlbjogZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGpzb24udXJsKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbG9jYXRpb24uaHJlZiA9IGpzb24udXJsO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBsb2NhdGlvbi5yZWxvYWQoKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgIH0pLnNob3coanNvbi5tc2cpO1xyXG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgICAgICB0b2FzdHIud2FybmluZyhqc29uLm1zZyk7XHJcbiAgICAgICAgICAgICAgICAgICAgJChidG4pLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9O1xyXG4gICAgICAgIGlmIChmb3JtLmF0dHIoJ2VuY3R5cGUnKSA9PT0gJ211bHRpcGFydC9mb3JtLWRhdGEnKSB7XHJcbiAgICAgICAgICAgIGlmICghRm9ybURhdGEpIHtcclxuICAgICAgICAgICAgICAgIHJldHVybiB0cnVlO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIG9wdGlvbnMuZGF0YSA9IG5ldyBGb3JtRGF0YShmb3JtWzBdKTtcclxuICAgICAgICAgICAgb3B0aW9ucy5jYWNoZSA9IGZhbHNlO1xyXG4gICAgICAgICAgICBvcHRpb25zLnByb2Nlc3NEYXRhID0gZmFsc2U7XHJcbiAgICAgICAgICAgIG9wdGlvbnMuY29udGVudFR5cGUgPSBmYWxzZTtcclxuICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICBvcHRpb25zLmRhdGEgPSAkKGZvcm0pLnNlcmlhbGl6ZSgpO1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xyXG4gICAgICAgICQodGhpcykuYXR0cignZGlzYWJsZWQnLCB0cnVlKTtcclxuICAgICAgICAkLmFqYXgob3B0aW9ucyk7XHJcbiAgICB9KTtcclxuXHJcbiAgICAkKCcucGlja3VzZXInKS5jbGljayhmdW5jdGlvbiAoZSkge1xyXG4gICAgICAgIHZhciBncm91cCA9ICQodGhpcykucGFyZW50cygnLmlucHV0LWdyb3VwJyk7XHJcbiAgICAgICAgdmFyIGlkZWxlID0gZ3JvdXAuZmluZCgnW25hbWU9bWVtYmVyX2lkXScpO1xyXG4gICAgICAgIHZhciBpbmZvZWxlID0gZ3JvdXAuZmluZCgnW25hbWU9bWVtYmVyX2luZm9dJyk7XHJcbiAgICAgICAgZGlhbG9nLnBpY2tVc2VyKCQodGhpcykuZGF0YSgndXJsJyksIGZ1bmN0aW9uICh1c2VyKSB7XHJcbiAgICAgICAgICAgIGlkZWxlLnZhbCh1c2VyLmlkKTtcclxuICAgICAgICAgICAgaW5mb2VsZS52YWwoJ1snICsgdXNlci5pZCArICddICcgKyB1c2VyLnVzZXJuYW1lICsgKHVzZXIubW9iaWxlID8gKCcgLyAnICsgdXNlci5tb2JpbGUpIDogJycpKTtcclxuICAgICAgICB9LCAkKHRoaXMpLmRhdGEoJ2ZpbHRlcicpKTtcclxuICAgIH0pO1xyXG4gICAgJCgnLnBpY2stbG9jYXRlJykuY2xpY2soZnVuY3Rpb24oZSl7XHJcbiAgICAgICAgdmFyIGdyb3VwPSQodGhpcykucGFyZW50cygnLmlucHV0LWdyb3VwJyk7XHJcbiAgICAgICAgdmFyIGlkZWxlPWdyb3VwLmZpbmQoJ2lucHV0W3R5cGU9dGV4dF0nKTtcclxuICAgICAgICBkaWFsb2cucGlja0xvY2F0ZSgncXEnLGZ1bmN0aW9uKGxvY2F0ZSl7XHJcbiAgICAgICAgICAgIGlkZWxlLnZhbChsb2NhdGUubG5nKycsJytsb2NhdGUubGF0KTtcclxuICAgICAgICB9LGlkZWxlLnZhbCgpKTtcclxuICAgIH0pO1xyXG59KTsiXX0=
