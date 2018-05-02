<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$title}</title>

    <meta name="keywords" content="{$keywords}" />
    <meta name="description" content="{$description}" />

    <link href="__STATIC__/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="__STATIC__/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="__STATIC__/ionicons/css/ionicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="__STATIC__/css/style.css">

    <script src="__STATIC__/jquery/jquery.min.js"></script>

    <!--[if lt IE 9]>
    <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<include file="public:header" />

<block name="body" ></block>

<include file="public:footer" />
<script src="__STATIC__/bootstrap/js/bootstrap.min.js"></script>
<script src="__STATIC__/js/init.js"></script>
<block name="script" ></block>
</body>

<if condition="$isWechat">
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
    wx.config({
        debug: false,
        appId: '{$signPackage.appId}',
        timestamp: {$signPackage.timestamp},
        nonceStr: '{$signPackage.nonceStr}',
        signature: '{$signPackage.signature}',
        jsApiList: [
        // 所有要调用的 API 都要加到这个列表中
        'onMenuShareTimeline',
        'onMenuShareAppMessage',
        'onMenuShareQQ',
        'onMenuShareWeibo',
        'onMenuShareQZone'
    ]
    });
    wx.ready(function () {
        // 在这里调用 API
        //获取“分享到朋友圈”按钮点击状态及自定义分享内容接口
        wx.onMenuShareTimeline({
            title: '{$title}', // 分享标题
            link:  window.location.href, // 分享链接
            imgUrl: '{$signPackage.logo}', // 分享图标
        });
        //获取“分享给朋友”按钮点击状态及自定义分享内容接口
        wx.onMenuShareAppMessage({
            title: '{$title}', // 分享标题
            desc: '{$description}', // 分享描述
            link:  window.location.href, // 分享链接
            imgUrl: '{$signPackage.logo}', // 分享图标
            type: '', // 分享类型,music、video或link，不填默认为link
            dataUrl: '' // 如果type是music或video，则要提供数据链接，默认为空
        });
        //获取“分享到QQ”按钮点击状态及自定义分享内容接口
        wx.onMenuShareQQ({
            title: '{$title}', // 分享标题
            desc: '{$description}', // 分享描述
            link:  window.location.href, // 分享链接
            imgUrl: '{$signPackage.logo}' // 分享图标
        });
        //获取“分享到腾讯微博”按钮点击状态及自定义分享内容接口
        wx.onMenuShareWeibo({
            title: '{$title}', // 分享标题
            desc: '{$description}', // 分享描述
            link:  window.location.href, // 分享链接
            imgUrl: '{$signPackage.logo}' // 分享图标

        });
        //获取“分享到QQ空间”按钮点击状态及自定义分享内容接口
        wx.onMenuShareQZone({
            title: '{$title}', // 分享标题
            desc: '{$description}', // 分享描述
            link:  window.location.href, // 分享链接
            imgUrl: '{$signPackage.logo}' // 分享图标
        });
    });
</script>
</if>

</html>