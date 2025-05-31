<?php

namespace extcore\upload;

/**
 * 本地上传驱动
 * Class LocalDriver
 * @package extcore\upload
 */
class LocalDriver extends UploadInterface{

    public function delFile($name) {
        if(is_array($name)){
            foreach ($name as $item){
                delete_image($item);
            }
        }else{
            if(!empty($name) && strpos($name,'/uploads/')===0){
                @unlink('.'.$name);
            }
        }
        return true;
    }

    public function thumb($src, $args) {
        $arguments = $this->parseArg($args);
        if(empty($arguments))return $src;
        $args = [];
        if(!empty($arguments['name'])){
            $arguments = $this->config['styles'][$arguments['name']] ?? [];
        }
        if(empty($arguments)){
            return $src;
        }
        if(!empty($arguments['width'])){
            $args['w']=$arguments['width'];
        }
        if(!empty($arguments['height'])){
            $args['h']=$arguments['height'];
        }
        if(!empty($arguments['mode'])){
            $args['m']=$arguments['mode'];
        }
        if(!empty($arguments['quality'])){
            $args['q']=$arguments['quality'];
        }
        return $src.'?'.http_build_query($args);
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
        $savepath=$file['savepath'] . $file['savename'];
		if(!move_uploaded_file($file['tmp_name'], $savepath)) {
            $this->errorMsg = '文件上传保存错误！';
            return false;
		}
		$file['url']=ltrim($savepath,'.');
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

}