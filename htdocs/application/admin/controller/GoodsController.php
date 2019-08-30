<?php
/**
 * 商品管理
 * User: shirne
 * Date: 2018/5/11
 * Time: 17:47
 */

namespace app\admin\controller;


use app\admin\model\SpecificationsModel;
use app\common\model\GoodsModel;
use app\common\model\GoodsSkuModel;
use app\admin\validate\GoodsSkuValidate;
use app\admin\validate\GoodsValidate;
use app\admin\validate\ImagesValidate;
use app\common\facade\GoodsCategoryFacade;
use think\Db;

class GoodsController extends BaseController
{
    public function index($key='',$cate_id=0){
        if($this->request->isPost()){
            return redirect(url('',['cate_id'=>$cate_id,'key'=>base64_encode($key)]));
        }
        $key=empty($key)?"":base64_decode($key);
        $model = Db::view('goods','*')
            ->view('goodsCategory',['name'=>'category_name','title'=>'category_title'],'goods.cate_id=goodsCategory.id','LEFT');

        if(!empty($key)){
            $model->whereLike('goods.title|goodsCategory.title',"%$key%");
        }
        if($cate_id>0){
            $model->whereIn('goods.cate_id',GoodsCategoryFacade::getSubCateIds($cate_id));
        }

        $lists=$model->order('create_time DESC')->paginate(10);

        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        $this->assign('keyword',$key);
        $this->assign('cate_id',$cate_id);
        $this->assign("category",GoodsCategoryFacade::getCategories());

        return $this->fetch();
    }
    
    public function set_increment($incre){
        $this->setAutoIncrement('goods',$incre);
    }

