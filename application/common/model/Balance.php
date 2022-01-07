<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2017-11-14
 * Time: 11:13
 */

namespace app\common\model;

use app\common\library\SubscribeMessage;
use app\common\library\Templetmsg;
use think\Model;

/**
 
 **/
class Balance extends Model
{
    /**
     * 订单消费
     */
    const STYLE_ORDERS = 1;
    /**
     * 佣金收入
     */
    const STYLE_REWARDS = 2;
    /**
     * 提现
     */
    const STYLE_WITHDRAW = 3;
    /**
     * 余额充值
     */
    const STYLE_RECHARGE = 4;
    /**
     * 退款
     */
    const STYLE_REFUND = 5;

    /**
     * 收入
     */
    public static function revenue($customer_id, $money, $remark, $style, $operator)
    {
        $uid = M('customer')->where(['id' => $customer_id])->setInc("balance", $money);
        if (!$uid) {
            return rjson(1, '收入失败');
        }
        $user = M('customer')->where(['id' => $customer_id])->find();
        $uid && M('balance_log')->insertGetId([
            'money' => $money,
            'type' => 1,
            'remark' => $remark,
            'create_time' => time(),
            'style' => $style,
            'customer_id' => $customer_id,
            'balance' => $user['balance'],
            'operator' => $operator
        ]);
        $user['client'] == Client::CLIENT_WX && Templetmsg::balanceCg($customer_id, '你有新的余额收入了', time_format(time()), $remark, $money, $user['balance']);
        $user['client'] == Client::CLIENT_MP && SubscribeMessage::balanceXf($customer_id, time(), $money, $user['balance']);
        return rjson(0, '操作成功');
    }

    /**
     * 作者：廖强
     * 支出
     */
    public static function expend($customer_id, $money, $remark, $style, $operator)
    {
        $uid = M('customer')->where(['id' => $customer_id, 'balance' => ['egt', $money]])->setDec("balance", $money);
        if (!$uid) {
            return rjson(1, '账户余额不足！');
        }
        $user = M('customer')->where(['id' => $customer_id])->find();
        $uid && M('balance_log')->insertGetId([
            'money' => $money,
            'type' => 2,
            'remark' => $remark,
            'create_time' => time(),
            'style' => $style,
            'customer_id' => $customer_id,
            'balance' => $user['balance'],
            'operator' => $operator
        ]);
        $user['client'] == Client::CLIENT_WX && Templetmsg::balanceCg($customer_id, '你有新的余额支出了', time_format(time()), $remark, $money, $user['balance']);
        $user['client'] == Client::CLIENT_MP && SubscribeMessage::balanceKk($customer_id, time(), $money, '你有新的余额支出了,点击查看详情');
        return rjson(0, '操作成功');
    }


}