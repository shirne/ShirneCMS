<?php

namespace app\admin\controller;


/**
 * 扩展
 * Class AddonController
 * @package app\admin\controller
 */
class AddonController extends BaseController
{
    protected $viewPath;
    protected $controller;
    protected $action;

    protected $addon;

    public function getViewPath(){
        return $this->viewPath;
    }

    public function index($addon, $controller = 'index', $action = 'index')
    {
        $tpl_replace = config('template.tpl_replace_string');
        $this->viewPath = '../../../addon/'.$addon.'/admin/view/';
        $tpl_replace['__ADDON__']=$this->viewPath;
        $tpl_replace['__ADDON_STATIC__']='/addon/'.$addon.'/static/';
        config('template.tpl_replace_string', $tpl_replace);
        $this->view->config('tpl_replace_string', $tpl_replace);

        $this->controller = $controller;
        $this->action = $action;

        $class = '\\addon\\'.$addon.'\\admin\\controller\\'.ucfirst($controller).'Controller';
        $this->addon = new $class($this);
        $method = new \ReflectionMethod($this->addon, $action);
        $arguments = [];
        foreach ($method->getParameters() as $param) {
            if ($this->request->has($param->name)) {
                $arguments[] = $this->request->param($param->name);
            } elseif ($param->isDefaultValueAvailable()) {
                $arguments[] = $param->getDefaultValue();
            }
        }
        
        return $method->invokeArgs($this->addon, $arguments);
    }

    public function __callProtected($method, $arguments){
        if(empty($this->addon) || !$this->addon instanceof \addon\base\BaseController){
            $this->error('页面不存在');
        }
        return call_user_func_array([$this, $method], $arguments);
    }

    public function __getProtected($name){
        if(empty($this->addon) || !$this->addon instanceof \addon\base\BaseController){
            $this->error('页面不存在');
        }
        return $this->$name;
    }

    public function public_fetch($template='', $vars=[], $connfig=[]){
        if(empty($template)){
            $template = $this->viewPath.$this->controller.'/'.$this->action;
        }else{
            if(strpos($template, '/') === false){
                $template = $this->viewPath.$this->controller.'/'.$template;
            }else{
                $template = $this->viewPath.'/'.$template;
            }
        }
        return $this->fetch($template, $vars, $connfig);
    }
}