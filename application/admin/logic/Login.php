<?php

namespace app\admin\logic;

/**
 * Created by PhpStorm.
 * User: liaoqiang
 * Date: 2018/11/12
 * Time: 17:40
 */
use think\Logic;

class Login extends Logic
{
    /**
     * allowMethods  get|post
     * rules  array
     * message array
     */

    public function logindo()
    {
        $this->allowMethods = 'post';
        $this->rules = [
            'verifycode' => 'require',
            'nickname' => 'require',
            'password' => 'require'
        ];
        $this->message = [
            'verifycode' => '名称必须',
            'nickname' => '用户名必须',
            'password' => '密码必须',
        ];
    }
}