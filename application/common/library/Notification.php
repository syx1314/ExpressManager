<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2018-03-23
 * Time: 21:42
 */

namespace app\common\library;


//下发通知
use app\common\enum\ExpressOrderEnum;
use app\common\model\Client;
use think\Log;
use Util\Http;

class Notification
{
    //充值成功
    public static function rechargeSus($porder_id)
    {
        $porder = M('porder')->where(['id' => $porder_id, 'status' => 4, 'is_del' => 0])->find();
        if (!$porder) {
            return rjson(1, '未找到订单');
        }
        $customer = M('customer')->where(['id' => $porder['customer_id'], 'is_del' => 0, 'status' => 1])->find();
        if (!$customer) {
            return rjson(1, '用户未找到');
        }
        switch ($porder['client']) {
            case Client::CLIENT_WX://公众号
                Templetmsg::chargeSus($porder['customer_id'], '已经充值成功！', C('PRODUCT_TYPE')[$porder['type']], $porder['title'], date('Y-m-d H:i', time()));
                M('porder')->where(['id' => $porder['id']])->setField(['is_notification' => 1]);
                return rjson(0, '公众号通知成功');
            case Client::CLIENT_MP://小程序
                SubscribeMessage::rechargeSus($porder['customer_id'], $porder['order_number'], $porder['title'], $porder['mobile'], "已充值成功，请注意查收！");
                M('porder')->where(['id' => $porder['id']])->setField(['is_notification' => 1]);
                return rjson(0, '小程序通知成功');
            case Client::CLIENT_API://API
                M('porder')->where(['id' => $porder['id']])->setInc("notification_num", 1);
                if (!$porder['notify_url']) {
                    Createlog::porderLog($porder['id'], '未设置api回调地址');
                    return rjson(1, '未设置api回调地址');
                } else {
                    $param = self::notify_data($customer, $porder);
                    Createlog::porderLog($porder['id'], '回调链接：' . $porder['notify_url'] . '，post数据:' . http_build_query($param));
                    $result = Http::post($porder['notify_url'], $param);
                    M('porder')->where(['id' => $porder['id']])->setField(['notification_time' => time()]);
                    if ($result == 'success') {
                        Createlog::porderLog($porder['id'], 'api回调通知成功');
                        M('porder')->where(['id' => $porder['id']])->setField(['is_notification' => 1]);
                        return rjson(0, 'api回调通知成功');
                    } else {
                        Createlog::porderLog($porder['id'], 'api回调通知失败,响应数据：' . $result);
                        return rjson(1, 'api回调通知失败,响应数据：' . $result);
                    }
                }
                break;
            default:
                Createlog::porderLog($porder['id'], '该端订单无需充值成功通知');
                return rjson(0, '无需通知');
        }
    }

    //充值失败
    public static function rechargeFail($porder_id)
    {
        $porder = M('porder')->where(['id' => $porder_id, 'status' => ['in', '1,5,6'], 'is_del' => 0])->find();
        if (!$porder) {
            return rjson(1, '未找到订单');
        }
        $customer = M('customer')->where(['id' => $porder['customer_id'], 'is_del' => 0, 'status' => 1])->find();
        if (!$customer) {
            return rjson(1, '用户未找到,可能原因删除、禁用');
        }
        switch ($porder['client']) {
            case Client::CLIENT_WX://公众号
                Templetmsg::chargeFail($porder['customer_id'], '充值失败了！', $porder['title'], time_format(time()));
                M('porder')->where(['id' => $porder['id']])->setField(['is_notification' => 1]);
                return rjson(0, '公众号通知成功');
            case Client::CLIENT_MP://小程序
                SubscribeMessage::rechargeFail($porder['customer_id'], $porder['order_number'], $porder['title'], $porder['mobile'], time_format(time()));
                M('porder')->where(['id' => $porder['id']])->setField(['is_notification' => 1]);
                return rjson(0, '小程序通知成功');
            case Client::CLIENT_API://API
                M('porder')->where(['id' => $porder['id']])->setInc("notification_num", 1);
                if (!$porder['notify_url']) {
                    Createlog::porderLog($porder['id'], '未设置api回调地址');
                    return rjson(1, '未设置api回调地址');
                } else {
                    $param = self::notify_data($customer, $porder);
                    Createlog::porderLog($porder['id'], '回调链接：' . $porder['notify_url'] . '，post数据:' . http_build_query($param));
                    $result = Http::post($porder['notify_url'], $param);
                    M('porder')->where(['id' => $porder['id']])->setField(['notification_time' => time()]);
                    if ($result == 'success') {
                        Createlog::porderLog($porder['id'], 'api回调通知成功');
                        M('porder')->where(['id' => $porder['id']])->setField(['is_notification' => 1]);
                        return rjson(0, 'api回调通知成功');
                    } else {
                        Createlog::porderLog($porder['id'], 'api回调通知失败,响应数据：' . var_export($result, true));
                        return rjson(1, 'api回调通知失败,响应数据：' . var_export($result, true));
                    }
                }
                break;
            default:
                Createlog::porderLog($porder['id'], '该端订单无需充值失败通知');
                return rjson(0, '无需通知');
        }
    }

