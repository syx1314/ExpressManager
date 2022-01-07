<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2017-11-18
 * Time: 9:19
 */

namespace app\common\library;

use Util\Wechat;

/**
 * 微信模板消息
 
 **/
class Templetmsg
{
    private static function sendTemplateMessage($open_id, $template_id, $config, $datam, $url)
    {
        $weixin = new Wechat($config);
        $data = [
            'touser' => $open_id,
            'template_id' => $template_id,
            'url' => $url,
            'topcolor' => "#FF0000",
            'data' => $datam
        ];
        $res = $weixin->sendTemplateMessage($data);
        M('weixin_templetmsg_log')->insert(array('msg' => $res['errmsg'], 'weixin_appid' => $config['appid'], 'data' => json_encode($data), 'create_time' => time(), 'ret' => json_encode($res)));
        return rjson($res['errno'], $res['errmsg'], $res['data']);
    }

    /**
     * 发送
     **/
    public static function send($user_id, $tmp_clo, $data, $url = "")
    {
        $customer = M("customer")->where(['id' => $user_id, 'is_del' => 0])->find();
        if (!$customer || !$customer['weixin_appid'] || !$customer['wx_openid']) {
            return rjson(1, '该用户无法发送模板消息');
        }
        $templet = M('weixin_templetmsg')->where(['weixin_appid' => $customer['weixin_appid']])->find();
        if (!$templet || !isset($templet[$tmp_clo])) {
            return rjson(1, '未设置模板消息ID');
        }
        $config = M('weixin')->where(['appid' => $customer['weixin_appid']])->find();
        if (!$config) {
            return rjson(1, '未找到微信公众号配置信息');
        }
        return self::sendTemplateMessage($customer['wx_openid'], $templet[$tmp_clo], $config, $data, $url);
    }

    //余额变动提示
    public static function balanceCg($user_id, $first, $cgtime, $reason, $money, $balance, $remark = "", $url = "")
    {
        $data = [
            'first' => ['value' => $first, 'color' => '#000'],
            'keyword1' => ['value' => $cgtime, 'color' => '#000'],
            'keyword2' => ['value' => $reason, 'color' => '#000'],
            'keyword3' => ['value' => '￥' . round($money, 2), 'color' => '#000'],
            'keyword4' => ['value' => '￥' . $balance, 'color' => '#000'],
            'remark' => ['value' => $remark, 'color' => '#000'],
        ];
        return self::send($user_id, 'balancecg_template_id', $data, $url);
    }

    //积分变动提示
    public static function integralCg($user_id, $first, $cgtime, $reason, $money, $balance, $remark = "", $url = "")
    {
        $data = [
            'first' => ['value' => $first, 'color' => '#000'],
            'keyword1' => ['value' => $cgtime, 'color' => '#000'],
            'keyword2' => ['value' => $reason, 'color' => '#000'],
            'keyword3' => ['value' => round($money, 0) . '积分', 'color' => '#000'],
            'keyword4' => ['value' => $balance . '积分', 'color' => '#000'],
            'remark' => ['value' => $remark, 'color' => '#000'],
        ];
        return self::send($user_id, 'balancecg_template_id', $data, $url);
    }

    //提现审核提醒
    public static function tixianSh($user_id, $first, $username, $shtime, $money, $remark = "", $url = "")
    {
        $data = [
            'first' => ['value' => $first, 'color' => '#000'],
            'keyword1' => ['value' => $username, 'color' => '#000'],
            'keyword2' => ['value' => $shtime, 'color' => '#000'],
            'keyword3' => ['value' => $money, 'color' => '#000'],
            'remark' => ['value' => $remark, 'color' => '#ff0000'],
        ];
        return self::send($user_id, 'tixiansh_template_id', $data, $url);
    }

    //邀请注册成功通知
    public static function newUser($user_id, $first, $nick, $userid, $regtime, $remark = "如有任何疑问请联系在线客服", $url = "")
    {
        $data = [
            'first' => ['value' => $first, 'color' => '#000'],
            'keyword1' => ['value' => $nick, 'color' => '#000'],
            'keyword2' => ['value' => $userid, 'color' => '#000'],
            'keyword3' => ['value' => $regtime, 'color' => '#000'],
            'remark' => ['value' => $remark, 'color' => '#ff0000'],
        ];
        return self::send($user_id, 'newuser_template_id', $data, $url);
    }

    //下单成功提醒
    public static function paySus($user_id, $first, $order_number, $money, $remark = "如有任何疑问请联系在线客服", $url = "")
    {
        $data = [
            'first' => ['value' => $first, 'color' => '#000'],
            'keyword1' => ['value' => $order_number, 'color' => '#000'],
            'keyword2' => ['value' => $money, 'color' => '#000'],
            'remark' => ['value' => $remark, 'color' => '#ff0000'],
        ];
        return self::send($user_id, 'paysus_template_id', $data, $url);
    }

    //充值成功
    public static function chargeSus($user_id, $first, $type, $money, $sustime, $remark = "如有任何疑问请联系在线客服", $url = "")
    {
        $data = [
            'first' => ['value' => $first, 'color' => '#000'],
            'keyword1' => ['value' => $type, 'color' => '#000'],
            'keyword2' => ['value' => $money, 'color' => '#000'],
            'keyword3' => ['value' => $sustime, 'color' => '#000'],
            'remark' => ['value' => $remark, 'color' => '#ff0000'],
        ];
        return self::send($user_id, 'chargesus_template_id', $data, $url);
    }

    //充值失败通知
    public static function chargeFail($user_id, $first, $money, $failtime, $remark = "如有任何疑问请联系在线客服", $url = "")
    {
        $data = [
            'first' => ['value' => $first, 'color' => '#000'],
            'keyword1' => ['value' => $money, 'color' => '#000'],
            'keyword2' => ['value' => $failtime, 'color' => '#000'],
            'remark' => ['value' => $remark, 'color' => '#ff0000'],
        ];
        return self::send($user_id, 'chargefail_template_id', $data, $url);
    }

    //退款通知
    public static function refund($user_id, $first, $money, $deltime, $restyle = '原路退回', $remark = "如有任何疑问请联系在线客服", $url = "")
    {
        $data = [
            'first' => ['value' => $first, 'color' => '#000'],
            'keyword1' => ['value' => "￥" . $money, 'color' => '#000'],
            'keyword2' => ['value' => $deltime, 'color' => '#000'],
            'keyword3' => ['value' => $restyle, 'color' => '#000'],
            'remark' => ['value' => $remark, 'color' => '#ff0000'],
        ];
        return self::send($user_id, 'refund_template_id', $data, $url);
    }

    //升级成功
    public static function upSus($user_id, $first, $name, $oldgrade, $newgrade, $remark = "如有任何疑问请联系在线客服", $url = "")
    {
        $data = [
            'first' => ['value' => $first, 'color' => '#000'],
            'keyword1' => ['value' => $name, 'color' => '#000'],
            'keyword2' => ['value' => $oldgrade, 'color' => '#000'],
            'keyword3' => ['value' => $newgrade, 'color' => '#000'],
            'keyword4' => ['value' => time_format(time()), 'color' => '#000'],
            'remark' => ['value' => $remark, 'color' => '#ff0000'],
        ];
        return self::send($user_id, 'upsus_template_id', $data, $url);
    }

}