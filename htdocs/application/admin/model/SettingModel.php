<?php
namespace app\admin\model;

use think\Model;

class SettingModel extends Model
{
    protected $_validate = array(
        array('key','require','请填写配置名！'), //默认情况下用正则进行验证
        array('title','require','请填写配置标题！'), //默认情况下用正则进行验证
        array('value','require','请填写配置值！'), //默认情况下用正则进行验证
        array('key','','配置名已经存在！',0,'unique',self::MODEL_BOTH), // 在新增的时候验证name字段是否唯一
    );
}