<?php

namespace extcore;


use think\Response;

class FileDownload extends Response
{
    // 输出参数
    protected $options = [
        'file_name' => 'download.txt',
    ];

    protected $contentType = 'application/octet-stream';

    /**
     * 处理数据
     * @access protected
     * @param  mixed $data 要处理的数据
     * @return mixed
     * @throws \Exception
     */
    protected function output($data)
    {
        try {
            $this->header('Content-Disposition', 'attachment; filename=' . $this->options['file_name']);

            return $data;
        } catch (\Exception $e) {
            if ($e->getPrevious()) {
                throw $e->getPrevious();
            }
            throw $e;
        }
    }
}
