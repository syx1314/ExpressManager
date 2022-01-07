<?php


namespace Recharge;


use think\Log;

/**
 * PP移动分省
 * 呆呆
 *  wx:trsoft66
 **/
class PPYiDongProvince2
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
     * @param $out_trade_num
     * @param $mobile
     * @param $param
     * @param string $isp
     * @param $product_id
     * @return \think\response\Json
     */
    public function recharge($out_trade_num, $mobile, $param, $isp = '')
    {
        $data = [
            "uid" => $this->userid,
            "phone" => $mobile,
            "orderid" => $out_trade_num,
            "money" => $param['param1'],
            "notify_url" => $this->notify,
        ];
        $data['sign'] = $this->sign_str($data,$this->apikey);
        return $this->http_post($this->apiurl, $data);
    }
    public function sign_str($array,$key){
        ksort($array);
        $sign_str = '';
        foreach($array as $k=>$v)
        {
            $sign_str.=($k.'='.$v);
        }
        $sign_str.=$key;
        return md5($sign_str);
    }

    /**
     * post请求
     */
    private function http_post($url, $param)
    {
        $oCurl = curl_init();
        if (is_string($param)) {
            $strPOST = $param;
        } else {
            $strPOST = json_encode($param);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_POST, true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
        curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($oCurl, CURLOPT_TIMEOUT, 30);
        curl_setopt($oCurl, CURLOPT_HEADER, 0);
        curl_setopt($oCurl, CURLOPT_LOCALPORT, '88');
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        Log::error('提交得参数'.$strPOST);
          Log::error('返回得参数'.$sContent);
        Log::error('返回'.http_build_query($aStatus));
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 0) {
        //   $interControl= new InterfaceControl();
        //   $interControl->sendMsg(__CLASS__,$param);
            return rjson(0, "http状态码0 无法确认是否提交成功请查看渠道", 'http状态码0 无法确认是否提交成功请查看渠道');
        }else {
            if (intval($aStatus["http_code"]) == 200) {
                $result = json_decode($sContent, true);
                Log::error($result);
                if ($result['return_code'] == 'SUCCESS') {
                    return rjson(0, $result['return_code'], $result['return_msg']);
                } else {
                    return rjson(1, $result['return_code'].$result['return_msg'], $result['return_msg']);
                }
            } else {
                return rjson(1, '接口访问失败，http错误码' . $aStatus["http_code"]);
            }
        }

    }
}