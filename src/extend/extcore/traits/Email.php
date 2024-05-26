<?php

namespace extcore\traits;

use PHPMailer\PHPMailer\PHPMailer;
use think\facade\Log;

/**
 * Trait Email
 * @package extcore\traits
 */
trait Email
{
    /**
     * @var array
     */
    protected $emailConfig;

    protected function _setEmailConfig($config)
    {
        $this->emailConfig = $config;
    }
    protected function _sendEmail($user, $subject, $body, $attachment = array())
    {
        if (empty($this->emailConfig) && !empty($this->config)) {
            $this->_setEmailConfig($this->config);
        }
        if (!is_array($user)) {
            $split = explode('@', $user);
            $user = array(
                'username' => $split[0],
                'email' => $user
            );
        }

        $mail             = new PHPMailer();
        $mail->CharSet    = 'UTF-8';
        $mail->IsSMTP();
        $mail->SMTPDebug  = 0;
        // 1 = errors and messages
        // 2 = messages only
        $mail->SMTPAuth   = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Host       = $this->emailConfig['mail_host'];
        $mail->Port       = $this->emailConfig['mail_port'];
        $mail->Username   = $this->emailConfig['mail_user'];
        $mail->Password   = $this->emailConfig['mail_pass'];
        $mail->SetFrom($this->emailConfig['mail_user'], $this->emailConfig['site-name']);
        /*$replyEmail       = $config['REPLY_EMAIL']?$config['REPLY_EMAIL']:$config['FROM_EMAIL'];
        $replyName        = $config['REPLY_NAME']?$config['REPLY_NAME']:$config['FROM_NAME'];
        $mail->AddReplyTo($replyEmail, $replyName);*/
        $mail->Subject    = $subject;
        $mail->MsgHTML($body);
        $mail->AddAddress($user['email'], $user['username']);
        if (!empty($attachment)) { // 添加附件
            foreach ($attachment as $file) {
                is_file($file) && $mail->AddAttachment($file);
            }
        }
        if ($mail->Send()) {
            return true;
        }
        Log::error('send mail error:' . $mail->ErrorInfo);
        return false;
    }
}
