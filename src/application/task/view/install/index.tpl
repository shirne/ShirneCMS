<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>ShirneCMS 系统安装</title>

    <link href="__STATIC__/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <script src="__STATIC__/jquery/jquery.min.js"></script>
    <style type="text/css">
        .main-body{
            max-width:500px;
            margin:0 auto;
        }
    </style>

    <!--[if lt IE 9]>
    <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body  class="bg-light">
<header>
<div class="container">
    <div class="py-5 text-center">
        <h2>ShirneCMS系统安装</h2>
        <p class="lead">以下选项将协助您安装本系统.</p>
    </div>

</div>
</header>
<div class="container main-body">
    <div class="text-muted mt-3">环境检测</div>
    <hr class="mb-4">

    <div class="text-muted mt-3">数据库设置</div>
    <hr class="mb-3">
    <div class="mb-3">
        <label for="email">服务器地址 </label>
        <input type="text" class="form-control" name="db[hostname]" value="{:config('database.hostname')}" >
        <div class="invalid-feedback">
        </div>
    </div>
    <div class="mb-3">
        <label for="address">数据库名</label>
        <input type="text" class="form-control" name="db[database]" value="{:config('database.database')}">
        <div class="invalid-feedback">
        </div>
    </div>
    <div class="mb-3">
        <label for="address">用户名</label>
        <input type="text" class="form-control" name="db[username]" value="{:config('database.username')}">
        <div class="invalid-feedback">
        </div>
    </div>
    <div class="mb-3">
        <label for="address">密码</label>
        <input type="text" class="form-control" name="db[password]" value="{:config('database.password')}">
        <div class="invalid-feedback">
        </div>
    </div>

    <div class="text-muted mt-3">要安装的模块</div>
    <hr class="mb-3">
    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" name="mode[]" value="shop" id="mode-shop" >
        <label class="custom-control-label" for="mode-shop">商城</label>
    </div>
    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" name="mode[]" value="wechat" id="mode-wechat" >
        <label class="custom-control-label" for="mode-wechat">微信</label>
    </div>

    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" name="mode[]" value="credit" id="mode-credit" >
        <label class="custom-control-label" for="mode-credit">积分商城</label>
    </div>

    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" name="mode[]" value="sign" id="mode-sign" >
        <label class="custom-control-label" for="mode-sign">会员签到</label>
    </div>


    <div class="text-muted mt-3">创始人设置</div>
    <hr class="mb-3">
    <div class="mb-3">
        <label for="email">登录账号 </label>
        <input type="text" class="form-control" name="admin" id="admin" value="administrator" >
        <div class="invalid-feedback">
        </div>
    </div>
    <div class="mb-3">
        <label for="address">登录密码</label>
        <input type="password" class="form-control" name="password" id="address"  required>
        <div class="invalid-feedback">

        </div>
    </div>

    <hr class="mb-4">
    <button class="btn btn-primary btn-lg btn-block" type="submit">开始安装</button>
</div>

<div class="footer">
    <div class="container">
        <hr class="my-4"/>
        <div class="copyright-row text-center">
            <div class="mt-3">
                &copy;2011-2018 {:config('app.app_name')}&nbsp;Release: {:config('app.app_release')}
            </div>
        </div>
    </div>
</div>
<script src="__STATIC__/bootstrap/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript">
    jQuery(function ($) {

    })
</script>
</body>


</html>