<?php

namespace app\index\controller;



/**
 * 扩展
 * Class AddonController
 * @package app\index\controller
 */
class AddonController extends BaseController
{
    protected $viewPath;
    protected $controller;
    protected $action;

    public function getViewPath(){
        return $this->viewPath;
    }

    public function index($addon, $controller = 'index', $action = 'index')
    {
        $tpl_replace = config('template.tpl_replace_string');
        $this->viewPath = '../../../addon/'.$addon.'/index/view/';
        $tpl_replace['__ADDON__']=$this->viewPath;
        $tpl_replace['__ADDON_STATIC__']='/addon/'.$addon.'/static/';
        config('template.tpl_replace_string', $tpl_replace);
        $this->view->config('tpl_replace_string', $tpl_replace);

        $this->controller = $controller;
        $this->action = $action;

        $class = '\\addon\\'.$addon.'\\index\\controller\\'.ucfirst($controller).'Controller';
        $addonController = new $class($this);
        return $addonController->$action();
    }

    public function public_assign($name, $value=''){
        $this->assign($name, $value);
        return $this;
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