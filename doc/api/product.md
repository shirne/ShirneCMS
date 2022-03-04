<center>product(商品接口)</center>
=======================================

## [get_all_cates](#get_all_cates)
获取全部分类树
* 请求地址 域名/api/product/get_all_cates
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
                "props": [],
                "specs": [],
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
* 请求地址 域名/api/product/get_cates
* 是否需要授权 否

### 参数
* pid 指定的上级分类id
* goods_count 每个分类携带的内容条数，默认为0 不携带
* withsku 是否携带sku信息，默认不携带
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
            "products": [
                ... // 携带出的内容列表
            ]
        },
    ]
}
```

## [get_list](#get_list)
获取文章列表(可分页)
* 请求地址 域名/api/product/get_list
* 是否需要授权 否

### 参数
* cate 指定的分类，可指定分类id或目录名
* type 商品类型 默认不指定
* order 排序 默认按更新时间和id倒序
* keyword 关键字
* withsku 是否携带sku列表
* page 页码
* pagesize 每页条数 默认10

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": {
        "lists": [],
        "page": 1,
        "total": 100,
        "total_page": 10
    }
}
```

## [brands](#brands)
获取品牌列表
* 请求地址 域名/api/product/brands
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
        "lists": [
            ... //品牌列表
        ]
    }
}
```

## [view](#view)
获取商品详情
* 请求地址 域名/api/product/view
* 是否需要授权 可选

### 参数
* id 商品id

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": {
        "product": {
            "id": 1,
            "name": "",
            "title": "商品名称",
            "vice_title": "",
            "unit": "个",
            "cate_id": 4,
            "image": "\/uploads\/product\/2021\/07\/157d1bef26900a3d285bae6f421b8b70.jpg",
            "description": "PHP开源介绍",
            "prop_data": [],
            "spec_data": [],
            "content": "<p>商品详情<\/p>",
            "create_time": 1625344889,
            "update_time": 1629084651,
            "type": 1,
            "template": "",
            "sale": 0, // 销量
            "status": 1
        }, // 商品详情
        "postage": "商品邮费信息",
        "images": [],
        "skus": [],
        "is_favourite": 0 // 是否收藏，登录状态显示
    }
}
```

## [flash](#flash)
获取商品快照
* 请求地址 域名/api/product/flash
* 是否需要授权 可选

### 参数
* id 商品id
* time 快照时间戳，一般是订单下单的时间戳

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": {
        "product": {
            "id": 1,
            "name": "",
            "title": "商品名称",
            "vice_title": "",
            "unit": "个",
            "cate_id": 4,
            "image": "\/uploads\/product\/2021\/07\/157d1bef26900a3d285bae6f421b8b70.jpg",
            "description": "PHP开源介绍",
            "prop_data": [],
            "spec_data": [],
            "content": "<p>商品详情<\/p>",
            "create_time": 1625344889,
            "update_time": 1629084651,
            "type": 1,
            "template": "",
            "sale": 0, // 销量
            "status": 1
        }, // 商品详情
        "images": [],
        "skus": [],
        "flash_date": 0 // 时间
    }
}
```

## [share](#share)
获取商品分享海报，支持web，公众号，小程序
* 请求地址 域名/api/product/share
* 是否需要授权 可选 登录状态下获取带推荐码的海报

### 参数
* id 商品id
* type 二维码形式 url/miniqr

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": {
        "share_url": "" // 图片地址
    }
}
```

## [comments](#comments)
评论列表
* 请求地址 域名/api/product/comments
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
                "member_id": 0,
                "product_id": 203,
                "sku_id": 299,
                "order_id": 19,
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