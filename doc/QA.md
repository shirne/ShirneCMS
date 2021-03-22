## 常见问题

#### SSL证书错误
接入微信公众号后，进入公众号管理页面出现 curl: (60) SSL certificate problem: unable to get local issuer certificate 错误，需要在php.ini中配置
```$ini
curl.cainfo = /path/to/downloaded/cacert.pem
```
并重启相关服务器 （cacert.pem在htdocs/config/cert目录下，该文件为从微信平台下载）

#### 虚拟主机不支持在web根目录之外布署系统

步骤较多，移步另一个说明[虚拟主机不支持web目录之外上传文件的解决办法](VIRTUAL.md)

#### 小程序本地测试 xxx.test.com无法访问

xxx.test.com 为本地测试目录，配置方法：
1. 在hosts 中增加 指定域名，指向到 127.0.0.1
2. 在本地服务器环境(IIS, Apache)中绑定该域名
3. 浏览器中打开该域名，即可访问到本地测试站点


#### iis+php环境下跨域对options请求的处理

iis里有个默认的处理程序拦截了OPTIONS请求.在跨域请求无法正确返回的情况下,可以去删除这个默认配置
[解决办法](https://www.zhaokeli.com/article/8542.html)