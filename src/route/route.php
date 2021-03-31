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

define('CHANNELS', 'product|industry|infomation|about');

Route::pattern([
    'name' => '[a-zA-Z]\w*',
    'id'   => '\d+',
    'page'   => '\d+',
    'group'=> '[a-zA-Z]\w*',
    'action'=> '[a-zA-Z]\w*',
    'channel_name'=> '('.CHANNELS.')',
    'cate_name'=> '[a-zA-Z]\w*',
    'article_name'=> '[a-zA-Z][\-\w]*',
    'type'=>'\w+',
    'agent'=>'\w{6,}'
]);

Route::get('index$', 'index/index/index');

Route::get('share/:agent', 'index/index/share');

Route::group('product',[
    ':id'=>'index/product/view',
    'comment/:id'=>'index/product/comment',
    'favourite'=>'index/product/favourite',
    '[:name]'=>'index/product/index'
])->method('GET|POST');

Route::group('cart',[
    'index'=>'index/cart/index',
    'add'=>'index/cart/add',
    'update'=>'index/cart/update',
    'del'=>'index/cart/del',
    'clear'=>'index/cart/clear'
])->method('GET|POST');

Route::group('order',[
    'confirm'=>'index/order/confirm',
    'wechatpay'=>'index/order/wechatpay'
])->method('GET|POST');

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
    'order/[:action]'=>'index/member.order/:action',
    'address/[:action]'=>'index/member.address/:action',
    'account/[:action]'=>'index/member.account/:action',
    'agent/[:action]'=>'index/member.agent/:action',
    '[:action]'=>'index/member/:action'
])->method('GET|POST');

Route::get(':channel_name/:cate_name/:article_name/comment/[:page]', 'index/channel/comment');
Route::get(':channel_name/:cate_name/:article_name', 'index/channel/view');
Route::get(':channel_name/:cate_name/[:page]', 'index/channel/list');
Route::get(':channel_name', 'index/channel/index');

return [

];
