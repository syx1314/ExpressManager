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

class Weixin extends Logic
{

    public function getOauthAccessToken()
    {
        $this->allowMethods = 'post';
        $this->rules = [
            'vi' => 'number'
        ];
        $this->message = [
            'vi' => 'vi错误'
        ];
    }

    public function create_js_config()
    {
        $this->allowMethods = 'post';
        $this->rules = [
            'url' => 'url'
        ];
        $this->message = [
            'url' => 'url错误'
        ];
    }

}