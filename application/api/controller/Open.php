<?php

namespace app\api\controller;

use app\common\library\PayApi;
use app\common\library\Userlogin;
use app\common\model\Customer as CustomerModel;

/**
 * Class Open
 * 开放控制器，登录注册等操作
 */
class Open extends Base
{
    /**
     * 密码登录
     */
    public function pwdlogin()
    {
        $username = I('username');
        $password = I('password');
        $verifycode = I('verifycode');
        $res = CustomerModel::pwdLogin($username, $password, $verifycode);
        if ($res['errno'] != 0) {
            return djson(1, $res['errmsg'], $res['data']);
        }
        $customer = $res['data'];
        $data = Userlogin::create_login_data($customer['id']);
        return djson($data['errno'], $data['errmsg'], $data['data']);
    }


    /**
     * H5注册
     */
    public function h5reg()
    {
        $username = I('username');
        $password = I('password');
        if (S('piccode' . md5($username)) != strtolower(I('imgcode'))) {
            return djson(1, "图片验证码错误");
        }
        $customer = M('customer')->where(['username' => $username, 'is_del' => 0])->find();
        if ($customer) {
            return djson(1, "账号已注册，请登录");
        }
        $res = Userlogin::h5_user_reg($username, $password, I('vi'));
        if ($res['errno'] != 0) {
            return djson(1, $res['errmsg']);
        }
        $inid = $res['data']['id'];
        $data = Userlogin::create_login_data($inid);
        return djson($data['errno'], $data['errmsg'], $data['data']);
    }


    /**
     * 广告位单个
     */
    public function get_ad()
    {
        $key = I('key');
        if ($data = M('ad a')->join("adc c", "a.id=c.ad_id")->where(['a.key' => $key])->order("c.sort asc")->find()) {
            return djson(0, 'ok', $data);
        } else {
            return djson(1, '没有');
        }

    }

    /**
     * 广告位多行
     */
    public function get_ads()
    {
        $key = I('key');
        if ($data = M('ad a')->join("dyr_adc c", "a.id=c.ad_id")->where(['a.key' => $key])->order("c.sort asc")->select()) {
            return djson(0, 'ok', $data);
        } else {
            return djson(1, '没有');
        }
    }

    /**
     * 文档详情页面
     */
    public function get_doc()
    {
        $id = I('id');
        M('archives')->where(['id' => $id])->setInc('click', 1);
        $detail = M('archives')->where(['id' => $id])->find();
        if (!$detail) {
            return djson(1, '失败');
        }
        $channel = M('channeltype')->where(['id' => $detail['channel']])->find();
        $addtable = M()->table($channel['addtable'])->where(['aid' => $detail['id']])->find();
        if ($addtable) {
            $detail = array_merge($detail, $addtable);
        }
        return djson(0, 'ok', $detail);
    }

    /**
     * 帮助
     */
    public function helptxt()
    {
        $map = [];
        $list = M('help_txt')->where($map)->order('sort asc')->select();
        return djson(0, 'ok', $list);
    }

    //宣传语
    public function taglinetxt()
    {
        $map = [];
        $list = M('tagline_txt')->where($map)->order('sort asc')->select();
        return djson(0, 'ok', $list);
    }

    //调试登录
    public function devlogin()
    {
        $data = Userlogin::create_login_data(1);
        return djson($data['errno'], $data['errmsg'], $data['data']);
    }

    //获取配置
    public function get_config()
    {
        return djson(0, 'ok', C(I('key')));
    }


    //跳转支付宝支付
    public function alipay_h5()
    {
        $param = I('param');
        $option = json_decode(dyr_decrypt($param), true);
        $ret = PayApi::create_alipay_h5([
            'total_price' => $option['total_price'],
            'order_number' => $option['order_number'],
            'body' => $option['body']
        ]);
        echo $ret['data'];
        exit();
    }

    public function get_city()
    {
        $initials = M('electricity_city')->group('initial asc')->field('initial')->select();
        $arr = [];
        foreach ($initials as $ini) {
            $list = M('electricity_city')->where(['initial' => $ini['initial']])->order('sort asc')->select();
            $arr[] = ['letter' => $ini['initial'], 'list' => $list];
        }
        return djson(0, 'ok', $arr);
    }

    public function test()
    {
        $config = M('reapi')->where(['id' => 7])->find();
        $param = M('reapi_param')->where(['id' => 12])->find();
        $classname = 'Recharge\\' . $config['callapi'];
        $model = new $classname($config);
        return $model->recharge(time(), '11122223333', $param, '电信');
    }


}
