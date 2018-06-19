<extend name="public:base" />

<block name="body">

    <include file="public/bread" menu="wechat_index" title="公众号信息" />

    <div id="page-wrapper">
        <div class="page-header">{$id>0?'编辑':'添加'}公众号</div>
        <div class="page-content">
            <form method="post" action="">
                <div class="form-row">
                    <div class="col form-group">
                        <label for="title">名称</label>
                        <input type="text" name="title" class="form-control" value="{$model.title}" placeholder="输入公众号名称">
                    </div>
                    <div class="col form-group">
                        <label for="type">平台类型</label>
                        <select name="type" class="form-control">
                            <option value="wechat">微信公众号</option>
                        </select>
                    </div>
                    <div class="col form-group">
                        <label for="account_type">账号类型</label>
                        <select name="account_type" class="form-control">
                            <option value="subscribe">订阅号</option>
                            <option value="service">服务号</option>
                            <option value="miniprogram">小程序</option>
                            <option value="minigame">小游戏</option>
                            <option value="enterprise">企业号</option>
                        </select>
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
                        <if condition="$model['logo']">
                            <figure class="figure">
                                <img src="{$model.logo}" class="figure-img img-fluid rounded" alt="image">
                                <figcaption class="figure-caption text-center">{$model.logo}</figcaption>
                            </figure>
                            <input type="hidden" name="delete_logo" value="{$model.logo}"/>
                        </if>
                    </div>
                    <div class="col form-group">
                        <label for="original">二维码</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="upload_qrcode"/>
                                <label class="custom-file-label" for="upload_qrcode">选择文件</label>
                            </div>
                        </div>
                        <if condition="$model['qrcode']">
                            <figure class="figure">
                                <img src="{$model.qrcode}" class="figure-img img-fluid rounded" alt="image">
                                <figcaption class="figure-caption text-center">{$model.qrcode}</figcaption>
                            </figure>
                            <input type="hidden" name="delete_qrcode" value="{$model.qrcode}"/>
                        </if>
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
                        <div class="input-group-append"><a href="javascript:" class="btn btn-outline-secondary">随机生成</a> </div>
                        </div>
                    </div>
                    <div class="col form-group">
                        <label for="encodingaeskey">AESKey</label>
                        <div class="input-group">
                        <input type="text" name="encodingaeskey" class="form-control" value="{$model.encodingaeskey}">
                            <div class="input-group-append"><a href="javascript:" class="btn btn-outline-secondary">随机生成</a> </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="subscribeurl">订阅链接</label>
                    <input type="text" name="subscribeurl" class="form-control" value="{$model.subscribeurl}">
                </div>
                <div class="form-group">
                    <input type="hidden" name="id" value="{$model.id}">
                    <button type="submit" class="btn btn-primary">{$id>0?'保存':'添加'}</button>
                </div>
            </form>
        </div>
    </div>
</block>