<?php
namespace app\common\model;

use app\common\core\ContentModel;
use app\common\facade\CategoryFacade;
use Overtrue\Pinyin\Pinyin;
use think\facade\Db;
use think\Paginator;

/**
 * Class ArticleModel
 * @package app\common\model
 */
class ArticleModel extends ContentModel
{
    protected $name = 'article';
    protected $autoWriteTimestamp = true;
    protected $type = ['prop_data'=>'array'];
    protected $auto = ['channel_id','name'];

    function __construct($data = [])
    {
        parent::__construct($data);
        $this->cateFacade=CategoryFacade::getFacadeInstance();
        $this->searchFields = 'title|vice_title|description';
    }

    public function setNameAttr($value)
    {
        if(!empty($value)){
            return $value;
        }
        $pinyin = new Pinyin();
        $value = $pinyin->permalink(trim($this->title),'');
        if(strlen($value) > 90){
            $value = substr($value, 0, 90);
        }
        $sufix = 0;
        $newValue = $value;
        while(Db::name('article')->where('name',$newValue)->count()>0){
            $sufix++;
            $newValue = $value .'_'.$sufix;
        }
        return $newValue;
    }

    public function setChannelIdAttr($value)
    {
        $topCate = CategoryFacade::getTopCategory($this->cate_id);
        return empty($topCate) ? intval($value) : $topCate['id'];
    }

    protected function tagBaseView($model){
        return $model->view($this->cateModel.' channel',
            ["title"=>"channel_title","name"=>"channel_name","short"=>"channel_short","icon"=>"channel_icon","image"=>"channel_image"],
            $this->model.".channel_id=channel.id",
            "LEFT"
        );
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
            $pids = array_column(is_array($lists)?$lists:$lists->all(),'id');
            if(!empty($attrs['withimgs'])){
                $imgs=Db::name('articleImages')->whereIn('article_id',$pids)->select();
                $imgs = array_index($imgs,'article_id',true);
                $lists = $this->appendTagData($lists,'imgs', $imgs);
            }
        }
        return $lists;
    }

    protected function afterTagItem($item,$attrs=[]){
        $item['digg']=$item['digg']+intval($item['v_digg']);
        $item['views']=$item['views']+intval($item['v_views']);
        return $item;
    }
}