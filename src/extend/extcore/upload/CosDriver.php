<?php


namespace extcore\upload;

use Qcloud\Cos\Client;

/**
 * 腾讯cos
 * Class CosDriver
 * @package extcore\upload
 */
class CosDriver extends UploadInterface {

    protected $client;

    public function __construct($config = array()) {
        parent::__construct(array_merge([
            'appid' => '',
            'region' => '',
            'srcret_id' => '',
            'secret_key' => '',
            'bucket' => '',
            'domain' => '',
            'url' => '',
    
        ], (array)$config['driver_config']));

        $this->client = new Client(array(
            'region' => $this->config['region'],
            'credentials' => array(
                'secretId' => $this->config['srcret_id'],
                'secretKey' => $this->config['secret_key'],
            )
        ));
    }

    public function thumb($src, $args) {
        $arguments = $this->parseArg($args);
        $src = $this->config['domain'].$src;
        if(!empty($arguments['name'])){
            $arguments = config('upload.styles')[$arguments['name']] ?? [];
        }
        if(empty($arguments))return $src;
        $arg = '?imageMogr2/thumbnail/';
        $mode = (isset($arguments['width']) && isset($arguments['height']))?'fill':'lfit';
        
        if(!empty($arguments['mode'])){
            $mode = $arguments['mode'];
        }
        if(!empty($arguments['width'])){
            $arg .= $arguments['width'];
        }
        $arg .= 'x';
        if(!empty($arguments['height'])){
            $arg .= $arguments['height'];
        }
        $arg .= $mode == 'fill'?'<':'r';
        
        if(!empty($arguments['quality'])){
            $arg .= '/quality/'.$arguments['quality'];
        }
        if(!empty($arguments['extra'])){
            $arg .= $arguments['extra'];
        }

        return $src.$arg;
    }

    public function rootPath($path) {
        if (empty($this->config['srcret_id']) || empty($this->config['secret_key']) || empty($this->config['bucket']) || empty($this->config['domain']) ) {
            $this->errorMsg = '请先配置Cos上传参数！';
            return false;
        }
        return true;
    }

    public function checkPath($path) {
        return true;
    }

    public function delFile($name){
        if(strpos($name,',') > 0){
            $name = explode(',',$name);
        }
        if(is_array($name)){
            $data = $this->client->DeleteObjects(array(
                'Bucket' => $this->config['bucket'],
                'Objects' => $name));
        }else{
            $data = $this->client->DeleteObject(array(
                'Bucket' => $this->config['bucket'],
                'Key' => $name));
        }

        if (empty($data)) {
            $this->errorMsg = '图片服务器连接失败！';
            return false;
        }
        return true;
    }

    public function saveFile($fileData) {

        $name = $fileData['savename'];
        $data = $this->client->putObject(array(
            'Bucket' => $this->config['bucket'],
            'Key' => $name,
            'Body' => fopen(realpath($fileData['tmp_name']), 'rb')));

        if (empty($data)) {
            $this->errorMsg = '图片服务器连接失败！';
            return false;
        }


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
}