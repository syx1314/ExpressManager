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
 * @package Util\Sms
 * 短信
 */
class Dyrsms
{

    const API_URL = 'http://sms.api.dayuanren.net/index.php/Send/template';
    const APP_ID = '';
    const APP_SECRET = '';
    public $msg;

    public function __construct()
    {
    }

    public function send($mobile, $code, $min)
    {
        $param = [
            'templateid' => 1,
            'mobile' => $mobile,
            'code' => $code,
            'min' => $min,
            'ip' => get_client_ip()
        ];
        return $this->http_post(self::API_URL, $param, $this->create_header());
    }

    private function create_header()
    {
        $timstamp = time();
        $appsecret = self::APP_SECRET;
        $appid = self::APP_ID;
        $headers = [
            'Timestamp:' . $timstamp,
            'Sign:' . md5($timstamp . $appsecret),
            'Appid:' . $appid
        ];
        return $headers;
    }

    private function http_post($url, $param, $headers)
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        if (is_string($param)) {
            $strPOST = $param;
        } else {
            $strPOST = http_build_query($param);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
        curl_setopt($oCurl, CURLOPT_HEADER, 0);
        curl_setopt($oCurl, CURLOPT_HTTPHEADER, $headers);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            $arr = json_decode($sContent, true);
            if ($arr['code'] == 0) {
                $this->msg = $arr['msg'];
                return true;
            } else {
                $this->msg = $arr['msg'];
                return false;
            }
        } else {
            $this->msg = $sContent;
            return false;
        }
    }

}