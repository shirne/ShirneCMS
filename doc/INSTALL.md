## 系统安装

### 包引用
系统代码全部在src目录下, 如果电脑有安装composer  可在src目录执行
```shell
composer install
```
来安装系统需要的库.<br />
**如果没有Composer**, 也可以从我分享的 [压缩包下载](https://pan.baidu.com/s/1i5l0qblUhhendpIhOqu4Iw) 提取码：7cwl
打包下载(zip包解压后放入src目录,即application同级目录)
下载后vendor和thinkphp两个目录, 解压到src目录下

### web配置

> PHP版本 7.1以上 (主要是easysdk4.x需要)<br />
iis下新建站点, 物理路径指向到  src/public 

### 数据库配置

**命令行导入和网页安装,需要事先把数据库配置填写好**
> 数据库配置文件在 src/config/database.php

### 导入数据库

> 数据库可以手动导入,顺序为<br />
```shell
scripts/struct.sql 数据表结构
scripts/init.sql 初始数据

//以下模块按需导入,不限顺序
scripts/update_shop.sql 商城模块
scripts/update_credit.sql 积分商城
scripts/update_wechat.sql 微信模块
scripts/update_sign.sql 会员签到
```

> 也可以在命令行导入
```shell
php think install
```

> 网页安装( 域名/task/install ) 服务器配置不高的情况下,安装全部功能, 有可能出现超时错误

**注意** 网页安装时把 dbscript 目录上传到 src目录下(application同级目录)

安装完成后就可以访问了
> 后台地址: 域名/admin<br />
> 默认账号: administrator/123456


## 开发说明

### 项目目录

>src 项目根目录<br />
src/public 网站根目录
resource 前端开发目录

### Javascript/CSS构建[Gulp](https://www.gulpjs.com.cn/)

>cd resource<br />
cnpm install<br />
构建并监视文件：gulp<br />
清理dest目录: gulp clean<br />
只构建文件: gulp build<br />
只监视文件: gulp watch


### 模板说明

模板目录: src/template

PC移动端分离模板/自适应模板<br />
分离模板目录配置 template.independence

标签库 [product](TAGLIB.md#product),[article](TAGLIB.md#article) 和 [extendtag](TAGLIB.md#extendtag)

弹出框组件说明 [Dialog](DIALOG.md)

导航配置 navigator.php