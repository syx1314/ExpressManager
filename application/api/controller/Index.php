<?php

namespace app\api\controller;


use app\common\model\Product;
use Map\Bmap;
use Recharge\Yuanren;

class Index extends Home
{
    /**
     * 获取默认显示的套餐
     */
    public function get_product()
    {
        $map['p.is_del'] = 0;
        $map['p.added'] = 1;
        $map['p.type'] = I('type') ? I('type') : 1;
        if (I('isp')) {
            $map['p.isp'] = ['like', '%' . I('isp') . '%'];
        }
        $data = Product::getProducts($map, $this->customer['id']);
        return djson(0, 'ok', $data);
    }

    /**
     * 根据手机号获取可充值的套餐
     */
    public function get_product_mobile()
    {
        $mobile = I('mobile');
        $guishu = QCellCore($mobile);
        if ($guishu['errno'] != 0) {
            return djson($guishu['errno'], $guishu['errmsg']);
        }
        $map['p.added'] = 1;
        $map['p.is_del'] = 0;
        //手机
        $map['p.isp'] = ['like', '%' . $guishu['data']['isp'] . '%'];
        $map['p.type'] = I('type') ? I('type') : 1;
        $data['lists'] = Product::getProducts($map, $this->customer['id']);
        $data['guishu'] = $guishu['data'];
        return djson(0, 'ok', $data);
    }


    //ip定位
    public function get_client_city()
    {
        $bmap = new Bmap();
        $res = $bmap->location_ip(get_client_ip());
        if ($res['status'] == 0 && isset($res['address'])) {
            $arr = explode('|', $res['address']);
            return djson(0, 'ok', ['pro' => $arr[1], 'city' => $arr[2]]);
        } else {
            return djson(1, '未获取到定位');
        }
    }

}
