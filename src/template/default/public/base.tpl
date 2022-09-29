<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>{$title}</title>
    <if condition="!empty($keywords)">
    <meta name="keywords" content="{$keywords}" />
    </if>
    <if condition="!empty($description)">
    <meta name="description" content="{$description}" />
    </if>
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
<script type="text/javascript" src="//res.wx.qq.com/open/js/jweixin-1.4.0.js"></script>
<script>
    jQuery(function($){
        $.ajax({
            url:"{:url('index/jssdk')}",
            success:function(json){
                if(json.code==1){
                    wx.config(json.data);
                    wx.error(function(res){
                        console.log(res)
                    });
                    wx.ready(function () {
                        var version= '1.3.2';
                        var logo_img="{:local_media($config['site-weblogo']?:'/static/images/share_logo.jpg')}";
                        var share_imgUrl= window.share_imgurl?window.share_imgurl: logo_img;
                        var share_title = '{$title}';
                        var share_desc = '{$description}';
                        var share_url = window.location.href;
                        var agent_code="{$isLogin && $user['is_agent']?$user['agentcode']:''}";
                        var shareimg=new Image();
                        shareimg.src = share_imgUrl;
                        if(agent_code){
                            if(share_url.indexOf('?')>0){
                                share_url += '&';
                            }else{
                                share_url += '?';
                            }
                            share_url += 'agent='+agent_code;
                        }
                        if(version !== '1.4.0'){
                            wx.onMenuShareTimeline({
                                title: share_title,
                                link:  share_url,
                                imgUrl: share_imgUrl
                            });
                            wx.onMenuShareAppMessage({
                                title: share_title,
                                desc: share_desc,
                                link:  share_url,
                                imgUrl: share_imgUrl,
                                type: '',
                                dataUrl: ''
                            });
                            wx.onMenuShareQQ({
                                title: share_title,
                                desc: share_desc,
                                link:  share_url,
                                imgUrl: share_imgUrl
                            });
                            wx.onMenuShareQZone({
                                title: share_title,
                                desc: share_desc,
                                link:  share_url,
                                imgUrl: share_imgUrl
                            });
                        }else {


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
                        }

                        wx.onMenuShareWeibo({
                            title: share_title,
                            desc: share_desc,
                            link:  share_url,
                            imgUrl: share_imgUrl

                        });
                    });
                }
            }
        })
    });
</script>
</if>

</html>