<center>member(会员基础接口)</center>
=======================================

## [profile](#profile)
获取会员资料
* 请求地址 域名/api/member/profile
* 是否需要授权 是

### 参数
* agent 可选 设置了agent将更新未绑定会员的推荐人

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": {
        "id": 1,

    }
}
```

## [update_profile](#update_profile)
更新会员资料
* 请求地址 域名/api/member/update_profile
* 是否需要授权 是

### 参数
* username 修改登录名，只有在会员登录名未初始化时才可以自行修改，否则请不要传此参数
* nickname 昵称
* realname 真实姓名
* email 邮箱
* mobile 手机号码
* gender 性别 
* birth 生日 yyyy-mm-dd
* qq QQ号码
* wechat 微信号
* alipay 支付宝账号
* province 所在省份
* city 所在城市
* county 所在街道
* address 详细地址

### 返回值
```json
{
    "code":1,
    "msg":"保存成功",
    "time":1630133833
}
```

## [mobile_register](#mobile_register)
首次绑定手机号
* 请求地址 域名/api/member/mobile_register
* 是否需要授权 是

### 参数
* mobile 手机号码
* code 短信验证码
* nickname 昵称 选填
* area 所在地区 选填

### 返回值
```json
{
    "code":1,
    "msg":"绑定成功",
    "time":1630133833
}
```

## [bind_mobile](#bind_mobile)
绑定或更改绑定手机号
* 请求地址 域名/api/member/bind_mobile
* 是否需要授权 是

### 参数
* mobile 手机号
* code 手机号验证码
* step 0/1 分两步提交，如果已绑定手机号，需要先发送旧手机号验证码提交通过后再验证新手机号

### 返回值
```json
{
    "code":1,
    "msg":"绑定成功",
    "time":1630133833
}
```

## [smscode](#smscode)
发送手机验证码
* 请求地址 域名/api/member/smscode
* 是否需要授权 是

### 参数
* mobile 手机号码

### 返回值
```json
{
    "code":1,
    "msg":"验证码已发送",
    "time":1630133833
}
```

## [avatar](#avatar)
更新头像
* 请求地址 域名/api/member/avatar
* 是否需要授权 是

### 参数
* upload_avatar 文件二进制

### 返回值
```json
{
    "code":1,
    "msg":"更新成功",
    "time":1630133833
}
```

## [uploadImage](#uploadImage)
上传会员图片
* 请求地址 域名/api/member/uploadImage
* 是否需要授权 是

### 参数
* file_upload 文件二进制

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": {
        "url": ""
    }
}
```

## [upgrade](#upgrade)
申请升级
* 请求地址 域名/api/member/upgrade
* 是否需要授权 是

### 参数
* level_id 要升级的等级id
* balance_pay 是否直接使用余额支付

### 返回值
```json
{
    "code":1,
    "msg":"开通成功",
    "time":1630133833
}
```

## [change_password](#change_password)
修改密码
* 请求地址 域名/api/member/change_password
* 是否需要授权 是

### 参数
* password 当前密码
* newpassword 新密码

### 返回值
```json
{
    "code":1,
    "msg":"密码修改成功",
    "time":1630133833
}
```

## [sec_password](#sec_password)
修改或设置二级密码
* 请求地址 域名/api/member/sec_password
* 是否需要授权 是

### 参数
* password 当前安全密码 首次设置时验证登录密码
* newpassword 新安全密码

### 返回值
```json
{
    "code":1,
    "msg":"安全密码修改成功",
    "time":1630133833
}
```

## [search](#search)
精确搜索会员资料
* 请求地址 域名/api/member/search
* 是否需要授权 是

### 参数
* keyword 会员名或手机号

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": {
        "id": 1,
        "username": "",
        "nickname": "",
        "realname": "",
        "mobile": "",
        "avatar": "",
    }
}
```

## [quit](#quit)
退出登录
* 请求地址 域名/api/member/quit
* 是否需要授权 是

### 参数

### 返回值
```json
{
    "code":1,
    "msg":"退出成功",
    "time":1630133833
}
```