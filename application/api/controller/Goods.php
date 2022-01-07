<?php

namespace app\api\controller;

class Goods extends Base
{
    //分类
    public function category()
    {
        $lists = M('goods_category')
            ->where(['is_del' => 0, 'status' => 1])
            ->order('sort asc')
            ->field("id,name,sort")->select();
        return djson(0, '', $lists);
    }

    //商品列表
    public function goods_list()
    {
        if (I('category')) {
            $map['g.category_id'] = I('category');
        }
        if (I('mode')) {
            $map['g.mode'] = I('mode');
        } else {
            $map['g.mode'] = 1;
        }
        $map['c.status'] = 1;
        $map['g.status'] = 1;
        $map['g.is_del'] = 0;
        $map['c.is_del'] = 0;
        $lists = M('goods g')
            ->join('dyr_goods_category c', 'c.id = g.category_id')
            ->where($map)
            ->field('g.*,c.name as cname')
            ->paginate(20);
        return djson(0, '', $lists);
    }

    //商品详情
    public function get_goods_info()
    {
        $map['g.id'] = I('id');
        $map['c.status'] = 1;
        $map['g.status'] = 1;
        $map['g.is_del'] = 0;
        $map['c.is_del'] = 0;
        $goods = M('goods g')
            ->join('dyr_goods_category c', 'c.id = g.category_id')
            ->where($map)
            ->field('g.*,c.name as cname')
            ->find();

        if (!$goods) {
            return djson(1, '商品找不到了');
        }
        if ($goods['banner_pic']) {
            $goods['banner_pic'] = unserialize($goods['banner_pic']);
        }
        if ($goods['details_pic']) {
            $goods['details_pic'] = unserialize($goods['details_pic']);
        }
        $goods['spec'] = M('goods_spec')->where(['goods_id' => $goods['id']])->select();
        return djson(0, 'ok', $goods);
    }


    //活动商品详情
    public function get_huodong_goods_info()
    {
        $huodong = M('huodong_config')->find();
        $map['g.id'] = $goods_id;
        $map['c.status'] = 1;
        $map['g.status'] = 1;
        $map['g.is_del'] = 0;
        $map['c.is_del'] = 0;
        $goods = M('goods g')
            ->join('dyr_goods_category c', 'c.id = g.category_id')
            ->where($map)
            ->field('g.*,c.name as cname')
            ->find();
        if (!$goods) {
            return djson(1, '商品找不到了');
        }
        if ($goods['banner_pic']) {
            $goods['banner_pic'] = unserialize($goods['banner_pic']);
        }
        if ($goods['details_pic']) {
            $goods['details_pic'] = unserialize($goods['details_pic']);
        }
        $goods['spec'] = M('goods_spec')->where(['goods_id' => $goods['id']])->select();
        return djson(0, 'ok', $goods);
    }

    //赠送商品指定规格数据
    public function huodong_goods_spec()
    {
        $map['s.id'] = I('spec_id');
        $map['g.id'] = I('id');
        $map['g.mode'] = 3;
        $goods = M('goods g')
            ->join('dyr_goods_spec s ', 'g.id=s.goods_id')
            ->where($map)
            ->field("g.name,g.postage,g.mode,s.spec,s.pic,s.now_price")
            ->find();
        if (!$goods) {
            return djson(1, '未找到商品信息');
        }
        $total_price = $goods['postage'];
        return djson(0, 'ok', ['info' => $goods, 'total_price' => sprintf("%.2f", $total_price)]);
    }


}
