<?php
namespace app\index\controller;

use app\common\model\AdvGroupModel;
use think\Db;

class IndexController extends BaseController
{
    public function index()
    {

        $this->seo();
        return $this->fetch();
    }


    private $xml='<xml><ToUserName><![CDATA[oia2TjjewbmiOUlr6X-1crbLOvLw]]></ToUserName><FromUserName><![CDATA[gh_7f083739789a]]></FromUserName><CreateTime>1407743423</CreateTime><MsgType><![CDATA[video]]></MsgType><Video><MediaId><![CDATA[eYJ1MbwPRJtOvIEabaxHs7TX2D-HV71s79GUxqdUkjm6Gs2Ed1KF3ulAOA9H1xG0]]></MediaId><Title><![CDATA[testCallBackReplyVideo]]></Title><Description><![CDATA[testCallBackReplyVideo]]></Description></Video></xml>';
    private $appid='10238852149';
    public function info(){
        phpinfo();
    }

    /**
     * 测试aes加密解密算法
     * @return mixed
     */
    public function test(){
        $pc = new \sdk\Prpcrypt('abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG');

        $encode=$this->request->post('encode');

        if(empty($encode)){
            return var_export($pc->encrypt($this->xml,$this->appid));
        }else{
            return var_export($pc->decrypt($encode,$this->appid));
        }
        //Db::execute('ALTER TABLE `sa_member_money_log` ADD `field` varchar(30) DEFAULT \'money\'');

    }

    /**
     * 测试openssl aes加密解密算法
     * @return mixed
     */
    public function test2(){

        $pc = new \sdk\AESEnctypt('abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG');

        $encode=$this->request->post('encode');

        if(empty($encode)){
            $return=$pc->encrypt($this->xml,$this->appid);
        }else{
            $return=$pc->decrypt($encode,$this->appid);
        }
        return $return?$return:openssl_error_string();
    }

}
