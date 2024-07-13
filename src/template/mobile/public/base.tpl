<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>{$title}</title>

    <meta name="keywords" content="{$keywords}" />
    <meta name="description" content="{$description}" />

    <link href="__STATIC__/weui/css/weui.min.css" rel="stylesheet">
    <link href="__STATIC__/ionicons/css/ionicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="__STATIC__/css/mobile.css">

    <script src="__STATIC__/jquery/jquery.min.js"></script>

</head>

<body>
    <div class="container">
        <div class="page tabbar">
            <div class="page__bd" style="height: 100%;">
                <div class="weui-tab">
                    <div class="weui-tab__panel">
                        {block name="body" }{/block}
                        {include file="public:footer" /}
                    </div>

                    {include file="public:header" /}
                </div>
            </div>
        </div>
    </div>
    <script src="__STATIC__/weui/js/weui.min.js"></script>
    <script src="__STATIC__/js/mobile.min.js"></script>
    {block name="script" }{/block}
</body>

{if $isWechat}
<script type="text/javascript" src="//res.wx.qq.com/open/js/jweixin-1.6.0.js"></script>
<script>
    jQuery(function ($) {
        $.ajax({
            url: "{:url('index/jssdk')}",
            success: function (json) {
                if (json.code == 1) {
                    wx.config(json.data);
                    wx.error(function (res) {
                        console.log(res)
                    });
                    wx.ready(function () {
                        var logo_img = "{:local_media($config['site-weblogo']?:'/static/images/share_logo.jpg')}";
                        var share_imgUrl = window.share_imgurl ? window.share_imgurl : logo_img;
                        var share_title = '{$title}';
                        var share_desc = '{$description}';
                        var share_url = window.location.href;
                        var agent_code = "{$isLogin && $user['is_agent']?$user['agentcode']:''}";
                        var shareimg = new Image();
                        shareimg.src = share_imgUrl;
                        if (agent_code) {
                            if (share_url.indexOf('?') > 0) {
                                share_url += '&';
                            } else {
                                share_url += '?';
                            }
                            share_url += 'agent=' + agent_code;
                        }
                        // old version
                        wx.onMenuShareTimeline({
                            title: share_title,
                            link: share_url,
                            imgUrl: share_imgUrl
                        });
                        wx.onMenuShareAppMessage({
                            title: share_title,
                            desc: share_desc,
                            link: share_url,
                            imgUrl: share_imgUrl,
                            type: '',
                            dataUrl: ''
                        });
                        wx.onMenuShareQQ({
                            title: share_title,
                            desc: share_desc,
                            link: share_url,
                            imgUrl: share_imgUrl
                        });
                        wx.onMenuShareQZone({
                            title: share_title,
                            desc: share_desc,
                            link: share_url,
                            imgUrl: share_imgUrl
                        });

                        // >=1.4.0
                        wx.updateAppMessageShareData({
                            title: share_title,
                            desc: share_desc,
                            link: share_url,
                            imgUrl: share_imgUrl,
                            success: function () {
                            }
                        });
                        wx.updateTimelineShareData({
                            title: share_title,
                            link: share_url,
                            imgUrl: share_imgUrl,
                            success: function () {
                            }
                        });

                        wx.onMenuShareWeibo({
                            title: share_title,
                            desc: share_desc,
                            link: share_url,
                            imgUrl: share_imgUrl

                        });
                    });
                }
            }
        })
    });
</script>
{/if}

</html>