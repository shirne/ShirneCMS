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

});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNvbW1vbi5qcyIsInRlbXBsYXRlLmpzIiwiZGlhbG9nLmpzIiwianF1ZXJ5LnRhZy5qcyIsImRhdGV0aW1lLmluaXQuanMiLCJmcm9udC5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDMURBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQzlEQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUNwV0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUMxQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUNsRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EiLCJmaWxlIjoiZnJvbnQuanMiLCJzb3VyY2VzQ29udGVudCI6WyJmdW5jdGlvbiBkZWwob2JqLG1zZykge1xyXG4gICAgZGlhbG9nLmNvbmZpcm0obXNnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgbG9jYXRpb24uaHJlZj0kKG9iaikuYXR0cignaHJlZicpO1xyXG4gICAgfSk7XHJcbiAgICByZXR1cm4gZmFsc2U7XHJcbn1cclxuXHJcbmZ1bmN0aW9uIHJhbmRvbVN0cmluZyhsZW4sIGNoYXJTZXQpIHtcclxuICAgIGNoYXJTZXQgPSBjaGFyU2V0IHx8ICdBQkNERUZHSElKS0xNTk9QUVJTVFVWV1hZWmFiY2RlZmdoaWprbG1ub3BxcnN0dXZ3eHl6MDEyMzQ1Njc4OSc7XHJcbiAgICB2YXIgc3RyID0gJycsYWxsTGVuPWNoYXJTZXQubGVuZ3RoO1xyXG4gICAgZm9yICh2YXIgaSA9IDA7IGkgPCBsZW47IGkrKykge1xyXG4gICAgICAgIHZhciByYW5kb21Qb3ogPSBNYXRoLmZsb29yKE1hdGgucmFuZG9tKCkgKiBhbGxMZW4pO1xyXG4gICAgICAgIHN0ciArPSBjaGFyU2V0LnN1YnN0cmluZyhyYW5kb21Qb3oscmFuZG9tUG96KzEpO1xyXG4gICAgfVxyXG4gICAgcmV0dXJuIHN0cjtcclxufVxyXG5cclxuZnVuY3Rpb24gY29weV9vYmooYXJyKXtcclxuICAgIHJldHVybiBKU09OLnBhcnNlKEpTT04uc3RyaW5naWZ5KGFycikpO1xyXG59XHJcblxyXG5mdW5jdGlvbiBpc09iamVjdFZhbHVlRXF1YWwoYSwgYikge1xyXG4gICAgaWYoIWEgJiYgIWIpcmV0dXJuIHRydWU7XHJcbiAgICBpZighYSB8fCAhYilyZXR1cm4gZmFsc2U7XHJcblxyXG4gICAgLy8gT2YgY291cnNlLCB3ZSBjYW4gZG8gaXQgdXNlIGZvciBpblxyXG4gICAgLy8gQ3JlYXRlIGFycmF5cyBvZiBwcm9wZXJ0eSBuYW1lc1xyXG4gICAgdmFyIGFQcm9wcyA9IE9iamVjdC5nZXRPd25Qcm9wZXJ0eU5hbWVzKGEpO1xyXG4gICAgdmFyIGJQcm9wcyA9IE9iamVjdC5nZXRPd25Qcm9wZXJ0eU5hbWVzKGIpO1xyXG5cclxuICAgIC8vIElmIG51bWJlciBvZiBwcm9wZXJ0aWVzIGlzIGRpZmZlcmVudCxcclxuICAgIC8vIG9iamVjdHMgYXJlIG5vdCBlcXVpdmFsZW50XHJcbiAgICBpZiAoYVByb3BzLmxlbmd0aCAhPSBiUHJvcHMubGVuZ3RoKSB7XHJcbiAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgfVxyXG5cclxuICAgIGZvciAodmFyIGkgPSAwOyBpIDwgYVByb3BzLmxlbmd0aDsgaSsrKSB7XHJcbiAgICAgICAgdmFyIHByb3BOYW1lID0gYVByb3BzW2ldO1xyXG5cclxuICAgICAgICAvLyBJZiB2YWx1ZXMgb2Ygc2FtZSBwcm9wZXJ0eSBhcmUgbm90IGVxdWFsLFxyXG4gICAgICAgIC8vIG9iamVjdHMgYXJlIG5vdCBlcXVpdmFsZW50XHJcbiAgICAgICAgaWYgKGFbcHJvcE5hbWVdICE9PSBiW3Byb3BOYW1lXSkge1xyXG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICAgICAgfVxyXG4gICAgfVxyXG5cclxuICAgIC8vIElmIHdlIG1hZGUgaXQgdGhpcyBmYXIsIG9iamVjdHNcclxuICAgIC8vIGFyZSBjb25zaWRlcmVkIGVxdWl2YWxlbnRcclxuICAgIHJldHVybiB0cnVlO1xyXG59XHJcblxyXG5mdW5jdGlvbiBhcnJheV9jb21iaW5lKGEsYikge1xyXG4gICAgdmFyIG9iaj17fTtcclxuICAgIGZvcih2YXIgaT0wO2k8YS5sZW5ndGg7aSsrKXtcclxuICAgICAgICBpZihiLmxlbmd0aDxpKzEpYnJlYWs7XHJcbiAgICAgICAgb2JqW2FbaV1dPWJbaV07XHJcbiAgICB9XHJcbiAgICByZXR1cm4gb2JqO1xyXG59IiwiXHJcbk51bWJlci5wcm90b3R5cGUuZm9ybWF0PWZ1bmN0aW9uKGZpeCl7XHJcbiAgICBpZihmaXg9PT11bmRlZmluZWQpZml4PTI7XHJcbiAgICB2YXIgbnVtPXRoaXMudG9GaXhlZChmaXgpO1xyXG4gICAgdmFyIHo9bnVtLnNwbGl0KCcuJyk7XHJcbiAgICB2YXIgZm9ybWF0PVtdLGY9elswXS5zcGxpdCgnJyksbD1mLmxlbmd0aDtcclxuICAgIGZvcih2YXIgaT0wO2k8bDtpKyspe1xyXG4gICAgICAgIGlmKGk+MCAmJiBpICUgMz09MCl7XHJcbiAgICAgICAgICAgIGZvcm1hdC51bnNoaWZ0KCcsJyk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIGZvcm1hdC51bnNoaWZ0KGZbbC1pLTFdKTtcclxuICAgIH1cclxuICAgIHJldHVybiBmb3JtYXQuam9pbignJykrKHoubGVuZ3RoPT0yPycuJyt6WzFdOicnKTtcclxufTtcclxuU3RyaW5nLnByb3RvdHlwZS5jb21waWxlPWZ1bmN0aW9uKGRhdGEsbGlzdCl7XHJcblxyXG4gICAgaWYobGlzdCl7XHJcbiAgICAgICAgdmFyIHRlbXBzPVtdO1xyXG4gICAgICAgIGZvcih2YXIgaSBpbiBkYXRhKXtcclxuICAgICAgICAgICAgdGVtcHMucHVzaCh0aGlzLmNvbXBpbGUoZGF0YVtpXSkpO1xyXG4gICAgICAgIH1cclxuICAgICAgICByZXR1cm4gdGVtcHMuam9pbihcIlxcblwiKTtcclxuICAgIH1lbHNle1xyXG4gICAgICAgIHJldHVybiB0aGlzLnJlcGxhY2UoL1xce0AoW1xcd1xcZFxcLl0rKSg/OlxcfChbXFx3XFxkXSspKD86XFxzKj1cXHMqKFtcXHdcXGQsXFxzI10rKSk/KT9cXH0vZyxmdW5jdGlvbihhbGwsbTEsZnVuYyxhcmdzKXtcclxuXHJcbiAgICAgICAgICAgIGlmKG0xLmluZGV4T2YoJy4nKT4wKXtcclxuICAgICAgICAgICAgICAgIHZhciBrZXlzPW0xLnNwbGl0KCcuJyksdmFsPWRhdGE7XHJcbiAgICAgICAgICAgICAgICBmb3IodmFyIGk9MDtpPGtleXMubGVuZ3RoO2krKyl7XHJcbiAgICAgICAgICAgICAgICAgICAgaWYodmFsW2tleXNbaV1dIT09dW5kZWZpbmVkKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgdmFsPXZhbFtrZXlzW2ldXTtcclxuICAgICAgICAgICAgICAgICAgICB9ZWxzZXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgdmFsID0gJyc7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIHJldHVybiBjYWxsZnVuYyh2YWwsZnVuYyxhcmdzKTtcclxuICAgICAgICAgICAgfWVsc2V7XHJcbiAgICAgICAgICAgICAgICByZXR1cm4gZGF0YVttMV0hPT11bmRlZmluZWQ/Y2FsbGZ1bmMoZGF0YVttMV0sZnVuYyxhcmdzLGRhdGEpOicnO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSk7XHJcbiAgICB9XHJcbn07XHJcblxyXG5mdW5jdGlvbiBjYWxsZnVuYyh2YWwsZnVuYyxhcmdzLHRoaXNvYmope1xyXG4gICAgaWYoIWFyZ3Mpe1xyXG4gICAgICAgIGFyZ3M9W3ZhbF07XHJcbiAgICB9ZWxzZXtcclxuICAgICAgICBpZih0eXBlb2YgYXJncz09PSdzdHJpbmcnKWFyZ3M9YXJncy5zcGxpdCgnLCcpO1xyXG4gICAgICAgIHZhciBhcmdpZHg9YXJncy5pbmRleE9mKCcjIyMnKTtcclxuICAgICAgICBpZihhcmdpZHg+PTApe1xyXG4gICAgICAgICAgICBhcmdzW2FyZ2lkeF09dmFsO1xyXG4gICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICBhcmdzPVt2YWxdLmNvbmNhdChhcmdzKTtcclxuICAgICAgICB9XHJcbiAgICB9XHJcbiAgICAvL2NvbnNvbGUubG9nKGFyZ3MpO1xyXG4gICAgcmV0dXJuIHdpbmRvd1tmdW5jXT93aW5kb3dbZnVuY10uYXBwbHkodGhpc29iaixhcmdzKTp2YWw7XHJcbn1cclxuXHJcbmZ1bmN0aW9uIGlpZih2LG0xLG0yKXtcclxuICAgIGlmKHY9PT0nMCcpdj0wO1xyXG4gICAgcmV0dXJuIHY/bTE6bTI7XHJcbn0iLCJcclxudmFyIGRpYWxvZ1RwbD0nPGRpdiBjbGFzcz1cIm1vZGFsIGZhZGVcIiBpZD1cIntAaWR9XCIgdGFiaW5kZXg9XCItMVwiIHJvbGU9XCJkaWFsb2dcIiBhcmlhLWxhYmVsbGVkYnk9XCJ7QGlkfUxhYmVsXCIgYXJpYS1oaWRkZW49XCJ0cnVlXCI+XFxuJyArXHJcbiAgICAnICAgIDxkaXYgY2xhc3M9XCJtb2RhbC1kaWFsb2dcIj5cXG4nICtcclxuICAgICcgICAgICAgIDxkaXYgY2xhc3M9XCJtb2RhbC1jb250ZW50XCI+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgPGRpdiBjbGFzcz1cIm1vZGFsLWhlYWRlclwiPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgICAgICA8aDQgY2xhc3M9XCJtb2RhbC10aXRsZVwiIGlkPVwie0BpZH1MYWJlbFwiPjwvaDQ+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgICAgIDxidXR0b24gdHlwZT1cImJ1dHRvblwiIGNsYXNzPVwiY2xvc2VcIiBkYXRhLWRpc21pc3M9XCJtb2RhbFwiPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgICAgICAgICAgPHNwYW4gYXJpYS1oaWRkZW49XCJ0cnVlXCI+JnRpbWVzOzwvc3Bhbj5cXG4nICtcclxuICAgICcgICAgICAgICAgICAgICAgICAgIDxzcGFuIGNsYXNzPVwic3Itb25seVwiPkNsb3NlPC9zcGFuPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgICAgICA8L2J1dHRvbj5cXG4nICtcclxuICAgICcgICAgICAgICAgICA8L2Rpdj5cXG4nICtcclxuICAgICcgICAgICAgICAgICA8ZGl2IGNsYXNzPVwibW9kYWwtYm9keVwiPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgIDwvZGl2PlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJtb2RhbC1mb290ZXJcIj5cXG4nICtcclxuICAgICcgICAgICAgICAgICAgICAgPG5hdiBjbGFzcz1cIm5hdiBuYXYtZmlsbFwiPjwvbmF2PlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgIDwvZGl2PlxcbicgK1xyXG4gICAgJyAgICAgICAgPC9kaXY+XFxuJyArXHJcbiAgICAnICAgIDwvZGl2PlxcbicgK1xyXG4gICAgJzwvZGl2Pic7XHJcbnZhciBkaWFsb2dJZHg9MDtcclxuZnVuY3Rpb24gRGlhbG9nKG9wdHMpe1xyXG4gICAgaWYoIW9wdHMpb3B0cz17fTtcclxuICAgIC8v5aSE55CG5oyJ6ZKuXHJcbiAgICBpZihvcHRzLmJ0bnMhPT11bmRlZmluZWQpIHtcclxuICAgICAgICBpZiAodHlwZW9mKG9wdHMuYnRucykgPT0gJ3N0cmluZycpIHtcclxuICAgICAgICAgICAgb3B0cy5idG5zID0gW29wdHMuYnRuc107XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHZhciBkZnQ9LTE7XHJcbiAgICAgICAgZm9yICh2YXIgaSA9IDA7IGkgPCBvcHRzLmJ0bnMubGVuZ3RoOyBpKyspIHtcclxuICAgICAgICAgICAgaWYodHlwZW9mKG9wdHMuYnRuc1tpXSk9PSdzdHJpbmcnKXtcclxuICAgICAgICAgICAgICAgIG9wdHMuYnRuc1tpXT17J3RleHQnOm9wdHMuYnRuc1tpXX07XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgaWYob3B0cy5idG5zW2ldLmlzZGVmYXVsdCl7XHJcbiAgICAgICAgICAgICAgICBkZnQ9aTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH1cclxuICAgICAgICBpZihkZnQ8MCl7XHJcbiAgICAgICAgICAgIGRmdD1vcHRzLmJ0bnMubGVuZ3RoLTE7XHJcbiAgICAgICAgICAgIG9wdHMuYnRuc1tkZnRdLmlzZGVmYXVsdD10cnVlO1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgaWYoIW9wdHMuYnRuc1tkZnRdWyd0eXBlJ10pe1xyXG4gICAgICAgICAgICBvcHRzLmJ0bnNbZGZ0XVsndHlwZSddPSdwcmltYXJ5JztcclxuICAgICAgICB9XHJcbiAgICAgICAgb3B0cy5kZWZhdWx0QnRuPWRmdDtcclxuICAgIH1cclxuXHJcbiAgICB0aGlzLm9wdGlvbnM9JC5leHRlbmQoe1xyXG4gICAgICAgICdpZCc6J2RsZ01vZGFsJytkaWFsb2dJZHgrKyxcclxuICAgICAgICAnc2l6ZSc6JycsXHJcbiAgICAgICAgJ2J0bnMnOltcclxuICAgICAgICAgICAgeyd0ZXh0Jzon5Y+W5raIJywndHlwZSc6J3NlY29uZGFyeSd9LFxyXG4gICAgICAgICAgICB7J3RleHQnOifnoa7lrponLCdpc2RlZmF1bHQnOnRydWUsJ3R5cGUnOidwcmltYXJ5J31cclxuICAgICAgICBdLFxyXG4gICAgICAgICdkZWZhdWx0QnRuJzoxLFxyXG4gICAgICAgICdvbnN1cmUnOm51bGwsXHJcbiAgICAgICAgJ29uc2hvdyc6bnVsbCxcclxuICAgICAgICAnb25zaG93bic6bnVsbCxcclxuICAgICAgICAnb25oaWRlJzpudWxsLFxyXG4gICAgICAgICdvbmhpZGRlbic6bnVsbFxyXG4gICAgfSxvcHRzKTtcclxuXHJcbiAgICB0aGlzLmJveD0kKHRoaXMub3B0aW9ucy5pZCk7XHJcbn1cclxuRGlhbG9nLnByb3RvdHlwZS5nZW5lckJ0bj1mdW5jdGlvbihvcHQsaWR4KXtcclxuICAgIGlmKG9wdFsndHlwZSddKW9wdFsnY2xhc3MnXT0nYnRuLW91dGxpbmUtJytvcHRbJ3R5cGUnXTtcclxuICAgIHJldHVybiAnPGEgaHJlZj1cImphdmFzY3JpcHQ6XCIgY2xhc3M9XCJuYXYtaXRlbSBidG4gJysob3B0WydjbGFzcyddP29wdFsnY2xhc3MnXTonYnRuLW91dGxpbmUtc2Vjb25kYXJ5JykrJ1wiIGRhdGEtaW5kZXg9XCInK2lkeCsnXCI+JytvcHQudGV4dCsnPC9hPic7XHJcbn07XHJcbkRpYWxvZy5wcm90b3R5cGUuc2hvdz1mdW5jdGlvbihodG1sLHRpdGxlKXtcclxuICAgIHRoaXMuYm94PSQoJyMnK3RoaXMub3B0aW9ucy5pZCk7XHJcbiAgICBpZighdGl0bGUpdGl0bGU9J+ezu+e7n+aPkOekuic7XHJcbiAgICBpZih0aGlzLmJveC5sZW5ndGg8MSkge1xyXG4gICAgICAgICQoZG9jdW1lbnQuYm9keSkuYXBwZW5kKGRpYWxvZ1RwbC5jb21waWxlKHsnaWQnOiB0aGlzLm9wdGlvbnMuaWR9KSk7XHJcbiAgICAgICAgdGhpcy5ib3g9JCgnIycrdGhpcy5vcHRpb25zLmlkKTtcclxuICAgIH1lbHNle1xyXG4gICAgICAgIHRoaXMuYm94LnVuYmluZCgpO1xyXG4gICAgfVxyXG5cclxuICAgIC8vdGhpcy5ib3guZmluZCgnLm1vZGFsLWZvb3RlciAuYnRuLXByaW1hcnknKS51bmJpbmQoKTtcclxuICAgIHZhciBzZWxmPXRoaXM7XHJcbiAgICBEaWFsb2cuaW5zdGFuY2U9c2VsZjtcclxuXHJcbiAgICAvL+eUn+aIkOaMiemSrlxyXG4gICAgdmFyIGJ0bnM9W107XHJcbiAgICBmb3IodmFyIGk9MDtpPHRoaXMub3B0aW9ucy5idG5zLmxlbmd0aDtpKyspe1xyXG4gICAgICAgIGJ0bnMucHVzaCh0aGlzLmdlbmVyQnRuKHRoaXMub3B0aW9ucy5idG5zW2ldLGkpKTtcclxuICAgIH1cclxuICAgIHRoaXMuYm94LmZpbmQoJy5tb2RhbC1mb290ZXIgLm5hdicpLmh0bWwoYnRucy5qb2luKCdcXG4nKSk7XHJcblxyXG4gICAgdmFyIGRpYWxvZz10aGlzLmJveC5maW5kKCcubW9kYWwtZGlhbG9nJyk7XHJcbiAgICBkaWFsb2cucmVtb3ZlQ2xhc3MoJ21vZGFsLXNtJykucmVtb3ZlQ2xhc3MoJ21vZGFsLWxnJyk7XHJcbiAgICBpZih0aGlzLm9wdGlvbnMuc2l6ZT09J3NtJykge1xyXG4gICAgICAgIGRpYWxvZy5hZGRDbGFzcygnbW9kYWwtc20nKTtcclxuICAgIH1lbHNlIGlmKHRoaXMub3B0aW9ucy5zaXplPT0nbGcnKSB7XHJcbiAgICAgICAgZGlhbG9nLmFkZENsYXNzKCdtb2RhbC1sZycpO1xyXG4gICAgfVxyXG4gICAgdGhpcy5ib3guZmluZCgnLm1vZGFsLXRpdGxlJykudGV4dCh0aXRsZSk7XHJcblxyXG4gICAgdmFyIGJvZHk9dGhpcy5ib3guZmluZCgnLm1vZGFsLWJvZHknKTtcclxuICAgIGJvZHkuaHRtbChodG1sKTtcclxuICAgIHRoaXMuYm94Lm9uKCdoaWRlLmJzLm1vZGFsJyxmdW5jdGlvbigpe1xyXG4gICAgICAgIGlmKHNlbGYub3B0aW9ucy5vbmhpZGUpe1xyXG4gICAgICAgICAgICBzZWxmLm9wdGlvbnMub25oaWRlKGJvZHksc2VsZi5ib3gpO1xyXG4gICAgICAgIH1cclxuICAgICAgICBEaWFsb2cuaW5zdGFuY2U9bnVsbDtcclxuICAgIH0pO1xyXG4gICAgdGhpcy5ib3gub24oJ2hpZGRlbi5icy5tb2RhbCcsZnVuY3Rpb24oKXtcclxuICAgICAgICBpZihzZWxmLm9wdGlvbnMub25oaWRkZW4pe1xyXG4gICAgICAgICAgICBzZWxmLm9wdGlvbnMub25oaWRkZW4oYm9keSxzZWxmLmJveCk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHNlbGYuYm94LnJlbW92ZSgpO1xyXG4gICAgfSk7XHJcbiAgICB0aGlzLmJveC5vbignc2hvdy5icy5tb2RhbCcsZnVuY3Rpb24oKXtcclxuICAgICAgICBpZihzZWxmLm9wdGlvbnMub25zaG93KXtcclxuICAgICAgICAgICAgc2VsZi5vcHRpb25zLm9uc2hvdyhib2R5LHNlbGYuYm94KTtcclxuICAgICAgICB9XHJcbiAgICB9KTtcclxuICAgIHRoaXMuYm94Lm9uKCdzaG93bi5icy5tb2RhbCcsZnVuY3Rpb24oKXtcclxuICAgICAgICBpZihzZWxmLm9wdGlvbnMub25zaG93bil7XHJcbiAgICAgICAgICAgIHNlbGYub3B0aW9ucy5vbnNob3duKGJvZHksc2VsZi5ib3gpO1xyXG4gICAgICAgIH1cclxuICAgIH0pO1xyXG4gICAgdGhpcy5ib3guZmluZCgnLm1vZGFsLWZvb3RlciAuYnRuJykuY2xpY2soZnVuY3Rpb24oKXtcclxuICAgICAgICB2YXIgcmVzdWx0PXRydWUsaWR4PSQodGhpcykuZGF0YSgnaW5kZXgnKTtcclxuICAgICAgICBpZihzZWxmLm9wdGlvbnMuYnRuc1tpZHhdLmNsaWNrKXtcclxuICAgICAgICAgICAgcmVzdWx0ID0gc2VsZi5vcHRpb25zLmJ0bnNbaWR4XS5jbGljay5hcHBseSh0aGlzLFtib2R5LCBzZWxmLmJveF0pO1xyXG4gICAgICAgIH1cclxuICAgICAgICBpZihpZHg9PXNlbGYub3B0aW9ucy5kZWZhdWx0QnRuKSB7XHJcbiAgICAgICAgICAgIGlmIChzZWxmLm9wdGlvbnMub25zdXJlKSB7XHJcbiAgICAgICAgICAgICAgICByZXN1bHQgPSBzZWxmLm9wdGlvbnMub25zdXJlLmFwcGx5KHRoaXMsW2JvZHksIHNlbGYuYm94XSk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9XHJcbiAgICAgICAgaWYocmVzdWx0IT09ZmFsc2Upe1xyXG4gICAgICAgICAgICBzZWxmLmJveC5tb2RhbCgnaGlkZScpO1xyXG4gICAgICAgIH1cclxuICAgIH0pO1xyXG4gICAgdGhpcy5ib3gubW9kYWwoJ3Nob3cnKTtcclxuICAgIHJldHVybiB0aGlzO1xyXG59O1xyXG5EaWFsb2cucHJvdG90eXBlLmhpZGU9RGlhbG9nLnByb3RvdHlwZS5jbG9zZT1mdW5jdGlvbigpe1xyXG4gICAgdGhpcy5ib3gubW9kYWwoJ2hpZGUnKTtcclxuICAgIHJldHVybiB0aGlzO1xyXG59O1xyXG5cclxudmFyIGRpYWxvZz17XHJcbiAgICBhbGVydDpmdW5jdGlvbihtZXNzYWdlLGNhbGxiYWNrLHRpdGxlKXtcclxuICAgICAgICB2YXIgY2FsbGVkPWZhbHNlO1xyXG4gICAgICAgIHZhciBpc2NhbGxiYWNrPXR5cGVvZiBjYWxsYmFjaz09J2Z1bmN0aW9uJztcclxuICAgICAgICByZXR1cm4gbmV3IERpYWxvZyh7XHJcbiAgICAgICAgICAgIGJ0bnM6J+ehruWumicsXHJcbiAgICAgICAgICAgIG9uc3VyZTpmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICAgICAgaWYoaXNjYWxsYmFjayl7XHJcbiAgICAgICAgICAgICAgICAgICAgY2FsbGVkPXRydWU7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGNhbGxiYWNrKHRydWUpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICBvbmhpZGU6ZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgICAgIGlmKCFjYWxsZWQgJiYgaXNjYWxsYmFjayl7XHJcbiAgICAgICAgICAgICAgICAgICAgY2FsbGJhY2soZmFsc2UpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkuc2hvdyhtZXNzYWdlLHRpdGxlKTtcclxuICAgIH0sXHJcbiAgICBjb25maXJtOmZ1bmN0aW9uKG1lc3NhZ2UsY29uZmlybSxjYW5jZWwpe1xyXG4gICAgICAgIHZhciBjYWxsZWQ9ZmFsc2U7XHJcbiAgICAgICAgcmV0dXJuIG5ldyBEaWFsb2coe1xyXG4gICAgICAgICAgICAnb25zdXJlJzpmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICAgICAgaWYodHlwZW9mIGNvbmZpcm09PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgICAgIGNhbGxlZD10cnVlO1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBjb25maXJtKCk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdvbmhpZGUnOmZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgIGlmKGNhbGxlZD1mYWxzZSAmJiB0eXBlb2YgY2FuY2VsPT0nZnVuY3Rpb24nKXtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gY2FuY2VsKCk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS5zaG93KG1lc3NhZ2UpO1xyXG4gICAgfSxcclxuICAgIHByb21wdDpmdW5jdGlvbihtZXNzYWdlLGNhbGxiYWNrLGNhbmNlbCl7XHJcbiAgICAgICAgdmFyIGNhbGxlZD1mYWxzZTtcclxuICAgICAgICB2YXIgY29udGVudEh0bWw9JzxkaXYgY2xhc3M9XCJmb3JtLWdyb3VwXCI+e0BpbnB1dH08L2Rpdj4nO1xyXG4gICAgICAgIHZhciB0aXRsZT0n6K+36L6T5YWl5L+h5oGvJztcclxuICAgICAgICBpZih0eXBlb2YgbWVzc2FnZT09J3N0cmluZycpe1xyXG4gICAgICAgICAgICB0aXRsZT1tZXNzYWdlO1xyXG4gICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICB0aXRsZT1tZXNzYWdlLnRpdGxlO1xyXG4gICAgICAgICAgICBpZihtZXNzYWdlLmNvbnRlbnQpIHtcclxuICAgICAgICAgICAgICAgIGNvbnRlbnRIdG1sID0gbWVzc2FnZS5jb250ZW50LmluZGV4T2YoJ3tAaW5wdXR9JykgPiAtMSA/IG1lc3NhZ2UuY29udGVudCA6IG1lc3NhZ2UuY29udGVudCArIGNvbnRlbnRIdG1sO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHJldHVybiBuZXcgRGlhbG9nKHtcclxuICAgICAgICAgICAgJ29uc2hvdyc6ZnVuY3Rpb24oYm9keSl7XHJcbiAgICAgICAgICAgICAgICBpZihtZXNzYWdlICYmIG1lc3NhZ2Uub25zaG93KXtcclxuICAgICAgICAgICAgICAgICAgICBtZXNzYWdlLm9uc2hvdyhib2R5KTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgJ29uc2hvd24nOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgYm9keS5maW5kKCdbbmFtZT1jb25maXJtX2lucHV0XScpLmZvY3VzKCk7XHJcbiAgICAgICAgICAgICAgICBpZihtZXNzYWdlICYmIG1lc3NhZ2Uub25zaG93bil7XHJcbiAgICAgICAgICAgICAgICAgICAgbWVzc2FnZS5vbnNob3duKGJvZHkpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAnb25zdXJlJzpmdW5jdGlvbihib2R5KXtcclxuICAgICAgICAgICAgICAgIHZhciB2YWw9Ym9keS5maW5kKCdbbmFtZT1jb25maXJtX2lucHV0XScpLnZhbCgpO1xyXG4gICAgICAgICAgICAgICAgaWYodHlwZW9mIGNhbGxiYWNrPT0nZnVuY3Rpb24nKXtcclxuICAgICAgICAgICAgICAgICAgICB2YXIgcmVzdWx0ID0gY2FsbGJhY2sodmFsKTtcclxuICAgICAgICAgICAgICAgICAgICBpZihyZXN1bHQ9PT10cnVlKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgY2FsbGVkPXRydWU7XHJcbiAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiByZXN1bHQ7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdvbmhpZGUnOmZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgIGlmKGNhbGxlZD1mYWxzZSAmJiB0eXBlb2YgY2FuY2VsPT0nZnVuY3Rpb24nKXtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gY2FuY2VsKCk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS5zaG93KGNvbnRlbnRIdG1sLmNvbXBpbGUoe2lucHV0Oic8aW5wdXQgdHlwZT1cInRleHRcIiBuYW1lPVwiY29uZmlybV9pbnB1dFwiIGNsYXNzPVwiZm9ybS1jb250cm9sXCIgLz4nfSksdGl0bGUpO1xyXG4gICAgfSxcclxuICAgIGFjdGlvbjpmdW5jdGlvbiAobGlzdCxjYWxsYmFjayx0aXRsZSkge1xyXG4gICAgICAgIHZhciBodG1sPSc8ZGl2IGNsYXNzPVwibGlzdC1ncm91cFwiPjxhIGhyZWY9XCJqYXZhc2NyaXB0OlwiIGNsYXNzPVwibGlzdC1ncm91cC1pdGVtIGxpc3QtZ3JvdXAtaXRlbS1hY3Rpb25cIj4nK2xpc3Quam9pbignPC9hPjxhIGhyZWY9XCJqYXZhc2NyaXB0OlwiIGNsYXNzPVwibGlzdC1ncm91cC1pdGVtIGxpc3QtZ3JvdXAtaXRlbS1hY3Rpb25cIj4nKSsnPC9hPjwvZGl2Pic7XHJcbiAgICAgICAgdmFyIGFjdGlvbnM9bnVsbDtcclxuICAgICAgICB2YXIgZGxnPW5ldyBEaWFsb2coe1xyXG4gICAgICAgICAgICAnb25zaG93JzpmdW5jdGlvbihib2R5KXtcclxuICAgICAgICAgICAgICAgIGFjdGlvbnM9Ym9keS5maW5kKCcubGlzdC1ncm91cC1pdGVtLWFjdGlvbicpO1xyXG4gICAgICAgICAgICAgICAgYWN0aW9ucy5jbGljayhmdW5jdGlvbiAoZSkge1xyXG4gICAgICAgICAgICAgICAgICAgIGFjdGlvbnMucmVtb3ZlQ2xhc3MoJ2FjdGl2ZScpO1xyXG4gICAgICAgICAgICAgICAgICAgICQodGhpcykuYWRkQ2xhc3MoJ2FjdGl2ZScpO1xyXG4gICAgICAgICAgICAgICAgfSlcclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgJ29uc3VyZSc6ZnVuY3Rpb24oYm9keSl7XHJcbiAgICAgICAgICAgICAgICB2YXIgYWN0aW9uPWFjdGlvbnMuZmlsdGVyKCcuYWN0aXZlJyk7XHJcbiAgICAgICAgICAgICAgICBpZihhY3Rpb24ubGVuZ3RoPjApe1xyXG4gICAgICAgICAgICAgICAgICAgIHZhciB2YWw9YWN0aW9ucy5pbmRleChhY3Rpb24pO1xyXG4gICAgICAgICAgICAgICAgICAgIGlmKHR5cGVvZiBjYWxsYmFjaz09J2Z1bmN0aW9uJyl7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiBjYWxsYmFjayh2YWwpO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgJ29uaGlkZSc6ZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAgICAgcmV0dXJuIHRydWU7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS5zaG93KGh0bWwsdGl0bGU/dGl0bGU6J+ivt+mAieaLqScpO1xyXG4gICAgfSxcclxuICAgIHBpY2tVc2VyOmZ1bmN0aW9uKHVybCxjYWxsYmFjayxmaWx0ZXIpe1xyXG4gICAgICAgIHZhciB1c2VyPW51bGw7XHJcbiAgICAgICAgaWYoIWZpbHRlcilmaWx0ZXI9e307XHJcbiAgICAgICAgdmFyIGRsZz1uZXcgRGlhbG9nKHtcclxuICAgICAgICAgICAgJ29uc2hvd24nOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgdmFyIGJ0bj1ib2R5LmZpbmQoJy5zZWFyY2hidG4nKTtcclxuICAgICAgICAgICAgICAgIHZhciBpbnB1dD1ib2R5LmZpbmQoJy5zZWFyY2h0ZXh0Jyk7XHJcbiAgICAgICAgICAgICAgICB2YXIgbGlzdGJveD1ib2R5LmZpbmQoJy5saXN0LWdyb3VwJyk7XHJcbiAgICAgICAgICAgICAgICB2YXIgaXNsb2FkaW5nPWZhbHNlO1xyXG4gICAgICAgICAgICAgICAgYnRuLmNsaWNrKGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgICAgICAgICAgaWYoaXNsb2FkaW5nKXJldHVybjtcclxuICAgICAgICAgICAgICAgICAgICBpc2xvYWRpbmc9dHJ1ZTtcclxuICAgICAgICAgICAgICAgICAgICBsaXN0Ym94Lmh0bWwoJzxzcGFuIGNsYXNzPVwibGlzdC1sb2FkaW5nXCI+5Yqg6L295LitLi4uPC9zcGFuPicpO1xyXG4gICAgICAgICAgICAgICAgICAgIGZpbHRlclsna2V5J109aW5wdXQudmFsKCk7XHJcbiAgICAgICAgICAgICAgICAgICAgJC5hamF4KFxyXG4gICAgICAgICAgICAgICAgICAgICAgICB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB1cmw6dXJsLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdHlwZTonR0VUJyxcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGRhdGFUeXBlOidKU09OJyxcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGRhdGE6ZmlsdGVyLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc3VjY2VzczpmdW5jdGlvbihqc29uKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpc2xvYWRpbmc9ZmFsc2U7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYoanNvbi5zdGF0dXMpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZihqc29uLmRhdGEgJiYganNvbi5kYXRhLmxlbmd0aCkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbGlzdGJveC5odG1sKCc8YSBocmVmPVwiamF2YXNjcmlwdDpcIiBkYXRhLWlkPVwie0BpZH1cIiBjbGFzcz1cImxpc3QtZ3JvdXAtaXRlbSBsaXN0LWdyb3VwLWl0ZW0tYWN0aW9uXCI+W3tAaWR9XSZuYnNwOzxpIGNsYXNzPVwiaW9uLW1kLXBlcnNvblwiPjwvaT4ge0B1c2VybmFtZX0mbmJzcDsmbmJzcDsmbmJzcDs8c21hbGw+PGkgY2xhc3M9XCJpb24tbWQtcGhvbmUtcG9ydHJhaXRcIj48L2k+IHtAbW9iaWxlfTwvc21hbGw+PC9hPicuY29tcGlsZShqc29uLmRhdGEsIHRydWUpKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxpc3Rib3guZmluZCgnYS5saXN0LWdyb3VwLWl0ZW0nKS5jbGljayhmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyIGlkID0gJCh0aGlzKS5kYXRhKCdpZCcpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGZvciAodmFyIGkgPSAwOyBpIDwganNvbi5kYXRhLmxlbmd0aDsgaSsrKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmKGpzb24uZGF0YVtpXS5pZD09aWQpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdXNlcj1qc29uLmRhdGFbaV07XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBsaXN0Ym94LmZpbmQoJ2EubGlzdC1ncm91cC1pdGVtJykucmVtb3ZlQ2xhc3MoJ2FjdGl2ZScpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5hZGRDbGFzcygnYWN0aXZlJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbGlzdGJveC5odG1sKCc8c3BhbiBjbGFzcz1cImxpc3QtbG9hZGluZ1wiPjxpIGNsYXNzPVwiaW9uLW1kLXdhcm5pbmdcIj48L2k+IOayoeacieajgOe0ouWIsOS8muWRmDwvc3Bhbj4nKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBsaXN0Ym94Lmh0bWwoJzxzcGFuIGNsYXNzPVwidGV4dC1kYW5nZXJcIj48aSBjbGFzcz1cImlvbi1tZC13YXJuaW5nXCI+PC9pPiDliqDovb3lpLHotKU8L3NwYW4+Jyk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgKTtcclxuXHJcbiAgICAgICAgICAgICAgICB9KS50cmlnZ2VyKCdjbGljaycpO1xyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAnb25zdXJlJzpmdW5jdGlvbihib2R5KXtcclxuICAgICAgICAgICAgICAgIGlmKCF1c2VyKXtcclxuICAgICAgICAgICAgICAgICAgICB0b2FzdHIud2FybmluZygn5rKh5pyJ6YCJ5oup5Lya5ZGYIScpO1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIGlmKHR5cGVvZiBjYWxsYmFjaz09J2Z1bmN0aW9uJyl7XHJcbiAgICAgICAgICAgICAgICAgICAgdmFyIHJlc3VsdCA9IGNhbGxiYWNrKHVzZXIpO1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiByZXN1bHQ7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS5zaG93KCc8ZGl2IGNsYXNzPVwiaW5wdXQtZ3JvdXBcIj48aW5wdXQgdHlwZT1cInRleHRcIiBjbGFzcz1cImZvcm0tY29udHJvbCBzZWFyY2h0ZXh0XCIgbmFtZT1cImtleXdvcmRcIiBwbGFjZWhvbGRlcj1cIuagueaNruS8muWRmGlk5oiW5ZCN56ew77yM55S16K+d5p2l5pCc57SiXCIvPjxkaXYgY2xhc3M9XCJpbnB1dC1ncm91cC1hcHBlbmRcIj48YSBjbGFzcz1cImJ0biBidG4tb3V0bGluZS1zZWNvbmRhcnkgc2VhcmNoYnRuXCI+PGkgY2xhc3M9XCJpb24tbWQtc2VhcmNoXCI+PC9pPjwvYT48L2Rpdj48L2Rpdj48ZGl2IGNsYXNzPVwibGlzdC1ncm91cCBtdC0yXCI+PC9kaXY+Jywn6K+35pCc57Si5bm26YCJ5oup5Lya5ZGYJyk7XHJcbiAgICB9LFxyXG4gICAgcGlja0xvY2F0ZTpmdW5jdGlvbih0eXBlLCBjYWxsYmFjaywgbG9jYXRlKXtcclxuICAgICAgICB2YXIgc2V0dGVkTG9jYXRlPW51bGw7XHJcbiAgICAgICAgdmFyIGRsZz1uZXcgRGlhbG9nKHtcclxuICAgICAgICAgICAgJ3NpemUnOidsZycsXHJcbiAgICAgICAgICAgICdvbnNob3duJzpmdW5jdGlvbihib2R5KXtcclxuICAgICAgICAgICAgICAgIHZhciBidG49Ym9keS5maW5kKCcuc2VhcmNoYnRuJyk7XHJcbiAgICAgICAgICAgICAgICB2YXIgaW5wdXQ9Ym9keS5maW5kKCcuc2VhcmNodGV4dCcpO1xyXG4gICAgICAgICAgICAgICAgdmFyIG1hcGJveD1ib2R5LmZpbmQoJy5tYXAnKTtcclxuICAgICAgICAgICAgICAgIHZhciBtYXBpbmZvPWJvZHkuZmluZCgnLm1hcGluZm8nKTtcclxuICAgICAgICAgICAgICAgIG1hcGJveC5jc3MoJ2hlaWdodCcsJCh3aW5kb3cpLmhlaWdodCgpKi42KTtcclxuICAgICAgICAgICAgICAgIHZhciBpc2xvYWRpbmc9ZmFsc2U7XHJcbiAgICAgICAgICAgICAgICB2YXIgbWFwPUluaXRNYXAoJ3RlbmNlbnQnLG1hcGJveCxmdW5jdGlvbihhZGRyZXNzLGxvY2F0ZSl7XHJcbiAgICAgICAgICAgICAgICAgICAgbWFwaW5mby5odG1sKGFkZHJlc3MrJyZuYnNwOycrbG9jYXRlLmxuZysnLCcrbG9jYXRlLmxhdCk7XHJcbiAgICAgICAgICAgICAgICAgICAgc2V0dGVkTG9jYXRlPWxvY2F0ZTtcclxuICAgICAgICAgICAgICAgIH0sbG9jYXRlKTtcclxuICAgICAgICAgICAgICAgIGJ0bi5jbGljayhmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICAgICAgICAgIHZhciBzZWFyY2g9aW5wdXQudmFsKCk7XHJcbiAgICAgICAgICAgICAgICAgICAgbWFwLnNldExvY2F0ZShzZWFyY2gpO1xyXG4gICAgICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdvbnN1cmUnOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgaWYoIXNldHRlZExvY2F0ZSl7XHJcbiAgICAgICAgICAgICAgICAgICAgdG9hc3RyLndhcm5pbmcoJ+ayoeaciemAieaLqeS9jee9riEnKTtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICBpZih0eXBlb2YgY2FsbGJhY2s9PT0nZnVuY3Rpb24nKXtcclxuICAgICAgICAgICAgICAgICAgICB2YXIgcmVzdWx0ID0gY2FsbGJhY2soc2V0dGVkTG9jYXRlKTtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gcmVzdWx0O1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkuc2hvdygnPGRpdiBjbGFzcz1cImlucHV0LWdyb3VwXCI+PGlucHV0IHR5cGU9XCJ0ZXh0XCIgY2xhc3M9XCJmb3JtLWNvbnRyb2wgc2VhcmNodGV4dFwiIG5hbWU9XCJrZXl3b3JkXCIgcGxhY2Vob2xkZXI9XCLloavlhpnlnLDlnYDmo4DntKLkvY3nva5cIi8+PGRpdiBjbGFzcz1cImlucHV0LWdyb3VwLWFwcGVuZFwiPjxhIGNsYXNzPVwiYnRuIGJ0bi1vdXRsaW5lLXNlY29uZGFyeSBzZWFyY2hidG5cIj48aSBjbGFzcz1cImlvbi1tZC1zZWFyY2hcIj48L2k+PC9hPjwvZGl2PjwvZGl2PicgK1xyXG4gICAgICAgICAgICAnPGRpdiBjbGFzcz1cIm1hcCBtdC0yXCI+PC9kaXY+JyArXHJcbiAgICAgICAgICAgICc8ZGl2IGNsYXNzPVwibWFwaW5mbyBtdC0yIHRleHQtbXV0ZWRcIj7mnKrpgInmi6nkvY3nva48L2Rpdj4nLCfor7fpgInmi6nlnLDlm77kvY3nva4nKTtcclxuICAgIH1cclxufTtcclxuXHJcbmpRdWVyeShmdW5jdGlvbigkKXtcclxuXHJcbiAgICAvL+ebkeaOp+aMiemUrlxyXG4gICAgJChkb2N1bWVudCkub24oJ2tleWRvd24nLCBmdW5jdGlvbihlKXtcclxuICAgICAgICBpZighRGlhbG9nLmluc3RhbmNlKXJldHVybjtcclxuICAgICAgICB2YXIgZGxnPURpYWxvZy5pbnN0YW5jZTtcclxuICAgICAgICBpZiAoZS5rZXlDb2RlID09IDEzKSB7XHJcbiAgICAgICAgICAgIGRsZy5ib3guZmluZCgnLm1vZGFsLWZvb3RlciAuYnRuJykuZXEoZGxnLm9wdGlvbnMuZGVmYXVsdEJ0bikudHJpZ2dlcignY2xpY2snKTtcclxuICAgICAgICB9XHJcbiAgICAgICAgLy/pu5jorqTlt7Lnm5HlkKzlhbPpl61cclxuICAgICAgICAvKmlmIChlLmtleUNvZGUgPT0gMjcpIHtcclxuICAgICAgICAgc2VsZi5oaWRlKCk7XHJcbiAgICAgICAgIH0qL1xyXG4gICAgfSk7XHJcbn0pOyIsIlxyXG5qUXVlcnkuZXh0ZW5kKGpRdWVyeS5mbix7XHJcbiAgICB0YWdzOmZ1bmN0aW9uKG5tLG9udXBkYXRlKXtcclxuICAgICAgICB2YXIgZGF0YT1bXTtcclxuICAgICAgICB2YXIgdHBsPSc8c3BhbiBjbGFzcz1cImJhZGdlIGJhZGdlLWluZm9cIj57QGxhYmVsfTxpbnB1dCB0eXBlPVwiaGlkZGVuXCIgbmFtZT1cIicrbm0rJ1wiIHZhbHVlPVwie0BsYWJlbH1cIi8+PGJ1dHRvbiB0eXBlPVwiYnV0dG9uXCIgY2xhc3M9XCJjbG9zZVwiIGRhdGEtZGlzbWlzcz1cImFsZXJ0XCIgYXJpYS1sYWJlbD1cIkNsb3NlXCI+PHNwYW4gYXJpYS1oaWRkZW49XCJ0cnVlXCI+JnRpbWVzOzwvc3Bhbj48L2J1dHRvbj48L3NwYW4+JztcclxuICAgICAgICB2YXIgaXRlbT0kKHRoaXMpLnBhcmVudHMoJy5mb3JtLWNvbnRyb2wnKTtcclxuICAgICAgICB2YXIgbGFiZWxncm91cD0kKCc8c3BhbiBjbGFzcz1cImJhZGdlLWdyb3VwXCI+PC9zcGFuPicpO1xyXG4gICAgICAgIHZhciBpbnB1dD10aGlzO1xyXG4gICAgICAgIHRoaXMuYmVmb3JlKGxhYmVsZ3JvdXApO1xyXG4gICAgICAgIHRoaXMub24oJ2tleXVwJyxmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICB2YXIgdmFsPSQodGhpcykudmFsKCkucmVwbGFjZSgv77yML2csJywnKTtcclxuICAgICAgICAgICAgdmFyIHVwZGF0ZWQ9ZmFsc2U7XHJcbiAgICAgICAgICAgIGlmKHZhbCAmJiB2YWwuaW5kZXhPZignLCcpPi0xKXtcclxuICAgICAgICAgICAgICAgIHZhciB2YWxzPXZhbC5zcGxpdCgnLCcpO1xyXG4gICAgICAgICAgICAgICAgZm9yKHZhciBpPTA7aTx2YWxzLmxlbmd0aDtpKyspe1xyXG4gICAgICAgICAgICAgICAgICAgIHZhbHNbaV09dmFsc1tpXS5yZXBsYWNlKC9eXFxzfFxccyQvZywnJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgaWYodmFsc1tpXSAmJiBkYXRhLmluZGV4T2YodmFsc1tpXSk9PT0tMSl7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGRhdGEucHVzaCh2YWxzW2ldKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgbGFiZWxncm91cC5hcHBlbmQodHBsLmNvbXBpbGUoe2xhYmVsOnZhbHNbaV19KSk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHVwZGF0ZWQ9dHJ1ZTtcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICBpbnB1dC52YWwoJycpO1xyXG4gICAgICAgICAgICAgICAgaWYodXBkYXRlZCAmJiBvbnVwZGF0ZSlvbnVwZGF0ZShkYXRhKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLm9uKCdibHVyJyxmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICB2YXIgdmFsPSQodGhpcykudmFsKCk7XHJcbiAgICAgICAgICAgIGlmKHZhbCkge1xyXG4gICAgICAgICAgICAgICAgJCh0aGlzKS52YWwodmFsICsgJywnKS50cmlnZ2VyKCdrZXl1cCcpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkudHJpZ2dlcignYmx1cicpO1xyXG4gICAgICAgIGxhYmVsZ3JvdXAub24oJ2NsaWNrJywnLmNsb3NlJyxmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICB2YXIgdGFnPSQodGhpcykucGFyZW50cygnLmJhZGdlJykuZmluZCgnaW5wdXQnKS52YWwoKTtcclxuICAgICAgICAgICAgdmFyIGlkPWRhdGEuaW5kZXhPZih0YWcpO1xyXG4gICAgICAgICAgICBpZihpZClkYXRhLnNwbGljZShpZCwxKTtcclxuICAgICAgICAgICAgJCh0aGlzKS5wYXJlbnRzKCcuYmFkZ2UnKS5yZW1vdmUoKTtcclxuICAgICAgICAgICAgaWYob251cGRhdGUpb251cGRhdGUoZGF0YSk7XHJcbiAgICAgICAgfSk7XHJcbiAgICAgICAgaXRlbS5jbGljayhmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICBpbnB1dC5mb2N1cygpO1xyXG4gICAgICAgIH0pO1xyXG4gICAgfVxyXG59KTsiLCIvL+aXpeacn+e7hOS7tlxyXG5pZigkLmZuLmRhdGV0aW1lcGlja2VyKSB7XHJcbiAgICB2YXIgdG9vbHRpcHM9IHtcclxuICAgICAgICB0b2RheTogJ+WumuS9jeW9k+WJjeaXpeacnycsXHJcbiAgICAgICAgY2xlYXI6ICfmuIXpmaTlt7LpgInml6XmnJ8nLFxyXG4gICAgICAgIGNsb3NlOiAn5YWz6Zet6YCJ5oup5ZmoJyxcclxuICAgICAgICBzZWxlY3RNb250aDogJ+mAieaLqeaciOS7vScsXHJcbiAgICAgICAgcHJldk1vbnRoOiAn5LiK5Liq5pyIJyxcclxuICAgICAgICBuZXh0TW9udGg6ICfkuIvkuKrmnIgnLFxyXG4gICAgICAgIHNlbGVjdFllYXI6ICfpgInmi6nlubTku70nLFxyXG4gICAgICAgIHByZXZZZWFyOiAn5LiK5LiA5bm0JyxcclxuICAgICAgICBuZXh0WWVhcjogJ+S4i+S4gOW5tCcsXHJcbiAgICAgICAgc2VsZWN0RGVjYWRlOiAn6YCJ5oup5bm05Lu95Yy66Ze0JyxcclxuICAgICAgICBwcmV2RGVjYWRlOiAn5LiK5LiA5Yy66Ze0JyxcclxuICAgICAgICBuZXh0RGVjYWRlOiAn5LiL5LiA5Yy66Ze0JyxcclxuICAgICAgICBwcmV2Q2VudHVyeTogJ+S4iuS4quS4lue6qicsXHJcbiAgICAgICAgbmV4dENlbnR1cnk6ICfkuIvkuKrkuJbnuqonXHJcbiAgICB9O1xyXG4gICAgdmFyIGljb25zPXtcclxuICAgICAgICB0aW1lOiAnaW9uLW1kLXRpbWUnLFxyXG4gICAgICAgIGRhdGU6ICdpb24tbWQtY2FsZW5kYXInLFxyXG4gICAgICAgIHVwOiAnaW9uLW1kLWFycm93LWRyb3B1cCcsXHJcbiAgICAgICAgZG93bjogJ2lvbi1tZC1hcnJvdy1kcm9wZG93bicsXHJcbiAgICAgICAgcHJldmlvdXM6ICdpb24tbWQtYXJyb3ctZHJvcGxlZnQnLFxyXG4gICAgICAgIG5leHQ6ICdpb24tbWQtYXJyb3ctZHJvcHJpZ2h0JyxcclxuICAgICAgICB0b2RheTogJ2lvbi1tZC10b2RheScsXHJcbiAgICAgICAgY2xlYXI6ICdpb24tbWQtdHJhc2gnLFxyXG4gICAgICAgIGNsb3NlOiAnaW9uLW1kLWNsb3NlJ1xyXG4gICAgfTtcclxuICAgICQoJy5kYXRlcGlja2VyJykuZWFjaChmdW5jdGlvbigpe1xyXG4gICAgICAgIHZhciBjb25maWc9JC5leHRlbmQoe1xyXG4gICAgICAgICAgICBpY29uczppY29ucyxcclxuICAgICAgICAgICAgdG9vbHRpcHM6dG9vbHRpcHMsXHJcbiAgICAgICAgICAgIGZvcm1hdDogJ1lZWVktTU0tREQnLFxyXG4gICAgICAgICAgICBsb2NhbGU6ICd6aC1jbicsXHJcbiAgICAgICAgICAgIHNob3dDbGVhcjp0cnVlLFxyXG4gICAgICAgICAgICBzaG93VG9kYXlCdXR0b246dHJ1ZSxcclxuICAgICAgICAgICAgc2hvd0Nsb3NlOnRydWUsXHJcbiAgICAgICAgICAgIGtlZXBJbnZhbGlkOnRydWVcclxuICAgICAgICB9LCQodGhpcykuZGF0YSgpKTtcclxuICAgICAgICAkKHRoaXMpLmRhdGV0aW1lcGlja2VyKGNvbmZpZyk7XHJcbiAgICB9KTtcclxuXHJcbiAgICAkKCcuZGF0ZS1yYW5nZScpLmVhY2goZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHZhciBmcm9tID0gJCh0aGlzKS5maW5kKCdbbmFtZT1mcm9tZGF0ZV0sLmZyb21kYXRlJyksIHRvID0gJCh0aGlzKS5maW5kKCdbbmFtZT10b2RhdGVdLC50b2RhdGUnKTtcclxuICAgICAgICB2YXIgb3B0aW9ucyA9ICQuZXh0ZW5kKHtcclxuICAgICAgICAgICAgaWNvbnM6aWNvbnMsXHJcbiAgICAgICAgICAgIHRvb2x0aXBzOnRvb2x0aXBzLFxyXG4gICAgICAgICAgICBmb3JtYXQ6ICdZWVlZLU1NLUREJyxcclxuICAgICAgICAgICAgbG9jYWxlOid6aC1jbicsXHJcbiAgICAgICAgICAgIHNob3dDbGVhcjp0cnVlLFxyXG4gICAgICAgICAgICBzaG93VG9kYXlCdXR0b246dHJ1ZSxcclxuICAgICAgICAgICAgc2hvd0Nsb3NlOnRydWUsXHJcbiAgICAgICAgICAgIGtlZXBJbnZhbGlkOnRydWVcclxuICAgICAgICB9LCQodGhpcykuZGF0YSgpKTtcclxuICAgICAgICBmcm9tLmRhdGV0aW1lcGlja2VyKG9wdGlvbnMpLm9uKCdkcC5jaGFuZ2UnLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIGlmIChmcm9tLnZhbCgpKSB7XHJcbiAgICAgICAgICAgICAgICB0by5kYXRhKCdEYXRlVGltZVBpY2tlcicpLm1pbkRhdGUoZnJvbS52YWwoKSk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KTtcclxuICAgICAgICB0by5kYXRldGltZXBpY2tlcihvcHRpb25zKS5vbignZHAuY2hhbmdlJywgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICBpZiAodG8udmFsKCkpIHtcclxuICAgICAgICAgICAgICAgIGZyb20uZGF0YSgnRGF0ZVRpbWVQaWNrZXInKS5tYXhEYXRlKHRvLnZhbCgpKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pO1xyXG4gICAgfSk7XHJcbn0iLCJmdW5jdGlvbiBzZXROYXYobmF2KSB7XHJcbiAgICB2YXIgaXRlbXM9JCgnLm1haW4tbmF2IC5uYXYtaXRlbScpO1xyXG4gICAgdmFyIGZpbmRlZD1mYWxzZTtcclxuICAgIGZvcih2YXIgaT0wO2k8aXRlbXMubGVuZ3RoO2krKyl7XHJcbiAgICAgICAgaWYoaXRlbXMuZXEoaSkuZGF0YSgnbW9kZWwnKT09PW5hdil7XHJcbiAgICAgICAgICAgIGl0ZW1zLmVxKGkpLmFkZENsYXNzKCdhY3RpdmUnKTtcclxuICAgICAgICAgICAgZmluZGVkPXRydWU7XHJcbiAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgIH1cclxuICAgIH1cclxuICAgIGlmKCFmaW5kZWQgJiYgbmF2LmluZGV4T2YoJy0nKT4wKXtcclxuICAgICAgICBuYXY9bmF2LnN1YnN0cigwLG5hdi5sYXN0SW5kZXhPZignLScpKTtcclxuICAgICAgICBzZXROYXYobmF2KTtcclxuICAgIH1cclxufVxyXG5cclxualF1ZXJ5KGZ1bmN0aW9uKCQpe1xyXG5cclxufSk7Il19
