<?php

namespace app\api\controller;

use app\common\library\Createlog;
use app\common\model\Client;
use app\common\model\Porder as PorderModel;

class Porder extends Home
{
    /**
     * 下单
     */
    public function create_order()
    {
        $mobile = I('mobile');
        $product_id = I('product_id');
        $area = I('area');
        $res = PorderModel::createOrder($mobile, $product_id, $area, $this->customer['id'], $this->client, '下单');
        if ($res['errno'] != 0) {
            return djson($res['errno'], $res['errmsg'], $res['data']);
        }
        $aid = $res['data'];
        PorderModel::compute_rebate($aid);
        Createlog::porderLog($aid, "用户下单成功");
        return djson(0, "ok", ['id' => $aid]);
    }

    //创建支付
    public function topay()
    {
        $res = PorderModel::create_pay(I('order_id'), I('paytype'), $this->client);
        return djson($res['errno'], $res['errmsg'], $res['data']);
    }

    /**
     * 充值记录
     */
    public function order_list()
    {
        $map = ['customer_id' => $this->customer['id'], 'is_del' => 0, 'status' => ['gt', 1]];
        if (I('type')) {
            $map['type'] = I('type');
        }
        if (I('key')) {
            $map['order_number|mobile'] = I('key');
        }
        $lists = PorderModel::where($map)->order("create_time desc")->paginate(10);
        if ($lists) {
            return djson(0, "ok", $lists);
        } else {
            return djson(1, "暂时还没有充值记录");
        }
    }

    /**
     * 充值记录详情
     */
    public function orderinfo()
    {
        $info = M('porder')->where(['customer_id' => $this->customer['id'], 'is_del' => 0, 'id' => I('id')])->find();
        if ($info) {
            return djson(0, "ok", $info);
        } else {
            return djson(1, "暂时没有记录信息");
        }
    }


}
