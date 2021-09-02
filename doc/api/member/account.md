<center>member.account(会员账户接口)</center>
=======================================

## [cards](#cards)
获取银行卡列表
* 请求地址 域名/api/member.account/cards
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
        "cards": [], // 会员的银行卡列表
        "banklist": [] //预设的银行列表
    }
}
```

## [card_view](#card_view)
获取银行卡详细资料
* 请求地址 域名/api/member.account/card_view
* 是否需要授权 是

### 参数
* id 银行卡id

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": {
        "card": {
            "id": 1,
            "member_id": 1,
            "cardno": xxxx,
            "bankname": "广东支行",
            "cardname": "真实姓名",
            "bank": "中国工商银行",
            "is_default":1
        },
        "banklist": [] //预设的银行列表
    }
}
```

## [card_save](#card_save)
添加/保存银行卡资料
* 请求地址 域名/api/member.account/card_save
* 是否需要授权 是

### 参数
* card 银行卡json数据
* id 为0时新增银行卡，不为0时按id更新

### 返回值
```json
{
    "code":1,
    "msg":"保存成功",
    "time":1630133833
}
```

## [recharge_types](#recharge_types)
获取充值设置列表
* 请求地址 域名/api/member.account/recharge_types
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
        "types": [
            {
                "id": 1,
                "title": "",
                "type": "",
                "qrcode": "",
                "status": 1
            }
        ]
    }
}
```

## [recharge](#recharge)
提交充值
* 请求地址 域名/api/member.account/recharge
* 是否需要授权 是

### 参数
* amount 充值金额
* type_id 充值设置的id

### 返回值
```json
{
    "code":1,
    "msg":"充值申请已提交",
    "time":1630133833,
    "data": {
        "order_id": "" //生成的充值订单id，用于在线支付
    }
}
```

## [recharge_list](#recharge_list)
获取充值记录列表
* 请求地址 域名/api/member.account/recharge_list
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
        "recharges": [],
        "total": 100,
        "page": 1,
        "total_page": 10
    }
}
```

## [recharge_cancel](#recharge_cancel)
未支付的充值订单取消
* 请求地址 域名/api/member.account/recharge_cancel
* 是否需要授权 是

### 参数
* order_id 充值id

### 返回值
```json
{
    "code":1,
    "msg":"取消成功",
    "time":1630133833
}
```

## [cash_config](#cash_config)
获取提现配置
* 请求地址 域名/api/member.account/cash_config
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
        "types": [], // 提现方式列表
        "limit": 100,
        "max": 10000,
        "power": 100,
        "fee": 1,
        "fee_min": 1,
        "fee_max": 10,
        "wechats": [
            ... // 会员已关联的公众号列表，用于选择提现渠道
        ]
    }
}
```

## [cash_list](#cash_list)
获取提现记录
* 请求地址 域名/api/member.account/cash_list
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
        "cashes": [],
        "total": 100,
        "page": 1,
        "total_page": 10
    }
}
```

## [cash](#cash)
获取提现记录
* 请求地址 域名/api/member.account/cash
* 是否需要授权 是

### 参数
* amount 金额
* card_id 提现银行卡
* remark 备注
* cashtype 提现类型
* form_id 表单id 用于小程序

### 返回值
```json
{
    "code":1,
    "msg":"提现申请已提交",
    "time":1630133833
}
```

## [money_log](#cash_list)
获取提现记录
* 请求地址 域名/api/member.account/money_log
* 是否需要授权 是

### 参数
* type
* field
* page 页码

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": {
        "logs": [],
        "total": 100,
        "page": 1,
        "total_page": 10
    }
}
```

## [transfer](#transfer)
会员之间转积分
* 请求地址 域名/api/member.account/transfer
* 是否需要授权 是

### 参数
* amount 额度
* action 转移类型 transout/transmoney
* member_id 目标会员id
* field 积分类型
* secpassword 安全密码

### 返回值
```json
{
    "code":1,
    "msg":"转出成功",
    "time":1630133833
}
```