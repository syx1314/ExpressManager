<?php

namespace app\api\controller;

use app\common\model\Balance;
use app\common\model\Client;
use app\common\model\Customer as CustomerModel;
use app\common\model\OrderUpgrade;

class Customer extends Home
{
    public function info()
    {
        $customer = CustomerModel::getInfo($this->customer['id']);
        if (in_array($customer['grade_id'], [2])) {
            CustomerModel::checkShareImg($this->customer['id']);
        }
        $customer['is_agent'] = $this->customer['grade_id'] > 0 ? 1 : 0;
        $customer['chongzhi_money'] = M('porder')->where(['customer_id' => $this->customer['id'], 'status' => ['egt', 2]])->sum('total_price');
        $customer['chongzhi_num'] = M('porder')->where(['customer_id' => $this->customer['id'], 'status' => ['egt', 2]])->count();
        $customer['renshu'] = M('customer')->where(['f_id' => $this->customer['id'], 'is_del' => 0])->count();
        $customer['earnings'] = M('balance_log')->where(['customer_id' => $this->customer['id'], 'style' => 2])->sum('money');
        $customer['tixian_sum'] = M('tixian')->where(['customer_id' => $this->customer['id']])->sum('money');
        return djson(0, 'ok', $customer);
    }

    /**
     * 保存用户信息
     */
    public function saveinfo()
    {
        $model = CustomerModel::get($this->customer['id']);
        if (I('?username')) {
            $arr['username'] = I('username');
        }
        if (I('?headimg')) {
            $arr['headimg'] = I('headimg');
        }
        if (I('?sex')) {
            $arr['sex'] = I('sex');
        }
        if (I('?is_mp_auth')) {
            $arr['is_mp_auth'] = I('is_mp_auth');
        }
        $model->save($arr);
        return djson(0, '保存成功', $arr);
    }


    public function tixian()
    {
        $money = I('money');
        $acount = I('acount');
        $name = I('name');
        $style = I('style');
        //保存账号
        $data = [
            'money' => '',
            'acount' => $acount,
            'name' => $name,
            'style' => $style
        ];
        M('customer')->where(['id' => $this->customer['id']])->setField(['tixian_data' => json_encode($data)]);

        if (round(C('TIXIAN_MIN'), 2) > $money) {
            return djson(1, '至少提现' . C('TIXIAN_MIN') . '元哦');
        }
        if (round(C('TIXIAN_MAX'), 2) < $money) {
            return djson(1, '一次最多提现' . C('TIXIAN_MAX') . '元哦');
        }
        $cus = M('customer')->where(['id' => $this->customer['id']])->find();
        if ($cus['balance'] >= $money) {
            $ret = Balance::expend($this->customer['id'], $money, '用户提现', Balance::STYLE_WITHDRAW, '代理商_api');
            if ($ret['errno'] != 0) {
                return djson($ret['errno'], $ret['errmsg']);
            }
            $balance = M('customer')->where(['id' => $this->customer['id']])->value('balance');
            M('tixian')->insertGetId([
                'order_number' => 'TX' . time() . rand(100, 999),
                'acount' => $acount,
                'name' => $name,
                'style' => $style,
                'money' => $money,
                'status' => 1,
                'create_time' => time(),
                'customer_id' => $this->customer['id'],
                'balance' => $balance
            ]);
            return djson(0, '提现申请成功！');
        } else {
            return djson(1, '余额不足');
        }
    }

    public function tixian_log()
    {
        $map['customer_id'] = $this->customer['id'];
        $lists = M('tixian')
            ->where($map)
            ->order("create_time desc")->paginate(20);
        return djson(0, 'ok', $lists);
    }

    public function balance_log()
    {
        $map['customer_id'] = $this->customer['id'];
        if (I('type')) {
            $map['type'] = I('type');
        }
        if (I('is_yongjin')) {
            $map['style'] = 2;
        }
        $lists = M('balance_log')
            ->where($map)
            ->order("create_time desc")->paginate(20);
        return djson(0, 'ok', $lists);
    }

    public function poster()
    {
        CustomerModel::checkShareImg($this->customer['id']);
        $lists = M('poster_config')->where(['status' => 1])->select();
        $domain = HTTP_TYPE . $_SERVER['SERVER_NAME'] . '/';
        foreach ($lists as &$item) {
            $item['path'] = $domain . $item['path'];
        }
        return djson(0, 'ok', $lists);
    }


