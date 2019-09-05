<?php
namespace app\common\model;

use app\common\core\ContentModel;
use app\common\facade\CategoryFacade;
use think\Db;
use think\Paginator;

/**
 * Class ArticleModel
 * @package app\common\model
 */
class ArticleModel extends ContentModel
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
            $pids = array_column(is_array($lists)?$lists:$lists->items(),'id');
            if(!empty($attrs['withimgs'])){
                $imgs=Db::name('articleImages')->whereIn('product_id',$pids)->select();
                $imgs = array_index($imgs,'product_id',true);
                if($lists instanceof Paginator){
                    $lists->each(function ($item)use($imgs){
                        if(isset($imgs[$item['id']])){
                            $item['imgs']=$imgs[$item['id']];
                        }else{
                            $item['imgs']=[];
                        }
                        return $item;
                    });
                }else{
                    foreach ($lists as &$item){
                        if(isset($imgs[$item['id']])){
                            $item['imgs']=$imgs[$item['id']];
                        }else{
                            $item['imgs']=[];
                        }
                    }
                    unset($item);
                }
            }
        }
        return $lists;
    }
}