<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2018-03-23
 * Time: 21:42
 */

namespace app\common\library;

use Util\Random;

class Wxrefundapi
{
    /**
     * 充值订单退款
     */
    public static function porder_wxpay_refund($id)
    {
        $porder = M('porder')->where(['id' => $id, 'status' => 5])->find();
        $customer = M('customer')->where(['id' => $porder['customer_id']])->find();
        if (!$porder || !$customer) {
            return ['errno' => 1, 'errmsg' => '数据错误', 'data' => ''];
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
            'out_trade_no' => $porder['order_number'],
            'out_refund_no' => $porder['order_number'],
            'total_fee' => (int)($porder['total_price'] * 100),
            'refund_fee' => (int)($porder['total_price'] * 100),
        ];
        $wxpay = new \Util\Wxpay();
        $data['sign'] = $wxpay->getSign($data, C('weixin.key'));

        $url = "https://api.mch.weixin.qq.com/secapi/pay/refund";
        $sslcert = ROOT_PATH . DS . 'public' . C('wxpub_apiclient_cert');
        $sslkey = ROOT_PATH . DS . 'public' . C('wxpub_apiclient_key');
        $ret = $wxpay->postXmlSSLCurl($wxpay->arrayToXml($data), $url, $sslcert, $sslkey);
        if (!$ret) {
            Createlog::porderLog($porder['id'], '退款失败|接口|请求接口未成功|可能原因：证书文件错误');
            return rjson(1, '退款接口未请求成功');
        }
        $json = json_decode(json_encode(simplexml_load_string($ret, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        if ($json['return_code'] != 'SUCCESS') {
            Createlog::porderLog($porder['id'], '退款失败|接口|请求退款失败' . $json['return_msg']);
            return ['errno' => 1, 'errmsg' => $json['return_msg'], 'data' => ''];
        }
        if ($json['result_code'] != 'SUCCESS') {
            Createlog::porderLog($porder['id'], '退款失败|接口|' . $json['err_code_des']);
            return ['errno' => 1, 'errmsg' => $json['err_code_des'], 'data' => ''];
        }
        Createlog::porderLog($porder['id'], '退款成功|接口|' . json_encode($json));
        return ['errno' => 0, 'errmsg' => "退款成功", 'data' => ''];
    }

}