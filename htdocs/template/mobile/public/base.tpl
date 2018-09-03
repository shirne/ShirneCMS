<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
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
                    <block name="body" ></block>
                    <include file="public:footer" />
                </div>

                <include file="public:header" />
            </div>
        </div>
    </div>
</div>
<script src="__STATIC__/weui/js/weui.min.js"></script>
<script src="__STATIC__/js/mobile.min.js"></script>
<block name="script" ></block>
</body>

<if condition="$isWechat">
    <script type="text/javascript" src="{$protocol}://res.wx.qq.com/open/js/jweixin-1.1.0.js"></script>
    <script>
        var imageUrl='__STATIC__/images/logo.png';
        wx.config({$signPackage|raw});
        wx.ready(function () {
            wx.onMenuShareTimeline({
                title: '{$title}',
                link:  window.location.href,
                imgUrl: imageUrl
            });
            wx.onMenuShareAppMessage({
                title: '{$title}',
                desc: '{$description}',
                link:  window.location.href,
                imgUrl: imageUrl,
                type: '',
                dataUrl: ''
            });
            wx.onMenuShareQQ({
                title: '{$title}',
                desc: '{$description}',
                link:  window.location.href,
                imgUrl: imageUrl
            });
            wx.onMenuShareWeibo({
                title: '{$title}',
                desc: '{$description}',
                link:  window.location.href,
                imgUrl: imageUrl

            });
            wx.onMenuShareQZone({
                title: '{$title}',
                desc: '{$description}',
                link:  window.location.href,
                imgUrl: imageUrl
            });
        });
    </script>
</if>

</html>