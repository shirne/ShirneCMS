<?php 
namespace app\common\model;

use think\Model;

class OAuthViewModel extends Model {
   public $viewFields = array(
     'member'=>array('*'),
     'member_oauth'=>array('type','type_id','openid','unionid','nickname'=>'auth_nickname', 'gender'=>'auth_nickname','avatar'=>'auth_nickname','province'=>'auth_nickname','city'=>'auth_nickname','country'=>'auth_nickname','is_follow','_on'=>'member_oauth.member_id=member.id'),
   );
 }

//end file