<?php

namespace app\api\processor;


use EasyWeChat\Kernel\Messages\Message;
use app\api\handler\BaseHandler;
use app\common\model\MemberModel;
use EasyWeChat\Kernel\ServiceContainer;

abstract class BaseProcessor
{
    /**
     * @var ServiceContainer|null
     */
    protected $app;

    /**
     * @var BaseHandler|null
     */
    protected $handler;

    private $_member;

    public function __construct($app = null, $handler = null)
    {
        $this->app = $app;
        $this->handler = $handler;
        $this->_member=null;
    }

    /**
     * @param $processor
     * @param $app
     * @return bool|BaseProcessor
     */
    public static function factory($processor, $app, $handler)
    {
        if (empty($processor) || strtolower($processor) == 'base') return false;
        $file = __DIR__ . '/' . ucfirst($processor) . 'Processor.php';
        if (file_exists($file)) {
            require_once($file);
            $class = "\\app\\api\\processor\\" . ucfirst($processor) . 'Processor';
            return new $class($app, $handler);
        }
        return false;
    }

    protected $processors = [];

    public final function all_processor()
    {
        if (empty($this->processors)) {
            $files = scandir(__DIR__);
            foreach ($files as $file) {
                if (in_array($file, ['.', '..'])) continue;
                if (strpos($file, 'Processor.php') < 1) continue;
                $processor = strtolower(str_replace('Processor.php', '', $file));
                if ($processor !== 'base') {
                    require_once(__DIR__.'/'.$file);
                    /**
                     * @var $class BaseProcessor
                     */
                    $class = "\\app\\api\\processor\\" . ucfirst($processor) . 'Processor';
                    $this->processors[] = $class::getActions();
                }
            }
        }
        return $this->processors;
    }

    private static $instances = [];
    public static function getInstance()
    {
        $class = static::class;
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new static();
        }
        return self::$instances[$class];
    }
    public static function getActions()
    {
        return static::getInstance()->_getActions();
    }

    public function getMember(){
        if($this->_member === null){
            if($this->handler && $this->handler->user){
                $member_id = $this->handler->user['member_id'];
                if($member_id > 0){
                    $this->_member = MemberModel::where('id',$member_id)->find();
                }
            }
        }
        return $this->_member;
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
