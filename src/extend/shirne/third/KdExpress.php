<?php

namespace shirne\third;

/**
 * 快递鸟接口
 * Class KdExpress
 * @package third
 */
class KdExpress extends ThirdBase
{
    public function __construct($options=[]){
        parent::__construct($options);
        $this->baseURL='http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx';
    }
    private function encrypt($data, $appkey) {
        return urlencode(base64_encode(md5($data.$appkey)));
    }

    public function QueryExpressTraces($shipper_code,$logistic_code)
    {
        //"{'OrderCode':'','ShipperCode':'{$shipper_code}','LogisticCode':'{$logistic_code}'}";
        $requestData= json_encode([
            'OrderCode'=>'',
            'ShipperCode'=>$shipper_code,
            'LogisticCode'=>$logistic_code
        ],JSON_UNESCAPED_UNICODE);

        $datas = array(
            'EBusinessID' => $this->appid,
            'RequestType' => '1002',
            'RequestData' => urlencode($requestData) ,
            'DataType' => '2',
        );
        $datas['DataSign'] = $this->encrypt($requestData, $this->appsecret);
        $result = $this->http_post($this->baseURL, $datas);
        return json_decode($result,true);
    }
}