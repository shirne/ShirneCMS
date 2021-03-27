<?php

namespace app\admin\validate;

use app\common\core\BaseUniqueValidate;
use think\Validate;

/**
 * 文章资料验证
 * Class ArticleValidate
 * @package app\admin\validate
 */
class ArticleValidate extends BaseUniqueValidate
{
    protected $rule=array(
        'title'=>'require|max:100',
        'name'=>'unique:article,%id%',
        'description'=>'max:250'
    );
    protected $message=array(
        'title.require'=>'请填写文章标题',
        'title.max'=>'文章标题不能超过100个字符',
        'name.unique'=>'URL名称已存在',
        'description.max'=>'简介长度不能超过250'
    );

}