    public function poster_config()
    {
        $domain = HTTP_TYPE . $_SERVER['SERVER_NAME'] . '/';
        $config = M('poster_config')->where(['status' => 1, 'id' => I('id')])->find();
        $customer = D('customer')->where(["id" => $this->customer['id']])->find();
        if (!$customer) {
            return djson(1, '用户信息未找到');
        }
        if ($this->client == Client::CLIENT_WX && !$customer['qrurl']) {
            return djson(1, '微信二维码还未生成,请联系客服');
        }
        if ($this->client == Client::CLIENT_MP && !$customer['mp_qrurl']) {
            return djson(1, '小程序二维码还未生成,请联系客服');
        }
        $data = [
            'bgimg' => $domain . $config['path'],
            'ctx' => [[
                'type' => 'img',
                'left' => $config['qr_left'],
                'top' => $config['qr_top'],
                'size' => $config['qr_width'],
                'src' => $this->client == Client::CLIENT_MP ? $domain . $customer['mp_qrurl'] : $domain . $customer['qrurl']
            ]]
        ];
        if ($config['is_hd']) {
            $data['ctx'][] = [
                'type' => 'img',
                'left' => $config['hd_left'],
                'top' => $config['hd_top'],
                'size' => $config['hd_width'],
                'src' => "data:image/png;base64," . $customer['headimg_base64']
            ];
        }
        if ($config['is_date']) {
            $data['ctx'][] = [
                'type' => 'text',
                'left' => $config['date_left'],
                'top' => $config['date_top'],
                'fontsize' => $config['date_size'],
                'fontcolor' => $config['date_color'],
                'text' => '有效期：' . date("Y-m-d", $customer['share_img_time'])
            ];

        }
        if ($config['is_id']) {
            $data['ctx'][] = [
                'type' => 'text',
                'left' => $config['id_left'],
                'top' => $config['id_top'],
                'fontsize' => $config['id_size'],
                'fontcolor' => $config['id_color'],
                'text' => "ID:" . $customer['id']
            ];
        }
        if ($config['is_nick']) {
            $data['ctx'][] = [
                'type' => 'text',
                'left' => $config['nick_left'],
                'top' => $config['nick_top'],
                'fontsize' => $config['nick_size'],
                'fontcolor' => $config['nick_color'],
                'text' => $customer['username']
            ];
        }
        return djson(0, 'ok', $data);
    }

    //判断是否代理
    public function is_agent()
    {
        if ($this->customer['grade_id'] > 1) {
            return djson(0, '是');
        } else {
            return djson(1, '否');
        }
    }

    /**
     * 判定是否关注
     */
    public function is_subscribe()
    {
        if (!C('SHOW_SUBSCRIBE_QR')) {
            return djson(1, '已关注');
        }
        $this->weixin = new \Util\Wechat($this->wxconfig);
        $res = $this->weixin->getUserInfo($this->customer['wx_openid']);
        if ($res['errno'] != 0) {
            return djson($res['errno'], $res['errmsg'], $res['data']);
        }
        $info = $res['data'];
        M('customer')->where(["id" => $this->customer['id']])->setField(['is_subscribe' => $info['subscribe']]);
        if (intval($info['subscribe']) == 0) {
            return djson(0, '未关注', C('WEIXIN_QR'));
        } else {
            return djson(1, '已关注');
        }
    }

    public function get_grade_info()
    {
        return rjson(0, 'ok', M('customer_grade')->where(['id' => I('grade_id')])->find());
    }

    //下单
    public function create_agent_order()
    {
        $ret = OrderUpgrade::createOrder(I('grade_id'), $this->customer['id']);
        if ($ret['errno'] != 0) {
            return djson($ret['errno'], $ret['errmsg'], $ret['data']);
        }
        $order = $ret['data'];
        if ($order['total_price'] > 0) {
            $paydata = OrderUpgrade::create_pay($order['id'], 1, $this->client);
            if ($paydata['errno'] != 0) {
                return djson($paydata['errno'], $paydata['errmsg'], $paydata['data']);
            }
            return djson(0, "ok", ['payinfo' => $paydata['data'], 'id' => $order['id']]);
        } else {
            $notify = new Notify();
            $notify->balance($order['order_number']);
            return djson(2, "升级成功");
        }
    }

}
