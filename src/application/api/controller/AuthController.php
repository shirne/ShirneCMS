<?php

namespace app\api\controller;

use app\api\facade\MemberTokenFacade;
use app\common\model\MemberModel;
use app\common\model\MemberOauthModel;
use app\common\model\OauthAppModel;
use app\common\model\WechatModel;
use app\common\service\CheckcodeService;
use app\common\validate\MemberValidate;
use EasyWeChat\Factory;
use EasyWeChat\OfficialAccount\Application;
use shirne\captcha\Captcha;
use shirne\common\ValidateHelper;
use shirne\sdk\OAuthFactory;
use think\Db;
use think\Exception;
use think\facade\Cache;
use think\facade\Log;
use think\Response;
use think\response\Json;

/**
 * 授权相关操作
 * Class AuthController
 * @package app\api\Controller
 */
class AuthController extends BaseController
{
    protected $accessToken = '';
    protected $accessSession = [];

    public function initialize()
    {
        parent::initialize();

        $tokenInvaild = false;
        $this->accessToken = request()->header('access_token');
        if (!$this->accessToken) {
            $this->accessToken = request()->param('access_token');
        }
        if (!empty($this->accessToken)) {
            $session = cache('access_' . $this->accessToken);
            if (!empty($session)) {
                $tokenInvaild = true;
                $sessData = json_decode($session, true);
                if (!empty($sessData)) {
                    $this->accessSession = $sessData;
                } else {
                    $this->accessToken = '';
                }
            }
        }
        Log::info('access:' . $this->accessToken . '/' . var_export($this->accessSession, true));
        if (
            empty($this->accessToken) &&
            !in_array($this->request->action(), ['token', 'wxsign', 'wxauth', 'wxautologin', 'wxlogin', 'refresh'])
        ) {
            if ($tokenInvaild) {
                $this->error('临时Token过期', ERROR_TMP_TOKEN_EXPIRE);
            } else {
                $this->error('未授权访问', ERROR_LOGIN_FAILED);
            }
        }
    }

    public function __destruct()
    {
        if ($this->accessToken) {
            if (empty($this->accessSession)) {
                $this->accessSession = ['time' => time()];
            }
            cache(
                'access_' . $this->accessToken,
                json_encode($this->accessSession, JSON_UNESCAPED_UNICODE),
                ['expire' => 60 * 10]
            );
            Log::info('access cache:' . $this->accessToken . '/' . var_export($this->accessSession, true));
        }
    }

    /**
     * 根据id生成一个会话key
     */
    private function getIpKey()
    {
        $ip = $this->request->ip();
        return 'access_' . str_replace([':', '.'], '_', $ip);
    }

    /**
     * 获取临时会话token
     * @param mixed $appid 
     * @param string $agent 
     * @return Json 
     */
    public function token($appid, $agent = '')
    {
        $app = $this->getApp($appid);
        if (empty($app)) {
            $this->error('未授权APP', ERROR_LOGIN_FAILED);
        }

        if ($this->accessToken) {
            cache('access_' . $this->accessToken, null);
            $this->accessToken = '';
            $this->accessSession = [];
        }

        // 根据IP限制token获取频率
        $ipkey = $this->getIpKey();
        $ipcount = cache($ipkey);
        if (!$ipcount) {
            cache($ipkey, 1, ['expire' => 60 * 60]);
        } else {
            if ($ipcount >= 10) {
                $this->error('操作过于频繁');
            }
            cache($ipkey, $ipcount + 1, ['expire' => 60 * 60]);
        }

        $this->accessToken = $this->createToken();
        $this->accessSession['appid'] = $appid;

        if ($agent) {
            $agentMember = Db('member')->where('agentcode', $agent)
                ->where('status', 1)
                ->where('is_agent', 'gt', 0)->find();
            if (!empty($agentMember)) {
                $this->accessSession['agent'] = $agentMember['id'];
            }
        }

        return $this->response($this->accessToken);
    }

    /**
     * 生成临时会话token
     * @return string 
     */
    private function createToken()
    {
        $token = md5(config('app.app_key') . time() . microtime() . mt_rand(999, 9999));

        while (Cache::has('access_' . $token)) {
            $token = md5(config('app.app_key') . time() . microtime() . mt_rand(999, 9999));
        }
        return $token;
    }

