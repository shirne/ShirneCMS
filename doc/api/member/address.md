<center>member.address(会员地址管理接口)</center>
=======================================

## [index](#index)
获取会员的地址列表
* 请求地址 域名/api/member.address/index
* 是否需要授权 是

### 参数
* page 页码

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": {
        "lists": [],
        "page": 1,
        "count": 100,
        "total_page": 10
    }
}
```

## [view](#view)
会员地址详细
* 请求地址 域名/api/member.address/view
* 是否需要授权 是

### 参数
* id 地址id

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": {
        "address_id": 1,
        "member_id": 1,
        "receive_name": "姓名",
        "mobile": "",
        "province": "",
        "city": "",
        "area": "",
        "street": "",
        "address": "详细地址", 
        "code":  "", // 邮编
        "locate": "", // 位置坐标 lat,lng
        "is_default": 1
    }
}
```

## [save](#save)
会员地址保存
* 请求地址 域名/api/member.address/save
* 是否需要授权 是

### 参数
* id 地址id
* address 地址资料json

### 返回值
```json
{
    "code":1,
    "msg":"修改成功",
    "time":1630133833
}
```

## [delete](#delete)
删除地址
* 请求地址 域名/api/member.address/delete
* 是否需要授权 是

### 参数
* id 地址id

### 返回值
```json
{
    "code":1,
    "msg":"删除成功",
    "time":1630133833
}
```

## [set_default](#set_default)
设置默认地址
* 请求地址 域名/api/member.address/set_default
* 是否需要授权 是

### 参数
* id 地址id

### 返回值
```json
{
    "code":1,
    "msg":"设置成功",
    "time":1630133833
}
```