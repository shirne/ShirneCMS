function del(msg) { 
//    var msg = "您真的确定要删除吗？\n\n删除后将不能恢复!请确认！"; 
    return confirm(msg);
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
        if(typeof args=='string')args=args.split(',');
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
    if(v=='0')v=0;
    return v?m1:m2;
}

var dialogTpl='<div class="modal fade" id="{@id}" tabindex="-1" role="dialog" aria-labelledby="{@id}Label" aria-hidden="true">\
    <div class="modal-dialog">\
    <div class="modal-content">\
    <div class="modal-header">\
    <h4 class="modal-title" id="{@id}Label"></h4>\
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>\
    </div>\
    <div class="modal-body">\
    </div>\
    <div class="modal-footer">\
    </div>\
    </div>\
    </div>\
    </div>';
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

        if(!opts.btns[dft]['class']){
            opts.btns[dft]['class']='btn-primary';
        }
        opts.defaultBtn=dft;
    }

    this.options=$.extend({
        'id':'dlgModal'+dialogIdx++,
        'size':'',
        'btns':[
            {'text':'取消','class':'btn-secondary'},
            {'text':'确定','isdefault':true,'class':'btn-primary'}
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
    return '<a class="btn '+(opt['class']?opt['class']:'btn-default')+'" data-index="'+idx+'">'+opt.text+'</a>';
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
    this.box.find('.modal-footer').html(btns.join('\n'));

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
        if(self.options.btns[idx]['click']){
            result = self.options.btns[idx]['click'].apply(this,[body, self.box]);
        }
        if(idx==self.options.defaultBtn) {
            if (self.options.onsure) {
                result = self.options.onsure.apply(this,[body, self.box]);
            }
        }
        if(result===true){
            self.box.modal('hide');
        }
    });
    this.box.modal('show');
    return this;
};
Dialog.prototype.hide=function(){
    this.box.modal('hide');
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

jQuery(function ($) {
    //高亮当前选中的导航
    var menu = $(".breadcrumb").data('menu');
    if(menu) {
        var link = $('.side-nav a[data-key=' + menu + ']');

        if (link.length > 0) {
            link.parents('.collapse').addClass('show');
            link.addClass("active");
        }

    }

    $('a[rel=ajax]').click(function(e){
       e.preventDefault();
        var self=$(this);
        var title=$(this).data('title');
        if(!title)title=$(this).text();
        var dlg=new Dialog({
            btns:['确定'],
            onshow:function(body){
                $.ajax({
                    url:self.attr('href'),
                    success:function(text){
                        body.html(text);
                    }
                });
            }
        }).show('<p class="loading">加载中...</p>',title);

    });

    $('.nav-tabs a').click(function (e) {
        e.preventDefault();
        $(this).tab('show')
    });

    $('.custom-file .custom-file-input').on('change',function(){
        var label=$(this).parents('.custom-file').find('.custom-file-label');
        label.text($(this).val());
    });

    $('.btn-primary[type=submit]').click(function(e){
        var form=$(this).parents('form');
        if(form.attr('enctype')=='multipart/form-data'){
            return true;
        }
        e.preventDefault();
        $(this).attr('disabled',true);
        var btn=this;
        $.ajax({
            url:$(form).attr('action'),
            type:'POST',
            dataType:'JSON',
            data:$(form).serialize(),
            success:function (json) {
                if(json.code==1){
                    new Dialog({
                        onhidden:function(){
                            if(json.url){
                                location.href=json.url;
                            }else{
                                location.reload();
                            }
                        }
                    }).show(json.msg);
                }else{
                    new Dialog({}).show(json.msg);
                    $(btn).removeAttr('disabled');
                }
            }
        })

    });

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
            time: 'ion-clock',
            date: 'ion-calendar',
            up: 'ion-arrow-up-c',
            down: 'ion-arrow-down-c',
            previous: 'ion-arrow-left-c',
            next: 'ion-arrow-right-c',
            today: 'ion-pinpoint',
            clear: 'ion-trash-a',
            close: 'ion-close'
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
});


