<?php

namespace app\api\processer;


use EasyWeChat\Kernel\Messages\Message;

abstract class BaseProcesser
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

    protected $processers=[];

    public final function all_processer(){
        if(empty($this->processers)){
            $files=scandir(__DIR__);
            foreach ($files as $file){
                if(in_array($file,['.','..']))continue;
                if(strpos($file,'Processer.php')<1)continue;
                $processer=strtolower(str_replace('Processer.php','',$file));
                if($processer!=='base'){
                    /**
                     * @var $class BaseProcesser
                     */
                    $class=ucfirst($processer).'Processer';
                    $this->processers[] = $class::getActions();
                }
            }
        }
        return $this->processers;
    }
    
    private static $instances=[];
    public static function getInstance(){
        $class=static::class;
        if(!isset(self::$instances[$class])){
            self::$instances[$class]=new static();
        }
        return self::$instances[$class];
    }
    public static function getActions(){
        return static::getInstance()->_getActions();
    }

    /**
     * 获取该处理器的方法及参数
     * @return array
     */
    protected abstract function _getActions();

    /**
     * @param $args
     * @return string|Message
     */
    public abstract function process($args);
}