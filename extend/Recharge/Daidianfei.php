<?php

namespace Recharge;

use app\common\model\Porder as PorderModel;
use think\Log;

/**
 * 电费
 */
class Daidianfei
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
    public function recharge($out_trade_num, $account, $param, $isp = '')
    {
        $isp1 ='';
        if ($isp) {
            $isp1 = str_replace('省','',$isp);
            $isp1 = str_replace('市','',$isp1);
        }
        $data = [
            "partner_id" => $this->userid,
            "partner_order_no" => $out_trade_num,
            "account" => $account,
            "amount" => $param['param1'],
            "type" => 1,
            "area" => $isp1,
            "notify_url" => $this->notify,
        ];
        $data['sign'] = $this->sign($data);
        return $this->http_get($this->apiurl, $data);
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
        echo '检查电费订单';
        $data = [
            "partner_id" => $this->userid,
            "partner_order_no" => $out_trade_nums
        ];
        $data['sign'] = md5($data['partner_id'].$data['partner_order_no'].$this->apikey);
        $res=$this->http_get('http://ed.hzjbgc.cn' . '/api/queryOrder', $data);
        if ($res) {
          $orderInfo =  $res['data']['data'];
          if ($orderInfo) {
              return $orderInfo['charge_amount'];
          }
        }
        return '';
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

        $sign_str = $data['partner_id'].$data['partner_order_no'].$data['account'].$data['amount'].
            $data['type'].$data['area'].$data['notify_url'].$this->apikey;
        return md5($sign_str);
    }

    //回调
    public function notify()
    {
//        status：-1充值失败,0未充值,1部分充值,2充值完成
       Log::error("煲事件电费返回".json_encode($_POST));
        $state = intval(I('status'));
        if ($state == 2) {
            $result= PorderModel::rechargeSusApi('daidaidianfei', I('partner_order_no'), $_POST);
            if ($result) {
                echo "success";
            }else{
                echo "fail--《1》数据库或者日志写入出错\n<2> 数据库记录已经存在日志写入过";
            }
        } elseif ($state == -1) {
            //充值失败,根据自身业务逻辑进行后续处理
            $result= PorderModel::rechargeFailApi('daidaidianfei', I('partner_order_no'), $_POST);
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
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        Log::error('提交得数据'.$strPOST);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 0){
            return rjson(0, 'http_code=0,无法确认结果提交请查询渠道', 'http_code=0,无法确认结果提交请查询渠道');
        }else if (intval($aStatus["http_code"]) == 200) {
            $result = json_decode($sContent, true);
            if ($result['code'] == 1) {
                return rjson(0, $result['msg'], $result);
            } else {
                return rjson(1, $result['msg'], $result);
            }
        } else {
            return rjson(1, '接口访问失败，http错误码' . $aStatus["http_code"]);
        }
    }
}