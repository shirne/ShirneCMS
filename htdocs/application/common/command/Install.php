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
use think\Db;

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
        $sql = trim($input->getArgument('sql'));
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

        //install code
        if(empty($sql))$sql='./struct.sql';
        $sqls=$this->explodesql($sql,'sa_',$dbconfig['prefix']);
        foreach ($sqls as $sql){
            Db::execute($sql);
        }

        $output->writeln("Install finished success.");
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