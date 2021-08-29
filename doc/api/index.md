## API说明索引

[通用接口](common.md)

[授权登录接口](auth.md)

[文章接口](article.md)

[产品接口](product.md)

[单页接口](page.md)

[购物车接口](cart.md)

[会员中心接口](member.md)

## 约定及说明

```tree
访问基础路径     域名/api/
授权header键    token
临时token       access_token
传参方式         put/get/post
```

## 全局字段
* code  状态码，成功时为1, 错误时为0或其它错误码
* message 错误或成功信息
* time 服务器时间戳
* url 需要跳转链接的接口指向的跳转地址
* data 接口返回的具体数据

## 全局错误码
* 99 需要登录 需要登录权限才可以访问的接口
* 101 登录失败
* 102 token无效
* 103 token过期
* 104 需要验证码  登录接口在登录失败后可能需要验证码才能继续登录，需要ui显示验证码让用户填写
* 105 refresh_token失效
* 109 登录失败,需要绑定 
* 111 注册失败
* 112 需要openid才能操作
* 113 用户已被禁用
* 115 临时token失效 需要再次获取新的临时token