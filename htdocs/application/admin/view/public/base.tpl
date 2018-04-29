<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理面板</title>

    <!-- Bootstrap core CSS -->
    <link href="__STATIC__/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Add custom CSS here -->
    <link href="__STATIC__/datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link rel="stylesheet" href="__STATIC__/font-awesome/css/font-awesome.min.css">
    <link href="__STATIC__/admin/css/common.css" rel="stylesheet">

    <!-- JavaScript -->
    <script src="__STATIC__/jquery/jquery.min.js"></script>
    <script src="__STATIC__/bootstrap/js/bootstrap.min.js"></script>

    <block name="header"></block>

</head>

<body>

<div id="wrapper">

    <!-- Sidebar -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{:U('index/index')}">管理后台</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse navbar-ex1-collapse">

            <include file="Public/sidebar" />

            <ul class="nav navbar-nav navbar-right navbar-user">

                <li class="dropdown user-dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> 你好,{:session('adminname')} <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="/" target="_blank"><i class="fa fa-home"></i> 浏览</a></li>
                        <li class="divider"></li>
                        <li><a href="{:U('setting/index')}"><i class="fa fa-gear"></i> 设置</a></li>
                        <li><a href="{:U('Index/profile')}"><i class="fa fa-user"></i> 资料</a></li>
                        <li class="divider"></li>
                        <li><a href="{:U('login/logout')}"><i class="fa fa-power-off"></i> 退出</a></li>
                    </ul>
                </li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </nav>

    <block name="body" ></block>

    <script src="__STATIC__/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
    <script src="__STATIC__/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js"></script>
    <script src="__STATIC__/admin/js/app.js?v=20180406"></script>
    <script type="text/javascript">
        (function(){
            var func=arguments.callee;
            $.ajax('{:U("index/newcount")}', {
                dataType:'JSON',
                type:'POST',
                success:function(json){
                    //console.log(json);
                    $('.side-nav .badge').remove();
                    for(var key in json){
                        var node=null;
                        switch (key){
                            case 'newMemberCount':
                                node=$('[data-key=member_index]');
                                break;
                            case 'newOrderCount':
                                node=$('[data-key=order_index]');
                                break;
                        }
                        if(node){
                            if(json[key]>0){
                                var badge=node.find('.badge');
                                if(badge.length<1){
                                    node.append('<span class="badge">'+json[key]+'</span>');
                                }else {
                                    badge.text(json[key]);
                                }
                                if(node.parents('.panel-body').length>0){
                                    var pbadge=node.parents('.panel').find('.panel-title a .badge');
                                    if(pbadge.length<1){
                                        node.parents('.panel').find('.panel-title a').append('<span class="badge">..</span>');
                                    }else {
                                        pbadge.text(json[key]);
                                    }
                                }
                            }
                        }
                    }

                    setTimeout(func,5000);
                }
            })
        })();
    </script>
    <block name="script"></block>
</body>
</html>