    /**
     * 获取指定的客户端账号
     * @param int|string $appid 
     * @return false|OauthAppModel 
     */
    private function getApp($appid)
    {
        if (empty($appid)) {
            return false;
        }
        $app = OauthAppModel::where('appid', $appid)->find();
        if (empty($app)) {
            return false;
        }
        return $app;
    }

    /**
     * 用户名/手机号+密码登录接口
     * 首次登录失败后启用验证码模式，需要提交验证码才能继续做登录验证
     * @param string $username 用户名或手机号
     * @param string $password 密码
     * @param string $verify 验证码
     * @return Json|void 
     */
    public function login($username, $password, $verify = '')
    {

        $this->check_submit_rate(2, 'global', md5($username));

        $app = $this->getApp($this->accessSession['appid']);
        if (empty($app)) {
            $this->error('未授权APP', ERROR_LOGIN_FAILED);
        }

        if (!empty($this->accessSession['need_verify'])) {
            if (empty($verify)) {
                $this->error('请填写验证码', ERROR_NEED_VERIFY);
            }
            $captchaVerify = new Captcha(array('seKey' => config('session.sec_key')), Cache::instance());
            $checked = $captchaVerify->check($verify, '_api_' . $this->accessToken);
            if (!$checked) {
                $this->error('验证码错误', ERROR_NEED_VERIFY);
            }
        }

        if (empty($username) || empty($password)) {
            $this->error('请填写登录账号及密码', ERROR_LOGIN_FAILED);
        }
        $respdata = [];
        $errcount = $this->accessSession['error_count'] ?? 0;
        if ($errcount > 4) {
            $this->error('登录尝试次数过多', ERROR_LOGIN_FAILED);
        }
        if ($errcount > 2) {
            $respdata['need_verify'] = 1;
        }
        if (ValidateHelper::isMobile($username)) {
            $member = MemberModel::where('mobile', $username)->where('mobile_bind', 1)->find();
        } else {
            $member = MemberModel::where('username', $username)->find();
        }
        if (!empty($member)) {
            $merrorcount = intval(cache('login_error_' . $member['id']));
            if ($merrorcount > 4) {
                $this->error('登录尝试次数过多', ERROR_LOGIN_FAILED);
            }
            if ($member['status'] == 1) {
                if (compare_password($member, $password)) {
                    $agentid = isset($this->accessSession['agent']) ? intval($this->accessSession['agent']) : 0;
                    if ($agentid > 0 && !$member['referer']) {
                        MemberModel::autoBindAgent($member, $agentid);
                    }
                    $token = MemberTokenFacade::createToken($member['id'], $app['platform'], $app['appid']);
                    if (!empty($token)) {
                        cache($this->getIpKey(), NULL);
                        user_log($member['id'], 'login', 1, '登录成功');
                        $this->accessSession['need_verify'] = 0;
                        $this->accessSession['error_count'] = 0;
                        cache('login_error_' . $member['id'], NULL);

                        $openid = $this->request->param('openid');
                        if (!empty($openid)) {
                            $oauth = MemberOauthModel::where('openid', $openid)->find();
                            if (!empty($oauth)) {
                                MemberOauthModel::where('openid', $openid)->where('member_id', 0)->update(['member_id' => $member['id']]);
                                $updata = MemberModel::checkUpdata($oauth->getData(), $member);
                                if (!empty($updata)) {
                                    $member->save($updata);
                                }
                            }
                        }
                        return $this->response($token);
                    }
                } else {
                    user_log($member['id'], 'login', 0, '登录失败');
                    $this->accessSession['need_verify'] = 1;
                    $this->accessSession['error_count'] = $errcount + 1;
                    $respdata['need_verify'] = 1;
                    $merrorcount += 1;
                    cache('login_error_' . $member['id'], $merrorcount, ['expire' => 60 * 60]);
                }
            } else {
                $this->error('账户已被禁用', ERROR_MEMBER_DISABLED);
            }
        }


        $this->accessSession['error_count'] = $errcount + 1;
        if (!empty($respdata['need_verify'])) {
            $this->accessSession['need_verify'] = 1;
        }

        $this->error('登录失败', ERROR_LOGIN_FAILED, $respdata);
    }

