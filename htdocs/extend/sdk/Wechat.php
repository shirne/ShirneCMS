<?php
namespace sdk;
/**
 *  微信公众平台PHP-SDK, 官方API部分
 *  @author  dodge <dodgepudding@gmail.com>
 *  @link https://github.com/dodgepudding/wechat-php-sdk
 *  @version 1.2
 *  usage:
 *   $options = array(
 *          'token'=>'tokenaccesskey', //填写你设定的key
 *          'encodingaeskey'=>'encodingaeskey', //填写加密用的EncodingAESKey
 *          'appid'=>'wxdk1234567890', //填写高级调用功能的app id
 *          'appsecret'=>'xxxxxxxxxxxxxxxxxxx' //填写高级调用功能的密钥
 *      );
 *   $weObj = new Wechat($options);
 *   $weObj->valid();
 *   $type = $weObj->getRev()->getRevType();
 *   switch($type) {
 *          case Wechat::MSGTYPE_TEXT:
 *              $weObj->text("hello, I'm wechat")->reply();
 *              exit;
 *              break;
 *          case Wechat::MSGTYPE_EVENT:
 *              ....
 *              break;
 *          case Wechat::MSGTYPE_IMAGE:
 *              ...
 *              break;
 *          default:
 *              $weObj->text("help info")->reply();
 *   }
 *
 *   //获取菜单操作:
 *   $menu = $weObj->getMenu();
 *   //设置菜单
 *   $newmenu =  array(
 *          "button"=>
 *              array(
 *                  array('type'=>'click','name'=>'最新消息','key'=>'MENU_KEY_NEWS'),
 *                  array('type'=>'view','name'=>'我要搜索','url'=>'http://www.baidu.com'),
 *                  )
 *          );
 *   $result = $weObj->createMenu($newmenu);
 */
class Wechat extends WechatAuth
{
    const MSGTYPE_TEXT = 'text';
    const MSGTYPE_IMAGE = 'image';
    const MSGTYPE_LOCATION = 'location';
    const MSGTYPE_LINK = 'link';
    const MSGTYPE_EVENT = 'event';
    const MSGTYPE_MUSIC = 'music';
    const MSGTYPE_NEWS = 'news';
    const MSGTYPE_VOICE = 'voice';
    const MSGTYPE_VIDEO = 'video';

    const EVENT_SUBSCRIBE = 'subscribe';       //订阅
    const EVENT_UNSUBSCRIBE = 'unsubscribe';   //取消订阅
    const EVENT_SCAN = 'SCAN';                 //扫描带参数二维码
    const EVENT_LOCATION = 'LOCATION';         //上报地理位置
    const EVENT_MENU_VIEW = 'VIEW';                     //菜单 - 点击菜单跳转链接
    const EVENT_MENU_CLICK = 'CLICK';                   //菜单 - 点击菜单拉取消息
    const EVENT_MENU_SCAN_PUSH = 'scancode_push';       //菜单 - 扫码推事件(客户端跳URL)
    const EVENT_MENU_SCAN_WAITMSG = 'scancode_waitmsg'; //菜单 - 扫码推事件(客户端不跳URL)
    const EVENT_MENU_PIC_SYS = 'pic_sysphoto';          //菜单 - 弹出系统拍照发图
    const EVENT_MENU_PIC_PHOTO = 'pic_photo_or_album';  //菜单 - 弹出拍照或者相册发图
    const EVENT_MENU_PIC_WEIXIN = 'pic_weixin';         //菜单 - 弹出微信相册发图器
    const EVENT_MENU_LOCATION = 'location_select';      //菜单 - 弹出地理位置选择器
    const EVENT_SEND_MASS = 'MASSSENDJOBFINISH';        //发送结果 - 高级群发完成
    const EVENT_SEND_TEMPLATE = 'TEMPLATESENDJOBFINISH';//发送结果 - 模板消息发送结果
    const EVENT_KF_SEESION_CREATE = 'kfcreatesession';  //多客服 - 接入会话
    const EVENT_KF_SEESION_CLOSE = 'kfclosesession';    //多客服 - 关闭会话
    const EVENT_KF_SEESION_SWITCH = 'kfswitchsession';  //多客服 - 转接会话
    const EVENT_CARD_PASS = 'card_pass_check';          //卡券 - 审核通过
    const EVENT_CARD_NOTPASS = 'card_not_pass_check';   //卡券 - 审核未通过
    const EVENT_CARD_USER_GET = 'user_get_card';        //卡券 - 用户领取卡券
    const EVENT_CARD_USER_DEL = 'user_del_card';        //卡券 - 用户删除卡券

    const MENU_CREATE_URL = '/menu/create?';
    const MENU_GET_URL = '/menu/get?';
    const MENU_DELETE_URL = '/menu/delete?';
    const CALLBACKSERVER_GET_URL = '/getcallbackip?';
    const QRCODE_CREATE_URL='/qrcode/create?';
    const QR_SCENE = 0;
    const QR_LIMIT_SCENE = 1;
    const QRCODE_IMG_URL='https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=';
    const SHORT_URL='/shorturl?';
    const USER_GET_URL='/user/get?';
    const USER_INFO_URL='/user/info?';
    const USER_UPDATEREMARK_URL='/user/info/updateremark?';
    const GROUP_GET_URL='/groups/get?';
    const USER_GROUP_URL='/groups/getid?';
    const GROUP_CREATE_URL='/groups/create?';
    const GROUP_UPDATE_URL='/groups/update?';
    const GROUP_MEMBER_UPDATE_URL='/groups/members/update?';
    const GROUP_MEMBER_BATCHUPDATE_URL='/groups/members/batchupdate?';
    const CUSTOM_SEND_URL='/message/custom/send?';
    const MEDIA_UPLOADNEWS_URL = '/media/uploadnews?';
    const MASS_SEND_URL = '/message/mass/send?';
    const TEMPLATE_SET_INDUSTRY_URL = '/message/template/api_set_industry?';
    const TEMPLATE_ADD_TPL_URL = '/message/template/api_add_template?';
    const TEMPLATE_SEND_URL = '/message/template/send?';
    const MASS_SEND_GROUP_URL = '/message/mass/sendall?';
    const MASS_DELETE_URL = '/message/mass/delete?';
    const MASS_PREVIEW_URL = '/message/mass/preview?';
    const MASS_QUERY_URL = '/message/mass/get?';
    const UPLOAD_MEDIA_URL = 'http://file.api.weixin.qq.com/cgi-bin';
    const MEDIA_UPLOAD_URL = '/media/upload?';
    const MEDIA_GET_URL = '/media/get?';
    const MEDIA_VIDEO_UPLOAD = '/media/uploadvideo?';
    const MEDIA_FOREVER_UPLOAD_URL = '/material/add_material?';
    const MEDIA_FOREVER_NEWS_UPLOAD_URL = '/material/add_news?';
    const MEDIA_FOREVER_NEWS_UPDATE_URL = '/material/update_news?';
    const MEDIA_FOREVER_GET_URL = '/material/get_material?';
    const MEDIA_FOREVER_DEL_URL = '/material/del_material?';
    const MEDIA_FOREVER_COUNT_URL = '/material/get_materialcount?';
    const MEDIA_FOREVER_BATCHGET_URL = '/material/batchget_material?';

    ///多客服相关地址
    const CUSTOM_SERVICE_GET_RECORD = '/customservice/getrecord?';
    const CUSTOM_SERVICE_GET_KFLIST = '/customservice/getkflist?';
    const CUSTOM_SERVICE_GET_ONLINEKFLIST = '/customservice/getonlinekflist?';

