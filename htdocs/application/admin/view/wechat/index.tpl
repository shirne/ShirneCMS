<extend name="public:base" />

<block name="body">

    <include file="public/bread" menu="wechat_index" title="公众号列表" />

    <div id="page-wrapper">

        <div class="row list-header">
            <div class="col-6">
                <a href="{:url('wechat/add')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 添加公众号</a>
            </div>
            <div class="col-6">
                <form action="{:url('wechat/index')}" method="post">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" name="key" placeholder="输入名称或者关键词搜索">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit"><i class="ion-md-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <th width="50">编号</th>
                <th>类型</th>
                <th>名称</th>
                <th>appid</th>
                <th>功能</th>
                <th width="160">&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <php>$empty=list_empty(6);</php>
            <volist name="lists" id="v" empty="$empty">
                <tr>
                    <td>{$v.id}</td>
                    <td>{$v.type}{$v.account_type}</td>
                    <td>{$v.title}<if condition="$v['is_default']"><span class="badge badge-info">默认</span></if></td>
                    <td>{$v.appid}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a class="btn btn-outline-dark qrcode-btn" href="javascript:" data-qrcode="{$v.qrcode}"><i class="ion-md-expand"></i> 二维码</a>
                            <a class="btn btn-outline-dark" href="{:url('wechat.material/index',array('wid'=>$v['id']))}" ><i class="ion-md-appstore"></i> 素材</a>
                            <a class="btn btn-outline-dark" href="{:url('wechat.fans/index',array('wid'=>$v['id']))}" ><i class="ion-md-contacts"></i> 粉丝</a>
                            <a class="btn btn-outline-dark" href="{:url('wechat.reply/index',array('wid'=>$v['id']))}" ><i class="ion-md-chatboxes"></i> 回复</a>
                            <a class="btn btn-outline-dark" href="{:url('wechat.menu/edit',array('wid'=>$v['id']))}"><i class="ion-md-reorder"></i> 菜单</a>
                        </div>
                    </td>
                    <td class="operations">
                        <a class="btn btn-outline-primary btn-config" title="配置" href="javascript:" data-payurl="{:url('index/order/wechatpay',[],false,true)}/order_id/" data-url="{:url('api/wechat/index',['hash'=>$v['hash']],false,true)}" data-hash="{$v['hash']}" data-token="{$v['token']}" data-aeskey="{$v['encodingaeskey']}" data-id="{$v.id}"><i class="ion-md-cog"></i> </a>
                        <a class="btn btn-outline-primary" title="编辑" href="{:url('wechat/edit',array('id'=>$v['id']))}"><i class="ion-md-create"></i> </a>
                        <a class="btn btn-outline-danger link-confirm" title="删除" data-confirm="您真的确定要删除吗？\n删除后将不能恢复!" href="{:url('wechat/delete',array('id'=>$v['id']))}" ><i class="ion-md-trash"></i> </a>
                    </td>
                </tr>
            </volist>
            </tbody>
        </table>
        {$page|raw}
    </div>
