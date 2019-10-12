<?php

namespace app\admin\controller\wechat;


use app\admin\controller\BaseController;
use app\common\model\WechatModel;
use EasyWeChat\Kernel\ServiceContainer;
use think\Db;

/**
 * 公众号控制器基类
 * Class WechatBaseController
 * @package app\admin\controller\wechat
 */
class WechatBaseController extends BaseController
{
    protected $wid=0;
    protected $currentWechat=null;

    /**
     * @var ServiceContainer
     */
    protected $wechatApp=null;

    public function initialize()
    {
        parent::initialize();
        $wid = $this->request->param('wid/d');
        if(!$wid){
            $wid=cookie('wechat_id');
            $wid=intval($wid);
        }else{
            cookie('wechat_id',$wid);
        }
        if($wid) {
            $this->wid = $wid;
            $this->wechatApp = $this->get_app($wid);
        }
    }

    /**
     * @param $wid
     * @return \EasyWeChat\OfficialAccount\Application
     */
    protected function get_app($wid){
        if(is_array($wid)){
            $wechat=$wid;
        }else {
            $wechat = Db::name('Wechat')->where('id', $wid)->find();
            if(empty($wechat)){
                $this->error('公众号信息不存在');
            }
        }
        $this->wid=$wechat['id'];
        $this->currentWechat=$wechat;
        $this->assign('wechat',$this->currentWechat);
        $this->assign('wid',$wid);
        
        return WechatModel::createApp($wechat);
    }
}