    /**
     * 生成微信公众号签名配置
     * @param string $wxid 
     * @return Json|void 
     */
    public function wxSign($wxid = '')
    {
        if (empty($wxid)) {
            $wechat = Db::name('wechat')->where('type', 'wechat')
                ->where('is_default', 1)->find();
        } else {
            $wechat = Db::name('wechat')->where('type', 'wechat')
                ->where(is_numeric($wxid) ? 'id' : 'hash', $wxid)->find();
        }

        if (!empty($wechat['appid'])) {
            $app = Factory::officialAccount(WechatModel::to_config($wechat));
            $url = $this->request->param('url');
            if (empty($url)) {
                $url = $this->request->server('HTTP_REFERER');
            }
            if ($url) $app->jssdk->setUrl($url);
            $signPackage = $app->jssdk->getConfigArray([
                'updateAppMessageShareData',
                'updateTimelineShareData',
                'onMenuShareTimeline',
                'onMenuShareAppMessage',
                'onMenuShareQQ',
                'onMenuShareWeibo',
                'onMenuShareQZone',
                'checkJsApi',
                'chooseImage',
                'previewImage',
                'openAddress',
                'openLocation',
                'getLocation',
                'hideOptionMenu',
                'showOptionMenu',
                'hideMenuItems',
                'showMenuItems'
            ]);
            $signPackage['url'] = $url;
            //$signPackage['debug']=true;
            return $this->response($signPackage);
        }
        return $this->error('当前公众号不支持操作');
    }

    /**
     * 获取微信公众号授权跳转的url
     * @param string $wxid 
     * @return Json 
     */
    public function wxAuth($wxid = '')
    {
        if (empty($wxid)) {
            $wechat = Db::name('wechat')->where('type', 'wechat')
                ->where('is_default', 1)->find();
        } else {
            $wechat = Db::name('wechat')->where('type', 'wechat')
                ->where(is_numeric($wxid) ? 'id' : 'hash', $wxid)->find();
        }
        if (empty($wechat)) {
            $this->error('服务器配置错误', ERROR_LOGIN_FAILED);
        }
        $url = $this->request->param('url');
        if (empty($url)) {
            $url = $this->request->server('HTTP_REFERER');
        }
        $oauth = OAuthFactory::getInstence($wechat['type'], $wechat['appid'], $wechat['appsecret'], $url);
        $url = $oauth->redirect();

        return $this->response(['url' => $url]);
    }

    /**
     * 自动登录
     */
    public function wxAutoLogin($wxid, $code)
    {
        $agent = $this->request->param('agent');
        $wechat = Db::name('wechat')->where('type', 'wechat')
            ->where(is_numeric($wxid) ? 'id' : 'hash', $wxid)->find();
        if (empty($wechat)) {
            $this->error('服务器配置错误', ERROR_LOGIN_FAILED);
        }
        $options = WechatModel::to_config($wechat);
        switch ($wechat['account_type']) {
            case 'wechat':
            case 'subscribe':
            case 'service':
                $weapp = Factory::officialAccount($options);
                break;
            case 'miniprogram':
            case 'minigame':
                $weapp = Factory::miniProgram($options);
                break;
            default:
                $this->error('配置错误', ERROR_LOGIN_FAILED);
                break;
        }
        if ($weapp instanceof Application) {
            try {
                $userinfo = $weapp->oauth->userFromCode($code)->getRaw();
            } catch (Exception $e) {
                $this->error('登录失败:' . $e->getMessage(), ERROR_LOGIN_FAILED);
            }
            if (empty($userinfo) || empty($userinfo['openid'])) {
                $this->error('登录失败', ERROR_LOGIN_FAILED);
            }

            $session = ['openid' => $userinfo['openid'], 'unionid' => $userinfo['unionid'] ?? ''];
        } else {

            try {
                $session = $weapp->auth->session($code);
            } catch (Exception $e) {
                $this->error('登录失败:' . $e->getMessage(), ERROR_LOGIN_FAILED);
            }
            if (empty($session) || empty($session['openid'])) {
                $this->error('登录失败', ERROR_LOGIN_FAILED);
            }
        }

        $condition = array('openid' => $session['openid']);
        $oauth = MemberOauthModel::where($condition)->find();
        if (!empty($oauth) && $oauth['member_id']) {
            $member = MemberModel::where('id', $oauth['member_id'])->find();
        } elseif ($this->isLogin) {
            $member = MemberModel::where('id', $this->user['id'])->find();
        } elseif (!empty($session['unionid'])) {
            $sameAuth = MemberOauthModel::where('unionid', $session['unionid'])->find();
            if (!empty($sameAuth)) {
                $member = MemberModel::where('id', $sameAuth['member_id'])->find();
            }
        }
        if (!empty($member)) {

            if ($member['status'] != 1) {
                $this->error('账户已被禁用', ERROR_MEMBER_DISABLED, ['openid' => $session['openid']]);
            }
            if (!empty($agent)) {
                MemberModel::autoBindAgent($member, $agent);
            }

            $token = MemberTokenFacade::createToken($member['id'], $wechat['type'] . '-' . $wechat['account_type'], $wechat['appid']);
            if (!empty($token)) {
                MemberModel::update([
                    'login_ip' => request()->ip(),
                    'logintime' => time()
                ], ['id' => $member['id']]);
                user_log($member['id'], 'login', 1, '登录' . $wechat['title']);
                $token['openid'] = $session['openid'];
                return $this->response($token);
            }
        }
        $this->error('登录失败', ERROR_LOGIN_FAILED);
    }

