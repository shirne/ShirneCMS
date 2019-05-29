<?php
namespace app\common\model;

use think\Db;

/**
 * Class SettingModel
 * @package app\admin\model
 */
class SettingModel extends BaseModel
{
    private static $cacheKey='setting';
    private static $settings;

    /**
     * 获取全部配置
     * @param $all bool 是否全部数据
     * @param $group bool 是否分组
     * @param $parse bool 是否解析值 针对多选解析成数组
     * @return array
     */
    public static function getSettings($all = false, $group = false, $parse=true)
    {
        if (empty(self::$settings)) {
            self::$settings = cache(self::$cacheKey);
            if (empty(self::$settings)) {
                self::$settings = Db::name('setting')->order('sort ASC,id ASC')->select();
                foreach (self::$settings as $k=>$v){
                    if($v['type']=='bool' && empty($v['data']))$v['data']="1:Yes\n0:No";
                    self::$settings[$k]['data']=self::parse_data($v['data']);
                }
                cache(self::$cacheKey, self::$settings);
            }
        }

        $return = array();
        if ($group) {
            foreach (self::$settings as $set) {
                if (empty($set['group'])) $set['group'] = 'common';
                if (!isset($return[$set['group']])) $return[$set['group']] = array();
                if($parse && $set['type']=='check') {
                    $set['value'] = @unserialize($set['value']);
                    if(empty($set['value']))$set['value']=array();
                }
                $return[$set['group']][$set['key']] = $all ? $set : $set['value'];
            }
        } else {
            foreach (self::$settings as $set) {
                if($parse && $set['type']=='check') {
                    $set['value'] = @unserialize($set['value']);
                    if(empty($set['value']))$set['value']=array();
                }
                $return[$set['key']] = $all ? $set : $set['value'];
            }
        }
        return $return;
    }

    public static function clearCache()
    {
        self::$settings = null;
        cache(self::$cacheKey, null);
    }

    public static function export($mode='simple')
    {
        $data=[];
        if($mode=='full'){
            $data['mode']='full';
            $data['date']=date('Y-m-d H:i:s');
            $data['data']=self::getSettings(true);
        }else{
            $data['mode']='simple';
            $data['date']=date('Y-m-d H:i:s');
            $data['data']=self::getSettings();
        }
        return json_encode($data,JSON_UNESCAPED_UNICODE);
    }
    public static function import($data)
    {
        $model=Db::name('setting');
        $settings=self::getSettings(false,false,true);
        if($data['mode']=='full'){
            foreach ($data['data'] as $k=>$v){
                if(is_array($v['value']))$v['value']=serialize($v['value']);
                if(isset($settings[$k])) {
                    if($settings[$k]!=$v)$model->where('key' , $k)->update(array('value' => $v['value']));
                }else{
                    $model->setOption('data',[]);
                    //if(is_array($v['data']))$v['data']=serialize($v['data']);
                    $model->insert(array(
                        'key'=>$k,
                        'title'=>$v['title'],
                        'type'=>$v['type'],
                        'group'=>$v['group'],
                        'sort'=>$v['sort'],
                        'value'=>$v['value'],
                        'description'=>$v['description'],
                        'data'=>self::serialize_data($v['data'])
                    ));
                }
            }
        }else{
            foreach ($data['data'] as $k=>$v){
                if(is_array($v))$v=serialize($v);
                if(isset($settings[$k])) {
                    if($settings[$k]!=$v)$model->where('key' , $k)->update(array('value' => $v));
                }else{
                    $model->setOption('data',[]);
                    $model->insert(array(
                        'key'=>$k,
                        'title'=>$k,
                        'type'=>'text',
                        'group'=>'advance',
                        'sort'=>0,
                        'value'=>$v,
                        'description'=>'',
                        'data'=>''
                    ));
                }
            }
        }
        self::clearCache();
        return true;
    }

    /**
     * 解析数据
     * @param $d
     * @return array
     */
    private static function parse_data($d)
    {
        if(empty($d))return array();
        $arr=array();
        $darr=preg_split('/[\n\r]+/',$d);
        foreach ($darr as $a){
            $idx=stripos($a,':');
            if($idx>0){
                $arr[substr($a,0,$idx)]=substr($a,$idx+1);
            }else{
                $arr[$a]=$a;
            }
        }
        return $arr;
    }

    /**
     * 序列化数据
     * @param $arr
     * @return string
     */
    private static function serialize_data($arr)
    {
        $str=[];
        foreach($arr as $k=>$v){
            if($k==$v){
                $str[]=$v;
            }else{
                $str[]=$k.':'.$v;
            }
        }
        return implode("\n",$str);
    }
}