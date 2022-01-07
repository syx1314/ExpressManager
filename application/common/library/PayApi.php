<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2018-03-23
 * Time: 21:42
 */

namespace app\common\library;


use Util\Random;
use WeChatPay\Builder;
use WeChatPay\Crypto\Rsa;
use WeChatPay\Formatter;
use WeChatPay\Util\PemUtil;

/*
 * 微信支付说明：
 * 平台证书
 * 这里的平台证书生成有些坑,我特意做说明,（成功环境php7.3）这是官方的介绍：https://github.com/wechatpay-apiv3/wechatpay-php/tree/main/bin
 * 1、下载sdk源码到本地,然后执行composer update 拉取所有包。
 * 2、进入到wechatpay目录下 执行代码（各个参数意义参考官方的文档） php -f  ./bin/CertificateDownloader.php --  -k GbtHMpepy7nEqpIxPmf3H4xkTpljVvfS -m 1294004701 -f d:\wechatpay\key.pem -s 6A0C139DB2036A7DBDCB1B21B69380D3D7F243D3 -o D:\
 * 3、如果提示ssl证书问题（unable to get local issuer certificate），下载ca证书放到php环境中（https://www.cnblogs.com/syay/p/10870726.html）
 * 4、生成成功后得到文件xxx.pem 放入站点使用
 */


class PayApi
{
    /**
     *H5微信支付
     */
    public static function create_wxpay_js($option)
    {
        if (!C('weixin.mch_id')) return rjson(1, '未配置微信mch_id');
        if (!C('weixin_notify_url')) return rjson(1, '未配置微信回调地址');
        if (!C('weixin.key')) return rjson(1, '未配置微信支付key');
        if (!C('wxpub_apiclient_cert')) return rjson(1, '未配置微信支付api证书');
        if (!C('wxpub_apiclient_key')) return rjson(1, '未配置微信支付api证书秘钥');
        if (!C('WEIXIN_PLATFORM_CERT')) return rjson(1, '未配置微信支付平台证书');

//        if (!$option['openid']) {
//            return rjson(1, '用户OPENID必须');
//        }

        $sslcert = ROOT_PATH . 'public' . C('wxpub_apiclient_cert');//api证书
        $sslkey = ROOT_PATH . 'public' . C('wxpub_apiclient_key');//api证书秘钥
        $platformcert = ROOT_PATH . 'public' . C('WEIXIN_PLATFORM_CERT');//平台证书
        // 加载商户私钥
        $merchantPrivateKeyInstance = PemUtil::loadPrivateKey($sslkey);
        // 加载商户证书
        $merchantCertificateInstance = PemUtil::loadCertificate($sslcert);
        // 解析商户证书序列号
        $merchantCertificateSerial = PemUtil::parseCertificateSerialNo($merchantCertificateInstance);
        // 加载平台证书
        $platformCertificateInstance = PemUtil::loadCertificate($platformcert);
        //解析平台证书序列号
        $platformCertificateSerial = PemUtil::parseCertificateSerialNo($platformCertificateInstance);
        // 工厂方法构造一个实例
        $instance = Builder::factory([
            'mchid' => C('weixin.mch_id'),
            'serial' => $merchantCertificateSerial,
            'privateKey' => $merchantPrivateKeyInstance,
            'certs' => [
                $platformCertificateSerial => $platformCertificateInstance,
            ],
        ]);
        try {
            $resp = $instance->chain("v3/pay/transactions/jsapi")->post(['json' => [
                'appid' => C('weixin.appid'),
                'mchid' => C('weixin.mch_id'),
                'description' => $option['body'],
                'out_trade_no' => $option['order_number'],
                'notify_url' => C('weixin_notify_url'),
                'amount' => [
                    'total' => (int)($option['total_price'] * 100),
                    'currency' => 'CNY'
                ],
                'payer' => [
                    'openid' => $option['openid']
                ]
            ]]);
            $resbody = $resp->getBody();
            $ret = json_decode($resbody, true);
            $params = [
                'appId' => $option['appid'],
                'timeStamp' => time(),
                'nonceStr' => Random::alnum(25),
                'package' => 'prepay_id=' . $ret['prepay_id']
            ];
            $params += ['sign' => Rsa::sign(
                Formatter::joinedByLineFeed(...array_values($params)),
                $merchantPrivateKeyInstance
            ), 'signType' => 'RSA'];
            return rjson(0, '创建微信支付订单成功', $params);
        } catch (\Exception $e) {
            $errmsg = mb_convert_encoding($e->getMessage(), 'UTF-8', 'UTF-8');
            return rjson(1, $errmsg);
        }
    }

