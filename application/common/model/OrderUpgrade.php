<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2017-11-14
 * Time: 11:13
 */

namespace app\common\model;

use app\common\library\Createlog;
use app\common\library\PayWay;
use app\common\library\SubscribeMessage;
use app\common\library\Templetmsg;
use think\Model;


class OrderUpgrade extends Model
{
    const PR = 'UPG';


    public static function createOrder($grade_id, $cus_id)
    {
        $customer = M('customer')->where(['id' => $cus_id])->find();
        $grade = M('customer_grade')->where(['id' => $grade_id])->find();
        if (!$grade) {
            return rjson(1, '未找到会员等级');
        }
        $aid = M('order_upgrade')->insertGetId([
            'grade_id' => $grade['id'],
            'customer_id' => $cus_id,
            'order_number' => self::PR . time() . rand(100, 999),
            'create_time' => time(),
            'is_pay' => 0,
            'total_price' => $grade['up_price'],
            'body' => $grade['grade_name'],
            'rebate_price' => $grade['rebate_price'],
            'reward_price' => $grade['up_rewards'],
            'rebate_id' => $customer['f_id'],
            'pay_way' => PayWay::PAY_WAY_NULL
        ]);
        if (!$aid) {
            return rjson(1, '创建订单失败');
        }
        return rjson(0, "创建订单成功", M('order_upgrade')->where(['id' => $aid])->find());
    }


    //生成支付数据
    public static function create_pay($aid, $payway, $client)
    {
        $order = M('order_upgrade')->where(['id' => $aid])->find();
        $customer = M('customer')->where(['id' => $order['customer_id']])->find();
        if (!$order || !$customer) {
            return rjson(1, '数据错误');
        }
        return PayWay::create($payway, $client, [
            'openid' => $customer['wx_openid'] ? $customer['wx_openid'] : $customer['ap_openid'],
            'body' => $order['body'],
            'order_number' => $order['order_number'],
            'total_price' => $order['total_price'],
            'appid' => $customer['weixin_appid']
        ]);
    }


    public static function notify($order_number, $payway, $serial_number)
    {
        $uorder = M('order_upgrade')->where(['order_number' => $order_number, 'is_pay' => 0])->find();
        if (!$uorder) {
            return rjson(1, 'no order');
        }
        M('order_upgrade')->where(['id' => $uorder['id'], 'is_pay' => 0])->setField(['is_pay' => 1, 'pay_time' => time(), 'pay_way' => $payway, 'serial_number' => $serial_number]);
        $cus = M('customer')->where(['id' => $uorder['customer_id']])->find();
        //升级操作
        self::upGrade($uorder['customer_id'], $uorder['grade_id'], '付费升级：' . $uorder['body'] . ",支付金额：" . $uorder['total_price'] . ",支付接口：" . C('PAYWAY')[$payway] . ",订单号：" . $uorder['order_number'], '回调系统');
        //升级-上级获得返利
        if ($uorder['is_rebate'] == 0 && $uorder['rebate_price'] > 0 && $uorder['rebate_id'] > 0) {
            self::where(['id' => $uorder['id']])->setField(['is_rebate' => 1]);
            Balance::revenue($uorder['rebate_id'], $uorder['rebate_price'], '会员[' . $cus['id'] . ']升级' . $uorder['body'] . '获得收益', Balance::STYLE_REWARDS, '系统');
        }
        //升级获得奖励
        if ($uorder['reward_price'] > 0) {
            Balance::revenue($uorder['customer_id'], $uorder['reward_price'], '自己升级' . $uorder['body'] . '获得奖励', Balance::STYLE_REWARDS, '系统');
        }
        return rjson(0, 'ok');
    }

    //升级操作
    public static function upGrade($customer_id, $grade_id, $remark, $operator)
    {
        $user = M('customer')->where(['id' => $customer_id])->find();

        M('customer')->where(['id' => $customer_id])->setField(['grade_id' => $grade_id]);
        Createlog::customerLog($customer_id, $remark, $operator);

        $newgrade = M('customer_grade')->where(['id' => $grade_id])->value('grade_name');
        $oldgrade = M('customer_grade')->where(['id' => $user['grade_id']])->value('grade_name');

        $user['client'] == Client::CLIENT_WX && Templetmsg::upSus($customer_id, '升级成功', $user['username'], $oldgrade, $newgrade);
        $user['client'] == Client::CLIENT_MP && SubscribeMessage::upSus($customer_id, '升级成功', $remark);

        return rjson(0, '操作成功');
    }
}