## batch
批量接口
```
两种请求方式
1. methods = a,b,c.d ; arg1 = a ;  arg2 = b ; ...
    该方式不能重复调同一个接口，各接口间参数不能有冲突
    返回数据以methods作为key索引
2. { method1 => { arg1 => a, arg2 => b}, method2 => { call => controller.method, arg1 => a, arg2 => b} }
    该方式可以重复调用同一个接口，使用不同的key并在参数中增加一个call来指定调用的方法，各调用的参数互相隔离
```

## search
全站搜索
参数 keyword, model

## booth
展位
参数: flags
格式: 展位标识,可以是一个或多个


## advs
广告图
参数: flag

## notice
公告
以下参数二选一
参数: flag
参数: id

## notices
公告列表
参数: flag
参数: count

## links
友链
参数: group
参数: islogo
参数: count

## feedback
留言提交 (未完成)

## feedbacks
留言列表

## siteinfo
网站配置(通用配置的部分)

## config
获取指定分组的配置(不可获取third分组)
参数: group 可以为一个或用,分割的多个分组

## signrank
签到排行

## data
公用数据