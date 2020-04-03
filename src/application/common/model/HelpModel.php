<?php
namespace app\common\model;

use app\common\core\ContentModel;
use app\common\facade\CategoryFacade;
use think\facade\Db;
use think\Paginator;

/**
 * Class HelpModel
 * @package app\common\model
 */
class HelpModel extends ContentModel
{
    protected $autoWriteTimestamp = true;
    protected $type = ['prop_data'=>'array'];

    function __construct($data = [])
    {
        parent::__construct($data);
        $this->cateFacade=CategoryFacade::getFacadeInstance();
        $this->searchFields = 'title|vice_title|description';
    }
    
    /**
     * @param array|Paginator $lists
     * @param array $attrs
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function afterTagList($lists,$attrs){
        if(!empty($lists)){
            
            
        }
        return $lists;
    }

    protected function afterTagItem($item,$attrs=[]){
        $item['digg']=$item['digg']+intval($item['v_digg']);
        $item['views']=$item['views']+intval($item['v_views']);
        return $item;
    }
}