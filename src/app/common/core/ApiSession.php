<?php

namespace app\common\core;

use think\App;
use think\Cache;
use think\Session;

class ApiSession extends Session
{
    private $prefix;
    public function setPrefix($prefix){
        $this->store = $prefix;
    }

    private $store;
    public function setStore($store){
        $this->store = $store;
    }

    public function __construct(App $app, $prefix = '')
    {
        parent::__construct($app);
        $this->store = Cache();
        $this->prefix = $prefix;
    }

    protected function createDriver(string $name)
    {
        return $this->store;
    }

    public function has($key){
        return $this->store->has($this->prefix.$key);
    }

    public function get($key){
        return $this->store->get($this->prefix.$key);
    }

    public function set($key, $value){
        return $this->store->set($this->prefix.$key, $value);
    }

    public function delete($key){
        return $this->store->delete($this->prefix.$key);
    }
}