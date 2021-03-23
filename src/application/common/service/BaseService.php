<?php

namespace app\common\service;


/**
 * BaseService
 * @package app\common\service
 */
class BaseService
{
    protected $errNo;
    protected $errMsg;

    public function getError(){
        return $this->errMsg;
    }

    public function setError($errmsg, $errno = -1){
        $this->errMsg = $errmsg;
        $this->errNo = $errno;
    }
}