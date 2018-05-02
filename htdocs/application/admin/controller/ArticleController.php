<?php
namespace app\admin\controller;
use app\admin\model\ArticleModel;
use app\admin\validate\ArticleValidate;
use think\Db;

/**
 * 文章管理
 */
class ArticleController extends BaseController
{
    /**
     * 文章列表
     */
    public function index($key="")
    {
        $model = Db::view('article','*')->view('category',['name'=>'category_name','title'=>'category_title'],'article.cate_id=category.id','LEFT')
            ->view('manager',['username'],'article.user_id=manager.id','LEFT');
        $where=array();
        if(!empty($key)){
            $where[]=['article.title|manager.username|category.title','like',"%$key%"];
        }

        $lists=$model->where($where)->paginate(10);

        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        $this->assign('keyword',$key);

        return $this->fetch();
    }

    public function add(){
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate = new ArticleValidate();

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
                $model=ArticleModel::create($data);
                if ($model->id) {
                    delete_image($delete_images);
                    user_log($this->mid,'addarticle',1,'添加文章 '.$model->id ,'manager');
                    $this->success("添加成功", url('Article/index'));
                } else {
                    $this->error("添加失败");
                }
            }
        }
        $model=array('type'=>1);
        $this->assign("category",getSortedCategory(Db::name('category')->select()));
        $this->assign('article',$model);
        $this->assign('id',0);
        return $this->fetch('edit');
    }

    /**
     * 更新文章信息
     */
    public function edit($id)
    {
        $id = intval($id);

        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new ArticleValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }else{
                $delete_images=[];
                $uploaded=$this->upload('article','upload_cover',true);
                if(!empty($uploaded)){
                    $data['cover']=$uploaded['url'];
                    $delete_images[]=$data['delete_cover'];
                }
                $model=ArticleModel::get($id);
                if ($model->allowField(true)->save($data)) {
                    delete_image($delete_images);
                    user_log($this->mid, 'updatearticle', 1, '修改文章 ' . $id, 'manager');
                    $this->success("编辑成功", url('Article/index'));
                } else {
                    $this->error("编辑失败");
                }
            }
        }else{

            $model = Db::name('article')->find($id);
            if(empty($model)){
                $this->error('文章不存在');
            }
            $this->assign("category",getSortedCategory(Db::name('category')->select()));
            $this->assign('article',$model);
            $this->assign('id',$id);
            return $this->fetch();
        }
    }
    /**
     * 删除文章
     */
    public function delete($id)
    {
        $id = intval($id);
        $model = Db::name('article');
        $result = $model->where(["id"=>$id])->delete();
        if($result){
            user_log($this->mid,'deletearticle',1,'删除文章 '.$id ,'manager');
            $this->success("删除成功", url('Article/index'));
        }else{
            $this->error("删除失败");
        }
    }
	public function push($id) {
		$id = intval($id);
        $status = Db::name('article')->where(["id"=>$id])->column('status');
        if ($status === '0') {
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }
        $result = Db::name('article')->where(["id"=>$id])->update($data);
        if ($result && $data['status'] === 1) {
            $this -> success("发布成功", url('Article/index'));
        } elseif ($result && $data['status'] === 0) {
            $this -> success("撤销成功", url('Article/index'));
        } else {
            $this -> error("操作失败");
        }
	}
}
