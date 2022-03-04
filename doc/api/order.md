<center>order(下单接口)</center>
=======================================

## [prepare](#prepare)
预下单接口，根据商品及地址获取邮费优惠等信息
* 请求地址 域名/api/order/prepare
* 是否需要授权 是

### 参数
* from 下单来源 quick/cart  如果从购物车下单，商品将从购物车获取，下单完成时将移除购物车中对应的商品
* products 下单商品列表 格式 {sku_id: count}
* address 当前使用的地址id

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": {
        "products":[], // 商品信息列表
        "address": {}, // 如果未指定地址，则尝试使用用户的默认地址
        "express": {} // 邮费策略
    }
}
```

## [confirm](#confirm)
下单接口
* 请求地址 域名/api/order/confirm
* 是否需要授权 是

### 参数
* from 下单来源 quick/cart  如果从购物车下单，商品将从购物车获取，下单完成时将移除购物车中对应的商品
* products 下单商品列表 格式 {sku_id: count}
* address_id 当前使用的地址id
* pay_type 用户选择的支付方式，如果是余额支付将直接扣除
* remark 订单备注
* form_id 表单id 在小程序环境获取到的
* total_price 客户端计算得的总价格，用于对比
* total_postage 总邮费

### 返回值
```json
{
    "code":1,
    "msg":"下单成功，请尽快支付",
    "time":1630133833
}
```

## [wechatpay](#wechatpay)
提交微信支付，获取支付参数
* 请求地址 域名/api/order/wechatpay
* 是否需要授权 是

### 参数
* order_id 订单id
* trade_type 交易类型，参考微信支付接口的参数说明
* payid 支付配置id，如果只有一个默认的配置，可忽略此参数，可以用于区分多个公众号和小程序

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": {
        "code_url": "", // NATIVE支付返回的二维码url
        "mweb_url": "", // h5支付返回的跳转地址
        "payment": {} // jsapi支付返回的签名数据
    }
}
```

## [balancepay](#balancepay)
提交微信支付，获取支付参数
* 请求地址 域名/api/order/balancepay
* 是否需要授权 是

### 参数
* order_id 下单来源 quick/cart  如果从购物车下单，商品将从购物车获取，下单完成时将移除购物车中对应的商品
* type 
* secpassword 安全密码

### 返回值
```json
{
    "code":1,
    "msg":"支付成功",
    "time":1630133833,
    "data": {
        "order_id": 1
    }
}
```