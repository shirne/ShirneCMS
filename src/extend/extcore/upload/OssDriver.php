<?php


namespace extcore\upload;

/**
 * 阿里Oss
 * Class OssDriver
 * @package extcore\upload
 */
class OssDriver implements UploadInterface {

    protected $config = [
        'access_id' => '',
        'secret_key' => '',
        'bucket' => '',
        'domain' => '',
        'url' => ''

    ];
    protected $errorMsg = '';

    public function __construct($config = array()) {
        $this->config = array_merge($this->config, (array)$config['driverConfig']);
    }

    public function rootPath($path) {
        if (empty($this->config['access_id']) || empty($this->config['secret_key']) || empty($this->config['bucket']) || empty($this->config['domain']) || empty($this->config['url'])) {
            $this->errorMsg = '请先配置Oss上传参数！';
            return false;
        }
        return true;
    }

    public function checkPath($path) {
        return true;
    }

    public function saveFile($fileData) {

        $date = gmdate('Y-m-d\TH:i:s\Z', strtotime('+1 day'));
        $policy = [
            'expiration' => $date,
            'conditions' => [
                [
                    'content-length-range',
                    0,
                    104857600
                ],
                [
                    'bucket' => $this->config['bucket']
                ]
            ]
        ];

        $policy = base64_encode(stripslashes(json_encode($policy)));
        $signature = base64_encode(hash_hmac('sha1', $policy, $this->config['secret_key'], true));

        $name = $fileData['savename'];

        $postFields = array(
            'OSSAccessKeyId' => $this->config['access_id'],
            'policy' => $policy,
            'signature' => $signature,
            'key' => $name,
            'file' => curl_file_create(realpath($fileData['tmp_name']), $fileData['type'], $name),
            'success_action_status' => 201
        );

        $data = $this->curl($this->config['url'], $postFields, 10, "Content-type: ". $fileData['type']);

        if (empty($data)) {
            $this->errorMsg = '图片服务器连接失败！';
            return false;
        }
        $data = simplexml_load_string($data);
        $data = json_decode(json_encode($data), TRUE);


        if (!empty($data['Message'])) {
            $this->errorMsg = $data['Message'];
            return false;
        }
        $fileData['url'] = $this->config['domain'] . '/' . $name;
        if ($data['error']) {
            if ($data['error'] == 'file exists') {
                return $fileData;
            }
            $this->errorMsg = $data['error'];
            return false;
        }
        return $fileData;
    }

    public function curl($url, $post_data = [], $header = '',$timeout=10) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function getError() {
        return $this->errorMsg;
    }
}