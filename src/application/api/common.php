<?php

define('ERROR_NEED_LOGIN',99);//需要登录
define('ERROR_LOGIN_FAILED',101);//登录失败
define('ERROR_NEED_VERIFY',104);//需要验证码
define('ERROR_NEED_REGISTER',109);//登录失败,需要绑定
define('ERROR_REGISTER_FAILED',111);//注册失败
define('ERROR_TOKEN_INVAILD',102);//token无效
define('ERROR_TOKEN_EXPIRE',103);//token过期
define('ERROR_REFRESH_TOKEN_INVAILD',105);//refresh_token失效

define('ERROR_TMP_TOKEN_EXPIRE', 115);

define('ERROR_NEED_OPENID',112);
define('ERROR_MEMBER_DISABLED',113);

function empty2null($arr, $keys, $islist = true)
{
    if (is_array($arr) && !empty($arr)) {
        if (!is_array($keys)) $keys = array_map('trim', explode(',', $keys));
        foreach ($arr as $k => $row) {
            if ($islist) {
                if (is_array($row)) {
                    foreach ($row as $key => $item) {
                        if (in_array($key, $keys) && empty($item)) {
                            $arr[$k][$key] = null;
                        }
                    }
                }
            } elseif (in_array($k, $keys)) {
                if (empty($row)) {
                    $arr[$k] = null;
                }
            }
        }
    }
    return $arr;
}
