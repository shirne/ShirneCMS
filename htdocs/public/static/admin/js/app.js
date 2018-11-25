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
if(!String.prototype.trim){
    String.prototype.trim=function () {
        return this.replace(/(^\s+|\s+$)/g,'');
    }
}
String.prototype.compile=function(data,list){

    if(list){
        var temps=[];
        for(var i in data){
            temps.push(this.compile(data[i]));
        }
        return temps.join("\n");
    }else{
        return this.replace(/\{if\s+([^\}]+)\}([\W\w]*){\/if}/g,function(all, condition, cont){
            var operation;
            if(operation=condition.match(/\s+(=+|<|>)\s+/)){
                operation=operation[0];
                var part=condition.split(operation);
                if(part[0].indexOf('@')===0){
                    part[0]=data[part[0].replace('@','')];
                }
                if(part[1].indexOf('@')===0){
                    part[1]=data[part[1].replace('@','')];
                }
                operation=operation.trim();
                var result=false;
                switch (operation){
                    case '==':
                        result = part[0] == part[1];
                        break;
                    case '===':
                        result = part[0] === part[1];
                        break;
                    case '>':
                        result = part[0] > part[1];
                        break;
                    case '<':
                        result = part[0] < part[1];
                        break;
                }
                if(result){
                    return cont;
                }
            }else {
                if (data[condition.replace('@','')]) return cont;
            }
            return '';
        }).replace(/\{@([\w\d\.]+)(?:\|([\w\d]+)(?:\s*=\s*([\w\d,\s#]+))?)?\}/g,function(all,m1,func,args){

            if(m1.indexOf('.')>0){
                var keys=m1.split('.'),val=data;
                for(var i=0;i<keys.length;i++){
                    if(val[keys[i]]!==undefined && val[keys[i]]!==null){
                        val=val[keys[i]];
                    }else{
                        val = '';
                        break;
                    }
                }
                return callfunc(val,func,args);
            }else{
                return callfunc(data[m1],func,args,data);
            }
        });
    }
};

function tostring(obj) {
    if(obj && obj.toString){
        return obj.toString();
    }
    return '';
}

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

    return window[func]?window[func].apply(thisobj,args):((val===undefined||val===null)?'':val);
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
        //var dft=-1;
        for (var i = 0; i < opts.btns.length; i++) {
            if(typeof(opts.btns[i])=='string'){
                opts.btns[i]={'text':opts.btns[i]};
            }
            if(opts.btns[i].isdefault){
                opts.defaultBtn=i;
            }
        }
        if(opts.defaultBtn===undefined){
            opts.defaultBtn=opts.btns.length-1;
            opts.btns[opts.defaultBtn].isdefault=true;
        }

        if(opts.btns[opts.defaultBtn] && !opts.btns[opts.defaultBtn]['type']){
            opts.btns[opts.defaultBtn]['type']='primary';
        }
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
        $(document.body).append(dialogTpl.replace('modal-body','modal-body'+(this.options.bodyClass?(' '+this.options.bodyClass):'')).compile({'id': this.options.id}));
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
            'bodyClass':'modal-action',
            'btns':[
                {'text':'取消','type':'secondary'}
            ],
            'onshow':function(body){
                actions=body.find('.list-group-item-action');
                actions.click(function (e) {
                    actions.removeClass('active');
                    $(this).addClass('active');
                    var val=actions.index(this);
                    if(typeof callback=='function'){
                        if( callback(val)!==false){
                            dlg.close();
                        }
                    }else {
                        dlg.close();
                    }
                })
            },
            'onsure':function(body){
                return true;
            },
            'onhide':function () {
                return true;
            }
        }).show(html,title?title:'请选择');
    },
    pickList:function (config,callback,filter) {
        if(typeof config==='string')config={url:config};
        config=$.extend({
            'url':'',
            'name':'对象',
            'searchHolder':'根据名称搜索',
            'idkey':'id',
            'onRow':null,
            'extend':null,
            'rowTemplate':'<a href="javascript:" data-id="{@id}" class="list-group-item list-group-item-action">[{@id}]&nbsp;<i class="ion-md-person"></i> {@username}&nbsp;&nbsp;&nbsp;<small><i class="ion-md-phone-portrait"></i> {@mobile}</small></a>'
        },config||{});
        var current=null;
        var exthtml='';
        if(config.extend){
            exthtml='<select name="'+config.extend.name+'" class="form-control"><option value="">'+config.extend.title+'</option></select>';
        }
        if(!filter)filter={};
        var dlg=new Dialog({
            'onshown':function(body){
                var btn=body.find('.searchbtn');
                var input=body.find('.searchtext');
                var listbox=body.find('.list-group');
                var isloading=false;
                var extField=null;
                if(config.extend){
                    extField=body.find('[name='+config.extend.name+']');
                    $.ajax({
                       url:config.extend.url,
                        type:'GET',
                        dataType:'JSON',
                        success:function (json) {
                            extField.append(config.extend.htmlRow.compile(json.data,true));
                        }
                    });
                }
                btn.click(function(){
                    if(isloading)return;
                    isloading=true;
                    listbox.html('<span class="list-loading">加载中...</span>');
                    filter['key']=input.val();
                    if(config.extend){
                        filter[config.extend.name]=extField.val();
                    }
                    $.ajax(
                        {
                            url:config.url,
                            type:'GET',
                            dataType:'JSON',
                            data:filter,
                            success:function(json){
                                isloading=false;
                                if(json.status){
                                    if(json.data && json.data.length) {
                                        listbox.html(config.rowTemplate.compile(json.data, true));
                                        listbox.find('a.list-group-item').click(function () {
                                            var id = $(this).data('id');
                                            for (var i = 0; i < json.data.length; i++) {
                                                if(json.data[i][config.idkey]==id){
                                                    current=json.data[i];
                                                    listbox.find('a.list-group-item').removeClass('active');
                                                    $(this).addClass('active');
                                                    break;
                                                }
                                            }
                                        })
                                    }else{
                                        listbox.html('<span class="list-loading"><i class="ion-md-warning"></i> 没有检索到'+config.name+'</span>');
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
                if(!current){
                    toastr.warning('没有选择'+config.name+'!');
                    return false;
                }
                if(typeof callback=='function'){
                    var result = callback(current);
                    return result;
                }
            }
        }).show('<div class="input-group">'+exthtml+'<input type="text" class="form-control searchtext" name="keyword" placeholder="'+config.searchHolder+'"/><div class="input-group-append"><a class="btn btn-outline-secondary searchbtn"><i class="ion-md-search"></i></a></div></div><div class="list-group list-group-picker mt-2"></div>','请搜索并选择'+config.name);
    },
    pickUser:function(callback,filter){
        return this.pickList({
            'url':window.get_search_url('member'),
            'name':'会员',
            'searchHolder':'根据会员id或名称，电话来搜索'
        },callback,filter);
    },
    pickArticle:function(callback,filter){
        return this.pickList({
            'url':window.get_search_url('article'),
            rowTemplate:'<a href="javascript:" data-id="{@id}" class="list-group-item list-group-item-action">{if @cover}<div style="background-image:url({@cover})" class="imgview" ></div>{/if}<div class="text-block">[{@id}]&nbsp;{@title}&nbsp;<br />{@description}</div></a>',
            name:'文章',
            idkey:'id',
            extend:{
               name:'cate',
                title:'按分类搜索',
                url:get_cate_url('article'),
                htmlRow:'<option value="{@id}">{@html}{@title}</option>'
            },
            'searchHolder':'根据文章标题搜索'
        },callback,filter);
    },
    pickProduct:function(callback,filter){
        return this.pickList({
            'url':window.get_search_url('product'),
            rowTemplate:'<a href="javascript:" data-id="{@id}" class="list-group-item list-group-item-action">{if @image}<div style="background-image:url({@image})" class="imgview" ></div>{/if}<div class="text-block">[{@id}]&nbsp;{@title}&nbsp;<br />{@min_price}~{@max_price}</div></a>',
            name:'产品',
            idkey:'id',
            extend:{
                name:'cate',
                title:'按分类搜索',
                url:get_cate_url('product'),
                htmlRow:'<option value="{@id}">{@html}{@title}</option>'
            },
            'searchHolder':'根据产品名称搜索'
        },callback,filter);
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
        dialog.pickUser( function (user) {
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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNvbW1vbi5qcyIsInRlbXBsYXRlLmpzIiwiZGlhbG9nLmpzIiwianF1ZXJ5LnRhZy5qcyIsImRhdGV0aW1lLmluaXQuanMiLCJtYXAuanMiLCJiYWNrZW5kLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ2pFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQzVHQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQzFhQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQzFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDekVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDMWpCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBIiwiZmlsZSI6ImJhY2tlbmQuanMiLCJzb3VyY2VzQ29udGVudCI6WyJmdW5jdGlvbiBkZWwob2JqLG1zZykge1xyXG4gICAgZGlhbG9nLmNvbmZpcm0obXNnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgbG9jYXRpb24uaHJlZj0kKG9iaikuYXR0cignaHJlZicpO1xyXG4gICAgfSk7XHJcbiAgICByZXR1cm4gZmFsc2U7XHJcbn1cclxuXHJcbmZ1bmN0aW9uIGxhbmcoa2V5KSB7XHJcbiAgICBpZih3aW5kb3cubGFuZ3VhZ2UgJiYgd2luZG93Lmxhbmd1YWdlW2tleV0pe1xyXG4gICAgICAgIHJldHVybiB3aW5kb3cubGFuZ3VhZ2Vba2V5XTtcclxuICAgIH1cclxuICAgIHJldHVybiBrZXk7XHJcbn1cclxuXHJcbmZ1bmN0aW9uIHJhbmRvbVN0cmluZyhsZW4sIGNoYXJTZXQpIHtcclxuICAgIGNoYXJTZXQgPSBjaGFyU2V0IHx8ICdBQkNERUZHSElKS0xNTk9QUVJTVFVWV1hZWmFiY2RlZmdoaWprbG1ub3BxcnN0dXZ3eHl6MDEyMzQ1Njc4OSc7XHJcbiAgICB2YXIgc3RyID0gJycsYWxsTGVuPWNoYXJTZXQubGVuZ3RoO1xyXG4gICAgZm9yICh2YXIgaSA9IDA7IGkgPCBsZW47IGkrKykge1xyXG4gICAgICAgIHZhciByYW5kb21Qb3ogPSBNYXRoLmZsb29yKE1hdGgucmFuZG9tKCkgKiBhbGxMZW4pO1xyXG4gICAgICAgIHN0ciArPSBjaGFyU2V0LnN1YnN0cmluZyhyYW5kb21Qb3oscmFuZG9tUG96KzEpO1xyXG4gICAgfVxyXG4gICAgcmV0dXJuIHN0cjtcclxufVxyXG5cclxuZnVuY3Rpb24gY29weV9vYmooYXJyKXtcclxuICAgIHJldHVybiBKU09OLnBhcnNlKEpTT04uc3RyaW5naWZ5KGFycikpO1xyXG59XHJcblxyXG5mdW5jdGlvbiBpc09iamVjdFZhbHVlRXF1YWwoYSwgYikge1xyXG4gICAgaWYoIWEgJiYgIWIpcmV0dXJuIHRydWU7XHJcbiAgICBpZighYSB8fCAhYilyZXR1cm4gZmFsc2U7XHJcblxyXG4gICAgLy8gT2YgY291cnNlLCB3ZSBjYW4gZG8gaXQgdXNlIGZvciBpblxyXG4gICAgLy8gQ3JlYXRlIGFycmF5cyBvZiBwcm9wZXJ0eSBuYW1lc1xyXG4gICAgdmFyIGFQcm9wcyA9IE9iamVjdC5nZXRPd25Qcm9wZXJ0eU5hbWVzKGEpO1xyXG4gICAgdmFyIGJQcm9wcyA9IE9iamVjdC5nZXRPd25Qcm9wZXJ0eU5hbWVzKGIpO1xyXG5cclxuICAgIC8vIElmIG51bWJlciBvZiBwcm9wZXJ0aWVzIGlzIGRpZmZlcmVudCxcclxuICAgIC8vIG9iamVjdHMgYXJlIG5vdCBlcXVpdmFsZW50XHJcbiAgICBpZiAoYVByb3BzLmxlbmd0aCAhPSBiUHJvcHMubGVuZ3RoKSB7XHJcbiAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgfVxyXG5cclxuICAgIGZvciAodmFyIGkgPSAwOyBpIDwgYVByb3BzLmxlbmd0aDsgaSsrKSB7XHJcbiAgICAgICAgdmFyIHByb3BOYW1lID0gYVByb3BzW2ldO1xyXG5cclxuICAgICAgICAvLyBJZiB2YWx1ZXMgb2Ygc2FtZSBwcm9wZXJ0eSBhcmUgbm90IGVxdWFsLFxyXG4gICAgICAgIC8vIG9iamVjdHMgYXJlIG5vdCBlcXVpdmFsZW50XHJcbiAgICAgICAgaWYgKGFbcHJvcE5hbWVdICE9PSBiW3Byb3BOYW1lXSkge1xyXG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICAgICAgfVxyXG4gICAgfVxyXG5cclxuICAgIC8vIElmIHdlIG1hZGUgaXQgdGhpcyBmYXIsIG9iamVjdHNcclxuICAgIC8vIGFyZSBjb25zaWRlcmVkIGVxdWl2YWxlbnRcclxuICAgIHJldHVybiB0cnVlO1xyXG59XHJcblxyXG5mdW5jdGlvbiBhcnJheV9jb21iaW5lKGEsYikge1xyXG4gICAgdmFyIG9iaj17fTtcclxuICAgIGZvcih2YXIgaT0wO2k8YS5sZW5ndGg7aSsrKXtcclxuICAgICAgICBpZihiLmxlbmd0aDxpKzEpYnJlYWs7XHJcbiAgICAgICAgb2JqW2FbaV1dPWJbaV07XHJcbiAgICB9XHJcbiAgICByZXR1cm4gb2JqO1xyXG59IiwiXHJcbk51bWJlci5wcm90b3R5cGUuZm9ybWF0PWZ1bmN0aW9uKGZpeCl7XHJcbiAgICBpZihmaXg9PT11bmRlZmluZWQpZml4PTI7XHJcbiAgICB2YXIgbnVtPXRoaXMudG9GaXhlZChmaXgpO1xyXG4gICAgdmFyIHo9bnVtLnNwbGl0KCcuJyk7XHJcbiAgICB2YXIgZm9ybWF0PVtdLGY9elswXS5zcGxpdCgnJyksbD1mLmxlbmd0aDtcclxuICAgIGZvcih2YXIgaT0wO2k8bDtpKyspe1xyXG4gICAgICAgIGlmKGk+MCAmJiBpICUgMz09MCl7XHJcbiAgICAgICAgICAgIGZvcm1hdC51bnNoaWZ0KCcsJyk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIGZvcm1hdC51bnNoaWZ0KGZbbC1pLTFdKTtcclxuICAgIH1cclxuICAgIHJldHVybiBmb3JtYXQuam9pbignJykrKHoubGVuZ3RoPT0yPycuJyt6WzFdOicnKTtcclxufTtcclxuaWYoIVN0cmluZy5wcm90b3R5cGUudHJpbSl7XHJcbiAgICBTdHJpbmcucHJvdG90eXBlLnRyaW09ZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHJldHVybiB0aGlzLnJlcGxhY2UoLyheXFxzK3xcXHMrJCkvZywnJyk7XHJcbiAgICB9XHJcbn1cclxuU3RyaW5nLnByb3RvdHlwZS5jb21waWxlPWZ1bmN0aW9uKGRhdGEsbGlzdCl7XHJcblxyXG4gICAgaWYobGlzdCl7XHJcbiAgICAgICAgdmFyIHRlbXBzPVtdO1xyXG4gICAgICAgIGZvcih2YXIgaSBpbiBkYXRhKXtcclxuICAgICAgICAgICAgdGVtcHMucHVzaCh0aGlzLmNvbXBpbGUoZGF0YVtpXSkpO1xyXG4gICAgICAgIH1cclxuICAgICAgICByZXR1cm4gdGVtcHMuam9pbihcIlxcblwiKTtcclxuICAgIH1lbHNle1xyXG4gICAgICAgIHJldHVybiB0aGlzLnJlcGxhY2UoL1xce2lmXFxzKyhbXlxcfV0rKVxcfShbXFxXXFx3XSope1xcL2lmfS9nLGZ1bmN0aW9uKGFsbCwgY29uZGl0aW9uLCBjb250KXtcclxuICAgICAgICAgICAgdmFyIG9wZXJhdGlvbjtcclxuICAgICAgICAgICAgaWYob3BlcmF0aW9uPWNvbmRpdGlvbi5tYXRjaCgvXFxzKyg9K3w8fD4pXFxzKy8pKXtcclxuICAgICAgICAgICAgICAgIG9wZXJhdGlvbj1vcGVyYXRpb25bMF07XHJcbiAgICAgICAgICAgICAgICB2YXIgcGFydD1jb25kaXRpb24uc3BsaXQob3BlcmF0aW9uKTtcclxuICAgICAgICAgICAgICAgIGlmKHBhcnRbMF0uaW5kZXhPZignQCcpPT09MCl7XHJcbiAgICAgICAgICAgICAgICAgICAgcGFydFswXT1kYXRhW3BhcnRbMF0ucmVwbGFjZSgnQCcsJycpXTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIGlmKHBhcnRbMV0uaW5kZXhPZignQCcpPT09MCl7XHJcbiAgICAgICAgICAgICAgICAgICAgcGFydFsxXT1kYXRhW3BhcnRbMV0ucmVwbGFjZSgnQCcsJycpXTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIG9wZXJhdGlvbj1vcGVyYXRpb24udHJpbSgpO1xyXG4gICAgICAgICAgICAgICAgdmFyIHJlc3VsdD1mYWxzZTtcclxuICAgICAgICAgICAgICAgIHN3aXRjaCAob3BlcmF0aW9uKXtcclxuICAgICAgICAgICAgICAgICAgICBjYXNlICc9PSc6XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHJlc3VsdCA9IHBhcnRbMF0gPT0gcGFydFsxXTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgICAgICAgICAgY2FzZSAnPT09JzpcclxuICAgICAgICAgICAgICAgICAgICAgICAgcmVzdWx0ID0gcGFydFswXSA9PT0gcGFydFsxXTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgICAgICAgICAgY2FzZSAnPic6XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHJlc3VsdCA9IHBhcnRbMF0gPiBwYXJ0WzFdO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgICAgICAgICBjYXNlICc8JzpcclxuICAgICAgICAgICAgICAgICAgICAgICAgcmVzdWx0ID0gcGFydFswXSA8IHBhcnRbMV07XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgaWYocmVzdWx0KXtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gY29udDtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfWVsc2Uge1xyXG4gICAgICAgICAgICAgICAgaWYgKGRhdGFbY29uZGl0aW9uLnJlcGxhY2UoJ0AnLCcnKV0pIHJldHVybiBjb250O1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIHJldHVybiAnJztcclxuICAgICAgICB9KS5yZXBsYWNlKC9cXHtAKFtcXHdcXGRcXC5dKykoPzpcXHwoW1xcd1xcZF0rKSg/Olxccyo9XFxzKihbXFx3XFxkLFxccyNdKykpPyk/XFx9L2csZnVuY3Rpb24oYWxsLG0xLGZ1bmMsYXJncyl7XHJcblxyXG4gICAgICAgICAgICBpZihtMS5pbmRleE9mKCcuJyk+MCl7XHJcbiAgICAgICAgICAgICAgICB2YXIga2V5cz1tMS5zcGxpdCgnLicpLHZhbD1kYXRhO1xyXG4gICAgICAgICAgICAgICAgZm9yKHZhciBpPTA7aTxrZXlzLmxlbmd0aDtpKyspe1xyXG4gICAgICAgICAgICAgICAgICAgIGlmKHZhbFtrZXlzW2ldXSE9PXVuZGVmaW5lZCAmJiB2YWxba2V5c1tpXV0hPT1udWxsKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgdmFsPXZhbFtrZXlzW2ldXTtcclxuICAgICAgICAgICAgICAgICAgICB9ZWxzZXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgdmFsID0gJyc7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIHJldHVybiBjYWxsZnVuYyh2YWwsZnVuYyxhcmdzKTtcclxuICAgICAgICAgICAgfWVsc2V7XHJcbiAgICAgICAgICAgICAgICByZXR1cm4gY2FsbGZ1bmMoZGF0YVttMV0sZnVuYyxhcmdzLGRhdGEpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSk7XHJcbiAgICB9XHJcbn07XHJcblxyXG5mdW5jdGlvbiB0b3N0cmluZyhvYmopIHtcclxuICAgIGlmKG9iaiAmJiBvYmoudG9TdHJpbmcpe1xyXG4gICAgICAgIHJldHVybiBvYmoudG9TdHJpbmcoKTtcclxuICAgIH1cclxuICAgIHJldHVybiAnJztcclxufVxyXG5cclxuZnVuY3Rpb24gY2FsbGZ1bmModmFsLGZ1bmMsYXJncyx0aGlzb2JqKXtcclxuICAgIGlmKCFhcmdzKXtcclxuICAgICAgICBhcmdzPVt2YWxdO1xyXG4gICAgfWVsc2V7XHJcbiAgICAgICAgaWYodHlwZW9mIGFyZ3M9PT0nc3RyaW5nJylhcmdzPWFyZ3Muc3BsaXQoJywnKTtcclxuICAgICAgICB2YXIgYXJnaWR4PWFyZ3MuaW5kZXhPZignIyMjJyk7XHJcbiAgICAgICAgaWYoYXJnaWR4Pj0wKXtcclxuICAgICAgICAgICAgYXJnc1thcmdpZHhdPXZhbDtcclxuICAgICAgICB9ZWxzZXtcclxuICAgICAgICAgICAgYXJncz1bdmFsXS5jb25jYXQoYXJncyk7XHJcbiAgICAgICAgfVxyXG4gICAgfVxyXG5cclxuICAgIHJldHVybiB3aW5kb3dbZnVuY10/d2luZG93W2Z1bmNdLmFwcGx5KHRoaXNvYmosYXJncyk6KCh2YWw9PT11bmRlZmluZWR8fHZhbD09PW51bGwpPycnOnZhbCk7XHJcbn1cclxuXHJcbmZ1bmN0aW9uIGlpZih2LG0xLG0yKXtcclxuICAgIGlmKHY9PT0nMCcpdj0wO1xyXG4gICAgcmV0dXJuIHY/bTE6bTI7XHJcbn0iLCJcclxudmFyIGRpYWxvZ1RwbD0nPGRpdiBjbGFzcz1cIm1vZGFsIGZhZGVcIiBpZD1cIntAaWR9XCIgdGFiaW5kZXg9XCItMVwiIHJvbGU9XCJkaWFsb2dcIiBhcmlhLWxhYmVsbGVkYnk9XCJ7QGlkfUxhYmVsXCIgYXJpYS1oaWRkZW49XCJ0cnVlXCI+XFxuJyArXHJcbiAgICAnICAgIDxkaXYgY2xhc3M9XCJtb2RhbC1kaWFsb2dcIj5cXG4nICtcclxuICAgICcgICAgICAgIDxkaXYgY2xhc3M9XCJtb2RhbC1jb250ZW50XCI+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgPGRpdiBjbGFzcz1cIm1vZGFsLWhlYWRlclwiPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgICAgICA8aDQgY2xhc3M9XCJtb2RhbC10aXRsZVwiIGlkPVwie0BpZH1MYWJlbFwiPjwvaDQ+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgICAgIDxidXR0b24gdHlwZT1cImJ1dHRvblwiIGNsYXNzPVwiY2xvc2VcIiBkYXRhLWRpc21pc3M9XCJtb2RhbFwiPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgICAgICAgICAgPHNwYW4gYXJpYS1oaWRkZW49XCJ0cnVlXCI+JnRpbWVzOzwvc3Bhbj5cXG4nICtcclxuICAgICcgICAgICAgICAgICAgICAgICAgIDxzcGFuIGNsYXNzPVwic3Itb25seVwiPkNsb3NlPC9zcGFuPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgICAgICA8L2J1dHRvbj5cXG4nICtcclxuICAgICcgICAgICAgICAgICA8L2Rpdj5cXG4nICtcclxuICAgICcgICAgICAgICAgICA8ZGl2IGNsYXNzPVwibW9kYWwtYm9keVwiPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgIDwvZGl2PlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJtb2RhbC1mb290ZXJcIj5cXG4nICtcclxuICAgICcgICAgICAgICAgICAgICAgPG5hdiBjbGFzcz1cIm5hdiBuYXYtZmlsbFwiPjwvbmF2PlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgIDwvZGl2PlxcbicgK1xyXG4gICAgJyAgICAgICAgPC9kaXY+XFxuJyArXHJcbiAgICAnICAgIDwvZGl2PlxcbicgK1xyXG4gICAgJzwvZGl2Pic7XHJcbnZhciBkaWFsb2dJZHg9MDtcclxuZnVuY3Rpb24gRGlhbG9nKG9wdHMpe1xyXG4gICAgaWYoIW9wdHMpb3B0cz17fTtcclxuICAgIC8v5aSE55CG5oyJ6ZKuXHJcbiAgICBpZihvcHRzLmJ0bnMhPT11bmRlZmluZWQpIHtcclxuICAgICAgICBpZiAodHlwZW9mKG9wdHMuYnRucykgPT0gJ3N0cmluZycpIHtcclxuICAgICAgICAgICAgb3B0cy5idG5zID0gW29wdHMuYnRuc107XHJcbiAgICAgICAgfVxyXG4gICAgICAgIC8vdmFyIGRmdD0tMTtcclxuICAgICAgICBmb3IgKHZhciBpID0gMDsgaSA8IG9wdHMuYnRucy5sZW5ndGg7IGkrKykge1xyXG4gICAgICAgICAgICBpZih0eXBlb2Yob3B0cy5idG5zW2ldKT09J3N0cmluZycpe1xyXG4gICAgICAgICAgICAgICAgb3B0cy5idG5zW2ldPXsndGV4dCc6b3B0cy5idG5zW2ldfTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICBpZihvcHRzLmJ0bnNbaV0uaXNkZWZhdWx0KXtcclxuICAgICAgICAgICAgICAgIG9wdHMuZGVmYXVsdEJ0bj1pO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfVxyXG4gICAgICAgIGlmKG9wdHMuZGVmYXVsdEJ0bj09PXVuZGVmaW5lZCl7XHJcbiAgICAgICAgICAgIG9wdHMuZGVmYXVsdEJ0bj1vcHRzLmJ0bnMubGVuZ3RoLTE7XHJcbiAgICAgICAgICAgIG9wdHMuYnRuc1tvcHRzLmRlZmF1bHRCdG5dLmlzZGVmYXVsdD10cnVlO1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgaWYob3B0cy5idG5zW29wdHMuZGVmYXVsdEJ0bl0gJiYgIW9wdHMuYnRuc1tvcHRzLmRlZmF1bHRCdG5dWyd0eXBlJ10pe1xyXG4gICAgICAgICAgICBvcHRzLmJ0bnNbb3B0cy5kZWZhdWx0QnRuXVsndHlwZSddPSdwcmltYXJ5JztcclxuICAgICAgICB9XHJcbiAgICB9XHJcblxyXG4gICAgdGhpcy5vcHRpb25zPSQuZXh0ZW5kKHtcclxuICAgICAgICAnaWQnOidkbGdNb2RhbCcrZGlhbG9nSWR4KyssXHJcbiAgICAgICAgJ3NpemUnOicnLFxyXG4gICAgICAgICdidG5zJzpbXHJcbiAgICAgICAgICAgIHsndGV4dCc6J+WPlua2iCcsJ3R5cGUnOidzZWNvbmRhcnknfSxcclxuICAgICAgICAgICAgeyd0ZXh0Jzon56Gu5a6aJywnaXNkZWZhdWx0Jzp0cnVlLCd0eXBlJzoncHJpbWFyeSd9XHJcbiAgICAgICAgXSxcclxuICAgICAgICAnZGVmYXVsdEJ0bic6MSxcclxuICAgICAgICAnb25zdXJlJzpudWxsLFxyXG4gICAgICAgICdvbnNob3cnOm51bGwsXHJcbiAgICAgICAgJ29uc2hvd24nOm51bGwsXHJcbiAgICAgICAgJ29uaGlkZSc6bnVsbCxcclxuICAgICAgICAnb25oaWRkZW4nOm51bGxcclxuICAgIH0sb3B0cyk7XHJcblxyXG4gICAgdGhpcy5ib3g9JCh0aGlzLm9wdGlvbnMuaWQpO1xyXG59XHJcbkRpYWxvZy5wcm90b3R5cGUuZ2VuZXJCdG49ZnVuY3Rpb24ob3B0LGlkeCl7XHJcbiAgICBpZihvcHRbJ3R5cGUnXSlvcHRbJ2NsYXNzJ109J2J0bi1vdXRsaW5lLScrb3B0Wyd0eXBlJ107XHJcbiAgICByZXR1cm4gJzxhIGhyZWY9XCJqYXZhc2NyaXB0OlwiIGNsYXNzPVwibmF2LWl0ZW0gYnRuICcrKG9wdFsnY2xhc3MnXT9vcHRbJ2NsYXNzJ106J2J0bi1vdXRsaW5lLXNlY29uZGFyeScpKydcIiBkYXRhLWluZGV4PVwiJytpZHgrJ1wiPicrb3B0LnRleHQrJzwvYT4nO1xyXG59O1xyXG5EaWFsb2cucHJvdG90eXBlLnNob3c9ZnVuY3Rpb24oaHRtbCx0aXRsZSl7XHJcbiAgICB0aGlzLmJveD0kKCcjJyt0aGlzLm9wdGlvbnMuaWQpO1xyXG4gICAgaWYoIXRpdGxlKXRpdGxlPSfns7vnu5/mj5DnpLonO1xyXG4gICAgaWYodGhpcy5ib3gubGVuZ3RoPDEpIHtcclxuICAgICAgICAkKGRvY3VtZW50LmJvZHkpLmFwcGVuZChkaWFsb2dUcGwucmVwbGFjZSgnbW9kYWwtYm9keScsJ21vZGFsLWJvZHknKyh0aGlzLm9wdGlvbnMuYm9keUNsYXNzPygnICcrdGhpcy5vcHRpb25zLmJvZHlDbGFzcyk6JycpKS5jb21waWxlKHsnaWQnOiB0aGlzLm9wdGlvbnMuaWR9KSk7XHJcbiAgICAgICAgdGhpcy5ib3g9JCgnIycrdGhpcy5vcHRpb25zLmlkKTtcclxuICAgIH1lbHNle1xyXG4gICAgICAgIHRoaXMuYm94LnVuYmluZCgpO1xyXG4gICAgfVxyXG5cclxuICAgIC8vdGhpcy5ib3guZmluZCgnLm1vZGFsLWZvb3RlciAuYnRuLXByaW1hcnknKS51bmJpbmQoKTtcclxuICAgIHZhciBzZWxmPXRoaXM7XHJcbiAgICBEaWFsb2cuaW5zdGFuY2U9c2VsZjtcclxuXHJcbiAgICAvL+eUn+aIkOaMiemSrlxyXG4gICAgdmFyIGJ0bnM9W107XHJcbiAgICBmb3IodmFyIGk9MDtpPHRoaXMub3B0aW9ucy5idG5zLmxlbmd0aDtpKyspe1xyXG4gICAgICAgIGJ0bnMucHVzaCh0aGlzLmdlbmVyQnRuKHRoaXMub3B0aW9ucy5idG5zW2ldLGkpKTtcclxuICAgIH1cclxuICAgIHRoaXMuYm94LmZpbmQoJy5tb2RhbC1mb290ZXIgLm5hdicpLmh0bWwoYnRucy5qb2luKCdcXG4nKSk7XHJcblxyXG4gICAgdmFyIGRpYWxvZz10aGlzLmJveC5maW5kKCcubW9kYWwtZGlhbG9nJyk7XHJcbiAgICBkaWFsb2cucmVtb3ZlQ2xhc3MoJ21vZGFsLXNtJykucmVtb3ZlQ2xhc3MoJ21vZGFsLWxnJyk7XHJcbiAgICBpZih0aGlzLm9wdGlvbnMuc2l6ZT09J3NtJykge1xyXG4gICAgICAgIGRpYWxvZy5hZGRDbGFzcygnbW9kYWwtc20nKTtcclxuICAgIH1lbHNlIGlmKHRoaXMub3B0aW9ucy5zaXplPT0nbGcnKSB7XHJcbiAgICAgICAgZGlhbG9nLmFkZENsYXNzKCdtb2RhbC1sZycpO1xyXG4gICAgfVxyXG4gICAgdGhpcy5ib3guZmluZCgnLm1vZGFsLXRpdGxlJykudGV4dCh0aXRsZSk7XHJcblxyXG4gICAgdmFyIGJvZHk9dGhpcy5ib3guZmluZCgnLm1vZGFsLWJvZHknKTtcclxuICAgIGJvZHkuaHRtbChodG1sKTtcclxuICAgIHRoaXMuYm94Lm9uKCdoaWRlLmJzLm1vZGFsJyxmdW5jdGlvbigpe1xyXG4gICAgICAgIGlmKHNlbGYub3B0aW9ucy5vbmhpZGUpe1xyXG4gICAgICAgICAgICBzZWxmLm9wdGlvbnMub25oaWRlKGJvZHksc2VsZi5ib3gpO1xyXG4gICAgICAgIH1cclxuICAgICAgICBEaWFsb2cuaW5zdGFuY2U9bnVsbDtcclxuICAgIH0pO1xyXG4gICAgdGhpcy5ib3gub24oJ2hpZGRlbi5icy5tb2RhbCcsZnVuY3Rpb24oKXtcclxuICAgICAgICBpZihzZWxmLm9wdGlvbnMub25oaWRkZW4pe1xyXG4gICAgICAgICAgICBzZWxmLm9wdGlvbnMub25oaWRkZW4oYm9keSxzZWxmLmJveCk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHNlbGYuYm94LnJlbW92ZSgpO1xyXG4gICAgfSk7XHJcbiAgICB0aGlzLmJveC5vbignc2hvdy5icy5tb2RhbCcsZnVuY3Rpb24oKXtcclxuICAgICAgICBpZihzZWxmLm9wdGlvbnMub25zaG93KXtcclxuICAgICAgICAgICAgc2VsZi5vcHRpb25zLm9uc2hvdyhib2R5LHNlbGYuYm94KTtcclxuICAgICAgICB9XHJcbiAgICB9KTtcclxuICAgIHRoaXMuYm94Lm9uKCdzaG93bi5icy5tb2RhbCcsZnVuY3Rpb24oKXtcclxuICAgICAgICBpZihzZWxmLm9wdGlvbnMub25zaG93bil7XHJcbiAgICAgICAgICAgIHNlbGYub3B0aW9ucy5vbnNob3duKGJvZHksc2VsZi5ib3gpO1xyXG4gICAgICAgIH1cclxuICAgIH0pO1xyXG4gICAgdGhpcy5ib3guZmluZCgnLm1vZGFsLWZvb3RlciAuYnRuJykuY2xpY2soZnVuY3Rpb24oKXtcclxuICAgICAgICB2YXIgcmVzdWx0PXRydWUsaWR4PSQodGhpcykuZGF0YSgnaW5kZXgnKTtcclxuICAgICAgICBpZihzZWxmLm9wdGlvbnMuYnRuc1tpZHhdLmNsaWNrKXtcclxuICAgICAgICAgICAgcmVzdWx0ID0gc2VsZi5vcHRpb25zLmJ0bnNbaWR4XS5jbGljay5hcHBseSh0aGlzLFtib2R5LCBzZWxmLmJveF0pO1xyXG4gICAgICAgIH1cclxuICAgICAgICBpZihpZHg9PXNlbGYub3B0aW9ucy5kZWZhdWx0QnRuKSB7XHJcbiAgICAgICAgICAgIGlmIChzZWxmLm9wdGlvbnMub25zdXJlKSB7XHJcbiAgICAgICAgICAgICAgICByZXN1bHQgPSBzZWxmLm9wdGlvbnMub25zdXJlLmFwcGx5KHRoaXMsW2JvZHksIHNlbGYuYm94XSk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9XHJcbiAgICAgICAgaWYocmVzdWx0IT09ZmFsc2Upe1xyXG4gICAgICAgICAgICBzZWxmLmJveC5tb2RhbCgnaGlkZScpO1xyXG4gICAgICAgIH1cclxuICAgIH0pO1xyXG4gICAgdGhpcy5ib3gubW9kYWwoJ3Nob3cnKTtcclxuICAgIHJldHVybiB0aGlzO1xyXG59O1xyXG5EaWFsb2cucHJvdG90eXBlLmhpZGU9RGlhbG9nLnByb3RvdHlwZS5jbG9zZT1mdW5jdGlvbigpe1xyXG4gICAgdGhpcy5ib3gubW9kYWwoJ2hpZGUnKTtcclxuICAgIHJldHVybiB0aGlzO1xyXG59O1xyXG5cclxudmFyIGRpYWxvZz17XHJcbiAgICBhbGVydDpmdW5jdGlvbihtZXNzYWdlLGNhbGxiYWNrLHRpdGxlKXtcclxuICAgICAgICB2YXIgY2FsbGVkPWZhbHNlO1xyXG4gICAgICAgIHZhciBpc2NhbGxiYWNrPXR5cGVvZiBjYWxsYmFjaz09J2Z1bmN0aW9uJztcclxuICAgICAgICByZXR1cm4gbmV3IERpYWxvZyh7XHJcbiAgICAgICAgICAgIGJ0bnM6J+ehruWumicsXHJcbiAgICAgICAgICAgIG9uc3VyZTpmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICAgICAgaWYoaXNjYWxsYmFjayl7XHJcbiAgICAgICAgICAgICAgICAgICAgY2FsbGVkPXRydWU7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGNhbGxiYWNrKHRydWUpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICBvbmhpZGU6ZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgICAgIGlmKCFjYWxsZWQgJiYgaXNjYWxsYmFjayl7XHJcbiAgICAgICAgICAgICAgICAgICAgY2FsbGJhY2soZmFsc2UpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkuc2hvdyhtZXNzYWdlLHRpdGxlKTtcclxuICAgIH0sXHJcbiAgICBjb25maXJtOmZ1bmN0aW9uKG1lc3NhZ2UsY29uZmlybSxjYW5jZWwpe1xyXG4gICAgICAgIHZhciBjYWxsZWQ9ZmFsc2U7XHJcbiAgICAgICAgcmV0dXJuIG5ldyBEaWFsb2coe1xyXG4gICAgICAgICAgICAnb25zdXJlJzpmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICAgICAgaWYodHlwZW9mIGNvbmZpcm09PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgICAgIGNhbGxlZD10cnVlO1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBjb25maXJtKCk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdvbmhpZGUnOmZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgIGlmKGNhbGxlZD1mYWxzZSAmJiB0eXBlb2YgY2FuY2VsPT0nZnVuY3Rpb24nKXtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gY2FuY2VsKCk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS5zaG93KG1lc3NhZ2UpO1xyXG4gICAgfSxcclxuICAgIHByb21wdDpmdW5jdGlvbihtZXNzYWdlLGNhbGxiYWNrLGNhbmNlbCl7XHJcbiAgICAgICAgdmFyIGNhbGxlZD1mYWxzZTtcclxuICAgICAgICB2YXIgY29udGVudEh0bWw9JzxkaXYgY2xhc3M9XCJmb3JtLWdyb3VwXCI+e0BpbnB1dH08L2Rpdj4nO1xyXG4gICAgICAgIHZhciB0aXRsZT0n6K+36L6T5YWl5L+h5oGvJztcclxuICAgICAgICBpZih0eXBlb2YgbWVzc2FnZT09J3N0cmluZycpe1xyXG4gICAgICAgICAgICB0aXRsZT1tZXNzYWdlO1xyXG4gICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICB0aXRsZT1tZXNzYWdlLnRpdGxlO1xyXG4gICAgICAgICAgICBpZihtZXNzYWdlLmNvbnRlbnQpIHtcclxuICAgICAgICAgICAgICAgIGNvbnRlbnRIdG1sID0gbWVzc2FnZS5jb250ZW50LmluZGV4T2YoJ3tAaW5wdXR9JykgPiAtMSA/IG1lc3NhZ2UuY29udGVudCA6IG1lc3NhZ2UuY29udGVudCArIGNvbnRlbnRIdG1sO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHJldHVybiBuZXcgRGlhbG9nKHtcclxuICAgICAgICAgICAgJ29uc2hvdyc6ZnVuY3Rpb24oYm9keSl7XHJcbiAgICAgICAgICAgICAgICBpZihtZXNzYWdlICYmIG1lc3NhZ2Uub25zaG93KXtcclxuICAgICAgICAgICAgICAgICAgICBtZXNzYWdlLm9uc2hvdyhib2R5KTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgJ29uc2hvd24nOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgYm9keS5maW5kKCdbbmFtZT1jb25maXJtX2lucHV0XScpLmZvY3VzKCk7XHJcbiAgICAgICAgICAgICAgICBpZihtZXNzYWdlICYmIG1lc3NhZ2Uub25zaG93bil7XHJcbiAgICAgICAgICAgICAgICAgICAgbWVzc2FnZS5vbnNob3duKGJvZHkpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAnb25zdXJlJzpmdW5jdGlvbihib2R5KXtcclxuICAgICAgICAgICAgICAgIHZhciB2YWw9Ym9keS5maW5kKCdbbmFtZT1jb25maXJtX2lucHV0XScpLnZhbCgpO1xyXG4gICAgICAgICAgICAgICAgaWYodHlwZW9mIGNhbGxiYWNrPT0nZnVuY3Rpb24nKXtcclxuICAgICAgICAgICAgICAgICAgICB2YXIgcmVzdWx0ID0gY2FsbGJhY2sodmFsKTtcclxuICAgICAgICAgICAgICAgICAgICBpZihyZXN1bHQ9PT10cnVlKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgY2FsbGVkPXRydWU7XHJcbiAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiByZXN1bHQ7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdvbmhpZGUnOmZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgIGlmKGNhbGxlZD1mYWxzZSAmJiB0eXBlb2YgY2FuY2VsPT0nZnVuY3Rpb24nKXtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gY2FuY2VsKCk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS5zaG93KGNvbnRlbnRIdG1sLmNvbXBpbGUoe2lucHV0Oic8aW5wdXQgdHlwZT1cInRleHRcIiBuYW1lPVwiY29uZmlybV9pbnB1dFwiIGNsYXNzPVwiZm9ybS1jb250cm9sXCIgLz4nfSksdGl0bGUpO1xyXG4gICAgfSxcclxuICAgIGFjdGlvbjpmdW5jdGlvbiAobGlzdCxjYWxsYmFjayx0aXRsZSkge1xyXG4gICAgICAgIHZhciBodG1sPSc8ZGl2IGNsYXNzPVwibGlzdC1ncm91cFwiPjxhIGhyZWY9XCJqYXZhc2NyaXB0OlwiIGNsYXNzPVwibGlzdC1ncm91cC1pdGVtIGxpc3QtZ3JvdXAtaXRlbS1hY3Rpb25cIj4nK2xpc3Quam9pbignPC9hPjxhIGhyZWY9XCJqYXZhc2NyaXB0OlwiIGNsYXNzPVwibGlzdC1ncm91cC1pdGVtIGxpc3QtZ3JvdXAtaXRlbS1hY3Rpb25cIj4nKSsnPC9hPjwvZGl2Pic7XHJcbiAgICAgICAgdmFyIGFjdGlvbnM9bnVsbDtcclxuICAgICAgICB2YXIgZGxnPW5ldyBEaWFsb2coe1xyXG4gICAgICAgICAgICAnYm9keUNsYXNzJzonbW9kYWwtYWN0aW9uJyxcclxuICAgICAgICAgICAgJ2J0bnMnOltcclxuICAgICAgICAgICAgICAgIHsndGV4dCc6J+WPlua2iCcsJ3R5cGUnOidzZWNvbmRhcnknfVxyXG4gICAgICAgICAgICBdLFxyXG4gICAgICAgICAgICAnb25zaG93JzpmdW5jdGlvbihib2R5KXtcclxuICAgICAgICAgICAgICAgIGFjdGlvbnM9Ym9keS5maW5kKCcubGlzdC1ncm91cC1pdGVtLWFjdGlvbicpO1xyXG4gICAgICAgICAgICAgICAgYWN0aW9ucy5jbGljayhmdW5jdGlvbiAoZSkge1xyXG4gICAgICAgICAgICAgICAgICAgIGFjdGlvbnMucmVtb3ZlQ2xhc3MoJ2FjdGl2ZScpO1xyXG4gICAgICAgICAgICAgICAgICAgICQodGhpcykuYWRkQ2xhc3MoJ2FjdGl2ZScpO1xyXG4gICAgICAgICAgICAgICAgICAgIHZhciB2YWw9YWN0aW9ucy5pbmRleCh0aGlzKTtcclxuICAgICAgICAgICAgICAgICAgICBpZih0eXBlb2YgY2FsbGJhY2s9PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiggY2FsbGJhY2sodmFsKSE9PWZhbHNlKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGRsZy5jbG9zZSgpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgfWVsc2Uge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBkbGcuY2xvc2UoKTtcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICB9KVxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAnb25zdXJlJzpmdW5jdGlvbihib2R5KXtcclxuICAgICAgICAgICAgICAgIHJldHVybiB0cnVlO1xyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAnb25oaWRlJzpmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgICAgICByZXR1cm4gdHJ1ZTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLnNob3coaHRtbCx0aXRsZT90aXRsZTon6K+36YCJ5oupJyk7XHJcbiAgICB9LFxyXG4gICAgcGlja0xpc3Q6ZnVuY3Rpb24gKGNvbmZpZyxjYWxsYmFjayxmaWx0ZXIpIHtcclxuICAgICAgICBpZih0eXBlb2YgY29uZmlnPT09J3N0cmluZycpY29uZmlnPXt1cmw6Y29uZmlnfTtcclxuICAgICAgICBjb25maWc9JC5leHRlbmQoe1xyXG4gICAgICAgICAgICAndXJsJzonJyxcclxuICAgICAgICAgICAgJ25hbWUnOiflr7nosaEnLFxyXG4gICAgICAgICAgICAnc2VhcmNoSG9sZGVyJzon5qC55o2u5ZCN56ew5pCc57SiJyxcclxuICAgICAgICAgICAgJ2lka2V5JzonaWQnLFxyXG4gICAgICAgICAgICAnb25Sb3cnOm51bGwsXHJcbiAgICAgICAgICAgICdleHRlbmQnOm51bGwsXHJcbiAgICAgICAgICAgICdyb3dUZW1wbGF0ZSc6JzxhIGhyZWY9XCJqYXZhc2NyaXB0OlwiIGRhdGEtaWQ9XCJ7QGlkfVwiIGNsYXNzPVwibGlzdC1ncm91cC1pdGVtIGxpc3QtZ3JvdXAtaXRlbS1hY3Rpb25cIj5be0BpZH1dJm5ic3A7PGkgY2xhc3M9XCJpb24tbWQtcGVyc29uXCI+PC9pPiB7QHVzZXJuYW1lfSZuYnNwOyZuYnNwOyZuYnNwOzxzbWFsbD48aSBjbGFzcz1cImlvbi1tZC1waG9uZS1wb3J0cmFpdFwiPjwvaT4ge0Btb2JpbGV9PC9zbWFsbD48L2E+J1xyXG4gICAgICAgIH0sY29uZmlnfHx7fSk7XHJcbiAgICAgICAgdmFyIGN1cnJlbnQ9bnVsbDtcclxuICAgICAgICB2YXIgZXh0aHRtbD0nJztcclxuICAgICAgICBpZihjb25maWcuZXh0ZW5kKXtcclxuICAgICAgICAgICAgZXh0aHRtbD0nPHNlbGVjdCBuYW1lPVwiJytjb25maWcuZXh0ZW5kLm5hbWUrJ1wiIGNsYXNzPVwiZm9ybS1jb250cm9sXCI+PG9wdGlvbiB2YWx1ZT1cIlwiPicrY29uZmlnLmV4dGVuZC50aXRsZSsnPC9vcHRpb24+PC9zZWxlY3Q+JztcclxuICAgICAgICB9XHJcbiAgICAgICAgaWYoIWZpbHRlcilmaWx0ZXI9e307XHJcbiAgICAgICAgdmFyIGRsZz1uZXcgRGlhbG9nKHtcclxuICAgICAgICAgICAgJ29uc2hvd24nOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgdmFyIGJ0bj1ib2R5LmZpbmQoJy5zZWFyY2hidG4nKTtcclxuICAgICAgICAgICAgICAgIHZhciBpbnB1dD1ib2R5LmZpbmQoJy5zZWFyY2h0ZXh0Jyk7XHJcbiAgICAgICAgICAgICAgICB2YXIgbGlzdGJveD1ib2R5LmZpbmQoJy5saXN0LWdyb3VwJyk7XHJcbiAgICAgICAgICAgICAgICB2YXIgaXNsb2FkaW5nPWZhbHNlO1xyXG4gICAgICAgICAgICAgICAgdmFyIGV4dEZpZWxkPW51bGw7XHJcbiAgICAgICAgICAgICAgICBpZihjb25maWcuZXh0ZW5kKXtcclxuICAgICAgICAgICAgICAgICAgICBleHRGaWVsZD1ib2R5LmZpbmQoJ1tuYW1lPScrY29uZmlnLmV4dGVuZC5uYW1lKyddJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgJC5hamF4KHtcclxuICAgICAgICAgICAgICAgICAgICAgICB1cmw6Y29uZmlnLmV4dGVuZC51cmwsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHR5cGU6J0dFVCcsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGRhdGFUeXBlOidKU09OJyxcclxuICAgICAgICAgICAgICAgICAgICAgICAgc3VjY2VzczpmdW5jdGlvbiAoanNvbikge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZXh0RmllbGQuYXBwZW5kKGNvbmZpZy5leHRlbmQuaHRtbFJvdy5jb21waWxlKGpzb24uZGF0YSx0cnVlKSk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIGJ0bi5jbGljayhmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICAgICAgICAgIGlmKGlzbG9hZGluZylyZXR1cm47XHJcbiAgICAgICAgICAgICAgICAgICAgaXNsb2FkaW5nPXRydWU7XHJcbiAgICAgICAgICAgICAgICAgICAgbGlzdGJveC5odG1sKCc8c3BhbiBjbGFzcz1cImxpc3QtbG9hZGluZ1wiPuWKoOi9veS4rS4uLjwvc3Bhbj4nKTtcclxuICAgICAgICAgICAgICAgICAgICBmaWx0ZXJbJ2tleSddPWlucHV0LnZhbCgpO1xyXG4gICAgICAgICAgICAgICAgICAgIGlmKGNvbmZpZy5leHRlbmQpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBmaWx0ZXJbY29uZmlnLmV4dGVuZC5uYW1lXT1leHRGaWVsZC52YWwoKTtcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgJC5hamF4KFxyXG4gICAgICAgICAgICAgICAgICAgICAgICB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB1cmw6Y29uZmlnLnVybCxcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHR5cGU6J0dFVCcsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBkYXRhVHlwZTonSlNPTicsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBkYXRhOmZpbHRlcixcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHN1Y2Nlc3M6ZnVuY3Rpb24oanNvbil7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaXNsb2FkaW5nPWZhbHNlO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmKGpzb24uc3RhdHVzKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYoanNvbi5kYXRhICYmIGpzb24uZGF0YS5sZW5ndGgpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxpc3Rib3guaHRtbChjb25maWcucm93VGVtcGxhdGUuY29tcGlsZShqc29uLmRhdGEsIHRydWUpKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxpc3Rib3guZmluZCgnYS5saXN0LWdyb3VwLWl0ZW0nKS5jbGljayhmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyIGlkID0gJCh0aGlzKS5kYXRhKCdpZCcpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGZvciAodmFyIGkgPSAwOyBpIDwganNvbi5kYXRhLmxlbmd0aDsgaSsrKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmKGpzb24uZGF0YVtpXVtjb25maWcuaWRrZXldPT1pZCl7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBjdXJyZW50PWpzb24uZGF0YVtpXTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxpc3Rib3guZmluZCgnYS5saXN0LWdyb3VwLWl0ZW0nKS5yZW1vdmVDbGFzcygnYWN0aXZlJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmFkZENsYXNzKCdhY3RpdmUnKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfSlcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfWVsc2V7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBsaXN0Ym94Lmh0bWwoJzxzcGFuIGNsYXNzPVwibGlzdC1sb2FkaW5nXCI+PGkgY2xhc3M9XCJpb24tbWQtd2FybmluZ1wiPjwvaT4g5rKh5pyJ5qOA57Si5YiwJytjb25maWcubmFtZSsnPC9zcGFuPicpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfWVsc2V7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxpc3Rib3guaHRtbCgnPHNwYW4gY2xhc3M9XCJ0ZXh0LWRhbmdlclwiPjxpIGNsYXNzPVwiaW9uLW1kLXdhcm5pbmdcIj48L2k+IOWKoOi9veWksei0pTwvc3Bhbj4nKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICApO1xyXG5cclxuICAgICAgICAgICAgICAgIH0pLnRyaWdnZXIoJ2NsaWNrJyk7XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdvbnN1cmUnOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgaWYoIWN1cnJlbnQpe1xyXG4gICAgICAgICAgICAgICAgICAgIHRvYXN0ci53YXJuaW5nKCfmsqHmnInpgInmi6knK2NvbmZpZy5uYW1lKychJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgaWYodHlwZW9mIGNhbGxiYWNrPT0nZnVuY3Rpb24nKXtcclxuICAgICAgICAgICAgICAgICAgICB2YXIgcmVzdWx0ID0gY2FsbGJhY2soY3VycmVudCk7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHJlc3VsdDtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLnNob3coJzxkaXYgY2xhc3M9XCJpbnB1dC1ncm91cFwiPicrZXh0aHRtbCsnPGlucHV0IHR5cGU9XCJ0ZXh0XCIgY2xhc3M9XCJmb3JtLWNvbnRyb2wgc2VhcmNodGV4dFwiIG5hbWU9XCJrZXl3b3JkXCIgcGxhY2Vob2xkZXI9XCInK2NvbmZpZy5zZWFyY2hIb2xkZXIrJ1wiLz48ZGl2IGNsYXNzPVwiaW5wdXQtZ3JvdXAtYXBwZW5kXCI+PGEgY2xhc3M9XCJidG4gYnRuLW91dGxpbmUtc2Vjb25kYXJ5IHNlYXJjaGJ0blwiPjxpIGNsYXNzPVwiaW9uLW1kLXNlYXJjaFwiPjwvaT48L2E+PC9kaXY+PC9kaXY+PGRpdiBjbGFzcz1cImxpc3QtZ3JvdXAgbGlzdC1ncm91cC1waWNrZXIgbXQtMlwiPjwvZGl2PicsJ+ivt+aQnOe0ouW5tumAieaLqScrY29uZmlnLm5hbWUpO1xyXG4gICAgfSxcclxuICAgIHBpY2tVc2VyOmZ1bmN0aW9uKGNhbGxiYWNrLGZpbHRlcil7XHJcbiAgICAgICAgcmV0dXJuIHRoaXMucGlja0xpc3Qoe1xyXG4gICAgICAgICAgICAndXJsJzp3aW5kb3cuZ2V0X3NlYXJjaF91cmwoJ21lbWJlcicpLFxyXG4gICAgICAgICAgICAnbmFtZSc6J+S8muWRmCcsXHJcbiAgICAgICAgICAgICdzZWFyY2hIb2xkZXInOifmoLnmja7kvJrlkZhpZOaIluWQjeensO+8jOeUteivneadpeaQnOe0oidcclxuICAgICAgICB9LGNhbGxiYWNrLGZpbHRlcik7XHJcbiAgICB9LFxyXG4gICAgcGlja0FydGljbGU6ZnVuY3Rpb24oY2FsbGJhY2ssZmlsdGVyKXtcclxuICAgICAgICByZXR1cm4gdGhpcy5waWNrTGlzdCh7XHJcbiAgICAgICAgICAgICd1cmwnOndpbmRvdy5nZXRfc2VhcmNoX3VybCgnYXJ0aWNsZScpLFxyXG4gICAgICAgICAgICByb3dUZW1wbGF0ZTonPGEgaHJlZj1cImphdmFzY3JpcHQ6XCIgZGF0YS1pZD1cIntAaWR9XCIgY2xhc3M9XCJsaXN0LWdyb3VwLWl0ZW0gbGlzdC1ncm91cC1pdGVtLWFjdGlvblwiPntpZiBAY292ZXJ9PGRpdiBzdHlsZT1cImJhY2tncm91bmQtaW1hZ2U6dXJsKHtAY292ZXJ9KVwiIGNsYXNzPVwiaW1ndmlld1wiID48L2Rpdj57L2lmfTxkaXYgY2xhc3M9XCJ0ZXh0LWJsb2NrXCI+W3tAaWR9XSZuYnNwO3tAdGl0bGV9Jm5ic3A7PGJyIC8+e0BkZXNjcmlwdGlvbn08L2Rpdj48L2E+JyxcclxuICAgICAgICAgICAgbmFtZTon5paH56ugJyxcclxuICAgICAgICAgICAgaWRrZXk6J2lkJyxcclxuICAgICAgICAgICAgZXh0ZW5kOntcclxuICAgICAgICAgICAgICAgbmFtZTonY2F0ZScsXHJcbiAgICAgICAgICAgICAgICB0aXRsZTon5oyJ5YiG57G75pCc57SiJyxcclxuICAgICAgICAgICAgICAgIHVybDpnZXRfY2F0ZV91cmwoJ2FydGljbGUnKSxcclxuICAgICAgICAgICAgICAgIGh0bWxSb3c6JzxvcHRpb24gdmFsdWU9XCJ7QGlkfVwiPntAaHRtbH17QHRpdGxlfTwvb3B0aW9uPidcclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgJ3NlYXJjaEhvbGRlcic6J+agueaNruaWh+eroOagh+mimOaQnOe0oidcclxuICAgICAgICB9LGNhbGxiYWNrLGZpbHRlcik7XHJcbiAgICB9LFxyXG4gICAgcGlja1Byb2R1Y3Q6ZnVuY3Rpb24oY2FsbGJhY2ssZmlsdGVyKXtcclxuICAgICAgICByZXR1cm4gdGhpcy5waWNrTGlzdCh7XHJcbiAgICAgICAgICAgICd1cmwnOndpbmRvdy5nZXRfc2VhcmNoX3VybCgncHJvZHVjdCcpLFxyXG4gICAgICAgICAgICByb3dUZW1wbGF0ZTonPGEgaHJlZj1cImphdmFzY3JpcHQ6XCIgZGF0YS1pZD1cIntAaWR9XCIgY2xhc3M9XCJsaXN0LWdyb3VwLWl0ZW0gbGlzdC1ncm91cC1pdGVtLWFjdGlvblwiPntpZiBAaW1hZ2V9PGRpdiBzdHlsZT1cImJhY2tncm91bmQtaW1hZ2U6dXJsKHtAaW1hZ2V9KVwiIGNsYXNzPVwiaW1ndmlld1wiID48L2Rpdj57L2lmfTxkaXYgY2xhc3M9XCJ0ZXh0LWJsb2NrXCI+W3tAaWR9XSZuYnNwO3tAdGl0bGV9Jm5ic3A7PGJyIC8+e0BtaW5fcHJpY2V9fntAbWF4X3ByaWNlfTwvZGl2PjwvYT4nLFxyXG4gICAgICAgICAgICBuYW1lOifkuqflk4EnLFxyXG4gICAgICAgICAgICBpZGtleTonaWQnLFxyXG4gICAgICAgICAgICBleHRlbmQ6e1xyXG4gICAgICAgICAgICAgICAgbmFtZTonY2F0ZScsXHJcbiAgICAgICAgICAgICAgICB0aXRsZTon5oyJ5YiG57G75pCc57SiJyxcclxuICAgICAgICAgICAgICAgIHVybDpnZXRfY2F0ZV91cmwoJ3Byb2R1Y3QnKSxcclxuICAgICAgICAgICAgICAgIGh0bWxSb3c6JzxvcHRpb24gdmFsdWU9XCJ7QGlkfVwiPntAaHRtbH17QHRpdGxlfTwvb3B0aW9uPidcclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgJ3NlYXJjaEhvbGRlcic6J+agueaNruS6p+WTgeWQjeensOaQnOe0oidcclxuICAgICAgICB9LGNhbGxiYWNrLGZpbHRlcik7XHJcbiAgICB9LFxyXG4gICAgcGlja0xvY2F0ZTpmdW5jdGlvbih0eXBlLCBjYWxsYmFjaywgbG9jYXRlKXtcclxuICAgICAgICB2YXIgc2V0dGVkTG9jYXRlPW51bGw7XHJcbiAgICAgICAgdmFyIGRsZz1uZXcgRGlhbG9nKHtcclxuICAgICAgICAgICAgJ3NpemUnOidsZycsXHJcbiAgICAgICAgICAgICdvbnNob3duJzpmdW5jdGlvbihib2R5KXtcclxuICAgICAgICAgICAgICAgIHZhciBidG49Ym9keS5maW5kKCcuc2VhcmNoYnRuJyk7XHJcbiAgICAgICAgICAgICAgICB2YXIgaW5wdXQ9Ym9keS5maW5kKCcuc2VhcmNodGV4dCcpO1xyXG4gICAgICAgICAgICAgICAgdmFyIG1hcGJveD1ib2R5LmZpbmQoJy5tYXAnKTtcclxuICAgICAgICAgICAgICAgIHZhciBtYXBpbmZvPWJvZHkuZmluZCgnLm1hcGluZm8nKTtcclxuICAgICAgICAgICAgICAgIG1hcGJveC5jc3MoJ2hlaWdodCcsJCh3aW5kb3cpLmhlaWdodCgpKi42KTtcclxuICAgICAgICAgICAgICAgIHZhciBpc2xvYWRpbmc9ZmFsc2U7XHJcbiAgICAgICAgICAgICAgICB2YXIgbWFwPUluaXRNYXAoJ3RlbmNlbnQnLG1hcGJveCxmdW5jdGlvbihhZGRyZXNzLGxvY2F0ZSl7XHJcbiAgICAgICAgICAgICAgICAgICAgbWFwaW5mby5odG1sKGFkZHJlc3MrJyZuYnNwOycrbG9jYXRlLmxuZysnLCcrbG9jYXRlLmxhdCk7XHJcbiAgICAgICAgICAgICAgICAgICAgc2V0dGVkTG9jYXRlPWxvY2F0ZTtcclxuICAgICAgICAgICAgICAgIH0sbG9jYXRlKTtcclxuICAgICAgICAgICAgICAgIGJ0bi5jbGljayhmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICAgICAgICAgIHZhciBzZWFyY2g9aW5wdXQudmFsKCk7XHJcbiAgICAgICAgICAgICAgICAgICAgbWFwLnNldExvY2F0ZShzZWFyY2gpO1xyXG4gICAgICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdvbnN1cmUnOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgaWYoIXNldHRlZExvY2F0ZSl7XHJcbiAgICAgICAgICAgICAgICAgICAgdG9hc3RyLndhcm5pbmcoJ+ayoeaciemAieaLqeS9jee9riEnKTtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICBpZih0eXBlb2YgY2FsbGJhY2s9PT0nZnVuY3Rpb24nKXtcclxuICAgICAgICAgICAgICAgICAgICB2YXIgcmVzdWx0ID0gY2FsbGJhY2soc2V0dGVkTG9jYXRlKTtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gcmVzdWx0O1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkuc2hvdygnPGRpdiBjbGFzcz1cImlucHV0LWdyb3VwXCI+PGlucHV0IHR5cGU9XCJ0ZXh0XCIgY2xhc3M9XCJmb3JtLWNvbnRyb2wgc2VhcmNodGV4dFwiIG5hbWU9XCJrZXl3b3JkXCIgcGxhY2Vob2xkZXI9XCLloavlhpnlnLDlnYDmo4DntKLkvY3nva5cIi8+PGRpdiBjbGFzcz1cImlucHV0LWdyb3VwLWFwcGVuZFwiPjxhIGNsYXNzPVwiYnRuIGJ0bi1vdXRsaW5lLXNlY29uZGFyeSBzZWFyY2hidG5cIj48aSBjbGFzcz1cImlvbi1tZC1zZWFyY2hcIj48L2k+PC9hPjwvZGl2PjwvZGl2PicgK1xyXG4gICAgICAgICAgICAnPGRpdiBjbGFzcz1cIm1hcCBtdC0yXCI+PC9kaXY+JyArXHJcbiAgICAgICAgICAgICc8ZGl2IGNsYXNzPVwibWFwaW5mbyBtdC0yIHRleHQtbXV0ZWRcIj7mnKrpgInmi6nkvY3nva48L2Rpdj4nLCfor7fpgInmi6nlnLDlm77kvY3nva4nKTtcclxuICAgIH1cclxufTtcclxuXHJcbmpRdWVyeShmdW5jdGlvbigkKXtcclxuXHJcbiAgICAvL+ebkeaOp+aMiemUrlxyXG4gICAgJChkb2N1bWVudCkub24oJ2tleWRvd24nLCBmdW5jdGlvbihlKXtcclxuICAgICAgICBpZighRGlhbG9nLmluc3RhbmNlKXJldHVybjtcclxuICAgICAgICB2YXIgZGxnPURpYWxvZy5pbnN0YW5jZTtcclxuICAgICAgICBpZiAoZS5rZXlDb2RlID09IDEzKSB7XHJcbiAgICAgICAgICAgIGRsZy5ib3guZmluZCgnLm1vZGFsLWZvb3RlciAuYnRuJykuZXEoZGxnLm9wdGlvbnMuZGVmYXVsdEJ0bikudHJpZ2dlcignY2xpY2snKTtcclxuICAgICAgICB9XHJcbiAgICAgICAgLy/pu5jorqTlt7Lnm5HlkKzlhbPpl61cclxuICAgICAgICAvKmlmIChlLmtleUNvZGUgPT0gMjcpIHtcclxuICAgICAgICAgc2VsZi5oaWRlKCk7XHJcbiAgICAgICAgIH0qL1xyXG4gICAgfSk7XHJcbn0pOyIsIlxyXG5qUXVlcnkuZXh0ZW5kKGpRdWVyeS5mbix7XHJcbiAgICB0YWdzOmZ1bmN0aW9uKG5tLG9udXBkYXRlKXtcclxuICAgICAgICB2YXIgZGF0YT1bXTtcclxuICAgICAgICB2YXIgdHBsPSc8c3BhbiBjbGFzcz1cImJhZGdlIGJhZGdlLWluZm9cIj57QGxhYmVsfTxpbnB1dCB0eXBlPVwiaGlkZGVuXCIgbmFtZT1cIicrbm0rJ1wiIHZhbHVlPVwie0BsYWJlbH1cIi8+PGJ1dHRvbiB0eXBlPVwiYnV0dG9uXCIgY2xhc3M9XCJjbG9zZVwiIGRhdGEtZGlzbWlzcz1cImFsZXJ0XCIgYXJpYS1sYWJlbD1cIkNsb3NlXCI+PHNwYW4gYXJpYS1oaWRkZW49XCJ0cnVlXCI+JnRpbWVzOzwvc3Bhbj48L2J1dHRvbj48L3NwYW4+JztcclxuICAgICAgICB2YXIgaXRlbT0kKHRoaXMpLnBhcmVudHMoJy5mb3JtLWNvbnRyb2wnKTtcclxuICAgICAgICB2YXIgbGFiZWxncm91cD0kKCc8c3BhbiBjbGFzcz1cImJhZGdlLWdyb3VwXCI+PC9zcGFuPicpO1xyXG4gICAgICAgIHZhciBpbnB1dD10aGlzO1xyXG4gICAgICAgIHRoaXMuYmVmb3JlKGxhYmVsZ3JvdXApO1xyXG4gICAgICAgIHRoaXMub24oJ2tleXVwJyxmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICB2YXIgdmFsPSQodGhpcykudmFsKCkucmVwbGFjZSgv77yML2csJywnKTtcclxuICAgICAgICAgICAgdmFyIHVwZGF0ZWQ9ZmFsc2U7XHJcbiAgICAgICAgICAgIGlmKHZhbCAmJiB2YWwuaW5kZXhPZignLCcpPi0xKXtcclxuICAgICAgICAgICAgICAgIHZhciB2YWxzPXZhbC5zcGxpdCgnLCcpO1xyXG4gICAgICAgICAgICAgICAgZm9yKHZhciBpPTA7aTx2YWxzLmxlbmd0aDtpKyspe1xyXG4gICAgICAgICAgICAgICAgICAgIHZhbHNbaV09dmFsc1tpXS5yZXBsYWNlKC9eXFxzfFxccyQvZywnJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgaWYodmFsc1tpXSAmJiBkYXRhLmluZGV4T2YodmFsc1tpXSk9PT0tMSl7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGRhdGEucHVzaCh2YWxzW2ldKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgbGFiZWxncm91cC5hcHBlbmQodHBsLmNvbXBpbGUoe2xhYmVsOnZhbHNbaV19KSk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHVwZGF0ZWQ9dHJ1ZTtcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICBpbnB1dC52YWwoJycpO1xyXG4gICAgICAgICAgICAgICAgaWYodXBkYXRlZCAmJiBvbnVwZGF0ZSlvbnVwZGF0ZShkYXRhKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLm9uKCdibHVyJyxmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICB2YXIgdmFsPSQodGhpcykudmFsKCk7XHJcbiAgICAgICAgICAgIGlmKHZhbCkge1xyXG4gICAgICAgICAgICAgICAgJCh0aGlzKS52YWwodmFsICsgJywnKS50cmlnZ2VyKCdrZXl1cCcpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkudHJpZ2dlcignYmx1cicpO1xyXG4gICAgICAgIGxhYmVsZ3JvdXAub24oJ2NsaWNrJywnLmNsb3NlJyxmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICB2YXIgdGFnPSQodGhpcykucGFyZW50cygnLmJhZGdlJykuZmluZCgnaW5wdXQnKS52YWwoKTtcclxuICAgICAgICAgICAgdmFyIGlkPWRhdGEuaW5kZXhPZih0YWcpO1xyXG4gICAgICAgICAgICBpZihpZClkYXRhLnNwbGljZShpZCwxKTtcclxuICAgICAgICAgICAgJCh0aGlzKS5wYXJlbnRzKCcuYmFkZ2UnKS5yZW1vdmUoKTtcclxuICAgICAgICAgICAgaWYob251cGRhdGUpb251cGRhdGUoZGF0YSk7XHJcbiAgICAgICAgfSk7XHJcbiAgICAgICAgaXRlbS5jbGljayhmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICBpbnB1dC5mb2N1cygpO1xyXG4gICAgICAgIH0pO1xyXG4gICAgfVxyXG59KTsiLCIvL+aXpeacn+e7hOS7tlxyXG5pZigkLmZuLmRhdGV0aW1lcGlja2VyKSB7XHJcbiAgICB2YXIgdG9vbHRpcHM9IHtcclxuICAgICAgICB0b2RheTogJ+WumuS9jeW9k+WJjeaXpeacnycsXHJcbiAgICAgICAgY2xlYXI6ICfmuIXpmaTlt7LpgInml6XmnJ8nLFxyXG4gICAgICAgIGNsb3NlOiAn5YWz6Zet6YCJ5oup5ZmoJyxcclxuICAgICAgICBzZWxlY3RNb250aDogJ+mAieaLqeaciOS7vScsXHJcbiAgICAgICAgcHJldk1vbnRoOiAn5LiK5Liq5pyIJyxcclxuICAgICAgICBuZXh0TW9udGg6ICfkuIvkuKrmnIgnLFxyXG4gICAgICAgIHNlbGVjdFllYXI6ICfpgInmi6nlubTku70nLFxyXG4gICAgICAgIHByZXZZZWFyOiAn5LiK5LiA5bm0JyxcclxuICAgICAgICBuZXh0WWVhcjogJ+S4i+S4gOW5tCcsXHJcbiAgICAgICAgc2VsZWN0RGVjYWRlOiAn6YCJ5oup5bm05Lu95Yy66Ze0JyxcclxuICAgICAgICBzZWxlY3RUaW1lOifpgInmi6nml7bpl7QnLFxyXG4gICAgICAgIHByZXZEZWNhZGU6ICfkuIrkuIDljLrpl7QnLFxyXG4gICAgICAgIG5leHREZWNhZGU6ICfkuIvkuIDljLrpl7QnLFxyXG4gICAgICAgIHByZXZDZW50dXJ5OiAn5LiK5Liq5LiW57qqJyxcclxuICAgICAgICBuZXh0Q2VudHVyeTogJ+S4i+S4quS4lue6qidcclxuICAgIH07XHJcblxyXG4gICAgZnVuY3Rpb24gdHJhbnNPcHRpb24ob3B0aW9uKSB7XHJcbiAgICAgICAgaWYoIW9wdGlvbilyZXR1cm4ge307XHJcbiAgICAgICAgdmFyIG5ld29wdD17fTtcclxuICAgICAgICBmb3IodmFyIGkgaW4gb3B0aW9uKXtcclxuICAgICAgICAgICAgc3dpdGNoIChpKXtcclxuICAgICAgICAgICAgICAgIGNhc2UgJ3ZpZXdtb2RlJzpcclxuICAgICAgICAgICAgICAgICAgICBuZXdvcHRbJ3ZpZXdNb2RlJ109b3B0aW9uW2ldO1xyXG4gICAgICAgICAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgICAgICAgICAgY2FzZSAna2VlcG9wZW4nOlxyXG4gICAgICAgICAgICAgICAgICAgIG5ld29wdFsna2VlcE9wZW4nXT1vcHRpb25baV07XHJcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgICAgICBkZWZhdWx0OlxyXG4gICAgICAgICAgICAgICAgICAgIG5ld29wdFtpXT1vcHRpb25baV07XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9XHJcbiAgICAgICAgcmV0dXJuIG5ld29wdDtcclxuICAgIH1cclxuICAgICQoJy5kYXRlcGlja2VyJykuZWFjaChmdW5jdGlvbigpe1xyXG4gICAgICAgIHZhciBjb25maWc9JC5leHRlbmQoe1xyXG4gICAgICAgICAgICB0b29sdGlwczp0b29sdGlwcyxcclxuICAgICAgICAgICAgZm9ybWF0OiAnWVlZWS1NTS1ERCcsXHJcbiAgICAgICAgICAgIGxvY2FsZTogJ3poLWNuJyxcclxuICAgICAgICAgICAgc2hvd0NsZWFyOnRydWUsXHJcbiAgICAgICAgICAgIHNob3dUb2RheUJ1dHRvbjp0cnVlLFxyXG4gICAgICAgICAgICBzaG93Q2xvc2U6dHJ1ZSxcclxuICAgICAgICAgICAga2VlcEludmFsaWQ6dHJ1ZVxyXG4gICAgICAgIH0sdHJhbnNPcHRpb24oJCh0aGlzKS5kYXRhKCkpKTtcclxuXHJcbiAgICAgICAgJCh0aGlzKS5kYXRldGltZXBpY2tlcihjb25maWcpO1xyXG4gICAgfSk7XHJcblxyXG4gICAgJCgnLmRhdGUtcmFuZ2UnKS5lYWNoKGZ1bmN0aW9uICgpIHtcclxuICAgICAgICB2YXIgZnJvbSA9ICQodGhpcykuZmluZCgnW25hbWU9ZnJvbWRhdGVdLC5mcm9tZGF0ZScpLCB0byA9ICQodGhpcykuZmluZCgnW25hbWU9dG9kYXRlXSwudG9kYXRlJyk7XHJcbiAgICAgICAgdmFyIG9wdGlvbnMgPSAkLmV4dGVuZCh7XHJcbiAgICAgICAgICAgIHRvb2x0aXBzOnRvb2x0aXBzLFxyXG4gICAgICAgICAgICBmb3JtYXQ6ICdZWVlZLU1NLUREJyxcclxuICAgICAgICAgICAgbG9jYWxlOid6aC1jbicsXHJcbiAgICAgICAgICAgIHNob3dDbGVhcjp0cnVlLFxyXG4gICAgICAgICAgICBzaG93VG9kYXlCdXR0b246dHJ1ZSxcclxuICAgICAgICAgICAgc2hvd0Nsb3NlOnRydWUsXHJcbiAgICAgICAgICAgIGtlZXBJbnZhbGlkOnRydWVcclxuICAgICAgICB9LCQodGhpcykuZGF0YSgpKTtcclxuICAgICAgICBmcm9tLmRhdGV0aW1lcGlja2VyKG9wdGlvbnMpLm9uKCdkcC5jaGFuZ2UnLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIGlmIChmcm9tLnZhbCgpKSB7XHJcbiAgICAgICAgICAgICAgICB0by5kYXRhKCdEYXRlVGltZVBpY2tlcicpLm1pbkRhdGUoZnJvbS52YWwoKSk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KTtcclxuICAgICAgICB0by5kYXRldGltZXBpY2tlcihvcHRpb25zKS5vbignZHAuY2hhbmdlJywgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICBpZiAodG8udmFsKCkpIHtcclxuICAgICAgICAgICAgICAgIGZyb20uZGF0YSgnRGF0ZVRpbWVQaWNrZXInKS5tYXhEYXRlKHRvLnZhbCgpKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pO1xyXG4gICAgfSk7XHJcbn0iLCJcclxuKGZ1bmN0aW9uKHdpbmRvdywkKSB7XHJcbiAgICB2YXIgYXBpcyA9IHtcclxuICAgICAgICAnYmFpZHUnOiAnaHR0cHM6Ly9hcGkubWFwLmJhaWR1LmNvbS9hcGk/YWs9ck85dE9kRVdGZnZ5R2dEa2lXcUZqeEs2JnY9MS41JnNlcnZpY2VzPWZhbHNlJmNhbGxiYWNrPScsXHJcbiAgICAgICAgJ2dvb2dsZSc6ICdodHRwczovL21hcHMuZ29vZ2xlLmNvbS9tYXBzL2FwaS9qcz9rZXk9QUl6YVN5Qjhsb3J2bDZFdHFJV3o2N2JqV0JydU9obTlOWVMxZTI0JmNhbGxiYWNrPScsXHJcbiAgICAgICAgJ3RlbmNlbnQnOiAnaHR0cHM6Ly9tYXAucXEuY29tL2FwaS9qcz92PTIuZXhwJmtleT03STVCWi1RVUU2Ui1KWExXVi1XVFZBQS1DSk1ZRi03UEJCSSZjYWxsYmFjaz0nLFxyXG4gICAgICAgICdnYW9kZSc6ICdodHRwczovL3dlYmFwaS5hbWFwLmNvbS9tYXBzP3Y9MS4zJmtleT0zZWMzMTFiNWRiMGQ1OTdlNzk0MjJlZWI5YTZkNDQ0OSZjYWxsYmFjaz0nXHJcbiAgICB9O1xyXG5cclxuICAgIGZ1bmN0aW9uIGxvYWRTY3JpcHQoc3JjKSB7XHJcbiAgICAgICAgdmFyIHNjcmlwdCA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoXCJzY3JpcHRcIik7XHJcbiAgICAgICAgc2NyaXB0LnR5cGUgPSBcInRleHQvamF2YXNjcmlwdFwiO1xyXG4gICAgICAgIHNjcmlwdC5zcmMgPSBzcmM7XHJcbiAgICAgICAgZG9jdW1lbnQuYm9keS5hcHBlbmRDaGlsZChzY3JpcHQpO1xyXG4gICAgfVxyXG5cclxuICAgIHZhciBtYXBPYmosbWFwQm94LG9uUGljaztcclxuXHJcbiAgICBmdW5jdGlvbiBJbml0TWFwKG1hcGtleSxib3gsY2FsbGJhY2ssbG9jYXRlKSB7XHJcbiAgICAgICAgaWYgKG1hcE9iaikgbWFwT2JqLmhpZGUoKTtcclxuICAgICAgICBtYXBCb3g9JChib3gpO1xyXG4gICAgICAgIG9uUGljaz1jYWxsYmFjaztcclxuXHJcbiAgICAgICAgc3dpdGNoIChtYXBrZXkudG9Mb3dlckNhc2UoKSkge1xyXG4gICAgICAgICAgICBjYXNlICdiYWlkdSc6XHJcbiAgICAgICAgICAgICAgICBtYXBPYmogPSBuZXcgQmFpZHVNYXAoKTtcclxuICAgICAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgICAgICBjYXNlICdnb29nbGUnOlxyXG4gICAgICAgICAgICAgICAgbWFwT2JqID0gbmV3IEdvb2dsZU1hcCgpO1xyXG4gICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgIGNhc2UgJ3RlbmNlbnQnOlxyXG4gICAgICAgICAgICBjYXNlICdxcSc6XHJcbiAgICAgICAgICAgICAgICBtYXBPYmogPSBuZXcgVGVuY2VudE1hcCgpO1xyXG4gICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgIGNhc2UgJ2dhb2RlJzpcclxuICAgICAgICAgICAgICAgIG1hcE9iaiA9IG5ldyBHYW9kZU1hcCgpO1xyXG4gICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIGlmICghbWFwT2JqKSByZXR1cm4gdG9hc3RyLndhcm5pbmcoJ+S4jeaUr+aMgeivpeWcsOWbvuexu+WeiycpO1xyXG4gICAgICAgIGlmKGxvY2F0ZSl7XHJcbiAgICAgICAgICAgIGlmKHR5cGVvZiBsb2NhdGU9PT0nc3RyaW5nJyl7XHJcbiAgICAgICAgICAgICAgICB2YXIgbG9jPWxvY2F0ZS5zcGxpdCgnLCcpO1xyXG4gICAgICAgICAgICAgICAgbG9jYXRlPXtcclxuICAgICAgICAgICAgICAgICAgICBsbmc6cGFyc2VGbG9hdChsb2NbMF0pLFxyXG4gICAgICAgICAgICAgICAgICAgIGxhdDpwYXJzZUZsb2F0KGxvY1sxXSlcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICBtYXBPYmoubG9jYXRlPWxvY2F0ZTtcclxuICAgICAgICB9XHJcbiAgICAgICAgbWFwT2JqLnNldE1hcCgpO1xyXG5cclxuICAgICAgICByZXR1cm4gbWFwT2JqO1xyXG4gICAgfVxyXG5cclxuICAgIGZ1bmN0aW9uIEJhc2VNYXAodHlwZSkge1xyXG4gICAgICAgIHRoaXMubWFwVHlwZSA9IHR5cGU7XHJcbiAgICAgICAgdGhpcy5pc2hpZGUgPSBmYWxzZTtcclxuICAgICAgICB0aGlzLmlzc2hvdyA9IGZhbHNlO1xyXG4gICAgICAgIHRoaXMudG9zaG93ID0gZmFsc2U7XHJcbiAgICAgICAgdGhpcy5tYXJrZXIgPSBudWxsO1xyXG4gICAgICAgIHRoaXMuaW5mb1dpbmRvdyA9IG51bGw7XHJcbiAgICAgICAgdGhpcy5tYXBib3ggPSBudWxsO1xyXG4gICAgICAgIHRoaXMubG9jYXRlID0ge2xuZzoxMTYuMzk2Nzk1LGxhdDozOS45MzMwODR9O1xyXG4gICAgICAgIHRoaXMubWFwID0gbnVsbDtcclxuICAgIH1cclxuXHJcbiAgICBCYXNlTWFwLnByb3RvdHlwZS5pc0FQSVJlYWR5ID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgIH07XHJcbiAgICBCYXNlTWFwLnByb3RvdHlwZS5zZXRNYXAgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICB9O1xyXG4gICAgQmFzZU1hcC5wcm90b3R5cGUuc2hvd0luZm8gPSBmdW5jdGlvbiAoKSB7XHJcbiAgICB9O1xyXG4gICAgQmFzZU1hcC5wcm90b3R5cGUuZ2V0QWRkcmVzcyA9IGZ1bmN0aW9uIChycykge1xyXG4gICAgICAgIHJldHVybiBcIlwiO1xyXG4gICAgfTtcclxuICAgIEJhc2VNYXAucHJvdG90eXBlLnNldExvY2F0ZSA9IGZ1bmN0aW9uIChhZGRyZXNzKSB7XHJcbiAgICB9O1xyXG5cclxuICAgIEJhc2VNYXAucHJvdG90eXBlLmxvYWRBUEkgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgdmFyIHNlbGYgPSB0aGlzO1xyXG4gICAgICAgIGlmICghdGhpcy5pc0FQSVJlYWR5KCkpIHtcclxuICAgICAgICAgICAgdGhpcy5tYXBib3ggPSAkKCc8ZGl2IGlkPVwiJyArIHRoaXMubWFwVHlwZSArICdtYXBcIiBjbGFzcz1cIm1hcGJveFwiPmxvYWRpbmcuLi48L2Rpdj4nKTtcclxuICAgICAgICAgICAgbWFwQm94LmFwcGVuZCh0aGlzLm1hcGJveCk7XHJcblxyXG4gICAgICAgICAgICAvL2NvbnNvbGUubG9nKHRoaXMubWFwVHlwZSsnIG1hcGxvYWRpbmcuLi4nKTtcclxuICAgICAgICAgICAgdmFyIGZ1bmMgPSAnbWFwbG9hZCcgKyBuZXcgRGF0ZSgpLmdldFRpbWUoKTtcclxuICAgICAgICAgICAgd2luZG93W2Z1bmNdID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAgICAgc2VsZi5zZXRNYXAoKTtcclxuICAgICAgICAgICAgICAgIGRlbGV0ZSB3aW5kb3dbZnVuY107XHJcbiAgICAgICAgICAgIH07XHJcbiAgICAgICAgICAgIGxvYWRTY3JpcHQoYXBpc1t0aGlzLm1hcFR5cGVdICsgZnVuYyk7XHJcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAvL2NvbnNvbGUubG9nKHRoaXMubWFwVHlwZSArICcgbWFwbG9hZGVkJyk7XHJcbiAgICAgICAgICAgIHRoaXMubWFwYm94ID0gJCgnIycgKyB0aGlzLm1hcFR5cGUgKyAnbWFwJyk7XHJcbiAgICAgICAgICAgIGlmICh0aGlzLm1hcGJveC5sZW5ndGggPCAxKSB7XHJcbiAgICAgICAgICAgICAgICB0aGlzLm1hcGJveCA9ICQoJzxkaXYgaWQ9XCInICsgdGhpcy5tYXBUeXBlICsgJ21hcFwiIGNsYXNzPVwibWFwYm94XCI+PC9kaXY+Jyk7XHJcbiAgICAgICAgICAgICAgICBtYXBCb3guYXBwZW5kKHRoaXMubWFwYm94KTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICByZXR1cm4gdHJ1ZTtcclxuICAgICAgICB9XHJcbiAgICB9O1xyXG4gICAgQmFzZU1hcC5wcm90b3R5cGUuYmluZEV2ZW50cyA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICB2YXIgc2VsZiA9IHRoaXM7XHJcbiAgICAgICAgJCgnI3R4dFRpdGxlJykudW5iaW5kKCkuYmx1cihmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIHNlbGYuc2hvd0luZm8oKTtcclxuICAgICAgICB9KTtcclxuICAgICAgICAkKCcjdHh0Q29udGVudCcpLnVuYmluZCgpLmJsdXIoZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICBzZWxmLnNob3dJbmZvKCk7XHJcbiAgICAgICAgfSk7XHJcbiAgICB9O1xyXG4gICAgQmFzZU1hcC5wcm90b3R5cGUuc2V0SW5mb0NvbnRlbnQgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgaWYgKCF0aGlzLmluZm9XaW5kb3cpIHJldHVybjtcclxuICAgICAgICB2YXIgdGl0bGUgPSAnPGI+5b2T5YmN5L2N572uPC9iPic7XHJcbiAgICAgICAgdmFyIGFkZHIgPSAnPHAgc3R5bGU9XCJsaW5lLWhlaWdodDoxLjZlbTtcIj48L3A+JztcclxuICAgICAgICBpZiAodGhpcy5pbmZvV2luZG93LnNldFRpdGxlKSB7XHJcbiAgICAgICAgICAgIHRoaXMuaW5mb1dpbmRvdy5zZXRUaXRsZSh0aXRsZSk7XHJcbiAgICAgICAgICAgIHRoaXMuaW5mb1dpbmRvdy5zZXRDb250ZW50KGFkZHIpO1xyXG4gICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgIHZhciBjb250ZW50ID0gJzxoMz4nICsgdGl0bGUgKyAnPC9oMz48ZGl2IHN0eWxlPVwid2lkdGg6MjUwcHhcIj4nICsgYWRkciArICc8L2Rpdj4nO1xyXG4gICAgICAgICAgICB0aGlzLmluZm9XaW5kb3cuc2V0Q29udGVudChjb250ZW50KTtcclxuICAgICAgICB9XHJcbiAgICB9O1xyXG4gICAgQmFzZU1hcC5wcm90b3R5cGUuc2hvd0xvY2F0aW9uSW5mbyA9IGZ1bmN0aW9uIChwdCwgcnMpIHtcclxuXHJcbiAgICAgICAgdGhpcy5zaG93SW5mbygpO1xyXG4gICAgICAgIHZhciBhZGRyZXNzPXRoaXMuZ2V0QWRkcmVzcyhycyk7XHJcbiAgICAgICAgdmFyIGxvY2F0ZT17fTtcclxuICAgICAgICBpZiAodHlwZW9mIChwdC5sbmcpID09PSAnZnVuY3Rpb24nKSB7XHJcbiAgICAgICAgICAgIGxvY2F0ZS5sbmc9cHQubG5nKCk7XHJcbiAgICAgICAgICAgIGxvY2F0ZS5sYXQ9cHQubGF0KCk7XHJcbiAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgbG9jYXRlLmxuZz1wdC5sbmc7XHJcbiAgICAgICAgICAgIGxvY2F0ZS5sYXQ9cHQubGF0O1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgb25QaWNrKGFkZHJlc3MsbG9jYXRlKTtcclxuICAgIH07XHJcbiAgICBCYXNlTWFwLnByb3RvdHlwZS5zaG93ID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHRoaXMuaXNoaWRlID0gZmFsc2U7XHJcbiAgICAgICAgdGhpcy5zZXRNYXAoKTtcclxuICAgICAgICB0aGlzLnNob3dJbmZvKCk7XHJcbiAgICB9O1xyXG4gICAgQmFzZU1hcC5wcm90b3R5cGUuaGlkZSA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICB0aGlzLmlzaGlkZSA9IHRydWU7XHJcbiAgICAgICAgaWYgKHRoaXMuaW5mb1dpbmRvdykge1xyXG4gICAgICAgICAgICB0aGlzLmluZm9XaW5kb3cuY2xvc2UoKTtcclxuICAgICAgICB9XHJcbiAgICAgICAgaWYgKHRoaXMubWFwYm94KSB7XHJcbiAgICAgICAgICAgICQodGhpcy5tYXBib3gpLnJlbW92ZSgpO1xyXG4gICAgICAgIH1cclxuICAgIH07XHJcblxyXG5cclxuICAgIGZ1bmN0aW9uIEJhaWR1TWFwKCkge1xyXG4gICAgICAgIEJhc2VNYXAuY2FsbCh0aGlzLCBcImJhaWR1XCIpO1xyXG4gICAgfVxyXG5cclxuICAgIEJhaWR1TWFwLnByb3RvdHlwZSA9IG5ldyBCYXNlTWFwKCk7XHJcbiAgICBCYWlkdU1hcC5wcm90b3R5cGUuY29uc3RydWN0b3IgPSBCYWlkdU1hcDtcclxuICAgIEJhaWR1TWFwLnByb3RvdHlwZS5pc0FQSVJlYWR5ID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHJldHVybiAhIXdpbmRvd1snQk1hcCddO1xyXG4gICAgfTtcclxuICAgIEJhaWR1TWFwLnByb3RvdHlwZS5zZXRNYXAgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgdmFyIHNlbGYgPSB0aGlzO1xyXG4gICAgICAgIGlmICh0aGlzLmlzc2hvdyB8fCB0aGlzLmlzaGlkZSkgcmV0dXJuO1xyXG4gICAgICAgIGlmICghdGhpcy5sb2FkQVBJKCkpIHJldHVybjtcclxuXHJcbiAgICAgICAgdmFyIG1hcCA9IHNlbGYubWFwID0gbmV3IEJNYXAuTWFwKHRoaXMubWFwYm94LmF0dHIoJ2lkJykpOyAvL+WIneWni+WMluWcsOWbvlxyXG4gICAgICAgIG1hcC5hZGRDb250cm9sKG5ldyBCTWFwLk5hdmlnYXRpb25Db250cm9sKCkpOyAgLy/liJ3lp4vljJblnLDlm77mjqfku7ZcclxuICAgICAgICBtYXAuYWRkQ29udHJvbChuZXcgQk1hcC5TY2FsZUNvbnRyb2woKSk7XHJcbiAgICAgICAgbWFwLmFkZENvbnRyb2wobmV3IEJNYXAuT3ZlcnZpZXdNYXBDb250cm9sKCkpO1xyXG4gICAgICAgIG1hcC5lbmFibGVTY3JvbGxXaGVlbFpvb20oKTtcclxuXHJcbiAgICAgICAgdmFyIHBvaW50ID0gbmV3IEJNYXAuUG9pbnQodGhpcy5sb2NhdGUubG5nLCB0aGlzLmxvY2F0ZS5sYXQpO1xyXG4gICAgICAgIG1hcC5jZW50ZXJBbmRab29tKHBvaW50LCAxNSk7IC8v5Yid5aeL5YyW5Zyw5Zu+5Lit5b+D54K5XHJcbiAgICAgICAgdGhpcy5tYXJrZXIgPSBuZXcgQk1hcC5NYXJrZXIocG9pbnQpOyAvL+WIneWni+WMluWcsOWbvuagh+iusFxyXG4gICAgICAgIHRoaXMubWFya2VyLmVuYWJsZURyYWdnaW5nKCk7IC8v5qCH6K6w5byA5ZCv5ouW5ou9XHJcblxyXG4gICAgICAgIHZhciBnYyA9IG5ldyBCTWFwLkdlb2NvZGVyKCk7IC8v5Zyw5Z2A6Kej5p6Q57G7XHJcbiAgICAgICAgLy/mt7vliqDmoIforrDmi5bmi73nm5HlkKxcclxuICAgICAgICB0aGlzLm1hcmtlci5hZGRFdmVudExpc3RlbmVyKFwiZHJhZ2VuZFwiLCBmdW5jdGlvbiAoZSkge1xyXG4gICAgICAgICAgICAvL+iOt+WPluWcsOWdgOS/oeaBr1xyXG4gICAgICAgICAgICBnYy5nZXRMb2NhdGlvbihlLnBvaW50LCBmdW5jdGlvbiAocnMpIHtcclxuICAgICAgICAgICAgICAgIHNlbGYuc2hvd0xvY2F0aW9uSW5mbyhlLnBvaW50LCBycyk7XHJcbiAgICAgICAgICAgIH0pO1xyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICAvL+a3u+WKoOagh+iusOeCueWHu+ebkeWQrFxyXG4gICAgICAgIHRoaXMubWFya2VyLmFkZEV2ZW50TGlzdGVuZXIoXCJjbGlja1wiLCBmdW5jdGlvbiAoZSkge1xyXG4gICAgICAgICAgICBnYy5nZXRMb2NhdGlvbihlLnBvaW50LCBmdW5jdGlvbiAocnMpIHtcclxuICAgICAgICAgICAgICAgIHNlbGYuc2hvd0xvY2F0aW9uSW5mbyhlLnBvaW50LCBycyk7XHJcbiAgICAgICAgICAgIH0pO1xyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICBtYXAuYWRkT3ZlcmxheSh0aGlzLm1hcmtlcik7IC8v5bCG5qCH6K6w5re75Yqg5Yiw5Zyw5Zu+5LitXHJcblxyXG4gICAgICAgIGdjLmdldExvY2F0aW9uKHBvaW50LCBmdW5jdGlvbiAocnMpIHtcclxuICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKHBvaW50LCBycyk7XHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIHRoaXMuaW5mb1dpbmRvdyA9IG5ldyBCTWFwLkluZm9XaW5kb3coXCJcIiwge1xyXG4gICAgICAgICAgICB3aWR0aDogMjUwLFxyXG4gICAgICAgICAgICB0aXRsZTogXCJcIlxyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICB0aGlzLmJpbmRFdmVudHMoKTtcclxuXHJcbiAgICAgICAgdGhpcy5pc3Nob3cgPSB0cnVlO1xyXG4gICAgICAgIGlmICh0aGlzLnRvc2hvdykge1xyXG4gICAgICAgICAgICB0aGlzLnNob3dJbmZvKCk7XHJcbiAgICAgICAgICAgIHRoaXMudG9zaG93ID0gZmFsc2U7XHJcbiAgICAgICAgfVxyXG4gICAgfTtcclxuXHJcbiAgICBCYWlkdU1hcC5wcm90b3R5cGUuc2hvd0luZm8gPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgaWYgKHRoaXMuaXNoaWRlKSByZXR1cm47XHJcbiAgICAgICAgaWYgKCF0aGlzLmlzc2hvdykge1xyXG4gICAgICAgICAgICB0aGlzLnRvc2hvdyA9IHRydWU7XHJcbiAgICAgICAgICAgIHJldHVybjtcclxuICAgICAgICB9XHJcbiAgICAgICAgdGhpcy5zZXRJbmZvQ29udGVudCgpO1xyXG5cclxuICAgICAgICB0aGlzLm1hcmtlci5vcGVuSW5mb1dpbmRvdyh0aGlzLmluZm9XaW5kb3cpO1xyXG4gICAgfTtcclxuICAgIEJhaWR1TWFwLnByb3RvdHlwZS5nZXRBZGRyZXNzID0gZnVuY3Rpb24gKHJzKSB7XHJcbiAgICAgICAgdmFyIGFkZENvbXAgPSBycy5hZGRyZXNzQ29tcG9uZW50cztcclxuICAgICAgICBpZihhZGRDb21wKSB7XHJcbiAgICAgICAgICAgIHJldHVybiBhZGRDb21wLnByb3ZpbmNlICsgXCIsIFwiICsgYWRkQ29tcC5jaXR5ICsgXCIsIFwiICsgYWRkQ29tcC5kaXN0cmljdCArIFwiLCBcIiArIGFkZENvbXAuc3RyZWV0ICsgXCIsIFwiICsgYWRkQ29tcC5zdHJlZXROdW1iZXI7XHJcbiAgICAgICAgfVxyXG4gICAgfTtcclxuICAgIEJhaWR1TWFwLnByb3RvdHlwZS5zZXRMb2NhdGUgPSBmdW5jdGlvbiAoYWRkcmVzcykge1xyXG4gICAgICAgIC8vIOWIm+W7uuWcsOWdgOino+aekOWZqOWunuS+i1xyXG4gICAgICAgIHZhciBteUdlbyA9IG5ldyBCTWFwLkdlb2NvZGVyKCk7XHJcbiAgICAgICAgdmFyIHNlbGYgPSB0aGlzO1xyXG4gICAgICAgIG15R2VvLmdldFBvaW50KGFkZHJlc3MsIGZ1bmN0aW9uIChwb2ludCkge1xyXG4gICAgICAgICAgICBpZiAocG9pbnQpIHtcclxuICAgICAgICAgICAgICAgIHNlbGYubWFwLmNlbnRlckFuZFpvb20ocG9pbnQsIDExKTtcclxuICAgICAgICAgICAgICAgIHNlbGYubWFya2VyLnNldFBvc2l0aW9uKHBvaW50KTtcclxuICAgICAgICAgICAgICAgIG15R2VvLmdldExvY2F0aW9uKHBvaW50LCBmdW5jdGlvbiAocnMpIHtcclxuICAgICAgICAgICAgICAgICAgICBzZWxmLnNob3dMb2NhdGlvbkluZm8ocG9pbnQsIHJzKTtcclxuICAgICAgICAgICAgICAgIH0pO1xyXG4gICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgdG9hc3RyLndhcm5pbmcoXCLlnLDlnYDkv6Hmga/kuI3mraPnoa7vvIzlrprkvY3lpLHotKVcIik7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9LCAnJyk7XHJcbiAgICB9O1xyXG5cclxuXHJcbiAgICBmdW5jdGlvbiBHb29nbGVNYXAoKSB7XHJcbiAgICAgICAgQmFzZU1hcC5jYWxsKHRoaXMsIFwiZ29vZ2xlXCIpO1xyXG4gICAgICAgIHRoaXMuaW5mb09wdHMgPSB7XHJcbiAgICAgICAgICAgIHdpZHRoOiAyNTAsICAgICAvL+S/oeaBr+eql+WPo+WuveW6plxyXG4gICAgICAgICAgICAvLyAgIGhlaWdodDogMTAwLCAgICAgLy/kv6Hmga/nqpflj6Ppq5jluqZcclxuICAgICAgICAgICAgdGl0bGU6IFwiXCIgIC8v5L+h5oGv56qX5Y+j5qCH6aKYXHJcbiAgICAgICAgfTtcclxuICAgIH1cclxuXHJcbiAgICBHb29nbGVNYXAucHJvdG90eXBlID0gbmV3IEJhc2VNYXAoKTtcclxuICAgIEdvb2dsZU1hcC5wcm90b3R5cGUuY29uc3RydWN0b3IgPSBHb29nbGVNYXA7XHJcbiAgICBHb29nbGVNYXAucHJvdG90eXBlLmlzQVBJUmVhZHkgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgcmV0dXJuIHdpbmRvd1snZ29vZ2xlJ10gJiYgd2luZG93Wydnb29nbGUnXVsnbWFwcyddXHJcbiAgICB9O1xyXG4gICAgR29vZ2xlTWFwLnByb3RvdHlwZS5zZXRNYXAgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgdmFyIHNlbGYgPSB0aGlzO1xyXG4gICAgICAgIGlmICh0aGlzLmlzc2hvdyB8fCB0aGlzLmlzaGlkZSkgcmV0dXJuO1xyXG4gICAgICAgIGlmICghdGhpcy5sb2FkQVBJKCkpIHJldHVybjtcclxuXHJcbiAgICAgICAgLy/or7TmmI7lnLDlm77lt7LliIfmjaJcclxuICAgICAgICBpZiAodGhpcy5tYXBib3gubGVuZ3RoIDwgMSkgcmV0dXJuO1xyXG5cclxuICAgICAgICB2YXIgbWFwID0gc2VsZi5tYXAgPSBuZXcgZ29vZ2xlLm1hcHMuTWFwKHRoaXMubWFwYm94WzBdLCB7XHJcbiAgICAgICAgICAgIHpvb206IDE1LFxyXG4gICAgICAgICAgICBkcmFnZ2FibGU6IHRydWUsXHJcbiAgICAgICAgICAgIHNjYWxlQ29udHJvbDogdHJ1ZSxcclxuICAgICAgICAgICAgc3RyZWV0Vmlld0NvbnRyb2w6IHRydWUsXHJcbiAgICAgICAgICAgIHpvb21Db250cm9sOiB0cnVlXHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIC8v6I635Y+W57uP57qs5bqm5Z2Q5qCH5YC8XHJcbiAgICAgICAgdmFyIHBvaW50ID0gbmV3IGdvb2dsZS5tYXBzLkxhdExuZyh0aGlzLmxvY2F0ZSk7XHJcbiAgICAgICAgbWFwLnBhblRvKHBvaW50KTtcclxuICAgICAgICB0aGlzLm1hcmtlciA9IG5ldyBnb29nbGUubWFwcy5NYXJrZXIoe3Bvc2l0aW9uOiBwb2ludCwgbWFwOiBtYXAsIGRyYWdnYWJsZTogdHJ1ZX0pO1xyXG5cclxuXHJcbiAgICAgICAgdmFyIGdjID0gbmV3IGdvb2dsZS5tYXBzLkdlb2NvZGVyKCk7XHJcblxyXG4gICAgICAgIHRoaXMubWFya2VyLmFkZExpc3RlbmVyKFwiZHJhZ2VuZFwiLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIHBvaW50ID0gc2VsZi5tYXJrZXIuZ2V0UG9zaXRpb24oKTtcclxuICAgICAgICAgICAgZ2MuZ2VvY29kZSh7J2xvY2F0aW9uJzogcG9pbnR9LCBmdW5jdGlvbiAocnMpIHtcclxuICAgICAgICAgICAgICAgIHNlbGYuc2hvd0xvY2F0aW9uSW5mbyhwb2ludCwgcnMpO1xyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgLy/mt7vliqDmoIforrDngrnlh7vnm5HlkKxcclxuICAgICAgICB0aGlzLm1hcmtlci5hZGRMaXN0ZW5lcihcImNsaWNrXCIsIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgcG9pbnQgPSBzZWxmLm1hcmtlci5nZXRQb3NpdGlvbigpO1xyXG4gICAgICAgICAgICBnYy5nZW9jb2RlKHsnbG9jYXRpb24nOiBwb2ludH0sIGZ1bmN0aW9uIChycykge1xyXG4gICAgICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKHBvaW50LCBycyk7XHJcbiAgICAgICAgICAgIH0pO1xyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICB0aGlzLmJpbmRFdmVudHMoKTtcclxuXHJcbiAgICAgICAgZ2MuZ2VvY29kZSh7J2xvY2F0aW9uJzogcG9pbnR9LCBmdW5jdGlvbiAocnMpIHtcclxuICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKHBvaW50LCBycyk7XHJcbiAgICAgICAgfSk7XHJcbiAgICAgICAgdGhpcy5pbmZvV2luZG93ID0gbmV3IGdvb2dsZS5tYXBzLkluZm9XaW5kb3coe21hcDogbWFwfSk7XHJcbiAgICAgICAgdGhpcy5pbmZvV2luZG93LnNldFBvc2l0aW9uKHBvaW50KTtcclxuXHJcbiAgICAgICAgdGhpcy5pc3Nob3cgPSB0cnVlO1xyXG4gICAgICAgIGlmICh0aGlzLnRvc2hvdykge1xyXG4gICAgICAgICAgICB0aGlzLnNob3dJbmZvKCk7XHJcbiAgICAgICAgICAgIHRoaXMudG9zaG93ID0gZmFsc2U7XHJcbiAgICAgICAgfVxyXG4gICAgfTtcclxuXHJcbiAgICBHb29nbGVNYXAucHJvdG90eXBlLnNob3dJbmZvID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIGlmICh0aGlzLmlzaGlkZSkgcmV0dXJuO1xyXG4gICAgICAgIGlmICghdGhpcy5pc3Nob3cpIHtcclxuICAgICAgICAgICAgdGhpcy50b3Nob3cgPSB0cnVlO1xyXG4gICAgICAgICAgICByZXR1cm47XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHRoaXMuaW5mb1dpbmRvdy5zZXRPcHRpb25zKHtwb3NpdGlvbjogdGhpcy5tYXJrZXIuZ2V0UG9zaXRpb24oKX0pO1xyXG4gICAgICAgIHRoaXMuc2V0SW5mb0NvbnRlbnQoKTtcclxuXHJcbiAgICB9O1xyXG4gICAgR29vZ2xlTWFwLnByb3RvdHlwZS5nZXRBZGRyZXNzID0gZnVuY3Rpb24gKHJzLCBzdGF0dXMpIHtcclxuICAgICAgICBpZiAocnMgJiYgcnNbMF0pIHtcclxuICAgICAgICAgICAgcmV0dXJuIHJzWzBdLmZvcm1hdHRlZF9hZGRyZXNzO1xyXG4gICAgICAgIH1cclxuICAgIH07XHJcbiAgICBHb29nbGVNYXAucHJvdG90eXBlLnNldExvY2F0ZSA9IGZ1bmN0aW9uIChhZGRyZXNzKSB7XHJcbiAgICAgICAgLy8g5Yib5bu65Zyw5Z2A6Kej5p6Q5Zmo5a6e5L6LXHJcbiAgICAgICAgdmFyIG15R2VvID0gbmV3IGdvb2dsZS5tYXBzLkdlb2NvZGVyKCk7XHJcbiAgICAgICAgdmFyIHNlbGYgPSB0aGlzO1xyXG4gICAgICAgIG15R2VvLmdldFBvaW50KGFkZHJlc3MsIGZ1bmN0aW9uIChwb2ludCkge1xyXG4gICAgICAgICAgICBpZiAocG9pbnQpIHtcclxuICAgICAgICAgICAgICAgIHNlbGYubWFwLmNlbnRlckFuZFpvb20ocG9pbnQsIDExKTtcclxuICAgICAgICAgICAgICAgIHNlbGYubWFya2VyLnNldFBvc2l0aW9uKHBvaW50KTtcclxuICAgICAgICAgICAgICAgIG15R2VvLmdldExvY2F0aW9uKHBvaW50LCBmdW5jdGlvbiAocnMpIHtcclxuICAgICAgICAgICAgICAgICAgICBzZWxmLnNob3dMb2NhdGlvbkluZm8ocG9pbnQsIHJzKTtcclxuICAgICAgICAgICAgICAgIH0pO1xyXG4gICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgdG9hc3RyLndhcm5pbmcoXCLlnLDlnYDkv6Hmga/kuI3mraPnoa7vvIzlrprkvY3lpLHotKVcIik7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9LCAnJyk7XHJcbiAgICB9O1xyXG5cclxuICAgIGZ1bmN0aW9uIFRlbmNlbnRNYXAoKSB7XHJcbiAgICAgICAgQmFzZU1hcC5jYWxsKHRoaXMsIFwidGVuY2VudFwiKTtcclxuICAgIH1cclxuXHJcbiAgICBUZW5jZW50TWFwLnByb3RvdHlwZSA9IG5ldyBCYXNlTWFwKCk7XHJcbiAgICBUZW5jZW50TWFwLnByb3RvdHlwZS5jb25zdHJ1Y3RvciA9IFRlbmNlbnRNYXA7XHJcbiAgICBUZW5jZW50TWFwLnByb3RvdHlwZS5pc0FQSVJlYWR5ID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHJldHVybiB3aW5kb3dbJ3FxJ10gJiYgd2luZG93WydxcSddWydtYXBzJ107XHJcbiAgICB9O1xyXG5cclxuICAgIFRlbmNlbnRNYXAucHJvdG90eXBlLnNldE1hcCA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICB2YXIgc2VsZiA9IHRoaXM7XHJcbiAgICAgICAgaWYgKHRoaXMuaXNzaG93IHx8IHRoaXMuaXNoaWRlKSByZXR1cm47XHJcbiAgICAgICAgaWYgKCF0aGlzLmxvYWRBUEkoKSkgcmV0dXJuO1xyXG5cclxuXHJcbiAgICAgICAgLy/liJ3lp4vljJblnLDlm75cclxuICAgICAgICB2YXIgbWFwID0gc2VsZi5tYXAgPSBuZXcgcXEubWFwcy5NYXAodGhpcy5tYXBib3hbMF0sIHt6b29tOiAxNX0pO1xyXG4gICAgICAgIC8v5Yid5aeL5YyW5Zyw5Zu+5o6n5Lu2XHJcbiAgICAgICAgbmV3IHFxLm1hcHMuU2NhbGVDb250cm9sKHtcclxuICAgICAgICAgICAgYWxpZ246IHFxLm1hcHMuQUxJR04uQk9UVE9NX0xFRlQsXHJcbiAgICAgICAgICAgIG1hcmdpbjogcXEubWFwcy5TaXplKDg1LCAxNSksXHJcbiAgICAgICAgICAgIG1hcDogbWFwXHJcbiAgICAgICAgfSk7XHJcbiAgICAgICAgLy9tYXAuYWRkQ29udHJvbChuZXcgQk1hcC5PdmVydmlld01hcENvbnRyb2woKSk7XHJcbiAgICAgICAgLy9tYXAuZW5hYmxlU2Nyb2xsV2hlZWxab29tKCk7XHJcblxyXG4gICAgICAgIC8v6I635Y+W57uP57qs5bqm5Z2Q5qCH5YC8XHJcbiAgICAgICAgdmFyIHBvaW50ID0gbmV3IHFxLm1hcHMuTGF0TG5nKHRoaXMubG9jYXRlLmxhdCwgdGhpcy5sb2NhdGUubG5nKTtcclxuICAgICAgICBtYXAucGFuVG8ocG9pbnQpOyAvL+WIneWni+WMluWcsOWbvuS4reW/g+eCuVxyXG5cclxuICAgICAgICAvL+WIneWni+WMluWcsOWbvuagh+iusFxyXG4gICAgICAgIHRoaXMubWFya2VyID0gbmV3IHFxLm1hcHMuTWFya2VyKHtcclxuICAgICAgICAgICAgcG9zaXRpb246IHBvaW50LFxyXG4gICAgICAgICAgICBkcmFnZ2FibGU6IHRydWUsXHJcbiAgICAgICAgICAgIG1hcDogbWFwXHJcbiAgICAgICAgfSk7XHJcbiAgICAgICAgdGhpcy5tYXJrZXIuc2V0QW5pbWF0aW9uKHFxLm1hcHMuTWFya2VyQW5pbWF0aW9uLkRPV04pO1xyXG5cclxuICAgICAgICAvL+WcsOWdgOino+aekOexu1xyXG4gICAgICAgIHZhciBnYyA9IG5ldyBxcS5tYXBzLkdlb2NvZGVyKHtcclxuICAgICAgICAgICAgY29tcGxldGU6IGZ1bmN0aW9uIChycykge1xyXG4gICAgICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKHBvaW50LCBycyk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgcXEubWFwcy5ldmVudC5hZGRMaXN0ZW5lcih0aGlzLm1hcmtlciwgJ2NsaWNrJywgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICBwb2ludCA9IHNlbGYubWFya2VyLmdldFBvc2l0aW9uKCk7XHJcbiAgICAgICAgICAgIGdjLmdldEFkZHJlc3MocG9pbnQpO1xyXG4gICAgICAgIH0pO1xyXG4gICAgICAgIC8v6K6+572uTWFya2Vy5YGc5q2i5ouW5Yqo5LqL5Lu2XHJcbiAgICAgICAgcXEubWFwcy5ldmVudC5hZGRMaXN0ZW5lcih0aGlzLm1hcmtlciwgJ2RyYWdlbmQnLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIHBvaW50ID0gc2VsZi5tYXJrZXIuZ2V0UG9zaXRpb24oKTtcclxuICAgICAgICAgICAgZ2MuZ2V0QWRkcmVzcyhwb2ludCk7XHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIGdjLmdldEFkZHJlc3MocG9pbnQpO1xyXG5cclxuICAgICAgICB0aGlzLmJpbmRFdmVudHMoKTtcclxuXHJcbiAgICAgICAgdGhpcy5pbmZvV2luZG93ID0gbmV3IHFxLm1hcHMuSW5mb1dpbmRvdyh7bWFwOiBtYXB9KTtcclxuXHJcbiAgICAgICAgdGhpcy5pc3Nob3cgPSB0cnVlO1xyXG4gICAgICAgIGlmICh0aGlzLnRvc2hvdykge1xyXG4gICAgICAgICAgICB0aGlzLnNob3dJbmZvKCk7XHJcbiAgICAgICAgICAgIHRoaXMudG9zaG93ID0gZmFsc2U7XHJcbiAgICAgICAgfVxyXG4gICAgfTtcclxuXHJcbiAgICBUZW5jZW50TWFwLnByb3RvdHlwZS5zaG93SW5mbyA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICBpZiAodGhpcy5pc2hpZGUpIHJldHVybjtcclxuICAgICAgICBpZiAoIXRoaXMuaXNzaG93KSB7XHJcbiAgICAgICAgICAgIHRoaXMudG9zaG93ID0gdHJ1ZTtcclxuICAgICAgICAgICAgcmV0dXJuO1xyXG4gICAgICAgIH1cclxuICAgICAgICB0aGlzLmluZm9XaW5kb3cub3BlbigpO1xyXG4gICAgICAgIHRoaXMuc2V0SW5mb0NvbnRlbnQoKTtcclxuICAgICAgICB0aGlzLmluZm9XaW5kb3cuc2V0UG9zaXRpb24odGhpcy5tYXJrZXIuZ2V0UG9zaXRpb24oKSk7XHJcbiAgICB9O1xyXG5cclxuICAgIFRlbmNlbnRNYXAucHJvdG90eXBlLmdldEFkZHJlc3MgPSBmdW5jdGlvbiAocnMpIHtcclxuICAgICAgICBpZihycyAmJiBycy5kZXRhaWwpIHtcclxuICAgICAgICAgICAgcmV0dXJuIHJzLmRldGFpbC5hZGRyZXNzO1xyXG4gICAgICAgIH1cclxuICAgIH07XHJcblxyXG4gICAgVGVuY2VudE1hcC5wcm90b3R5cGUuc2V0TG9jYXRlID0gZnVuY3Rpb24gKGFkZHJlc3MpIHtcclxuICAgICAgICAvLyDliJvlu7rlnLDlnYDop6PmnpDlmajlrp7kvotcclxuICAgICAgICB2YXIgc2VsZiA9IHRoaXM7XHJcbiAgICAgICAgdmFyIG15R2VvID0gbmV3IHFxLm1hcHMuR2VvY29kZXIoe1xyXG4gICAgICAgICAgICBjb21wbGV0ZTogZnVuY3Rpb24gKHJlc3VsdCkge1xyXG4gICAgICAgICAgICAgICAgaWYocmVzdWx0ICYmIHJlc3VsdC5kZXRhaWwgJiYgcmVzdWx0LmRldGFpbC5sb2NhdGlvbil7XHJcbiAgICAgICAgICAgICAgICAgICAgdmFyIHBvaW50PXJlc3VsdC5kZXRhaWwubG9jYXRpb247XHJcbiAgICAgICAgICAgICAgICAgICAgc2VsZi5tYXAuc2V0Q2VudGVyKHBvaW50KTtcclxuICAgICAgICAgICAgICAgICAgICBzZWxmLm1hcmtlci5zZXRQb3NpdGlvbihwb2ludCk7XHJcbiAgICAgICAgICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKHBvaW50LCByZXN1bHQpO1xyXG4gICAgICAgICAgICAgICAgfWVsc2V7XHJcbiAgICAgICAgICAgICAgICAgICAgdG9hc3RyLndhcm5pbmcoXCLlnLDlnYDkv6Hmga/kuI3mraPnoa7vvIzlrprkvY3lpLHotKVcIik7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgIGVycm9yOmZ1bmN0aW9uKHJlc3VsdCl7XHJcbiAgICAgICAgICAgICAgICB0b2FzdHIud2FybmluZyhcIuWcsOWdgOS/oeaBr+S4jeato+ehru+8jOWumuS9jeWksei0pVwiKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pO1xyXG4gICAgICAgIG15R2VvLmdldExvY2F0aW9uKGFkZHJlc3MpO1xyXG4gICAgfTtcclxuXHJcblxyXG4gICAgZnVuY3Rpb24gR2FvZGVNYXAoKSB7XHJcbiAgICAgICAgQmFzZU1hcC5jYWxsKHRoaXMsIFwiZ2FvZGVcIik7XHJcbiAgICAgICAgdGhpcy5pbmZvT3B0cyA9IHtcclxuICAgICAgICAgICAgd2lkdGg6IDI1MCwgICAgIC8v5L+h5oGv56qX5Y+j5a695bqmXHJcbiAgICAgICAgICAgIC8vICAgaGVpZ2h0OiAxMDAsICAgICAvL+S/oeaBr+eql+WPo+mrmOW6plxyXG4gICAgICAgICAgICB0aXRsZTogXCJcIiAgLy/kv6Hmga/nqpflj6PmoIfpophcclxuICAgICAgICB9O1xyXG4gICAgfVxyXG5cclxuICAgIEdhb2RlTWFwLnByb3RvdHlwZSA9IG5ldyBCYXNlTWFwKCk7XHJcbiAgICBHYW9kZU1hcC5wcm90b3R5cGUuY29uc3RydWN0b3IgPSBHYW9kZU1hcDtcclxuICAgIEdhb2RlTWFwLnByb3RvdHlwZS5pc0FQSVJlYWR5ID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHJldHVybiAhIXdpbmRvd1snQU1hcCddXHJcbiAgICB9O1xyXG5cclxuICAgIEdhb2RlTWFwLnByb3RvdHlwZS5zZXRNYXAgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgdmFyIHNlbGYgPSB0aGlzO1xyXG4gICAgICAgIGlmICh0aGlzLmlzc2hvdyB8fCB0aGlzLmlzaGlkZSkgcmV0dXJuO1xyXG4gICAgICAgIGlmICghdGhpcy5sb2FkQVBJKCkpIHJldHVybjtcclxuXHJcblxyXG4gICAgICAgIHZhciBtYXAgPSBzZWxmLm1hcCA9IG5ldyBBTWFwLk1hcCh0aGlzLm1hcGJveC5hdHRyKCdpZCcpLCB7XHJcbiAgICAgICAgICAgIHJlc2l6ZUVuYWJsZTogdHJ1ZSxcclxuICAgICAgICAgICAgZHJhZ0VuYWJsZTogdHJ1ZSxcclxuICAgICAgICAgICAgem9vbTogMTNcclxuICAgICAgICB9KTtcclxuICAgICAgICBtYXAucGx1Z2luKFtcIkFNYXAuVG9vbEJhclwiLCBcIkFNYXAuU2NhbGVcIiwgXCJBTWFwLk92ZXJWaWV3XCJdLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIG1hcC5hZGRDb250cm9sKG5ldyBBTWFwLlRvb2xCYXIoKSk7XHJcbiAgICAgICAgICAgIG1hcC5hZGRDb250cm9sKG5ldyBBTWFwLlNjYWxlKCkpO1xyXG4gICAgICAgICAgICBtYXAuYWRkQ29udHJvbChuZXcgQU1hcC5PdmVyVmlldygpKTtcclxuICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgJCgnW25hbWU9dHh0TGFuZ10nKS51bmJpbmQoKS5vbignY2hhbmdlJywgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICB2YXIgbGFuZyA9ICQodGhpcykudmFsKCk7XHJcbiAgICAgICAgICAgIGlmIChsYW5nKSBtYXAuc2V0TGFuZyhsYW5nKTtcclxuICAgICAgICB9KS50cmlnZ2VyKCdjaGFuZ2UnKTtcclxuXHJcblxyXG4gICAgICAgIC8v6I635Y+W57uP57qs5bqm5Z2Q5qCH5YC8XHJcbiAgICAgICAgdmFyIHBvaW50ID0gbmV3IEFNYXAuTG5nTGF0KHRoaXMubG9jYXRlLmxuZywgdGhpcy5sb2NhdGUubGF0KTtcclxuICAgICAgICBtYXAuc2V0Q2VudGVyKHBvaW50KTtcclxuXHJcbiAgICAgICAgdGhpcy5tYXJrZXIgPSBuZXcgQU1hcC5NYXJrZXIoe3Bvc2l0aW9uOiBwb2ludCwgbWFwOiBtYXB9KTsgLy/liJ3lp4vljJblnLDlm77moIforrBcclxuICAgICAgICB0aGlzLm1hcmtlci5zZXREcmFnZ2FibGUodHJ1ZSk7IC8v5qCH6K6w5byA5ZCv5ouW5ou9XHJcblxyXG5cclxuICAgICAgICB0aGlzLmluZm9XaW5kb3cgPSBuZXcgQU1hcC5JbmZvV2luZG93KCk7XHJcbiAgICAgICAgdGhpcy5pbmZvV2luZG93Lm9wZW4obWFwLCBwb2ludCk7XHJcblxyXG4gICAgICAgIG1hcC5wbHVnaW4oW1wiQU1hcC5HZW9jb2RlclwiXSwgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICB2YXIgZ2MgPSBuZXcgQU1hcC5HZW9jb2RlcigpOyAvL+WcsOWdgOino+aekOexu1xyXG4gICAgICAgICAgICAvL+a3u+WKoOagh+iusOaLluaLveebkeWQrFxyXG4gICAgICAgICAgICBzZWxmLm1hcmtlci5vbihcImRyYWdlbmRcIiwgZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICAgICAgICAgIC8v6I635Y+W5Zyw5Z2A5L+h5oGvXHJcbiAgICAgICAgICAgICAgICBnYy5nZXRBZGRyZXNzKGUubG5nbGF0LCBmdW5jdGlvbiAoc3QsIHJzKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKGUubG5nbGF0LCBycyk7XHJcbiAgICAgICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgfSk7XHJcblxyXG4gICAgICAgICAgICAvL+a3u+WKoOagh+iusOeCueWHu+ebkeWQrFxyXG4gICAgICAgICAgICBzZWxmLm1hcmtlci5vbihcImNsaWNrXCIsIGZ1bmN0aW9uIChlKSB7XHJcbiAgICAgICAgICAgICAgICBnYy5nZXRBZGRyZXNzKGUubG5nbGF0LCBmdW5jdGlvbiAoc3QsIHJzKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKGUubG5nbGF0LCBycyk7XHJcbiAgICAgICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgfSk7XHJcblxyXG4gICAgICAgICAgICBnYy5nZXRBZGRyZXNzKHBvaW50LCBmdW5jdGlvbiAoc3QsIHJzKSB7XHJcbiAgICAgICAgICAgICAgICBzZWxmLnNob3dMb2NhdGlvbkluZm8ocG9pbnQsIHJzKTtcclxuICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIHRoaXMuYmluZEV2ZW50cygpO1xyXG5cclxuICAgICAgICB0aGlzLmlzc2hvdyA9IHRydWU7XHJcbiAgICAgICAgaWYgKHRoaXMudG9zaG93KSB7XHJcbiAgICAgICAgICAgIHRoaXMuc2hvd0luZm8oKTtcclxuICAgICAgICAgICAgdGhpcy50b3Nob3cgPSBmYWxzZTtcclxuICAgICAgICB9XHJcbiAgICB9O1xyXG5cclxuICAgIEdhb2RlTWFwLnByb3RvdHlwZS5zaG93SW5mbyA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICBpZiAodGhpcy5pc2hpZGUpIHJldHVybjtcclxuICAgICAgICBpZiAoIXRoaXMuaXNzaG93KSB7XHJcbiAgICAgICAgICAgIHRoaXMudG9zaG93ID0gdHJ1ZTtcclxuICAgICAgICAgICAgcmV0dXJuO1xyXG4gICAgICAgIH1cclxuICAgICAgICB0aGlzLnNldEluZm9Db250ZW50KCk7XHJcbiAgICAgICAgdGhpcy5pbmZvV2luZG93LnNldFBvc2l0aW9uKHRoaXMubWFya2VyLmdldFBvc2l0aW9uKCkpO1xyXG4gICAgfTtcclxuXHJcbiAgICBHYW9kZU1hcC5wcm90b3R5cGUuZ2V0QWRkcmVzcyA9IGZ1bmN0aW9uIChycykge1xyXG4gICAgICAgIHJldHVybiBycy5yZWdlb2NvZGUuZm9ybWF0dGVkQWRkcmVzcztcclxuICAgIH07XHJcblxyXG4gICAgR2FvZGVNYXAucHJvdG90eXBlLnNldExvY2F0ZSA9IGZ1bmN0aW9uIChhZGRyZXNzKSB7XHJcbiAgICAgICAgLy8g5Yib5bu65Zyw5Z2A6Kej5p6Q5Zmo5a6e5L6LXHJcbiAgICAgICAgdmFyIG15R2VvID0gbmV3IEFNYXAuR2VvY29kZXIoKTtcclxuICAgICAgICB2YXIgc2VsZiA9IHRoaXM7XHJcbiAgICAgICAgbXlHZW8uZ2V0UG9pbnQoYWRkcmVzcywgZnVuY3Rpb24gKHBvaW50KSB7XHJcbiAgICAgICAgICAgIGlmIChwb2ludCkge1xyXG4gICAgICAgICAgICAgICAgc2VsZi5tYXAuY2VudGVyQW5kWm9vbShwb2ludCwgMTEpO1xyXG4gICAgICAgICAgICAgICAgc2VsZi5tYXJrZXIuc2V0UG9zaXRpb24ocG9pbnQpO1xyXG4gICAgICAgICAgICAgICAgbXlHZW8uZ2V0TG9jYXRpb24ocG9pbnQsIGZ1bmN0aW9uIChycykge1xyXG4gICAgICAgICAgICAgICAgICAgIHNlbGYuc2hvd0xvY2F0aW9uSW5mbyhwb2ludCwgcnMpO1xyXG4gICAgICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICB0b2FzdHIud2FybmluZyhcIuWcsOWdgOS/oeaBr+S4jeato+ehru+8jOWumuS9jeWksei0pVwiKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0sICcnKTtcclxuICAgIH07XHJcblxyXG4gICAgd2luZG93LkluaXRNYXA9SW5pdE1hcDtcclxufSkod2luZG93LGpRdWVyeSk7Iiwid2luZG93LnN0b3BfYWpheD1mYWxzZTtcclxualF1ZXJ5KGZ1bmN0aW9uICgkKSB7XHJcbiAgICAvL+mrmOS6ruW9k+WJjemAieS4reeahOWvvOiIqlxyXG4gICAgdmFyIGJyZWFkID0gJChcIi5icmVhZGNydW1iXCIpO1xyXG4gICAgdmFyIG1lbnUgPSBicmVhZC5kYXRhKCdtZW51Jyk7XHJcbiAgICBpZiAobWVudSkge1xyXG4gICAgICAgIHZhciBsaW5rID0gJCgnLnNpZGUtbmF2IGFbZGF0YS1rZXk9JyArIG1lbnUgKyAnXScpO1xyXG5cclxuICAgICAgICB2YXIgaHRtbCA9IFtdO1xyXG4gICAgICAgIGlmIChsaW5rLmxlbmd0aCA+IDApIHtcclxuICAgICAgICAgICAgaWYgKGxpbmsuaXMoJy5tZW51X3RvcCcpKSB7XHJcbiAgICAgICAgICAgICAgICBodG1sLnB1c2goJzxsaSBjbGFzcz1cImJyZWFkY3J1bWItaXRlbVwiPjxhIGhyZWY9XCJqYXZhc2NyaXB0OlwiPjxpIGNsYXNzPVwiJyArIGxpbmsuZmluZCgnaScpLmF0dHIoJ2NsYXNzJykgKyAnXCI+PC9pPiZuYnNwOycgKyBsaW5rLnRleHQoKSArICc8L2E+PC9saT4nKTtcclxuICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgIHZhciBwYXJlbnQgPSBsaW5rLnBhcmVudHMoJy5jb2xsYXBzZScpLmVxKDApO1xyXG4gICAgICAgICAgICAgICAgcGFyZW50LmFkZENsYXNzKCdzaG93Jyk7XHJcbiAgICAgICAgICAgICAgICBsaW5rLmFkZENsYXNzKFwiYWN0aXZlXCIpO1xyXG4gICAgICAgICAgICAgICAgdmFyIHRvcG1lbnUgPSBwYXJlbnQuc2libGluZ3MoJy5jYXJkLWhlYWRlcicpLmZpbmQoJ2EubWVudV90b3AnKTtcclxuICAgICAgICAgICAgICAgIGh0bWwucHVzaCgnPGxpIGNsYXNzPVwiYnJlYWRjcnVtYi1pdGVtXCI+PGEgaHJlZj1cImphdmFzY3JpcHQ6XCI+PGkgY2xhc3M9XCInICsgdG9wbWVudS5maW5kKCdpJykuYXR0cignY2xhc3MnKSArICdcIj48L2k+Jm5ic3A7JyArIHRvcG1lbnUudGV4dCgpICsgJzwvYT48L2xpPicpO1xyXG4gICAgICAgICAgICAgICAgaHRtbC5wdXNoKCc8bGkgY2xhc3M9XCJicmVhZGNydW1iLWl0ZW1cIj48YSBocmVmPVwiamF2YXNjcmlwdDpcIj4nICsgbGluay50ZXh0KCkgKyAnPC9hPjwvbGk+Jyk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9XHJcbiAgICAgICAgdmFyIHRpdGxlID0gYnJlYWQuZGF0YSgndGl0bGUnKTtcclxuICAgICAgICBpZiAodGl0bGUpIHtcclxuICAgICAgICAgICAgaHRtbC5wdXNoKCc8bGkgY2xhc3M9XCJicmVhZGNydW1iLWl0ZW0gYWN0aXZlXCIgYXJpYS1jdXJyZW50PVwicGFnZVwiPicgKyB0aXRsZSArICc8L2xpPicpO1xyXG4gICAgICAgIH1cclxuICAgICAgICBicmVhZC5odG1sKGh0bWwuam9pbihcIlxcblwiKSk7XHJcbiAgICB9XHJcblxyXG4gICAgLy/lhajpgInjgIHlj43pgInmjInpkq5cclxuICAgICQoJy5jaGVja2FsbC1idG4nKS5jbGljayhmdW5jdGlvbiAoZSkge1xyXG4gICAgICAgIHZhciB0YXJnZXQgPSAkKHRoaXMpLmRhdGEoJ3RhcmdldCcpO1xyXG4gICAgICAgIGlmICghdGFyZ2V0KSB0YXJnZXQgPSAnaWQnO1xyXG4gICAgICAgIHZhciBpZHMgPSAkKCdbbmFtZT0nICsgdGFyZ2V0ICsgJ10nKTtcclxuICAgICAgICBpZiAoJCh0aGlzKS5pcygnLmFjdGl2ZScpKSB7XHJcbiAgICAgICAgICAgIGlkcy5wcm9wKCdjaGVja2VkJywgZmFsc2UpO1xyXG4gICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgIGlkcy5wcm9wKCdjaGVja2VkJywgdHJ1ZSk7XHJcbiAgICAgICAgfVxyXG4gICAgfSk7XHJcbiAgICAkKCcuY2hlY2tyZXZlcnNlLWJ0bicpLmNsaWNrKGZ1bmN0aW9uIChlKSB7XHJcbiAgICAgICAgdmFyIHRhcmdldCA9ICQodGhpcykuZGF0YSgndGFyZ2V0Jyk7XHJcbiAgICAgICAgaWYgKCF0YXJnZXQpIHRhcmdldCA9ICdpZCc7XHJcbiAgICAgICAgdmFyIGlkcyA9ICQoJ1tuYW1lPScgKyB0YXJnZXQgKyAnXScpO1xyXG4gICAgICAgIGZvciAodmFyIGkgPSAwOyBpIDwgaWRzLmxlbmd0aDsgaSsrKSB7XHJcbiAgICAgICAgICAgIGlmIChpZHNbaV0uY2hlY2tlZCkge1xyXG4gICAgICAgICAgICAgICAgaWRzLmVxKGkpLnByb3AoJ2NoZWNrZWQnLCBmYWxzZSk7XHJcbiAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICBpZHMuZXEoaSkucHJvcCgnY2hlY2tlZCcsIHRydWUpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfVxyXG4gICAgfSk7XHJcbiAgICAvL+aTjeS9nOaMiemSrlxyXG4gICAgJCgnLmFjdGlvbi1idG4nKS5jbGljayhmdW5jdGlvbiAoZSkge1xyXG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcclxuICAgICAgICB2YXIgYWN0aW9uID0gJCh0aGlzKS5kYXRhKCdhY3Rpb24nKTtcclxuICAgICAgICBpZiAoIWFjdGlvbikge1xyXG4gICAgICAgICAgICByZXR1cm4gdG9hc3RyLmVycm9yKCfmnKrnn6Xmk43kvZwnKTtcclxuICAgICAgICB9XHJcbiAgICAgICAgYWN0aW9uID0gJ2FjdGlvbicgKyBhY3Rpb24ucmVwbGFjZSgvXlthLXpdLywgZnVuY3Rpb24gKGxldHRlcikge1xyXG4gICAgICAgICAgICByZXR1cm4gbGV0dGVyLnRvVXBwZXJDYXNlKCk7XHJcbiAgICAgICAgfSk7XHJcbiAgICAgICAgaWYgKCF3aW5kb3dbYWN0aW9uXSB8fCB0eXBlb2Ygd2luZG93W2FjdGlvbl0gIT09ICdmdW5jdGlvbicpIHtcclxuICAgICAgICAgICAgcmV0dXJuIHRvYXN0ci5lcnJvcign5pyq55+l5pON5L2cJyk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHZhciBuZWVkQ2hlY2tzID0gJCh0aGlzKS5kYXRhKCduZWVkQ2hlY2tzJyk7XHJcbiAgICAgICAgaWYgKG5lZWRDaGVja3MgPT09IHVuZGVmaW5lZCkgbmVlZENoZWNrcyA9IHRydWU7XHJcbiAgICAgICAgaWYgKG5lZWRDaGVja3MpIHtcclxuICAgICAgICAgICAgdmFyIHRhcmdldCA9ICQodGhpcykuZGF0YSgndGFyZ2V0Jyk7XHJcbiAgICAgICAgICAgIGlmICghdGFyZ2V0KSB0YXJnZXQgPSAnaWQnO1xyXG4gICAgICAgICAgICB2YXIgaWRzID0gJCgnW25hbWU9JyArIHRhcmdldCArICddOmNoZWNrZWQnKTtcclxuICAgICAgICAgICAgaWYgKGlkcy5sZW5ndGggPCAxKSB7XHJcbiAgICAgICAgICAgICAgICByZXR1cm4gdG9hc3RyLndhcm5pbmcoJ+ivt+mAieaLqemcgOimgeaTjeS9nOeahOmhueebricpO1xyXG4gICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgdmFyIGlkY2hlY2tzID0gW107XHJcbiAgICAgICAgICAgICAgICBmb3IgKHZhciBpID0gMDsgaSA8IGlkcy5sZW5ndGg7IGkrKykge1xyXG4gICAgICAgICAgICAgICAgICAgIGlkY2hlY2tzLnB1c2goaWRzLmVxKGkpLnZhbCgpKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIHdpbmRvd1thY3Rpb25dKGlkY2hlY2tzKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgIHdpbmRvd1thY3Rpb25dKCk7XHJcbiAgICAgICAgfVxyXG4gICAgfSk7XHJcblxyXG4gICAgLy/lvILmraXmmL7npLrotYTmlpnpk77mjqVcclxuICAgICQoJ2FbcmVsPWFqYXhdJykuY2xpY2soZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XHJcbiAgICAgICAgdmFyIHNlbGYgPSAkKHRoaXMpO1xyXG4gICAgICAgIHZhciB0aXRsZSA9ICQodGhpcykuZGF0YSgndGl0bGUnKTtcclxuICAgICAgICBpZiAoIXRpdGxlKSB0aXRsZSA9ICQodGhpcykudGV4dCgpO1xyXG4gICAgICAgIHZhciBkbGcgPSBuZXcgRGlhbG9nKHtcclxuICAgICAgICAgICAgYnRuczogWyfnoa7lrponXSxcclxuICAgICAgICAgICAgb25zaG93OiBmdW5jdGlvbiAoYm9keSkge1xyXG4gICAgICAgICAgICAgICAgJC5hamF4KHtcclxuICAgICAgICAgICAgICAgICAgICB1cmw6IHNlbGYuYXR0cignaHJlZicpLFxyXG4gICAgICAgICAgICAgICAgICAgIHN1Y2Nlc3M6IGZ1bmN0aW9uICh0ZXh0KSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGJvZHkuaHRtbCh0ZXh0KTtcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLnNob3coJzxwIGNsYXNzPVwibG9hZGluZ1wiPicrbGFuZygnbG9hZGluZy4uLicpKyc8L3A+JywgdGl0bGUpO1xyXG5cclxuICAgIH0pO1xyXG5cclxuICAgIC8v56Gu6K6k5pON5L2cXHJcbiAgICAkKCcubGluay1jb25maXJtJykuY2xpY2soZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XHJcbiAgICAgICAgZS5zdG9wUHJvcGFnYXRpb24oKTtcclxuICAgICAgICB2YXIgdGV4dD0kKHRoaXMpLmRhdGEoJ2NvbmZpcm0nKTtcclxuICAgICAgICB2YXIgdXJsPSQodGhpcykuZGF0YSgnaHJlZicpO1xyXG4gICAgICAgIGlmKCF0ZXh0KXRleHQ9bGFuZygnQ29uZmlybSBvcGVyYXRpb24/Jyk7XHJcblxyXG4gICAgICAgIGRpYWxvZy5jb25maXJtKHRleHQsZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAkLmFqYXgoe1xyXG4gICAgICAgICAgICAgICAgdXJsOnVybCxcclxuICAgICAgICAgICAgICAgIGRhdGFUeXBlOidKU09OJyxcclxuICAgICAgICAgICAgICAgIHN1Y2Nlc3M6ZnVuY3Rpb24gKGpzb24pIHtcclxuICAgICAgICAgICAgICAgICAgICBkaWFsb2cuYWxlcnQoanNvbi5tc2cpO1xyXG4gICAgICAgICAgICAgICAgICAgIGlmKGpzb24uY29kZT09MSl7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmKGpzb24udXJsKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxvY2F0aW9uLmhyZWY9anNvbi51cmw7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbG9jYXRpb24ucmVsb2FkKCk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAgICAgZXJyb3I6ZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAgICAgICAgIGRpYWxvZy5hbGVydChsYW5nKCdTZXJ2ZXIgZXJyb3IuJykpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9KVxyXG4gICAgICAgIH0pO1xyXG4gICAgfSk7XHJcblxyXG4gICAgJCgnLmltZy12aWV3JykuY2xpY2soZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XHJcbiAgICAgICAgZS5zdG9wUHJvcGFnYXRpb24oKTtcclxuICAgICAgICB2YXIgdXJsPSQodGhpcykuYXR0cignaHJlZicpO1xyXG4gICAgICAgIGlmKCF1cmwpdXJsPSQodGhpcykuZGF0YSgnaW1nJyk7XHJcbiAgICAgICAgZGlhbG9nLmFsZXJ0KCc8YSBocmVmPVwiJyt1cmwrJ1wiIGNsYXNzPVwiZC1ibG9jayB0ZXh0LWNlbnRlclwiIHRhcmdldD1cIl9ibGFua1wiPjxpbWcgY2xhc3M9XCJpbWctZmx1aWRcIiBzcmM9XCInK3VybCsnXCIgLz48L2E+PGRpdiBjbGFzcz1cInRleHQtbXV0ZWQgdGV4dC1jZW50ZXJcIj7ngrnlh7vlm77niYflnKjmlrDpobXpnaLmlL7lpKfmn6XnnIs8L2Rpdj4nLG51bGwsJ+afpeeci+WbvueJhycpO1xyXG4gICAgfSk7XHJcblxyXG4gICAgJCgnLm5hdi10YWJzIGEnKS5jbGljayhmdW5jdGlvbiAoZSkge1xyXG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcclxuICAgICAgICAkKHRoaXMpLnRhYignc2hvdycpO1xyXG4gICAgfSk7XHJcblxyXG4gICAgLy/kuIrkvKDmoYZcclxuICAgICQoJy5jdXN0b20tZmlsZSAuY3VzdG9tLWZpbGUtaW5wdXQnKS5vbignY2hhbmdlJywgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHZhciBsYWJlbCA9ICQodGhpcykucGFyZW50cygnLmN1c3RvbS1maWxlJykuZmluZCgnLmN1c3RvbS1maWxlLWxhYmVsJyk7XHJcbiAgICAgICAgbGFiZWwudGV4dCgkKHRoaXMpLnZhbCgpKTtcclxuICAgIH0pO1xyXG5cclxuICAgIC8v6KGo5Y2VQWpheOaPkOS6pFxyXG4gICAgJCgnLmJ0bi1wcmltYXJ5W3R5cGU9c3VibWl0XScpLmNsaWNrKGZ1bmN0aW9uIChlKSB7XHJcbiAgICAgICAgdmFyIGZvcm0gPSAkKHRoaXMpLnBhcmVudHMoJ2Zvcm0nKTtcclxuICAgICAgICBpZihmb3JtLmlzKCcubm9hamF4JykpcmV0dXJuIHRydWU7XHJcbiAgICAgICAgdmFyIGJ0biA9IHRoaXM7XHJcblxyXG4gICAgICAgIHZhciBpc2J0bj0kKGJ0bikucHJvcCgndGFnTmFtZScpLnRvVXBwZXJDYXNlKCk9PSdCVVRUT04nO1xyXG4gICAgICAgIHZhciBvcmlnVGV4dD1pc2J0bj8kKGJ0bikudGV4dCgpOiQoYnRuKS52YWwoKTtcclxuICAgICAgICB2YXIgb3B0aW9ucyA9IHtcclxuICAgICAgICAgICAgdXJsOiAkKGZvcm0pLmF0dHIoJ2FjdGlvbicpLFxyXG4gICAgICAgICAgICB0eXBlOiAnUE9TVCcsXHJcbiAgICAgICAgICAgIGRhdGFUeXBlOiAnSlNPTicsXHJcbiAgICAgICAgICAgIHN1Y2Nlc3M6IGZ1bmN0aW9uIChqc29uKSB7XHJcbiAgICAgICAgICAgICAgICB3aW5kb3cuc3RvcF9hamF4PWZhbHNlO1xyXG4gICAgICAgICAgICAgICAgaXNidG4/JChidG4pLnRleHQob3JpZ1RleHQpOiQoYnRuKS52YWwob3JpZ1RleHQpO1xyXG4gICAgICAgICAgICAgICAgaWYgKGpzb24uY29kZSA9PSAxKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgZGlhbG9nLmFsZXJ0KGpzb24ubXNnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChqc29uLnVybCkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbG9jYXRpb24uaHJlZiA9IGpzb24udXJsO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbG9jYXRpb24ucmVsb2FkKCk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICAgICAgdG9hc3RyLndhcm5pbmcoanNvbi5tc2cpO1xyXG4gICAgICAgICAgICAgICAgICAgICQoYnRuKS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICBlcnJvcjogZnVuY3Rpb24gKHhocikge1xyXG4gICAgICAgICAgICAgICAgd2luZG93LnN0b3BfYWpheD1mYWxzZTtcclxuICAgICAgICAgICAgICAgIGlzYnRuPyQoYnRuKS50ZXh0KG9yaWdUZXh0KTokKGJ0bikudmFsKG9yaWdUZXh0KTtcclxuICAgICAgICAgICAgICAgICQoYnRuKS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xyXG4gICAgICAgICAgICAgICAgdG9hc3RyLmVycm9yKCfmnI3liqHlmajlpITnkIbplJnor68nKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH07XHJcbiAgICAgICAgaWYgKGZvcm0uYXR0cignZW5jdHlwZScpID09PSAnbXVsdGlwYXJ0L2Zvcm0tZGF0YScpIHtcclxuICAgICAgICAgICAgaWYgKCFGb3JtRGF0YSkge1xyXG4gICAgICAgICAgICAgICAgd2luZG93LnN0b3BfYWpheD10cnVlO1xyXG4gICAgICAgICAgICAgICAgcmV0dXJuIHRydWU7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgb3B0aW9ucy5kYXRhID0gbmV3IEZvcm1EYXRhKGZvcm1bMF0pO1xyXG4gICAgICAgICAgICBvcHRpb25zLmNhY2hlID0gZmFsc2U7XHJcbiAgICAgICAgICAgIG9wdGlvbnMucHJvY2Vzc0RhdGEgPSBmYWxzZTtcclxuICAgICAgICAgICAgb3B0aW9ucy5jb250ZW50VHlwZSA9IGZhbHNlO1xyXG4gICAgICAgICAgICBvcHRpb25zLnhocj0gZnVuY3Rpb24oKSB7IC8v55So5Lul5pi+56S65LiK5Lyg6L+b5bqmXHJcbiAgICAgICAgICAgICAgICB2YXIgeGhyID0gJC5hamF4U2V0dGluZ3MueGhyKCk7XHJcbiAgICAgICAgICAgICAgICBpZiAoeGhyLnVwbG9hZCkge1xyXG4gICAgICAgICAgICAgICAgICAgIHhoci51cGxvYWQuYWRkRXZlbnRMaXN0ZW5lcigncHJvZ3Jlc3MnLCBmdW5jdGlvbihldmVudCkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgcGVyY2VudCA9IE1hdGguZmxvb3IoZXZlbnQubG9hZGVkIC8gZXZlbnQudG90YWwgKiAxMDApO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAkKGJ0bikudGV4dChvcmlnVGV4dCsnICAoJytwZXJjZW50KyclKScpO1xyXG4gICAgICAgICAgICAgICAgICAgIH0sIGZhbHNlKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIHJldHVybiB4aHI7XHJcbiAgICAgICAgICAgIH07XHJcbiAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgb3B0aW9ucy5kYXRhID0gJChmb3JtKS5zZXJpYWxpemUoKTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcclxuICAgICAgICAkKHRoaXMpLmF0dHIoJ2Rpc2FibGVkJywgdHJ1ZSk7XHJcbiAgICAgICAgd2luZG93LnN0b3BfYWpheD10cnVlO1xyXG4gICAgICAgICQuYWpheChvcHRpb25zKTtcclxuICAgIH0pO1xyXG5cclxuICAgICQoJy5waWNrdXNlcicpLmNsaWNrKGZ1bmN0aW9uIChlKSB7XHJcbiAgICAgICAgdmFyIGdyb3VwID0gJCh0aGlzKS5wYXJlbnRzKCcuaW5wdXQtZ3JvdXAnKTtcclxuICAgICAgICB2YXIgaWRlbGUgPSBncm91cC5maW5kKCdbbmFtZT1tZW1iZXJfaWRdJyk7XHJcbiAgICAgICAgdmFyIGluZm9lbGUgPSBncm91cC5maW5kKCdbbmFtZT1tZW1iZXJfaW5mb10nKTtcclxuICAgICAgICBkaWFsb2cucGlja1VzZXIoIGZ1bmN0aW9uICh1c2VyKSB7XHJcbiAgICAgICAgICAgIGlkZWxlLnZhbCh1c2VyLmlkKTtcclxuICAgICAgICAgICAgaW5mb2VsZS52YWwoJ1snICsgdXNlci5pZCArICddICcgKyB1c2VyLnVzZXJuYW1lICsgKHVzZXIubW9iaWxlID8gKCcgLyAnICsgdXNlci5tb2JpbGUpIDogJycpKTtcclxuICAgICAgICB9LCAkKHRoaXMpLmRhdGEoJ2ZpbHRlcicpKTtcclxuICAgIH0pO1xyXG4gICAgJCgnLnBpY2stbG9jYXRlJykuY2xpY2soZnVuY3Rpb24oZSl7XHJcbiAgICAgICAgdmFyIGdyb3VwPSQodGhpcykucGFyZW50cygnLmlucHV0LWdyb3VwJyk7XHJcbiAgICAgICAgdmFyIGlkZWxlPWdyb3VwLmZpbmQoJ2lucHV0W3R5cGU9dGV4dF0nKTtcclxuICAgICAgICBkaWFsb2cucGlja0xvY2F0ZSgncXEnLGZ1bmN0aW9uKGxvY2F0ZSl7XHJcbiAgICAgICAgICAgIGlkZWxlLnZhbChsb2NhdGUubG5nKycsJytsb2NhdGUubGF0KTtcclxuICAgICAgICB9LGlkZWxlLnZhbCgpKTtcclxuICAgIH0pO1xyXG59KTsiXX0=