    /**
     * 微信公众号/小程序自动注册登录
     * @param string $wxid 小程序对应的系统id或hash
     * @param string $code 客户端获取到的授权码
     * @param string $rawData 客户端获取到的用户资料
     * @param string $phoneCode 客户端获取到的手机号码session
     * @return Json|void 
     */
    public function wxLogin($wxid, $code, $rawData = null, $phoneCode = null)
    {

        $agent = $this->request->param('agent');
        $wechat = Db::name('wechat')->where('type', 'wechat')
            ->where(is_numeric($wxid) ? 'id' : 'hash', $wxid)->find();
        if (empty($wechat)) {
            $this->error('服务器配置错误', ERROR_LOGIN_FAILED);
        }
        $options = WechatModel::to_config($wechat);
        switch ($wechat['account_type']) {
            case 'wechat':
            case 'subscribe':
            case 'service':
                $weapp = Factory::officialAccount($options);
                break;
            case 'miniprogram':
            case 'minigame':
                $weapp = Factory::miniProgram($options);
                break;
            default:
                $this->error('配置错误', ERROR_LOGIN_FAILED);
                break;
        }
        $mobileData = [];
        if ($weapp instanceof Application) {
            try {
                $userinfo = $weapp->oauth->userFromCode($code)->getRaw();
            } catch (Exception $e) {
                $this->error('登录失败:' . $e->getMessage(), ERROR_LOGIN_FAILED);
            }
            if (empty($userinfo) || empty($userinfo['openid'])) {
                $this->error('登录失败', ERROR_LOGIN_FAILED);
            }
            $rawData = json_encode($userinfo, JSON_UNESCAPED_UNICODE);
            $session = ['openid' => $userinfo['openid'], 'unionid' => $userinfo['unionid'] ?? ''];
        } else {
            //调试模式允许mock登录
            if ($wechat['is_debug'] && $code == 'the code is a mock one') {
                $userinfo = json_decode($rawData, TRUE);
                $session = ['openid' => md5($userinfo['nickName']), 'unionid' => ''];
            } else {
                try {
                    $session = $weapp->auth->session($code);
                } catch (Exception $e) {
                    $this->error('登录失败:' . $e->getMessage(), ERROR_LOGIN_FAILED);
                }
                if (empty($session) || empty($session['openid'])) {
                    $this->error('登录失败', ERROR_LOGIN_FAILED);
                }

                if (!empty($rawData)) {
                    $signature = $this->request->param('signature');
                    if (sha1($rawData . $session['session_key']) == $signature) {
                        $userinfo = json_decode($rawData, TRUE);
                    }
                }

                if (!empty($phoneCode)) {
                    $mobileData = $weapp->getPhoneNumber($phoneCode);
                    if (isset($mobileData['phone_info'])) {
                        $mobileData = $mobileData['phone_info'];
                    }
                } else {
                    //兼容低版本
                    $ivdata = $this->request->param('phoneIv');
                    $encdata = $this->request->param('phoneData');
                    if (!empty($ivdata) && !empty($encdata)) {
                        $mobileData = $this->decodeAES($encdata, $session['session_key'], $ivdata);
                    }
                }
            }
        }

        // 只使用code登录，自动生成空微信信息
        if (empty($userinfo)) {
            $userinfo = [
                "nickName" => "微信用户",
                "gender" => 0,
                "language" => "",
                "city" => "",
                "province" => "",
                "country" => "",
                "avatarUrl" => "https://thirdwx.qlogo.cn/mmopen/vi_32/POgEwh4mIHO4nibH0KlMECNjjGxQUq24ZEaGT4poC6icRiccVGKSyXwibcPq4BWmiaIGuG1icwxaQX6grC9VemZoJ8rg/132",
            ];
        }
        $type = $wechat['account_type'];
        $typeid = $wechat['id'];

        $condition = array('openid' => $session['openid']);
        $oauth = MemberOauthModel::where($condition)->find();
        if (!empty($oauth) && $oauth['member_id']) {
            $member = MemberModel::where('id', $oauth['member_id'])->find();
        } elseif ($this->isLogin) {
            $member = MemberModel::where('id', $this->user['id'])->find();
        } elseif (!empty($session['unionid'])) {
            $sameAuth = MemberOauthModel::where('unionid', $session['unionid'])->find();
            if (!empty($sameAuth)) {
                $member = MemberModel::where('id', $sameAuth['member_id'])->find();
            }
        }

        $data = $this->wxMapdata($userinfo, $rawData);
        $data['type'] = $type;
        $data['type_id'] = $typeid;
        if (!empty($session['unionid'])) $data['unionid'] = $session['unionid'];

        if (empty($member)) {
            $register = getSetting('m_register');
            if ($register != '1' || !empty($mobileData['purePhoneNumber'])) {

                //自动注册
                $data['openid'] = $session['openid'];

                $referid = $this->getAgentId($agent);

                //系统配置的默认推荐人
                if ($referid <= 0 && $this->config['referer_id']) {
                    $referid = intval($this->config['referer_id']);
                }
                $member = MemberModel::createFromOauth($data, $referid, $mobileData['purePhoneNumber'] ?? '');

                if ($member['id']) {
                    $data['member_id'] = $member['id'];
                }
            } else {
                $this->error('请注册账号', ERROR_NEED_REGISTER, ['openid' => $session['openid']]);
            }
        } else {
            //更新资料
            if (empty($oauth['member_id'])) {
                $data['member_id'] = $member['id'];
            }
            if (!empty($mobileData['purePhoneNumber'])) {
                $data['mobile'] = $mobileData['purePhoneNumber'];
                $data['mobile_bind'] = 1;
            }
            $updata = MemberModel::checkUpdata($data, $member);
            if (!empty($updata)) {
                MemberModel::update($updata, array('id' => $member['id']));
            }
            if (!empty($agent)) {
                MemberModel::autoBindAgent($member, $agent);
            }
        }

        if (empty($oauth)) {
            $data['openid'] = $session['openid'];
            MemberOauthModel::create($data);
        } else {
            MemberOauthModel::update($data, ['id' => $oauth['id']]);
        }

        if ($this->isLogin) {
            return $this->response(['openid' => $session['openid']]);
        }

        if (!empty($member)) {

            if ($member['status'] != 1) {
                $this->error('账户已被禁用', ERROR_MEMBER_DISABLED, ['openid' => $session['openid']]);
            }

            $token = MemberTokenFacade::createToken($member['id'], $wechat['type'] . '-' . $wechat['account_type'], $wechat['appid']);
            if (!empty($token)) {
                MemberModel::update([
                    'login_ip' => request()->ip(),
                    'logintime' => time()
                ], ['id' => $member['id']]);
                user_log($member['id'], 'login', 1, '登录' . $wechat['title']);
                $token['openid'] = $session['openid'];
                return $this->response($token);
            }
        }
        $this->error('登录失败', ERROR_LOGIN_FAILED);
    }

