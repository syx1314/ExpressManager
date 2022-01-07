<?php

namespace app\yrapi\controller;


use app\common\library\Email;
use app\common\library\Yrapilib;

/**
 * @package app\api\controller
 */
class Home extends Base
{

    public function _dayuanren()
    {
        $param = I('post.');
        if (!isset($param['userid']) || !isset($param['sign'])) {
            djson(1, '参数错误或请求方式错误，请使用http-post表单')->send();
            exit;
        }
        $this->customer = M('customer')->where(['id' => $param['userid'], 'type' => 2])->find();
        if (!$this->customer) {
            djson(1, '未开通的商户ID,请联系客服开通')->send();
            exit;
        }
        unset($param['sign']);
        ksort($param);
        $sign_str = urldecode(http_build_query($param) . '&apikey=' . $this->customer['apikey']);
        $sign = Yrapilib::sign($sign_str);
        if ($sign != I('sign')) {
            djson(1, "签名错误")->send();
            exit;
        }
        if ($this->customer['status'] != 1) {
            djson(1, '账户不可用,请联系客服')->send();
            exit;
        }
        //ip白名单检测
        if ($this->customer['ip_white_list'] && strpos($this->customer['ip_white_list'], get_client_ip()) === false) {
            djson(1, '请将ip加入接口白名单')->send();
            exit;
        }
        //调用下级初始化函数
        if (method_exists($this, '_inithomechild')) {
            $this->_inithomechild();
        }
    }

}
