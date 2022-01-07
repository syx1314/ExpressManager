<?php

namespace app\yrapi\logic;

/**
 * 该类和我们的controller类名和方法名是一一对应的
 */

use think\Logic;

class Index extends Logic
{

    public function index()
    {
        $this->allowMethods = 'post';
        $this->rules = [
            'userid' => 'require|number',
            'sign' => 'require|length:32'
        ];
    }

    public function recharge()
    {
        $this->allowMethods = 'post';
        $this->rules = [
            'userid' => 'require|number',
            'mobile' => 'require',
            'product_id' => 'require|number',
            'out_trade_num' => 'require',
            'sign' => 'require|length:32',
            'notify_url' => 'require|url',
        ];
    }

    public function user()
    {
        $this->allowMethods = 'post';
        $this->rules = [
            'userid' => 'require|number',
            'sign' => 'require|length:32'
        ];
    }

    public function check()
    {
        $this->allowMethods = 'post';
        $this->rules = [
            'userid' => 'require|number',
            'out_trade_nums' => 'require',
            'sign' => 'require|length:32'
        ];
    }

    public function product()
    {
        $this->allowMethods = 'post';
        $this->rules = [
            'userid' => 'require|number',
            'sign' => 'require|length:32'
        ];
    }
}