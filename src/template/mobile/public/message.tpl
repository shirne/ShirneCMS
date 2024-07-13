{__NOLAYOUT__}<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>跳转提示</title>
    <link rel="stylesheet" href="__STATIC__/weui/css/weui.min.css" />
    <?php
    if($code==1){
        $class='weui-icon-success';
    }else{
        $class='weui-icon-info';
    }
    ?>
</head>

<body>
    <div class="page msg_warn js_show">
        <div class="weui-msg">
            <div class="weui-msg__icon-area"><i class="<?php echo $class;?> weui-icon_msg"></i></div>
            <div class="weui-msg__text-area">
                <h2 class="weui-msg__title">
                    <?php echo(strip_tags($msg));?>
                </h2>
                <p class="weui-msg__desc"></p>
            </div>
            <div class="weui-msg__opr-area">
                <p class="weui-btn-area">
                    <a href="<?php echo($url);?>" id="href" class="weui-btn weui-btn_primary">确定(<span id="wait">
                            <?php echo($wait);?>
                        </span>)</a>
                    <a href="javascript:history.back();" class="weui-btn weui-btn_default">返回</a>
                </p>
            </div>
            <div class="weui-msg__extra-area">
                <div class="weui-footer">
                    <p class="weui-footer__links">
                        <a href="/" class="weui-footer__link">首页</a>
                    </p>
                    <p class="weui-footer__text">Copyright © 2015-2018 ShirneCMS</p>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            (function () {
                var wait = document.getElementById('wait'),
                    href = document.getElementById('href').href;
                var time = parseInt('<?php echo $wait;?>');
                var interval = setInterval(function () {
                    wait.innerHTML = (--time).toString();
                    if (time <= 0) {
                        location.href = href;
                        clearInterval(interval);
                    }
                }, 1000);
            })();
        </script>
    </div>
</body>

</html>