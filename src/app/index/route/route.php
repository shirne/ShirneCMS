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


Route::get('index$', 'index/index');

Route::get('share/:agent', 'index/share');

Route::group('product',function() {
    Route::get(':id', 'product/view');
    Route::rule('comment/:id', 'product/comment','GET|POST');
    Route::get('[:name]', 'product/index');
});

Route::group('cart',function() {
    Route::get('index', 'cart/index');
    Route::rule('add', 'cart/add','GET|POST');
    Route::rule('update', 'cart/update','GET|POST');
    Route::rule('del', 'cart/del','GET|POST');
    Route::rule('clear', 'cart/clear','GET|POST');
});

Route::group('order',function() {
    Route::rule('confirm', 'order/confirm','GET|POST');
    Route::rule('wechatpay', 'order/wechatpay','GET|POST');
});

Route::get('notice/:id', 'index/notice');

Route::group('auth',function() {
    Route::rule('login/[:type]', 'login/index','GET|POST');
    Route::get('callback', 'login/callback');
    Route::rule('getpassword', 'login/getpassword','GET|POST');
    Route::rule('register/[:agent]', 'login/register','GET|POST');
    Route::rule('checkusername', 'login/checkusername','GET|POST');
    Route::rule('checkunique', 'login/checkunique','GET|POST');
    Route::get('verify', 'login/verify');
    Route::rule('forgot', 'login/forgot','GET|POST');
});

Route::group('user',function() {
    Route::rule('order/[:action]','member.order/:action','GET|POST');
    Route::rule('address/[:action]','member.address/:action','GET|POST');
    Route::rule('account/[:action]','member.account/:action','GET|POST');
    Route::rule('agent/[:action]','member.agent/:action','GET|POST');
    Route::rule('[:action]','member/:action','GET|POST');
});

Route::get(':channel_name/:cate_name/:article_name/comment/[:page]', 'channel/comment');
Route::get(':channel_name/:cate_name/:article_name', 'channel/view');
Route::get(':channel_name/:cate_name/[:page]', 'channel/list');
Route::get(':channel_name', 'channel/index');

Route::rule('<addon>\\.<controller>/<action>', 'addon/index');
