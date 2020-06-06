<?php

namespace app\admin\controller\wechat;

use EasyWeChat\OfficialAccount\Application;
use think\Db;
use think\Response;

/**
 * 素材管理
 * Class MaterialController
 * @package app\admin\controller\wechat
 */
class MaterialController extends WechatBaseController
{
    public function search($key='',$type=''){
        $model=Db::name('wechatMaterial');
        if(!empty($key)){
            $model->where('id|media_id|keyword|title','like',"%$key%");
        }
        if(!empty($type)){
            $model->where('type',$type);
        }
        
        $lists=$model->field('id,type,title,media_id,keyword,description')
            ->order('id ASC')->limit(10)->select();
        return json(['data'=>$lists,'code'=>1]);
    }
    
    /**
     * 素材管理
     * @param $key
     * @param string $type
     * @param int $page
     * @return mixed
     */
    public function index($key='',$type=''){
        if($this->request->isPost()){
            return redirect(url('',['wid'=>$this->wid,'key'=>base64url_encode($key),'type'=>$type]));
        }
        if(!$this->wechatApp instanceof Application){
            $this->error('该类型账号不支持素材管理功能');
        }
        $key=empty($key)?"":base64url_decode($key);
        $model = Db::name('wechatMaterial');
        
        if(!empty($key)){
            $model->whereLike('title|keyword',"%$key%");
        }
        if(!empty($type)){
            $model->where('type',$type);
        }
        $lists=$model->order('update_time DESC')->paginate(15);
        $this->assign('type',$type);
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
            try{
                $materials=$app->material->list($type,$count,20);
            }catch(\Exception $e){
                $this->apiException($e);
            }
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

    public function view($media_id){
        $media = Db::name('wechatMaterial')->where('media_id',$media_id)->find();

        if(empty($media)){
            $this->error('素材不存在，请先同步');
        }
        if($media['type'] == 'news'){
            $articles = Db::name('wechatMaterialArticle')->where('material_id',$media['id'])->select();
            $this->assign('articles',$articles);
        }

        $this->assign('media',$media);
        return $this->fetch();
    }

    public function thumb($media_id){
        $response = $this->wechatApp->material->get($media_id);

        return Response::create($response->getBody()->getContents(), 'image/jpeg');
    }

    /**
     * 素材删除
     * @param $media_id
     */
    public function delete($media_id){

        try{
            $this->wechatApp->material->delete($media_id);
        }catch(\Exception $e){
            $this->error($e);
        }
        Db::name('wechatMaterial')->where('media_id',$media_id)->delete();

        $this->success('删除成功');
    }
}