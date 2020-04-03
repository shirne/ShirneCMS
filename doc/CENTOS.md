本系统所需环境 php7.1^+Mysql5.6^+apache/nginx+ redis(可选)
## 集成环境 (宝塔面板等)
集成环境中都有相应软件版本的设置,按需求设置即可, 不再讲解

## 手动安装(yum)
apache和mysql 在centos7以上的版本中, 系统默认的版本可满足需求, 不需要自行编译或改源

### apache
```
# 需要ssl模块安装
yum install httpd httpd_ssl

# 加入开机启动
systemctl enable httpd

# 立即启动 stop/restart 停止/重启,修改了配置文件需要重启
systemctl start httpd
```

### nginx
nginx+php-fpm环境

php安装增加php-fpm
```
yum --enablerepo=remi-php72 install php-fpm
```

启动php-fpm及加入开机启动
```
# start/stop/status/restart
systemctl start php-fpm

systemctl enable php-fpm
```

安装nginx
```
yum install nginx
```

启动nginx及加入开机启动
```
systemctl start nginx

systemctl enable nginx
```

nginx配置
```
server {
        listen       80;
        server_name  localhost; # 你的域名
        index  index.html index.htm index.php;
	    root   /path/to/ShirneCMS/src/public;

    location / {
        if (!-e $request_filename) {
            rewrite  ^/(^static|uploads)(.*)$  /index.php/$1  last;
            break;
        }
    }

    location ~ \.php
    {
        #fastcgi_pass  unix:/tmp/php-cgi.sock;
        fastcgi_pass  127.0.0.1:9001;
        fastcgi_index index.php;
        include   fastcgi_params;
        set $real_script_name $fastcgi_script_name;
        if ($fastcgi_script_name ~ "^(.+?\.php)(/.+)$") {
            set $real_script_name $1;
            set $path_info $2;
        }
        fastcgi_param SCRIPT_FILENAME $document_root$real_script_name;
        fastcgi_param SCRIPT_NAME $real_script_name;
        fastcgi_param PATH_INFO $path_info;
    }
}
```

### mysql (mariadb)
```
yum install mariadb mariadb-server

# 开机启动
systemctl enable mariadb

# 启动
systemctl start mariadb

# 配置 (命令打一半可按tab自动识别)
mysql_secure_installation
# 输入命令按回车后需要几个步骤
# 第一步需要当前密码,新安装的一般是空的,直接回车
# 第二步是否设置新密码, 要输两遍确认
# 然后就是禁止root的远程连接,以及其它一些安全配置, 一路按 y即可
```

### php
php需要设置安装源, 默认的是5.6的版本,不建议使用
```
# remi源
yum install epel-release
yum install http://rpms.remirepo.net/enterprise/remi-release-7.rpm
yum install yum-utils

# 安装php
yum --enablerepo=remi-php72 install php
# 安装php模块
yum --enablerepo=remi-php72 install php-xml php-soap php-xmlrpc php-mbstring php-json php-gd php-bcmath php-pdo php-cli php-ssl php-pecl-redis
```

## 初始化操作
###建数据库及用户
```mysql
create database `数据库名` default character set utf8mb4;
-- 添加本机用户
create user 用户名@'localhost' identified by '密码';
-- 添加远程用户
create user 用户名@'%' identified by '密码';

-- 授权数据库
GRANT ALL PRIVILEGES ON 数据库.* TO '用户名'@'localhost';

-- 刷新权限
FLUSH PRIVILEGES;
```

### 配置web站点(virtualhost)
```conf
<VirtualHost *:80>
 DocumentRoot /path/to/shirnecms/src/public
 ServerName www.域名.com
 ServerAlias 域名.com
 Options +ExecCGI
 php_admin_value open_basedir "/path/to/shirnecms/src/:/tmp/"
 
 <Directory /path/to/shirnecms/src/public/>
  Options -Indexes +FollowSymlinks  
  AllowOverride All  
  Require all granted  
 </Directory>
</VirtualHost>
```

最后将新建的数据库名及用户,密码修改到 源码/config/database.php中
重启apache

从命令行或web界面安装系统即可