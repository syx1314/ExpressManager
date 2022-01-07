<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 廖强
// +----------------------------------------------------------------------

namespace app\common\library;

/**
 * 模板消息
 *
 */
class Templetmsgapplet
{

    private static $applet = null;
    private static $errmsg = "";
    private static $retmsg = "";

    /**
     
     * @param $form_id
     * @param $num 使用次数
     * @param $customer_id
     */
    public static function saveFormId($form_id, $num, $customer_id)
    {
        $arr['form_id'] = $form_id;
        $arr['num'] = $num;
        $arr['customer_id'] = $customer_id;
        $arr['create_time'] = time();
        M('weixin_form_id')->insertGetId($arr);
    }

    public static function sendTemplateMessage($user_id, $template_id, $page, $datam)
    {
        C(Configapi::getconfig());
        self::$applet = new \Util\Applet(C('APPLET'));
        $customer = M("customer")->where(['id' => $user_id])->find();
        $form_id = self::get_form_id($user_id);
        if (!$form_id) return false;
        $data['touser'] = $customer['ap_openid'];
        $data['template_id'] = $template_id;
        $data['form_id'] = $form_id;
        $data['page'] = $page;
        $data['data'] = $datam;
        $ret = self::$applet->sendTemplateMessage($data);
        //写入日志
        M('weixin_templetmsg_log')->insert(array('msg' => self::$applet->errMsg, 'data' => json_encode($data), 'create_time' => time(), 'ret' => json_encode($ret)));
        if ($ret) {
            return true;
        } else {
            self::$errmsg = self::$applet->errMsg;
            self::$retmsg = $ret;
            return false;
        }
    }

    public static function sendTemplateMessageFormid($userid, $formid, $template_id, $page, $datam)
    {
        C(Configapi::getconfig());
        self::$applet = new \Util\Applet(C('APPLET'));
        $customer = M("customer")->where(['id' => $userid])->find($userid);
        $data['touser'] = $customer['ap_openid'];
        $data['template_id'] = $template_id;
        $data['form_id'] = $formid;
        $data['page'] = $page;
        $data['data'] = $datam;
        $ret = self::$applet->sendTemplateMessage($data);
        //写入日志
        M('weixin_templetmsg_log')->insert(array('msg' => self::$applet->errMsg, 'data' => json_encode($data), 'create_time' => time(), 'ret' => json_encode($ret)));
        if ($ret) {
            return true;
        } else {
            self::$errmsg = self::$applet->errMsg;
            self::$retmsg = $ret;
            return false;
        }
    }


    /**
     
     * @param $customer_id
     * @return bool
     * 获取form_id
     */
    public static function get_form_id($customer_id)
    {
        $formid = M('weixin_form_id')->where(['customer_id' => $customer_id, 'status' => 1, 'create_time' => ['gt', time() - 601200]])->order("create_time asc")->find();
        if ($formid) {
            if ($formid['num'] - 1 > 0) {
                M('weixin_form_id')->where(['id' => $formid['id']])->setDec('num');
            } else {
                M('weixin_form_id')->where(['id' => $formid['id']])->setField(['status' => 0]);
            }
            return $formid['form_id'];
        } else {
            self::$errmsg = "没有可用的form_id";
            return false;
        }
    }

    /**
     * 作者：廖强
     * 付款成功
     */
    public static function paySusMsg($user_id, $order_number, $body, $price, $pay_time)
    {
        $data = array(
            'keyword1' => array('value' => $order_number, 'color' => '#1a1a1a'),
            'keyword2' => array('value' => $body, 'color' => '#173177'),
            'keyword3' => array('value' => $price, 'color' => '#173177'),
            'keyword4' => array('value' => time_format($pay_time), 'color' => '#173177')
        );
        $ret = self::sendTemplateMessage($user_id, 'JgH4BNOSMGNqAcFDiQ6LAvO7sJecc4qkZqdKaJrNJo0', 'pages/views/index/record', $data);
        return $ret;
    }

    /**
     * 作者：廖强
     * 充值成功
     */
    public static function rechargeSus($user_id, $order_number, $type, $mobile, $taocan, $remark)
    {
        $data = array(
            'keyword1' => array('value' => $order_number, 'color' => '#1a1a1a'),
            'keyword2' => array('value' => $type, 'color' => '#173177'),
            'keyword3' => array('value' => $mobile, 'color' => '#173177'),
            'keyword4' => array('value' => $taocan, 'color' => '#173177'),
            'keyword5' => array('value' => $remark, 'color' => '#173177')
        );
        $ret = self::sendTemplateMessage($user_id, 'b2GjYpi_rm8R21oxRpSI3Ts5wBf6a2GqpJ7ghcD4l00', 'pages/views/index/record', $data);
        return $ret;
    }


    /**
     * 作者：廖强
     * 充值失败
     */
    public static function rechargeFail($user_id, $order_number, $type, $mobile, $taocan, $price, $remark)
    {
        $data = array(
            'keyword1' => array('value' => $order_number, 'color' => '#1a1a1a'),
            'keyword2' => array('value' => $type, 'color' => '#1a1a1a'),
            'keyword3' => array('value' => $mobile, 'color' => '#1a1a1a'),
            'keyword4' => array('value' => $taocan, 'color' => '#1a1a1a'),
            'keyword5' => array('value' => $price, 'color' => '#1a1a1a'),
            'keyword6' => array('value' => $remark, 'color' => '#1a1a1a')
        );
        $ret = self::sendTemplateMessage($user_id, 'a2sShps8o-1Wn1hNVUUKbov6pl60BMYqgsqLpMfAJCs', 'pages/views/index/record', $data);
        return $ret;
    }
}
