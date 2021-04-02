{extend name="public:base" /}

{block name="body"}

    {include  file="public/bread" menu="wechat_index" title="公众号信息"  /}

    <div id="page-wrapper">
        <div class="page-header">{$id>0?'编辑':'添加'}公众号</div>
        <div class="page-content">
            <form method="post" action="" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="col form-group">
                        <label for="title">名称</label>
                        <div class="input-group">
                            <input type="text" name="title" class="form-control" value="{$model.title}" placeholder="输入公众号名称">
                            <div class="input-group-append">
                                <label class="input-group-text">
                                    <input type="checkbox" name="is_default" value="1" {$model['is_default']?'checked':''}>
                                    默认
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-2 form-group">
                        <label for="title">调试模式</label>
                        <div>
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-outline-primary{$model['is_debug']=='1'?' active':''}">
                                <input type="radio" name="is_debug" value="1" autocomplete="off" {$model['is_debug']=='1'?'checked':''}>是
                            </label>
                            <label class="btn btn-outline-secondary{$model['is_debug']!='1'?' active':''}">
                                <input type="radio" name="is_debug" value="0" autocomplete="off" {$model['is_debug']!='1'?'checked':''}>否
                            </label>
                        </div>
                        </div>
                    </div>
                    <div class="col form-group">
                        <label for="type">平台类型</label>
                        <select name="type" class="form-control">
                            <option value="wechat" {$model['type']=='wechat'?'selected':''}>微信公众号</option>
                        </select>
                    </div>
                    <div class="col form-group">
                        <label for="account_type">账号类型</label>
                        <select name="account_type" class="form-control">
                            <option value="subscribe" {$model['account_type']=='subscribe'?'selected':''}>订阅号</option>
                            <option value="service" {$model['account_type']=='service'?'selected':''}>服务号</option>
                            <option value="miniprogram" {$model['account_type']=='miniprogram'?'selected':''}>小程序</option>
                            <option value="minigame" {$model['account_type']=='minigame'?'selected':''}>小游戏</option>
                            <option value="enterprise" {$model['account_type']=='enterprise'?'selected':''}>企业号</option>
                        </select>
                    </div>
                </div>
                <div class="text-muted">只有服务号才能设置默认为默认，开启调试模式将允许开发工具使用mock数据登录</div>
                <div class="form-group">
                    <label for="token">接口地址</label>
                    <div class="input-group">
                        <input type="text" readonly class="form-control" value="{:url('api/wechat/index',['hash'=>$model['hash']],false,true)}">
                        <div class="input-group-append"><a href="javascript:" class="btn btn-outline-secondary gener-url">修改URL</a> </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col form-group">
                        <label for="account">账号</label>
                        <input type="text" name="account" class="form-control" value="{$model.account}">
                    </div>
                    <div class="col form-group">
                        <label for="original">原始账号</label>
                        <input type="text" name="original" class="form-control" value="{$model.original}" >
                    </div>
                </div>
                <div class="form-row">
                    <div class="col form-group">
                        <label for="account">LOGO</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="upload_logo"/>
                                <label class="custom-file-label" for="upload_logo">选择文件</label>
                            </div>
                        </div>
                        {if $model['logo']}
                            <figure class="figure">
                                <img src="{$model.logo}" class="figure-img img-fluid rounded" alt="image">
                                <figcaption class="figure-caption text-center">{$model.logo}</figcaption>
                            </figure>
                            <input type="hidden" name="delete_logo" value="{$model.logo}"/>
                        {/if}
                    </div>
                    <div class="col form-group">
                        <label for="original">二维码</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" accept="image/*" class="custom-file-input" name="upload_qrcode"/>
                                <label class="custom-file-label" for="upload_qrcode">选择文件</label>
                            </div>
                        </div>
                        {if $model['qrcode']}
                            <figure class="figure">
                                <img src="{$model.qrcode}" accept="image/*" class="figure-img img-fluid rounded" alt="image">
                                <figcaption class="figure-caption text-center">{$model.qrcode}</figcaption>
                            </figure>
                            <input type="hidden" name="delete_qrcode" value="{$model.qrcode}"/>
                        {/if}
                    </div>
                    <div class="col form-group">
                        <label for="original">分享图</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" accept="image/*" class="custom-file-input" name="upload_shareimg"/>
                                <label class="custom-file-label" for="upload_shareimg">选择文件</label>
                            </div>
                        </div>
                        {if $model['shareimg']}
                            <figure class="figure">
                                <img src="{$model.shareimg}" class="figure-img img-fluid rounded" alt="image">
                                <figcaption class="figure-caption text-center">{$model.shareimg}</figcaption>
                            </figure>
                            <input type="hidden" name="delete_shareimg" value="{$model.shareimg}"/>
                        {/if}
                    </div>
                </div>
                <div class="form-row">
                    <div class="col form-group">
                        <label for="account">海报链接</label>
                        <input type="text" name="share_poster_url" class="form-control" value="{$model.share_poster_url}">
                    </div>
                    <div class="col text-muted">
                        代理码使用 [code] 代替
                    </div>
                </div>
                <div class="form-row">
                    <div class="col form-group">
                        <label for="appid">APPID</label>
                        <input type="text" name="appid" class="form-control" value="{$model.appid}">
                    </div>
                    <div class="col form-group">
                        <label for="appsecret">APPSecret</label>
                        <input type="text" name="appsecret" class="form-control" value="{$model.appsecret}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col form-group">
                        <label for="token">Token</label>
                        <div class="input-group">
                        <input type="text" name="token" class="form-control" value="{$model.token}">
                        <div class="input-group-append"><a href="javascript:" class="btn btn-outline-secondary gener-token">随机生成</a> </div>
                        </div>
                    </div>
                    <div class="col form-group">
                        <label for="encodingaeskey">AESKey</label>
                        <div class="input-group">
                        <input type="text" name="encodingaeskey" class="form-control" value="{$model.encodingaeskey}">
                            <div class="input-group-append"><a href="javascript:" class="btn btn-outline-secondary gener-aeskey">随机生成</a> </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="subscribeurl">订阅链接</label>
                    <input type="text" name="subscribeurl" class="form-control" value="{$model.subscribeurl}">
                </div>
                <h3>支付参数</h3>
                <hr/>
                <div class="form-row">
                    <div class="col form-group">
                        <label for="token">商户ID</label>
                        <div class="input-group">
                            <input type="text" name="mch_id" class="form-control" value="{$model.mch_id}">
                        </div>
                    </div>
                    <div class="col form-group">
                        <label for="encodingaeskey">支付密钥</label>
                        <div class="input-group">
                            <input type="text" name="key" class="form-control" value="{$model.key}">
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col form-group">
                        <label for="account">证书文件(cert.pem)</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="upload_cert_path"/>
                                <label class="custom-file-label" for="upload_cert_path">选择文件</label>
                            </div>
                        </div>
                        {if $model['cert_path']}
                            <figure class="figure">
                                <figcaption class="figure-caption text-center">{$model.cert_path}</figcaption>
                            </figure>
                            <input type="hidden" name="delete_cert_path" value="{$model.cert_path}"/>
                        {/if}
                    </div>
                    <div class="col form-group">
                        <label for="original">密钥文件(key.pem)</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="upload_key_path"/>
                                <label class="custom-file-label" for="upload_key_path">选择文件</label>
                            </div>
                        </div>
                        {if $model['key_path']}
                            <figure class="figure">
                                <figcaption class="figure-caption text-center">{$model.key_path}</figcaption>
                            </figure>
                            <input type="hidden" name="delete_key_path" value="{$model.key_path}"/>
                        {/if}
                    </div>
                </div>
                <div class="form-group">
                    <input type="hidden" name="id" value="{$model.id}">
                    <button type="submit" class="btn btn-primary">{$model['id']>0?'保存':'添加'}</button>
                </div>
            </form>
        </div>
    </div>
{/block}
{block name="script"}
    <script type="text/javascript">
        jQuery(function ($) {
            $('.gener-token').click(function() {
                var newtoken=randomString(Math.floor(Math.random()*16+16));
                var input=$(this).parents('.input-group').find('input');
                if(newtoken!==input.val()){
                    input.val(newtoken);
                }else{
                    $(this).trigger('click');
                }
            });
            $('.gener-aeskey').click(function() {
                var newtoken=randomString(43);
                var input=$(this).parents('.input-group').find('input');
                if(newtoken!==input.val()){
                    input.val(newtoken);
                }else{
                    $(this).trigger('click');
                }
            });
            var hash='{$model.hash}';
            $('.gener-url').click(function () {
                if($(this).data('ajaxing'))return;
                var btn=$(this);
                dialog.confirm('是否确认重新生成url？<br />重新生成后原有绑定的设置将会无效，需要重新绑定',function () {
                    btn.data('ajaxing',1);
                    var newtoken = randomString(Math.floor(Math.random() * 4 + 6));
                    var input = btn.parents('.input-group').find('input');
                    if (newtoken !== hash) {
                        btn.data('ajaxing',1);
                        $.ajax({
                            url: "{:url('admin/wechat/updateField')}",
                            type: 'POST',
                            dataType: 'JSON',
                            data: {
                                'id': '{$model.id}',
                                'field': 'hash',
                                'value': newtoken
                            },
                            success: function (json) {
                                btn.data('ajaxing',0);
                                hash=newtoken;
                                input.val("{:url('api/wechat/index',['hash'=>'__HASH__'],false,true)}".replace('__HASH__',newtoken));
                            }
                        })
                    } else {
                        $(this).trigger('click');
                    }
                });

            });
        })
    </script>
{/block}