<?php
/**
 * 导航配置
 * User: shirne
 * Date: 2018/5/5
 * Time: 16:04
 */
return [
    [
        'title'=>'首页',
        'url'=>'Index/index'
    ],
    [
        'title'=>'服务范围',
        'url'=>['Page/index',['group'=>'services']],
        'subnav'=>'Page/services'
    ],
    [
        'title'=>'解决方案',
        'url'=>['Page/index',['group'=>'solutions']],
        'subnav'=>'Page/solutions'
    ],
    [
        'title'=>'案例中心',
        'url'=>['Article/index',['name'=>'cases']],
        'subnav'=>'Article/cases'
    ],
    [
        'title'=>'关于原设',
        'url'=>['Page/index',['group'=>'about']],
        'subnav'=>'Page/about'
    ],
    [
        'title'=>'新闻动态',
        'url'=>['Article/index',['name'=>'news']],
        'subnav'=>'Article/news'
    ],
    [
        'title'=>'云计算',
        'url'=>'http://cloud.shirne.cn'
    ]
];