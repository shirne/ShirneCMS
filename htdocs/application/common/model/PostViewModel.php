<?php 
namespace app\common\model;

use think\model\ViewModel;

class PostViewModel extends ViewModel {
   public $viewFields = array(
     'post'=>array('id','title','cover','content','user_id','cate_id','time','type','status'),
     'category'=>array('name'=>'category_name','title'=>'category_title', '_on'=>'post.cate_id=category.id'),
     'manager'=>array('username', '_on'=>'post.user_id=manager.id'),
   );
 }

//