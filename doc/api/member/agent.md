<center>member.agent(会员代理商接口)</center>
=======================================

## [generic](#generic)
代理统计
* 请求地址 域名/api/member.agent/generic
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
        "order_count": 10,
        "amount_future": 100.0,
        "total_award": 150.0
    }
}
```

## [upgrade](#upgrade)
代理升级
* 请求地址 域名/api/member.agent/upgrade
* 是否需要授权 是

### 参数
* level_id 升级的级别
* realname 真实姓名
* mobile 手机号码
* province 所在省份
* city 所在城市

### 返回值
```json
{
    "code":1,
    "msg":"申请已提交",
    "time":1630133833
}
```

## [poster](#poster)
分享海报
* 请求地址 域名/api/member.agent/poster
* 是否需要授权 是

### 参数
* page 页面链接

### 返回值
```json
{
    "code":1,
    "msg":"申请已提交",
    "time":1630133833,
    "data": {
        "poster_url":"", // 海报图片地址
        "qr_url": "" // 专用二维码图片地址
    }
}
```

## [rank](#rank)
排行榜
* 请求地址 域名/api/member.agent/rank
* 是否需要授权 是

### 参数
* page 页面链接

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": {
        "rank":[]
    }
}
```

## [award_log](#award_log)
佣金明细
* 请求地址 域名/api/member.agent/award_log
* 是否需要授权 是

### 参数
* type 类型
* status 状态
* daterange 日期范围
* page 页码

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": {
        "types":[],
        "static_data": {},
        "logs": [], // 明细列表
        "total": 200,
        "total_page": 20,
        "page": 1
    }
}
```

## [orders](#orders)
订单明细
* 请求地址 域名/api/member.agent/orders
* 是否需要授权 是

### 参数
* status 状态
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
        "total": 200,
        "total_page": 20,
        "page": 1
    }
}
```

## [counts](#counts)
各状态订单数
* 请求地址 域名/api/member.agent/counts
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
        
    }
}
```

## [team](#team)
团队明细
* 请求地址 域名/api/member.agent/team
* 是否需要授权 是

### 参数
* pid 推荐人id
* level 层数
* page 页码

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": {
        "users": [],
        "total": 100,
        "total_page": 10,
        "page": 1,
    }
}
```