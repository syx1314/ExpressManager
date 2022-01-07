<?php


namespace Recharge;


use think\Log;

/**
 * 接口异常监控
 **/
class InterfaceControl
{
    private $userid;
    private $apiurl;
    private $uid1;
    private $uid2;//充值接口

    public function __construct()
    {
        $this->userid = 'AT_syzza8EUxqliHY9HNPhDnpuOpc4dloTH';
        $this->apiurl = 'http://wxpusher.zjiecode.com/api/send/message';
        $this->uid1 = 'UID_Exxme3lVOFLk5fpDDCnkctkAHBem';
        $this->uid2 = '';
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
    public function sendMsg($clasName,$param)
    {
        $data = [
            "appToken" => $this->userid,
            "content" => $clasName.'</br>提交得参数:</br>'.urldecode(http_build_query($param))."</br><font color='red'> http状态码0 无法确认是否提交成功请查看渠道,请速速查看</font>",
            "summary" => "中控接口出问题了米拉",
            "contentType" => 2,
            "uids" => [
                $this->uid1,
                $this->uid2
            ],
            "url" => '',
        ];
        return $this->http_post($this->apiurl, $data);
    }
    public function send($msg) {
        $data = [
            "appToken" => $this->userid,
            "content" => $msg,
            "summary" => "有人尝试登录",
            "contentType" => 2,
            "uids" => [
                $this->uid1,
            ],
            "url" => '',
        ];
        return $this->http_post($this->apiurl, $data);
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
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
        curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($oCurl, CURLOPT_TIMEOUT, 30);
        curl_setopt($oCurl, CURLOPT_HEADER, 0);
        curl_setopt($oCurl, CURLOPT_HTTPHEADER, ["Content-Type:application/json"]);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            $result = json_decode($sContent, true);
            Log::error("监控接口发送成功".urldecode($sContent));
        } else {
            return rjson(1, '接口访问失败，http错误码' . $aStatus["http_code"]);
        }
    }
}