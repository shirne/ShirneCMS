<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirn
 * Date: 2016/9/10
 * Time: 11:46
 */

namespace app\index\controller;


class AuthedController extends BaseController
{
    public function initialize(){
        parent::initialize();

    }

    public function checkLogin()
    {
        parent::checkLogin();

        //如果没有的登录 重定向至登录页面
        if(empty($this->userid ) ) {
            redirect()->remember();
            $this->error('请先登录',url('index/login/index'));
        }

    }

    protected function uploadFile($folder,$field,$isreturn=false,$is_img=false){
        $uploadpath='/uploads/';
        $config=array(
            'maxSize'       =>  2000000, //上传的文件大小限制 (0-不做限制)
            'exts'          =>  $is_img?array('jpg','jpeg','png','gif','bmp','tif'):array('jpg','jpeg','png','gif','bmp','tif','txt','csv','xls','doc','zip','json'), //允许上传的文件后缀
            'rootPath'      =>  '.'.$uploadpath, //保存根路径
            'savePath'      =>  $folder.'/', //保存路径
        );
        $file = $this->request->file($field);
        if(empty($file)){
            return false;
        }

        $info = $file->validate(['size'=>$config['maxSize'],'ext'=>$config['exts']])->rule(function() use ($config){
            return $config['savePath'].date('Y/m/').md5(microtime(true));
        })->move( $config['rootPath']);
        if($info){
            $upload=array();
            $upload['url']=$uploadpath.$info->getSaveName();
            return $upload;
        }else{
            $this->errMsg=$file->getError();
            if($isreturn)return false;
            $this->error($this->errMsg);
        }
    }

    protected function upload($folder,$field,$isreturn=false){
        return $this->uploadFile($folder,$field,$isreturn,true);
    }
}