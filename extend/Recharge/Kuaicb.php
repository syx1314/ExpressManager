<?php


namespace Recharge;


use app\common\model\Porder as PorderModel;

/**
 * 快充呗
 **/
class Kuaicb
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
            "account" => $mobile,
            "out_trade_num" => $out_trade_num,
            "product_id" => $param['param1'],
            "notify_url" => $this->notify
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
     *查询qq昵称
     */
    public function qqnick($qq)
    {
        $data = [
            "userid" => $this->userid,
            "qq" => $qq
        ];
        $data['sign'] = $this->sign($data);
        return $this->http_get($this->apiurl . 'index/qqnick', $data);
    }

    /**
     *查询手机余额
     */
    public function mobile_balance($mobile)
    {
        $data = [
            "userid" => $this->userid,
            "mobile" => $mobile
        ];
        $data['sign'] = $this->sign($data);
        return $this->http_get($this->apiurl . 'index/mobile_balance', $data);
    }

    /**
     *电费缴费地区
     */
    public function ele_city()
    {
        $data = [
            "userid" => $this->userid
        ];
        $data['sign'] = $this->sign($data);
        return $this->http_get($this->apiurl . 'index/ele_city', $data);
    }

    /**
     *电费缴费单位
     */
    public function ele_dw($city_id = '')
    {
        $data = [
            "userid" => $this->userid,
            "city_id" => $city_id
        ];
        $data['sign'] = $this->sign($data);
        return $this->http_get($this->apiurl . 'index/ele_dw', $data);
    }

    /**
     * 电费余额查询
     */
    public function ele_balance($dw_id, $account)
    {
        $data = [
            "userid" => $this->userid,
            "dw_id" => $dw_id,
            "account" => $account
        ];
        $data['sign'] = $this->sign($data);
        return $this->http_get($this->apiurl . 'index/ele_balance', $data);
    }

    /**
     * 电费下单
     */
    public function ele_recharge($out_trade_num, $account, $money, $dw_id)
    {
        $data = [
            "userid" => $this->userid,
            "money" => $money,
            "out_trade_num" => $out_trade_num,
            "dw_id" => $dw_id,
            "account" => $account,
            "notify_url" => $this->notify
        ];
        $data['sign'] = $this->sign($data);
        return $this->http_get($this->apiurl . 'index/ele_recharge', $data);
    }

    //签名
    public function sign($data)
    {
        ksort($data);
        $sign_str = urldecode(http_build_query($data) . '&apikey=' . $this->apikey);
        return strtoupper(md5($sign_str));
    }


    //回调
    public function notify()
    {
        $state = intval(I('state'));
        if ($state == 1) {
            //充值成功,根据自身业务逻辑进行后续处理
            PorderModel::rechargeSusApi('kuaicb', I('out_trade_num'), $_GET);
            echo "success";
        } elseif ($state == 2) {
            //充值失败,根据自身业务逻辑进行后续处理
            PorderModel::rechargeFailApi('kuaicb', I('out_trade_num'), $_GET);
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