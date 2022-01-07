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

class Applet extends Logic
{

    public function login()
    {
        $this->allowMethods = 'post';
        $this->rules = [
            'code' => 'require',
            'vi' => 'number'
        ];
        $this->message = [
            'code' => 'code错误',
            'vi' => 'vi错误'
        ];
    }

    public function applet_reg()
    {
        $this->allowMethods = 'post';
        $this->rules = [
            'code' => 'require',
            'iv' => 'require',
            'vi' => 'number'
        ];
        $this->message = [
            'code' => 'code错误',
            'iv' => 'iv错误',
            'vi' => 'vi错误'
        ];
    }

}