    /**
     * 根据推荐码获取推荐人id，如果推荐人已失效则返回0
     * @param string $agent 推荐码
     * @return int 
     */
    private function getAgentId($agent)
    {
        $referid = 0;
        if (!empty($agent)) {
            $islock = getSetting('agent_lock') == '1';
            $amem = Db::name('Member')
                ->where('agentcode', $agent)
                ->where('status', 1)->find();
            if (!empty($amem)) {
                $referid = $amem['id'];
                if (!$islock) {
                    while ($amem['is_agent'] < 1) {
                        if ($amem['referer'] < 1) {
                            break;
                        }
                        $amem = Db::name('Member')
                            ->where('id', $amem['referer'])
                            ->where('status', 1)->find();
                    }
                }
                Log::info('With Agent code: ' . $agent . ',' . $referid . ',' . $amem['id']);
                $referid = $amem['id'];
            } else {
                Log::error('With Agent code: ' . $agent . ' ERROR');
            }
        }
        return $referid;
    }

    /**
     * 第三方登录数据转换
     * @param $userinfo
     * @param $rawData
     * @return array
     */
    private function wxMapdata($userinfo, $rawData)
    {
        $nickname = '';
        if (isset($userinfo['nickName'])) {
            $nickname = $userinfo['nickName'];
        }
        if (isset($userinfo['nickname'])) {
            $nickname = $userinfo['nickname'];
        }

        $avatar = '';
        if (isset($userinfo['avatar'])) {
            $avatar = $userinfo['avatar'];
        }
        if (isset($userinfo['avatarUrl'])) {
            $avatar = $userinfo['avatarUrl'];
        }
        if (isset($userinfo['headimgurl'])) {
            $avatar = $userinfo['headimgurl'];
        }
        $gender = '';
        if (isset($userinfo['gender'])) {
            $gender = $userinfo['gender'];
        }
        if (isset($userinfo['sex'])) {
            $gender = $userinfo['sex'];
        }
        $data = [
            'data' => $rawData,
            //'is_follow'=>0,
            'nickname' => $nickname,
            'gender' => $gender,
            //'unionid'=>isset($userinfo['unionid'])?$userinfo['unionid']:'',
            'avatar' => $avatar,
            'city' => $userinfo['city'],
            'province' => $userinfo['province'],
            'country' => isset($userinfo['country']) ? $userinfo['country'] : '',
            'language' => isset($userinfo['language']) ? $userinfo['language'] : ''
        ];
        if (isset($userinfo['is_follow'])) {
            $data['is_follow'] = $userinfo['is_follow'];
        } elseif (!empty($userinfo['subscribe_time'])) {
            $data['is_follow'] = 1;
        }
        if (isset($userinfo['unionid'])) {
            $data['unionid'] = $userinfo['unionid'];
        }
        return $data;
    }

