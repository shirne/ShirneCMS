{__NOLAYOUT__}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
    <title>跳转提示</title>
    <link href="__STATIC__/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="__STATIC__/ionicons/css/ionicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="__STATIC__/css/style.css">
    <style type="text/css">
        .card{margin:100px auto;width:25rem;}
    </style>
</head>
<body>
<?php
if($code==1){
$class='border-success';
$class2='text-success';
$title='<i class="ion-happy-outline"></i>';
}
else{
 $class='border-danger';
$class2='text-danger';
$title='<i class="ion-sad-outline"></i>';
}
?>
<div class="card <?php echo $class;?>">
    <div class="card-body <?php echo $class2;?>">
        <h5 class="card-title"><?php echo $title;?></h5>
        <p class="card-text"><?php echo(strip_tags($msg));?></p>
        <p class="jump">
            页面自动 <a class="card-link" id="href" href="<?php echo($url);?>">跳转</a> 等待时间： <b id="wait"><?php echo($wait);?></b>
        </p>
    </div>
</div>
<script type="text/javascript">
    (function(){
        var wait = document.getElementById('wait'),
            href = document.getElementById('href').href;
        var interval = setInterval(function(){
            var time = --wait.innerHTML;
            if(time <= 0) {
                location.href = href;
                clearInterval(interval);
            };
        }, 1000);
    })();
</script>
</body>
</html>
