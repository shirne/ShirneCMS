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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNvbW1vbi5qcyIsInRlbXBsYXRlLmpzIiwiZGlhbG9nLmpzIiwianF1ZXJ5LnRhZy5qcyIsImRhdGV0aW1lLmluaXQuanMiLCJmcm9udC5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUNqRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDOURBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ3BXQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQzFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDekVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBIiwiZmlsZSI6ImZyb250LmpzIiwic291cmNlc0NvbnRlbnQiOlsiZnVuY3Rpb24gZGVsKG9iaixtc2cpIHtcclxuICAgIGRpYWxvZy5jb25maXJtKG1zZyxmdW5jdGlvbigpe1xyXG4gICAgICAgIGxvY2F0aW9uLmhyZWY9JChvYmopLmF0dHIoJ2hyZWYnKTtcclxuICAgIH0pO1xyXG4gICAgcmV0dXJuIGZhbHNlO1xyXG59XHJcblxyXG5mdW5jdGlvbiBsYW5nKGtleSkge1xyXG4gICAgaWYod2luZG93Lmxhbmd1YWdlICYmIHdpbmRvdy5sYW5ndWFnZVtrZXldKXtcclxuICAgICAgICByZXR1cm4gd2luZG93Lmxhbmd1YWdlW2tleV07XHJcbiAgICB9XHJcbiAgICByZXR1cm4ga2V5O1xyXG59XHJcblxyXG5mdW5jdGlvbiByYW5kb21TdHJpbmcobGVuLCBjaGFyU2V0KSB7XHJcbiAgICBjaGFyU2V0ID0gY2hhclNldCB8fCAnQUJDREVGR0hJSktMTU5PUFFSU1RVVldYWVphYmNkZWZnaGlqa2xtbm9wcXJzdHV2d3h5ejAxMjM0NTY3ODknO1xyXG4gICAgdmFyIHN0ciA9ICcnLGFsbExlbj1jaGFyU2V0Lmxlbmd0aDtcclxuICAgIGZvciAodmFyIGkgPSAwOyBpIDwgbGVuOyBpKyspIHtcclxuICAgICAgICB2YXIgcmFuZG9tUG96ID0gTWF0aC5mbG9vcihNYXRoLnJhbmRvbSgpICogYWxsTGVuKTtcclxuICAgICAgICBzdHIgKz0gY2hhclNldC5zdWJzdHJpbmcocmFuZG9tUG96LHJhbmRvbVBveisxKTtcclxuICAgIH1cclxuICAgIHJldHVybiBzdHI7XHJcbn1cclxuXHJcbmZ1bmN0aW9uIGNvcHlfb2JqKGFycil7XHJcbiAgICByZXR1cm4gSlNPTi5wYXJzZShKU09OLnN0cmluZ2lmeShhcnIpKTtcclxufVxyXG5cclxuZnVuY3Rpb24gaXNPYmplY3RWYWx1ZUVxdWFsKGEsIGIpIHtcclxuICAgIGlmKCFhICYmICFiKXJldHVybiB0cnVlO1xyXG4gICAgaWYoIWEgfHwgIWIpcmV0dXJuIGZhbHNlO1xyXG5cclxuICAgIC8vIE9mIGNvdXJzZSwgd2UgY2FuIGRvIGl0IHVzZSBmb3IgaW5cclxuICAgIC8vIENyZWF0ZSBhcnJheXMgb2YgcHJvcGVydHkgbmFtZXNcclxuICAgIHZhciBhUHJvcHMgPSBPYmplY3QuZ2V0T3duUHJvcGVydHlOYW1lcyhhKTtcclxuICAgIHZhciBiUHJvcHMgPSBPYmplY3QuZ2V0T3duUHJvcGVydHlOYW1lcyhiKTtcclxuXHJcbiAgICAvLyBJZiBudW1iZXIgb2YgcHJvcGVydGllcyBpcyBkaWZmZXJlbnQsXHJcbiAgICAvLyBvYmplY3RzIGFyZSBub3QgZXF1aXZhbGVudFxyXG4gICAgaWYgKGFQcm9wcy5sZW5ndGggIT0gYlByb3BzLmxlbmd0aCkge1xyXG4gICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgIH1cclxuXHJcbiAgICBmb3IgKHZhciBpID0gMDsgaSA8IGFQcm9wcy5sZW5ndGg7IGkrKykge1xyXG4gICAgICAgIHZhciBwcm9wTmFtZSA9IGFQcm9wc1tpXTtcclxuXHJcbiAgICAgICAgLy8gSWYgdmFsdWVzIG9mIHNhbWUgcHJvcGVydHkgYXJlIG5vdCBlcXVhbCxcclxuICAgICAgICAvLyBvYmplY3RzIGFyZSBub3QgZXF1aXZhbGVudFxyXG4gICAgICAgIGlmIChhW3Byb3BOYW1lXSAhPT0gYltwcm9wTmFtZV0pIHtcclxuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgIH1cclxuICAgIH1cclxuXHJcbiAgICAvLyBJZiB3ZSBtYWRlIGl0IHRoaXMgZmFyLCBvYmplY3RzXHJcbiAgICAvLyBhcmUgY29uc2lkZXJlZCBlcXVpdmFsZW50XHJcbiAgICByZXR1cm4gdHJ1ZTtcclxufVxyXG5cclxuZnVuY3Rpb24gYXJyYXlfY29tYmluZShhLGIpIHtcclxuICAgIHZhciBvYmo9e307XHJcbiAgICBmb3IodmFyIGk9MDtpPGEubGVuZ3RoO2krKyl7XHJcbiAgICAgICAgaWYoYi5sZW5ndGg8aSsxKWJyZWFrO1xyXG4gICAgICAgIG9ialthW2ldXT1iW2ldO1xyXG4gICAgfVxyXG4gICAgcmV0dXJuIG9iajtcclxufSIsIlxyXG5OdW1iZXIucHJvdG90eXBlLmZvcm1hdD1mdW5jdGlvbihmaXgpe1xyXG4gICAgaWYoZml4PT09dW5kZWZpbmVkKWZpeD0yO1xyXG4gICAgdmFyIG51bT10aGlzLnRvRml4ZWQoZml4KTtcclxuICAgIHZhciB6PW51bS5zcGxpdCgnLicpO1xyXG4gICAgdmFyIGZvcm1hdD1bXSxmPXpbMF0uc3BsaXQoJycpLGw9Zi5sZW5ndGg7XHJcbiAgICBmb3IodmFyIGk9MDtpPGw7aSsrKXtcclxuICAgICAgICBpZihpPjAgJiYgaSAlIDM9PTApe1xyXG4gICAgICAgICAgICBmb3JtYXQudW5zaGlmdCgnLCcpO1xyXG4gICAgICAgIH1cclxuICAgICAgICBmb3JtYXQudW5zaGlmdChmW2wtaS0xXSk7XHJcbiAgICB9XHJcbiAgICByZXR1cm4gZm9ybWF0LmpvaW4oJycpKyh6Lmxlbmd0aD09Mj8nLicrelsxXTonJyk7XHJcbn07XHJcblN0cmluZy5wcm90b3R5cGUuY29tcGlsZT1mdW5jdGlvbihkYXRhLGxpc3Qpe1xyXG5cclxuICAgIGlmKGxpc3Qpe1xyXG4gICAgICAgIHZhciB0ZW1wcz1bXTtcclxuICAgICAgICBmb3IodmFyIGkgaW4gZGF0YSl7XHJcbiAgICAgICAgICAgIHRlbXBzLnB1c2godGhpcy5jb21waWxlKGRhdGFbaV0pKTtcclxuICAgICAgICB9XHJcbiAgICAgICAgcmV0dXJuIHRlbXBzLmpvaW4oXCJcXG5cIik7XHJcbiAgICB9ZWxzZXtcclxuICAgICAgICByZXR1cm4gdGhpcy5yZXBsYWNlKC9cXHtAKFtcXHdcXGRcXC5dKykoPzpcXHwoW1xcd1xcZF0rKSg/Olxccyo9XFxzKihbXFx3XFxkLFxccyNdKykpPyk/XFx9L2csZnVuY3Rpb24oYWxsLG0xLGZ1bmMsYXJncyl7XHJcblxyXG4gICAgICAgICAgICBpZihtMS5pbmRleE9mKCcuJyk+MCl7XHJcbiAgICAgICAgICAgICAgICB2YXIga2V5cz1tMS5zcGxpdCgnLicpLHZhbD1kYXRhO1xyXG4gICAgICAgICAgICAgICAgZm9yKHZhciBpPTA7aTxrZXlzLmxlbmd0aDtpKyspe1xyXG4gICAgICAgICAgICAgICAgICAgIGlmKHZhbFtrZXlzW2ldXSE9PXVuZGVmaW5lZCl7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhbD12YWxba2V5c1tpXV07XHJcbiAgICAgICAgICAgICAgICAgICAgfWVsc2V7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhbCA9ICcnO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICByZXR1cm4gY2FsbGZ1bmModmFsLGZ1bmMsYXJncyk7XHJcbiAgICAgICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICAgICAgcmV0dXJuIGRhdGFbbTFdIT09dW5kZWZpbmVkP2NhbGxmdW5jKGRhdGFbbTFdLGZ1bmMsYXJncyxkYXRhKTonJztcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pO1xyXG4gICAgfVxyXG59O1xyXG5cclxuZnVuY3Rpb24gY2FsbGZ1bmModmFsLGZ1bmMsYXJncyx0aGlzb2JqKXtcclxuICAgIGlmKCFhcmdzKXtcclxuICAgICAgICBhcmdzPVt2YWxdO1xyXG4gICAgfWVsc2V7XHJcbiAgICAgICAgaWYodHlwZW9mIGFyZ3M9PT0nc3RyaW5nJylhcmdzPWFyZ3Muc3BsaXQoJywnKTtcclxuICAgICAgICB2YXIgYXJnaWR4PWFyZ3MuaW5kZXhPZignIyMjJyk7XHJcbiAgICAgICAgaWYoYXJnaWR4Pj0wKXtcclxuICAgICAgICAgICAgYXJnc1thcmdpZHhdPXZhbDtcclxuICAgICAgICB9ZWxzZXtcclxuICAgICAgICAgICAgYXJncz1bdmFsXS5jb25jYXQoYXJncyk7XHJcbiAgICAgICAgfVxyXG4gICAgfVxyXG4gICAgLy9jb25zb2xlLmxvZyhhcmdzKTtcclxuICAgIHJldHVybiB3aW5kb3dbZnVuY10/d2luZG93W2Z1bmNdLmFwcGx5KHRoaXNvYmosYXJncyk6dmFsO1xyXG59XHJcblxyXG5mdW5jdGlvbiBpaWYodixtMSxtMil7XHJcbiAgICBpZih2PT09JzAnKXY9MDtcclxuICAgIHJldHVybiB2P20xOm0yO1xyXG59IiwiXHJcbnZhciBkaWFsb2dUcGw9JzxkaXYgY2xhc3M9XCJtb2RhbCBmYWRlXCIgaWQ9XCJ7QGlkfVwiIHRhYmluZGV4PVwiLTFcIiByb2xlPVwiZGlhbG9nXCIgYXJpYS1sYWJlbGxlZGJ5PVwie0BpZH1MYWJlbFwiIGFyaWEtaGlkZGVuPVwidHJ1ZVwiPlxcbicgK1xyXG4gICAgJyAgICA8ZGl2IGNsYXNzPVwibW9kYWwtZGlhbG9nXCI+XFxuJyArXHJcbiAgICAnICAgICAgICA8ZGl2IGNsYXNzPVwibW9kYWwtY29udGVudFwiPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJtb2RhbC1oZWFkZXJcIj5cXG4nICtcclxuICAgICcgICAgICAgICAgICAgICAgPGg0IGNsYXNzPVwibW9kYWwtdGl0bGVcIiBpZD1cIntAaWR9TGFiZWxcIj48L2g0PlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgICAgICA8YnV0dG9uIHR5cGU9XCJidXR0b25cIiBjbGFzcz1cImNsb3NlXCIgZGF0YS1kaXNtaXNzPVwibW9kYWxcIj5cXG4nICtcclxuICAgICcgICAgICAgICAgICAgICAgICAgIDxzcGFuIGFyaWEtaGlkZGVuPVwidHJ1ZVwiPiZ0aW1lczs8L3NwYW4+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgICAgICAgICA8c3BhbiBjbGFzcz1cInNyLW9ubHlcIj5DbG9zZTwvc3Bhbj5cXG4nICtcclxuICAgICcgICAgICAgICAgICAgICAgPC9idXR0b24+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgPC9kaXY+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgPGRpdiBjbGFzcz1cIm1vZGFsLWJvZHlcIj5cXG4nICtcclxuICAgICcgICAgICAgICAgICA8L2Rpdj5cXG4nICtcclxuICAgICcgICAgICAgICAgICA8ZGl2IGNsYXNzPVwibW9kYWwtZm9vdGVyXCI+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgICAgIDxuYXYgY2xhc3M9XCJuYXYgbmF2LWZpbGxcIj48L25hdj5cXG4nICtcclxuICAgICcgICAgICAgICAgICA8L2Rpdj5cXG4nICtcclxuICAgICcgICAgICAgIDwvZGl2PlxcbicgK1xyXG4gICAgJyAgICA8L2Rpdj5cXG4nICtcclxuICAgICc8L2Rpdj4nO1xyXG52YXIgZGlhbG9nSWR4PTA7XHJcbmZ1bmN0aW9uIERpYWxvZyhvcHRzKXtcclxuICAgIGlmKCFvcHRzKW9wdHM9e307XHJcbiAgICAvL+WkhOeQhuaMiemSrlxyXG4gICAgaWYob3B0cy5idG5zIT09dW5kZWZpbmVkKSB7XHJcbiAgICAgICAgaWYgKHR5cGVvZihvcHRzLmJ0bnMpID09ICdzdHJpbmcnKSB7XHJcbiAgICAgICAgICAgIG9wdHMuYnRucyA9IFtvcHRzLmJ0bnNdO1xyXG4gICAgICAgIH1cclxuICAgICAgICB2YXIgZGZ0PS0xO1xyXG4gICAgICAgIGZvciAodmFyIGkgPSAwOyBpIDwgb3B0cy5idG5zLmxlbmd0aDsgaSsrKSB7XHJcbiAgICAgICAgICAgIGlmKHR5cGVvZihvcHRzLmJ0bnNbaV0pPT0nc3RyaW5nJyl7XHJcbiAgICAgICAgICAgICAgICBvcHRzLmJ0bnNbaV09eyd0ZXh0JzpvcHRzLmJ0bnNbaV19O1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIGlmKG9wdHMuYnRuc1tpXS5pc2RlZmF1bHQpe1xyXG4gICAgICAgICAgICAgICAgZGZ0PWk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9XHJcbiAgICAgICAgaWYoZGZ0PDApe1xyXG4gICAgICAgICAgICBkZnQ9b3B0cy5idG5zLmxlbmd0aC0xO1xyXG4gICAgICAgICAgICBvcHRzLmJ0bnNbZGZ0XS5pc2RlZmF1bHQ9dHJ1ZTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIGlmKCFvcHRzLmJ0bnNbZGZ0XVsndHlwZSddKXtcclxuICAgICAgICAgICAgb3B0cy5idG5zW2RmdF1bJ3R5cGUnXT0ncHJpbWFyeSc7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIG9wdHMuZGVmYXVsdEJ0bj1kZnQ7XHJcbiAgICB9XHJcblxyXG4gICAgdGhpcy5vcHRpb25zPSQuZXh0ZW5kKHtcclxuICAgICAgICAnaWQnOidkbGdNb2RhbCcrZGlhbG9nSWR4KyssXHJcbiAgICAgICAgJ3NpemUnOicnLFxyXG4gICAgICAgICdidG5zJzpbXHJcbiAgICAgICAgICAgIHsndGV4dCc6J+WPlua2iCcsJ3R5cGUnOidzZWNvbmRhcnknfSxcclxuICAgICAgICAgICAgeyd0ZXh0Jzon56Gu5a6aJywnaXNkZWZhdWx0Jzp0cnVlLCd0eXBlJzoncHJpbWFyeSd9XHJcbiAgICAgICAgXSxcclxuICAgICAgICAnZGVmYXVsdEJ0bic6MSxcclxuICAgICAgICAnb25zdXJlJzpudWxsLFxyXG4gICAgICAgICdvbnNob3cnOm51bGwsXHJcbiAgICAgICAgJ29uc2hvd24nOm51bGwsXHJcbiAgICAgICAgJ29uaGlkZSc6bnVsbCxcclxuICAgICAgICAnb25oaWRkZW4nOm51bGxcclxuICAgIH0sb3B0cyk7XHJcblxyXG4gICAgdGhpcy5ib3g9JCh0aGlzLm9wdGlvbnMuaWQpO1xyXG59XHJcbkRpYWxvZy5wcm90b3R5cGUuZ2VuZXJCdG49ZnVuY3Rpb24ob3B0LGlkeCl7XHJcbiAgICBpZihvcHRbJ3R5cGUnXSlvcHRbJ2NsYXNzJ109J2J0bi1vdXRsaW5lLScrb3B0Wyd0eXBlJ107XHJcbiAgICByZXR1cm4gJzxhIGhyZWY9XCJqYXZhc2NyaXB0OlwiIGNsYXNzPVwibmF2LWl0ZW0gYnRuICcrKG9wdFsnY2xhc3MnXT9vcHRbJ2NsYXNzJ106J2J0bi1vdXRsaW5lLXNlY29uZGFyeScpKydcIiBkYXRhLWluZGV4PVwiJytpZHgrJ1wiPicrb3B0LnRleHQrJzwvYT4nO1xyXG59O1xyXG5EaWFsb2cucHJvdG90eXBlLnNob3c9ZnVuY3Rpb24oaHRtbCx0aXRsZSl7XHJcbiAgICB0aGlzLmJveD0kKCcjJyt0aGlzLm9wdGlvbnMuaWQpO1xyXG4gICAgaWYoIXRpdGxlKXRpdGxlPSfns7vnu5/mj5DnpLonO1xyXG4gICAgaWYodGhpcy5ib3gubGVuZ3RoPDEpIHtcclxuICAgICAgICAkKGRvY3VtZW50LmJvZHkpLmFwcGVuZChkaWFsb2dUcGwuY29tcGlsZSh7J2lkJzogdGhpcy5vcHRpb25zLmlkfSkpO1xyXG4gICAgICAgIHRoaXMuYm94PSQoJyMnK3RoaXMub3B0aW9ucy5pZCk7XHJcbiAgICB9ZWxzZXtcclxuICAgICAgICB0aGlzLmJveC51bmJpbmQoKTtcclxuICAgIH1cclxuXHJcbiAgICAvL3RoaXMuYm94LmZpbmQoJy5tb2RhbC1mb290ZXIgLmJ0bi1wcmltYXJ5JykudW5iaW5kKCk7XHJcbiAgICB2YXIgc2VsZj10aGlzO1xyXG4gICAgRGlhbG9nLmluc3RhbmNlPXNlbGY7XHJcblxyXG4gICAgLy/nlJ/miJDmjInpkq5cclxuICAgIHZhciBidG5zPVtdO1xyXG4gICAgZm9yKHZhciBpPTA7aTx0aGlzLm9wdGlvbnMuYnRucy5sZW5ndGg7aSsrKXtcclxuICAgICAgICBidG5zLnB1c2godGhpcy5nZW5lckJ0bih0aGlzLm9wdGlvbnMuYnRuc1tpXSxpKSk7XHJcbiAgICB9XHJcbiAgICB0aGlzLmJveC5maW5kKCcubW9kYWwtZm9vdGVyIC5uYXYnKS5odG1sKGJ0bnMuam9pbignXFxuJykpO1xyXG5cclxuICAgIHZhciBkaWFsb2c9dGhpcy5ib3guZmluZCgnLm1vZGFsLWRpYWxvZycpO1xyXG4gICAgZGlhbG9nLnJlbW92ZUNsYXNzKCdtb2RhbC1zbScpLnJlbW92ZUNsYXNzKCdtb2RhbC1sZycpO1xyXG4gICAgaWYodGhpcy5vcHRpb25zLnNpemU9PSdzbScpIHtcclxuICAgICAgICBkaWFsb2cuYWRkQ2xhc3MoJ21vZGFsLXNtJyk7XHJcbiAgICB9ZWxzZSBpZih0aGlzLm9wdGlvbnMuc2l6ZT09J2xnJykge1xyXG4gICAgICAgIGRpYWxvZy5hZGRDbGFzcygnbW9kYWwtbGcnKTtcclxuICAgIH1cclxuICAgIHRoaXMuYm94LmZpbmQoJy5tb2RhbC10aXRsZScpLnRleHQodGl0bGUpO1xyXG5cclxuICAgIHZhciBib2R5PXRoaXMuYm94LmZpbmQoJy5tb2RhbC1ib2R5Jyk7XHJcbiAgICBib2R5Lmh0bWwoaHRtbCk7XHJcbiAgICB0aGlzLmJveC5vbignaGlkZS5icy5tb2RhbCcsZnVuY3Rpb24oKXtcclxuICAgICAgICBpZihzZWxmLm9wdGlvbnMub25oaWRlKXtcclxuICAgICAgICAgICAgc2VsZi5vcHRpb25zLm9uaGlkZShib2R5LHNlbGYuYm94KTtcclxuICAgICAgICB9XHJcbiAgICAgICAgRGlhbG9nLmluc3RhbmNlPW51bGw7XHJcbiAgICB9KTtcclxuICAgIHRoaXMuYm94Lm9uKCdoaWRkZW4uYnMubW9kYWwnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgaWYoc2VsZi5vcHRpb25zLm9uaGlkZGVuKXtcclxuICAgICAgICAgICAgc2VsZi5vcHRpb25zLm9uaGlkZGVuKGJvZHksc2VsZi5ib3gpO1xyXG4gICAgICAgIH1cclxuICAgICAgICBzZWxmLmJveC5yZW1vdmUoKTtcclxuICAgIH0pO1xyXG4gICAgdGhpcy5ib3gub24oJ3Nob3cuYnMubW9kYWwnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgaWYoc2VsZi5vcHRpb25zLm9uc2hvdyl7XHJcbiAgICAgICAgICAgIHNlbGYub3B0aW9ucy5vbnNob3coYm9keSxzZWxmLmJveCk7XHJcbiAgICAgICAgfVxyXG4gICAgfSk7XHJcbiAgICB0aGlzLmJveC5vbignc2hvd24uYnMubW9kYWwnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgaWYoc2VsZi5vcHRpb25zLm9uc2hvd24pe1xyXG4gICAgICAgICAgICBzZWxmLm9wdGlvbnMub25zaG93bihib2R5LHNlbGYuYm94KTtcclxuICAgICAgICB9XHJcbiAgICB9KTtcclxuICAgIHRoaXMuYm94LmZpbmQoJy5tb2RhbC1mb290ZXIgLmJ0bicpLmNsaWNrKGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgdmFyIHJlc3VsdD10cnVlLGlkeD0kKHRoaXMpLmRhdGEoJ2luZGV4Jyk7XHJcbiAgICAgICAgaWYoc2VsZi5vcHRpb25zLmJ0bnNbaWR4XS5jbGljayl7XHJcbiAgICAgICAgICAgIHJlc3VsdCA9IHNlbGYub3B0aW9ucy5idG5zW2lkeF0uY2xpY2suYXBwbHkodGhpcyxbYm9keSwgc2VsZi5ib3hdKTtcclxuICAgICAgICB9XHJcbiAgICAgICAgaWYoaWR4PT1zZWxmLm9wdGlvbnMuZGVmYXVsdEJ0bikge1xyXG4gICAgICAgICAgICBpZiAoc2VsZi5vcHRpb25zLm9uc3VyZSkge1xyXG4gICAgICAgICAgICAgICAgcmVzdWx0ID0gc2VsZi5vcHRpb25zLm9uc3VyZS5hcHBseSh0aGlzLFtib2R5LCBzZWxmLmJveF0pO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfVxyXG4gICAgICAgIGlmKHJlc3VsdCE9PWZhbHNlKXtcclxuICAgICAgICAgICAgc2VsZi5ib3gubW9kYWwoJ2hpZGUnKTtcclxuICAgICAgICB9XHJcbiAgICB9KTtcclxuICAgIHRoaXMuYm94Lm1vZGFsKCdzaG93Jyk7XHJcbiAgICByZXR1cm4gdGhpcztcclxufTtcclxuRGlhbG9nLnByb3RvdHlwZS5oaWRlPURpYWxvZy5wcm90b3R5cGUuY2xvc2U9ZnVuY3Rpb24oKXtcclxuICAgIHRoaXMuYm94Lm1vZGFsKCdoaWRlJyk7XHJcbiAgICByZXR1cm4gdGhpcztcclxufTtcclxuXHJcbnZhciBkaWFsb2c9e1xyXG4gICAgYWxlcnQ6ZnVuY3Rpb24obWVzc2FnZSxjYWxsYmFjayx0aXRsZSl7XHJcbiAgICAgICAgdmFyIGNhbGxlZD1mYWxzZTtcclxuICAgICAgICB2YXIgaXNjYWxsYmFjaz10eXBlb2YgY2FsbGJhY2s9PSdmdW5jdGlvbic7XHJcbiAgICAgICAgcmV0dXJuIG5ldyBEaWFsb2coe1xyXG4gICAgICAgICAgICBidG5zOifnoa7lrponLFxyXG4gICAgICAgICAgICBvbnN1cmU6ZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgICAgIGlmKGlzY2FsbGJhY2spe1xyXG4gICAgICAgICAgICAgICAgICAgIGNhbGxlZD10cnVlO1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBjYWxsYmFjayh0cnVlKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgb25oaWRlOmZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgICAgICBpZighY2FsbGVkICYmIGlzY2FsbGJhY2spe1xyXG4gICAgICAgICAgICAgICAgICAgIGNhbGxiYWNrKGZhbHNlKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLnNob3cobWVzc2FnZSx0aXRsZSk7XHJcbiAgICB9LFxyXG4gICAgY29uZmlybTpmdW5jdGlvbihtZXNzYWdlLGNvbmZpcm0sY2FuY2VsKXtcclxuICAgICAgICB2YXIgY2FsbGVkPWZhbHNlO1xyXG4gICAgICAgIHJldHVybiBuZXcgRGlhbG9nKHtcclxuICAgICAgICAgICAgJ29uc3VyZSc6ZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgICAgIGlmKHR5cGVvZiBjb25maXJtPT0nZnVuY3Rpb24nKXtcclxuICAgICAgICAgICAgICAgICAgICBjYWxsZWQ9dHJ1ZTtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gY29uZmlybSgpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAnb25oaWRlJzpmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgICAgICBpZihjYWxsZWQ9ZmFsc2UgJiYgdHlwZW9mIGNhbmNlbD09J2Z1bmN0aW9uJyl7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGNhbmNlbCgpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkuc2hvdyhtZXNzYWdlKTtcclxuICAgIH0sXHJcbiAgICBwcm9tcHQ6ZnVuY3Rpb24obWVzc2FnZSxjYWxsYmFjayxjYW5jZWwpe1xyXG4gICAgICAgIHZhciBjYWxsZWQ9ZmFsc2U7XHJcbiAgICAgICAgdmFyIGNvbnRlbnRIdG1sPSc8ZGl2IGNsYXNzPVwiZm9ybS1ncm91cFwiPntAaW5wdXR9PC9kaXY+JztcclxuICAgICAgICB2YXIgdGl0bGU9J+ivt+i+k+WFpeS/oeaBryc7XHJcbiAgICAgICAgaWYodHlwZW9mIG1lc3NhZ2U9PSdzdHJpbmcnKXtcclxuICAgICAgICAgICAgdGl0bGU9bWVzc2FnZTtcclxuICAgICAgICB9ZWxzZXtcclxuICAgICAgICAgICAgdGl0bGU9bWVzc2FnZS50aXRsZTtcclxuICAgICAgICAgICAgaWYobWVzc2FnZS5jb250ZW50KSB7XHJcbiAgICAgICAgICAgICAgICBjb250ZW50SHRtbCA9IG1lc3NhZ2UuY29udGVudC5pbmRleE9mKCd7QGlucHV0fScpID4gLTEgPyBtZXNzYWdlLmNvbnRlbnQgOiBtZXNzYWdlLmNvbnRlbnQgKyBjb250ZW50SHRtbDtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH1cclxuICAgICAgICByZXR1cm4gbmV3IERpYWxvZyh7XHJcbiAgICAgICAgICAgICdvbnNob3cnOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgaWYobWVzc2FnZSAmJiBtZXNzYWdlLm9uc2hvdyl7XHJcbiAgICAgICAgICAgICAgICAgICAgbWVzc2FnZS5vbnNob3coYm9keSk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdvbnNob3duJzpmdW5jdGlvbihib2R5KXtcclxuICAgICAgICAgICAgICAgIGJvZHkuZmluZCgnW25hbWU9Y29uZmlybV9pbnB1dF0nKS5mb2N1cygpO1xyXG4gICAgICAgICAgICAgICAgaWYobWVzc2FnZSAmJiBtZXNzYWdlLm9uc2hvd24pe1xyXG4gICAgICAgICAgICAgICAgICAgIG1lc3NhZ2Uub25zaG93bihib2R5KTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgJ29uc3VyZSc6ZnVuY3Rpb24oYm9keSl7XHJcbiAgICAgICAgICAgICAgICB2YXIgdmFsPWJvZHkuZmluZCgnW25hbWU9Y29uZmlybV9pbnB1dF0nKS52YWwoKTtcclxuICAgICAgICAgICAgICAgIGlmKHR5cGVvZiBjYWxsYmFjaz09J2Z1bmN0aW9uJyl7XHJcbiAgICAgICAgICAgICAgICAgICAgdmFyIHJlc3VsdCA9IGNhbGxiYWNrKHZhbCk7XHJcbiAgICAgICAgICAgICAgICAgICAgaWYocmVzdWx0PT09dHJ1ZSl7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGNhbGxlZD10cnVlO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gcmVzdWx0O1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAnb25oaWRlJzpmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgICAgICBpZihjYWxsZWQ9ZmFsc2UgJiYgdHlwZW9mIGNhbmNlbD09J2Z1bmN0aW9uJyl7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGNhbmNlbCgpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkuc2hvdyhjb250ZW50SHRtbC5jb21waWxlKHtpbnB1dDonPGlucHV0IHR5cGU9XCJ0ZXh0XCIgbmFtZT1cImNvbmZpcm1faW5wdXRcIiBjbGFzcz1cImZvcm0tY29udHJvbFwiIC8+J30pLHRpdGxlKTtcclxuICAgIH0sXHJcbiAgICBhY3Rpb246ZnVuY3Rpb24gKGxpc3QsY2FsbGJhY2ssdGl0bGUpIHtcclxuICAgICAgICB2YXIgaHRtbD0nPGRpdiBjbGFzcz1cImxpc3QtZ3JvdXBcIj48YSBocmVmPVwiamF2YXNjcmlwdDpcIiBjbGFzcz1cImxpc3QtZ3JvdXAtaXRlbSBsaXN0LWdyb3VwLWl0ZW0tYWN0aW9uXCI+JytsaXN0LmpvaW4oJzwvYT48YSBocmVmPVwiamF2YXNjcmlwdDpcIiBjbGFzcz1cImxpc3QtZ3JvdXAtaXRlbSBsaXN0LWdyb3VwLWl0ZW0tYWN0aW9uXCI+JykrJzwvYT48L2Rpdj4nO1xyXG4gICAgICAgIHZhciBhY3Rpb25zPW51bGw7XHJcbiAgICAgICAgdmFyIGRsZz1uZXcgRGlhbG9nKHtcclxuICAgICAgICAgICAgJ29uc2hvdyc6ZnVuY3Rpb24oYm9keSl7XHJcbiAgICAgICAgICAgICAgICBhY3Rpb25zPWJvZHkuZmluZCgnLmxpc3QtZ3JvdXAtaXRlbS1hY3Rpb24nKTtcclxuICAgICAgICAgICAgICAgIGFjdGlvbnMuY2xpY2soZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICAgICAgICAgICAgICBhY3Rpb25zLnJlbW92ZUNsYXNzKCdhY3RpdmUnKTtcclxuICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmFkZENsYXNzKCdhY3RpdmUnKTtcclxuICAgICAgICAgICAgICAgIH0pXHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdvbnN1cmUnOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgdmFyIGFjdGlvbj1hY3Rpb25zLmZpbHRlcignLmFjdGl2ZScpO1xyXG4gICAgICAgICAgICAgICAgaWYoYWN0aW9uLmxlbmd0aD4wKXtcclxuICAgICAgICAgICAgICAgICAgICB2YXIgdmFsPWFjdGlvbnMuaW5kZXgoYWN0aW9uKTtcclxuICAgICAgICAgICAgICAgICAgICBpZih0eXBlb2YgY2FsbGJhY2s9PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gY2FsbGJhY2sodmFsKTtcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdvbmhpZGUnOmZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgIHJldHVybiB0cnVlO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkuc2hvdyhodG1sLHRpdGxlP3RpdGxlOifor7fpgInmi6knKTtcclxuICAgIH0sXHJcbiAgICBwaWNrVXNlcjpmdW5jdGlvbih1cmwsY2FsbGJhY2ssZmlsdGVyKXtcclxuICAgICAgICB2YXIgdXNlcj1udWxsO1xyXG4gICAgICAgIGlmKCFmaWx0ZXIpZmlsdGVyPXt9O1xyXG4gICAgICAgIHZhciBkbGc9bmV3IERpYWxvZyh7XHJcbiAgICAgICAgICAgICdvbnNob3duJzpmdW5jdGlvbihib2R5KXtcclxuICAgICAgICAgICAgICAgIHZhciBidG49Ym9keS5maW5kKCcuc2VhcmNoYnRuJyk7XHJcbiAgICAgICAgICAgICAgICB2YXIgaW5wdXQ9Ym9keS5maW5kKCcuc2VhcmNodGV4dCcpO1xyXG4gICAgICAgICAgICAgICAgdmFyIGxpc3Rib3g9Ym9keS5maW5kKCcubGlzdC1ncm91cCcpO1xyXG4gICAgICAgICAgICAgICAgdmFyIGlzbG9hZGluZz1mYWxzZTtcclxuICAgICAgICAgICAgICAgIGJ0bi5jbGljayhmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICAgICAgICAgIGlmKGlzbG9hZGluZylyZXR1cm47XHJcbiAgICAgICAgICAgICAgICAgICAgaXNsb2FkaW5nPXRydWU7XHJcbiAgICAgICAgICAgICAgICAgICAgbGlzdGJveC5odG1sKCc8c3BhbiBjbGFzcz1cImxpc3QtbG9hZGluZ1wiPuWKoOi9veS4rS4uLjwvc3Bhbj4nKTtcclxuICAgICAgICAgICAgICAgICAgICBmaWx0ZXJbJ2tleSddPWlucHV0LnZhbCgpO1xyXG4gICAgICAgICAgICAgICAgICAgICQuYWpheChcclxuICAgICAgICAgICAgICAgICAgICAgICAge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdXJsOnVybCxcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHR5cGU6J0dFVCcsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBkYXRhVHlwZTonSlNPTicsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBkYXRhOmZpbHRlcixcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHN1Y2Nlc3M6ZnVuY3Rpb24oanNvbil7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaXNsb2FkaW5nPWZhbHNlO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmKGpzb24uc3RhdHVzKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYoanNvbi5kYXRhICYmIGpzb24uZGF0YS5sZW5ndGgpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxpc3Rib3guaHRtbCgnPGEgaHJlZj1cImphdmFzY3JpcHQ6XCIgZGF0YS1pZD1cIntAaWR9XCIgY2xhc3M9XCJsaXN0LWdyb3VwLWl0ZW0gbGlzdC1ncm91cC1pdGVtLWFjdGlvblwiPlt7QGlkfV0mbmJzcDs8aSBjbGFzcz1cImlvbi1tZC1wZXJzb25cIj48L2k+IHtAdXNlcm5hbWV9Jm5ic3A7Jm5ic3A7Jm5ic3A7PHNtYWxsPjxpIGNsYXNzPVwiaW9uLW1kLXBob25lLXBvcnRyYWl0XCI+PC9pPiB7QG1vYmlsZX08L3NtYWxsPjwvYT4nLmNvbXBpbGUoanNvbi5kYXRhLCB0cnVlKSk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBsaXN0Ym94LmZpbmQoJ2EubGlzdC1ncm91cC1pdGVtJykuY2xpY2soZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHZhciBpZCA9ICQodGhpcykuZGF0YSgnaWQnKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBmb3IgKHZhciBpID0gMDsgaSA8IGpzb24uZGF0YS5sZW5ndGg7IGkrKykge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZihqc29uLmRhdGFbaV0uaWQ9PWlkKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHVzZXI9anNvbi5kYXRhW2ldO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbGlzdGJveC5maW5kKCdhLmxpc3QtZ3JvdXAtaXRlbScpLnJlbW92ZUNsYXNzKCdhY3RpdmUnKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykuYWRkQ2xhc3MoJ2FjdGl2ZScpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9KVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9ZWxzZXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxpc3Rib3guaHRtbCgnPHNwYW4gY2xhc3M9XCJsaXN0LWxvYWRpbmdcIj48aSBjbGFzcz1cImlvbi1tZC13YXJuaW5nXCI+PC9pPiDmsqHmnInmo4DntKLliLDkvJrlkZg8L3NwYW4+Jyk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9ZWxzZXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbGlzdGJveC5odG1sKCc8c3BhbiBjbGFzcz1cInRleHQtZGFuZ2VyXCI+PGkgY2xhc3M9XCJpb24tbWQtd2FybmluZ1wiPjwvaT4g5Yqg6L295aSx6LSlPC9zcGFuPicpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgICk7XHJcblxyXG4gICAgICAgICAgICAgICAgfSkudHJpZ2dlcignY2xpY2snKTtcclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgJ29uc3VyZSc6ZnVuY3Rpb24oYm9keSl7XHJcbiAgICAgICAgICAgICAgICBpZighdXNlcil7XHJcbiAgICAgICAgICAgICAgICAgICAgdG9hc3RyLndhcm5pbmcoJ+ayoeaciemAieaLqeS8muWRmCEnKTtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICBpZih0eXBlb2YgY2FsbGJhY2s9PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgICAgIHZhciByZXN1bHQgPSBjYWxsYmFjayh1c2VyKTtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gcmVzdWx0O1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkuc2hvdygnPGRpdiBjbGFzcz1cImlucHV0LWdyb3VwXCI+PGlucHV0IHR5cGU9XCJ0ZXh0XCIgY2xhc3M9XCJmb3JtLWNvbnRyb2wgc2VhcmNodGV4dFwiIG5hbWU9XCJrZXl3b3JkXCIgcGxhY2Vob2xkZXI9XCLmoLnmja7kvJrlkZhpZOaIluWQjeensO+8jOeUteivneadpeaQnOe0olwiLz48ZGl2IGNsYXNzPVwiaW5wdXQtZ3JvdXAtYXBwZW5kXCI+PGEgY2xhc3M9XCJidG4gYnRuLW91dGxpbmUtc2Vjb25kYXJ5IHNlYXJjaGJ0blwiPjxpIGNsYXNzPVwiaW9uLW1kLXNlYXJjaFwiPjwvaT48L2E+PC9kaXY+PC9kaXY+PGRpdiBjbGFzcz1cImxpc3QtZ3JvdXAgbXQtMlwiPjwvZGl2PicsJ+ivt+aQnOe0ouW5tumAieaLqeS8muWRmCcpO1xyXG4gICAgfSxcclxuICAgIHBpY2tMb2NhdGU6ZnVuY3Rpb24odHlwZSwgY2FsbGJhY2ssIGxvY2F0ZSl7XHJcbiAgICAgICAgdmFyIHNldHRlZExvY2F0ZT1udWxsO1xyXG4gICAgICAgIHZhciBkbGc9bmV3IERpYWxvZyh7XHJcbiAgICAgICAgICAgICdzaXplJzonbGcnLFxyXG4gICAgICAgICAgICAnb25zaG93bic6ZnVuY3Rpb24oYm9keSl7XHJcbiAgICAgICAgICAgICAgICB2YXIgYnRuPWJvZHkuZmluZCgnLnNlYXJjaGJ0bicpO1xyXG4gICAgICAgICAgICAgICAgdmFyIGlucHV0PWJvZHkuZmluZCgnLnNlYXJjaHRleHQnKTtcclxuICAgICAgICAgICAgICAgIHZhciBtYXBib3g9Ym9keS5maW5kKCcubWFwJyk7XHJcbiAgICAgICAgICAgICAgICB2YXIgbWFwaW5mbz1ib2R5LmZpbmQoJy5tYXBpbmZvJyk7XHJcbiAgICAgICAgICAgICAgICBtYXBib3guY3NzKCdoZWlnaHQnLCQod2luZG93KS5oZWlnaHQoKSouNik7XHJcbiAgICAgICAgICAgICAgICB2YXIgaXNsb2FkaW5nPWZhbHNlO1xyXG4gICAgICAgICAgICAgICAgdmFyIG1hcD1Jbml0TWFwKCd0ZW5jZW50JyxtYXBib3gsZnVuY3Rpb24oYWRkcmVzcyxsb2NhdGUpe1xyXG4gICAgICAgICAgICAgICAgICAgIG1hcGluZm8uaHRtbChhZGRyZXNzKycmbmJzcDsnK2xvY2F0ZS5sbmcrJywnK2xvY2F0ZS5sYXQpO1xyXG4gICAgICAgICAgICAgICAgICAgIHNldHRlZExvY2F0ZT1sb2NhdGU7XHJcbiAgICAgICAgICAgICAgICB9LGxvY2F0ZSk7XHJcbiAgICAgICAgICAgICAgICBidG4uY2xpY2soZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgICAgICAgICB2YXIgc2VhcmNoPWlucHV0LnZhbCgpO1xyXG4gICAgICAgICAgICAgICAgICAgIG1hcC5zZXRMb2NhdGUoc2VhcmNoKTtcclxuICAgICAgICAgICAgICAgIH0pO1xyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAnb25zdXJlJzpmdW5jdGlvbihib2R5KXtcclxuICAgICAgICAgICAgICAgIGlmKCFzZXR0ZWRMb2NhdGUpe1xyXG4gICAgICAgICAgICAgICAgICAgIHRvYXN0ci53YXJuaW5nKCfmsqHmnInpgInmi6nkvY3nva4hJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgaWYodHlwZW9mIGNhbGxiYWNrPT09J2Z1bmN0aW9uJyl7XHJcbiAgICAgICAgICAgICAgICAgICAgdmFyIHJlc3VsdCA9IGNhbGxiYWNrKHNldHRlZExvY2F0ZSk7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHJlc3VsdDtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLnNob3coJzxkaXYgY2xhc3M9XCJpbnB1dC1ncm91cFwiPjxpbnB1dCB0eXBlPVwidGV4dFwiIGNsYXNzPVwiZm9ybS1jb250cm9sIHNlYXJjaHRleHRcIiBuYW1lPVwia2V5d29yZFwiIHBsYWNlaG9sZGVyPVwi5aGr5YaZ5Zyw5Z2A5qOA57Si5L2N572uXCIvPjxkaXYgY2xhc3M9XCJpbnB1dC1ncm91cC1hcHBlbmRcIj48YSBjbGFzcz1cImJ0biBidG4tb3V0bGluZS1zZWNvbmRhcnkgc2VhcmNoYnRuXCI+PGkgY2xhc3M9XCJpb24tbWQtc2VhcmNoXCI+PC9pPjwvYT48L2Rpdj48L2Rpdj4nICtcclxuICAgICAgICAgICAgJzxkaXYgY2xhc3M9XCJtYXAgbXQtMlwiPjwvZGl2PicgK1xyXG4gICAgICAgICAgICAnPGRpdiBjbGFzcz1cIm1hcGluZm8gbXQtMiB0ZXh0LW11dGVkXCI+5pyq6YCJ5oup5L2N572uPC9kaXY+Jywn6K+36YCJ5oup5Zyw5Zu+5L2N572uJyk7XHJcbiAgICB9XHJcbn07XHJcblxyXG5qUXVlcnkoZnVuY3Rpb24oJCl7XHJcblxyXG4gICAgLy/nm5HmjqfmjInplK5cclxuICAgICQoZG9jdW1lbnQpLm9uKCdrZXlkb3duJywgZnVuY3Rpb24oZSl7XHJcbiAgICAgICAgaWYoIURpYWxvZy5pbnN0YW5jZSlyZXR1cm47XHJcbiAgICAgICAgdmFyIGRsZz1EaWFsb2cuaW5zdGFuY2U7XHJcbiAgICAgICAgaWYgKGUua2V5Q29kZSA9PSAxMykge1xyXG4gICAgICAgICAgICBkbGcuYm94LmZpbmQoJy5tb2RhbC1mb290ZXIgLmJ0bicpLmVxKGRsZy5vcHRpb25zLmRlZmF1bHRCdG4pLnRyaWdnZXIoJ2NsaWNrJyk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIC8v6buY6K6k5bey55uR5ZCs5YWz6ZetXHJcbiAgICAgICAgLyppZiAoZS5rZXlDb2RlID09IDI3KSB7XHJcbiAgICAgICAgIHNlbGYuaGlkZSgpO1xyXG4gICAgICAgICB9Ki9cclxuICAgIH0pO1xyXG59KTsiLCJcclxualF1ZXJ5LmV4dGVuZChqUXVlcnkuZm4se1xyXG4gICAgdGFnczpmdW5jdGlvbihubSxvbnVwZGF0ZSl7XHJcbiAgICAgICAgdmFyIGRhdGE9W107XHJcbiAgICAgICAgdmFyIHRwbD0nPHNwYW4gY2xhc3M9XCJiYWRnZSBiYWRnZS1pbmZvXCI+e0BsYWJlbH08aW5wdXQgdHlwZT1cImhpZGRlblwiIG5hbWU9XCInK25tKydcIiB2YWx1ZT1cIntAbGFiZWx9XCIvPjxidXR0b24gdHlwZT1cImJ1dHRvblwiIGNsYXNzPVwiY2xvc2VcIiBkYXRhLWRpc21pc3M9XCJhbGVydFwiIGFyaWEtbGFiZWw9XCJDbG9zZVwiPjxzcGFuIGFyaWEtaGlkZGVuPVwidHJ1ZVwiPiZ0aW1lczs8L3NwYW4+PC9idXR0b24+PC9zcGFuPic7XHJcbiAgICAgICAgdmFyIGl0ZW09JCh0aGlzKS5wYXJlbnRzKCcuZm9ybS1jb250cm9sJyk7XHJcbiAgICAgICAgdmFyIGxhYmVsZ3JvdXA9JCgnPHNwYW4gY2xhc3M9XCJiYWRnZS1ncm91cFwiPjwvc3Bhbj4nKTtcclxuICAgICAgICB2YXIgaW5wdXQ9dGhpcztcclxuICAgICAgICB0aGlzLmJlZm9yZShsYWJlbGdyb3VwKTtcclxuICAgICAgICB0aGlzLm9uKCdrZXl1cCcsZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgdmFyIHZhbD0kKHRoaXMpLnZhbCgpLnJlcGxhY2UoL++8jC9nLCcsJyk7XHJcbiAgICAgICAgICAgIHZhciB1cGRhdGVkPWZhbHNlO1xyXG4gICAgICAgICAgICBpZih2YWwgJiYgdmFsLmluZGV4T2YoJywnKT4tMSl7XHJcbiAgICAgICAgICAgICAgICB2YXIgdmFscz12YWwuc3BsaXQoJywnKTtcclxuICAgICAgICAgICAgICAgIGZvcih2YXIgaT0wO2k8dmFscy5sZW5ndGg7aSsrKXtcclxuICAgICAgICAgICAgICAgICAgICB2YWxzW2ldPXZhbHNbaV0ucmVwbGFjZSgvXlxcc3xcXHMkL2csJycpO1xyXG4gICAgICAgICAgICAgICAgICAgIGlmKHZhbHNbaV0gJiYgZGF0YS5pbmRleE9mKHZhbHNbaV0pPT09LTEpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBkYXRhLnB1c2godmFsc1tpXSk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGxhYmVsZ3JvdXAuYXBwZW5kKHRwbC5jb21waWxlKHtsYWJlbDp2YWxzW2ldfSkpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB1cGRhdGVkPXRydWU7XHJcbiAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgaW5wdXQudmFsKCcnKTtcclxuICAgICAgICAgICAgICAgIGlmKHVwZGF0ZWQgJiYgb251cGRhdGUpb251cGRhdGUoZGF0YSk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS5vbignYmx1cicsZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgdmFyIHZhbD0kKHRoaXMpLnZhbCgpO1xyXG4gICAgICAgICAgICBpZih2YWwpIHtcclxuICAgICAgICAgICAgICAgICQodGhpcykudmFsKHZhbCArICcsJykudHJpZ2dlcigna2V5dXAnKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLnRyaWdnZXIoJ2JsdXInKTtcclxuICAgICAgICBsYWJlbGdyb3VwLm9uKCdjbGljaycsJy5jbG9zZScsZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgdmFyIHRhZz0kKHRoaXMpLnBhcmVudHMoJy5iYWRnZScpLmZpbmQoJ2lucHV0JykudmFsKCk7XHJcbiAgICAgICAgICAgIHZhciBpZD1kYXRhLmluZGV4T2YodGFnKTtcclxuICAgICAgICAgICAgaWYoaWQpZGF0YS5zcGxpY2UoaWQsMSk7XHJcbiAgICAgICAgICAgICQodGhpcykucGFyZW50cygnLmJhZGdlJykucmVtb3ZlKCk7XHJcbiAgICAgICAgICAgIGlmKG9udXBkYXRlKW9udXBkYXRlKGRhdGEpO1xyXG4gICAgICAgIH0pO1xyXG4gICAgICAgIGl0ZW0uY2xpY2soZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgaW5wdXQuZm9jdXMoKTtcclxuICAgICAgICB9KTtcclxuICAgIH1cclxufSk7IiwiLy/ml6XmnJ/nu4Tku7ZcclxuaWYoJC5mbi5kYXRldGltZXBpY2tlcikge1xyXG4gICAgdmFyIHRvb2x0aXBzPSB7XHJcbiAgICAgICAgdG9kYXk6ICflrprkvY3lvZPliY3ml6XmnJ8nLFxyXG4gICAgICAgIGNsZWFyOiAn5riF6Zmk5bey6YCJ5pel5pyfJyxcclxuICAgICAgICBjbG9zZTogJ+WFs+mXremAieaLqeWZqCcsXHJcbiAgICAgICAgc2VsZWN0TW9udGg6ICfpgInmi6nmnIjku70nLFxyXG4gICAgICAgIHByZXZNb250aDogJ+S4iuS4quaciCcsXHJcbiAgICAgICAgbmV4dE1vbnRoOiAn5LiL5Liq5pyIJyxcclxuICAgICAgICBzZWxlY3RZZWFyOiAn6YCJ5oup5bm05Lu9JyxcclxuICAgICAgICBwcmV2WWVhcjogJ+S4iuS4gOW5tCcsXHJcbiAgICAgICAgbmV4dFllYXI6ICfkuIvkuIDlubQnLFxyXG4gICAgICAgIHNlbGVjdERlY2FkZTogJ+mAieaLqeW5tOS7veWMuumXtCcsXHJcbiAgICAgICAgc2VsZWN0VGltZTon6YCJ5oup5pe26Ze0JyxcclxuICAgICAgICBwcmV2RGVjYWRlOiAn5LiK5LiA5Yy66Ze0JyxcclxuICAgICAgICBuZXh0RGVjYWRlOiAn5LiL5LiA5Yy66Ze0JyxcclxuICAgICAgICBwcmV2Q2VudHVyeTogJ+S4iuS4quS4lue6qicsXHJcbiAgICAgICAgbmV4dENlbnR1cnk6ICfkuIvkuKrkuJbnuqonXHJcbiAgICB9O1xyXG5cclxuICAgIGZ1bmN0aW9uIHRyYW5zT3B0aW9uKG9wdGlvbikge1xyXG4gICAgICAgIGlmKCFvcHRpb24pcmV0dXJuIHt9O1xyXG4gICAgICAgIHZhciBuZXdvcHQ9e307XHJcbiAgICAgICAgZm9yKHZhciBpIGluIG9wdGlvbil7XHJcbiAgICAgICAgICAgIHN3aXRjaCAoaSl7XHJcbiAgICAgICAgICAgICAgICBjYXNlICd2aWV3bW9kZSc6XHJcbiAgICAgICAgICAgICAgICAgICAgbmV3b3B0Wyd2aWV3TW9kZSddPW9wdGlvbltpXTtcclxuICAgICAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgICAgIGNhc2UgJ2tlZXBvcGVuJzpcclxuICAgICAgICAgICAgICAgICAgICBuZXdvcHRbJ2tlZXBPcGVuJ109b3B0aW9uW2ldO1xyXG4gICAgICAgICAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgICAgICAgICAgZGVmYXVsdDpcclxuICAgICAgICAgICAgICAgICAgICBuZXdvcHRbaV09b3B0aW9uW2ldO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHJldHVybiBuZXdvcHQ7XHJcbiAgICB9XHJcbiAgICAkKCcuZGF0ZXBpY2tlcicpLmVhY2goZnVuY3Rpb24oKXtcclxuICAgICAgICB2YXIgY29uZmlnPSQuZXh0ZW5kKHtcclxuICAgICAgICAgICAgdG9vbHRpcHM6dG9vbHRpcHMsXHJcbiAgICAgICAgICAgIGZvcm1hdDogJ1lZWVktTU0tREQnLFxyXG4gICAgICAgICAgICBsb2NhbGU6ICd6aC1jbicsXHJcbiAgICAgICAgICAgIHNob3dDbGVhcjp0cnVlLFxyXG4gICAgICAgICAgICBzaG93VG9kYXlCdXR0b246dHJ1ZSxcclxuICAgICAgICAgICAgc2hvd0Nsb3NlOnRydWUsXHJcbiAgICAgICAgICAgIGtlZXBJbnZhbGlkOnRydWVcclxuICAgICAgICB9LHRyYW5zT3B0aW9uKCQodGhpcykuZGF0YSgpKSk7XHJcblxyXG4gICAgICAgICQodGhpcykuZGF0ZXRpbWVwaWNrZXIoY29uZmlnKTtcclxuICAgIH0pO1xyXG5cclxuICAgICQoJy5kYXRlLXJhbmdlJykuZWFjaChmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgdmFyIGZyb20gPSAkKHRoaXMpLmZpbmQoJ1tuYW1lPWZyb21kYXRlXSwuZnJvbWRhdGUnKSwgdG8gPSAkKHRoaXMpLmZpbmQoJ1tuYW1lPXRvZGF0ZV0sLnRvZGF0ZScpO1xyXG4gICAgICAgIHZhciBvcHRpb25zID0gJC5leHRlbmQoe1xyXG4gICAgICAgICAgICB0b29sdGlwczp0b29sdGlwcyxcclxuICAgICAgICAgICAgZm9ybWF0OiAnWVlZWS1NTS1ERCcsXHJcbiAgICAgICAgICAgIGxvY2FsZTonemgtY24nLFxyXG4gICAgICAgICAgICBzaG93Q2xlYXI6dHJ1ZSxcclxuICAgICAgICAgICAgc2hvd1RvZGF5QnV0dG9uOnRydWUsXHJcbiAgICAgICAgICAgIHNob3dDbG9zZTp0cnVlLFxyXG4gICAgICAgICAgICBrZWVwSW52YWxpZDp0cnVlXHJcbiAgICAgICAgfSwkKHRoaXMpLmRhdGEoKSk7XHJcbiAgICAgICAgZnJvbS5kYXRldGltZXBpY2tlcihvcHRpb25zKS5vbignZHAuY2hhbmdlJywgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICBpZiAoZnJvbS52YWwoKSkge1xyXG4gICAgICAgICAgICAgICAgdG8uZGF0YSgnRGF0ZVRpbWVQaWNrZXInKS5taW5EYXRlKGZyb20udmFsKCkpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSk7XHJcbiAgICAgICAgdG8uZGF0ZXRpbWVwaWNrZXIob3B0aW9ucykub24oJ2RwLmNoYW5nZScsIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgaWYgKHRvLnZhbCgpKSB7XHJcbiAgICAgICAgICAgICAgICBmcm9tLmRhdGEoJ0RhdGVUaW1lUGlja2VyJykubWF4RGF0ZSh0by52YWwoKSk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KTtcclxuICAgIH0pO1xyXG59IiwiZnVuY3Rpb24gc2V0TmF2KG5hdikge1xyXG4gICAgdmFyIGl0ZW1zPSQoJy5tYWluLW5hdiAubmF2LWl0ZW0nKTtcclxuICAgIHZhciBmaW5kZWQ9ZmFsc2U7XHJcbiAgICBmb3IodmFyIGk9MDtpPGl0ZW1zLmxlbmd0aDtpKyspe1xyXG4gICAgICAgIGlmKGl0ZW1zLmVxKGkpLmRhdGEoJ21vZGVsJyk9PT1uYXYpe1xyXG4gICAgICAgICAgICBpdGVtcy5lcShpKS5hZGRDbGFzcygnYWN0aXZlJyk7XHJcbiAgICAgICAgICAgIGZpbmRlZD10cnVlO1xyXG4gICAgICAgICAgICBicmVhaztcclxuICAgICAgICB9XHJcbiAgICB9XHJcbiAgICBpZighZmluZGVkICYmIG5hdi5pbmRleE9mKCctJyk+MCl7XHJcbiAgICAgICAgbmF2PW5hdi5zdWJzdHIoMCxuYXYubGFzdEluZGV4T2YoJy0nKSk7XHJcbiAgICAgICAgc2V0TmF2KG5hdik7XHJcbiAgICB9XHJcbn1cclxuXHJcbmpRdWVyeShmdW5jdGlvbigkKXtcclxuXHJcbn0pOyJdfQ==
