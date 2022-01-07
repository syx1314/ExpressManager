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

class Agent extends Logic
{
    /**
     * allowMethods  get|post
     */
    public function apply()
    {
        $this->allowMethods = 'post';
        $this->rules = [
            'name' => 'require|chs|max:25|min:2',
            'weixin' => 'require|alphaDash',
            'content' => 'require'
        ];
        $this->message = [
            'name' => '姓名格式填写错误',
            'weixin' => '微信格式填写错误',
            'content' => '介绍填写错误',
        ];
    }


}