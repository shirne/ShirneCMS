<?php

namespace extcore\upload;


use Qiniu\Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;

/**
 * 七牛上传驱动
 * Class QiniuDriver
 * @package extcore\upload
 */
class QiniuDriver extends UploadInterface
{

    protected $auth;

    public function __construct($config = array())
    {
        parent::__construct(array_merge([
            'access_key' => '',
            'secret_key' => '',
            'bucket' => '',
            'domain' => '',
            'url' => ''

        ], (array)$config['driver_config']));

        $this->auth = new Auth($this->config['access_key'], $this->config['secret_key']);
    }

    public function delFile($name)
    {
        if (!is_array($name)) {
            $name = explode(',', $name);
        }
        try {
            $bucketMgr = new BucketManager($this->auth);
            foreach ($name as $item) {
                if(empty($item))continue;
                $bucketMgr->delete($this->config['bucket'], $item);
            }
        } catch (\Exception $e) {
            $this->errorMsg = $e->getMessage();
            return false;
        }
        return true;
    }


    public function thumb($src, $args)
    {
        $arguments = $this->parseArg($args);
        $src = $this->config['domain'] . $src;
        if (empty($arguments)) return $src;

        // todo 实现七牛云图片样式裁剪
        return $src;
    }

    public function rootPath($path)
    {
        if (empty($this->config['access_key']) || empty($this->config['secret_key']) || empty($this->config['bucket']) || empty($this->config['domain']) || empty($this->config['url'])) {
            $this->errorMsg = '请先配置七牛上传参数！';
            return false;
        }
        return true;
    }

    public function checkPath($path)
    {
        return true;
    }

    public function saveFile($fileData)
    {

        $name = $fileData['savename'];
        try {
            $uploadToken = $this->auth->uploadToken($this->config['bucket']);
            $uploadMgr = new UploadManager();
            $uploadMgr->putFile($uploadToken, $name, $fileData['tmp_name']);
        } catch (\Exception $e) {
            $this->errorMsg = $e->getMessage();
            return false;
        }
        return $fileData;
    }
}
