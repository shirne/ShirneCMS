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
        'header':true,
        'backdrop':true,
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
    if(!this.options.header){
        this.box.find('.modal-header').remove();
    }
    if(this.options.backdrop!==true){
        this.box.data('backdrop',this.options.backdrop);
    }
    if(!this.options.keyboard){
        this.box.data('keyboard',false);
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
    alert:function(message,callback,icon){
        var called=false;
        var iscallback=typeof callback=='function';
        if(callback && !iscallback){
            icon=callback;
        }
        var iconMap= {
            'success':'checkmark-circle',
            'info': 'information-circle',
            'warning':'alert',
            'error':'remove-circle'
        };
        var color='primary';
        if(!icon)icon='information-circle';
        else if(iconMap[icon]){
            color=icon=='error'?'danger':icon;
            icon=iconMap[icon];
        }
        var html='<div class="row" style="align-items: center;"><div class="col-3 text-right"><i class="ion-md-{@icon} text-{@color}" style="font-size:3em;"></i> </div><div class="col-9" >{@message}</div> </div>'.compile({
            message:message,
            icon:icon,
            color:color
        });
        return new Dialog({
            btns:'确定',
            header:false,
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
        }).show(html,'');
    },
    confirm:function(message,confirm,cancel,icon){
        var called=false;
        if(typeof confirm=='string'){
            icon=confirm;
            if(typeof cancel=='function'){
                confirm=cancel;
                cancel=null;
            }
        }else if(typeof calcel=='string'){
            icon=cancel;
        }
        var iconMap= {
            'success':'checkmark-circle',
            'info': 'information-circle',
            'warning':'alert',
            'error':'remove-circle'
        };
        var color='primary';
        if(!icon)icon='information-circle';
        else if(iconMap[icon]){
            color=icon=='error'?'danger':icon;
            icon=iconMap[icon];
        }
        var html='<div class="row" style="align-items: center;"><div class="col-3 text-right"><i class="ion-md-{@icon} text-{@color}" style="font-size:3em;"></i> </div><div class="col-9" >{@message}</div> </div>'.compile({
            message:message,
            icon:icon,
            color:color
        });
        return new Dialog({
            'header':false,
            'backdrop':'static',
            'onsure':function(){
                if(confirm && typeof confirm=='function'){
                    called=true;
                    return confirm();
                }
            },
            'onhide':function () {
                if(called==false && typeof cancel=='function'){
                    return cancel();
                }
            }
        }).show(html);
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
            'backdrop':'static',
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
            'backdrop':'static',
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
            'backdrop':'static',
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
                                if(json.code===1){
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
            'backdrop':'static',
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
                body.find('.setToCenter').click(function (e) {
                    map.showAtCenter();
                })
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
            '<div class="float-right mt-2 mapactions"><a href="javascript:" class="setToCenter">定位到地图中心</a></div>' +
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
        'baidu': '//api.map.baidu.com/api?ak=rO9tOdEWFfvyGgDkiWqFjxK6&v=1.5&services=false&callback=',
        'google': '//maps.google.com/maps/api/js?key=AIzaSyB8lorvl6EtqIWz67bjWBruOhm9NYS1e24&callback=',
        'tencent': '//map.qq.com/api/js?v=2.exp&key=7I5BZ-QUE6R-JXLWV-WTVAA-CJMYF-7PBBI&callback=',
        'gaode': '//webapi.amap.com/maps?v=1.3&key=3ec311b5db0d597e79422eeb9a6d4449&callback='
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

    //全局操作按钮
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

    //表格行操作提示
    $('.operations .btn').tooltip();

    //异步显示资料链接
    $('a[rel=ajax]').click(function (e) {
        e.preventDefault();
        var self = $(this);
        var title = $(this).data('title');
        if (!title) title = $(this).text();
        if (!title) title = $(this).attr('title');
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
        var url=$(this).attr('href');
        text=text.replace(/(\\n|\n)+/g,"<br />");
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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNvbW1vbi5qcyIsInRlbXBsYXRlLmpzIiwiZGlhbG9nLmpzIiwianF1ZXJ5LnRhZy5qcyIsImRhdGV0aW1lLmluaXQuanMiLCJtYXAuanMiLCJiYWNrZW5kLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ2pFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQzVHQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQy9lQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQzFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDekVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ3huQkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSIsImZpbGUiOiJiYWNrZW5kLmpzIiwic291cmNlc0NvbnRlbnQiOlsiZnVuY3Rpb24gZGVsKG9iaixtc2cpIHtcclxuICAgIGRpYWxvZy5jb25maXJtKG1zZyxmdW5jdGlvbigpe1xyXG4gICAgICAgIGxvY2F0aW9uLmhyZWY9JChvYmopLmF0dHIoJ2hyZWYnKTtcclxuICAgIH0pO1xyXG4gICAgcmV0dXJuIGZhbHNlO1xyXG59XHJcblxyXG5mdW5jdGlvbiBsYW5nKGtleSkge1xyXG4gICAgaWYod2luZG93Lmxhbmd1YWdlICYmIHdpbmRvdy5sYW5ndWFnZVtrZXldKXtcclxuICAgICAgICByZXR1cm4gd2luZG93Lmxhbmd1YWdlW2tleV07XHJcbiAgICB9XHJcbiAgICByZXR1cm4ga2V5O1xyXG59XHJcblxyXG5mdW5jdGlvbiByYW5kb21TdHJpbmcobGVuLCBjaGFyU2V0KSB7XHJcbiAgICBjaGFyU2V0ID0gY2hhclNldCB8fCAnQUJDREVGR0hJSktMTU5PUFFSU1RVVldYWVphYmNkZWZnaGlqa2xtbm9wcXJzdHV2d3h5ejAxMjM0NTY3ODknO1xyXG4gICAgdmFyIHN0ciA9ICcnLGFsbExlbj1jaGFyU2V0Lmxlbmd0aDtcclxuICAgIGZvciAodmFyIGkgPSAwOyBpIDwgbGVuOyBpKyspIHtcclxuICAgICAgICB2YXIgcmFuZG9tUG96ID0gTWF0aC5mbG9vcihNYXRoLnJhbmRvbSgpICogYWxsTGVuKTtcclxuICAgICAgICBzdHIgKz0gY2hhclNldC5zdWJzdHJpbmcocmFuZG9tUG96LHJhbmRvbVBveisxKTtcclxuICAgIH1cclxuICAgIHJldHVybiBzdHI7XHJcbn1cclxuXHJcbmZ1bmN0aW9uIGNvcHlfb2JqKGFycil7XHJcbiAgICByZXR1cm4gSlNPTi5wYXJzZShKU09OLnN0cmluZ2lmeShhcnIpKTtcclxufVxyXG5cclxuZnVuY3Rpb24gaXNPYmplY3RWYWx1ZUVxdWFsKGEsIGIpIHtcclxuICAgIGlmKCFhICYmICFiKXJldHVybiB0cnVlO1xyXG4gICAgaWYoIWEgfHwgIWIpcmV0dXJuIGZhbHNlO1xyXG5cclxuICAgIC8vIE9mIGNvdXJzZSwgd2UgY2FuIGRvIGl0IHVzZSBmb3IgaW5cclxuICAgIC8vIENyZWF0ZSBhcnJheXMgb2YgcHJvcGVydHkgbmFtZXNcclxuICAgIHZhciBhUHJvcHMgPSBPYmplY3QuZ2V0T3duUHJvcGVydHlOYW1lcyhhKTtcclxuICAgIHZhciBiUHJvcHMgPSBPYmplY3QuZ2V0T3duUHJvcGVydHlOYW1lcyhiKTtcclxuXHJcbiAgICAvLyBJZiBudW1iZXIgb2YgcHJvcGVydGllcyBpcyBkaWZmZXJlbnQsXHJcbiAgICAvLyBvYmplY3RzIGFyZSBub3QgZXF1aXZhbGVudFxyXG4gICAgaWYgKGFQcm9wcy5sZW5ndGggIT0gYlByb3BzLmxlbmd0aCkge1xyXG4gICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgIH1cclxuXHJcbiAgICBmb3IgKHZhciBpID0gMDsgaSA8IGFQcm9wcy5sZW5ndGg7IGkrKykge1xyXG4gICAgICAgIHZhciBwcm9wTmFtZSA9IGFQcm9wc1tpXTtcclxuXHJcbiAgICAgICAgLy8gSWYgdmFsdWVzIG9mIHNhbWUgcHJvcGVydHkgYXJlIG5vdCBlcXVhbCxcclxuICAgICAgICAvLyBvYmplY3RzIGFyZSBub3QgZXF1aXZhbGVudFxyXG4gICAgICAgIGlmIChhW3Byb3BOYW1lXSAhPT0gYltwcm9wTmFtZV0pIHtcclxuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgIH1cclxuICAgIH1cclxuXHJcbiAgICAvLyBJZiB3ZSBtYWRlIGl0IHRoaXMgZmFyLCBvYmplY3RzXHJcbiAgICAvLyBhcmUgY29uc2lkZXJlZCBlcXVpdmFsZW50XHJcbiAgICByZXR1cm4gdHJ1ZTtcclxufVxyXG5cclxuZnVuY3Rpb24gYXJyYXlfY29tYmluZShhLGIpIHtcclxuICAgIHZhciBvYmo9e307XHJcbiAgICBmb3IodmFyIGk9MDtpPGEubGVuZ3RoO2krKyl7XHJcbiAgICAgICAgaWYoYi5sZW5ndGg8aSsxKWJyZWFrO1xyXG4gICAgICAgIG9ialthW2ldXT1iW2ldO1xyXG4gICAgfVxyXG4gICAgcmV0dXJuIG9iajtcclxufSIsIlxyXG5OdW1iZXIucHJvdG90eXBlLmZvcm1hdD1mdW5jdGlvbihmaXgpe1xyXG4gICAgaWYoZml4PT09dW5kZWZpbmVkKWZpeD0yO1xyXG4gICAgdmFyIG51bT10aGlzLnRvRml4ZWQoZml4KTtcclxuICAgIHZhciB6PW51bS5zcGxpdCgnLicpO1xyXG4gICAgdmFyIGZvcm1hdD1bXSxmPXpbMF0uc3BsaXQoJycpLGw9Zi5sZW5ndGg7XHJcbiAgICBmb3IodmFyIGk9MDtpPGw7aSsrKXtcclxuICAgICAgICBpZihpPjAgJiYgaSAlIDM9PTApe1xyXG4gICAgICAgICAgICBmb3JtYXQudW5zaGlmdCgnLCcpO1xyXG4gICAgICAgIH1cclxuICAgICAgICBmb3JtYXQudW5zaGlmdChmW2wtaS0xXSk7XHJcbiAgICB9XHJcbiAgICByZXR1cm4gZm9ybWF0LmpvaW4oJycpKyh6Lmxlbmd0aD09Mj8nLicrelsxXTonJyk7XHJcbn07XHJcbmlmKCFTdHJpbmcucHJvdG90eXBlLnRyaW0pe1xyXG4gICAgU3RyaW5nLnByb3RvdHlwZS50cmltPWZ1bmN0aW9uICgpIHtcclxuICAgICAgICByZXR1cm4gdGhpcy5yZXBsYWNlKC8oXlxccyt8XFxzKyQpL2csJycpO1xyXG4gICAgfVxyXG59XHJcblN0cmluZy5wcm90b3R5cGUuY29tcGlsZT1mdW5jdGlvbihkYXRhLGxpc3Qpe1xyXG5cclxuICAgIGlmKGxpc3Qpe1xyXG4gICAgICAgIHZhciB0ZW1wcz1bXTtcclxuICAgICAgICBmb3IodmFyIGkgaW4gZGF0YSl7XHJcbiAgICAgICAgICAgIHRlbXBzLnB1c2godGhpcy5jb21waWxlKGRhdGFbaV0pKTtcclxuICAgICAgICB9XHJcbiAgICAgICAgcmV0dXJuIHRlbXBzLmpvaW4oXCJcXG5cIik7XHJcbiAgICB9ZWxzZXtcclxuICAgICAgICByZXR1cm4gdGhpcy5yZXBsYWNlKC9cXHtpZlxccysoW15cXH1dKylcXH0oW1xcV1xcd10qKXtcXC9pZn0vZyxmdW5jdGlvbihhbGwsIGNvbmRpdGlvbiwgY29udCl7XHJcbiAgICAgICAgICAgIHZhciBvcGVyYXRpb247XHJcbiAgICAgICAgICAgIGlmKG9wZXJhdGlvbj1jb25kaXRpb24ubWF0Y2goL1xccysoPSt8PHw+KVxccysvKSl7XHJcbiAgICAgICAgICAgICAgICBvcGVyYXRpb249b3BlcmF0aW9uWzBdO1xyXG4gICAgICAgICAgICAgICAgdmFyIHBhcnQ9Y29uZGl0aW9uLnNwbGl0KG9wZXJhdGlvbik7XHJcbiAgICAgICAgICAgICAgICBpZihwYXJ0WzBdLmluZGV4T2YoJ0AnKT09PTApe1xyXG4gICAgICAgICAgICAgICAgICAgIHBhcnRbMF09ZGF0YVtwYXJ0WzBdLnJlcGxhY2UoJ0AnLCcnKV07XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICBpZihwYXJ0WzFdLmluZGV4T2YoJ0AnKT09PTApe1xyXG4gICAgICAgICAgICAgICAgICAgIHBhcnRbMV09ZGF0YVtwYXJ0WzFdLnJlcGxhY2UoJ0AnLCcnKV07XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICBvcGVyYXRpb249b3BlcmF0aW9uLnRyaW0oKTtcclxuICAgICAgICAgICAgICAgIHZhciByZXN1bHQ9ZmFsc2U7XHJcbiAgICAgICAgICAgICAgICBzd2l0Y2ggKG9wZXJhdGlvbil7XHJcbiAgICAgICAgICAgICAgICAgICAgY2FzZSAnPT0nOlxyXG4gICAgICAgICAgICAgICAgICAgICAgICByZXN1bHQgPSBwYXJ0WzBdID09IHBhcnRbMV07XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgICAgICAgICAgICAgIGNhc2UgJz09PSc6XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHJlc3VsdCA9IHBhcnRbMF0gPT09IHBhcnRbMV07XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgICAgICAgICAgICAgIGNhc2UgJz4nOlxyXG4gICAgICAgICAgICAgICAgICAgICAgICByZXN1bHQgPSBwYXJ0WzBdID4gcGFydFsxXTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgICAgICAgICAgY2FzZSAnPCc6XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHJlc3VsdCA9IHBhcnRbMF0gPCBwYXJ0WzFdO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIGlmKHJlc3VsdCl7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGNvbnQ7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH1lbHNlIHtcclxuICAgICAgICAgICAgICAgIGlmIChkYXRhW2NvbmRpdGlvbi5yZXBsYWNlKCdAJywnJyldKSByZXR1cm4gY29udDtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICByZXR1cm4gJyc7XHJcbiAgICAgICAgfSkucmVwbGFjZSgvXFx7QChbXFx3XFxkXFwuXSspKD86XFx8KFtcXHdcXGRdKykoPzpcXHMqPVxccyooW1xcd1xcZCxcXHMjXSspKT8pP1xcfS9nLGZ1bmN0aW9uKGFsbCxtMSxmdW5jLGFyZ3Mpe1xyXG5cclxuICAgICAgICAgICAgaWYobTEuaW5kZXhPZignLicpPjApe1xyXG4gICAgICAgICAgICAgICAgdmFyIGtleXM9bTEuc3BsaXQoJy4nKSx2YWw9ZGF0YTtcclxuICAgICAgICAgICAgICAgIGZvcih2YXIgaT0wO2k8a2V5cy5sZW5ndGg7aSsrKXtcclxuICAgICAgICAgICAgICAgICAgICBpZih2YWxba2V5c1tpXV0hPT11bmRlZmluZWQgJiYgdmFsW2tleXNbaV1dIT09bnVsbCl7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhbD12YWxba2V5c1tpXV07XHJcbiAgICAgICAgICAgICAgICAgICAgfWVsc2V7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhbCA9ICcnO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICByZXR1cm4gY2FsbGZ1bmModmFsLGZ1bmMsYXJncyk7XHJcbiAgICAgICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICAgICAgcmV0dXJuIGNhbGxmdW5jKGRhdGFbbTFdLGZ1bmMsYXJncyxkYXRhKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pO1xyXG4gICAgfVxyXG59O1xyXG5cclxuZnVuY3Rpb24gdG9zdHJpbmcob2JqKSB7XHJcbiAgICBpZihvYmogJiYgb2JqLnRvU3RyaW5nKXtcclxuICAgICAgICByZXR1cm4gb2JqLnRvU3RyaW5nKCk7XHJcbiAgICB9XHJcbiAgICByZXR1cm4gJyc7XHJcbn1cclxuXHJcbmZ1bmN0aW9uIGNhbGxmdW5jKHZhbCxmdW5jLGFyZ3MsdGhpc29iail7XHJcbiAgICBpZighYXJncyl7XHJcbiAgICAgICAgYXJncz1bdmFsXTtcclxuICAgIH1lbHNle1xyXG4gICAgICAgIGlmKHR5cGVvZiBhcmdzPT09J3N0cmluZycpYXJncz1hcmdzLnNwbGl0KCcsJyk7XHJcbiAgICAgICAgdmFyIGFyZ2lkeD1hcmdzLmluZGV4T2YoJyMjIycpO1xyXG4gICAgICAgIGlmKGFyZ2lkeD49MCl7XHJcbiAgICAgICAgICAgIGFyZ3NbYXJnaWR4XT12YWw7XHJcbiAgICAgICAgfWVsc2V7XHJcbiAgICAgICAgICAgIGFyZ3M9W3ZhbF0uY29uY2F0KGFyZ3MpO1xyXG4gICAgICAgIH1cclxuICAgIH1cclxuXHJcbiAgICByZXR1cm4gd2luZG93W2Z1bmNdP3dpbmRvd1tmdW5jXS5hcHBseSh0aGlzb2JqLGFyZ3MpOigodmFsPT09dW5kZWZpbmVkfHx2YWw9PT1udWxsKT8nJzp2YWwpO1xyXG59XHJcblxyXG5mdW5jdGlvbiBpaWYodixtMSxtMil7XHJcbiAgICBpZih2PT09JzAnKXY9MDtcclxuICAgIHJldHVybiB2P20xOm0yO1xyXG59IiwiXHJcbnZhciBkaWFsb2dUcGw9JzxkaXYgY2xhc3M9XCJtb2RhbCBmYWRlXCIgaWQ9XCJ7QGlkfVwiIHRhYmluZGV4PVwiLTFcIiByb2xlPVwiZGlhbG9nXCIgYXJpYS1sYWJlbGxlZGJ5PVwie0BpZH1MYWJlbFwiIGFyaWEtaGlkZGVuPVwidHJ1ZVwiPlxcbicgK1xyXG4gICAgJyAgICA8ZGl2IGNsYXNzPVwibW9kYWwtZGlhbG9nXCI+XFxuJyArXHJcbiAgICAnICAgICAgICA8ZGl2IGNsYXNzPVwibW9kYWwtY29udGVudFwiPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJtb2RhbC1oZWFkZXJcIj5cXG4nICtcclxuICAgICcgICAgICAgICAgICAgICAgPGg0IGNsYXNzPVwibW9kYWwtdGl0bGVcIiBpZD1cIntAaWR9TGFiZWxcIj48L2g0PlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgICAgICA8YnV0dG9uIHR5cGU9XCJidXR0b25cIiBjbGFzcz1cImNsb3NlXCIgZGF0YS1kaXNtaXNzPVwibW9kYWxcIj5cXG4nICtcclxuICAgICcgICAgICAgICAgICAgICAgICAgIDxzcGFuIGFyaWEtaGlkZGVuPVwidHJ1ZVwiPiZ0aW1lczs8L3NwYW4+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgICAgICAgICA8c3BhbiBjbGFzcz1cInNyLW9ubHlcIj5DbG9zZTwvc3Bhbj5cXG4nICtcclxuICAgICcgICAgICAgICAgICAgICAgPC9idXR0b24+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgPC9kaXY+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgPGRpdiBjbGFzcz1cIm1vZGFsLWJvZHlcIj5cXG4nICtcclxuICAgICcgICAgICAgICAgICA8L2Rpdj5cXG4nICtcclxuICAgICcgICAgICAgICAgICA8ZGl2IGNsYXNzPVwibW9kYWwtZm9vdGVyXCI+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgICAgIDxuYXYgY2xhc3M9XCJuYXYgbmF2LWZpbGxcIj48L25hdj5cXG4nICtcclxuICAgICcgICAgICAgICAgICA8L2Rpdj5cXG4nICtcclxuICAgICcgICAgICAgIDwvZGl2PlxcbicgK1xyXG4gICAgJyAgICA8L2Rpdj5cXG4nICtcclxuICAgICc8L2Rpdj4nO1xyXG52YXIgZGlhbG9nSWR4PTA7XHJcbmZ1bmN0aW9uIERpYWxvZyhvcHRzKXtcclxuICAgIGlmKCFvcHRzKW9wdHM9e307XHJcbiAgICAvL+WkhOeQhuaMiemSrlxyXG4gICAgaWYob3B0cy5idG5zIT09dW5kZWZpbmVkKSB7XHJcbiAgICAgICAgaWYgKHR5cGVvZihvcHRzLmJ0bnMpID09ICdzdHJpbmcnKSB7XHJcbiAgICAgICAgICAgIG9wdHMuYnRucyA9IFtvcHRzLmJ0bnNdO1xyXG4gICAgICAgIH1cclxuICAgICAgICAvL3ZhciBkZnQ9LTE7XHJcbiAgICAgICAgZm9yICh2YXIgaSA9IDA7IGkgPCBvcHRzLmJ0bnMubGVuZ3RoOyBpKyspIHtcclxuICAgICAgICAgICAgaWYodHlwZW9mKG9wdHMuYnRuc1tpXSk9PSdzdHJpbmcnKXtcclxuICAgICAgICAgICAgICAgIG9wdHMuYnRuc1tpXT17J3RleHQnOm9wdHMuYnRuc1tpXX07XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgaWYob3B0cy5idG5zW2ldLmlzZGVmYXVsdCl7XHJcbiAgICAgICAgICAgICAgICBvcHRzLmRlZmF1bHRCdG49aTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH1cclxuICAgICAgICBpZihvcHRzLmRlZmF1bHRCdG49PT11bmRlZmluZWQpe1xyXG4gICAgICAgICAgICBvcHRzLmRlZmF1bHRCdG49b3B0cy5idG5zLmxlbmd0aC0xO1xyXG4gICAgICAgICAgICBvcHRzLmJ0bnNbb3B0cy5kZWZhdWx0QnRuXS5pc2RlZmF1bHQ9dHJ1ZTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIGlmKG9wdHMuYnRuc1tvcHRzLmRlZmF1bHRCdG5dICYmICFvcHRzLmJ0bnNbb3B0cy5kZWZhdWx0QnRuXVsndHlwZSddKXtcclxuICAgICAgICAgICAgb3B0cy5idG5zW29wdHMuZGVmYXVsdEJ0bl1bJ3R5cGUnXT0ncHJpbWFyeSc7XHJcbiAgICAgICAgfVxyXG4gICAgfVxyXG5cclxuICAgIHRoaXMub3B0aW9ucz0kLmV4dGVuZCh7XHJcbiAgICAgICAgJ2lkJzonZGxnTW9kYWwnK2RpYWxvZ0lkeCsrLFxyXG4gICAgICAgICdoZWFkZXInOnRydWUsXHJcbiAgICAgICAgJ2JhY2tkcm9wJzp0cnVlLFxyXG4gICAgICAgICdzaXplJzonJyxcclxuICAgICAgICAnYnRucyc6W1xyXG4gICAgICAgICAgICB7J3RleHQnOiflj5bmtognLCd0eXBlJzonc2Vjb25kYXJ5J30sXHJcbiAgICAgICAgICAgIHsndGV4dCc6J+ehruWumicsJ2lzZGVmYXVsdCc6dHJ1ZSwndHlwZSc6J3ByaW1hcnknfVxyXG4gICAgICAgIF0sXHJcbiAgICAgICAgJ2RlZmF1bHRCdG4nOjEsXHJcbiAgICAgICAgJ29uc3VyZSc6bnVsbCxcclxuICAgICAgICAnb25zaG93JzpudWxsLFxyXG4gICAgICAgICdvbnNob3duJzpudWxsLFxyXG4gICAgICAgICdvbmhpZGUnOm51bGwsXHJcbiAgICAgICAgJ29uaGlkZGVuJzpudWxsXHJcbiAgICB9LG9wdHMpO1xyXG5cclxuICAgIHRoaXMuYm94PSQodGhpcy5vcHRpb25zLmlkKTtcclxufVxyXG5EaWFsb2cucHJvdG90eXBlLmdlbmVyQnRuPWZ1bmN0aW9uKG9wdCxpZHgpe1xyXG4gICAgaWYob3B0Wyd0eXBlJ10pb3B0WydjbGFzcyddPSdidG4tb3V0bGluZS0nK29wdFsndHlwZSddO1xyXG4gICAgcmV0dXJuICc8YSBocmVmPVwiamF2YXNjcmlwdDpcIiBjbGFzcz1cIm5hdi1pdGVtIGJ0biAnKyhvcHRbJ2NsYXNzJ10/b3B0WydjbGFzcyddOididG4tb3V0bGluZS1zZWNvbmRhcnknKSsnXCIgZGF0YS1pbmRleD1cIicraWR4KydcIj4nK29wdC50ZXh0Kyc8L2E+JztcclxufTtcclxuRGlhbG9nLnByb3RvdHlwZS5zaG93PWZ1bmN0aW9uKGh0bWwsdGl0bGUpe1xyXG4gICAgdGhpcy5ib3g9JCgnIycrdGhpcy5vcHRpb25zLmlkKTtcclxuICAgIGlmKCF0aXRsZSl0aXRsZT0n57O757uf5o+Q56S6JztcclxuXHJcbiAgICBpZih0aGlzLmJveC5sZW5ndGg8MSkge1xyXG4gICAgICAgICQoZG9jdW1lbnQuYm9keSkuYXBwZW5kKGRpYWxvZ1RwbC5yZXBsYWNlKCdtb2RhbC1ib2R5JywnbW9kYWwtYm9keScrKHRoaXMub3B0aW9ucy5ib2R5Q2xhc3M/KCcgJyt0aGlzLm9wdGlvbnMuYm9keUNsYXNzKTonJykpLmNvbXBpbGUoeydpZCc6IHRoaXMub3B0aW9ucy5pZH0pKTtcclxuICAgICAgICB0aGlzLmJveD0kKCcjJyt0aGlzLm9wdGlvbnMuaWQpO1xyXG4gICAgfWVsc2V7XHJcbiAgICAgICAgdGhpcy5ib3gudW5iaW5kKCk7XHJcbiAgICB9XHJcbiAgICBpZighdGhpcy5vcHRpb25zLmhlYWRlcil7XHJcbiAgICAgICAgdGhpcy5ib3guZmluZCgnLm1vZGFsLWhlYWRlcicpLnJlbW92ZSgpO1xyXG4gICAgfVxyXG4gICAgaWYodGhpcy5vcHRpb25zLmJhY2tkcm9wIT09dHJ1ZSl7XHJcbiAgICAgICAgdGhpcy5ib3guZGF0YSgnYmFja2Ryb3AnLHRoaXMub3B0aW9ucy5iYWNrZHJvcCk7XHJcbiAgICB9XHJcbiAgICBpZighdGhpcy5vcHRpb25zLmtleWJvYXJkKXtcclxuICAgICAgICB0aGlzLmJveC5kYXRhKCdrZXlib2FyZCcsZmFsc2UpO1xyXG4gICAgfVxyXG5cclxuICAgIC8vdGhpcy5ib3guZmluZCgnLm1vZGFsLWZvb3RlciAuYnRuLXByaW1hcnknKS51bmJpbmQoKTtcclxuICAgIHZhciBzZWxmPXRoaXM7XHJcbiAgICBEaWFsb2cuaW5zdGFuY2U9c2VsZjtcclxuXHJcbiAgICAvL+eUn+aIkOaMiemSrlxyXG4gICAgdmFyIGJ0bnM9W107XHJcbiAgICBmb3IodmFyIGk9MDtpPHRoaXMub3B0aW9ucy5idG5zLmxlbmd0aDtpKyspe1xyXG4gICAgICAgIGJ0bnMucHVzaCh0aGlzLmdlbmVyQnRuKHRoaXMub3B0aW9ucy5idG5zW2ldLGkpKTtcclxuICAgIH1cclxuICAgIHRoaXMuYm94LmZpbmQoJy5tb2RhbC1mb290ZXIgLm5hdicpLmh0bWwoYnRucy5qb2luKCdcXG4nKSk7XHJcblxyXG4gICAgdmFyIGRpYWxvZz10aGlzLmJveC5maW5kKCcubW9kYWwtZGlhbG9nJyk7XHJcbiAgICBkaWFsb2cucmVtb3ZlQ2xhc3MoJ21vZGFsLXNtJykucmVtb3ZlQ2xhc3MoJ21vZGFsLWxnJyk7XHJcbiAgICBpZih0aGlzLm9wdGlvbnMuc2l6ZT09J3NtJykge1xyXG4gICAgICAgIGRpYWxvZy5hZGRDbGFzcygnbW9kYWwtc20nKTtcclxuICAgIH1lbHNlIGlmKHRoaXMub3B0aW9ucy5zaXplPT0nbGcnKSB7XHJcbiAgICAgICAgZGlhbG9nLmFkZENsYXNzKCdtb2RhbC1sZycpO1xyXG4gICAgfVxyXG4gICAgdGhpcy5ib3guZmluZCgnLm1vZGFsLXRpdGxlJykudGV4dCh0aXRsZSk7XHJcblxyXG4gICAgdmFyIGJvZHk9dGhpcy5ib3guZmluZCgnLm1vZGFsLWJvZHknKTtcclxuICAgIGJvZHkuaHRtbChodG1sKTtcclxuICAgIHRoaXMuYm94Lm9uKCdoaWRlLmJzLm1vZGFsJyxmdW5jdGlvbigpe1xyXG4gICAgICAgIGlmKHNlbGYub3B0aW9ucy5vbmhpZGUpe1xyXG4gICAgICAgICAgICBzZWxmLm9wdGlvbnMub25oaWRlKGJvZHksc2VsZi5ib3gpO1xyXG4gICAgICAgIH1cclxuICAgICAgICBEaWFsb2cuaW5zdGFuY2U9bnVsbDtcclxuICAgIH0pO1xyXG4gICAgdGhpcy5ib3gub24oJ2hpZGRlbi5icy5tb2RhbCcsZnVuY3Rpb24oKXtcclxuICAgICAgICBpZihzZWxmLm9wdGlvbnMub25oaWRkZW4pe1xyXG4gICAgICAgICAgICBzZWxmLm9wdGlvbnMub25oaWRkZW4oYm9keSxzZWxmLmJveCk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHNlbGYuYm94LnJlbW92ZSgpO1xyXG4gICAgfSk7XHJcbiAgICB0aGlzLmJveC5vbignc2hvdy5icy5tb2RhbCcsZnVuY3Rpb24oKXtcclxuICAgICAgICBpZihzZWxmLm9wdGlvbnMub25zaG93KXtcclxuICAgICAgICAgICAgc2VsZi5vcHRpb25zLm9uc2hvdyhib2R5LHNlbGYuYm94KTtcclxuICAgICAgICB9XHJcbiAgICB9KTtcclxuICAgIHRoaXMuYm94Lm9uKCdzaG93bi5icy5tb2RhbCcsZnVuY3Rpb24oKXtcclxuICAgICAgICBpZihzZWxmLm9wdGlvbnMub25zaG93bil7XHJcbiAgICAgICAgICAgIHNlbGYub3B0aW9ucy5vbnNob3duKGJvZHksc2VsZi5ib3gpO1xyXG4gICAgICAgIH1cclxuICAgIH0pO1xyXG4gICAgdGhpcy5ib3guZmluZCgnLm1vZGFsLWZvb3RlciAuYnRuJykuY2xpY2soZnVuY3Rpb24oKXtcclxuICAgICAgICB2YXIgcmVzdWx0PXRydWUsaWR4PSQodGhpcykuZGF0YSgnaW5kZXgnKTtcclxuICAgICAgICBpZihzZWxmLm9wdGlvbnMuYnRuc1tpZHhdLmNsaWNrKXtcclxuICAgICAgICAgICAgcmVzdWx0ID0gc2VsZi5vcHRpb25zLmJ0bnNbaWR4XS5jbGljay5hcHBseSh0aGlzLFtib2R5LCBzZWxmLmJveF0pO1xyXG4gICAgICAgIH1cclxuICAgICAgICBpZihpZHg9PXNlbGYub3B0aW9ucy5kZWZhdWx0QnRuKSB7XHJcbiAgICAgICAgICAgIGlmIChzZWxmLm9wdGlvbnMub25zdXJlKSB7XHJcbiAgICAgICAgICAgICAgICByZXN1bHQgPSBzZWxmLm9wdGlvbnMub25zdXJlLmFwcGx5KHRoaXMsW2JvZHksIHNlbGYuYm94XSk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9XHJcbiAgICAgICAgaWYocmVzdWx0IT09ZmFsc2Upe1xyXG4gICAgICAgICAgICBzZWxmLmJveC5tb2RhbCgnaGlkZScpO1xyXG4gICAgICAgIH1cclxuICAgIH0pO1xyXG4gICAgdGhpcy5ib3gubW9kYWwoJ3Nob3cnKTtcclxuICAgIHJldHVybiB0aGlzO1xyXG59O1xyXG5EaWFsb2cucHJvdG90eXBlLmhpZGU9RGlhbG9nLnByb3RvdHlwZS5jbG9zZT1mdW5jdGlvbigpe1xyXG4gICAgdGhpcy5ib3gubW9kYWwoJ2hpZGUnKTtcclxuICAgIHJldHVybiB0aGlzO1xyXG59O1xyXG5cclxudmFyIGRpYWxvZz17XHJcbiAgICBhbGVydDpmdW5jdGlvbihtZXNzYWdlLGNhbGxiYWNrLGljb24pe1xyXG4gICAgICAgIHZhciBjYWxsZWQ9ZmFsc2U7XHJcbiAgICAgICAgdmFyIGlzY2FsbGJhY2s9dHlwZW9mIGNhbGxiYWNrPT0nZnVuY3Rpb24nO1xyXG4gICAgICAgIGlmKGNhbGxiYWNrICYmICFpc2NhbGxiYWNrKXtcclxuICAgICAgICAgICAgaWNvbj1jYWxsYmFjaztcclxuICAgICAgICB9XHJcbiAgICAgICAgdmFyIGljb25NYXA9IHtcclxuICAgICAgICAgICAgJ3N1Y2Nlc3MnOidjaGVja21hcmstY2lyY2xlJyxcclxuICAgICAgICAgICAgJ2luZm8nOiAnaW5mb3JtYXRpb24tY2lyY2xlJyxcclxuICAgICAgICAgICAgJ3dhcm5pbmcnOidhbGVydCcsXHJcbiAgICAgICAgICAgICdlcnJvcic6J3JlbW92ZS1jaXJjbGUnXHJcbiAgICAgICAgfTtcclxuICAgICAgICB2YXIgY29sb3I9J3ByaW1hcnknO1xyXG4gICAgICAgIGlmKCFpY29uKWljb249J2luZm9ybWF0aW9uLWNpcmNsZSc7XHJcbiAgICAgICAgZWxzZSBpZihpY29uTWFwW2ljb25dKXtcclxuICAgICAgICAgICAgY29sb3I9aWNvbj09J2Vycm9yJz8nZGFuZ2VyJzppY29uO1xyXG4gICAgICAgICAgICBpY29uPWljb25NYXBbaWNvbl07XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHZhciBodG1sPSc8ZGl2IGNsYXNzPVwicm93XCIgc3R5bGU9XCJhbGlnbi1pdGVtczogY2VudGVyO1wiPjxkaXYgY2xhc3M9XCJjb2wtMyB0ZXh0LXJpZ2h0XCI+PGkgY2xhc3M9XCJpb24tbWQte0BpY29ufSB0ZXh0LXtAY29sb3J9XCIgc3R5bGU9XCJmb250LXNpemU6M2VtO1wiPjwvaT4gPC9kaXY+PGRpdiBjbGFzcz1cImNvbC05XCIgPntAbWVzc2FnZX08L2Rpdj4gPC9kaXY+Jy5jb21waWxlKHtcclxuICAgICAgICAgICAgbWVzc2FnZTptZXNzYWdlLFxyXG4gICAgICAgICAgICBpY29uOmljb24sXHJcbiAgICAgICAgICAgIGNvbG9yOmNvbG9yXHJcbiAgICAgICAgfSk7XHJcbiAgICAgICAgcmV0dXJuIG5ldyBEaWFsb2coe1xyXG4gICAgICAgICAgICBidG5zOifnoa7lrponLFxyXG4gICAgICAgICAgICBoZWFkZXI6ZmFsc2UsXHJcbiAgICAgICAgICAgIG9uc3VyZTpmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICAgICAgaWYoaXNjYWxsYmFjayl7XHJcbiAgICAgICAgICAgICAgICAgICAgY2FsbGVkPXRydWU7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGNhbGxiYWNrKHRydWUpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICBvbmhpZGU6ZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgICAgIGlmKCFjYWxsZWQgJiYgaXNjYWxsYmFjayl7XHJcbiAgICAgICAgICAgICAgICAgICAgY2FsbGJhY2soZmFsc2UpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkuc2hvdyhodG1sLCcnKTtcclxuICAgIH0sXHJcbiAgICBjb25maXJtOmZ1bmN0aW9uKG1lc3NhZ2UsY29uZmlybSxjYW5jZWwsaWNvbil7XHJcbiAgICAgICAgdmFyIGNhbGxlZD1mYWxzZTtcclxuICAgICAgICBpZih0eXBlb2YgY29uZmlybT09J3N0cmluZycpe1xyXG4gICAgICAgICAgICBpY29uPWNvbmZpcm07XHJcbiAgICAgICAgICAgIGlmKHR5cGVvZiBjYW5jZWw9PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgY29uZmlybT1jYW5jZWw7XHJcbiAgICAgICAgICAgICAgICBjYW5jZWw9bnVsbDtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH1lbHNlIGlmKHR5cGVvZiBjYWxjZWw9PSdzdHJpbmcnKXtcclxuICAgICAgICAgICAgaWNvbj1jYW5jZWw7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHZhciBpY29uTWFwPSB7XHJcbiAgICAgICAgICAgICdzdWNjZXNzJzonY2hlY2ttYXJrLWNpcmNsZScsXHJcbiAgICAgICAgICAgICdpbmZvJzogJ2luZm9ybWF0aW9uLWNpcmNsZScsXHJcbiAgICAgICAgICAgICd3YXJuaW5nJzonYWxlcnQnLFxyXG4gICAgICAgICAgICAnZXJyb3InOidyZW1vdmUtY2lyY2xlJ1xyXG4gICAgICAgIH07XHJcbiAgICAgICAgdmFyIGNvbG9yPSdwcmltYXJ5JztcclxuICAgICAgICBpZighaWNvbilpY29uPSdpbmZvcm1hdGlvbi1jaXJjbGUnO1xyXG4gICAgICAgIGVsc2UgaWYoaWNvbk1hcFtpY29uXSl7XHJcbiAgICAgICAgICAgIGNvbG9yPWljb249PSdlcnJvcic/J2Rhbmdlcic6aWNvbjtcclxuICAgICAgICAgICAgaWNvbj1pY29uTWFwW2ljb25dO1xyXG4gICAgICAgIH1cclxuICAgICAgICB2YXIgaHRtbD0nPGRpdiBjbGFzcz1cInJvd1wiIHN0eWxlPVwiYWxpZ24taXRlbXM6IGNlbnRlcjtcIj48ZGl2IGNsYXNzPVwiY29sLTMgdGV4dC1yaWdodFwiPjxpIGNsYXNzPVwiaW9uLW1kLXtAaWNvbn0gdGV4dC17QGNvbG9yfVwiIHN0eWxlPVwiZm9udC1zaXplOjNlbTtcIj48L2k+IDwvZGl2PjxkaXYgY2xhc3M9XCJjb2wtOVwiID57QG1lc3NhZ2V9PC9kaXY+IDwvZGl2PicuY29tcGlsZSh7XHJcbiAgICAgICAgICAgIG1lc3NhZ2U6bWVzc2FnZSxcclxuICAgICAgICAgICAgaWNvbjppY29uLFxyXG4gICAgICAgICAgICBjb2xvcjpjb2xvclxyXG4gICAgICAgIH0pO1xyXG4gICAgICAgIHJldHVybiBuZXcgRGlhbG9nKHtcclxuICAgICAgICAgICAgJ2hlYWRlcic6ZmFsc2UsXHJcbiAgICAgICAgICAgICdiYWNrZHJvcCc6J3N0YXRpYycsXHJcbiAgICAgICAgICAgICdvbnN1cmUnOmZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgICAgICBpZihjb25maXJtICYmIHR5cGVvZiBjb25maXJtPT0nZnVuY3Rpb24nKXtcclxuICAgICAgICAgICAgICAgICAgICBjYWxsZWQ9dHJ1ZTtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gY29uZmlybSgpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAnb25oaWRlJzpmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgICAgICBpZihjYWxsZWQ9PWZhbHNlICYmIHR5cGVvZiBjYW5jZWw9PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBjYW5jZWwoKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLnNob3coaHRtbCk7XHJcbiAgICB9LFxyXG4gICAgcHJvbXB0OmZ1bmN0aW9uKG1lc3NhZ2UsY2FsbGJhY2ssY2FuY2VsKXtcclxuICAgICAgICB2YXIgY2FsbGVkPWZhbHNlO1xyXG4gICAgICAgIHZhciBjb250ZW50SHRtbD0nPGRpdiBjbGFzcz1cImZvcm0tZ3JvdXBcIj57QGlucHV0fTwvZGl2Pic7XHJcbiAgICAgICAgdmFyIHRpdGxlPSfor7fovpPlhaXkv6Hmga8nO1xyXG4gICAgICAgIGlmKHR5cGVvZiBtZXNzYWdlPT0nc3RyaW5nJyl7XHJcbiAgICAgICAgICAgIHRpdGxlPW1lc3NhZ2U7XHJcbiAgICAgICAgfWVsc2V7XHJcbiAgICAgICAgICAgIHRpdGxlPW1lc3NhZ2UudGl0bGU7XHJcbiAgICAgICAgICAgIGlmKG1lc3NhZ2UuY29udGVudCkge1xyXG4gICAgICAgICAgICAgICAgY29udGVudEh0bWwgPSBtZXNzYWdlLmNvbnRlbnQuaW5kZXhPZigne0BpbnB1dH0nKSA+IC0xID8gbWVzc2FnZS5jb250ZW50IDogbWVzc2FnZS5jb250ZW50ICsgY29udGVudEh0bWw7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9XHJcbiAgICAgICAgcmV0dXJuIG5ldyBEaWFsb2coe1xyXG4gICAgICAgICAgICAnYmFja2Ryb3AnOidzdGF0aWMnLFxyXG4gICAgICAgICAgICAnb25zaG93JzpmdW5jdGlvbihib2R5KXtcclxuICAgICAgICAgICAgICAgIGlmKG1lc3NhZ2UgJiYgbWVzc2FnZS5vbnNob3cpe1xyXG4gICAgICAgICAgICAgICAgICAgIG1lc3NhZ2Uub25zaG93KGJvZHkpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAnb25zaG93bic6ZnVuY3Rpb24oYm9keSl7XHJcbiAgICAgICAgICAgICAgICBib2R5LmZpbmQoJ1tuYW1lPWNvbmZpcm1faW5wdXRdJykuZm9jdXMoKTtcclxuICAgICAgICAgICAgICAgIGlmKG1lc3NhZ2UgJiYgbWVzc2FnZS5vbnNob3duKXtcclxuICAgICAgICAgICAgICAgICAgICBtZXNzYWdlLm9uc2hvd24oYm9keSk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdvbnN1cmUnOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgdmFyIHZhbD1ib2R5LmZpbmQoJ1tuYW1lPWNvbmZpcm1faW5wdXRdJykudmFsKCk7XHJcbiAgICAgICAgICAgICAgICBpZih0eXBlb2YgY2FsbGJhY2s9PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgICAgIHZhciByZXN1bHQgPSBjYWxsYmFjayh2YWwpO1xyXG4gICAgICAgICAgICAgICAgICAgIGlmKHJlc3VsdD09PXRydWUpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBjYWxsZWQ9dHJ1ZTtcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHJlc3VsdDtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgJ29uaGlkZSc6ZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAgICAgaWYoY2FsbGVkPWZhbHNlICYmIHR5cGVvZiBjYW5jZWw9PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBjYW5jZWwoKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLnNob3coY29udGVudEh0bWwuY29tcGlsZSh7aW5wdXQ6JzxpbnB1dCB0eXBlPVwidGV4dFwiIG5hbWU9XCJjb25maXJtX2lucHV0XCIgY2xhc3M9XCJmb3JtLWNvbnRyb2xcIiAvPid9KSx0aXRsZSk7XHJcbiAgICB9LFxyXG4gICAgYWN0aW9uOmZ1bmN0aW9uIChsaXN0LGNhbGxiYWNrLHRpdGxlKSB7XHJcbiAgICAgICAgdmFyIGh0bWw9JzxkaXYgY2xhc3M9XCJsaXN0LWdyb3VwXCI+PGEgaHJlZj1cImphdmFzY3JpcHQ6XCIgY2xhc3M9XCJsaXN0LWdyb3VwLWl0ZW0gbGlzdC1ncm91cC1pdGVtLWFjdGlvblwiPicrbGlzdC5qb2luKCc8L2E+PGEgaHJlZj1cImphdmFzY3JpcHQ6XCIgY2xhc3M9XCJsaXN0LWdyb3VwLWl0ZW0gbGlzdC1ncm91cC1pdGVtLWFjdGlvblwiPicpKyc8L2E+PC9kaXY+JztcclxuICAgICAgICB2YXIgYWN0aW9ucz1udWxsO1xyXG4gICAgICAgIHZhciBkbGc9bmV3IERpYWxvZyh7XHJcbiAgICAgICAgICAgICdib2R5Q2xhc3MnOidtb2RhbC1hY3Rpb24nLFxyXG4gICAgICAgICAgICAnYmFja2Ryb3AnOidzdGF0aWMnLFxyXG4gICAgICAgICAgICAnYnRucyc6W1xyXG4gICAgICAgICAgICAgICAgeyd0ZXh0Jzon5Y+W5raIJywndHlwZSc6J3NlY29uZGFyeSd9XHJcbiAgICAgICAgICAgIF0sXHJcbiAgICAgICAgICAgICdvbnNob3cnOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgYWN0aW9ucz1ib2R5LmZpbmQoJy5saXN0LWdyb3VwLWl0ZW0tYWN0aW9uJyk7XHJcbiAgICAgICAgICAgICAgICBhY3Rpb25zLmNsaWNrKGZ1bmN0aW9uIChlKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgYWN0aW9ucy5yZW1vdmVDbGFzcygnYWN0aXZlJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5hZGRDbGFzcygnYWN0aXZlJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgdmFyIHZhbD1hY3Rpb25zLmluZGV4KHRoaXMpO1xyXG4gICAgICAgICAgICAgICAgICAgIGlmKHR5cGVvZiBjYWxsYmFjaz09J2Z1bmN0aW9uJyl7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmKCBjYWxsYmFjayh2YWwpIT09ZmFsc2Upe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZGxnLmNsb3NlKCk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICB9ZWxzZSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGRsZy5jbG9zZSgpO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIH0pXHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdvbnN1cmUnOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgcmV0dXJuIHRydWU7XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdvbmhpZGUnOmZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgIHJldHVybiB0cnVlO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkuc2hvdyhodG1sLHRpdGxlP3RpdGxlOifor7fpgInmi6knKTtcclxuICAgIH0sXHJcbiAgICBwaWNrTGlzdDpmdW5jdGlvbiAoY29uZmlnLGNhbGxiYWNrLGZpbHRlcikge1xyXG4gICAgICAgIGlmKHR5cGVvZiBjb25maWc9PT0nc3RyaW5nJyljb25maWc9e3VybDpjb25maWd9O1xyXG4gICAgICAgIGNvbmZpZz0kLmV4dGVuZCh7XHJcbiAgICAgICAgICAgICd1cmwnOicnLFxyXG4gICAgICAgICAgICAnbmFtZSc6J+WvueixoScsXHJcbiAgICAgICAgICAgICdzZWFyY2hIb2xkZXInOifmoLnmja7lkI3np7DmkJzntKInLFxyXG4gICAgICAgICAgICAnaWRrZXknOidpZCcsXHJcbiAgICAgICAgICAgICdvblJvdyc6bnVsbCxcclxuICAgICAgICAgICAgJ2V4dGVuZCc6bnVsbCxcclxuICAgICAgICAgICAgJ3Jvd1RlbXBsYXRlJzonPGEgaHJlZj1cImphdmFzY3JpcHQ6XCIgZGF0YS1pZD1cIntAaWR9XCIgY2xhc3M9XCJsaXN0LWdyb3VwLWl0ZW0gbGlzdC1ncm91cC1pdGVtLWFjdGlvblwiPlt7QGlkfV0mbmJzcDs8aSBjbGFzcz1cImlvbi1tZC1wZXJzb25cIj48L2k+IHtAdXNlcm5hbWV9Jm5ic3A7Jm5ic3A7Jm5ic3A7PHNtYWxsPjxpIGNsYXNzPVwiaW9uLW1kLXBob25lLXBvcnRyYWl0XCI+PC9pPiB7QG1vYmlsZX08L3NtYWxsPjwvYT4nXHJcbiAgICAgICAgfSxjb25maWd8fHt9KTtcclxuICAgICAgICB2YXIgY3VycmVudD1udWxsO1xyXG4gICAgICAgIHZhciBleHRodG1sPScnO1xyXG4gICAgICAgIGlmKGNvbmZpZy5leHRlbmQpe1xyXG4gICAgICAgICAgICBleHRodG1sPSc8c2VsZWN0IG5hbWU9XCInK2NvbmZpZy5leHRlbmQubmFtZSsnXCIgY2xhc3M9XCJmb3JtLWNvbnRyb2xcIj48b3B0aW9uIHZhbHVlPVwiXCI+Jytjb25maWcuZXh0ZW5kLnRpdGxlKyc8L29wdGlvbj48L3NlbGVjdD4nO1xyXG4gICAgICAgIH1cclxuICAgICAgICBpZighZmlsdGVyKWZpbHRlcj17fTtcclxuICAgICAgICB2YXIgZGxnPW5ldyBEaWFsb2coe1xyXG4gICAgICAgICAgICAnYmFja2Ryb3AnOidzdGF0aWMnLFxyXG4gICAgICAgICAgICAnb25zaG93bic6ZnVuY3Rpb24oYm9keSl7XHJcbiAgICAgICAgICAgICAgICB2YXIgYnRuPWJvZHkuZmluZCgnLnNlYXJjaGJ0bicpO1xyXG4gICAgICAgICAgICAgICAgdmFyIGlucHV0PWJvZHkuZmluZCgnLnNlYXJjaHRleHQnKTtcclxuICAgICAgICAgICAgICAgIHZhciBsaXN0Ym94PWJvZHkuZmluZCgnLmxpc3QtZ3JvdXAnKTtcclxuICAgICAgICAgICAgICAgIHZhciBpc2xvYWRpbmc9ZmFsc2U7XHJcbiAgICAgICAgICAgICAgICB2YXIgZXh0RmllbGQ9bnVsbDtcclxuICAgICAgICAgICAgICAgIGlmKGNvbmZpZy5leHRlbmQpe1xyXG4gICAgICAgICAgICAgICAgICAgIGV4dEZpZWxkPWJvZHkuZmluZCgnW25hbWU9Jytjb25maWcuZXh0ZW5kLm5hbWUrJ10nKTtcclxuICAgICAgICAgICAgICAgICAgICAkLmFqYXgoe1xyXG4gICAgICAgICAgICAgICAgICAgICAgIHVybDpjb25maWcuZXh0ZW5kLnVybCxcclxuICAgICAgICAgICAgICAgICAgICAgICAgdHlwZTonR0VUJyxcclxuICAgICAgICAgICAgICAgICAgICAgICAgZGF0YVR5cGU6J0pTT04nLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICBzdWNjZXNzOmZ1bmN0aW9uIChqc29uKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBleHRGaWVsZC5hcHBlbmQoY29uZmlnLmV4dGVuZC5odG1sUm93LmNvbXBpbGUoanNvbi5kYXRhLHRydWUpKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgIH0pO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgYnRuLmNsaWNrKGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgICAgICAgICAgaWYoaXNsb2FkaW5nKXJldHVybjtcclxuICAgICAgICAgICAgICAgICAgICBpc2xvYWRpbmc9dHJ1ZTtcclxuICAgICAgICAgICAgICAgICAgICBsaXN0Ym94Lmh0bWwoJzxzcGFuIGNsYXNzPVwibGlzdC1sb2FkaW5nXCI+5Yqg6L295LitLi4uPC9zcGFuPicpO1xyXG4gICAgICAgICAgICAgICAgICAgIGZpbHRlclsna2V5J109aW5wdXQudmFsKCk7XHJcbiAgICAgICAgICAgICAgICAgICAgaWYoY29uZmlnLmV4dGVuZCl7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGZpbHRlcltjb25maWcuZXh0ZW5kLm5hbWVdPWV4dEZpZWxkLnZhbCgpO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICAkLmFqYXgoXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHVybDpjb25maWcudXJsLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdHlwZTonR0VUJyxcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGRhdGFUeXBlOidKU09OJyxcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGRhdGE6ZmlsdGVyLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc3VjY2VzczpmdW5jdGlvbihqc29uKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpc2xvYWRpbmc9ZmFsc2U7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYoanNvbi5jb2RlPT09MSl7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmKGpzb24uZGF0YSAmJiBqc29uLmRhdGEubGVuZ3RoKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBsaXN0Ym94Lmh0bWwoY29uZmlnLnJvd1RlbXBsYXRlLmNvbXBpbGUoanNvbi5kYXRhLCB0cnVlKSk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBsaXN0Ym94LmZpbmQoJ2EubGlzdC1ncm91cC1pdGVtJykuY2xpY2soZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHZhciBpZCA9ICQodGhpcykuZGF0YSgnaWQnKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBmb3IgKHZhciBpID0gMDsgaSA8IGpzb24uZGF0YS5sZW5ndGg7IGkrKykge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZihqc29uLmRhdGFbaV1bY29uZmlnLmlka2V5XT09aWQpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgY3VycmVudD1qc29uLmRhdGFbaV07XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBsaXN0Ym94LmZpbmQoJ2EubGlzdC1ncm91cC1pdGVtJykucmVtb3ZlQ2xhc3MoJ2FjdGl2ZScpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5hZGRDbGFzcygnYWN0aXZlJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbGlzdGJveC5odG1sKCc8c3BhbiBjbGFzcz1cImxpc3QtbG9hZGluZ1wiPjxpIGNsYXNzPVwiaW9uLW1kLXdhcm5pbmdcIj48L2k+IOayoeacieajgOe0ouWIsCcrY29uZmlnLm5hbWUrJzwvc3Bhbj4nKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBsaXN0Ym94Lmh0bWwoJzxzcGFuIGNsYXNzPVwidGV4dC1kYW5nZXJcIj48aSBjbGFzcz1cImlvbi1tZC13YXJuaW5nXCI+PC9pPiDliqDovb3lpLHotKU8L3NwYW4+Jyk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgKTtcclxuXHJcbiAgICAgICAgICAgICAgICB9KS50cmlnZ2VyKCdjbGljaycpO1xyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAnb25zdXJlJzpmdW5jdGlvbihib2R5KXtcclxuICAgICAgICAgICAgICAgIGlmKCFjdXJyZW50KXtcclxuICAgICAgICAgICAgICAgICAgICB0b2FzdHIud2FybmluZygn5rKh5pyJ6YCJ5oupJytjb25maWcubmFtZSsnIScpO1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIGlmKHR5cGVvZiBjYWxsYmFjaz09J2Z1bmN0aW9uJyl7XHJcbiAgICAgICAgICAgICAgICAgICAgdmFyIHJlc3VsdCA9IGNhbGxiYWNrKGN1cnJlbnQpO1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiByZXN1bHQ7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS5zaG93KCc8ZGl2IGNsYXNzPVwiaW5wdXQtZ3JvdXBcIj4nK2V4dGh0bWwrJzxpbnB1dCB0eXBlPVwidGV4dFwiIGNsYXNzPVwiZm9ybS1jb250cm9sIHNlYXJjaHRleHRcIiBuYW1lPVwia2V5d29yZFwiIHBsYWNlaG9sZGVyPVwiJytjb25maWcuc2VhcmNoSG9sZGVyKydcIi8+PGRpdiBjbGFzcz1cImlucHV0LWdyb3VwLWFwcGVuZFwiPjxhIGNsYXNzPVwiYnRuIGJ0bi1vdXRsaW5lLXNlY29uZGFyeSBzZWFyY2hidG5cIj48aSBjbGFzcz1cImlvbi1tZC1zZWFyY2hcIj48L2k+PC9hPjwvZGl2PjwvZGl2PjxkaXYgY2xhc3M9XCJsaXN0LWdyb3VwIGxpc3QtZ3JvdXAtcGlja2VyIG10LTJcIj48L2Rpdj4nLCfor7fmkJzntKLlubbpgInmi6knK2NvbmZpZy5uYW1lKTtcclxuICAgIH0sXHJcbiAgICBwaWNrVXNlcjpmdW5jdGlvbihjYWxsYmFjayxmaWx0ZXIpe1xyXG4gICAgICAgIHJldHVybiB0aGlzLnBpY2tMaXN0KHtcclxuICAgICAgICAgICAgJ3VybCc6d2luZG93LmdldF9zZWFyY2hfdXJsKCdtZW1iZXInKSxcclxuICAgICAgICAgICAgJ25hbWUnOifkvJrlkZgnLFxyXG4gICAgICAgICAgICAnc2VhcmNoSG9sZGVyJzon5qC55o2u5Lya5ZGYaWTmiJblkI3np7DvvIznlLXor53mnaXmkJzntKInXHJcbiAgICAgICAgfSxjYWxsYmFjayxmaWx0ZXIpO1xyXG4gICAgfSxcclxuICAgIHBpY2tBcnRpY2xlOmZ1bmN0aW9uKGNhbGxiYWNrLGZpbHRlcil7XHJcbiAgICAgICAgcmV0dXJuIHRoaXMucGlja0xpc3Qoe1xyXG4gICAgICAgICAgICAndXJsJzp3aW5kb3cuZ2V0X3NlYXJjaF91cmwoJ2FydGljbGUnKSxcclxuICAgICAgICAgICAgcm93VGVtcGxhdGU6JzxhIGhyZWY9XCJqYXZhc2NyaXB0OlwiIGRhdGEtaWQ9XCJ7QGlkfVwiIGNsYXNzPVwibGlzdC1ncm91cC1pdGVtIGxpc3QtZ3JvdXAtaXRlbS1hY3Rpb25cIj57aWYgQGNvdmVyfTxkaXYgc3R5bGU9XCJiYWNrZ3JvdW5kLWltYWdlOnVybCh7QGNvdmVyfSlcIiBjbGFzcz1cImltZ3ZpZXdcIiA+PC9kaXY+ey9pZn08ZGl2IGNsYXNzPVwidGV4dC1ibG9ja1wiPlt7QGlkfV0mbmJzcDt7QHRpdGxlfSZuYnNwOzxiciAvPntAZGVzY3JpcHRpb259PC9kaXY+PC9hPicsXHJcbiAgICAgICAgICAgIG5hbWU6J+aWh+eroCcsXHJcbiAgICAgICAgICAgIGlka2V5OidpZCcsXHJcbiAgICAgICAgICAgIGV4dGVuZDp7XHJcbiAgICAgICAgICAgICAgIG5hbWU6J2NhdGUnLFxyXG4gICAgICAgICAgICAgICAgdGl0bGU6J+aMieWIhuexu+aQnOe0oicsXHJcbiAgICAgICAgICAgICAgICB1cmw6Z2V0X2NhdGVfdXJsKCdhcnRpY2xlJyksXHJcbiAgICAgICAgICAgICAgICBodG1sUm93Oic8b3B0aW9uIHZhbHVlPVwie0BpZH1cIj57QGh0bWx9e0B0aXRsZX08L29wdGlvbj4nXHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdzZWFyY2hIb2xkZXInOifmoLnmja7mlofnq6DmoIfpopjmkJzntKInXHJcbiAgICAgICAgfSxjYWxsYmFjayxmaWx0ZXIpO1xyXG4gICAgfSxcclxuICAgIHBpY2tQcm9kdWN0OmZ1bmN0aW9uKGNhbGxiYWNrLGZpbHRlcil7XHJcbiAgICAgICAgcmV0dXJuIHRoaXMucGlja0xpc3Qoe1xyXG4gICAgICAgICAgICAndXJsJzp3aW5kb3cuZ2V0X3NlYXJjaF91cmwoJ3Byb2R1Y3QnKSxcclxuICAgICAgICAgICAgcm93VGVtcGxhdGU6JzxhIGhyZWY9XCJqYXZhc2NyaXB0OlwiIGRhdGEtaWQ9XCJ7QGlkfVwiIGNsYXNzPVwibGlzdC1ncm91cC1pdGVtIGxpc3QtZ3JvdXAtaXRlbS1hY3Rpb25cIj57aWYgQGltYWdlfTxkaXYgc3R5bGU9XCJiYWNrZ3JvdW5kLWltYWdlOnVybCh7QGltYWdlfSlcIiBjbGFzcz1cImltZ3ZpZXdcIiA+PC9kaXY+ey9pZn08ZGl2IGNsYXNzPVwidGV4dC1ibG9ja1wiPlt7QGlkfV0mbmJzcDt7QHRpdGxlfSZuYnNwOzxiciAvPntAbWluX3ByaWNlfX57QG1heF9wcmljZX08L2Rpdj48L2E+JyxcclxuICAgICAgICAgICAgbmFtZTon5Lqn5ZOBJyxcclxuICAgICAgICAgICAgaWRrZXk6J2lkJyxcclxuICAgICAgICAgICAgZXh0ZW5kOntcclxuICAgICAgICAgICAgICAgIG5hbWU6J2NhdGUnLFxyXG4gICAgICAgICAgICAgICAgdGl0bGU6J+aMieWIhuexu+aQnOe0oicsXHJcbiAgICAgICAgICAgICAgICB1cmw6Z2V0X2NhdGVfdXJsKCdwcm9kdWN0JyksXHJcbiAgICAgICAgICAgICAgICBodG1sUm93Oic8b3B0aW9uIHZhbHVlPVwie0BpZH1cIj57QGh0bWx9e0B0aXRsZX08L29wdGlvbj4nXHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdzZWFyY2hIb2xkZXInOifmoLnmja7kuqflk4HlkI3np7DmkJzntKInXHJcbiAgICAgICAgfSxjYWxsYmFjayxmaWx0ZXIpO1xyXG4gICAgfSxcclxuICAgIHBpY2tMb2NhdGU6ZnVuY3Rpb24odHlwZSwgY2FsbGJhY2ssIGxvY2F0ZSl7XHJcbiAgICAgICAgdmFyIHNldHRlZExvY2F0ZT1udWxsO1xyXG4gICAgICAgIHZhciBkbGc9bmV3IERpYWxvZyh7XHJcbiAgICAgICAgICAgICdzaXplJzonbGcnLFxyXG4gICAgICAgICAgICAnYmFja2Ryb3AnOidzdGF0aWMnLFxyXG4gICAgICAgICAgICAnb25zaG93bic6ZnVuY3Rpb24oYm9keSl7XHJcbiAgICAgICAgICAgICAgICB2YXIgYnRuPWJvZHkuZmluZCgnLnNlYXJjaGJ0bicpO1xyXG4gICAgICAgICAgICAgICAgdmFyIGlucHV0PWJvZHkuZmluZCgnLnNlYXJjaHRleHQnKTtcclxuICAgICAgICAgICAgICAgIHZhciBtYXBib3g9Ym9keS5maW5kKCcubWFwJyk7XHJcbiAgICAgICAgICAgICAgICB2YXIgbWFwaW5mbz1ib2R5LmZpbmQoJy5tYXBpbmZvJyk7XHJcbiAgICAgICAgICAgICAgICBtYXBib3guY3NzKCdoZWlnaHQnLCQod2luZG93KS5oZWlnaHQoKSouNik7XHJcbiAgICAgICAgICAgICAgICB2YXIgaXNsb2FkaW5nPWZhbHNlO1xyXG4gICAgICAgICAgICAgICAgdmFyIG1hcD1Jbml0TWFwKCd0ZW5jZW50JyxtYXBib3gsZnVuY3Rpb24oYWRkcmVzcyxsb2NhdGUpe1xyXG4gICAgICAgICAgICAgICAgICAgIG1hcGluZm8uaHRtbChhZGRyZXNzKycmbmJzcDsnK2xvY2F0ZS5sbmcrJywnK2xvY2F0ZS5sYXQpO1xyXG4gICAgICAgICAgICAgICAgICAgIHNldHRlZExvY2F0ZT1sb2NhdGU7XHJcbiAgICAgICAgICAgICAgICB9LGxvY2F0ZSk7XHJcbiAgICAgICAgICAgICAgICBidG4uY2xpY2soZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgICAgICAgICB2YXIgc2VhcmNoPWlucHV0LnZhbCgpO1xyXG4gICAgICAgICAgICAgICAgICAgIG1hcC5zZXRMb2NhdGUoc2VhcmNoKTtcclxuICAgICAgICAgICAgICAgIH0pO1xyXG4gICAgICAgICAgICAgICAgYm9keS5maW5kKCcuc2V0VG9DZW50ZXInKS5jbGljayhmdW5jdGlvbiAoZSkge1xyXG4gICAgICAgICAgICAgICAgICAgIG1hcC5zaG93QXRDZW50ZXIoKTtcclxuICAgICAgICAgICAgICAgIH0pXHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdvbnN1cmUnOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgaWYoIXNldHRlZExvY2F0ZSl7XHJcbiAgICAgICAgICAgICAgICAgICAgdG9hc3RyLndhcm5pbmcoJ+ayoeaciemAieaLqeS9jee9riEnKTtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICBpZih0eXBlb2YgY2FsbGJhY2s9PT0nZnVuY3Rpb24nKXtcclxuICAgICAgICAgICAgICAgICAgICB2YXIgcmVzdWx0ID0gY2FsbGJhY2soc2V0dGVkTG9jYXRlKTtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gcmVzdWx0O1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkuc2hvdygnPGRpdiBjbGFzcz1cImlucHV0LWdyb3VwXCI+PGlucHV0IHR5cGU9XCJ0ZXh0XCIgY2xhc3M9XCJmb3JtLWNvbnRyb2wgc2VhcmNodGV4dFwiIG5hbWU9XCJrZXl3b3JkXCIgcGxhY2Vob2xkZXI9XCLloavlhpnlnLDlnYDmo4DntKLkvY3nva5cIi8+PGRpdiBjbGFzcz1cImlucHV0LWdyb3VwLWFwcGVuZFwiPjxhIGNsYXNzPVwiYnRuIGJ0bi1vdXRsaW5lLXNlY29uZGFyeSBzZWFyY2hidG5cIj48aSBjbGFzcz1cImlvbi1tZC1zZWFyY2hcIj48L2k+PC9hPjwvZGl2PjwvZGl2PicgK1xyXG4gICAgICAgICAgICAnPGRpdiBjbGFzcz1cIm1hcCBtdC0yXCI+PC9kaXY+JyArXHJcbiAgICAgICAgICAgICc8ZGl2IGNsYXNzPVwiZmxvYXQtcmlnaHQgbXQtMiBtYXBhY3Rpb25zXCI+PGEgaHJlZj1cImphdmFzY3JpcHQ6XCIgY2xhc3M9XCJzZXRUb0NlbnRlclwiPuWumuS9jeWIsOWcsOWbvuS4reW/gzwvYT48L2Rpdj4nICtcclxuICAgICAgICAgICAgJzxkaXYgY2xhc3M9XCJtYXBpbmZvIG10LTIgdGV4dC1tdXRlZFwiPuacqumAieaLqeS9jee9rjwvZGl2PicsJ+ivt+mAieaLqeWcsOWbvuS9jee9ricpO1xyXG4gICAgfVxyXG59O1xyXG5cclxualF1ZXJ5KGZ1bmN0aW9uKCQpe1xyXG5cclxuICAgIC8v55uR5o6n5oyJ6ZSuXHJcbiAgICAkKGRvY3VtZW50KS5vbigna2V5ZG93bicsIGZ1bmN0aW9uKGUpe1xyXG4gICAgICAgIGlmKCFEaWFsb2cuaW5zdGFuY2UpcmV0dXJuO1xyXG4gICAgICAgIHZhciBkbGc9RGlhbG9nLmluc3RhbmNlO1xyXG4gICAgICAgIGlmIChlLmtleUNvZGUgPT0gMTMpIHtcclxuICAgICAgICAgICAgZGxnLmJveC5maW5kKCcubW9kYWwtZm9vdGVyIC5idG4nKS5lcShkbGcub3B0aW9ucy5kZWZhdWx0QnRuKS50cmlnZ2VyKCdjbGljaycpO1xyXG4gICAgICAgIH1cclxuICAgICAgICAvL+m7mOiupOW3suebkeWQrOWFs+mXrVxyXG4gICAgICAgIC8qaWYgKGUua2V5Q29kZSA9PSAyNykge1xyXG4gICAgICAgICBzZWxmLmhpZGUoKTtcclxuICAgICAgICAgfSovXHJcbiAgICB9KTtcclxufSk7IiwiXHJcbmpRdWVyeS5leHRlbmQoalF1ZXJ5LmZuLHtcclxuICAgIHRhZ3M6ZnVuY3Rpb24obm0sb251cGRhdGUpe1xyXG4gICAgICAgIHZhciBkYXRhPVtdO1xyXG4gICAgICAgIHZhciB0cGw9JzxzcGFuIGNsYXNzPVwiYmFkZ2UgYmFkZ2UtaW5mb1wiPntAbGFiZWx9PGlucHV0IHR5cGU9XCJoaWRkZW5cIiBuYW1lPVwiJytubSsnXCIgdmFsdWU9XCJ7QGxhYmVsfVwiLz48YnV0dG9uIHR5cGU9XCJidXR0b25cIiBjbGFzcz1cImNsb3NlXCIgZGF0YS1kaXNtaXNzPVwiYWxlcnRcIiBhcmlhLWxhYmVsPVwiQ2xvc2VcIj48c3BhbiBhcmlhLWhpZGRlbj1cInRydWVcIj4mdGltZXM7PC9zcGFuPjwvYnV0dG9uPjwvc3Bhbj4nO1xyXG4gICAgICAgIHZhciBpdGVtPSQodGhpcykucGFyZW50cygnLmZvcm0tY29udHJvbCcpO1xyXG4gICAgICAgIHZhciBsYWJlbGdyb3VwPSQoJzxzcGFuIGNsYXNzPVwiYmFkZ2UtZ3JvdXBcIj48L3NwYW4+Jyk7XHJcbiAgICAgICAgdmFyIGlucHV0PXRoaXM7XHJcbiAgICAgICAgdGhpcy5iZWZvcmUobGFiZWxncm91cCk7XHJcbiAgICAgICAgdGhpcy5vbigna2V5dXAnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgIHZhciB2YWw9JCh0aGlzKS52YWwoKS5yZXBsYWNlKC/vvIwvZywnLCcpO1xyXG4gICAgICAgICAgICB2YXIgdXBkYXRlZD1mYWxzZTtcclxuICAgICAgICAgICAgaWYodmFsICYmIHZhbC5pbmRleE9mKCcsJyk+LTEpe1xyXG4gICAgICAgICAgICAgICAgdmFyIHZhbHM9dmFsLnNwbGl0KCcsJyk7XHJcbiAgICAgICAgICAgICAgICBmb3IodmFyIGk9MDtpPHZhbHMubGVuZ3RoO2krKyl7XHJcbiAgICAgICAgICAgICAgICAgICAgdmFsc1tpXT12YWxzW2ldLnJlcGxhY2UoL15cXHN8XFxzJC9nLCcnKTtcclxuICAgICAgICAgICAgICAgICAgICBpZih2YWxzW2ldICYmIGRhdGEuaW5kZXhPZih2YWxzW2ldKT09PS0xKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgZGF0YS5wdXNoKHZhbHNbaV0pO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBsYWJlbGdyb3VwLmFwcGVuZCh0cGwuY29tcGlsZSh7bGFiZWw6dmFsc1tpXX0pKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgdXBkYXRlZD10cnVlO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIGlucHV0LnZhbCgnJyk7XHJcbiAgICAgICAgICAgICAgICBpZih1cGRhdGVkICYmIG9udXBkYXRlKW9udXBkYXRlKGRhdGEpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkub24oJ2JsdXInLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgIHZhciB2YWw9JCh0aGlzKS52YWwoKTtcclxuICAgICAgICAgICAgaWYodmFsKSB7XHJcbiAgICAgICAgICAgICAgICAkKHRoaXMpLnZhbCh2YWwgKyAnLCcpLnRyaWdnZXIoJ2tleXVwJyk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS50cmlnZ2VyKCdibHVyJyk7XHJcbiAgICAgICAgbGFiZWxncm91cC5vbignY2xpY2snLCcuY2xvc2UnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgIHZhciB0YWc9JCh0aGlzKS5wYXJlbnRzKCcuYmFkZ2UnKS5maW5kKCdpbnB1dCcpLnZhbCgpO1xyXG4gICAgICAgICAgICB2YXIgaWQ9ZGF0YS5pbmRleE9mKHRhZyk7XHJcbiAgICAgICAgICAgIGlmKGlkKWRhdGEuc3BsaWNlKGlkLDEpO1xyXG4gICAgICAgICAgICAkKHRoaXMpLnBhcmVudHMoJy5iYWRnZScpLnJlbW92ZSgpO1xyXG4gICAgICAgICAgICBpZihvbnVwZGF0ZSlvbnVwZGF0ZShkYXRhKTtcclxuICAgICAgICB9KTtcclxuICAgICAgICBpdGVtLmNsaWNrKGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgIGlucHV0LmZvY3VzKCk7XHJcbiAgICAgICAgfSk7XHJcbiAgICB9XHJcbn0pOyIsIi8v5pel5pyf57uE5Lu2XHJcbmlmKCQuZm4uZGF0ZXRpbWVwaWNrZXIpIHtcclxuICAgIHZhciB0b29sdGlwcz0ge1xyXG4gICAgICAgIHRvZGF5OiAn5a6a5L2N5b2T5YmN5pel5pyfJyxcclxuICAgICAgICBjbGVhcjogJ+a4hemZpOW3sumAieaXpeacnycsXHJcbiAgICAgICAgY2xvc2U6ICflhbPpl63pgInmi6nlmagnLFxyXG4gICAgICAgIHNlbGVjdE1vbnRoOiAn6YCJ5oup5pyI5Lu9JyxcclxuICAgICAgICBwcmV2TW9udGg6ICfkuIrkuKrmnIgnLFxyXG4gICAgICAgIG5leHRNb250aDogJ+S4i+S4quaciCcsXHJcbiAgICAgICAgc2VsZWN0WWVhcjogJ+mAieaLqeW5tOS7vScsXHJcbiAgICAgICAgcHJldlllYXI6ICfkuIrkuIDlubQnLFxyXG4gICAgICAgIG5leHRZZWFyOiAn5LiL5LiA5bm0JyxcclxuICAgICAgICBzZWxlY3REZWNhZGU6ICfpgInmi6nlubTku73ljLrpl7QnLFxyXG4gICAgICAgIHNlbGVjdFRpbWU6J+mAieaLqeaXtumXtCcsXHJcbiAgICAgICAgcHJldkRlY2FkZTogJ+S4iuS4gOWMuumXtCcsXHJcbiAgICAgICAgbmV4dERlY2FkZTogJ+S4i+S4gOWMuumXtCcsXHJcbiAgICAgICAgcHJldkNlbnR1cnk6ICfkuIrkuKrkuJbnuqonLFxyXG4gICAgICAgIG5leHRDZW50dXJ5OiAn5LiL5Liq5LiW57qqJ1xyXG4gICAgfTtcclxuXHJcbiAgICBmdW5jdGlvbiB0cmFuc09wdGlvbihvcHRpb24pIHtcclxuICAgICAgICBpZighb3B0aW9uKXJldHVybiB7fTtcclxuICAgICAgICB2YXIgbmV3b3B0PXt9O1xyXG4gICAgICAgIGZvcih2YXIgaSBpbiBvcHRpb24pe1xyXG4gICAgICAgICAgICBzd2l0Y2ggKGkpe1xyXG4gICAgICAgICAgICAgICAgY2FzZSAndmlld21vZGUnOlxyXG4gICAgICAgICAgICAgICAgICAgIG5ld29wdFsndmlld01vZGUnXT1vcHRpb25baV07XHJcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgICAgICBjYXNlICdrZWVwb3Blbic6XHJcbiAgICAgICAgICAgICAgICAgICAgbmV3b3B0WydrZWVwT3BlbiddPW9wdGlvbltpXTtcclxuICAgICAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgICAgIGRlZmF1bHQ6XHJcbiAgICAgICAgICAgICAgICAgICAgbmV3b3B0W2ldPW9wdGlvbltpXTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH1cclxuICAgICAgICByZXR1cm4gbmV3b3B0O1xyXG4gICAgfVxyXG4gICAgJCgnLmRhdGVwaWNrZXInKS5lYWNoKGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgdmFyIGNvbmZpZz0kLmV4dGVuZCh7XHJcbiAgICAgICAgICAgIHRvb2x0aXBzOnRvb2x0aXBzLFxyXG4gICAgICAgICAgICBmb3JtYXQ6ICdZWVlZLU1NLUREJyxcclxuICAgICAgICAgICAgbG9jYWxlOiAnemgtY24nLFxyXG4gICAgICAgICAgICBzaG93Q2xlYXI6dHJ1ZSxcclxuICAgICAgICAgICAgc2hvd1RvZGF5QnV0dG9uOnRydWUsXHJcbiAgICAgICAgICAgIHNob3dDbG9zZTp0cnVlLFxyXG4gICAgICAgICAgICBrZWVwSW52YWxpZDp0cnVlXHJcbiAgICAgICAgfSx0cmFuc09wdGlvbigkKHRoaXMpLmRhdGEoKSkpO1xyXG5cclxuICAgICAgICAkKHRoaXMpLmRhdGV0aW1lcGlja2VyKGNvbmZpZyk7XHJcbiAgICB9KTtcclxuXHJcbiAgICAkKCcuZGF0ZS1yYW5nZScpLmVhY2goZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHZhciBmcm9tID0gJCh0aGlzKS5maW5kKCdbbmFtZT1mcm9tZGF0ZV0sLmZyb21kYXRlJyksIHRvID0gJCh0aGlzKS5maW5kKCdbbmFtZT10b2RhdGVdLC50b2RhdGUnKTtcclxuICAgICAgICB2YXIgb3B0aW9ucyA9ICQuZXh0ZW5kKHtcclxuICAgICAgICAgICAgdG9vbHRpcHM6dG9vbHRpcHMsXHJcbiAgICAgICAgICAgIGZvcm1hdDogJ1lZWVktTU0tREQnLFxyXG4gICAgICAgICAgICBsb2NhbGU6J3poLWNuJyxcclxuICAgICAgICAgICAgc2hvd0NsZWFyOnRydWUsXHJcbiAgICAgICAgICAgIHNob3dUb2RheUJ1dHRvbjp0cnVlLFxyXG4gICAgICAgICAgICBzaG93Q2xvc2U6dHJ1ZSxcclxuICAgICAgICAgICAga2VlcEludmFsaWQ6dHJ1ZVxyXG4gICAgICAgIH0sJCh0aGlzKS5kYXRhKCkpO1xyXG4gICAgICAgIGZyb20uZGF0ZXRpbWVwaWNrZXIob3B0aW9ucykub24oJ2RwLmNoYW5nZScsIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgaWYgKGZyb20udmFsKCkpIHtcclxuICAgICAgICAgICAgICAgIHRvLmRhdGEoJ0RhdGVUaW1lUGlja2VyJykubWluRGF0ZShmcm9tLnZhbCgpKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pO1xyXG4gICAgICAgIHRvLmRhdGV0aW1lcGlja2VyKG9wdGlvbnMpLm9uKCdkcC5jaGFuZ2UnLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIGlmICh0by52YWwoKSkge1xyXG4gICAgICAgICAgICAgICAgZnJvbS5kYXRhKCdEYXRlVGltZVBpY2tlcicpLm1heERhdGUodG8udmFsKCkpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSk7XHJcbiAgICB9KTtcclxufSIsIlxyXG4oZnVuY3Rpb24od2luZG93LCQpIHtcclxuICAgIHZhciBhcGlzID0ge1xyXG4gICAgICAgICdiYWlkdSc6ICcvL2FwaS5tYXAuYmFpZHUuY29tL2FwaT9haz1yTzl0T2RFV0ZmdnlHZ0RraVdxRmp4SzYmdj0xLjUmc2VydmljZXM9ZmFsc2UmY2FsbGJhY2s9JyxcclxuICAgICAgICAnZ29vZ2xlJzogJy8vbWFwcy5nb29nbGUuY29tL21hcHMvYXBpL2pzP2tleT1BSXphU3lCOGxvcnZsNkV0cUlXejY3YmpXQnJ1T2htOU5ZUzFlMjQmY2FsbGJhY2s9JyxcclxuICAgICAgICAndGVuY2VudCc6ICcvL21hcC5xcS5jb20vYXBpL2pzP3Y9Mi5leHAma2V5PTdJNUJaLVFVRTZSLUpYTFdWLVdUVkFBLUNKTVlGLTdQQkJJJmNhbGxiYWNrPScsXHJcbiAgICAgICAgJ2dhb2RlJzogJy8vd2ViYXBpLmFtYXAuY29tL21hcHM/dj0xLjMma2V5PTNlYzMxMWI1ZGIwZDU5N2U3OTQyMmVlYjlhNmQ0NDQ5JmNhbGxiYWNrPSdcclxuICAgIH07XHJcblxyXG4gICAgZnVuY3Rpb24gbG9hZFNjcmlwdChzcmMpIHtcclxuICAgICAgICB2YXIgc2NyaXB0ID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudChcInNjcmlwdFwiKTtcclxuICAgICAgICBzY3JpcHQudHlwZSA9IFwidGV4dC9qYXZhc2NyaXB0XCI7XHJcbiAgICAgICAgc2NyaXB0LnNyYyA9IHNyYztcclxuICAgICAgICBkb2N1bWVudC5ib2R5LmFwcGVuZENoaWxkKHNjcmlwdCk7XHJcbiAgICB9XHJcblxyXG4gICAgdmFyIG1hcE9iaixtYXBCb3gsb25QaWNrO1xyXG5cclxuICAgIGZ1bmN0aW9uIEluaXRNYXAobWFwa2V5LGJveCxjYWxsYmFjayxsb2NhdGUpIHtcclxuICAgICAgICBpZiAobWFwT2JqKSBtYXBPYmouaGlkZSgpO1xyXG4gICAgICAgIG1hcEJveD0kKGJveCk7XHJcbiAgICAgICAgb25QaWNrPWNhbGxiYWNrO1xyXG5cclxuICAgICAgICBzd2l0Y2ggKG1hcGtleS50b0xvd2VyQ2FzZSgpKSB7XHJcbiAgICAgICAgICAgIGNhc2UgJ2JhaWR1JzpcclxuICAgICAgICAgICAgICAgIG1hcE9iaiA9IG5ldyBCYWlkdU1hcCgpO1xyXG4gICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgIGNhc2UgJ2dvb2dsZSc6XHJcbiAgICAgICAgICAgICAgICBtYXBPYmogPSBuZXcgR29vZ2xlTWFwKCk7XHJcbiAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgY2FzZSAndGVuY2VudCc6XHJcbiAgICAgICAgICAgIGNhc2UgJ3FxJzpcclxuICAgICAgICAgICAgICAgIG1hcE9iaiA9IG5ldyBUZW5jZW50TWFwKCk7XHJcbiAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgY2FzZSAnZ2FvZGUnOlxyXG4gICAgICAgICAgICAgICAgbWFwT2JqID0gbmV3IEdhb2RlTWFwKCk7XHJcbiAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICB9XHJcbiAgICAgICAgaWYgKCFtYXBPYmopIHJldHVybiB0b2FzdHIud2FybmluZygn5LiN5pSv5oyB6K+l5Zyw5Zu+57G75Z6LJyk7XHJcbiAgICAgICAgaWYobG9jYXRlKXtcclxuICAgICAgICAgICAgaWYodHlwZW9mIGxvY2F0ZT09PSdzdHJpbmcnKXtcclxuICAgICAgICAgICAgICAgIHZhciBsb2M9bG9jYXRlLnNwbGl0KCcsJyk7XHJcbiAgICAgICAgICAgICAgICBsb2NhdGU9e1xyXG4gICAgICAgICAgICAgICAgICAgIGxuZzpwYXJzZUZsb2F0KGxvY1swXSksXHJcbiAgICAgICAgICAgICAgICAgICAgbGF0OnBhcnNlRmxvYXQobG9jWzFdKVxyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIG1hcE9iai5sb2NhdGU9bG9jYXRlO1xyXG4gICAgICAgIH1cclxuICAgICAgICBtYXBPYmouc2V0TWFwKCk7XHJcblxyXG4gICAgICAgIHJldHVybiBtYXBPYmo7XHJcbiAgICB9XHJcblxyXG4gICAgZnVuY3Rpb24gQmFzZU1hcCh0eXBlKSB7XHJcbiAgICAgICAgdGhpcy5tYXBUeXBlID0gdHlwZTtcclxuICAgICAgICB0aGlzLmlzaGlkZSA9IGZhbHNlO1xyXG4gICAgICAgIHRoaXMuaXNzaG93ID0gZmFsc2U7XHJcbiAgICAgICAgdGhpcy50b3Nob3cgPSBmYWxzZTtcclxuICAgICAgICB0aGlzLm1hcmtlciA9IG51bGw7XHJcbiAgICAgICAgdGhpcy5pbmZvV2luZG93ID0gbnVsbDtcclxuICAgICAgICB0aGlzLm1hcGJveCA9IG51bGw7XHJcbiAgICAgICAgdGhpcy5sb2NhdGUgPSB7bG5nOjExNi4zOTY3OTUsbGF0OjM5LjkzMzA4NH07XHJcbiAgICAgICAgdGhpcy5tYXAgPSBudWxsO1xyXG4gICAgfVxyXG5cclxuICAgIEJhc2VNYXAucHJvdG90eXBlLmlzQVBJUmVhZHkgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgfTtcclxuICAgIEJhc2VNYXAucHJvdG90eXBlLnBvaW50VG9PYmplY3QgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICB9O1xyXG4gICAgQmFzZU1hcC5wcm90b3R5cGUub2JqZWN0VG9Qb2ludCA9IGZ1bmN0aW9uICgpIHtcclxuICAgIH07XHJcbiAgICBCYXNlTWFwLnByb3RvdHlwZS5zZXRNYXAgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICB9O1xyXG4gICAgQmFzZU1hcC5wcm90b3R5cGUuc2hvd0luZm8gPSBmdW5jdGlvbiAoKSB7XHJcbiAgICB9O1xyXG4gICAgQmFzZU1hcC5wcm90b3R5cGUuZ2V0Q2VudGVyID0gZnVuY3Rpb24gKCkge1xyXG4gICAgfTtcclxuICAgIEJhc2VNYXAucHJvdG90eXBlLmdldEFkZHJlc3MgPSBmdW5jdGlvbiAocnMpIHtcclxuICAgICAgICByZXR1cm4gXCJcIjtcclxuICAgIH07XHJcbiAgICBCYXNlTWFwLnByb3RvdHlwZS5zZXRMb2NhdGUgPSBmdW5jdGlvbiAoYWRkcmVzcykge1xyXG4gICAgfTtcclxuXHJcbiAgICBCYXNlTWFwLnByb3RvdHlwZS5sb2FkQVBJID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHZhciBzZWxmID0gdGhpcztcclxuICAgICAgICBpZiAoIXRoaXMuaXNBUElSZWFkeSgpKSB7XHJcbiAgICAgICAgICAgIHRoaXMubWFwYm94ID0gJCgnPGRpdiBpZD1cIicgKyB0aGlzLm1hcFR5cGUgKyAnbWFwXCIgY2xhc3M9XCJtYXBib3hcIj5sb2FkaW5nLi4uPC9kaXY+Jyk7XHJcbiAgICAgICAgICAgIG1hcEJveC5hcHBlbmQodGhpcy5tYXBib3gpO1xyXG5cclxuICAgICAgICAgICAgLy9jb25zb2xlLmxvZyh0aGlzLm1hcFR5cGUrJyBtYXBsb2FkaW5nLi4uJyk7XHJcbiAgICAgICAgICAgIHZhciBmdW5jID0gJ21hcGxvYWQnICsgbmV3IERhdGUoKS5nZXRUaW1lKCk7XHJcbiAgICAgICAgICAgIHdpbmRvd1tmdW5jXSA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgIHNlbGYuc2V0TWFwKCk7XHJcbiAgICAgICAgICAgICAgICBkZWxldGUgd2luZG93W2Z1bmNdO1xyXG4gICAgICAgICAgICB9O1xyXG4gICAgICAgICAgICBsb2FkU2NyaXB0KGFwaXNbdGhpcy5tYXBUeXBlXSArIGZ1bmMpO1xyXG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgLy9jb25zb2xlLmxvZyh0aGlzLm1hcFR5cGUgKyAnIG1hcGxvYWRlZCcpO1xyXG4gICAgICAgICAgICB0aGlzLm1hcGJveCA9ICQoJyMnICsgdGhpcy5tYXBUeXBlICsgJ21hcCcpO1xyXG4gICAgICAgICAgICBpZiAodGhpcy5tYXBib3gubGVuZ3RoIDwgMSkge1xyXG4gICAgICAgICAgICAgICAgdGhpcy5tYXBib3ggPSAkKCc8ZGl2IGlkPVwiJyArIHRoaXMubWFwVHlwZSArICdtYXBcIiBjbGFzcz1cIm1hcGJveFwiPjwvZGl2PicpO1xyXG4gICAgICAgICAgICAgICAgbWFwQm94LmFwcGVuZCh0aGlzLm1hcGJveCk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgcmV0dXJuIHRydWU7XHJcbiAgICAgICAgfVxyXG4gICAgfTtcclxuICAgIEJhc2VNYXAucHJvdG90eXBlLnNldEluZm9Db250ZW50ID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIGlmICghdGhpcy5pbmZvV2luZG93KSByZXR1cm47XHJcbiAgICAgICAgdmFyIHRpdGxlID0gJzxiPuW9k+WJjeS9jee9rjwvYj4nO1xyXG4gICAgICAgIHZhciBhZGRyID0gJzxwIHN0eWxlPVwibGluZS1oZWlnaHQ6MS42ZW07XCI+PC9wPic7XHJcbiAgICAgICAgaWYgKHRoaXMuaW5mb1dpbmRvdy5zZXRUaXRsZSkge1xyXG4gICAgICAgICAgICB0aGlzLmluZm9XaW5kb3cuc2V0VGl0bGUodGl0bGUpO1xyXG4gICAgICAgICAgICB0aGlzLmluZm9XaW5kb3cuc2V0Q29udGVudChhZGRyKTtcclxuICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICB2YXIgY29udGVudCA9ICc8aDM+JyArIHRpdGxlICsgJzwvaDM+PGRpdiBzdHlsZT1cIndpZHRoOjI1MHB4XCI+JyArIGFkZHIgKyAnPC9kaXY+JztcclxuICAgICAgICAgICAgdGhpcy5pbmZvV2luZG93LnNldENvbnRlbnQoY29udGVudCk7XHJcbiAgICAgICAgfVxyXG4gICAgfTtcclxuICAgIEJhc2VNYXAucHJvdG90eXBlLnNob3dBdENlbnRlcj1mdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgdmFyIGNlbnRlcj10aGlzLmdldENlbnRlcigpO1xyXG4gICAgICAgIHRoaXMuc2hvd0luZm8oY2VudGVyKTtcclxuICAgIH07XHJcbiAgICBCYXNlTWFwLnByb3RvdHlwZS5zaG93TG9jYXRpb25JbmZvID0gZnVuY3Rpb24gKHB0LCBycykge1xyXG5cclxuICAgICAgICB0aGlzLnNob3dJbmZvKCk7XHJcbiAgICAgICAgdmFyIGFkZHJlc3M9dGhpcy5nZXRBZGRyZXNzKHJzKTtcclxuICAgICAgICB2YXIgbG9jYXRlPXt9O1xyXG4gICAgICAgIGlmICh0eXBlb2YgKHB0LmxuZykgPT09ICdmdW5jdGlvbicpIHtcclxuICAgICAgICAgICAgbG9jYXRlLmxuZz1wdC5sbmcoKTtcclxuICAgICAgICAgICAgbG9jYXRlLmxhdD1wdC5sYXQoKTtcclxuICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICBsb2NhdGUubG5nPXB0LmxuZztcclxuICAgICAgICAgICAgbG9jYXRlLmxhdD1wdC5sYXQ7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICBvblBpY2soYWRkcmVzcyxsb2NhdGUpO1xyXG4gICAgfTtcclxuICAgIEJhc2VNYXAucHJvdG90eXBlLnNob3cgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgdGhpcy5pc2hpZGUgPSBmYWxzZTtcclxuICAgICAgICB0aGlzLnNldE1hcCgpO1xyXG4gICAgICAgIHRoaXMuc2hvd0luZm8oKTtcclxuICAgIH07XHJcbiAgICBCYXNlTWFwLnByb3RvdHlwZS5oaWRlID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHRoaXMuaXNoaWRlID0gdHJ1ZTtcclxuICAgICAgICBpZiAodGhpcy5pbmZvV2luZG93KSB7XHJcbiAgICAgICAgICAgIHRoaXMuaW5mb1dpbmRvdy5jbG9zZSgpO1xyXG4gICAgICAgIH1cclxuICAgICAgICBpZiAodGhpcy5tYXBib3gpIHtcclxuICAgICAgICAgICAgJCh0aGlzLm1hcGJveCkucmVtb3ZlKCk7XHJcbiAgICAgICAgfVxyXG4gICAgfTtcclxuXHJcblxyXG4gICAgZnVuY3Rpb24gQmFpZHVNYXAoKSB7XHJcbiAgICAgICAgQmFzZU1hcC5jYWxsKHRoaXMsIFwiYmFpZHVcIik7XHJcbiAgICB9XHJcblxyXG4gICAgQmFpZHVNYXAucHJvdG90eXBlID0gbmV3IEJhc2VNYXAoKTtcclxuICAgIEJhaWR1TWFwLnByb3RvdHlwZS5jb25zdHJ1Y3RvciA9IEJhaWR1TWFwO1xyXG4gICAgQmFpZHVNYXAucHJvdG90eXBlLmlzQVBJUmVhZHkgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgcmV0dXJuICEhd2luZG93WydCTWFwJ107XHJcbiAgICB9O1xyXG5cclxuICAgIEJhaWR1TWFwLnByb3RvdHlwZS5zZXRNYXAgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgdmFyIHNlbGYgPSB0aGlzO1xyXG4gICAgICAgIGlmICh0aGlzLmlzc2hvdyB8fCB0aGlzLmlzaGlkZSkgcmV0dXJuO1xyXG4gICAgICAgIGlmICghdGhpcy5sb2FkQVBJKCkpIHJldHVybjtcclxuXHJcbiAgICAgICAgdmFyIG1hcCA9IHNlbGYubWFwID0gbmV3IEJNYXAuTWFwKHRoaXMubWFwYm94LmF0dHIoJ2lkJykpOyAvL+WIneWni+WMluWcsOWbvlxyXG4gICAgICAgIG1hcC5hZGRDb250cm9sKG5ldyBCTWFwLk5hdmlnYXRpb25Db250cm9sKCkpOyAgLy/liJ3lp4vljJblnLDlm77mjqfku7ZcclxuICAgICAgICBtYXAuYWRkQ29udHJvbChuZXcgQk1hcC5TY2FsZUNvbnRyb2woKSk7XHJcbiAgICAgICAgbWFwLmFkZENvbnRyb2wobmV3IEJNYXAuT3ZlcnZpZXdNYXBDb250cm9sKCkpO1xyXG4gICAgICAgIG1hcC5lbmFibGVTY3JvbGxXaGVlbFpvb20oKTtcclxuXHJcbiAgICAgICAgdmFyIHBvaW50ID0gdGhpcy5vYmplY3RUb1BvaW50KHRoaXMubG9jYXRlKTtcclxuICAgICAgICBtYXAuY2VudGVyQW5kWm9vbShwb2ludCwgMTUpOyAvL+WIneWni+WMluWcsOWbvuS4reW/g+eCuVxyXG4gICAgICAgIHRoaXMubWFya2VyID0gbmV3IEJNYXAuTWFya2VyKHBvaW50KTsgLy/liJ3lp4vljJblnLDlm77moIforrBcclxuICAgICAgICB0aGlzLm1hcmtlci5lbmFibGVEcmFnZ2luZygpOyAvL+agh+iusOW8gOWQr+aLluaLvVxyXG5cclxuICAgICAgICB2YXIgZ2MgPSBuZXcgQk1hcC5HZW9jb2RlcigpOyAvL+WcsOWdgOino+aekOexu1xyXG4gICAgICAgIC8v5re75Yqg5qCH6K6w5ouW5ou955uR5ZCsXHJcbiAgICAgICAgdGhpcy5tYXJrZXIuYWRkRXZlbnRMaXN0ZW5lcihcImRyYWdlbmRcIiwgZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICAgICAgLy/ojrflj5blnLDlnYDkv6Hmga9cclxuICAgICAgICAgICAgZ2MuZ2V0TG9jYXRpb24oZS5wb2ludCwgZnVuY3Rpb24gKHJzKSB7XHJcbiAgICAgICAgICAgICAgICBzZWxmLnNob3dMb2NhdGlvbkluZm8oZS5wb2ludCwgcnMpO1xyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgLy/mt7vliqDmoIforrDngrnlh7vnm5HlkKxcclxuICAgICAgICB0aGlzLm1hcmtlci5hZGRFdmVudExpc3RlbmVyKFwiY2xpY2tcIiwgZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICAgICAgZ2MuZ2V0TG9jYXRpb24oZS5wb2ludCwgZnVuY3Rpb24gKHJzKSB7XHJcbiAgICAgICAgICAgICAgICBzZWxmLnNob3dMb2NhdGlvbkluZm8oZS5wb2ludCwgcnMpO1xyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgbWFwLmFkZE92ZXJsYXkodGhpcy5tYXJrZXIpOyAvL+Wwhuagh+iusOa3u+WKoOWIsOWcsOWbvuS4rVxyXG5cclxuICAgICAgICBnYy5nZXRMb2NhdGlvbihwb2ludCwgZnVuY3Rpb24gKHJzKSB7XHJcbiAgICAgICAgICAgIHNlbGYuc2hvd0xvY2F0aW9uSW5mbyhwb2ludCwgcnMpO1xyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICB0aGlzLmluZm9XaW5kb3cgPSBuZXcgQk1hcC5JbmZvV2luZG93KFwiXCIsIHtcclxuICAgICAgICAgICAgd2lkdGg6IDI1MCxcclxuICAgICAgICAgICAgdGl0bGU6IFwiXCJcclxuICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgdGhpcy5pc3Nob3cgPSB0cnVlO1xyXG4gICAgICAgIGlmICh0aGlzLnRvc2hvdykge1xyXG4gICAgICAgICAgICB0aGlzLnNob3dJbmZvKCk7XHJcbiAgICAgICAgICAgIHRoaXMudG9zaG93ID0gZmFsc2U7XHJcbiAgICAgICAgfVxyXG4gICAgfTtcclxuICAgIEJhaWR1TWFwLnByb3RvdHlwZS5wb2ludFRvT2JqZWN0ID0gZnVuY3Rpb24gKHBvaW50KSB7XHJcbiAgICAgICAgcmV0dXJuIHtcclxuICAgICAgICAgICAgbG5nOnBvaW50LmxuZyxcclxuICAgICAgICAgICAgbGF0OnBvaW50LmxhdFxyXG4gICAgICAgIH07XHJcbiAgICB9O1xyXG4gICAgQmFpZHVNYXAucHJvdG90eXBlLm9iamVjdFRvUG9pbnQgPSBmdW5jdGlvbiAob2JqZWN0KSB7XHJcbiAgICAgICAgcmV0dXJuIG5ldyBCTWFwLlBvaW50KHBhcnNlRmxvYXQob2JqZWN0LmxuZykscGFyc2VGbG9hdChvYmplY3QubGF0KSk7XHJcbiAgICB9O1xyXG4gICAgQmFpZHVNYXAucHJvdG90eXBlLmdldENlbnRlciA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICB2YXIgY2VudGVyPXRoaXMubWFwLmdldENlbnRlcigpO1xyXG4gICAgICAgIHJldHVybiB0aGlzLnBvaW50VG9PYmplY3QoY2VudGVyKTtcclxuICAgIH07XHJcblxyXG4gICAgQmFpZHVNYXAucHJvdG90eXBlLnNob3dJbmZvID0gZnVuY3Rpb24gKHBvaW50KSB7XHJcbiAgICAgICAgaWYgKHRoaXMuaXNoaWRlKSByZXR1cm47XHJcbiAgICAgICAgaWYgKCF0aGlzLmlzc2hvdykge1xyXG4gICAgICAgICAgICB0aGlzLnRvc2hvdyA9IHRydWU7XHJcbiAgICAgICAgICAgIHJldHVybjtcclxuICAgICAgICB9XHJcbiAgICAgICAgaWYocG9pbnQpe1xyXG4gICAgICAgICAgICB0aGlzLmxvY2F0ZT1wb2ludDtcclxuICAgICAgICAgICAgdGhpcy5tYXJrZXIucGFuVG8obmV3IEJNYXAuUG9pbnQocG9pbnQubG5nLHBvaW50LmxhdCkpO1xyXG4gICAgICAgIH1cclxuICAgICAgICB0aGlzLnNldEluZm9Db250ZW50KCk7XHJcblxyXG4gICAgICAgIHRoaXMubWFya2VyLm9wZW5JbmZvV2luZG93KHRoaXMuaW5mb1dpbmRvdyk7XHJcbiAgICB9O1xyXG4gICAgQmFpZHVNYXAucHJvdG90eXBlLmdldEFkZHJlc3MgPSBmdW5jdGlvbiAocnMpIHtcclxuICAgICAgICB2YXIgYWRkQ29tcCA9IHJzLmFkZHJlc3NDb21wb25lbnRzO1xyXG4gICAgICAgIGlmKGFkZENvbXApIHtcclxuICAgICAgICAgICAgcmV0dXJuIGFkZENvbXAucHJvdmluY2UgKyBcIiwgXCIgKyBhZGRDb21wLmNpdHkgKyBcIiwgXCIgKyBhZGRDb21wLmRpc3RyaWN0ICsgXCIsIFwiICsgYWRkQ29tcC5zdHJlZXQgKyBcIiwgXCIgKyBhZGRDb21wLnN0cmVldE51bWJlcjtcclxuICAgICAgICB9XHJcbiAgICB9O1xyXG4gICAgQmFpZHVNYXAucHJvdG90eXBlLnNldExvY2F0ZSA9IGZ1bmN0aW9uIChhZGRyZXNzKSB7XHJcbiAgICAgICAgLy8g5Yib5bu65Zyw5Z2A6Kej5p6Q5Zmo5a6e5L6LXHJcbiAgICAgICAgdmFyIG15R2VvID0gbmV3IEJNYXAuR2VvY29kZXIoKTtcclxuICAgICAgICB2YXIgc2VsZiA9IHRoaXM7XHJcbiAgICAgICAgbXlHZW8uZ2V0UG9pbnQoYWRkcmVzcywgZnVuY3Rpb24gKHBvaW50KSB7XHJcbiAgICAgICAgICAgIGlmIChwb2ludCkge1xyXG4gICAgICAgICAgICAgICAgc2VsZi5tYXAuY2VudGVyQW5kWm9vbShwb2ludCwgMTEpO1xyXG4gICAgICAgICAgICAgICAgc2VsZi5tYXJrZXIuc2V0UG9zaXRpb24ocG9pbnQpO1xyXG4gICAgICAgICAgICAgICAgbXlHZW8uZ2V0TG9jYXRpb24ocG9pbnQsIGZ1bmN0aW9uIChycykge1xyXG4gICAgICAgICAgICAgICAgICAgIHNlbGYuc2hvd0xvY2F0aW9uSW5mbyhwb2ludCwgcnMpO1xyXG4gICAgICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICB0b2FzdHIud2FybmluZyhcIuWcsOWdgOS/oeaBr+S4jeato+ehru+8jOWumuS9jeWksei0pVwiKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0sICcnKTtcclxuICAgIH07XHJcblxyXG5cclxuICAgIGZ1bmN0aW9uIEdvb2dsZU1hcCgpIHtcclxuICAgICAgICBCYXNlTWFwLmNhbGwodGhpcywgXCJnb29nbGVcIik7XHJcbiAgICAgICAgdGhpcy5pbmZvT3B0cyA9IHtcclxuICAgICAgICAgICAgd2lkdGg6IDI1MCwgICAgIC8v5L+h5oGv56qX5Y+j5a695bqmXHJcbiAgICAgICAgICAgIC8vICAgaGVpZ2h0OiAxMDAsICAgICAvL+S/oeaBr+eql+WPo+mrmOW6plxyXG4gICAgICAgICAgICB0aXRsZTogXCJcIiAgLy/kv6Hmga/nqpflj6PmoIfpophcclxuICAgICAgICB9O1xyXG4gICAgfVxyXG5cclxuICAgIEdvb2dsZU1hcC5wcm90b3R5cGUgPSBuZXcgQmFzZU1hcCgpO1xyXG4gICAgR29vZ2xlTWFwLnByb3RvdHlwZS5jb25zdHJ1Y3RvciA9IEdvb2dsZU1hcDtcclxuICAgIEdvb2dsZU1hcC5wcm90b3R5cGUuaXNBUElSZWFkeSA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICByZXR1cm4gd2luZG93Wydnb29nbGUnXSAmJiB3aW5kb3dbJ2dvb2dsZSddWydtYXBzJ11cclxuICAgIH07XHJcbiAgICBHb29nbGVNYXAucHJvdG90eXBlLnNldE1hcCA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICB2YXIgc2VsZiA9IHRoaXM7XHJcbiAgICAgICAgaWYgKHRoaXMuaXNzaG93IHx8IHRoaXMuaXNoaWRlKSByZXR1cm47XHJcbiAgICAgICAgaWYgKCF0aGlzLmxvYWRBUEkoKSkgcmV0dXJuO1xyXG5cclxuICAgICAgICAvL+ivtOaYjuWcsOWbvuW3suWIh+aNolxyXG4gICAgICAgIGlmICh0aGlzLm1hcGJveC5sZW5ndGggPCAxKSByZXR1cm47XHJcblxyXG4gICAgICAgIHZhciBtYXAgPSBzZWxmLm1hcCA9IG5ldyBnb29nbGUubWFwcy5NYXAodGhpcy5tYXBib3hbMF0sIHtcclxuICAgICAgICAgICAgem9vbTogMTUsXHJcbiAgICAgICAgICAgIGRyYWdnYWJsZTogdHJ1ZSxcclxuICAgICAgICAgICAgc2NhbGVDb250cm9sOiB0cnVlLFxyXG4gICAgICAgICAgICBzdHJlZXRWaWV3Q29udHJvbDogdHJ1ZSxcclxuICAgICAgICAgICAgem9vbUNvbnRyb2w6IHRydWVcclxuICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgLy/ojrflj5bnu4/nuqzluqblnZDmoIflgLxcclxuICAgICAgICB2YXIgcG9pbnQgPSB0aGlzLm9iamVjdFRvUG9pbnQodGhpcy5sb2NhdGUpO1xyXG4gICAgICAgIG1hcC5wYW5Ubyhwb2ludCk7XHJcbiAgICAgICAgdGhpcy5tYXJrZXIgPSBuZXcgZ29vZ2xlLm1hcHMuTWFya2VyKHtwb3NpdGlvbjogcG9pbnQsIG1hcDogbWFwLCBkcmFnZ2FibGU6IHRydWV9KTtcclxuXHJcblxyXG4gICAgICAgIHZhciBnYyA9IG5ldyBnb29nbGUubWFwcy5HZW9jb2RlcigpO1xyXG5cclxuICAgICAgICB0aGlzLm1hcmtlci5hZGRMaXN0ZW5lcihcImRyYWdlbmRcIiwgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICBwb2ludCA9IHNlbGYubWFya2VyLmdldFBvc2l0aW9uKCk7XHJcbiAgICAgICAgICAgIGdjLmdlb2NvZGUoeydsb2NhdGlvbic6IHBvaW50fSwgZnVuY3Rpb24gKHJzKSB7XHJcbiAgICAgICAgICAgICAgICBzZWxmLnNob3dMb2NhdGlvbkluZm8ocG9pbnQsIHJzKTtcclxuICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIC8v5re75Yqg5qCH6K6w54K55Ye755uR5ZCsXHJcbiAgICAgICAgdGhpcy5tYXJrZXIuYWRkTGlzdGVuZXIoXCJjbGlja1wiLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIHBvaW50ID0gc2VsZi5tYXJrZXIuZ2V0UG9zaXRpb24oKTtcclxuICAgICAgICAgICAgZ2MuZ2VvY29kZSh7J2xvY2F0aW9uJzogcG9pbnR9LCBmdW5jdGlvbiAocnMpIHtcclxuICAgICAgICAgICAgICAgIHNlbGYuc2hvd0xvY2F0aW9uSW5mbyhwb2ludCwgcnMpO1xyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgZ2MuZ2VvY29kZSh7J2xvY2F0aW9uJzogcG9pbnR9LCBmdW5jdGlvbiAocnMpIHtcclxuICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKHBvaW50LCBycyk7XHJcbiAgICAgICAgfSk7XHJcbiAgICAgICAgdGhpcy5pbmZvV2luZG93ID0gbmV3IGdvb2dsZS5tYXBzLkluZm9XaW5kb3coe21hcDogbWFwfSk7XHJcbiAgICAgICAgdGhpcy5pbmZvV2luZG93LnNldFBvc2l0aW9uKHBvaW50KTtcclxuXHJcbiAgICAgICAgdGhpcy5pc3Nob3cgPSB0cnVlO1xyXG4gICAgICAgIGlmICh0aGlzLnRvc2hvdykge1xyXG4gICAgICAgICAgICB0aGlzLnNob3dJbmZvKCk7XHJcbiAgICAgICAgICAgIHRoaXMudG9zaG93ID0gZmFsc2U7XHJcbiAgICAgICAgfVxyXG4gICAgfTtcclxuXHJcbiAgICBHb29nbGVNYXAucHJvdG90eXBlLnBvaW50VG9PYmplY3QgPSBmdW5jdGlvbiAocG9pbnQpIHtcclxuICAgICAgICByZXR1cm4ge1xyXG4gICAgICAgICAgICBsbmc6cG9pbnQubG5nLFxyXG4gICAgICAgICAgICBsYXQ6cG9pbnQubGF0XHJcbiAgICAgICAgfTtcclxuICAgIH07XHJcbiAgICBHb29nbGVNYXAucHJvdG90eXBlLm9iamVjdFRvUG9pbnQgPSBmdW5jdGlvbiAob2JqZWN0KSB7XHJcbiAgICAgICAgcmV0dXJuIG5ldyBnb29nbGUubWFwcy5MYXRMbmcocGFyc2VGbG9hdChvYmplY3QubGF0KSxwYXJzZUZsb2F0KG9iamVjdC5sbmcpKTtcclxuICAgIH07XHJcbiAgICBHb29nbGVNYXAucHJvdG90eXBlLmdldENlbnRlciA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICB2YXIgY2VudGVyPXRoaXMubWFwLmdldENlbnRlcigpO1xyXG4gICAgICAgIHJldHVybiB0aGlzLnBvaW50VG9PYmplY3QoY2VudGVyKTtcclxuICAgIH07XHJcblxyXG4gICAgR29vZ2xlTWFwLnByb3RvdHlwZS5zaG93SW5mbyA9IGZ1bmN0aW9uIChwb2ludCkge1xyXG4gICAgICAgIGlmICh0aGlzLmlzaGlkZSkgcmV0dXJuO1xyXG4gICAgICAgIGlmICghdGhpcy5pc3Nob3cpIHtcclxuICAgICAgICAgICAgdGhpcy50b3Nob3cgPSB0cnVlO1xyXG4gICAgICAgICAgICByZXR1cm47XHJcbiAgICAgICAgfVxyXG4gICAgICAgIGlmKHBvaW50KXtcclxuICAgICAgICAgICAgdGhpcy5sb2NhdGU9cG9pbnQ7XHJcbiAgICAgICAgICAgIHRoaXMubWFya2VyLnNldFBvc2l0aW9uKHRoaXMub2JqZWN0VG9Qb2ludChwb2ludCkpO1xyXG4gICAgICAgIH1cclxuICAgICAgICB0aGlzLmluZm9XaW5kb3cuc2V0T3B0aW9ucyh7cG9zaXRpb246IHRoaXMubWFya2VyLmdldFBvc2l0aW9uKCl9KTtcclxuICAgICAgICB0aGlzLnNldEluZm9Db250ZW50KCk7XHJcblxyXG4gICAgfTtcclxuICAgIEdvb2dsZU1hcC5wcm90b3R5cGUuZ2V0QWRkcmVzcyA9IGZ1bmN0aW9uIChycywgc3RhdHVzKSB7XHJcbiAgICAgICAgaWYgKHJzICYmIHJzWzBdKSB7XHJcbiAgICAgICAgICAgIHJldHVybiByc1swXS5mb3JtYXR0ZWRfYWRkcmVzcztcclxuICAgICAgICB9XHJcbiAgICB9O1xyXG4gICAgR29vZ2xlTWFwLnByb3RvdHlwZS5zZXRMb2NhdGUgPSBmdW5jdGlvbiAoYWRkcmVzcykge1xyXG4gICAgICAgIC8vIOWIm+W7uuWcsOWdgOino+aekOWZqOWunuS+i1xyXG4gICAgICAgIHZhciBteUdlbyA9IG5ldyBnb29nbGUubWFwcy5HZW9jb2RlcigpO1xyXG4gICAgICAgIHZhciBzZWxmID0gdGhpcztcclxuICAgICAgICBteUdlby5nZXRQb2ludChhZGRyZXNzLCBmdW5jdGlvbiAocG9pbnQpIHtcclxuICAgICAgICAgICAgaWYgKHBvaW50KSB7XHJcbiAgICAgICAgICAgICAgICBzZWxmLm1hcC5jZW50ZXJBbmRab29tKHBvaW50LCAxMSk7XHJcbiAgICAgICAgICAgICAgICBzZWxmLm1hcmtlci5zZXRQb3NpdGlvbihwb2ludCk7XHJcbiAgICAgICAgICAgICAgICBteUdlby5nZXRMb2NhdGlvbihwb2ludCwgZnVuY3Rpb24gKHJzKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKHBvaW50LCBycyk7XHJcbiAgICAgICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgIHRvYXN0ci53YXJuaW5nKFwi5Zyw5Z2A5L+h5oGv5LiN5q2j56Gu77yM5a6a5L2N5aSx6LSlXCIpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSwgJycpO1xyXG4gICAgfTtcclxuXHJcbiAgICBmdW5jdGlvbiBUZW5jZW50TWFwKCkge1xyXG4gICAgICAgIEJhc2VNYXAuY2FsbCh0aGlzLCBcInRlbmNlbnRcIik7XHJcbiAgICB9XHJcblxyXG4gICAgVGVuY2VudE1hcC5wcm90b3R5cGUgPSBuZXcgQmFzZU1hcCgpO1xyXG4gICAgVGVuY2VudE1hcC5wcm90b3R5cGUuY29uc3RydWN0b3IgPSBUZW5jZW50TWFwO1xyXG4gICAgVGVuY2VudE1hcC5wcm90b3R5cGUuaXNBUElSZWFkeSA9IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICByZXR1cm4gd2luZG93WydxcSddICYmIHdpbmRvd1sncXEnXVsnbWFwcyddO1xyXG4gICAgfTtcclxuICAgIFRlbmNlbnRNYXAucHJvdG90eXBlLnBvaW50VG9PYmplY3QgPSBmdW5jdGlvbiAocG9pbnQpIHtcclxuICAgICAgICByZXR1cm4ge1xyXG4gICAgICAgICAgICBsbmc6cG9pbnQuZ2V0TG5nKCksXHJcbiAgICAgICAgICAgIGxhdDpwb2ludC5nZXRMYXQoKVxyXG4gICAgICAgIH07XHJcbiAgICB9O1xyXG4gICAgVGVuY2VudE1hcC5wcm90b3R5cGUub2JqZWN0VG9Qb2ludCA9IGZ1bmN0aW9uIChvYmplY3QpIHtcclxuICAgICAgICByZXR1cm4gbmV3IHFxLm1hcHMuTGF0TG5nKHBhcnNlRmxvYXQob2JqZWN0LmxhdCkscGFyc2VGbG9hdChvYmplY3QubG5nKSk7XHJcbiAgICB9O1xyXG4gICAgVGVuY2VudE1hcC5wcm90b3R5cGUuc2V0TWFwID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHZhciBzZWxmID0gdGhpcztcclxuICAgICAgICBpZiAodGhpcy5pc3Nob3cgfHwgdGhpcy5pc2hpZGUpIHJldHVybjtcclxuICAgICAgICBpZiAoIXRoaXMubG9hZEFQSSgpKSByZXR1cm47XHJcblxyXG5cclxuICAgICAgICAvL+WIneWni+WMluWcsOWbvlxyXG4gICAgICAgIHZhciBtYXAgPSBzZWxmLm1hcCA9IG5ldyBxcS5tYXBzLk1hcCh0aGlzLm1hcGJveFswXSwge3pvb206IDE1fSk7XHJcbiAgICAgICAgLy/liJ3lp4vljJblnLDlm77mjqfku7ZcclxuICAgICAgICBuZXcgcXEubWFwcy5TY2FsZUNvbnRyb2woe1xyXG4gICAgICAgICAgICBhbGlnbjogcXEubWFwcy5BTElHTi5CT1RUT01fTEVGVCxcclxuICAgICAgICAgICAgbWFyZ2luOiBxcS5tYXBzLlNpemUoODUsIDE1KSxcclxuICAgICAgICAgICAgbWFwOiBtYXBcclxuICAgICAgICB9KTtcclxuICAgICAgICAvL21hcC5hZGRDb250cm9sKG5ldyBCTWFwLk92ZXJ2aWV3TWFwQ29udHJvbCgpKTtcclxuICAgICAgICAvL21hcC5lbmFibGVTY3JvbGxXaGVlbFpvb20oKTtcclxuXHJcbiAgICAgICAgLy/ojrflj5bnu4/nuqzluqblnZDmoIflgLxcclxuICAgICAgICB2YXIgcG9pbnQgPSB0aGlzLm9iamVjdFRvUG9pbnQodGhpcy5sb2NhdGUpO1xyXG4gICAgICAgIG1hcC5wYW5Ubyhwb2ludCk7IC8v5Yid5aeL5YyW5Zyw5Zu+5Lit5b+D54K5XHJcblxyXG4gICAgICAgIC8v5Yid5aeL5YyW5Zyw5Zu+5qCH6K6wXHJcbiAgICAgICAgdGhpcy5tYXJrZXIgPSBuZXcgcXEubWFwcy5NYXJrZXIoe1xyXG4gICAgICAgICAgICBwb3NpdGlvbjogcG9pbnQsXHJcbiAgICAgICAgICAgIGRyYWdnYWJsZTogdHJ1ZSxcclxuICAgICAgICAgICAgbWFwOiBtYXBcclxuICAgICAgICB9KTtcclxuICAgICAgICB0aGlzLm1hcmtlci5zZXRBbmltYXRpb24ocXEubWFwcy5NYXJrZXJBbmltYXRpb24uRE9XTik7XHJcblxyXG4gICAgICAgIC8v5Zyw5Z2A6Kej5p6Q57G7XHJcbiAgICAgICAgdmFyIGdjID0gbmV3IHFxLm1hcHMuR2VvY29kZXIoe1xyXG4gICAgICAgICAgICBjb21wbGV0ZTogZnVuY3Rpb24gKHJzKSB7XHJcbiAgICAgICAgICAgICAgICBzZWxmLnNob3dMb2NhdGlvbkluZm8ocG9pbnQsIHJzKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICBxcS5tYXBzLmV2ZW50LmFkZExpc3RlbmVyKHRoaXMubWFya2VyLCAnY2xpY2snLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIHBvaW50ID0gc2VsZi5tYXJrZXIuZ2V0UG9zaXRpb24oKTtcclxuICAgICAgICAgICAgZ2MuZ2V0QWRkcmVzcyhwb2ludCk7XHJcbiAgICAgICAgfSk7XHJcbiAgICAgICAgLy/orr7nva5NYXJrZXLlgZzmraLmi5bliqjkuovku7ZcclxuICAgICAgICBxcS5tYXBzLmV2ZW50LmFkZExpc3RlbmVyKHRoaXMubWFya2VyLCAnZHJhZ2VuZCcsIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgcG9pbnQgPSBzZWxmLm1hcmtlci5nZXRQb3NpdGlvbigpO1xyXG4gICAgICAgICAgICBnYy5nZXRBZGRyZXNzKHBvaW50KTtcclxuICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgZ2MuZ2V0QWRkcmVzcyhwb2ludCk7XHJcblxyXG4gICAgICAgIHRoaXMuaW5mb1dpbmRvdyA9IG5ldyBxcS5tYXBzLkluZm9XaW5kb3coe21hcDogbWFwfSk7XHJcblxyXG4gICAgICAgIHRoaXMuaXNzaG93ID0gdHJ1ZTtcclxuICAgICAgICBpZiAodGhpcy50b3Nob3cpIHtcclxuICAgICAgICAgICAgdGhpcy5zaG93SW5mbygpO1xyXG4gICAgICAgICAgICB0aGlzLnRvc2hvdyA9IGZhbHNlO1xyXG4gICAgICAgIH1cclxuICAgIH07XHJcblxyXG4gICAgVGVuY2VudE1hcC5wcm90b3R5cGUuZ2V0Q2VudGVyID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHZhciBjZW50ZXI9dGhpcy5tYXAuZ2V0Q2VudGVyKCk7XHJcbiAgICAgICAgcmV0dXJuIHRoaXMucG9pbnRUb09iamVjdChjZW50ZXIpO1xyXG4gICAgfTtcclxuXHJcbiAgICBUZW5jZW50TWFwLnByb3RvdHlwZS5zaG93SW5mbyA9IGZ1bmN0aW9uIChwb2ludCkge1xyXG4gICAgICAgIGlmICh0aGlzLmlzaGlkZSkgcmV0dXJuO1xyXG4gICAgICAgIGlmICghdGhpcy5pc3Nob3cpIHtcclxuICAgICAgICAgICAgdGhpcy50b3Nob3cgPSB0cnVlO1xyXG4gICAgICAgICAgICByZXR1cm47XHJcbiAgICAgICAgfVxyXG4gICAgICAgIGlmKHBvaW50KXtcclxuICAgICAgICAgICAgdGhpcy5sb2NhdGU9cG9pbnQ7XHJcbiAgICAgICAgICAgIHRoaXMubWFya2VyLnNldFBvc2l0aW9uKHRoaXMub2JqZWN0VG9Qb2ludChwb2ludCkpO1xyXG4gICAgICAgIH1cclxuICAgICAgICB0aGlzLmluZm9XaW5kb3cuc2V0UG9zaXRpb24odGhpcy5tYXJrZXIuZ2V0UG9zaXRpb24oKSk7XHJcbiAgICAgICAgdGhpcy5pbmZvV2luZG93Lm9wZW4oKTtcclxuICAgICAgICB0aGlzLnNldEluZm9Db250ZW50KCk7XHJcbiAgICB9O1xyXG5cclxuICAgIFRlbmNlbnRNYXAucHJvdG90eXBlLmdldEFkZHJlc3MgPSBmdW5jdGlvbiAocnMpIHtcclxuICAgICAgICBpZihycyAmJiBycy5kZXRhaWwpIHtcclxuICAgICAgICAgICAgcmV0dXJuIHJzLmRldGFpbC5hZGRyZXNzO1xyXG4gICAgICAgIH1cclxuICAgIH07XHJcblxyXG4gICAgVGVuY2VudE1hcC5wcm90b3R5cGUuc2V0TG9jYXRlID0gZnVuY3Rpb24gKGFkZHJlc3MpIHtcclxuICAgICAgICAvLyDliJvlu7rlnLDlnYDop6PmnpDlmajlrp7kvotcclxuICAgICAgICB2YXIgc2VsZiA9IHRoaXM7XHJcbiAgICAgICAgdmFyIG15R2VvID0gbmV3IHFxLm1hcHMuR2VvY29kZXIoe1xyXG4gICAgICAgICAgICBjb21wbGV0ZTogZnVuY3Rpb24gKHJlc3VsdCkge1xyXG4gICAgICAgICAgICAgICAgaWYocmVzdWx0ICYmIHJlc3VsdC5kZXRhaWwgJiYgcmVzdWx0LmRldGFpbC5sb2NhdGlvbil7XHJcbiAgICAgICAgICAgICAgICAgICAgdmFyIHBvaW50PXJlc3VsdC5kZXRhaWwubG9jYXRpb247XHJcbiAgICAgICAgICAgICAgICAgICAgc2VsZi5tYXAuc2V0Q2VudGVyKHBvaW50KTtcclxuICAgICAgICAgICAgICAgICAgICBzZWxmLm1hcmtlci5zZXRQb3NpdGlvbihwb2ludCk7XHJcbiAgICAgICAgICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKHBvaW50LCByZXN1bHQpO1xyXG4gICAgICAgICAgICAgICAgfWVsc2V7XHJcbiAgICAgICAgICAgICAgICAgICAgdG9hc3RyLndhcm5pbmcoXCLlnLDlnYDkv6Hmga/kuI3mraPnoa7vvIzlrprkvY3lpLHotKVcIik7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgIGVycm9yOmZ1bmN0aW9uKHJlc3VsdCl7XHJcbiAgICAgICAgICAgICAgICB0b2FzdHIud2FybmluZyhcIuWcsOWdgOS/oeaBr+S4jeato+ehru+8jOWumuS9jeWksei0pVwiKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pO1xyXG4gICAgICAgIG15R2VvLmdldExvY2F0aW9uKGFkZHJlc3MpO1xyXG4gICAgfTtcclxuXHJcblxyXG4gICAgZnVuY3Rpb24gR2FvZGVNYXAoKSB7XHJcbiAgICAgICAgQmFzZU1hcC5jYWxsKHRoaXMsIFwiZ2FvZGVcIik7XHJcbiAgICAgICAgdGhpcy5pbmZvT3B0cyA9IHtcclxuICAgICAgICAgICAgd2lkdGg6IDI1MCwgICAgIC8v5L+h5oGv56qX5Y+j5a695bqmXHJcbiAgICAgICAgICAgIC8vICAgaGVpZ2h0OiAxMDAsICAgICAvL+S/oeaBr+eql+WPo+mrmOW6plxyXG4gICAgICAgICAgICB0aXRsZTogXCJcIiAgLy/kv6Hmga/nqpflj6PmoIfpophcclxuICAgICAgICB9O1xyXG4gICAgfVxyXG5cclxuICAgIEdhb2RlTWFwLnByb3RvdHlwZSA9IG5ldyBCYXNlTWFwKCk7XHJcbiAgICBHYW9kZU1hcC5wcm90b3R5cGUuY29uc3RydWN0b3IgPSBHYW9kZU1hcDtcclxuICAgIEdhb2RlTWFwLnByb3RvdHlwZS5pc0FQSVJlYWR5ID0gZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHJldHVybiAhIXdpbmRvd1snQU1hcCddXHJcbiAgICB9O1xyXG5cclxuICAgIEdhb2RlTWFwLnByb3RvdHlwZS5zZXRNYXAgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgdmFyIHNlbGYgPSB0aGlzO1xyXG4gICAgICAgIGlmICh0aGlzLmlzc2hvdyB8fCB0aGlzLmlzaGlkZSkgcmV0dXJuO1xyXG4gICAgICAgIGlmICghdGhpcy5sb2FkQVBJKCkpIHJldHVybjtcclxuXHJcblxyXG4gICAgICAgIHZhciBtYXAgPSBzZWxmLm1hcCA9IG5ldyBBTWFwLk1hcCh0aGlzLm1hcGJveC5hdHRyKCdpZCcpLCB7XHJcbiAgICAgICAgICAgIHJlc2l6ZUVuYWJsZTogdHJ1ZSxcclxuICAgICAgICAgICAgZHJhZ0VuYWJsZTogdHJ1ZSxcclxuICAgICAgICAgICAgem9vbTogMTNcclxuICAgICAgICB9KTtcclxuICAgICAgICBtYXAucGx1Z2luKFtcIkFNYXAuVG9vbEJhclwiLCBcIkFNYXAuU2NhbGVcIiwgXCJBTWFwLk92ZXJWaWV3XCJdLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIG1hcC5hZGRDb250cm9sKG5ldyBBTWFwLlRvb2xCYXIoKSk7XHJcbiAgICAgICAgICAgIG1hcC5hZGRDb250cm9sKG5ldyBBTWFwLlNjYWxlKCkpO1xyXG4gICAgICAgICAgICBtYXAuYWRkQ29udHJvbChuZXcgQU1hcC5PdmVyVmlldygpKTtcclxuICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgJCgnW25hbWU9dHh0TGFuZ10nKS51bmJpbmQoKS5vbignY2hhbmdlJywgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICB2YXIgbGFuZyA9ICQodGhpcykudmFsKCk7XHJcbiAgICAgICAgICAgIGlmIChsYW5nKSBtYXAuc2V0TGFuZyhsYW5nKTtcclxuICAgICAgICB9KS50cmlnZ2VyKCdjaGFuZ2UnKTtcclxuXHJcblxyXG4gICAgICAgIC8v6I635Y+W57uP57qs5bqm5Z2Q5qCH5YC8XHJcbiAgICAgICAgdmFyIHBvaW50ID0gdGhpcy5vYmplY3RUb1BvaW50KHRoaXMubG9jYXRlKTtcclxuICAgICAgICBtYXAuc2V0Q2VudGVyKHBvaW50KTtcclxuXHJcbiAgICAgICAgdGhpcy5tYXJrZXIgPSBuZXcgQU1hcC5NYXJrZXIoe3Bvc2l0aW9uOiBwb2ludCwgbWFwOiBtYXB9KTsgLy/liJ3lp4vljJblnLDlm77moIforrBcclxuICAgICAgICB0aGlzLm1hcmtlci5zZXREcmFnZ2FibGUodHJ1ZSk7IC8v5qCH6K6w5byA5ZCv5ouW5ou9XHJcblxyXG5cclxuICAgICAgICB0aGlzLmluZm9XaW5kb3cgPSBuZXcgQU1hcC5JbmZvV2luZG93KCk7XHJcbiAgICAgICAgdGhpcy5pbmZvV2luZG93Lm9wZW4obWFwLCBwb2ludCk7XHJcblxyXG4gICAgICAgIG1hcC5wbHVnaW4oW1wiQU1hcC5HZW9jb2RlclwiXSwgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICB2YXIgZ2MgPSBuZXcgQU1hcC5HZW9jb2RlcigpOyAvL+WcsOWdgOino+aekOexu1xyXG4gICAgICAgICAgICAvL+a3u+WKoOagh+iusOaLluaLveebkeWQrFxyXG4gICAgICAgICAgICBzZWxmLm1hcmtlci5vbihcImRyYWdlbmRcIiwgZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICAgICAgICAgIC8v6I635Y+W5Zyw5Z2A5L+h5oGvXHJcbiAgICAgICAgICAgICAgICBnYy5nZXRBZGRyZXNzKGUubG5nbGF0LCBmdW5jdGlvbiAoc3QsIHJzKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKGUubG5nbGF0LCBycyk7XHJcbiAgICAgICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgfSk7XHJcblxyXG4gICAgICAgICAgICAvL+a3u+WKoOagh+iusOeCueWHu+ebkeWQrFxyXG4gICAgICAgICAgICBzZWxmLm1hcmtlci5vbihcImNsaWNrXCIsIGZ1bmN0aW9uIChlKSB7XHJcbiAgICAgICAgICAgICAgICBnYy5nZXRBZGRyZXNzKGUubG5nbGF0LCBmdW5jdGlvbiAoc3QsIHJzKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKGUubG5nbGF0LCBycyk7XHJcbiAgICAgICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgfSk7XHJcblxyXG4gICAgICAgICAgICBnYy5nZXRBZGRyZXNzKHBvaW50LCBmdW5jdGlvbiAoc3QsIHJzKSB7XHJcbiAgICAgICAgICAgICAgICBzZWxmLnNob3dMb2NhdGlvbkluZm8ocG9pbnQsIHJzKTtcclxuICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIHRoaXMuaXNzaG93ID0gdHJ1ZTtcclxuICAgICAgICBpZiAodGhpcy50b3Nob3cpIHtcclxuICAgICAgICAgICAgdGhpcy5zaG93SW5mbygpO1xyXG4gICAgICAgICAgICB0aGlzLnRvc2hvdyA9IGZhbHNlO1xyXG4gICAgICAgIH1cclxuICAgIH07XHJcbiAgICBHYW9kZU1hcC5wcm90b3R5cGUucG9pbnRUb09iamVjdCA9IGZ1bmN0aW9uIChwb2ludCkge1xyXG4gICAgICAgIHJldHVybiB7XHJcbiAgICAgICAgICAgIGxuZzpwb2ludC5nZXRMbmcoKSxcclxuICAgICAgICAgICAgbGF0OnBvaW50LmdldExhdCgpXHJcbiAgICAgICAgfTtcclxuICAgIH07XHJcbiAgICBHYW9kZU1hcC5wcm90b3R5cGUub2JqZWN0VG9Qb2ludCA9IGZ1bmN0aW9uIChvYmplY3QpIHtcclxuICAgICAgICByZXR1cm4gbmV3IEFNYXAuTG5nTGF0KG9iamVjdC5sbmcsIG9iamVjdC5sYXQpO1xyXG4gICAgfTtcclxuICAgIEdhb2RlTWFwLnByb3RvdHlwZS5nZXRDZW50ZXIgPSBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgdmFyIGNlbnRlcj10aGlzLm1hcC5jZW50ZXIoKTtcclxuICAgICAgICByZXR1cm4gdGhpcy5wb2ludFRvT2JqZWN0KGNlbnRlcik7XHJcbiAgICB9O1xyXG5cclxuICAgIEdhb2RlTWFwLnByb3RvdHlwZS5zaG93SW5mbyA9IGZ1bmN0aW9uIChwb2ludCkge1xyXG4gICAgICAgIGlmICh0aGlzLmlzaGlkZSkgcmV0dXJuO1xyXG4gICAgICAgIGlmICghdGhpcy5pc3Nob3cpIHtcclxuICAgICAgICAgICAgdGhpcy50b3Nob3cgPSB0cnVlO1xyXG4gICAgICAgICAgICByZXR1cm47XHJcbiAgICAgICAgfVxyXG4gICAgICAgIGlmKHBvaW50KXtcclxuICAgICAgICAgICAgdGhpcy5tYXJrZXIuc2V0UG9zaXRpb24odGhpcy5vYmplY3RUb1BvaW50KHBvaW50KSk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHRoaXMuc2V0SW5mb0NvbnRlbnQoKTtcclxuICAgICAgICB0aGlzLmluZm9XaW5kb3cuc2V0UG9zaXRpb24odGhpcy5tYXJrZXIuZ2V0UG9zaXRpb24oKSk7XHJcbiAgICB9O1xyXG5cclxuICAgIEdhb2RlTWFwLnByb3RvdHlwZS5nZXRBZGRyZXNzID0gZnVuY3Rpb24gKHJzKSB7XHJcbiAgICAgICAgcmV0dXJuIHJzLnJlZ2VvY29kZS5mb3JtYXR0ZWRBZGRyZXNzO1xyXG4gICAgfTtcclxuXHJcbiAgICBHYW9kZU1hcC5wcm90b3R5cGUuc2V0TG9jYXRlID0gZnVuY3Rpb24gKGFkZHJlc3MpIHtcclxuICAgICAgICAvLyDliJvlu7rlnLDlnYDop6PmnpDlmajlrp7kvotcclxuICAgICAgICB2YXIgbXlHZW8gPSBuZXcgQU1hcC5HZW9jb2RlcigpO1xyXG4gICAgICAgIHZhciBzZWxmID0gdGhpcztcclxuICAgICAgICBteUdlby5nZXRQb2ludChhZGRyZXNzLCBmdW5jdGlvbiAocG9pbnQpIHtcclxuICAgICAgICAgICAgaWYgKHBvaW50KSB7XHJcbiAgICAgICAgICAgICAgICBzZWxmLm1hcC5jZW50ZXJBbmRab29tKHBvaW50LCAxMSk7XHJcbiAgICAgICAgICAgICAgICBzZWxmLm1hcmtlci5zZXRQb3NpdGlvbihwb2ludCk7XHJcbiAgICAgICAgICAgICAgICBteUdlby5nZXRMb2NhdGlvbihwb2ludCwgZnVuY3Rpb24gKHJzKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgc2VsZi5zaG93TG9jYXRpb25JbmZvKHBvaW50LCBycyk7XHJcbiAgICAgICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgIHRvYXN0ci53YXJuaW5nKFwi5Zyw5Z2A5L+h5oGv5LiN5q2j56Gu77yM5a6a5L2N5aSx6LSlXCIpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSwgJycpO1xyXG4gICAgfTtcclxuXHJcbiAgICB3aW5kb3cuSW5pdE1hcD1Jbml0TWFwO1xyXG59KSh3aW5kb3csalF1ZXJ5KTsiLCJ3aW5kb3cuc3RvcF9hamF4PWZhbHNlO1xyXG5qUXVlcnkoZnVuY3Rpb24gKCQpIHtcclxuICAgIC8v6auY5Lqu5b2T5YmN6YCJ5Lit55qE5a+86IiqXHJcbiAgICB2YXIgYnJlYWQgPSAkKFwiLmJyZWFkY3J1bWJcIik7XHJcbiAgICB2YXIgbWVudSA9IGJyZWFkLmRhdGEoJ21lbnUnKTtcclxuICAgIGlmIChtZW51KSB7XHJcbiAgICAgICAgdmFyIGxpbmsgPSAkKCcuc2lkZS1uYXYgYVtkYXRhLWtleT0nICsgbWVudSArICddJyk7XHJcblxyXG4gICAgICAgIHZhciBodG1sID0gW107XHJcbiAgICAgICAgaWYgKGxpbmsubGVuZ3RoID4gMCkge1xyXG4gICAgICAgICAgICBpZiAobGluay5pcygnLm1lbnVfdG9wJykpIHtcclxuICAgICAgICAgICAgICAgIGh0bWwucHVzaCgnPGxpIGNsYXNzPVwiYnJlYWRjcnVtYi1pdGVtXCI+PGEgaHJlZj1cImphdmFzY3JpcHQ6XCI+PGkgY2xhc3M9XCInICsgbGluay5maW5kKCdpJykuYXR0cignY2xhc3MnKSArICdcIj48L2k+Jm5ic3A7JyArIGxpbmsudGV4dCgpICsgJzwvYT48L2xpPicpO1xyXG4gICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgdmFyIHBhcmVudCA9IGxpbmsucGFyZW50cygnLmNvbGxhcHNlJykuZXEoMCk7XHJcbiAgICAgICAgICAgICAgICBwYXJlbnQuYWRkQ2xhc3MoJ3Nob3cnKTtcclxuICAgICAgICAgICAgICAgIGxpbmsuYWRkQ2xhc3MoXCJhY3RpdmVcIik7XHJcbiAgICAgICAgICAgICAgICB2YXIgdG9wbWVudSA9IHBhcmVudC5zaWJsaW5ncygnLmNhcmQtaGVhZGVyJykuZmluZCgnYS5tZW51X3RvcCcpO1xyXG4gICAgICAgICAgICAgICAgaHRtbC5wdXNoKCc8bGkgY2xhc3M9XCJicmVhZGNydW1iLWl0ZW1cIj48YSBocmVmPVwiamF2YXNjcmlwdDpcIj48aSBjbGFzcz1cIicgKyB0b3BtZW51LmZpbmQoJ2knKS5hdHRyKCdjbGFzcycpICsgJ1wiPjwvaT4mbmJzcDsnICsgdG9wbWVudS50ZXh0KCkgKyAnPC9hPjwvbGk+Jyk7XHJcbiAgICAgICAgICAgICAgICBodG1sLnB1c2goJzxsaSBjbGFzcz1cImJyZWFkY3J1bWItaXRlbVwiPjxhIGhyZWY9XCJqYXZhc2NyaXB0OlwiPicgKyBsaW5rLnRleHQoKSArICc8L2E+PC9saT4nKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH1cclxuICAgICAgICB2YXIgdGl0bGUgPSBicmVhZC5kYXRhKCd0aXRsZScpO1xyXG4gICAgICAgIGlmICh0aXRsZSkge1xyXG4gICAgICAgICAgICBodG1sLnB1c2goJzxsaSBjbGFzcz1cImJyZWFkY3J1bWItaXRlbSBhY3RpdmVcIiBhcmlhLWN1cnJlbnQ9XCJwYWdlXCI+JyArIHRpdGxlICsgJzwvbGk+Jyk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIGJyZWFkLmh0bWwoaHRtbC5qb2luKFwiXFxuXCIpKTtcclxuICAgIH1cclxuXHJcbiAgICAvL+WFqOmAieOAgeWPjemAieaMiemSrlxyXG4gICAgJCgnLmNoZWNrYWxsLWJ0bicpLmNsaWNrKGZ1bmN0aW9uIChlKSB7XHJcbiAgICAgICAgdmFyIHRhcmdldCA9ICQodGhpcykuZGF0YSgndGFyZ2V0Jyk7XHJcbiAgICAgICAgaWYgKCF0YXJnZXQpIHRhcmdldCA9ICdpZCc7XHJcbiAgICAgICAgdmFyIGlkcyA9ICQoJ1tuYW1lPScgKyB0YXJnZXQgKyAnXScpO1xyXG4gICAgICAgIGlmICgkKHRoaXMpLmlzKCcuYWN0aXZlJykpIHtcclxuICAgICAgICAgICAgaWRzLnByb3AoJ2NoZWNrZWQnLCBmYWxzZSk7XHJcbiAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgaWRzLnByb3AoJ2NoZWNrZWQnLCB0cnVlKTtcclxuICAgICAgICB9XHJcbiAgICB9KTtcclxuICAgICQoJy5jaGVja3JldmVyc2UtYnRuJykuY2xpY2soZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICB2YXIgdGFyZ2V0ID0gJCh0aGlzKS5kYXRhKCd0YXJnZXQnKTtcclxuICAgICAgICBpZiAoIXRhcmdldCkgdGFyZ2V0ID0gJ2lkJztcclxuICAgICAgICB2YXIgaWRzID0gJCgnW25hbWU9JyArIHRhcmdldCArICddJyk7XHJcbiAgICAgICAgZm9yICh2YXIgaSA9IDA7IGkgPCBpZHMubGVuZ3RoOyBpKyspIHtcclxuICAgICAgICAgICAgaWYgKGlkc1tpXS5jaGVja2VkKSB7XHJcbiAgICAgICAgICAgICAgICBpZHMuZXEoaSkucHJvcCgnY2hlY2tlZCcsIGZhbHNlKTtcclxuICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgIGlkcy5lcShpKS5wcm9wKCdjaGVja2VkJywgdHJ1ZSk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9XHJcbiAgICB9KTtcclxuXHJcbiAgICAvL+WFqOWxgOaTjeS9nOaMiemSrlxyXG4gICAgJCgnLmFjdGlvbi1idG4nKS5jbGljayhmdW5jdGlvbiAoZSkge1xyXG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcclxuICAgICAgICB2YXIgYWN0aW9uID0gJCh0aGlzKS5kYXRhKCdhY3Rpb24nKTtcclxuICAgICAgICBpZiAoIWFjdGlvbikge1xyXG4gICAgICAgICAgICByZXR1cm4gdG9hc3RyLmVycm9yKCfmnKrnn6Xmk43kvZwnKTtcclxuICAgICAgICB9XHJcbiAgICAgICAgYWN0aW9uID0gJ2FjdGlvbicgKyBhY3Rpb24ucmVwbGFjZSgvXlthLXpdLywgZnVuY3Rpb24gKGxldHRlcikge1xyXG4gICAgICAgICAgICByZXR1cm4gbGV0dGVyLnRvVXBwZXJDYXNlKCk7XHJcbiAgICAgICAgfSk7XHJcbiAgICAgICAgaWYgKCF3aW5kb3dbYWN0aW9uXSB8fCB0eXBlb2Ygd2luZG93W2FjdGlvbl0gIT09ICdmdW5jdGlvbicpIHtcclxuICAgICAgICAgICAgcmV0dXJuIHRvYXN0ci5lcnJvcign5pyq55+l5pON5L2cJyk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHZhciBuZWVkQ2hlY2tzID0gJCh0aGlzKS5kYXRhKCduZWVkQ2hlY2tzJyk7XHJcbiAgICAgICAgaWYgKG5lZWRDaGVja3MgPT09IHVuZGVmaW5lZCkgbmVlZENoZWNrcyA9IHRydWU7XHJcbiAgICAgICAgaWYgKG5lZWRDaGVja3MpIHtcclxuICAgICAgICAgICAgdmFyIHRhcmdldCA9ICQodGhpcykuZGF0YSgndGFyZ2V0Jyk7XHJcbiAgICAgICAgICAgIGlmICghdGFyZ2V0KSB0YXJnZXQgPSAnaWQnO1xyXG4gICAgICAgICAgICB2YXIgaWRzID0gJCgnW25hbWU9JyArIHRhcmdldCArICddOmNoZWNrZWQnKTtcclxuICAgICAgICAgICAgaWYgKGlkcy5sZW5ndGggPCAxKSB7XHJcbiAgICAgICAgICAgICAgICByZXR1cm4gdG9hc3RyLndhcm5pbmcoJ+ivt+mAieaLqemcgOimgeaTjeS9nOeahOmhueebricpO1xyXG4gICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgdmFyIGlkY2hlY2tzID0gW107XHJcbiAgICAgICAgICAgICAgICBmb3IgKHZhciBpID0gMDsgaSA8IGlkcy5sZW5ndGg7IGkrKykge1xyXG4gICAgICAgICAgICAgICAgICAgIGlkY2hlY2tzLnB1c2goaWRzLmVxKGkpLnZhbCgpKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIHdpbmRvd1thY3Rpb25dKGlkY2hlY2tzKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgIHdpbmRvd1thY3Rpb25dKCk7XHJcbiAgICAgICAgfVxyXG4gICAgfSk7XHJcblxyXG4gICAgLy/ooajmoLzooYzmk43kvZzmj5DnpLpcclxuICAgICQoJy5vcGVyYXRpb25zIC5idG4nKS50b29sdGlwKCk7XHJcblxyXG4gICAgLy/lvILmraXmmL7npLrotYTmlpnpk77mjqVcclxuICAgICQoJ2FbcmVsPWFqYXhdJykuY2xpY2soZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XHJcbiAgICAgICAgdmFyIHNlbGYgPSAkKHRoaXMpO1xyXG4gICAgICAgIHZhciB0aXRsZSA9ICQodGhpcykuZGF0YSgndGl0bGUnKTtcclxuICAgICAgICBpZiAoIXRpdGxlKSB0aXRsZSA9ICQodGhpcykudGV4dCgpO1xyXG4gICAgICAgIGlmICghdGl0bGUpIHRpdGxlID0gJCh0aGlzKS5hdHRyKCd0aXRsZScpO1xyXG4gICAgICAgIHZhciBkbGcgPSBuZXcgRGlhbG9nKHtcclxuICAgICAgICAgICAgYnRuczogWyfnoa7lrponXSxcclxuICAgICAgICAgICAgb25zaG93OiBmdW5jdGlvbiAoYm9keSkge1xyXG4gICAgICAgICAgICAgICAgJC5hamF4KHtcclxuICAgICAgICAgICAgICAgICAgICB1cmw6IHNlbGYuYXR0cignaHJlZicpLFxyXG4gICAgICAgICAgICAgICAgICAgIHN1Y2Nlc3M6IGZ1bmN0aW9uICh0ZXh0KSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGJvZHkuaHRtbCh0ZXh0KTtcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLnNob3coJzxwIGNsYXNzPVwibG9hZGluZ1wiPicrbGFuZygnbG9hZGluZy4uLicpKyc8L3A+JywgdGl0bGUpO1xyXG5cclxuICAgIH0pO1xyXG5cclxuICAgIC8v56Gu6K6k5pON5L2cXHJcbiAgICAkKCcubGluay1jb25maXJtJykuY2xpY2soZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XHJcbiAgICAgICAgZS5zdG9wUHJvcGFnYXRpb24oKTtcclxuICAgICAgICB2YXIgdGV4dD0kKHRoaXMpLmRhdGEoJ2NvbmZpcm0nKTtcclxuICAgICAgICB2YXIgdXJsPSQodGhpcykuYXR0cignaHJlZicpO1xyXG4gICAgICAgIHRleHQ9dGV4dC5yZXBsYWNlKC8oXFxcXG58XFxuKSsvZyxcIjxiciAvPlwiKTtcclxuICAgICAgICBpZighdGV4dCl0ZXh0PWxhbmcoJ0NvbmZpcm0gb3BlcmF0aW9uPycpO1xyXG5cclxuICAgICAgICBkaWFsb2cuY29uZmlybSh0ZXh0LGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgJC5hamF4KHtcclxuICAgICAgICAgICAgICAgIHVybDp1cmwsXHJcbiAgICAgICAgICAgICAgICBkYXRhVHlwZTonSlNPTicsXHJcbiAgICAgICAgICAgICAgICBzdWNjZXNzOmZ1bmN0aW9uIChqc29uKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgZGlhbG9nLmFsZXJ0KGpzb24ubXNnKTtcclxuICAgICAgICAgICAgICAgICAgICBpZihqc29uLmNvZGU9PTEpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBpZihqc29uLnVybCl7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBsb2NhdGlvbi5ocmVmPWpzb24udXJsO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB9ZWxzZXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxvY2F0aW9uLnJlbG9hZCgpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgICAgIGVycm9yOmZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgICAgICBkaWFsb2cuYWxlcnQobGFuZygnU2VydmVyIGVycm9yLicpKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSlcclxuICAgICAgICB9KTtcclxuICAgIH0pO1xyXG5cclxuICAgICQoJy5pbWctdmlldycpLmNsaWNrKGZ1bmN0aW9uIChlKSB7XHJcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xyXG4gICAgICAgIGUuc3RvcFByb3BhZ2F0aW9uKCk7XHJcbiAgICAgICAgdmFyIHVybD0kKHRoaXMpLmF0dHIoJ2hyZWYnKTtcclxuICAgICAgICBpZighdXJsKXVybD0kKHRoaXMpLmRhdGEoJ2ltZycpO1xyXG4gICAgICAgIGRpYWxvZy5hbGVydCgnPGEgaHJlZj1cIicrdXJsKydcIiBjbGFzcz1cImQtYmxvY2sgdGV4dC1jZW50ZXJcIiB0YXJnZXQ9XCJfYmxhbmtcIj48aW1nIGNsYXNzPVwiaW1nLWZsdWlkXCIgc3JjPVwiJyt1cmwrJ1wiIC8+PC9hPjxkaXYgY2xhc3M9XCJ0ZXh0LW11dGVkIHRleHQtY2VudGVyXCI+54K55Ye75Zu+54mH5Zyo5paw6aG16Z2i5pS+5aSn5p+l55yLPC9kaXY+JyxudWxsLCfmn6XnnIvlm77niYcnKTtcclxuICAgIH0pO1xyXG5cclxuICAgICQoJy5uYXYtdGFicyBhJykuY2xpY2soZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XHJcbiAgICAgICAgJCh0aGlzKS50YWIoJ3Nob3cnKTtcclxuICAgIH0pO1xyXG5cclxuICAgIC8v5LiK5Lyg5qGGXHJcbiAgICAkKCcuY3VzdG9tLWZpbGUgLmN1c3RvbS1maWxlLWlucHV0Jykub24oJ2NoYW5nZScsIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICB2YXIgbGFiZWwgPSAkKHRoaXMpLnBhcmVudHMoJy5jdXN0b20tZmlsZScpLmZpbmQoJy5jdXN0b20tZmlsZS1sYWJlbCcpO1xyXG4gICAgICAgIGxhYmVsLnRleHQoJCh0aGlzKS52YWwoKSk7XHJcbiAgICB9KTtcclxuXHJcbiAgICAvL+ihqOWNlUFqYXjmj5DkuqRcclxuICAgICQoJy5idG4tcHJpbWFyeVt0eXBlPXN1Ym1pdF0nKS5jbGljayhmdW5jdGlvbiAoZSkge1xyXG4gICAgICAgIHZhciBmb3JtID0gJCh0aGlzKS5wYXJlbnRzKCdmb3JtJyk7XHJcbiAgICAgICAgaWYoZm9ybS5pcygnLm5vYWpheCcpKXJldHVybiB0cnVlO1xyXG4gICAgICAgIHZhciBidG4gPSB0aGlzO1xyXG5cclxuICAgICAgICB2YXIgaXNidG49JChidG4pLnByb3AoJ3RhZ05hbWUnKS50b1VwcGVyQ2FzZSgpPT0nQlVUVE9OJztcclxuICAgICAgICB2YXIgb3JpZ1RleHQ9aXNidG4/JChidG4pLnRleHQoKTokKGJ0bikudmFsKCk7XHJcbiAgICAgICAgdmFyIG9wdGlvbnMgPSB7XHJcbiAgICAgICAgICAgIHVybDogJChmb3JtKS5hdHRyKCdhY3Rpb24nKSxcclxuICAgICAgICAgICAgdHlwZTogJ1BPU1QnLFxyXG4gICAgICAgICAgICBkYXRhVHlwZTogJ0pTT04nLFxyXG4gICAgICAgICAgICBzdWNjZXNzOiBmdW5jdGlvbiAoanNvbikge1xyXG4gICAgICAgICAgICAgICAgd2luZG93LnN0b3BfYWpheD1mYWxzZTtcclxuICAgICAgICAgICAgICAgIGlzYnRuPyQoYnRuKS50ZXh0KG9yaWdUZXh0KTokKGJ0bikudmFsKG9yaWdUZXh0KTtcclxuICAgICAgICAgICAgICAgIGlmIChqc29uLmNvZGUgPT0gMSkge1xyXG4gICAgICAgICAgICAgICAgICAgIGRpYWxvZy5hbGVydChqc29uLm1zZyxmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoanNvbi51cmwpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxvY2F0aW9uLmhyZWYgPSBqc29uLnVybDtcclxuICAgICAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxvY2F0aW9uLnJlbG9hZCgpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgICAgIHRvYXN0ci53YXJuaW5nKGpzb24ubXNnKTtcclxuICAgICAgICAgICAgICAgICAgICAkKGJ0bikucmVtb3ZlQXR0cignZGlzYWJsZWQnKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgZXJyb3I6IGZ1bmN0aW9uICh4aHIpIHtcclxuICAgICAgICAgICAgICAgIHdpbmRvdy5zdG9wX2FqYXg9ZmFsc2U7XHJcbiAgICAgICAgICAgICAgICBpc2J0bj8kKGJ0bikudGV4dChvcmlnVGV4dCk6JChidG4pLnZhbChvcmlnVGV4dCk7XHJcbiAgICAgICAgICAgICAgICAkKGJ0bikucmVtb3ZlQXR0cignZGlzYWJsZWQnKTtcclxuICAgICAgICAgICAgICAgIHRvYXN0ci5lcnJvcign5pyN5Yqh5Zmo5aSE55CG6ZSZ6K+vJyk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9O1xyXG4gICAgICAgIGlmIChmb3JtLmF0dHIoJ2VuY3R5cGUnKSA9PT0gJ211bHRpcGFydC9mb3JtLWRhdGEnKSB7XHJcbiAgICAgICAgICAgIGlmICghRm9ybURhdGEpIHtcclxuICAgICAgICAgICAgICAgIHdpbmRvdy5zdG9wX2FqYXg9dHJ1ZTtcclxuICAgICAgICAgICAgICAgIHJldHVybiB0cnVlO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIG9wdGlvbnMuZGF0YSA9IG5ldyBGb3JtRGF0YShmb3JtWzBdKTtcclxuICAgICAgICAgICAgb3B0aW9ucy5jYWNoZSA9IGZhbHNlO1xyXG4gICAgICAgICAgICBvcHRpb25zLnByb2Nlc3NEYXRhID0gZmFsc2U7XHJcbiAgICAgICAgICAgIG9wdGlvbnMuY29udGVudFR5cGUgPSBmYWxzZTtcclxuICAgICAgICAgICAgb3B0aW9ucy54aHI9IGZ1bmN0aW9uKCkgeyAvL+eUqOS7peaYvuekuuS4iuS8oOi/m+W6plxyXG4gICAgICAgICAgICAgICAgdmFyIHhociA9ICQuYWpheFNldHRpbmdzLnhocigpO1xyXG4gICAgICAgICAgICAgICAgaWYgKHhoci51cGxvYWQpIHtcclxuICAgICAgICAgICAgICAgICAgICB4aHIudXBsb2FkLmFkZEV2ZW50TGlzdGVuZXIoJ3Byb2dyZXNzJywgZnVuY3Rpb24oZXZlbnQpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgdmFyIHBlcmNlbnQgPSBNYXRoLmZsb29yKGV2ZW50LmxvYWRlZCAvIGV2ZW50LnRvdGFsICogMTAwKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgJChidG4pLnRleHQob3JpZ1RleHQrJyAgKCcrcGVyY2VudCsnJSknKTtcclxuICAgICAgICAgICAgICAgICAgICB9LCBmYWxzZSk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICByZXR1cm4geGhyO1xyXG4gICAgICAgICAgICB9O1xyXG4gICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgIG9wdGlvbnMuZGF0YSA9ICQoZm9ybSkuc2VyaWFsaXplKCk7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XHJcbiAgICAgICAgJCh0aGlzKS5hdHRyKCdkaXNhYmxlZCcsIHRydWUpO1xyXG4gICAgICAgIHdpbmRvdy5zdG9wX2FqYXg9dHJ1ZTtcclxuICAgICAgICAkLmFqYXgob3B0aW9ucyk7XHJcbiAgICB9KTtcclxuXHJcbiAgICAkKCcucGlja3VzZXInKS5jbGljayhmdW5jdGlvbiAoZSkge1xyXG4gICAgICAgIHZhciBncm91cCA9ICQodGhpcykucGFyZW50cygnLmlucHV0LWdyb3VwJyk7XHJcbiAgICAgICAgdmFyIGlkZWxlID0gZ3JvdXAuZmluZCgnW25hbWU9bWVtYmVyX2lkXScpO1xyXG4gICAgICAgIHZhciBpbmZvZWxlID0gZ3JvdXAuZmluZCgnW25hbWU9bWVtYmVyX2luZm9dJyk7XHJcbiAgICAgICAgZGlhbG9nLnBpY2tVc2VyKCBmdW5jdGlvbiAodXNlcikge1xyXG4gICAgICAgICAgICBpZGVsZS52YWwodXNlci5pZCk7XHJcbiAgICAgICAgICAgIGluZm9lbGUudmFsKCdbJyArIHVzZXIuaWQgKyAnXSAnICsgdXNlci51c2VybmFtZSArICh1c2VyLm1vYmlsZSA/ICgnIC8gJyArIHVzZXIubW9iaWxlKSA6ICcnKSk7XHJcbiAgICAgICAgfSwgJCh0aGlzKS5kYXRhKCdmaWx0ZXInKSk7XHJcbiAgICB9KTtcclxuICAgICQoJy5waWNrLWxvY2F0ZScpLmNsaWNrKGZ1bmN0aW9uKGUpe1xyXG4gICAgICAgIHZhciBncm91cD0kKHRoaXMpLnBhcmVudHMoJy5pbnB1dC1ncm91cCcpO1xyXG4gICAgICAgIHZhciBpZGVsZT1ncm91cC5maW5kKCdpbnB1dFt0eXBlPXRleHRdJyk7XHJcbiAgICAgICAgZGlhbG9nLnBpY2tMb2NhdGUoJ3FxJyxmdW5jdGlvbihsb2NhdGUpe1xyXG4gICAgICAgICAgICBpZGVsZS52YWwobG9jYXRlLmxuZysnLCcrbG9jYXRlLmxhdCk7XHJcbiAgICAgICAgfSxpZGVsZS52YWwoKSk7XHJcbiAgICB9KTtcclxufSk7Il19
