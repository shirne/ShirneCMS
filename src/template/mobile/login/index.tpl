{extend name="public:base"/}

{block name="body"}
<div class="page__hd">
    <h1 class="page__title">Login</h1>
    <p class="page__desc">会员登录</p>
</div>
<form method="post">
    <div class="weui-cells weui-cells_form">
        {if !empty($wechatUser)}
        <div class="weui-cell">
            <div class="weui-cell__hd" style="position: relative;margin-right: 10px;">
                <img src="{$wechatUser['avatar']}" style="width: 50px;display: block;border-radius: 1000px;">
            </div>
            <div class="weui-cell__bd">
                <p>{$wechatUser['nickname']}</p>
                <p style="font-size: 13px;color: #888888;">登录后将与微信账号绑定</p>
            </div>
        </div>
        {/if}
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">用户名</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="text" name="username" placeholder="请输入用户名">
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd">
                <label class="weui-label">密&emsp;码</label>
            </div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="password" name="password" placeholder="请输入密码">
            </div>
        </div>
        <div class="weui-cell weui-cell_vcode">
            <div class="weui-cell__hd"><label class="weui-label">验证码</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="text" name="verify" placeholder="请输入验证码">
            </div>
            <div class="weui-cell__ft">
                <a href="javascript:" class="verifybox" style="padding:0;"><img class="weui-vcode-img"
                        src="{:url('index/login/verify')}"></a>
            </div>
        </div>
    </div>
    <div class="weui-btn-area">
        <a class="weui-btn weui-btn_primary" href="javascript:" id="showTooltips">登陆</a>
        <div class="text-center">
            没有账号?<a href="{:url('index/login/register')}">前往注册</a>
        </div>
    </div>
</form>
{/block}
{block name="script"}
<script type="text/javascript">
    jQuery(function ($) {
        var verifyurl = '{:url('index/login/verify')}';
        if (verifyurl.indexOf('?') > 0) verifyurl += '&';
        else verifyurl += '?';
        $('.verifybox').click(function () {
            $(this).find('img').attr('src', verifyurl + '_t=' + new Date().getTime());
        });
    });
</script>
{/block}