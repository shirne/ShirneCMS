<?php

namespace shirne\third;

use AlibabaCloud\Client\AlibabaCloud;
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
        if(empty($this->accessKeyId) || empty($this->accessKeySecret))return 0;

        try {
            AlibabaCloud::accessKeyClient($this->accessKeyId,$this->accessKeySecret)
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

            if (200 == $result->code) {
                $taskResults = $result->data;
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
                            Log::record($content);
                            Log::record(json_encode($taskResults,JSON_UNESCAPED_UNICODE));
                            return -1;
                        }
                    } else {
                        $this->set_error("task process fail:" + $result->code);
                        Log::record("task process fail:" + $result->code);
                    }
                }
            } else {
                $this->set_error("detect fail. code:" + $result->code);
                Log::record("detect fail. code:" + $result->code);
            }
        } catch (\Exception $e) {
            $this->set_error($e->getMessage());
            Log::record($e->getMessage());
            Log::record($e->getTraceAsString());
        }
        return 0;
    }
}
