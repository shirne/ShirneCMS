<center>auth(登录注册相关接口)</center>
=======================================

## [token](#token)
获取临时访问access_token，用于注册、登录、验证码请求
* 请求地址 域名/api/auth/token
* 是否需要授权 否

### 参数
* appid 预设的平台id
* agent 可空

### 返回值
```
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data":
}
```

## [login](#login)
用户名密码登录，首次登录失败需要验证码
* 请求地址 域名/api/auth/login
* 是否需要授权 是 临时授权

### 参数

### 返回值
```
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data":
}
```

## [wxSign](#wxSign)
生成公众号签名
* 请求地址 域名/api/auth/wxsign
* 是否需要授权 否

### 参数
* wxid 公众号hasid或数字id 为空时返回默认公众号的签名结果
* url 要签名的链接地址，留空自动从referer中获取

### 返回值
```
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data":{
        "url":"页面链接"
        "debug": false,
        "beta": false,
        "jsApiList": [
            "updateAppMessageShareData",
            "updateTimelineShareData",
            "onMenuShareTimeline",
            "onMenuShareAppMessage",
            "onMenuShareQQ",
            "onMenuShareWeibo",
            "onMenuShareQZone",
            "checkJsApi",
            "chooseImage",
            "previewImage",
            "openAddress",
            "openLocation",
            "getLocation",
            "hideOptionMenu",
            "showOptionMenu",
            "hideMenuItems",
            "showMenuItems"
        ],
        "openTagList": [],
        "appId": "公众号appid",
        "nonceStr": "随机串",
        "timestamp": 1630134596,
        "signature": "签名字串"
    }
}
```

## [wxAuth](#wxAuth)
获取授权跳转链接
* 请求地址 域名/api/auth/wxauth
* 是否需要授权 否

### 参数
* wxid 公众号hasid或数字id 为空时返回默认公众号的授权链接
* url 跳转链接，留空自动获取referer

### 返回结果
```
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data":{
        "url": "跳转链接"
    }
}
```

## [wxLogin](#wxLogin)
微信公众号/小程序登录
* 请求地址 域名/api/auth/wxlogin
* 是否需要授权 否

### 参数
* wxid 公众号hasid或数字id 为空时返回默认公众号的授权链接
* code 从微信授权链接或小程序内调用获得的授权码
* agent 推荐码，可留空

### 返回值
```
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": {
        "token": "",
        "refresh_token": "",
        "expire_in": 1800, //token有效时间(秒)，失效后使用refresh_token重新获取
        "member_id": 1,
        "openid": ""
    }
}
```

## [refresh](#refresh)
刷新token
* 请求地址 域名/api/auth/refresh
* 是否需要授权 是

### 参数
* refresh_token 登录或刷新时获取的刷新token

### 返回值
```
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": {
        "token": "",
        "refresh_token": "", // 每次刷新都会更新，上一个refresh_token就会失效
        "expire_in": 1800, //token有效时间(秒)
        "member_id": 1
    }
}
```

## [captcha](#captcha)
验证码 (图片)
* 请求地址 域名/api/auth/login
* 是否需要授权 是 临时授权

### 参数
无

### 返回值 
```
图片数据，直接在img标签引用
```

## [smscode](#smscode)
获取短信验证码
* 请求地址 域名/api/auth/smscode
* 是否需要授权 是 临时授权

### 参数
* mobile 接收验证码的手机号
* captcha 图形验证码
* type 验证码类型 login/register/verify

### 返回值 
```
{
    "code":1,
    "msg":"验证码已发送",
    "time":1630133833
}
```

## [quit](#quit)
退出登录
* 请求地址 域名/api/auth/quit
* 是否需要授权 是

### 参数
无

### 返回值 
```
{
    "code":1,
    "msg":"退出成功",
    "time":1630133833
}
```

## [forgot](#forgot)
忘记密码
* 请求地址 域名/api/auth/forgot
* 是否需要授权 是 临时授权

### 参数
* account 要验证的账号(手机号或邮箱)
* type 账号类型 mobile/email
* password 新设置的密码
* verify 验证码(手机或邮箱接收到的验证码)

### 返回值
```
{
    "code":1,
    "msg":"密码重置成功",
    "time":1630133833
}
```

## [register](#register)
会员注册
* 请求地址 域名/api/auth/register
* 是否需要授权 是 临时授权

### 参数
* agent 推荐码 可选
* username 登录名
* password 密码
* repassword 确认密码
* email 注册邮箱 可空
* realname 真实姓名 可空
* mobile 注册手机号
* mobilecheck 手机号是否验证
* verify 短信验证码
* invite_code 邀请码 根据设置为必填或可空
* openid 绑定的微信资料

### 返回值
注册成功后无需再登录，直接返回token数据
```
{
    "code":1,
    "msg":"注册成功",
    "time":1630133833,
    "data": {
        "token": "",
        "refresh_token": "", 
        "expire_in": 1800, //token有效时间(秒)
        "member_id": 1
    }
}
```