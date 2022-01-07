<?php


namespace Recharge;

use app\common\model\Porder as PorderModel;

/**
 * 宜信友充值接口
 * http://ip:port/webInface/PayPhone.ashx
 **/
class Yxyou
{
    private $usr;
    private $key;
    private $apiurl;


    public function __construct($option)
    {
        $this->usr = isset($option['param1']) ? $option['param1'] : '';
        $this->key = isset($option['param2']) ? $option['param2'] : '';
        $this->apiurl = isset($option['param4']) ? $option['param4'] : '';
    }

    /**
     * 提交充值号码充值
     */
    public function recharge($out_trade_num, $mobile, $param, $isp = '')
    {
        $tim = date("YmdHis", time());
        $amt = $param['param1'];
        $sgn_str = $this->usr . $out_trade_num . $mobile . $amt . $tim . $this->key;
        $sgn = strtoupper(md5($sgn_str));
        return $this->http_get($this->apiurl, [
            "usr" => $this->usr,
            "ord" => $out_trade_num,
            "mob" => $mobile,
            "amt" => $amt,
            "tim" => $tim,
            "yysid" => 0,
            'sgn' => $sgn,
            'hmlx' => 0
        ]);
    }

    //回调
    public function notify()
    {
        $state = intval(I('state'));
        if ($state == 1) {
            //充值成功,根据自身业务逻辑进行后续处理
            PorderModel::rechargeSusApi('yxyou', I('ord'), $_GET);
            echo "success";
        } elseif ($state == 2) {
            //充值失败,根据自身业务逻辑进行后续处理
            PorderModel::rechargeFailApi('yxyou', I('ord'), $_GET);
            echo "success";
        } else {
            echo "fail";
        }
    }

    /**
     * get请求
     */
    private function http_get($url, $param)
    {
        $url = $url . "?" . http_build_query($param);
        $oCurl = curl_init();
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, false);
        curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($oCurl, CURLOPT_TIMEOUT, 60);
        curl_setopt($oCurl, CURLOPT_HEADER, 0);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            $result = explode("|", $sContent);
            if ($result[0] == 0) {
                return rjson(0, $result[1], $sContent);
            } else {
                return rjson(1, $result[1], $sContent);
            }
        } else {
            return rjson(1, '接口访问失败，http错误码' . $aStatus["http_code"]);
        }
    }
}