    ///多客服相关地址
    const CUSTOM_SESSION_CREATE = '/customservice/kfsession/create?';
    const CUSTOM_SESSION_CLOSE = '/customservice/kfsession/close?';
    const CUSTOM_SESSION_SWITCH = '/customservice/kfsession/switch?';
    const CUSTOM_SESSION_GET = '/customservice/kfsession/getsession?';
    const CUSTOM_SESSION_GET_LIST = '/customservice/kfsession/getsessionlist?';
    const CUSTOM_SESSION_GET_WAIT = '/customservice/kfsession/getwaitcase?';
    const CS_KF_ACCOUNT_ADD_URL = '/customservice/kfaccount/add?';
    const CS_KF_ACCOUNT_UPDATE_URL = '/customservice/kfaccount/update?';
    const CS_KF_ACCOUNT_DEL_URL = '/customservice/kfaccount/del?';
    const CS_KF_ACCOUNT_UPLOAD_HEADIMG_URL = '/customservice/kfaccount/uploadheadimg?';
    ///卡券相关地址
    const CARD_CREATE                     = '/card/create?';
    const CARD_DELETE                     = '/card/delete?';
    const CARD_UPDATE                     = '/card/update?';
    const CARD_GET                        = '/card/get?';
    const CARD_BATCHGET                   = '/card/batchget?';
    const CARD_MODIFY_STOCK               = '/card/modifystock?';
    const CARD_LOCATION_BATCHADD          = '/card/location/batchadd?';
    const CARD_LOCATION_BATCHGET          = '/card/location/batchget?';
    const CARD_GETCOLORS                  = '/card/getcolors?';
    const CARD_QRCODE_CREATE              = '/card/qrcode/create?';
    const CARD_CODE_CONSUME               = '/card/code/consume?';
    const CARD_CODE_DECRYPT               = '/card/code/decrypt?';
    const CARD_CODE_GET                   = '/card/code/get?';
    const CARD_CODE_UPDATE                = '/card/code/update?';
    const CARD_CODE_UNAVAILABLE           = '/card/code/unavailable?';
    const CARD_TESTWHILELIST_SET          = '/card/testwhitelist/set?';
    const CARD_MEMBERCARD_ACTIVATE        = '/card/membercard/activate?';      //激活会员卡
    const CARD_MEMBERCARD_UPDATEUSER      = '/card/membercard/updateuser?';    //更新会员卡
    const CARD_MOVIETICKET_UPDATEUSER     = '/card/movieticket/updateuser?';   //更新电影票(未加方法)
    const CARD_BOARDINGPASS_CHECKIN       = '/card/boardingpass/checkin?';     //飞机票-在线选座(未加方法)
    const CARD_LUCKYMONEY_UPDATE          = '/card/luckymoney/updateuserbalance?';     //更新红包金额
    const SEMANTIC_API_URL = '/semantic/semproxy/search?'; //语义理解
    ///数据分析接口
    static $DATACUBE_URL_ARR = array(        //用户分析
            'user' => array(
                    'summary' => '/datacube/getusersummary?',       //获取用户增减数据（getusersummary）
                    'cumulate' => '/datacube/getusercumulate?',     //获取累计用户数据（getusercumulate）
            ),
            'article' => array(            //图文分析
                    'summary' => '/datacube/getarticlesummary?',        //获取图文群发每日数据（getarticlesummary）
                    'total' => '/datacube/getarticletotal?',        //获取图文群发总数据（getarticletotal）
                    'read' => '/datacube/getuserread?',         //获取图文统计数据（getuserread）
                    'readhour' => '/datacube/getuserreadhour?',     //获取图文统计分时数据（getuserreadhour）
                    'share' => '/datacube/getusershare?',           //获取图文分享转发数据（getusershare）
                    'sharehour' => '/datacube/getusersharehour?',       //获取图文分享转发分时数据（getusersharehour）
            ),
            'upstreammsg' => array(        //消息分析
                    'summary' => '/datacube/getupstreammsg?',       //获取消息发送概况数据（getupstreammsg）
                    'hour' => '/datacube/getupstreammsghour?',  //获取消息分送分时数据（getupstreammsghour）
                    'week' => '/datacube/getupstreammsgweek?',  //获取消息发送周数据（getupstreammsgweek）
                    'month' => '/datacube/getupstreammsgmonth?',    //获取消息发送月数据（getupstreammsgmonth）
                    'dist' => '/datacube/getupstreammsgdist?',  //获取消息发送分布数据（getupstreammsgdist）
                    'distweek' => '/datacube/getupstreammsgdistweek?',  //获取消息发送分布周数据（getupstreammsgdistweek）
                    'distmonth' => '/datacube/getupstreammsgdistmonth?',    //获取消息发送分布月数据（getupstreammsgdistmonth）
            ),
            'interface' => array(        //接口分析
                    'summary' => '/datacube/getinterfacesummary?',  //获取接口分析数据（getinterfacesummary）
                    'summaryhour' => '/datacube/getinterfacesummaryhour?',  //获取接口分析分时数据（getinterfacesummaryhour）
            )
    );

    ///微信摇一摇周边
    const SHAKEAROUND_DEVICE_APPLYID = '/shakearound/device/applyid?';//申请设备ID
    const SHAKEAROUND_DEVICE_SEARCH = '/shakearound/device/search?';//查询设备列表
    const SHAKEAROUND_DEVICE_BINDLOCATION = '/shakearound/device/bindlocation?';//配置设备与门店ID的关系
    const SHAKEAROUND_DEVICE_BINDPAGE = '/shakearound/device/bindpage?';//配置设备与页面的绑定关系
    const SHAKEAROUND_PAGE_ADD = '/shakearound/page/add?';//增加页面
    const SHAKEAROUND_PAGE_UPDATE = '/shakearound/page/update?';//编辑页面
    const SHAKEAROUND_PAGE_SEARCH = '/shakearound/page/search?';//查询页面列表
    const SHAKEAROUND_PAGE_DELETE = '/shakearound/page/delete?';//删除页面
    const SHAKEAROUND_USER_GETSHAKEINFO = '/shakearound/user/getshakeinfo?';//获取摇周边的设备及用户信息
    const SHAKEAROUND_STATISTICS_DEVICE = '/shakearound/statistics/device?';//以设备为维度的数据统计接口


    //private $partnerid;
    //private $partnerkey;
    //private $paysignkey;
    private $postxml;
    private $_msg;
    private $_funcflag = false;
    private $_receive;
    private $_text_filter = true;

    public function __construct($options)
    {
        parent::__construct($options);
    }

    /**
     * 设置发送消息
     * @param array $msg 消息数组
     * @param bool $append 是否在原消息数组追加
     * @return mixed
     */
    public function Message($msg = array(),$append = false){
        if (is_null($msg)) {
            $this->_msg =array();
        }elseif (is_array($msg)) {
            if ($append)
                $this->_msg = array_merge($this->_msg,$msg);
            else
                $this->_msg = $msg;
        }

        return $this->_msg;
    }

    /**
     * 设置消息的星标标志，官方已取消对此功能的支持
     * @param $flag
     * @return $this
     */
    public function setFuncFlag($flag) {
        $this->_funcflag = $flag;
        return $this;
    }

    /**
     * 获取微信服务器发来的信息
     */
    public function getRev()
    {
        if ($this->_receive) return $this;
        $postStr = !empty($this->postxml)?$this->postxml:file_get_contents("php://input");
        //兼顾使用明文又不想调用valid()方法的情况
        $this->log($postStr);
        if (!empty($postStr)) {
            $this->_receive = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        }
        return $this;
    }

    /**
     * 获取微信服务器发来的信息
     */
    public function getRevData()
    {
        return $this->_receive;
    }

    /**
     * 获取消息发送者
     */
    public function getRevFrom() {
        if (isset($this->_receive['FromUserName']))
            return $this->_receive['FromUserName'];
        else
            return false;
    }

    /**
     * 获取消息接受者
     */
    public function getRevTo() {
        if (isset($this->_receive['ToUserName']))
            return $this->_receive['ToUserName'];
        else
            return false;
    }

    /**
     * 获取接收消息的类型
     */
    public function getRevType() {
        if (isset($this->_receive['MsgType']))
            return $this->_receive['MsgType'];
        else
            return false;
    }

    /**
     * 获取消息ID
     */
    public function getRevID() {
        if (isset($this->_receive['MsgId']))
            return $this->_receive['MsgId'];
        else
            return false;
    }

    /**
     * 获取消息发送时间
     */
    public function getRevCtime() {
        if (isset($this->_receive['CreateTime']))
            return $this->_receive['CreateTime'];
        else
            return false;
    }

    /**
     * 获取接收消息内容正文
     */
    public function getRevContent(){
        if (isset($this->_receive['Content']))
            return $this->_receive['Content'];
        else if (isset($this->_receive['Recognition'])) //获取语音识别文字内容，需申请开通
            return $this->_receive['Recognition'];
        else
            return false;
    }

    /**
     * 获取接收消息图片
     */
    public function getRevPic(){
        if (isset($this->_receive['PicUrl']))
            return array(
                'mediaid'=>$this->_receive['MediaId'],
                'picurl'=>(string)$this->_receive['PicUrl'],    //防止picurl为空导致解析出错
            );
        else
            return false;
    }

    /**
     * 获取接收消息链接
     */
    public function getRevLink(){
        if (isset($this->_receive['Url'])){
            return array(
                'url'=>$this->_receive['Url'],
                'title'=>$this->_receive['Title'],
                'description'=>$this->_receive['Description']
            );
        } else
            return false;
    }

    /**
     * 获取接收地理位置
     */
    public function getRevGeo(){
        if (isset($this->_receive['Location_X'])){
            return array(
                'x'=>$this->_receive['Location_X'],
                'y'=>$this->_receive['Location_Y'],
                'scale'=>$this->_receive['Scale'],
                'label'=>$this->_receive['Label']
            );
        } else
            return false;
    }

    /**
     * 获取上报地理位置事件
     */
    public function getRevEventGeo(){
        if (isset($this->_receive['Latitude'])){
             return array(
                'x'=>$this->_receive['Latitude'],
                'y'=>$this->_receive['Longitude'],
                'precision'=>$this->_receive['Precision'],
             );
        } else
            return false;
    }

