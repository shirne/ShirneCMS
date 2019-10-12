<?php


namespace app\task\controller;


use think\Console;
use think\console\Input;
use think\console\Output;
use think\Controller;
use think\facade\Env;

class InstallController extends Controller
{
    public function index($sql='',$mode='')
    {
        $runtimepath=Env::get('runtime_path');
        if(file_exists($runtimepath.'.lock')){
            header('Location: /');
            exit;
        }
        if($this->request->isPost()) {
            $console = Console::init(false);
            $output = new Output('buffer');
            $args = ['install'];
            if (!empty($sql)) {
                $args[] = '--sql';
                $args[] = $sql;
            }
            if (!empty($mode)) {
                $args[] = '--mode';
                $args[] = $mode;
            }
            if ($this->request->has('admin', 'post')) {
                $args[] = '--admin';
                $args[] = $this->request->post('admin');
            }
            if ($this->request->has('password', 'post')) {
                $args[] = '--password';
                $args[] = $this->request->post('password');
            }
            $input = new Input($args);
            
            $console->doRun($input, $output);
            
            return $output->fetch();
        }
        
        return $this->fetch();
    }
    
    public function connectdb($db){
    
    }
}