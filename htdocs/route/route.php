<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::pattern([
    'name' => '[a-zA-Z]\w*',
    'id'   => '\d+',
    'group'=> '[a-zA-Z]\w*',
    'action'=> '[a-zA-Z]\w*',
    'type'=>'\w+',
    'agent'=>'\w{6,}'
]);

Route::get('index$', 'index/index/index');

Route::group('article',[
    ':id'=>'index/article/view',
    '[:name]'=>'index/article/index',
    'comment/:id'=>'index/article/comment'
])->method('GET');

Route::group('product',[
    ':id'=>'index/product/view',
    '[:name]'=>'index/product/index',
    'comment/:id'=>'index/product/comment'
])->method('GET');

Route::group('order',[
    'confirm'=>'index/order/confirm',
    'wechatpay'=>'index/order/wechatpay'
])->method('GET|POST');

Route::get('page/:group/[:name]','index/page/index');

Route::get('notice/:id', 'index/article/notice');

Route::group('auth',[
    'login/[:type]'=>'index/login/index',
    'callback'=>'index/login/callback',
    'getpassword'=>'index/login/getpassword',
    'register/[:agent]'=>'index/login/register',
    'checkusername'=>'index/login/checkusername',
    'checkunique'=>'index/login/checkunique',
    'verify'=>'index/login/verify',
    'forgot'=>'index/login/forgot',
])->method('GET|POST');

Route::group('user',[
    'index'=>'index/member/index',
    'profile'=>'index/member/profile',
    'avatar'=>'index/member/avatar',
    'password'=>'index/member/password',
    'security'=>'index/member/security',
    'addressadd'=>'index/member/addressAdd',
    'address'=>'index/member/address',
    'cards'=>'index/member/cards',
    'cardlist'=>'index/member/cardList',
    'cashlist'=>'index/member/cashList',
    'cash'=>'index/member/cash',
    'actionlog'=>'index/member/actionLog',
    'balance'=>'index/member/moneyLog',
    'shares'=>'index/member/shares',
    'team'=>'index/member/team',
    'order'=>'index/member/order',
    'confirm'=>'index/member/confirm',
    'notice'=>'index/member/notice',
    'feedback'=>'index/member/feedback',
    'logout'=>'index/member/logout'
])->method('GET|POST');


return [

];