</block>
<block name="script">
    <script type="text/plain" id="configTpl">
        <div class="form-group">
            <label for="token">接口地址</label>
            <div class="input-group">
                <input type="text" name="token" readonly class="form-control" data-hash="{@hash}" value="{@url}">
                <div class="input-group-append"><a href="javascript:" class="btn btn-outline-secondary gener-url">修改URL</a> </div>
            </div>
        </div>
        <div class="form-group">
            <label for="token">Token</label>
            <div class="input-group">
            <input type="text" name="token" readonly class="form-control" value="{@token}">
            <div class="input-group-append"><a href="javascript:" class="btn btn-outline-secondary gener-token">随机生成</a> </div>
            </div>
        </div>
        <div class="form-group">
            <label for="encodingaeskey">AESKey</label>
            <div class="input-group">
            <input type="text" name="encodingaeskey" readonly class="form-control" value="{@aeskey}">
                <div class="input-group-append"><a href="javascript:" class="btn btn-outline-secondary gener-aeskey">随机生成</a> </div>
            </div>
        </div>
        <div class="form-group">
            <label for="token">支付目录</label>
            <div class="input-group">
                <input type="text" name="token" readonly class="form-control" value="{@payurl}">
            </div>
        </div>
        <div class="form-group">
            <label for="encodingaeskey">上传域名认证文件</label>
            <div class="input-group">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" name="upload_verify"/>
                    <label class="custom-file-label" for="upload_verify">选择文件</label>
                </div>
                <div class="input-group-append"><a href="javascript:" class="btn btn-outline-secondary btn-upload">上传文件</a> </div>
            </div>
        </div>
    </script>
    <script type="text/javascript">
        jQuery(function($){
            $('.qrcode-btn').click(function() {
                var qrcode=$(this).data('qrcode');
                if(qrcode){
                    dialog.alert({
                        content:'<div class="text-center"><figure class="figure">\n' +
                        ' <img src="'+qrcode+'" class="figure-img img-fluid rounded" alt="image">\n' +
                        ' <figcaption class="figure-caption text-center">扫描二维码关注公众号</figcaption>\n' +
                        '</figure></div>',
                        title:'二维码'
                    },null);
                }else{
                    dialog.info('没有上传二维码');
                }
            });
            var curid=0;
            function bindEvents(body) {

                body.find('.gener-url').click(function () {
                    if($(this).data('ajaxing'))return;
                    var btn = $(this);
                    dialog.confirm('是否确认重新生成url？<br />重新生成后原有绑定的设置将会无效，需要重新绑定',function () {
                        var newtoken = randomString(Math.floor(Math.random() * 4 + 6));
                        var input = btn.parents('.input-group').find('input');
                        if (newtoken !== input.data('hash')) {
                            $(this).data('ajaxing', 1);
                            $.ajax({
                                url: "{:url('admin/wechat/updateField')}",
                                type: 'POST',
                                dataType: 'JSON',
                                data: {
                                    'id': curid,
                                    'field': 'hash',
                                    'value': newtoken
                                },
                                success: function (json) {
                                    btn.data('ajaxing', 0);
                                    input.val("{:url('api/wechat/index',['hash'=>'__HASH__'],false,true)}".replace('__HASH__', newtoken));
                                }
                            })
                        } else {
                            $(this).trigger('click');
                        }
                    });
                });
                body.find('.gener-token').click(function () {
                    if($(this).data('ajaxing'))return;
                    var newtoken = randomString(Math.floor(Math.random() * 16 + 16));
                    var input = $(this).parents('.input-group').find('input');
                    if (newtoken !== input.val()) {
                        $(this).data('ajaxing',1);
                        var btn=$(this);
                        $.ajax({
                            url: "{:url('admin/wechat/updateField')}",
                            type: 'POST',
                            dataType: 'JSON',
                            data: {
                                'id': curid,
                                'field': 'token',
                                'value': newtoken
                            },
                            success: function (json) {
                                btn.data('ajaxing',0);
                                input.val(newtoken);
                            }
                        })
                    } else {
                        $(this).trigger('click');
                    }
                });
                body.find('.gener-aeskey').click(function () {
                    if($(this).data('ajaxing'))return;
                    var newtoken = randomString(43);
                    var input = $(this).parents('.input-group').find('input');
                    if (newtoken !== input.val()) {
                        $(this).data('ajaxing',1);
                        var btn=$(this);
                        $.ajax({
                            url: "{:url('admin/wechat/updateField')}",
                            type: 'POST',
                            dataType: 'JSON',
                            data: {
                                'id': curid,
                                'field': 'encodingaeskey',
                                'value': newtoken
                            },
                            success: function (json) {
                                btn.data('ajaxing',0);
                                input.val(newtoken);
                            }
                        })
                    } else {
                        $(this).trigger('click');
                    }
                });

                body.find('.btn-upload').click(function (e) {
                    if($(this).data('ajaxing'))return;
                    if(!FileReader){
                        dialog.alert('您的浏览器接口较旧，请手动上传');
                        return;
                    }
                    var fileField=$(this).parents('.input-group').find('input');
                    var filename=fileField.val().split(/[\/\\]/g);
                    filename=filename[filename.length-1];
                    if(!filename){
                        dialog.alert('请选择验证文件 (MP_Verify_xxx.txt)');
                        return;
                    }
                    if(!filename.match(/^MP_verify_[a-zA-Z0-9]+\.txt$/)){
                        dialog.alert('验证文件格式错误');
                        return;
                    }
                    $(this).data('ajaxing',1);
                    var btn=$(this);
                    var reader = new FileReader();
                    reader.readAsText(fileField[0].files[0], "UTF-8");
                    reader.onload = function(evt){
                        var fileString = evt.target.result;
                        $.ajax({
                            url: "{:url('admin/wechat/uploadVerify')}",
                            type: 'POST',
                            dataType: 'JSON',
                            data: {
                                'name': filename,
                                'content': fileString
                            },
                            success: function (json) {
                                btn.data('ajaxing',0);
                                dialog.alert(json.msg);
                                fileField.val('');
                            }
                        })
                    }
                })
            }
            $('.btn-config').click(function (e) {
                var data=$(this).data();
                curid=data.id;
                var dlg=new Dialog({
                    btns:[{
                        'text':'关闭',
                        'type':'secondary'
                    }],
                    onshow:function (body) {
                        bindEvents(body);
                    }
                }).show($('#configTpl').text().compile(data),'微信配置参数');
            })
        })
    </script>
</block>