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
function setNav(nav) {
    var items=$('.main-nav .nav-item');
    var finded=false;
    for(var i=0;i<items.length;i++){
        if(items.eq(i).data('model')===nav){
            items.eq(i).addClass('active');
            finded=true;
            break;
        }
    }
    if(!finded && nav.indexOf('-')>0){
        nav=nav.substr(0,nav.lastIndexOf('-'));
        setNav(nav);
    }
}

jQuery(function($){
    if($(window).width()>=991){
        $('.main-nav>.dropdown').hover(
            function () {
                $(this).find('.dropdown-menu').stop(true,false).slideDown();
            },
            function () {
                $(this).find('.dropdown-menu').stop(true,false).slideUp();
            }
        );
    }else{
        $('.main-nav>.dropdown>.dropdown-toggle').click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            var opened=$(this).data('opened');
            var p = $(this).parents('.dropdown');
            if(opened){
                p.find('.dropdown-menu').stop(true, false).slideUp();
            }else {
                p.siblings().children('.dropdown-menu').stop(true, false).slideUp();
                p.siblings().children('.dropdown-toggle').data('opened',false);
                p.find('.dropdown-menu').stop(true, false).slideDown();
            }
            $(this).data('opened',!opened);
        })
    }
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNvbW1vbi5qcyIsInRlbXBsYXRlLmpzIiwiZGlhbG9nLmpzIiwianF1ZXJ5LnRhZy5qcyIsImRhdGV0aW1lLmluaXQuanMiLCJmcm9udC5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUNqRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUM1R0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUMvZUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUMxQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ3pFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSIsImZpbGUiOiJmcm9udC5qcyIsInNvdXJjZXNDb250ZW50IjpbImZ1bmN0aW9uIGRlbChvYmosbXNnKSB7XHJcbiAgICBkaWFsb2cuY29uZmlybShtc2csZnVuY3Rpb24oKXtcclxuICAgICAgICBsb2NhdGlvbi5ocmVmPSQob2JqKS5hdHRyKCdocmVmJyk7XHJcbiAgICB9KTtcclxuICAgIHJldHVybiBmYWxzZTtcclxufVxyXG5cclxuZnVuY3Rpb24gbGFuZyhrZXkpIHtcclxuICAgIGlmKHdpbmRvdy5sYW5ndWFnZSAmJiB3aW5kb3cubGFuZ3VhZ2Vba2V5XSl7XHJcbiAgICAgICAgcmV0dXJuIHdpbmRvdy5sYW5ndWFnZVtrZXldO1xyXG4gICAgfVxyXG4gICAgcmV0dXJuIGtleTtcclxufVxyXG5cclxuZnVuY3Rpb24gcmFuZG9tU3RyaW5nKGxlbiwgY2hhclNldCkge1xyXG4gICAgY2hhclNldCA9IGNoYXJTZXQgfHwgJ0FCQ0RFRkdISUpLTE1OT1BRUlNUVVZXWFlaYWJjZGVmZ2hpamtsbW5vcHFyc3R1dnd4eXowMTIzNDU2Nzg5JztcclxuICAgIHZhciBzdHIgPSAnJyxhbGxMZW49Y2hhclNldC5sZW5ndGg7XHJcbiAgICBmb3IgKHZhciBpID0gMDsgaSA8IGxlbjsgaSsrKSB7XHJcbiAgICAgICAgdmFyIHJhbmRvbVBveiA9IE1hdGguZmxvb3IoTWF0aC5yYW5kb20oKSAqIGFsbExlbik7XHJcbiAgICAgICAgc3RyICs9IGNoYXJTZXQuc3Vic3RyaW5nKHJhbmRvbVBveixyYW5kb21Qb3orMSk7XHJcbiAgICB9XHJcbiAgICByZXR1cm4gc3RyO1xyXG59XHJcblxyXG5mdW5jdGlvbiBjb3B5X29iaihhcnIpe1xyXG4gICAgcmV0dXJuIEpTT04ucGFyc2UoSlNPTi5zdHJpbmdpZnkoYXJyKSk7XHJcbn1cclxuXHJcbmZ1bmN0aW9uIGlzT2JqZWN0VmFsdWVFcXVhbChhLCBiKSB7XHJcbiAgICBpZighYSAmJiAhYilyZXR1cm4gdHJ1ZTtcclxuICAgIGlmKCFhIHx8ICFiKXJldHVybiBmYWxzZTtcclxuXHJcbiAgICAvLyBPZiBjb3Vyc2UsIHdlIGNhbiBkbyBpdCB1c2UgZm9yIGluXHJcbiAgICAvLyBDcmVhdGUgYXJyYXlzIG9mIHByb3BlcnR5IG5hbWVzXHJcbiAgICB2YXIgYVByb3BzID0gT2JqZWN0LmdldE93blByb3BlcnR5TmFtZXMoYSk7XHJcbiAgICB2YXIgYlByb3BzID0gT2JqZWN0LmdldE93blByb3BlcnR5TmFtZXMoYik7XHJcblxyXG4gICAgLy8gSWYgbnVtYmVyIG9mIHByb3BlcnRpZXMgaXMgZGlmZmVyZW50LFxyXG4gICAgLy8gb2JqZWN0cyBhcmUgbm90IGVxdWl2YWxlbnRcclxuICAgIGlmIChhUHJvcHMubGVuZ3RoICE9IGJQcm9wcy5sZW5ndGgpIHtcclxuICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICB9XHJcblxyXG4gICAgZm9yICh2YXIgaSA9IDA7IGkgPCBhUHJvcHMubGVuZ3RoOyBpKyspIHtcclxuICAgICAgICB2YXIgcHJvcE5hbWUgPSBhUHJvcHNbaV07XHJcblxyXG4gICAgICAgIC8vIElmIHZhbHVlcyBvZiBzYW1lIHByb3BlcnR5IGFyZSBub3QgZXF1YWwsXHJcbiAgICAgICAgLy8gb2JqZWN0cyBhcmUgbm90IGVxdWl2YWxlbnRcclxuICAgICAgICBpZiAoYVtwcm9wTmFtZV0gIT09IGJbcHJvcE5hbWVdKSB7XHJcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICB9XHJcbiAgICB9XHJcblxyXG4gICAgLy8gSWYgd2UgbWFkZSBpdCB0aGlzIGZhciwgb2JqZWN0c1xyXG4gICAgLy8gYXJlIGNvbnNpZGVyZWQgZXF1aXZhbGVudFxyXG4gICAgcmV0dXJuIHRydWU7XHJcbn1cclxuXHJcbmZ1bmN0aW9uIGFycmF5X2NvbWJpbmUoYSxiKSB7XHJcbiAgICB2YXIgb2JqPXt9O1xyXG4gICAgZm9yKHZhciBpPTA7aTxhLmxlbmd0aDtpKyspe1xyXG4gICAgICAgIGlmKGIubGVuZ3RoPGkrMSlicmVhaztcclxuICAgICAgICBvYmpbYVtpXV09YltpXTtcclxuICAgIH1cclxuICAgIHJldHVybiBvYmo7XHJcbn0iLCJcclxuTnVtYmVyLnByb3RvdHlwZS5mb3JtYXQ9ZnVuY3Rpb24oZml4KXtcclxuICAgIGlmKGZpeD09PXVuZGVmaW5lZClmaXg9MjtcclxuICAgIHZhciBudW09dGhpcy50b0ZpeGVkKGZpeCk7XHJcbiAgICB2YXIgej1udW0uc3BsaXQoJy4nKTtcclxuICAgIHZhciBmb3JtYXQ9W10sZj16WzBdLnNwbGl0KCcnKSxsPWYubGVuZ3RoO1xyXG4gICAgZm9yKHZhciBpPTA7aTxsO2krKyl7XHJcbiAgICAgICAgaWYoaT4wICYmIGkgJSAzPT0wKXtcclxuICAgICAgICAgICAgZm9ybWF0LnVuc2hpZnQoJywnKTtcclxuICAgICAgICB9XHJcbiAgICAgICAgZm9ybWF0LnVuc2hpZnQoZltsLWktMV0pO1xyXG4gICAgfVxyXG4gICAgcmV0dXJuIGZvcm1hdC5qb2luKCcnKSsoei5sZW5ndGg9PTI/Jy4nK3pbMV06JycpO1xyXG59O1xyXG5pZighU3RyaW5nLnByb3RvdHlwZS50cmltKXtcclxuICAgIFN0cmluZy5wcm90b3R5cGUudHJpbT1mdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgcmV0dXJuIHRoaXMucmVwbGFjZSgvKF5cXHMrfFxccyskKS9nLCcnKTtcclxuICAgIH1cclxufVxyXG5TdHJpbmcucHJvdG90eXBlLmNvbXBpbGU9ZnVuY3Rpb24oZGF0YSxsaXN0KXtcclxuXHJcbiAgICBpZihsaXN0KXtcclxuICAgICAgICB2YXIgdGVtcHM9W107XHJcbiAgICAgICAgZm9yKHZhciBpIGluIGRhdGEpe1xyXG4gICAgICAgICAgICB0ZW1wcy5wdXNoKHRoaXMuY29tcGlsZShkYXRhW2ldKSk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHJldHVybiB0ZW1wcy5qb2luKFwiXFxuXCIpO1xyXG4gICAgfWVsc2V7XHJcbiAgICAgICAgcmV0dXJuIHRoaXMucmVwbGFjZSgvXFx7aWZcXHMrKFteXFx9XSspXFx9KFtcXFdcXHddKil7XFwvaWZ9L2csZnVuY3Rpb24oYWxsLCBjb25kaXRpb24sIGNvbnQpe1xyXG4gICAgICAgICAgICB2YXIgb3BlcmF0aW9uO1xyXG4gICAgICAgICAgICBpZihvcGVyYXRpb249Y29uZGl0aW9uLm1hdGNoKC9cXHMrKD0rfDx8PilcXHMrLykpe1xyXG4gICAgICAgICAgICAgICAgb3BlcmF0aW9uPW9wZXJhdGlvblswXTtcclxuICAgICAgICAgICAgICAgIHZhciBwYXJ0PWNvbmRpdGlvbi5zcGxpdChvcGVyYXRpb24pO1xyXG4gICAgICAgICAgICAgICAgaWYocGFydFswXS5pbmRleE9mKCdAJyk9PT0wKXtcclxuICAgICAgICAgICAgICAgICAgICBwYXJ0WzBdPWRhdGFbcGFydFswXS5yZXBsYWNlKCdAJywnJyldO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgaWYocGFydFsxXS5pbmRleE9mKCdAJyk9PT0wKXtcclxuICAgICAgICAgICAgICAgICAgICBwYXJ0WzFdPWRhdGFbcGFydFsxXS5yZXBsYWNlKCdAJywnJyldO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgb3BlcmF0aW9uPW9wZXJhdGlvbi50cmltKCk7XHJcbiAgICAgICAgICAgICAgICB2YXIgcmVzdWx0PWZhbHNlO1xyXG4gICAgICAgICAgICAgICAgc3dpdGNoIChvcGVyYXRpb24pe1xyXG4gICAgICAgICAgICAgICAgICAgIGNhc2UgJz09JzpcclxuICAgICAgICAgICAgICAgICAgICAgICAgcmVzdWx0ID0gcGFydFswXSA9PSBwYXJ0WzFdO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgICAgICAgICBjYXNlICc9PT0nOlxyXG4gICAgICAgICAgICAgICAgICAgICAgICByZXN1bHQgPSBwYXJ0WzBdID09PSBwYXJ0WzFdO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgICAgICAgICBjYXNlICc+JzpcclxuICAgICAgICAgICAgICAgICAgICAgICAgcmVzdWx0ID0gcGFydFswXSA+IHBhcnRbMV07XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgICAgICAgICAgICAgIGNhc2UgJzwnOlxyXG4gICAgICAgICAgICAgICAgICAgICAgICByZXN1bHQgPSBwYXJ0WzBdIDwgcGFydFsxXTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICBpZihyZXN1bHQpe1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBjb250O1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9ZWxzZSB7XHJcbiAgICAgICAgICAgICAgICBpZiAoZGF0YVtjb25kaXRpb24ucmVwbGFjZSgnQCcsJycpXSkgcmV0dXJuIGNvbnQ7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgcmV0dXJuICcnO1xyXG4gICAgICAgIH0pLnJlcGxhY2UoL1xce0AoW1xcd1xcZFxcLl0rKSg/OlxcfChbXFx3XFxkXSspKD86XFxzKj1cXHMqKFtcXHdcXGQsXFxzI10rKSk/KT9cXH0vZyxmdW5jdGlvbihhbGwsbTEsZnVuYyxhcmdzKXtcclxuXHJcbiAgICAgICAgICAgIGlmKG0xLmluZGV4T2YoJy4nKT4wKXtcclxuICAgICAgICAgICAgICAgIHZhciBrZXlzPW0xLnNwbGl0KCcuJyksdmFsPWRhdGE7XHJcbiAgICAgICAgICAgICAgICBmb3IodmFyIGk9MDtpPGtleXMubGVuZ3RoO2krKyl7XHJcbiAgICAgICAgICAgICAgICAgICAgaWYodmFsW2tleXNbaV1dIT09dW5kZWZpbmVkICYmIHZhbFtrZXlzW2ldXSE9PW51bGwpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB2YWw9dmFsW2tleXNbaV1dO1xyXG4gICAgICAgICAgICAgICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB2YWwgPSAnJztcclxuICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgcmV0dXJuIGNhbGxmdW5jKHZhbCxmdW5jLGFyZ3MpO1xyXG4gICAgICAgICAgICB9ZWxzZXtcclxuICAgICAgICAgICAgICAgIHJldHVybiBjYWxsZnVuYyhkYXRhW20xXSxmdW5jLGFyZ3MsZGF0YSk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KTtcclxuICAgIH1cclxufTtcclxuXHJcbmZ1bmN0aW9uIHRvc3RyaW5nKG9iaikge1xyXG4gICAgaWYob2JqICYmIG9iai50b1N0cmluZyl7XHJcbiAgICAgICAgcmV0dXJuIG9iai50b1N0cmluZygpO1xyXG4gICAgfVxyXG4gICAgcmV0dXJuICcnO1xyXG59XHJcblxyXG5mdW5jdGlvbiBjYWxsZnVuYyh2YWwsZnVuYyxhcmdzLHRoaXNvYmope1xyXG4gICAgaWYoIWFyZ3Mpe1xyXG4gICAgICAgIGFyZ3M9W3ZhbF07XHJcbiAgICB9ZWxzZXtcclxuICAgICAgICBpZih0eXBlb2YgYXJncz09PSdzdHJpbmcnKWFyZ3M9YXJncy5zcGxpdCgnLCcpO1xyXG4gICAgICAgIHZhciBhcmdpZHg9YXJncy5pbmRleE9mKCcjIyMnKTtcclxuICAgICAgICBpZihhcmdpZHg+PTApe1xyXG4gICAgICAgICAgICBhcmdzW2FyZ2lkeF09dmFsO1xyXG4gICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICBhcmdzPVt2YWxdLmNvbmNhdChhcmdzKTtcclxuICAgICAgICB9XHJcbiAgICB9XHJcblxyXG4gICAgcmV0dXJuIHdpbmRvd1tmdW5jXT93aW5kb3dbZnVuY10uYXBwbHkodGhpc29iaixhcmdzKTooKHZhbD09PXVuZGVmaW5lZHx8dmFsPT09bnVsbCk/Jyc6dmFsKTtcclxufVxyXG5cclxuZnVuY3Rpb24gaWlmKHYsbTEsbTIpe1xyXG4gICAgaWYodj09PScwJyl2PTA7XHJcbiAgICByZXR1cm4gdj9tMTptMjtcclxufSIsIlxyXG52YXIgZGlhbG9nVHBsPSc8ZGl2IGNsYXNzPVwibW9kYWwgZmFkZVwiIGlkPVwie0BpZH1cIiB0YWJpbmRleD1cIi0xXCIgcm9sZT1cImRpYWxvZ1wiIGFyaWEtbGFiZWxsZWRieT1cIntAaWR9TGFiZWxcIiBhcmlhLWhpZGRlbj1cInRydWVcIj5cXG4nICtcclxuICAgICcgICAgPGRpdiBjbGFzcz1cIm1vZGFsLWRpYWxvZ1wiPlxcbicgK1xyXG4gICAgJyAgICAgICAgPGRpdiBjbGFzcz1cIm1vZGFsLWNvbnRlbnRcIj5cXG4nICtcclxuICAgICcgICAgICAgICAgICA8ZGl2IGNsYXNzPVwibW9kYWwtaGVhZGVyXCI+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgICAgIDxoNCBjbGFzcz1cIm1vZGFsLXRpdGxlXCIgaWQ9XCJ7QGlkfUxhYmVsXCI+PC9oND5cXG4nICtcclxuICAgICcgICAgICAgICAgICAgICAgPGJ1dHRvbiB0eXBlPVwiYnV0dG9uXCIgY2xhc3M9XCJjbG9zZVwiIGRhdGEtZGlzbWlzcz1cIm1vZGFsXCI+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgICAgICAgICA8c3BhbiBhcmlhLWhpZGRlbj1cInRydWVcIj4mdGltZXM7PC9zcGFuPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgICAgICAgICAgPHNwYW4gY2xhc3M9XCJzci1vbmx5XCI+Q2xvc2U8L3NwYW4+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgICAgIDwvYnV0dG9uPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgIDwvZGl2PlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJtb2RhbC1ib2R5XCI+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgPC9kaXY+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgPGRpdiBjbGFzcz1cIm1vZGFsLWZvb3RlclwiPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgICAgICA8bmF2IGNsYXNzPVwibmF2IG5hdi1maWxsXCI+PC9uYXY+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgPC9kaXY+XFxuJyArXHJcbiAgICAnICAgICAgICA8L2Rpdj5cXG4nICtcclxuICAgICcgICAgPC9kaXY+XFxuJyArXHJcbiAgICAnPC9kaXY+JztcclxudmFyIGRpYWxvZ0lkeD0wO1xyXG5mdW5jdGlvbiBEaWFsb2cob3B0cyl7XHJcbiAgICBpZighb3B0cylvcHRzPXt9O1xyXG4gICAgLy/lpITnkIbmjInpkq5cclxuICAgIGlmKG9wdHMuYnRucyE9PXVuZGVmaW5lZCkge1xyXG4gICAgICAgIGlmICh0eXBlb2Yob3B0cy5idG5zKSA9PSAnc3RyaW5nJykge1xyXG4gICAgICAgICAgICBvcHRzLmJ0bnMgPSBbb3B0cy5idG5zXTtcclxuICAgICAgICB9XHJcbiAgICAgICAgLy92YXIgZGZ0PS0xO1xyXG4gICAgICAgIGZvciAodmFyIGkgPSAwOyBpIDwgb3B0cy5idG5zLmxlbmd0aDsgaSsrKSB7XHJcbiAgICAgICAgICAgIGlmKHR5cGVvZihvcHRzLmJ0bnNbaV0pPT0nc3RyaW5nJyl7XHJcbiAgICAgICAgICAgICAgICBvcHRzLmJ0bnNbaV09eyd0ZXh0JzpvcHRzLmJ0bnNbaV19O1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIGlmKG9wdHMuYnRuc1tpXS5pc2RlZmF1bHQpe1xyXG4gICAgICAgICAgICAgICAgb3B0cy5kZWZhdWx0QnRuPWk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9XHJcbiAgICAgICAgaWYob3B0cy5kZWZhdWx0QnRuPT09dW5kZWZpbmVkKXtcclxuICAgICAgICAgICAgb3B0cy5kZWZhdWx0QnRuPW9wdHMuYnRucy5sZW5ndGgtMTtcclxuICAgICAgICAgICAgb3B0cy5idG5zW29wdHMuZGVmYXVsdEJ0bl0uaXNkZWZhdWx0PXRydWU7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICBpZihvcHRzLmJ0bnNbb3B0cy5kZWZhdWx0QnRuXSAmJiAhb3B0cy5idG5zW29wdHMuZGVmYXVsdEJ0bl1bJ3R5cGUnXSl7XHJcbiAgICAgICAgICAgIG9wdHMuYnRuc1tvcHRzLmRlZmF1bHRCdG5dWyd0eXBlJ109J3ByaW1hcnknO1xyXG4gICAgICAgIH1cclxuICAgIH1cclxuXHJcbiAgICB0aGlzLm9wdGlvbnM9JC5leHRlbmQoe1xyXG4gICAgICAgICdpZCc6J2RsZ01vZGFsJytkaWFsb2dJZHgrKyxcclxuICAgICAgICAnaGVhZGVyJzp0cnVlLFxyXG4gICAgICAgICdiYWNrZHJvcCc6dHJ1ZSxcclxuICAgICAgICAnc2l6ZSc6JycsXHJcbiAgICAgICAgJ2J0bnMnOltcclxuICAgICAgICAgICAgeyd0ZXh0Jzon5Y+W5raIJywndHlwZSc6J3NlY29uZGFyeSd9LFxyXG4gICAgICAgICAgICB7J3RleHQnOifnoa7lrponLCdpc2RlZmF1bHQnOnRydWUsJ3R5cGUnOidwcmltYXJ5J31cclxuICAgICAgICBdLFxyXG4gICAgICAgICdkZWZhdWx0QnRuJzoxLFxyXG4gICAgICAgICdvbnN1cmUnOm51bGwsXHJcbiAgICAgICAgJ29uc2hvdyc6bnVsbCxcclxuICAgICAgICAnb25zaG93bic6bnVsbCxcclxuICAgICAgICAnb25oaWRlJzpudWxsLFxyXG4gICAgICAgICdvbmhpZGRlbic6bnVsbFxyXG4gICAgfSxvcHRzKTtcclxuXHJcbiAgICB0aGlzLmJveD0kKHRoaXMub3B0aW9ucy5pZCk7XHJcbn1cclxuRGlhbG9nLnByb3RvdHlwZS5nZW5lckJ0bj1mdW5jdGlvbihvcHQsaWR4KXtcclxuICAgIGlmKG9wdFsndHlwZSddKW9wdFsnY2xhc3MnXT0nYnRuLW91dGxpbmUtJytvcHRbJ3R5cGUnXTtcclxuICAgIHJldHVybiAnPGEgaHJlZj1cImphdmFzY3JpcHQ6XCIgY2xhc3M9XCJuYXYtaXRlbSBidG4gJysob3B0WydjbGFzcyddP29wdFsnY2xhc3MnXTonYnRuLW91dGxpbmUtc2Vjb25kYXJ5JykrJ1wiIGRhdGEtaW5kZXg9XCInK2lkeCsnXCI+JytvcHQudGV4dCsnPC9hPic7XHJcbn07XHJcbkRpYWxvZy5wcm90b3R5cGUuc2hvdz1mdW5jdGlvbihodG1sLHRpdGxlKXtcclxuICAgIHRoaXMuYm94PSQoJyMnK3RoaXMub3B0aW9ucy5pZCk7XHJcbiAgICBpZighdGl0bGUpdGl0bGU9J+ezu+e7n+aPkOekuic7XHJcblxyXG4gICAgaWYodGhpcy5ib3gubGVuZ3RoPDEpIHtcclxuICAgICAgICAkKGRvY3VtZW50LmJvZHkpLmFwcGVuZChkaWFsb2dUcGwucmVwbGFjZSgnbW9kYWwtYm9keScsJ21vZGFsLWJvZHknKyh0aGlzLm9wdGlvbnMuYm9keUNsYXNzPygnICcrdGhpcy5vcHRpb25zLmJvZHlDbGFzcyk6JycpKS5jb21waWxlKHsnaWQnOiB0aGlzLm9wdGlvbnMuaWR9KSk7XHJcbiAgICAgICAgdGhpcy5ib3g9JCgnIycrdGhpcy5vcHRpb25zLmlkKTtcclxuICAgIH1lbHNle1xyXG4gICAgICAgIHRoaXMuYm94LnVuYmluZCgpO1xyXG4gICAgfVxyXG4gICAgaWYoIXRoaXMub3B0aW9ucy5oZWFkZXIpe1xyXG4gICAgICAgIHRoaXMuYm94LmZpbmQoJy5tb2RhbC1oZWFkZXInKS5yZW1vdmUoKTtcclxuICAgIH1cclxuICAgIGlmKHRoaXMub3B0aW9ucy5iYWNrZHJvcCE9PXRydWUpe1xyXG4gICAgICAgIHRoaXMuYm94LmRhdGEoJ2JhY2tkcm9wJyx0aGlzLm9wdGlvbnMuYmFja2Ryb3ApO1xyXG4gICAgfVxyXG4gICAgaWYoIXRoaXMub3B0aW9ucy5rZXlib2FyZCl7XHJcbiAgICAgICAgdGhpcy5ib3guZGF0YSgna2V5Ym9hcmQnLGZhbHNlKTtcclxuICAgIH1cclxuXHJcbiAgICAvL3RoaXMuYm94LmZpbmQoJy5tb2RhbC1mb290ZXIgLmJ0bi1wcmltYXJ5JykudW5iaW5kKCk7XHJcbiAgICB2YXIgc2VsZj10aGlzO1xyXG4gICAgRGlhbG9nLmluc3RhbmNlPXNlbGY7XHJcblxyXG4gICAgLy/nlJ/miJDmjInpkq5cclxuICAgIHZhciBidG5zPVtdO1xyXG4gICAgZm9yKHZhciBpPTA7aTx0aGlzLm9wdGlvbnMuYnRucy5sZW5ndGg7aSsrKXtcclxuICAgICAgICBidG5zLnB1c2godGhpcy5nZW5lckJ0bih0aGlzLm9wdGlvbnMuYnRuc1tpXSxpKSk7XHJcbiAgICB9XHJcbiAgICB0aGlzLmJveC5maW5kKCcubW9kYWwtZm9vdGVyIC5uYXYnKS5odG1sKGJ0bnMuam9pbignXFxuJykpO1xyXG5cclxuICAgIHZhciBkaWFsb2c9dGhpcy5ib3guZmluZCgnLm1vZGFsLWRpYWxvZycpO1xyXG4gICAgZGlhbG9nLnJlbW92ZUNsYXNzKCdtb2RhbC1zbScpLnJlbW92ZUNsYXNzKCdtb2RhbC1sZycpO1xyXG4gICAgaWYodGhpcy5vcHRpb25zLnNpemU9PSdzbScpIHtcclxuICAgICAgICBkaWFsb2cuYWRkQ2xhc3MoJ21vZGFsLXNtJyk7XHJcbiAgICB9ZWxzZSBpZih0aGlzLm9wdGlvbnMuc2l6ZT09J2xnJykge1xyXG4gICAgICAgIGRpYWxvZy5hZGRDbGFzcygnbW9kYWwtbGcnKTtcclxuICAgIH1cclxuICAgIHRoaXMuYm94LmZpbmQoJy5tb2RhbC10aXRsZScpLnRleHQodGl0bGUpO1xyXG5cclxuICAgIHZhciBib2R5PXRoaXMuYm94LmZpbmQoJy5tb2RhbC1ib2R5Jyk7XHJcbiAgICBib2R5Lmh0bWwoaHRtbCk7XHJcbiAgICB0aGlzLmJveC5vbignaGlkZS5icy5tb2RhbCcsZnVuY3Rpb24oKXtcclxuICAgICAgICBpZihzZWxmLm9wdGlvbnMub25oaWRlKXtcclxuICAgICAgICAgICAgc2VsZi5vcHRpb25zLm9uaGlkZShib2R5LHNlbGYuYm94KTtcclxuICAgICAgICB9XHJcbiAgICAgICAgRGlhbG9nLmluc3RhbmNlPW51bGw7XHJcbiAgICB9KTtcclxuICAgIHRoaXMuYm94Lm9uKCdoaWRkZW4uYnMubW9kYWwnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgaWYoc2VsZi5vcHRpb25zLm9uaGlkZGVuKXtcclxuICAgICAgICAgICAgc2VsZi5vcHRpb25zLm9uaGlkZGVuKGJvZHksc2VsZi5ib3gpO1xyXG4gICAgICAgIH1cclxuICAgICAgICBzZWxmLmJveC5yZW1vdmUoKTtcclxuICAgIH0pO1xyXG4gICAgdGhpcy5ib3gub24oJ3Nob3cuYnMubW9kYWwnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgaWYoc2VsZi5vcHRpb25zLm9uc2hvdyl7XHJcbiAgICAgICAgICAgIHNlbGYub3B0aW9ucy5vbnNob3coYm9keSxzZWxmLmJveCk7XHJcbiAgICAgICAgfVxyXG4gICAgfSk7XHJcbiAgICB0aGlzLmJveC5vbignc2hvd24uYnMubW9kYWwnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgaWYoc2VsZi5vcHRpb25zLm9uc2hvd24pe1xyXG4gICAgICAgICAgICBzZWxmLm9wdGlvbnMub25zaG93bihib2R5LHNlbGYuYm94KTtcclxuICAgICAgICB9XHJcbiAgICB9KTtcclxuICAgIHRoaXMuYm94LmZpbmQoJy5tb2RhbC1mb290ZXIgLmJ0bicpLmNsaWNrKGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgdmFyIHJlc3VsdD10cnVlLGlkeD0kKHRoaXMpLmRhdGEoJ2luZGV4Jyk7XHJcbiAgICAgICAgaWYoc2VsZi5vcHRpb25zLmJ0bnNbaWR4XS5jbGljayl7XHJcbiAgICAgICAgICAgIHJlc3VsdCA9IHNlbGYub3B0aW9ucy5idG5zW2lkeF0uY2xpY2suYXBwbHkodGhpcyxbYm9keSwgc2VsZi5ib3hdKTtcclxuICAgICAgICB9XHJcbiAgICAgICAgaWYoaWR4PT1zZWxmLm9wdGlvbnMuZGVmYXVsdEJ0bikge1xyXG4gICAgICAgICAgICBpZiAoc2VsZi5vcHRpb25zLm9uc3VyZSkge1xyXG4gICAgICAgICAgICAgICAgcmVzdWx0ID0gc2VsZi5vcHRpb25zLm9uc3VyZS5hcHBseSh0aGlzLFtib2R5LCBzZWxmLmJveF0pO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfVxyXG4gICAgICAgIGlmKHJlc3VsdCE9PWZhbHNlKXtcclxuICAgICAgICAgICAgc2VsZi5ib3gubW9kYWwoJ2hpZGUnKTtcclxuICAgICAgICB9XHJcbiAgICB9KTtcclxuICAgIHRoaXMuYm94Lm1vZGFsKCdzaG93Jyk7XHJcbiAgICByZXR1cm4gdGhpcztcclxufTtcclxuRGlhbG9nLnByb3RvdHlwZS5oaWRlPURpYWxvZy5wcm90b3R5cGUuY2xvc2U9ZnVuY3Rpb24oKXtcclxuICAgIHRoaXMuYm94Lm1vZGFsKCdoaWRlJyk7XHJcbiAgICByZXR1cm4gdGhpcztcclxufTtcclxuXHJcbnZhciBkaWFsb2c9e1xyXG4gICAgYWxlcnQ6ZnVuY3Rpb24obWVzc2FnZSxjYWxsYmFjayxpY29uKXtcclxuICAgICAgICB2YXIgY2FsbGVkPWZhbHNlO1xyXG4gICAgICAgIHZhciBpc2NhbGxiYWNrPXR5cGVvZiBjYWxsYmFjaz09J2Z1bmN0aW9uJztcclxuICAgICAgICBpZihjYWxsYmFjayAmJiAhaXNjYWxsYmFjayl7XHJcbiAgICAgICAgICAgIGljb249Y2FsbGJhY2s7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHZhciBpY29uTWFwPSB7XHJcbiAgICAgICAgICAgICdzdWNjZXNzJzonY2hlY2ttYXJrLWNpcmNsZScsXHJcbiAgICAgICAgICAgICdpbmZvJzogJ2luZm9ybWF0aW9uLWNpcmNsZScsXHJcbiAgICAgICAgICAgICd3YXJuaW5nJzonYWxlcnQnLFxyXG4gICAgICAgICAgICAnZXJyb3InOidyZW1vdmUtY2lyY2xlJ1xyXG4gICAgICAgIH07XHJcbiAgICAgICAgdmFyIGNvbG9yPSdwcmltYXJ5JztcclxuICAgICAgICBpZighaWNvbilpY29uPSdpbmZvcm1hdGlvbi1jaXJjbGUnO1xyXG4gICAgICAgIGVsc2UgaWYoaWNvbk1hcFtpY29uXSl7XHJcbiAgICAgICAgICAgIGNvbG9yPWljb249PSdlcnJvcic/J2Rhbmdlcic6aWNvbjtcclxuICAgICAgICAgICAgaWNvbj1pY29uTWFwW2ljb25dO1xyXG4gICAgICAgIH1cclxuICAgICAgICB2YXIgaHRtbD0nPGRpdiBjbGFzcz1cInJvd1wiIHN0eWxlPVwiYWxpZ24taXRlbXM6IGNlbnRlcjtcIj48ZGl2IGNsYXNzPVwiY29sLTMgdGV4dC1yaWdodFwiPjxpIGNsYXNzPVwiaW9uLW1kLXtAaWNvbn0gdGV4dC17QGNvbG9yfVwiIHN0eWxlPVwiZm9udC1zaXplOjNlbTtcIj48L2k+IDwvZGl2PjxkaXYgY2xhc3M9XCJjb2wtOVwiID57QG1lc3NhZ2V9PC9kaXY+IDwvZGl2PicuY29tcGlsZSh7XHJcbiAgICAgICAgICAgIG1lc3NhZ2U6bWVzc2FnZSxcclxuICAgICAgICAgICAgaWNvbjppY29uLFxyXG4gICAgICAgICAgICBjb2xvcjpjb2xvclxyXG4gICAgICAgIH0pO1xyXG4gICAgICAgIHJldHVybiBuZXcgRGlhbG9nKHtcclxuICAgICAgICAgICAgYnRuczon56Gu5a6aJyxcclxuICAgICAgICAgICAgaGVhZGVyOmZhbHNlLFxyXG4gICAgICAgICAgICBvbnN1cmU6ZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgICAgIGlmKGlzY2FsbGJhY2spe1xyXG4gICAgICAgICAgICAgICAgICAgIGNhbGxlZD10cnVlO1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBjYWxsYmFjayh0cnVlKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgb25oaWRlOmZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgICAgICBpZighY2FsbGVkICYmIGlzY2FsbGJhY2spe1xyXG4gICAgICAgICAgICAgICAgICAgIGNhbGxiYWNrKGZhbHNlKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLnNob3coaHRtbCwnJyk7XHJcbiAgICB9LFxyXG4gICAgY29uZmlybTpmdW5jdGlvbihtZXNzYWdlLGNvbmZpcm0sY2FuY2VsLGljb24pe1xyXG4gICAgICAgIHZhciBjYWxsZWQ9ZmFsc2U7XHJcbiAgICAgICAgaWYodHlwZW9mIGNvbmZpcm09PSdzdHJpbmcnKXtcclxuICAgICAgICAgICAgaWNvbj1jb25maXJtO1xyXG4gICAgICAgICAgICBpZih0eXBlb2YgY2FuY2VsPT0nZnVuY3Rpb24nKXtcclxuICAgICAgICAgICAgICAgIGNvbmZpcm09Y2FuY2VsO1xyXG4gICAgICAgICAgICAgICAgY2FuY2VsPW51bGw7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9ZWxzZSBpZih0eXBlb2YgY2FsY2VsPT0nc3RyaW5nJyl7XHJcbiAgICAgICAgICAgIGljb249Y2FuY2VsO1xyXG4gICAgICAgIH1cclxuICAgICAgICB2YXIgaWNvbk1hcD0ge1xyXG4gICAgICAgICAgICAnc3VjY2Vzcyc6J2NoZWNrbWFyay1jaXJjbGUnLFxyXG4gICAgICAgICAgICAnaW5mbyc6ICdpbmZvcm1hdGlvbi1jaXJjbGUnLFxyXG4gICAgICAgICAgICAnd2FybmluZyc6J2FsZXJ0JyxcclxuICAgICAgICAgICAgJ2Vycm9yJzoncmVtb3ZlLWNpcmNsZSdcclxuICAgICAgICB9O1xyXG4gICAgICAgIHZhciBjb2xvcj0ncHJpbWFyeSc7XHJcbiAgICAgICAgaWYoIWljb24paWNvbj0naW5mb3JtYXRpb24tY2lyY2xlJztcclxuICAgICAgICBlbHNlIGlmKGljb25NYXBbaWNvbl0pe1xyXG4gICAgICAgICAgICBjb2xvcj1pY29uPT0nZXJyb3InPydkYW5nZXInOmljb247XHJcbiAgICAgICAgICAgIGljb249aWNvbk1hcFtpY29uXTtcclxuICAgICAgICB9XHJcbiAgICAgICAgdmFyIGh0bWw9JzxkaXYgY2xhc3M9XCJyb3dcIiBzdHlsZT1cImFsaWduLWl0ZW1zOiBjZW50ZXI7XCI+PGRpdiBjbGFzcz1cImNvbC0zIHRleHQtcmlnaHRcIj48aSBjbGFzcz1cImlvbi1tZC17QGljb259IHRleHQte0Bjb2xvcn1cIiBzdHlsZT1cImZvbnQtc2l6ZTozZW07XCI+PC9pPiA8L2Rpdj48ZGl2IGNsYXNzPVwiY29sLTlcIiA+e0BtZXNzYWdlfTwvZGl2PiA8L2Rpdj4nLmNvbXBpbGUoe1xyXG4gICAgICAgICAgICBtZXNzYWdlOm1lc3NhZ2UsXHJcbiAgICAgICAgICAgIGljb246aWNvbixcclxuICAgICAgICAgICAgY29sb3I6Y29sb3JcclxuICAgICAgICB9KTtcclxuICAgICAgICByZXR1cm4gbmV3IERpYWxvZyh7XHJcbiAgICAgICAgICAgICdoZWFkZXInOmZhbHNlLFxyXG4gICAgICAgICAgICAnYmFja2Ryb3AnOidzdGF0aWMnLFxyXG4gICAgICAgICAgICAnb25zdXJlJzpmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICAgICAgaWYoY29uZmlybSAmJiB0eXBlb2YgY29uZmlybT09J2Z1bmN0aW9uJyl7XHJcbiAgICAgICAgICAgICAgICAgICAgY2FsbGVkPXRydWU7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGNvbmZpcm0oKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgJ29uaGlkZSc6ZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAgICAgaWYoY2FsbGVkPT1mYWxzZSAmJiB0eXBlb2YgY2FuY2VsPT0nZnVuY3Rpb24nKXtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gY2FuY2VsKCk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS5zaG93KGh0bWwpO1xyXG4gICAgfSxcclxuICAgIHByb21wdDpmdW5jdGlvbihtZXNzYWdlLGNhbGxiYWNrLGNhbmNlbCl7XHJcbiAgICAgICAgdmFyIGNhbGxlZD1mYWxzZTtcclxuICAgICAgICB2YXIgY29udGVudEh0bWw9JzxkaXYgY2xhc3M9XCJmb3JtLWdyb3VwXCI+e0BpbnB1dH08L2Rpdj4nO1xyXG4gICAgICAgIHZhciB0aXRsZT0n6K+36L6T5YWl5L+h5oGvJztcclxuICAgICAgICBpZih0eXBlb2YgbWVzc2FnZT09J3N0cmluZycpe1xyXG4gICAgICAgICAgICB0aXRsZT1tZXNzYWdlO1xyXG4gICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICB0aXRsZT1tZXNzYWdlLnRpdGxlO1xyXG4gICAgICAgICAgICBpZihtZXNzYWdlLmNvbnRlbnQpIHtcclxuICAgICAgICAgICAgICAgIGNvbnRlbnRIdG1sID0gbWVzc2FnZS5jb250ZW50LmluZGV4T2YoJ3tAaW5wdXR9JykgPiAtMSA/IG1lc3NhZ2UuY29udGVudCA6IG1lc3NhZ2UuY29udGVudCArIGNvbnRlbnRIdG1sO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHJldHVybiBuZXcgRGlhbG9nKHtcclxuICAgICAgICAgICAgJ2JhY2tkcm9wJzonc3RhdGljJyxcclxuICAgICAgICAgICAgJ29uc2hvdyc6ZnVuY3Rpb24oYm9keSl7XHJcbiAgICAgICAgICAgICAgICBpZihtZXNzYWdlICYmIG1lc3NhZ2Uub25zaG93KXtcclxuICAgICAgICAgICAgICAgICAgICBtZXNzYWdlLm9uc2hvdyhib2R5KTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgJ29uc2hvd24nOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgYm9keS5maW5kKCdbbmFtZT1jb25maXJtX2lucHV0XScpLmZvY3VzKCk7XHJcbiAgICAgICAgICAgICAgICBpZihtZXNzYWdlICYmIG1lc3NhZ2Uub25zaG93bil7XHJcbiAgICAgICAgICAgICAgICAgICAgbWVzc2FnZS5vbnNob3duKGJvZHkpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAnb25zdXJlJzpmdW5jdGlvbihib2R5KXtcclxuICAgICAgICAgICAgICAgIHZhciB2YWw9Ym9keS5maW5kKCdbbmFtZT1jb25maXJtX2lucHV0XScpLnZhbCgpO1xyXG4gICAgICAgICAgICAgICAgaWYodHlwZW9mIGNhbGxiYWNrPT0nZnVuY3Rpb24nKXtcclxuICAgICAgICAgICAgICAgICAgICB2YXIgcmVzdWx0ID0gY2FsbGJhY2sodmFsKTtcclxuICAgICAgICAgICAgICAgICAgICBpZihyZXN1bHQ9PT10cnVlKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgY2FsbGVkPXRydWU7XHJcbiAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiByZXN1bHQ7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdvbmhpZGUnOmZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgIGlmKGNhbGxlZD1mYWxzZSAmJiB0eXBlb2YgY2FuY2VsPT0nZnVuY3Rpb24nKXtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gY2FuY2VsKCk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS5zaG93KGNvbnRlbnRIdG1sLmNvbXBpbGUoe2lucHV0Oic8aW5wdXQgdHlwZT1cInRleHRcIiBuYW1lPVwiY29uZmlybV9pbnB1dFwiIGNsYXNzPVwiZm9ybS1jb250cm9sXCIgLz4nfSksdGl0bGUpO1xyXG4gICAgfSxcclxuICAgIGFjdGlvbjpmdW5jdGlvbiAobGlzdCxjYWxsYmFjayx0aXRsZSkge1xyXG4gICAgICAgIHZhciBodG1sPSc8ZGl2IGNsYXNzPVwibGlzdC1ncm91cFwiPjxhIGhyZWY9XCJqYXZhc2NyaXB0OlwiIGNsYXNzPVwibGlzdC1ncm91cC1pdGVtIGxpc3QtZ3JvdXAtaXRlbS1hY3Rpb25cIj4nK2xpc3Quam9pbignPC9hPjxhIGhyZWY9XCJqYXZhc2NyaXB0OlwiIGNsYXNzPVwibGlzdC1ncm91cC1pdGVtIGxpc3QtZ3JvdXAtaXRlbS1hY3Rpb25cIj4nKSsnPC9hPjwvZGl2Pic7XHJcbiAgICAgICAgdmFyIGFjdGlvbnM9bnVsbDtcclxuICAgICAgICB2YXIgZGxnPW5ldyBEaWFsb2coe1xyXG4gICAgICAgICAgICAnYm9keUNsYXNzJzonbW9kYWwtYWN0aW9uJyxcclxuICAgICAgICAgICAgJ2JhY2tkcm9wJzonc3RhdGljJyxcclxuICAgICAgICAgICAgJ2J0bnMnOltcclxuICAgICAgICAgICAgICAgIHsndGV4dCc6J+WPlua2iCcsJ3R5cGUnOidzZWNvbmRhcnknfVxyXG4gICAgICAgICAgICBdLFxyXG4gICAgICAgICAgICAnb25zaG93JzpmdW5jdGlvbihib2R5KXtcclxuICAgICAgICAgICAgICAgIGFjdGlvbnM9Ym9keS5maW5kKCcubGlzdC1ncm91cC1pdGVtLWFjdGlvbicpO1xyXG4gICAgICAgICAgICAgICAgYWN0aW9ucy5jbGljayhmdW5jdGlvbiAoZSkge1xyXG4gICAgICAgICAgICAgICAgICAgIGFjdGlvbnMucmVtb3ZlQ2xhc3MoJ2FjdGl2ZScpO1xyXG4gICAgICAgICAgICAgICAgICAgICQodGhpcykuYWRkQ2xhc3MoJ2FjdGl2ZScpO1xyXG4gICAgICAgICAgICAgICAgICAgIHZhciB2YWw9YWN0aW9ucy5pbmRleCh0aGlzKTtcclxuICAgICAgICAgICAgICAgICAgICBpZih0eXBlb2YgY2FsbGJhY2s9PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiggY2FsbGJhY2sodmFsKSE9PWZhbHNlKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGRsZy5jbG9zZSgpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgfWVsc2Uge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBkbGcuY2xvc2UoKTtcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICB9KVxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAnb25zdXJlJzpmdW5jdGlvbihib2R5KXtcclxuICAgICAgICAgICAgICAgIHJldHVybiB0cnVlO1xyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAnb25oaWRlJzpmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgICAgICByZXR1cm4gdHJ1ZTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLnNob3coaHRtbCx0aXRsZT90aXRsZTon6K+36YCJ5oupJyk7XHJcbiAgICB9LFxyXG4gICAgcGlja0xpc3Q6ZnVuY3Rpb24gKGNvbmZpZyxjYWxsYmFjayxmaWx0ZXIpIHtcclxuICAgICAgICBpZih0eXBlb2YgY29uZmlnPT09J3N0cmluZycpY29uZmlnPXt1cmw6Y29uZmlnfTtcclxuICAgICAgICBjb25maWc9JC5leHRlbmQoe1xyXG4gICAgICAgICAgICAndXJsJzonJyxcclxuICAgICAgICAgICAgJ25hbWUnOiflr7nosaEnLFxyXG4gICAgICAgICAgICAnc2VhcmNoSG9sZGVyJzon5qC55o2u5ZCN56ew5pCc57SiJyxcclxuICAgICAgICAgICAgJ2lka2V5JzonaWQnLFxyXG4gICAgICAgICAgICAnb25Sb3cnOm51bGwsXHJcbiAgICAgICAgICAgICdleHRlbmQnOm51bGwsXHJcbiAgICAgICAgICAgICdyb3dUZW1wbGF0ZSc6JzxhIGhyZWY9XCJqYXZhc2NyaXB0OlwiIGRhdGEtaWQ9XCJ7QGlkfVwiIGNsYXNzPVwibGlzdC1ncm91cC1pdGVtIGxpc3QtZ3JvdXAtaXRlbS1hY3Rpb25cIj5be0BpZH1dJm5ic3A7PGkgY2xhc3M9XCJpb24tbWQtcGVyc29uXCI+PC9pPiB7QHVzZXJuYW1lfSZuYnNwOyZuYnNwOyZuYnNwOzxzbWFsbD48aSBjbGFzcz1cImlvbi1tZC1waG9uZS1wb3J0cmFpdFwiPjwvaT4ge0Btb2JpbGV9PC9zbWFsbD48L2E+J1xyXG4gICAgICAgIH0sY29uZmlnfHx7fSk7XHJcbiAgICAgICAgdmFyIGN1cnJlbnQ9bnVsbDtcclxuICAgICAgICB2YXIgZXh0aHRtbD0nJztcclxuICAgICAgICBpZihjb25maWcuZXh0ZW5kKXtcclxuICAgICAgICAgICAgZXh0aHRtbD0nPHNlbGVjdCBuYW1lPVwiJytjb25maWcuZXh0ZW5kLm5hbWUrJ1wiIGNsYXNzPVwiZm9ybS1jb250cm9sXCI+PG9wdGlvbiB2YWx1ZT1cIlwiPicrY29uZmlnLmV4dGVuZC50aXRsZSsnPC9vcHRpb24+PC9zZWxlY3Q+JztcclxuICAgICAgICB9XHJcbiAgICAgICAgaWYoIWZpbHRlcilmaWx0ZXI9e307XHJcbiAgICAgICAgdmFyIGRsZz1uZXcgRGlhbG9nKHtcclxuICAgICAgICAgICAgJ2JhY2tkcm9wJzonc3RhdGljJyxcclxuICAgICAgICAgICAgJ29uc2hvd24nOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgdmFyIGJ0bj1ib2R5LmZpbmQoJy5zZWFyY2hidG4nKTtcclxuICAgICAgICAgICAgICAgIHZhciBpbnB1dD1ib2R5LmZpbmQoJy5zZWFyY2h0ZXh0Jyk7XHJcbiAgICAgICAgICAgICAgICB2YXIgbGlzdGJveD1ib2R5LmZpbmQoJy5saXN0LWdyb3VwJyk7XHJcbiAgICAgICAgICAgICAgICB2YXIgaXNsb2FkaW5nPWZhbHNlO1xyXG4gICAgICAgICAgICAgICAgdmFyIGV4dEZpZWxkPW51bGw7XHJcbiAgICAgICAgICAgICAgICBpZihjb25maWcuZXh0ZW5kKXtcclxuICAgICAgICAgICAgICAgICAgICBleHRGaWVsZD1ib2R5LmZpbmQoJ1tuYW1lPScrY29uZmlnLmV4dGVuZC5uYW1lKyddJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgJC5hamF4KHtcclxuICAgICAgICAgICAgICAgICAgICAgICB1cmw6Y29uZmlnLmV4dGVuZC51cmwsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHR5cGU6J0dFVCcsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGRhdGFUeXBlOidKU09OJyxcclxuICAgICAgICAgICAgICAgICAgICAgICAgc3VjY2VzczpmdW5jdGlvbiAoanNvbikge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZXh0RmllbGQuYXBwZW5kKGNvbmZpZy5leHRlbmQuaHRtbFJvdy5jb21waWxlKGpzb24uZGF0YSx0cnVlKSk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIGJ0bi5jbGljayhmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICAgICAgICAgIGlmKGlzbG9hZGluZylyZXR1cm47XHJcbiAgICAgICAgICAgICAgICAgICAgaXNsb2FkaW5nPXRydWU7XHJcbiAgICAgICAgICAgICAgICAgICAgbGlzdGJveC5odG1sKCc8c3BhbiBjbGFzcz1cImxpc3QtbG9hZGluZ1wiPuWKoOi9veS4rS4uLjwvc3Bhbj4nKTtcclxuICAgICAgICAgICAgICAgICAgICBmaWx0ZXJbJ2tleSddPWlucHV0LnZhbCgpO1xyXG4gICAgICAgICAgICAgICAgICAgIGlmKGNvbmZpZy5leHRlbmQpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBmaWx0ZXJbY29uZmlnLmV4dGVuZC5uYW1lXT1leHRGaWVsZC52YWwoKTtcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgJC5hamF4KFxyXG4gICAgICAgICAgICAgICAgICAgICAgICB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB1cmw6Y29uZmlnLnVybCxcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHR5cGU6J0dFVCcsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBkYXRhVHlwZTonSlNPTicsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBkYXRhOmZpbHRlcixcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHN1Y2Nlc3M6ZnVuY3Rpb24oanNvbil7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaXNsb2FkaW5nPWZhbHNlO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmKGpzb24uY29kZT09PTEpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZihqc29uLmRhdGEgJiYganNvbi5kYXRhLmxlbmd0aCkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbGlzdGJveC5odG1sKGNvbmZpZy5yb3dUZW1wbGF0ZS5jb21waWxlKGpzb24uZGF0YSwgdHJ1ZSkpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbGlzdGJveC5maW5kKCdhLmxpc3QtZ3JvdXAtaXRlbScpLmNsaWNrKGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgaWQgPSAkKHRoaXMpLmRhdGEoJ2lkJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgZm9yICh2YXIgaSA9IDA7IGkgPCBqc29uLmRhdGEubGVuZ3RoOyBpKyspIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYoanNvbi5kYXRhW2ldW2NvbmZpZy5pZGtleV09PWlkKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGN1cnJlbnQ9anNvbi5kYXRhW2ldO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbGlzdGJveC5maW5kKCdhLmxpc3QtZ3JvdXAtaXRlbScpLnJlbW92ZUNsYXNzKCdhY3RpdmUnKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykuYWRkQ2xhc3MoJ2FjdGl2ZScpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9KVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9ZWxzZXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxpc3Rib3guaHRtbCgnPHNwYW4gY2xhc3M9XCJsaXN0LWxvYWRpbmdcIj48aSBjbGFzcz1cImlvbi1tZC13YXJuaW5nXCI+PC9pPiDmsqHmnInmo4DntKLliLAnK2NvbmZpZy5uYW1lKyc8L3NwYW4+Jyk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9ZWxzZXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbGlzdGJveC5odG1sKCc8c3BhbiBjbGFzcz1cInRleHQtZGFuZ2VyXCI+PGkgY2xhc3M9XCJpb24tbWQtd2FybmluZ1wiPjwvaT4g5Yqg6L295aSx6LSlPC9zcGFuPicpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgICk7XHJcblxyXG4gICAgICAgICAgICAgICAgfSkudHJpZ2dlcignY2xpY2snKTtcclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgJ29uc3VyZSc6ZnVuY3Rpb24oYm9keSl7XHJcbiAgICAgICAgICAgICAgICBpZighY3VycmVudCl7XHJcbiAgICAgICAgICAgICAgICAgICAgdG9hc3RyLndhcm5pbmcoJ+ayoeaciemAieaLqScrY29uZmlnLm5hbWUrJyEnKTtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICBpZih0eXBlb2YgY2FsbGJhY2s9PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgICAgIHZhciByZXN1bHQgPSBjYWxsYmFjayhjdXJyZW50KTtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gcmVzdWx0O1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkuc2hvdygnPGRpdiBjbGFzcz1cImlucHV0LWdyb3VwXCI+JytleHRodG1sKyc8aW5wdXQgdHlwZT1cInRleHRcIiBjbGFzcz1cImZvcm0tY29udHJvbCBzZWFyY2h0ZXh0XCIgbmFtZT1cImtleXdvcmRcIiBwbGFjZWhvbGRlcj1cIicrY29uZmlnLnNlYXJjaEhvbGRlcisnXCIvPjxkaXYgY2xhc3M9XCJpbnB1dC1ncm91cC1hcHBlbmRcIj48YSBjbGFzcz1cImJ0biBidG4tb3V0bGluZS1zZWNvbmRhcnkgc2VhcmNoYnRuXCI+PGkgY2xhc3M9XCJpb24tbWQtc2VhcmNoXCI+PC9pPjwvYT48L2Rpdj48L2Rpdj48ZGl2IGNsYXNzPVwibGlzdC1ncm91cCBsaXN0LWdyb3VwLXBpY2tlciBtdC0yXCI+PC9kaXY+Jywn6K+35pCc57Si5bm26YCJ5oupJytjb25maWcubmFtZSk7XHJcbiAgICB9LFxyXG4gICAgcGlja1VzZXI6ZnVuY3Rpb24oY2FsbGJhY2ssZmlsdGVyKXtcclxuICAgICAgICByZXR1cm4gdGhpcy5waWNrTGlzdCh7XHJcbiAgICAgICAgICAgICd1cmwnOndpbmRvdy5nZXRfc2VhcmNoX3VybCgnbWVtYmVyJyksXHJcbiAgICAgICAgICAgICduYW1lJzon5Lya5ZGYJyxcclxuICAgICAgICAgICAgJ3NlYXJjaEhvbGRlcic6J+agueaNruS8muWRmGlk5oiW5ZCN56ew77yM55S16K+d5p2l5pCc57SiJ1xyXG4gICAgICAgIH0sY2FsbGJhY2ssZmlsdGVyKTtcclxuICAgIH0sXHJcbiAgICBwaWNrQXJ0aWNsZTpmdW5jdGlvbihjYWxsYmFjayxmaWx0ZXIpe1xyXG4gICAgICAgIHJldHVybiB0aGlzLnBpY2tMaXN0KHtcclxuICAgICAgICAgICAgJ3VybCc6d2luZG93LmdldF9zZWFyY2hfdXJsKCdhcnRpY2xlJyksXHJcbiAgICAgICAgICAgIHJvd1RlbXBsYXRlOic8YSBocmVmPVwiamF2YXNjcmlwdDpcIiBkYXRhLWlkPVwie0BpZH1cIiBjbGFzcz1cImxpc3QtZ3JvdXAtaXRlbSBsaXN0LWdyb3VwLWl0ZW0tYWN0aW9uXCI+e2lmIEBjb3Zlcn08ZGl2IHN0eWxlPVwiYmFja2dyb3VuZC1pbWFnZTp1cmwoe0Bjb3Zlcn0pXCIgY2xhc3M9XCJpbWd2aWV3XCIgPjwvZGl2PnsvaWZ9PGRpdiBjbGFzcz1cInRleHQtYmxvY2tcIj5be0BpZH1dJm5ic3A7e0B0aXRsZX0mbmJzcDs8YnIgLz57QGRlc2NyaXB0aW9ufTwvZGl2PjwvYT4nLFxyXG4gICAgICAgICAgICBuYW1lOifmlofnq6AnLFxyXG4gICAgICAgICAgICBpZGtleTonaWQnLFxyXG4gICAgICAgICAgICBleHRlbmQ6e1xyXG4gICAgICAgICAgICAgICBuYW1lOidjYXRlJyxcclxuICAgICAgICAgICAgICAgIHRpdGxlOifmjInliIbnsbvmkJzntKInLFxyXG4gICAgICAgICAgICAgICAgdXJsOmdldF9jYXRlX3VybCgnYXJ0aWNsZScpLFxyXG4gICAgICAgICAgICAgICAgaHRtbFJvdzonPG9wdGlvbiB2YWx1ZT1cIntAaWR9XCI+e0BodG1sfXtAdGl0bGV9PC9vcHRpb24+J1xyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAnc2VhcmNoSG9sZGVyJzon5qC55o2u5paH56ug5qCH6aKY5pCc57SiJ1xyXG4gICAgICAgIH0sY2FsbGJhY2ssZmlsdGVyKTtcclxuICAgIH0sXHJcbiAgICBwaWNrUHJvZHVjdDpmdW5jdGlvbihjYWxsYmFjayxmaWx0ZXIpe1xyXG4gICAgICAgIHJldHVybiB0aGlzLnBpY2tMaXN0KHtcclxuICAgICAgICAgICAgJ3VybCc6d2luZG93LmdldF9zZWFyY2hfdXJsKCdwcm9kdWN0JyksXHJcbiAgICAgICAgICAgIHJvd1RlbXBsYXRlOic8YSBocmVmPVwiamF2YXNjcmlwdDpcIiBkYXRhLWlkPVwie0BpZH1cIiBjbGFzcz1cImxpc3QtZ3JvdXAtaXRlbSBsaXN0LWdyb3VwLWl0ZW0tYWN0aW9uXCI+e2lmIEBpbWFnZX08ZGl2IHN0eWxlPVwiYmFja2dyb3VuZC1pbWFnZTp1cmwoe0BpbWFnZX0pXCIgY2xhc3M9XCJpbWd2aWV3XCIgPjwvZGl2PnsvaWZ9PGRpdiBjbGFzcz1cInRleHQtYmxvY2tcIj5be0BpZH1dJm5ic3A7e0B0aXRsZX0mbmJzcDs8YnIgLz57QG1pbl9wcmljZX1+e0BtYXhfcHJpY2V9PC9kaXY+PC9hPicsXHJcbiAgICAgICAgICAgIG5hbWU6J+S6p+WTgScsXHJcbiAgICAgICAgICAgIGlka2V5OidpZCcsXHJcbiAgICAgICAgICAgIGV4dGVuZDp7XHJcbiAgICAgICAgICAgICAgICBuYW1lOidjYXRlJyxcclxuICAgICAgICAgICAgICAgIHRpdGxlOifmjInliIbnsbvmkJzntKInLFxyXG4gICAgICAgICAgICAgICAgdXJsOmdldF9jYXRlX3VybCgncHJvZHVjdCcpLFxyXG4gICAgICAgICAgICAgICAgaHRtbFJvdzonPG9wdGlvbiB2YWx1ZT1cIntAaWR9XCI+e0BodG1sfXtAdGl0bGV9PC9vcHRpb24+J1xyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAnc2VhcmNoSG9sZGVyJzon5qC55o2u5Lqn5ZOB5ZCN56ew5pCc57SiJ1xyXG4gICAgICAgIH0sY2FsbGJhY2ssZmlsdGVyKTtcclxuICAgIH0sXHJcbiAgICBwaWNrTG9jYXRlOmZ1bmN0aW9uKHR5cGUsIGNhbGxiYWNrLCBsb2NhdGUpe1xyXG4gICAgICAgIHZhciBzZXR0ZWRMb2NhdGU9bnVsbDtcclxuICAgICAgICB2YXIgZGxnPW5ldyBEaWFsb2coe1xyXG4gICAgICAgICAgICAnc2l6ZSc6J2xnJyxcclxuICAgICAgICAgICAgJ2JhY2tkcm9wJzonc3RhdGljJyxcclxuICAgICAgICAgICAgJ29uc2hvd24nOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgdmFyIGJ0bj1ib2R5LmZpbmQoJy5zZWFyY2hidG4nKTtcclxuICAgICAgICAgICAgICAgIHZhciBpbnB1dD1ib2R5LmZpbmQoJy5zZWFyY2h0ZXh0Jyk7XHJcbiAgICAgICAgICAgICAgICB2YXIgbWFwYm94PWJvZHkuZmluZCgnLm1hcCcpO1xyXG4gICAgICAgICAgICAgICAgdmFyIG1hcGluZm89Ym9keS5maW5kKCcubWFwaW5mbycpO1xyXG4gICAgICAgICAgICAgICAgbWFwYm94LmNzcygnaGVpZ2h0JywkKHdpbmRvdykuaGVpZ2h0KCkqLjYpO1xyXG4gICAgICAgICAgICAgICAgdmFyIGlzbG9hZGluZz1mYWxzZTtcclxuICAgICAgICAgICAgICAgIHZhciBtYXA9SW5pdE1hcCgndGVuY2VudCcsbWFwYm94LGZ1bmN0aW9uKGFkZHJlc3MsbG9jYXRlKXtcclxuICAgICAgICAgICAgICAgICAgICBtYXBpbmZvLmh0bWwoYWRkcmVzcysnJm5ic3A7Jytsb2NhdGUubG5nKycsJytsb2NhdGUubGF0KTtcclxuICAgICAgICAgICAgICAgICAgICBzZXR0ZWRMb2NhdGU9bG9jYXRlO1xyXG4gICAgICAgICAgICAgICAgfSxsb2NhdGUpO1xyXG4gICAgICAgICAgICAgICAgYnRuLmNsaWNrKGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgICAgICAgICAgdmFyIHNlYXJjaD1pbnB1dC52YWwoKTtcclxuICAgICAgICAgICAgICAgICAgICBtYXAuc2V0TG9jYXRlKHNlYXJjaCk7XHJcbiAgICAgICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgICAgIGJvZHkuZmluZCgnLnNldFRvQ2VudGVyJykuY2xpY2soZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICAgICAgICAgICAgICBtYXAuc2hvd0F0Q2VudGVyKCk7XHJcbiAgICAgICAgICAgICAgICB9KVxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAnb25zdXJlJzpmdW5jdGlvbihib2R5KXtcclxuICAgICAgICAgICAgICAgIGlmKCFzZXR0ZWRMb2NhdGUpe1xyXG4gICAgICAgICAgICAgICAgICAgIHRvYXN0ci53YXJuaW5nKCfmsqHmnInpgInmi6nkvY3nva4hJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgaWYodHlwZW9mIGNhbGxiYWNrPT09J2Z1bmN0aW9uJyl7XHJcbiAgICAgICAgICAgICAgICAgICAgdmFyIHJlc3VsdCA9IGNhbGxiYWNrKHNldHRlZExvY2F0ZSk7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHJlc3VsdDtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLnNob3coJzxkaXYgY2xhc3M9XCJpbnB1dC1ncm91cFwiPjxpbnB1dCB0eXBlPVwidGV4dFwiIGNsYXNzPVwiZm9ybS1jb250cm9sIHNlYXJjaHRleHRcIiBuYW1lPVwia2V5d29yZFwiIHBsYWNlaG9sZGVyPVwi5aGr5YaZ5Zyw5Z2A5qOA57Si5L2N572uXCIvPjxkaXYgY2xhc3M9XCJpbnB1dC1ncm91cC1hcHBlbmRcIj48YSBjbGFzcz1cImJ0biBidG4tb3V0bGluZS1zZWNvbmRhcnkgc2VhcmNoYnRuXCI+PGkgY2xhc3M9XCJpb24tbWQtc2VhcmNoXCI+PC9pPjwvYT48L2Rpdj48L2Rpdj4nICtcclxuICAgICAgICAgICAgJzxkaXYgY2xhc3M9XCJtYXAgbXQtMlwiPjwvZGl2PicgK1xyXG4gICAgICAgICAgICAnPGRpdiBjbGFzcz1cImZsb2F0LXJpZ2h0IG10LTIgbWFwYWN0aW9uc1wiPjxhIGhyZWY9XCJqYXZhc2NyaXB0OlwiIGNsYXNzPVwic2V0VG9DZW50ZXJcIj7lrprkvY3liLDlnLDlm77kuK3lv4M8L2E+PC9kaXY+JyArXHJcbiAgICAgICAgICAgICc8ZGl2IGNsYXNzPVwibWFwaW5mbyBtdC0yIHRleHQtbXV0ZWRcIj7mnKrpgInmi6nkvY3nva48L2Rpdj4nLCfor7fpgInmi6nlnLDlm77kvY3nva4nKTtcclxuICAgIH1cclxufTtcclxuXHJcbmpRdWVyeShmdW5jdGlvbigkKXtcclxuXHJcbiAgICAvL+ebkeaOp+aMiemUrlxyXG4gICAgJChkb2N1bWVudCkub24oJ2tleWRvd24nLCBmdW5jdGlvbihlKXtcclxuICAgICAgICBpZighRGlhbG9nLmluc3RhbmNlKXJldHVybjtcclxuICAgICAgICB2YXIgZGxnPURpYWxvZy5pbnN0YW5jZTtcclxuICAgICAgICBpZiAoZS5rZXlDb2RlID09IDEzKSB7XHJcbiAgICAgICAgICAgIGRsZy5ib3guZmluZCgnLm1vZGFsLWZvb3RlciAuYnRuJykuZXEoZGxnLm9wdGlvbnMuZGVmYXVsdEJ0bikudHJpZ2dlcignY2xpY2snKTtcclxuICAgICAgICB9XHJcbiAgICAgICAgLy/pu5jorqTlt7Lnm5HlkKzlhbPpl61cclxuICAgICAgICAvKmlmIChlLmtleUNvZGUgPT0gMjcpIHtcclxuICAgICAgICAgc2VsZi5oaWRlKCk7XHJcbiAgICAgICAgIH0qL1xyXG4gICAgfSk7XHJcbn0pOyIsIlxyXG5qUXVlcnkuZXh0ZW5kKGpRdWVyeS5mbix7XHJcbiAgICB0YWdzOmZ1bmN0aW9uKG5tLG9udXBkYXRlKXtcclxuICAgICAgICB2YXIgZGF0YT1bXTtcclxuICAgICAgICB2YXIgdHBsPSc8c3BhbiBjbGFzcz1cImJhZGdlIGJhZGdlLWluZm9cIj57QGxhYmVsfTxpbnB1dCB0eXBlPVwiaGlkZGVuXCIgbmFtZT1cIicrbm0rJ1wiIHZhbHVlPVwie0BsYWJlbH1cIi8+PGJ1dHRvbiB0eXBlPVwiYnV0dG9uXCIgY2xhc3M9XCJjbG9zZVwiIGRhdGEtZGlzbWlzcz1cImFsZXJ0XCIgYXJpYS1sYWJlbD1cIkNsb3NlXCI+PHNwYW4gYXJpYS1oaWRkZW49XCJ0cnVlXCI+JnRpbWVzOzwvc3Bhbj48L2J1dHRvbj48L3NwYW4+JztcclxuICAgICAgICB2YXIgaXRlbT0kKHRoaXMpLnBhcmVudHMoJy5mb3JtLWNvbnRyb2wnKTtcclxuICAgICAgICB2YXIgbGFiZWxncm91cD0kKCc8c3BhbiBjbGFzcz1cImJhZGdlLWdyb3VwXCI+PC9zcGFuPicpO1xyXG4gICAgICAgIHZhciBpbnB1dD10aGlzO1xyXG4gICAgICAgIHRoaXMuYmVmb3JlKGxhYmVsZ3JvdXApO1xyXG4gICAgICAgIHRoaXMub24oJ2tleXVwJyxmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICB2YXIgdmFsPSQodGhpcykudmFsKCkucmVwbGFjZSgv77yML2csJywnKTtcclxuICAgICAgICAgICAgdmFyIHVwZGF0ZWQ9ZmFsc2U7XHJcbiAgICAgICAgICAgIGlmKHZhbCAmJiB2YWwuaW5kZXhPZignLCcpPi0xKXtcclxuICAgICAgICAgICAgICAgIHZhciB2YWxzPXZhbC5zcGxpdCgnLCcpO1xyXG4gICAgICAgICAgICAgICAgZm9yKHZhciBpPTA7aTx2YWxzLmxlbmd0aDtpKyspe1xyXG4gICAgICAgICAgICAgICAgICAgIHZhbHNbaV09dmFsc1tpXS5yZXBsYWNlKC9eXFxzfFxccyQvZywnJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgaWYodmFsc1tpXSAmJiBkYXRhLmluZGV4T2YodmFsc1tpXSk9PT0tMSl7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGRhdGEucHVzaCh2YWxzW2ldKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgbGFiZWxncm91cC5hcHBlbmQodHBsLmNvbXBpbGUoe2xhYmVsOnZhbHNbaV19KSk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHVwZGF0ZWQ9dHJ1ZTtcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICBpbnB1dC52YWwoJycpO1xyXG4gICAgICAgICAgICAgICAgaWYodXBkYXRlZCAmJiBvbnVwZGF0ZSlvbnVwZGF0ZShkYXRhKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLm9uKCdibHVyJyxmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICB2YXIgdmFsPSQodGhpcykudmFsKCk7XHJcbiAgICAgICAgICAgIGlmKHZhbCkge1xyXG4gICAgICAgICAgICAgICAgJCh0aGlzKS52YWwodmFsICsgJywnKS50cmlnZ2VyKCdrZXl1cCcpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkudHJpZ2dlcignYmx1cicpO1xyXG4gICAgICAgIGxhYmVsZ3JvdXAub24oJ2NsaWNrJywnLmNsb3NlJyxmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICB2YXIgdGFnPSQodGhpcykucGFyZW50cygnLmJhZGdlJykuZmluZCgnaW5wdXQnKS52YWwoKTtcclxuICAgICAgICAgICAgdmFyIGlkPWRhdGEuaW5kZXhPZih0YWcpO1xyXG4gICAgICAgICAgICBpZihpZClkYXRhLnNwbGljZShpZCwxKTtcclxuICAgICAgICAgICAgJCh0aGlzKS5wYXJlbnRzKCcuYmFkZ2UnKS5yZW1vdmUoKTtcclxuICAgICAgICAgICAgaWYob251cGRhdGUpb251cGRhdGUoZGF0YSk7XHJcbiAgICAgICAgfSk7XHJcbiAgICAgICAgaXRlbS5jbGljayhmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICBpbnB1dC5mb2N1cygpO1xyXG4gICAgICAgIH0pO1xyXG4gICAgfVxyXG59KTsiLCIvL+aXpeacn+e7hOS7tlxyXG5pZigkLmZuLmRhdGV0aW1lcGlja2VyKSB7XHJcbiAgICB2YXIgdG9vbHRpcHM9IHtcclxuICAgICAgICB0b2RheTogJ+WumuS9jeW9k+WJjeaXpeacnycsXHJcbiAgICAgICAgY2xlYXI6ICfmuIXpmaTlt7LpgInml6XmnJ8nLFxyXG4gICAgICAgIGNsb3NlOiAn5YWz6Zet6YCJ5oup5ZmoJyxcclxuICAgICAgICBzZWxlY3RNb250aDogJ+mAieaLqeaciOS7vScsXHJcbiAgICAgICAgcHJldk1vbnRoOiAn5LiK5Liq5pyIJyxcclxuICAgICAgICBuZXh0TW9udGg6ICfkuIvkuKrmnIgnLFxyXG4gICAgICAgIHNlbGVjdFllYXI6ICfpgInmi6nlubTku70nLFxyXG4gICAgICAgIHByZXZZZWFyOiAn5LiK5LiA5bm0JyxcclxuICAgICAgICBuZXh0WWVhcjogJ+S4i+S4gOW5tCcsXHJcbiAgICAgICAgc2VsZWN0RGVjYWRlOiAn6YCJ5oup5bm05Lu95Yy66Ze0JyxcclxuICAgICAgICBzZWxlY3RUaW1lOifpgInmi6nml7bpl7QnLFxyXG4gICAgICAgIHByZXZEZWNhZGU6ICfkuIrkuIDljLrpl7QnLFxyXG4gICAgICAgIG5leHREZWNhZGU6ICfkuIvkuIDljLrpl7QnLFxyXG4gICAgICAgIHByZXZDZW50dXJ5OiAn5LiK5Liq5LiW57qqJyxcclxuICAgICAgICBuZXh0Q2VudHVyeTogJ+S4i+S4quS4lue6qidcclxuICAgIH07XHJcblxyXG4gICAgZnVuY3Rpb24gdHJhbnNPcHRpb24ob3B0aW9uKSB7XHJcbiAgICAgICAgaWYoIW9wdGlvbilyZXR1cm4ge307XHJcbiAgICAgICAgdmFyIG5ld29wdD17fTtcclxuICAgICAgICBmb3IodmFyIGkgaW4gb3B0aW9uKXtcclxuICAgICAgICAgICAgc3dpdGNoIChpKXtcclxuICAgICAgICAgICAgICAgIGNhc2UgJ3ZpZXdtb2RlJzpcclxuICAgICAgICAgICAgICAgICAgICBuZXdvcHRbJ3ZpZXdNb2RlJ109b3B0aW9uW2ldO1xyXG4gICAgICAgICAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgICAgICAgICAgY2FzZSAna2VlcG9wZW4nOlxyXG4gICAgICAgICAgICAgICAgICAgIG5ld29wdFsna2VlcE9wZW4nXT1vcHRpb25baV07XHJcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgICAgICBkZWZhdWx0OlxyXG4gICAgICAgICAgICAgICAgICAgIG5ld29wdFtpXT1vcHRpb25baV07XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9XHJcbiAgICAgICAgcmV0dXJuIG5ld29wdDtcclxuICAgIH1cclxuICAgICQoJy5kYXRlcGlja2VyJykuZWFjaChmdW5jdGlvbigpe1xyXG4gICAgICAgIHZhciBjb25maWc9JC5leHRlbmQoe1xyXG4gICAgICAgICAgICB0b29sdGlwczp0b29sdGlwcyxcclxuICAgICAgICAgICAgZm9ybWF0OiAnWVlZWS1NTS1ERCcsXHJcbiAgICAgICAgICAgIGxvY2FsZTogJ3poLWNuJyxcclxuICAgICAgICAgICAgc2hvd0NsZWFyOnRydWUsXHJcbiAgICAgICAgICAgIHNob3dUb2RheUJ1dHRvbjp0cnVlLFxyXG4gICAgICAgICAgICBzaG93Q2xvc2U6dHJ1ZSxcclxuICAgICAgICAgICAga2VlcEludmFsaWQ6dHJ1ZVxyXG4gICAgICAgIH0sdHJhbnNPcHRpb24oJCh0aGlzKS5kYXRhKCkpKTtcclxuXHJcbiAgICAgICAgJCh0aGlzKS5kYXRldGltZXBpY2tlcihjb25maWcpO1xyXG4gICAgfSk7XHJcblxyXG4gICAgJCgnLmRhdGUtcmFuZ2UnKS5lYWNoKGZ1bmN0aW9uICgpIHtcclxuICAgICAgICB2YXIgZnJvbSA9ICQodGhpcykuZmluZCgnW25hbWU9ZnJvbWRhdGVdLC5mcm9tZGF0ZScpLCB0byA9ICQodGhpcykuZmluZCgnW25hbWU9dG9kYXRlXSwudG9kYXRlJyk7XHJcbiAgICAgICAgdmFyIG9wdGlvbnMgPSAkLmV4dGVuZCh7XHJcbiAgICAgICAgICAgIHRvb2x0aXBzOnRvb2x0aXBzLFxyXG4gICAgICAgICAgICBmb3JtYXQ6ICdZWVlZLU1NLUREJyxcclxuICAgICAgICAgICAgbG9jYWxlOid6aC1jbicsXHJcbiAgICAgICAgICAgIHNob3dDbGVhcjp0cnVlLFxyXG4gICAgICAgICAgICBzaG93VG9kYXlCdXR0b246dHJ1ZSxcclxuICAgICAgICAgICAgc2hvd0Nsb3NlOnRydWUsXHJcbiAgICAgICAgICAgIGtlZXBJbnZhbGlkOnRydWVcclxuICAgICAgICB9LCQodGhpcykuZGF0YSgpKTtcclxuICAgICAgICBmcm9tLmRhdGV0aW1lcGlja2VyKG9wdGlvbnMpLm9uKCdkcC5jaGFuZ2UnLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIGlmIChmcm9tLnZhbCgpKSB7XHJcbiAgICAgICAgICAgICAgICB0by5kYXRhKCdEYXRlVGltZVBpY2tlcicpLm1pbkRhdGUoZnJvbS52YWwoKSk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KTtcclxuICAgICAgICB0by5kYXRldGltZXBpY2tlcihvcHRpb25zKS5vbignZHAuY2hhbmdlJywgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICBpZiAodG8udmFsKCkpIHtcclxuICAgICAgICAgICAgICAgIGZyb20uZGF0YSgnRGF0ZVRpbWVQaWNrZXInKS5tYXhEYXRlKHRvLnZhbCgpKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pO1xyXG4gICAgfSk7XHJcbn0iLCJmdW5jdGlvbiBzZXROYXYobmF2KSB7XHJcbiAgICB2YXIgaXRlbXM9JCgnLm1haW4tbmF2IC5uYXYtaXRlbScpO1xyXG4gICAgdmFyIGZpbmRlZD1mYWxzZTtcclxuICAgIGZvcih2YXIgaT0wO2k8aXRlbXMubGVuZ3RoO2krKyl7XHJcbiAgICAgICAgaWYoaXRlbXMuZXEoaSkuZGF0YSgnbW9kZWwnKT09PW5hdil7XHJcbiAgICAgICAgICAgIGl0ZW1zLmVxKGkpLmFkZENsYXNzKCdhY3RpdmUnKTtcclxuICAgICAgICAgICAgZmluZGVkPXRydWU7XHJcbiAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgIH1cclxuICAgIH1cclxuICAgIGlmKCFmaW5kZWQgJiYgbmF2LmluZGV4T2YoJy0nKT4wKXtcclxuICAgICAgICBuYXY9bmF2LnN1YnN0cigwLG5hdi5sYXN0SW5kZXhPZignLScpKTtcclxuICAgICAgICBzZXROYXYobmF2KTtcclxuICAgIH1cclxufVxyXG5cclxualF1ZXJ5KGZ1bmN0aW9uKCQpe1xyXG4gICAgaWYoJCh3aW5kb3cpLndpZHRoKCk+PTk5MSl7XHJcbiAgICAgICAgJCgnLm1haW4tbmF2Pi5kcm9wZG93bicpLmhvdmVyKFxyXG4gICAgICAgICAgICBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgICAgICAkKHRoaXMpLmZpbmQoJy5kcm9wZG93bi1tZW51Jykuc3RvcCh0cnVlLGZhbHNlKS5zbGlkZURvd24oKTtcclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAgICAgJCh0aGlzKS5maW5kKCcuZHJvcGRvd24tbWVudScpLnN0b3AodHJ1ZSxmYWxzZSkuc2xpZGVVcCgpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgKTtcclxuICAgIH1lbHNle1xyXG4gICAgICAgICQoJy5tYWluLW5hdj4uZHJvcGRvd24+LmRyb3Bkb3duLXRvZ2dsZScpLmNsaWNrKGZ1bmN0aW9uIChlKSB7XHJcbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcclxuICAgICAgICAgICAgZS5zdG9wUHJvcGFnYXRpb24oKTtcclxuICAgICAgICAgICAgdmFyIG9wZW5lZD0kKHRoaXMpLmRhdGEoJ29wZW5lZCcpO1xyXG4gICAgICAgICAgICB2YXIgcCA9ICQodGhpcykucGFyZW50cygnLmRyb3Bkb3duJyk7XHJcbiAgICAgICAgICAgIGlmKG9wZW5lZCl7XHJcbiAgICAgICAgICAgICAgICBwLmZpbmQoJy5kcm9wZG93bi1tZW51Jykuc3RvcCh0cnVlLCBmYWxzZSkuc2xpZGVVcCgpO1xyXG4gICAgICAgICAgICB9ZWxzZSB7XHJcbiAgICAgICAgICAgICAgICBwLnNpYmxpbmdzKCkuY2hpbGRyZW4oJy5kcm9wZG93bi1tZW51Jykuc3RvcCh0cnVlLCBmYWxzZSkuc2xpZGVVcCgpO1xyXG4gICAgICAgICAgICAgICAgcC5zaWJsaW5ncygpLmNoaWxkcmVuKCcuZHJvcGRvd24tdG9nZ2xlJykuZGF0YSgnb3BlbmVkJyxmYWxzZSk7XHJcbiAgICAgICAgICAgICAgICBwLmZpbmQoJy5kcm9wZG93bi1tZW51Jykuc3RvcCh0cnVlLCBmYWxzZSkuc2xpZGVEb3duKCk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgJCh0aGlzKS5kYXRhKCdvcGVuZWQnLCFvcGVuZWQpO1xyXG4gICAgICAgIH0pXHJcbiAgICB9XHJcbn0pOyJdfQ==
