<?php

namespace app\task\controller;

use app\common\model\MemberModel;
use app\common\model\MemberOauthModel;
use app\common\model\OrderModel;
use app\common\model\WechatModel;
use EasyWeChat\Kernel\Messages\Image;
use think\Console;
use think\console\Input;
use think\console\Output;
use think\Controller;
use think\Db;
use think\facade\Env;
use think\facade\Log;
use think\Response;

class UtilController extends Controller
{
    public function cropimage($img)
    {
        Log::close();

        $imageCrop = new \extcore\ImageCrop($img, $this->request->get());
        $response = $imageCrop->crop();
        $response->cacheControl('max-age=2592000');
        return $response;
    }

    public function cacheimage($img)
    {
        Log::close();
        $paths = explode('.', $img);
        if (count($paths) == 3) {
            preg_match_all('/(w|h|q|m)(\d+)(?:_|$)/', $paths[1], $matches);
            $args = [];
            foreach ($matches[1] as $idx => $key) {
                $args[$key] = $matches[2][$idx];
            }
            $response = crop_image($paths[0] . '.' . $paths[2], $args);
            if ($response->getCode() == 200) {
                file_put_contents(DOC_ROOT . '/' . $img, $response->getData());
            }
            $response->cacheControl('max-age=2592000');
            return $response;
        } else {
            return redirect(ltrim(config('upload.default_img'), '.'));
        }
    }

    public function restatic()
    {

        Db::name('member')->where('status', 1)->update(['recom_total' => 0, 'recom_count' => 0, 'team_count' => 0, 'recom_performance' => 0, 'total_performance' => 0]);
        $members = Db::name('member')->where('referer', '<>', 0)->select();
        foreach ($members as $member) {
            Db::name('member')->where('id', $member['referer'])->setInc('recom_total', 1);
            if ($member['is_agent'] > 0) {
                Db::name('member')->where('id', $member['referer'])->setInc('recom_count', 1);

                $parents = MemberModel::getParents($member['id'], 0);
                Db::name('member')->whereIn('id', $parents)->setInc('team_count', 1);
            }
        }
        unset($members, $member);

        $orders = Db::view('order', '*')->view('member', 'referer', 'order.member_id=member.id', 'LEFT')->where('order.status', '>', 0)->select();
        foreach ($orders as $order) {
            if ($order['referer'] > 0) {
                $amount = round($order['payamount'] * 100);
                Db::name('member')->where('id', $member['referer'])->setInc('recom_performance', $amount);

                $parents = MemberModel::getParents($order['member_id'], 0);
                Db::name('member')->whereIn('id', $parents)->setInc('total_performance', $amount);
            }
        }
        exit('success');
    }

    /**
     * 向用户发送推广海报
     * @return void 
     */
    public function poster($key)
    {
        $data = cache($key);
        if (empty($data)) {
            exit('N');
        }
        ignore_user_abort(true);
        set_time_limit(0);

        list($member_id, $account_id) = explode('-', $data);
        $oauth = MemberOauthModel::where('type_id', $account_id)->where('member_id', $member_id)->find();
        if (empty($oauth)) {
            exit('NMO');
        }
        $userModel = MemberModel::where('id', $member_id)->find();
        if (empty($userModel)) {
            exit('NM');
        }
        $account = WechatModel::where('id', $account_id)->find();
        if (empty($account)) {
            exit('NA');
        }
        $poster = $userModel->getSharePoster($account['type'] . '-' . $account['account_type'] . '-' . $account['id'], str_replace('[code]', $userModel['agentcode'], $account['share_poster_url']));
        if (empty($poster)) {
            exit('NP');
        }
        $app = WechatModel::createApp($account);
        $media = $app->media->uploadImage(DOC_ROOT . $poster);
        if (empty($media['media_id'])) {
            return 'NW';
        }
        $app->customer_service->message(new Image($media['media_id']))->to($oauth['openid'])->send();

        exit('Y');
    }

    public function daily()
    {
        $time = time();
        $isbreaked = false;

        $shopset = getSettings(false, 'shop');

        // 未支付提醒
        $orders = Db::name('Order')->where('status', 0)
            ->where('noticed', 0)
            ->where('create_time', '<', time() - 10 * 60)->select();
        $ids = [];
        foreach ($orders as $order) {
            $ids[] = $order['order_id'];
            OrderModel::sendOrderMessage($order, 'order_need_pay');
            if (time() - $time > 25) {
                $isbreaked = true;
                break;
            }
        }
        Db::name('Order')->whereIn('order_id', $ids)->update(['noticed' => 1]);
        if ($isbreaked) exit;

        if ($shopset['shop_order_pay_limit'] > 0) {
            $orders = OrderModel::where('status', ORDER_STATUS_UNPAIED)
                ->where('create_time', '<', time() - $shopset['shop_order_pay_limit'] * 60)->select();
            foreach ($orders as $order) {
                $order->updateStatus(['status' => -1, 'cancel_time' => time(), 'reason' => '订单 ' . $shopset['shop_order_pay_limit'] . ' 分钟内未支付自动取消']);
                if (time() - $time > 25) {
                    $isbreaked = true;
                    break;
                }
            }
        }
        if ($isbreaked) exit;

        $orders = OrderModel::where('status', ORDER_STATUS_SHIPED)
            ->where('deliver_time', '<', time() - $shopset['shop_order_receive_limit'] * 60 * 60 * 24)->select();
        foreach ($orders as $order) {
            $order->updateStatus(['status' => ORDER_STATUS_RECEIVED, 'reason' => '订单自动收货']);
            if (time() - $time > 25) {
                $isbreaked = true;
                break;
            }
        }
        if ($isbreaked) exit;

        $orders = OrderModel::where('status', ORDER_STATUS_RECEIVED)->where('islock', 0)
            ->where('receive_time', '<', time() - $shopset['shop_order_refund_limit'] * 60 * 60 * 24)->select();
        foreach ($orders as $order) {
            $order->updateStatus(['status' => ORDER_STATUS_FINISH, 'islock' => 1, 'reason' => '订单自动完成']);

            //todo 默认好评

            if (time() - $time > 25) {
                $isbreaked = true;
                break;
            }
        }
        if ($isbreaked) exit;

        $orders = OrderModel::where('status', ORDER_STATUS_FINISH)->where('islock', 0)
            ->where('confirm_time', '<', time() - $shopset['shop_order_refund_limit'] * 60 * 60 * 24)->select();
        foreach ($orders as $order) {
            $order->save(['islock' => 1]);
            if (time() - $time > 25) {
                $isbreaked = true;
                break;
            }
        }
        if ($isbreaked) exit;

        exit;
    }
}
