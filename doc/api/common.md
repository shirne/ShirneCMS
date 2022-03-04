<center>common 通用接口</center>
====================================

## [batch](#batch)
批量请求接口
* 请求地址: 域名/api/common/batch
* 是否需要授权: 根据子请求确定

### 参数
Json格式的Put参数或键值对参数二选一

#### PUT 参数
```json
{
    "method": {
        ... // 参数列表
    },
    "controller.method": {
        ... // 参数列表
    },
    "mykey": {
        "call": "controller.method",
        ... // 参数列表
    }
}
```

#### GET/POST参数
* methods 以逗号分割的多个接口组合，Common控制器下的接口直接写方法名，其它控制器的接口写 控制器.方法
* ... 子请求所需的参数

### 返回值
Json  (data 代表单独请求接口时接口返回的data字段值)
```json
{
    "code": 1,
    "msg": "",
    "time": 1630133640,
    "data":{
        "method": data,
        "controller.method": data,
        "mykey": data
    }
}
```

### 示例
* GET/POST 请求参数

methods=advs,article.get_cates&flag=banner&pid=1

返回结果
```json
{
    "code": 1,
    "msg": "",
    "time": 1630111504,
    "data": {
        "advs": [
            {
                "id": 1,
                "lang": "",
                "main_id": 0,
                "group_id": 1,
                "title": "test",
                "image": "\/uploads\/banner\/2021\/07\/8d9dbb09a70b3fd9777daaac29da28bb.jpg",
                "video": "",
                "url": "",
                "elements": [],
                "ext_data": null,
                "start_date": 0,
                "end_date": 0,
                "create_time": 0,
                "update_time": 0,
                "sort": 0,
                "status": 1,
                "ext": []
            },
            ...
        ],
        "article.get_cates": [
            {
                "id": 4,
                "pid": 1,
                "title": "PHP",
                "short": "PHP",
                "name": "PHP",
                "icon": null,
                "image": null,
                "sort": 1,
                "props": null,
                "fields": null,
                "list_sort": null,
                "pagesize": 12,
                "use_template": 0,
                "template_dir": "0",
                "channel_mode": 0,
                "is_comment": 0,
                "is_images": 0,
                "is_attachments": 0,
                "keywords": null,
                "description": null,
                "html": "│　├─"
            },
            ...
        ]
    }
}

```
* PUT 请求参数
```json
{ 
    "advs" => { "flag" => "banner"},
    "article.get_cates" => {
        "pid" => 0
    },
    "subcates" => {,
        "call"=>"article.get_cates",
        "pid" => 1
    }
}
```
返回结果
```json
{
    "code": 1,
    "msg": "",
    "time": 1630111504,
    "data": {
        "advs": [
            {
                "id": 1,
                "lang": "",
                "main_id": 0,
                "group_id": 1,
                "title": "test",
                "image": "\/uploads\/banner\/2021\/07\/8d9dbb09a70b3fd9777daaac29da28bb.jpg",
                "video": "",
                "url": "",
                "elements": [],
                "ext_data": null,
                "start_date": 0,
                "end_date": 0,
                "create_time": 0,
                "update_time": 0,
                "sort": 0,
                "status": 1,
                "ext": []
            },
            ...
        ],
        "article.get_cates": [
            {
                "id": 1,
                "pid": 0,
                "title": "日志",
                "short": "日志",
                "name": "blog",
                "icon": null,
                "image": null,
                "sort": 1,
                "props": null,
                "fields": null,
                "list_sort": null,
                "pagesize": 12,
                "use_template": 0,
                "template_dir": "0",
                "channel_mode": 0,
                "is_comment": 0,
                "is_images": 0,
                "is_attachments": 0,
                "keywords": null,
                "description": null,
                "html": "├─"
            },
            ...
        ],
        "subcates": [
            {
                "id": 4,
                "pid": 1,
                "title": "PHP",
                "short": "PHP",
                "name": "PHP",
                "icon": null,
                "image": null,
                "sort": 1,
                "props": null,
                "fields": null,
                "list_sort": null,
                "pagesize": 12,
                "use_template": 0,
                "template_dir": "0",
                "channel_mode": 0,
                "is_comment": 0,
                "is_images": 0,
                "is_attachments": 0,
                "keywords": null,
                "description": null,
                "html": "│　├─"
            },
            ...
        ]
    }
}
```

