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
jQuery(function($){

});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNvbW1vbi5qcyIsInRlbXBsYXRlLmpzIiwiZGlhbG9nLmpzIiwianF1ZXJ5LnRhZy5qcyIsImRhdGV0aW1lLmluaXQuanMiLCJmcm9udC5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ2ZBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQzlEQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQ3hUQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUNuQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUMvREE7QUFDQTtBQUNBIiwiZmlsZSI6ImZyb250LmpzIiwic291cmNlc0NvbnRlbnQiOlsiZnVuY3Rpb24gZGVsKG9iaixtc2cpIHtcclxuICAgIGRpYWxvZy5jb25maXJtKG1zZyxmdW5jdGlvbigpe1xyXG4gICAgICAgIGxvY2F0aW9uLmhyZWY9JChvYmopLmF0dHIoJ2hyZWYnKTtcclxuICAgIH0pO1xyXG4gICAgcmV0dXJuIGZhbHNlO1xyXG59XHJcblxyXG5mdW5jdGlvbiByYW5kb21TdHJpbmcobGVuLCBjaGFyU2V0KSB7XHJcbiAgICBjaGFyU2V0ID0gY2hhclNldCB8fCAnQUJDREVGR0hJSktMTU5PUFFSU1RVVldYWVphYmNkZWZnaGlqa2xtbm9wcXJzdHV2d3h5ejAxMjM0NTY3ODknO1xyXG4gICAgdmFyIHN0ciA9ICcnLGFsbExlbj1jaGFyU2V0Lmxlbmd0aDtcclxuICAgIGZvciAodmFyIGkgPSAwOyBpIDwgbGVuOyBpKyspIHtcclxuICAgICAgICB2YXIgcmFuZG9tUG96ID0gTWF0aC5mbG9vcihNYXRoLnJhbmRvbSgpICogYWxsTGVuKTtcclxuICAgICAgICBzdHIgKz0gY2hhclNldC5zdWJzdHJpbmcocmFuZG9tUG96LHJhbmRvbVBveisxKTtcclxuICAgIH1cclxuICAgIHJldHVybiBzdHI7XHJcbn0iLCJcclxuTnVtYmVyLnByb3RvdHlwZS5mb3JtYXQ9ZnVuY3Rpb24oZml4KXtcclxuICAgIGlmKGZpeD09PXVuZGVmaW5lZClmaXg9MjtcclxuICAgIHZhciBudW09dGhpcy50b0ZpeGVkKGZpeCk7XHJcbiAgICB2YXIgej1udW0uc3BsaXQoJy4nKTtcclxuICAgIHZhciBmb3JtYXQ9W10sZj16WzBdLnNwbGl0KCcnKSxsPWYubGVuZ3RoO1xyXG4gICAgZm9yKHZhciBpPTA7aTxsO2krKyl7XHJcbiAgICAgICAgaWYoaT4wICYmIGkgJSAzPT0wKXtcclxuICAgICAgICAgICAgZm9ybWF0LnVuc2hpZnQoJywnKTtcclxuICAgICAgICB9XHJcbiAgICAgICAgZm9ybWF0LnVuc2hpZnQoZltsLWktMV0pO1xyXG4gICAgfVxyXG4gICAgcmV0dXJuIGZvcm1hdC5qb2luKCcnKSsoei5sZW5ndGg9PTI/Jy4nK3pbMV06JycpO1xyXG59O1xyXG5TdHJpbmcucHJvdG90eXBlLmNvbXBpbGU9ZnVuY3Rpb24oZGF0YSxsaXN0KXtcclxuXHJcbiAgICBpZihsaXN0KXtcclxuICAgICAgICB2YXIgdGVtcHM9W107XHJcbiAgICAgICAgZm9yKHZhciBpIGluIGRhdGEpe1xyXG4gICAgICAgICAgICB0ZW1wcy5wdXNoKHRoaXMuY29tcGlsZShkYXRhW2ldKSk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIHJldHVybiB0ZW1wcy5qb2luKFwiXFxuXCIpO1xyXG4gICAgfWVsc2V7XHJcbiAgICAgICAgcmV0dXJuIHRoaXMucmVwbGFjZSgvXFx7QChbXFx3XFxkXFwuXSspKD86XFx8KFtcXHdcXGRdKykoPzpcXHMqPVxccyooW1xcd1xcZCxcXHMjXSspKT8pP1xcfS9nLGZ1bmN0aW9uKGFsbCxtMSxmdW5jLGFyZ3Mpe1xyXG5cclxuICAgICAgICAgICAgaWYobTEuaW5kZXhPZignLicpPjApe1xyXG4gICAgICAgICAgICAgICAgdmFyIGtleXM9bTEuc3BsaXQoJy4nKSx2YWw9ZGF0YTtcclxuICAgICAgICAgICAgICAgIGZvcih2YXIgaT0wO2k8a2V5cy5sZW5ndGg7aSsrKXtcclxuICAgICAgICAgICAgICAgICAgICBpZih2YWxba2V5c1tpXV0hPT11bmRlZmluZWQpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB2YWw9dmFsW2tleXNbaV1dO1xyXG4gICAgICAgICAgICAgICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB2YWwgPSAnJztcclxuICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgcmV0dXJuIGNhbGxmdW5jKHZhbCxmdW5jLGFyZ3MpO1xyXG4gICAgICAgICAgICB9ZWxzZXtcclxuICAgICAgICAgICAgICAgIHJldHVybiBkYXRhW20xXSE9PXVuZGVmaW5lZD9jYWxsZnVuYyhkYXRhW20xXSxmdW5jLGFyZ3MsZGF0YSk6Jyc7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KTtcclxuICAgIH1cclxufTtcclxuXHJcbmZ1bmN0aW9uIGNhbGxmdW5jKHZhbCxmdW5jLGFyZ3MsdGhpc29iail7XHJcbiAgICBpZighYXJncyl7XHJcbiAgICAgICAgYXJncz1bdmFsXTtcclxuICAgIH1lbHNle1xyXG4gICAgICAgIGlmKHR5cGVvZiBhcmdzPT09J3N0cmluZycpYXJncz1hcmdzLnNwbGl0KCcsJyk7XHJcbiAgICAgICAgdmFyIGFyZ2lkeD1hcmdzLmluZGV4T2YoJyMjIycpO1xyXG4gICAgICAgIGlmKGFyZ2lkeD49MCl7XHJcbiAgICAgICAgICAgIGFyZ3NbYXJnaWR4XT12YWw7XHJcbiAgICAgICAgfWVsc2V7XHJcbiAgICAgICAgICAgIGFyZ3M9W3ZhbF0uY29uY2F0KGFyZ3MpO1xyXG4gICAgICAgIH1cclxuICAgIH1cclxuICAgIC8vY29uc29sZS5sb2coYXJncyk7XHJcbiAgICByZXR1cm4gd2luZG93W2Z1bmNdP3dpbmRvd1tmdW5jXS5hcHBseSh0aGlzb2JqLGFyZ3MpOnZhbDtcclxufVxyXG5cclxuZnVuY3Rpb24gaWlmKHYsbTEsbTIpe1xyXG4gICAgaWYodj09PScwJyl2PTA7XHJcbiAgICByZXR1cm4gdj9tMTptMjtcclxufSIsIlxyXG52YXIgZGlhbG9nVHBsPSc8ZGl2IGNsYXNzPVwibW9kYWwgZmFkZVwiIGlkPVwie0BpZH1cIiB0YWJpbmRleD1cIi0xXCIgcm9sZT1cImRpYWxvZ1wiIGFyaWEtbGFiZWxsZWRieT1cIntAaWR9TGFiZWxcIiBhcmlhLWhpZGRlbj1cInRydWVcIj5cXG4nICtcclxuICAgICcgICAgPGRpdiBjbGFzcz1cIm1vZGFsLWRpYWxvZ1wiPlxcbicgK1xyXG4gICAgJyAgICAgICAgPGRpdiBjbGFzcz1cIm1vZGFsLWNvbnRlbnRcIj5cXG4nICtcclxuICAgICcgICAgICAgICAgICA8ZGl2IGNsYXNzPVwibW9kYWwtaGVhZGVyXCI+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgICAgIDxoNCBjbGFzcz1cIm1vZGFsLXRpdGxlXCIgaWQ9XCJ7QGlkfUxhYmVsXCI+PC9oND5cXG4nICtcclxuICAgICcgICAgICAgICAgICAgICAgPGJ1dHRvbiB0eXBlPVwiYnV0dG9uXCIgY2xhc3M9XCJjbG9zZVwiIGRhdGEtZGlzbWlzcz1cIm1vZGFsXCI+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgICAgICAgICA8c3BhbiBhcmlhLWhpZGRlbj1cInRydWVcIj4mdGltZXM7PC9zcGFuPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgICAgICAgICAgPHNwYW4gY2xhc3M9XCJzci1vbmx5XCI+Q2xvc2U8L3NwYW4+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgICAgIDwvYnV0dG9uPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgIDwvZGl2PlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJtb2RhbC1ib2R5XCI+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgPC9kaXY+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgPGRpdiBjbGFzcz1cIm1vZGFsLWZvb3RlclwiPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgICAgICA8bmF2IGNsYXNzPVwibmF2IG5hdi1maWxsXCI+PC9uYXY+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgPC9kaXY+XFxuJyArXHJcbiAgICAnICAgICAgICA8L2Rpdj5cXG4nICtcclxuICAgICcgICAgPC9kaXY+XFxuJyArXHJcbiAgICAnPC9kaXY+JztcclxudmFyIGRpYWxvZ0lkeD0wO1xyXG5mdW5jdGlvbiBEaWFsb2cob3B0cyl7XHJcbiAgICBpZighb3B0cylvcHRzPXt9O1xyXG4gICAgLy/lpITnkIbmjInpkq5cclxuICAgIGlmKG9wdHMuYnRucyE9PXVuZGVmaW5lZCkge1xyXG4gICAgICAgIGlmICh0eXBlb2Yob3B0cy5idG5zKSA9PSAnc3RyaW5nJykge1xyXG4gICAgICAgICAgICBvcHRzLmJ0bnMgPSBbb3B0cy5idG5zXTtcclxuICAgICAgICB9XHJcbiAgICAgICAgdmFyIGRmdD0tMTtcclxuICAgICAgICBmb3IgKHZhciBpID0gMDsgaSA8IG9wdHMuYnRucy5sZW5ndGg7IGkrKykge1xyXG4gICAgICAgICAgICBpZih0eXBlb2Yob3B0cy5idG5zW2ldKT09J3N0cmluZycpe1xyXG4gICAgICAgICAgICAgICAgb3B0cy5idG5zW2ldPXsndGV4dCc6b3B0cy5idG5zW2ldfTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICBpZihvcHRzLmJ0bnNbaV0uaXNkZWZhdWx0KXtcclxuICAgICAgICAgICAgICAgIGRmdD1pO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfVxyXG4gICAgICAgIGlmKGRmdDwwKXtcclxuICAgICAgICAgICAgZGZ0PW9wdHMuYnRucy5sZW5ndGgtMTtcclxuICAgICAgICAgICAgb3B0cy5idG5zW2RmdF0uaXNkZWZhdWx0PXRydWU7XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICBpZighb3B0cy5idG5zW2RmdF1bJ3R5cGUnXSl7XHJcbiAgICAgICAgICAgIG9wdHMuYnRuc1tkZnRdWyd0eXBlJ109J3ByaW1hcnknO1xyXG4gICAgICAgIH1cclxuICAgICAgICBvcHRzLmRlZmF1bHRCdG49ZGZ0O1xyXG4gICAgfVxyXG5cclxuICAgIHRoaXMub3B0aW9ucz0kLmV4dGVuZCh7XHJcbiAgICAgICAgJ2lkJzonZGxnTW9kYWwnK2RpYWxvZ0lkeCsrLFxyXG4gICAgICAgICdzaXplJzonJyxcclxuICAgICAgICAnYnRucyc6W1xyXG4gICAgICAgICAgICB7J3RleHQnOiflj5bmtognLCd0eXBlJzonc2Vjb25kYXJ5J30sXHJcbiAgICAgICAgICAgIHsndGV4dCc6J+ehruWumicsJ2lzZGVmYXVsdCc6dHJ1ZSwndHlwZSc6J3ByaW1hcnknfVxyXG4gICAgICAgIF0sXHJcbiAgICAgICAgJ2RlZmF1bHRCdG4nOjEsXHJcbiAgICAgICAgJ29uc3VyZSc6bnVsbCxcclxuICAgICAgICAnb25zaG93JzpudWxsLFxyXG4gICAgICAgICdvbnNob3duJzpudWxsLFxyXG4gICAgICAgICdvbmhpZGUnOm51bGwsXHJcbiAgICAgICAgJ29uaGlkZGVuJzpudWxsXHJcbiAgICB9LG9wdHMpO1xyXG5cclxuICAgIHRoaXMuYm94PSQodGhpcy5vcHRpb25zLmlkKTtcclxufVxyXG5EaWFsb2cucHJvdG90eXBlLmdlbmVyQnRuPWZ1bmN0aW9uKG9wdCxpZHgpe1xyXG4gICAgaWYob3B0Wyd0eXBlJ10pb3B0WydjbGFzcyddPSdidG4tb3V0bGluZS0nK29wdFsndHlwZSddO1xyXG4gICAgcmV0dXJuICc8YSBocmVmPVwiamF2YXNjcmlwdDpcIiBjbGFzcz1cIm5hdi1pdGVtIGJ0biAnKyhvcHRbJ2NsYXNzJ10/b3B0WydjbGFzcyddOididG4tb3V0bGluZS1zZWNvbmRhcnknKSsnXCIgZGF0YS1pbmRleD1cIicraWR4KydcIj4nK29wdC50ZXh0Kyc8L2E+JztcclxufTtcclxuRGlhbG9nLnByb3RvdHlwZS5zaG93PWZ1bmN0aW9uKGh0bWwsdGl0bGUpe1xyXG4gICAgdGhpcy5ib3g9JCgnIycrdGhpcy5vcHRpb25zLmlkKTtcclxuICAgIGlmKCF0aXRsZSl0aXRsZT0n57O757uf5o+Q56S6JztcclxuICAgIGlmKHRoaXMuYm94Lmxlbmd0aDwxKSB7XHJcbiAgICAgICAgJChkb2N1bWVudC5ib2R5KS5hcHBlbmQoZGlhbG9nVHBsLmNvbXBpbGUoeydpZCc6IHRoaXMub3B0aW9ucy5pZH0pKTtcclxuICAgICAgICB0aGlzLmJveD0kKCcjJyt0aGlzLm9wdGlvbnMuaWQpO1xyXG4gICAgfWVsc2V7XHJcbiAgICAgICAgdGhpcy5ib3gudW5iaW5kKCk7XHJcbiAgICB9XHJcblxyXG4gICAgLy90aGlzLmJveC5maW5kKCcubW9kYWwtZm9vdGVyIC5idG4tcHJpbWFyeScpLnVuYmluZCgpO1xyXG4gICAgdmFyIHNlbGY9dGhpcztcclxuICAgIERpYWxvZy5pbnN0YW5jZT1zZWxmO1xyXG5cclxuICAgIC8v55Sf5oiQ5oyJ6ZKuXHJcbiAgICB2YXIgYnRucz1bXTtcclxuICAgIGZvcih2YXIgaT0wO2k8dGhpcy5vcHRpb25zLmJ0bnMubGVuZ3RoO2krKyl7XHJcbiAgICAgICAgYnRucy5wdXNoKHRoaXMuZ2VuZXJCdG4odGhpcy5vcHRpb25zLmJ0bnNbaV0saSkpO1xyXG4gICAgfVxyXG4gICAgdGhpcy5ib3guZmluZCgnLm1vZGFsLWZvb3RlciAubmF2JykuaHRtbChidG5zLmpvaW4oJ1xcbicpKTtcclxuXHJcbiAgICB2YXIgZGlhbG9nPXRoaXMuYm94LmZpbmQoJy5tb2RhbC1kaWFsb2cnKTtcclxuICAgIGRpYWxvZy5yZW1vdmVDbGFzcygnbW9kYWwtc20nKS5yZW1vdmVDbGFzcygnbW9kYWwtbGcnKTtcclxuICAgIGlmKHRoaXMub3B0aW9ucy5zaXplPT0nc20nKSB7XHJcbiAgICAgICAgZGlhbG9nLmFkZENsYXNzKCdtb2RhbC1zbScpO1xyXG4gICAgfWVsc2UgaWYodGhpcy5vcHRpb25zLnNpemU9PSdsZycpIHtcclxuICAgICAgICBkaWFsb2cuYWRkQ2xhc3MoJ21vZGFsLWxnJyk7XHJcbiAgICB9XHJcbiAgICB0aGlzLmJveC5maW5kKCcubW9kYWwtdGl0bGUnKS50ZXh0KHRpdGxlKTtcclxuXHJcbiAgICB2YXIgYm9keT10aGlzLmJveC5maW5kKCcubW9kYWwtYm9keScpO1xyXG4gICAgYm9keS5odG1sKGh0bWwpO1xyXG4gICAgdGhpcy5ib3gub24oJ2hpZGUuYnMubW9kYWwnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgaWYoc2VsZi5vcHRpb25zLm9uaGlkZSl7XHJcbiAgICAgICAgICAgIHNlbGYub3B0aW9ucy5vbmhpZGUoYm9keSxzZWxmLmJveCk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIERpYWxvZy5pbnN0YW5jZT1udWxsO1xyXG4gICAgfSk7XHJcbiAgICB0aGlzLmJveC5vbignaGlkZGVuLmJzLm1vZGFsJyxmdW5jdGlvbigpe1xyXG4gICAgICAgIGlmKHNlbGYub3B0aW9ucy5vbmhpZGRlbil7XHJcbiAgICAgICAgICAgIHNlbGYub3B0aW9ucy5vbmhpZGRlbihib2R5LHNlbGYuYm94KTtcclxuICAgICAgICB9XHJcbiAgICAgICAgc2VsZi5ib3gucmVtb3ZlKCk7XHJcbiAgICB9KTtcclxuICAgIHRoaXMuYm94Lm9uKCdzaG93LmJzLm1vZGFsJyxmdW5jdGlvbigpe1xyXG4gICAgICAgIGlmKHNlbGYub3B0aW9ucy5vbnNob3cpe1xyXG4gICAgICAgICAgICBzZWxmLm9wdGlvbnMub25zaG93KGJvZHksc2VsZi5ib3gpO1xyXG4gICAgICAgIH1cclxuICAgIH0pO1xyXG4gICAgdGhpcy5ib3gub24oJ3Nob3duLmJzLm1vZGFsJyxmdW5jdGlvbigpe1xyXG4gICAgICAgIGlmKHNlbGYub3B0aW9ucy5vbnNob3duKXtcclxuICAgICAgICAgICAgc2VsZi5vcHRpb25zLm9uc2hvd24oYm9keSxzZWxmLmJveCk7XHJcbiAgICAgICAgfVxyXG4gICAgfSk7XHJcbiAgICB0aGlzLmJveC5maW5kKCcubW9kYWwtZm9vdGVyIC5idG4nKS5jbGljayhmdW5jdGlvbigpe1xyXG4gICAgICAgIHZhciByZXN1bHQ9dHJ1ZSxpZHg9JCh0aGlzKS5kYXRhKCdpbmRleCcpO1xyXG4gICAgICAgIGlmKHNlbGYub3B0aW9ucy5idG5zW2lkeF0uY2xpY2spe1xyXG4gICAgICAgICAgICByZXN1bHQgPSBzZWxmLm9wdGlvbnMuYnRuc1tpZHhdLmNsaWNrLmFwcGx5KHRoaXMsW2JvZHksIHNlbGYuYm94XSk7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIGlmKGlkeD09c2VsZi5vcHRpb25zLmRlZmF1bHRCdG4pIHtcclxuICAgICAgICAgICAgaWYgKHNlbGYub3B0aW9ucy5vbnN1cmUpIHtcclxuICAgICAgICAgICAgICAgIHJlc3VsdCA9IHNlbGYub3B0aW9ucy5vbnN1cmUuYXBwbHkodGhpcyxbYm9keSwgc2VsZi5ib3hdKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH1cclxuICAgICAgICBpZihyZXN1bHQhPT1mYWxzZSl7XHJcbiAgICAgICAgICAgIHNlbGYuYm94Lm1vZGFsKCdoaWRlJyk7XHJcbiAgICAgICAgfVxyXG4gICAgfSk7XHJcbiAgICB0aGlzLmJveC5tb2RhbCgnc2hvdycpO1xyXG4gICAgcmV0dXJuIHRoaXM7XHJcbn07XHJcbkRpYWxvZy5wcm90b3R5cGUuaGlkZT1mdW5jdGlvbigpe1xyXG4gICAgdGhpcy5ib3gubW9kYWwoJ2hpZGUnKTtcclxuICAgIHJldHVybiB0aGlzO1xyXG59O1xyXG5cclxudmFyIGRpYWxvZz17XHJcbiAgICBhbGVydDpmdW5jdGlvbihtZXNzYWdlLGNhbGxiYWNrLHRpdGxlKXtcclxuICAgICAgICB2YXIgY2FsbGVkPWZhbHNlO1xyXG4gICAgICAgIHZhciBpc2NhbGxiYWNrPXR5cGVvZiBjYWxsYmFjaz09J2Z1bmN0aW9uJztcclxuICAgICAgICByZXR1cm4gbmV3IERpYWxvZyh7XHJcbiAgICAgICAgICAgIGJ0bnM6J+ehruWumicsXHJcbiAgICAgICAgICAgIG9uc3VyZTpmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICAgICAgaWYoaXNjYWxsYmFjayl7XHJcbiAgICAgICAgICAgICAgICAgICAgY2FsbGVkPXRydWU7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGNhbGxiYWNrKHRydWUpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICBvbmhpZGU6ZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgICAgIGlmKCFjYWxsZWQgJiYgaXNjYWxsYmFjayl7XHJcbiAgICAgICAgICAgICAgICAgICAgY2FsbGJhY2soZmFsc2UpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkuc2hvdyhtZXNzYWdlLHRpdGxlKTtcclxuICAgIH0sXHJcbiAgICBjb25maXJtOmZ1bmN0aW9uKG1lc3NhZ2UsY29uZmlybSxjYW5jZWwpe1xyXG4gICAgICAgIHZhciBjYWxsZWQ9ZmFsc2U7XHJcbiAgICAgICAgcmV0dXJuIG5ldyBEaWFsb2coe1xyXG4gICAgICAgICAgICAnb25zdXJlJzpmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICAgICAgaWYodHlwZW9mIGNvbmZpcm09PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgICAgIGNhbGxlZD10cnVlO1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBjb25maXJtKCk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdvbmhpZGUnOmZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgIGlmKGNhbGxlZD1mYWxzZSAmJiB0eXBlb2YgY2FuY2VsPT0nZnVuY3Rpb24nKXtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gY2FuY2VsKCk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS5zaG93KG1lc3NhZ2UpO1xyXG4gICAgfSxcclxuICAgIHByb21wdDpmdW5jdGlvbihtZXNzYWdlLGNhbGxiYWNrLGNhbmNlbCl7XHJcbiAgICAgICAgdmFyIGNhbGxlZD1mYWxzZTtcclxuICAgICAgICByZXR1cm4gbmV3IERpYWxvZyh7XHJcbiAgICAgICAgICAgICdvbnNob3duJzpmdW5jdGlvbihib2R5KXtcclxuICAgICAgICAgICAgICAgIGJvZHkuZmluZCgnW25hbWU9Y29uZmlybV9pbnB1dF0nKS5mb2N1cygpO1xyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAnb25zdXJlJzpmdW5jdGlvbihib2R5KXtcclxuICAgICAgICAgICAgICAgIHZhciB2YWw9Ym9keS5maW5kKCdbbmFtZT1jb25maXJtX2lucHV0XScpLnZhbCgpO1xyXG4gICAgICAgICAgICAgICAgaWYodHlwZW9mIGNhbGxiYWNrPT0nZnVuY3Rpb24nKXtcclxuICAgICAgICAgICAgICAgICAgICB2YXIgcmVzdWx0ID0gY2FsbGJhY2sodmFsKTtcclxuICAgICAgICAgICAgICAgICAgICBpZihyZXN1bHQ9PT10cnVlKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgY2FsbGVkPXRydWU7XHJcbiAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiByZXN1bHQ7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdvbmhpZGUnOmZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgIGlmKGNhbGxlZD1mYWxzZSAmJiB0eXBlb2YgY2FuY2VsPT0nZnVuY3Rpb24nKXtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gY2FuY2VsKCk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS5zaG93KCc8aW5wdXQgdHlwZT1cInRleHRcIiBuYW1lPVwiY29uZmlybV9pbnB1dFwiIGNsYXNzPVwiZm9ybS1jb250cm9sXCIgLz4nLG1lc3NhZ2UpO1xyXG4gICAgfSxcclxuICAgIHBpY2tVc2VyOmZ1bmN0aW9uKHVybCxjYWxsYmFjayxmaWx0ZXIpe1xyXG4gICAgICAgIHZhciB1c2VyPW51bGw7XHJcbiAgICAgICAgaWYoIWZpbHRlcilmaWx0ZXI9e307XHJcbiAgICAgICAgdmFyIGRsZz1uZXcgRGlhbG9nKHtcclxuICAgICAgICAgICAgJ29uc2hvd24nOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgdmFyIGJ0bj1ib2R5LmZpbmQoJy5zZWFyY2hidG4nKTtcclxuICAgICAgICAgICAgICAgIHZhciBpbnB1dD1ib2R5LmZpbmQoJy5zZWFyY2h0ZXh0Jyk7XHJcbiAgICAgICAgICAgICAgICB2YXIgbGlzdGJveD1ib2R5LmZpbmQoJy5saXN0LWdyb3VwJyk7XHJcbiAgICAgICAgICAgICAgICB2YXIgaXNsb2FkaW5nPWZhbHNlO1xyXG4gICAgICAgICAgICAgICAgYnRuLmNsaWNrKGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgICAgICAgICAgaWYoaXNsb2FkaW5nKXJldHVybjtcclxuICAgICAgICAgICAgICAgICAgICBpc2xvYWRpbmc9dHJ1ZTtcclxuICAgICAgICAgICAgICAgICAgICBsaXN0Ym94Lmh0bWwoJzxzcGFuIGNsYXNzPVwibGlzdC1sb2FkaW5nXCI+5Yqg6L295LitLi4uPC9zcGFuPicpO1xyXG4gICAgICAgICAgICAgICAgICAgIGZpbHRlclsna2V5J109aW5wdXQudmFsKCk7XHJcbiAgICAgICAgICAgICAgICAgICAgJC5hamF4KFxyXG4gICAgICAgICAgICAgICAgICAgICAgICB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB1cmw6dXJsLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdHlwZTonR0VUJyxcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGRhdGFUeXBlOidKU09OJyxcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGRhdGE6ZmlsdGVyLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc3VjY2VzczpmdW5jdGlvbihqc29uKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpc2xvYWRpbmc9ZmFsc2U7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYoanNvbi5zdGF0dXMpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZihqc29uLmRhdGEgJiYganNvbi5kYXRhLmxlbmd0aCkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbGlzdGJveC5odG1sKCc8YSBocmVmPVwiamF2YXNjcmlwdDpcIiBkYXRhLWlkPVwie0BpZH1cIiBjbGFzcz1cImxpc3QtZ3JvdXAtaXRlbSBsaXN0LWdyb3VwLWl0ZW0tYWN0aW9uXCI+W3tAaWR9XSZuYnNwOzxpIGNsYXNzPVwiaW9uLW1kLXBlcnNvblwiPjwvaT4ge0B1c2VybmFtZX0mbmJzcDsmbmJzcDsmbmJzcDs8c21hbGw+PGkgY2xhc3M9XCJpb24tbWQtcGhvbmUtcG9ydHJhaXRcIj48L2k+IHtAbW9iaWxlfTwvc21hbGw+PC9hPicuY29tcGlsZShqc29uLmRhdGEsIHRydWUpKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxpc3Rib3guZmluZCgnYS5saXN0LWdyb3VwLWl0ZW0nKS5jbGljayhmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyIGlkID0gJCh0aGlzKS5kYXRhKCdpZCcpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGZvciAodmFyIGkgPSAwOyBpIDwganNvbi5kYXRhLmxlbmd0aDsgaSsrKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmKGpzb24uZGF0YVtpXS5pZD09aWQpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdXNlcj1qc29uLmRhdGFbaV07XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBsaXN0Ym94LmZpbmQoJ2EubGlzdC1ncm91cC1pdGVtJykucmVtb3ZlQ2xhc3MoJ2FjdGl2ZScpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5hZGRDbGFzcygnYWN0aXZlJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbGlzdGJveC5odG1sKCc8c3BhbiBjbGFzcz1cImxpc3QtbG9hZGluZ1wiPjxpIGNsYXNzPVwiaW9uLW1kLXdhcm5pbmdcIj48L2k+IOayoeacieajgOe0ouWIsOS8muWRmDwvc3Bhbj4nKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBsaXN0Ym94Lmh0bWwoJzxzcGFuIGNsYXNzPVwidGV4dC1kYW5nZXJcIj48aSBjbGFzcz1cImlvbi1tZC13YXJuaW5nXCI+PC9pPiDliqDovb3lpLHotKU8L3NwYW4+Jyk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgKTtcclxuXHJcbiAgICAgICAgICAgICAgICB9KS50cmlnZ2VyKCdjbGljaycpO1xyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAnb25zdXJlJzpmdW5jdGlvbihib2R5KXtcclxuICAgICAgICAgICAgICAgIGlmKCF1c2VyKXtcclxuICAgICAgICAgICAgICAgICAgICB0b2FzdHIud2FybmluZygn5rKh5pyJ6YCJ5oup5Lya5ZGYIScpO1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIGlmKHR5cGVvZiBjYWxsYmFjaz09J2Z1bmN0aW9uJyl7XHJcbiAgICAgICAgICAgICAgICAgICAgdmFyIHJlc3VsdCA9IGNhbGxiYWNrKHVzZXIpO1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiByZXN1bHQ7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS5zaG93KCc8ZGl2IGNsYXNzPVwiaW5wdXQtZ3JvdXBcIj48aW5wdXQgdHlwZT1cInRleHRcIiBjbGFzcz1cImZvcm0tY29udHJvbCBzZWFyY2h0ZXh0XCIgbmFtZT1cImtleXdvcmRcIiBwbGFjZWhvbGRlcj1cIuagueaNruS8muWRmGlk5oiW5ZCN56ew77yM55S16K+d5p2l5pCc57SiXCIvPjxkaXYgY2xhc3M9XCJpbnB1dC1ncm91cC1hcHBlbmRcIj48YSBjbGFzcz1cImJ0biBidG4tb3V0bGluZS1zZWNvbmRhcnkgc2VhcmNoYnRuXCI+PGkgY2xhc3M9XCJpb24tbWQtc2VhcmNoXCI+PC9pPjwvYT48L2Rpdj48L2Rpdj48ZGl2IGNsYXNzPVwibGlzdC1ncm91cCBtdC0yXCI+PC9kaXY+Jywn6K+35pCc57Si5bm26YCJ5oup5Lya5ZGYJyk7XHJcbiAgICB9LFxyXG4gICAgcGlja0xvY2F0ZTpmdW5jdGlvbih0eXBlLCBjYWxsYmFjaywgbG9jYXRlKXtcclxuICAgICAgICB2YXIgc2V0dGVkTG9jYXRlPW51bGw7XHJcbiAgICAgICAgdmFyIGRsZz1uZXcgRGlhbG9nKHtcclxuICAgICAgICAgICAgJ3NpemUnOidsZycsXHJcbiAgICAgICAgICAgICdvbnNob3duJzpmdW5jdGlvbihib2R5KXtcclxuICAgICAgICAgICAgICAgIHZhciBidG49Ym9keS5maW5kKCcuc2VhcmNoYnRuJyk7XHJcbiAgICAgICAgICAgICAgICB2YXIgaW5wdXQ9Ym9keS5maW5kKCcuc2VhcmNodGV4dCcpO1xyXG4gICAgICAgICAgICAgICAgdmFyIG1hcGJveD1ib2R5LmZpbmQoJy5tYXAnKTtcclxuICAgICAgICAgICAgICAgIHZhciBtYXBpbmZvPWJvZHkuZmluZCgnLm1hcGluZm8nKTtcclxuICAgICAgICAgICAgICAgIG1hcGJveC5jc3MoJ2hlaWdodCcsJCh3aW5kb3cpLmhlaWdodCgpKi42KTtcclxuICAgICAgICAgICAgICAgIHZhciBpc2xvYWRpbmc9ZmFsc2U7XHJcbiAgICAgICAgICAgICAgICB2YXIgbWFwPUluaXRNYXAoJ3RlbmNlbnQnLG1hcGJveCxmdW5jdGlvbihhZGRyZXNzLGxvY2F0ZSl7XHJcbiAgICAgICAgICAgICAgICAgICAgbWFwaW5mby5odG1sKGFkZHJlc3MrJyZuYnNwOycrbG9jYXRlLmxuZysnLCcrbG9jYXRlLmxhdCk7XHJcbiAgICAgICAgICAgICAgICAgICAgc2V0dGVkTG9jYXRlPWxvY2F0ZTtcclxuICAgICAgICAgICAgICAgIH0sbG9jYXRlKTtcclxuICAgICAgICAgICAgICAgIGJ0bi5jbGljayhmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICAgICAgICAgIHZhciBzZWFyY2g9aW5wdXQudmFsKCk7XHJcbiAgICAgICAgICAgICAgICAgICAgbWFwLnNldExvY2F0ZShzZWFyY2gpO1xyXG4gICAgICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdvbnN1cmUnOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgaWYoIXNldHRlZExvY2F0ZSl7XHJcbiAgICAgICAgICAgICAgICAgICAgdG9hc3RyLndhcm5pbmcoJ+ayoeaciemAieaLqeS9jee9riEnKTtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICBpZih0eXBlb2YgY2FsbGJhY2s9PT0nZnVuY3Rpb24nKXtcclxuICAgICAgICAgICAgICAgICAgICB2YXIgcmVzdWx0ID0gY2FsbGJhY2soc2V0dGVkTG9jYXRlKTtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gcmVzdWx0O1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkuc2hvdygnPGRpdiBjbGFzcz1cImlucHV0LWdyb3VwXCI+PGlucHV0IHR5cGU9XCJ0ZXh0XCIgY2xhc3M9XCJmb3JtLWNvbnRyb2wgc2VhcmNodGV4dFwiIG5hbWU9XCJrZXl3b3JkXCIgcGxhY2Vob2xkZXI9XCLloavlhpnlnLDlnYDmo4DntKLkvY3nva5cIi8+PGRpdiBjbGFzcz1cImlucHV0LWdyb3VwLWFwcGVuZFwiPjxhIGNsYXNzPVwiYnRuIGJ0bi1vdXRsaW5lLXNlY29uZGFyeSBzZWFyY2hidG5cIj48aSBjbGFzcz1cImlvbi1tZC1zZWFyY2hcIj48L2k+PC9hPjwvZGl2PjwvZGl2PicgK1xyXG4gICAgICAgICAgICAnPGRpdiBjbGFzcz1cIm1hcCBtdC0yXCI+PC9kaXY+JyArXHJcbiAgICAgICAgICAgICc8ZGl2IGNsYXNzPVwibWFwaW5mbyBtdC0yIHRleHQtbXV0ZWRcIj7mnKrpgInmi6nkvY3nva48L2Rpdj4nLCfor7fpgInmi6nlnLDlm77kvY3nva4nKTtcclxuICAgIH1cclxufTtcclxuXHJcbmpRdWVyeShmdW5jdGlvbigkKXtcclxuXHJcbiAgICAvL+ebkeaOp+aMiemUrlxyXG4gICAgJChkb2N1bWVudCkub24oJ2tleWRvd24nLCBmdW5jdGlvbihlKXtcclxuICAgICAgICBpZighRGlhbG9nLmluc3RhbmNlKXJldHVybjtcclxuICAgICAgICB2YXIgZGxnPURpYWxvZy5pbnN0YW5jZTtcclxuICAgICAgICBpZiAoZS5rZXlDb2RlID09IDEzKSB7XHJcbiAgICAgICAgICAgIGRsZy5ib3guZmluZCgnLm1vZGFsLWZvb3RlciAuYnRuJykuZXEoZGxnLm9wdGlvbnMuZGVmYXVsdEJ0bikudHJpZ2dlcignY2xpY2snKTtcclxuICAgICAgICB9XHJcbiAgICAgICAgLy/pu5jorqTlt7Lnm5HlkKzlhbPpl61cclxuICAgICAgICAvKmlmIChlLmtleUNvZGUgPT0gMjcpIHtcclxuICAgICAgICAgc2VsZi5oaWRlKCk7XHJcbiAgICAgICAgIH0qL1xyXG4gICAgfSk7XHJcbn0pOyIsIlxyXG5qUXVlcnkuZXh0ZW5kKGpRdWVyeS5mbix7XHJcbiAgICB0YWdzOmZ1bmN0aW9uKG5tKXtcclxuICAgICAgICB2YXIgZGF0YT1bXTtcclxuICAgICAgICB2YXIgdHBsPSc8c3BhbiBjbGFzcz1cImJhZGdlIGJhZGdlLWluZm9cIj57QGxhYmVsfTxpbnB1dCB0eXBlPVwiaGlkZGVuXCIgbmFtZT1cIicrbm0rJ1wiIHZhbHVlPVwie0BsYWJlbH1cIi8+PGJ1dHRvbiB0eXBlPVwiYnV0dG9uXCIgY2xhc3M9XCJjbG9zZVwiIGRhdGEtZGlzbWlzcz1cImFsZXJ0XCIgYXJpYS1sYWJlbD1cIkNsb3NlXCI+PHNwYW4gYXJpYS1oaWRkZW49XCJ0cnVlXCI+JnRpbWVzOzwvc3Bhbj48L2J1dHRvbj48L3NwYW4+JztcclxuICAgICAgICB2YXIgaXRlbT0kKHRoaXMpLnBhcmVudHMoJy5mb3JtLWNvbnRyb2wnKTtcclxuICAgICAgICB2YXIgbGFiZWxncm91cD0kKCc8c3BhbiBjbGFzcz1cImJhZGdlLWdyb3VwXCI+PC9zcGFuPicpO1xyXG4gICAgICAgIHZhciBpbnB1dD10aGlzO1xyXG4gICAgICAgIHRoaXMuYmVmb3JlKGxhYmVsZ3JvdXApO1xyXG4gICAgICAgIHRoaXMub24oJ2tleXVwJyxmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICB2YXIgdmFsPSQodGhpcykudmFsKCkucmVwbGFjZSgv77yML2csJywnKTtcclxuICAgICAgICAgICAgaWYodmFsICYmIHZhbC5pbmRleE9mKCcsJyk+LTEpe1xyXG4gICAgICAgICAgICAgICAgdmFyIHZhbHM9dmFsLnNwbGl0KCcsJyk7XHJcbiAgICAgICAgICAgICAgICBmb3IodmFyIGk9MDtpPHZhbHMubGVuZ3RoO2krKyl7XHJcbiAgICAgICAgICAgICAgICAgICAgdmFsc1tpXT12YWxzW2ldLnJlcGxhY2UoL15cXHN8XFxzJC9nLCcnKTtcclxuICAgICAgICAgICAgICAgICAgICBpZih2YWxzW2ldICYmIGRhdGEuaW5kZXhPZih2YWxzW2ldKT09PS0xKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgZGF0YS5wdXNoKHZhbHNbaV0pO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBsYWJlbGdyb3VwLmFwcGVuZCh0cGwuY29tcGlsZSh7bGFiZWw6dmFsc1tpXX0pKTtcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICBpbnB1dC52YWwoJycpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSkub24oJ2JsdXInLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgICQodGhpcykudmFsKCQodGhpcykudmFsKCkrJywnKS50cmlnZ2VyKCdrZXl1cCcpO1xyXG4gICAgICAgIH0pLnRyaWdnZXIoJ2tleXVwJyk7XHJcbiAgICAgICAgbGFiZWxncm91cC5vbignY2xpY2snLCcuY2xvc2UnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgIHZhciB0YWc9JCh0aGlzKS5wYXJlbnRzKCcuYmFkZ2UnKS5maW5kKCdpbnB1dCcpLnZhbCgpO1xyXG4gICAgICAgICAgICB2YXIgaWQ9ZGF0YS5pbmRleE9mKHRhZyk7XHJcbiAgICAgICAgICAgIGlmKGlkKWRhdGEuc3BsaWNlKGlkLDEpO1xyXG4gICAgICAgICAgICAkKHRoaXMpLnBhcmVudHMoJy5iYWRnZScpLnJlbW92ZSgpO1xyXG4gICAgICAgIH0pO1xyXG4gICAgICAgIGl0ZW0uY2xpY2soZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgaW5wdXQuZm9jdXMoKTtcclxuICAgICAgICB9KTtcclxuICAgIH1cclxufSk7IiwiLy/ml6XmnJ/nu4Tku7ZcclxuaWYoJC5mbi5kYXRldGltZXBpY2tlcikge1xyXG4gICAgdmFyIHRvb2x0aXBzPSB7XHJcbiAgICAgICAgdG9kYXk6ICflrprkvY3lvZPliY3ml6XmnJ8nLFxyXG4gICAgICAgIGNsZWFyOiAn5riF6Zmk5bey6YCJ5pel5pyfJyxcclxuICAgICAgICBjbG9zZTogJ+WFs+mXremAieaLqeWZqCcsXHJcbiAgICAgICAgc2VsZWN0TW9udGg6ICfpgInmi6nmnIjku70nLFxyXG4gICAgICAgIHByZXZNb250aDogJ+S4iuS4quaciCcsXHJcbiAgICAgICAgbmV4dE1vbnRoOiAn5LiL5Liq5pyIJyxcclxuICAgICAgICBzZWxlY3RZZWFyOiAn6YCJ5oup5bm05Lu9JyxcclxuICAgICAgICBwcmV2WWVhcjogJ+S4iuS4gOW5tCcsXHJcbiAgICAgICAgbmV4dFllYXI6ICfkuIvkuIDlubQnLFxyXG4gICAgICAgIHNlbGVjdERlY2FkZTogJ+mAieaLqeW5tOS7veWMuumXtCcsXHJcbiAgICAgICAgcHJldkRlY2FkZTogJ+S4iuS4gOWMuumXtCcsXHJcbiAgICAgICAgbmV4dERlY2FkZTogJ+S4i+S4gOWMuumXtCcsXHJcbiAgICAgICAgcHJldkNlbnR1cnk6ICfkuIrkuKrkuJbnuqonLFxyXG4gICAgICAgIG5leHRDZW50dXJ5OiAn5LiL5Liq5LiW57qqJ1xyXG4gICAgfTtcclxuICAgIHZhciBpY29ucz17XHJcbiAgICAgICAgdGltZTogJ2lvbi1tZC10aW1lJyxcclxuICAgICAgICBkYXRlOiAnaW9uLW1kLWNhbGVuZGFyJyxcclxuICAgICAgICB1cDogJ2lvbi1tZC1hcnJvdy1kcm9wdXAnLFxyXG4gICAgICAgIGRvd246ICdpb24tbWQtYXJyb3ctZHJvcGRvd24nLFxyXG4gICAgICAgIHByZXZpb3VzOiAnaW9uLW1kLWFycm93LWRyb3BsZWZ0JyxcclxuICAgICAgICBuZXh0OiAnaW9uLW1kLWFycm93LWRyb3ByaWdodCcsXHJcbiAgICAgICAgdG9kYXk6ICdpb24tbWQtdG9kYXknLFxyXG4gICAgICAgIGNsZWFyOiAnaW9uLW1kLXRyYXNoJyxcclxuICAgICAgICBjbG9zZTogJ2lvbi1tZC1jbG9zZSdcclxuICAgIH07XHJcbiAgICAkKCcuZGF0ZXBpY2tlcicpLmRhdGV0aW1lcGlja2VyKHtcclxuICAgICAgICBpY29uczppY29ucyxcclxuICAgICAgICB0b29sdGlwczp0b29sdGlwcyxcclxuICAgICAgICBmb3JtYXQ6ICdZWVlZLU1NLUREJyxcclxuICAgICAgICBsb2NhbGU6ICd6aC1jbicsXHJcbiAgICAgICAgc2hvd0NsZWFyOnRydWUsXHJcbiAgICAgICAgc2hvd1RvZGF5QnV0dG9uOnRydWUsXHJcbiAgICAgICAgc2hvd0Nsb3NlOnRydWUsXHJcbiAgICAgICAga2VlcEludmFsaWQ6dHJ1ZVxyXG4gICAgfSk7XHJcblxyXG4gICAgJCgnLmRhdGUtcmFuZ2UnKS5lYWNoKGZ1bmN0aW9uICgpIHtcclxuICAgICAgICB2YXIgZnJvbSA9ICQodGhpcykuZmluZCgnW25hbWU9ZnJvbWRhdGVdLC5mcm9tZGF0ZScpLCB0byA9ICQodGhpcykuZmluZCgnW25hbWU9dG9kYXRlXSwudG9kYXRlJyk7XHJcbiAgICAgICAgdmFyIG9wdGlvbnMgPSB7XHJcbiAgICAgICAgICAgIGljb25zOmljb25zLFxyXG4gICAgICAgICAgICB0b29sdGlwczp0b29sdGlwcyxcclxuICAgICAgICAgICAgZm9ybWF0OiAnWVlZWS1NTS1ERCcsXHJcbiAgICAgICAgICAgIGxvY2FsZTonemgtY24nLFxyXG4gICAgICAgICAgICBzaG93Q2xlYXI6dHJ1ZSxcclxuICAgICAgICAgICAgc2hvd1RvZGF5QnV0dG9uOnRydWUsXHJcbiAgICAgICAgICAgIHNob3dDbG9zZTp0cnVlLFxyXG4gICAgICAgICAgICBrZWVwSW52YWxpZDp0cnVlXHJcbiAgICAgICAgfTtcclxuICAgICAgICBmcm9tLmRhdGV0aW1lcGlja2VyKG9wdGlvbnMpLm9uKCdkcC5jaGFuZ2UnLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIGlmIChmcm9tLnZhbCgpKSB7XHJcbiAgICAgICAgICAgICAgICB0by5kYXRhKCdEYXRlVGltZVBpY2tlcicpLm1pbkRhdGUoZnJvbS52YWwoKSk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KTtcclxuICAgICAgICB0by5kYXRldGltZXBpY2tlcihvcHRpb25zKS5vbignZHAuY2hhbmdlJywgZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICBpZiAodG8udmFsKCkpIHtcclxuICAgICAgICAgICAgICAgIGZyb20uZGF0YSgnRGF0ZVRpbWVQaWNrZXInKS5tYXhEYXRlKHRvLnZhbCgpKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pO1xyXG4gICAgfSk7XHJcbn0iLCJqUXVlcnkoZnVuY3Rpb24oJCl7XHJcblxyXG59KTsiXX0=
