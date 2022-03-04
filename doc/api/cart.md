<center>cart(购物车接口)</center>
=======================================

## [getall](#getall)
获取全部购物车商品列表
* 请求地址 域名/api/cart/getall
* 是否需要授权 是

### 参数
无

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": [
        {
        }
    ]
}
```

## [getcount](#getcount)
获取购物车商品数量总和
* 请求地址 域名/api/cart/getcount
* 是否需要授权 是

### 参数
无

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": {
        "count":99
    }
}
```

## [add](#add)
添加到购物车
* 请求地址 域名/api/cart/add
* 是否需要授权 是

### 参数
* sku_id 商品skuid
* count 购买数量

### 返回值
```json
{
    "code":1,
    "msg":"成功添加到购物车",
    "time":1630133833
}
```

## [update](#update)
更新购物车中指定商品的数量
* 请求地址 域名/api/cart/update
* 是否需要授权 是

### 参数
* sku_id 商品唯一skuid
* count 要更新的数量
* id 指定购物车id时更新购物车数据资料，如商品选项

### 返回值
```json
{
    "code":1,
    "msg":"购物车已更新",
    "time":1630133833
}
```

## [delete](#delete)
删除购物车中的指定商品
* 请求地址 域名/api/cart/delete
* 是否需要授权 是

### 参数
* sku_id

### 返回值
```json
{
    "code":1,
    "msg":"购物车已更新",
    "time":1630133833
}
```

## [clear](#clear)
清空购物车
* 请求地址 域名/api/cart/clear
* 是否需要授权 是

### 参数


### 返回值
```json
{
    "code":1,
    "msg":"购物车已清空",
    "time":1630133833
}
```