<?php
namespace app\common\model;

use app\common\core\BaseModel;
use think\Db;
use think\facade\Log;

/**
 * Class CategoryModel
 * @package app\common\model
 */
class CategoryModel extends BaseModel
{
    protected $precache='';

    protected $type = ['props'=>'array', 'fields'=>'array'];

    protected $data;
    protected $treed;

    protected function _get_data(){
        return Db::name('Category')->order('pid ASC,sort ASC,id ASC')->select();
    }

    public function getCategories($force=false){
        if(empty($this->data) || $force){
            $this->data=cache($this->precache.'categories');
            if(empty($this->data) || $force){
                $tmpdata = $this->_get_data();
                $this->data=getSortedCategory($tmpdata);
                cache($this->precache.'categories',$this->data);
            }
        }
        return $this->data;
    }

    public function findCategoryByAttr($attr, $value){
        $this->getCategories();
        foreach ($this->data as $cate){
            if($cate[$attr]==$value){
                return $cate;
            }
        }
        return NULL;
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
    public function findCategories($idornames){
        $this->getCategories();
        if(!is_array($idornames)){
            $idornames = array_map('trim', explode(',', $idornames));
        }
        $cates = [];
        foreach ($this->data as $cate){
            if(in_array($cate['id'], $idornames) || in_array($cate['name'], $idornames)){
                $cates[] = $cate;
            }
        }
        return $cates;
    }
    public function getCategoryId($idorname){
        if(preg_match('/^[a-zA-Z]\w*$/',$idorname)) {
            $cate = $this->findCategory( $idorname);
            if (!empty($cate)) {
                return $cate['id'];
            } else {
                return 0;
            }
        }
        
        return intval($idorname);
    }

    public function getCategoryIds($idornames){
        $cates = $this->findCategories( $idornames );
        if (!empty($cates)) {
            return array_column($cates, 'id');
        }

        return [];
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
            $this->treed[-1]=[];
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

    public function getSubCateNames($pid, $recursive=false, $includeSelf = true)
    {
        $names = [];
        $treedCategories=$this->getTreedCategory();
        if($includeSelf){
            $cates = $this->findCategories($pid);
            $names = array_column($cates, 'name');
        }

        if(is_array($pid)){
            foreach($pid as $p){
                if(isset($treedCategories[$p]) && !empty($treedCategories[$p])) {
                    $sons = $treedCategories[$p];
                    $sonids = array_column($sons, 'id');
                    $sonNames = array_column($sons, 'name');
                    $names = array_merge($names, $sonNames);
                    if($recursive){
                        $names = array_merge($names, $this->getSubCateNames($sonids, true, false));
                    }
                }
            }
        }else{
            if(isset($treedCategories[$pid]) && !empty($treedCategories[$pid])) {
                $sons = $treedCategories[$pid];
                $sonids = array_column($sons, 'id');
                $sonNames = array_column($sons, 'name');
                $names = array_merge($names, $sonNames);
                if($recursive){
                    $names = array_merge($names, $this->getSubCateNames($sonids, true, false));
                }
            }
        }

        return $names;
    }

    public function getSubCateIds($pid, $recursive=false, $includeSelf = true)
    {
        $ids = [];
        $treedCategories = $this->getTreedCategory();
        if($includeSelf){
            $ids = (array)$pid;
        }

        if(is_array($pid)){
            foreach($pid as $p){
                if(isset($treedCategories[$p]) && !empty($treedCategories[$p])) {
                    $sons = $treedCategories[$p];
                    $sonids = array_column($sons, 'id');
                    $ids = array_merge($ids, $sonids);
                    if($recursive){
                        $ids = array_merge($ids, $this->getSubCateIds($sonids, true, false));
                    }
                }
            }
        }else{
            if(isset($treedCategories[$pid]) && !empty($treedCategories[$pid])) {
                $sons = $treedCategories[$pid];
                $sonids = array_column($sons, 'id');
                $ids = array_merge($ids, $sonids);
                if($recursive){
                    $ids = array_merge($ids, $this->getSubCateIds($sonids, true, false));
                }
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