ShirneCMS
===============

基于[ThinkPHP5.1](https://github.com/top-think/think/tree/5.1)+[bootstrap4.x](https://v4.bootcss.com/docs/4.0/getting-started/introduction/)开发的后台管理系统,集成会员管理，文章管理，产品管理，微信接入，第三方登录等功能

基于[ThinkPHP6.0](https://github.com/top-think/think/tree/6.0) 的版本 [ShirneCMS-tp6](https://gitee.com/shirnecn/ShirneCMS/tree/dev_tp6/) 目前正在进行框架兼容性调整

> 运行环境要求PHP7.2以上，Mysql5.5以上<br />
> PHP扩展：mbstring,gd,mysql,pdo,cURL,OpenSSL,SimpleXML,fileinfo,zip,cli。

## 相关项目

[小程序企业官网](https://gitee.com/shirnecn/website_mapp)<br />
[小程序商城](https://gitee.com/shirnecn/shop_mapp)<br />
[FlutterAPP](https://gitee.com/shirnecn/ShirneApp) -- 开发中<br />
[VueShop](https://gitee.com/shirnecn/ShirneVueShop) -- 优化中<br />

## 功能特点

本项目侧重于二次开发使用，后台功能在逐步完善。前台功能仅用于功能演示。目前个人及团队的一些订制项目均基于此后台开发。
* 后台管理员登录验证，基于方法名的全局权限验证系统
* 管理员/会员操作日志
* 系统配置，可后台自定义配置项
* 会员/会员组 可自定义分销层级
* 无限级分类/文章系统，分类可独立设置模板
* 无限级分类/产品/订单  产品采用多选项SKU模式
* 广告管理（多用于网站banner图）/公告/链接/留言 等独立小功能
* 基于bs的Modal写的Dialog组件，支持alert,prompt,confirm,以及常用的列表搜索/选择对话框，地图位置选择对话框（支持腾讯/高德/百度/谷哥地图）
* 后台表单异步提交/文件上传进度显示
* bs日期组件本地化，自动初始化
* excel封装类，第三方接口（短信/快递等），文件上传封装 等便于开发的优化
* 图片自动处理/缓存 上传文件夹中的图片在地址后加入w/h/q/m参数可按指定需求自动裁剪，按\[原文件名.参数.原后缀\]调用的地址会自动裁剪并缓存
* 微信基本功能接入(自动登录，接口绑定，支付，公众号菜单)
* API模块采用简单的OAuth模式实现登录授权，微信授权，token刷新等

## 功能规划

### 系统功能结构图
![功能结构图](screenshot/struct.png "功能结构图")

### 系统基础
- [x] 系统安装
- [x] 后台界面使用标签页(dev_tabs分支)
- [x] 分类/文章模块
- [x] 单页模块
- [ ] 文章模块自定义字段

### 商城系统
- [x] 无限级分类
- [x] 商品属性
- [x] 商品规格，分类绑定规格
- [x] 购物车
- [x] SKU管理
- [x] 订单管理
- [x] 商品品牌
- [x] 优惠券 ... 后台功能完成，待完善使用功能
- [x] 邮费模板
- [x] 推荐位
- [ ] 促销功能

### 积分商城
- [x] 无限级分类
- [x] 积分商品
- [x] 订单管理

### 其它功能
- [x] 积分商城……测试中
- [x] 会员签到

### 多语言支持
- [x] 前台多语言切换逻辑
- [ ] 前后台多语言翻译（中/英）……开发中
 
### 微信功能完善
- [x] 粉丝管理/同步/推送消息(文本/文章/产品/素材)
- [x] 自动回复(文本/图文)，托管消息处理待支持
- [x] 素材管理
- [x] 自定义菜单(待增加: 小程序绑定/回复绑定/处理程序绑定)
- [x] 模板消息……支持预设ID导入
- [ ] 二维码管理
- [ ] 客服管理

### API部分功能完善
- [x] 通用接口，批量接口
- [x] 文章接口（含分类）
- [x] 登录接口（账号密码登录，小程序授权登录）
- [x] 商品接口
- [x] 购物车接口
- [x] 订单及支付接口
- [x] 积分商品接口
- [x] 积分商城下单接口
- [x] 会员资料
- [x] 会员签到,签到排行
- [x] 收货地址管理
- [x] 订单管理
- [x] 账户管理(余额/积分明细,提现充值) ……在线充值接口待开发
- [ ] 会员升级申请

## 感谢
### 前端库

[twbs/bootstrap 4.x](https://v4.bootcss.com/docs/4.0/getting-started/introduction/)<br />
[components/jquery 3.3.1](http://api.jquery.com/)<br />
[eonasdan/bootstrap-datetimepicker](https://github.com/Eonasdan/bootstrap-datetimepicker/blob/master/docs/Options.md) 针对bootstrap4.x修改<br />
[driftyco/ionicons](http://ionicons.com/)<br />
[chartjs/Chart.js 2.7.2](https://chartjs.bootcss.com/docs/)<br />
[swiper](http://www.swiper.com.cn/)

### 后端库
[ThinkPHP](http://www.thinkphp.cn/)<br />
[EasyWechat](https://www.easywechat.com/docs/3.x/zh-CN/index)<br />
[phpoffice/phpspreadsheet](https://phpspreadsheet.readthedocs.io/en/develop/)<br />
[phpmailer](https://github.com/PHPMailer/PHPMailer)<br />
[endroid/qr-code](https://github.com/endroid/qr-code)

### 字体(生成图片使用)
[NotoSansCJKsc]
[百度网盘](https://pan.baidu.com/s/1i5l0qblUhhendpIhOqu4Iw) 提取码：7cwl
```
# 字体文件目录
\src\public\static\fonts\NotoSansCJKsc
```

## 安装 及 开发说明

#### 服务器环境

>IIS7以上 + UrlRewrite <br />
>Apache2.2以上 + mod_rewrite <br />
>Nginx + php-fpm

[Windows配置说明](doc/WINDOWS.md)

[CentOS配置说明](doc/CENTOS.md)

[CMS安装说明](doc/INSTALL.md)

[API接口说明](doc/api/index.md)

## Docker
配置参见Dockerfile
注：仅初步配置成功环境参数，具体运行过程中还有一些文件权限和挂载问题未搞清楚

```
// 创建镜像
cd ./docker-php-apache
docker build -t shirnecms .

// 运行
docker run -itd -p 8080:80/tcp -v $PWD/src:/data/wwwroot/shirnecms:rw shirnecms --privileged=true

// 需要在线导入sql，则把dbscript拷贝到容器中
docker cp dbscript <容器ID>:/data/wwwroot/shirnecms/

// macos中mysql使用宿主机的话主机填写
docker.for.mac.host.internal

```

## 演示
#### 后台默认登录账号 [演示网站](http://cms.qisoweb.com/admin)
>test<br />
密码：123456

#### 基于本系统开发的 [蔬菜库存管理系统](http://erp.qisoweb.com/)
>test<br />
密码：123456

## 常见问题

常见问题 [Dialog](doc/QA.md)


## 后台功能截图
|登录|主面板|
|:---:|:---:|
|![登录](screenshot/login.jpg "登录")|![主面板](screenshot/dashboard.jpg "主面板")|
|分类管理|添加分类|
|![分类管理](screenshot/category.jpg "分类管理")|![添加分类](screenshot/category-add.jpg "添加分类")|
|发布文章|发布单页|
|![发布文章](screenshot/article-add.jpg "发布文章")|![单页](screenshot/single.jpg "发布单页")|
|发布商品|发布商品|
|![发布商品](screenshot/product-add.jpg "发布商品")|![发布商品](screenshot/product-add2.jpg "发布商品")|
|运费模板|订单统计|
|![运费模板](screenshot/postage.jpg "运费模板")|![订单统计](screenshot/order-static.jpg "订单统计")|
|系统设置|优惠券|
|![系统设置](screenshot/setting.jpg "系统设置")|![优惠券](screenshot/coupon.jpg "优惠券")|
|会员设置|签到设置|
|![会员设置](screenshot/setting-member.jpg "会员设置")|![签到设置](screenshot/setting-sign.jpg "签到设置")|
|会员组|会员管理|
|![会员组](screenshot/user-level.jpg "会员组")|![会员管理](screenshot/user.jpg "会员管理")|
|微信设置|微信菜单|
|![微信设置](screenshot/wechat-setting.jpg "微信设置")|![微信菜单](screenshot/wechat-menu.jpg "微信菜单")|
|广告位|发布广告|
|![广告位](screenshot/ad-group.jpg "广告位")|![发布广告](screenshot/ad-detail.jpg "发布广告")|

## 联系我
欢迎技术探讨，部署安装及定制开发收费
* QQ 79099818
* 微信 shirnewei

备注 技术探讨/咨询 或 其它需求

## 开源协议
ShirneCMS延续ThinkPHP开源协议Apache-2.0，提供个人及商业免费使用，但不对使用本系统引起的任何后果负责。