    /**
     * 获取接收事件推送
     */
    public function getRevEvent(){
        if (isset($this->_receive['Event'])){
            $array['event'] = $this->_receive['Event'];
        }
        if (isset($this->_receive['EventKey'])){
            $array['key'] = $this->_receive['EventKey'];
        }
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取自定义菜单的扫码推事件信息
     * @return array | bool
     */
    public function getRevScanInfo(){
        if (isset($this->_receive['ScanCodeInfo'])){
            if (!is_array($this->_receive['ScanCodeInfo'])) {
                $array=(array)$this->_receive['ScanCodeInfo'];
                $this->_receive['ScanCodeInfo']=$array;
            }else {
                $array=$this->_receive['ScanCodeInfo'];
            }
        }
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取自定义菜单的图片发送事件信息
     * @return array | false
     */
    public function getRevSendPicsInfo(){
        if (isset($this->_receive['SendPicsInfo'])){
            if (!is_array($this->_receive['SendPicsInfo'])) {
                $array=(array)$this->_receive['SendPicsInfo'];
                if (isset($array['PicList'])){
                    $array['PicList']=(array)$array['PicList'];
                    $item=$array['PicList']['item'];
                    $array['PicList']['item']=array();
                    foreach ( $item as $key => $value ){
                        $array['PicList']['item'][$key]=(array)$value;
                    }
                }
                $this->_receive['SendPicsInfo']=$array;
            } else {
                $array=$this->_receive['SendPicsInfo'];
            }
        }
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取自定义菜单的地理位置选择器事件推送
     *
     * 事件类型为以下时则可以调用此方法有效
     * Event     事件类型，location_select        弹出地理位置选择器的事件推送
     *
     * @return array | bool
     */
    public function getRevSendGeoInfo(){
        if (isset($this->_receive['SendLocationInfo'])){
            if (!is_array($this->_receive['SendLocationInfo'])) {
                $array=(array)$this->_receive['SendLocationInfo'];
                if (empty($array['Poiname'])) {
                    $array['Poiname']="";
                }
                if (empty($array['Label'])) {
                    $array['Label']="";
                }
                $this->_receive['SendLocationInfo']=$array;
            } else {
                $array=$this->_receive['SendLocationInfo'];
            }
        }
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取接收语音推送
     */
    public function getRevVoice(){
        if (isset($this->_receive['MediaId'])){
            return array(
                'mediaid'=>$this->_receive['MediaId'],
                'format'=>$this->_receive['Format'],
            );
        } else
            return false;
    }

    /**
     * 获取接收视频推送
     */
    public function getRevVideo(){
        if (isset($this->_receive['MediaId'])){
            return array(
                    'mediaid'=>$this->_receive['MediaId'],
                    'thumbmediaid'=>$this->_receive['ThumbMediaId']
            );
        } else
            return false;
    }

    /**
     * 获取接收TICKET
     */
    public function getRevTicket(){
        if (isset($this->_receive['Ticket'])){
            return $this->_receive['Ticket'];
        } else
            return false;
    }

    /**
    * 获取二维码的场景值
    */
    public function getRevSceneId (){
        if (isset($this->_receive['EventKey'])){
            return str_replace('qrscene_','',$this->_receive['EventKey']);
        } else{
            return false;
        }
    }

    /**
    * 获取主动推送的消息ID
    * 经过验证，这个和普通的消息MsgId不一样
    * 当Event为 MASSSENDJOBFINISH 或 TEMPLATESENDJOBFINISH
    */
    public function getRevTplMsgID(){
        if (isset($this->_receive['MsgID'])){
            return $this->_receive['MsgID'];
        } else
            return false;
    }

    /**
    * 获取模板消息发送状态
    */
    public function getRevStatus(){
        if (isset($this->_receive['Status'])){
            return $this->_receive['Status'];
        } else
            return false;
    }

    /**
    * 获取群发或模板消息发送结果
    * 当Event为 MASSSENDJOBFINISH 或 TEMPLATESENDJOBFINISH，即高级群发/模板消息
    */
    public function getRevResult(){
        if (isset($this->_receive['Status'])) //发送是否成功，具体的返回值请参考 高级群发/模板消息 的事件推送说明
            $array['Status'] = $this->_receive['Status'];
        if (isset($this->_receive['MsgID'])) //发送的消息id
            $array['MsgID'] = $this->_receive['MsgID'];

        //以下仅当群发消息时才会有的事件内容
        if (isset($this->_receive['TotalCount']))     //分组或openid列表内粉丝数量
            $array['TotalCount'] = $this->_receive['TotalCount'];
        if (isset($this->_receive['FilterCount']))    //过滤（过滤是指特定地区、性别的过滤、用户设置拒收的过滤，用户接收已超4条的过滤）后，准备发送的粉丝数
            $array['FilterCount'] = $this->_receive['FilterCount'];
        if (isset($this->_receive['SentCount']))     //发送成功的粉丝数
            $array['SentCount'] = $this->_receive['SentCount'];
        if (isset($this->_receive['ErrorCount']))    //发送失败的粉丝数
            $array['ErrorCount'] = $this->_receive['ErrorCount'];
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取多客服会话状态推送事件 - 接入会话
     * 当Event为 kfcreatesession 即接入会话
     * @return string | boolean  返回分配到的客服
     */
    public function getRevKFCreate(){
        if (isset($this->_receive['KfAccount'])){
            return $this->_receive['KfAccount'];
        } else
            return false;
    }

    /**
     * 获取多客服会话状态推送事件 - 关闭会话
     * 当Event为 kfclosesession 即关闭会话
     * @return string | boolean  返回分配到的客服
     */
    public function getRevKFClose(){
        if (isset($this->_receive['KfAccount'])){
            return $this->_receive['KfAccount'];
        } else
            return false;
    }

    /**
     * 获取多客服会话状态推送事件 - 转接会话
     * 当Event为 kfswitchsession 即转接会话
     * @return array | boolean  返回分配到的客服
     * {
     *     'FromKfAccount' => '',      //原接入客服
     *     'ToKfAccount' => ''            //转接到客服
     * }
     */
    public function getRevKFSwitch(){
        if (isset($this->_receive['FromKfAccount']))     //原接入客服
            $array['FromKfAccount'] = $this->_receive['FromKfAccount'];
        if (isset($this->_receive['ToKfAccount']))    //转接到客服
            $array['ToKfAccount'] = $this->_receive['ToKfAccount'];
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取卡券事件推送 - 卡卷审核是否通过
     * 当Event为 card_pass_check(审核通过) 或 card_not_pass_check(未通过)
     * @return string|boolean  返回卡券ID
     */
    public function getRevCardPass(){
        if (isset($this->_receive['CardId']))
            return $this->_receive['CardId'];
        else
            return false;
    }

    /**
     * 获取卡券事件推送 - 领取卡券
     * 当Event为 user_get_card(用户领取卡券)
     * @return array|boolean
     */
    public function getRevCardGet(){
        if (isset($this->_receive['CardId']))     //卡券 ID
            $array['CardId'] = $this->_receive['CardId'];
        if (isset($this->_receive['IsGiveByFriend']))    //是否为转赠，1 代表是，0 代表否。
            $array['IsGiveByFriend'] = $this->_receive['IsGiveByFriend'];
        if (isset($this->_receive['UserCardCode']) && !empty($this->_receive['UserCardCode'])) //code 序列号。自定义 code 及非自定义 code的卡券被领取后都支持事件推送。
            $array['UserCardCode'] = $this->_receive['UserCardCode'];
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取卡券事件推送 - 删除卡券
     * 当Event为 user_del_card(用户删除卡券)
     * @return array|boolean
     */
    public function getRevCardDel(){
        if (isset($this->_receive['CardId']))     //卡券 ID
            $array['CardId'] = $this->_receive['CardId'];
        if (isset($this->_receive['UserCardCode']) && !empty($this->_receive['UserCardCode'])) //code 序列号。自定义 code 及非自定义 code的卡券被领取后都支持事件推送。
            $array['UserCardCode'] = $this->_receive['UserCardCode'];
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    public static function xmlSafeStr($str)
    {
        return '<![CDATA['.preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/",'',$str).']]>';
    }

    /**
     * 数据XML编码
     * @param mixed $data 数据
     * @param string $item
     * @param string $id
     * @return string
     */
    public static function data_to_xml($data,$item='item',$id='id') {
        $xml = '';
        foreach ($data as $key => $val) {
            $xml    .=  "<$item";
            $xml    .=  " $id=\"$key\"";
            $xml    .=  ">";
            $xml    .=  ( is_array($val) || is_object($val)) ? self::data_to_xml($val)  : self::xmlSafeStr($val);
            //list($key, ) = explode(' ', $key);
            $xml    .=  "</$item>";
        }
        return $xml;
    }

    /**
     * XML编码
     * @param mixed $data 数据
     * @param string $root 根节点名
     * @param string $item 数字索引的子节点名
     * @param string $attr 根节点属性
     * @param string $id   数字索引子节点key转换的属性名
     * @return string
    */
    public function xml_encode($data, $root='xml', $item='item', $attr='', $id='id') {
        if(is_array($attr)){
            $_attr = array();
            foreach ($attr as $key => $value) {
                $_attr[] = "{$key}=\"{$value}\"";
            }
            $attr = implode(' ', $_attr);
        }
        $attr   = trim($attr);
        $attr   = empty($attr) ? '' : " {$attr}";
        $xml   = "<{$root}{$attr}>";
        $xml   .= self::data_to_xml($data, $item, $id);
        $xml   .= "</{$root}>";
        return $xml;
    }

    /**
     * 过滤文字回复\r\n换行符
     * @param string $text
     * @return string|mixed
     */
    private function _auto_text_filter($text) {
        if (!$this->_text_filter) return $text;
        return str_replace("\r\n", "\n", $text);
    }

    /**
     * 设置回复消息
     * Example: $obj->text('hello')->reply();
     * @param string $text
     * @return Wechat
     */
    public function text($text='')
    {
        $FuncFlag = $this->_funcflag ? 1 : 0;
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName'=>$this->getRevTo(),
            'MsgType'=>self::MSGTYPE_TEXT,
            'Content'=>$this->_auto_text_filter($text),
            'CreateTime'=>time(),
            'FuncFlag'=>$FuncFlag
        );
        $this->Message($msg);
        return $this;
    }
    /**
     * 设置回复消息
     * Example: $obj->image('media_id')->reply();
     * @param string $mediaid
     * @return Wechat
     */
    public function image($mediaid='')
    {
        $FuncFlag = $this->_funcflag ? 1 : 0;
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName'=>$this->getRevTo(),
            'MsgType'=>self::MSGTYPE_IMAGE,
            'Image'=>array('MediaId'=>$mediaid),
            'CreateTime'=>time(),
            'FuncFlag'=>$FuncFlag
        );
        $this->Message($msg);
        return $this;
    }

    /**
     * 设置回复消息
     * Example: $obj->voice('media_id')->reply();
     * @param string $mediaid
     * @return Wechat
     */
    public function voice($mediaid='')
    {
        $FuncFlag = $this->_funcflag ? 1 : 0;
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName'=>$this->getRevTo(),
            'MsgType'=>self::MSGTYPE_VOICE,
            'Voice'=>array('MediaId'=>$mediaid),
            'CreateTime'=>time(),
            'FuncFlag'=>$FuncFlag
        );
        $this->Message($msg);
        return $this;
    }

    /**
     * 设置回复消息
     * Example: $obj->video('media_id','title','description')->reply();
     * @param string $mediaid
     * @return Wechat
     */
    public function video($mediaid='',$title='',$description='')
    {
        $FuncFlag = $this->_funcflag ? 1 : 0;
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName'=>$this->getRevTo(),
            'MsgType'=>self::MSGTYPE_VIDEO,
            'Video'=>array(
                    'MediaId'=>$mediaid,
                    'Title'=>$title,
                    'Description'=>$description
            ),
            'CreateTime'=>time(),
            'FuncFlag'=>$FuncFlag
        );
        $this->Message($msg);
        return $this;
    }

    /**
     * 设置回复音乐
     * @param string $title
     * @param string $desc
     * @param string $musicurl
     * @param string $hgmusicurl
     * @param string $thumbmediaid 音乐图片缩略图的媒体id，非必须
     * @return Wechat
     */
    public function music($title,$desc,$musicurl,$hgmusicurl='',$thumbmediaid='') {
        $FuncFlag = $this->_funcflag ? 1 : 0;
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName'=>$this->getRevTo(),
            'CreateTime'=>time(),
            'MsgType'=>self::MSGTYPE_MUSIC,
            'Music'=>array(
                'Title'=>$title,
                'Description'=>$desc,
                'MusicUrl'=>$musicurl,
                'HQMusicUrl'=>$hgmusicurl
            ),
            'FuncFlag'=>$FuncFlag
        );
        if ($thumbmediaid) {
            $msg['Music']['ThumbMediaId'] = $thumbmediaid;
        }
        $this->Message($msg);
        return $this;
    }

    /**
     * 设置回复图文
     * @param array $newsData
     * @return Wechat
     */
    public function news($newsData=array())
    {
        $FuncFlag = $this->_funcflag ? 1 : 0;
        $count = count($newsData);

        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName'=>$this->getRevTo(),
            'MsgType'=>self::MSGTYPE_NEWS,
            'CreateTime'=>time(),
            'ArticleCount'=>$count,
            'Articles'=>$newsData,
            'FuncFlag'=>$FuncFlag
        );
        $this->Message($msg);
        return $this;
    }

    /**
     *
     * 回复微信服务器, 此函数支持链式操作
     * Example: $this->text('msg tips')->reply();
     * @param array $msg 要发送的信息, 默认取$this->_msg
     * @param bool $return 是否返回信息而不抛出到浏览器 默认:否
     * @return string|bool
     */
    public function reply($msg=array(),$return = false)
    {
        if (empty($msg)) {
            if (empty($this->_msg))   //防止不先设置回复内容，直接调用reply方法导致异常
                return false;
            $msg = $this->_msg;
        }
        $xmldata=  $this->xml_encode($msg);
        $this->log($xmldata);
        if ($this->encrypt_type == 'aes') { //如果来源消息为加密方式
            $pc = new Prpcrypt($this->encodingAesKey);
            $array = $pc->encrypt($xmldata, $this->appid);
            $ret = $array[0];
            if ($ret != 0) {
                $this->log('encrypt err!');
                return false;
            }
            $timestamp = time();
            $nonce = rand(77,999)*rand(605,888)*rand(11,99);
            $encrypt = $array[1];
            $tmpArr = array($this->token, $timestamp, $nonce,$encrypt);//比普通公众平台多了一个加密的密文
            sort($tmpArr, SORT_STRING);
            $signature = implode($tmpArr);
            $signature = sha1($signature);
            $xmldata = $this->generate($encrypt, $signature, $timestamp, $nonce);
            $this->log($xmldata);
        }
        if ($return)
            return $xmldata;
        else
            echo $xmldata;
        return true;
    }

    /**
     * xml格式加密，仅请求为加密方式时再用
     * @param $encrypt
     * @param $signature
     * @param $timestamp
     * @param $nonce
     * @return string
     */
    private function generate($encrypt, $signature, $timestamp, $nonce)
    {
        //格式化加密信息
        $format = "<xml>
<Encrypt><![CDATA[%s]]></Encrypt>
<MsgSignature><![CDATA[%s]]></MsgSignature>
<TimeStamp>%s</TimeStamp>
<Nonce><![CDATA[%s]]></Nonce>
</xml>";
        return sprintf($format, $encrypt, $signature, $timestamp, $nonce);
    }



    /**
     * 获取微信服务器IP地址列表
     * @return array|bool ('127.0.0.1','127.0.0.1')
     */
    public function getServerIp(){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_get(self::API_URL_PREFIX.self::CALLBACKSERVER_GET_URL.'access_token='.$this->access_token);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json['ip_list'];
        }
        return false;
    }

    /**
     * 创建菜单(认证后的订阅号可用)
     * @param array $data 菜单数组数据
     * doc: https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141013
     * @return bool
     */
    public function createMenu($data){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_URL_PREFIX.self::MENU_CREATE_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 获取菜单(认证后的订阅号可用)
     * @return array|bool ('menu'=>array(....s))
     */
    public function getMenu(){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_get(self::API_URL_PREFIX.self::MENU_GET_URL.'access_token='.$this->access_token);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 删除菜单(认证后的订阅号可用)
     * @return boolean
     */
    public function deleteMenu(){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_get(self::API_URL_PREFIX.self::MENU_DELETE_URL.'access_token='.$this->access_token);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 上传临时素材，有效期为3天(认证后的订阅号可用)
     * 注意：上传大文件时可能需要先调用 set_time_limit(0) 避免超时
     * 注意：数组的键值任意，但文件名前必须加@，使用单引号以避免本地路径斜杠被转义
     * 注意：临时素材的media_id是可复用的！
     * @param array $data {"media":'@Path\filename.jpg'}
     * @param $type string 图片:image 语音:voice 视频:video 缩略图:thumb
     * @return boolean|array
     */
    public function uploadMedia($data, $type){
        if (!$this->access_token && !$this->checkAuth()) return false;
        //原先的上传多媒体文件接口使用 self::UPLOAD_MEDIA_URL 前缀
        $result = $this->http_post(self::API_URL_PREFIX.self::MEDIA_UPLOAD_URL.'access_token='.$this->access_token.'&type='.$type,$data,true);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取临时素材(认证后的订阅号可用)
     * @param string $media_id 媒体文件id
     * @param boolean $is_video 是否为视频文件，默认为否
     * @return string|bool
     */
    public function getMedia($media_id,$is_video=false){
        if (!$this->access_token && !$this->checkAuth()) return false;
        //原先的上传多媒体文件接口使用 self::UPLOAD_MEDIA_URL 前缀
        //如果要获取的素材是视频文件时，不能使用https协议，必须更换成http协议
        $url_prefix = $is_video?str_replace('https','http',self::API_URL_PREFIX):self::API_URL_PREFIX;
        $result = $this->http_get($url_prefix.self::MEDIA_GET_URL.'access_token='.$this->access_token.'&media_id='.$media_id);
        if ($result)
        {
            if (is_string($result)) {
                $json = json_decode($result,true);
                if (isset($json['errcode'])) {
                    $this->errCode = $json['errcode'];
                    $this->errMsg = $json['errmsg'];
                    return false;
                }
            }
            return $result;
        }
        return false;
    }


    /**
     * 上传永久素材(认证后的订阅号可用)
     * 新增的永久素材也可以在公众平台官网素材管理模块中看到
     * 注意：上传大文件时可能需要先调用 set_time_limit(0) 避免超时
     * 注意：数组的键值任意，但文件名前必须加@，使用单引号以避免本地路径斜杠被转义
     * @param $data array {"media":'@Path\filename.jpg'}
     * @param $type string 图片:image 语音:voice 视频:video 缩略图:thumb
     * @param $is_video boolean 是否为视频文件，默认为否
     * @param $video_info array 视频信息数组，非视频素材不需要提供 array('title'=>'视频标题','introduction'=>'描述')
     * @return boolean|array
     */
    public function uploadForeverMedia($data, $type,$is_video=false,$video_info=array()){
        if (!$this->access_token && !$this->checkAuth()) return false;
        //#TODO 暂不确定此接口是否需要让视频文件走http协议
        //如果要获取的素材是视频文件时，不能使用https协议，必须更换成http协议
        //$url_prefix = $is_video?str_replace('https','http',self::API_URL_PREFIX):self::API_URL_PREFIX;
        //当上传视频文件时，附加视频文件信息
        if ($is_video) $data['description'] = self::json_encode($video_info);
        $result = $this->http_post(self::API_URL_PREFIX.self::MEDIA_FOREVER_UPLOAD_URL.'access_token='.$this->access_token.'&type='.$type,$data,true);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 上传永久图文素材(认证后的订阅号可用)
     * 新增的永久素材也可以在公众平台官网素材管理模块中看到
     * @param array $data 消息结构{"articles":[{...}]}
     * @return boolean|array
     */
    public function uploadForeverArticles($data){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_URL_PREFIX.self::MEDIA_FOREVER_NEWS_UPLOAD_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 修改永久图文素材(认证后的订阅号可用)
     * 永久素材也可以在公众平台官网素材管理模块中看到
     * @param string $media_id 图文素材id
     * @param array $data 消息结构{"articles":[{...}]}
     * @param int $index 更新的文章在图文素材的位置，第一篇为0，仅多图文使用
     * @return boolean|array
     */
    public function updateForeverArticles($media_id,$data,$index=0){
        if (!$this->access_token && !$this->checkAuth()) return false;
        if (!isset($data['media_id'])) $data['media_id'] = $media_id;
        if (!isset($data['index'])) $data['index'] = $index;
        $result = $this->http_post(self::API_URL_PREFIX.self::MEDIA_FOREVER_NEWS_UPDATE_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取永久素材(认证后的订阅号可用)
     * 返回图文消息数组或二进制数据，失败返回false
     * @param string $media_id 媒体文件id
     * @param boolean $is_video 是否为视频文件，默认为否
     * @return boolean|array|string data
     */
    public function getForeverMedia($media_id, $is_video=false){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $data = array('media_id' => $media_id);
        //#TODO 暂不确定此接口是否需要让视频文件走http协议
        //如果要获取的素材是视频文件时，不能使用https协议，必须更换成http协议
        //$url_prefix = $is_video?str_replace('https','http',self::API_URL_PREFIX):self::API_URL_PREFIX;
        $result = $this->http_post(self::API_URL_PREFIX.self::MEDIA_FOREVER_GET_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            if (is_string($result)) {
                $json = json_decode($result,true);
                if (isset($json['errcode'])) {
                    $this->errCode = $json['errcode'];
                    $this->errMsg = $json['errmsg'];
                    return false;
                }
                return $json;
            }
            return $result;
        }
        return false;
    }

    /**
     * 删除永久素材(认证后的订阅号可用)
     * @param string $media_id 媒体文件id
     * @return boolean
     */
    public function delForeverMedia($media_id){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $data = array('media_id' => $media_id);
        $result = $this->http_post(self::API_URL_PREFIX.self::MEDIA_FOREVER_DEL_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 获取永久素材列表(认证后的订阅号可用)
     * @param string $type 素材的类型,图片（image）、视频（video）、语音 （voice）、图文（news）
     * @param int $offset 全部素材的偏移位置，0表示从第一个素材
     * @param int $count 返回素材的数量，取值在1到20之间
     * @return boolean|array
     */
    public function getForeverList($type,$offset,$count){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $data = array(
            'type' => $type,
            'offset' => $offset,
            'count' => $count,
        );
        $result = $this->http_post(self::API_URL_PREFIX.self::MEDIA_FOREVER_BATCHGET_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取永久素材总数(认证后的订阅号可用)
     * @return boolean|array
     */
    public function getForeverCount(){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_get(self::API_URL_PREFIX.self::MEDIA_FOREVER_COUNT_URL.'access_token='.$this->access_token);
        if ($result)
        {
            $json = json_decode($result,true);
            if (isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 上传图文消息素材，用于群发(认证后的订阅号可用)
     * @param array $data 消息结构{"articles":[{...}]}
     * @return boolean|array
     */
    public function uploadArticles($data){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_URL_PREFIX.self::MEDIA_UPLOADNEWS_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 上传视频素材(认证后的订阅号可用)
     * @param array $data 消息结构
     * @return boolean|array
     */
    public function uploadMpVideo($data){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::UPLOAD_MEDIA_URL.self::MEDIA_VIDEO_UPLOAD.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 高级群发消息, 根据OpenID列表群发图文消息(订阅号不可用)
     *  注意：视频需要在调用uploadMedia()方法后，再使用 uploadMpVideo() 方法生成，
     *             然后获得的 mediaid 才能用于群发，且消息类型为 mpvideo 类型。
     * @param array $data 消息结构
     * @return boolean|array
     */
    public function sendMassMessage($data){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_URL_PREFIX.self::MASS_SEND_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 高级群发消息, 根据群组id群发图文消息(认证后的订阅号可用)
     *  注意：视频需要在调用uploadMedia()方法后，再使用 uploadMpVideo() 方法生成，
     *             然后获得的 mediaid 才能用于群发，且消息类型为 mpvideo 类型。
     * @param array $data 消息结构
     * @return boolean|array
     */
    public function sendGroupMassMessage($data){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_URL_PREFIX.self::MASS_SEND_GROUP_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 高级群发消息, 删除群发图文消息(认证后的订阅号可用)
     * @param int $msg_id 消息id
     * @return boolean|array
     */
    public function deleteMassMessage($msg_id){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_URL_PREFIX.self::MASS_DELETE_URL.'access_token='.$this->access_token,self::json_encode(array('msg_id'=>$msg_id)));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 高级群发消息, 预览群发消息(认证后的订阅号可用)
     *  注意：视频需要在调用uploadMedia()方法后，再使用 uploadMpVideo() 方法生成，
     *             然后获得的 mediaid 才能用于群发，且消息类型为 mpvideo 类型。
     * @param array $data 消息结构
     * @return boolean|array
     */
    public function previewMassMessage($data){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_URL_PREFIX.self::MASS_PREVIEW_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 高级群发消息, 查询群发消息发送状态(认证后的订阅号可用)
     * @param int $msg_id 消息id
     */
    public function queryMassMessage($msg_id){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_URL_PREFIX.self::MASS_QUERY_URL.'access_token='.$this->access_token,self::json_encode(array('msg_id'=>$msg_id)));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 创建二维码ticket
     * @param int|string $scene_id 自定义追踪id,临时二维码只能用数值型
     * @param int $type 0:临时二维码；1:永久二维码(此时expire参数无效)；2:永久二维码(此时expire参数无效)
     * @param int $expire 临时二维码有效期，最大为1800秒
     * @return array|bool
     * array('ticket'=>'qrcode字串','expire_seconds'=>1800,'url'=>'二维码图片解析后的地址')
     */
    public function getQRCode($scene_id,$type=0,$expire=1800){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $type = ($type && is_string($scene_id))?2:$type;
        $data = array(
            'action_name'=>$type?($type == 2?"QR_LIMIT_STR_SCENE":"QR_LIMIT_SCENE"):"QR_SCENE",
            'expire_seconds'=>$expire,
            'action_info'=>array('scene'=>($type == 2?array('scene_str'=>$scene_id):array('scene_id'=>$scene_id)))
        );
        if ($type == 1) {
            unset($data['expire_seconds']);
        }
        $result = $this->http_post(self::API_URL_PREFIX.self::QRCODE_CREATE_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取二维码图片
     * @param string $ticket 传入由getQRCode方法生成的ticket参数
     * @return string url 返回http地址
     */
    public function getQRUrl($ticket) {
        return self::QRCODE_IMG_URL.urlencode($ticket);
    }

    /**
     * 长链接转短链接接口
     * @param string $long_url 传入要转换的长url
     * @return boolean|string url 成功则返回转换后的短url
     */
    public function getShortUrl($long_url){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $data = array(
            'action'=>'long2short',
            'long_url'=>$long_url
        );
        $result = $this->http_post(self::API_URL_PREFIX.self::SHORT_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json['short_url'];
        }
        return false;
    }

    /**
     * 获取统计数据
     * @param string $type  数据分类(user|article|upstreammsg|interface)分别为(用户分析|图文分析|消息分析|接口分析)
     * @param string $subtype   数据子分类，参考 DATACUBE_URL_ARR 常量定义部分 或者README.md说明文档
     * @param string $begin_date 开始时间
     * @param string $end_date   结束时间
     * @return boolean|array 成功返回查询结果数组，其定义请看官方文档
     */
    public function getDatacube($type,$subtype,$begin_date,$end_date=''){
        if (!$this->access_token && !$this->checkAuth()) return false;
        if (!isset(self::$DATACUBE_URL_ARR[$type]) || !isset(self::$DATACUBE_URL_ARR[$type][$subtype]))
            return false;
        $data = array(
            'begin_date'=>$begin_date,
            'end_date'=>$end_date?$end_date:$begin_date
        );
        $result = $this->http_post(self::API_BASE_URL_PREFIX.self::$DATACUBE_URL_ARR[$type][$subtype].'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return isset($json['list'])?$json['list']:$json;
        }
        return false;
    }

    /**
     * 批量获取关注用户列表
     * @param string $next_openid
     * @return array|bool
     */
    public function getUserList($next_openid=''){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_get(self::API_URL_PREFIX.self::USER_GET_URL.'access_token='.$this->access_token.'&next_openid='.$next_openid);
        if ($result)
        {
            $json = json_decode($result,true);
            if (isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取关注者详细信息
     * @param string $openid
     * @return array|bool
     * {subscribe,openid,nickname,sex,city,province,country,language,headimgurl,subscribe_time,[unionid]}
     * 注意：unionid字段 只有在用户将公众号绑定到微信开放平台账号后，才会出现。建议调用前用isset()检测一下
     */
    public function getUserInfo($openid){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_get(self::API_URL_PREFIX.self::USER_INFO_URL.'access_token='.$this->access_token.'&openid='.$openid);
        if ($result)
        {
            $json = json_decode($result,true);
            if (isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 设置用户备注名
     * @param string $openid
     * @param string $remark 备注名
     * @return boolean|array
     */
    public function updateUserRemark($openid,$remark){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $data = array(
            'openid'=>$openid,
            'remark'=>$remark
        );
        $result = $this->http_post(self::API_URL_PREFIX.self::USER_UPDATEREMARK_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取用户分组列表
     * @return boolean|array
     */
    public function getGroup(){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_get(self::API_URL_PREFIX.self::GROUP_GET_URL.'access_token='.$this->access_token);
        if ($result)
        {
            $json = json_decode($result,true);
            if (isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取用户所在分组
     * @param string $openid
     * @return boolean|int 成功则返回用户分组id
     */
    public function getUserGroup($openid){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $data = array(
                'openid'=>$openid
        );
        $result = $this->http_post(self::API_URL_PREFIX.self::USER_GROUP_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            } else
                if (isset($json['groupid'])) return $json['groupid'];
        }
        return false;
    }

    /**
     * 新增自定分组
     * @param string $name 分组名称
     * @return boolean|array
     */
    public function createGroup($name){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $data = array(
                'group'=>array('name'=>$name)
        );
        $result = $this->http_post(self::API_URL_PREFIX.self::GROUP_CREATE_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 更改分组名称
     * @param int $groupid 分组id
     * @param string $name 分组名称
     * @return boolean|array
     */
    public function updateGroup($groupid,$name){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $data = array(
                'group'=>array('id'=>$groupid,'name'=>$name)
        );
        $result = $this->http_post(self::API_URL_PREFIX.self::GROUP_UPDATE_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 移动用户分组
     * @param int $groupid 分组id
     * @param string $openid 用户openid
     * @return boolean|array
     */
    public function updateGroupMembers($groupid,$openid){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $data = array(
                'openid'=>$openid,
                'to_groupid'=>$groupid
        );
        $result = $this->http_post(self::API_URL_PREFIX.self::GROUP_MEMBER_UPDATE_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 批量移动用户分组
     * @param int $groupid 分组id
     * @param string $openid_list 用户openid数组,一次不能超过50个
     * @return boolean|array
     */
    public function batchUpdateGroupMembers($groupid,$openid_list){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $data = array(
                'openid_list'=>$openid_list,
                'to_groupid'=>$groupid
        );
        $result = $this->http_post(self::API_URL_PREFIX.self::GROUP_MEMBER_BATCHUPDATE_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 发送客服消息
     * @param array $data 消息结构{"touser":"OPENID","msgtype":"news","news":{...}}
     * @return boolean|array
     */
    public function sendCustomMessage($data){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_URL_PREFIX.self::CUSTOM_SEND_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 模板消息 设置所属行业
     * @param int $id1  公众号模板消息所属行业编号，参看官方开发文档 行业代码
     * @param int $id2  同$id1。但如果只有一个行业，此参数可省略
     * @return boolean|array
     */
    public function setTMIndustry($id1,$id2=0){
        $data=array();
        if ($id1) $data['industry_id1'] = $id1;
        if ($id2) $data['industry_id2'] = $id2;
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_URL_PREFIX.self::TEMPLATE_SET_INDUSTRY_URL.'access_token='.$this->access_token,self::json_encode($data));
        if($result){
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 模板消息 添加消息模板
     * 成功返回消息模板的调用id
     * @param string $tpl_id 模板库中模板的编号，有“TM**”和“OPENTMTM**”等形式
     * @return boolean|string
     */
    public function addTemplateMessage($tpl_id){
        $data = array ('template_id_short' =>$tpl_id);
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_URL_PREFIX.self::TEMPLATE_ADD_TPL_URL.'access_token='.$this->access_token,self::json_encode($data));
        if($result){
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json['template_id'];
        }
        return false;
    }

    /**
     * 发送模板消息
     * @param array $data 消息结构
     * @return boolean|array
     */
    public function sendTemplateMessage($data){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_URL_PREFIX.self::TEMPLATE_SEND_URL.'access_token='.$this->access_token,self::json_encode($data));
        if($result){
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取多客服会话记录
     * @param array $data 数据结构{"starttime":123456789,"endtime":987654321,"openid":"OPENID","pagesize":10,"pageindex":1,}
     * @return boolean|array
     */
    public function getCustomServiceMessage($data){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_URL_PREFIX.self::CUSTOM_SERVICE_GET_RECORD.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 转发多客服消息
     * Example: $obj->transfer_customer_service($customer_account)->reply();
     * @param string $customer_account 转发到指定客服帐号：test1@test
     * @return object
     */
    public function transfer_customer_service($customer_account = '')
    {
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName'=>$this->getRevTo(),
            'CreateTime'=>time(),
            'MsgType'=>'transfer_customer_service',
        );
        if ($customer_account) {
            $msg['TransInfo'] = array('KfAccount'=>$customer_account);
        }
        $this->Message($msg);
        return $this;
    }

    /**
     * 获取多客服客服基本信息
     *
     * @return boolean|array
     */
    public function getCustomServiceKFlist(){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_get(self::API_URL_PREFIX.self::CUSTOM_SERVICE_GET_KFLIST.'access_token='.$this->access_token);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取多客服在线客服接待信息
     *
     * @return boolean|array
     */
    public function getCustomServiceOnlineKFlist(){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_get(self::API_URL_PREFIX.self::CUSTOM_SERVICE_GET_ONLINEKFLIST.'access_token='.$this->access_token);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 创建指定多客服会话
     * @tutorial 当用户已被其他客服接待或指定客服不在线则会失败
     * @param string $openid           //用户openid
     * @param string $kf_account     //客服账号
     * @param string $text                 //附加信息，文本会展示在客服人员的多客服客户端，可为空
     * @return boolean | array            //成功返回json数组
     */
    public function createKFSession($openid,$kf_account,$text=''){
        $data=array(
            "openid" =>$openid,
            "kf_account" => $kf_account
        );
        if ($text) $data["text"] = $text;
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX.self::CUSTOM_SESSION_CREATE.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 关闭指定多客服会话
     * @tutorial 当用户被其他客服接待时则会失败
     * @param string $openid           //用户openid
     * @param string $kf_account     //客服账号
     * @param string $text                 //附加信息，文本会展示在客服人员的多客服客户端，可为空
     * @return boolean | array            //成功返回json数组
     */
    public function closeKFSession($openid,$kf_account,$text=''){
        $data=array(
            "openid" =>$openid,
            "nickname" => $kf_account
        );
        if ($text) $data["text"] = $text;
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX.self::CUSTOM_SESSION_CLOSE .'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取用户会话状态
     * @param string $openid           //用户openid
     * @return boolean | array            //成功返回json数组
     */
    public function getKFSession($openid){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_get(self::API_BASE_URL_PREFIX.self::CUSTOM_SESSION_GET .'access_token='.$this->access_token.'&openid='.$openid);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取指定客服的会话列表
     * @param string $kf_account           //客服openid
     * @return boolean | array            //成功返回json数组
     */
    public function getKFSessionlist($kf_account){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_get(self::API_BASE_URL_PREFIX.self::CUSTOM_SESSION_GET_LIST .'access_token='.$this->access_token.'&kf_account='.$kf_account);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取未接入会话列表
     * @return boolean | array
     */
    public function getKFSessionWait(){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_get(self::API_BASE_URL_PREFIX.self::CUSTOM_SESSION_GET_WAIT .'access_token='.$this->access_token);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 添加客服账号
     *
     * @param string $account      //完整客服账号，格式为：账号前缀@公众号微信号，账号前缀最多10个字符，必须是英文或者数字字符
     * @param string $nickname     //客服昵称，最长6个汉字或12个英文字符
     * @param string $password     //客服账号明文登录密码，会自动加密
     * @return boolean|array
     */
    public function addKFAccount($account,$nickname,$password){
        $data=array(
            "kf_account" =>$account,
            "nickname" => $nickname,
            "password" => md5($password)
        );
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX.self::CS_KF_ACCOUNT_ADD_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 修改客服账号信息
     *
     * @param string $account      //完整客服账号，格式为：账号前缀@公众号微信号，账号前缀最多10个字符，必须是英文或者数字字符
     * @param string $nickname     //客服昵称，最长6个汉字或12个英文字符
     * @param string $password     //客服账号明文登录密码，会自动加密
     * @return boolean|array
     */
    public function updateKFAccount($account,$nickname,$password){
        $data=array(
                "kf_account" =>$account,
                "nickname" => $nickname,
                "password" => md5($password)
        );
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX.self::CS_KF_ACCOUNT_UPDATE_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 删除客服账号
     *
     * @param string $account      //完整客服账号，格式为：账号前缀@公众号微信号，账号前缀最多10个字符，必须是英文或者数字字符
     * @return boolean|array
     * 成功返回结果
     * {
     *   "errcode": 0,
     *   "errmsg": "ok",
     * }
     */
    public function deleteKFAccount($account){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_get(self::API_BASE_URL_PREFIX.self::CS_KF_ACCOUNT_DEL_URL.'access_token='.$this->access_token.'&kf_account='.$account);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 上传客服头像
     *
     * @param string $account //完整客服账号，格式为：账号前缀@公众号微信号，账号前缀最多10个字符，必须是英文或者数字字符
     * @param string $imgfile //头像文件完整路径,如：'D:\user.jpg'。头像文件必须JPG格式，像素建议640*640
     * @return boolean|array
     */
    public function setKFHeadImg($account,$imgfile){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX.self::CS_KF_ACCOUNT_UPLOAD_HEADIMG_URL.'access_token='.$this->access_token.'&kf_account='.$account,array('media'=>'@'.$imgfile),true);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 语义理解接口
     * @param string $uid      用户唯一id（非开发者id），用户区分公众号下的不同用户（建议填入用户openid）
     * @param string $query    输入文本串
     * @param string $category 需要使用的服务类型，多个用“，”隔开，不能为空
     * @param string $latitude  纬度坐标，与经度同时传入；与城市二选一传入
     * @param string $longitude 经度坐标，与纬度同时传入；与城市二选一传入
     * @param string $city     城市名称，与经纬度二选一传入
     * @param string $region   区域名称，在城市存在的情况下可省略；与经纬度二选一传入
     * @return boolean|array
     */
    public function querySemantic($uid,$query,$category,$latitude='',$longitude='',$city="",$region=""){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $data=array(
                'query' => $query,
                'category' => $category,
                'appid' => $this->appid,
                'uid' => $uid
        );
        //地理坐标或城市名称二选一
        if ($latitude) {
            $data['latitude'] = $latitude;
            $data['longitude'] = $longitude;
        } elseif ($city) {
            $data['city'] = $city;
        } elseif ($region) {
            $data['region'] = $region;
        }
        $result = $this->http_post(self::API_BASE_URL_PREFIX.self::SEMANTIC_API_URL.'access_token='.$this->access_token,self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 创建卡券
     * @param array $data      卡券数据
     * @return array|boolean 返回数组中card_id为卡券ID
     */
    public function createCard($data) {
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_CREATE . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 更改卡券信息
     * 调用该接口更新信息后会重新送审，卡券状态变更为待审核。已被用户领取的卡券会实时更新票面信息。
     * @param string $data
     * @return boolean
     */
    public function updateCard($data) {
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_UPDATE . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 删除卡券
     * 允许商户删除任意一类卡券。删除卡券后，该卡券对应已生成的领取用二维码、添加到卡包 JS API 均会失效。
     * 注意：删除卡券不能删除已被用户领取，保存在微信客户端中的卡券，已领取的卡券依旧有效。
     * @param string $card_id 卡券ID
     * @return boolean
     */
    public function delCard($card_id) {
        $data = array(
            'card_id' => $card_id,
        );
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_DELETE . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 查询卡券详情
     * @param string $card_id
     * @return boolean|array    返回数组信息比较复杂，请参看卡券接口文档
     */
    public function getCardInfo($card_id) {
        $data = array(
            'card_id' => $card_id,
        );
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_GET . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取颜色列表
     * 获得卡券的最新颜色列表，用于创建卡券
     * @return boolean|array   返回数组请参看 微信卡券接口文档 的json格式
     */
    public function getCardColors() {
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_get(self::API_BASE_URL_PREFIX . self::CARD_GETCOLORS . 'access_token=' . $this->access_token);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 拉取门店列表
     * 获取在公众平台上申请创建的门店列表
     * @param int $offset  开始拉取的偏移，默认为0从头开始
     * @param int $count   拉取的数量，默认为0拉取全部
     * @return boolean|array   返回数组请参看 微信卡券接口文档 的json格式
     */
    public function getCardLocations($offset=0,$count=0) {
        $data=array(
            'offset'=>$offset,
            'count'=>$count
        );
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_LOCATION_BATCHGET . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 批量导入门店信息
     * @tutorial 返回插入的门店id列表，以逗号分隔。如果有插入失败的，则为-1，请自行核查是哪个插入失败
     * @param array $data    数组形式的json数据，由于内容较多，具体内容格式请查看 微信卡券接口文档
     * @return boolean|string 成功返回插入的门店id列表
     */
    public function addCardLocations($data) {
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_LOCATION_BATCHADD . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 生成卡券二维码
     * 成功则直接返回ticket值，可以用 getQRUrl($ticket) 换取二维码url
     *
     * @param string $card_id 卡券ID 必须
     * @param string $code 指定卡券 code 码，只能被领一次。use_custom_code 字段为 true 的卡券必须填写，非自定义 code 不必填写。
     * @param string $openid 指定领取者的 openid，只有该用户能领取。bind_openid 字段为 true 的卡券必须填写，非自定义 openid 不必填写。
     * @param int $expire_seconds 指定二维码的有效时间，范围是 60 ~ 1800 秒。不填默认为永久有效。
     * @param boolean $is_unique_code 指定下发二维码，生成的二维码随机分配一个 code，领取后不可再次扫描。填写 true 或 false。默认 false。
     * @param string $balance 红包余额，以分为单位。红包类型必填（LUCKY_MONEY），其他卡券类型不填。
     * @return boolean|string
     */
    public function createCardQrcode($card_id,$code='',$openid='',$expire_seconds=0,$is_unique_code=false,$balance='') {
        $card = array(
                'card_id' => $card_id
        );
        if ($code)
            $card['code'] = $code;
        if ($openid)
            $card['openid'] = $openid;
        if ($expire_seconds)
            $card['expire_seconds'] = $expire_seconds;
        if ($is_unique_code)
            $card['is_unique_code'] = $is_unique_code;
        if ($balance)
            $card['balance'] = $balance;
        $data = array(
            'action_name' => "QR_CARD",
            'action_info' => array('card' => $card)
        );
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_QRCODE_CREATE . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 消耗 code
     * 自定义 code（use_custom_code 为 true）的优惠券，在 code 被核销时，必须调用此接口。
     *
     * @param string $code 要消耗的序列号
     * @param string $card_id 要消耗序列号所述的 card_id，创建卡券时use_custom_code 填写 true 时必填。
     * @return boolean|array
     */
    public function consumeCardCode($code,$card_id='') {
        $data = array('code' => $code);
        if ($card_id)
            $data['card_id'] = $card_id;
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_CODE_CONSUME . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * code 解码
     * @param string $encrypt_code 通过 choose_card_info 获取的加密字符串
     * @return boolean|array
     */
    public function decryptCardCode($encrypt_code) {
        $data = array(
            'encrypt_code' => $encrypt_code,
        );
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_CODE_DECRYPT . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 查询 code 的有效性（非自定义 code）
     * @param string $code
     * @return boolean|array
     * doc https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1451025272
     */
    public function checkCardCode($code) {
        $data = array(
            'code' => $code,
        );
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_CODE_GET . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 批量查询卡列表
     * @param $offset int  开始拉取的偏移，默认为0从头开始
     * @param $count int  需要查询的卡片的数量（数量最大50,默认50）
     * @return boolean|array
     * doc https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1451025272
     */
    public function getCardIdList($offset=0,$count=50) {
        if ($count>50)
            $count = 50;
        $data = array(
            'offset' => $offset,
            'count'  => $count,
        );
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_BATCHGET . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 更改 code
     * 为确保转赠后的安全性，微信允许自定义code的商户对已下发的code进行更改。
     * 注：为避免用户疑惑，建议仅在发生转赠行为后（发生转赠后，微信会通过事件推送的方式告知商户被转赠的卡券code）对用户的code进行更改。
     * @param string $code      卡券的 code 编码
     * @param string $card_id   卡券 ID
     * @param string $new_code  新的卡券 code 编码
     * @return boolean
     */
    public function updateCardCode($code,$card_id,$new_code) {
        $data = array(
            'code' => $code,
            'card_id' => $card_id,
            'new_code' => $new_code,
        );
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_CODE_UPDATE . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 设置卡券失效
     * 设置卡券失效的操作不可逆
     * @param string $code 需要设置为失效的 code
     * @param string $card_id 自定义 code 的卡券必填。非自定义 code 的卡券不填。
     * @return boolean
     */
    public function unavailableCardCode($code,$card_id='') {
        $data = array(
            'code' => $code,
        );
        if ($card_id)
            $data['card_id'] = $card_id;
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_CODE_UNAVAILABLE . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 库存修改
     * @param string $data
     * @return boolean
     */
    public function modifyCardStock($data) {
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_MODIFY_STOCK . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 激活/绑定会员卡
     * @param string $data 具体结构请参看卡券开发文档(6.1.1 激活/绑定会员卡)章节
     * @return boolean
     */
    public function activateMemberCard($data) {
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_MEMBERCARD_ACTIVATE . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 会员卡交易
     * 会员卡交易后每次积分及余额变更需通过接口通知微信，便于后续消息通知及其他扩展功能。
     * @param string $data 具体结构请参看卡券开发文档(6.1.2 会员卡交易)章节
     * @return boolean|array
     */
    public function updateMemberCard($data) {
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_MEMBERCARD_UPDATEUSER . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 更新红包金额
     * @param string $code      红包的序列号
     * @param $balance  number        红包余额
     * @param string $card_id   自定义 code 的卡券必填。非自定义 code 可不填。
     * @return boolean|array
     */
    public function updateLuckyMoney($code,$balance,$card_id='') {
        $data = array(
                'code' => $code,
                'balance' => $balance
        );
        if ($card_id)
            $data['card_id'] = $card_id;
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_LUCKYMONEY_UPDATE . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 设置卡券测试白名单
     * @param array $openid    测试的 openid 列表
     * @param array $user      测试的微信号列表
     * @return boolean
     */
    public function setCardTestWhiteList($openid=array(),$user=array()) {
        $data = array();
        if (count($openid) > 0)
            $data['openid'] = $openid;
        if (count($user) > 0)
            $data['username'] = $user;
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_TESTWHILELIST_SET . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }
    /**
     * applyShakeAroundDevice 申请配置设备所需的UUID、Major、Minor。
     * @return boolean|mixed
     * doc https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1459246241
     */
    public function applyShakeAroundDevice($data){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::SHAKEAROUND_DEVICE_APPLYID . 'access_token=' . $this->access_token, self::json_encode($data));
        $this->log($result);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
    /**
     * searchShakeAroundDevice 查询已有的设备ID、UUID、Major、Minor、激活状态、备注信息、关联门店、关联页面等信息。
     * @param array $data
     * @return boolean|mixed
     * doc https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1443447624
     */
    public function searchShakeAroundDevice($data){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::SHAKEAROUND_DEVICE_SEARCH . 'access_token=' . $this->access_token, self::json_encode($data));
        $this->log($result);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
    /**
     * bindLocationShakeAroundDevice 修改设备关联的门店ID、设备的备注信息。
     * @param int $poi_id 待关联的门店ID
     * @param string $comment 设备的备注信息
     * @param string $uuid UUID、major、minor，三个信息需填写完整，若填了设备编号，则可不填此信息
     * @param int $major
     * @param int $minor
     * @return boolean|mixed
     * doc https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1459246245
     */
    public function bindLocationShakeAroundDevice($device_id,$poi_id,$comment,$uuid,$major,$minor){
        if (!$this->access_token && !$this->checkAuth()) return false;
        if(!$device_id){
            if(!$uuid || !$major || !$minor){
                return false;
            }
            $device_identifier = array(
                'uuid' => $uuid,
                'major' => $major,
                'minor' => $minor
            );
        }else{
            $device_identifier = array(
                'device_id' => $device_id
            );
        }
        $data = array(
            'device_identifier' => $device_identifier,
            'poi_id' => $poi_id,
            'comment' => $comment
        );
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::SHAKEAROUND_DEVICE_BINDLOCATION . 'access_token=' . $this->access_token, self::json_encode($data));
        $this->log($result);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
    /**
     * bindPageShakeAroundDevice 配置设备与页面的关联关系。
     * @param string $device_id 设备编号，若填了UUID、major、minor，则可不填设备编号，若二者都填，则以设备编号为优先
     * @param array $page_ids 待关联的页面列表
     * @param int $bind 关联操作标志位， 0 为解除关联关系，1 为建立关联关系
     * @param int $append 新增操作标志位， 0 为覆盖，1 为新增
     * @param string $uuid UUID、major、minor，三个信息需填写完整，若填了设备编号，则可不填此信息
     * @param int $major
     * @param int $minor
     * @return boolean|mixed
     */
    public function bindPageShakeAroundDevice($device_id,$page_ids=array(),$bind=1,$append=1,$uuid,$major,$minor){
        if (!$this->access_token && !$this->checkAuth()) return false;
        if(!$device_id){
            if(!$uuid || !$major || !$minor){
                return false;
            }
            $device_identifier = array(
                'uuid' => $uuid,
                'major' => $major,
                'minor' => $minor
            );
        }else{
            $device_identifier = array(
                'device_id' => $device_id
            );
        }
        $data = array(
            'device_identifier' => $device_identifier,
            'page_ids' => $page_ids,
            'bind' => $bind,
            'append' => $append
        );
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::SHAKEAROUND_DEVICE_BINDPAGE . 'access_token=' . $this->access_token, self::json_encode($data));
        $this->log($result);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
    /**
     * addShakeAroundPage 增加摇一摇出来的页面信息，包括在摇一摇页面出现的主标题、副标题、图片和点击进去的超链接。
     * @param string $title 在摇一摇页面展示的主标题，不超过6 个字
     * @param string $description 在摇一摇页面展示的副标题，不超过7 个字
     * @param string $icon_url 在摇一摇页面展示的图片， 格式限定为：jpg,jpeg,png,gif; 建议120*120 ， 限制不超过200*200
     * @param string $page_url 跳转链接
     * @param string $comment 页面的备注信息，不超过15 个字,可不填
     * @return boolean|mixed
     */
    public function addShakeAroundPage($title,$description,$icon_url,$page_url,$comment=''){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $data = array(
            "title" => $title,
            "description" => $description,
            "icon_url" => $icon_url,
            "page_url" => $page_url,
            "comment" => $comment
        );
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::SHAKEAROUND_PAGE_ADD . 'access_token=' . $this->access_token, self::json_encode($data));
        $this->log($result);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
    /**
     * updateShakeAroundPage 编辑摇一摇出来的页面信息，包括在摇一摇页面出现的主标题、副标题、图片和点击进去的超链接。
     * @param int $page_id
     * @param string $title 在摇一摇页面展示的主标题，不超过6 个字
     * @param string $description 在摇一摇页面展示的副标题，不超过7 个字
     * @param string $icon_url 在摇一摇页面展示的图片， 格式限定为：jpg,jpeg,png,gif; 建议120*120 ， 限制不超过200*200
     * @param string $page_url 跳转链接
     * @param string $comment 页面的备注信息，不超过15 个字,可不填
     * @return boolean|mixed
     */
    public function updateShakeAroundPage($page_id,$title,$description,$icon_url,$page_url,$comment=''){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $data = array(
            "page_id" => $page_id,
            "title" => $title,
            "description" => $description,
            "icon_url" => $icon_url,
            "page_url" => $page_url,
            "comment" => $comment
        );
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::SHAKEAROUND_PAGE_UPDATE . 'access_token=' . $this->access_token, self::json_encode($data));
        $this->log($result);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
    /**
     * searchShakeAroundPage 查询已有的页面，包括在摇一摇页面出现的主标题、副标题、图片和点击进去的超链接。
     * @param array $page_ids
     * @param int $begin
     * @param int $count
     * @return boolean|mixed
     */
    public function searchShakeAroundPage($page_ids=array(),$begin=0,$count=1){
        if (!$this->access_token && !$this->checkAuth()) return false;
        if(!empty($page_ids)){
            $data = array(
                'page_ids' => $page_ids
            );
        }else{
            $data = array(
                'begin' => $begin,
                'count' => $count
            );
        }
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::SHAKEAROUND_PAGE_SEARCH . 'access_token=' . $this->access_token, self::json_encode($data));
        $this->log($result);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
    /**
     * deleteShakeAroundPage 删除已有的页面，包括在摇一摇页面出现的主标题、副标题、图片和点击进去的超链接。
     * @param array $page_ids
     * @return boolean|mixed
     */
    public function deleteShakeAroundPage($page_ids=array()){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $data = array(
            'page_ids' => $page_ids
        );
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::SHAKEAROUND_PAGE_DELETE . 'access_token=' . $this->access_token, self::json_encode($data));
        $this->log($result);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
    /**
     * getShakeInfoShakeAroundUser 获取设备信息，包括UUID、major、minor，以及距离、openID 等信息。
     * @param string $ticket 摇周边业务的ticket，可在摇到的URL 中得到，ticket生效时间为30 分钟
     * @return boolean|mixed
     */
    public function getShakeInfoShakeAroundUser($ticket){
        if (!$this->access_token && !$this->checkAuth()) return false;
        $data = array('ticket' => $ticket);
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::SHAKEAROUND_USER_GETSHAKEINFO . 'access_token=' . $this->access_token, self::json_encode($data));
        $this->log($result);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
    /**
     * deviceShakeAroundStatistics description
     * @param int $device_id 设备编号，若填了UUID、major、minor，即可不填设备编号，二者选其一
     * @param int $begin_date 起始日期时间戳，最长时间跨度为30 天
     * @param int $end_date 结束日期时间戳，最长时间跨度为30 天
     * @param string $uuid UUID、major、minor，三个信息需填写完成，若填了设备编辑，即可不填此信息，二者选其一
     * @param int $major
     * @param int $minor
     * @return boolean|mixed
     */
    public function deviceShakeAroundStatistics($device_id,$begin_date,$end_date,$uuid,$major,$minor){
        if (!$this->access_token && !$this->checkAuth()) return false;
        if(!$device_id){
            if(!$uuid || !$major || !$minor){
                return false;
            }
            $device_identifier = array(
                'uuid' => $uuid,
                'major' => $major,
                'minor' => $minor
            );
        }else{
            $device_identifier = array(
                'device_id' => $device_id
            );
        }
        $data = array(
            'device_identifier' => $device_identifier,
            'begin_date' => $begin_date,
            'end_date' => $end_date
        );
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::SHAKEAROUND_STATISTICS_DEVICE . 'access_token=' . $this->access_token, self::json_encode($data));
        $this->log($result);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
}




