<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2017-11-18
 * Time: 9:19
 */

namespace app\common\library;

use Emails\Phpmailers;
use think\Exception;
use think\Log;
use Util\Http;

/**
 
 **/
class Email
{
    //发送邮件
    public static function sendMail($title, $content)
    {
        $content = is_string($content) ? $content : var_export($content, true);
//        if (C('EMAIL_TYPE') == 1) {
//            return self::sendMailSmtp($title, $content);
//        } else {
//            return self::sendMailApi($title, $content);
//        }
    }

    //发送邮件
    protected static function sendMailApi($title, $content)
    {
        $config = C('mail_api');
        $toemial = explode("\n", C('EMAIL_RECEIVER'));
        foreach ($toemial as $key => $to) {
            Http::postAsync($config['apiurl'] . 'mail_sys/send_mail_http.json', [
                'mail_from' => $config['mail_from'],
                'password' => $config['password'],
                'mail_to' => $to,
                'subject' => $title,
                'content' => $content,
                'subtype' => $config['subtype']
            ]);
        }
        return true;
    }

    //发送邮件
    protected static function sendMailSmtp($title, $content)
    {
        if (!function_exists('openssl_encrypt')) {
            Log::error("[通知邮件发送异常]【请先开启openssl环境】");
            return false;
        }
        try {
            $toemial = explode("\n", C('EMAIL_RECEIVER'));
            $email = new Phpmailers(C('mail_smtp'));
            foreach ($toemial as $key => $vo) {
                $ret = $email->sendMail($vo, $title, $content);
                if (!$ret) {
                    Log::error("[异常通知邮件发送失败][" . $vo . "]【" . date("Y-m-d H:i:s") . "】" . $email->errmsg);
                    return false;
                }
            }
            return true;
        } catch (Exception $e) {
            Log::error("[通知邮件发送异常]【" . date("Y-m-d H:i:s") . "】" . $e);
            return false;
        }
    }


}