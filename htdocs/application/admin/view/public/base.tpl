<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>{:lang('Dashboard')}</title>

    <!-- Bootstrap core CSS -->
    <link href="__STATIC__/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Add custom CSS here -->
    <link href="__STATIC__/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link rel="stylesheet" href="__STATIC__/ionicons/css/ionicons.min.css">
    <link href="__STATIC__/admin/css/common.css" rel="stylesheet">

    <!-- JavaScript -->
    <script src="__STATIC__/jquery/jquery.min.js"></script>
    <script src="__STATIC__/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript">
        window.get_cate_url=function (model) {
            return "{:url('admin/index/getCate',['model'=>'__MODEL__'])}".replace('__MODEL__',model);
        };
        window.get_search_url=function (model) {
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

    <block name="header"></block>
    <script type="text/javascript">
        if(!window.IS_TOP && !window.frameElement){
            //console.log('{:url("index/index")}?url={:url()}')
            top.location = '{:url("index/index")}?url={:url()}'
        }
    </script>

</head>

<body>


    <block name="body" ></block>

    <script src="__STATIC__/moment/min/moment.min.js"></script>
    <script src="__STATIC__/moment/locale/zh-cn.js"></script>
    <script src="__STATIC__/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
    <script src="__STATIC__/admin/js/app.min.js?v={:config('template.static_version')}"></script>

    <block name="script"></block>
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
                        for (var key in json) {
                            var node = null;
                            switch (key) {
                                case 'newMemberCount':
                                    node = $('[data-key=member_index]');
                                    break;
                                case 'newOrderCount':
                                    node = $('[data-key=order_index]');
                                    break;
                            }
                            if (node) {
                                if (json[key] > 0) {
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
        (function(){

            if(!window.IS_TOP){
                var curkey = $(window.frameElement).data('key');

                $('a[href]').click(function (e) {
                    var target=$(this).attr('target');
                    if(target && target !== '_self')return;

                    e.preventDefault();

                    var url = $(this).attr('href');
                    if($(this).attr('rel')==='ajax')return;
                    if(url.indexOf('javascript')===0 || url.indexOf('#')===0)return;
                    var subkey = $(this).data('tab');
                    if(!subkey)subkey = url.replace(/^(\/|http:|https:)/g,'').replace(/[\/.]/g,'_').replace('.html','');

                    if(subkey === 'random') {
                        subkey = curkey + '_' + Math.random().toString().substr(2);
                    }else if(subkey === 'timestamp'){
                        subkey = curkey + '_' + new Date().getTime();
                    }

                    var title=$(this).text();
                    if(!title)title=$(this).attr('title');
                    top.createPage(subkey, title, url, curkey);
                });

                if(window.page_title){
                    top.updatePage(curkey, window.page_title);
                }else {
                    var title = $('.breadcrumb').data('title');
                    if (title) {
                        top.updatePage(curkey, title);
                    }
                }

                $('.bread_refresh').click(function (e) {
                    location.reload();
                });

                $(document.body).click(function () {
                    top.$(top.document.body).trigger('click');
                });
            }
        })();

    </script>
</body>
</html>