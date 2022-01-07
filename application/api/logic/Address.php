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

class Address extends Logic
{

    public function save_info()
    {
        $this->allowMethods = 'post';
        $this->rules = [
            'name' => 'require|max:25|min:2',
            'mobile' => 'require|number|length:11',
            'city' => 'require',
            'province' => 'require',
            'county' => 'require',
            'street' => 'require',
            'isdefault' => 'require|in:0,1',
            'type' => 'require'
        ];
        $this->message = [
            'name' => '收件人错误',
            'mobile' => '手机号错误',
            'city' => '城市信息错误',
            'province' => '省信息错误',
            'county' => '区县信息错误',
            'street' => '详细地址信息错误',
            'isdefault' => '是否默认信息错误',
            'type' => '地址类型错误'
        ];
    }

    public function del_address()
    {
        $this->allowMethods = 'post';
        $this->rules = [
            'id' => 'require|number',
        ];
        $this->message = [
            'id' => 'ID格式错误',
        ];
    }


}