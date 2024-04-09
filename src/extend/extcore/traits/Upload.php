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
    protected $uploadConfig = array(
        //上传的文件大小限制 默认10M
        'max_size'       =>  10485760,
        //允许的文件后缀
        'allow_exts'     =>  array(
            'jpg', 'jpeg', 'png', 'gif', 'bmp', 'tif',
            'swf', 'mp4', 'mp3', 'flv', 'avi',
            'txt', 'csv', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx', 'pdf',
            'zip', 'rar', 'json', 'pem'
        ),
        'img_exts'       =>  array('gif', 'jpg', 'jpeg', 'bmp', 'png', 'swf', 'tif'),
        'media_exts'       =>  array('gif', 'jpg', 'jpeg', 'bmp', 'png', 'swf', 'tif', 'swf', 'mp4', 'mp3', 'flv', 'avi'),
        'root_path'      =>  './uploads/',
        'save_path'      =>  '',
        'save_rule'      =>  'file_rule',
        'driver'         =>    'local',
        'driverConfig'   =>  array(),
    );

    /**
     * @var string
     */
    protected $uploadError;
    protected $uploadErrorCode;

    protected function _setUploadDriver()
    {
        $config = config('upload.');
        $this->uploadConfig = array_merge($this->uploadConfig, $config);
        $uploadDriver = '\\extcore\\upload\\' . ucfirst($this->uploadConfig['driver']) . 'Driver';
        $this->uploader = new $uploadDriver($this->uploadConfig);
        if (!$this->uploader) {
            throw new \Exception("Upload Driver '{$this->uploadConfig['driver']}' not found'", 500);
        }
    }

    /**
     * 检测文件合法性
     * @param  array $file 文件名
     * @param bool $isImg
     * @return boolean
     */
    protected function _checkFile($file, $isImg = false)
    {
        //文件上传失败
        if ($file['error'] !== 0) {
            $this->uploadError = '文件上传失败' . ($file['error']) . '！';
            return false;
        }
        //检查文件类型
        $allowExts = array_map('strtolower', $this->uploadConfig['allow_exts']);
        if (!in_array($file['extension'], $allowExts)) {
            $this->uploadError = '上传文件类型不允许！';
            return false;
        }
        //检查文件大小
        if ($file['size'] > $this->uploadConfig['max_size']) {
            $this->uploadError = '上传文件大小超出限制！';
            return false;
        }
        //检查是否合法上传
        if (!is_uploaded_file($file['tmp_name'])) {
            $this->uploadError = '非法上传文件！';
            return false;
        }
        // 如果是图像文件 检测文件格式
        if ($isImg > 1) {
            if (!in_array($file['extension'], $this->uploadConfig['media_exts'])) {
                $this->uploadError = '只能上传媒体文件！';
                return false;
            }
        } elseif ($isImg && !in_array($file['extension'], $this->uploadConfig['img_exts'])) {
            $this->uploadError = '只能上传图片！';
            return false;
        }
        if (in_array($file['extension'], $this->uploadConfig['img_exts']) && false === getimagesize($file['tmp_name'])) {
            $this->uploadError = '非法图像文件！';
            return false;
        }
        //检查通过，返回true
        return true;
    }

    protected function _createSavePath($rule, $folder)
    {
        if (!empty($rule)) {
            return $folder . '/' . date($rule);
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
    protected function _uploadFile($folder, $field, $isImg = false)
    {
        if (!$this->uploader) {
            $this->_setUploadDriver();
        }
        if (empty($_FILES)) {
            $this->uploadError = '没有文件上传！';
            $this->uploadErrorCode = 101;
            return false;
        }
        if (empty($field)) {
            $files = $_FILES;
        } else {
            $files[$field] = $_FILES[$field];
        }
        if (empty($files[$field]) || empty($files[$field]['tmp_name'])) {
            $this->uploadError = '没有文件上传！';
            $this->uploadErrorCode = 102;
            return false;
        }
        //上传根目录检查
        if (!$this->uploader->rootPath($this->uploadConfig['root_path'])) {
            $this->uploadError = $this->uploader->getError();
            return false;
        }
        //上传目录检查
        $savePath = $this->uploadConfig['root_path'] . $this->_createSavePath($this->uploadConfig['save_path'], $folder);
        if (!$this->uploader->checkPath($savePath)) {
            $this->uploadError = $this->uploader->getError();
            $this->uploadErrorCode = 105;
            return false;
        }
        $uploadFileInfo = array();
        foreach ($files as $key => $file) {
            if (is_array($file['name'])) {
                $file['name'] = $file['name'][0];
            }
            if (is_array($file['type'])) {
                $file['type'] = $file['type'][0];
            }
            if (is_array($file['tmp_name'])) {
                $file['tmp_name'] = $file['tmp_name'][0];
            }
            if (is_array($file['error'])) {
                $file['error'] = $file['error'][0];
            }
            if (is_array($file['size'])) {
                $file['size'] = $file['size'][0];
            }
            if ($file['error'] == 4) continue;
            $saveRuleFunc = $this->uploadConfig['save_rule'];
            $pathinfo = pathinfo($file['name']);
            $file['key'] = $key;
            $file['extension'] = strtolower($pathinfo['extension']);
            $file['savepath'] = $savePath;
            $file['driver'] = $this->uploadConfig['driver'];
            if (empty($file['extension'])) {
                $typesplit = explode('/', $file['type']);
                if (!empty($typesplit[1])) {
                    $file['extension'] = $typesplit[1];
                }
            }

            //检查文件类型大小和合法性
            if (!$this->_checkFile($file, $isImg)) {
                $this->uploadError = '文件类型不合法';
                $this->uploadErrorCode = 108;
                return false;
            }
            $file['savename'] = $saveRuleFunc($file['tmp_name']) . '.' . $file['extension'];
            //存储文件
            $info = $this->uploader->saveFile($file);
            if (!$info) {
                $this->uploadError = $this->uploader->getError();
                $this->uploadErrorCode = 109;
                return false;
            }
            $uploadFileInfo[$key] = $info;
        }

        return empty($field) ? $uploadFileInfo : (isset($uploadFileInfo[$field]) ? $uploadFileInfo[$field] : null);
    }

    protected function _upload($folder, $field)
    {
        return $this->_uploadFile($folder, $field, true);
    }

    protected $deleteFiles;

    /**
     * 批量接收多个上传字段
     * @param $folder string 上传保存的目录名
     * @param $fields array|string 上传的字段,用逗号分割，用/限制上传类型(文件或图片img/image)
     * @param int $warnLevel 报错等级 0 不报错, 1 非空文件报错, 2 全部报错
     * @return array
     */
    protected function _batchUpload($folder, $fields, $warnLevel = 1)
    {
        if (!is_array($fields)) {
            $fields = explode(',', $fields);
        }
        $this->deleteFiles = [];
        $request = request()->post();
        $uploaded = [];
        foreach ($fields as $field) {
            $isImg = false;
            if (strpos($field, '/') > 0) {
                $fieldArr = explode('/', $field);
                $field = $fieldArr[0];
                $isImg = in_array(strtolower($fieldArr[1]), ['img', 'image']) ? true : false;
            }
            $uploadResult = $this->_uploadFile($folder, 'upload_' . $field, $isImg);
            if ($uploadResult) {
                $uploaded[$field] = $uploadResult['url'];
                if (!empty($request['delete_' . $field])) {
                    $this->deleteFiles[] = $request['delete_' . $field];
                }
            } else {
                if ($warnLevel == 1) {
                    if ($this->uploadErrorCode > 102) {
                        delete_image($uploaded);
                        $this->error($field . ':' . $this->uploadError);
                    }
                } elseif ($warnLevel == 2) {
                    delete_image($uploaded);
                    $this->error($field . ':' . $this->uploadError);
                }
            }
        }
        return $uploaded;
    }

    /**
     * 移除删除用的字段
     * @param $data
     * @return mixed
     */
    protected function _removeDeleteFields($data)
    {
        foreach ($data as $field => $val) {
            if (strpos($field, 'delete_') === 0) {
                unset($data[$field]);
            }
        }
        return $data;
    }
}
