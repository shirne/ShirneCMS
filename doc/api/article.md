<center>article(内容接口)</center>
=======================================

## [get_all_cates](#get_all_cates)
获取全部分类树
* 请求地址 域名/api/article/get_all_cates
* 是否需要授权 否

### 参数
无

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": {
        "0": [
            {
                "id": 1,
                "pid": 0,
                "title": "分类名",
                "short": "分类简称",
                "name": "blog",
                "icon": null,
                "image": null,
                "sort": 1,
                "props": null,
                "fields": null,
                "list_sort": null,
                "pagesize": 12,
                "keywords": null,
                "description": null,
                "html": "├─"
            },
            ... // 顶级分类
        ],
        "1": [
            ... // id为1的子分类列表
        ],
        "2": [
            ... // id为2的子分类列表
        ],
        ...
    }
}
```

## [get_cates](#get_cates)
获取指定上级的子分类(不指定则获取顶级类目)
并可指定携带数条文章
* 请求地址 域名/api/article/get_cates
* 是否需要授权 否

### 参数
* pid 指定的上级分类id
* list_count 每个分类携带的内容条数，默认为0 不携带
* filters 内容的筛选条件，具体参考get_list

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": [
        {
            "id": 1,
            "pid": 0,
            "title": "分类名",
            "short": "分类简称",
            "name": "blog",
            "icon": null,
            "image": null,
            "sort": 1,
            "props": null,
            "fields": null,
            "list_sort": null,
            "pagesize": 12,
            "keywords": null,
            "description": null,
            "html": "├─",
            "articles": [
                ... // 携带出的内容列表
            ]
        },
    ]
}
```

## [get_list](#get_list)
获取文章列表(可分页)
* 请求地址 域名/api/article/get_list
* 是否需要授权 否

### 参数
* cate 指定的分类，可指定分类id或目录名
* order 排序 默认按更新时间和id倒序
* keyword 关键字
* page 页码
* type 文章类型 默认不指定
* pagesize 每页条数 默认10

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": {
        "lists": [],
        "category": {},
        "page": 1,
        "total": 100,
        "total_page": 10
    }
}
```

## [view](#view)
获取文章详情
* 请求地址 域名/api/article/view
* 是否需要授权 可选

### 参数
* id 文章id

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": {
        "article": {
            "id": 1,
            "name": "",
            "title": "PHP开源介绍",
            "vice_title": "",
            "channel_id": 0,
            "cate_id": 4,
            "cover": "\/uploads\/article\/2021\/07\/157d1bef26900a3d285bae6f421b8b70.jpg",
            "keywords": "",
            "description": "PHP开源介绍",
            "source": "",
            "prop_data": [],
            "content": "<p>PHP开源介绍PHP开源介绍PHP开源介绍PHP开源介绍<\/p>",
            "create_time": 1625344889,
            "update_time": 1629084651,
            "user_id": 1,
            "copyright_id": 1,
            "digg": 1,
            "v_digg": 0,
            "close_comment": 0,
            "comment": 0,
            "views": 27,
            "v_views": 0,
            "type": 1,
            "template": "",
            "is_hidden": 0,
            "status": 1
        }, // 文章内容
        "url": "文章唯一链接",
        "images": [],
        "digged": 0, // 是否点赞过 ，登录状态显示
        "is_favourite": 0 // 是否收藏，登录状态显示
    }
}
```

## [digg](#digg)
点赞
* 请求地址 域名/api/article/digg
* 是否需要授权 是

### 参数
* id 文章id
* type 类型，默认 up 为点赞，其它参数取消点赞

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data":{
        "digg": 9 // 当前点赞数
    }
}
```

## [comments](#comments)
评论列表
* 请求地址 域名/api/article/comments
* 是否需要授权 可选

### 参数
* id 文章id
* pagesize 每页条数
* page 页码

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": {
        "lists": [
            {
                "id": 174,
                "channel_id": 0,
                "member_id": 0,
                "article_id": 203,
                "nickname": "昵称",
                "email": "xxx@qq.com",
                "create_time": 1425110426,
                "device": "",
                "ip": "xxx",
                "status": 1,
                "is_anonymous": 0,
                "content": "好久没来 帮着顶顶",
                "reply_id": 0,
                "group_id": 0,
                "username": null,
                "realname": null,
                "avatar": null
            },
            ...
        ],
        "page": 1,
        "total": 100,
        "total_page": 10
    }
}
```

## [do_comment](#do_comment)
提交评论
* 请求地址 域名/api/article/do_comment
* 是否需要授权 是

### 参数
* id 文章id
* reply_id 回复的评论id
* email 邮箱
* is_anonymous 是否匿名
* content 评论内容

### 返回值
```json
{
    "code":1,
    "msg":"评论成功",
    "time":1630133833,
}
```