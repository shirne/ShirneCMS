<?php


namespace extcore\upload;

use OSS\Core\OssException;
use OSS\OssClient;

/**
 * 阿里Oss
 * Class OssDriver
 * @package extcore\upload
 */
class OssDriver extends UploadInterface
{

    protected $client;

    public function __construct($config = array())
    {
        parent::__construct(array_merge([
            'region' => '',
            'access_id' => '',
            'secret_key' => '',
            'bucket' => '',
            'domain' => '',
            'url' => '',
            'delimiter' => '?x-oss-process=style/',

        ], (array)$config['driver_config']));

        $this->client = new OssClient($this->config['access_id'], $this->config['secret_key'], $this->config['region']);
    }

    public function thumb($src, $args)
    {
        $arguments = $this->parseArg($args);
        $src = $this->config['domain'] . $src;
        if (empty($arguments)) return $src;
        if (!empty($arguments['name'])) {
            return $this->config['delimiter'] . $arguments['name'];
        }
        $arg = '?x-oss-process=image/resize,';
        $mode = (isset($arguments['width']) && isset($arguments['height'])) ? 'fill' : 'lfit';

        if (!empty($arguments['mode'])) {
            $mode = $arguments['mode'];
        }
        $arg .= "m_$mode,";
        if (!empty($arguments['width'])) {
            $arg .= "w_{$arguments['width']},";
        }
        if (!empty($arguments['height'])) {
            $arg .= "h_{$arguments['height']},";
        }
        $arg = rtrim($arg, ' ,');
        if (!empty($arguments['quality'])) {
            $arg .= "/quality,q_{$arguments['quality']}";
        }
        if (!empty($arguments['extra'])) {
            $arg .= $arguments['extra'];
        }
        return $src . $arg;
    }

    public function rootPath($path)
    {
        if (empty($this->config['access_id']) || empty($this->config['secret_key']) || empty($this->config['bucket']) || empty($this->config['domain'])) {
            $this->errorMsg = '请先配置Oss上传参数！';
            return false;
        }
        return true;
    }

    public function checkPath($path)
    {
        return true;
    }

    public function delFile($name)
    {
        if (strpos($name, ',') > 0) {
            $name = explode(',', $name);
        }
        try {
            if (is_array($name)) {
                $this->client->deleteObjects($this->config['bucket'], $name);
            } else {
                $this->client->deleteObject($this->config['bucket'], $name);
            }
        } catch (OssException $e) {
            $this->errorMsg = $e->getMessage();
            return false;
        }

        return true;
    }

    public function saveFile($fileData)
    {

        $name = $fileData['savename'];

        try {
            $this->client->putObject($this->config['bucket'], $name, file_get_contents(realpath($fileData['tmp_name'])));
        } catch (OssException $e) {
            $this->errorMsg = $e->getMessage();
            return false;
        }

        $fileData['url'] = $this->config['domain'] . '/' . $name;
        return $fileData;
    }
}
