<?php

namespace shirne\third;

use think\facade\Log;

/**
 * 第三方接口基类
 * Class ThirdBase
 * @package third
 */
class ThirdModelBase
{

    protected $debug;
    public $errCode = 0;
    protected $errCodeKey = 'errcode';
    public $errMsg = "";
    protected $errMsgKey = 'errmsg';

    public function __construct($options)
    {
        $this->debug = isset($options['debug']) ? $options['debug'] : false;
    }

    protected function set_error($errmsg, $errno = -1)
    {
        $this->errMsg = $errmsg;
        $this->errCode = $errno;
    }
    protected function clear_error()
    {
        $this->set_error('', 0);
    }

    public function get_error()
    {
        if ($this->errCode == 0) {
            return [];
        }
        return [
            'errno' => $this->errCode,
            'errmsg' => $this->errMsg
        ];
    }
    public function has_error()
    {
        return $this->errCode != 0;
    }
    public function get_error_msg()
    {
        return $this->errMsg;
    }


    /**
     * 设置缓存，按需重载
     * @param string $cachename
     * @param mixed $value
     * @param int $expired
     * @return boolean
     */
    protected function setCache($cachename, $value, $expired)
    {
        cache($cachename, $value, $expired);
        return false;
    }

    /**
     * 获取缓存，按需重载
     * @param string $cachename
     * @return mixed
     */
    protected function getCache($cachename)
    {
        cache($cachename);
        return false;
    }

    /**
     * 清除缓存，按需重载
     * @param string $cachename
     * @return boolean
     */
    protected function removeCache($cachename)
    {
        cache($cachename, null);
        return false;
    }
}
