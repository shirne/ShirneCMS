<?php

namespace app\admin\controller\wechat;


use app\admin\controller\BaseController;
use EasyWeChat\Factory;
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
     * @var \EasyWeChat\OfficialAccount\Application
     */
    protected $wechatApp=null;

    public function initialize()
    {
        parent::initialize();

        $this->wechatApp = $this->get_app($this->request->get('wid'));
    }

    /**
     * @param $wid
     * @return \EasyWeChat\OfficialAccount\Application
     */
    private function get_app($wid){
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

        return Factory::officialAccount([
            'token'=>$wechat['token'],
            'app_id'=>$wechat['appid'],
            'secret'=>$wechat['appsecret'],
            'aes_key'=>$wechat['encodingaeskey']
        ]);
    }
}