<?php
namespace app\common\model;
use think\Model;
class MemberModel extends Model{
    protected $_validate = array(
        array('username','require','请填写用户名！'),
        array('email','require','请填写邮箱！'),
        array('email','email','邮箱格式错误！'),
        array('mobile','mobile','手机号格式错误！'),
        array('password','require','请填写密码！','','',self::MODEL_INSERT),
     	array('repassword','password','确认密码不正确',0,'confirm'),
        array('username','','用户名已存在！',0,'unique',self::MODEL_BOTH),
        array('email','','邮箱已存在！',0,'unique',self::MODEL_BOTH),
        array('mobile','','手机号已存在！',0,'unique',self::MODEL_BOTH),
        array('staus',array(0,1),'请勿恶意修改字段',3,'in'),
        array('type',array(1,2,3,4,5,6),'请勿恶意修改字段',3,'in'),
    );

    protected $_auto = array(
        //array('password','encode_password',self::MODEL_BOTH,'callback') ,
        array('password','',self::MODEL_UPDATE,'ignore'),
        array('update_at','time',self::MODEL_BOTH,'function'),
        array('create_at','time',self::MODEL_INSERT,'function'),
        //array('login_ip','get_client_ip',self::MODEL_INSERT,'function')
    );


}