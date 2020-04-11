<?php

namespace app\common\command;


use app\common\model\MemberModel;
use app\common\model\OrderModel;
use app\common\model\ProductModel;
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
            ->addOption('type', 't', Option::VALUE_OPTIONAL, 'type of product for member upgrade, default 4')
            ->setDescription('Testing command');
    }

    /**
     * 命令调度
     * @param Input $input
     * @param Output $output
     * @return mixed
     */
    protected function execute(Input $input, Output $output)
    {
        $action=$input->getArgument('action');

        if(method_exists($this,'action'.ucfirst($action))){
            call_user_func([$this,'action'.ucfirst($action)],$input,$output);
        }else{
            $output->error('act error. excepted actions: random, adduser, resetrcount, order');
        }
        $output->writeln('exit.');
    }

    /**
     * 重置会员的推荐人数目
     * @param Input $input
     * @param Output $output
     */
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

    /**
     * 随机数据测试
     * @param Input $input
     * @param Output $output
     */
    protected function actionRandom(Input $input, Output $output)
    {
        $pid=0;
        $ptype=4;
        if($input->hasOption('buyproduct')){
            $pid=$input->getOption('buyproduct');
        }
        if($input->hasOption('type')){
            $ptype=$input->getOption('type');
        }

        $count=$input->getOption('count');
        while($count--){
            $isnew = rand(0,99)>50;
            if($isnew){
                $product=$this->getProduct($pid, $ptype);
            }else{
                $product=$this->getProduct($pid, 1);
            }
            if(empty($product)){
                $output->writeln('No suitable goods.');
                break;
            }
            $member=Db::name('member')->where('is_agent','GT',0)->order(Db::raw('rand()'))->find();
            if($isnew){
                $output->writeln('User '.$member['username'].'['.$member['id'].'] recommended new member: ');

                $newname='u'.random_str(mt_rand(5,8));
                while(Db::name('member')->where('username',$newname)->count()){
                    $newname='u'.random_str(mt_rand(5,8));
                }

                $this->createUser($output,$newname,'123456',$member['id'],$product);
            }else{
                $output->writeln('User '.$member['username'].'['.$member['id'].'] buy goods:');

                $this->makeOrder($output,$member,$product);
            }

            sleep(1);
        }

    }

    /**
     * 添加会员的命令
     * @param Input $input
     * @param Output $output
     */
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
            $product=$this->getProduct($buyproduct);
        }

        if($count<=1){
            $this->createUser($output,$username,$password,$parent,$product);
        }else{
            for($i=0;$i<$count;$i++){
                $sufix=str_pad($i,strlen($count),'0',STR_PAD_LEFT);
                $address['recive_name']=$username.$sufix;
                $this->createUser($output,$username.$sufix,$password,$parent,$product);
            }
        }
    }

    /**
     * 调用下单命令
     * @param Input $input
     * @param Output $output
     */
    protected function actionOrder(Input $input, Output $output)
    {
        $userid='';
        $buyproduct=0;
        if($input->hasOption('username')){
            $userid=$input->getOption('username');
        }
        if($input->hasOption('buyproduct')){
            $buyproduct=intval($input->getOption('buyproduct'));
        }

        if(!$userid){
            $output->error('username option must be specified.');
            return;
        }
        if(!$buyproduct){
            $output->error('buyproduct option must be specified.');
            return;
        }

        $product=$this->getProduct($buyproduct);

        $user=Db::name('Member')->where('id|username',$userid)->find();
        $this->makeOrder($output,$user,$product);
    }

    /**
     * 根据会员的推荐人的地址新增一条，如果没有，使用默认数据
     * @param $user
     */
    private function createAddress($user)
    {
        $address=Db::name('MemberAddress')->where('member_id',$user['referer'])->find();
        if(empty($address)){
            $address=[
                'recive_name'=>$user['username'],
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
        $address['member_id'] = $user['id'];
        Db::name('MemberAddress')->insert($address);
    }

    /**
     * 创建一个会员，如果指定了商品id，则顺便下单购买了
     * @param Output $output
     * @param $username
     * @param $password
     * @param $parent
     * @param $product
     * @return bool
     */
    private function createUser(Output $output,$username,$password,$parent,$product)
    {
        $data['username']=$username;
        $data['salt']=random_str(8);
        $data['password']=encode_password($password,$data['salt']);
        $data['referer']=$parent;
        $data['level_id']=getDefaultLevel();
        $model=MemberModel::create($data);
        if(empty($model['id'])){
            $output->error('Create user '.$username.' failed!');
            return false;
        }
        //$model->setReferer($parent);
        $output->writeln('Create user '.$username.'['.$model['id'].','.$parent.'] successed!');
        if(!empty($product)){
            $this->makeOrder($output,$model,$product);
        }
        return true;
    }

    /**
     * 获取指定产品用于下单,不指定id则随机获取一件激活商品
     * @param int $id
     * @return array|null|\PDOStatement|string|\think\Model
     */
    private function getProduct($id=0, $type=2)
    {
        if(!$id){
            $aprods = Db::name('product')->where('type',$type)->where('status',1)->field('id,min_price')->select();
            $aprod = weight_random($aprods,'min_price', false);
            $id=$aprod['id'];
        }
        $sku = Db::name('productSku')->where('product_id',$id)->find();

        $products=ProductModel::getForOrder([$sku['sku_id']=>1]);

        return $products[0];
    }

    /**
     * 自动充值并下单
     * @param Output $output
     * @param $user
     * @param $product
     */
    private function makeOrder(Output $output,$user,$product){

        money_log($user['id'],$product['product_price']*100,'Testing recharge','system');

        $address=Db::name('MemberAddress')
            ->where('member_id',$user['id'])
            ->order('is_default DESC')->find();
        if(empty($address)){
            $this->createAddress($user);
            $address=Db::name('MemberAddress')
                ->where('member_id',$user['id'])
                ->order('is_default DESC')->find();
        }
        $model = new OrderModel();
        $result=$model->makeOrder($user,[$product],$address,'Testing order',1);
        if($result){
            $output->writeln('User '.$user['username'].'['.$user['id'].'] order successed!');
        }else{
            $output->error('User '.$user['username'].'['.$user['id'].'] order failed:'.$model->getError());
        }
    }
}