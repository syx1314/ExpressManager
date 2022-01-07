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
    private $notify;
    private $apiurl;//充值接口

    public function __construct($option)
    {
        $this->userid = isset($option['param1']) ? $option['param1'] : '';
        $this->apikey = isset($option['param2']) ? $option['param2'] : '';
        $this->notify = isset($option['param3']) ? $option['param3'] : '';
        $this->apiurl = isset($option['param4']) ? $option['param4'] : '';
    }

    /**
     * 提交充值号码充值
     */
    public function recharge($out_trade_num, $mobile, $param, $isp = '')
    {
        $data = [
            "userid" => $this->userid,
            "mobile" => $mobile,
            "out_trade_num" => $out_trade_num,
            "product_id" => $param['param1'],
            "notify_url" => $this->notify,
        ];
        $data['sign'] = $this->sign($data);
        return $this->http_get($this->apiurl . 'index/recharge', $data);
    }


    /**
     * 查询用户信息
     */
    public function user()
    {
        $data = [
            "userid" => $this->userid
        ];
        $data['sign'] = $this->sign($data);
        return $this->http_get($this->apiurl . 'index/user', $data);
    }

    /**
     *查询订单状态
     */
    public function check($out_trade_nums)
    {
        $data = [
            "userid" => $this->userid,
            "out_trade_nums" => $out_trade_nums
        ];
        $data['sign'] = $this->sign($data);
        return $this->http_get($this->apiurl . 'index/check', $data);
    }

    /**
     * 获取所有产品
     */
    public function product()
    {
        $data = [
            "userid" => $this->userid
        ];
        $data['sign'] = $this->sign($data);
        return $this->http_get($this->apiurl . 'index/product', $data);
    }

    //签名
    public function sign($data)
    {
        ksort($data);
        $sign_str = http_build_query($data) . '&apikey=' . $this->apikey;
        return strtoupper(md5(urldecode($sign_str)));
    }

    //回调
    public function notify()
    {
        $state = intval(I('state'));
        if ($state == 1) {
           $result= PorderModel::rechargeSusApi('yuanren', I('out_trade_num'), $_POST);
            if ($result) {
                echo "success";
            }else{
                echo "fail--《1》数据库或者日志写入出错\n<2> 数据库记录已经存在日志写入过";
            }
        } elseif ($state == 2) {
            //充值失败,根据自身业务逻辑进行后续处理
            $result= PorderModel::rechargeFailApi('yuanren', I('out_trade_num'), $_POST);
            if ($result) {
                echo "success";
            }else{
                echo "fail--《1》数据库或者日志写入出错\n<2> 数据库记录已经存在日志写入过";
            }
        } else {
            echo "fail";
        }
    }

    /**
     * get请求
     */
    private function http_get($url, $param)
    {
        $oCurl = curl_init();
        if (is_string($param)) {
            $strPOST = $param;
        } else {
            $strPOST = http_build_query($param);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
        curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($oCurl, CURLOPT_TIMEOUT, 30);
        curl_setopt($oCurl, CURLOPT_HEADER, 0);
        curl_setopt($oCurl, CURLOPT_HTTPHEADER, ["ContentType:application/x-www-form-urlencoded;charset=utf-8"]);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            $result = json_decode($sContent, true);
            if ($result['errno'] == 0) {
                return rjson(0, $result['errmsg'], $result['data']);
            } else {
                return rjson(1, $result['errmsg'], $result['data']);
            }
        } else {
            return rjson(1, '接口访问失败，http错误码' . $aStatus["http_code"]);
        }
    }
}