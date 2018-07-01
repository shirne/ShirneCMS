<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/7/1
 * Time: 15:23
 */

namespace app\common\command;


use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class Install extends Command
{
    protected function configure()
    {
        $this->setName('install')
            ->addArgument('name', Argument::OPTIONAL, "database connect args like  username:password@dataname or username:password@host:dataname")
            ->addOption('mode', null, Option::VALUE_REQUIRED, 'install mode full(with test data) or cms or shop')
            ->setDescription('Install db script');
    }

    protected function execute(Input $input, Output $output)
    {
        $dbconfig=config('database.');
        $name = trim($input->getArgument('name'));
        if(!empty($name)){
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

        //todo install code

        $output->writeln("Install finished success.");
    }
}