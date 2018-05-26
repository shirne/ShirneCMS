ShirneCMS
===============

基于[ThinkPHP5.1](https://github.com/top-think/think/tree/5.1)+bootstrap4.x开发的后台管理系统

> 运行环境要求PHP5.6以上。


## 前端库引用

[twbs/bootstrap 4.x](https://v4.bootcss.com/docs/4.0/getting-started/introduction/)<br />
[components/jquery 3.3.1](http://api.jquery.com/)<br />
[eonasdan/bootstrap-datetimepicker](https://github.com/Eonasdan/bootstrap-datetimepicker/blob/master/docs/Options.md)<br />
[driftyco/ionicons](http://ionicons.com/)<br />
[chartjs/Chart.js 2.7.2](https://chartjs.bootcss.com/docs/)<br />
[codeseven/toastr](http://codeseven.github.io/toastr/)

## 安装说明

PHP库引用[Composer](https://getcomposer.org/download/)

>composer install

Javascript/CSS构建[Grunt](http://www.gruntjs.net/)

>cd htdocs/resource<br />
cnpm install<br />
监视文件：grunt watch<br />
生成文件：grunt

数据库

>scripts/struct.sql 数据表结构<br />
scripts/init.sql 初始数据

项目目录

>htdocs 项目根据目录<br />
htdocs/public 网站根目录

后台登录账号及密码

>admin<br />
123456

## 模板说明

标签库 [product](TAGLIB.md),[article](TAGLIB.md) 和 [extend](TAGLIB.md)

导航配置 navigator.php