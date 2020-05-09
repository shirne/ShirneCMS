<?php
namespace app\vue\controller;

use think\Controller;

class IndexController extends Controller
{
    public function index()
    {
        $this->config=getSettings();
        
        if(isset($this->config['site-close']) && $this->config['site-close'] == 1){
            if($this->request->get('force') == 1){
                session('noclose-force',1);
            }
            if(session('noclose-force')!=1){
                $this->error($this->config['site-close-desc']);
            }
        }

        $this->assign('config',$this->config);
        return $this->fetch();
    }
}