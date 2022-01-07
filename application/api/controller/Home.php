<?php

namespace app\api\controller;

use app\common\library\Userlogin;

/**
 * Class Home
 * @package app\api\controller
 * 需要在登录状态获取数据的控制器请继承此控制器
 */
class Home extends Base
{
    /**
     
     * 验证登录状态
     */
    public function _initbasechild()
    {
        if (!array_key_exists('Authorization', $this->header)) {
            djson(-1, "请登录")->send();
            exit;
        }
        $token = $this->header['Authorization'];
        $data = Userlogin::get_userinfo_by_token($token);
        if ($data['errno'] != 0) {
            djson($data['errno'], $data['errmsg'], $data['data'])->send();
            exit;
        }
        $this->customer = $data['data'];
        //调用下级初始化函数
        if (method_exists($this, '_inithomechild')) {
            $this->_inithomechild();
        }
    }
}
