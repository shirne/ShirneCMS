ShirneCMS
===============

基于[ThinkPHP5.1](https://github.com/top-think/think/tree/5.1)+[bootstrap4.x](https://v4.bootcss.com/docs/4.0/getting-started/introduction/)开发的后台管理系统

> 运行环境要求PHP7.1.3以上。

## 功能说明

本项目侧重于二次开发使用，后台功能在逐步完善。前台功能仅用于功能演示。目前个人及团队的一些订制项目均基于此后台开发。
> 后台管理员登录验证，基于方法名的全局权限验证系统<br />
> 管理员/会员操作日志<br />
> 系统配置，可后台自定义配置项<br />
> 会员/会员组 可自定义分销层级<br />
> 无限级分类/文章系统，分类可独立设置模板<br />
> 无限级分类/产品/订单  产品采用多选项SKU模式<br />
> 广告管理（多用于网站banner图）/公告/链接/留言 等独立小功能<br />
> 基于bs的Modal写的Dialog组件，支持alert,prompt,confirm,以及常用的列表搜索/选择对话框，地图位置选择对话框（支持腾讯/高德/百度/谷哥地图）<br />
> 后台表单异步提交/文件上传进度显示<br />
> bs日期组件本地化，自动初始化<br />
> excel封装类，第三方接口（短信/快递等），文件上传封装 等便于开发的优化<br />
> 图片自动处理/缓存 上传文件夹中的图片在地址后加入w/h/q/m参数可按指定需求自动裁剪，按[原文件名.参数.原后缀]调用的地址会自动裁剪并缓存

## 前端库引用

[twbs/bootstrap 4.x](https://v4.bootcss.com/docs/4.0/getting-started/introduction/)<br />
[components/jquery 3.3.1](http://api.jquery.com/)<br />
[eonasdan/bootstrap-datetimepicker](https://github.com/Eonasdan/bootstrap-datetimepicker/blob/master/docs/Options.md)<br />
[driftyco/ionicons](http://ionicons.com/)<br />
[chartjs/Chart.js 2.7.2](https://chartjs.bootcss.com/docs/)<br />
[codeseven/toastr](http://codeseven.github.io/toastr/)<br />
[swiper](http://www.swiper.com.cn/)

## 后端库引用
[EasyWechat](https://www.easywechat.com/docs/3.x/zh-CN/index)<br />
[phpoffice/phpspreadsheet]()<br />
[phpmailer]()<br />
[endroid/qr-code]()

## 安装说明

PHP库引用[Composer](https://getcomposer.org/download/)

>cd htdocs<br />
>composer install

Javascript/CSS构建[Gulp](https://www.gulpjs.com.cn/)

>cd htdocs/resource<br />
cnpm install<br />
生成并监视文件：gulp

~~Javascript/CSS构建~~[~~Grunt~~](http://www.gruntjs.net/)

>cd htdocs/resource<br />
cnpm install<br />
监视文件：grunt watch<br />
生成文件：grunt

数据库

>scripts/struct.sql 数据表结构<br />
scripts/init.sql 初始数据<br />
scripts/update_shop.sql 商城模块

项目目录

>htdocs 项目根目录<br />
htdocs/public 网站根目录

后台默认登录账号 [演示网站](http://host3.shirne.net/admin)
>admin<br />
密码：123456

## 模板说明

分离模板目录配置 template.independence

标签库 [product](TAGLIB.md),[article](TAGLIB.md) 和 [extendtag](TAGLIB.md)

导航配置 navigator.php

## 后台功能截图
![登录](screenshot/login.jpg "登录")
![主面板](screenshot/dashboard.jpg "主面板")
![分类管理](screenshot/category.jpg "分类管理")
![添加分类](screenshot/category-add.jpg "添加分类")
![发布文章](screenshot/article-add.jpg "发布文章")
![发布商品](screenshot/product-add.jpg "发布商品")
![发布商品](screenshot/product-add2.jpg "发布商品")
![订单统计](screenshot/order-static.jpg "订单统计")
![广告管理](screenshot/ad.jpg "广告管理")
![系统设置](screenshot/setting.jpg "系统设置")
![会员组](screenshot/user-level.jpg "会员组")
![微信菜单](screenshot/wechat-menu.jpg "微信菜单")