<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>管理员登录</title>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap core CSS -->
    <link href="__STATIC__/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Add custom CSS here -->
    <link href="__STATIC__/admin/css/common.css?v={:config('template.static_version')}" rel="stylesheet">
    <link rel="stylesheet" href="__STATIC__/ionicons/css/ionicons.min.css">
    <style type="text/css">
        #canvas {
            position: absolute;
            left: 0;
            top: 0;
            background: #000;
            /*background-image: -webkit-radial-gradient(ellipse farthest-corner at center 30%, #000d4d 0%, #000105 100%);
            background-image: radial-gradient(ellipse farthest-corner at center 30%, #000d4d 0%, #000105 100%);*/
        }
    </style>
</head>
<body>
<canvas id="canvas"></canvas>
<script type="text/javascript" src="__STATIC__/admin/js/effect-dot.js"></script>
<div class="container" id="loginContainer">
    <div class="row justify-content-center">
        <div class="col-10 col-md-7 col-lg-5" id="loginBox">
            <h1>管理员登录</h1>

            <form action="{:url('login/login')}" method="post">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="ion-md-person"></i> </span>
                        </div>
                        <input type="text" name="username" class="form-control" id="exampleInputUser" placeholder="用户名">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="ion-md-lock"></i> </span>
                        </div>
                        <input type="password" name="password" class="form-control" id="exampleInputPassword"
                               placeholder="密码">
                    </div>
                </div>
                {if $config['captcha_mode'] EQ 1}
                <div class="form-group">
                    <p id="wait" class="text-muted">验证码加载中...</p>
                    <div id="captchabox" class="m-auto"></div>
                </div>
                {else/}
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="ion-md-checkmark-circle"></i> </span>
                        </div>
                        <input type="text" name="verify" class="form-control" id="exampleInputCode" autocomplete="off" placeholder="验证码">
                    </div>
                </div>
                <div class="form-group text-center">
                    <figure class="figure m-0 text-center">
                        <img class="figure-img img-fluid rounded verify" src="{:url('login/verify')}" alt="点击刷新"/>
                        <figcaption class="figure-caption">看不清？点击图片刷新</figcaption>
                    </figure>
                </div>
                {/if}
                <button type="submit" class="btn btn-block btn-primary">登陆</button>
                <div class="alert fade show" role="alert">
                    <span class="alert-content"></span>
                </div>
            </form>
            <div class="browser-check text-center hidden">
                <h3 class="m-2 text-danger">您使用的浏览器功能不完整</h3>
                <div class="mb-3"><b>双核</b>浏览器请切换到<b>极速模式</b>使用</div>
                <h3 class="text-success mb-3">推荐使用</h3>
                <div class="browser-list">
                    <a class="browser-item" href="https://www.google.cn/chrome/" target="_blank">
                        <div class="browser-icon" style="background-image:url(/static/admin/images/chrome-logo.svg)"></div>
                        <div class="browser-text">谷哥</div>
                    </a>
                    <a class="browser-item" href="https://www.mozilla.org/zh-CN/firefox/new/" target="_blank">
                        <div class="browser-icon" style="background-image:url(/static/admin/images/firefox-logo.png)"></div>
                        <div class="browser-text">火狐</div>
                    </a>
                    <a class="browser-item" href="https://browser.360.cn/ee/" target="_blank">
                        <div class="browser-icon" style="background-image:url(/static/admin/images/360-logo.png)"></div>
                        <div class="browser-text">360极速</div>
                    </a>
                    <a class="browser-item" href="https://www.opera.com/zh-cn" target="_blank">
                        <div class="browser-icon" style="background-image:url(/static/admin/images/opera-logo.png)"></div>
                        <div class="browser-text">Opera</div>
                    </a>
                    <div class="clear-fix"></div>
                </div>
                <div class="mt-3"><a href="javascript:" class="text-muted force-login">我知道了，继续登录</a> </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-md-center">
        <div class="col-md-4 copy">
            <p>&copy;原设软件 2015-2017</p>
        </div>
    </div>
</div>
<script src="__STATIC__/jquery/jquery.min.js"></script>
{if $config['captcha_mode'] EQ 1}
<script type="text/javascript" src="/static/js/gt.js"></script>
{/if}
<script>
    jQuery(function ($) {
        //浏览器功能检测
        if(!(window.URL && window.URL.createObjectURL) || !File || !FormData){
            $('form').hide();
            $('.browser-check').show();
        }
        $('.force-login').click(function () {
            $('.browser-check').hide();
            $('form').show();
        });

        var verify_mode="{$config['captcha_mode']}";
        var captchaObj=null;

        if(verify_mode==1){
            $.ajax({
                url:"{:url('login/verify')}",
                dataType:'JSON',
                success:function (data) {
                    //console.log(data);
                    initGeetest({
                        gt: data.gt,
                        width:'100%',
                        challenge: data.challenge,
                        new_captcha: data.new_captcha,
                        product: "embed", // 产品形式，包括：float，embed，popup。注意只对PC版验证码有效
                        offline: !data.success
                    }, function (newObj) {
                        captchaObj=newObj;
                        $("#captchabox").click(function (e) {
                            var validate = captchaObj.getValidate();
                            if (!validate) {
                                showmsg('请先完成验证');
                                e.preventDefault();
                            }
                        });
                        captchaObj.appendTo("#captchabox");
                        captchaObj.onReady(function () {
                            $("#wait").hide();
                        });
                    });
                }
            })
        }else {
            var verify = $(".verify"), verifysrc = verify.attr('src');
            if (verifysrc.indexOf('?') > 0) {
                verifysrc += '&';
            } else {
                verifysrc += '?';
            }
            verify.click(function () {
                $(this).attr("src", verifysrc + "_t=" + new Date().getTime());

            });
        }

        function showmsg(message,type) {
            if(!type)type='danger';
            var icon='information-circle-outline';
            if(type=='success')icon='checkmark-circle';
            $('.alert-content').html('<i class="ion-md-'+icon+'"></i> '+message);
            $('.alert').attr('class','alert fade show').addClass('alert-'+type).show();
        }

        $('form').submit(function(e) {
            e.preventDefault();
            var errors=[];
            if(!this.username.value){
                errors.push('用户名');
            }
            if(!this.password.value) {
                errors.push('密码');
            }
            if(verify_mode!=1) {
                if (!this.verify.value) {
                    errors.push('验证码');
                }
            }
            if(errors.length>0){
                showmsg('请填写'+errors.join('、'));
                return false;
            }
            if(verify_mode==1) {
                if (!captchaObj || !captchaObj.getValidate()) {
                    showmsg('请完成验证');
                    return false;
                }
            }
            $('.btn-primary').attr('disabled',true);
            $.ajax({
                url:"{:url('login')}",
                type:'POST',
                dataType:'JSON',
                data:$(this).serialize(),
                success:function(json){
                    if(json.code==1){
                        showmsg(json.msg,'success');
                        location.href=json.url;
                    }else{
                        showmsg(json.msg);
                        $('.btn-primary').removeAttr('disabled');
                        if(verify_mode==1){
                            captchaObj && captchaObj.reset();
                        }else {
                            verify.trigger('click');
                        }
                    }
                },
                error:function () {
                    showmsg('服务器错误');
                    $('.btn-primary').removeAttr('disabled');
                    if(verify_mode==1){
                        captchaObj && captchaObj.reset();
                    }else {
                        verify.trigger('click');
                    }
                }
            })
        })
    })
</script>
</body>
</html>