## [search](#search)
全站搜索
* 请求地址: 域名/api/common/search
* 是否需要授权: 可选

### 参数 
* keyword  搜索关键字
* model 搜索模块 默认搜索article 可选 product/goods
* page 页码

### 返回值
```json
{
    "code": 1,
    "msg": "",
    "time": 1630133640,
    "data": {
        "lists":[], // 列表数据
        "page": 1, // 当前页码
        "total": 100, // 总数量
        "total_page": 10, // 总页数
    }
}
```

## [booth](#booth)
展位
* 请求地址 域名/api/common/booth
* 是否需要授权 否

### 参数
* flags  展位标识,可以是一个或多个，多个用 **,** 分割

### 返回值
```json
{
    "code": 1,
    "msg": "",
    "time": 1630133640,
    "data": {
        "lists":[], // 列表数据，具体内容根据展位设置而定
        "page": 1, // 当前页码
        "total": 100, // 总数量
        "total_page": 10, // 总页数
    }
}
```

## [advs](#advs)
广告图
* 请求地址 域名/api/common/advs
* 是否需要授权 否

### 参数
* flag 广告位标识

### 返回值
```json
{
    "code": 1,
    "msg": "",
    "time": 1630133640,
    "data": [
        {
            "id": 1,
            "lang": "",
            "main_id": 0,
            "group_id": 1,
            "title": "test",
            "image": "\/uploads\/banner\/2021\/07\/8d9dbb09a70b3fd9777daaac29da28bb.jpg",
            "video": "",
            "url": "",
            "elements": [],
            "ext_data": null,
            "start_date": 0,
            "end_date": 0,
            "create_time": 0,
            "update_time": 0,
            "sort": 0,
            "status": 1,
            "ext": []
        }
    ]
}
```

## [notice](#notice)
公告
* 请求地址 域名/api/common/notice
* 是否需要授权 否

### 参数
* flag 公告的调用标志，**推荐使用此参数调用**
* id 公告id 指定`id`后`flag`参数无效

### 返回值
```json
{
    "code": 1,
    "msg": "",
    "time": 1630133640,
    "data": {
        "id":1,
        "title": "公告标题",
        "page": "",
        "url": "",
        "status": "",
        "manager_id": 1,
        "summary": "公告摘要",
        "content": "公告内容", 
        "create_time":1475412326,
        "update_time":1475412326
    }
}
```

## [notices](#notices)
公告列表
* 请求地址 域名/api/common/notices
* 是否需要授权 否

### 参数
* flag 公告调用标志，可空
* count 调用条数，默认 10

### 返回值
```json
{
    "code": 1,
    "msg": "",
    "time": 1630133640,
    "data": [
        {
            "id":1,
            "title": "公告标题",
            "page": "",
            "url": "",
            "status": "",
            "manager_id": 1,
            "summary": "公告摘要",
            "create_time":1475412326,
            "update_time":1475412326
        },
        ...
    ]
}
```

## [links](#links)
友链
* 请求地址 域名/api/common/links
* 是否需要授权 否

### 参数
* group 链接分组
* islogo 是否调用有logo的
* count 调用数量

### 返回值
```json
{
    "code": 1,
    "msg": "",
    "time": 1630133640,
    "data": [
        {
            "id":1,
            "title": "链接名称",
            "group": "",
            "logo": "",
            "status": "",
            "url": "链接地址",
            "sort": 1,
            "create_time":1475412326,
            "update_time":1475412326
        },
        ...
    ]
}
```

## [do_feedback](#do_feedback)
留言提交
* 请求地址 域名/api/common/do_feedback
* 是否需要授权 可选 如果系统设置不允许匿名留言，则必须授权

### 参数
* content 留言内容
* realname
* mobile
* email
* type

### 返回值
```json
{
    "code": 1,
    "msg": "提交成功",
    "time": 1630133640
}
```

## [feedbacks](#feedbacks)
留言列表
* 请求地址 域名/api/common/feedbacks
* 是否需要授权 可选

### 参数
* pagesize 每页数量 默认10
* page 页码

