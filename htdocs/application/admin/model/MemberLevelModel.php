<?php
/**
 * 会员组
 * User: shirne
 * Date: 2018/4/11
 * Time: 9:01
 */

namespace app\admin\model;

use think\Model;

class MemberLevelModel extends Model{
    protected $pk="level_id";

    protected $_validate = array(
        array('level_name','require','请填写名称！')
    );
    protected $_auto = array (
        array('commission_percent','joinPercent',self::MODEL_BOTH,'callback')
    );

    protected function joinPercent($data){
        if(is_array($data)){
            return implode(',',$data);
        }
        return '';
    }

    protected function _after_find(&$result, $options)
    {
        parent::_after_find($result, $options);
        $result['commission_percent']=explode(',',$result['lead_percent']);
        $layerCount=count($result['commission_percent']);
        if($layerCount<$result['commission_layer']){
            for($i=0;$i<$result['commission_layer']-$layerCount;$i++) {
                $result['commission_percent'][] = '';
            }
        }
    }

    protected function _after_update($data, $options)
    {
        parent::_after_update($data, $options);
        if($data['is_default']){
            $this->_set_default($data['level_id']);
        }
    }
    protected function _after_insert($data, $options)
    {
        parent::_after_insert($data, $options);
        if($data['is_default']){
            $this->_set_default($this->getLastInsID());
        }
    }
    protected function _set_default($current_id){
        $this->where(array('level_id'=>array('NEQ',$current_id)))
            ->save(array('is_default'=>0));
    }
}