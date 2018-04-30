<?php
namespace app\admin\controller;
use app\admin\model\SettingModel;
use app\index\validate\SettingValidate;
use think\Db;

/**
 * 字段管理
 */
class SettingController extends BaseController
{
    /**
     * 配置列表
     */
    public function index($group="")
    {
        if($this->request->isPost()){
            $this->checkPermision("setting_update");
            $data=$this->request->post();
            $model=Db::name('setting');
            $settings=getSettings(false,false,true);
            foreach ($data as $k=>$v){
                if(substr($k,0,2)=='v-'){
                    $key=substr($k,2);
                    if(is_array($v))$v=serialize($v);
                    if($settings[$key]!=$v) {
                        $model->where(array('key' => $key))->update(array('value' => $v));
                        
                    }
                }
            }
            cache('setting',null);
            user_log($this->mid,'sysconfig',1,'修改系统配置' ,'manager');
            $this->success('配置已更新',url('setting/index',array('group'=>$group)));
        }
        $this->assign('group',$group);
        $this->assign('groups', settingGroups());
        $this->assign('settings',getSettings(true,true));
        $this->display();
    }
    public function refresh(){
        cache('setting',null);
        $this->success("刷新成功",url('setting/index'));
    }

    public function advance($key=""){

        $model = Db::name('setting');
        $where=array();
        if(!empty($key)){
            $where['key'] = array('like',"%$key%");
            $where['description'] = array('like',"%$key%");
            $where['_logic'] = 'or';
        }

        $this->assign('key',$key);

        $setting  = $model->where($where)->paginate(15);// 查询满足要求的总记录数

        $this->assign('model', $setting);
        $this->assign('page',$setting->render());
        $this->display();
    }

    /**
     * 添加分类
     */
    public function edit($id=0)
    {
        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new SettingValidate();
            $validate->setId($id);
            if (!$validate->check($data)) {

                $this->error($validate->getError());
            } else {
                if($id>0){
                    $data['id']=$id;
                    if (SettingModel::update($data)) {
                        cache('setting',null);
                        $this->success("字段更新成功", url('setting/advance'));
                    } else {
                        $this->error("字段更新失败");
                    }
                }else {
                    if (SettingModel::create($data)) {
                        cache('setting', null);
                        $this->success("字段添加成功", url('setting/advance'));
                    } else {
                        $this->error("字段添加失败");
                    }
                }
            }
        }else{
            if($id>0){
                $model = Db::name('setting')->find($id);
            }else{
                $model=array();
            }
            $this->assign('model',$model);
            $this->assign('groups',settingGroups());
            $this->assign('types',settingTypes());
            $this->display();
        }
    }
    /**
     * 删除配置
     */
    public function delete($id)
    {
        $id = intval($id);
        $model = Db::name('setting');
 
        //验证通过
        $result = $model->delete($id);
        if($result){
            $this->success("字段删除成功", url('setting/advance'));
        }else{
            $this->error("字段删除失败");
        }
    }


}