    /**
     * 刷新API token
     * @param string $refresh_token 
     * @return Json|void 
     */
    public function refresh($refresh_token)
    {

        if (!empty($refresh_token)) {
            $token = MemberTokenFacade::refreshToken($refresh_token);
            if (!empty($token)) {
                $agent = $this->request->param('agent');
                if (!empty($agent)) {
                    $member = Db::name('member')->where('id', $token['member_id'])->find();
                    MemberModel::autoBindAgent($member, $agent);
                }
                return $this->response($token);
            }
        }
        $this->error('刷新失败', ERROR_REFRESH_TOKEN_INVAILD);
    }

    /**
     * 输出验证码
     * @return Response 
     */
    public function captcha()
    {

        $verify = new Captcha(array('seKey' => config('session.sec_key')), Cache::instance());

        $verify->fontSize = 16;
        $verify->length = 4;
        return $verify->entry('_api_' . $this->accessToken);
    }

    /**
     * 判断验证码是否有效
     * @param string $mobile 
     * @param string $type 
     * @param string $code 
     * @return bool 
     */
    protected function smsverify($mobile, $type, $code)
    {
        switch ($type) {
            case 'login':
                $key = 'login_verify';
                break;
            case 'register':
                $key = 'register_verify';
                break;
                /* case 'forget':
                $key = 'forget_verify';
            break; */
            default:
                return false;
        }
        $key .= '_' . $mobile;
        if (!empty($this->accessSession[$key])) {
            $savecode = $this->accessSession[$key];
            unset($this->accessSession[$key]);
            unset($this->accessSession['verify_count']);
            cache('verify_' . $mobile, NULL);
            return $savecode == $code;
        }

        return false;
    }

    /**
     * 发送验证码，前端需要同时提交手机号码和图形验证码
     * @param string $mobile 
     * @param string $captcha 
     * @param string $type 
     * @param bool $isverify 
     * @return void 
     */
    public function smscode($mobile, $captcha, $type = 'login', $isverify = false)
    {
        if (!empty($this->accessSession['need_verify'])) {
            if (empty($captcha)) {
                $this->error('请填写图形验证码', ERROR_NEED_VERIFY);
            }
            $verify = new Captcha(array('seKey' => config('session.sec_key')), Cache::instance());
            $checked = $verify->check($captcha, '_api_' . $this->accessToken);
            if (!$checked) {
                $this->error('验证码错误', ERROR_NEED_VERIFY);
            }
        }


        $service = new CheckcodeService();
        $result = $service->sendCode('mobile', $mobile, $type);
        if (!$result) {
            $this->error($service->getError());
        }

        $this->accessSession['need_verify'] = 1;
        $this->success('验证码已发送');
    }

    /**
     * 退出登录，清除token 未登录状态不作操作
     * @return void 
     */
    public function quit()
    {
        if ($this->isLogin) {
            MemberTokenFacade::clearToken($this->token);
        }
        $this->success('退出成功');
    }

