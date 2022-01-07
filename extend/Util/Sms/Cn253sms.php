<?php
/**
 * Created by 廖强.
 * TEL:18380807104
 * QQ:1204887277
 * User: admin
 * Date: 2016-11-20
 * Time: 11:27
 * Company:网络科技有限责任公司
 */

namespace Util\Sms;

/**
 * Class Cn253sms
 * @package Util\Sms
 * 创蓝253短信
 */
class Cn253sms
{

    const SEND_URL = 'http://sms.253.com/msg/send';
    private $un = "";
    private $pw = "";
    private $rd = 0;
    public $msg = "";

    public function __construct()
    {
    }

    /**
     *
     * @param $mobile
     * @param $code
     * @return bool
     * 短信验证码
     */
    public function vi_code($mobile, $code)
    {
        $smsContent = "【在家购】您获得的验证码是:" . $code . "，2分钟内有效，打死也不要告诉别人哦！感谢使用在家购!";
        return $this->send($mobile, $smsContent);
    }

    /**
     *
     * @param $mobile
     * @param $code
     * @return bool
     * 自定义短信
     *  $sms = new \Util\Sms\Cn253sms();
     * $sms->custom("18380807104", "【在家购】你好，感谢您订购水杯产品X1")
     */
    public function custom($mobile, $smsContent)
    {
        return $this->send($mobile, $smsContent);
    }

    /**
     *
     * @param $mobile
     * @param $content
     * @return bool
     * 发送
     */
    private function send($mobile, $content)
    {
        $post_data = array();
        $post_data['un'] = $this->un;//账号
        $post_data['pw'] = $this->pw;//密码
        $post_data['msg'] = $content;
        $post_data['phone'] = $mobile . "";//手机
        $post_data['rd'] = $this->rd;
        $res = $this->http_request("http://sms.253.com/msg/send", http_build_query($post_data));
        return $res;
    }


    private function http_request($url, $data = null)
    {

        if (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);

            if (!empty($data)) {
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($curl);
//            echo $output;
            curl_close($curl);

            $result = preg_split("/[,\r\n]/", $output);
            if ($result[1] == 0) {
                $this->msg = "发送成功";
                return true;
            } else {
                $this->msg = "发送失败：" . $result[1];
//                echo $result[1];
                return false;
//                  return "curl error".$result[1];
            }
        } elseif (function_exists('file_get_contents')) {

            $output = file_get_contents($url . $data);
//            echo $output;
            $result = preg_split("/[,\r\n]/", $output);
            if ($result[1] == 0) {
                return true;
                // return "success";
            } else {
                $this->msg = $result[1];
//                echo $result[1];
                return false;
//                return "error".$result[1];
            }


        } else {
            return false;
        }

    }

}