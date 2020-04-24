<div class="form-row form-group">
    <label for="v-wechat_autologin" class="text-right align-middle">微信自动登录</label>
    <div class="pl-2 pr-2">
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
            {foreach name="setting.wechat_autologin.data" item="value" key="k"}
                {if $k==$setting['wechat_autologin']['value']}
                    <label class="btn btn-outline-secondary active">
                        <input type="radio" name="v-wechat_autologin" value="{$k}" autocomplete="off" checked> {$value}
                    </label>
                    {else /}
                    <label class="btn btn-outline-secondary">
                        <input type="radio" name="v-wechat_autologin" value="{$k}" autocomplete="off"> {$value}
                    </label>
                {/if}
            {/foreach}
        </div>
    </div>
    <div class="col">
        <div class="text-muted">开启自动登录时用户从微信环境进入则自动跳转授权并使用授权信息登录或自动注册</div>
    </div>
</div>
<div class="form-row mb-3">
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">验证码<span class="float-right"><a href="https://www.geetest.com/" target="_blank">极验</a></span></div>
            <div class="card-body">
                <div class="form-row form-group">
                    <label for="v-captcha_mode" class="text-right align-middle">验证码模式</label>
                    <div class="col">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            {foreach name="setting.captcha_mode.data" item="value" key="k"}
                                {if $k==$setting['captcha_mode']['value']}
                                    <label class="btn btn-outline-secondary active">
                                        <input type="radio" name="v-captcha_mode" value="{$k}" autocomplete="off" checked> {$value}
                                    </label>
                                    {else /}
                                    <label class="btn btn-outline-secondary">
                                        <input type="radio" name="v-captcha_mode" value="{$k}" autocomplete="off"> {$value}
                                    </label>
                                {/if}
                            {/foreach}
                        </div>
                    </div>
                </div>
                <div class="form-row form-group">
                    <label for="v-captcha_geeid" class="col-3 col-md-2 text-right align-middle">极验ID</label>
                    <div class="col">
                        <input type="text" class="form-control" name="v-captcha_geeid" value="{$setting['captcha_geeid']['value']}" placeholder="">
                    </div>
                </div>
                <div class="form-row form-group">
                    <label for="v-captcha_geekey" class="col-3 col-md-2 text-right align-middle">极验密钥</label>
                    <div class="col">
                        <input type="text" class="form-control" name="v-captcha_geekey" value="{$setting['captcha_geekey']['value']}" placeholder="">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">快递鸟 <span class="float-right"><a href="http://www.kdniao.com/" target="_blank">快递鸟</a></span> </div>
            <div class="card-body">
                <div class="form-row form-group">
                    <label for="v-kd_userid" class="col-3 col-md-2 text-right align-middle">用户ID</label>
                    <div class="col">
                        <input type="text" class="form-control" name="v-kd_userid" value="{$setting['kd_userid']['value']}" placeholder="">
                    </div>
                </div>
                <div class="form-row form-group">
                    <label for="v-kd_apikey" class="col-3 col-md-2 text-right align-middle">API Key</label>
                    <div class="col">
                        <input type="text" class="form-control" name="v-kd_apikey" value="{$setting['kd_apikey']['value']}" placeholder="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-row mb-3">
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">短信</div>
            <div class="card-body">
                <div class="form-row form-group">
                    <label for="v-sms_code" class="col-3 col-md-2 text-right align-middle">短信验证</label>
                    <div class="col">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            {foreach name="setting.sms_code.data" item="value" key="k"}
                                {if $k==$setting['sms_code']['value']}
                                    <label class="btn btn-outline-secondary active">
                                        <input type="radio" name="v-sms_code" value="{$k}" autocomplete="off" checked> {$value}
                                    </label>
                                    {else /}
                                    <label class="btn btn-outline-secondary">
                                        <input type="radio" name="v-sms_code" value="{$k}" autocomplete="off"> {$value}
                                    </label>
                                {/if}
                            {/foreach}
                        </div>
                    </div>
                </div>
                <div class="form-row form-group">
                    <label for="v-sms_spcode" class="col-3 col-md-2 text-right align-middle">企业编号</label>
                    <div class="col">
                        <input type="text" class="form-control" name="v-sms_spcode" value="{$setting['sms_spcode']['value']}" placeholder="">
                    </div>
                </div>
                <div class="form-row form-group">
                    <label for="v-sms_loginname" class="col-3 col-md-2 text-right align-middle">登录名称</label>
                    <div class="col">
                        <input type="text" class="form-control" name="v-sms_loginname" value="{$setting['sms_loginname']['value']}" placeholder="">
                    </div>
                </div>
                <div class="form-row form-group">
                    <label for="v-sms_password" class="col-3 col-md-2 text-right align-middle">登录密码</label>
                    <div class="col">
                        <input type="text" class="form-control" name="v-sms_password" value="{$setting['sms_password']['value']}" placeholder="">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">地图</div>
            <div class="card-body">
                <div class="form-row form-group">
                    <label for="v-mapkey_baidu" class="col-3 col-md-2 text-right align-middle">百度地图</label>
                    <div class="col">
                        <input type="text" class="form-control" name="v-mapkey_baidu" value="{$setting['mapkey_baidu']['value']}" placeholder="">
                    </div>
                </div>
                <div class="form-row form-group">
                    <label for="v-mapkey_google" class="col-3 col-md-2 text-right align-middle">谷哥地图</label>
                    <div class="col">
                        <input type="text" class="form-control" name="v-mapkey_google" value="{$setting['mapkey_google']['value']}"
                               placeholder="">
                    </div>
                </div>
                <div class="form-row form-group">
                    <label for="v-mapkey_tencent" class="col-3 col-md-2 text-right align-middle">腾讯地图</label>
                    <div class="col">
                        <input type="text" class="form-control" name="v-mapkey_tencent" value="{$setting['mapkey_tencent']['value']}"
                               placeholder="">
                    </div>
                </div>
                <div class="form-row form-group">
                    <label for="v-mapkey_gaode" class="col-3 col-md-2 text-right align-middle">高德地图</label>
                    <div class="col">
                        <input type="text" class="form-control" name="v-mapkey_gaode" value="{$setting['mapkey_gaode']['value']}"
                               placeholder="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-row mb-3">
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">邮箱</div>
            <div class="card-body">
                <div class="form-row form-group">
                    <label for="v-mail_host" class="col-3 col-md-2 text-right align-middle">邮箱主机</label>
                    <div class="col">
                        <input type="text" class="form-control" name="v-mail_host" value="{$setting['mail_host']['value']|default=''}" placeholder="">
                    </div>
                </div>
                <div class="form-row form-group">
                    <label for="v-mail_port" class="col-3 col-md-2 text-right align-middle">邮箱端口</label>
                    <div class="col">
                        <input type="text" class="form-control" name="v-mail_port" value="{$setting['mail_port']['value']|default=''}" placeholder="">
                    </div>
                </div>
                <div class="form-row form-group">
                    <label for="v-mail_user" class="col-3 col-md-2 text-right align-middle">邮箱账户</label>
                    <div class="col">
                        <input type="text" class="form-control" name="v-mail_user" value="{$setting['mail_user']['value']|default=''}" placeholder="">
                    </div>
                </div>
                <div class="form-row form-group">
                    <label for="v-mail_pass" class="col-3 col-md-2 text-right align-middle">邮箱密码</label>
                    <div class="col">
                        <input type="text" class="form-control" name="v-mail_pass" value="{$setting['mail_pass']['value']|default=''}" placeholder="">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">阿里云</div>
            <div class="card-body">
                <div class="form-row form-group">
                    <label for="v-accesskey_id" class="col-3 col-md-2 text-right align-middle">账号ID</label>
                    <div class="col">
                        <input type="text" class="form-control" name="v-accesskey_id" value="{$setting['accesskey_id']['value']}" placeholder="">
                    </div>
                </div>
                <div class="form-row form-group">
                    <label for="v-accesskey_secret" class="col-3 col-md-2 text-right align-middle">账号密钥</label>
                    <div class="col">
                        <input type="text" class="form-control" name="v-accesskey_secret" value="{$setting['accesskey_secret']['value']}" placeholder="">
                    </div>
                </div>
                <div class="form-row form-group">
                    <label for="v-aliyun_oss" class="col-3 col-md-2 text-right align-middle">OSS Buket</label>
                    <div class="col">
                        <input type="text" class="form-control" name="v-aliyun_oss" value="{$setting['aliyun_oss']['value']}" placeholder="">
                    </div>
                </div>
                <div class="form-row form-group">
                    <label for="v-aliyun_oss_domain" class="col-3 col-md-2 text-right align-middle">OSS域名</label>
                    <div class="col">
                        <input type="text" class="form-control" name="v-aliyun_oss_domain" value="{$setting['aliyun_oss_domain']['value']}" placeholder="">
                    </div>
                    <div class="col-2">
                        <input type="hidden" id="sslhidden" name="v-aliyun_oss_ssl" value="{$setting['aliyun_oss_domain']['value']}" />
                        <label><input type="checkbox" {$setting['aliyun_oss_ssl']['value']==1?'checked':''} class="sslcheck" value="1"/> 是否SSL</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
jQuery(function($){
    $('.sslcheck').change(function(e){
        if($(this).prop('checked')){
            $('#sslhidden').val(1)
        }else{
            $('#sslhidden').val(0)
        }
    })
})
</script>