    //退款成功
    public static function refundSus($porder_id)
    {
        $porder = M('porder')->where(['id' => $porder_id, 'status' => 6, 'is_del' => 0])->find();
        if (!$porder) {
            return rjson(1, '未找到订单');
        }
        $customer = M('customer')->where(['id' => $porder['customer_id'], 'is_del' => 0, 'status' => 1])->find();
        if (!$customer) {
            return rjson(1, '用户未找到');
        }
        switch ($porder['client']) {
            case Client::CLIENT_WX://公众号
                return Templetmsg::refund($porder['customer_id'], '订单' . $porder['order_number'] . '退款成功！', $porder['total_price'], date('Y-m-d H:i', time()));
            case Client::CLIENT_MP://小程序
                return SubscribeMessage::refundSus($porder['customer_id'], $porder['order_number'], $porder['total_price'], '原路退回', '退款成功', date('Y-m-d H:i', time()));
            default:
                Createlog::porderLog($porder['id'], '该端订单无需退款通知');
                return rjson(0, '无需通知');
        }
    }

    //下单成功
    public static function paySus($porder_id)
    {
        $porder = M('porder')->where(['id' => $porder_id, 'status' => 2, 'is_del' => 0])->find();
        if (!$porder) {
            return rjson(1, '未找到订单');
        }
        $customer = M('customer')->where(['id' => $porder['customer_id'], 'is_del' => 0, 'status' => 1])->find();
        if (!$customer) {
            return rjson(1, '用户未找到');
        }
        switch ($porder['client']) {
            case Client::CLIENT_WX://公众号
                return Templetmsg::paySus($porder['customer_id'], '订单已经提交，正在充值中...', $porder['order_number'], $porder['total_price']);
            case Client::CLIENT_MP://小程序
                return SubscribeMessage::paySus($porder['customer_id'], $porder['order_number'], $porder['title'], $porder['total_price'], '订单已经提交，正在充值中...');
            default:
                Createlog::porderLog($porder['id'], '该端订单无需下单成功通知');
                return rjson(0, '无需通知');
        }
    }
    //快递下单成功
    public static function payExpressSus($bill_id)
    {
        Log::error('发送公众号模板消息');
        // 账单支付完成
        $bill=M('expressorder_bill')->where(['id' => $bill_id,'pay_status'=>2] )->find();
        if (!$bill) {
            return rjson(1, '未找到符合账单');
        }
        $customer = M('customer')->where(['id' => $bill['userid'], 'is_del' => 0, 'status' => 1])->find();
        if (!$customer) {
            return rjson(1, '用户未找到');
        }
        Log::error('发送公众号模板消息2');
        $first ='';
        if ($bill['type'] ==1) {
            return Templetmsg::paySus($bill['userid'], '快递下单完成,等待快递接单中......', $bill['order_number'], $bill['pay_money'].'元');
        }else if ($bill['type'] ==1) {
            return Templetmsg::paySus($bill['userid'], '超重/转寄等费用支付完毕', $bill['order_number'], $bill['pay_money'].'元');
        }
    }

    //生成回调数据
    private static function notify_data($customer, $porder)
    {
        $state = $porder['status'] == 4 ? 1 : 2;
        $data = [
            'userid' => $porder['customer_id'],
            'order_number' => $porder['order_number'],
            'out_trade_num' => $porder['out_trade_num'],
            'mobile' => $porder['mobile'],
            'otime' => time(),
            'state' => $state,
            'remark' => $porder['remark'],
            'voucher' => $state == 1 ? C('WEB_URL') . 'yrapi.php/open/voucher/id/' . $porder['id'] . '.html' : ''
        ];
        ksort($data);
        $sign_str = urldecode(http_build_query($data) . '&apikey=' . $customer['apikey']);
        $data['sign'] = Yrapilib::sign($sign_str);
        return $data;
    }

}
