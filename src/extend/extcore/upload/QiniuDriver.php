<?php



namespace extcore\upload;

/**
 * 七牛上传驱动
 * Class QiniuDriver
 * @package extcore\upload
 */
class QiniuDriver implements UploadInterface
{

    protected $config = [
        'access_key' => '',
        'secret_key' => '',
        'bucket' => '',
        'domain' => '',
        'url' => ''

    ];
    protected $errorMsg = '';

    public function __construct($config = array())
    {
        $this->config = array_merge($this->config, (array)$config['driverConfig']);
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

        $uploadToken = $this->uploadToken();
        $name = $fileData['savename'];
        $postFields = array(
            'token' => $uploadToken,
            'file'  => curl_file_create(realpath($fileData['tmp_name']), $fileData['type'], $name),
            'key' => $name
        );

        $data = $this->curl($this->config['url'], $postFields, 10);
        if (empty($data)) {
            $this->errorMsg = '图片服务器连接失败！';
            return false;
        }
        $data = json_decode($data, true);
        if (empty($data)) {
            $this->errorMsg = '图片服务器连接失败！';
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

    public function curl($url, $post_data = array(), $timeout = 10)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_HOST']);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function getError()
    {
        return $this->errorMsg;
    }

    protected function uploadToken($param = [])
    {
        $deadline = time() + 3600;
        $data = array('scope' => $this->config['bucket'], 'deadline' => $deadline);
        $data = array_merge($data, $param);
        $data = json_encode($data);
        $data = $this->encode($data);
        return $this->sign($this->config['secret_key'], $this->config['access_key'], $data) . ':' . $data;
    }

    protected function encode($str)
    {
        $find = array('+', '/');
        $replace = array('-', '_');
        return str_replace($find, $replace, base64_encode($str));
    }

    protected function sign($sk, $ak, $data)
    {
        $sign = hash_hmac('sha1', $data, $sk, true);
        return $ak . ':' . $this->encode($sign);
    }
}
