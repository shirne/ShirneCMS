{__NOLAYOUT__}
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport"
        content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <title>跳转提示</title>
    <link href="__STATIC__/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="__STATIC__/ionicons/css/ionicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="__STATIC__/admin/css/common.css">
    <style type="text/css">
        .jumbotron {
            margin: 100px auto;
            max-width: 400px;
        }
    </style>
</head>

<body>
    <?php
if($code==1){
$class='text-success';
$title='<i class="ion-md-happy"></i>';
}
else{
$class='text-danger';
$title='<i class="ion-md-sad"></i>';
}
?>
    <div class="jumbotron w-75">
        <h2 class="display-4 <?php echo $class;?>">
            <?php echo $title;?>
        </h2>
        <p class="lead <?php echo $class;?>">
            <?php echo(strip_tags($msg));?>
        </p>
        <hr class="my-4">
        <p class="lead">
            <a class="btn btn-primary" id="href" href="<?php echo($url);?>" role="button">立即跳转</a> &nbsp;<span
                class="text-muted"><b id="wait">
                    <?php echo($wait);?>
                </b>s后自动跳转</span>
        </p>
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
                };
            }, 1000);
        })();
    </script>
</body>

</html>