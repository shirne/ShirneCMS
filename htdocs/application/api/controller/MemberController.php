<?php

namespace app\api\Controller;


use app\common\model\MemberFavouriteModel;
use app\common\validate\MemberAddressValidate;
use app\common\validate\MemberValidate;
use extcore\traits\Upload;
use think\Db;

/**
 * 会员操作接口
 * Class MemberController
 * @package app\api\Controller
 */
class MemberController extends AuthedController
{
    use Upload;

    public function profile(){
        $profile=Db::name('member')
            ->hidden('password,salt,sec_password,sec_salt,delete_time')
            ->where('id',$this->user['id'])
            ->find();
        return $this->response($profile);
    }

    public function update_profile(){
        $data=$this->request->only(['realname','email','mobile','gender','birth','qq','wechat','alipay'],'put');
        if(!empty($data['birth']) && $data['birth']!='') {
            $data['birth'] = strtotime($data['birth']);
        }else{
            unset($data['birth']);
        }
        $validate=new MemberValidate();
        $validate->setId($this->user['id']);
        if(!$validate->scene('edit')->check($data)){
            $this->error($validate->getError(),0);
        }else{
            $data['id']=$this->user['id'];
            Db::name('Member')->update($data);
            user_log($this->user['id'],'addressadd',1,'修改个人资料');
            $this->success('保存成功');
        }
    }

    public function avatar(){
        $data=[];
        $uploaded=$this->upload('avatar','upload_avatar');
        if(empty($uploaded)){
            $this->error('请选择文件',0);
        }
        $data['avatar']=$uploaded['url'];
        $result=Db::name('Member')->where('id',$this->user['id'])->update($data);
        if($result){
            if(!empty($this->user['avatar']))delete_image($this->user['avatar']);
            user_log($this->user['id'], 'avatar', 1, '修改头像');
            $this->success('更新成功');
        }else{
            $this->error('更新失败',0);
        }
    }

    public function uploadImage(){
        $uploaded=$this->upload('member','file_upload');

        return $this->response([
            'url'=>$uploaded['url']
        ]);
    }

    public function addresses(){
        $lists=Db::name('memberAddress')
            ->where('member_id',$this->user['id'])
            ->select();
        return $this->response($lists);
    }

    public function get_address($id){
        $id=intval($id);
        $address=Db::name('memberAddress')
            ->where('member_id',$this->user['id'])
            ->where('address_id',$id)
            ->find();
        return $this->response($address);
    }
    public function edit_address($id=0){
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
    public function del_address($id){
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
    public function set_default_address($id){
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

    public function change_password(){
        $password=$this->request->post('password');
        if(!compare_password($this->user,$password)){
            $this->error('密码输入错误',0);
        }

        $newpassword=$this->request->post('newpassword');
        $salt=random_str(8);
        $data=array(
            'password'=>encode_password($newpassword,$salt),
            'salt'=>$salt
        );
        Db::name('Member')->where('id',$this->user['id'])->update($data);
        $this->success('密码修改成功');
    }

    public function sec_password(){

    }

    public function orders($status=''){
        $model=Db::name('Order')->where('member_id',$this->user['id'])
            ->where('delete_time',0);
        if($status>0){
            $model->where('status',$status-1);
        }
        $orders =$model->order('status ASC,create_time DESC')->paginate();
        if(!empty($orders) && !$orders->isEmpty()) {
            $order_ids = array_column($orders->items(), 'order_id');
            $products = Db::view('OrderProduct', '*')
                ->view('Product', ['id' => 'orig_product_id', 'update_time' => 'orig_product_update'], 'OrderProduct.product_id=Product.id', 'LEFT')
                ->view('ProductSku', ['sku_id' => 'orig_sku_id', 'price' => 'orig_product_price'], 'ProductSku.sku_id=OrderProduct.sku_id', 'LEFT')
                ->whereIn('OrderProduct.order_id', $order_ids)
                ->select();
            $products=array_index($products,'order_id',true);
            $orders->each(function($item) use ($products){
                $item['products']=isset($products[$item['order_id']])?$products[$item['order_id']]:[];
                return $item;
            });
        }

        $countlist=Db::name('Order')->where('member_id',$this->user['id'])
            ->group('status')->field('status,count(order_id) as order_count')->paginate(10);
        $counts=[0,0,0,0,0,0,0];
        foreach ($countlist as $row){
            $counts[$row['status']]=$row['order_count'];
        }
        return $this->response([
            'lists'=>$orders->items(),
            'page'=>$orders->currentPage(),
            'count'=>$orders->total(),
            'total_page'=>$orders->lastPage(),
            'counts'=>$counts
        ]);
    }

    public function order_view($id){
        $order=Db::name('Order')->where('order_id',intval($id))->find();
        if(empty($order) || $order['delete_time']>0){
            $this->error('订单不存在或已删除',0);
        }
        $order['products']=Db::view('OrderProduct', '*')
            ->view('Product', ['id' => 'orig_product_id', 'update_time' => 'orig_product_update'], 'OrderProduct.product_id=Product.id', 'LEFT')
            ->view('ProductSku', ['sku_id' => 'orig_sku_id', 'price' => 'orig_product_price'], 'ProductSku.sku_id=OrderProduct.sku_id', 'LEFT')
            ->where('OrderProduct.order_id', $order['order_id'])
            ->select();
        return $this->response($order);
    }

    public function favourite($type){

    }

    public function add_favourite($type,$id){
        $model=new MemberFavouriteModel();
        if($model->addFavourite($this->user['id'],$type,$id)){
            $this->success('已添加收藏');
        }else{
            $this->error($model->getError());

        }
    }

    public function del_favourite($type,$ids){
        $model=Db::name('memberFavourite')
        ->where('member_id',$this->user['id']);
        if(empty($type)){
            $model->whereIn('id',idArr($ids));
        }else{
            $model->where('fav_type',$type)
            ->whereIn('fav_id',idArr($ids));
        }
        $model->delete();
        $this->success('已移除收藏');
    }
}