<?php

namespace extcore\upload;

/**
 * 本地上传驱动
 * Class LocalDriver
 * @package extcore\upload
 */
class LocalDriver implements UploadInterface{

	protected $config = array();
	protected $errorMsg = '';

    public function __construct( $config = array() ) {
		$this->config = $config;
    }

    public function rootPath($path) {
    	if(!(is_dir($path) && is_writable($path))){
            $this->errorMsg = '上传根目录不存在！';
            return false;
        }
        return true;
    }

    public function checkPath($path) {
    	if (!$this->mkdir($path)) {
            return false;
        } else {
            if (!is_writable($path)) {
                $this->errorMsg = "上传目录 '{$path}' 不可写入！";
                return false;
            } else {
                return true;
            }
        }
    }

    public function saveFile($file) {
		if(!move_uploaded_file($file['tmp_name'], $file['savepath'] . $file['savename'])) {
            $this->errorMsg = '文件上传保存错误！';
            return false;
		}
        return $file;
    }

    public function mkdir($path){
        $dir = $path;
        if(is_dir($dir)){
            return true;
        }
        try {
            mkdir($dir, 0777, true);
        }catch (\Exception $e) {
            $this->errorMsg = "上传目录 '{$path}' 创建失败！";
            return false;
        }
        return true;
    }

    public function getError(){
        return $this->errorMsg;
    }
}