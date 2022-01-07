<?php

namespace app\api\logic;

/**
 * Created by PhpStorm.
 * User: liaoqiang
 * Date: 2018/11/12
 * Time: 17:40
 * 该类和我们的controller类名和方法名是一一对应的
 */

use think\Logic;

class Porder extends Logic
{

    public function create_order()
    {
        $this->allowMethods = 'post';
        $this->rules = [
            'mobile' => 'require',
            'product_id' => 'require|number'
        ];
        $this->message = [
            'mobile' => '手机格式不正确',
            'product_id' => '产品ID错误'
        ];
    }

    public function topay()
    {
        $this->allowMethods = 'post';
        $this->rules = [
            'order_id' => 'require|number',
            'paytype' => 'require|number|in:1,2'
        ];
        $this->message = [
            'order_id' => '订单编号错误',
            'paytype' => '支付方式错误'
        ];
    }

    public function orderinfo()
    {
        $this->allowMethods = 'post';
        $this->rules = [
            'id' => 'require|number'
        ];
        $this->message = [
            'id' => 'ID错误'
        ];
    }

}