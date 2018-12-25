<?php

namespace app\index\controller\member;


use app\common\validate\MemberAddressValidate;
use think\Db;

/**
 * 地址控制器
 * Class AddressController
 * @package app\index\controller\member
 */
class AddressController extends BaseController
{
    public function index(){
        if($this->request->isPost()){
            $data=$this->request->only('id','post');
            $result=Db::name('MemberAddress')->where('member_id',$this->userid)
                ->whereIn('address_id',idArr($data['id']))->delete();
            if($result){
                user_log($this->userid,'addressdel',1,'删除收货地址:'.$data['id']);
                $this->success('删除成功！');
            }else{
                $this->error('删除失败！');
            }
        }
        $addressed=Db::name('MemberAddress')->where('member_id',$this->userid)->select();
        $this->assign('addressed',$addressed);
        return $this->fetch();
    }

    public function add(){
        if($this->request->isPost()){
            $data=$this->request->only('recive_name,mobile,province,city,area,address,code,is_default','post');
            $data['is_default']=empty($data['is_default'])?0:1;
            $validate=new MemberAddressValidate();
            if(!$validate->check($data)){
                $this->error($validate->getError());
            }else {
                $data['member_id'] = $this->userid;
                $id = Db::name('MemberAddress')->insert($data, false, true);
                if ($id) {
                    user_log($this->userid, 'addressadd', 1, '添加收货地址:' . $id);
                    $this->success('添加成功', aurl('index/member.address'), Db::name('MemberAddress')->find($id));
                } else {
                    $this->error('添加失败');
                }
            }
        }
        $address=[];
        $count=Db::name('MemberAddress')->where('member_id',$this->userid)->count();
        if($count<1){
            $address['is_default']=1;
        }
        $this->assign('address',$address);
        return $this->fetch('edit');
    }
    public function edit($id){
        $address = Db::name('MemberAddress')
            ->where('member_id',$this->userid)
            ->where('address_id',$id)->find();
        if(empty($address)){
            $this->error('地址资料不存在');
        }
        if($this->request->isPost()){
            $data=$this->request->only('recive_name,mobile,province,city,area,address,code,is_default','post');
            $data['is_default']=empty($data['is_default'])?0:1;
            $validate=new MemberAddressValidate();
            if(!$validate->check($data)){
                $this->error($validate->getError());
            }else{
                $result=Db::name('MemberAddress')->where('member_id',$this->userid)
                    ->where('address_id',$id)->update($data);
                if($result){
                    user_log($this->userid,'addressedit',1,'修改收货地址:'.$id);
                    $this->success('修改成功',aurl('index/member.address'));
                }else{
                    $this->error('修改失败');
                }
            }

        }

        $this->assign('address',$address);
        return $this->fetch();
    }
}