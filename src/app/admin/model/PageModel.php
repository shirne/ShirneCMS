<?php
namespace app\admin\model;

use app\common\core\BaseModel;
use think\facade\Db;

/**
 * Class PageModel
 * @package app\admin\model
 */
class PageModel extends BaseModel
{
    protected $name = 'page';
    protected $autoWriteTimestamp = true;
    
    public static function init(){
        self::event('after_write', function ($page) {
            if (!empty($page['group'])) {
                $group=$page['group'];
                $exists=Db::name('PageGroup')->where('group',$group)->find();
                if(empty($exists)){
                    Db::name('PageGroup')->insert([
                        'group_name'=>$group,
                        'group'=>$group,
                        'sort'=>99
                    ]);
                    cache('page_group',null);
                }
            }
        });
    }
}