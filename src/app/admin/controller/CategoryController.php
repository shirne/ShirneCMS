<?php

namespace app\admin\controller;

use app\admin\validate\CategoryValidate;
use app\common\facade\CategoryFacade;
use Overtrue\Pinyin\Pinyin;
use think\facade\Db;

/**
 * 文章分类管理
 */
class CategoryController extends BaseController
{
    /**
     * 分类列表
     */
    public function index()
    {

        $this->assign('model',CategoryFacade::getCategories(true));
        return $this->fetch();
    }

    /**
     * 添加
     * @param int $pid
     * @return mixed
     */
    public function add($pid=0){
        $pid=intval($pid);
        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new CategoryValidate();
            $validate->setId();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $iconupload=$this->upload('category','upload_icon');
                if(!empty($iconupload))$data['icon']=$iconupload['url'];
                elseif($this->uploadErrorCode>102){
                    $this->error($this->uploadErrorCode.':'.$this->uploadError);
                }
                $uploaded=$this->upload('category','upload_image');
                if(!empty($uploaded))$data['image']=$uploaded['url'];
                elseif($this->uploadErrorCode>102){
                    delete_image($data['icon']);
                    $this->error($this->uploadErrorCode.':'.$this->uploadError);
                }

                $result=Db::name('category')->insert($data);
                if ($result) {
                    CategoryFacade::clearCache();
                    $this->success(lang('Add success!'), url('category/index'));
                } else {
                    delete_image([$data['icon'],$data['image']]);
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $cate = CategoryFacade::getCategories();
        $model=array('sort'=>99,'pid'=>$pid,'use_template'=>0);
        $this->assign('cate',$cate);
        $this->assign('model',$model);
        $this->assign('id',0);
        return $this->fetch('edit');
    }

    /**
     * 编辑
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new CategoryValidate();
            $validate->setId($id);

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $delete_images=[];
                $iconupload=$this->upload('category','upload_icon');
                if(!empty($iconupload)){
                    $data['icon']=$iconupload['url'];
                    $delete_images[]=$data['delete_icon'];
                }
                $uploaded=$this->upload('category','upload_image');
                if(!empty($uploaded)){
                    $data['image']=$uploaded['url'];
                    $delete_images[]=$data['delete_image'];
                }
                unset($data['delete_icon']);
                unset($data['delete_image']);

                $result=Db::name('category')->where('id',$id)->update($data);

                if ($result) {
                    delete_image($delete_images);
                    CategoryFacade::clearCache();
                    $this->success(lang('Update success!'), url('category/index'));
                } else {
                    delete_image([$data['icon'],$data['image']]);
                    $this->error(lang('Update failed!'));
                }
            }
        }else{
            $model = Db::name('category')->find($id);
            if(empty($model)){
                $this->error('分类不存在');
            }
            $cate = CategoryFacade::getCategories();

            $this->assign('cate',$cate);
            $this->assign('model',$model);
            $this->assign('id',$id);
            return $this->fetch();
        }
    }

    public function batch($pid=0){
        $content = $this->request->post('content');
        $rows = explode("\n",$content);
        $datas = [];
        $names = Db::name('category')->field('name')->select();
        $names = array_column($names,'name');
        $pinyin=new Pinyin();
        $sort = 0;
        if($pid>0){
            $sort = Db::name('category')->where('pid', $pid)->max('sort')+1;
        }else{
            $sort = Db::name('category')->max('sort')+1;
        }
        foreach($rows as $item){
            $item = trim($item);
            if(empty($item))continue;
            $fields = explode(' ',$item);
            $fieldCount=count($fields);
            $data=['pid'=>$pid,'sort'=>$sort++];
            if($fieldCount > 2){
                $data['title']=trim($fields[0]);
                $data['short']=trim($fields[1]);
                $data['name']=trim($fields[2]);
            }elseif($fieldCount > 1){
                $data['title']=trim($fields[0]);
                $data['short']=trim($fields[0]);
                $data['name']=trim($fields[1]);
            }else{
                $data['title']=trim($fields[0]);
                $data['short']=trim($fields[0]);
                $data['name']= $pinyin->permalink(trim($fields[0]),'');
            }
            if(in_array($data['name'],$names)){
                $parts = explode('_',$data['name']);
                $partCount = count($parts);
                if(count($parts)>1){
                    $parts[$partCount-1] += 1;
                    while(((in_array(implode('_',$parts),$names)))){
                        $parts[$partCount-1] += 1;
                    }
                }else{
                    $parts[] = 1;
                    while(((in_array(implode('_',$parts),$names)))){
                        $parts[$partCount] += 1;
                    }
                }
                $data['name'] = implode('_',$parts);
            }
            $names[]=$data['name'];
            
            $datas[]=$data;
            unset($data);
        }
        if(!empty($datas)){
            Db::name('category')->insertAll($datas);
            $this->success('添加成功');
        }
        $this->error('未提交数据');
    }

    /**
     * 删除分类
     * @param $id
     */
    public function delete($id)
    {
        $id = idArr($id);
        //查询属于这个分类的文章
        $posts = Db::name('Article')->where('cate_id','in',$id)->count();
        if($posts){
            $this->error("禁止删除含有文章的分类");
        }
        //禁止删除含有子分类的分类
        $hasChild = Db::name('Category')->where('pid','in',$id)->count();
        if($hasChild){
            $this->error("禁止删除含有子分类的分类");
        }
        //验证通过
        $result = Db::name('Category')->where('id','in',$id)->delete();
        if($result){
            CategoryFacade::clearCache();
            $this->success(lang('Delete success!'), url('category/index'));
        }else{
            $this->error(lang('Delete failed!'));
        }
    }
}
