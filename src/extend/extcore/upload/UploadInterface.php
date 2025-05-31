<?php

namespace extcore\upload;

/**
 * 上传驱动接口
 * abstract class UploadInterface
 * @package extcore\upload
 */
abstract class UploadInterface {

    protected $errorMsg = '';
	protected $config;

	// mode, width, height, quality, extra
	protected $prefixMap = ['m'=>'mode','w'=>'width','h'=>'height','q'=>'quality','e'=>'extra'];

	/**
	 * 构建函数
	 * @param array $config 上传配置
	 */
	public function __construct($config = []){
		$this->config = $config;
	}

	/**
	 * 检查根路径
	 * @param  string $path 路径
	 * @return boolean
	 */
	abstract public function rootPath($path);

    /**
	 * 检查上传路径
	 * @param  string $path 路径
	 * @return boolean
	 */
    abstract public function checkPath($path);

    /**
	 * 保存文件
	 * @param  array $file 文件名
	 * @return boolean|array
	 */
    abstract public function saveFile($file);

	/**
	 * 删除文件
	 * @param string|array $name 
	 * @return boolean 
	 */
	abstract public function delFile($name);

	/**
	 * 生成缩略参数
	 * @param string|array $args 
	 * @return string 
	 */
	abstract public function thumb($src, $args);

	protected function parseArg($args){
		$arguments = [];
		if(!empty($args)){
			if(strpos($args, ',') > 0){
				$parts = explode(',', $args.',,');
				if(strpos($parts[0],'_') === false){
					$arguments['width'] = intval($parts[0]);
					$arguments['height'] = intval($parts[1]);
					$arguments['quality'] = intval($parts[2]);
				}else{
					foreach($parts as $part){
						if(empty($part))continue;
						list($key, $value) = explode('_', $part);
						if(isset($this->prefixMap[$key])) $key = $this->prefixMap[$key];
						$arguments[$key] = $value;
					}
				}
			}else{
				if(is_numeric($args)){
					$arguments['width'] = intval($args);
				}else{
					$arguments['name'] = $args;
				}
			}
		}
		return $arguments;
	}

    /**
     * 获取错误
     * @return string
     */
    public function getError(){
		return $this->errorMsg;
	}
		
}