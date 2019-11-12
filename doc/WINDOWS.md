## Windows7+ 开启IIS支持

在 **控制面板** 中找到 **程序和功能** <br />
打开 **启用或关闭** 功能界面<br />
![IIS配置](https://images.gitee.com/uploads/images/2019/0724/191051_998a52b2_23652.png "aaa.png")
对web平台的支持主要在应用程序开发功能的列表里，看需要勾选(php需要的应该是CGI)

#### 安装 URLRewrite 
搜索关键字： microsoft urlrewrite<br />
目前的下载链接： [URLRewrite](https://www.microsoft.com/en-us/download/details.aspx?id=47337)

## 下载配置PHP

#### 下载
打开 [PHP官网](http://php.net) 在导航找到下载页(Downloads)<br />
注意这里的版本是针对 linux平台的，找到对应版本 (我目前用的7.2) 下面下载列表里有 [Windows downloads](https://windows.php.net/download#php-7.2)<br />
到下载页面，找到对应版本的 Non Thread Safe 版本（现在系统都是x64的吧，默认按x64下载）下载zip压缩包<br />
注意在下载项名称里，有个所需VC版本 ，比如7.2需要 VC15 。在左侧栏内的小字里可以找到对应的VC运行时包的下载链接。（过低的版本不会显示在这里了，要自己去找）
![PHP下载页](https://images.gitee.com/uploads/images/2019/0724/192022_95dcca11_23652.png "bbb.png")
两个都下载完成后，安装VC运行时，解压PHP文件到一个合适的目录

#### 配置
解压后的php文件，根目录有  php.ini-development 和 php.ini-production 两个配置文件示例。<br />
开发环境可以把php.ini-development 复制一份或直接重命名为php.ini ，还放在原目录内( 不要移动到系统 windows目录，移动到系统目录后无法使用多php版本切换了，因为读配置文件总会读到同一个 )。<br />
然后使用文本编辑软件打开php.ini，修改以下几项(找不到可以搜索关键字)
```ini
;扩展目录，相对于当前php的根目录
 extension_dir = "ext"


;默认时区，Asia/Shanghai 是中国的标准时区 PRC, Asia/Chongqing  是以前版本的别名，可以用但不推荐
date.timezone = Asia/Shanghai


;开启的扩展项 前面带分号的是不开启的。这里根据自己需要选择。
;一般常用到的 gd2,curl,fileinfo,pdo,mysql,mbstring,openssl 
extension=bz2
extension=curl
extension=fileinfo
extension=gd2
;extension=gettext
;extension=gmp
;extension=intl
;extension=imap
;extension=interbase
;extension=ldap
extension=mbstring
extension=exif      ; Must be after mbstring as it depends on it
extension=mysqli
;extension=oci8_12c  ; Use with Oracle Database 12c Instant Client
extension=odbc
extension=openssl
;extension=pdo_firebird
extension=pdo_mysql
;extension=pdo_oci
extension=pdo_odbc
;extension=pdo_pgsql
extension=pdo_sqlite
;extension=pgsql
;extension=shmop
extension=redis
extension=php_pdo_sqlsrv_72_nts_x64
extension=php_sqlsrv_72_nts_x64
zend_extension=opcache

; The MIBS data available in the PHP distribution must be installed.
; See http://www.php.net/manual/en/snmp.installation.php
;extension=snmp

extension=soap
extension=sockets
extension=sqlite3
;extension=tidy
extension=xmlrpc
extension=xsl
```

#### 在iis中添加模块映射

在iis管理器的根节点(这个是针对全部站点的默认配置)，找到处理程序映射<br />
如需多个站点配置不同版本的支持，就要在具体的站点内，设置这个模块映射，指向到对应的php版本
![iis 处理程序映射](https://images.gitee.com/uploads/images/2019/0724/193223_6bd0d815_23652.png "ccc.png")
在处理程序映射的列表中，路径一栏，如果找到 *.php 就选中点编辑，如果没有（新配置的一般是没有），点击右侧操作  **添加模块映射**  
![添加模块映射](https://images.gitee.com/uploads/images/2019/0724/193545_fda40e80_23652.png "ddd.png")
选择文件时如果只显示dll，不显示exe  就在文件名右侧类型中选择 * 或 *.exe<br />
名称一栏自己随便填。<br />
点击确定时，会弹出添加到fastcgi集合的提示，点  **是**  就好了

#### 配置默认文档
同样是在iis根节点配置<br />
IIS 默认文档列表中一般只有html和aspx,  可以把没用的删掉，添加一个 index.php


#### 使用域名做本机开发
如果不使用域名，就只能用ip地址，localhost等，绑定的虚拟站点有限，多了就要用端口，很不方便。有些系统内也会根据域名作一些处理。<br />
可以找一个用不到的域名，或没人注册的域名 （我用 test.com） ，把主域名以及设置几个子域名，在hosts(C:\Windows\System32\drivers\etc)里绑定到本机IP<br />
![hosts绑定本机ip](https://images.gitee.com/uploads/images/2019/0724/194259_fbc48fc0_23652.png "rrr.png")
hosts文件直接打开修改是不能保存的，可以在打开编辑器的时候使用管理员方式打开，再打开hosts<br />
或者将hosts复制一份到桌面，修改后复制回去覆盖
![管理员方式打开](https://images.gitee.com/uploads/images/2019/0724/194447_486165e0_23652.png "qqq.png")

然后在创建虚拟站点的时候，就可以使用绑定过本机的域名了，绑定好在开发调试的时候直接使用域名就可以访问了<br />
注意，外部是不能访问的哦，局域网要想访问，在对方电脑中修改hosts，指向你的电脑的局域网ip (比如 192.168.0.105)