<center>member.favourite(会员收藏管理接口)</center>
=======================================

## [index](#index)
获取收藏列表
* 请求地址 域名/api/member.favourite/index
* 是否需要授权 是

### 参数
* type 收藏类型 article/product

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": [
        {

        },
        ...
    ]
}
```

## [add](#add)
添加到收藏
* 请求地址 域名/api/member.favourite/add
* 是否需要授权 是

### 参数
* type 收藏类型 article/product
* id 关联id

### 返回值
```json
{
    "code":1,
    "msg":"处理成功",
    "time":1630133833
}
```

## [remove](#remove)
添加到收藏
* 请求地址 域名/api/member.favourite/remove
* 是否需要授权 是

### 参数
* type 收藏类型 article/product
* ids 关联id列表

### 返回值
```json
{
    "code":1,
    "msg":"已移除收藏",
    "time":1630133833
}
```