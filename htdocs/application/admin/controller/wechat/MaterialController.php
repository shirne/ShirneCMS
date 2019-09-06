<?php

namespace app\admin\controller\wechat;
use EasyWeChat\OfficialAccount\Application;
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
    public function index($key='',$type='news'){
        if($this->request->isPost()){
            return redirect(url('',['wid'=>$this->wid,'key'=>base64_encode($key),'type'=>$type]));
        }
        if(!$this->wechatApp instanceof Application){
            $this->error('该类型账号不支持素材管理功能');
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
    public function sync($type, $count=0){
        $app=$this->wechatApp;

        $totals=$app->material->stats();
        $total_count=$totals[$type.'_count'];
        if($count<$total_count){
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
                    }else{
                        $data['title']=$item['content']['news_item'][0]['title'];
                    }
                    if(empty($exist)){
                        $data['media_id']=$item['media_id'];
                        $result = Db::name('wechatMaterial')->insert($data,false,true);
                        $exist = ['id'=>$result];
                    }else{
                        $result = Db::name('wechatMaterial')->where('id',$exist['id'])
                            ->update($data);
                    }
    
                    if($result && $type=='news'){
                        $content=is_array($item['content'])?$item['content']:json_encode($item['content'],JSON_UNESCAPED_UNICODE);
                        $exists =Db::name('wechatMaterialArticle')->where('material_id',$exist['id'])->select();
                        $exists = array_index($exists,'title');
                        $updateids=[];
                        foreach ($content['news_item'] as $news){
                            $news['update_time']=time();
                            if(isset($exists[$news['title']])){
                                Db::name('wechatMaterialArticle')->where('id',$exists[$news['title']]['id'])
                                    ->update($news);
                                $updateids[]=$exists[$news['title']]['id'];
                            }else{
                                $news['wechat_id'] = $this->wid;
                                $news['material_id'] = $exist['id'];
                                $news['create_time']=time();
                                $aid = Db::name('wechatMaterialArticle')->insert($news,false,true);
                                $updateids[]=$aid;
                            }
                        }
                        Db::name('wechatMaterialArticle')->where('wechat_id',$this->wid)
                            ->where('material_id',$exist['id'])
                            ->whereNotIn('id',$updateids)->delete();
                    }
                }
            }
            if($materials['item_count']>0) {
                $count += $materials['item_count'];
                $this->success('已同步 ' . $count . '/' . $total_count, url('wechat.material/sync', ['type' => $type, 'count' => $count ]));
            }
        }
        $this->success('同步成功 '.$total_count);
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