    /**
     * todo 验证码
     */
    public function verify() {}

    /**
     * 忘记密码
     */
    public function forgot($account, $password, $verify)
    {
        $app = $this->getApp($this->accessSession['appid']);
        if (empty($app)) {
            $this->error('未授权APP', ERROR_LOGIN_FAILED);
        }

        if (empty($verify)) {
            $this->error(' 请填写验证码');
        }
        if (empty($password)) {
            $this->error(' 请填写新密码');

            // todo 密码强度验证
        }
        $account_type = $this->request->param('type');
        $model = Db::name('member')->where('status', 1);
        if ($account_type == 'mobile') {
            $model->where('mobile', $account)->where('mobile_bind', 1);
        } elseif ($account_type == 'email') {
            $model->where('email', $account)->where('email_bind', 1);
        } else {
            $this->error('账号类型错误');
        }
        $member = $model->find();
        if (empty($member)) {
            $this->error('账号错误');
        }

        $service = new CheckcodeService();
        $verifyed = $service->verifyCode($account, $verify);
        if (!$verifyed) {
            $this->error('验证码填写错误');
        }
        $data['salt'] = random_str(8);
        $data['password'] = encode_password($password, $data['salt']);
        Db::name('member')->where('id', $member['id'])->update($data);
        $this->success('密码重置成功!');
    }

    /**
     * 忘记密码
     * @deprecated
     */
    public function forget($step = 0)
    {
        $app = $this->getApp($this->accessSession['appid']);
        if (empty($app)) {
            $this->error('未授权APP', ERROR_LOGIN_FAILED);
        }

        //第一步:确认账号
        if ($step == 0) {
            $account = $this->request->param('account');
            $account_type = $this->request->param('type');
            $model = Db::name('member')->where('status', 1);
            if ($account_type == 'mobile') {
                $model->where('mobile', $account)->where('mobile_bind', 1);
            } elseif ($account_type == 'email') {
                $model->where('email', $account)->where('email_bind', 1);
            } else {
                $this->error('账号类型错误');
            }
            $member = $model->find();
            if (empty($member)) {
                $this->error('账号错误');
            }
            if ($account_type == 'mobile') {
                //发送验证码
                $verify = '';
            } elseif ($account_type == 'email') {
                //发送验证码
                $verify = '';
            }
            $this->accessSession['forget_account'] = $member['id'];
            $this->accessSession['forget_verify'] = $verify;
            $this->success('验证码已发送');

            //第二步:验证验证码
        } elseif ($step == 1) {
            if (empty($this->accessSession['forget_account'])) {
                $this->success('验证失效,请重新填写账号');
            }
            $verifycode = $this->request->param('verify');
            if (empty($this->accessSession['forget_verify'])) {
                $this->error('验证码已失效');
            }
            if ($verifycode != $this->accessSession['forget_verify']) {
                $this->error('验证码错误');
            }

            $this->accessSession['forget_pass'] = 1;
            $this->success('验证通过');

            //第三步:重置密码
        } elseif ($step == 2) {
            if (empty($this->accessSession['forget_account'])) {
                $this->success('验证失效,请重新填写账号');
            }
            if (empty($this->accessSession['forget_pass'])) {
                $this->success('验证失效,请重新发送验证码');
            }
            $password = $this->request->param('password');
            $repassword = $this->request->param('repassword');

            if ($password != $repassword) {
                $this->success('两次密码输入不一致，请确认输入');
            }

            $data['salt'] = random_str(8);
            $data['password'] = encode_password($password, $data['salt']);
            Db::name('member')->where('id', $this->accessSession['forget_account'])->update($data);
            $this->success('密码重置成功!');
        }
    }

