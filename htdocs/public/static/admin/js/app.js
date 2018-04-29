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
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>\
    <h4 class="modal-title" id="{@id}Label"></h4>\
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
            {'text':'取消','class':'btn-default'},
            {'text':'确定','isdefault':true,'class':'btn-primary'}
        ],
        'defaultBtn':1,
        'onsure':null,
        'onshow':null,
        'onshown':null,
        'onhide':null
    },opts);

    this.box=$(this.options.id);
}
Dialog.prototype.generBtn=function(opt,idx){
    return '<a class="btn '+(opt['class']?opt['class']:'btn-default')+'" data-index="'+idx+'">'+opt.text+'</a>';
};
Dialog.prototype.show=function(html,title){
    this.box=$('#'+this.options.id);
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
            self.options.onhide(body);
        }
        Dialog.instance=null;
    });
    this.box.on('hidden.bs.modal',function(){
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
            link.parents('.panel-collapse').addClass('in');
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

    $('.datepicker').datetimepicker({
        language:'zh-CN',
        minView:'month',
        autoclose:true
    });

    $('.date-range').each(function(){
        var from=$(this).find('[name=fromdate],.fromdate'),to=$(this).find('[name=todate],.todate');
        var options={
            format: 'yyyy-mm-dd',
            minView:'month',
            autoclose:true,
            language:'zh-CN'
        };
        from.datetimepicker(options).on('changeDate',function(){
            if(from.val()){
                to.datetimepicker('setStartDate',from.val());
            }
        });
        to.datetimepicker(options).on('changeDate',function(){
            if(to.val()){
                from.datetimepicker('setEndDate',to.val());
            }
        });
    });
});


