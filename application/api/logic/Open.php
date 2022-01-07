<?php

namespace app\api\logic;

/**
 */

use think\Logic;

class Open extends Logic
{

    public function pwdlogin()
    {
        $this->allowMethods = 'post';
        $this->rules = [
            'username' => 'require',
            'password' => 'require',
            'imgcode' => 'require'
        ];
        $this->message = [
            'username' => '用户名必填',
            'password' => '密码错误必填',
            'imgcode' => '图片验证码必填'
        ];
    }

    public function h5reg()
    {
        $this->allowMethods = 'post';
        $this->rules = [
            'username' => 'require',
            'password' => 'require',
            'imgcode' => 'require'
        ];
        $this->message = [
            'username' => '用户名必填',
            'password' => '密码错误必填',
            'imgcode' => '图片验证码必填'
        ];
    }

    public function get_ad()
    {
        $this->allowMethods = 'post';
        $this->rules = [
            'key' => 'require'
        ];
        $this->message = [
            'key' => 'key必填'
        ];
    }

    public function get_ads()
    {
        $this->allowMethods = 'post';
        $this->rules = [
            'key' => 'require'
        ];
        $this->message = [
            'key' => 'key必填'
        ];
    }

    public function get_doc()
    {
        $this->allowMethods = 'post';
        $this->rules = [
            'id' => 'require|number'
        ];
        $this->message = [
            'id' => 'id错误'
        ];
    }

    public function get_config()
    {
        $this->allowMethods = 'post';
        $this->rules = [
            'key' => 'require'
        ];
        $this->message = [
            'key' => 'key必填'
        ];
    }
}