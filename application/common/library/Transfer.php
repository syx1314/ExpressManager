<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2018-03-23
 * Time: 21:42
 */

namespace app\common\library;


use Util\Random;
use Util\Rsa;

class Transfer
{
    public static $msg;

    /**
     * @param $config
     * @param $sslcert
     * @param $sslkey
     * @param $openid
     * @param $partner_trade_no
     * @param $totalfee
     * @param string $desc
     * @return bool|\think\response\Json
     *  提现到零钱
     */
    public static function wx_transfers($appid, $openid, $partner_trade_no, $totalfee, $desc = '付款到零钱')
    {
        $config = C('weixinappwithdrawal');
        if (!$config['mch_id']) return rjson(1, '未配置微信商户号mch_id');
        if (!$config['key']) return rjson(1, '未配置微信支付key');
        if (!C('wx_withdrawal_apiclient_cert')) return rjson(1, '未上传微信提现cert证书');
        if (!C('wx_withdrawal_apiclient_key')) return rjson(1, '未上传微信提现key证书');
        if (!$openid) return rjson(1, '未设置用户openid');
        $data = [
            'mch_appid' => $appid,
            'mchid' => $config['mch_id'],
            'nonce_str' => Random::alnum(25),
            'partner_trade_no' => $partner_trade_no,
            'openid' => $openid,
            'check_name' => 'NO_CHECK',
            'amount' => $totalfee,
            'desc' => $desc,
            'spbill_create_ip' => get_client_ip()
        ];
        $wxpay = new \Util\Wxpay();
        $data['sign'] = $wxpay->getSign($data, $config['key']);
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";

        $sslcert = ROOT_PATH . DS . 'public' . C('wx_withdrawal_apiclient_cert');
        $sslkey = ROOT_PATH . DS . 'public' . C('wx_withdrawal_apiclient_key');

        $trid = M('transfers_log')->insertGetId(['trade_no' => $partner_trade_no, 'totalfee' => $totalfee, 'param' => json_encode($data), 'create_time' => time()]);

        $ret = $wxpay->postXmlSSLCurl($wxpay->arrayToXml($data), $url, $sslcert, $sslkey);
        if (!$ret) {
            M('transfers_log')->where(['id' => $trid])->setField(['return_all' => '没有返回值', 'status' => 0, 'err_msg' => '没有返回值']);
            return rjson(1, '微信付款零钱请求失败');
        }
        $json = json_decode(json_encode(simplexml_load_string($ret, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        if ($json['return_code'] != 'SUCCESS') {
            M('transfers_log')->where(['id' => $trid])->setField(['return_all' => json_encode($json), 'status' => 0, 'err_msg' => $json['return_msg']]);
            return rjson(1, $json['return_msg'], $json);
        }
        if ($json['result_code'] != 'SUCCESS') {
            M('transfers_log')->where(['id' => $trid])->setField(['return_all' => json_encode($json), 'status' => 0, 'err_msg' => $json['err_code_des']]);
            return rjson(1, $json['err_code_des'], $json);
        }

        M('transfers_log')->where(['id' => $trid])->setField(['return_all' => json_encode($json), 'status' => 1]);
        return rjson(0, '付款成功', $json);
    }

    /**
     * @param $config
     * @param $sslcert
     * @param $sslkey
     * @param $partner_trade_no
     * @param $bank_no
     * @param $name
     * @param $bank_code
     * @param $totalfee
     * @param string $desc
     * @return bool|\think\response\Json
     * 付款到银行卡
     * Transfer::wx_pay_bank(C('weixinwzpay'), C('wxpub_apiclient_cert'), C('wxpub_apiclient_key'), 'TF' . time(), "6227003661040542705", "廖强", "1003", 100);
     */
    public static function wx_pay_bank($config, $sslcert, $sslkey, $partner_trade_no, $bank_no, $name, $bank_code, $totalfee, $desc = '付款到银行卡')
    {
        if (!$config['appid']) {
            self::$msg = '未配置微信商户账号appi';
            return false;
        }
        if (!$config['mch_id']) {
            self::$msg = '未配置微信商户号mch_id';
            return false;
        }
        if (!$config['key']) {
            self::$msg = '未配置微信支付key';
            return false;
        }
        if (!$config['publickey']) {
            self::$msg = '公钥未配置';
            return false;
        }
        if (!$sslcert) {
            self::$msg = '未上传微信cert证书';
            return false;
        }
        if (!$sslkey) {
            self::$msg = '未上传微信key证书';
            return false;
        }

        $wxpay = new \Util\Wxpay();
        $rsa = new RSA($config['publickey']);
        $data = [
            'mch_id' => $config['mch_id'],
            'nonce_str' => Random::alnum(25),
            'partner_trade_no' => $partner_trade_no,
            'enc_bank_no' => $rsa->pubEncrypt($bank_no),
            'enc_true_name' => $rsa->pubEncrypt($name),//收款方用户名
            'bank_code' => $bank_code,
            'amount' => $totalfee,
            'desc' => $desc
        ];
        $data['sign'] = $wxpay->getSign($data, $config['key']);
        $sslcert = $_SERVER['DOCUMENT_ROOT'] . $sslcert;
        $sslkey = $_SERVER['DOCUMENT_ROOT'] . $sslkey;

        $trid = M('transfers_log')->insertGetId(['trade_no' => $partner_trade_no, 'totalfee' => $totalfee, 'param' => json_encode($data), 'create_time' => time()]);

        $ret = $wxpay->postXmlSSLCurl($wxpay->arrayToXml($data), "https://api.mch.weixin.qq.com/mmpaysptrans/pay_bank", $sslcert, $sslkey);
        if ($ret) {
            $json = json_decode(json_encode(simplexml_load_string($ret, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
            if ($json['return_code'] == "SUCCESS") {
                if ($json['result_code'] == 'SUCCESS') {
                    M('transfers_log')->where(['id' => $trid])->setField(['return_all' => json_encode($json), 'status' => 1, 'err_msg' => $json['return_msg']]);
                    return true;
                } else {
                    self::$msg = $json['err_code_des'];
                    return false;
                }
            }
            M('transfers_log')->where(['id' => $trid])->setField(['return_all' => json_encode($json), 'status' => 0, 'err_msg' => $json['return_msg']]);
            self::$msg = $json['return_msg'];
            return false;
        }
        M('transfers_log')->where(['id' => $trid])->setField(['return_all' => '没有返回值', 'status' => 0, 'err_msg' => '没有返回值']);
        self::$msg = "请求失败！";
        return false;
    }

    /**
     * @param $mch_id
     * @param $nonce_str
     * 获取商户公钥，用于rsa 加密;
     * 公钥文件需要转换为KCS#8才可以用,使用以下【openssl rsa -RSAPublicKey_in -in pcs1.pem -out  pcs8.pem】指令转码后使用。openssl安装方法：https://blog.csdn.net/kitok/article/details/72957185
     * Transfer::wx_get_public_key(C('weixinwzpay')['mch_id'], 'STR' . time(), C('weixinwzpay')['key'],C('wxpub_apiclient_cert'), C('wxpub_refund_apiclient_key'));
     */
    public static function wx_get_public_key($mch_id, $nonce_str, $key, $sslcert, $sslkey)
    {
        $wxpay = new \Util\Wxpay();
        $publickeydata = [
            'mch_id' => $mch_id,
            'nonce_str' => $nonce_str,
            'sign_type' => 'MD5'
        ];
        $sslcert = $_SERVER['DOCUMENT_ROOT'] . $sslcert;
        $sslkey = $_SERVER['DOCUMENT_ROOT'] . $sslkey;
        $publickeydata['sign'] = $wxpay->getSign($publickeydata, $key);
        $ret_pu_key = $wxpay->postXmlSSLCurl($wxpay->arrayToXml($publickeydata), "https://fraud.mch.weixin.qq.com/risk/getpublickey ", $sslcert, $sslkey);
        if ($ret_pu_key) {
            $json_pu_key = json_decode(json_encode(simplexml_load_string($ret_pu_key, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
            if ($json_pu_key['return_code'] == "SUCCESS" && $json_pu_key['result_code'] == 'SUCCESS') {
                $myfile = fopen($mch_id . "pub.pem", "w") or die("Unable to open file!");
                fwrite($myfile, $json_pu_key['pub_key']);
                fclose($myfile);
                return true;
            }
            self::$msg = $json_pu_key['return_msg'];
        }
        return false;
    }
}