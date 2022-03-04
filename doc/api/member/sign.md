<center>member.sign(会员签到接口)</center>
=======================================

## [dosign](#dosign)
提交签到
* 请求地址 域名/api/member.sign/dosign
* 是否需要授权 是

### 参数
* mood 今天心情 可空

### 返回值
```json
{
    "code":1,
    "msg":"签到成功",
    "time":1630133833
}
```

## [dosupsign](#dosupsign)
补签
* 请求地址 域名/api/member.sign/dosupsign
* 是否需要授权 是

### 参数
* date 补签日期
* mood 心情 可空

### 返回值
```json
{
    "code":1,
    "msg":"补签成功",
    "time":1630133833
}
```

## [getlastsign](#getlastsign)
获取最后一次签到
* 请求地址 域名/api/member.sign/getlastsign
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
        "id":1,
        "member_id":1,
        "is_sup": 0,
        "ranking_day": 1, // 第几个签到的
        "keep_days": 1, // 连续签到天数
        "signdate": "2021-08-22",
        "signtime": 1630133833,
        "mood": "",
        "remark": "首次签到积分10"
    }
}
```

## [getsigns](#getsigns)
已签到的日期列表
* 请求地址 域名/api/member.sign/getsigns
* 是否需要授权 是

### 参数
* from_date 开始日期 可空 留空时获取当月签到
* to_date 结束日期 可空 起始日期为空时此参数无效

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": [
        "2021-09-01",
        "2021-09-02",
    ]
}
```

## [totaldays](#totaldays)
获取从开始日期之后的签到次数
* 请求地址 域名/api/member.sign/totaldays
* 是否需要授权 是

### 参数
* from_date 开始日期 留空时获取总次数

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": {
        "count": 10
    }
}
```

## [totalcredit](#totalcredit)
获取从开始日期之后的签到获得的总积分
* 请求地址 域名/api/member.sign/totalcredit
* 是否需要授权 是

### 参数
* from_date 开始日期 留空时获取总积分

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": {
        "total": 100
    }
}
```