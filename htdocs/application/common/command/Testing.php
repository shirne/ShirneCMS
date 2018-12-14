<?php

namespace app\common\command;


use app\common\facade\OrderFacade;
use app\common\model\MemberModel;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\Db;

/**
 * 测试用例
 * Class Testing
 * @package app\common\command
 */
class Testing extends Command
{
    protected function configure()
    {
        $this->setName('testing')
            ->addArgument('action', Argument::REQUIRED, "add front member")
            ->addOption('username', 'u', Option::VALUE_OPTIONAL, 'username, usefor prefix if count great then 1')
            ->addOption('count', 'c', Option::VALUE_OPTIONAL, 'count of user, default 1')
            ->addOption('password', 's', Option::VALUE_OPTIONAL, 'password of all user, default 123456')
            ->addOption('parent', 'p', Option::VALUE_OPTIONAL, 'reference of the users.')
            ->addOption('buyproduct', 'b', Option::VALUE_OPTIONAL, 'auto recharge and buy the specified product.')
            ->setDescription('Testing command');
    }
    protected function execute(Input $input, Output $output)
    {
        $action=$input->getArgument('action');

        if(method_exists($this,'action'.ucfirst($action))){
            call_user_func([$this,'action'.ucfirst($action)],$input,$output);
        }else{
            $output->error('act error. excepted actions: adduser, resetrcount');
        }
        $output->writeln('exit.');
    }

    protected function actionResetrcount(Input $input, Output $output)
    {
        Db::name('member')->where('id','GT',0)->update(['recom_count'=>0,'team_count'=>0]);
        $members=Db::name('member')->field('id,referer')
            ->where('is_agent','GT',0)
            ->where('referer','GT',0)
            ->select();
        $layer=getSetting('performance_layer');
        foreach ($members as $member){
            $parents=getMemberParents($member['id'],$layer);
            if(!empty($parents)) {
                Db::name('member')->where('id', $parents[0])->setInc('recom_count', 1);
                Db::name('member')->whereIn('id', $parents)->setInc('team_count', 1);
                $output->writeln('user '.$member['id'].'\'s parents recommend count updated');
            }else{
                $output->error('user '.$member['id'].'\'s parent '.$member['referer'].' not found');
            }
        }
    }

    protected function actionAdduser(Input $input, Output $output)
    {
        if(!$input->hasOption('username')){
            $output->error('username option must be specified.');
            return;
        }
        $username=$input->getOption('username');
        $count=1;
        $password='123456';
        $parent=0;
        $buyproduct=0;
        $product=[];
        $address=[];
        if($input->hasOption('count')){
            $count=intval($input->getOption('count'));
        }
        if($input->hasOption('password')){
            $password=intval($input->getOption('password'));
        }
        if($input->hasOption('parent')){
            $parent=intval($input->getOption('parent'));
        }
        if($input->hasOption('buyproduct')){
            $buyproduct=intval($input->getOption('buyproduct'));
        }
        if($buyproduct>0){
            $product=Db::view('ProductSku','*')
                ->view('Product',['title'=>'product_title','image'=>'product_image','levels','is_discount','is_commission','type'],'ProductSku.product_id=Product.id','LEFT')
                ->where('Product.id',$buyproduct)
                ->find();
            $product['product_price']=$product['price'];
            $product['count']=1;
            if(!empty($product['image']))$product['product_image']=$product['image'];

            if($parent){
                $address=Db::name('MemberAddress')->where('member_id',$parent)->find();
                if(empty($address)){
                    $address=[
                        'recive_name'=>$username,
                        'mobile'=>'13866888866',
                        'province'=>'广东省',
                        'city'=>'中山市',
                        'area'=>'市区',
                        'address'=>'测试地址',
                        'code'=>'000000',
                        'is_default'=>1
                    ];
                }else{
                    $address['is_default']=1;
                    unset($address['address_id']);
                }
            }
        }

        if($count<=1){
            $this->createUser($output,$username,$password,$parent,$address,$product);
        }else{
            for($i=0;$i<$count;$i++){
                $sufix=str_pad($i,strlen($count),'0',STR_PAD_LEFT);
                $address['recive_name']=$username.$sufix;
                $this->createUser($output,$username.$sufix,$password,$parent,$address,$product);
            }
        }
    }
    protected function createUser(Output $output,$username,$password,$parent,$address,$product)
    {
        $data['username']=$username;
        $data['salt']=random_str(8);
        $data['password']=encode_password($password,$data['salt']);
        $data['referer']=$parent;
        $data['level_id']=getDefaultLevel();
        $model=MemberModel::create($data);
        if(empty($model['id'])){
            $output->error('创建用户 '.$username.' 失败！');
            return false;
        }
        $output->writeln('成功添加用户 '.$username.'['.$model['id'].']');
        if(!empty($product)){
            $ordertype=1;
            if($product['type']==2){
                $ordertype=2;
            }

            money_log($model['id'],$product['product_price']*100,'测试程序自动充值','system');

            $address['member_id']=$model['id'];
            Db::name('MemberAddress')->insert($address);
            $address=Db::name('MemberAddress')->where('member_id',$model['id'])->find();
            $result=OrderFacade::makeOrder($model,[$product],$address,$data['remark'],1,$ordertype);
            if($result){
                $output->writeln('用户 '.$username.'['.$model['id'].'] 下单成功');
            }else{
                $output->error('用户 '.$username.'['.$model['id'].'] 下单失败');
            }
        }
        return true;
    }
}