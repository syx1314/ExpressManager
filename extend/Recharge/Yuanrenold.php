<?php


namespace Recharge;

use app\common\model\Porder as PorderModel;

/**
 * 猿人接口
 * yrapi.php/index/recharge
 **/
class Yuanren
{
    private $userid;
    private $apikey;
    private $pwd;
    private $apiurl;


    public function __construct($option)
    {
        $this->userid = isset($option['param1']) ? $option['param1'] : '';
        $this->apikey = isset($option['param2']) ? $option['param2'] : '';
        $this->pwd = isset($option['param3']) ? $option['param3'] : '';
        $this->apiurl = isset($option['param4']) ? $option['param4'] : '';
    }

    /**
     * 提交充值号码充值
     */
    public function recharge($out_trade_num, $mobile, $param, $isp = '')
    {
        return $this->http_get($this->apiurl, [
            "userid" => $this->userid,
            "pwd" => $this->pwd,
            "mobile" => $mobile,
            "out_trade_num" => $out_trade_num,
            "product_id" => $param['param1'],
            "sign" => strtoupper(md5($out_trade_num . $mobile . $param['param1'] . $this->userid . $this->apikey))
        ]);
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
            $result = json_decode($sContent, true);
            if ($result['errno'] == 0) {
                return rjson(0, $result['errmsg'], $result);
            } else {
                return rjson(1, $result['errmsg'], $result);
            }
        } else {
            return rjson(1, '接口访问失败，http错误码' . $aStatus["http_code"]);
        }
    }

    //回调
    public function notify()
    {
        $state = intval(I('state'));
        if ($state == 1) {
            //充值成功,根据自身业务逻辑进行后续处理
            PorderModel::rechargeSusApi('yuanrenold', I('out_trade_num'), $_GET);
            echo "success";
        } elseif ($state == 2) {
            //充值失败,根据自身业务逻辑进行后续处理
            PorderModel::rechargeFailApi('yuanrenold', I('out_trade_num'), $_GET);
        } else {
            echo "fail";
        }
    }
}