    /**
     *H5微信支付
     */
    public static function create_wxpay_h5($option)
    {
        if (!C('weixin.appid')) return rjson(1, '未配置微信appid');
        if (!C('weixin.mch_id')) return rjson(1, '未配置微信mch_id');
        if (!C('weixin_notify_url')) return rjson(1, '未配置微信回调地址');
        if (!C('weixin.key')) return rjson(1, '未配置微信支付key');
        if (!C('wxpub_apiclient_cert')) return rjson(1, '未配置微信支付api证书');
        if (!C('wxpub_apiclient_key')) return rjson(1, '未配置微信支付api证书秘钥');
        if (!C('WEIXIN_PLATFORM_CERT')) return rjson(1, '未配置微信支付平台证书');

        $sslcert = ROOT_PATH . 'public' . C('wxpub_apiclient_cert');//api证书
        $sslkey = ROOT_PATH . 'public' . C('wxpub_apiclient_key');//api证书秘钥
        $platformcert = ROOT_PATH . 'public' . C('WEIXIN_PLATFORM_CERT');//平台证书
        // 加载商户私钥
        $merchantPrivateKeyInstance = PemUtil::loadPrivateKey($sslkey);
        // 加载商户证书
        $merchantCertificateInstance = PemUtil::loadCertificate($sslcert);
        // 解析商户证书序列号
        $merchantCertificateSerial = PemUtil::parseCertificateSerialNo($merchantCertificateInstance);
        // 加载平台证书
        $platformCertificateInstance = PemUtil::loadCertificate($platformcert);
        //解析平台证书序列号
        $platformCertificateSerial = PemUtil::parseCertificateSerialNo($platformCertificateInstance);
        // 工厂方法构造一个实例
        $instance = Builder::factory([
            'mchid' => C('weixin.mch_id'),
            'serial' => $merchantCertificateSerial,
            'privateKey' => $merchantPrivateKeyInstance,
            'certs' => [
                $platformCertificateSerial => $platformCertificateInstance,
            ],
        ]);
        try {
            $resp = $instance->chain("v3/pay/transactions/h5")->post(['json' => [
                'appid' => C('weixin.appid'),
                'mchid' => C('weixin.mch_id'),
                'description' => $option['body'],
                'out_trade_no' => $option['order_number'],
                'notify_url' => C('weixin_notify_url'),
                'amount' => [
                    'total' => (int)($option['total_price'] * 100),
                    'currency' => 'CNY'
                ],
                'scene_info' => [
                    'payer_client_ip' => get_client_ip(),
                    'h5_info' => [
                        'type' => 'Wap'
                    ]
                ]
            ]]);
            $resbody = $resp->getBody();
            $ret = json_decode($resbody, true);
            return rjson(0, '创建微信H5支付订单成功', $ret['mweb_url']);
        } catch (\Exception $e) {
            $errmsg = mb_convert_encoding($e->getMessage(), 'UTF-8', 'UTF-8');
            return rjson(1, $errmsg);
        }
    }

