<?php

namespace app\api\controller;

use app\common\model\CustomerHezuoPrice;
use app\common\model\Porder as PorderModel;
use app\common\model\Product as ProductModel;

class Agent extends Home
{

    public function _inithomechild()
    {

    }


    //邀请记录
    public function get_invite_log()
    {
        $lists = M('customer')
            ->where(['f_id' => $this->customer['id'], 'is_del' => 0])
            ->field("id,username,headimg,create_time")
            ->order("create_time desc")
            ->paginate(20);
        return djson(0, 'ok', $lists);
    }

    //推广链接
    public function tg_links()
    {
        return djson(0, 'ok', [C('WEB_URL') . '#/?vi=' . $this->customer['id']]);
    }

    //返利订单
    public function rebate_order()
    {
        $map['rebate_id'] = $this->customer['id'];
        $map['status'] = ['gt', 1];
        if (I('key')) {
            $map['customer_id|order_number'] = I('key');
        }
        if (I('is_rebate')) {
            $map['is_rebate'] = 1;
        }
        if (I('end_time') && I('begin_time')) {
            $map['create_time'] = array('between', [strtotime(I('begin_time')), strtotime(I('end_time')) + 86400]);
        }
        $data['lists'] = PorderModel::where($map)
            ->field("order_number,type,customer_id,status,mobile,total_price,product_name,rebate_price,is_rebate,create_time,title,rebate_time")
            ->order("create_time desc")
            ->paginate(20)->each(function ($item) {
                $item['rebate_status_text'] = $item->getRebateStatusText($item->is_rebate, $item->status);
            });
        $data['total_price'] = M('porder')->where($map)->sum('total_price');
        $data['rebate_price'] = M('porder')->where($map)->sum('rebate_price');
        $data['counts'] = M('porder')->where($map)->count();
        return djson(0, 'ok', $data);
    }

    //合作商价格表
    public function hzPriceList()
    {
        CustomerHezuoPrice::initPrice();
        $customer = M('customer')->where(['id' => $this->customer['id']])->find();
        $map['p.is_del'] = 0;
        $map['p.added'] = 1;
        $key = trim(I('key'));
        if ($key) {
            if (I('query_name')) {
                $map[I('query_name')] = $key;
            } else {
                $map['p.name|p.desc'] = ['like', '%' . $key . '%'];
            }
        }
        if (I('type')) {
            $map['p.type'] = I('type');
        }
        if (I('cate_id')) {
            $map['p.cate_id'] = I('cate_id');
        }
        if (I('product_id')) {
            $map['p.id'] = I('product_id');
        }
        $cates = ProductModel::getProducts($map, $customer['id']);
        foreach ($cates as &$cate) {
            foreach ($cate['products'] as &$item) {
                $hzprice = M('customer_hezuo_price')->where(['customer_id' => $customer['id'], 'product_id' => $item['id']])->field('id as rangesid,ranges,ys_tag')->find();
                if (!$hzprice) {
                    continue;
                }
                $item['ys_tag'] = $hzprice['ys_tag'];
                $item['rangesid'] = $hzprice['rangesid'];
                $item['ranges'] = $hzprice['ranges'];
            }
        }
        return djson(0, '', $cates);
    }

    //编辑合作商价格
    public function upHzPrice()
    {
        $price = M('customer_hezuo_price')->where(['id' => I('id'), 'customer_id' => $this->customer['id']])->field('id,product_id,customer_id')->find();
        if (!$price) {
            return djson(1, '未找到设置');
        }
        $customer = M('customer')->where(['id' => $this->customer['id']])->find();
        $map['p.is_del'] = 0;
        $map['p.id'] = $price['product_id'];
        $product = ProductModel::getProduct($map, $customer['id']);

        $ranges = floatval(I('ranges'));
        if ($ranges < 0) {
            return djson(1, '浮动金额不能小于0');
        }
        if (floatval($product['max_price']) > 0 && ($product['price'] + $ranges) > $product['max_price']) {
            return djson(1, '不能设置高于封顶价格');
        }
        M('customer_hezuo_price')->where(['id' => $price['id']])->setField(['ranges' => $ranges]);
        return djson(0, '保存成功');
    }


    //编辑合作商tag
    public function uphz_ystag()
    {
        $data = M('customer_hezuo_price')->where(['id' => I('id'), 'customer_id' => $this->customer['id']])->setField(['ys_tag' => I('ys_tag')]);
        if ($data) {
            return djson(0, '保存成功');
        } else {
            return djson(1, '编辑失败(可能未修改数据)');
        }
    }

    public function get_tixian_styles()
    {
        $arr = [['id' => '0', 'name' => '请选择']];
        $types = C('TIXIAN_STYLE');
        foreach ($types as $k => $item) {
            $arr[] = ['id' => $k, 'name' => $item];
        }
        $tx_d_str = M('customer')->where(['id' => $this->customer['id']])->value('tixian_data');
        if ($tx_d_str && $data = json_decode($tx_d_str, true)) {

        } else {
            $data = [
                'money' => '',
                'acount' => '',
                'name' => '',
                'style' => 0
            ];
        }
        $active = 0;
        foreach ($arr as $key => &$item) {
            if ($item['id'] == $data['style']) {
                $active = $key;
                continue;
            }
        }
        return rjson(0, 'ok', ['txdata' => $data, 'styles' => $arr, 'styles_index' => $active]);
    }
}