### 返回值
```json
{
    "code": 1,
    "msg": "",
    "time": 1630133640,
    "data": {
        "lists": [], // 数据列表
        "total": 100, // 总条数
        "page": 1,   // 当前页码
        "total_page": 10
    }
}
```

## [siteinfo](#siteinfo)
网站配置(通用配置的部分)
留言列表
* 请求地址 域名/api/common/siteinfo
* 是否需要授权 可选

### 参数
无

### 返回值
```json
{
    "code": 1,
    "msg": "",
    "time": 1630133640,
    "data": {
        "webname": "ShirneCMS",
        "keywords": "关键词1,关键词2",
        "description": "站点描述信息",
        "weblogo": "",
        "close": "0",
        "close-desc": "系统维护中",
        "shareimg": "",
        "tongji": "",
        "icp": "",
        "gongan-icp": "",
        "url": "https:\/\/www.shirne.com",
        "name": "ShirneCMS",
        "400": "",
        "email": "",
        "telephone": "",
        "address": "",
        "location": ""
    }
}
```

## [config](#config)
获取指定分组(除third外)的配置
* 请求地址 域名/api/common/config
* 是否需要授权 可选

### 参数
* group 可以为一个或用 **,** 分割的多个分组
        wechat 可获取默认微信公众号的配置信息

### 返回值
单个分组的请求
```json
// 请求参数 group=common
{
    "code": 1,
    "msg": "",
    "time": 1630133640,
    "data": {
        "webname": "ShirneCMS",
        "keywords": "关键词1,关键词2",
        "description": "站点描述信息",
        "weblogo": "",
        "close": "0",
        "close-desc": "系统维护中",
        "shareimg": "",
        "tongji": "",
        "icp": "",
        "gongan-icp": "",
        "url": "https:\/\/www.shirne.com",
        "name": "ShirneCMS",
        "400": "",
        "email": "",
        "telephone": "",
        "address": "",
        "location": ""
    }
}
```
多个分组的请求
```json
// 请求参数 group=common,member
{
    "code": 1,
    "msg": "",
    "data": {
        "common":{
            "webname": "ShirneCMS",
            "keywords": "关键词1,关键词2",
            "description": "站点描述信息",
            "weblogo": "",
            "close": "0",
            "close-desc": "系统维护中",
            "shareimg": "",
            "tongji": "",
            "icp": "",
            "gongan-icp": "",
            "url": "https:\/\/www.shirne.com",
            "name": "ShirneCMS",
            "400": "",
            "email": "",
            "telephone": "",
            "address": "",
            "location": ""
        },
        "member":{
            "m_open": "1",
            "m_register_open": "1",
            "m_register": "0",
            "m_invite": "1",
            "m_checkcode": "1",
            "anonymous_comment": "1",
            "autoaudit": "1",
            "commission_type": "0",
            "agent_start": "0",
            "commission_delay": "0",
            "commission_delay_days": "0",
            "cash_types": [],
            "cash_fee": "10",
            "cash_fee_min": "1",
            "cash_fee_max": "50",
            "cash_limit": "10",
            "cash_max": "100000",
            "cash_power": "100",
            "share_product": ""
        }
    }
}
```

## [signrank](#signrank)
签到排行
* 请求地址 域名/api/common/signrank
* 是否需要授权 可选

### 参数
* date 日期 格式 2021-08-28  默认获取当天

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data":[
        ... //签到列表
    ]
}
```

## [data](#data)
公用数据
* 请求地址 域名/api/common/data
* 是否需要授权 可选

### 参数
* keys 可选值
  * banklist 获取预设的银行名列表
  * log_types 获取日志类型
  * money_fields 获取用户积分字段类型
  * levels 获取会员组列表
  * agents 获取代理级别列表

### 返回值
```json
{
    "code": 1,
    "msg": "",
    "time": 1630134045,
    "data": {
        "levels": {
            "1": {
                "level_id": 1,
                "level_name": "普通会员",
                "short_name": "普",
                "style": "secondary",
                "is_default": 1,
                "upgrade_type": 0,
                "diy_price": 0,
                "level_price": "0.00",
                "discount": 100,
                "is_agent": 0,
                "sort": 0,
                "commission_layer": 3,
                "commission_limit": 0,
                "commission_percent": [
                    "0",
                    "0",
                    "0"
                ]
            }
        },
        "agents": []
    }
}
```