    //微信小程序支付
    public static function create_wxpay_mp($option)
    {
        if (!C('weixin.mch_id')) return rjson(1, '未配置微信mch_id');
        if (!C('weixin_notify_url')) return rjson(1, '未配置微信回调地址');
        if (!C('weixin.key')) return rjson(1, '未配置微信支付key');
        if (!$option['openid']) {
            return rjson(1, '用户OPENID必须');
        }

        $sslcert = ROOT_PATH . 'public' . C('wxpub_apiclient_cert');//api证书
        $sslkey = ROOT_PATH . 'public' . C('wxpub_apiclient_key');//api证书秘钥
        $platformcert = ROOT_PATH . 'public' . C('WEIXIN_PLATFORM_CERT');//平台证书
        // 加载商户私钥
        $merchantPrivateKeyInstance = PemUtil::loadPrivateKey($sslkey);
        // 加载商户证书
        $merchantCertificateInstance = PemUtil::loadCertificate($sslcert);
        // 解析商户证书序列号
        $merchantCertificateSerial = PemUtil::parseCertificateSerialNo($merchantCertificateInstance);
        // 加载平台证书
        $platformCertificateInstance = PemUtil::loadCertificate($platformcert);
        //解析平台证书序列号
        $platformCertificateSerial = PemUtil::parseCertificateSerialNo($platformCertificateInstance);
        // 工厂方法构造一个实例
        $instance = Builder::factory([
            'mchid' => C('weixin.mch_id'),
            'serial' => $merchantCertificateSerial,
            'privateKey' => $merchantPrivateKeyInstance,
            'certs' => [
                $platformCertificateSerial => $platformCertificateInstance,
            ],
        ]);
        try {
            $resp = $instance->chain("v3/pay/transactions/jsapi")->post(['json' => [
                'appid' => C('weixin.appid'),
                'mchid' => C('weixin.mch_id'),
                'description' => $option['body'],
                'out_trade_no' => $option['order_number'],
                'notify_url' => C('weixin_notify_url'),
                'amount' => [
                    'total' => (int)($option['total_price'] * 100),
                    'currency' => 'CNY'
                ],
                'payer' => [
                    'openid' => $option['openid']
                ]
            ]]);
            $resbody = $resp->getBody();
            $ret = json_decode($resbody, true);
            $params = [
                'appId' => $option['appid'],
                'timeStamp' => time(),
                'nonceStr' => Random::alnum(25),
                'package' => 'prepay_id=' . $ret['prepay_id']
            ];
            $params += ['sign' => Rsa::sign(
                Formatter::joinedByLineFeed(...array_values($params)),
                $merchantPrivateKeyInstance
            ), 'signType' => 'RSA'];
            return rjson(0, '创建微信支付订单成功', $params);
        } catch (\Exception $e) {
            $errmsg = mb_convert_encoding($e->getMessage(), 'UTF-8', 'UTF-8');
            return rjson(1, $errmsg);
        }
    }

    /**
     * 支付宝支付
     */
    public static function create_alipay_h5($option)
    {
        if (!C('rsaPrivateKey')) return rjson(1, '未配置支付宝商户私钥');
        if (!C('alipay_rsaPublicKey')) return rjson(1, '未配置支付宝应用公钥');
        if (!C('alipay.appid')) return rjson(1, '未配置支付宝appid');
        if (!C('alipay_notify_url')) return rjson(1, '未配置支付宝回调地址');
        $biz_content = json_encode([
            'body' => $option['body'],
            'subject' => $option['body'],
            'out_trade_no' => $option['order_number'],
            'timeout_express' => '90m',
            'total_amount' => $option['total_price'],
            'product_code' => 'QUICK_WAP_WAY'
        ]);
        vendor('alipay.AopSdk');
        $c = new \AopClient();
        $c->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $c->appId = C('alipay.appid');
        $c->rsaPrivateKey = C('rsaPrivateKey');
        $c->alipayrsaPublicKey = C('alipay_rsaPublicKey');
        $c->format = "json";
        $c->charset = "utf-8";
        $c->signType = "RSA2";

        $request = new \AlipayTradeWapPayRequest();
        $request->setBizContent($biz_content);
        $request->setNotifyUrl(C('alipay_notify_url'));
        $request->setReturnUrl(C('WEB_URL2') . 'pages/index/index');
        $result = $c->pageExecute($request);
        return rjson(0, '成功', $result);
    }


}