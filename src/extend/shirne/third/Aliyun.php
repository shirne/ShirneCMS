<?php

namespace shirne\third;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Dysmsapi\Dysmsapi;
use AlibabaCloud\Green\Green;
use think\facade\Log;

class Aliyun extends ThirdModelBase
{
    protected $accessKeyId = '';
    protected $accessKeySecret = '';
    protected $oss_bucket = '';
    protected $oss_domain = '';
    protected $oss_ssl = '';


    public function __construct($config)
    {
        $this->accessKeyId = $config['accesskey_id'];
        $this->accessKeySecret = $config['accesskey_secret'];
        $this->oss_bucket = $config['aliyun_oss'];
        $this->oss_domain = $config['aliyun_oss_domain'];
        $this->oss_ssl = $config['aliyun_oss_ssl'];
    }

    public function greenScan($content)
    {
        if (empty($this->accessKeyId) || empty($this->accessKeySecret)) return 0;

        try {
            AlibabaCloud::accessKeyClient($this->accessKeyId, $this->accessKeySecret)
                ->regionId('cn-shenzhen')
                ->asDefaultClient();

            $request = Green::v20180509()->textScan();
            $task = array(
                'dataId' =>  uniqid(),
                'content' => $content
            );
            $result = $request->setContent(json_encode(array(
                "tasks" => array($task),
                "scenes" => array("antispam")
            )))->request();

            if (200 == $result->Code) {
                $taskResults = $result->data;
                foreach ($taskResults as $taskResult) {
                    if (200 == $taskResult->code) {
                        $sceneResults = $taskResult->results;
                        foreach ($sceneResults as $sceneResult) {
                            $scene = $sceneResult->scene;
                            $suggestion = $sceneResult->suggestion;

                            if ($suggestion == 'pass') {
                                return 1;
                            } elseif ($suggestion == 'review') {
                                return 0;
                            }
                            Log::record($content);
                            Log::record(json_encode($taskResults, JSON_UNESCAPED_UNICODE));
                            return -1;
                        }
                    } else {
                        $this->set_error("task process fail:" + $result->Message);
                        Log::record("task process fail:" + $result->Code);
                    }
                }
            } else {
                $this->set_error("detect fail. code:" + $result->Code);
                Log::record("detect fail. " + json_encode($result, JSON_UNESCAPED_UNICODE));
            }
        } catch (\Exception $e) {
            $this->set_error($e->getMessage());
            Log::record($e->getMessage());
            Log::record($e->getTraceAsString());
        }
        return 0;
    }

    public function sendSms($mobiles, $param, $tplCode, $sign)
    {
        if (empty($this->accessKeyId) || empty($this->accessKeySecret)) return 0;
        try {
            if (is_string($param)) {
                $param = ['code' => $param];
            }
            $param = json_encode($param, JSON_UNESCAPED_UNICODE);

            AlibabaCloud::accessKeyClient($this->accessKeyId, $this->accessKeySecret)
                ->regionId('cn-shenzhen')
                ->asDefaultClient();

            $request = Dysmsapi::v20170525()->sendSms();

            $result = $request->withPhoneNumbers($mobiles)
                ->withSignName($sign)
                ->withTemplateCode($tplCode)
                ->withTemplateParam($param)
                ->request();

            if (200 == $result->Code || $result->Code == 'OK') {
                return 1;
            } else {
                $this->set_error($result->Message);
                Log::record("sendsms fail." . json_encode($result, JSON_UNESCAPED_UNICODE));
            }
        } catch (\Exception $e) {
            $this->set_error($e->getMessage());
            Log::record($e->getMessage());
            Log::record($e->getTraceAsString());
        }
        return 0;
    }
}
