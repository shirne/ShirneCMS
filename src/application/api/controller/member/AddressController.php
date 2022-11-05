<?php

namespace app\api\controller\member;


use app\api\controller\AuthedController;
use app\common\validate\MemberAddressValidate;
use think\Db;

/**
 * 会员地址管理接口
 * @package app\api\controller\member
 */
class AddressController extends AuthedController
{
    /**
     * 会员地址列表
     * @return Json 
     */
    public function index(){
        $lists=Db::name('memberAddress')
            ->where('member_id',$this->user['id'])
            ->paginate(10);
        return $this->respList($lists);
    }

    /**
     * 会员地址详细
     * @param int $id 
     * @return Json 
     */
    public function view($id){
        $id=intval($id);
        $address=Db::name('memberAddress')
            ->where('member_id',$this->user['id'])
            ->where('address_id',$id)
            ->find();
        return $this->response($address);
    }
    
    /**
     * 会员地址保存
     * @param int $id 为0时新增地址
     * @return void 
     */
    public function save($id=0){
        $data=$this->request->param('address');
        $data['is_default']=empty($data['is_default'])?0:1;
        $validate=new MemberAddressValidate();
        if(!$validate->check($data)){
            $this->error($validate->getError(),0);
        }else{
            // 如果会员地区未设置，则同步到会员资料中
            if(empty($this->user['province'])){
                Db::name('member')->where('id',$this->user['id'])->update([
                    'province'=>$data['province'],
                    'city'=>$data['city'],
                    'county'=>$data['area'],
                ]);
            }
            if($id>0){
                $result=Db::name('MemberAddress')->where('member_id',$this->user['id'])
                    ->where('address_id',$id)->update($data);
                if($result){
                    user_log($this->user['id'],'addressedit',1,'修改收货地址:'.$id);
                    $this->success('修改成功');
                }else{
                    $this->error('修改失败',0);
                }
            }else{
                $data['member_id']=$this->user['id'];
                $id=Db::name('MemberAddress')->insert($data,false,true);
                if($id){
                    user_log($this->user['id'],'addressadd',1,'添加收货地址:'.$id);
                    $this->success('添加成功');
                }else{
                    $this->error('添加失败',0);
                }
            }
        }

    }

    /**
     * 删除地址
     * @param int|string|array $id 
     * @return void 
     */
    public function delete($id){
        $id=idArr($id);
        $deleted=Db::name('memberAddress')
            ->where('member_id',$this->user['id'])
            ->whereIn('address_id',$id)
            ->delete();
        if($deleted){
            $this->success('删除成功');
        }else{
            $this->success('删除失败');
        }
    }

    /**
     * 设置默认地址
     * @param int $id 
     * @return void 
     */
    public function set_default($id){
        $id=intval($id);
        $updated=Db::name('memberAddress')
            ->where('member_id',$this->user['id'])
            ->where('address_id',$id)
            ->update(['is_default'=>1]);
        if($updated){
            Db::name('memberAddress')
                ->where('member_id',$this->user['id'])
                ->where('address_id','NEQ',$id)
                ->update(['is_default'=>0]);
            $this->success('设置成功');
        }else{
            $this->success('设置失败');
        }
    }
}