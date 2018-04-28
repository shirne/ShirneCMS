<?php
namespace app\admin\model;

use think\Model;

class PostModel extends Model
{
    protected $autoWriteTimestamp = true;

    protected $_validate = array(
        array('title','require','请填写文章标题！')
    );
    protected $_auto = array (
        array('user_id','getUid',1,'callback'),
    );
    protected function getUid(){
    	return session('adminId');
    }
}