    public function add($cid=0){
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate = new GoodsValidate();
            $validate->setId();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $delete_images=[];
                $uploaded = $this->upload('goods', 'upload_image');
                if (!empty($uploaded)) {
                    $data['image'] = $uploaded['url'];
                    $delete_images[]=$data['delete_image'];
                }elseif($this->uploadErrorCode>102){
                    $this->error($this->uploadErrorCode.':'.$this->uploadError);
                }
                unset($data['delete_image']);
                $data['user_id'] = $this->mid;

                $data['price']=floatval($data['price']);

                $data['storage']=intval($data['storage']);
                if(!empty($data['prop_data'])){
                    $data['prop_data']=array_combine($data['prop_data']['keys'],$data['prop_data']['values']);
                }else{
                    $data['prop_data']=[];
                }

                $model=GoodsModel::create($data);
                if ($model['id']) {
                    //delete_image($delete_images);

                    user_log($this->mid,'addgoods',1,'添加商品 '.$model->id ,'manager');
                    $this->success("添加成功", url('Goods/index'));
                } else {
                    delete_image($data['image']);
                    $this->error("添加失败");
                }
            }
        }
        $model=array('type'=>1,'status'=>1,'cate_id'=>$cid,'sale'=>0);
        $this->assign("category",GoodsCategoryFacade::getCategories());
        $this->assign('goods',$model);
        $this->assign('levels',getMemberLevels());
        $this->assign('id',0);
        return $this->fetch('edit');
    }

    /**
     * 更新商品信息
     */
    public function edit($id)
    {
        $id = intval($id);

        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new GoodsValidate();
            $validate->setId($id);
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }else{
                $delete_images=[];
                $uploaded=$this->upload('goods','upload_image');
                if(!empty($uploaded)){
                    $data['image']=$uploaded['url'];
                    $delete_images[]=$data['delete_image'];
                }elseif($this->uploadErrorCode>102){
                    $this->error($this->uploadErrorCode.':'.$this->uploadError);
                }
                $model=GoodsModel::get($id);
                //$skus=$data['skus'];
                if(!empty($data['prop_data'])){
                    $data['prop_data']=array_combine($data['prop_data']['keys'],$data['prop_data']['values']);
                }else{
                    $data['prop_data']=[];
                }
                $data['price']=floatval($data['price']);

                $data['storage']=intval($data['storage']);
                if(empty($data['levels']))$data['levels']=[];
                if ($model->allowField(true)->save($data)) {
                    //delete_image($delete_images);

                    user_log($this->mid, 'updategoods', 1, '修改商品 ' . $id, 'manager');
                    $this->success("编辑成功", url('goods/index'));
                } else {
                    delete_image($data['image']);
                    $this->error("编辑失败");
                }
            }
        }

        $model = GoodsModel::get($id);
        if(empty($model)){
            $this->error('商品不存在');
        }

        $this->assign("category",GoodsCategoryFacade::getCategories());
        $this->assign('levels',getMemberLevels());
        $this->assign('goods',$model);
        $this->assign('id',$id);
        return $this->fetch();
    }

    /**
     * 删除商品
     */
    public function delete($id)
    {
        $model = Db::name('goods');
        $result = $model->where('id','in',idArr($id))->delete();
        if($result){

            Db::name('goodsImages')->where('goods_id','in',idArr($id))->delete();
            user_log($this->mid,'deletegoods',1,'删除商品 '.$id ,'manager');
            $this->success("删除成功", url('Goods/index'));
        }else{
            $this->error("删除失败");
        }
    }
    public function push($id,$type=0)
    {
        $data['status'] = $type==1?1:0;

        $result = Db::name('goods')->where('id','in',idArr($id))->update($data);
        if ($result && $data['status'] === 1) {
            user_log($this->mid,'pushgoods',1,'上架商品 '.$id ,'manager');
            $this -> success("上架成功", url('Goods/index'));
        } elseif ($result && $data['status'] === 0) {
            user_log($this->mid,'cancelgoods',1,'下架商品 '.$id ,'manager');
            $this -> success("下架成功", url('Goods/index'));
        } else {
            $this -> error("操作失败");
        }
    }

    /**
     * 图集
     * @param $aid
     * @return mixed
     */
    public function imagelist($aid){
        $model = Db::name('GoodsImages');
        $goods=Db::name('Goods')->find($aid);
        if(empty($goods)){
            $this->error('产品不存在');
        }
        $model->where('goods_id',$aid);
        if(!empty($key)){
            $model->where('title','like',"%$key%");
        }
        $lists=$model->order('sort ASC,id DESC')->paginate(15);
        $this->assign('goods',$goods);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        $this->assign('aid',$aid);
        return $this->fetch();
    }

    public function imageadd($aid){
        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new ImagesValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }else{
                $uploaded=$this->upload('goods','upload_image');
                if(!empty($uploaded)){
                    $data['image']=$uploaded['url'];
                }
                $model = Db::name("GoodsImages");
                $url=url('goods/imagelist',array('aid'=>$aid));
                if ($model->insert($data)) {
                    $this->success("添加成功",$url);
                } else {
                    delete_image($data['image']);
                    $this->error("添加失败");
                }
            }
        }
        $model=array('status'=>1,'goods_id'=>$aid);
        $this->assign('model',$model);
        $this->assign('aid',$aid);
        $this->assign('id',0);
        return $this->fetch('imageupdate');
    }

    /**
     * 添加/修改
     */
    public function imageupdate($id)
    {
        $id = intval($id);

        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new ImagesValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }else{
                $model = Db::name("GoodsImages");
                $url=url('goods/imagelist',array('aid'=>$data['goods_id']));
                $delete_images=[];
                $uploaded=$this->upload('goods','upload_image');
                if(!empty($uploaded)){
                    $data['image']=$uploaded['url'];
                    $delete_images[]=$data['delete_image'];
                }
                unset($data['delete_image']);
                $data['id']=$id;
                if ($model->update($data)) {
                    delete_image($delete_images);
                    $this->success("更新成功", $url);
                } else {
                    delete_image($data['image']);
                    $this->error("更新失败");
                }
            }
        }

        $model = Db::name('GoodsImages')->where('id', $id)->find();
        if(empty($model)){
            $this->error('图片不存在');
        }

        $this->assign('model',$model);
        $this->assign('aid',$model['goods_id']);
        $this->assign('id',$id);
        return $this->fetch();
    }
    /**
     * 删除图片
     */
    public function imagedelete($aid,$id)
    {
        $id = intval($id);
        $model = Db::name('GoodsImages');
        $result = $model->delete($id);
        if($result){
            $this->success("删除成功", url('goods/imagelist',array('aid'=>$aid)));
        }else{
            $this->error("删除失败");
        }
    }

}