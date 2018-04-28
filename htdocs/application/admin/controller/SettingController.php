<?php
namespace app\admin\controller;
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
        if(IS_POST){
            $this->checkPermision("setting_update");
            $data=I();
            $model=M('setting');
            $settings=getSettings(false,false,true);
            foreach ($data as $k=>$v){
                if(substr($k,0,2)=='v-'){
                    $key=substr($k,2);
                    if(is_array($v))$v=serialize($v);
                    if($settings[$key]!=$v) {
                        $model->where(array('key' => $key))->save(array('value' => $v));
                        
                    }
                }
            }
            S('setting',null);
            user_log($this->mid,'sysconfig',1,'修改系统配置' ,'manager');
            $this->success('配置已更新',U('setting/index',array('group'=>$group)));
        }
        $this->assign('group',$group);
        $this->assign('groups', settingGroups());
        $this->assign('settings',getSettings(true,true));
        $this->display();
    }
    public function refresh(){
        S('setting',null);
        $this->success("刷新成功",U('setting/index'));
    }

    public function advance($key=""){
        if(empty($key)){
            $model = M('setting');
        }else{
            $where['key'] = array('like',"%$key%");
            $where['description'] = array('like',"%$key%");
            $where['_logic'] = 'or';
            $model = M('setting')->where($where);
        }

        $this->assign('key',$key);

        $count  = $model->where($where)->count();// 查询满足要求的总记录数
        $Page = new \Extend\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出
        $setting = $model->limit($Page->firstRow.','.$Page->listRows)->where($where)->order('id ASC')->select();
        $this->assign('model', $setting);
        $this->assign('page',$show);
        $this->display();
    }

    /**
     * 添加分类
     */
    public function add()
    {
        if (IS_POST) {
            //如果用户提交数据
            $model = D("Setting");
            if (!$model->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($model->getError());
                exit();
            } else {
                if ($model->add()) {
                    S('setting',null);
                    $this->success("字段添加成功", U('setting/advance'));
                } else {
                    $this->error("字段添加失败");
                }
            }
        }else{
            $this->assign('groups',settingGroups());
            $this->assign('types',settingTypes());
            $this->display();
        }
    }
    /**
     * 更新分类信息
     */
    public function update()
    {
        if (IS_POST) {
            $model = D("Setting");
            if (!$model->create()) {
                $this->error($model->getError());
            }else{
                if ($model->save()) {
                    S('setting',null);
                    $this->success("字段更新成功", U('setting/advance'));
                } else {
                    $this->error("字段更新失败".$model->getLastSql());
                }        
            }
        }else{
            $model = M('setting')->find(I('id/d'));
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
        $model = M('setting');
 
        //验证通过
        $result = $model->delete($id);
        if($result){
            $this->success("字段删除成功", U('setting/advance'));
        }else{
            $this->error("字段删除失败");
        }
    }


}
