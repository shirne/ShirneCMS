<?php


namespace app\common\command;


use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\Db;

/**
 * 安装命令
 * Class Install
 * @package app\common\command
 */
class Install extends Command
{
    protected function configure()
    {
        $this->setName('install')
            ->addOption('name', NULL, Option::VALUE_OPTIONAL, "database connect args like  username:password@dataname or username:password@host:dataname")
            ->addOption('file', 'f', Option::VALUE_OPTIONAL, "script file name")
            ->addOption('mode', 'm', Option::VALUE_REQUIRED, 'install extend model ex. shop')
            ->addOption('username', 'u', Option::VALUE_OPTIONAL, "Specify the super admin account")
            ->addOption('password', 'p', Option::VALUE_REQUIRED, 'Specify the super admin password')
            ->setDescription('Install db script');
    }

    /**
     * 执行安装
     * @param Input $input
     * @param Output $output
     * @return void
     */
    protected function execute(Input $input, Output $output)
    {
        $lockfile=app()->getRuntimePath().'install.lock';
        if(file_exists($lockfile)){
            $output->error('The system has been installed. If you want to reinstall, please delete the file '.$lockfile.' and run this command again.');
            return;
        }

        $dbconfig=config('database');

        if($input->hasOption('name')){
            $name = trim($input->getOption('name'));
            $args=explode('@',$name);
            if(empty($args) || count($args)<2 || strpos(':',$args[0])===false){
                $output->writeln("Install aborted with error: arguments error.");
                exit;
            }
            list($username,$password)=explode(':',$args[0]);
            $dbconfig['username']=$username;
            $dbconfig['password']=$password;

            if(strpos(':',$args[1])===false){
                $dbname=$args[1];
            }else{
                list($host,$dbname)=explode(':',$args[1]);
                $dbconfig['hostname']=$host;
            }
            $dbconfig['database']=$dbname;
        }

        //install code
        if($input->hasOption('sql')){
            $sql ='./'.trim($input->getOption('sql')).'.sql';
            if(!file_exists($sql)){
                $output->error('The specified sql script not exists.');
                return;
            }
            $this->runsql($sql,$dbconfig['prefix']);
        }
        else{
            $path=app()->getAppPath();
            $sqlpath=$path.'../dbscript';
            if(!is_dir($sqlpath)){
                $sqlpath=$path.'../../dbscript';
            }
            if(!is_readable($sqlpath)){
                $output->error('Please ensure the dbscript folder exists and accessable.');
                return;
            }
            if(!is_dir($sqlpath)){
                $output->error('Please upload the dbscript folder or specify sql option.');
                return;
            }else{
                foreach (['struct','init'] as $script){
                    $sql = $sqlpath.'/'.$script.'.sql';
                    if(file_exists($sql)){
                        $this->runsql($sql,$dbconfig['prefix']);
                    }else{
                        $output->warning('Sql file '.$script.'.sql not exists!');
                    }
                }
                if($input->hasOption('mode')){
                    $mode=$input->getOption('mode');
                    if(!empty($mode)){
                        $modes=explode(',',$mode);
                        foreach ($modes as $mode){
                            $sql = $sqlpath.'/update_'.$mode.'.sql';
                            if(file_exists($sql)) {
                                $this->runsql($sql, $dbconfig['prefix']);
                            }else{
                                $output->warning('Sql file update_'.$mode.'.sql not exists!');
                            }
                        }
                    }
                }
            }
        }

        $admin='admin';
        if($input->hasOption('username')){
            $admin=$input->getOption('username');
        }
        $password='123456';
        if($input->hasOption('password')){
            $password=$input->getOption('password');
        }
        $data['username']=$admin;
        $data['salt']=random_str(8);
        $data['password'] = encode_password($password,$data['salt']);
        Db::name('Manager')->where('id',1)->update($data);

        file_put_contents($lockfile,time());

        $output->writeln("Install finished success.");
    }

    protected function runsql($file,$prefix){
        $sqls=$this->explodesql($file,'sa_',$prefix);
        foreach ($sqls as $sql){
            Db::execute($sql);
        }
    }

    protected function explodesql($sql_path,$old_prefix="",$new_prefix="",$separator=";\n")
    {
        $commenter = array('#','--');
        //判断文件是否存在
        if(!file_exists($sql_path))
            return false;

        $content = file_get_contents($sql_path);   //读取sql文件
        $content = str_replace(array('`'.$old_prefix,' '.$old_prefix, "\r"), array('`'.$new_prefix,' '.$new_prefix, "\n"), $content);//替换前缀

        //通过sql语法的语句分割符进行分割
        $segment = explode($separator,trim($content));

        //去掉注释和多余的空行
        $data=array();
        foreach($segment as  $statement)
        {
            $sentence = explode("\n",$statement);
            $newStatement = array();
            foreach($sentence as $subSentence)
            {
                if('' != trim($subSentence))
                {
                    //判断是会否是注释
                    $isComment = false;
                    foreach($commenter as $comer)
                    {
                        if(preg_match("/^(".$comer.")/is",trim($subSentence)))
                        {
                            $isComment = true;
                            break;
                        }
                    }
                    //如果不是注释，则认为是sql语句
                    if(!$isComment)
                        $newStatement[] = $subSentence;
                }
            }
            $data[] = $newStatement;
        }

        //组合sql语句
        foreach($data as  $statement)
        {
            $newStmt = '';
            foreach($statement as $sentence)
            {
                $newStmt = $newStmt.trim($sentence)."\n";
            }
            if(!empty($newStmt))
            {
                $result[] = $newStmt;
            }
        }
        return $result;
    }
}