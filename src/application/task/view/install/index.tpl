<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>ShirneCMS 系统安装</title>

    <link href="__STATIC__/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="__STATIC__/ionicons/css/ionicons.min.css" rel="stylesheet">

    <script src="__STATIC__/jquery/jquery.min.js"></script>
    <style type="text/css">
        .main-body{
            max-width:500px;
            margin:0 auto;
        }
        .step h4{
            font-size:1.2rem;
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
    <div class="progress">
    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 1%"></div>
    </div>
    <form action="" class="install-form">
        <div class="step step-1">
            <div class="text-muted mt-3"><a href="javascript:location.reload()" class="float-right">刷新</a>环境检测</div>
            <hr class="mb-4">
            <ul class="list-group">
                {volist name="envs" id="item"}
                    {if is_null($item['pass'])}
                        <li class="list-group-item">
                            <div class="float-right">未检测 <i class="ion-md-help-circle-outline"></i></div>
                            <div>
                                <h4 class="mb-0">{$item['title']}</h4>
                                <div class="text-muted">Require: {$item['require']}</div>
                            </div>
                        </li>
                    {elseif !empty($item['pass'])/}
                        <li class="list-group-item text-success">
                            <div class="float-right">{$item['current']} <i class="ion-md-checkmark-circle-outline"></i></div>
                            <div>
                                <h4 class="mb-0">{$item['title']}</h4>
                                <div class="text-muted">Require: {$item['require']}</div>
                            </div>
                        </li>
                    {else/}
                        <li class="list-group-item text-danger">
                            <div class="float-right">{$item['current']} <i class="ion-md-close-circle-outline"></i></div>
                            <div>
                                <h4 class="mb-0">{$item['title']}</h4>
                                <div class="text-muted">Require: {$item['require']}</div>
                            </div>
                        </li>
                    {/if}
                {/volist}
            </ul>
        </div>

        <div class="step step-2">
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
            <div class="dbmsgbox"></div>
        </div>

        <div class="step step-3">
            <div class="text-muted mt-3">要安装的模块</div>
            <hr class="mb-3">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" name="mode[]" value="shop" id="mode-shop" >
                <label class="custom-control-label" for="mode-shop">商城</label>
            </div>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" name="mode[]" value="wechat" id="mode-wechat" >
                <label class="custom-control-label" for="mode-wechat">微信公众号管理</label>
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
        </div>
        <hr class="mb-4">
        <div class="text-center mb-2"><a class="btn-prev" href="javascript:">上一步</a></div>
        <button class="btn btn-primary btn-lg btn-block btn-next" {$pass?'':'disabled'} type="button">下一步</button>
        <button class="btn btn-primary btn-lg btn-block btn-submit" type="submit">开始安装</button>
        <div class="msgbox mt-2"></div>
    </form>
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
        var step=0;
        var env_pass="{$pass?1:''}";
        
        function updateProgress(){
            var progress=Math.round(step*100/($('.step').length+1));
            $('.progress-bar').css('width',progress+'%')
        }
        function checkStep(){
            if(step==1){
                if(!env_pass)return;
            }
            if(step==2){
                $.ajax({
                    url:"{:url('task/install/connectdb')}",
                    dataType:'json',
                    type:'POST',
                    data:$('form').serialize(),
                    success:function(json){
                        if(json.code==1){
                            $('.btn-next').prop('disabled',false);
                            $('.dbmsgbox').html('<div class="alert alert-success" role="alert">'+json.msg+'</div>').show();
                        }else{
                            $('.dbmsgbox').html('<div class="alert alert-danger" role="alert">'+json.msg+'</div>').show();
                        }
                    },
                    error:function(){
                        $('.dbmsgbox').html('<div class="alert alert-danger" role="alert">数据库连接失败</div>').show();
                    }
                })
                return;
            }
            $('.btn-next').prop('disabled',false);
            $('.btn-submit').prop('disabled',false);
        }

        var timeout=0;
        $('.step-2 input').change(function(e){
            clearTimeout(timeout);
            $('.btn-next').prop('disabled',true);
            $('.dbmsgbox').html('<div class="alert alert-secondary" role="alert"><div class="spinner-border  spinner-border-sm text-secondary" role="status">  <span class="sr-only">Loading...</span></div> 正在尝试数据库连接...</div>');
            timeout=setTimeout(function(){
                checkStep();
            },800)
        })
        
        $('.btn-submit').hide();
        $('.btn-prev').hide();
        $('.btn-next').click(function(e){
            e.preventDefault();

            step ++;

            $('.step').hide();
            $('.step-'+step).show();
            
            $('.btn-next').prop('disabled',true);
            if(step>1){
                $('.btn-prev').show();
            }else{
                $('.btn-prev').hide();
            }
            if($('.step-'+(step+1)).length<1){
                $('.btn-submit').show();
                $('.btn-next').hide();
            }
            updateProgress()
            checkStep()
        }).trigger('click')

        $('.btn-prev').click(function(e){
            e.preventDefault();
            if(step <=1)return;
            step --;
            $('.msgbox').hide();
            $('.step').hide();
            $('.step-'+step).show();
            $('.btn-next').show();
            $('.btn-submit').hide();
            if(step<2){
                $('.btn-prev').hide();
            }
            updateProgress()
            checkStep()
        })
        
        $('.install-form').submit(function(e){
            e.preventDefault();
            $('.btn-submit,.btn-prev').prop('disabled',true);
            $('.msgbox').html('<div class="alert alert-secondary" role="alert"><div class="spinner-border  spinner-border-sm text-secondary" role="status">  <span class="sr-only">Loading...</span></div> 正在执行安装...</div>').show();
            $.ajax({
                url:"{:url('task/install/index')}",
                dataType:'json',
                type:'POST',
                data:$(this).serialize(),
                success:function(json){
                    $('.btn-submit,.btn-prev').prop('disabled',false);
                    if(json.code==1){
                        $('.btn-submit,.btn-prev').hide();
                        $('.step').hide();
                        step++;
                        updateProgress();
                        $('.msgbox').html('<div class="alert alert-success" role="alert">'+json.msg+'  前往<a href="{:url("admin/index/index")}">后台管理</a></div>').show();
                    }else{
                        $('.msgbox').html('<div class="alert alert-danger" role="alert">'+json.msg+'</div>').show();
                    }
                },
                error:function(){
                    $('.btn-submit,.btn-prev').prop('disabled',false);
                    $('.msgbox').html('<div class="alert alert-danger" role="alert">安装失败,请检查安装日志</div>').show();
                }
            })
        })
    })
</script>
</body>


</html>