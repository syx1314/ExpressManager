<?php

namespace app\agent\logic;

/**
 * Created by PhpStorm.
 * User: liaoqiang
 * Date: 2018/11/12
 * Time: 17:40
 */
use think\Logic;

class Porder extends Logic
{
    /**
     * allowMethods  get|post
     * rules  array
     * message array
     */

    public function get_product()
    {
        $this->allowMethods = 'post';
        $this->rules = [
            'mobile' => 'number'
        ];
        $this->message = [
            'mobile' => '手机号不正确'
        ];
    }

    public function create_order()
    {
        $this->allowMethods = 'post';
        $this->rules = [
            'mobile' => 'require',
            'product_id' => 'require|number'
        ];
        $this->message = [
            'mobile' => '充值账号必填',
            'product_id' => '产品选择不正确'
        ];
    }
}