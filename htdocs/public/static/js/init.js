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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNvbW1vbi5qcyIsInRlbXBsYXRlLmpzIiwiZGlhbG9nLmpzIiwianF1ZXJ5LnRhZy5qcyIsImRhdGV0aW1lLmluaXQuanMiLCJmcm9udC5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUNqRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUM1R0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUMxYUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUMxQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ3pFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSIsImZpbGUiOiJmcm9udC5qcyIsInNvdXJjZXNDb250ZW50IjpbImZ1bmN0aW9uIGRlbChvYmosbXNnKSB7XHJcbiAgICBkaWFsb2cuY29uZmlybShtc2csZnVuY3Rpb24oKXtcclxuICAgICAgICBsb2NhdGlvbi5ocmVmPSQob2JqKS5hdHRyKCdocmVmJyk7XHJcbiAgICB9KTtcclxuICAgIHJldHVybiBmYWxzZTtcclxufVxyXG5cclxuZnVuY3Rpb24gbGFuZyhrZXkpIHtcclxuICAgIGlmKHdpbmRvdy5sYW5ndWFnZSAmJiB3aW5kb3cubGFuZ3VhZ2Vba2V5XSl7XHJcbiAgICAgICAgcmV0dXJuIHdpbmRvdy5sYW5ndWFnZVtrZXldO1xyXG4gICAgfVxyXG4gICAgcmV0dXJuIGtleTtcclxufVxyXG5cclxuZnVuY3Rpb24gcmFuZG9tU3RyaW5nKGxlbiwgY2hhclNldCkge1xyXG4gICAgY2hhclNldCA9IGNoYXJTZXQgfHwgJ0FCQ0RFRkdISUpLTE1OT1BRUlNUVVZXWFlaYWJjZGVmZ2hpamtsbW5vcHFyc3R1dnd4eXowMTIzNDU2Nzg5JztcclxuICAgIHZhciBzdHIgPSAnJyxhbGxMZW49Y2hhclNldC5sZW5ndGg7XHJcbiAgICBmb3IgKHZhciBpID0gMDsgaSA8IGxlbjsgaSsrKSB7XHJcbiAgICAgICAgdmFyIHJhbmRvbVBveiA9IE1hdGguZmxvb3IoTWF0aC5yYW5kb20oKSAqIGFsbExlbik7XHJcbiAgICAgICAgc3RyICs9IGNoYXJTZXQuc3Vic3RyaW5nKHJhbmRvbVBveixyYW5kb21Qb3orMSk7XHJcbiAgICB9XHJcbiAgICByZXR1cm4gc3RyO1xyXG59XHJcblxyXG5mdW5jdGlvbiBjb3B5X29iaihhcnIpe1xyXG4gICAgcmV0dXJuIEpTT04ucGFyc2UoSlNPTi5zdHJpbmdpZnkoYXJyKSk7XHJcbn1cclxuXHJcbmZ1bmN0aW9uIGlzT2JqZWN0VmFsdWVFcXVhbChhLCBiKSB7XHJcbiAgICBpZighYSAmJiAhYilyZXR1cm4gdHJ1ZTtcclxuICAgIGlmKCFhIHx8ICFiKXJldHVybiBmYWxzZTtcclxuXHJcbiAgICAvLyBPZiBjb3Vyc2UsIHdlIGNhbiBkbyBpdCB1c2UgZm9yIGluXHJcbiAgICAvLyBDcmVhdGUgYXJyYXlzIG9mIHByb3BlcnR5IG5hbWVzXHJcbiAgICB2YXIgYVByb3BzID0gT2JqZWN0LmdldE93blByb3BlcnR5TmFtZXMoYSk7XHJcbiAgICB2YXIgYlByb3BzID0gT2JqZWN0LmdldE93blByb3BlcnR5TmFtZXMoYik7XHJcblxyXG4gICAgLy8gSWYgbnVtYmVyIG9mIHByb3BlcnRpZXMgaXMgZGlmZmVyZW50LFxyXG4gICAgLy8gb2JqZWN0cyBhcmUgbm90IGVxdWl2YWxlbnRcclxuICAgIGlmIChhUHJvcHMubGVuZ3RoICE9IGJQcm9wcy5sZW5ndGgpIHtcclxuICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICB9XHJcblxyXG4gICAgZm9yICh2YXIgaSA9IDA7IGkgPCBhUHJvcHMubGVuZ3RoOyBpKyspIHtcclxuICAgICAgICB2YXIgcHJvcE5hbWUgPSBhUHJvcHNbaV07XHJcblxyXG4gICAgICAgIC8vIElmIHZhbHVlcyBvZiBzYW1lIHByb3BlcnR5IGFyZSBub3QgZXF1YWwsXHJcbiAgICAgICAgLy8gb2JqZWN0cyBhcmUgbm90IGVxdWl2YWxlbnRcclxuICAgICAgICBpZiAoYVtwcm9wTmFtZV0gIT09IGJbcHJvcE5hbWVdKSB7XHJcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICB9XHJcbiAgICB9XHJcblxyXG4gICAgLy8gSWYgd2UgbWFkZSBpdCB0aGlzIGZhciwgb2JqZWN0c1xyXG4gICAgLy8gYXJlIGNvbnNpZGVyZWQgZXF1aXZhbGVudFxyXG4gICAgcmV0dXJuIHRydWU7XHJcbn1cclxuXHJcbmZ1bmN0aW9uIGFycmF5X2NvbWJpbmUoYSxiKSB7XHJcbiAgICB2YXIgb2JqPXt9O1xyXG4gICAgZm9yKHZhciBpPTA7aTxhLmxlbmd0aDtpKyspe1xyXG4gICAgICAgIGlmKGIubGVuZ3RoPGkrMSlicmVhaztcclxuICAgICAgICBvYmpbYVtpXV09YltpXTtcclxuICAgIH1cclxuICAgIHJldHVybiBvYmo7XHJcbn0iLCJcclxuTnVtYmVyLnByb3RvdHlwZS5mb3JtYXQ9ZnVuY3Rpb24oZml4KXtcclxuICAgIGlmKGZpeD09PXVuZGVmaW5lZClmaXg9MjtcclxuICAgIHZhciBudW09dGhpcy50b0ZpeGVkKGZpeCk7XHJcbiAgICB2YXIgej1udW0uc3BsaXQoJy4nKTtcclxuICAgIHZhciBmb3JtYXQ9W10sZj16WzBdLnNwbGl0KCcnKSxsPWYubGVuZ3RoO1xyXG4gICAgZm9yKHZhciBpPTA7aTxsO2krKyl7XHJcbiAgICAgICAgaWYoaT4wICYmIGkgJSAzPT0wKXtcclxuICAgICAgICAgICAgZm9ybWF0LnVuc2hpZnQoJywnKTtcclxuICAgICAgICB9XHJcbiAgICAgICAgZm9ybWF0LnVuc2hpZnQoZltsLWktMV0pO1xyXG4gICAgfVxyXG4gICAgcmV0dXJuIGZvcm1hdC5qb2luKCcnKSsoei5sZW5ndGg9PTI/Jy4nK3pbMV06JycpO1xyXG59O1xyXG5pZighU3RyaW5nLnByb3RvdHlwZS50cmltKXtcclxuICAgIFN0cmluZy5wcm90b3R5cGUudHJpbT1mdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgcmV0dXJuIHRoaXMucmVwbGFjZSgvKF5cXHMrfFxccyskKS9nLCcnKTtcclxuICAgIH1cclxufVxyXG5TdHJpbmcucHJvdG90eXBlLmNvbXBpbGU9ZnVuY3Rpb24oZGF0YSxsaXN0KXtcclxuXHJcbiAgICBpZihsaXN0KXtcclxuICAgICAgICB2YXIgdGVtcHM9W107XHJcbiAgICAgICAgZm9yKHZhciBpIGluIGRhdGEpe1xyXG4gICAgICAgICAgICB0ZW1wcy5wdXNoKHRoaXMuY29tcGlsZShkYXRhW2ldKSk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHJldHVybiB0ZW1wcy5qb2luKFwiXFxuXCIpO1xyXG4gICAgfWVsc2V7XHJcbiAgICAgICAgcmV0dXJuIHRoaXMucmVwbGFjZSgvXFx7aWZcXHMrKFteXFx9XSspXFx9KFtcXFdcXHddKil7XFwvaWZ9L2csZnVuY3Rpb24oYWxsLCBjb25kaXRpb24sIGNvbnQpe1xyXG4gICAgICAgICAgICB2YXIgb3BlcmF0aW9uO1xyXG4gICAgICAgICAgICBpZihvcGVyYXRpb249Y29uZGl0aW9uLm1hdGNoKC9cXHMrKD0rfDx8PilcXHMrLykpe1xyXG4gICAgICAgICAgICAgICAgb3BlcmF0aW9uPW9wZXJhdGlvblswXTtcclxuICAgICAgICAgICAgICAgIHZhciBwYXJ0PWNvbmRpdGlvbi5zcGxpdChvcGVyYXRpb24pO1xyXG4gICAgICAgICAgICAgICAgaWYocGFydFswXS5pbmRleE9mKCdAJyk9PT0wKXtcclxuICAgICAgICAgICAgICAgICAgICBwYXJ0WzBdPWRhdGFbcGFydFswXS5yZXBsYWNlKCdAJywnJyldO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgaWYocGFydFsxXS5pbmRleE9mKCdAJyk9PT0wKXtcclxuICAgICAgICAgICAgICAgICAgICBwYXJ0WzFdPWRhdGFbcGFydFsxXS5yZXBsYWNlKCdAJywnJyldO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgb3BlcmF0aW9uPW9wZXJhdGlvbi50cmltKCk7XHJcbiAgICAgICAgICAgICAgICB2YXIgcmVzdWx0PWZhbHNlO1xyXG4gICAgICAgICAgICAgICAgc3dpdGNoIChvcGVyYXRpb24pe1xyXG4gICAgICAgICAgICAgICAgICAgIGNhc2UgJz09JzpcclxuICAgICAgICAgICAgICAgICAgICAgICAgcmVzdWx0ID0gcGFydFswXSA9PSBwYXJ0WzFdO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgICAgICAgICBjYXNlICc9PT0nOlxyXG4gICAgICAgICAgICAgICAgICAgICAgICByZXN1bHQgPSBwYXJ0WzBdID09PSBwYXJ0WzFdO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgICAgICAgICBjYXNlICc+JzpcclxuICAgICAgICAgICAgICAgICAgICAgICAgcmVzdWx0ID0gcGFydFswXSA+IHBhcnRbMV07XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgICAgICAgICAgICAgIGNhc2UgJzwnOlxyXG4gICAgICAgICAgICAgICAgICAgICAgICByZXN1bHQgPSBwYXJ0WzBdIDwgcGFydFsxXTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICBpZihyZXN1bHQpe1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBjb250O1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9ZWxzZSB7XHJcbiAgICAgICAgICAgICAgICBpZiAoZGF0YVtjb25kaXRpb24ucmVwbGFjZSgnQCcsJycpXSkgcmV0dXJuIGNvbnQ7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgcmV0dXJuICcnO1xyXG4gICAgICAgIH0pLnJlcGxhY2UoL1xce0AoW1xcd1xcZFxcLl0rKSg/OlxcfChbXFx3XFxkXSspKD86XFxzKj1cXHMqKFtcXHdcXGQsXFxzI10rKSk/KT9cXH0vZyxmdW5jdGlvbihhbGwsbTEsZnVuYyxhcmdzKXtcclxuXHJcbiAgICAgICAgICAgIGlmKG0xLmluZGV4T2YoJy4nKT4wKXtcclxuICAgICAgICAgICAgICAgIHZhciBrZXlzPW0xLnNwbGl0KCcuJyksdmFsPWRhdGE7XHJcbiAgICAgICAgICAgICAgICBmb3IodmFyIGk9MDtpPGtleXMubGVuZ3RoO2krKyl7XHJcbiAgICAgICAgICAgICAgICAgICAgaWYodmFsW2tleXNbaV1dIT09dW5kZWZpbmVkICYmIHZhbFtrZXlzW2ldXSE9PW51bGwpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB2YWw9dmFsW2tleXNbaV1dO1xyXG4gICAgICAgICAgICAgICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB2YWwgPSAnJztcclxuICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgcmV0dXJuIGNhbGxmdW5jKHZhbCxmdW5jLGFyZ3MpO1xyXG4gICAgICAgICAgICB9ZWxzZXtcclxuICAgICAgICAgICAgICAgIHJldHVybiBjYWxsZnVuYyhkYXRhW20xXSxmdW5jLGFyZ3MsZGF0YSk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KTtcclxuICAgIH1cclxufTtcclxuXHJcbmZ1bmN0aW9uIHRvc3RyaW5nKG9iaikge1xyXG4gICAgaWYob2JqICYmIG9iai50b1N0cmluZyl7XHJcbiAgICAgICAgcmV0dXJuIG9iai50b1N0cmluZygpO1xyXG4gICAgfVxyXG4gICAgcmV0dXJuICcnO1xyXG59XHJcblxyXG5mdW5jdGlvbiBjYWxsZnVuYyh2YWwsZnVuYyxhcmdzLHRoaXNvYmope1xyXG4gICAgaWYoIWFyZ3Mpe1xyXG4gICAgICAgIGFyZ3M9W3ZhbF07XHJcbiAgICB9ZWxzZXtcclxuICAgICAgICBpZih0eXBlb2YgYXJncz09PSdzdHJpbmcnKWFyZ3M9YXJncy5zcGxpdCgnLCcpO1xyXG4gICAgICAgIHZhciBhcmdpZHg9YXJncy5pbmRleE9mKCcjIyMnKTtcclxuICAgICAgICBpZihhcmdpZHg+PTApe1xyXG4gICAgICAgICAgICBhcmdzW2FyZ2lkeF09dmFsO1xyXG4gICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICBhcmdzPVt2YWxdLmNvbmNhdChhcmdzKTtcclxuICAgICAgICB9XHJcbiAgICB9XHJcblxyXG4gICAgcmV0dXJuIHdpbmRvd1tmdW5jXT93aW5kb3dbZnVuY10uYXBwbHkodGhpc29iaixhcmdzKTooKHZhbD09PXVuZGVmaW5lZHx8dmFsPT09bnVsbCk/Jyc6dmFsKTtcclxufVxyXG5cclxuZnVuY3Rpb24gaWlmKHYsbTEsbTIpe1xyXG4gICAgaWYodj09PScwJyl2PTA7XHJcbiAgICByZXR1cm4gdj9tMTptMjtcclxufSIsIlxyXG52YXIgZGlhbG9nVHBsPSc8ZGl2IGNsYXNzPVwibW9kYWwgZmFkZVwiIGlkPVwie0BpZH1cIiB0YWJpbmRleD1cIi0xXCIgcm9sZT1cImRpYWxvZ1wiIGFyaWEtbGFiZWxsZWRieT1cIntAaWR9TGFiZWxcIiBhcmlhLWhpZGRlbj1cInRydWVcIj5cXG4nICtcclxuICAgICcgICAgPGRpdiBjbGFzcz1cIm1vZGFsLWRpYWxvZ1wiPlxcbicgK1xyXG4gICAgJyAgICAgICAgPGRpdiBjbGFzcz1cIm1vZGFsLWNvbnRlbnRcIj5cXG4nICtcclxuICAgICcgICAgICAgICAgICA8ZGl2IGNsYXNzPVwibW9kYWwtaGVhZGVyXCI+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgICAgIDxoNCBjbGFzcz1cIm1vZGFsLXRpdGxlXCIgaWQ9XCJ7QGlkfUxhYmVsXCI+PC9oND5cXG4nICtcclxuICAgICcgICAgICAgICAgICAgICAgPGJ1dHRvbiB0eXBlPVwiYnV0dG9uXCIgY2xhc3M9XCJjbG9zZVwiIGRhdGEtZGlzbWlzcz1cIm1vZGFsXCI+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgICAgICAgICA8c3BhbiBhcmlhLWhpZGRlbj1cInRydWVcIj4mdGltZXM7PC9zcGFuPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgICAgICAgICAgPHNwYW4gY2xhc3M9XCJzci1vbmx5XCI+Q2xvc2U8L3NwYW4+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgICAgIDwvYnV0dG9uPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgIDwvZGl2PlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJtb2RhbC1ib2R5XCI+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgPC9kaXY+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgPGRpdiBjbGFzcz1cIm1vZGFsLWZvb3RlclwiPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgICAgICA8bmF2IGNsYXNzPVwibmF2IG5hdi1maWxsXCI+PC9uYXY+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgPC9kaXY+XFxuJyArXHJcbiAgICAnICAgICAgICA8L2Rpdj5cXG4nICtcclxuICAgICcgICAgPC9kaXY+XFxuJyArXHJcbiAgICAnPC9kaXY+JztcclxudmFyIGRpYWxvZ0lkeD0wO1xyXG5mdW5jdGlvbiBEaWFsb2cob3B0cyl7XHJcbiAgICBpZighb3B0cylvcHRzPXt9O1xyXG4gICAgLy/lpITnkIbmjInpkq5cclxuICAgIGlmKG9wdHMuYnRucyE9PXVuZGVmaW5lZCkge1xyXG4gICAgICAgIGlmICh0eXBlb2Yob3B0cy5idG5zKSA9PSAnc3RyaW5nJykge1xyXG4gICAgICAgICAgICBvcHRzLmJ0bnMgPSBbb3B0cy5idG5zXTtcclxuICAgICAgICB9XHJcbiAgICAgICAgLy92YXIgZGZ0PS0xO1xyXG4gICAgICAgIGZvciAodmFyIGkgPSAwOyBpIDwgb3B0cy5idG5zLmxlbmd0aDsgaSsrKSB7XHJcbiAgICAgICAgICAgIGlmKHR5cGVvZihvcHRzLmJ0bnNbaV0pPT0nc3RyaW5nJyl7XHJcbiAgICAgICAgICAgICAgICBvcHRzLmJ0bnNbaV09eyd0ZXh0JzpvcHRzLmJ0bnNbaV19O1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIGlmKG9wdHMuYnRuc1tpXS5pc2RlZmF1bHQpe1xyXG4gICAgICAgICAgICAgICAgb3B0cy5kZWZhdWx0QnRuPWk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9XHJcbiAgICAgICAgaWYob3B0cy5kZWZhdWx0QnRuPT09dW5kZWZpbmVkKXtcclxuICAgICAgICAgICAgb3B0cy5kZWZhdWx0QnRuPW9wdHMuYnRucy5sZW5ndGgtMTtcclxuICAgICAgICAgICAgb3B0cy5idG5zW29wdHMuZGVmYXVsdEJ0bl0uaXNkZWZhdWx0PXRydWU7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICBpZihvcHRzLmJ0bnNbb3B0cy5kZWZhdWx0QnRuXSAmJiAhb3B0cy5idG5zW29wdHMuZGVmYXVsdEJ0bl1bJ3R5cGUnXSl7XHJcbiAgICAgICAgICAgIG9wdHMuYnRuc1tvcHRzLmRlZmF1bHRCdG5dWyd0eXBlJ109J3ByaW1hcnknO1xyXG4gICAgICAgIH1cclxuICAgIH1cclxuXHJcbiAgICB0aGlzLm9wdGlvbnM9JC5leHRlbmQoe1xyXG4gICAgICAgICdpZCc6J2RsZ01vZGFsJytkaWFsb2dJZHgrKyxcclxuICAgICAgICAnc2l6ZSc6JycsXHJcbiAgICAgICAgJ2J0bnMnOltcclxuICAgICAgICAgICAgeyd0ZXh0Jzon5Y+W5raIJywndHlwZSc6J3NlY29uZGFyeSd9LFxyXG4gICAgICAgICAgICB7J3RleHQnOifnoa7lrponLCdpc2RlZmF1bHQnOnRydWUsJ3R5cGUnOidwcmltYXJ5J31cclxuICAgICAgICBdLFxyXG4gICAgICAgICdkZWZhdWx0QnRuJzoxLFxyXG4gICAgICAgICdvbnN1cmUnOm51bGwsXHJcbiAgICAgICAgJ29uc2hvdyc6bnVsbCxcclxuICAgICAgICAnb25zaG93bic6bnVsbCxcclxuICAgICAgICAnb25oaWRlJzpudWxsLFxyXG4gICAgICAgICdvbmhpZGRlbic6bnVsbFxyXG4gICAgfSxvcHRzKTtcclxuXHJcbiAgICB0aGlzLmJveD0kKHRoaXMub3B0aW9ucy5pZCk7XHJcbn1cclxuRGlhbG9nLnByb3RvdHlwZS5nZW5lckJ0bj1mdW5jdGlvbihvcHQsaWR4KXtcclxuICAgIGlmKG9wdFsndHlwZSddKW9wdFsnY2xhc3MnXT0nYnRuLW91dGxpbmUtJytvcHRbJ3R5cGUnXTtcclxuICAgIHJldHVybiAnPGEgaHJlZj1cImphdmFzY3JpcHQ6XCIgY2xhc3M9XCJuYXYtaXRlbSBidG4gJysob3B0WydjbGFzcyddP29wdFsnY2xhc3MnXTonYnRuLW91dGxpbmUtc2Vjb25kYXJ5JykrJ1wiIGRhdGEtaW5kZXg9XCInK2lkeCsnXCI+JytvcHQudGV4dCsnPC9hPic7XHJcbn07XHJcbkRpYWxvZy5wcm90b3R5cGUuc2hvdz1mdW5jdGlvbihodG1sLHRpdGxlKXtcclxuICAgIHRoaXMuYm94PSQoJyMnK3RoaXMub3B0aW9ucy5pZCk7XHJcbiAgICBpZighdGl0bGUpdGl0bGU9J+ezu+e7n+aPkOekuic7XHJcbiAgICBpZih0aGlzLmJveC5sZW5ndGg8MSkge1xyXG4gICAgICAgICQoZG9jdW1lbnQuYm9keSkuYXBwZW5kKGRpYWxvZ1RwbC5yZXBsYWNlKCdtb2RhbC1ib2R5JywnbW9kYWwtYm9keScrKHRoaXMub3B0aW9ucy5ib2R5Q2xhc3M/KCcgJyt0aGlzLm9wdGlvbnMuYm9keUNsYXNzKTonJykpLmNvbXBpbGUoeydpZCc6IHRoaXMub3B0aW9ucy5pZH0pKTtcclxuICAgICAgICB0aGlzLmJveD0kKCcjJyt0aGlzLm9wdGlvbnMuaWQpO1xyXG4gICAgfWVsc2V7XHJcbiAgICAgICAgdGhpcy5ib3gudW5iaW5kKCk7XHJcbiAgICB9XHJcblxyXG4gICAgLy90aGlzLmJveC5maW5kKCcubW9kYWwtZm9vdGVyIC5idG4tcHJpbWFyeScpLnVuYmluZCgpO1xyXG4gICAgdmFyIHNlbGY9dGhpcztcclxuICAgIERpYWxvZy5pbnN0YW5jZT1zZWxmO1xyXG5cclxuICAgIC8v55Sf5oiQ5oyJ6ZKuXHJcbiAgICB2YXIgYnRucz1bXTtcclxuICAgIGZvcih2YXIgaT0wO2k8dGhpcy5vcHRpb25zLmJ0bnMubGVuZ3RoO2krKyl7XHJcbiAgICAgICAgYnRucy5wdXNoKHRoaXMuZ2VuZXJCdG4odGhpcy5vcHRpb25zLmJ0bnNbaV0saSkpO1xyXG4gICAgfVxyXG4gICAgdGhpcy5ib3guZmluZCgnLm1vZGFsLWZvb3RlciAubmF2JykuaHRtbChidG5zLmpvaW4oJ1xcbicpKTtcclxuXHJcbiAgICB2YXIgZGlhbG9nPXRoaXMuYm94LmZpbmQoJy5tb2RhbC1kaWFsb2cnKTtcclxuICAgIGRpYWxvZy5yZW1vdmVDbGFzcygnbW9kYWwtc20nKS5yZW1vdmVDbGFzcygnbW9kYWwtbGcnKTtcclxuICAgIGlmKHRoaXMub3B0aW9ucy5zaXplPT0nc20nKSB7XHJcbiAgICAgICAgZGlhbG9nLmFkZENsYXNzKCdtb2RhbC1zbScpO1xyXG4gICAgfWVsc2UgaWYodGhpcy5vcHRpb25zLnNpemU9PSdsZycpIHtcclxuICAgICAgICBkaWFsb2cuYWRkQ2xhc3MoJ21vZGFsLWxnJyk7XHJcbiAgICB9XHJcbiAgICB0aGlzLmJveC5maW5kKCcubW9kYWwtdGl0bGUnKS50ZXh0KHRpdGxlKTtcclxuXHJcbiAgICB2YXIgYm9keT10aGlzLmJveC5maW5kKCcubW9kYWwtYm9keScpO1xyXG4gICAgYm9keS5odG1sKGh0bWwpO1xyXG4gICAgdGhpcy5ib3gub24oJ2hpZGUuYnMubW9kYWwnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgaWYoc2VsZi5vcHRpb25zLm9uaGlkZSl7XHJcbiAgICAgICAgICAgIHNlbGYub3B0aW9ucy5vbmhpZGUoYm9keSxzZWxmLmJveCk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIERpYWxvZy5pbnN0YW5jZT1udWxsO1xyXG4gICAgfSk7XHJcbiAgICB0aGlzLmJveC5vbignaGlkZGVuLmJzLm1vZGFsJyxmdW5jdGlvbigpe1xyXG4gICAgICAgIGlmKHNlbGYub3B0aW9ucy5vbmhpZGRlbil7XHJcbiAgICAgICAgICAgIHNlbGYub3B0aW9ucy5vbmhpZGRlbihib2R5LHNlbGYuYm94KTtcclxuICAgICAgICB9XHJcbiAgICAgICAgc2VsZi5ib3gucmVtb3ZlKCk7XHJcbiAgICB9KTtcclxuICAgIHRoaXMuYm94Lm9uKCdzaG93LmJzLm1vZGFsJyxmdW5jdGlvbigpe1xyXG4gICAgICAgIGlmKHNlbGYub3B0aW9ucy5vbnNob3cpe1xyXG4gICAgICAgICAgICBzZWxmLm9wdGlvbnMub25zaG93KGJvZHksc2VsZi5ib3gpO1xyXG4gICAgICAgIH1cclxuICAgIH0pO1xyXG4gICAgdGhpcy5ib3gub24oJ3Nob3duLmJzLm1vZGFsJyxmdW5jdGlvbigpe1xyXG4gICAgICAgIGlmKHNlbGYub3B0aW9ucy5vbnNob3duKXtcclxuICAgICAgICAgICAgc2VsZi5vcHRpb25zLm9uc2hvd24oYm9keSxzZWxmLmJveCk7XHJcbiAgICAgICAgfVxyXG4gICAgfSk7XHJcbiAgICB0aGlzLmJveC5maW5kKCcubW9kYWwtZm9vdGVyIC5idG4nKS5jbGljayhmdW5jdGlvbigpe1xyXG4gICAgICAgIHZhciByZXN1bHQ9dHJ1ZSxpZHg9JCh0aGlzKS5kYXRhKCdpbmRleCcpO1xyXG4gICAgICAgIGlmKHNlbGYub3B0aW9ucy5idG5zW2lkeF0uY2xpY2spe1xyXG4gICAgICAgICAgICByZXN1bHQgPSBzZWxmLm9wdGlvbnMuYnRuc1tpZHhdLmNsaWNrLmFwcGx5KHRoaXMsW2JvZHksIHNlbGYuYm94XSk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIGlmKGlkeD09c2VsZi5vcHRpb25zLmRlZmF1bHRCdG4pIHtcclxuICAgICAgICAgICAgaWYgKHNlbGYub3B0aW9ucy5vbnN1cmUpIHtcclxuICAgICAgICAgICAgICAgIHJlc3VsdCA9IHNlbGYub3B0aW9ucy5vbnN1cmUuYXBwbHkodGhpcyxbYm9keSwgc2VsZi5ib3hdKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH1cclxuICAgICAgICBpZihyZXN1bHQhPT1mYWxzZSl7XHJcbiAgICAgICAgICAgIHNlbGYuYm94Lm1vZGFsKCdoaWRlJyk7XHJcbiAgICAgICAgfVxyXG4gICAgfSk7XHJcbiAgICB0aGlzLmJveC5tb2RhbCgnc2hvdycpO1xyXG4gICAgcmV0dXJuIHRoaXM7XHJcbn07XHJcbkRpYWxvZy5wcm90b3R5cGUuaGlkZT1EaWFsb2cucHJvdG90eXBlLmNsb3NlPWZ1bmN0aW9uKCl7XHJcbiAgICB0aGlzLmJveC5tb2RhbCgnaGlkZScpO1xyXG4gICAgcmV0dXJuIHRoaXM7XHJcbn07XHJcblxyXG52YXIgZGlhbG9nPXtcclxuICAgIGFsZXJ0OmZ1bmN0aW9uKG1lc3NhZ2UsY2FsbGJhY2ssdGl0bGUpe1xyXG4gICAgICAgIHZhciBjYWxsZWQ9ZmFsc2U7XHJcbiAgICAgICAgdmFyIGlzY2FsbGJhY2s9dHlwZW9mIGNhbGxiYWNrPT0nZnVuY3Rpb24nO1xyXG4gICAgICAgIHJldHVybiBuZXcgRGlhbG9nKHtcclxuICAgICAgICAgICAgYnRuczon56Gu5a6aJyxcclxuICAgICAgICAgICAgb25zdXJlOmZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgICAgICBpZihpc2NhbGxiYWNrKXtcclxuICAgICAgICAgICAgICAgICAgICBjYWxsZWQ9dHJ1ZTtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gY2FsbGJhY2sodHJ1ZSk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgIG9uaGlkZTpmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICAgICAgaWYoIWNhbGxlZCAmJiBpc2NhbGxiYWNrKXtcclxuICAgICAgICAgICAgICAgICAgICBjYWxsYmFjayhmYWxzZSk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS5zaG93KG1lc3NhZ2UsdGl0bGUpO1xyXG4gICAgfSxcclxuICAgIGNvbmZpcm06ZnVuY3Rpb24obWVzc2FnZSxjb25maXJtLGNhbmNlbCl7XHJcbiAgICAgICAgdmFyIGNhbGxlZD1mYWxzZTtcclxuICAgICAgICByZXR1cm4gbmV3IERpYWxvZyh7XHJcbiAgICAgICAgICAgICdvbnN1cmUnOmZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgICAgICBpZih0eXBlb2YgY29uZmlybT09J2Z1bmN0aW9uJyl7XHJcbiAgICAgICAgICAgICAgICAgICAgY2FsbGVkPXRydWU7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGNvbmZpcm0oKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgJ29uaGlkZSc6ZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAgICAgaWYoY2FsbGVkPWZhbHNlICYmIHR5cGVvZiBjYW5jZWw9PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBjYW5jZWwoKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLnNob3cobWVzc2FnZSk7XHJcbiAgICB9LFxyXG4gICAgcHJvbXB0OmZ1bmN0aW9uKG1lc3NhZ2UsY2FsbGJhY2ssY2FuY2VsKXtcclxuICAgICAgICB2YXIgY2FsbGVkPWZhbHNlO1xyXG4gICAgICAgIHZhciBjb250ZW50SHRtbD0nPGRpdiBjbGFzcz1cImZvcm0tZ3JvdXBcIj57QGlucHV0fTwvZGl2Pic7XHJcbiAgICAgICAgdmFyIHRpdGxlPSfor7fovpPlhaXkv6Hmga8nO1xyXG4gICAgICAgIGlmKHR5cGVvZiBtZXNzYWdlPT0nc3RyaW5nJyl7XHJcbiAgICAgICAgICAgIHRpdGxlPW1lc3NhZ2U7XHJcbiAgICAgICAgfWVsc2V7XHJcbiAgICAgICAgICAgIHRpdGxlPW1lc3NhZ2UudGl0bGU7XHJcbiAgICAgICAgICAgIGlmKG1lc3NhZ2UuY29udGVudCkge1xyXG4gICAgICAgICAgICAgICAgY29udGVudEh0bWwgPSBtZXNzYWdlLmNvbnRlbnQuaW5kZXhPZigne0BpbnB1dH0nKSA+IC0xID8gbWVzc2FnZS5jb250ZW50IDogbWVzc2FnZS5jb250ZW50ICsgY29udGVudEh0bWw7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9XHJcbiAgICAgICAgcmV0dXJuIG5ldyBEaWFsb2coe1xyXG4gICAgICAgICAgICAnb25zaG93JzpmdW5jdGlvbihib2R5KXtcclxuICAgICAgICAgICAgICAgIGlmKG1lc3NhZ2UgJiYgbWVzc2FnZS5vbnNob3cpe1xyXG4gICAgICAgICAgICAgICAgICAgIG1lc3NhZ2Uub25zaG93KGJvZHkpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAnb25zaG93bic6ZnVuY3Rpb24oYm9keSl7XHJcbiAgICAgICAgICAgICAgICBib2R5LmZpbmQoJ1tuYW1lPWNvbmZpcm1faW5wdXRdJykuZm9jdXMoKTtcclxuICAgICAgICAgICAgICAgIGlmKG1lc3NhZ2UgJiYgbWVzc2FnZS5vbnNob3duKXtcclxuICAgICAgICAgICAgICAgICAgICBtZXNzYWdlLm9uc2hvd24oYm9keSk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdvbnN1cmUnOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgdmFyIHZhbD1ib2R5LmZpbmQoJ1tuYW1lPWNvbmZpcm1faW5wdXRdJykudmFsKCk7XHJcbiAgICAgICAgICAgICAgICBpZih0eXBlb2YgY2FsbGJhY2s9PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgICAgIHZhciByZXN1bHQgPSBjYWxsYmFjayh2YWwpO1xyXG4gICAgICAgICAgICAgICAgICAgIGlmKHJlc3VsdD09PXRydWUpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBjYWxsZWQ9dHJ1ZTtcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHJlc3VsdDtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgJ29uaGlkZSc6ZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAgICAgaWYoY2FsbGVkPWZhbHNlICYmIHR5cGVvZiBjYW5jZWw9PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBjYW5jZWwoKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLnNob3coY29udGVudEh0bWwuY29tcGlsZSh7aW5wdXQ6JzxpbnB1dCB0eXBlPVwidGV4dFwiIG5hbWU9XCJjb25maXJtX2lucHV0XCIgY2xhc3M9XCJmb3JtLWNvbnRyb2xcIiAvPid9KSx0aXRsZSk7XHJcbiAgICB9LFxyXG4gICAgYWN0aW9uOmZ1bmN0aW9uIChsaXN0LGNhbGxiYWNrLHRpdGxlKSB7XHJcbiAgICAgICAgdmFyIGh0bWw9JzxkaXYgY2xhc3M9XCJsaXN0LWdyb3VwXCI+PGEgaHJlZj1cImphdmFzY3JpcHQ6XCIgY2xhc3M9XCJsaXN0LWdyb3VwLWl0ZW0gbGlzdC1ncm91cC1pdGVtLWFjdGlvblwiPicrbGlzdC5qb2luKCc8L2E+PGEgaHJlZj1cImphdmFzY3JpcHQ6XCIgY2xhc3M9XCJsaXN0LWdyb3VwLWl0ZW0gbGlzdC1ncm91cC1pdGVtLWFjdGlvblwiPicpKyc8L2E+PC9kaXY+JztcclxuICAgICAgICB2YXIgYWN0aW9ucz1udWxsO1xyXG4gICAgICAgIHZhciBkbGc9bmV3IERpYWxvZyh7XHJcbiAgICAgICAgICAgICdib2R5Q2xhc3MnOidtb2RhbC1hY3Rpb24nLFxyXG4gICAgICAgICAgICAnYnRucyc6W1xyXG4gICAgICAgICAgICAgICAgeyd0ZXh0Jzon5Y+W5raIJywndHlwZSc6J3NlY29uZGFyeSd9XHJcbiAgICAgICAgICAgIF0sXHJcbiAgICAgICAgICAgICdvbnNob3cnOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgYWN0aW9ucz1ib2R5LmZpbmQoJy5saXN0LWdyb3VwLWl0ZW0tYWN0aW9uJyk7XHJcbiAgICAgICAgICAgICAgICBhY3Rpb25zLmNsaWNrKGZ1bmN0aW9uIChlKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgYWN0aW9ucy5yZW1vdmVDbGFzcygnYWN0aXZlJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5hZGRDbGFzcygnYWN0aXZlJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgdmFyIHZhbD1hY3Rpb25zLmluZGV4KHRoaXMpO1xyXG4gICAgICAgICAgICAgICAgICAgIGlmKHR5cGVvZiBjYWxsYmFjaz09J2Z1bmN0aW9uJyl7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmKCBjYWxsYmFjayh2YWwpIT09ZmFsc2Upe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZGxnLmNsb3NlKCk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICB9ZWxzZSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGRsZy5jbG9zZSgpO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIH0pXHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdvbnN1cmUnOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgcmV0dXJuIHRydWU7XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdvbmhpZGUnOmZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgIHJldHVybiB0cnVlO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkuc2hvdyhodG1sLHRpdGxlP3RpdGxlOifor7fpgInmi6knKTtcclxuICAgIH0sXHJcbiAgICBwaWNrTGlzdDpmdW5jdGlvbiAoY29uZmlnLGNhbGxiYWNrLGZpbHRlcikge1xyXG4gICAgICAgIGlmKHR5cGVvZiBjb25maWc9PT0nc3RyaW5nJyljb25maWc9e3VybDpjb25maWd9O1xyXG4gICAgICAgIGNvbmZpZz0kLmV4dGVuZCh7XHJcbiAgICAgICAgICAgICd1cmwnOicnLFxyXG4gICAgICAgICAgICAnbmFtZSc6J+WvueixoScsXHJcbiAgICAgICAgICAgICdzZWFyY2hIb2xkZXInOifmoLnmja7lkI3np7DmkJzntKInLFxyXG4gICAgICAgICAgICAnaWRrZXknOidpZCcsXHJcbiAgICAgICAgICAgICdvblJvdyc6bnVsbCxcclxuICAgICAgICAgICAgJ2V4dGVuZCc6bnVsbCxcclxuICAgICAgICAgICAgJ3Jvd1RlbXBsYXRlJzonPGEgaHJlZj1cImphdmFzY3JpcHQ6XCIgZGF0YS1pZD1cIntAaWR9XCIgY2xhc3M9XCJsaXN0LWdyb3VwLWl0ZW0gbGlzdC1ncm91cC1pdGVtLWFjdGlvblwiPlt7QGlkfV0mbmJzcDs8aSBjbGFzcz1cImlvbi1tZC1wZXJzb25cIj48L2k+IHtAdXNlcm5hbWV9Jm5ic3A7Jm5ic3A7Jm5ic3A7PHNtYWxsPjxpIGNsYXNzPVwiaW9uLW1kLXBob25lLXBvcnRyYWl0XCI+PC9pPiB7QG1vYmlsZX08L3NtYWxsPjwvYT4nXHJcbiAgICAgICAgfSxjb25maWd8fHt9KTtcclxuICAgICAgICB2YXIgY3VycmVudD1udWxsO1xyXG4gICAgICAgIHZhciBleHRodG1sPScnO1xyXG4gICAgICAgIGlmKGNvbmZpZy5leHRlbmQpe1xyXG4gICAgICAgICAgICBleHRodG1sPSc8c2VsZWN0IG5hbWU9XCInK2NvbmZpZy5leHRlbmQubmFtZSsnXCIgY2xhc3M9XCJmb3JtLWNvbnRyb2xcIj48b3B0aW9uIHZhbHVlPVwiXCI+Jytjb25maWcuZXh0ZW5kLnRpdGxlKyc8L29wdGlvbj48L3NlbGVjdD4nO1xyXG4gICAgICAgIH1cclxuICAgICAgICBpZighZmlsdGVyKWZpbHRlcj17fTtcclxuICAgICAgICB2YXIgZGxnPW5ldyBEaWFsb2coe1xyXG4gICAgICAgICAgICAnb25zaG93bic6ZnVuY3Rpb24oYm9keSl7XHJcbiAgICAgICAgICAgICAgICB2YXIgYnRuPWJvZHkuZmluZCgnLnNlYXJjaGJ0bicpO1xyXG4gICAgICAgICAgICAgICAgdmFyIGlucHV0PWJvZHkuZmluZCgnLnNlYXJjaHRleHQnKTtcclxuICAgICAgICAgICAgICAgIHZhciBsaXN0Ym94PWJvZHkuZmluZCgnLmxpc3QtZ3JvdXAnKTtcclxuICAgICAgICAgICAgICAgIHZhciBpc2xvYWRpbmc9ZmFsc2U7XHJcbiAgICAgICAgICAgICAgICB2YXIgZXh0RmllbGQ9bnVsbDtcclxuICAgICAgICAgICAgICAgIGlmKGNvbmZpZy5leHRlbmQpe1xyXG4gICAgICAgICAgICAgICAgICAgIGV4dEZpZWxkPWJvZHkuZmluZCgnW25hbWU9Jytjb25maWcuZXh0ZW5kLm5hbWUrJ10nKTtcclxuICAgICAgICAgICAgICAgICAgICAkLmFqYXgoe1xyXG4gICAgICAgICAgICAgICAgICAgICAgIHVybDpjb25maWcuZXh0ZW5kLnVybCxcclxuICAgICAgICAgICAgICAgICAgICAgICAgdHlwZTonR0VUJyxcclxuICAgICAgICAgICAgICAgICAgICAgICAgZGF0YVR5cGU6J0pTT04nLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICBzdWNjZXNzOmZ1bmN0aW9uIChqc29uKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBleHRGaWVsZC5hcHBlbmQoY29uZmlnLmV4dGVuZC5odG1sUm93LmNvbXBpbGUoanNvbi5kYXRhLHRydWUpKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgIH0pO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgYnRuLmNsaWNrKGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgICAgICAgICAgaWYoaXNsb2FkaW5nKXJldHVybjtcclxuICAgICAgICAgICAgICAgICAgICBpc2xvYWRpbmc9dHJ1ZTtcclxuICAgICAgICAgICAgICAgICAgICBsaXN0Ym94Lmh0bWwoJzxzcGFuIGNsYXNzPVwibGlzdC1sb2FkaW5nXCI+5Yqg6L295LitLi4uPC9zcGFuPicpO1xyXG4gICAgICAgICAgICAgICAgICAgIGZpbHRlclsna2V5J109aW5wdXQudmFsKCk7XHJcbiAgICAgICAgICAgICAgICAgICAgaWYoY29uZmlnLmV4dGVuZCl7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGZpbHRlcltjb25maWcuZXh0ZW5kLm5hbWVdPWV4dEZpZWxkLnZhbCgpO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICAkLmFqYXgoXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHVybDpjb25maWcudXJsLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdHlwZTonR0VUJyxcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGRhdGFUeXBlOidKU09OJyxcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGRhdGE6ZmlsdGVyLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc3VjY2VzczpmdW5jdGlvbihqc29uKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpc2xvYWRpbmc9ZmFsc2U7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYoanNvbi5zdGF0dXMpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZihqc29uLmRhdGEgJiYganNvbi5kYXRhLmxlbmd0aCkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbGlzdGJveC5odG1sKGNvbmZpZy5yb3dUZW1wbGF0ZS5jb21waWxlKGpzb24uZGF0YSwgdHJ1ZSkpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbGlzdGJveC5maW5kKCdhLmxpc3QtZ3JvdXAtaXRlbScpLmNsaWNrKGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgaWQgPSAkKHRoaXMpLmRhdGEoJ2lkJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgZm9yICh2YXIgaSA9IDA7IGkgPCBqc29uLmRhdGEubGVuZ3RoOyBpKyspIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYoanNvbi5kYXRhW2ldW2NvbmZpZy5pZGtleV09PWlkKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGN1cnJlbnQ9anNvbi5kYXRhW2ldO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbGlzdGJveC5maW5kKCdhLmxpc3QtZ3JvdXAtaXRlbScpLnJlbW92ZUNsYXNzKCdhY3RpdmUnKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykuYWRkQ2xhc3MoJ2FjdGl2ZScpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9KVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9ZWxzZXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxpc3Rib3guaHRtbCgnPHNwYW4gY2xhc3M9XCJsaXN0LWxvYWRpbmdcIj48aSBjbGFzcz1cImlvbi1tZC13YXJuaW5nXCI+PC9pPiDmsqHmnInmo4DntKLliLAnK2NvbmZpZy5uYW1lKyc8L3NwYW4+Jyk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9ZWxzZXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbGlzdGJveC5odG1sKCc8c3BhbiBjbGFzcz1cInRleHQtZGFuZ2VyXCI+PGkgY2xhc3M9XCJpb24tbWQtd2FybmluZ1wiPjwvaT4g5Yqg6L295aSx6LSlPC9zcGFuPicpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgICk7XHJcblxyXG4gICAgICAgICAgICAgICAgfSkudHJpZ2dlcignY2xpY2snKTtcclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgJ29uc3VyZSc6ZnVuY3Rpb24oYm9keSl7XHJcbiAgICAgICAgICAgICAgICBpZighY3VycmVudCl7XHJcbiAgICAgICAgICAgICAgICAgICAgdG9hc3RyLndhcm5pbmcoJ+ayoeaciemAieaLqScrY29uZmlnLm5hbWUrJyEnKTtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICBpZih0eXBlb2YgY2FsbGJhY2s9PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgICAgIHZhciByZXN1bHQgPSBjYWxsYmFjayhjdXJyZW50KTtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gcmVzdWx0O1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkuc2hvdygnPGRpdiBjbGFzcz1cImlucHV0LWdyb3VwXCI+JytleHRodG1sKyc8aW5wdXQgdHlwZT1cInRleHRcIiBjbGFzcz1cImZvcm0tY29udHJvbCBzZWFyY2h0ZXh0XCIgbmFtZT1cImtleXdvcmRcIiBwbGFjZWhvbGRlcj1cIicrY29uZmlnLnNlYXJjaEhvbGRlcisnXCIvPjxkaXYgY2xhc3M9XCJpbnB1dC1ncm91cC1hcHBlbmRcIj48YSBjbGFzcz1cImJ0biBidG4tb3V0bGluZS1zZWNvbmRhcnkgc2VhcmNoYnRuXCI+PGkgY2xhc3M9XCJpb24tbWQtc2VhcmNoXCI+PC9pPjwvYT48L2Rpdj48L2Rpdj48ZGl2IGNsYXNzPVwibGlzdC1ncm91cCBsaXN0LWdyb3VwLXBpY2tlciBtdC0yXCI+PC9kaXY+Jywn6K+35pCc57Si5bm26YCJ5oupJytjb25maWcubmFtZSk7XHJcbiAgICB9LFxyXG4gICAgcGlja1VzZXI6ZnVuY3Rpb24oY2FsbGJhY2ssZmlsdGVyKXtcclxuICAgICAgICByZXR1cm4gdGhpcy5waWNrTGlzdCh7XHJcbiAgICAgICAgICAgICd1cmwnOndpbmRvdy5nZXRfc2VhcmNoX3VybCgnbWVtYmVyJyksXHJcbiAgICAgICAgICAgICduYW1lJzon5Lya5ZGYJyxcclxuICAgICAgICAgICAgJ3NlYXJjaEhvbGRlcic6J+agueaNruS8muWRmGlk5oiW5ZCN56ew77yM55S16K+d5p2l5pCc57SiJ1xyXG4gICAgICAgIH0sY2FsbGJhY2ssZmlsdGVyKTtcclxuICAgIH0sXHJcbiAgICBwaWNrQXJ0aWNsZTpmdW5jdGlvbihjYWxsYmFjayxmaWx0ZXIpe1xyXG4gICAgICAgIHJldHVybiB0aGlzLnBpY2tMaXN0KHtcclxuICAgICAgICAgICAgJ3VybCc6d2luZG93LmdldF9zZWFyY2hfdXJsKCdhcnRpY2xlJyksXHJcbiAgICAgICAgICAgIHJvd1RlbXBsYXRlOic8YSBocmVmPVwiamF2YXNjcmlwdDpcIiBkYXRhLWlkPVwie0BpZH1cIiBjbGFzcz1cImxpc3QtZ3JvdXAtaXRlbSBsaXN0LWdyb3VwLWl0ZW0tYWN0aW9uXCI+e2lmIEBjb3Zlcn08ZGl2IHN0eWxlPVwiYmFja2dyb3VuZC1pbWFnZTp1cmwoe0Bjb3Zlcn0pXCIgY2xhc3M9XCJpbWd2aWV3XCIgPjwvZGl2PnsvaWZ9PGRpdiBjbGFzcz1cInRleHQtYmxvY2tcIj5be0BpZH1dJm5ic3A7e0B0aXRsZX0mbmJzcDs8YnIgLz57QGRlc2NyaXB0aW9ufTwvZGl2PjwvYT4nLFxyXG4gICAgICAgICAgICBuYW1lOifmlofnq6AnLFxyXG4gICAgICAgICAgICBpZGtleTonaWQnLFxyXG4gICAgICAgICAgICBleHRlbmQ6e1xyXG4gICAgICAgICAgICAgICBuYW1lOidjYXRlJyxcclxuICAgICAgICAgICAgICAgIHRpdGxlOifmjInliIbnsbvmkJzntKInLFxyXG4gICAgICAgICAgICAgICAgdXJsOmdldF9jYXRlX3VybCgnYXJ0aWNsZScpLFxyXG4gICAgICAgICAgICAgICAgaHRtbFJvdzonPG9wdGlvbiB2YWx1ZT1cIntAaWR9XCI+e0BodG1sfXtAdGl0bGV9PC9vcHRpb24+J1xyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAnc2VhcmNoSG9sZGVyJzon5qC55o2u5paH56ug5qCH6aKY5pCc57SiJ1xyXG4gICAgICAgIH0sY2FsbGJhY2ssZmlsdGVyKTtcclxuICAgIH0sXHJcbiAgICBwaWNrUHJvZHVjdDpmdW5jdGlvbihjYWxsYmFjayxmaWx0ZXIpe1xyXG4gICAgICAgIHJldHVybiB0aGlzLnBpY2tMaXN0KHtcclxuICAgICAgICAgICAgJ3VybCc6d2luZG93LmdldF9zZWFyY2hfdXJsKCdwcm9kdWN0JyksXHJcbiAgICAgICAgICAgIHJvd1RlbXBsYXRlOic8YSBocmVmPVwiamF2YXNjcmlwdDpcIiBkYXRhLWlkPVwie0BpZH1cIiBjbGFzcz1cImxpc3QtZ3JvdXAtaXRlbSBsaXN0LWdyb3VwLWl0ZW0tYWN0aW9uXCI+e2lmIEBpbWFnZX08ZGl2IHN0eWxlPVwiYmFja2dyb3VuZC1pbWFnZTp1cmwoe0BpbWFnZX0pXCIgY2xhc3M9XCJpbWd2aWV3XCIgPjwvZGl2PnsvaWZ9PGRpdiBjbGFzcz1cInRleHQtYmxvY2tcIj5be0BpZH1dJm5ic3A7e0B0aXRsZX0mbmJzcDs8YnIgLz57QG1pbl9wcmljZX1+e0BtYXhfcHJpY2V9PC9kaXY+PC9hPicsXHJcbiAgICAgICAgICAgIG5hbWU6J+S6p+WTgScsXHJcbiAgICAgICAgICAgIGlka2V5OidpZCcsXHJcbiAgICAgICAgICAgIGV4dGVuZDp7XHJcbiAgICAgICAgICAgICAgICBuYW1lOidjYXRlJyxcclxuICAgICAgICAgICAgICAgIHRpdGxlOifmjInliIbnsbvmkJzntKInLFxyXG4gICAgICAgICAgICAgICAgdXJsOmdldF9jYXRlX3VybCgncHJvZHVjdCcpLFxyXG4gICAgICAgICAgICAgICAgaHRtbFJvdzonPG9wdGlvbiB2YWx1ZT1cIntAaWR9XCI+e0BodG1sfXtAdGl0bGV9PC9vcHRpb24+J1xyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAnc2VhcmNoSG9sZGVyJzon5qC55o2u5Lqn5ZOB5ZCN56ew5pCc57SiJ1xyXG4gICAgICAgIH0sY2FsbGJhY2ssZmlsdGVyKTtcclxuICAgIH0sXHJcbiAgICBwaWNrTG9jYXRlOmZ1bmN0aW9uKHR5cGUsIGNhbGxiYWNrLCBsb2NhdGUpe1xyXG4gICAgICAgIHZhciBzZXR0ZWRMb2NhdGU9bnVsbDtcclxuICAgICAgICB2YXIgZGxnPW5ldyBEaWFsb2coe1xyXG4gICAgICAgICAgICAnc2l6ZSc6J2xnJyxcclxuICAgICAgICAgICAgJ29uc2hvd24nOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgdmFyIGJ0bj1ib2R5LmZpbmQoJy5zZWFyY2hidG4nKTtcclxuICAgICAgICAgICAgICAgIHZhciBpbnB1dD1ib2R5LmZpbmQoJy5zZWFyY2h0ZXh0Jyk7XHJcbiAgICAgICAgICAgICAgICB2YXIgbWFwYm94PWJvZHkuZmluZCgnLm1hcCcpO1xyXG4gICAgICAgICAgICAgICAgdmFyIG1hcGluZm89Ym9keS5maW5kKCcubWFwaW5mbycpO1xyXG4gICAgICAgICAgICAgICAgbWFwYm94LmNzcygnaGVpZ2h0JywkKHdpbmRvdykuaGVpZ2h0KCkqLjYpO1xyXG4gICAgICAgICAgICAgICAgdmFyIGlzbG9hZGluZz1mYWxzZTtcclxuICAgICAgICAgICAgICAgIHZhciBtYXA9SW5pdE1hcCgndGVuY2VudCcsbWFwYm94LGZ1bmN0aW9uKGFkZHJlc3MsbG9jYXRlKXtcclxuICAgICAgICAgICAgICAgICAgICBtYXBpbmZvLmh0bWwoYWRkcmVzcysnJm5ic3A7Jytsb2NhdGUubG5nKycsJytsb2NhdGUubGF0KTtcclxuICAgICAgICAgICAgICAgICAgICBzZXR0ZWRMb2NhdGU9bG9jYXRlO1xyXG4gICAgICAgICAgICAgICAgfSxsb2NhdGUpO1xyXG4gICAgICAgICAgICAgICAgYnRuLmNsaWNrKGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgICAgICAgICAgdmFyIHNlYXJjaD1pbnB1dC52YWwoKTtcclxuICAgICAgICAgICAgICAgICAgICBtYXAuc2V0TG9jYXRlKHNlYXJjaCk7XHJcbiAgICAgICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgJ29uc3VyZSc6ZnVuY3Rpb24oYm9keSl7XHJcbiAgICAgICAgICAgICAgICBpZighc2V0dGVkTG9jYXRlKXtcclxuICAgICAgICAgICAgICAgICAgICB0b2FzdHIud2FybmluZygn5rKh5pyJ6YCJ5oup5L2N572uIScpO1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIGlmKHR5cGVvZiBjYWxsYmFjaz09PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgICAgIHZhciByZXN1bHQgPSBjYWxsYmFjayhzZXR0ZWRMb2NhdGUpO1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiByZXN1bHQ7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS5zaG93KCc8ZGl2IGNsYXNzPVwiaW5wdXQtZ3JvdXBcIj48aW5wdXQgdHlwZT1cInRleHRcIiBjbGFzcz1cImZvcm0tY29udHJvbCBzZWFyY2h0ZXh0XCIgbmFtZT1cImtleXdvcmRcIiBwbGFjZWhvbGRlcj1cIuWhq+WGmeWcsOWdgOajgOe0ouS9jee9rlwiLz48ZGl2IGNsYXNzPVwiaW5wdXQtZ3JvdXAtYXBwZW5kXCI+PGEgY2xhc3M9XCJidG4gYnRuLW91dGxpbmUtc2Vjb25kYXJ5IHNlYXJjaGJ0blwiPjxpIGNsYXNzPVwiaW9uLW1kLXNlYXJjaFwiPjwvaT48L2E+PC9kaXY+PC9kaXY+JyArXHJcbiAgICAgICAgICAgICc8ZGl2IGNsYXNzPVwibWFwIG10LTJcIj48L2Rpdj4nICtcclxuICAgICAgICAgICAgJzxkaXYgY2xhc3M9XCJtYXBpbmZvIG10LTIgdGV4dC1tdXRlZFwiPuacqumAieaLqeS9jee9rjwvZGl2PicsJ+ivt+mAieaLqeWcsOWbvuS9jee9ricpO1xyXG4gICAgfVxyXG59O1xyXG5cclxualF1ZXJ5KGZ1bmN0aW9uKCQpe1xyXG5cclxuICAgIC8v55uR5o6n5oyJ6ZSuXHJcbiAgICAkKGRvY3VtZW50KS5vbigna2V5ZG93bicsIGZ1bmN0aW9uKGUpe1xyXG4gICAgICAgIGlmKCFEaWFsb2cuaW5zdGFuY2UpcmV0dXJuO1xyXG4gICAgICAgIHZhciBkbGc9RGlhbG9nLmluc3RhbmNlO1xyXG4gICAgICAgIGlmIChlLmtleUNvZGUgPT0gMTMpIHtcclxuICAgICAgICAgICAgZGxnLmJveC5maW5kKCcubW9kYWwtZm9vdGVyIC5idG4nKS5lcShkbGcub3B0aW9ucy5kZWZhdWx0QnRuKS50cmlnZ2VyKCdjbGljaycpO1xyXG4gICAgICAgIH1cclxuICAgICAgICAvL+m7mOiupOW3suebkeWQrOWFs+mXrVxyXG4gICAgICAgIC8qaWYgKGUua2V5Q29kZSA9PSAyNykge1xyXG4gICAgICAgICBzZWxmLmhpZGUoKTtcclxuICAgICAgICAgfSovXHJcbiAgICB9KTtcclxufSk7IiwiXHJcbmpRdWVyeS5leHRlbmQoalF1ZXJ5LmZuLHtcclxuICAgIHRhZ3M6ZnVuY3Rpb24obm0sb251cGRhdGUpe1xyXG4gICAgICAgIHZhciBkYXRhPVtdO1xyXG4gICAgICAgIHZhciB0cGw9JzxzcGFuIGNsYXNzPVwiYmFkZ2UgYmFkZ2UtaW5mb1wiPntAbGFiZWx9PGlucHV0IHR5cGU9XCJoaWRkZW5cIiBuYW1lPVwiJytubSsnXCIgdmFsdWU9XCJ7QGxhYmVsfVwiLz48YnV0dG9uIHR5cGU9XCJidXR0b25cIiBjbGFzcz1cImNsb3NlXCIgZGF0YS1kaXNtaXNzPVwiYWxlcnRcIiBhcmlhLWxhYmVsPVwiQ2xvc2VcIj48c3BhbiBhcmlhLWhpZGRlbj1cInRydWVcIj4mdGltZXM7PC9zcGFuPjwvYnV0dG9uPjwvc3Bhbj4nO1xyXG4gICAgICAgIHZhciBpdGVtPSQodGhpcykucGFyZW50cygnLmZvcm0tY29udHJvbCcpO1xyXG4gICAgICAgIHZhciBsYWJlbGdyb3VwPSQoJzxzcGFuIGNsYXNzPVwiYmFkZ2UtZ3JvdXBcIj48L3NwYW4+Jyk7XHJcbiAgICAgICAgdmFyIGlucHV0PXRoaXM7XHJcbiAgICAgICAgdGhpcy5iZWZvcmUobGFiZWxncm91cCk7XHJcbiAgICAgICAgdGhpcy5vbigna2V5dXAnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgIHZhciB2YWw9JCh0aGlzKS52YWwoKS5yZXBsYWNlKC/vvIwvZywnLCcpO1xyXG4gICAgICAgICAgICB2YXIgdXBkYXRlZD1mYWxzZTtcclxuICAgICAgICAgICAgaWYodmFsICYmIHZhbC5pbmRleE9mKCcsJyk+LTEpe1xyXG4gICAgICAgICAgICAgICAgdmFyIHZhbHM9dmFsLnNwbGl0KCcsJyk7XHJcbiAgICAgICAgICAgICAgICBmb3IodmFyIGk9MDtpPHZhbHMubGVuZ3RoO2krKyl7XHJcbiAgICAgICAgICAgICAgICAgICAgdmFsc1tpXT12YWxzW2ldLnJlcGxhY2UoL15cXHN8XFxzJC9nLCcnKTtcclxuICAgICAgICAgICAgICAgICAgICBpZih2YWxzW2ldICYmIGRhdGEuaW5kZXhPZih2YWxzW2ldKT09PS0xKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgZGF0YS5wdXNoKHZhbHNbaV0pO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBsYWJlbGdyb3VwLmFwcGVuZCh0cGwuY29tcGlsZSh7bGFiZWw6dmFsc1tpXX0pKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgdXBkYXRlZD10cnVlO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIGlucHV0LnZhbCgnJyk7XHJcbiAgICAgICAgICAgICAgICBpZih1cGRhdGVkICYmIG9udXBkYXRlKW9udXBkYXRlKGRhdGEpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkub24oJ2JsdXInLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgIHZhciB2YWw9JCh0aGlzKS52YWwoKTtcclxuICAgICAgICAgICAgaWYodmFsKSB7XHJcbiAgICAgICAgICAgICAgICAkKHRoaXMpLnZhbCh2YWwgKyAnLCcpLnRyaWdnZXIoJ2tleXVwJyk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS50cmlnZ2VyKCdibHVyJyk7XHJcbiAgICAgICAgbGFiZWxncm91cC5vbignY2xpY2snLCcuY2xvc2UnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgIHZhciB0YWc9JCh0aGlzKS5wYXJlbnRzKCcuYmFkZ2UnKS5maW5kKCdpbnB1dCcpLnZhbCgpO1xyXG4gICAgICAgICAgICB2YXIgaWQ9ZGF0YS5pbmRleE9mKHRhZyk7XHJcbiAgICAgICAgICAgIGlmKGlkKWRhdGEuc3BsaWNlKGlkLDEpO1xyXG4gICAgICAgICAgICAkKHRoaXMpLnBhcmVudHMoJy5iYWRnZScpLnJlbW92ZSgpO1xyXG4gICAgICAgICAgICBpZihvbnVwZGF0ZSlvbnVwZGF0ZShkYXRhKTtcclxuICAgICAgICB9KTtcclxuICAgICAgICBpdGVtLmNsaWNrKGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgIGlucHV0LmZvY3VzKCk7XHJcbiAgICAgICAgfSk7XHJcbiAgICB9XHJcbn0pOyIsIi8v5pel5pyf57uE5Lu2XHJcbmlmKCQuZm4uZGF0ZXRpbWVwaWNrZXIpIHtcclxuICAgIHZhciB0b29sdGlwcz0ge1xyXG4gICAgICAgIHRvZGF5OiAn5a6a5L2N5b2T5YmN5pel5pyfJyxcclxuICAgICAgICBjbGVhcjogJ+a4hemZpOW3sumAieaXpeacnycsXHJcbiAgICAgICAgY2xvc2U6ICflhbPpl63pgInmi6nlmagnLFxyXG4gICAgICAgIHNlbGVjdE1vbnRoOiAn6YCJ5oup5pyI5Lu9JyxcclxuICAgICAgICBwcmV2TW9udGg6ICfkuIrkuKrmnIgnLFxyXG4gICAgICAgIG5leHRNb250aDogJ+S4i+S4quaciCcsXHJcbiAgICAgICAgc2VsZWN0WWVhcjogJ+mAieaLqeW5tOS7vScsXHJcbiAgICAgICAgcHJldlllYXI6ICfkuIrkuIDlubQnLFxyXG4gICAgICAgIG5leHRZZWFyOiAn5LiL5LiA5bm0JyxcclxuICAgICAgICBzZWxlY3REZWNhZGU6ICfpgInmi6nlubTku73ljLrpl7QnLFxyXG4gICAgICAgIHNlbGVjdFRpbWU6J+mAieaLqeaXtumXtCcsXHJcbiAgICAgICAgcHJldkRlY2FkZTogJ+S4iuS4gOWMuumXtCcsXHJcbiAgICAgICAgbmV4dERlY2FkZTogJ+S4i+S4gOWMuumXtCcsXHJcbiAgICAgICAgcHJldkNlbnR1cnk6ICfkuIrkuKrkuJbnuqonLFxyXG4gICAgICAgIG5leHRDZW50dXJ5OiAn5LiL5Liq5LiW57qqJ1xyXG4gICAgfTtcclxuXHJcbiAgICBmdW5jdGlvbiB0cmFuc09wdGlvbihvcHRpb24pIHtcclxuICAgICAgICBpZighb3B0aW9uKXJldHVybiB7fTtcclxuICAgICAgICB2YXIgbmV3b3B0PXt9O1xyXG4gICAgICAgIGZvcih2YXIgaSBpbiBvcHRpb24pe1xyXG4gICAgICAgICAgICBzd2l0Y2ggKGkpe1xyXG4gICAgICAgICAgICAgICAgY2FzZSAndmlld21vZGUnOlxyXG4gICAgICAgICAgICAgICAgICAgIG5ld29wdFsndmlld01vZGUnXT1vcHRpb25baV07XHJcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgICAgICBjYXNlICdrZWVwb3Blbic6XHJcbiAgICAgICAgICAgICAgICAgICAgbmV3b3B0WydrZWVwT3BlbiddPW9wdGlvbltpXTtcclxuICAgICAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgICAgIGRlZmF1bHQ6XHJcbiAgICAgICAgICAgICAgICAgICAgbmV3b3B0W2ldPW9wdGlvbltpXTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH1cclxuICAgICAgICByZXR1cm4gbmV3b3B0O1xyXG4gICAgfVxyXG4gICAgJCgnLmRhdGVwaWNrZXInKS5lYWNoKGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgdmFyIGNvbmZpZz0kLmV4dGVuZCh7XHJcbiAgICAgICAgICAgIHRvb2x0aXBzOnRvb2x0aXBzLFxyXG4gICAgICAgICAgICBmb3JtYXQ6ICdZWVlZLU1NLUREJyxcclxuICAgICAgICAgICAgbG9jYWxlOiAnemgtY24nLFxyXG4gICAgICAgICAgICBzaG93Q2xlYXI6dHJ1ZSxcclxuICAgICAgICAgICAgc2hvd1RvZGF5QnV0dG9uOnRydWUsXHJcbiAgICAgICAgICAgIHNob3dDbG9zZTp0cnVlLFxyXG4gICAgICAgICAgICBrZWVwSW52YWxpZDp0cnVlXHJcbiAgICAgICAgfSx0cmFuc09wdGlvbigkKHRoaXMpLmRhdGEoKSkpO1xyXG5cclxuICAgICAgICAkKHRoaXMpLmRhdGV0aW1lcGlja2VyKGNvbmZpZyk7XHJcbiAgICB9KTtcclxuXHJcbiAgICAkKCcuZGF0ZS1yYW5nZScpLmVhY2goZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHZhciBmcm9tID0gJCh0aGlzKS5maW5kKCdbbmFtZT1mcm9tZGF0ZV0sLmZyb21kYXRlJyksIHRvID0gJCh0aGlzKS5maW5kKCdbbmFtZT10b2RhdGVdLC50b2RhdGUnKTtcclxuICAgICAgICB2YXIgb3B0aW9ucyA9ICQuZXh0ZW5kKHtcclxuICAgICAgICAgICAgdG9vbHRpcHM6dG9vbHRpcHMsXHJcbiAgICAgICAgICAgIGZvcm1hdDogJ1lZWVktTU0tREQnLFxyXG4gICAgICAgICAgICBsb2NhbGU6J3poLWNuJyxcclxuICAgICAgICAgICAgc2hvd0NsZWFyOnRydWUsXHJcbiAgICAgICAgICAgIHNob3dUb2RheUJ1dHRvbjp0cnVlLFxyXG4gICAgICAgICAgICBzaG93Q2xvc2U6dHJ1ZSxcclxuICAgICAgICAgICAga2VlcEludmFsaWQ6dHJ1ZVxyXG4gICAgICAgIH0sJCh0aGlzKS5kYXRhKCkpO1xyXG4gICAgICAgIGZyb20uZGF0ZXRpbWVwaWNrZXIob3B0aW9ucykub24oJ2RwLmNoYW5nZScsIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgaWYgKGZyb20udmFsKCkpIHtcclxuICAgICAgICAgICAgICAgIHRvLmRhdGEoJ0RhdGVUaW1lUGlja2VyJykubWluRGF0ZShmcm9tLnZhbCgpKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pO1xyXG4gICAgICAgIHRvLmRhdGV0aW1lcGlja2VyKG9wdGlvbnMpLm9uKCdkcC5jaGFuZ2UnLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIGlmICh0by52YWwoKSkge1xyXG4gICAgICAgICAgICAgICAgZnJvbS5kYXRhKCdEYXRlVGltZVBpY2tlcicpLm1heERhdGUodG8udmFsKCkpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSk7XHJcbiAgICB9KTtcclxufSIsImZ1bmN0aW9uIHNldE5hdihuYXYpIHtcclxuICAgIHZhciBpdGVtcz0kKCcubWFpbi1uYXYgLm5hdi1pdGVtJyk7XHJcbiAgICB2YXIgZmluZGVkPWZhbHNlO1xyXG4gICAgZm9yKHZhciBpPTA7aTxpdGVtcy5sZW5ndGg7aSsrKXtcclxuICAgICAgICBpZihpdGVtcy5lcShpKS5kYXRhKCdtb2RlbCcpPT09bmF2KXtcclxuICAgICAgICAgICAgaXRlbXMuZXEoaSkuYWRkQ2xhc3MoJ2FjdGl2ZScpO1xyXG4gICAgICAgICAgICBmaW5kZWQ9dHJ1ZTtcclxuICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgfVxyXG4gICAgfVxyXG4gICAgaWYoIWZpbmRlZCAmJiBuYXYuaW5kZXhPZignLScpPjApe1xyXG4gICAgICAgIG5hdj1uYXYuc3Vic3RyKDAsbmF2Lmxhc3RJbmRleE9mKCctJykpO1xyXG4gICAgICAgIHNldE5hdihuYXYpO1xyXG4gICAgfVxyXG59XHJcblxyXG5qUXVlcnkoZnVuY3Rpb24oJCl7XHJcbiAgICBpZigkKHdpbmRvdykud2lkdGgoKT49OTkxKXtcclxuICAgICAgICAkKCcubWFpbi1uYXY+LmRyb3Bkb3duJykuaG92ZXIoXHJcbiAgICAgICAgICAgIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgICQodGhpcykuZmluZCgnLmRyb3Bkb3duLW1lbnUnKS5zdG9wKHRydWUsZmFsc2UpLnNsaWRlRG93bigpO1xyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgICAgICAkKHRoaXMpLmZpbmQoJy5kcm9wZG93bi1tZW51Jykuc3RvcCh0cnVlLGZhbHNlKS5zbGlkZVVwKCk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICApO1xyXG4gICAgfWVsc2V7XHJcbiAgICAgICAgJCgnLm1haW4tbmF2Pi5kcm9wZG93bj4uZHJvcGRvd24tdG9nZ2xlJykuY2xpY2soZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xyXG4gICAgICAgICAgICBlLnN0b3BQcm9wYWdhdGlvbigpO1xyXG4gICAgICAgICAgICB2YXIgb3BlbmVkPSQodGhpcykuZGF0YSgnb3BlbmVkJyk7XHJcbiAgICAgICAgICAgIHZhciBwID0gJCh0aGlzKS5wYXJlbnRzKCcuZHJvcGRvd24nKTtcclxuICAgICAgICAgICAgaWYob3BlbmVkKXtcclxuICAgICAgICAgICAgICAgIHAuZmluZCgnLmRyb3Bkb3duLW1lbnUnKS5zdG9wKHRydWUsIGZhbHNlKS5zbGlkZVVwKCk7XHJcbiAgICAgICAgICAgIH1lbHNlIHtcclxuICAgICAgICAgICAgICAgIHAuc2libGluZ3MoKS5jaGlsZHJlbignLmRyb3Bkb3duLW1lbnUnKS5zdG9wKHRydWUsIGZhbHNlKS5zbGlkZVVwKCk7XHJcbiAgICAgICAgICAgICAgICBwLnNpYmxpbmdzKCkuY2hpbGRyZW4oJy5kcm9wZG93bi10b2dnbGUnKS5kYXRhKCdvcGVuZWQnLGZhbHNlKTtcclxuICAgICAgICAgICAgICAgIHAuZmluZCgnLmRyb3Bkb3duLW1lbnUnKS5zdG9wKHRydWUsIGZhbHNlKS5zbGlkZURvd24oKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAkKHRoaXMpLmRhdGEoJ29wZW5lZCcsIW9wZW5lZCk7XHJcbiAgICAgICAgfSlcclxuICAgIH1cclxufSk7Il19
