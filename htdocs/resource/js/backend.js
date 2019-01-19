window.stop_ajax=false;

function radio_tab(radios,lists,prefix) {
    $(radios).on('change',function (e) {
        if(!$(this).is(':checked'))return;
        var curval=$(this).val();
        $(lists).hide();
        $('.'+prefix+curval).show();
    }).filter(':checked').trigger('change');
}

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

    $(window).on('scroll',function (e) {

    }).trigger('scroll');

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
            return dialog.error('未知操作');
        }
        action = 'action' + action.replace(/^[a-z]/, function (letter) {
            return letter.toUpperCase();
        });
        if (!window[action] || typeof window[action] !== 'function') {
            return dialog.error('未知操作');
        }
        var needChecks = $(this).data('needChecks');
        if (needChecks === undefined) needChecks = true;
        if (needChecks) {
            var target = $(this).data('target');
            if (!target) target = 'id';
            var ids = $('[name=' + target + ']:checked');
            if (ids.length < 1) {
                return dialog.warning('请选择需要操作的项目');
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

    //状态切换按钮
    $('.chgstatus').click(function (e) {
        if($(this).data('ajaxing'))return;
        $(this).data('ajaxing',1);
        var self=$(this);
        var parent=self.parents('td');
        var id=parent.data('id');
        var status=self.data('status');
        $.ajax({
            url:parent.data('url'),
            type:'POST',
            dataType:'JSON',
            data:{
                id:id,
                status:status
            },
            success:function (json) {
                self.data('ajaxing',0);
                if(json.code==1){
                    dialog.success(json.msg);
                    self.toggleClass('off');
                    var totext=self.attr('title').replace('点击','');
                    self.text(totext);
                    setTimeout(function () {
                        location.reload();
                    },1000);
                }else{
                    dialog.error(json.msg);
                }
            }
        })
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
        if(text)text=text.replace(/(\\n|\n)+/g,"<br />");
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

    //点击放大浏览图片效果
    $('.img-view').click(function (e) {
        e.preventDefault();
        e.stopPropagation();
        var url=$(this).attr('href');
        if(!url)url=$(this).data('img');
        var dlg = new Dialog({
            btns: ['确定']
        }).show('<a href="'+url+'" class="d-block text-center" target="_blank"><img class="img-fluid" src="'+url+'" /></a><div class="text-muted text-center">点击图片在新页面放大查看</div>', '查看图片');
    });

    //tab切换效果
    $('.nav-tabs a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    //上传框
    $('.custom-file .custom-file-input').on('change', function () {
        var self=$(this);
        var inputgroup=$(this).parents('.input-group').eq(0);
        var parent=inputgroup.parents('div').eq(0);
        var label = $(this).parents('.custom-file').find('.custom-file-label');
        if(!label.data('origtext')){
            label.data('origtext',label.text());
        }
        label.text($(this).val());

        if(!window.URL && !window.URL.createObjectURL)return;
        var file=self[0].files[0];
        var is_img=file.type && file.type.match(/(\.|\/)(jpe?g|png|gif)$/);


        var figure = parent.find('.figure');
        if (!figure.length) {
            parent.append('<figure class="figure">\n' +
                '                            <img src="/static/images/blank.gif" class="figure-img img-fluid rounded" alt="image">\n' +
                '                            <figcaption class="figure-caption text-center"></figcaption>\n' +
                '                        </figure>');
            figure = parent.find('.figure');
        }
        if(is_img) {
            var img = figure.find('img');
            var origurl = img.data('origurl');
            if (!origurl) {
                origurl = img.attr('src');
                img.data('origurl', origurl);
            }
            var figcap = figure.find('figcaption');
            if (figcap.data('origtext') === undefined) {
                figcap.data('origtext', figcap.text());
            }
            img.attr('src', window.URL.createObjectURL(file));
            figcap.text(self.val());
        }

        var cancel = inputgroup.find('a.cancel');
        if (!cancel.length) {
            inputgroup.append('<div class="input-group-append"><a href="javascript:" class="btn btn-outline-danger cancel">取消</a></div>');
            cancel = inputgroup.find('a.cancel');
            cancel.click(function (e) {
                dialog.confirm('取消上传该文件?', function () {
                    self.val('');
                    label.text(label.data('origtext'));
                    if(is_img) {
                        img.attr('src', origurl);
                        figcap.text(figcap.data('origtext'));
                    }
                    cancel.parent().remove();
                })

            })
        }
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
                    dialog.warning(json.msg);
                    $(btn).removeAttr('disabled');
                }
            },
            error: function (xhr) {
                window.stop_ajax=false;
                isbtn?$(btn).text(origText):$(btn).val(origText);
                $(btn).removeAttr('disabled');
                dialog.error('服务器处理错误');
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

    //用户选择按钮绑定
    $('.pickuser').click(function (e) {
        var group = $(this).parents('.input-group');
        var idele = group.find('[name=member_id]');
        var infoele = group.find('[name=member_info]');
        dialog.pickUser( function (user) {
            idele.val(user.id);
            infoele.val('[' + user.id + '] ' + user.username + (user.mobile ? (' / ' + user.mobile) : ''));
        }, $(this).data('filter'));
    });

    //位置选择按钮绑定
    $('.pick-locate').click(function(e){
        var group=$(this).parents('.input-group');
        var idele=group.find('input[type=text]');
        dialog.pickLocate('qq',function(locate){
            idele.val(locate.lng+','+locate.lat);
        },idele.val());
    });
});