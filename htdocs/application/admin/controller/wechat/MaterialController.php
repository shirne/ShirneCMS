<?php

namespace app\admin\controller\wechat;
use think\Db;

/**
 * 素材管理
 * Class MaterialController
 * @package app\admin\controller\wechat
 */
class MaterialController extends WechatBaseController
{
    /**
     * 素材管理
     * @param $key
     * @param string $type
     * @param int $page
     * @return mixed
     */
    public function index($key='',$type='news',$page=1){
        if($this->request->isPost()){
            return redirect(url('',['wid'=>$this->wid,'key'=>base64_encode($key),'type'=>$type]));
        }
        $key=empty($key)?"":base64_decode($key);
        $model = Db::name('wechatMaterial');
        $where=array();
        if(!empty($key)){
            $where[] = array('title|keyword','like',"%$key%");
        }
        $lists=$model->where($where)->order('update_time DESC')->paginate(15);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        return $this->fetch();
    }

    /**
     * 素材同步
     * @param $type
     */
    public function sync($type){
        $app=$this->wechatApp;

        $count=0;
        $totals=$app->material->stats();
        $total_count=$totals[$type.'_count'];
        while($count<$total_count){
            $materials=$app->material->list($type,$count,20);
            if(!empty($materials['item'])){
                foreach ($materials['item'] as $item){
                    $exist=Db::name('wechatMaterial')->where('media_id',$item['media_id'])->find();
                    $data=[
                        'type'=>$type,
                        'wechat_id'=>$this->wid,
                        'update_time'=>$item['update_time']
                    ];
                    if(in_array($type,['image','voice','video'])){
                        $data['url']=$item['url'];
                        $data['title']=$item['name'];
                    }
                    if($type=='news'){
                        $data['content']=json_encode($item['content'],JSON_UNESCAPED_UNICODE);
                    }
                    if(empty($exist)){
                        $data['media_id']=$item['media_id'];
                        Db::name('wechatMaterial')->insert($data);
                    }else{
                        Db::name('wechatMaterial')->where('id',$exist['id'])
                            ->update($data);
                    }
                }
            }

            $count += $materials['ITEM_COUNT'];
        }
        $this->success('同步成功');
    }

    /**
     * 素材删除
     * @param $media_id
     */
    public function delete($media_id){

        $this->wechatApp->material->delete($media_id);
        Db::name('wechatMaterial')->where('media_id',$media_id)->delete();

        $this->success('删除成功');
    }
}