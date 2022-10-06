<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2018-03-23
 * Time: 21:42
 */

namespace app\common\library;

use app\common\enum\ExpressOrderEnum;
use app\common\enum\ExpressOrderPayStatusEnum;
use Util\Random;

// 快递单子退款
class WxExpressrefundapi
{
    /**
     * 充值订单退款
     */
    public static function porder_wxpay_refund($order_number)
    {


        $bill =  M('expressorder_bill')->where(['order_number' => $order_number])->find();
        if(!$bill) {
            Createlog::expressOrderLog($order_number, '账单：'.$bill['bill_no'].' 退款失败 找不到账单');
            return djson(1,"退款失败 找不到账单","退款失败 找不到账单");
        }
        $customer = M('customer')->where(['id' => $bill['userid']])->find();
        if (!$customer) {
            return djson(1,"退款失败 没有此用户","退款失败 没有此用户");
        }

        if (!C('weixin.mch_id')) return rjson(1, '未配置微信的mch_id');
        if (!C('weixin.key')) return rjson(1, '未配置微信支付key');
        if (!C('wxpub_apiclient_cert')) return rjson(1, '未上传微信退款cert证书');
        if (!C('wxpub_apiclient_key')) return rjson(1, '未上传微信退款key证书');
        $data = [
            'appid' => $customer['weixin_appid'],
            'mch_id' => C('weixin.mch_id'),
            'nonce_str' => Random::alnum(25),
            'sign_type' => 'MD5',
            'out_trade_no' => $bill['bill_no'],  // 本账单号
            'out_refund_no' => $bill['bill_no'],  // 微信交易流水号
            'total_fee' => (int)($bill['pay_money'] * 100), // 推支付的金额
            'refund_fee' => (int)($bill['pay_money'] * 100),
        ];
        $wxpay = new \Util\Wxpay();
        $data['sign'] = $wxpay->getSign($data, C('weixin.key'));

        $url = "https://api.mch.weixin.qq.com/secapi/pay/refund";
        $sslcert = ROOT_PATH . DS . 'public' . C('wxpub_apiclient_cert');
        $sslkey = ROOT_PATH . DS . 'public' . C('wxpub_apiclient_key');
        $ret = $wxpay->postXmlSSLCurl($wxpay->arrayToXml($data), $url, $sslcert, $sslkey);
        if (!$ret) {
            Createlog::expressOrderLog($bill['order_number'], '账单：'.$bill['bill_no'].' 退款失败|接口|请求接口未成功|可能原因：证书文件错误');
            return rjson(1, '退款接口未请求成功');
        }
        $json = json_decode(json_encode(simplexml_load_string($ret, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        if ($json['return_code'] != 'SUCCESS') {
            Createlog::expressOrderLog($bill['order_number'], '账单：'.$bill['bill_no'].' 退款失败|接口|请求退款失败' . $json['return_msg']);
            return ['errno' => 1, 'errmsg' => $json['return_msg'], 'data' => ''];
        }
        if ($json['result_code'] != 'SUCCESS') {
            Createlog::expressOrderLog($bill['order_number'], '账单：'.$bill['bill_no'].' 退款失败|接口|' . $json['err_code_des']);
            return ['errno' => 1, 'errmsg' => $json['err_code_des'], 'data' => ''];
        }
        Createlog::expressOrderLog($bill['order_number'], '账单：'.$bill['bill_no'].' 退款成功|接口|' . json_encode($json));
        // 退款置状态
        M('expressorder_bill')->where(['order_number' => $order_number])->setField(['pay_status'=>ExpressOrderPayStatusEnum::REFUND_COMPLETE]);
        return ['errno' => 0, 'errmsg' => "退款成功", 'data' => ''];
    }

}
