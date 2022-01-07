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

class Customer extends Logic
{

    public function tixian()
    {
        $this->allowMethods = 'post';
        $this->rules = [
            'money' => 'require|number|gt:0'
        ];
        $this->message = [
            'money' => '提现金额不正确'
        ];
    }
}