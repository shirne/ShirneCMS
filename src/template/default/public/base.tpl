<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>{$title}</title>
    {if !empty($keywords)}
    <meta name="keywords" content="{$keywords}" />
    {/if}
    {if !empty($description)}
    <meta name="description" content="{$description}" />
    {/if}
    <link href="__STATIC__/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="__STATIC__/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="__STATIC__/ionicons/css/ionicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="__STATIC__/css/style.css">

    <script src="__STATIC__/jquery/jquery.min.js"></script>

    <!--[if lt IE 9]>
    <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>
    {include file="public:header" /}

    {block name="body" }{/block}

    {include file="public:footer" /}
    <script src="__STATIC__/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="__STATIC__/js/init.min.js"></script>
    <script type="text/javascript">
        jQuery(function ($) {
            setNav('{$navmodel}');

            $('.list-group-item').click(function (e) {
                var target = $(e.target);
                if (target.is('a')) return;
                if (target.parents('a').length > 0) return;
                var link = $(this).find('a[href]')
                if (link.length > 0) {
                    if (link.is('.btn-confirm')) {
                        return;
                    }
                    var anchor = target.data('anchor')
                    if (anchor) {
                        link.attr('href', link.attr('href') + '#' + anchor)
                    }
                    link[0].click();
                }
            });
            $('.gotop-btn').click(function () {
                $("html,body").animate({
                    scrollTop: 0
                }, 500);
            })

            $('.need-login').click(function () {
                dialog.confirm("{:lang( 'Please login first!' )}", function () {
                    location.href = "{:url('index/login/index')}"
                })
            })

            $('.favate-btn').click(function (e) {
                e.preventDefault();
                if (!isLogin) {
                    dialog.alert('请先登录')
                    return
                }

                var type = $(this).data('type')
                var id = $(this).data('id')
                $.ajax({
                    url: "{:aurl('index/member.favourite/add')}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        type: type,
                        id: id,
                    },
                    success: function (json) {
                        if (json.code == 1) {
                            dialog.success(json.msg)
                            setTimeout(function () {
                                location.reload()
                            }, 500)
                        } else {
                            dialog.alert(json.msg)
                        }
                    }
                })
            })
            $('.cancel-favate-btn').click(function (e) {
                e.preventDefault();
                if (!isLogin) {
                    dialog.alert('请先登录')
                    return
                }

                var type = $(this).data('type')
                var id = $(this).data('id')
                dialog.confirm('取消收藏？', function () {
                    $.ajax({
                        url: "{:aurl('index/member.favourite/remove')}",
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            type: type,
                            id: id,
                        },
                        success: function (json) {
                            if (json.code == 1) {
                                dialog.success(json.msg)
                                setTimeout(function () {
                                    location.reload()
                                }, 500)
                            } else {
                                dialog.alert(json.msg)
                            }
                        }
                    })
                })
            })

            var ajaxes = $('.ajax-notice')
            var timer
            function startAjax() {
                clearTimeout(timer)
                var modules = []
                for (var i = 0; i < ajaxes.length; i++) {
                    var module = ajaxes.eq(i).data('module')
                    if (modules.indexOf(module) > -1) continue;
                    modules.push(module)
                    switch (module) {
                        default:
                            getMessageCount()
                    }
                }
                timer = setTimeout(startAjax, 5000);
            }
            if (isLogin) {
                startAjax();
            }

            var isMessageSync = false
            function getMessageCount() {
                if (isMessageSync) return
                isMessageSync = true
                try {
                    $.ajax({
                        url: "{:aurl('index/member.message/count')}",
                        type: 'POST',
                        dataType: 'json',
                        success: function (json) {
                            isMessageSync = false
                            if (json.code == 1) {
                                $('.ajax-notice-message').each(function () {
                                    var badge = $(this).find('.badge')
                                    var count = json.data.total
                                    if ($(this).data('type') == 'receive') {
                                        count = json.data.receive
                                    } else if ($(this).data('type') == 'send') {
                                        count = json.data.send
                                    }
                                    if (count > 0) {
                                        if (badge.length < 1) {
                                            $(this).append('<span class="badge badge-light">' + count + '</span>')
                                        } else {
                                            badge.text(count)
                                        }
                                    } else {
                                        badge.remove()
                                    }
                                })
                            }
                        },
                        error: function () {
                            isMessageSync = false
                        }
                    })
                } catch (err) {
                    isMessageSync = false
                }

            }
        })
    </script>
    {block name="script" }{/block}
</body>

{if $isWechat}
<script type="text/javascript" src="//res.wx.qq.com/open/js/jweixin-1.6.0.js"></script>
<script>
    jQuery(function ($) {
        $.ajax({
            url: "{:url('index/jssdk')}",
            data: {
                url: location.href
            },
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