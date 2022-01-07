<?php

namespace app\api\controller;

use app\common\library\Email;
use app\common\library\PayWay;
use app\common\model\OrderUpgrade;
use app\common\model\Porder as PorderModel;
use WeChatPay\Crypto\AesGcm;


/**
 * Class Notify
 * 支付回调控制器
 */
class Notify extends \app\common\controller\Base
{
    public function _commonbase()
    {

    }

    //回调
    private function notify($info)
    {
        $szm = substr($info['order_number'], 0, 3);
        switch ($szm) {
            case PorderModel::PR:
                PorderModel::notify($info['order_number'], $info['payway'], $info['serial_number']);
                break;
            case OrderUpgrade::PR:
                OrderUpgrade::notify($info['order_number'], $info['payway'], $info['serial_number']);
                break;
            default:
        }
    }


    /**
     * 微信支付
     */
    public function weixin()
    {
        $inpstr = file_get_contents("php://input");
        $miarr = json_decode($inpstr, true);
        $data_str = AesGcm::decrypt($miarr['resource']['ciphertext'], C('weixin.key'), $miarr['resource']['nonce'], $miarr['resource']['associated_data']);
        $data = json_decode($data_str, true);
        $weixin = M('weixin')->where(['appid' => $data['appid']])->find();
        if (!$weixin) {
            Email::sendMail('非平台微信支付回调', var_export($data, true));
            return false;
        }
        $sv = [
            'order_number' => $data['out_trade_no'],
            'channel_type' => '微信公众号',
            'trade_status' => $data['trade_state'],
            'transaction_id' => $data['transaction_id'],
            'transaction_type' => '支付',
            'total_fee' => $data['amount']['total'],
            'allstring' => $inpstr,
            'create_time' => time(),
            'optionals' => isset($data['attach']) ? $data['attach'] : "",
        ];
        M('pay_log')->insertGetId($sv);

        if ($data['trade_state'] == "SUCCESS") {
            $info = [
                'order_number' => $data['out_trade_no'],
                'money' => intval(strval(floatval($data['amount']['total']) / 100)),
                'serial_number' => $data['transaction_id'],
                'pay_time' => strtotime($data['success_time']),
                'payway' => $data['trade_type'] == 'MWEB' ? PayWay::PAY_WAY_H5YS : ($weixin['type'] == 1 ? PayWay::PAY_WAY_JSYS : PayWay::PAY_WAY_MPYS)
            ];
            $this->notify($info);
        }
        echo json_encode(['code' => "SUCCESS", "message" => "成功"]);
        return true;
    }

    /**
     * 支付宝回调
     */
    public function alipay()
    {
        vendor('alipay.AopSdk');
        $aop = new \AopClient();
        $aop->alipayrsaPublicKey = C('alipay_rsaPublicKey');
        $flag = $aop->rsaCheckV1($_POST, NULL, "RSA2");
        if ($flag) {
            $data = $_POST;
            if (M('pay_log')->where(['transaction_id' => $data['trade_no']])->find()) {
                echo 'success';
                exit;
            }
            $sv = [
                'order_number' => $data['out_trade_no'],
                'sign' => $data['sign'],
                'channel_type' => '支付宝',
                'trade_status' => $data['trade_status'],
                'transaction_id' => $data['trade_no'],
                'transaction_type' => '支付',
                'total_fee' => intval(strval(floatval($data['total_amount']) * 100)),
                'allstring' => json_encode($data),
                'create_time' => time(),
                'optionals' => isset($data['passback_params']) ? $data['passback_params'] : "",
            ];

            M('pay_log')->insertGetId($sv);
            $info = [
                'order_number' => $data['out_trade_no'],
                'money' => floatval($data['total_amount']),
                'serial_number' => $data['trade_no'],
                'payway' => PayWay::PAY_WAY_ALIH5,
            ];
            $this->notify($info);
            echo 'success';
        } else {
            echo 'fail';
        }
    }

    /**
     * 微信h5页面支付回调
     */
    public function balance($order_number)
    {
        $this->notify([
            'order_number' => $order_number,
            'serial_number' => $order_number,
            'payway' => PayWay::PAY_WAY_BLA
        ]);
    }

    /**
     * 线下支付
     */
    public function offline($order_number)
    {
        $this->notify([
            'order_number' => $order_number,
            'serial_number' => $order_number,
            'payway' => PayWay::PAY_WAY_OFFL
        ]);
    }

    /**
     * 写日志
     */
    private function write($text)
    {
        $myfile = fopen("paylog.txt", "a") or die("Unable to open file!");
        fwrite($myfile, $text . "\r\n");
        fclose($myfile);
    }
}
