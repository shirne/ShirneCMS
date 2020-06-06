<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>{:lang('Dashboard')}</title>

    <!-- Bootstrap core CSS -->
    <link href="__STATIC__/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Add custom CSS here -->
    <link href="__STATIC__/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link rel="stylesheet" href="__STATIC__/ionicons/css/ionicons.min.css">
    <link href="__STATIC__/admin/css/common.css?v={:config('template.static_version')}" rel="stylesheet">

    <!-- JavaScript -->
    <script src="__STATIC__/jquery/jquery.min.js"></script>
    <script src="__STATIC__/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript">
        window.get_cate_url=function (model) {
            return "{:url('admin/index/getCate',['model'=>'__MODEL__'])}".replace('__MODEL__',model);
        };
        window.get_search_url=function (model) {
            if(model == 'product')model = 'shop.product';
            return "{:url('admin/--model--/search')}".replace('--model--',model);
        };
        window.get_view_url=function (model,id) {
            var baseurl='';
            switch (model){
                case 'article':
                    baseurl="{:url('index/article/view',['id'=>0])}";
                    break;
                case 'product':
                    baseurl="{:url('index/product/view',['id'=>0])}";
                    break;
            }
            return baseurl.replace('0',id);
        };
        //地图密钥
        window['MAPKEY_BAIDU'] = '{:getSetting("mapkey_baidu")}';
        window['MAPKEY_GOOGLE'] = '{:getSetting("mapkey_google")}';
        window['MAPKEY_TENCENT'] = '{:getSetting("mapkey_tencent")}';
        window['MAPKEY_GAODE'] = '{:getSetting("mapkey_gaode")}';
    </script>

    {block name="header"}{/block}

</head>

<body>

<div id="wrapper">

    <!-- Sidebar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" role="navigation">

        <a class="navbar-brand text-light" href="{:url('index/index')}"><i class="ion-md-speedometer"></i> {:lang('Management')}</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end navbar-ex1-collapse" id="navbarSupportedContent">

            {include file="public/sidebar" /}

            <div class="nav navbar-nav navbar-user">
                <li class="dropdown user-dropdown">
                    <a href="javascript:" class="nav-link" data-toggle="dropdown"><i class="ion-md-notifications"></i></a>
                    <div class="dropdown-menu">
                        <span class="dropdown-item">暂无提醒</span>
                    </div>
                </li>
                <li class="dropdown user-dropdown">
                    <a href="javascript:" class="nav-link dropdown-toggle" data-toggle="dropdown"><i class="ion-md-person"></i> {:lang('Welcome %s',[session('adminname')])} <b class="caret"></b></a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="/" target="_blank"><i class="ion-md-home"></i> {:lang('Home ')}</a>
                        <a class="dropdown-item" href="{:url('index/clearcache')}"><i class="ion-md-sync"></i> {:lang('Clear Cache')}</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{:url('setting/index')}"><i class="ion-md-options"></i> {:lang('Settings')}</a>
                        <a class="dropdown-item" href="{:url('index/profile')}"><i class="ion-md-person"></i> {:lang('Profile')}</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{:url('login/logout')}"><i class="ion-md-log-out"></i> {:lang('Sign out')}</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    {block name="body" }{/block}

    <script src="__STATIC__/moment/min/moment.min.js"></script>
    <script src="__STATIC__/moment/locale/zh-cn.js"></script>
    <script src="__STATIC__/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
    <script src="__STATIC__/admin/js/app.min.js?v={:config('template.static_version')}"></script>
    <script type="text/javascript">
        (function(){
            $('img').on('error',function() {
                $(this).attr('src','/static/images/nopic.png');
            });

            var func=arguments.callee;
            if(window.stop_ajax){
                setTimeout(func, 2000);
            }else {
                $.ajax('{:url("index/newcount")}', {
                    dataType: 'JSON',
                    type: 'POST',
                    success: function (json) {
                        //console.log(json);
                        $('.side-nav .badge').remove();
                        if(json.total){
                            $('#notice-icon').html('<i class="ion-md-notifications"></i> ('+json.total+')')
                            $('#notice-box').html('')
                        }else{
                            $('#notice-icon').html('<i class="ion-md-notifications"></i>')
                            $('#notice-box').html('<span class="dropdown-item">暂无提醒</span>')
                        }
                        for (var key in json) {
                            var node = null;
                            var notice_title = '';
                            var args='';

                            switch (key) {
                                case 'newMemberCount':
                                    node = $('[data-key=member_index]');
                                    notice_title = '新会员'
                                    break;
                                case 'newOrderCount':
                                    node = $('[data-key=shop_order_index]');
                                    notice_title = '待发货订单'
                                    args='?status=1'
                                    break;
                                case 'newMemberAuthen':
                                    node = $('[data-key=member_authen_index]');
                                    notice_title = '合伙人申请'
                                    break;
                                case 'newMemberCashin':
                                    node = $('[data-key=paylog_cashin]');
                                    notice_title = '提现申请'
                                    break;
                            }
                            if (node) {
                                if ( json[key] > 0 ) {
                                    $('#notice-box').append('<a class="dropdown-item" href="'+node.attr('href')+args+'" >'+notice_title+'<span class="badge badge-light">' + json[key] + '</span></a>');
                                    
                                    var badge = node.find('.badge');
                                    if (badge.length < 1) {
                                        node.append('<span class="badge badge-light">' + json[key] + '</span>');
                                    } else {
                                        badge.text(json[key]);
                                    }
                                    if (node.parents('.panel-body').length > 0) {
                                        var pbadge = node.parents('.panel').find('.panel-title a .badge');
                                        if (pbadge.length < 1) {
                                            node.parents('.panel').find('.panel-title a').append('<span class="badge badge-light">..</span>');
                                        } else {
                                            pbadge.text(json[key]);
                                        }
                                    }
                                }else{
                                    node.find('.badge').remove();
                                }
                            }
                        }

                        setTimeout(func, 10000);
                    },
                    error: function () {
                        setTimeout(func, 10000);
                    }
                });
            }
        })();
    </script>
    {block name="script"}{/block}
</body>
</html>