<?php
namespace app\admin\model;

use app\common\model\BaseModel;
use think\Db;
use think\Exception;

/**
 * Class ManagerModel
 * @package app\admin\model
 */
class ManagerModel extends BaseModel
{
    protected $autoWriteTimestamp = true;
    
    public static function get_parents($id, $full=false){
        $model=Db::name('manager');
        $data=[];
        $pids=[];
        $manage=$model->where('id',$id)->find();
        while(!empty($manage)){
            $pid = $manage['pid'];
            if(!$pid || $pid==$manage['id'])break;
            if(in_array($pid,$pids)){
                //获取注册时间最早的，pid设为1
                $oldest=$model->removeOption()->whereIn('id',$pids)->order('create_time ASC')->find();
                if($oldest['id']==SUPER_ADMIN_ID){
                    $model->where('id',$oldest['id'])->update(['pid'=>0]);
                }else{
                    $model->where('id',$oldest['id'])->update(['pid'=>SUPER_ADMIN_ID]);
                }
                //重新获取
                return self::get_parents($id,$full);
            }
            $data[]=$full?$manage:$pid;
            $pids[]=$pid;
            $manage=$model->removeOption()->where('id',$pid)->find();
        }
        return $pids;
    }
    
    /**
     * 检测指定的账号是否对本账号有编辑权限
     * @param $id
     * @return bool
     * @throws Exception
     */
    public function hasPermission($id){
        if($id == SUPER_ADMIN_ID || $this->id == $id)return true;
        $toManager = Db::name('manager')->where('id',$id)->find();
        if(empty($toManager))return false;
        
        if($this->type < $toManager['type'])return false;
        
        return $this->isProgeny($id);
    }
    
    /**
     * 检测当前账号，是否指定id的后代
     * @param $pid
     * @return bool
     */
    public function isProgeny($pid){
        if($pid == SUPER_ADMIN_ID || $this->id == $pid)return true;
        $pids = self::get_parents($this->id);
        if(in_array($pid,$pids)){
            return true;
        }
        return false;
    }
}