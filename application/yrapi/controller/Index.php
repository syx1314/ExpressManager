<?php

namespace app\yrapi\controller;


use app\common\model\Client;
use app\common\model\Porder as PorderModel;
use app\common\model\Product as ProductModel;


class Index extends Home
{

    public function index()
    {
        return djson(1, '欢迎访问');
    }

    /**
     * 下单
     */
    public function recharge()
    {
        $mobile = I('mobile');
        $product_id = I('product_id');
        $out_trade_num = I('out_trade_num');
        $notify_url = I('notify_url');
        $area = I('area');

        if ($reod = M('porder')->where(['out_trade_num' => $out_trade_num, 'status' => ['gt', 1], 'customer_id' => $this->customer['id']])->find()) {
            return djson(1, '已经存在相同商户订单号的订单');
        }
        $res = PorderModel::createOrder($mobile, $product_id, $area, $this->customer['id'], Client::CLIENT_API, 'api下单', $out_trade_num);
        if ($res['errno'] != 0) {
            return djson($res['errno'], $res['errmsg'], $res['data']);
        }
        $aid = $res['data'];
        queue('app\queue\job\Work@agentApiPayPorder', ['porder_id' => $aid, 'customer_id' => $this->customer['id'], 'notify_url' => $notify_url]);
        $porder = M('porder')->where(['id' => $aid])->field("id,order_number,mobile,product_id,total_price,create_time,guishu,title,out_trade_num")->find();
        return djson(0, "提交成功", $porder);
    }

    //账号信息
    public function user()
    {
        return djson(0, "ok", [
            'id' => $this->customer['id'],
            'username' => $this->customer['username'],
            'balance' => $this->customer['balance']
        ]);
    }

    //检查订单状态
    public function check()
    {
        $out_trade_nums = I('out_trade_nums');
        $porder = M('porder')->where([
            'out_trade_num' => ['in', $out_trade_nums],
            'customer_id' => $this->customer['id'],
            'is_del' => 0
        ])->field("order_number,status,out_trade_num,create_time,mobile,product_id")->select();
        foreach ($porder as &$vo) {
            if ($vo['status'] == 4) {
                $state = 1;
            } elseif ($vo['status'] == 2 || $vo['status'] == 3) {
                $state = 0;
            } else {
                $state = 2;
            }
            $vo['state'] = $state;
        }
        return djson(0, 'ok', $porder);
    }

    //获取产品
    public function product()
    {
        $map['p.is_del'] = 0;
        $map['p.added'] = 1;
        $lists = ProductModel::getProducts($map, $this->customer['id']);
        return djson(0, 'ok', $lists);
    }

}
