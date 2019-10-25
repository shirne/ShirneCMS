<?php

namespace extcore\traits;

use think\captcha\Captcha;
use shirne\third\GeetestLib;

trait Verify{

    protected function verify_auto($scene, $setting){
        if($setting['captcha_mode']==1){
            return $this->verify_geetest($scene, $setting);
        }else{
            return $this->verify_code($scene);
        }
    }
    protected function check_verify_auto($scene, $data, $setting){
        if($setting['captcha_mode']==1){
            return $this->check_verify_geetest($scene, $data, $setting);
        }else{
            return $this->check_verify_code($scene, $data['verify']);
        }
    }

    protected function verify_code($scene){
        $verify = new Captcha();
        //$Verify->codeSet = '0123456789';
        $verify->seKey=config('session.sec_key');
        $verify->fontSize = 28;
        $verify->length = 4;
        return $verify->entry($scene);
    }

    protected function check_verify_code($scene,$code){
        $verify = new Captcha();
        $verify->seKey=config('session.sec_key');
        return $verify->check($code,$scene);
    }

    protected function verify_geetest($scene, $setting){
        $GtSdk = new GeetestLib($setting['captcha_geeid'], $setting['captcha_geekey']);

        $data = array(
            "user_id" => $scene.session_id(),
            //web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
            "client_type" => request()->isMobile()?'h5':"web",
            "ip_address" => request()->ip()
        );

        $status = $GtSdk->pre_process($data, 1);
        session($scene.'gtserver', $status);
        return Json(json_decode($GtSdk->get_response_str(),true));

    }

    protected function check_verify_geetest($scene, $post, $setting){

        $GtSdk = new GeetestLib($setting['captcha_geeid'], $setting['captcha_geekey']);
        $data = array(
            "user_id" => $scene.session_id(),
            "client_type" => request()->isMobile()?'h5':"web",
            "ip_address" => request()->ip()
        );


        if (session($scene.'gtserver') == 1) {   //服务器正常
            $result = $GtSdk->success_validate($post['geetest_challenge'], $post['geetest_validate'], $post['geetest_seccode'], $data);
            if ($result) {
                return true;
            } else{
                return false;
            }
        }else{  //服务器宕机,走failback模式
            if ($GtSdk->fail_validate($post['geetest_challenge'],$post['geetest_validate'],$post['geetest_seccode'])) {
                return true;
            }else{
                return false;
            }
        }
    }
}