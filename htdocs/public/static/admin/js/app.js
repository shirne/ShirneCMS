function del(obj,msg) {
    dialog.confirm(msg,function(){
        location.href=$(obj).attr('href');
    });
    return false;
}

function lang(key) {
    if(window.language && window.language[key]){
        return window.language[key];
    }
    return key;
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
Dialog.prototype.hide=Dialog.prototype.close=function(){
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
        var contentHtml='<div class="form-group">{@input}</div>';
        var title='请输入信息';
        if(typeof message=='string'){
            title=message;
        }else{
            title=message.title;
            if(message.content) {
                contentHtml = message.content.indexOf('{@input}') > -1 ? message.content : message.content + contentHtml;
            }
        }
        return new Dialog({
            'onshow':function(body){
                if(message && message.onshow){
                    message.onshow(body);
                }
            },
            'onshown':function(body){
                body.find('[name=confirm_input]').focus();
                if(message && message.onshown){
                    message.onshown(body);
                }
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
        }).show(contentHtml.compile({input:'<input type="text" name="confirm_input" class="form-control" />'}),title);
    },
    action:function (list,callback,title) {
        var html='<div class="list-group"><a href="javascript:" class="list-group-item list-group-item-action">'+list.join('</a><a href="javascript:" class="list-group-item list-group-item-action">')+'</a></div>';
        var actions=null;
        var dlg=new Dialog({
            'onshow':function(body){
                actions=body.find('.list-group-item-action');
                actions.click(function (e) {
                    actions.removeClass('active');
                    $(this).addClass('active');
                })
            },
            'onsure':function(body){
                var action=actions.filter('.active');
                if(action.length>0){
                    var val=actions.index(action);
                    if(typeof callback=='function'){
                        return callback(val);
                    }
                }
                return false;
            },
            'onhide':function () {
                return true;
            }
        }).show(html,title?title:'请选择');
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
        selectTime:'选择时间',
        prevDecade: '上一区间',
        nextDecade: '下一区间',
        prevCentury: '上个世纪',
        nextCentury: '下个世纪'
    };

    function transOption(option) {
        if(!option)return {};
        var newopt={};
        for(var i in option){
            switch (i){
                case 'viewmode':
                    newopt['viewMode']=option[i];
                    break;
                case 'keepopen':
                    newopt['keepOpen']=option[i];
                    break;
                default:
                    newopt[i]=option[i];
            }
        }
        return newopt;
    }
    $('.datepicker').each(function(){
        var config=$.extend({
            tooltips:tooltips,
            format: 'YYYY-MM-DD',
            locale: 'zh-cn',
            showClear:true,
            showTodayButton:true,
            showClose:true,
            keepInvalid:true
        },transOption($(this).data()));

        $(this).datetimepicker(config);
    });

    $('.date-range').each(function () {
        var from = $(this).find('[name=fromdate],.fromdate'), to = $(this).find('[name=todate],.todate');
        var options = $.extend({
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
window.stop_ajax=false;
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
            ids.prop('checked', false);
        } else {
            ids.prop('checked', true);
        }
    });
    $('.checkreverse-btn').click(function (e) {
        var target = $(this).data('target');
        if (!target) target = 'id';
        var ids = $('[name=' + target + ']');
        for (var i = 0; i < ids.length; i++) {
            if (ids[i].checked) {
                ids.eq(i).prop('checked', false);
            } else {
                ids.eq(i).prop('checked', true);
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
        }).show('<p class="loading">'+lang('loading...')+'</p>', title);

    });

    //确认操作
    $('.link-confirm').click(function (e) {
        e.preventDefault();
        e.stopPropagation();
        var text=$(this).data('confirm');
        var url=$(this).data('href');
        if(!text)text=lang('Confirm operation?');

        dialog.confirm(text,function () {
            $.ajax({
                url:url,
                dataType:'JSON',
                success:function (json) {
                    dialog.alert(json.msg);
                    if(json.code==1){
                        if(json.url){
                            location.href=json.url;
                        }else{
                            location.reload();
                        }
                    }
                },
                error:function () {
                    dialog.alert(lang('Server error.'));
                }
            })
        });
    });

    $('.img-view').click(function (e) {
        e.preventDefault();
        e.stopPropagation();
        var url=$(this).attr('href');
        if(!url)url=$(this).data('img');
        dialog.alert('<a href="'+url+'" class="d-block text-center" target="_blank"><img class="img-fluid" src="'+url+'" /></a><div class="text-muted text-center">点击图片在新页面放大查看</div>',null,'查看图片');
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
        if(form.is('.noajax'))return true;
        var btn = this;

        var isbtn=$(btn).prop('tagName').toUpperCase()=='BUTTON';
        var origText=isbtn?$(btn).text():$(btn).val();
        var options = {
            url: $(form).attr('action'),
            type: 'POST',
            dataType: 'JSON',
            success: function (json) {
                window.stop_ajax=false;
                isbtn?$(btn).text(origText):$(btn).val(origText);
                if (json.code == 1) {
                    dialog.alert(json.msg,function(){
                        if (json.url) {
                            location.href = json.url;
                        } else {
                            location.reload();
                        }
                    });
                } else {
                    toastr.warning(json.msg);
                    $(btn).removeAttr('disabled');
                }
            },
            error: function (xhr) {
                window.stop_ajax=false;
                isbtn?$(btn).text(origText):$(btn).val(origText);
                $(btn).removeAttr('disabled');
                toastr.error('服务器处理错误');
            }
        };
        if (form.attr('enctype') === 'multipart/form-data') {
            if (!FormData) {
                window.stop_ajax=true;
                return true;
            }
            options.data = new FormData(form[0]);
            options.cache = false;
            options.processData = false;
            options.contentType = false;
            options.xhr= function() { //用以显示上传进度
                var xhr = $.ajaxSettings.xhr();
                if (xhr.upload) {
                    xhr.upload.addEventListener('progress', function(event) {
                        var percent = Math.floor(event.loaded / event.total * 100);
                        $(btn).text(origText+'  ('+percent+'%)');
                    }, false);
                }
                return xhr;
            };
        } else {
            options.data = $(form).serialize();
        }

        e.preventDefault();
        $(this).attr('disabled', true);
        window.stop_ajax=true;
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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNvbW1vbi5qcyIsInRlbXBsYXRlLmpzIiwiZGlhbG9nLmpzIiwianF1ZXJ5LnRhZy5qcyIsImRhdGV0aW1lLmluaXQuanMiLCJtYXAuanMiLCJiYWNrZW5kLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ2pFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUM5REE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDcFdBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDMUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUN6RUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUMxakJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EiLCJmaWxlIjoiYmFja2VuZC5qcyIsInNvdXJjZXNDb250ZW50IjpbImZ1bmN0aW9uIGRlbChvYmosbXNnKSB7XHJcbiAgICBkaWFsb2cuY29uZmlybShtc2csZnVuY3Rpb24oKXtcclxuICAgICAgICBsb2NhdGlvbi5ocmVmPSQob2JqKS5hdHRyKCdocmVmJyk7XHJcbiAgICB9KTtcclxuICAgIHJldHVybiBmYWxzZTtcclxufVxyXG5cclxuZnVuY3Rpb24gbGFuZyhrZXkpIHtcclxuICAgIGlmKHdpbmRvdy5sYW5ndWFnZSAmJiB3aW5kb3cubGFuZ3VhZ2Vba2V5XSl7XHJcbiAgICAgICAgcmV0dXJuIHdpbmRvdy5sYW5ndWFnZVtrZXldO1xyXG4gICAgfVxyXG4gICAgcmV0dXJuIGtleTtcclxufVxyXG5cclxuZnVuY3Rpb24gcmFuZG9tU3RyaW5nKGxlbiwgY2hhclNldCkge1xyXG4gICAgY2hhclNldCA9IGNoYXJTZXQgfHwgJ0FCQ0RFRkdISUpLTE1OT1BRUlNUVVZXWFlaYWJjZGVmZ2hpamtsbW5vcHFyc3R1dnd4eXowMTIzNDU2Nzg5JztcclxuICAgIHZhciBzdHIgPSAnJyxhbGxMZW49Y2hhclNldC5sZW5ndGg7XHJcbiAgICBmb3IgKHZhciBpID0gMDsgaSA8IGxlbjsgaSsrKSB7XHJcbiAgICAgICAgdmFyIHJhbmRvbVBveiA9IE1hdGguZmxvb3IoTWF0aC5yYW5kb20oKSAqIGFsbExlbik7XHJcbiAgICAgICAgc3RyICs9IGNoYXJTZXQuc3Vic3RyaW5nKHJhbmRvbVBveixyYW5kb21Qb3orMSk7XHJcbiAgICB9XHJcbiAgICByZXR1cm4gc3RyO1xyXG59XHJcblxyXG5mdW5jdGlvbiBjb3B5X29iaihhcnIpe1xyXG4gICAgcmV0dXJuIEpTT04ucGFyc2UoSlNPTi5zdHJpbmdpZnkoYXJyKSk7XHJcbn1cclxuXHJcbmZ1bmN0aW9uIGlzT2JqZWN0VmFsdWVFcXVhbChhLCBiKSB7XHJcbiAgICBpZighYSAmJiAhYilyZXR1cm4gdHJ1ZTtcclxuICAgIGlmKCFhIHx8ICFiKXJldHVybiBmYWxzZTtcclxuXHJcbiAgICAvLyBPZiBjb3Vyc2UsIHdlIGNhbiBkbyBpdCB1c2UgZm9yIGluXHJcbiAgICAvLyBDcmVhdGUgYXJyYXlzIG9mIHByb3BlcnR5IG5hbWVzXHJcbiAgICB2YXIgYVByb3BzID0gT2JqZWN0LmdldE93blByb3BlcnR5TmFtZXMoYSk7XHJcbiAgICB2YXIgYlByb3BzID0gT2JqZWN0LmdldE93blByb3BlcnR5TmFtZXMoYik7XHJcblxyXG4gICAgLy8gSWYgbnVtYmVyIG9mIHByb3BlcnRpZXMgaXMgZGlmZmVyZW50LFxyXG4gICAgLy8gb2JqZWN0cyBhcmUgbm90IGVxdWl2YWxlbnRcclxuICAgIGlmIChhUHJvcHMubGVuZ3RoICE9IGJQcm9wcy5sZW5ndGgpIHtcclxuICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICB9XHJcblxyXG4gICAgZm9yICh2YXIgaSA9IDA7IGkgPCBhUHJvcHMubGVuZ3RoOyBpKyspIHtcclxuICAgICAgICB2YXIgcHJvcE5hbWUgPSBhUHJvcHNbaV07XHJcblxyXG4gICAgICAgIC8vIElmIHZhbHVlcyBvZiBzYW1lIHByb3BlcnR5IGFyZSBub3QgZXF1YWwsXHJcbiAgICAgICAgLy8gb2JqZWN0cyBhcmUgbm90IGVxdWl2YWxlbnRcclxuICAgICAgICBpZiAoYVtwcm9wTmFtZV0gIT09IGJbcHJvcE5hbWVdKSB7XHJcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICB9XHJcbiAgICB9XHJcblxyXG4gICAgLy8gSWYgd2UgbWFkZSBpdCB0aGlzIGZhciwgb2JqZWN0c1xyXG4gICAgLy8gYXJlIGNvbnNpZGVyZWQgZXF1aXZhbGVudFxyXG4gICAgcmV0dXJuIHRydWU7XHJcbn1cclxuXHJcbmZ1bmN0aW9uIGFycmF5X2NvbWJpbmUoYSxiKSB7XHJcbiAgICB2YXIgb2JqPXt9O1xyXG4gICAgZm9yKHZhciBpPTA7aTxhLmxlbmd0aDtpKyspe1xyXG4gICAgICAgIGlmKGIubGVuZ3RoPGkrMSlicmVhaztcclxuICAgICAgICBvYmpbYVtpXV09YltpXTtcclxuICAgIH1cclxuICAgIHJldHVybiBvYmo7XHJcbn0iLCJcclxuTnVtYmVyLnByb3RvdHlwZS5mb3JtYXQ9ZnVuY3Rpb24oZml4KXtcclxuICAgIGlmKGZpeD09PXVuZGVmaW5lZClmaXg9MjtcclxuICAgIHZhciBudW09dGhpcy50b0ZpeGVkKGZpeCk7XHJcbiAgICB2YXIgej1udW0uc3BsaXQoJy4nKTtcclxuICAgIHZhciBmb3JtYXQ9W10sZj16WzBdLnNwbGl0KCcnKSxsPWYubGVuZ3RoO1xyXG4gICAgZm9yKHZhciBpPTA7aTxsO2krKyl7XHJcbiAgICAgICAgaWYoaT4wICYmIGkgJSAzPT0wKXtcclxuICAgICAgICAgICAgZm9ybWF0LnVuc2hpZnQoJywnKTtcclxuICAgICAgICB9XHJcbiAgICAgICAgZm9ybWF0LnVuc2hpZnQoZltsLWktMV0pO1xyXG4gICAgfVxyXG4gICAgcmV0dXJuIGZvcm1hdC5qb2luKCcnKSsoei5sZW5ndGg9PTI/Jy4nK3pbMV06JycpO1xyXG59O1xyXG5TdHJpbmcucHJvdG90eXBlLmNvbXBpbGU9ZnVuY3Rpb24oZGF0YSxsaXN0KXtcclxuXHJcbiAgICBpZihsaXN0KXtcclxuICAgICAgICB2YXIgdGVtcHM9W107XHJcbiAgICAgICAgZm9yKHZhciBpIGluIGRhdGEpe1xyXG4gICAgICAgICAgICB0ZW1wcy5wdXNoKHRoaXMuY29tcGlsZShkYXRhW2ldKSk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHJldHVybiB0ZW1wcy5qb2luKFwiXFxuXCIpO1xyXG4gICAgfWVsc2V7XHJcbiAgICAgICAgcmV0dXJuIHRoaXMucmVwbGFjZSgvXFx7QChbXFx3XFxkXFwuXSspKD86XFx8KFtcXHdcXGRdKykoPzpcXHMqPVxccyooW1xcd1xcZCxcXHMjXSspKT8pP1xcfS9nLGZ1bmN0aW9uKGFsbCxtMSxmdW5jLGFyZ3Mpe1xyXG5cclxuICAgICAgICAgICAgaWYobTEuaW5kZXhPZignLicpPjApe1xyXG4gICAgICAgICAgICAgICAgdmFyIGtleXM9bTEuc3BsaXQoJy4nKSx2YWw9ZGF0YTtcclxuICAgICAgICAgICAgICAgIGZvcih2YXIgaT0wO2k8a2V5cy5sZW5ndGg7aSsrKXtcclxuICAgICAgICAgICAgICAgICAgICBpZih2YWxba2V5c1tpXV0hPT11bmRlZmluZWQpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB2YWw9dmFsW2tleXNbaV1dO1xyXG4gICAgICAgICAgICAgICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB2YWwgPSAnJztcclxuICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgcmV0dXJuIGNhbGxmdW5jKHZhbCxmdW5jLGFyZ3MpO1xyXG4gICAgICAgICAgICB9ZWxzZXtcclxuICAgICAgICAgICAgICAgIHJldHVybiBkYXRhW20xXSE9PXVuZGVmaW5lZD9jYWxsZnVuYyhkYXRhW20xXSxmdW5jLGFyZ3MsZGF0YSk6Jyc7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KTtcclxuICAgIH1cclxufTtcclxuXHJcbmZ1bmN0aW9uIGNhbGxmdW5jKHZhbCxmdW5jLGFyZ3MsdGhpc29iail7XHJcbiAgICBpZighYXJncyl7XHJcbiAgICAgICAgYXJncz1bdmFsXTtcclxuICAgIH1lbHNle1xyXG4gICAgICAgIGlmKHR5cGVvZiBhcmdzPT09J3N0cmluZycpYXJncz1hcmdzLnNwbGl0KCcsJyk7XHJcbiAgICAgICAgdmFyIGFyZ2lkeD1hcmdzLmluZGV4T2YoJyMjIycpO1xyXG4gICAgICAgIGlmKGFyZ2lkeD49MCl7XHJcbiAgICAgICAgICAgIGFyZ3NbYXJnaWR4XT12YWw7XHJcbiAgICAgICAgfWVsc2V7XHJcbiAgICAgICAgICAgIGFyZ3M9W3ZhbF0uY29uY2F0KGFyZ3MpO1xyXG4gICAgICAgIH1cclxuICAgIH1cclxuICAgIC8vY29uc29sZS5sb2coYXJncyk7XHJcbiAgICByZXR1cm4gd2luZG93W2Z1bmNdP3dpbmRvd1tmdW5jXS5hcHBseSh0aGlzb2JqLGFyZ3MpOnZhbDtcclxufVxyXG5cclxuZnVuY3Rpb24gaWlmKHYsbTEsbTIpe1xyXG4gICAgaWYodj09PScwJyl2PTA7XHJcbiAgICByZXR1cm4gdj9tMTptMjtcclxufSIsIlxyXG52YXIgZGlhbG9nVHBsPSc8ZGl2IGNsYXNzPVwibW9kYWwgZmFkZVwiIGlkPVwie0BpZH1cIiB0YWJpbmRleD1cIi0xXCIgcm9sZT1cImRpYWxvZ1wiIGFyaWEtbGFiZWxsZWRieT1cIntAaWR9TGFiZWxcIiBhcmlhLWhpZGRlbj1cInRydWVcIj5cXG4nICtcclxuICAgICcgICAgPGRpdiBjbGFzcz1cIm1vZGFsLWRpYWxvZ1wiPlxcbicgK1xyXG4gICAgJyAgICAgICAgPGRpdiBjbGFzcz1cIm1vZGFsLWNvbnRlbnRcIj5cXG4nICtcclxuICAgICcgICAgICAgICAgICA8ZGl2IGNsYXNzPVwibW9kYWwtaGVhZGVyXCI+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgICAgIDxoNCBjbGFzcz1cIm1vZGFsLXRpdGxlXCIgaWQ9XCJ7QGlkfUxhYmVsXCI+PC9oND5cXG4nICtcclxuICAgICcgICAgICAgICAgICAgICAgPGJ1dHRvbiB0eXBlPVwiYnV0dG9uXCIgY2xhc3M9XCJjbG9zZVwiIGRhdGEtZGlzbWlzcz1cIm1vZGFsXCI+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgICAgICAgICA8c3BhbiBhcmlhLWhpZGRlbj1cInRydWVcIj4mdGltZXM7PC9zcGFuPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgICAgICAgICAgPHNwYW4gY2xhc3M9XCJzci1vbmx5XCI+Q2xvc2U8L3NwYW4+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgICAgIDwvYnV0dG9uPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgIDwvZGl2PlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJtb2RhbC1ib2R5XCI+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgPC9kaXY+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgPGRpdiBjbGFzcz1cIm1vZGFsLWZvb3RlclwiPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgICAgICA8bmF2IGNsYXNzPVwibmF2IG5hdi1maWxsXCI+PC9uYXY+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgPC9kaXY+XFxuJyArXHJcbiAgICAnICAgICAgICA8L2Rpdj5cXG4nICtcclxuICAgICcgICAgPC9kaXY+XFxuJyArXHJcbiAgICAnPC9kaXY+JztcclxudmFyIGRpYWxvZ0lkeD0wO1xyXG5mdW5jdGlvbiBEaWFsb2cob3B0cyl7XHJcbiAgICBpZighb3B0cylvcHRzPXt9O1xyXG4gICAgLy/lpITnkIbmjInpkq5cclxuICAgIGlmKG9wdHMuYnRucyE9PXVuZGVmaW5lZCkge1xyXG4gICAgICAgIGlmICh0eXBlb2Yob3B0cy5idG5zKSA9PSAnc3RyaW5nJykge1xyXG4gICAgICAgICAgICBvcHRzLmJ0bnMgPSBbb3B0cy5idG5zXTtcclxuICAgICAgICB9XHJcbiAgICAgICAgdmFyIGRmdD0tMTtcclxuICAgICAgICBmb3IgKHZhciBpID0gMDsgaSA8IG9wdHMuYnRucy5sZW5ndGg7IGkrKykge1xyXG4gICAgICAgICAgICBpZih0eXBlb2Yob3B0cy5idG5zW2ldKT09J3N0cmluZycpe1xyXG4gICAgICAgICAgICAgICAgb3B0cy5idG5zW2ldPXsndGV4dCc6b3B0cy5idG5zW2ldfTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICBpZihvcHRzLmJ0bnNbaV0uaXNkZWZhdWx0KXtcclxuICAgICAgICAgICAgICAgIGRmdD1pO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfVxyXG4gICAgICAgIGlmKGRmdDwwKXtcclxuICAgICAgICAgICAgZGZ0PW9wdHMuYnRucy5sZW5ndGgtMTtcclxuICAgICAgICAgICAgb3B0cy5idG5zW2RmdF0uaXNkZWZhdWx0PXRydWU7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICBpZighb3B0cy5idG5zW2RmdF1bJ3R5cGUnXSl7XHJcbiAgICAgICAgICAgIG9wdHMuYnRuc1tkZnRdWyd0eXBlJ109J3ByaW1hcnknO1xyXG4gICAgICAgIH1cclxuICAgICAgICBvcHRzLmRlZmF1bHRCdG49ZGZ0O1xyXG4gICAgfVxyXG5cclxuICAgIHRoaXMub3B0aW9ucz0kLmV4dGVuZCh7XHJcbiAgICAgICAgJ2lkJzonZGxnTW9kYWwnK2RpYWxvZ0lkeCsrLFxyXG4gICAgICAgICdzaXplJzonJyxcclxuICAgICAgICAnYnRucyc6W1xyXG4gICAgICAgICAgICB7J3RleHQnOiflj5bmtognLCd0eXBlJzonc2Vjb25kYXJ5J30sXHJcbiAgICAgICAgICAgIHsndGV4dCc6J+ehruWumicsJ2lzZGVmYXVsdCc6dHJ1ZSwndHlwZSc6J3ByaW1hcnknfVxyXG4gICAgICAgIF0sXHJcbiAgICAgICAgJ2RlZmF1bHRCdG4nOjEsXHJcbiAgICAgICAgJ29uc3VyZSc6bnVsbCxcclxuICAgICAgICAnb25zaG93JzpudWxsLFxyXG4gICAgICAgICdvbnNob3duJzpudWxsLFxyXG4gICAgICAgICdvbmhpZGUnOm51bGwsXHJcbiAgICAgICAgJ29uaGlkZGVuJzpudWxsXHJcbiAgICB9LG9wdHMpO1xyXG5cclxuICAgIHRoaXMuYm94PSQodGhpcy5vcHRpb25zLmlkKTtcclxufVxyXG5EaWFsb2cucHJvdG90eXBlLmdlbmVyQnRuPWZ1bmN0aW9uKG9wdCxpZHgpe1xyXG4gICAgaWYob3B0Wyd0eXBlJ10pb3B0WydjbGFzcyddPSdidG4tb3V0bGluZS0nK29wdFsndHlwZSddO1xyXG4gICAgcmV0dXJuICc8YSBocmVmPVwiamF2YXNjcmlwdDpcIiBjbGFzcz1cIm5hdi1pdGVtIGJ0biAnKyhvcHRbJ2NsYXNzJ10/b3B0WydjbGFzcyddOididG4tb3V0bGluZS1zZWNvbmRhcnknKSsnXCIgZGF0YS1pbmRleD1cIicraWR4KydcIj4nK29wdC50ZXh0Kyc8L2E+JztcclxufTtcclxuRGlhbG9nLnByb3RvdHlwZS5zaG93PWZ1bmN0aW9uKGh0bWwsdGl0bGUpe1xyXG4gICAgdGhpcy5ib3g9JCgnIycrdGhpcy5vcHRpb25zLmlkKTtcclxuICAgIGlmKCF0aXRsZSl0aXRsZT0n57O757uf5o+Q56S6JztcclxuICAgIGlmKHRoaXMuYm94Lmxlbmd0aDwxKSB7XHJcbiAgICAgICAgJChkb2N1bWVudC5ib2R5KS5hcHBlbmQoZGlhbG9nVHBsLmNvbXBpbGUoeydpZCc6IHRoaXMub3B0aW9ucy5pZH0pKTtcclxuICAgICAgICB0aGlzLmJveD0kKCcjJyt0aGlzLm9wdGlvbnMuaWQpO1xyXG4gICAgfWVsc2V7XHJcbiAgICAgICAgdGhpcy5ib3gudW5iaW5kKCk7XHJcbiAgICB9XHJcblxyXG4gICAgLy90aGlzLmJveC5maW5kKCcubW9kYWwtZm9vdGVyIC5idG4tcHJpbWFyeScpLnVuYmluZCgpO1xyXG4gICAgdmFyIHNlbGY9dGhpcztcclxuICAgIERpYWxvZy5pbnN0YW5jZT1zZWxmO1xyXG5cclxuICAgIC8v55Sf5oiQ5oyJ6ZKuXHJcbiAgICB2YXIgYnRucz1bXTtcclxuICAgIGZvcih2YXIgaT0wO2k8dGhpcy5vcHRpb25zLmJ0bnMubGVuZ3RoO2krKyl7XHJcbiAgICAgICAgYnRucy5wdXNoKHRoaXMuZ2VuZXJCdG4odGhpcy5vcHRpb25zLmJ0bnNbaV0saSkpO1xyXG4gICAgfVxyXG4gICAgdGhpcy5ib3guZmluZCgnLm1vZGFsLWZvb3RlciAubmF2JykuaHRtbChidG5zLmpvaW4oJ1xcbicpKTtcclxuXHJcbiAgICB2YXIgZGlhbG9nPXRoaXMuYm94LmZpbmQoJy5tb2RhbC1kaWFsb2cnKTtcclxuICAgIGRpYWxvZy5yZW1vdmVDbGFzcygnbW9kYWwtc20nKS5yZW1vdmVDbGFzcygnbW9kYWwtbGcnKTtcclxuICAgIGlmKHRoaXMub3B0aW9ucy5zaXplPT0nc20nKSB7XHJcbiAgICAgICAgZGlhbG9nLmFkZENsYXNzKCdtb2RhbC1zbScpO1xyXG4gICAgfWVsc2UgaWYodGhpcy5vcHRpb25zLnNpemU9PSdsZycpIHtcclxuICAgICAgICBkaWFsb2cuYWRkQ2xhc3MoJ21vZGFsLWxnJyk7XHJcbiAgICB9XHJcbiAgICB0aGlzLmJveC5maW5kKCcubW9kYWwtdGl0bGUnKS50ZXh0KHRpdGxlKTtcclxuXHJcbiAgICB2YXIgYm9keT10aGlzLmJveC5maW5kKCcubW9kYWwtYm9keScpO1xyXG4gICAgYm9keS5odG1sKGh0bWwpO1xyXG4gICAgdGhpcy5ib3gub24oJ2hpZGUuYnMubW9kYWwnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgaWYoc2VsZi5vcHRpb25zLm9uaGlkZSl7XHJcbiAgICAgICAgICAgIHNlbGYub3B0aW9ucy5vbmhpZGUoYm9keSxzZWxmLmJveCk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIERpYWxvZy5pbnN0YW5jZT1udWxsO1xyXG4gICAgfSk7XHJcbiAgICB0aGlzLmJveC5vbignaGlkZGVuLmJzLm1vZGFsJyxmdW5jdGlvbigpe1xyXG4gICAgICAgIGlmKHNlbGYub3B0aW9ucy5vbmhpZGRlbil7XHJcbiAgICAgICAgICAgIHNlbGYub3B0aW9ucy5vbmhpZGRlbihib2R5LHNlbGYuYm94KTtcclxuICAgICAgICB9XHJcbiAgICAgICAgc2VsZi5ib3gucmVtb3ZlKCk7XHJcbiAgICB9KTtcclxuICAgIHRoaXMuYm94Lm9uKCdzaG93LmJzLm1vZGFsJyxmdW5jdGlvbigpe1xyXG4gICAgICAgIGlmKHNlbGYub3B0aW9ucy5vbnNob3cpe1xyXG4gICAgICAgICAgICBzZWxmLm9wdGlvbnMub25zaG93KGJvZHksc2VsZi5ib3gpO1xyXG4gICAgICAgIH1cclxuICAgIH0pO1xyXG4gICAgdGhpcy5ib3gub24oJ3Nob3duLmJzLm1vZGFsJyxmdW5jdGlvbigpe1xyXG4gICAgICAgIGlmKHNlbGYub3B0aW9ucy5vbnNob3duKXtcclxuICAgICAgICAgICAgc2VsZi5vcHRpb25zLm9uc2hvd24oYm9keSxzZWxmLmJveCk7XHJcbiAgICAgICAgfVxyXG4gICAgfSk7XHJcbiAgICB0aGlzLmJveC5maW5kKCcubW9kYWwtZm9vdGVyIC5idG4nKS5jbGljayhmdW5jdGlvbigpe1xyXG4gICAgICAgIHZhciByZXN1bHQ9dHJ1ZSxpZHg9JCh0aGlzKS5kYXRhKCdpbmRleCcpO1xyXG4gICAgICAgIGlmKHNlbGYub3B0aW9ucy5idG5zW2lkeF0uY2xpY2spe1xyXG4gICAgICAgICAgICByZXN1bHQgPSBzZWxmLm9wdGlvbnMuYnRuc1tpZHhdLmNsaWNrLmFwcGx5KHRoaXMsW2JvZHksIHNlbGYuYm94XSk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIGlmKGlkeD09c2VsZi5vcHRpb25zLmRlZmF1bHRCdG4pIHtcclxuICAgICAgICAgICAgaWYgKHNlbGYub3B0aW9ucy5vbnN1cmUpIHtcclxuICAgICAgICAgICAgICAgIHJlc3VsdCA9IHNlbGYub3B0aW9ucy5vbnN1cmUuYXBwbHkodGhpcyxbYm9keSwgc2VsZi5ib3hdKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH1cclxuICAgICAgICBpZihyZXN1bHQhPT1mYWxzZSl7XHJcbiAgICAgICAgICAgIHNlbGYuYm94Lm1vZGFsKCdoaWRlJyk7XHJcbiAgICAgICAgfVxyXG4gICAgfSk7XHJcbiAgICB0aGlzLmJveC5tb2RhbCgnc2hvdycpO1xyXG4gICAgcmV0dXJuIHRoaXM7XHJcbn07XHJcbkRpYWxvZy5wcm90b3R5cGUuaGlkZT1EaWFsb2cucHJvdG90eXBlLmNsb3NlPWZ1bmN0aW9uKCl7XHJcbiAgICB0aGlzLmJveC5tb2RhbCgnaGlkZScpO1xyXG4gICAgcmV0dXJuIHRoaXM7XHJcbn07XHJcblxyXG52YXIgZGlhbG9nPXtcclxuICAgIGFsZXJ0OmZ1bmN0aW9uKG1lc3NhZ2UsY2FsbGJhY2ssdGl0bGUpe1xyXG4gICAgICAgIHZhciBjYWxsZWQ9ZmFsc2U7XHJcbiAgICAgICAgdmFyIGlzY2FsbGJhY2s9dHlwZW9mIGNhbGxiYWNrPT0nZnVuY3Rpb24nO1xyXG4gICAgICAgIHJldHVybiBuZXcgRGlhbG9nKHtcclxuICAgICAgICAgICAgYnRuczon56Gu5a6aJyxcclxuICAgICAgICAgICAgb25zdXJlOmZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgICAgICBpZihpc2NhbGxiYWNrKXtcclxuICAgICAgICAgICAgICAgICAgICBjYWxsZWQ9dHJ1ZTtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gY2FsbGJhY2sodHJ1ZSk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgIG9uaGlkZTpmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICAgICAgaWYoIWNhbGxlZCAmJiBpc2NhbGxiYWNrKXtcclxuICAgICAgICAgICAgICAgICAgICBjYWxsYmFjayhmYWxzZSk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS5zaG93KG1lc3NhZ2UsdGl0bGUpO1xyXG4gICAgfSxcclxuICAgIGNvbmZpcm06ZnVuY3Rpb24obWVzc2FnZSxjb25maXJtLGNhbmNlbCl7XHJcbiAgICAgICAgdmFyIGNhbGxlZD1mYWxzZTtcclxuICAgICAgICByZXR1cm4gbmV3IERpYWxvZyh7XHJcbiAgICAgICAgICAgICdvbnN1cmUnOmZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgICAgICBpZih0eXBlb2YgY29uZmlybT09J2Z1bmN0aW9uJyl7XHJcbiAgICAgICAgICAgICAgICAgICAgY2FsbGVkPXRydWU7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGNvbmZpcm0oKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgJ29uaGlkZSc6ZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAgICAgaWYoY2FsbGVkPWZhbHNlICYmIHR5cGVvZiBjYW5jZWw9PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBjYW5jZWwoKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLnNob3cobWVzc2FnZSk7XHJcbiAgICB9LFxyXG4gICAgcHJvbXB0OmZ1bmN0aW9uKG1lc3NhZ2UsY2FsbGJhY2ssY2FuY2VsKXtcclxuICAgICAgICB2YXIgY2FsbGVkPWZhbHNlO1xyXG4gICAgICAgIHZhciBjb250ZW50SHRtbD0nPGRpdiBjbGFzcz1cImZvcm0tZ3JvdXBcIj57QGlucHV0fTwvZGl2Pic7XHJcbiAgICAgICAgdmFyIHRpdGxlPSfor7fovpPlhaXkv6Hmga8nO1xyXG4gICAgICAgIGlmKHR5cGVvZiBtZXNzYWdlPT0nc3RyaW5nJyl7XHJcbiAgICAgICAgICAgIHRpdGxlPW1lc3NhZ2U7XHJcbiAgICAgICAgfWVsc2V7XHJcbiAgICAgICAgICAgIHRpdGxlPW1lc3NhZ2UudGl0bGU7XHJcbiAgICAgICAgICAgIGlmKG1lc3NhZ2UuY29udGVudCkge1xyXG4gICAgICAgICAgICAgICAgY29udGVudEh0bWwgPSBtZXNzYWdlLmNvbnRlbnQuaW5kZXhPZigne0BpbnB1dH0nKSA+IC0xID8gbWVzc2FnZS5jb250ZW50IDogbWVzc2FnZS5jb250ZW50ICsgY29udGVudEh0bWw7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9XHJcbiAgICAgICAgcmV0dXJuIG5ldyBEaWFsb2coe1xyXG4gICAgICAgICAgICAnb25zaG93JzpmdW5jdGlvbihib2R5KXtcclxuICAgICAgICAgICAgICAgIGlmKG1lc3NhZ2UgJiYgbWVzc2FnZS5vbnNob3cpe1xyXG4gICAgICAgICAgICAgICAgICAgIG1lc3NhZ2Uub25zaG93KGJvZHkpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAnb25zaG93bic6ZnVuY3Rpb24oYm9keSl7XHJcbiAgICAgICAgICAgICAgICBib2R5LmZpbmQoJ1tuYW1lPWNvbmZpcm1faW5wdXRdJykuZm9jdXMoKTtcclxuICAgICAgICAgICAgICAgIGlmKG1lc3NhZ2UgJiYgbWVzc2FnZS5vbnNob3duKXtcclxuICAgICAgICAgICAgICAgICAgICBtZXNzYWdlLm9uc2hvd24oYm9keSk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdvbnN1cmUnOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgdmFyIHZhbD1ib2R5LmZpbmQoJ1tuYW1lPWNvbmZpcm1faW5wdXRdJykudmFsKCk7XHJcbiAgICAgICAgICAgICAgICBpZih0eXBlb2YgY2FsbGJhY2s9PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgICAgIHZhciByZXN1bHQgPSBjYWxsYmFjayh2YWwpO1xyXG4gICAgICAgICAgICAgICAgICAgIGlmKHJlc3VsdD09PXRydWUpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBjYWxsZWQ9dHJ1ZTtcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHJlc3VsdDtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgJ29uaGlkZSc6ZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAgICAgaWYoY2FsbGVkPWZhbHNlICYmIHR5cGVvZiBjYW5jZWw9PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBjYW5jZWwoKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLnNob3coY29udGVudEh0bWwuY29tcGlsZSh7aW5wdXQ6JzxpbnB1dCB0eXBlPVwidGV4dFwiIG5hbWU9XCJjb25maXJtX2lucHV0XCIgY2xhc3M9XCJmb3JtLWNvbnRyb2xcIiAvPid9KSx0aXRsZSk7XHJcbiAgICB9LFxyXG4gICAgYWN0aW9uOmZ1bmN0aW9uIChsaXN0LGNhbGxiYWNrLHRpdGxlKSB7XHJcbiAgICAgICAgdmFyIGh0bWw9JzxkaXYgY2xhc3M9XCJsaXN0LWdyb3VwXCI+PGEgaHJlZj1cImphdmFzY3JpcHQ6XCIgY2xhc3M9XCJsaXN0LWdyb3VwLWl0ZW0gbGlzdC1ncm91cC1pdGVtLWFjdGlvblwiPicrbGlzdC5qb2luKCc8L2E+PGEgaHJlZj1cImphdmFzY3JpcHQ6XCIgY2xhc3M9XCJsaXN0LWdyb3VwLWl0ZW0gbGlzdC1ncm91cC1pdGVtLWFjdGlvblwiPicpKyc8L2E+PC9kaXY+JztcclxuICAgICAgICB2YXIgYWN0aW9ucz1udWxsO1xyXG4gICAgICAgIHZhciBkbGc9bmV3IERpYWxvZyh7XHJcbiAgICAgICAgICAgICdvbnNob3cnOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgYWN0aW9ucz1ib2R5LmZpbmQoJy5saXN0LWdyb3VwLWl0ZW0tYWN0aW9uJyk7XHJcbiAgICAgICAgICAgICAgICBhY3Rpb25zLmNsaWNrKGZ1bmN0aW9uIChlKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgYWN0aW9ucy5yZW1vdmVDbGFzcygnYWN0aXZlJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5hZGRDbGFzcygnYWN0aXZlJyk7XHJcbiAgICAgICAgICAgICAgICB9KVxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAnb25zdXJlJzpmdW5jdGlvbihib2R5KXtcclxuICAgICAgICAgICAgICAgIHZhciBhY3Rpb249YWN0aW9ucy5maWx0ZXIoJy5hY3RpdmUnKTtcclxuICAgICAgICAgICAgICAgIGlmKGFjdGlvbi5sZW5ndGg+MCl7XHJcbiAgICAgICAgICAgICAgICAgICAgdmFyIHZhbD1hY3Rpb25zLmluZGV4KGFjdGlvbik7XHJcbiAgICAgICAgICAgICAgICAgICAgaWYodHlwZW9mIGNhbGxiYWNrPT0nZnVuY3Rpb24nKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGNhbGxiYWNrKHZhbCk7XHJcbiAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAnb25oaWRlJzpmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgICAgICByZXR1cm4gdHJ1ZTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLnNob3coaHRtbCx0aXRsZT90aXRsZTon6K+36YCJ5oupJyk7XHJcbiAgICB9LFxyXG4gICAgcGlja1VzZXI6ZnVuY3Rpb24odXJsLGNhbGxiYWNrLGZpbHRlcil7XHJcbiAgICAgICAgdmFyIHVzZXI9bnVsbDtcclxuICAgICAgICBpZighZmlsdGVyKWZpbHRlcj17fTtcclxuICAgICAgICB2YXIgZGxnPW5ldyBEaWFsb2coe1xyXG4gICAgICAgICAgICAnb25zaG93bic6ZnVuY3Rpb24oYm9keSl7XHJcbiAgICAgICAgICAgICAgICB2YXIgYnRuPWJvZHkuZmluZCgnLnNlYXJjaGJ0bicpO1xyXG4gICAgICAgICAgICAgICAgdmFyIGlucHV0PWJvZHkuZmluZCgnLnNlYXJjaHRleHQnKTtcclxuICAgICAgICAgICAgICAgIHZhciBsaXN0Ym94PWJvZHkuZmluZCgnLmxpc3QtZ3JvdXAnKTtcclxuICAgICAgICAgICAgICAgIHZhciBpc2xvYWRpbmc9ZmFsc2U7XHJcbiAgICAgICAgICAgICAgICBidG4uY2xpY2soZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgICAgICAgICBpZihpc2xvYWRpbmcpcmV0dXJuO1xyXG4gICAgICAgICAgICAgICAgICAgIGlzbG9hZGluZz10cnVlO1xyXG4gICAgICAgICAgICAgICAgICAgIGxpc3Rib3guaHRtbCgnPHNwYW4gY2xhc3M9XCJsaXN0LWxvYWRpbmdcIj7liqDovb3kuK0uLi48L3NwYW4+Jyk7XHJcbiAgICAgICAgICAgICAgICAgICAgZmlsdGVyWydrZXknXT1pbnB1dC52YWwoKTtcclxuICAgICAgICAgICAgICAgICAgICAkLmFqYXgoXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHVybDp1cmwsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0eXBlOidHRVQnLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZGF0YVR5cGU6J0pTT04nLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZGF0YTpmaWx0ZXIsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBzdWNjZXNzOmZ1bmN0aW9uKGpzb24pe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlzbG9hZGluZz1mYWxzZTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZihqc29uLnN0YXR1cyl7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmKGpzb24uZGF0YSAmJiBqc29uLmRhdGEubGVuZ3RoKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBsaXN0Ym94Lmh0bWwoJzxhIGhyZWY9XCJqYXZhc2NyaXB0OlwiIGRhdGEtaWQ9XCJ7QGlkfVwiIGNsYXNzPVwibGlzdC1ncm91cC1pdGVtIGxpc3QtZ3JvdXAtaXRlbS1hY3Rpb25cIj5be0BpZH1dJm5ic3A7PGkgY2xhc3M9XCJpb24tbWQtcGVyc29uXCI+PC9pPiB7QHVzZXJuYW1lfSZuYnNwOyZuYnNwOyZuYnNwOzxzbWFsbD48aSBjbGFzcz1cImlvbi1tZC1waG9uZS1wb3J0cmFpdFwiPjwvaT4ge0Btb2JpbGV9PC9zbWFsbD48L2E+Jy5jb21waWxlKGpzb24uZGF0YSwgdHJ1ZSkpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbGlzdGJveC5maW5kKCdhLmxpc3QtZ3JvdXAtaXRlbScpLmNsaWNrKGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgaWQgPSAkKHRoaXMpLmRhdGEoJ2lkJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgZm9yICh2YXIgaSA9IDA7IGkgPCBqc29uLmRhdGEubGVuZ3RoOyBpKyspIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYoanNvbi5kYXRhW2ldLmlkPT1pZCl7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB1c2VyPWpzb24uZGF0YVtpXTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxpc3Rib3guZmluZCgnYS5saXN0LWdyb3VwLWl0ZW0nKS5yZW1vdmVDbGFzcygnYWN0aXZlJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmFkZENsYXNzKCdhY3RpdmUnKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfSlcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfWVsc2V7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBsaXN0Ym94Lmh0bWwoJzxzcGFuIGNsYXNzPVwibGlzdC1sb2FkaW5nXCI+PGkgY2xhc3M9XCJpb24tbWQtd2FybmluZ1wiPjwvaT4g5rKh5pyJ5qOA57Si5Yiw5Lya5ZGYPC9zcGFuPicpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfWVsc2V7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxpc3Rib3guaHRtbCgnPHNwYW4gY2xhc3M9XCJ0ZXh0LWRhbmdlclwiPjxpIGNsYXNzPVwiaW9uLW1kLXdhcm5pbmdcIj48L2k+IOWKoOi9veWksei0pTwvc3Bhbj4nKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICApO1xyXG5cclxuICAgICAgICAgICAgICAgIH0pLnRyaWdnZXIoJ2NsaWNrJyk7XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdvbnN1cmUnOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgaWYoIXVzZXIpe1xyXG4gICAgICAgICAgICAgICAgICAgIHRvYXN0ci53YXJuaW5nKCfmsqHmnInpgInmi6nkvJrlkZghJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgaWYodHlwZW9mIGNhbGxiYWNrPT0nZnVuY3Rpb24nKXtcclxuICAgICAgICAgICAgICAgICAgICB2YXIgcmVzdWx0ID0gY2FsbGJhY2sodXNlcik7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHJlc3VsdDtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLnNob3coJzxkaXYgY2xhc3M9XCJpbnB1dC1ncm91cFwiPjxpbnB1dCB0eXBlPVwidGV4dFwiIGNsYXNzPVwiZm9ybS1jb250cm9sIHNlYXJjaHRleHRcIiBuYW1lPVwia2V5d29yZFwiIHBsYWNlaG9sZGVyPVwi5qC55o2u5Lya5ZGYaWTmiJblkI3np7DvvIznlLXor53mnaXmkJzntKJcIi8+PGRpdiBjbGFzcz1cImlucHV0LWdyb3VwLWFwcGVuZFwiPjxhIGNsYXNzPVwiYnRuIGJ0bi1vdXRsaW5lLXNlY29uZGFyeSBzZWFyY2hidG5cIj48aSBjbGFzcz1cImlvbi1tZC1zZWFyY2hcIj48L2k+PC9hPjwvZGl2PjwvZGl2PjxkaXYgY2xhc3M9XCJsaXN0LWdyb3VwIG10LTJcIj48L2Rpdj4nLCfor7fmkJzntKLlubbpgInmi6nkvJrlkZgnKTtcclxuICAgIH0sXHJcbiAgICBwaWNrTG9jYXRlOmZ1bmN0aW9uKHR5cGUsIGNhbGxiYWNrLCBsb2NhdGUpe1xyXG4gICAgICAgIHZhciBzZXR0ZWRMb2NhdGU9bnVsbDtcclxuICAgICAgICB2YXIgZGxnPW5ldyBEaWFsb2coe1xyXG4gICAgICAgICAgICAnc2l6ZSc6J2xnJyxcclxuICAgICAgICAgICAgJ29uc2hvd24nOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgdmFyIGJ0bj1ib2R5LmZpbmQoJy5zZWFyY2hidG4nKTtcclxuICAgICAgICAgICAgICAgIHZhciBpbnB1dD1ib2R5LmZpbmQoJy5zZWFyY2h0ZXh0Jyk7XHJcbiAgICAgICAgICAgICAgICB2YXIgbWFwYm94PWJvZHkuZmluZCgnLm1hcCcpO1xyXG4gICAgICAgICAgICAgICAgdmFyIG1hcGluZm89Ym9keS5maW5kKCcubWFwaW5mbycpO1xyXG4gICAgICAgICAgICAgICAgbWFwYm94LmNzcygnaGVpZ2h0JywkKHdpbmRvdykuaGVpZ2h0KCkqLjYpO1xyXG4gICAgICAgICAgICAgICAgdmFyIGlzbG9hZGluZz1mYWxzZTtcclxuICAgICAgICAgICAgICAgIHZhciBtYXA9SW5pdE1hcCgndGVuY2VudCcsbWFwYm94LGZ1bmN0aW9uKGFkZHJlc3MsbG9jYXRlKXtcclxuICAgICAgICAgICAgICAgICAgICBtYXBpbmZvLmh0bWwoYWRkcmVzcysnJm5ic3A7Jytsb2NhdGUubG5nKycsJytsb2NhdGUubGF0KTtcclxuICAgICAgICAgICAgICAgICAgICBzZXR0ZWRMb2NhdGU9bG9jYXRlO1xyXG4gICAgICAgICAgICAgICAgfSxsb2NhdGUpO1xyXG4gICAgICAgICAgICAgICAgYnRuLmNsaWNrKGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgICAgICAgICAgdmFyIHNlYXJjaD1pbnB1dC52YWwoKTtcclxuICAgICAgICAgICAgICAgICAgICBtYXAuc2V0TG9jYXRlKHNlYXJjaCk7XHJcbiAgICAgICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgJ29uc3VyZSc6ZnVuY3Rpb24oYm9keSl7XHJcbiAgICAgICAgICAgICAgICBpZighc2V0dGVkTG9jYXRlKXtcclxuICAgICAgICAgICAgICAgICAgICB0b2FzdHIud2FybmluZygn5rKh5pyJ6YCJ5oup5L2N572uIScpO1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIGlmKHR5cGVvZiBjYWxsYmFjaz09PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgICAgIHZhciByZXN1bHQgPSBjYWxsYmFjayhzZXR0ZWRMb2NhdGUpO1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiByZXN1bHQ7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS5zaG93KCc8ZGl2IGNsYXNzPVwiaW5wdXQtZ3JvdXBcIj48aW5wdXQgdHlwZT1cInRleHRcIiBjbGFzcz1cImZvcm0tY29udHJvbCBzZWFyY2h0ZXh0XCIgbmFtZT1cImtleXdvcmRcIiBwbGFjZWhvbGRlcj1cIuWhq+WGmeWcsOWdgOajgOe0ouS9jee9rlwiLz48ZGl2IGNsYXNzPVwiaW5wdXQtZ3JvdXAtYXBwZW5kXCI+PGEgY2xhc3M9XCJidG4gYnRuLW91dGxpbmUtc2Vjb25kYXJ5IHNlYXJjaGJ0blwiPjxpIGNsYXNzPVwiaW9uLW1kLXNlYXJjaFwiPjwvaT48L2E+PC9kaXY+PC9kaXY+JyArXHJcbiAgICAgICAgICAgICc8ZGl2IGNsYXNzPVwibWFwIG10LTJcIj48L2Rpdj4nICtcclxuICAgICAgICAgICAgJzxkaXYgY2xhc3M9XCJtYXBpbmZvIG10LTIgdGV4dC1tdXRlZFwiPuacqumAieaLqeS9jee9rjwvZGl2PicsJ+ivt+mAieaLqeWcsOWbvuS9jee9ricpO1xyXG4gICAgfVxyXG59O1xyXG5cclxualF1ZXJ5KGZ1bmN0aW9uKCQpe1xyXG5cclxuICAgIC8v55uR5o6n5oyJ6ZSuXHJcbiAgICAkKGRvY3VtZW50KS5vbigna2V5ZG93bicsIGZ1bmN0aW9uKGUpe1xyXG4gICAgICAgIGlmKCFEaWFsb2cuaW5zdGFuY2UpcmV0dXJuO1xyXG4gICAgICAgIHZhciBkbGc9RGlhbG9nLmluc3RhbmNlO1xyXG4gICAgICAgIGlmIChlLmtleUNvZGUgPT0gMTMpIHtcclxuICAgICAgICAgICAgZGxnLmJveC5maW5kKCcubW9kYWwtZm9vdGVyIC5idG4nKS5lcShkbGcub3B0aW9ucy5kZWZhdWx0QnRuKS50cmlnZ2VyKCdjbGljaycpO1xyXG4gICAgICAgIH1cclxuICAgICAgICAvL+m7mOiupOW3suebkeWQrOWFs+mXrVxyXG4gICAgICAgIC8qaWYgKGUua2V5Q29kZSA9PSAyNykge1xyXG4gICAgICAgICBzZWxmLmhpZGUoKTtcclxuICAgICAgICAgfSovXHJcbiAgICB9KTtcclxufSk7IiwiXHJcbmpRdWVyeS5leHRlbmQoalF1ZXJ5LmZuLHtcclxuICAgIHRhZ3M6ZnVuY3Rpb24obm0sb251cGRhdGUpe1xyXG4gICAgICAgIHZhciBkYXRhPVtdO1xyXG4gICAgICAgIHZhciB0cGw9JzxzcGFuIGNsYXNzPVwiYmFkZ2UgYmFkZ2UtaW5mb1wiPntAbGFiZWx9PGlucHV0IHR5cGU9XCJoaWRkZW5cIiBuYW1lPVwiJytubSsnXCIgdmFsdWU9XCJ7QGxhYmVsfVwiLz48YnV0dG9uIHR5cGU9XCJidXR0b25cIiBjbGFzcz1cImNsb3NlXCIgZGF0YS1kaXNtaXNzPVwiYWxlcnRcIiBhcmlhLWxhYmVsPVwiQ2xvc2VcIj48c3BhbiBhcmlhLWhpZGRlbj1cInRydWVcIj4mdGltZXM7PC9zcGFuPjwvYnV0dG9uPjwvc3Bhbj4nO1xyXG4gICAgICAgIHZhciBpdGVtPSQodGhpcykucGFyZW50cygnLmZvcm0tY29udHJvbCcpO1xyXG4gICAgICAgIHZhciBsYWJlbGdyb3VwPSQoJzxzcGFuIGNsYXNzPVwiYmFkZ2UtZ3JvdXBcIj48L3NwYW4+Jyk7XHJcbiAgICAgICAgdmFyIGlucHV0PXRoaXM7XHJcbiAgICAgICAgdGhpcy5iZWZvcmUobGFiZWxncm91cCk7XHJcbiAgICAgICAgdGhpcy5vbigna2V5dXAnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgIHZhciB2YWw9JCh0aGlzKS52YWwoKS5yZXBsYWNlKC/vvIwvZywnLCcpO1xyXG4gICAgICAgICAgICB2YXIgdXBkYXRlZD1mYWxzZTtcclxuICAgICAgICAgICAgaWYodmFsICYmIHZhbC5pbmRleE9mKCcsJyk+LTEpe1xyXG4gICAgICAgICAgICAgICAgdmFyIHZhbHM9dmFsLnNwbGl0KCcsJyk7XHJcbiAgICAgICAgICAgICAgICBmb3IodmFyIGk9MDtpPHZhbHMubGVuZ3RoO2krKyl7XHJcbiAgICAgICAgICAgICAgICAgICAgdmFsc1tpXT12YWxzW2ldLnJlcGxhY2UoL15cXHN8XFxzJC9nLCcnKTtcclxuICAgICAgICAgICAgICAgICAgICBpZih2YWxzW2ldICYmIGRhdGEuaW5kZXhPZih2YWxzW2ldKT09PS0xKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgZGF0YS5wdXNoKHZhbHNbaV0pO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBsYWJlbGdyb3VwLmFwcGVuZCh0cGwuY29tcGlsZSh7bGFiZWw6dmFsc1tpXX0pKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgdXBkYXRlZD10cnVlO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIGlucHV0LnZhbCgnJyk7XHJcbiAgICAgICAgICAgICAgICBpZih1cGRhdGVkICYmIG9udXBkYXRlKW9udXBkYXRlKGRhdGEpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkub24oJ2JsdXInLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgIHZhciB2YWw9JCh0aGlzKS52YWwoKTtcclxuICAgICAgICAgICAgaWYodmFsKSB7XHJcbiAgICAgICAgICAgICAgICAkKHRoaXMpLnZhbCh2YWwgKyAnLCcpLnRyaWdnZXIoJ2tleXVwJyk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS50cmlnZ2VyKCdibHVyJyk7XHJcbiAgICAgICAgbGFiZWxncm91cC5vbignY2xpY2snLCcuY2xvc2UnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgIHZhciB0YWc9JCh0aGlzKS5wYXJlbnRzKCcuYmFkZ2UnKS5maW5kKCdpbnB1dCcpLnZhbCgpO1xyXG4gICAgICAgICAgICB2YXIgaWQ9ZGF0YS5pbmRleE9mKHRhZyk7XHJcbiAgICAgICAgICAgIGlmKGlkKWRhdGEuc3BsaWNlKGlkLDEpO1xyXG4gICAgICAgICAgICAkKHRoaXMpLnBhcmVudHMoJy5iYWRnZScpLnJlbW92ZSgpO1xyXG4gICAgICAgICAgICBpZihvbnVwZGF0ZSlvbnVwZGF0ZShkYXRhKTtcclxuICAgICAgICB9KTtcclxuICAgICAgICBpdGVtLmNsaWNrKGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgIGlucHV0LmZvY3VzKCk7XHJcbiAgICAgICAgfSk7XHJcbiAgICB9XHJcbn0pOyIsIi8v5pel5pyf57uE5Lu2XHJcbmlmKCQuZm4uZGF0ZXRpbWVwaWNrZXIpIHtcclxuICAgIHZhciB0b29sdGlwcz0ge1xyXG4gICAgICAgIHRvZGF5OiAn5a6a5L2N5b2T5YmN5pel5pyfJyxcclxuICAgICAgICBjbGVhcjogJ+a4hemZpOW3sumAieaXpeacnycsXHJcbiAgICAgICAgY2xvc2U6ICflhbPpl63pgInmi6nlmagnLFxyXG4gICAgICAgIHNlbGVjdE1vbnRoOiAn6YCJ5oup5pyI5Lu9JyxcclxuICAgICAgICBwcmV2TW9udGg6ICfkuIrkuKrmnIgnLFxyXG4gICAgICAgIG5leHRNb250aDogJ+S4i+S4quaciCcsXHJcbiAgICAgICAgc2VsZWN0WWVhcjogJ+mAieaLqeW5tOS7vScsXHJcbiAgICAgICAgcHJldlllYXI6ICfkuIrkuIDlubQnLFxyXG4gICAgICAgIG5leHRZZWFyOiAn5LiL5LiA5bm0JyxcclxuICAgICAgICBzZWxlY3REZWNhZGU6ICfpgInmi6nlubTku73ljLrpl7QnLFxyXG4gICAgICAgIHNlbGVjdFRpbWU6J+mAieaLqeaXtumXtCcsXHJcbiAgICAgICAgcHJldkRlY2FkZTogJ+S4iuS4gOWMuumXtCcsXHJcbiAgICAgICAgbmV4dERlY2FkZTogJ+S4i+S4gOWMuumXtCcsXHJcbiAgICAgICAgcHJldkNlbnR1cnk6ICfkuIrkuKrkuJbnuqonLFxyXG4gICAgICAgIG5leHRDZW50dXJ5OiAn5LiL5Liq5LiW57qqJ1xyXG4gICAgfTtcclxuXHJcbiAgICBmdW5jdGlvbiB0cmFuc09wdGlvbihvcHRpb24pIHtcclxuICAgICAgICBpZighb3B0aW9uKXJldHVybiB7fTtcclxuICAgICAgICB2YXIgbmV3b3B0PXt9O1xyXG4gICAgICAgIGZvcih2YXIgaSBpbiBvcHRpb24pe1xyXG4gICAgICAgICAgICBzd2l0Y2ggKGkpe1xyXG4gICAgICAgICAgICAgICAgY2FzZSAndmlld21vZGUnOlxyXG4gICAgICAgICAgICAgICAgICAgIG5ld29wdFsndmlld01vZGUnXT1vcHRpb25baV07XHJcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgICAgICBjYXNlICdrZWVwb3Blbic6XHJcbiAgICAgICAgICAgICAgICAgICAgbmV3b3B0WydrZWVwT3BlbiddPW9wdGlvbltpXTtcclxuICAgICAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgICAgIGRlZmF1bHQ6XHJcbiAgICAgICAgICAgICAgICAgICAgbmV3b3B0W2ldPW9wdGlvbltpXTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH1cclxuICAgICAgICByZXR1cm4gbmV3b3B0O1xyXG4gICAgfVxyXG4gICAgJCgnLmRhdGVwaWNrZXInKS5lYWNoKGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgdmFyIGNvbmZpZz0kLmV4dGVuZCh7XHJcbiAgICAgICAgICAgIHRvb2x0aXBzOnRvb2x0aXBzLFxyXG4gICAgICAgICAgICBmb3JtYXQ6ICdZWVlZLU1NLUREJyxcclxuICAgICAgICAgICAgbG9jYWxlOiAnemgtY24nLFxyXG4gICAgICAgICAgICBzaG93Q2xlYXI6dHJ1ZSxcclxuICAgICAgICAgICAgc2hvd1RvZGF5QnV0dG9uOnRydWUsXHJcbiAgICAgICAgICAgIHNob3dDbG9zZTp0cnVlLFxyXG4gICAgICAgICAgICBrZWVwSW52YWxpZDp0cnVlXHJcbiAgICAgICAgfSx0cmFuc09wdGlvbigkKHRoaXMpLmRhdGEoKSkpO1xyXG5cclxuICAgICAgICAkKHRoaXMpLmRhdGV0aW1lcGlja2VyKGNvbmZpZyk7XHJcbiAgICB9KTtcclxuXHJcbiAgICAkKCcuZGF0ZS1yYW5nZScpLmVhY2goZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHZhciBmcm9tID0gJCh0aGlzKS5maW5kKCdbbmFtZT1mcm9tZGF0ZV0sLmZyb21kYXRlJyksIHRvID0gJCh0aGlzKS5maW5kKCdbbmFtZT10b2RhdGVdLC50b2RhdGUnKTtcclxuICAgICAgICB2YXIgb3B0aW9ucyA9ICQuZXh0ZW5kKHtcclxuICAgICAgICAgICAgdG9vbHRpcHM6dG9vbHRpcHMsXHJcbiAgICAgICAgICAgIGZvcm1hdDogJ1lZWVktTU0tREQnLFxyXG4gICAgICAgICAgICBsb2NhbGU6J3poLWNuJyxcclxuICAgICAgICAgICAgc2hvd0NsZWFyOnRydWUsXHJcbiAgICAgICAgICAgIHNob3dUb2RheUJ1dHRvbjp0cnVlLFxyXG4gICAgICAgICAgICBzaG93Q2xvc2U6dHJ1ZSxcclxuICAgICAgICAgICAga2VlcEludmFsaWQ6dHJ1ZVxyXG4gICAgICAgIH0sJCh0aGlzKS5kYXRhKCkpO1xyXG4gICAgICAgIGZyb20uZGF0ZXRpbWVwaWNrZXIob3B0aW9ucykub24oJ2RwLmNoYW5nZScsIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgaWYgKGZyb20udmFsKCkpIHtcclxuICAgICAgICAgICAgICAgIHRvLmRhdGEoJ0RhdGVUaW1lUGlja2VyJykubWluRGF0ZShmcm9tLnZhbCgpKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pO1xyXG4gICAgICAgIHRvLmRhdGV0aW1lcGlja2VyKG9wdGlvbnMpLm9uKCdkcC5jaGFuZ2UnLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIGlmICh0by52YWwoKSkge1xyXG4gICAgICAgICAgICAgICAgZnJvbS5kYXRhKCdEYXRlVGltZVBpY2tlcicpLm1heERhdGUodG8udmFsKCkpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSk7XHJcbiAgICB9KTtcclxufSIsIlxyXG4oZnVuY3Rpb24od2luZG93LCQpIHtcclxuICAgIHZhciBhcGlzID0ge1xyXG4gICAgICAgICdiYWlkdSc6ICdodHRwczovL2FwaS5tYXAuYmFpZHUuY29tL2FwaT9haz1yTzl0T2RFV0ZmdnlHZ0RraVdxRmp4SzYmdj0xLjUmc2VydmljZXM9ZmFsc2UmY2FsbGJhY2s9JyxcclxuICAgICAgICAnZ29vZ2xlJzogJ2h0dHBzOi8vbWFwcy5nb29nbGUuY29tL21hcHMvYXBpL2pzP2tleT1BSXphU3lCOGxvcnZsNkV0cUlXejY3YmpXQnJ1T2htOU5ZUzFlMjQmY2FsbGJhY2s9JyxcclxuICAgICAgICAndGVuY2VudCc6ICdodHRwczovL21hcC5xcS5jb20vYXBpL2pzP3Y9Mi5leHAma2V5PTdJNUJaLVFVRTZSLUpYTFdWLVdUVkFBLUNKTVlGLTdQQkJJJmNhbGxiYWNrPScsXHJcbiAgICAgICAgJ2dhb2RlJzogJ2h0dHBzOi8vd2ViYXBpLmFtYXAuY29tL21hcHM/dj0xLjMma2V5PTNlYzMxMWI1ZGIwZDU5N2U3OTQyMmVlYjlhNmQ0NDQ5JmNhbGxiYWNrPSdcclxuICAgIH07XHJcblxyXG4gICAgZnVuY3Rpb24gbG9hZFNjcmlwdChzcmMpIHtcclxuICAgICAgICB2YXIgc2NyaXB0ID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudChcInNjcmlwdFwiKTtcclxuICAgICAgICBzY3JpcHQudHlwZSA9IFwidGV4dC9qYXZhc2NyaXB0XCI7XHJcbiAgICAgICAgc2NyaXB0LnNyYyA9IHNyYztcclxuICAgICAgICBkb2N1bWVudC5ib2R5LmFwcGVuZENoaWxkKHNjcmlwdCk7XHJcbiAgICB9XHJcblxyXG4gICAgdmFyIG1hcE9iaixtYXBCb3gsb25QaWNrO1xyXG5cclxuICAgIGZ1bmN0aW9uIEluaXRNYXAobWFwa2V5LGJveCxjYWxsYmFjayxsb2NhdGUpIHtcclxuICAgICAgICBpZiAobWFwT2JqKSBtYXBPYmouaGlkZSgpO1xyXG4gICAgICAgIG1hcEJveD0kKGJveCk7XHJcbiAgICAgICAgb25QaWNrPWNhbGxiYWNrO1xyXG5cclxuICAgICAgICBzd2l0Y2ggKG1hcGtleS50b0xvd2VyQ2FzZSgpKSB7XHJcbiAgICAgICAgICAgIGNhc2UgJ2JhaWR1JzpcclxuICAgICAgICAgICAgICAgIG1hcE9iaiA9IG5ldyBCYWlkdU1hcCgpO1xyXG4gICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgIGNhc2UgJ2dvb2dsZSc6XHJcbiAgICAgICAgICAgICAgICBtYXBPYmogPSBuZXcgR29vZ2xlTWFwKCk7XHJcbiAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgY2FzZSAndGVuY2VudCc6XHJcbiAgICAgICAgICAgIGNhc2UgJ3FxJzpcclxuICAgICAgICAgICAgICAgIG1hcE9iaiA9IG5ldyBUZW5jZW50TWFwKCk7XHJcbiAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgY2FzZSAnZ2FvZGUnOlxyXG4gICAgICAgICAgICAgICAgbWFwT2JqID0gbmV3IEdhb2RlTWFwKCk7XHJcbiAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICB9XHJcbiAgICAgICAgaWYgKCFtYXBPYmopIHJldHVybiB0b2FzdHIud2FybmluZygn5LiN5pSv5oyB6K+l5Zyw5Zu+57G75Z6LJyk7XHJcbiAgICAgICAgaWYobG9jYXRlKXtcclxuICAgICAgICAgICAgaWYodHlwZW9mIGxvY2F0ZT09PSdzdHJpbmcnKXtcclxuICAgICAgICAgICAgICAgIHZhciBsb2M9bG9jYXRlLnNwbGl0KCcsJyk7XHJcbiAgICAgICAgICAgICAgICBsb2NhdGU9e1xyXG4gICAgICAgICAgICAgICAgICAgIGxuZzpwYXJzZUZsb2F0KGxvY1swXSksXHJcbiAgICAgICAgICAgICAgICAgICAgbGF0OnBhcnNlRmxvYXQobG9jWzFdKVxyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIG1hcE9iai5sb2NhdGU9bG9jYXRlO1xyXG4gICAgICAgIH1cclxuICAgICAgICBtYXBPYmouc2V0TWFwKCk7XHJcblxyXG4gICAgICAgIHJldHVybiBtYXBPYmo7XHJcbiAgICB9XHJcblxyXG4gICAgZnVuY3Rpb24gQmFzZU1hcCh0eXBlKSB7XHJcbiAgICAgICAgdGhpcy5tYXBUeXBlID0gdHlwZTtcclxuICAgICAgICB0aGlzLmlzaGlkZSA9IGZhbHNlO1xyXG4gICAgICAgIHRoaXMuaXNzaG93ID0gZmFsc2U7XHJcbiAgICAgICAgdGhpcy50b3Nob3cgPSBmYWxzZTtcclxuICAgICAgICB0aGlzLm1hcmtlciA9IG51bGw7XHJcbiAgICAgICAgdGhpcy5pbmZvV2luZG93ID0gbnVsbDtcclxuICAgICAgICB0aGlzLm1hcGJveCA9IG51bGw7XHJcbiAgICAgICAgdGhpcy5sb2NhdGUgPSB7bG5nOjExNi4zOTY3OTUsbGF0OjM5LjkzMzA4NH07XHJcbiAgICAgICAgdGhpcy5tYXAgPSBudWxsO1xyXG4gICAgfVxyXG5cclxuICAgIEJhc2VNYXAucHJvdG90eXBlLmlzQVBJUmVhZHkgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgfTtcclxuICAgIEJhc2VNYXAucHJvdG90eXBlLnNldE1hcCA9IGZ1bmN0aW9uICgpIHtcclxuICAgIH07XHJcbiAgICBCYXNlTWFwLnByb3RvdHlwZS5zaG93SW5mbyA9IGZ1bmN0aW9uICgpIHtcclxuICAgIH07XHJcbiAgICBCYXNlTWFwLnByb3RvdHlwZS5nZXRBZGRyZXNzID0gZnVuY3Rpb24gKHJzKSB7XHJcbiAgICAgICAgcmV0dXJuIFwiXCI7XHJcbiAgICB9O1xyXG4gICAgQmFzZU1hcC5wcm90b3R5cGUuc2V0TG9jYXRlID0gZnVuY3Rpb24gKGFkZHJlc3MpIHtcclxuICAgIH07XHJcblxyXG4gICAgQmFzZU1hcC5wcm90b3R5cGUubG9hZEFQSSA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICB2YXIgc2VsZiA9IHRoaXM7XHJcbiAgICAgICAgaWYgKCF0aGlzLmlzQVBJUmVhZHkoKSkge1xyXG4gICAgICAgICAgICB0aGlzLm1hcGJveCA9ICQoJzxkaXYgaWQ9XCInICsgdGhpcy5tYXBUeXBlICsgJ21hcFwiIGNsYXNzPVwibWFwYm94XCI+bG9hZGluZy4uLjwvZGl2PicpO1xyXG4gICAgICAgICAgICBtYXBCb3guYXBwZW5kKHRoaXMubWFwYm94KTtcclxuXHJcbiAgICAgICAgICAgIC8vY29uc29sZS5sb2codGhpcy5tYXBUeXBlKycgbWFwbG9hZGluZy4uLicpO1xyXG4gICAgICAgICAgICB2YXIgZnVuYyA9ICdtYXBsb2FkJyArIG5ldyBEYXRlKCkuZ2V0VGltZSgpO1xyXG4gICAgICAgICAgICB3aW5kb3dbZnVuY10gPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgICAgICBzZWxmLnNldE1hcCgpO1xyXG4gICAgICAgICAgICAgICAgZGVsZXRlIHdpbmRvd1tmdW5jXTtcclxuICAgICAgICAgICAgfTtcclxuICAgICAgICAgICAgbG9hZFNjcmlwdChhcGlzW3RoaXMubWFwVHlwZV0gKyBmdW5jKTtcclxuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgIC8vY29uc29sZS5sb2codGhpcy5tYXBUeXBlICsgJyBtYXBsb2FkZWQnKTtcclxuICAgICAgICAgICAgdGhpcy5tYXBib3ggPSAkKCcjJyArIHRoaXMubWFwVHlwZSArICdtYXAnKTtcclxuICAgICAgICAgICAgaWYgKHRoaXMubWFwYm94Lmxlbmd0aCA8IDEpIHtcclxuICAgICAgICAgICAgICAgIHRoaXMubWFwYm94ID0gJCgnPGRpdiBpZD1cIicgKyB0aGlzLm1hcFR5cGUgKyAnbWFwXCIgY2xhc3M9XCJtYXBib3hcIj48L2Rpdj4nKTtcclxuICAgICAgICAgICAgICAgIG1hcEJveC5hcHBlbmQodGhpcy5tYXBib3gpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIHJldHVybiB0cnVlO1xyXG4gICAgICAgIH1cclxuICAgIH07XHJcbiAgICBCYXNlTWFwLnByb3RvdHlwZS5iaW5kRXZlbnRzID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHZhciBzZWxmID0gdGhpcztcclxuICAgICAgICAkKCcjdHh0VGl0bGUnKS51bmJpbmQoKS5ibHVyKGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgc2VsZi5zaG93SW5mbygpO1xyXG4gICAgICAgIH0pO1xyXG4gICAgICAgICQoJyN0eHRDb250ZW50JykudW5iaW5kKCkuYmx1cihmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIHNlbGYuc2hvd0luZm8oKTtcclxuICAgICAgICB9KTtcclxuICAgIH07XHJcbiAgICBCYXNlTWFwLnByb3RvdHlwZS5zZXRJbmZvQ29udGVudCA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICBpZiAoIXRoaXMuaW5mb1dpbmRvdykgcmV0dXJuO1xyXG4gICAgICAgIHZhciB0aXRsZSA9ICc8Yj7lvZPliY3kvY3nva48L2I+JztcclxuICAgICAgICB2YXIgYWRkciA9ICc8cCBzdHlsZT1cImxpbmUtaGVpZ2h0OjEuNmVtO1wiPjwvcD4nO1xyXG4gICAgICAgIGlmICh0aGlzLmluZm9XaW5kb3cuc2V0VGl0bGUpIHtcclxuICAgICAgICAgICAgdGhpcy5pbmZvV2luZG93LnNldFRpdGxlKHRpdGxlKTtcclxuICAgICAgICAgICAgdGhpcy5pbmZvV2luZG93LnNldENvbnRlbnQoYWRkcik7XHJcbiAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgdmFyIGNvbnRlbnQgPSAnPGgzPicgKyB0aXRsZSArICc8L2gzPjxkaXYgc3R5bGU9XCJ3aWR0aDoyNTBweFwiPicgKyBhZGRyICsgJzwvZGl2Pic7XHJcbiAgICAgICAgICAgIHRoaXMuaW5mb1dpbmRvdy5zZXRDb250ZW50KGNvbnRlbnQpO1xyXG4gICAgICAgIH1cclxuICAgIH07XHJcbiAgICBCYXNlTWFwLnByb3RvdHlwZS5zaG93TG9jYXRpb25JbmZvID0gZnVuY3Rpb24gKHB0LCBycykge1xyXG5cclxuICAgICAgICB0aGlzLnNob3dJbmZvKCk7XHJcbiAgICAgICAgdmFyIGFkZHJlc3M9dGhpcy5nZXRBZGRyZXNzKHJzKTtcclxuICAgICAgICB2YXIgbG9jYXRlPXt9O1xyXG4gICAgICAgIGlmICh0eXBlb2YgKHB0LmxuZykgPT09ICdmdW5jdGlvbicpIHtcclxuICAgICAgICAgICAgbG9jYXRlLmxuZz1wdC5sbmcoKTtcclxuICAgICAgICAgICAgbG9jYXRlLmxhdD1wdC5sYXQoKTtcclxuICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICBsb2NhdGUubG5nPXB0LmxuZztcclxuICAgICAgICAgICAgbG9jYXRlLmxhdD1wdC5sYXQ7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICBvblBpY2soYWRkcmVzcyxsb2NhdGUpO1xyXG4gICAgfTtcclxuICAgIEJhc2VNYXAucHJvdG90eXBlLnNob3cgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgdGhpcy5pc2hpZGUgPSBmYWxzZTtcclxuICAgICAgICB0aGlzLnNldE1hcCgpO1xyXG4gICAgICAgIHRoaXMuc2hvd0luZm8oKTtcclxuICAgIH07XHJcbiAgICBCYXNlTWFwLnByb3RvdHlwZS5oaWRlID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHRoaXMuaXNoaWRlID0gdHJ1ZTtcclxuICAgICAgICBpZiAodGhpcy5pbmZvV2luZG93KSB7XHJcbiAgICAgICAgICAgIHRoaXMuaW5mb1dpbmRvdy5jbG9zZSgpO1xyXG4gICAgICAgIH1cclxuICAgICAgICBpZiAodGhpcy5tYXBib3gpIHtcclxuICAgICAgICAgICAgJCh0aGlzLm1hcGJveCkucmVtb3ZlKCk7XHJcbiAgICAgICAgfVxyXG4gICAgfTtcclxuXHJcblxyXG4gICAgZnVuY3Rpb24gQmFpZHVNYXAoKSB7XHJcbiAgICAgICAgQmFzZU1hcC5jYWxsKHRoaXMsIFwiYmFpZHVcIik7XHJcbiAgICB9XHJcblxyXG4gICAgQmFpZHVNYXAucHJvdG90eXBlID0gbmV3IEJhc2VNYXAoKTtcclxuICAgIEJhaWR1TWFwLnByb3RvdHlwZS5jb25zdHJ1Y3RvciA9IEJhaWR1TWFwO1xyXG4gICAgQmFpZHVNYXAucHJvdG90eXBlLmlzQVBJUmVhZHkgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgcmV0dXJuICEhd2luZG93WydCTWFwJ107XHJcbiAgICB9O1xyXG4gICAgQmFpZHVNYXAucHJvdG90eXBlLnNldE1hcCA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICB2YXIgc2VsZiA9IHRoaXM7XHJcbiAgICAgICAgaWYgKHRoaXMuaXNzaG93IHx8IHRoaXMuaXNoaWRlKSByZXR1cm47XHJcbiAgICAgICAgaWYgKCF0aGlzLmxvYWRBUEkoKSkgcmV0dXJuO1xyXG5cclxuICAgICAgICB2YXIgbWFwID0gc2VsZi5tYXAgPSBuZXcgQk1hcC5NYXAodGhpcy5tYXBib3guYXR0cignaWQnKSk7IC8v5Yid5aeL5YyW5Zyw5Zu+XHJcbiAgICAgICAgbWFwLmFkZENvbnRyb2wobmV3IEJNYXAuTmF2aWdhdGlvbkNvbnRyb2woKSk7ICAvL+WIneWni+WMluWcsOWbvuaOp+S7tlxyXG4gICAgICAgIG1hcC5hZGRDb250cm9sKG5ldyBCTWFwLlNjYWxlQ29udHJvbCgpKTtcclxuICAgICAgICBtYXAuYWRkQ29udHJvbChuZXcgQk1hcC5PdmVydmlld01hcENvbnRyb2woKSk7XHJcbiAgICAgICAgbWFwLmVuYWJsZVNjcm9sbFdoZWVsWm9vbSgpO1xyXG5cclxuICAgICAgICB2YXIgcG9pbnQgPSBuZXcgQk1hcC5Qb2ludCh0aGlzLmxvY2F0ZS5sbmcsIHRoaXMubG9jYXRlLmxhdCk7XHJcbiAgICAgICAgbWFwLmNlbnRlckFuZFpvb20ocG9pbnQsIDE1KTsgLy/liJ3lp4vljJblnLDlm77kuK3lv4PngrlcclxuICAgICAgICB0aGlzLm1hcmtlciA9IG5ldyBCTWFwLk1hcmtlcihwb2ludCk7IC8v5Yid5aeL5YyW5Zyw5Zu+5qCH6K6wXHJcbiAgICAgICAgdGhpcy5tYXJrZXIuZW5hYmxlRHJhZ2dpbmcoKTsgLy/moIforrDlvIDlkK/mi5bmi71cclxuXHJcbiAgICAgICAgdmFyIGdjID0gbmV3IEJNYXAuR2VvY29kZXIoKTsgLy/lnLDlnYDop6PmnpDnsbtcclxuICAgICAgICAvL+a3u+WKoOagh+iusOaLluaLveebkeWQrFxyXG4gICAgICAgIHRoaXMubWFya2VyLmFkZEV2ZW50TGlzdGVuZXIoXCJkcmFnZW5kXCIsIGZ1bmN0aW9uIChlKSB7XHJcbiAgICAgICAgICAgIC8v6I635Y+W5Zyw5Z2A5L+h5oGvXHJcbiAgICAgICAgICAgIGdjLmdldExvY2F0aW9uKGUucG9pbnQsIGZ1bmN0aW9uIChycykge1xyXG4gICAgICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKGUucG9pbnQsIHJzKTtcclxuICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIC8v5re75Yqg5qCH6K6w54K55Ye755uR5ZCsXHJcbiAgICAgICAgdGhpcy5tYXJrZXIuYWRkRXZlbnRMaXN0ZW5lcihcImNsaWNrXCIsIGZ1bmN0aW9uIChlKSB7XHJcbiAgICAgICAgICAgIGdjLmdldExvY2F0aW9uKGUucG9pbnQsIGZ1bmN0aW9uIChycykge1xyXG4gICAgICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKGUucG9pbnQsIHJzKTtcclxuICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIG1hcC5hZGRPdmVybGF5KHRoaXMubWFya2VyKTsgLy/lsIbmoIforrDmt7vliqDliLDlnLDlm77kuK1cclxuXHJcbiAgICAgICAgZ2MuZ2V0TG9jYXRpb24ocG9pbnQsIGZ1bmN0aW9uIChycykge1xyXG4gICAgICAgICAgICBzZWxmLnNob3dMb2NhdGlvbkluZm8ocG9pbnQsIHJzKTtcclxuICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgdGhpcy5pbmZvV2luZG93ID0gbmV3IEJNYXAuSW5mb1dpbmRvdyhcIlwiLCB7XHJcbiAgICAgICAgICAgIHdpZHRoOiAyNTAsXHJcbiAgICAgICAgICAgIHRpdGxlOiBcIlwiXHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIHRoaXMuYmluZEV2ZW50cygpO1xyXG5cclxuICAgICAgICB0aGlzLmlzc2hvdyA9IHRydWU7XHJcbiAgICAgICAgaWYgKHRoaXMudG9zaG93KSB7XHJcbiAgICAgICAgICAgIHRoaXMuc2hvd0luZm8oKTtcclxuICAgICAgICAgICAgdGhpcy50b3Nob3cgPSBmYWxzZTtcclxuICAgICAgICB9XHJcbiAgICB9O1xyXG5cclxuICAgIEJhaWR1TWFwLnByb3RvdHlwZS5zaG93SW5mbyA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICBpZiAodGhpcy5pc2hpZGUpIHJldHVybjtcclxuICAgICAgICBpZiAoIXRoaXMuaXNzaG93KSB7XHJcbiAgICAgICAgICAgIHRoaXMudG9zaG93ID0gdHJ1ZTtcclxuICAgICAgICAgICAgcmV0dXJuO1xyXG4gICAgICAgIH1cclxuICAgICAgICB0aGlzLnNldEluZm9Db250ZW50KCk7XHJcblxyXG4gICAgICAgIHRoaXMubWFya2VyLm9wZW5JbmZvV2luZG93KHRoaXMuaW5mb1dpbmRvdyk7XHJcbiAgICB9O1xyXG4gICAgQmFpZHVNYXAucHJvdG90eXBlLmdldEFkZHJlc3MgPSBmdW5jdGlvbiAocnMpIHtcclxuICAgICAgICB2YXIgYWRkQ29tcCA9IHJzLmFkZHJlc3NDb21wb25lbnRzO1xyXG4gICAgICAgIGlmKGFkZENvbXApIHtcclxuICAgICAgICAgICAgcmV0dXJuIGFkZENvbXAucHJvdmluY2UgKyBcIiwgXCIgKyBhZGRDb21wLmNpdHkgKyBcIiwgXCIgKyBhZGRDb21wLmRpc3RyaWN0ICsgXCIsIFwiICsgYWRkQ29tcC5zdHJlZXQgKyBcIiwgXCIgKyBhZGRDb21wLnN0cmVldE51bWJlcjtcclxuICAgICAgICB9XHJcbiAgICB9O1xyXG4gICAgQmFpZHVNYXAucHJvdG90eXBlLnNldExvY2F0ZSA9IGZ1bmN0aW9uIChhZGRyZXNzKSB7XHJcbiAgICAgICAgLy8g5Yib5bu65Zyw5Z2A6Kej5p6Q5Zmo5a6e5L6LXHJcbiAgICAgICAgdmFyIG15R2VvID0gbmV3IEJNYXAuR2VvY29kZXIoKTtcclxuICAgICAgICB2YXIgc2VsZiA9IHRoaXM7XHJcbiAgICAgICAgbXlHZW8uZ2V0UG9pbnQoYWRkcmVzcywgZnVuY3Rpb24gKHBvaW50KSB7XHJcbiAgICAgICAgICAgIGlmIChwb2ludCkge1xyXG4gICAgICAgICAgICAgICAgc2VsZi5tYXAuY2VudGVyQW5kWm9vbShwb2ludCwgMTEpO1xyXG4gICAgICAgICAgICAgICAgc2VsZi5tYXJrZXIuc2V0UG9zaXRpb24ocG9pbnQpO1xyXG4gICAgICAgICAgICAgICAgbXlHZW8uZ2V0TG9jYXRpb24ocG9pbnQsIGZ1bmN0aW9uIChycykge1xyXG4gICAgICAgICAgICAgICAgICAgIHNlbGYuc2hvd0xvY2F0aW9uSW5mbyhwb2ludCwgcnMpO1xyXG4gICAgICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICB0b2FzdHIud2FybmluZyhcIuWcsOWdgOS/oeaBr+S4jeato+ehru+8jOWumuS9jeWksei0pVwiKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0sICcnKTtcclxuICAgIH07XHJcblxyXG5cclxuICAgIGZ1bmN0aW9uIEdvb2dsZU1hcCgpIHtcclxuICAgICAgICBCYXNlTWFwLmNhbGwodGhpcywgXCJnb29nbGVcIik7XHJcbiAgICAgICAgdGhpcy5pbmZvT3B0cyA9IHtcclxuICAgICAgICAgICAgd2lkdGg6IDI1MCwgICAgIC8v5L+h5oGv56qX5Y+j5a695bqmXHJcbiAgICAgICAgICAgIC8vICAgaGVpZ2h0OiAxMDAsICAgICAvL+S/oeaBr+eql+WPo+mrmOW6plxyXG4gICAgICAgICAgICB0aXRsZTogXCJcIiAgLy/kv6Hmga/nqpflj6PmoIfpophcclxuICAgICAgICB9O1xyXG4gICAgfVxyXG5cclxuICAgIEdvb2dsZU1hcC5wcm90b3R5cGUgPSBuZXcgQmFzZU1hcCgpO1xyXG4gICAgR29vZ2xlTWFwLnByb3RvdHlwZS5jb25zdHJ1Y3RvciA9IEdvb2dsZU1hcDtcclxuICAgIEdvb2dsZU1hcC5wcm90b3R5cGUuaXNBUElSZWFkeSA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICByZXR1cm4gd2luZG93Wydnb29nbGUnXSAmJiB3aW5kb3dbJ2dvb2dsZSddWydtYXBzJ11cclxuICAgIH07XHJcbiAgICBHb29nbGVNYXAucHJvdG90eXBlLnNldE1hcCA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICB2YXIgc2VsZiA9IHRoaXM7XHJcbiAgICAgICAgaWYgKHRoaXMuaXNzaG93IHx8IHRoaXMuaXNoaWRlKSByZXR1cm47XHJcbiAgICAgICAgaWYgKCF0aGlzLmxvYWRBUEkoKSkgcmV0dXJuO1xyXG5cclxuICAgICAgICAvL+ivtOaYjuWcsOWbvuW3suWIh+aNolxyXG4gICAgICAgIGlmICh0aGlzLm1hcGJveC5sZW5ndGggPCAxKSByZXR1cm47XHJcblxyXG4gICAgICAgIHZhciBtYXAgPSBzZWxmLm1hcCA9IG5ldyBnb29nbGUubWFwcy5NYXAodGhpcy5tYXBib3hbMF0sIHtcclxuICAgICAgICAgICAgem9vbTogMTUsXHJcbiAgICAgICAgICAgIGRyYWdnYWJsZTogdHJ1ZSxcclxuICAgICAgICAgICAgc2NhbGVDb250cm9sOiB0cnVlLFxyXG4gICAgICAgICAgICBzdHJlZXRWaWV3Q29udHJvbDogdHJ1ZSxcclxuICAgICAgICAgICAgem9vbUNvbnRyb2w6IHRydWVcclxuICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgLy/ojrflj5bnu4/nuqzluqblnZDmoIflgLxcclxuICAgICAgICB2YXIgcG9pbnQgPSBuZXcgZ29vZ2xlLm1hcHMuTGF0TG5nKHRoaXMubG9jYXRlKTtcclxuICAgICAgICBtYXAucGFuVG8ocG9pbnQpO1xyXG4gICAgICAgIHRoaXMubWFya2VyID0gbmV3IGdvb2dsZS5tYXBzLk1hcmtlcih7cG9zaXRpb246IHBvaW50LCBtYXA6IG1hcCwgZHJhZ2dhYmxlOiB0cnVlfSk7XHJcblxyXG5cclxuICAgICAgICB2YXIgZ2MgPSBuZXcgZ29vZ2xlLm1hcHMuR2VvY29kZXIoKTtcclxuXHJcbiAgICAgICAgdGhpcy5tYXJrZXIuYWRkTGlzdGVuZXIoXCJkcmFnZW5kXCIsIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgcG9pbnQgPSBzZWxmLm1hcmtlci5nZXRQb3NpdGlvbigpO1xyXG4gICAgICAgICAgICBnYy5nZW9jb2RlKHsnbG9jYXRpb24nOiBwb2ludH0sIGZ1bmN0aW9uIChycykge1xyXG4gICAgICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKHBvaW50LCBycyk7XHJcbiAgICAgICAgICAgIH0pO1xyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICAvL+a3u+WKoOagh+iusOeCueWHu+ebkeWQrFxyXG4gICAgICAgIHRoaXMubWFya2VyLmFkZExpc3RlbmVyKFwiY2xpY2tcIiwgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICBwb2ludCA9IHNlbGYubWFya2VyLmdldFBvc2l0aW9uKCk7XHJcbiAgICAgICAgICAgIGdjLmdlb2NvZGUoeydsb2NhdGlvbic6IHBvaW50fSwgZnVuY3Rpb24gKHJzKSB7XHJcbiAgICAgICAgICAgICAgICBzZWxmLnNob3dMb2NhdGlvbkluZm8ocG9pbnQsIHJzKTtcclxuICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIHRoaXMuYmluZEV2ZW50cygpO1xyXG5cclxuICAgICAgICBnYy5nZW9jb2RlKHsnbG9jYXRpb24nOiBwb2ludH0sIGZ1bmN0aW9uIChycykge1xyXG4gICAgICAgICAgICBzZWxmLnNob3dMb2NhdGlvbkluZm8ocG9pbnQsIHJzKTtcclxuICAgICAgICB9KTtcclxuICAgICAgICB0aGlzLmluZm9XaW5kb3cgPSBuZXcgZ29vZ2xlLm1hcHMuSW5mb1dpbmRvdyh7bWFwOiBtYXB9KTtcclxuICAgICAgICB0aGlzLmluZm9XaW5kb3cuc2V0UG9zaXRpb24ocG9pbnQpO1xyXG5cclxuICAgICAgICB0aGlzLmlzc2hvdyA9IHRydWU7XHJcbiAgICAgICAgaWYgKHRoaXMudG9zaG93KSB7XHJcbiAgICAgICAgICAgIHRoaXMuc2hvd0luZm8oKTtcclxuICAgICAgICAgICAgdGhpcy50b3Nob3cgPSBmYWxzZTtcclxuICAgICAgICB9XHJcbiAgICB9O1xyXG5cclxuICAgIEdvb2dsZU1hcC5wcm90b3R5cGUuc2hvd0luZm8gPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgaWYgKHRoaXMuaXNoaWRlKSByZXR1cm47XHJcbiAgICAgICAgaWYgKCF0aGlzLmlzc2hvdykge1xyXG4gICAgICAgICAgICB0aGlzLnRvc2hvdyA9IHRydWU7XHJcbiAgICAgICAgICAgIHJldHVybjtcclxuICAgICAgICB9XHJcbiAgICAgICAgdGhpcy5pbmZvV2luZG93LnNldE9wdGlvbnMoe3Bvc2l0aW9uOiB0aGlzLm1hcmtlci5nZXRQb3NpdGlvbigpfSk7XHJcbiAgICAgICAgdGhpcy5zZXRJbmZvQ29udGVudCgpO1xyXG5cclxuICAgIH07XHJcbiAgICBHb29nbGVNYXAucHJvdG90eXBlLmdldEFkZHJlc3MgPSBmdW5jdGlvbiAocnMsIHN0YXR1cykge1xyXG4gICAgICAgIGlmIChycyAmJiByc1swXSkge1xyXG4gICAgICAgICAgICByZXR1cm4gcnNbMF0uZm9ybWF0dGVkX2FkZHJlc3M7XHJcbiAgICAgICAgfVxyXG4gICAgfTtcclxuICAgIEdvb2dsZU1hcC5wcm90b3R5cGUuc2V0TG9jYXRlID0gZnVuY3Rpb24gKGFkZHJlc3MpIHtcclxuICAgICAgICAvLyDliJvlu7rlnLDlnYDop6PmnpDlmajlrp7kvotcclxuICAgICAgICB2YXIgbXlHZW8gPSBuZXcgZ29vZ2xlLm1hcHMuR2VvY29kZXIoKTtcclxuICAgICAgICB2YXIgc2VsZiA9IHRoaXM7XHJcbiAgICAgICAgbXlHZW8uZ2V0UG9pbnQoYWRkcmVzcywgZnVuY3Rpb24gKHBvaW50KSB7XHJcbiAgICAgICAgICAgIGlmIChwb2ludCkge1xyXG4gICAgICAgICAgICAgICAgc2VsZi5tYXAuY2VudGVyQW5kWm9vbShwb2ludCwgMTEpO1xyXG4gICAgICAgICAgICAgICAgc2VsZi5tYXJrZXIuc2V0UG9zaXRpb24ocG9pbnQpO1xyXG4gICAgICAgICAgICAgICAgbXlHZW8uZ2V0TG9jYXRpb24ocG9pbnQsIGZ1bmN0aW9uIChycykge1xyXG4gICAgICAgICAgICAgICAgICAgIHNlbGYuc2hvd0xvY2F0aW9uSW5mbyhwb2ludCwgcnMpO1xyXG4gICAgICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICB0b2FzdHIud2FybmluZyhcIuWcsOWdgOS/oeaBr+S4jeato+ehru+8jOWumuS9jeWksei0pVwiKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0sICcnKTtcclxuICAgIH07XHJcblxyXG4gICAgZnVuY3Rpb24gVGVuY2VudE1hcCgpIHtcclxuICAgICAgICBCYXNlTWFwLmNhbGwodGhpcywgXCJ0ZW5jZW50XCIpO1xyXG4gICAgfVxyXG5cclxuICAgIFRlbmNlbnRNYXAucHJvdG90eXBlID0gbmV3IEJhc2VNYXAoKTtcclxuICAgIFRlbmNlbnRNYXAucHJvdG90eXBlLmNvbnN0cnVjdG9yID0gVGVuY2VudE1hcDtcclxuICAgIFRlbmNlbnRNYXAucHJvdG90eXBlLmlzQVBJUmVhZHkgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgcmV0dXJuIHdpbmRvd1sncXEnXSAmJiB3aW5kb3dbJ3FxJ11bJ21hcHMnXTtcclxuICAgIH07XHJcblxyXG4gICAgVGVuY2VudE1hcC5wcm90b3R5cGUuc2V0TWFwID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHZhciBzZWxmID0gdGhpcztcclxuICAgICAgICBpZiAodGhpcy5pc3Nob3cgfHwgdGhpcy5pc2hpZGUpIHJldHVybjtcclxuICAgICAgICBpZiAoIXRoaXMubG9hZEFQSSgpKSByZXR1cm47XHJcblxyXG5cclxuICAgICAgICAvL+WIneWni+WMluWcsOWbvlxyXG4gICAgICAgIHZhciBtYXAgPSBzZWxmLm1hcCA9IG5ldyBxcS5tYXBzLk1hcCh0aGlzLm1hcGJveFswXSwge3pvb206IDE1fSk7XHJcbiAgICAgICAgLy/liJ3lp4vljJblnLDlm77mjqfku7ZcclxuICAgICAgICBuZXcgcXEubWFwcy5TY2FsZUNvbnRyb2woe1xyXG4gICAgICAgICAgICBhbGlnbjogcXEubWFwcy5BTElHTi5CT1RUT01fTEVGVCxcclxuICAgICAgICAgICAgbWFyZ2luOiBxcS5tYXBzLlNpemUoODUsIDE1KSxcclxuICAgICAgICAgICAgbWFwOiBtYXBcclxuICAgICAgICB9KTtcclxuICAgICAgICAvL21hcC5hZGRDb250cm9sKG5ldyBCTWFwLk92ZXJ2aWV3TWFwQ29udHJvbCgpKTtcclxuICAgICAgICAvL21hcC5lbmFibGVTY3JvbGxXaGVlbFpvb20oKTtcclxuXHJcbiAgICAgICAgLy/ojrflj5bnu4/nuqzluqblnZDmoIflgLxcclxuICAgICAgICB2YXIgcG9pbnQgPSBuZXcgcXEubWFwcy5MYXRMbmcodGhpcy5sb2NhdGUubGF0LCB0aGlzLmxvY2F0ZS5sbmcpO1xyXG4gICAgICAgIG1hcC5wYW5Ubyhwb2ludCk7IC8v5Yid5aeL5YyW5Zyw5Zu+5Lit5b+D54K5XHJcblxyXG4gICAgICAgIC8v5Yid5aeL5YyW5Zyw5Zu+5qCH6K6wXHJcbiAgICAgICAgdGhpcy5tYXJrZXIgPSBuZXcgcXEubWFwcy5NYXJrZXIoe1xyXG4gICAgICAgICAgICBwb3NpdGlvbjogcG9pbnQsXHJcbiAgICAgICAgICAgIGRyYWdnYWJsZTogdHJ1ZSxcclxuICAgICAgICAgICAgbWFwOiBtYXBcclxuICAgICAgICB9KTtcclxuICAgICAgICB0aGlzLm1hcmtlci5zZXRBbmltYXRpb24ocXEubWFwcy5NYXJrZXJBbmltYXRpb24uRE9XTik7XHJcblxyXG4gICAgICAgIC8v5Zyw5Z2A6Kej5p6Q57G7XHJcbiAgICAgICAgdmFyIGdjID0gbmV3IHFxLm1hcHMuR2VvY29kZXIoe1xyXG4gICAgICAgICAgICBjb21wbGV0ZTogZnVuY3Rpb24gKHJzKSB7XHJcbiAgICAgICAgICAgICAgICBzZWxmLnNob3dMb2NhdGlvbkluZm8ocG9pbnQsIHJzKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICBxcS5tYXBzLmV2ZW50LmFkZExpc3RlbmVyKHRoaXMubWFya2VyLCAnY2xpY2snLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIHBvaW50ID0gc2VsZi5tYXJrZXIuZ2V0UG9zaXRpb24oKTtcclxuICAgICAgICAgICAgZ2MuZ2V0QWRkcmVzcyhwb2ludCk7XHJcbiAgICAgICAgfSk7XHJcbiAgICAgICAgLy/orr7nva5NYXJrZXLlgZzmraLmi5bliqjkuovku7ZcclxuICAgICAgICBxcS5tYXBzLmV2ZW50LmFkZExpc3RlbmVyKHRoaXMubWFya2VyLCAnZHJhZ2VuZCcsIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgcG9pbnQgPSBzZWxmLm1hcmtlci5nZXRQb3NpdGlvbigpO1xyXG4gICAgICAgICAgICBnYy5nZXRBZGRyZXNzKHBvaW50KTtcclxuICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgZ2MuZ2V0QWRkcmVzcyhwb2ludCk7XHJcblxyXG4gICAgICAgIHRoaXMuYmluZEV2ZW50cygpO1xyXG5cclxuICAgICAgICB0aGlzLmluZm9XaW5kb3cgPSBuZXcgcXEubWFwcy5JbmZvV2luZG93KHttYXA6IG1hcH0pO1xyXG5cclxuICAgICAgICB0aGlzLmlzc2hvdyA9IHRydWU7XHJcbiAgICAgICAgaWYgKHRoaXMudG9zaG93KSB7XHJcbiAgICAgICAgICAgIHRoaXMuc2hvd0luZm8oKTtcclxuICAgICAgICAgICAgdGhpcy50b3Nob3cgPSBmYWxzZTtcclxuICAgICAgICB9XHJcbiAgICB9O1xyXG5cclxuICAgIFRlbmNlbnRNYXAucHJvdG90eXBlLnNob3dJbmZvID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIGlmICh0aGlzLmlzaGlkZSkgcmV0dXJuO1xyXG4gICAgICAgIGlmICghdGhpcy5pc3Nob3cpIHtcclxuICAgICAgICAgICAgdGhpcy50b3Nob3cgPSB0cnVlO1xyXG4gICAgICAgICAgICByZXR1cm47XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHRoaXMuaW5mb1dpbmRvdy5vcGVuKCk7XHJcbiAgICAgICAgdGhpcy5zZXRJbmZvQ29udGVudCgpO1xyXG4gICAgICAgIHRoaXMuaW5mb1dpbmRvdy5zZXRQb3NpdGlvbih0aGlzLm1hcmtlci5nZXRQb3NpdGlvbigpKTtcclxuICAgIH07XHJcblxyXG4gICAgVGVuY2VudE1hcC5wcm90b3R5cGUuZ2V0QWRkcmVzcyA9IGZ1bmN0aW9uIChycykge1xyXG4gICAgICAgIGlmKHJzICYmIHJzLmRldGFpbCkge1xyXG4gICAgICAgICAgICByZXR1cm4gcnMuZGV0YWlsLmFkZHJlc3M7XHJcbiAgICAgICAgfVxyXG4gICAgfTtcclxuXHJcbiAgICBUZW5jZW50TWFwLnByb3RvdHlwZS5zZXRMb2NhdGUgPSBmdW5jdGlvbiAoYWRkcmVzcykge1xyXG4gICAgICAgIC8vIOWIm+W7uuWcsOWdgOino+aekOWZqOWunuS+i1xyXG4gICAgICAgIHZhciBzZWxmID0gdGhpcztcclxuICAgICAgICB2YXIgbXlHZW8gPSBuZXcgcXEubWFwcy5HZW9jb2Rlcih7XHJcbiAgICAgICAgICAgIGNvbXBsZXRlOiBmdW5jdGlvbiAocmVzdWx0KSB7XHJcbiAgICAgICAgICAgICAgICBpZihyZXN1bHQgJiYgcmVzdWx0LmRldGFpbCAmJiByZXN1bHQuZGV0YWlsLmxvY2F0aW9uKXtcclxuICAgICAgICAgICAgICAgICAgICB2YXIgcG9pbnQ9cmVzdWx0LmRldGFpbC5sb2NhdGlvbjtcclxuICAgICAgICAgICAgICAgICAgICBzZWxmLm1hcC5zZXRDZW50ZXIocG9pbnQpO1xyXG4gICAgICAgICAgICAgICAgICAgIHNlbGYubWFya2VyLnNldFBvc2l0aW9uKHBvaW50KTtcclxuICAgICAgICAgICAgICAgICAgICBzZWxmLnNob3dMb2NhdGlvbkluZm8ocG9pbnQsIHJlc3VsdCk7XHJcbiAgICAgICAgICAgICAgICB9ZWxzZXtcclxuICAgICAgICAgICAgICAgICAgICB0b2FzdHIud2FybmluZyhcIuWcsOWdgOS/oeaBr+S4jeato+ehru+8jOWumuS9jeWksei0pVwiKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgZXJyb3I6ZnVuY3Rpb24ocmVzdWx0KXtcclxuICAgICAgICAgICAgICAgIHRvYXN0ci53YXJuaW5nKFwi5Zyw5Z2A5L+h5oGv5LiN5q2j56Gu77yM5a6a5L2N5aSx6LSlXCIpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSk7XHJcbiAgICAgICAgbXlHZW8uZ2V0TG9jYXRpb24oYWRkcmVzcyk7XHJcbiAgICB9O1xyXG5cclxuXHJcbiAgICBmdW5jdGlvbiBHYW9kZU1hcCgpIHtcclxuICAgICAgICBCYXNlTWFwLmNhbGwodGhpcywgXCJnYW9kZVwiKTtcclxuICAgICAgICB0aGlzLmluZm9PcHRzID0ge1xyXG4gICAgICAgICAgICB3aWR0aDogMjUwLCAgICAgLy/kv6Hmga/nqpflj6Plrr3luqZcclxuICAgICAgICAgICAgLy8gICBoZWlnaHQ6IDEwMCwgICAgIC8v5L+h5oGv56qX5Y+j6auY5bqmXHJcbiAgICAgICAgICAgIHRpdGxlOiBcIlwiICAvL+S/oeaBr+eql+WPo+agh+mimFxyXG4gICAgICAgIH07XHJcbiAgICB9XHJcblxyXG4gICAgR2FvZGVNYXAucHJvdG90eXBlID0gbmV3IEJhc2VNYXAoKTtcclxuICAgIEdhb2RlTWFwLnByb3RvdHlwZS5jb25zdHJ1Y3RvciA9IEdhb2RlTWFwO1xyXG4gICAgR2FvZGVNYXAucHJvdG90eXBlLmlzQVBJUmVhZHkgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgcmV0dXJuICEhd2luZG93WydBTWFwJ11cclxuICAgIH07XHJcblxyXG4gICAgR2FvZGVNYXAucHJvdG90eXBlLnNldE1hcCA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICB2YXIgc2VsZiA9IHRoaXM7XHJcbiAgICAgICAgaWYgKHRoaXMuaXNzaG93IHx8IHRoaXMuaXNoaWRlKSByZXR1cm47XHJcbiAgICAgICAgaWYgKCF0aGlzLmxvYWRBUEkoKSkgcmV0dXJuO1xyXG5cclxuXHJcbiAgICAgICAgdmFyIG1hcCA9IHNlbGYubWFwID0gbmV3IEFNYXAuTWFwKHRoaXMubWFwYm94LmF0dHIoJ2lkJyksIHtcclxuICAgICAgICAgICAgcmVzaXplRW5hYmxlOiB0cnVlLFxyXG4gICAgICAgICAgICBkcmFnRW5hYmxlOiB0cnVlLFxyXG4gICAgICAgICAgICB6b29tOiAxM1xyXG4gICAgICAgIH0pO1xyXG4gICAgICAgIG1hcC5wbHVnaW4oW1wiQU1hcC5Ub29sQmFyXCIsIFwiQU1hcC5TY2FsZVwiLCBcIkFNYXAuT3ZlclZpZXdcIl0sIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgbWFwLmFkZENvbnRyb2wobmV3IEFNYXAuVG9vbEJhcigpKTtcclxuICAgICAgICAgICAgbWFwLmFkZENvbnRyb2wobmV3IEFNYXAuU2NhbGUoKSk7XHJcbiAgICAgICAgICAgIG1hcC5hZGRDb250cm9sKG5ldyBBTWFwLk92ZXJWaWV3KCkpO1xyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICAkKCdbbmFtZT10eHRMYW5nXScpLnVuYmluZCgpLm9uKCdjaGFuZ2UnLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIHZhciBsYW5nID0gJCh0aGlzKS52YWwoKTtcclxuICAgICAgICAgICAgaWYgKGxhbmcpIG1hcC5zZXRMYW5nKGxhbmcpO1xyXG4gICAgICAgIH0pLnRyaWdnZXIoJ2NoYW5nZScpO1xyXG5cclxuXHJcbiAgICAgICAgLy/ojrflj5bnu4/nuqzluqblnZDmoIflgLxcclxuICAgICAgICB2YXIgcG9pbnQgPSBuZXcgQU1hcC5MbmdMYXQodGhpcy5sb2NhdGUubG5nLCB0aGlzLmxvY2F0ZS5sYXQpO1xyXG4gICAgICAgIG1hcC5zZXRDZW50ZXIocG9pbnQpO1xyXG5cclxuICAgICAgICB0aGlzLm1hcmtlciA9IG5ldyBBTWFwLk1hcmtlcih7cG9zaXRpb246IHBvaW50LCBtYXA6IG1hcH0pOyAvL+WIneWni+WMluWcsOWbvuagh+iusFxyXG4gICAgICAgIHRoaXMubWFya2VyLnNldERyYWdnYWJsZSh0cnVlKTsgLy/moIforrDlvIDlkK/mi5bmi71cclxuXHJcblxyXG4gICAgICAgIHRoaXMuaW5mb1dpbmRvdyA9IG5ldyBBTWFwLkluZm9XaW5kb3coKTtcclxuICAgICAgICB0aGlzLmluZm9XaW5kb3cub3BlbihtYXAsIHBvaW50KTtcclxuXHJcbiAgICAgICAgbWFwLnBsdWdpbihbXCJBTWFwLkdlb2NvZGVyXCJdLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIHZhciBnYyA9IG5ldyBBTWFwLkdlb2NvZGVyKCk7IC8v5Zyw5Z2A6Kej5p6Q57G7XHJcbiAgICAgICAgICAgIC8v5re75Yqg5qCH6K6w5ouW5ou955uR5ZCsXHJcbiAgICAgICAgICAgIHNlbGYubWFya2VyLm9uKFwiZHJhZ2VuZFwiLCBmdW5jdGlvbiAoZSkge1xyXG4gICAgICAgICAgICAgICAgLy/ojrflj5blnLDlnYDkv6Hmga9cclxuICAgICAgICAgICAgICAgIGdjLmdldEFkZHJlc3MoZS5sbmdsYXQsIGZ1bmN0aW9uIChzdCwgcnMpIHtcclxuICAgICAgICAgICAgICAgICAgICBzZWxmLnNob3dMb2NhdGlvbkluZm8oZS5sbmdsYXQsIHJzKTtcclxuICAgICAgICAgICAgICAgIH0pO1xyXG4gICAgICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgICAgIC8v5re75Yqg5qCH6K6w54K55Ye755uR5ZCsXHJcbiAgICAgICAgICAgIHNlbGYubWFya2VyLm9uKFwiY2xpY2tcIiwgZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICAgICAgICAgIGdjLmdldEFkZHJlc3MoZS5sbmdsYXQsIGZ1bmN0aW9uIChzdCwgcnMpIHtcclxuICAgICAgICAgICAgICAgICAgICBzZWxmLnNob3dMb2NhdGlvbkluZm8oZS5sbmdsYXQsIHJzKTtcclxuICAgICAgICAgICAgICAgIH0pO1xyXG4gICAgICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgICAgIGdjLmdldEFkZHJlc3MocG9pbnQsIGZ1bmN0aW9uIChzdCwgcnMpIHtcclxuICAgICAgICAgICAgICAgIHNlbGYuc2hvd0xvY2F0aW9uSW5mbyhwb2ludCwgcnMpO1xyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgdGhpcy5iaW5kRXZlbnRzKCk7XHJcblxyXG4gICAgICAgIHRoaXMuaXNzaG93ID0gdHJ1ZTtcclxuICAgICAgICBpZiAodGhpcy50b3Nob3cpIHtcclxuICAgICAgICAgICAgdGhpcy5zaG93SW5mbygpO1xyXG4gICAgICAgICAgICB0aGlzLnRvc2hvdyA9IGZhbHNlO1xyXG4gICAgICAgIH1cclxuICAgIH07XHJcblxyXG4gICAgR2FvZGVNYXAucHJvdG90eXBlLnNob3dJbmZvID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIGlmICh0aGlzLmlzaGlkZSkgcmV0dXJuO1xyXG4gICAgICAgIGlmICghdGhpcy5pc3Nob3cpIHtcclxuICAgICAgICAgICAgdGhpcy50b3Nob3cgPSB0cnVlO1xyXG4gICAgICAgICAgICByZXR1cm47XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHRoaXMuc2V0SW5mb0NvbnRlbnQoKTtcclxuICAgICAgICB0aGlzLmluZm9XaW5kb3cuc2V0UG9zaXRpb24odGhpcy5tYXJrZXIuZ2V0UG9zaXRpb24oKSk7XHJcbiAgICB9O1xyXG5cclxuICAgIEdhb2RlTWFwLnByb3RvdHlwZS5nZXRBZGRyZXNzID0gZnVuY3Rpb24gKHJzKSB7XHJcbiAgICAgICAgcmV0dXJuIHJzLnJlZ2VvY29kZS5mb3JtYXR0ZWRBZGRyZXNzO1xyXG4gICAgfTtcclxuXHJcbiAgICBHYW9kZU1hcC5wcm90b3R5cGUuc2V0TG9jYXRlID0gZnVuY3Rpb24gKGFkZHJlc3MpIHtcclxuICAgICAgICAvLyDliJvlu7rlnLDlnYDop6PmnpDlmajlrp7kvotcclxuICAgICAgICB2YXIgbXlHZW8gPSBuZXcgQU1hcC5HZW9jb2RlcigpO1xyXG4gICAgICAgIHZhciBzZWxmID0gdGhpcztcclxuICAgICAgICBteUdlby5nZXRQb2ludChhZGRyZXNzLCBmdW5jdGlvbiAocG9pbnQpIHtcclxuICAgICAgICAgICAgaWYgKHBvaW50KSB7XHJcbiAgICAgICAgICAgICAgICBzZWxmLm1hcC5jZW50ZXJBbmRab29tKHBvaW50LCAxMSk7XHJcbiAgICAgICAgICAgICAgICBzZWxmLm1hcmtlci5zZXRQb3NpdGlvbihwb2ludCk7XHJcbiAgICAgICAgICAgICAgICBteUdlby5nZXRMb2NhdGlvbihwb2ludCwgZnVuY3Rpb24gKHJzKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKHBvaW50LCBycyk7XHJcbiAgICAgICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgIHRvYXN0ci53YXJuaW5nKFwi5Zyw5Z2A5L+h5oGv5LiN5q2j56Gu77yM5a6a5L2N5aSx6LSlXCIpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSwgJycpO1xyXG4gICAgfTtcclxuXHJcbiAgICB3aW5kb3cuSW5pdE1hcD1Jbml0TWFwO1xyXG59KSh3aW5kb3csalF1ZXJ5KTsiLCJ3aW5kb3cuc3RvcF9hamF4PWZhbHNlO1xyXG5qUXVlcnkoZnVuY3Rpb24gKCQpIHtcclxuICAgIC8v6auY5Lqu5b2T5YmN6YCJ5Lit55qE5a+86IiqXHJcbiAgICB2YXIgYnJlYWQgPSAkKFwiLmJyZWFkY3J1bWJcIik7XHJcbiAgICB2YXIgbWVudSA9IGJyZWFkLmRhdGEoJ21lbnUnKTtcclxuICAgIGlmIChtZW51KSB7XHJcbiAgICAgICAgdmFyIGxpbmsgPSAkKCcuc2lkZS1uYXYgYVtkYXRhLWtleT0nICsgbWVudSArICddJyk7XHJcblxyXG4gICAgICAgIHZhciBodG1sID0gW107XHJcbiAgICAgICAgaWYgKGxpbmsubGVuZ3RoID4gMCkge1xyXG4gICAgICAgICAgICBpZiAobGluay5pcygnLm1lbnVfdG9wJykpIHtcclxuICAgICAgICAgICAgICAgIGh0bWwucHVzaCgnPGxpIGNsYXNzPVwiYnJlYWRjcnVtYi1pdGVtXCI+PGEgaHJlZj1cImphdmFzY3JpcHQ6XCI+PGkgY2xhc3M9XCInICsgbGluay5maW5kKCdpJykuYXR0cignY2xhc3MnKSArICdcIj48L2k+Jm5ic3A7JyArIGxpbmsudGV4dCgpICsgJzwvYT48L2xpPicpO1xyXG4gICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgdmFyIHBhcmVudCA9IGxpbmsucGFyZW50cygnLmNvbGxhcHNlJykuZXEoMCk7XHJcbiAgICAgICAgICAgICAgICBwYXJlbnQuYWRkQ2xhc3MoJ3Nob3cnKTtcclxuICAgICAgICAgICAgICAgIGxpbmsuYWRkQ2xhc3MoXCJhY3RpdmVcIik7XHJcbiAgICAgICAgICAgICAgICB2YXIgdG9wbWVudSA9IHBhcmVudC5zaWJsaW5ncygnLmNhcmQtaGVhZGVyJykuZmluZCgnYS5tZW51X3RvcCcpO1xyXG4gICAgICAgICAgICAgICAgaHRtbC5wdXNoKCc8bGkgY2xhc3M9XCJicmVhZGNydW1iLWl0ZW1cIj48YSBocmVmPVwiamF2YXNjcmlwdDpcIj48aSBjbGFzcz1cIicgKyB0b3BtZW51LmZpbmQoJ2knKS5hdHRyKCdjbGFzcycpICsgJ1wiPjwvaT4mbmJzcDsnICsgdG9wbWVudS50ZXh0KCkgKyAnPC9hPjwvbGk+Jyk7XHJcbiAgICAgICAgICAgICAgICBodG1sLnB1c2goJzxsaSBjbGFzcz1cImJyZWFkY3J1bWItaXRlbVwiPjxhIGhyZWY9XCJqYXZhc2NyaXB0OlwiPicgKyBsaW5rLnRleHQoKSArICc8L2E+PC9saT4nKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH1cclxuICAgICAgICB2YXIgdGl0bGUgPSBicmVhZC5kYXRhKCd0aXRsZScpO1xyXG4gICAgICAgIGlmICh0aXRsZSkge1xyXG4gICAgICAgICAgICBodG1sLnB1c2goJzxsaSBjbGFzcz1cImJyZWFkY3J1bWItaXRlbSBhY3RpdmVcIiBhcmlhLWN1cnJlbnQ9XCJwYWdlXCI+JyArIHRpdGxlICsgJzwvbGk+Jyk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIGJyZWFkLmh0bWwoaHRtbC5qb2luKFwiXFxuXCIpKTtcclxuICAgIH1cclxuXHJcbiAgICAvL+WFqOmAieOAgeWPjemAieaMiemSrlxyXG4gICAgJCgnLmNoZWNrYWxsLWJ0bicpLmNsaWNrKGZ1bmN0aW9uIChlKSB7XHJcbiAgICAgICAgdmFyIHRhcmdldCA9ICQodGhpcykuZGF0YSgndGFyZ2V0Jyk7XHJcbiAgICAgICAgaWYgKCF0YXJnZXQpIHRhcmdldCA9ICdpZCc7XHJcbiAgICAgICAgdmFyIGlkcyA9ICQoJ1tuYW1lPScgKyB0YXJnZXQgKyAnXScpO1xyXG4gICAgICAgIGlmICgkKHRoaXMpLmlzKCcuYWN0aXZlJykpIHtcclxuICAgICAgICAgICAgaWRzLnByb3AoJ2NoZWNrZWQnLCBmYWxzZSk7XHJcbiAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgaWRzLnByb3AoJ2NoZWNrZWQnLCB0cnVlKTtcclxuICAgICAgICB9XHJcbiAgICB9KTtcclxuICAgICQoJy5jaGVja3JldmVyc2UtYnRuJykuY2xpY2soZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICB2YXIgdGFyZ2V0ID0gJCh0aGlzKS5kYXRhKCd0YXJnZXQnKTtcclxuICAgICAgICBpZiAoIXRhcmdldCkgdGFyZ2V0ID0gJ2lkJztcclxuICAgICAgICB2YXIgaWRzID0gJCgnW25hbWU9JyArIHRhcmdldCArICddJyk7XHJcbiAgICAgICAgZm9yICh2YXIgaSA9IDA7IGkgPCBpZHMubGVuZ3RoOyBpKyspIHtcclxuICAgICAgICAgICAgaWYgKGlkc1tpXS5jaGVja2VkKSB7XHJcbiAgICAgICAgICAgICAgICBpZHMuZXEoaSkucHJvcCgnY2hlY2tlZCcsIGZhbHNlKTtcclxuICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgIGlkcy5lcShpKS5wcm9wKCdjaGVja2VkJywgdHJ1ZSk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9XHJcbiAgICB9KTtcclxuICAgIC8v5pON5L2c5oyJ6ZKuXHJcbiAgICAkKCcuYWN0aW9uLWJ0bicpLmNsaWNrKGZ1bmN0aW9uIChlKSB7XHJcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xyXG4gICAgICAgIHZhciBhY3Rpb24gPSAkKHRoaXMpLmRhdGEoJ2FjdGlvbicpO1xyXG4gICAgICAgIGlmICghYWN0aW9uKSB7XHJcbiAgICAgICAgICAgIHJldHVybiB0b2FzdHIuZXJyb3IoJ+acquefpeaTjeS9nCcpO1xyXG4gICAgICAgIH1cclxuICAgICAgICBhY3Rpb24gPSAnYWN0aW9uJyArIGFjdGlvbi5yZXBsYWNlKC9eW2Etel0vLCBmdW5jdGlvbiAobGV0dGVyKSB7XHJcbiAgICAgICAgICAgIHJldHVybiBsZXR0ZXIudG9VcHBlckNhc2UoKTtcclxuICAgICAgICB9KTtcclxuICAgICAgICBpZiAoIXdpbmRvd1thY3Rpb25dIHx8IHR5cGVvZiB3aW5kb3dbYWN0aW9uXSAhPT0gJ2Z1bmN0aW9uJykge1xyXG4gICAgICAgICAgICByZXR1cm4gdG9hc3RyLmVycm9yKCfmnKrnn6Xmk43kvZwnKTtcclxuICAgICAgICB9XHJcbiAgICAgICAgdmFyIG5lZWRDaGVja3MgPSAkKHRoaXMpLmRhdGEoJ25lZWRDaGVja3MnKTtcclxuICAgICAgICBpZiAobmVlZENoZWNrcyA9PT0gdW5kZWZpbmVkKSBuZWVkQ2hlY2tzID0gdHJ1ZTtcclxuICAgICAgICBpZiAobmVlZENoZWNrcykge1xyXG4gICAgICAgICAgICB2YXIgdGFyZ2V0ID0gJCh0aGlzKS5kYXRhKCd0YXJnZXQnKTtcclxuICAgICAgICAgICAgaWYgKCF0YXJnZXQpIHRhcmdldCA9ICdpZCc7XHJcbiAgICAgICAgICAgIHZhciBpZHMgPSAkKCdbbmFtZT0nICsgdGFyZ2V0ICsgJ106Y2hlY2tlZCcpO1xyXG4gICAgICAgICAgICBpZiAoaWRzLmxlbmd0aCA8IDEpIHtcclxuICAgICAgICAgICAgICAgIHJldHVybiB0b2FzdHIud2FybmluZygn6K+36YCJ5oup6ZyA6KaB5pON5L2c55qE6aG555uuJyk7XHJcbiAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICB2YXIgaWRjaGVja3MgPSBbXTtcclxuICAgICAgICAgICAgICAgIGZvciAodmFyIGkgPSAwOyBpIDwgaWRzLmxlbmd0aDsgaSsrKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgaWRjaGVja3MucHVzaChpZHMuZXEoaSkudmFsKCkpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgd2luZG93W2FjdGlvbl0oaWRjaGVja3MpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgd2luZG93W2FjdGlvbl0oKTtcclxuICAgICAgICB9XHJcbiAgICB9KTtcclxuXHJcbiAgICAvL+W8guatpeaYvuekuui1hOaWmemTvuaOpVxyXG4gICAgJCgnYVtyZWw9YWpheF0nKS5jbGljayhmdW5jdGlvbiAoZSkge1xyXG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcclxuICAgICAgICB2YXIgc2VsZiA9ICQodGhpcyk7XHJcbiAgICAgICAgdmFyIHRpdGxlID0gJCh0aGlzKS5kYXRhKCd0aXRsZScpO1xyXG4gICAgICAgIGlmICghdGl0bGUpIHRpdGxlID0gJCh0aGlzKS50ZXh0KCk7XHJcbiAgICAgICAgdmFyIGRsZyA9IG5ldyBEaWFsb2coe1xyXG4gICAgICAgICAgICBidG5zOiBbJ+ehruWumiddLFxyXG4gICAgICAgICAgICBvbnNob3c6IGZ1bmN0aW9uIChib2R5KSB7XHJcbiAgICAgICAgICAgICAgICAkLmFqYXgoe1xyXG4gICAgICAgICAgICAgICAgICAgIHVybDogc2VsZi5hdHRyKCdocmVmJyksXHJcbiAgICAgICAgICAgICAgICAgICAgc3VjY2VzczogZnVuY3Rpb24gKHRleHQpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgYm9keS5odG1sKHRleHQpO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIH0pO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkuc2hvdygnPHAgY2xhc3M9XCJsb2FkaW5nXCI+JytsYW5nKCdsb2FkaW5nLi4uJykrJzwvcD4nLCB0aXRsZSk7XHJcblxyXG4gICAgfSk7XHJcblxyXG4gICAgLy/noa7orqTmk43kvZxcclxuICAgICQoJy5saW5rLWNvbmZpcm0nKS5jbGljayhmdW5jdGlvbiAoZSkge1xyXG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcclxuICAgICAgICBlLnN0b3BQcm9wYWdhdGlvbigpO1xyXG4gICAgICAgIHZhciB0ZXh0PSQodGhpcykuZGF0YSgnY29uZmlybScpO1xyXG4gICAgICAgIHZhciB1cmw9JCh0aGlzKS5kYXRhKCdocmVmJyk7XHJcbiAgICAgICAgaWYoIXRleHQpdGV4dD1sYW5nKCdDb25maXJtIG9wZXJhdGlvbj8nKTtcclxuXHJcbiAgICAgICAgZGlhbG9nLmNvbmZpcm0odGV4dCxmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgICQuYWpheCh7XHJcbiAgICAgICAgICAgICAgICB1cmw6dXJsLFxyXG4gICAgICAgICAgICAgICAgZGF0YVR5cGU6J0pTT04nLFxyXG4gICAgICAgICAgICAgICAgc3VjY2VzczpmdW5jdGlvbiAoanNvbikge1xyXG4gICAgICAgICAgICAgICAgICAgIGRpYWxvZy5hbGVydChqc29uLm1zZyk7XHJcbiAgICAgICAgICAgICAgICAgICAgaWYoanNvbi5jb2RlPT0xKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgaWYoanNvbi51cmwpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbG9jYXRpb24uaHJlZj1qc29uLnVybDtcclxuICAgICAgICAgICAgICAgICAgICAgICAgfWVsc2V7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBsb2NhdGlvbi5yZWxvYWQoKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICAgICBlcnJvcjpmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgZGlhbG9nLmFsZXJ0KGxhbmcoJ1NlcnZlciBlcnJvci4nKSk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH0pXHJcbiAgICAgICAgfSk7XHJcbiAgICB9KTtcclxuXHJcbiAgICAkKCcuaW1nLXZpZXcnKS5jbGljayhmdW5jdGlvbiAoZSkge1xyXG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcclxuICAgICAgICBlLnN0b3BQcm9wYWdhdGlvbigpO1xyXG4gICAgICAgIHZhciB1cmw9JCh0aGlzKS5hdHRyKCdocmVmJyk7XHJcbiAgICAgICAgaWYoIXVybCl1cmw9JCh0aGlzKS5kYXRhKCdpbWcnKTtcclxuICAgICAgICBkaWFsb2cuYWxlcnQoJzxhIGhyZWY9XCInK3VybCsnXCIgY2xhc3M9XCJkLWJsb2NrIHRleHQtY2VudGVyXCIgdGFyZ2V0PVwiX2JsYW5rXCI+PGltZyBjbGFzcz1cImltZy1mbHVpZFwiIHNyYz1cIicrdXJsKydcIiAvPjwvYT48ZGl2IGNsYXNzPVwidGV4dC1tdXRlZCB0ZXh0LWNlbnRlclwiPueCueWHu+WbvueJh+WcqOaWsOmhtemdouaUvuWkp+afpeecizwvZGl2PicsbnVsbCwn5p+l55yL5Zu+54mHJyk7XHJcbiAgICB9KTtcclxuXHJcbiAgICAkKCcubmF2LXRhYnMgYScpLmNsaWNrKGZ1bmN0aW9uIChlKSB7XHJcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xyXG4gICAgICAgICQodGhpcykudGFiKCdzaG93Jyk7XHJcbiAgICB9KTtcclxuXHJcbiAgICAvL+S4iuS8oOahhlxyXG4gICAgJCgnLmN1c3RvbS1maWxlIC5jdXN0b20tZmlsZS1pbnB1dCcpLm9uKCdjaGFuZ2UnLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgdmFyIGxhYmVsID0gJCh0aGlzKS5wYXJlbnRzKCcuY3VzdG9tLWZpbGUnKS5maW5kKCcuY3VzdG9tLWZpbGUtbGFiZWwnKTtcclxuICAgICAgICBsYWJlbC50ZXh0KCQodGhpcykudmFsKCkpO1xyXG4gICAgfSk7XHJcblxyXG4gICAgLy/ooajljZVBamF45o+Q5LqkXHJcbiAgICAkKCcuYnRuLXByaW1hcnlbdHlwZT1zdWJtaXRdJykuY2xpY2soZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICB2YXIgZm9ybSA9ICQodGhpcykucGFyZW50cygnZm9ybScpO1xyXG4gICAgICAgIGlmKGZvcm0uaXMoJy5ub2FqYXgnKSlyZXR1cm4gdHJ1ZTtcclxuICAgICAgICB2YXIgYnRuID0gdGhpcztcclxuXHJcbiAgICAgICAgdmFyIGlzYnRuPSQoYnRuKS5wcm9wKCd0YWdOYW1lJykudG9VcHBlckNhc2UoKT09J0JVVFRPTic7XHJcbiAgICAgICAgdmFyIG9yaWdUZXh0PWlzYnRuPyQoYnRuKS50ZXh0KCk6JChidG4pLnZhbCgpO1xyXG4gICAgICAgIHZhciBvcHRpb25zID0ge1xyXG4gICAgICAgICAgICB1cmw6ICQoZm9ybSkuYXR0cignYWN0aW9uJyksXHJcbiAgICAgICAgICAgIHR5cGU6ICdQT1NUJyxcclxuICAgICAgICAgICAgZGF0YVR5cGU6ICdKU09OJyxcclxuICAgICAgICAgICAgc3VjY2VzczogZnVuY3Rpb24gKGpzb24pIHtcclxuICAgICAgICAgICAgICAgIHdpbmRvdy5zdG9wX2FqYXg9ZmFsc2U7XHJcbiAgICAgICAgICAgICAgICBpc2J0bj8kKGJ0bikudGV4dChvcmlnVGV4dCk6JChidG4pLnZhbChvcmlnVGV4dCk7XHJcbiAgICAgICAgICAgICAgICBpZiAoanNvbi5jb2RlID09IDEpIHtcclxuICAgICAgICAgICAgICAgICAgICBkaWFsb2cuYWxlcnQoanNvbi5tc2csZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGpzb24udXJsKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBsb2NhdGlvbi5ocmVmID0ganNvbi51cmw7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBsb2NhdGlvbi5yZWxvYWQoKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgIH0pO1xyXG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgICAgICB0b2FzdHIud2FybmluZyhqc29uLm1zZyk7XHJcbiAgICAgICAgICAgICAgICAgICAgJChidG4pLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgIGVycm9yOiBmdW5jdGlvbiAoeGhyKSB7XHJcbiAgICAgICAgICAgICAgICB3aW5kb3cuc3RvcF9hamF4PWZhbHNlO1xyXG4gICAgICAgICAgICAgICAgaXNidG4/JChidG4pLnRleHQob3JpZ1RleHQpOiQoYnRuKS52YWwob3JpZ1RleHQpO1xyXG4gICAgICAgICAgICAgICAgJChidG4pLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XHJcbiAgICAgICAgICAgICAgICB0b2FzdHIuZXJyb3IoJ+acjeWKoeWZqOWkhOeQhumUmeivrycpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfTtcclxuICAgICAgICBpZiAoZm9ybS5hdHRyKCdlbmN0eXBlJykgPT09ICdtdWx0aXBhcnQvZm9ybS1kYXRhJykge1xyXG4gICAgICAgICAgICBpZiAoIUZvcm1EYXRhKSB7XHJcbiAgICAgICAgICAgICAgICB3aW5kb3cuc3RvcF9hamF4PXRydWU7XHJcbiAgICAgICAgICAgICAgICByZXR1cm4gdHJ1ZTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICBvcHRpb25zLmRhdGEgPSBuZXcgRm9ybURhdGEoZm9ybVswXSk7XHJcbiAgICAgICAgICAgIG9wdGlvbnMuY2FjaGUgPSBmYWxzZTtcclxuICAgICAgICAgICAgb3B0aW9ucy5wcm9jZXNzRGF0YSA9IGZhbHNlO1xyXG4gICAgICAgICAgICBvcHRpb25zLmNvbnRlbnRUeXBlID0gZmFsc2U7XHJcbiAgICAgICAgICAgIG9wdGlvbnMueGhyPSBmdW5jdGlvbigpIHsgLy/nlKjku6XmmL7npLrkuIrkvKDov5vluqZcclxuICAgICAgICAgICAgICAgIHZhciB4aHIgPSAkLmFqYXhTZXR0aW5ncy54aHIoKTtcclxuICAgICAgICAgICAgICAgIGlmICh4aHIudXBsb2FkKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgeGhyLnVwbG9hZC5hZGRFdmVudExpc3RlbmVyKCdwcm9ncmVzcycsIGZ1bmN0aW9uKGV2ZW50KSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciBwZXJjZW50ID0gTWF0aC5mbG9vcihldmVudC5sb2FkZWQgLyBldmVudC50b3RhbCAqIDEwMCk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICQoYnRuKS50ZXh0KG9yaWdUZXh0KycgICgnK3BlcmNlbnQrJyUpJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgfSwgZmFsc2UpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgcmV0dXJuIHhocjtcclxuICAgICAgICAgICAgfTtcclxuICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICBvcHRpb25zLmRhdGEgPSAkKGZvcm0pLnNlcmlhbGl6ZSgpO1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xyXG4gICAgICAgICQodGhpcykuYXR0cignZGlzYWJsZWQnLCB0cnVlKTtcclxuICAgICAgICB3aW5kb3cuc3RvcF9hamF4PXRydWU7XHJcbiAgICAgICAgJC5hamF4KG9wdGlvbnMpO1xyXG4gICAgfSk7XHJcblxyXG4gICAgJCgnLnBpY2t1c2VyJykuY2xpY2soZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICB2YXIgZ3JvdXAgPSAkKHRoaXMpLnBhcmVudHMoJy5pbnB1dC1ncm91cCcpO1xyXG4gICAgICAgIHZhciBpZGVsZSA9IGdyb3VwLmZpbmQoJ1tuYW1lPW1lbWJlcl9pZF0nKTtcclxuICAgICAgICB2YXIgaW5mb2VsZSA9IGdyb3VwLmZpbmQoJ1tuYW1lPW1lbWJlcl9pbmZvXScpO1xyXG4gICAgICAgIGRpYWxvZy5waWNrVXNlcigkKHRoaXMpLmRhdGEoJ3VybCcpLCBmdW5jdGlvbiAodXNlcikge1xyXG4gICAgICAgICAgICBpZGVsZS52YWwodXNlci5pZCk7XHJcbiAgICAgICAgICAgIGluZm9lbGUudmFsKCdbJyArIHVzZXIuaWQgKyAnXSAnICsgdXNlci51c2VybmFtZSArICh1c2VyLm1vYmlsZSA/ICgnIC8gJyArIHVzZXIubW9iaWxlKSA6ICcnKSk7XHJcbiAgICAgICAgfSwgJCh0aGlzKS5kYXRhKCdmaWx0ZXInKSk7XHJcbiAgICB9KTtcclxuICAgICQoJy5waWNrLWxvY2F0ZScpLmNsaWNrKGZ1bmN0aW9uKGUpe1xyXG4gICAgICAgIHZhciBncm91cD0kKHRoaXMpLnBhcmVudHMoJy5pbnB1dC1ncm91cCcpO1xyXG4gICAgICAgIHZhciBpZGVsZT1ncm91cC5maW5kKCdpbnB1dFt0eXBlPXRleHRdJyk7XHJcbiAgICAgICAgZGlhbG9nLnBpY2tMb2NhdGUoJ3FxJyxmdW5jdGlvbihsb2NhdGUpe1xyXG4gICAgICAgICAgICBpZGVsZS52YWwobG9jYXRlLmxuZysnLCcrbG9jYXRlLmxhdCk7XHJcbiAgICAgICAgfSxpZGVsZS52YWwoKSk7XHJcbiAgICB9KTtcclxufSk7Il19
