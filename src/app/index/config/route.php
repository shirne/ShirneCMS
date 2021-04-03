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

use think\facade\Route;

Route::pattern([
    'name' => '[a-zA-Z]\w*',
    'id'   => '\d+',
    'group'=> '[a-zA-Z]\w*',
    'action'=> '[a-zA-Z]\w*',
    'type'=>'\w+',
    'agent'=>'\w{6,}'
]);
Route::get('index', 'index/index/index');

Route::get('share/:agent', 'index/index/share');

Route::get('page/:group/[:name]','index/page/index');

Route::get('notice/:id', 'index/article/notice');
//Route::group('article',function() {
    Route::get('article/:id', 'index/article/view');
    Route::rule('article/comment/:id', 'index/article/comment','GET|POST');
    Route::get('article/[:name]', 'index/article/index');
//});

//Route::group('product',function() {
    Route::get('product/:id', 'index/product/view');
    Route::rule('product/comment/:id', 'index/product/comment','GET|POST');
    Route::get('product/[:name]', 'index/product/index');
//});

//Route::group('cart',function() {
    Route::get('cart/index', 'index/cart/index');
    Route::rule('cart/add', 'index/cart/add','GET|POST');
    Route::rule('cart/update', 'index/cart/update','GET|POST');
    Route::rule('cart/del', 'index/cart/del','GET|POST');
    Route::rule('cart/clear', 'index/cart/clear','GET|POST');
//});

//Route::group('order',function() {
    Route::rule('order/confirm', 'index/order/confirm','GET|POST');
    Route::rule('order/wechatpay', 'index/order/wechatpay','GET|POST');
//});

//Route::group('auth',function() {
    Route::rule('auth/login/[:type]', 'index/login/index','GET|POST');
    Route::get('auth/callback', 'index/login/callback');
    Route::rule('auth/getpassword', 'index/login/getpassword','GET|POST');
    Route::rule('auth/register/[:agent]', 'index/login/register','GET|POST');
    Route::rule('auth/checkusername', 'index/login/checkusername','GET|POST');
    Route::rule('auth/checkunique', 'index/login/checkunique','GET|POST');
    Route::get('auth/verify', 'index/login/verify');
    Route::rule('auth/forgot', 'index/login/forgot','GET|POST');
//});

//Route::group('user',function() {
    Route::rule('user/order/[:action]','index/member.order/:action','GET|POST');
    Route::rule('user/address/[:action]','index/member.address/:action','GET|POST');
    Route::rule('user/account/[:action]','index/member.account/:action','GET|POST');
    Route::rule('user/agent/[:action]','index/member.agent/:action','GET|POST');
    Route::rule('user/[:action]','index/member/:action','GET|POST');
//});