<?php


namespace app\task\controller;


use think\facade\Console;
use think\console\Input;
use think\console\Output;
use app\BaseController as Controller;
use think\Exception;
use think\facade\Config;
use think\facade\Env;
use think\facade\Db;
use think\facade\Log;

define('MYSQL_MIN_VERSION','5.5.3');

class InstallController extends Controller
{
    /**
     * @param $mode string|array
     */
    public function index($sql='',$mode='')
    {
        $runtimepath=Env::get('runtime_path');
        if(file_exists($runtimepath.'install.lock')){
            header('Location: /');
            exit;
        }
        
        if($this->request->isPost()) {
            set_time_limit(0);
            @ini_set('max_execution_time',0);
            
            $db = $this->request->post('db');
            if(!empty($db) && !empty($db['hostname'])){
                $dbconfig=config('database');
                $needupdate=false;

                foreach($db as $k=>$v){
                    if(isset($dbconfig[$k]) && $v != $dbconfig[$k]){
                        $needupdate=true;
                        break;
                    }
                }
                if($needupdate){
                    $config = env('config_path').'/../.env';
                    if(!is_file($config)){
                        $example = env('config_path').'/../.env.example';
                        if(is_file($example)){
                            copy($example,$config);
                        }else{
                            $configstr=<<<'ENVDATA'
APP_DEBUG = false

[APP]
DEFAULT_TIMEZONE = Asia/Shanghai

[DATABASE]
TYPE = mysql
HOSTNAME = localhost
DATABASE = shirnecms
USERNAME = root
PASSWORD = 123456
PREFIX = sa_
HOSTPORT = 3306
CHARSET = utf8mb4
DEBUG = false

[LANG]
default_lang = zh-cn
ENVDATA;
                            file_put_contents($config, $configstr);
                        }
                    }
                    if(!is_writable($config)){
                        $this->error('数据库配置文件不可写,请修改权限或手动配置');
                    }
                    $content = file_get_contents($config);
                    $content = preg_replace_callback('/\'([\w\d]+)\'(\s*)=(\s*)\'[^\']+\'/',function($matches)use($db,&$dbconfig){
                        $key = strtolower($matches[1]);
                        if(isset($db[$key])){
                            $dbconfig[$matches['1']]=$db[$key];
                            return "'{$matches['1']}'{$matches['2']}={$matches['3']}'{$db[$key]}'";
                        }
                        return $matches[0];
                    },$content);

                    file_put_contents($config,$content);
                    Config::set($dbconfig, 'database');
                }
            }
            $console = app()->console;
            $output = new Output('buffer');
            $args = ['install'];
            if (!empty($sql)) {
                $args[] = '--sql';
                $args[] = $sql;
            }
            if (!empty($mode)) {
                $args[] = '--module';
                $args[] = is_array($mode)?implode(',',$mode):$mode;
            }
            if ($this->request->has('admin', 'post')) {
                $args[] = '--username';
                $args[] = $this->request->post('admin');
            }
            if ($this->request->has('password', 'post')) {
                $args[] = '--password';
                $args[] = $this->request->post('password');
            }
            $input = new Input($args);
            
            $console->doRun($input, $output);
            
            $message = $output->fetch();
            if(strpos($message,'success') !== false){
                $this->success($message);
            }else{
                $this->error($message);
            }
        }
        
        $envs=[];
        $mysqlenv=['title'=>'Mysql','require'=>MYSQL_MIN_VERSION,'current'=>'','pass'=>null];
        $dbcfg=config('database');
        try{
            $default = $dbcfg['default']??'mysql';
            $connections = $dbcfg['connections']??[];
            if(!empty($connections[$default])){
                $dbcfg = $connections[$default];
                $version=Db::connect($default)->query('select version()');
                $mysqlenv['current']=$version[0]['version()'];
                if($mysqlenv['current'] && version_compare($mysqlenv['require'],$mysqlenv['current'],'<=')){
                    $mysqlenv['pass']=true;
                }else{
                    $mysqlenv['pass']=false;
                }
            }
        }catch(Exception $e){
            Log::record($e->getMessage());
        }
        $envs[]=$mysqlenv;

        $phpenv=['title'=>'PHP','require'=>'7.1.3','current'=>PHP_VERSION,'pass'=>false];
        if(version_compare($phpenv['require'],$phpenv['current'],'<=')){
            $phpenv['pass']=true;
        }
        $envs[]=$phpenv;

        $phpsqlenv=['title'=>'PHP-数据库','require'=>'Mysqli/PDO','current'=>'','pass'=>false];
        $exists=[];
        if(function_exists('mysqli_connect')){
            $exists[]='Mysqli';
            $phpsqlenv['pass']=true;
        }
        if(class_exists('PDO')){
            if(defined('PDO_MYSQL')){
                $exists[]='PDO-Mysql';
                $phpsqlenv['pass']=true;
            }else{
                $exists[]='PDO';
            }
        }
        $phpsqlenv['current']=implode(' & ',$exists);
        $envs[]=$phpsqlenv;

        $phpgdenv=['title'=>'PHP-gd','require'=>'*','current'=>'','pass'=>false];
        if(function_exists('gd_info')){
            $gdinfo=gd_info();
            $phpgdenv['current']=$gdinfo['GD Version'];
            $phpgdenv['pass']=true;
        }
        $envs[]=$phpgdenv;

        $phpmbenv=['title'=>'PHP-mbstring','require'=>'*','current'=>'','pass'=>false];
        if(function_exists('mb_strlen')){
            $phpmbenv['pass']=true;
        }
        $envs[]=$phpmbenv;

        $phpsslenv=['title'=>'PHP-OpenSSL','require'=>'*','current'=>'','pass'=>false];
        if(function_exists('openssl_encrypt')){
            $phpsslenv['pass']=true;
        }
        $envs[]=$phpsslenv;

        $phpcurlenv=['title'=>'PHP-curl','require'=>'*','current'=>'','pass'=>false];
        if(function_exists('curl_version')){
            $version=curl_version();
            $phpcurlenv['current']=$version['version'];
            $phpcurlenv['pass']=true;
        }
        $envs[]=$phpcurlenv;

        $pass = true;
        foreach($envs as $item){
            if($item['pass']===false){
                $pass=false;
                break;
            }
        }
        $this->assign('dbcfg',$dbcfg);
        $this->assign('envs',$envs);
        $this->assign('pass',$pass);
        return $this->fetch();
    }
    
    public function connectdb($db){
        if(empty($db['hostname'])){
            $this->error('主机名称未填写');
        }
        $db['type']='mysql';
        try{
            $tmpConfig = config('database');
            $key = 'db'.time();
            $default = $tmpConfig['connections'][$tmpConfig['default']];
            $tmpConfig['connections'][$key]=array_merge($default, $db);
            
            Config::set($tmpConfig,'database');
            
            $version=Db::connect($key)->query('select version()');
            $versionstr=$version[0]['version()'];
            
        }catch(\Throwable $e){
            $message=$e->getMessage();
            Log::record($message.$e->getTraceAsString());
            $this->error('数据库连接失败');
        }

        if($versionstr && version_compare($versionstr,MYSQL_MIN_VERSION,'>=')){
            $this->success('数据库连接成功');
        }
        $this->error('数据库版本最低'.MYSQL_MIN_VERSION.',当前'.$versionstr);
    }
}