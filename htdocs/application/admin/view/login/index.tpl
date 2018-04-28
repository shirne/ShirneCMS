<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>管理员登陆</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap core CSS -->
    <link href="__STATIC__/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Add custom CSS here -->
    <link href="__STATIC__/admin/css/common.css" rel="stylesheet">
    <link rel="stylesheet" href="__STATIC__/font-awesome/css/font-awesome.min.css">
    <style type="text/css">
        #canvas{
            position: absolute;left:0;top:0;background:#000;
            /*background-image: -webkit-radial-gradient(ellipse farthest-corner at center 30%, #000d4d 0%, #000105 100%);
            background-image: radial-gradient(ellipse farthest-corner at center 30%, #000d4d 0%, #000105 100%);*/
        }
    </style>
</head>
<body>
<canvas id="canvas" ></canvas>
<script type="text/javascript" src="__STATIC__/admin/js/effect-dot.js"></script>
<div class="container" id="loginContainer">
    <div class="row">
        <div class="col-md-4 col-md-offset-4" id="loginBox">
            <h1>管理员登陆</h1>

            <form action="{:U('login/login')}" method="post">
                <div class="form-group">
                    <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-user"></i> </span>
                    <input type="text" name="username" class="form-control" id="exampleInputUser" placeholder="用户名">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-lock"></i> </span>
                    <input type="password" name="password" class="form-control" id="exampleInputPassword" placeholder="密码">
                        </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-check-circle-o"></i> </span>
                    <input type="text"  name="verify" class="form-control" id="exampleInputCode" placeholder="验证码">
                        <span class="input-group-addon" style="padding:0;"><img class="verify" src="{:U('login/verify')}" alt="点击刷新"/></span>
                    </div>
                </div>
                <button type="submit" class="btn btn-block btn-primary">登陆</button>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-md-offset-4 copy">
        <p>&copy;原设软件 2015-2017</p>
        </div>
    </div>
</div>
<script src="__STATIC__/js/jquery-1.10.2.js"></script>
<script>
    jQuery(function($){
        var verify=$(".verify"),verifysrc=verify.attr('src');
        if(verifysrc.indexOf('?')>0){
            verifysrc+='&';
        }else{
            verifysrc+='?';
        }
        verify.click(function(){
            $(this).attr("src",verifysrc+"_t="+new Date().getTime());

        });
    })
</script>
</body>
</html>