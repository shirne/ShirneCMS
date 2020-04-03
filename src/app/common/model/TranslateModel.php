<?php

namespace app\common\model;


use app\common\core\BaseModel;
use think\facade\Db;
use think\facade\Lang;

/**
 * 多语言转换表
 * Class TranslateModel
 * @package app\common\model
 */
class TranslateModel extends BaseModel
{
    protected $data;
    protected $loaded=false;
    protected $loadedLang=[];

    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->table='lang';
    }

    public function loadAll(){
        if(!$this->loaded) {
            $result = Db::name($this->table)->select();
            $data = [];
            foreach ($result as $item) {
                $data[$item['lang']][$item['table']][$item['key_id']][$item['field']] = $item['value'];
            }
            $this->data = $data;
        }
        return $this->data;
    }

    public function loadLang($lang){
        if(!$this->loadedLang[$lang] && !$this->loaded) {
            $model = Db::name($this->table)->where('lang', $lang);
            $result = $model->select();
            $data = [];
            foreach ($result as $item) {
                $data[$item['table']][$item['key_id']][$item['field']] = $item['value'];
            }
            $this->data[$lang] = $data;
            $this->loadedLang[$lang]=true;
        }
        return $this->data[$lang];
    }

    public function loadTable($table,$lang=''){
        if($lang==='')$lang=Lang::range();
        if(!isset($this->data[$lang][$table])) {
            $model = Db::name($this->table)->where('table', $table)
                ->where('lang', $lang);
            $result = $model->select();
            $data = [];
            foreach ($result as $item) {
                $data[$item['key_id']][$item['field']] = $item['value'];
            }
            $this->data[$lang][$table] = $data;
        }
        return $this->data[$lang][$table];
    }

    public function get_trans($table,$key='',$field='',$lang=''){
        if($lang==='')$lang=Lang::range();
        if($this->loaded || $this->loadedLang[$lang]){
            $data=$this->data[$lang][$table];
        }else {
            $model = Db::name($this->table)->where('table', $table)
                ->where('lang', $lang);
            if ($key !== '') {
                $model->where('key_id', $key);
            }
            if ($field !== '') {
                $model->where('field', $field);
            }
            $result = $model->select();
            if (empty($result)) {
                if ($key !== '' && $field !== '') {
                    return '';
                } else {
                    return [];
                }
            }
            $data = [];
            foreach ($result as $item) {
                $data[$item['key_id']][$item['field']] = $item['value'];
            }
        }
        if($key!=='' && $field!==''){
            return $data[$key][$field];
        }
        return $data;
    }

    public function trans_list($data,$table,$key_id='id',$lang=''){
        if($lang==='')$lang=Lang::range();

        $trans=$this->get_trans($table,'','',$lang);
        if(!empty($trans)){
            foreach ($data as $k=>$row){
                if(isset($trans[$row[$key_id]]) && is_array($trans[$row[$key_id]])) {
                    $data[$k] = array_merge($row, $trans[$row[$key_id]]);
                }
            }
        }
        return $data;
    }
}