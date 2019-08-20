<?php

namespace app\api\controller\member;


use app\api\Controller\AuthedController;
use app\common\validate\MemberAddressValidate;
use think\Db;

class AddressController extends AuthedController
{
    public function index(){
        $lists=Db::name('memberAddress')
            ->where('member_id',$this->user['id'])
            ->select();
        return $this->response($lists);
    }

    public function view($id){
        $id=intval($id);
        $address=Db::name('memberAddress')
            ->where('member_id',$this->user['id'])
            ->where('address_id',$id)
            ->find();
        return $this->response($address);
    }
    
    public function save($id=0){
        $data=$this->input['address'];
        $data['is_default']=empty($data['is_default'])?0:1;
        $validate=new MemberAddressValidate();
        if(!$validate->check($data)){
            $this->error($validate->getError(),0);
        }else{
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
    public function delete($id){
        $id=intval($id);
        $deleted=Db::name('memberAddress')
            ->where('member_id',$this->user['id'])
            ->where('address_id',$id)
            ->delete();
        if($deleted){
            $this->success('删除成功');
        }else{
            $this->success('删除失败');
        }
    }
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