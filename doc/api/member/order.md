<center>member.order(会员订单管理接口)</center>
=======================================

## [index](#index)
获取会员订单列表
* 请求地址 域名/api/member.order/index
* 是否需要授权 是

### 参数
* status 订单状态
* pagesize 每页条数
* page 页码

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": {
        "lists": [], // 订单列表
        "page": 1,
        "count": 100,
        "total_page": 10,
        "counts": {} //各状态订单数量，用于角标
    }
}
```


## [counts](#counts)
获取不同状态的订单数量
* 请求地址 域名/api/member.order/counts
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
        "0": 10, // 待付款
        "1": 1, // 待发货
        "2": 0  // 待收货
    }
}
```

## [view](#view)
获取不同状态的订单数量
* 请求地址 域名/api/member.order/view
* 是否需要授权 是

### 参数
* id 订单id

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": {
        "order_id": 10, // 待付款
        "platform": 1, // 待发货
        "appid": "",
        "form_id": "",
        "order_no": "202109023358001",
        "member_id": 1,
        "payamount": 10.0, // 应付款
        "payedamount": 10.0, // 已付款
        "product_amount": 10.0, // 商品总金额
        "cost_amount": 4.0, // 商品总成本
        "discount_amount": 0, // 优惠金额
        "commission_amount": 10.0, // 佣金基数
        "commission_special": "", // 特殊佣金参数
        "level_id": 1, // 下单后会员升级
        "pay_type": "wechat", // 支付类型
        "create_time":1630133833, // 下单时间
        "pay_time": 1630133833, // 支付时间
        "deliver_time":1630133833, // 发货时间
        "confirm_time": 0, // 确认时间
        "comment_time": 0, // 评论时间
        "reason":"", // 退款原因
        "cancel_time":0, // 取消时间
        "refund_time":0, // 退款时间
        "status": 2, // 订单状态
        "remark":"", // 订单备注
        "address_id":1, // 下单地址
        ... // 地址信息 防止下单后原地址资料修改或删除
        "type":0, //订单类型
        "product_count": 1, // 总数量
        "products": [

        ]
    }
}
```

## [cancel](#cancel)
取消订单 未支付的订单可直接取消
* 请求地址 域名/api/member.order/cancel
* 是否需要授权 是

### 参数
* id 订单id
* reason 取消原因

### 返回值
```json
{
    "code":1,
    "msg":"订单已取消",
    "time":1630133833
}
```

## [refund](#refund)
申请退款 已支付的订单需申请退款
* 请求地址 域名/api/member.order/refund
* 是否需要授权 是

### 参数
* id 订单id
* type 退款类型
* amount 退款金额
* reason 退款原因
* image 相关截图

### 返回值
```json
{
    "code":1,
    "msg":"订单已申请退款",
    "time":1630133833
}
```

## [express](#express)
快递进度
* 请求地址 域名/api/member.order/express
* 是否需要授权 是

### 参数
* id 订单id

### 返回值
```json
{
    "code":1,
    "msg":"订单已申请退款",
    "time":1630133833,
    "data":{
        "express": {
            "traces": [],
            "express":"",
            "express_code": "",
            "express_no":""
        },
        "product": {}
    }
}
```

## [confirm](#confirm)
确认收货
* 请求地址 域名/api/member.order/confirm
* 是否需要授权 是

### 参数
* id 订单id

### 返回值
```json
{
    "code":1,
    "msg":"确认成功",
    "time":1630133833
}
```

## [delete](#delete)
删除失效订单
* 请求地址 域名/api/member.order/delete
* 是否需要授权 是

### 参数
* id 订单id

### 返回值
```json
{
    "code":1,
    "msg":"订单已删除",
    "time":1630133833
}
```

## [comment](#comment)
订单评价
* 请求地址 域名/api/member.order/comment
* 是否需要授权 是

### 参数
* id 订单id

### 返回值
```json
{
    "code":1,
    "msg":"评价提交成功",
    "time":1630133833
}
```