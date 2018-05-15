<?php
/**
 * 商品管理
 * User: shirne
 * Date: 2018/5/11
 * Time: 17:47
 */

namespace app\admin\controller;


use app\admin\model\ProductModel;
use app\admin\validate\ProductValidate;
use app\common\facade\ProductCategoryModel;
use think\Db;

class ProductController extends BaseController
{
    public function index($key='',$cate_id=0){
        $model = Db::view('product','*')
            ->view('productCategory',['name'=>'category_name','title'=>'category_title'],'product.cate_id=productCategory.id','LEFT');
        $where=array();
        if(!empty($key)){
            $where[]=['product.title|productCategory.title','like',"%$key%"];
        }
        if($cate_id>0){
            $where[]=['product.cate_id','in',ProductCategoryModel::getSubCateIds($cate_id)];
        }

        $lists=$model->where($where)->paginate(10);

        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        $this->assign('types',getProductTypes());
        $this->assign('keyword',$key);
        $this->assign('cate_id',$cate_id);
        $this->assign("category",ProductCategoryModel::getCategories());

        return $this->fetch();
    }

    public function add($cid=0){
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate = new ProductValidate();
            $validate->setId();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $delete_images=[];
                $uploaded = $this->upload('article', 'upload_cover', true);
                if (!empty($uploaded)) {
                    $data['cover'] = $uploaded['url'];
                    $delete_images[]=$data['delete_cover'];
                }
                unset($data['delete_cover']);
                $data['user_id'] = $this->mid;
                $model=ProductModel::create($data);
                if ($model->id) {
                    delete_image($delete_images);
                    user_log($this->mid,'addproduct',1,'添加商品 '.$model->id ,'manager');
                    $this->success("添加成功", url('Article/index'));
                } else {
                    $this->error("添加失败");
                }
            }
        }
        $model=array('type'=>1,'cate_id'=>$cid);
        $this->assign("category",ProductCategoryModel::getCategories());
        $this->assign('article',$model);
        $this->assign('types',getProductTypes());
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
            $validate=new ProductValidate();
            $validate->setId($id);
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }else{
                $delete_images=[];
                $uploaded=$this->upload('article','upload_cover',true);
                if(!empty($uploaded)){
                    $data['cover']=$uploaded['url'];
                    $delete_images[]=$data['delete_cover'];
                }
                $model=ProductModel::get($id);
                if ($model->allowField(true)->save($data)) {
                    delete_image($delete_images);
                    user_log($this->mid, 'updateproduct', 1, '修改商品 ' . $id, 'manager');
                    $this->success("编辑成功", url('product/index'));
                } else {
                    $this->error("编辑失败");
                }
            }
        }else{

            $model = Db::name('product')->find($id);
            if(empty($model)){
                $this->error('商品不存在');
            }
            $this->assign("category",ProductCategoryModel::getCategories());
            $this->assign('product',$model);
            $this->assign('types',getProductTypes());
            $this->assign('id',$id);
            return $this->fetch();
        }
    }
    /**
     * 删除商品
     */
    public function delete($id)
    {
        $model = Db::name('product');
        $result = $model->where("id",'in',idArr($id))->delete();
        if($result){
            user_log($this->mid,'deleteproduct',1,'删除商品 '.$id ,'manager');
            $this->success("删除成功", url('Product/index'));
        }else{
            $this->error("删除失败");
        }
    }
    public function push($id,$type=0)
    {
        $data['status'] = $type==1?1:0;

        $result = Db::name('product')->where("id",'in',idArr($id))->update($data);
        if ($result && $data['status'] === 1) {
            user_log($this->mid,'pusharticle',1,'上架商品 '.$id ,'manager');
            $this -> success("上架成功", url('Product/index'));
        } elseif ($result && $data['status'] === 0) {
            user_log($this->mid,'cancelproduct',1,'下架商品 '.$id ,'manager');
            $this -> success("下架成功", url('Product/index'));
        } else {
            $this -> error("操作失败");
        }
    }


    /**
     * 商品评论
     * @param int $id
     * @return \think\Response
     */
    public function comments($id=0,$key=''){
        $model = Db::view('productComment','*')
            ->view('member',['username','level_id','avatar'],'member.id=productComment.member_id','LEFT')
            ->view('product',['title'=>'article_title','cate_id','cover'],'article.id=productComment.article_id','LEFT')
            ->view('productCategory',['name'=>'category_name','title'=>'category_title'],'product.cate_id=productCategory.id','LEFT');
        $where=array();
        if($id>0){
            $where[]=['product_id',$id];
        }
        if(!empty($key)){
            $where[]=['product.title|productCategory.title','like',"%$key%"];
        }

        $lists=$model->where($where)->paginate(10);

        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        $this->assign('keyword',$key);
        $this->assign('product_id',$id);
        $this->assign("category",ProductCategoryModel::getCategories());

        return $this->fetch();
    }

    public function commentview($id){
        $model=Db::name('productComment')->find('id');
        if(empty($model)){
            $this->error('评论不存在');
        }
        if($this->request->isPost()){
            $data=$this->request->post();
            $data['reply_time']=$id;
            $data['reply_user_id']=$this->mid;
            Db::name('productComment')->where('id',$id)->update($data);
            $this->success('回复成功');

        }
        $product=Db::name('product')->find($model['product_id']);
        $category=Db::name('productCategory')->find($product['cate_id']);
        $member=Db::name('member')->find($model['member_id']);

        $this->assign('model',$model);
        $this->assign('product',$product);
        $this->assign('category',$category);
        $this->assign('member',$member);
        return $this->fetch();
    }

    public function commentstatus($id,$type=1)
    {
        $data['status'] = $type==1?1:2;

        $result = Db::name('productComment')->where("id",'in',idArr($id))->update($data);
        if ($result && $data['status'] === 1) {
            user_log($this->mid,'auditproductcomment',1,'审核评论 '.$id ,'manager');
            $this -> success("审核成功", url('Product/comments'));
        } elseif ($result && $data['status'] === 2) {
            user_log($this->mid,'hideproductcomment',1,'隐藏评论 '.$id ,'manager');
            $this -> success("评论已隐藏", url('Product/comments'));
        } else {
            $this -> error("操作失败");
        }
    }
    public function commentdelete($id)
    {
        $model = Db::name('productComment');
        $result = $model->where("id",'in',idArr($id))->delete();
        if($result){
            user_log($this->mid,'deleteproductcomment',1,'删除评论 '.$id ,'manager');
            $this->success("删除成功", url('Product/comments'));
        }else{
            $this->error("删除失败");
        }
    }
}