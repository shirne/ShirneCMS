<?php

namespace extcore\upload;

/**
 * 上传驱动接口
 * Interface UploadInterface
 * @package extcore\upload
 */
Interface UploadInterface {

	/**
	 * 构建函数
	 * @param array $config 上传配置
	 */
	public function __construct($config);

	/**
	 * 检查根路径
	 * @param  string $path 路径
	 * @return boolean
	 */
	public function rootPath($path);

    /**
	 * 检查上传路径
	 * @param  string $path 路径
	 * @return boolean
	 */
    public function checkPath($path);

    /**
	 * 保存文件
	 * @param  array $file 文件名
	 * @return boolean|array
	 */
    public function saveFile($file);

    /**
     * 获取错误
     * @return string
     */
    public function getError();
		
}