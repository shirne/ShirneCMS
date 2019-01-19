<?php
namespace app\common\model;

use think\Db;
use think\facade\Log;

/**
 * Class CategoryModel
 * @package app\common\model
 */
class CategoryModel extends BaseModel
{
    protected $precache='';

    protected $data;
    protected $treed;

    protected function _get_data(){
        return Db::name('Category')->order('pid ASC,sort ASC,id ASC')->select();
    }

    public function getCategories($force=false){
        if(empty($this->data) || $force){
            $this->data=cache($this->precache.'categories');
            if(empty($this->data) || $force){
                $this->data=getSortedCategory($this->_get_data());
                cache($this->precache.'categories',$this->data);
            }
        }
        return $this->data;
    }

    public function findCategory($idorname){
        $this->getCategories();
        foreach ($this->data as $cate){
            if($cate['id']==$idorname || $cate['name']==$idorname){
                return $cate;
            }
        }
        return NULL;
    }
    public function getCategoryId($idorname){
        if(preg_match('/^[a-zA-Z]\w*$/',$idorname)) {
            $cate = $this->findCategory( $idorname);
            if (!empty($cate)) {
                return $cate['id'];
            } else {
                return 0;
            }
        }else{
            return intval($idorname);
        }
    }

    public function getCategoryTree($idorname){
        $this->getCategories();
        $tree=array();
        while($idorname!='0'){
            $current=$this->findCategory($idorname);
            if(empty($current)){
                Log::record('Category error at '.$idorname.'\'s parent');
                break;
            }
            array_unshift($tree,$current);
            $idorname=$current['pid'];
            if($idorname=='0')break;
        }

        return $tree;
    }

    public function getTopCategory($idorname)
    {
        $this->getCategories();
        $current=[];
        while($idorname!='0'){
            $current=$this->findCategory($idorname);
            if(empty($current)){
                Log::record('Category error at '.$idorname.'\'s parent');
                $current=[];
                break;
            }

            $idorname=$current['pid'];

            if($idorname=='0')break;
        }

        return $current;
    }

    public function getTreedCategory($force=false)
    {
        if(empty($this->treed)){
            $this->treed=cache($this->precache.'categorietree');
        }
        if(empty($this->treed) || $force==true){
            $data=$this->getCategories($force);
            $this->treed=array('0'=>[]);
            foreach ($data as $cate){
                $this->treed[$cate['pid']][]=$cate;
            }
            cache($this->precache.'categorietree',$this->treed);
        }
        return $this->treed;
    }

    public function getSubCategory($pid=0){
        $treedCategories=$this->getTreedCategory();
        if(isset($treedCategories[$pid])){
            return $treedCategories[$pid];
        }
        return [];
    }

    public function getSubCateIds($pid,$recursive=false)
    {
        $ids=[];
        $treedCategories=$this->getTreedCategory();

        if(is_array($pid)){
            if(!$recursive)$ids=$pid;
            foreach($pid as $p){
                if(isset($treedCategories[$p]) && !empty($treedCategories[$p])) {
                    $sons = $treedCategories[$p];
                    $sonids = array_column($sons, 'id');
                    $ids = array_merge($ids, $sonids);
                    $ids = array_merge($ids, $this->getSubCateIds($sonids, true));
                }
            }
        }else{
            if(!$recursive)$ids=[$pid];
            if(isset($treedCategories[$pid]) && !empty($treedCategories[$pid])) {
                $sons = $treedCategories[$pid];
                $sonids = array_column($sons, 'id');
                $ids = array_merge($ids, $sonids);
                $ids = array_merge($ids, $this->getSubCateIds($sonids, true));
            }
        }

        return $ids;
    }

    public function clearCache()
    {
        cache($this->precache.'categorietree',null);
        cache($this->precache.'categories',null);
    }
}