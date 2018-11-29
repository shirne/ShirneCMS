<?php

namespace app\api\Processer;


class BaseProcesser
{
    protected $app;

    public function __construct($app=null)
    {
        $this->app=$app;
    }

    /**
     * @param $processer
     * @param $app
     * @return bool|BaseProcesser
     */
    public static function factory($processer,$app){
        if(empty($processer) || strtolower($processer)=='base')return false;
        if(file_exists(__DIR__.'/'.ucfirst($processer).'Processer.php')){
            $class=ucfirst($processer).'Processer';
            return new $class($app);
        }
        return false;
    }

    protected function all_processer(){

    }

    public function process($args){}
}