<?php

namespace extcore\traits;

trait Upload
{
    /**
     * @var \extcore\upload\UploadInterface
     */
    protected $uploader;

    /**
     * @var array
     */
    protected $uploadConfig=array(
        'max_size'       =>  1048576, //上传的文件大小限制 默认10M
        'allow_exts'     =>  array('jpg','jpeg','png','gif','bmp','tif','swf','txt','csv','xls','xlsx','doc','docx','ppt','pptx','pdf','zip','rar','json'), //允许的文件后缀
        'img_exts'       =>  array('gif','jpg','jpeg','bmp','png','swf','tif'),
        'root_path'      =>  './uploads/', //上传根路径
        'save_path'      =>  '', //保存路径
        'save_rule'      =>  'md5_file', //命名规则
        'driver'         =>	'local',
        'driverConfig'   =>  array(),
    );

    /**
     * @var string
     */
    protected $uploadError;

    protected function setUploadDriver(){
        $config=config('upload.');
        $this->uploadConfig=array_merge($this->uploadConfig,$config);
        $uploadDriver = '\\extcore\\upload\\' . ucfirst($this->uploadConfig['driver'] ).'Driver';
        $this->uploader = new $uploadDriver($this->uploadConfig);
        if(!$this->uploader){
            throw new \Exception("Upload Driver '{$this->uploadConfig['driver']}' not found'", 500);
        }
    }

    /**
     * 检测文件合法性
     * @param  array $file 文件名
     * @param bool $isImg
     * @return boolean
     */
    protected function checkFile($file,$isImg=false) {
        //文件上传失败
        if($file['error'] !== 0) {
            $this->uploadError= '文件上传失败！';
            return false;
        }
        //检查文件类型
        $allowExts = array_map('strtolower', $this->uploadConfig['allow_exts']);
        if( !in_array($file['extension'], $allowExts)) {
            $this->uploadError = '上传文件类型不允许！';
            return false;
        }
        //检查文件大小
        if ($file['size'] > $this->uploadConfig['max_size']) {
            $this->uploadError = '上传文件大小超出限制！';
            return false;
        }
        //检查是否合法上传
        if(!is_uploaded_file($file['tmp_name'])) {
            $this->uploadError = '非法上传文件！';
            return false;
        }
        // 如果是图像文件 检测文件格式
        if($isImg && !in_array($file['extension'], $this->uploadConfig['img_exts'])){
            $this->uploadError = '只能上传图片！';
            return false;
        }
        if( in_array($file['extension'], $this->uploadConfig['img_exts']) && false === getimagesize($file['tmp_name']) ) {
            $this->uploadError = '非法图像文件！';
            return false;
        }
        //检查通过，返回true
        return true;
    }

    protected function createSavePath($rule,$folder){
        if(!empty($rule)){
            return $folder.'/'.date($rule);
        }
        return $folder;
    }
    /**
     * 上传文件
     * @param $folder
     * @param $field
     * @param bool $isImg
     * @return bool|array
     */
    protected function uploadFile($folder,$field,$isImg=false){
        if(empty($_FILES)) {
            $this->uploadError = '没有文件上传！';
            return false;
        }
        if(empty($field)) {
            $files = $_FILES;
        } else {
            $files[$field] = $_FILES[$field];
        }
        //上传根目录检查
        if(!$this->uploader->rootPath($this->uploadConfig['root_path'])){
            $this->uploadError = $this->uploader->getError();
            return false;
        }
        //上传目录检查
        $savePath = $this->uploadConfig['root_path'] . $this->createSavePath($this->uploadConfig['save_path'],$folder);
        if(!$this->uploader->checkPath($savePath)){
            $this->uploadError = $this->uploader->getError();
            return false;
        }
        $uploadFileInfo=array();
        foreach($files as $key =>$file) {
            if(is_array($file['name'])){
                $file['name'] = $file['name'][0];
            }
            if(is_array($file['type'])){
                $file['type'] = $file['type'][0];
            }
            if(is_array($file['tmp_name'])){
                $file['tmp_name'] = $file['tmp_name'][0];
            }
            if(is_array($file['error'])){
                $file['error'] = $file['error'][0];
            }
            if(is_array($file['size'])){
                $file['size'] = $file['size'][0];
            }
            if( $file['error'] == 4 ) continue;
            $saveRuleFunc = $this->uploadConfig['save_rule'];
            $pathinfo = pathinfo($file['name']);
            $file['key'] = $key;
            $file['extension'] = strtolower( $pathinfo['extension'] );
            $file['savepath'] = $savePath;
            $file['savename'] = $saveRuleFunc( $file['tmp_name'] ) . '.' . $file['extension'];
            $file['driver'] = $this->uploadConfig['driver'];
            //检查文件类型大小和合法性
            if (!$this->checkFile($file,$isImg)) {
                return false;
            }
            //存储文件
            $info = $this->uploader->saveFile($file);
            if(!$info){
                $this->uploadError = $this->uploader->getError();
                return false;
            }
            $uploadFileInfo[$key] = $info;
        }
        return $uploadFileInfo;
    }

    protected function upload($folder,$field){
        return $this->uploadFile($folder,$field,true);
    }
}