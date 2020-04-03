<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>{$title}</title>

    <meta name="keywords" content="{$keywords}" />
    <meta name="description" content="{$description}" />

    <link href="__STATIC__/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="__STATIC__/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="__STATIC__/ionicons/css/ionicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="__STATIC__/css/blog.css?v={:config('template.static_version')}">

    <script src="__STATIC__/jquery/jquery.min.js"></script>
    {block name="header" }{/block}
    <!--[if lt IE 9]>
    <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<include file="public:header" />

{block name="body" }{/block}

<include file="public:footer" />
<script src="__STATIC__/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="__STATIC__/js/init.min.js"></script>
<script type="text/javascript">
    jQuery(function ($) {
        setNav('{$navmodel}');
        $('.list-group-item').click(function(e){
            var target=$(e.target);
            if(target.is('a'))return;
            if(target.parents('a').length>0)return;
            var link=$(this).find('a[href]')
            if(link.length>0){
                var anchor=target.data('anchor')
                if(anchor){
                    link.attr('href',link.attr('href')+'#'+anchor)
                }
                link[0].click();
            }
        });
    })
</script>
{block name="script" }{/block}
</body>

{if $isWechat}
<script type="text/javascript" src="{$protocol}://res.wx.qq.com/open/js/jweixin-1.3.2.js"></script>
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
{/if}

</html>