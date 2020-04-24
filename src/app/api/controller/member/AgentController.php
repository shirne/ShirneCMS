<?php

namespace app\api\controller\member;

use app\api\controller\AuthedController;
use app\common\model\AwardLogModel;
use app\common\model\MemberAuthenModel;
use app\common\model\OrderModel;
use app\common\model\WechatModel;
use shirne\common\Poster;
use think\facade\Db;

class AgentController extends AuthedController
{
    public function initialize()
    {
        parent::initialize();
        if(!$this->user['is_agent']){
            $this->error('您还不是代理，请先升级为代理');
        }
    }
    
    public function generic(){
        $data=[];
        $data['order_count']=Db::name('awardLog')->where('member_id',$this->user['id'])
            ->where('create_time','gt',strtotime('today -7 days'))
            ->where('status','>',-1)
            ->distinct(true)->field('order_id')->count();
        $data['amount_future']=Db::name('awardLog')->where('member_id',$this->user['id'])
            ->where('status',0)->sum('amount');
    
        $data['total_award']=Db::name('awardLog')->where('member_id',$this->user['id'])
            ->where('status',1)->sum('amount');
        return $this->response($data);
    }

    public function upgrade($level_id=2){
        $authen= MemberAuthenModel::where('level_id',$level_id)
            ->where('member_id',$this->user['id'])
            ->find();
        if($this->request->isPost()){
            if($authen['status'] == 1){
                $this->error('申请已审核通过,不能修改');
            }
            $data = $this->request->only(['realname','mobile','province','city']);
            try{
                $data['status']=-1;
                if(empty($authen)){
                    $data['member_id']=$this->user['id'];
                    $data['level_id']=$level_id;
                    MemberAuthenModel::insert($data);
                }else{
                    $authen->save($data);
                }
            }catch(\Exception $err){
                $this->error('保存失败: %s',[$err->getMessage()]);
            }
            $this->error('申请已提交');
        }
        return $this->response([
            'authen'=>$authen
        ]);
    }
    
    public function poster($page = 'pages/index/index'){
    
        $platform=$this->request->tokenData['platform'];
        
        try{
            $url = $this->get_share_img($platform,$page);
        }catch(\Exception $e){
            $this->error($e->getMessage());
        }
        
        $qrurl = str_replace('-'.$platform.'.jpg','-qrcode.png',$url);
        return $this->response(['poster_url'=>$url,'qr_url'=>$qrurl]);
    }
    private function get_share_img($platform,$page){
    
        $sharepath = './uploads/share/'.($this->user['id']%100).'/'.$this->user['agentcode'].'-'.$platform.'.jpg';
        $config=config('poster');
        if(empty($config) || empty($config['background'])){
            $this->error('请配置海报生成样式(config/poster.php)');
        }
        if(!file_exists($config['background'])){
            $this->error('分享图生成失败(bg)');
        }
        if(file_exists($sharepath)){
            $fileatime=filemtime($sharepath);
            if($this->user['update_time']<$fileatime &&
                filemtime($config['background'])<$fileatime
            ){
                return media(ltrim($sharepath,'.'));
            }
        }
        if(in_array($platform, ['wechat-miniprogram','wechat-minigame'])){
            $this->create_appcode_img($config,$sharepath,$page);
        }else{
            $this->create_share_img($config,$sharepath,$page);
        }
        
        return media(ltrim($sharepath,'.'));
    }
    private function create_share_img($config,$sharepath,$page){
        $qrpath=dirname($sharepath);
        $qrfile = $this->user['agentcode'].'-qrcode.png';
        $filename=$qrpath.'/'.$qrfile;

        if(!file_exists($filename)) {
            $content=gener_qrcode($page, 430);
            if(!is_dir($qrpath)){
                mkdir($qrpath,0777,true);
            }
            file_put_contents($filename,$content);
            if(!file_exists($filename)){
                $this->error('二维码生成失败');
            }
        }
        
        //$config['background']=$bgpath;
        $poster = new Poster($config);
        $poster->generate([
            'qrcode'=>$filename,
            'avatar'=>$this->user['avatar'],
            'bg'=>1,
            'nickname'=>$this->user['nickname']
        ]);
        $poster->save($sharepath);
    }
    private function create_appcode_img($config,$sharepath,$page){
        $appid=$this->request->tokenData['appid'];
        $wechat=WechatModel::where('appid',$appid)->find();
        if(empty($wechat)){
            $this->error('分享图生成失败(wechat)');
        }
    
        $qrpath=dirname($sharepath);
        $qrfile = $this->user['agentcode'].'-appcode.png';
        $filename=$qrpath.'/'.$qrfile;
        if(!file_exists($filename)) {
            $app = WechatModel::createApp($wechat);
            if (empty($app)) {
                $this->error('分享图生成失败(app)');
            }
    
            $response = $app->app_code->getUnlimit('agent=' . $this->user['agentcode'], [
                'page' => $page,
                'width' => 520
            ]);
            if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
                $genername = $response->saveAs($qrpath, $this->user['agentcode'] . '-appcode.png');
            }
            if(empty($genername)){
                $this->error('小程序码生成失败');
            }
        }
        
