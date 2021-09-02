<center>page(页面接口)</center>
=======================================

## [groups](#groups)
获取页面分组
* 请求地址 域名/api/page/groups
* 是否需要授权 否

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
            "id": 1,
            "group_name": "about",
            "group": "关于",
            "sort": 99,
            "use_template": 0
        },
        {
            "id": 2,
            "group_name": "联系",
            "group": "contact",
            "sort": 99,
            "use_template": 0
        }
    ]
}
```

## [pages](#pages)
获取页面分组
* 请求地址 域名/api/page/pages
* 是否需要授权 否

### 参数
* group 指定分组，可留空

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": [
        {
            "id": 1,
            "lang": null,
            "main_id": null,
            "title": "公司简介",
            "vice_title": "简介",
            "group": "关于",
            "icon": "",
            "image": "",
            "name": "profile",
            "sort": 0,
            "status": 1,
            "use_template": 0,
            "content": "<p>企业简介<\/p>"
        },
        ...
    ]
}
```

## [page](#page)
获取页面分组
* 请求地址 域名/api/page/page
* 是否需要授权 否

### 参数
* name 指定页面标识或id

### 返回值
```json
{
    "code":1,
    "msg":"",
    "time":1630133833,
    "data": {
        "page":{
            "id": 1,
            "lang": null,
            "main_id": null,
            "title": "公司简介",
            "vice_title": "简介",
            "group": "关于",
            "icon": "",
            "image": "",
            "name": "profile",
            "sort": 0,
            "status": 1,
            "use_template": 0,
            "content": "<p>企业简介<\/p>"
        },
        "images":[ ]
    }
}
```