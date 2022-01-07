<?php


namespace Recharge;

use app\common\model\Porder as PorderModel;

/**
 * 聚合数据充值平台
 * http://op.tianjurenhe.com/ofpay/mobile/onlineorder
 **/
class Juheh
{
    private $appkey;
    private $openid;
    private $apiurl;

    private $telCheckUrl = 'http://op.tianjurenhe.com/ofpay/mobile/telcheck';
    private $telQueryUrl = 'http://op.tianjurenhe.com/ofpay/mobile/telquery';
    private $submitUrl = 'http://op.tianjurenhe.com/ofpay/mobile/onlineorder';
    private $staUrl = 'http://op.tianjurenhe.com/ofpay/mobile/ordersta';


    public function __construct($option)
    {
        $this->openid = isset($option['param1']) ? $option['param1'] : '';
        $this->appkey = isset($option['param2']) ? $option['param2'] : '';
        $this->apiurl = isset($option['param4']) ? $option['param4'] : '';
    }

    /**
     * 根据手机号码及面额查询是否支持充值
     */
    public function mobile_telcheck($mobile, $pervalue)
    {
        $params = 'key=' . $this->appkey . '&phoneno=' . $mobile . '&cardnum=' . $pervalue;
        $content = $this->juhecurl($this->telCheckUrl, $params);
        $result = $this->_returnArray($content);
        if ($result['error_code'] == '0') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 根据手机号码和面额获取商品信息
     */
    public function mobile_telquery($mobile, $pervalue)
    {
        $params = 'key=' . $this->appkey . '&phoneno=' . $mobile . '&cardnum=' . $pervalue;
        $content = $this->juhecurl($this->telQueryUrl, $params);
        return $this->_returnArray($content);
    }

    /**
     * 提交话费充值
     */
    public function recharge($orderid, $mobile, $param, $isp = '')
    {
        $pervalue = $param['param1'];
        $sign = md5($this->openid . $this->appkey . $mobile . $pervalue . $orderid);//校验值计算
        $data = array(
            'key' => $this->appkey,
            'phoneno' => $mobile,
            'cardnum' => $pervalue,
            'orderid' => $orderid,
            'sign' => $sign
        );
        $content = $this->juhecurl($this->apiurl, $data, 1);
        $res = $this->_returnArray($content);
        if ($res['error_code'] == 0) {
            return rjson(0, $res['reason'], $res['result']);
        } else {
            return rjson(1, $res['reason'], $res['result']);
        }
    }

    /**
     * 话费订单的充值状态
     */
    public function mobile_sta($orderid)
    {
        $params = 'key=' . $this->appkey . '&orderid=' . $orderid;
        $content = $this->juhecurl($this->staUrl, $params);
        return $this->_returnArray($content);
    }


    /**
     * 将JSON内容转为数据，并返回
     * @param string $content [内容]
     * @return array
     */
    public function _returnArray($content)
    {
        return json_decode($content, true);
    }

    /**
     * 请求接口返回内容
     * @param string $url [请求的URL地址]
     * @param string $params [请求的参数]
     * @param int $ipost [是否采用POST形式]
     * @return  string
     */
    public function juhecurl($url, $params = false, $ispost = 0)
    {
        $httpInfo = array();
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'JuheData');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($ispost) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            if ($params) {
                curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }
        $response = curl_exec($ch);
        if ($response === FALSE) {
            return false;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
        curl_close($ch);
        return $response;
    }

    //回调
    public function notify()
    {
        $orderid = addslashes(I('orderid')); //商户的单号
        $sta = addslashes(I('sta')); //充值状态
        if ($sta == '1') {
            //充值成功,根据自身业务逻辑进行后续处理
            PorderModel::rechargeSusApi('juhe', $orderid, file_get_contents("php://input"));
        } elseif ($sta == '9') {
            //充值失败,根据自身业务逻辑进行后续处理
            PorderModel::rechargeFailApi('juhe', $orderid, file_get_contents("php://input"));
        }
        echo "success";
    }
}