        //$config['background']=$bgpath;
        $poster = new Poster($config);
        $poster->generate([
            'appcode'=>$filename,
            'avatar'=>$this->user['avatar'],
            'bg'=>1,
            'nickname'=>$this->user['nickname']
        ]);
        $poster->save($sharepath);
    }
    
    public function rank($mode='month'){
        
        $list=AwardLogModel::ranks($mode);
        
        return $this->response(['ranks'=>$list]);
    }
    
    
    
    public function award_log($type='',$status=''){
        $model=Db::view('awardLog mlog','*')
            ->view('Member m',['username','level_id','nickname','avatar'],'m.id=mlog.from_member_id','LEFT')
            ->where('mlog.member_id',$this->user['id']);
        if(!empty($type) && $type!='all'){
            $model->where('mlog.type',$type);
        }
        if($status!==''){
            $model->where('mlog.status',$status);
        }
        
        $logs = $model->order('mlog.id DESC')->paginate(10);
        
        return $this->response([
            'logs'=>$logs->all(),
            'total'=>$logs->total(),
            'page'=>$logs->currentPage()
        ]);
    }
    
    public function orders($status='',$pagesize=10){
        $level = $this->userLevel();
        $sonids=getMemberSons($this->user['id'],$level['commission_layer']);
        $model=Db::view('Order','order_id,order_no,member_id,status,payamount,type,create_time')
            ->view('member',['username','nickname','avatar','level_id'],'Order.member_id=member.id')
            ->whereIn('Order.member_id',$sonids)
            ->where('Order.delete_time',0);
        if($status !== ''){
            $model->where('Order.status',intval($status));
        }
        $orders =$model->order('Order.status ASC,Order.create_time DESC')->paginate($pagesize);
        if(!empty($orders) && !$orders->isEmpty()) {
            $order_ids = array_column($orders->all(), 'order_id');
            $products = Db::view('OrderProduct', '*')
                ->view('Product', ['id' => 'orig_product_id', 'update_time' => 'orig_product_update'], 'OrderProduct.product_id=Product.id', 'LEFT')
                ->view('ProductSku', ['sku_id' => 'orig_sku_id', 'price' => 'orig_product_price'], 'ProductSku.sku_id=OrderProduct.sku_id', 'LEFT')
                ->whereIn('OrderProduct.order_id', $order_ids)
                ->select();
            $products=array_index($products,'order_id',true);
            
            $awards = Db::name('awardLog')->whereIn('order_id',$order_ids)->field('order_id,sum(amount) as commision')->group('order_id')->select();
            $awards = array_column($awards->all(),'commision','order_id');
            
            $orders->each(function($item) use ($products,$awards){
                $item['product_count']=isset($products[$item['order_id']])?array_sum(array_column($products[$item['order_id']],'count')):0;
                $item['products']=isset($products[$item['order_id']])?$products[$item['order_id']]:[];
                $item['commision'] = number_format(($awards[$item['order_id']]?:0) *.01,2);
                $item['create_date']=date('Y-m-d H:i:s',$item['create_time']);
                return $item;
            });
        }
        
        //$counts = OrderModel::getCounts($this->user['id']);
        return $this->response([
            'lists'=>$orders->all(),
            'page'=>$orders->currentPage(),
            'total'=>$orders->total(),
            'total_page'=>$orders->lastPage(),
            //'counts'=>$counts
        ]);
    }
    
    public function counts(){
        $counts = OrderModel::getCounts($this->user['id']);
        return $this->response($counts);
    }
    
    public function team($pid=0, $level=1){
        $levels=getMemberLevels();
        $curLevel=$levels[$this->user['level_id']];
        $maxlayer=$curLevel['commission_layer'];
        
        $model = Db::name('Member')->where('status',1);
        if($pid==0){
            $pid = $this->user['id'];
            if($level<2) {
                $model->where('referer',$pid);
            }else{
                if($level>$maxlayer){
                    $this->error('您只能查看'.$maxlayer.'层的会员');
                }
                $dbpre = config('database.prefix');
                $where = [];
                $sufix=[];
                while($level > 1){
                    $where[]= '`referer` IN( SELECT id FROM `'.$dbpre.'member` WHERE ';
                    $sufix[]=')';
                    $level--;
                }
                $condition=implode('',$where).'`referer`='.$pid.implode('',$sufix);
                $model->where(Db::raw($condition));
            }
        }elseif($pid!=$this->user['id']){
            $member=Db::name('Member')->find($pid);
            if(empty($member)){
                $this->error('会员不存在');
            }
            if(!$member['is_agent']){
                $this->error('会员不是代理');
            }
            $paths=[$member];
            while($member['id']!=$this->user['id']){
                $member=Db::name('Member')->find($member['referer']);
                $paths[]=$member;
                if(count($paths)>$maxlayer){
                    $this->error('您只能查看'.$maxlayer.'层的会员');
                }
            }
            //$paths=array_reverse($paths);
            //$this->assign('paths',$paths);
    
            $model->where('referer',$pid);
        }
        $users=$model->field('id,username,nickname,level_id,mobile,avatar,gender,is_agent,province,city,county')
            ->order('create_time DESC')->paginate(10);
        
        if(!empty($users) && !$users->isEmpty()) {
            $uids = array_column($users->all(), 'id');
            $soncounts = [];
            if (!empty($uids)) {
                $sondata = Db::name('Member')->whereIn('referer', $uids)
                    ->group('referer')->field('referer,COUNT(id) as count_member')->select();
                $soncounts = [];
                foreach ($sondata as $row) {
                    $soncounts[$row['referer']] = $row['count_member'];
                }
            }
            
            $users->each(function ($item) use ($soncounts,$levels) {
                if(isset($soncounts[$item['id']])) {
                    $item['soncount'] = $soncounts[$item['id']];
                }else{
                    $item['soncount'] = 0;
                }
                $item['level_name']=$levels[$item['level_id']]['level_name']?:'-';
                return $item;
            });
        }
        
        return $this->response([
            'users'=>$users->all(),
            'total'=>$users->total(),
            'page'=>$users->currentPage()
        ]);
    }
}