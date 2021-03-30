<?php

namespace app\task\controller;

use addon\base\BaseAddon;
use app\common\model\MemberModel;
use app\common\model\MemberOauthModel;
use app\common\model\WechatModel;
use EasyWeChat\Kernel\Messages\Image;
use think\Controller;
use think\Db;
use think\facade\Log;

class UtilController extends Controller
{
    public function cropimage($img){
        Log::close();
        
        $imageCrop=new \extcore\ImageCrop($img, $this->request->get());
        $response = $imageCrop->crop();
        $response->cacheControl('max-age=2592000');
        return $response;
    }

    public function cacheimage($img){
        Log::close();
        $paths=explode('.',$img);
        if(count($paths)==3) {
            preg_match_all('/(w|h|q|m)(\d+)(?:_|$)/', $paths[1], $matches);
            $args = [];
            foreach ($matches[1] as $idx=>$key){
                $args[$key]=$matches[2][$idx];
            }
            $response = crop_image($paths[0].'.'.$paths[2], $args);
            if($response->getCode()==200) {
                file_put_contents(DOC_ROOT . '/' . $img, $response->getData());
            }
            $response->cacheControl('max-age=2592000');
            return $response;
        }else{
            return redirect(ltrim(config('upload.default_img'),'.'));
        }
    }

    public function restatic(){

        Db::name('member')->where('status',1)->update(['recom_total'=>0,'recom_count'=>0,'team_count'=>0,'recom_performance'=>0,'total_performance'=>0]);
        $members = Db::name('member')->where('referer','<>',0)->select();
        foreach($members as $member){
            Db::name('member')->where('id',$member['referer'])->setInc('recom_total',1);
            if($member['is_agent']>0){
                Db::name('member')->where('id',$member['referer'])->setInc('recom_count',1);
                
                $parents = MemberModel::getParents($member['id'],0);
                Db::name('member')->whereIn('id',$parents)->setInc('team_count',1);
            }
        }
        unset($members,$member);

        $orders = Db::view('order','*')->view('member','referer','order.member_id=member.id','LEFT')->where('order.status','>',0)->select();
        foreach($orders as $order){
            if($order['referer'] > 0){
                $amount = round($order['payamount']*100);
                Db::name('member')->where('id',$member['referer'])->setInc('recom_performance',$amount);
                
                $parents = MemberModel::getParents($order['member_id'],0);
                Db::name('member')->whereIn('id',$parents)->setInc('total_performance',$amount);
            }
        }
        exit('success');
    }

    /**
     * 向用户发送推广海报
     * @return void 
     */
    public function poster($key){
        $data = cache($key);
        if(empty($data)){
            exit('N');
        }
        ignore_user_abort(true);
        set_time_limit(0);

        list($member_id,$account_id) = explode('-',$data);
        $oauth = MemberOauthModel::where('type_id',$account_id)->where('member_id',$member_id)->find();
        if(empty($oauth)){
            exit('NMO');
        }
        $userModel = MemberModel::where('id',$member_id)->find();
        if(empty($userModel)){
            exit('NM');
        }
        $account = WechatModel::where('id',$account_id)->find();
        if(empty($account)){
            exit('NA');
        }
        $poster = $userModel->getSharePoster($account['type'].'-'.$account['account_type'],str_replace('[code]',$userModel['agentcode'],$account['share_poster_url']));
        if(empty($poster)){
            exit('NP');
        }
        $app = WechatModel::createApp($account);
        $media = $app->media->uploadImage(DOC_ROOT . $poster );
        if (empty($media['media_id'])) {
            return 'NW';
        }
        $app->customer_service->message(new Image($media['media_id']))->to($oauth['openid'])->send();

        exit('Y');
    }

    public function daily()
    {
        $modules = Db::name('permission')->where('parent_id',0)->where('is_sys',0)->where('disable',0)->select();

        foreach($modules as $module){
            if(!empty($module['key'])){
                try{
                    BaseAddon::factory($module['key'])->task();
                }catch(\Exception $e){
                    Log::record('Addon '.$module['key'].' task error:'.$e->getMessage());
                }
            }
        }

        exit;
    }

}