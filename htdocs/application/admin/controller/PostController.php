<?php
namespace app\admin\controller;
use app\admin\model\PostModel;
use app\index\validate\PostValidate;
use think\Db;

/**
 * 文章管理
 */
class PostController extends BaseController
{
    /**
     * 文章列表
     */
    public function index($key="")
    {
        $model = Db::view('post','*')->view('category',['name'=>'category_name','title'=>'category_title'],'post.cate_id=category.id','LEFT')
            ->view('manager',['username'],'post.user_id=manager.id','LEFT');
        $where=array();
        if(!empty($key)){
            $where['post.title'] = array('like',"%$key%");
            $where['manager.username'] = array('like',"%$key%");
            $where['category.title'] = array('like',"%$key%");
            $where['_logic'] = 'or';
        }

        $lists=$model->where($where)->paginate(10);

        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());

        return $this->fetch();
    }

    public function add(){
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate = new PostValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $uploaded = $this->upload('post', 'upload_cover', true);
                if (!empty($uploaded)) {
                    $data['cover'] = $uploaded['url'];
                }
                $data['user_id'] = $this->mid;
                if ($insert_id=PostModel::create($data)) {
                    user_log($this->mid,'addpost',1,'添加文章 '.$insert_id ,'manager');
                    $this->success("添加成功", url('post/index'));
                } else {
                    $this->error("添加失败");
                }
            }
        }
        $model=array('type'=>1);
        $this->assign("category",getSortedCategory(Db::name('category')->select()));
        $this->assign('post',$model);
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
            $validate=new PostValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }else{
                $uploaded=$this->upload('post','upload_cover',true);
                if(!empty($uploaded)){
                    $data['cover']=$uploaded['url'];
                }
                $data['id']=$id;
                if (PostModel::update($data)) {
                    user_log($this->mid, 'updatepost', 1, '修改文章 ' . $id, 'manager');
                    $this->success("编辑成功", url('post/index'));
                } else {
                    $this->error("编辑失败");
                }
            }
        }else{

            $model = Db::name('post')->find($id);
            if(empty($model)){
                $this->error('文章不存在');
            }
            $this->assign("category",getSortedCategory(Db::name('category')->select()));
            $this->assign('post',$model);
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
        $model = Db::name('post');
        $result = $model->where(["id"=>$id])->delete();
        if($result){
            user_log($this->mid,'deletepost',1,'删除文章 '.$id ,'manager');
            $this->success("删除成功", url('post/index'));
        }else{
            $this->error("删除失败");
        }
    }
	public function push($id) {//post到前台
		$id = intval($id);
        $status = Db::name('post')->where(["id"=>$id])->column('status');
        if ($status === '0') {
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }
        $result = Db::name('post')->where(["id"=>$id])->update($data);
        if ($result && $data['status'] === 1) {
            $this -> success("发布成功", url('post/index'));
        } elseif ($result && $data['status'] === 0) {
            $this -> success("撤销成功", url('post/index'));
        } else {
            $this -> error("操作失败");
        }
	}
}
