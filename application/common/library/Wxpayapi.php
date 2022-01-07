<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2018-03-23
 * Time: 21:42
 */

namespace app\common\library;


use Util\Random;

class Wxpayapi
{
    /**
     *H5微信支付
     */
    public static function create_wxpay_h5($option)
    {
        if (!C('weixin.appid')) return rjson(1, '未配置微信appid');
        if (!C('weixin.mch_id')) return rjson(1, '未配置微信mch_id');
        if (!C('weixin_notify_url')) return rjson(1, '未配置微信回调地址');
        if (!C('weixin.key')) return rjson(1, '未配置微信支付key');
        $data = [
            'appid' => C('weixin.appid'),
            'mch_id' => C('weixin.mch_id'),
            'nonce_str' => Random::alnum(25),
            'openid' => $option['openid'],
            'sign_type' => 'MD5',
            'body' => $option['body'],
            'detail' => $option['body'],
            'attach' => json_encode(['order_number' => $option['order_number']]),
            'out_trade_no' => $option['order_number'],
            'total_fee' => (int)($option['total_price'] * 100),
            'spbill_create_ip' => get_client_ip(),
            'notify_url' => C('weixin_notify_url'),
            'trade_type' => "JSAPI"
        ];
        $wxpay = new \Util\Wxpay();
        $data['sign'] = $wxpay->getSign($data, C('weixin.key'));
        $xml = $wxpay->postXmlCurl($wxpay->arrayToXml($data), "https://api.mch.weixin.qq.com/pay/unifiedorder");
        $ret = $wxpay->xmlToArray($xml);
        if (!$ret) {
            return rjson(1, '微信支付提交失败');
        }
        if ($ret['return_code'] != 'SUCCESS') {
            return rjson(1, $ret['return_msg']);
        }
        if ($ret['result_code'] != 'SUCCESS') {
            return rjson(1, $ret['err_code_des']);
        }
        $pays = [
            'appId' => C('weixin.appid'),
            'timeStamp' => time(),
            'nonceStr' => Random::alnum(25),
            'package' => 'prepay_id=' . $ret['prepay_id'],
            'signType' => 'MD5'
        ];
        $pays['sign'] = $wxpay->getSign($pays, C('weixin.key'));
        return rjson(0, '创建微信支付订单成功', $pays);
    }

    //微信小程序支付
    public static function create_wxpay_mp($option)
    {
        if (!C('APPLET.appid')) return rjson(1, '未配置微信appid');
        if (!C('APPLET.mch_id')) return rjson(1, '未配置微信mch_id');
        if (!C('applet_notify_url')) return rjson(1, '未配置微信回调地址');
        if (!C('APPLET.key')) return rjson(1, '未配置微信支付key');
        $data = [
            'appid' => C('APPLET.appid'),
            'mch_id' => C('APPLET.mch_id'),
            'nonce_str' => Random::alnum(25),
            'openid' => $option['openid'],
            'sign_type' => 'MD5',
            'body' => $option['body'],
            'detail' => $option['body'],
            'attach' => json_encode(['order_number' => $option['order_number']]),
            'out_trade_no' => $option['order_number'],
            'total_fee' => (int)($option['total_price'] * 100),
            'spbill_create_ip' => get_client_ip(),
            'notify_url' => C('applet_notify_url'),
            'trade_type' => "JSAPI"
        ];
        $wxpay = new \Util\Wxpay();
        $data['sign'] = $wxpay->getSign($data, C('APPLET.key'));
        $xml = $wxpay->postXmlCurl($wxpay->arrayToXml($data), "https://api.mch.weixin.qq.com/pay/unifiedorder");
        $ret = $wxpay->xmlToArray($xml);
        if (!$ret) {
            return rjson(1, '微信支付提交失败');
        }
        if ($ret['return_code'] != 'SUCCESS') {
            return rjson(1, $ret['return_msg']);
        }
        if ($ret['result_code'] != 'SUCCESS') {
            return rjson(1, $ret['err_code_des']);
        }
        $pays = [
            'appId' => C('APPLET.appid'),
            'timeStamp' => time(),
            'nonceStr' => Random::alnum(25),
            'package' => 'prepay_id=' . $ret['prepay_id'],
            'signType' => 'MD5'
        ];
        $pays['sign'] = $wxpay->getSign($pays, C('APPLET.key'));
        return rjson(0, '创建微信支付订单成功', $pays);
    }

    /**
     *生成代扣数据
     */
    public static function create_wxpay_dk($option)
    {
        if (!C('APPLET.appid')) return rjson(1, '未配置微信appid');
        if (!C('APPLET.mch_id')) return rjson(1, '未配置微信mch_id');
        if (!C('APPLET_DK_NOTIFY_URL')) return rjson(1, '未配置微信代扣回调地址');
        if (!C('APPLET.key')) return rjson(1, '未配置微信支付key');
        if (!C('APPLET.plan_id')) return rjson(1, '未配置微信代扣协议模板id');
        $data = [
            'appid' => C('APPLET.appid'),
            'contract_code' => $option['contract_code'],
            'contract_display_account' => $option['contract_display_account'],
            'mch_id' => C('APPLET.mch_id'),
            'notify_url' => C('APPLET_DK_NOTIFY_URL'),
            'plan_id' => C('APPLET.plan_id'),
            'request_serial' => $option['request_serial'],
            'timestamp' => time()
        ];
        $wxpay = new \Util\Wxpay();
        $data['sign'] = $wxpay->getSign($data, C('APPLET.key'));
        return rjson(0, 'ok', $data);
    }

    //签约代扣协议以后-申请扣款
    public static function dk_apply_pay($option)
    {
        if (!C('APPLET.appid')) return rjson(1, '未配置微信appid');
        if (!C('APPLET.mch_id')) return rjson(1, '未配置微信mch_id');
        if (!C('applet_notify_url')) return rjson(1, '未配置微信小程序回调地址');
        if (!C('APPLET.key')) return rjson(1, '未配置微信支付key');
        $data = [
            'appid' => C('APPLET.appid'),
            'mch_id' => C('APPLET.mch_id'),
            'nonce_str' => Random::alnum(25),
            'body' => $option['body'],
            'out_trade_no' => $option['out_trade_no'],
            'total_fee' => (int)($option['total_price'] * 100),
            'notify_url' => C('applet_notify_url'),
            'trade_type' => 'PAP',
            'contract_id' => $option['contract_id'],
        ];
        $wxpay = new \Util\Wxpay();
        $data['sign'] = $wxpay->getSign($data, C('APPLET.key'));
        $xml = $wxpay->postXmlCurl($wxpay->arrayToXml($data), "https://api.mch.weixin.qq.com/pay/pappayapply");
        $ret = $wxpay->xmlToArray($xml);
        if (!$ret) {
            return rjson(1, '微信支付提交失败');
        }
        if ($ret['return_code'] != 'SUCCESS') {
            return rjson(1, $ret['return_msg']);
        }
        if ($ret['result_code'] != 'SUCCESS') {
            return rjson(1, $ret['err_code_des']);
        }
        return rjson(0, '代扣订单已受理', $ret);
    }

}