<?php

namespace app\common\command;


use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use think\Db;

/**
 * 重置管理员命令
 * Class Manager
 * @package app\common\command
 */
class Manager extends Command
{
    protected function configure()
    {
        $this->setName('manager')
            ->addOption('username', 'u', Option::VALUE_OPTIONAL, "Specify the super admin account, default admin")
            ->addOption('password', 'p', Option::VALUE_REQUIRED, 'Specify the super admin password')
            ->setDescription('reset super admin');
    }

    protected function execute(Input $input, Output $output)
    {
        $admin='admin';
        if($input->hasOption('admin')){
            $admin=$input->getOption('admin');
        }
        if($input->hasOption('password')){
            $password=$input->getOption('password');

            $data['type']=1;
            $data['username']=$admin;
            $data['salt']=random_str(8);
            $data['password'] = encode_password($password,$data['salt']);
            Db::name('Manager')->where('id',1)->update($data);
        }else{
            $output->error('The password option mast be specified.');
            exit;
        }

        $output->writeln("Reset success.");
    }
}