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
<script src="__STATIC__/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="__STATIC__/js/init.min.js"></script>
<script type="text/javascript">
    jQuery(function ($) {
        setNav('{$navmodel}');
    })
</script>
<block name="script" ></block>
</body>

<if condition="$isWechat">
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js"></script>
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