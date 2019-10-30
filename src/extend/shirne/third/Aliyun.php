<?php

namespace shirne\third;

use AlibabaCloud\Client\Profile\DefaultProfile;
use AlibabaCloud\Green\V20180509\Green;
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
        if(empty($this->accessKeyId) || empty($this->accessKeySecret))return 0;
        $iClientProfile = DefaultProfile::getProfile("cn-shanghai", $this->accessKeyId, $this->accessKeySecret); // TODO
        DefaultProfile::addEndpoint("cn-shanghai", "cn-shanghai", "Green", "green.cn-shanghai.aliyuncs.com");
        $client = new DefaultAcsClient($iClientProfile);

        $request = new Green\TextScanRequest();
        $request->setMethod("POST");
        $request->setAcceptFormat("JSON");

        $task = array(
            'dataId' =>  uniqid(),
            'content' => $content
        );
        $request->setContent(json_encode(array(
            "tasks" => array($task),
            "scenes" => array("antispam")
        )));

        try {
            $response = $client->getAcsResponse($request);
            print_r($response);
            if (200 == $response->code) {
                $taskResults = $response->data;
                foreach ($taskResults as $taskResult) {
                    if (200 == $taskResult->code) {
                        $sceneResults = $taskResult->results;
                        foreach ($sceneResults as $sceneResult) {
                            $scene = $sceneResult->scene;
                            $suggestion = $sceneResult->suggestion;

                            if ($suggestion == 'pass') {
                                return 1;
                            }elseif($suggestion == 'review'){
                                return 0;
                            }

                            return -1;
                        }
                    } else {
                        $this->set_error("task process fail:" + $response->code);
                        Log::record("task process fail:" + $response->code);
                    }
                }
            } else {
                $this->set_error("detect fail. code:" + $response->code);
                Log::record("detect fail. code:" + $response->code);
            }
        } catch (\Exception $e) {
            $this->set_error($e->getMessage());
            Log::record($e->getMessage());
            Log::record($e->getTraceAsString());
        }
        return 0;
    }
}
