<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2018-03-23
 * Time: 21:42
 */

namespace app\common\library;


use app\common\model\Client;

class PayWay
{
    const PAY_WAY_NULL = 0;//未选择
    const PAY_WAY_JSYS = 1;//JS原生
    const PAY_WAY_BLA = 2;//余额支付
    const PAY_WAY_MPYS = 3;//小程序原生
    const PAY_WAY_OFFL = 4;//线下支付
    const PAY_WAY_H5YS = 5;//H5原生
    const PAY_WAY_ALIH5 = 6;//支付宝H5

    const ORDER_TYPE_CZ = 1;//充值订单
    const ORDER_TYPE_UP = 2;//升级订单

    //创建支付数据
    public static function create($paytype, $client, $option)
    {
        switch ($paytype) {
            case 1://微信
                switch ($client) {
                    case Client::CLIENT_WX: //公众号
                        return self::pay(self::PAY_WAY_JSYS, $option);
                    case Client::CLIENT_MP: //小程序
                        return self::pay(self::PAY_WAY_MPYS, $option);
                    case Client::CLIENT_H5: //H5
                        return self::pay(self::PAY_WAY_H5YS, $option);
                    default:
                        return rjson(1, '该客户端不支持的支付方式');
                }
                break;
            case 2://支付宝
                switch ($client) {
                    case Client::CLIENT_H5: //H5
                        return self::pay(self::PAY_WAY_ALIH5, $option);
                    default:
                        return rjson(1, '该客户端不支持的支付方式');
                }
                break;
            default:
                return rjson(1, '未知的支付方式');
        }
    }

    private static function pay($payway, $option)
    {
        switch ($payway) {
            case self::PAY_WAY_JSYS:
                return PayApi::create_wxpay_js($option);
            case self::PAY_WAY_MPYS:
                return PayApi::create_wxpay_mp($option);
            case self::PAY_WAY_H5YS:
                $res = PayApi::create_wxpay_h5($option);
                if ($res['errno'] == 0) {
                    return rjson(100, 'ok', $res['data']);
                }
                return $res;
            case self::PAY_WAY_ALIH5:
                $url = U('open/alipay_h5', ['param' => dyr_encrypt(json_encode($option))], true, true);
                return rjson(100, 'ok', $url);
            default:
                return rjson(1, '未知的支付方式');
        }
    }


}