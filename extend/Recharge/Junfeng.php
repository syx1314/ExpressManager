<?php


namespace Recharge;


use app\common\model\Porder as PorderModel;

/**
 * http://ip:9086/onlinepay.do
 **/
class Junfeng
{
    private $userId;
    private $key;
    private $notify;
    private $apiurl;

    public function __construct($option)
    {
        $this->userId = isset($option['param1']) ? $option['param1'] : '';
        $this->key = isset($option['param2']) ? $option['param2'] : '';
        $this->notify = isset($option['param3']) ? $option['param3'] : '';
        $this->apiurl = isset($option['param4']) ? $option['param4'] : '';
    }

    /**
     * 提交充值号码充值
     */
    public function recharge($orderid, $mobile, $param, $isp = '')
    {
        $time = time_format(time(), 'YmdHis');
        $data = [
            "userid" => $this->userId,
            "productid" => '',
            'price' => $param['param1'],
            'num' => 1,
            "mobile" => $mobile,
            'spordertime' => $time,
            "sporderid" => $orderid
        ];
        $sign_str = http_build_query($data) . '&key=' . $this->key;
        $sign = md5($sign_str);
        $paytype = $this->get_teltype($isp);
        $data['sign'] = $sign;
        $data['back_url'] = $this->notify;
        $data['paytype'] = $paytype;
        return $this->http_post($this->apiurl, $data);
    }

    private function get_teltype($str)
    {
        switch ($str) {
            case '移动':
                return 'yd';
            case '联通':
                return 'lt';
            case '电信':
                return 'dx';
            default:
                return '';
        }
    }

    /**
     * @param $methond
     * @param $param
     * @return bool|mixed
     */
    private function http_post($url, $param)
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
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
        curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($oCurl, CURLOPT_TIMEOUT, 60);
        curl_setopt($oCurl, CURLOPT_HEADER, 0);
        curl_setopt($oCurl, CURLOPT_HTTPHEADER, ["ContentType:application/x-www-form-urlencoded;charset=utf-8"]);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            $result = $this->xmlToArray($sContent);
            if ($sContent && $result['resultno'] == 0) {
                return rjson(0, '下单成功', $result);
            } else {
                return rjson(1, '下单失败', $result);
            }
        } else {
            return rjson(1, '接口访问失败，http错误码' . $aStatus["http_code"]);
        }
    }

    /**
     *    作用：将xml转为array
     */
    private function xmlToArray($xml)
    {
        //将XML转为array
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }


    public function notify()
    {
        $state = intval(I('resultno'));
        if ($state == 1) {
            //充值成功,根据自身业务逻辑进行后续处理
            PorderModel::rechargeSusApi('junfeng', I('sporderid'), $_POST);
            echo "success";
        } else if ($state == 9) {
            //充值失败,根据自身业务逻辑进行后续处理
            PorderModel::rechargeFailApi('junfeng', I('sporderid'), $_POST);
            echo "success";
        } else {
            echo "fail";
        }
    }
}