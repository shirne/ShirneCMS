<?php

namespace app\common\service;

use Exception;
use think\facade\Env;
use think\facade\Log;

class EncryptService extends BaseService
{
    private $certRoot;
    private $privateKey;
    private $publicKey;

    private $isNew = false;
    private $inited = false;

    function __construct()
    {
        $this->certRoot = Env::get('config_path').'/cert/';
        if(!file_exists($this->certRoot.'private.pem')){
            $this->createKeys();
        }
        
        $this->loadKeys();
    }

    public static $instance;
    public static function getInstance(){
        if(!self::$instance){
            self::$instance = new static();
        }
        return self::$instance;
    }

    private function createKeys(){
        $keyres = openssl_pkey_new([
            'config' => $this->certRoot.'openssl.cnf'
        ]);
        if($keyres === false){
            throw new Exception('openssl generate private key failed');
        }
        $this->isNew = true;
        openssl_pkey_export_to_file($keyres, $this->certRoot.'private.pem', null, ['config' => $this->certRoot.'openssl.cnf']);
    }
    private function loadKeys(){
        $this->privateKey = file_get_contents($this->certRoot.'private.pem');
        $keyres = openssl_pkey_get_private($this->privateKey);
        if($keyres !== false){
            $details = openssl_pkey_get_details($keyres);
            $this->publicKey = $details['key'];
            $this->inited = true;
        }else{
            Log::error('private key load error');
        }
    }

    public function encrypt($data){
        if(!$this->inited){
            return false;
        }
        openssl_public_encrypt($data, $encrypted, $this->publicKey);
        return base64url_encode($encrypted);
    }

    public function decrypt($data){
        if(!$this->inited){
            return false;
        }
        if($this->isNew){
            return false;
        }
        openssl_private_decrypt(base64url_decode($data), $decrypted, $this->privateKey);
        return $decrypted;
    }
}