    /**
     * 注册会员
     * @param string $agent 推荐码
     * @param string $username 注册用户名
     * @param string $password 登录密码
     * @param string $repassword 登录密码确认
     * @param string $email 邮箱
     * @param string $realname 真实姓名
     * @param string $mobile 手机号码
     * @param string $mobilecheck 手机验证码，与图形验证码二选一
     * @param string $verify 图形验证码
     * @param string $invite_code 激活码，预生成的激活码，可绑定激活码所属会员作为推荐人
     * @param string $openid 微信生态内已获取的openid，注册时提交此信息会绑定对应的会员
     * @return void 
     */
    public function register($agent = '')
    {
        $this->check_submit_rate(2);
        $app = $this->getApp($this->accessSession['appid']);
        if (empty($app)) {
            $this->error('未授权APP', ERROR_LOGIN_FAILED);
        }

        // 未开启手机验证码的情况下验证图形码
        if ($this->config['sms_code'] != 1) {
            $verifycode = $this->request->param('verify');
            if (empty($verifycode)) {
                $this->error('请填写验证码', ERROR_NEED_VERIFY);
            }
            $verify = new Captcha(array('seKey' => config('session.sec_key')), Cache::instance());
            $checked = $verify->check($verifycode, '_api_' . $this->accessToken);
            if (!$checked) {
                $this->error('验证码错误', ERROR_NEED_VERIFY);
            }
        }

        $data = $this->request->only('username,password,repassword,email,realname,mobile,mobilecheck', 'post');

        $validate = new MemberValidate();
        $validate->setId();
        if (!$validate->scene('register')->check($data)) {
            $this->error($validate->getError());
        }

        $invite_code = $this->request->post('invite_code');
        if (($this->config['m_invite'] == 1 && !empty($invite_code)) || $this->config['m_invite'] == 2) {
            if (empty($invite_code)) $this->error("请填写激活码");
            $invite = Db::name('inviteCode')->where(array('code' => $invite_code, 'is_lock' => 0, 'member_use' => 0))->find();
            if (empty($invite) || ($invite['invalid_time'] > 0 && $invite['invalid_time'] < time())) {
                $this->error("激活码不正确");
            }
        }

        if ($this->config['sms_code'] == 1) {
            if (empty($data['mobilecheck'])) {
                $this->error(' 请填写手机验证码');
            }
            $service = new CheckcodeService();
            $verifyed = $service->verifyCode($data['mobile'], $data['mobilecheck']);
            if (!$verifyed) {
                $this->error(' 手机验证码填写错误');
            }
            $data['mobile_bind'] = 1;
            unset($data['mobilecheck']);
        }

        $openid = $this->request->param('openid');
        if (empty($openid)) {
            $openid = $this->accessSession['openid'];
        }

        Db::startTrans();
        if (!empty($invite)) {
            $invite = Db::name('inviteCode')->lock(true)->find($invite['id']);
            if (!empty($invite['member_use'])) {
                Db::rollback();
                $this->error("激活码已被使用");
            }
            $data['referer'] = $invite['member_id'];
            if ($invite['level_id']) {
                $data['level_id'] = $invite['level_id'];
            } else {
                $data['level_id'] = getDefaultLevel();
            }
        } else {
            $agentid = isset($this->accessSession['agent']) ? intval($this->accessSession['agent']) : 0;
            if ($agent) {
                $agentMember = Db('member')->where('agentcode', $agent)
                    ->where('status', 1)->find();
                if (!empty($agentMember) && ($this->config['agent_lock'] || $agentMember['is_agent'] > 0)) {
                    $agentid = $agentMember['id'];
                }
            }
            //系统配置的默认推荐人
            if ($agentid <= 0 && $this->config['referer_id']) {
                $agentid = intval($this->config['referer_id']);
            }
            $data['referer'] = $agentid;
            $data['level_id'] = getDefaultLevel();
        }
        $data['salt'] = random_str(8);
        $data['password'] = encode_password($data['password'], $data['salt']);
        $data['login_ip'] = $this->request->ip();

        unset($data['repassword']);
        if (!empty($openid)) {
            $oauth = MemberOauthModel::where('openid', $openid)->find();
            if (!empty($oauth)) {
                $updata = MemberModel::checkUpdata($oauth->getData(), $data);
                $data = array_merge($data, $updata);
            } else {
                $openid = '';
            }
        }
        $model = MemberModel::create($data);

        if (empty($model['id'])) {
            Db::rollback();
            $this->error("注册失败");
        }
        if (!empty($invite)) {
            $invite['member_use'] = $model['id'];
            $invite['use_time'] = time();
            Db::name('inviteCode')->update($invite);
        }
        if (!empty($this->accessSession['openid'])) {
            Db::name('memberOauth')->where('openid', $this->accessSession['openid'])
                ->update(['member_id' => $model['id']]);
        }
        if (!empty($openid)) {
            MemberOauthModel::where('openid', $openid)->where('member_id', 0)->update(['member_id' => $model['id']]);
        }
        Db::commit();
        $token = MemberTokenFacade::createToken($model['id'], $app['platform'], $app['appid']);

        $this->success($token, 1, "注册成功");
    }
}
