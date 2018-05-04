<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>管理员登陆</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap core CSS -->
    <link href="__STATIC__/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Add custom CSS here -->
    <link href="__STATIC__/admin/css/common.css" rel="stylesheet">
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
        <div class="col-10 col-md-5" id="loginBox">
            <h1>管理员登陆</h1>

            <form action="{:url('login/login')}" method="post">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="ion-person"></i> </span>
                        </div>
                        <input type="text" name="username" class="form-control" id="exampleInputUser" placeholder="用户名">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="ion-locked"></i> </span>
                        </div>
                        <input type="password" name="password" class="form-control" id="exampleInputPassword"
                               placeholder="密码">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="ion-checkmark-circled"></i> </span>
                        </div>
                        <input type="text" name="verify" class="form-control" id="exampleInputCode" placeholder="验证码">
                        <div class="input-group-append">
                            <span class="input-group-text" style="padding:0;">
                                <img class="verify" src="{:url('login/verify')}" alt="点击刷新"/>
                            </span>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-block btn-primary">登陆</button>
                <div class="alert fade show" role="alert">
                    <span class="alert-content"></span>
                </div>
            </form>
        </div>
    </div>
    <div class="row justify-content-md-center">
        <div class="col-md-4 copy">
            <p>&copy;原设软件 2015-2017</p>
        </div>
    </div>
</div>
<script src="__STATIC__/jquery/jquery.min.js"></script>
<script>
    jQuery(function ($) {
        var verify = $(".verify"), verifysrc = verify.attr('src');
        if (verifysrc.indexOf('?') > 0) {
            verifysrc += '&';
        } else {
            verifysrc += '?';
        }
        verify.click(function () {
            $(this).attr("src", verifysrc + "_t=" + new Date().getTime());

        });

        $('form').submit(function(e) {
            e.preventDefault();
            var errors=[];
            if(!this.username.value){
                errors.push('用户名');
            }
            if(!this.password.value) {
                errors.push('密码');
            }
            if(!this.verify.value) {
                errors.push('验证码');
            }
            if(errors.length>0){
                $('.alert-content').html('<i class="ion-alert-circled"></i> 请填写'+errors.join('、'));
                $('.alert').addClass('alert-danger').show();
                return false;
            }
            $('.btn-primary').attr('disabled',true);
            $.ajax({
                url:"{:url('login')}",
                type:'POST',
                dataType:'JSON',
                data:$(this).serialize(),
                success:function(json){
                    if(json.code==1){
                        $('.alert-content').html('<i class="ion-checkmark-circled"></i> '+json.msg);
                        $('.alert').removeClass('alert-danger').addClass('alert-success').show();
                        location.href=json.url;
                    }else{
                        $('.alert-content').html('<i class="ion-alert-circled"></i> '+json.msg);
                        $('.alert').addClass('alert-danger').show();
                        $('.btn-primary').removeAttr('disabled');
                        verify.trigger('click');
                    }
                }
            })
        })
    })
</script>
</body>
</html>