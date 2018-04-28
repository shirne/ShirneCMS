<?php
    if(C('LAYOUT_ON')) {
        echo '{__NOLAYOUT__}';
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>跳转提示</title>
    <link href="__STATIC__/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="__STATIC__/font-awesome/css/font-awesome.min.css">
    <style type="text/css">
        body{
            background: #f6f6f6;
            font-family: "Microsoft Yahei";
        }
        .panel{
            width:500px;margin:150px auto;
            box-shadow: 1px 1px 5px rgba(0,0,0,.2);
        }
        .panel .panel-body{
            padding:15px 25px;
        }
        .panel .panel-body .iconbox{
            font-size:32px;
            float:left;
        }
        .panel .panel-body a{
            color:#333;
        }
        i.icon-info{
            color:#bce8f1;
        }
        i.icon-success{
            color:#d6e9c6;
        }
        i.icon-danger{
            color:#ebccd1;
        }
        p.message{
            margin-top:10px;
            padding-left:10px;text-indent: 2em;
            color:#111;
        }
        p.jump{
            clear:both;
            color:#888;
            text-align:right;
        }
    </style>
</head>
<body>
<?php
$type='info';
$icon='info-circle';
if(isset($message)) {
    $type='success';
    $icon='check-circle';
}
if(isset($error)) {
    $type='danger';
    $icon='minus-circle';
}
?>

<div class="panel panel-{$type}">
    <div class="panel-heading">系统信息</div>
    <div class="panel-body">
        <p class="iconbox">
        <i class="bigicon icon-{$type} fa fa-{$icon}"></i>
        </p>
        <p class="message"><?php echo isset($error)?$error:$message; ?></p>

        <p class="jump"> <b id="wait"><?php echo($waitSecond); ?></b>s 后自动跳转 <a id="href" class="btn btn-default btn-sm" href="<?php echo($jumpUrl); ?>">立即跳转</a></p>
    </div>
</div>

<script type="text/javascript">
    (function () {
        var wait = document.getElementById('wait'), href = document.getElementById('href').href;
        var interval = setInterval(function () {
            var time = --wait.innerHTML;
            if (time <= 0) {
                location.href = href;
                clearInterval(interval);
            }
        }, 1000);
    })();
</script>
</body>
</html>
