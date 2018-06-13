function del(obj,msg) {
    dialog.confirm(msg,function(){
        location.href=$(obj).attr('href');
    });
    return false;
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
jQuery(function($){

});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNvbW1vbi5qcyIsInRlbXBsYXRlLmpzIiwiZGlhbG9nLmpzIiwianF1ZXJ5LnRhZy5qcyIsImRhdGV0aW1lLmluaXQuanMiLCJmcm9udC5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUNMQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUM5REE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUN4VEE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDbkNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDL0RBO0FBQ0E7QUFDQSIsImZpbGUiOiJmcm9udC5qcyIsInNvdXJjZXNDb250ZW50IjpbImZ1bmN0aW9uIGRlbChvYmosbXNnKSB7XHJcbiAgICBkaWFsb2cuY29uZmlybShtc2csZnVuY3Rpb24oKXtcclxuICAgICAgICBsb2NhdGlvbi5ocmVmPSQob2JqKS5hdHRyKCdocmVmJyk7XHJcbiAgICB9KTtcclxuICAgIHJldHVybiBmYWxzZTtcclxufSIsIlxyXG5OdW1iZXIucHJvdG90eXBlLmZvcm1hdD1mdW5jdGlvbihmaXgpe1xyXG4gICAgaWYoZml4PT09dW5kZWZpbmVkKWZpeD0yO1xyXG4gICAgdmFyIG51bT10aGlzLnRvRml4ZWQoZml4KTtcclxuICAgIHZhciB6PW51bS5zcGxpdCgnLicpO1xyXG4gICAgdmFyIGZvcm1hdD1bXSxmPXpbMF0uc3BsaXQoJycpLGw9Zi5sZW5ndGg7XHJcbiAgICBmb3IodmFyIGk9MDtpPGw7aSsrKXtcclxuICAgICAgICBpZihpPjAgJiYgaSAlIDM9PTApe1xyXG4gICAgICAgICAgICBmb3JtYXQudW5zaGlmdCgnLCcpO1xyXG4gICAgICAgIH1cclxuICAgICAgICBmb3JtYXQudW5zaGlmdChmW2wtaS0xXSk7XHJcbiAgICB9XHJcbiAgICByZXR1cm4gZm9ybWF0LmpvaW4oJycpKyh6Lmxlbmd0aD09Mj8nLicrelsxXTonJyk7XHJcbn07XHJcblN0cmluZy5wcm90b3R5cGUuY29tcGlsZT1mdW5jdGlvbihkYXRhLGxpc3Qpe1xyXG5cclxuICAgIGlmKGxpc3Qpe1xyXG4gICAgICAgIHZhciB0ZW1wcz1bXTtcclxuICAgICAgICBmb3IodmFyIGkgaW4gZGF0YSl7XHJcbiAgICAgICAgICAgIHRlbXBzLnB1c2godGhpcy5jb21waWxlKGRhdGFbaV0pKTtcclxuICAgICAgICB9XHJcbiAgICAgICAgcmV0dXJuIHRlbXBzLmpvaW4oXCJcXG5cIik7XHJcbiAgICB9ZWxzZXtcclxuICAgICAgICByZXR1cm4gdGhpcy5yZXBsYWNlKC9cXHtAKFtcXHdcXGRcXC5dKykoPzpcXHwoW1xcd1xcZF0rKSg/Olxccyo9XFxzKihbXFx3XFxkLFxccyNdKykpPyk/XFx9L2csZnVuY3Rpb24oYWxsLG0xLGZ1bmMsYXJncyl7XHJcblxyXG4gICAgICAgICAgICBpZihtMS5pbmRleE9mKCcuJyk+MCl7XHJcbiAgICAgICAgICAgICAgICB2YXIga2V5cz1tMS5zcGxpdCgnLicpLHZhbD1kYXRhO1xyXG4gICAgICAgICAgICAgICAgZm9yKHZhciBpPTA7aTxrZXlzLmxlbmd0aDtpKyspe1xyXG4gICAgICAgICAgICAgICAgICAgIGlmKHZhbFtrZXlzW2ldXSl7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhbD12YWxba2V5c1tpXV07XHJcbiAgICAgICAgICAgICAgICAgICAgfWVsc2V7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhbCA9ICcnO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICByZXR1cm4gY2FsbGZ1bmModmFsLGZ1bmMsYXJncyk7XHJcbiAgICAgICAgICAgIH1lbHNle1xyXG4gICAgICAgICAgICAgICAgcmV0dXJuIGRhdGFbbTFdP2NhbGxmdW5jKGRhdGFbbTFdLGZ1bmMsYXJncyxkYXRhKTonJztcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pO1xyXG4gICAgfVxyXG59O1xyXG5cclxuZnVuY3Rpb24gY2FsbGZ1bmModmFsLGZ1bmMsYXJncyx0aGlzb2JqKXtcclxuICAgIGlmKCFhcmdzKXtcclxuICAgICAgICBhcmdzPVt2YWxdO1xyXG4gICAgfWVsc2V7XHJcbiAgICAgICAgaWYodHlwZW9mIGFyZ3M9PT0nc3RyaW5nJylhcmdzPWFyZ3Muc3BsaXQoJywnKTtcclxuICAgICAgICB2YXIgYXJnaWR4PWFyZ3MuaW5kZXhPZignIyMjJyk7XHJcbiAgICAgICAgaWYoYXJnaWR4Pj0wKXtcclxuICAgICAgICAgICAgYXJnc1thcmdpZHhdPXZhbDtcclxuICAgICAgICB9ZWxzZXtcclxuICAgICAgICAgICAgYXJncz1bdmFsXS5jb25jYXQoYXJncyk7XHJcbiAgICAgICAgfVxyXG4gICAgfVxyXG4gICAgLy9jb25zb2xlLmxvZyhhcmdzKTtcclxuICAgIHJldHVybiB3aW5kb3dbZnVuY10/d2luZG93W2Z1bmNdLmFwcGx5KHRoaXNvYmosYXJncyk6dmFsO1xyXG59XHJcblxyXG5mdW5jdGlvbiBpaWYodixtMSxtMil7XHJcbiAgICBpZih2PT09JzAnKXY9MDtcclxuICAgIHJldHVybiB2P20xOm0yO1xyXG59IiwiXHJcbnZhciBkaWFsb2dUcGw9JzxkaXYgY2xhc3M9XCJtb2RhbCBmYWRlXCIgaWQ9XCJ7QGlkfVwiIHRhYmluZGV4PVwiLTFcIiByb2xlPVwiZGlhbG9nXCIgYXJpYS1sYWJlbGxlZGJ5PVwie0BpZH1MYWJlbFwiIGFyaWEtaGlkZGVuPVwidHJ1ZVwiPlxcbicgK1xyXG4gICAgJyAgICA8ZGl2IGNsYXNzPVwibW9kYWwtZGlhbG9nXCI+XFxuJyArXHJcbiAgICAnICAgICAgICA8ZGl2IGNsYXNzPVwibW9kYWwtY29udGVudFwiPlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJtb2RhbC1oZWFkZXJcIj5cXG4nICtcclxuICAgICcgICAgICAgICAgICAgICAgPGg0IGNsYXNzPVwibW9kYWwtdGl0bGVcIiBpZD1cIntAaWR9TGFiZWxcIj48L2g0PlxcbicgK1xyXG4gICAgJyAgICAgICAgICAgICAgICA8YnV0dG9uIHR5cGU9XCJidXR0b25cIiBjbGFzcz1cImNsb3NlXCIgZGF0YS1kaXNtaXNzPVwibW9kYWxcIj5cXG4nICtcclxuICAgICcgICAgICAgICAgICAgICAgICAgIDxzcGFuIGFyaWEtaGlkZGVuPVwidHJ1ZVwiPiZ0aW1lczs8L3NwYW4+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgICAgICAgICA8c3BhbiBjbGFzcz1cInNyLW9ubHlcIj5DbG9zZTwvc3Bhbj5cXG4nICtcclxuICAgICcgICAgICAgICAgICAgICAgPC9idXR0b24+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgPC9kaXY+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgPGRpdiBjbGFzcz1cIm1vZGFsLWJvZHlcIj5cXG4nICtcclxuICAgICcgICAgICAgICAgICA8L2Rpdj5cXG4nICtcclxuICAgICcgICAgICAgICAgICA8ZGl2IGNsYXNzPVwibW9kYWwtZm9vdGVyXCI+XFxuJyArXHJcbiAgICAnICAgICAgICAgICAgICAgIDxuYXYgY2xhc3M9XCJuYXYgbmF2LWZpbGxcIj48L25hdj5cXG4nICtcclxuICAgICcgICAgICAgICAgICA8L2Rpdj5cXG4nICtcclxuICAgICcgICAgICAgIDwvZGl2PlxcbicgK1xyXG4gICAgJyAgICA8L2Rpdj5cXG4nICtcclxuICAgICc8L2Rpdj4nO1xyXG52YXIgZGlhbG9nSWR4PTA7XHJcbmZ1bmN0aW9uIERpYWxvZyhvcHRzKXtcclxuICAgIGlmKCFvcHRzKW9wdHM9e307XHJcbiAgICAvL+WkhOeQhuaMiemSrlxyXG4gICAgaWYob3B0cy5idG5zIT09dW5kZWZpbmVkKSB7XHJcbiAgICAgICAgaWYgKHR5cGVvZihvcHRzLmJ0bnMpID09ICdzdHJpbmcnKSB7XHJcbiAgICAgICAgICAgIG9wdHMuYnRucyA9IFtvcHRzLmJ0bnNdO1xyXG4gICAgICAgIH1cclxuICAgICAgICB2YXIgZGZ0PS0xO1xyXG4gICAgICAgIGZvciAodmFyIGkgPSAwOyBpIDwgb3B0cy5idG5zLmxlbmd0aDsgaSsrKSB7XHJcbiAgICAgICAgICAgIGlmKHR5cGVvZihvcHRzLmJ0bnNbaV0pPT0nc3RyaW5nJyl7XHJcbiAgICAgICAgICAgICAgICBvcHRzLmJ0bnNbaV09eyd0ZXh0JzpvcHRzLmJ0bnNbaV19O1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIGlmKG9wdHMuYnRuc1tpXS5pc2RlZmF1bHQpe1xyXG4gICAgICAgICAgICAgICAgZGZ0PWk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9XHJcbiAgICAgICAgaWYoZGZ0PDApe1xyXG4gICAgICAgICAgICBkZnQ9b3B0cy5idG5zLmxlbmd0aC0xO1xyXG4gICAgICAgICAgICBvcHRzLmJ0bnNbZGZ0XS5pc2RlZmF1bHQ9dHJ1ZTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIGlmKCFvcHRzLmJ0bnNbZGZ0XVsndHlwZSddKXtcclxuICAgICAgICAgICAgb3B0cy5idG5zW2RmdF1bJ3R5cGUnXT0ncHJpbWFyeSc7XHJcbiAgICAgICAgfVxyXG4gICAgICAgIG9wdHMuZGVmYXVsdEJ0bj1kZnQ7XHJcbiAgICB9XHJcblxyXG4gICAgdGhpcy5vcHRpb25zPSQuZXh0ZW5kKHtcclxuICAgICAgICAnaWQnOidkbGdNb2RhbCcrZGlhbG9nSWR4KyssXHJcbiAgICAgICAgJ3NpemUnOicnLFxyXG4gICAgICAgICdidG5zJzpbXHJcbiAgICAgICAgICAgIHsndGV4dCc6J+WPlua2iCcsJ3R5cGUnOidzZWNvbmRhcnknfSxcclxuICAgICAgICAgICAgeyd0ZXh0Jzon56Gu5a6aJywnaXNkZWZhdWx0Jzp0cnVlLCd0eXBlJzoncHJpbWFyeSd9XHJcbiAgICAgICAgXSxcclxuICAgICAgICAnZGVmYXVsdEJ0bic6MSxcclxuICAgICAgICAnb25zdXJlJzpudWxsLFxyXG4gICAgICAgICdvbnNob3cnOm51bGwsXHJcbiAgICAgICAgJ29uc2hvd24nOm51bGwsXHJcbiAgICAgICAgJ29uaGlkZSc6bnVsbCxcclxuICAgICAgICAnb25oaWRkZW4nOm51bGxcclxuICAgIH0sb3B0cyk7XHJcblxyXG4gICAgdGhpcy5ib3g9JCh0aGlzLm9wdGlvbnMuaWQpO1xyXG59XHJcbkRpYWxvZy5wcm90b3R5cGUuZ2VuZXJCdG49ZnVuY3Rpb24ob3B0LGlkeCl7XHJcbiAgICBpZihvcHRbJ3R5cGUnXSlvcHRbJ2NsYXNzJ109J2J0bi1vdXRsaW5lLScrb3B0Wyd0eXBlJ107XHJcbiAgICByZXR1cm4gJzxhIGhyZWY9XCJqYXZhc2NyaXB0OlwiIGNsYXNzPVwibmF2LWl0ZW0gYnRuICcrKG9wdFsnY2xhc3MnXT9vcHRbJ2NsYXNzJ106J2J0bi1vdXRsaW5lLXNlY29uZGFyeScpKydcIiBkYXRhLWluZGV4PVwiJytpZHgrJ1wiPicrb3B0LnRleHQrJzwvYT4nO1xyXG59O1xyXG5EaWFsb2cucHJvdG90eXBlLnNob3c9ZnVuY3Rpb24oaHRtbCx0aXRsZSl7XHJcbiAgICB0aGlzLmJveD0kKCcjJyt0aGlzLm9wdGlvbnMuaWQpO1xyXG4gICAgaWYoIXRpdGxlKXRpdGxlPSfns7vnu5/mj5DnpLonO1xyXG4gICAgaWYodGhpcy5ib3gubGVuZ3RoPDEpIHtcclxuICAgICAgICAkKGRvY3VtZW50LmJvZHkpLmFwcGVuZChkaWFsb2dUcGwuY29tcGlsZSh7J2lkJzogdGhpcy5vcHRpb25zLmlkfSkpO1xyXG4gICAgICAgIHRoaXMuYm94PSQoJyMnK3RoaXMub3B0aW9ucy5pZCk7XHJcbiAgICB9ZWxzZXtcclxuICAgICAgICB0aGlzLmJveC51bmJpbmQoKTtcclxuICAgIH1cclxuXHJcbiAgICAvL3RoaXMuYm94LmZpbmQoJy5tb2RhbC1mb290ZXIgLmJ0bi1wcmltYXJ5JykudW5iaW5kKCk7XHJcbiAgICB2YXIgc2VsZj10aGlzO1xyXG4gICAgRGlhbG9nLmluc3RhbmNlPXNlbGY7XHJcblxyXG4gICAgLy/nlJ/miJDmjInpkq5cclxuICAgIHZhciBidG5zPVtdO1xyXG4gICAgZm9yKHZhciBpPTA7aTx0aGlzLm9wdGlvbnMuYnRucy5sZW5ndGg7aSsrKXtcclxuICAgICAgICBidG5zLnB1c2godGhpcy5nZW5lckJ0bih0aGlzLm9wdGlvbnMuYnRuc1tpXSxpKSk7XHJcbiAgICB9XHJcbiAgICB0aGlzLmJveC5maW5kKCcubW9kYWwtZm9vdGVyIC5uYXYnKS5odG1sKGJ0bnMuam9pbignXFxuJykpO1xyXG5cclxuICAgIHZhciBkaWFsb2c9dGhpcy5ib3guZmluZCgnLm1vZGFsLWRpYWxvZycpO1xyXG4gICAgZGlhbG9nLnJlbW92ZUNsYXNzKCdtb2RhbC1zbScpLnJlbW92ZUNsYXNzKCdtb2RhbC1sZycpO1xyXG4gICAgaWYodGhpcy5vcHRpb25zLnNpemU9PSdzbScpIHtcclxuICAgICAgICBkaWFsb2cuYWRkQ2xhc3MoJ21vZGFsLXNtJyk7XHJcbiAgICB9ZWxzZSBpZih0aGlzLm9wdGlvbnMuc2l6ZT09J2xnJykge1xyXG4gICAgICAgIGRpYWxvZy5hZGRDbGFzcygnbW9kYWwtbGcnKTtcclxuICAgIH1cclxuICAgIHRoaXMuYm94LmZpbmQoJy5tb2RhbC10aXRsZScpLnRleHQodGl0bGUpO1xyXG5cclxuICAgIHZhciBib2R5PXRoaXMuYm94LmZpbmQoJy5tb2RhbC1ib2R5Jyk7XHJcbiAgICBib2R5Lmh0bWwoaHRtbCk7XHJcbiAgICB0aGlzLmJveC5vbignaGlkZS5icy5tb2RhbCcsZnVuY3Rpb24oKXtcclxuICAgICAgICBpZihzZWxmLm9wdGlvbnMub25oaWRlKXtcclxuICAgICAgICAgICAgc2VsZi5vcHRpb25zLm9uaGlkZShib2R5LHNlbGYuYm94KTtcclxuICAgICAgICB9XHJcbiAgICAgICAgRGlhbG9nLmluc3RhbmNlPW51bGw7XHJcbiAgICB9KTtcclxuICAgIHRoaXMuYm94Lm9uKCdoaWRkZW4uYnMubW9kYWwnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgaWYoc2VsZi5vcHRpb25zLm9uaGlkZGVuKXtcclxuICAgICAgICAgICAgc2VsZi5vcHRpb25zLm9uaGlkZGVuKGJvZHksc2VsZi5ib3gpO1xyXG4gICAgICAgIH1cclxuICAgICAgICBzZWxmLmJveC5yZW1vdmUoKTtcclxuICAgIH0pO1xyXG4gICAgdGhpcy5ib3gub24oJ3Nob3cuYnMubW9kYWwnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgaWYoc2VsZi5vcHRpb25zLm9uc2hvdyl7XHJcbiAgICAgICAgICAgIHNlbGYub3B0aW9ucy5vbnNob3coYm9keSxzZWxmLmJveCk7XHJcbiAgICAgICAgfVxyXG4gICAgfSk7XHJcbiAgICB0aGlzLmJveC5vbignc2hvd24uYnMubW9kYWwnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgaWYoc2VsZi5vcHRpb25zLm9uc2hvd24pe1xyXG4gICAgICAgICAgICBzZWxmLm9wdGlvbnMub25zaG93bihib2R5LHNlbGYuYm94KTtcclxuICAgICAgICB9XHJcbiAgICB9KTtcclxuICAgIHRoaXMuYm94LmZpbmQoJy5tb2RhbC1mb290ZXIgLmJ0bicpLmNsaWNrKGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgdmFyIHJlc3VsdD10cnVlLGlkeD0kKHRoaXMpLmRhdGEoJ2luZGV4Jyk7XHJcbiAgICAgICAgaWYoc2VsZi5vcHRpb25zLmJ0bnNbaWR4XS5jbGljayl7XHJcbiAgICAgICAgICAgIHJlc3VsdCA9IHNlbGYub3B0aW9ucy5idG5zW2lkeF0uY2xpY2suYXBwbHkodGhpcyxbYm9keSwgc2VsZi5ib3hdKTtcclxuICAgICAgICB9XHJcbiAgICAgICAgaWYoaWR4PT1zZWxmLm9wdGlvbnMuZGVmYXVsdEJ0bikge1xyXG4gICAgICAgICAgICBpZiAoc2VsZi5vcHRpb25zLm9uc3VyZSkge1xyXG4gICAgICAgICAgICAgICAgcmVzdWx0ID0gc2VsZi5vcHRpb25zLm9uc3VyZS5hcHBseSh0aGlzLFtib2R5LCBzZWxmLmJveF0pO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfVxyXG4gICAgICAgIGlmKHJlc3VsdCE9PWZhbHNlKXtcclxuICAgICAgICAgICAgc2VsZi5ib3gubW9kYWwoJ2hpZGUnKTtcclxuICAgICAgICB9XHJcbiAgICB9KTtcclxuICAgIHRoaXMuYm94Lm1vZGFsKCdzaG93Jyk7XHJcbiAgICByZXR1cm4gdGhpcztcclxufTtcclxuRGlhbG9nLnByb3RvdHlwZS5oaWRlPWZ1bmN0aW9uKCl7XHJcbiAgICB0aGlzLmJveC5tb2RhbCgnaGlkZScpO1xyXG4gICAgcmV0dXJuIHRoaXM7XHJcbn07XHJcblxyXG52YXIgZGlhbG9nPXtcclxuICAgIGFsZXJ0OmZ1bmN0aW9uKG1lc3NhZ2UsY2FsbGJhY2ssdGl0bGUpe1xyXG4gICAgICAgIHZhciBjYWxsZWQ9ZmFsc2U7XHJcbiAgICAgICAgdmFyIGlzY2FsbGJhY2s9dHlwZW9mIGNhbGxiYWNrPT0nZnVuY3Rpb24nO1xyXG4gICAgICAgIHJldHVybiBuZXcgRGlhbG9nKHtcclxuICAgICAgICAgICAgYnRuczon56Gu5a6aJyxcclxuICAgICAgICAgICAgb25zdXJlOmZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgICAgICBpZihpc2NhbGxiYWNrKXtcclxuICAgICAgICAgICAgICAgICAgICBjYWxsZWQ9dHJ1ZTtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gY2FsbGJhY2sodHJ1ZSk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgIG9uaGlkZTpmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICAgICAgaWYoIWNhbGxlZCAmJiBpc2NhbGxiYWNrKXtcclxuICAgICAgICAgICAgICAgICAgICBjYWxsYmFjayhmYWxzZSk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS5zaG93KG1lc3NhZ2UsdGl0bGUpO1xyXG4gICAgfSxcclxuICAgIGNvbmZpcm06ZnVuY3Rpb24obWVzc2FnZSxjb25maXJtLGNhbmNlbCl7XHJcbiAgICAgICAgdmFyIGNhbGxlZD1mYWxzZTtcclxuICAgICAgICByZXR1cm4gbmV3IERpYWxvZyh7XHJcbiAgICAgICAgICAgICdvbnN1cmUnOmZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgICAgICBpZih0eXBlb2YgY29uZmlybT09J2Z1bmN0aW9uJyl7XHJcbiAgICAgICAgICAgICAgICAgICAgY2FsbGVkPXRydWU7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGNvbmZpcm0oKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgJ29uaGlkZSc6ZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAgICAgaWYoY2FsbGVkPWZhbHNlICYmIHR5cGVvZiBjYW5jZWw9PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBjYW5jZWwoKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLnNob3cobWVzc2FnZSk7XHJcbiAgICB9LFxyXG4gICAgcHJvbXB0OmZ1bmN0aW9uKG1lc3NhZ2UsY2FsbGJhY2ssY2FuY2VsKXtcclxuICAgICAgICB2YXIgY2FsbGVkPWZhbHNlO1xyXG4gICAgICAgIHJldHVybiBuZXcgRGlhbG9nKHtcclxuICAgICAgICAgICAgJ29uc2hvd24nOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgYm9keS5maW5kKCdbbmFtZT1jb25maXJtX2lucHV0XScpLmZvY3VzKCk7XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdvbnN1cmUnOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgdmFyIHZhbD1ib2R5LmZpbmQoJ1tuYW1lPWNvbmZpcm1faW5wdXRdJykudmFsKCk7XHJcbiAgICAgICAgICAgICAgICBpZih0eXBlb2YgY2FsbGJhY2s9PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgICAgIHZhciByZXN1bHQgPSBjYWxsYmFjayh2YWwpO1xyXG4gICAgICAgICAgICAgICAgICAgIGlmKHJlc3VsdD09PXRydWUpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBjYWxsZWQ9dHJ1ZTtcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHJlc3VsdDtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgJ29uaGlkZSc6ZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAgICAgaWYoY2FsbGVkPWZhbHNlICYmIHR5cGVvZiBjYW5jZWw9PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBjYW5jZWwoKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLnNob3coJzxpbnB1dCB0eXBlPVwidGV4dFwiIG5hbWU9XCJjb25maXJtX2lucHV0XCIgY2xhc3M9XCJmb3JtLWNvbnRyb2xcIiAvPicsbWVzc2FnZSk7XHJcbiAgICB9LFxyXG4gICAgcGlja1VzZXI6ZnVuY3Rpb24odXJsLGNhbGxiYWNrLGZpbHRlcil7XHJcbiAgICAgICAgdmFyIHVzZXI9bnVsbDtcclxuICAgICAgICBpZighZmlsdGVyKWZpbHRlcj17fTtcclxuICAgICAgICB2YXIgZGxnPW5ldyBEaWFsb2coe1xyXG4gICAgICAgICAgICAnb25zaG93bic6ZnVuY3Rpb24oYm9keSl7XHJcbiAgICAgICAgICAgICAgICB2YXIgYnRuPWJvZHkuZmluZCgnLnNlYXJjaGJ0bicpO1xyXG4gICAgICAgICAgICAgICAgdmFyIGlucHV0PWJvZHkuZmluZCgnLnNlYXJjaHRleHQnKTtcclxuICAgICAgICAgICAgICAgIHZhciBsaXN0Ym94PWJvZHkuZmluZCgnLmxpc3QtZ3JvdXAnKTtcclxuICAgICAgICAgICAgICAgIHZhciBpc2xvYWRpbmc9ZmFsc2U7XHJcbiAgICAgICAgICAgICAgICBidG4uY2xpY2soZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgICAgICAgICBpZihpc2xvYWRpbmcpcmV0dXJuO1xyXG4gICAgICAgICAgICAgICAgICAgIGlzbG9hZGluZz10cnVlO1xyXG4gICAgICAgICAgICAgICAgICAgIGxpc3Rib3guaHRtbCgnPHNwYW4gY2xhc3M9XCJsaXN0LWxvYWRpbmdcIj7liqDovb3kuK0uLi48L3NwYW4+Jyk7XHJcbiAgICAgICAgICAgICAgICAgICAgZmlsdGVyWydrZXknXT1pbnB1dC52YWwoKTtcclxuICAgICAgICAgICAgICAgICAgICAkLmFqYXgoXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHVybDp1cmwsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0eXBlOidHRVQnLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZGF0YVR5cGU6J0pTT04nLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZGF0YTpmaWx0ZXIsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBzdWNjZXNzOmZ1bmN0aW9uKGpzb24pe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlzbG9hZGluZz1mYWxzZTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZihqc29uLnN0YXR1cyl7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmKGpzb24uZGF0YSAmJiBqc29uLmRhdGEubGVuZ3RoKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBsaXN0Ym94Lmh0bWwoJzxhIGhyZWY9XCJqYXZhc2NyaXB0OlwiIGRhdGEtaWQ9XCJ7QGlkfVwiIGNsYXNzPVwibGlzdC1ncm91cC1pdGVtIGxpc3QtZ3JvdXAtaXRlbS1hY3Rpb25cIj5be0BpZH1dJm5ic3A7PGkgY2xhc3M9XCJpb24tbWQtcGVyc29uXCI+PC9pPiB7QHVzZXJuYW1lfSZuYnNwOyZuYnNwOyZuYnNwOzxzbWFsbD48aSBjbGFzcz1cImlvbi1tZC1waG9uZS1wb3J0cmFpdFwiPjwvaT4ge0Btb2JpbGV9PC9zbWFsbD48L2E+Jy5jb21waWxlKGpzb24uZGF0YSwgdHJ1ZSkpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbGlzdGJveC5maW5kKCdhLmxpc3QtZ3JvdXAtaXRlbScpLmNsaWNrKGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgaWQgPSAkKHRoaXMpLmRhdGEoJ2lkJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgZm9yICh2YXIgaSA9IDA7IGkgPCBqc29uLmRhdGEubGVuZ3RoOyBpKyspIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYoanNvbi5kYXRhW2ldLmlkPT1pZCl7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB1c2VyPWpzb24uZGF0YVtpXTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxpc3Rib3guZmluZCgnYS5saXN0LWdyb3VwLWl0ZW0nKS5yZW1vdmVDbGFzcygnYWN0aXZlJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmFkZENsYXNzKCdhY3RpdmUnKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfSlcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfWVsc2V7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBsaXN0Ym94Lmh0bWwoJzxzcGFuIGNsYXNzPVwibGlzdC1sb2FkaW5nXCI+PGkgY2xhc3M9XCJpb24tbWQtd2FybmluZ1wiPjwvaT4g5rKh5pyJ5qOA57Si5Yiw5Lya5ZGYPC9zcGFuPicpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfWVsc2V7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxpc3Rib3guaHRtbCgnPHNwYW4gY2xhc3M9XCJ0ZXh0LWRhbmdlclwiPjxpIGNsYXNzPVwiaW9uLW1kLXdhcm5pbmdcIj48L2k+IOWKoOi9veWksei0pTwvc3Bhbj4nKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICApO1xyXG5cclxuICAgICAgICAgICAgICAgIH0pLnRyaWdnZXIoJ2NsaWNrJyk7XHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICdvbnN1cmUnOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgaWYoIXVzZXIpe1xyXG4gICAgICAgICAgICAgICAgICAgIHRvYXN0ci53YXJuaW5nKCfmsqHmnInpgInmi6nkvJrlkZghJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgaWYodHlwZW9mIGNhbGxiYWNrPT0nZnVuY3Rpb24nKXtcclxuICAgICAgICAgICAgICAgICAgICB2YXIgcmVzdWx0ID0gY2FsbGJhY2sodXNlcik7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHJlc3VsdDtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pLnNob3coJzxkaXYgY2xhc3M9XCJpbnB1dC1ncm91cFwiPjxpbnB1dCB0eXBlPVwidGV4dFwiIGNsYXNzPVwiZm9ybS1jb250cm9sIHNlYXJjaHRleHRcIiBuYW1lPVwia2V5d29yZFwiIHBsYWNlaG9sZGVyPVwi5qC55o2u5Lya5ZGYaWTmiJblkI3np7DvvIznlLXor53mnaXmkJzntKJcIi8+PGRpdiBjbGFzcz1cImlucHV0LWdyb3VwLWFwcGVuZFwiPjxhIGNsYXNzPVwiYnRuIGJ0bi1vdXRsaW5lLXNlY29uZGFyeSBzZWFyY2hidG5cIj48aSBjbGFzcz1cImlvbi1tZC1zZWFyY2hcIj48L2k+PC9hPjwvZGl2PjwvZGl2PjxkaXYgY2xhc3M9XCJsaXN0LWdyb3VwIG10LTJcIj48L2Rpdj4nLCfor7fmkJzntKLlubbpgInmi6nkvJrlkZgnKTtcclxuICAgIH0sXHJcbiAgICBwaWNrTG9jYXRlOmZ1bmN0aW9uKHR5cGUsIGNhbGxiYWNrLCBsb2NhdGUpe1xyXG4gICAgICAgIHZhciBzZXR0ZWRMb2NhdGU9bnVsbDtcclxuICAgICAgICB2YXIgZGxnPW5ldyBEaWFsb2coe1xyXG4gICAgICAgICAgICAnc2l6ZSc6J2xnJyxcclxuICAgICAgICAgICAgJ29uc2hvd24nOmZ1bmN0aW9uKGJvZHkpe1xyXG4gICAgICAgICAgICAgICAgdmFyIGJ0bj1ib2R5LmZpbmQoJy5zZWFyY2hidG4nKTtcclxuICAgICAgICAgICAgICAgIHZhciBpbnB1dD1ib2R5LmZpbmQoJy5zZWFyY2h0ZXh0Jyk7XHJcbiAgICAgICAgICAgICAgICB2YXIgbWFwYm94PWJvZHkuZmluZCgnLm1hcCcpO1xyXG4gICAgICAgICAgICAgICAgdmFyIG1hcGluZm89Ym9keS5maW5kKCcubWFwaW5mbycpO1xyXG4gICAgICAgICAgICAgICAgbWFwYm94LmNzcygnaGVpZ2h0JywkKHdpbmRvdykuaGVpZ2h0KCkqLjYpO1xyXG4gICAgICAgICAgICAgICAgdmFyIGlzbG9hZGluZz1mYWxzZTtcclxuICAgICAgICAgICAgICAgIHZhciBtYXA9SW5pdE1hcCgndGVuY2VudCcsbWFwYm94LGZ1bmN0aW9uKGFkZHJlc3MsbG9jYXRlKXtcclxuICAgICAgICAgICAgICAgICAgICBtYXBpbmZvLmh0bWwoYWRkcmVzcysnJm5ic3A7Jytsb2NhdGUubG5nKycsJytsb2NhdGUubGF0KTtcclxuICAgICAgICAgICAgICAgICAgICBzZXR0ZWRMb2NhdGU9bG9jYXRlO1xyXG4gICAgICAgICAgICAgICAgfSxsb2NhdGUpO1xyXG4gICAgICAgICAgICAgICAgYnRuLmNsaWNrKGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgICAgICAgICAgdmFyIHNlYXJjaD1pbnB1dC52YWwoKTtcclxuICAgICAgICAgICAgICAgICAgICBtYXAuc2V0TG9jYXRlKHNlYXJjaCk7XHJcbiAgICAgICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgJ29uc3VyZSc6ZnVuY3Rpb24oYm9keSl7XHJcbiAgICAgICAgICAgICAgICBpZighc2V0dGVkTG9jYXRlKXtcclxuICAgICAgICAgICAgICAgICAgICB0b2FzdHIud2FybmluZygn5rKh5pyJ6YCJ5oup5L2N572uIScpO1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIGlmKHR5cGVvZiBjYWxsYmFjaz09PSdmdW5jdGlvbicpe1xyXG4gICAgICAgICAgICAgICAgICAgIHZhciByZXN1bHQgPSBjYWxsYmFjayhzZXR0ZWRMb2NhdGUpO1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiByZXN1bHQ7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS5zaG93KCc8ZGl2IGNsYXNzPVwiaW5wdXQtZ3JvdXBcIj48aW5wdXQgdHlwZT1cInRleHRcIiBjbGFzcz1cImZvcm0tY29udHJvbCBzZWFyY2h0ZXh0XCIgbmFtZT1cImtleXdvcmRcIiBwbGFjZWhvbGRlcj1cIuWhq+WGmeWcsOWdgOajgOe0ouS9jee9rlwiLz48ZGl2IGNsYXNzPVwiaW5wdXQtZ3JvdXAtYXBwZW5kXCI+PGEgY2xhc3M9XCJidG4gYnRuLW91dGxpbmUtc2Vjb25kYXJ5IHNlYXJjaGJ0blwiPjxpIGNsYXNzPVwiaW9uLW1kLXNlYXJjaFwiPjwvaT48L2E+PC9kaXY+PC9kaXY+JyArXHJcbiAgICAgICAgICAgICc8ZGl2IGNsYXNzPVwibWFwIG10LTJcIj48L2Rpdj4nICtcclxuICAgICAgICAgICAgJzxkaXYgY2xhc3M9XCJtYXBpbmZvIG10LTIgdGV4dC1tdXRlZFwiPuacqumAieaLqeS9jee9rjwvZGl2PicsJ+ivt+mAieaLqeWcsOWbvuS9jee9ricpO1xyXG4gICAgfVxyXG59O1xyXG5cclxualF1ZXJ5KGZ1bmN0aW9uKCQpe1xyXG5cclxuICAgIC8v55uR5o6n5oyJ6ZSuXHJcbiAgICAkKGRvY3VtZW50KS5vbigna2V5ZG93bicsIGZ1bmN0aW9uKGUpe1xyXG4gICAgICAgIGlmKCFEaWFsb2cuaW5zdGFuY2UpcmV0dXJuO1xyXG4gICAgICAgIHZhciBkbGc9RGlhbG9nLmluc3RhbmNlO1xyXG4gICAgICAgIGlmIChlLmtleUNvZGUgPT0gMTMpIHtcclxuICAgICAgICAgICAgZGxnLmJveC5maW5kKCcubW9kYWwtZm9vdGVyIC5idG4nKS5lcShkbGcub3B0aW9ucy5kZWZhdWx0QnRuKS50cmlnZ2VyKCdjbGljaycpO1xyXG4gICAgICAgIH1cclxuICAgICAgICAvL+m7mOiupOW3suebkeWQrOWFs+mXrVxyXG4gICAgICAgIC8qaWYgKGUua2V5Q29kZSA9PSAyNykge1xyXG4gICAgICAgICBzZWxmLmhpZGUoKTtcclxuICAgICAgICAgfSovXHJcbiAgICB9KTtcclxufSk7IiwiXHJcbmpRdWVyeS5leHRlbmQoalF1ZXJ5LmZuLHtcclxuICAgIHRhZ3M6ZnVuY3Rpb24obm0pe1xyXG4gICAgICAgIHZhciBkYXRhPVtdO1xyXG4gICAgICAgIHZhciB0cGw9JzxzcGFuIGNsYXNzPVwiYmFkZ2UgYmFkZ2UtaW5mb1wiPntAbGFiZWx9PGlucHV0IHR5cGU9XCJoaWRkZW5cIiBuYW1lPVwiJytubSsnXCIgdmFsdWU9XCJ7QGxhYmVsfVwiLz48YnV0dG9uIHR5cGU9XCJidXR0b25cIiBjbGFzcz1cImNsb3NlXCIgZGF0YS1kaXNtaXNzPVwiYWxlcnRcIiBhcmlhLWxhYmVsPVwiQ2xvc2VcIj48c3BhbiBhcmlhLWhpZGRlbj1cInRydWVcIj4mdGltZXM7PC9zcGFuPjwvYnV0dG9uPjwvc3Bhbj4nO1xyXG4gICAgICAgIHZhciBpdGVtPSQodGhpcykucGFyZW50cygnLmZvcm0tY29udHJvbCcpO1xyXG4gICAgICAgIHZhciBsYWJlbGdyb3VwPSQoJzxzcGFuIGNsYXNzPVwiYmFkZ2UtZ3JvdXBcIj48L3NwYW4+Jyk7XHJcbiAgICAgICAgdmFyIGlucHV0PXRoaXM7XHJcbiAgICAgICAgdGhpcy5iZWZvcmUobGFiZWxncm91cCk7XHJcbiAgICAgICAgdGhpcy5vbigna2V5dXAnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgIHZhciB2YWw9JCh0aGlzKS52YWwoKS5yZXBsYWNlKC/vvIwvZywnLCcpO1xyXG4gICAgICAgICAgICBpZih2YWwgJiYgdmFsLmluZGV4T2YoJywnKT4tMSl7XHJcbiAgICAgICAgICAgICAgICB2YXIgdmFscz12YWwuc3BsaXQoJywnKTtcclxuICAgICAgICAgICAgICAgIGZvcih2YXIgaT0wO2k8dmFscy5sZW5ndGg7aSsrKXtcclxuICAgICAgICAgICAgICAgICAgICB2YWxzW2ldPXZhbHNbaV0ucmVwbGFjZSgvXlxcc3xcXHMkL2csJycpO1xyXG4gICAgICAgICAgICAgICAgICAgIGlmKHZhbHNbaV0gJiYgZGF0YS5pbmRleE9mKHZhbHNbaV0pPT09LTEpe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBkYXRhLnB1c2godmFsc1tpXSk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGxhYmVsZ3JvdXAuYXBwZW5kKHRwbC5jb21waWxlKHtsYWJlbDp2YWxzW2ldfSkpO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIGlucHV0LnZhbCgnJyk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KS5vbignYmx1cicsZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgJCh0aGlzKS52YWwoJCh0aGlzKS52YWwoKSsnLCcpLnRyaWdnZXIoJ2tleXVwJyk7XHJcbiAgICAgICAgfSkudHJpZ2dlcigna2V5dXAnKTtcclxuICAgICAgICBsYWJlbGdyb3VwLm9uKCdjbGljaycsJy5jbG9zZScsZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgdmFyIHRhZz0kKHRoaXMpLnBhcmVudHMoJy5iYWRnZScpLmZpbmQoJ2lucHV0JykudmFsKCk7XHJcbiAgICAgICAgICAgIHZhciBpZD1kYXRhLmluZGV4T2YodGFnKTtcclxuICAgICAgICAgICAgaWYoaWQpZGF0YS5zcGxpY2UoaWQsMSk7XHJcbiAgICAgICAgICAgICQodGhpcykucGFyZW50cygnLmJhZGdlJykucmVtb3ZlKCk7XHJcbiAgICAgICAgfSk7XHJcbiAgICAgICAgaXRlbS5jbGljayhmdW5jdGlvbigpe1xyXG4gICAgICAgICAgICBpbnB1dC5mb2N1cygpO1xyXG4gICAgICAgIH0pO1xyXG4gICAgfVxyXG59KTsiLCIvL+aXpeacn+e7hOS7tlxyXG5pZigkLmZuLmRhdGV0aW1lcGlja2VyKSB7XHJcbiAgICB2YXIgdG9vbHRpcHM9IHtcclxuICAgICAgICB0b2RheTogJ+WumuS9jeW9k+WJjeaXpeacnycsXHJcbiAgICAgICAgY2xlYXI6ICfmuIXpmaTlt7LpgInml6XmnJ8nLFxyXG4gICAgICAgIGNsb3NlOiAn5YWz6Zet6YCJ5oup5ZmoJyxcclxuICAgICAgICBzZWxlY3RNb250aDogJ+mAieaLqeaciOS7vScsXHJcbiAgICAgICAgcHJldk1vbnRoOiAn5LiK5Liq5pyIJyxcclxuICAgICAgICBuZXh0TW9udGg6ICfkuIvkuKrmnIgnLFxyXG4gICAgICAgIHNlbGVjdFllYXI6ICfpgInmi6nlubTku70nLFxyXG4gICAgICAgIHByZXZZZWFyOiAn5LiK5LiA5bm0JyxcclxuICAgICAgICBuZXh0WWVhcjogJ+S4i+S4gOW5tCcsXHJcbiAgICAgICAgc2VsZWN0RGVjYWRlOiAn6YCJ5oup5bm05Lu95Yy66Ze0JyxcclxuICAgICAgICBwcmV2RGVjYWRlOiAn5LiK5LiA5Yy66Ze0JyxcclxuICAgICAgICBuZXh0RGVjYWRlOiAn5LiL5LiA5Yy66Ze0JyxcclxuICAgICAgICBwcmV2Q2VudHVyeTogJ+S4iuS4quS4lue6qicsXHJcbiAgICAgICAgbmV4dENlbnR1cnk6ICfkuIvkuKrkuJbnuqonXHJcbiAgICB9O1xyXG4gICAgdmFyIGljb25zPXtcclxuICAgICAgICB0aW1lOiAnaW9uLW1kLXRpbWUnLFxyXG4gICAgICAgIGRhdGU6ICdpb24tbWQtY2FsZW5kYXInLFxyXG4gICAgICAgIHVwOiAnaW9uLW1kLWFycm93LWRyb3B1cCcsXHJcbiAgICAgICAgZG93bjogJ2lvbi1tZC1hcnJvdy1kcm9wZG93bicsXHJcbiAgICAgICAgcHJldmlvdXM6ICdpb24tbWQtYXJyb3ctZHJvcGxlZnQnLFxyXG4gICAgICAgIG5leHQ6ICdpb24tbWQtYXJyb3ctZHJvcHJpZ2h0JyxcclxuICAgICAgICB0b2RheTogJ2lvbi1tZC10b2RheScsXHJcbiAgICAgICAgY2xlYXI6ICdpb24tbWQtdHJhc2gnLFxyXG4gICAgICAgIGNsb3NlOiAnaW9uLW1kLWNsb3NlJ1xyXG4gICAgfTtcclxuICAgICQoJy5kYXRlcGlja2VyJykuZGF0ZXRpbWVwaWNrZXIoe1xyXG4gICAgICAgIGljb25zOmljb25zLFxyXG4gICAgICAgIHRvb2x0aXBzOnRvb2x0aXBzLFxyXG4gICAgICAgIGZvcm1hdDogJ1lZWVktTU0tREQnLFxyXG4gICAgICAgIGxvY2FsZTogJ3poLWNuJyxcclxuICAgICAgICBzaG93Q2xlYXI6dHJ1ZSxcclxuICAgICAgICBzaG93VG9kYXlCdXR0b246dHJ1ZSxcclxuICAgICAgICBzaG93Q2xvc2U6dHJ1ZSxcclxuICAgICAgICBrZWVwSW52YWxpZDp0cnVlXHJcbiAgICB9KTtcclxuXHJcbiAgICAkKCcuZGF0ZS1yYW5nZScpLmVhY2goZnVuY3Rpb24gKCkge1xyXG4gICAgICAgIHZhciBmcm9tID0gJCh0aGlzKS5maW5kKCdbbmFtZT1mcm9tZGF0ZV0sLmZyb21kYXRlJyksIHRvID0gJCh0aGlzKS5maW5kKCdbbmFtZT10b2RhdGVdLC50b2RhdGUnKTtcclxuICAgICAgICB2YXIgb3B0aW9ucyA9IHtcclxuICAgICAgICAgICAgaWNvbnM6aWNvbnMsXHJcbiAgICAgICAgICAgIHRvb2x0aXBzOnRvb2x0aXBzLFxyXG4gICAgICAgICAgICBmb3JtYXQ6ICdZWVlZLU1NLUREJyxcclxuICAgICAgICAgICAgbG9jYWxlOid6aC1jbicsXHJcbiAgICAgICAgICAgIHNob3dDbGVhcjp0cnVlLFxyXG4gICAgICAgICAgICBzaG93VG9kYXlCdXR0b246dHJ1ZSxcclxuICAgICAgICAgICAgc2hvd0Nsb3NlOnRydWUsXHJcbiAgICAgICAgICAgIGtlZXBJbnZhbGlkOnRydWVcclxuICAgICAgICB9O1xyXG4gICAgICAgIGZyb20uZGF0ZXRpbWVwaWNrZXIob3B0aW9ucykub24oJ2RwLmNoYW5nZScsIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgaWYgKGZyb20udmFsKCkpIHtcclxuICAgICAgICAgICAgICAgIHRvLmRhdGEoJ0RhdGVUaW1lUGlja2VyJykubWluRGF0ZShmcm9tLnZhbCgpKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pO1xyXG4gICAgICAgIHRvLmRhdGV0aW1lcGlja2VyKG9wdGlvbnMpLm9uKCdkcC5jaGFuZ2UnLCBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIGlmICh0by52YWwoKSkge1xyXG4gICAgICAgICAgICAgICAgZnJvbS5kYXRhKCdEYXRlVGltZVBpY2tlcicpLm1heERhdGUodG8udmFsKCkpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSk7XHJcbiAgICB9KTtcclxufSIsImpRdWVyeShmdW5jdGlvbigkKXtcclxuXHJcbn0pOyJdfQ==
