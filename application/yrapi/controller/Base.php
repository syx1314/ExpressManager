<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2017-11-13
 * Time: 17:30
 */

namespace app\yrapi\controller;


/**
 
 **/
class Base extends \app\common\controller\Base
{
    /**
     * 初始化
     */
    public function _commonbase()
    {
        //你的初始化
        $host_name = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : "*";
        header("Access-Control-Allow-Origin: $host_name");
        header("Access-Control-Allow-Credentials:true");
        header("Access-Control-Max-Age:120");
        header("Access-Control-Allow-Methods: PUT,POST,GET,DELETE,OPTIONS");
        header("Access-Control-Allow-Headers: Origin,X-Requested-With,Content-Type,Accept,Authorization,Content-Language");

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit;
        }

        if (C('API_SWITCH') == 0) {
            djson(1, '供应商接口已经关闭', '')->send();
            exit;
        }
        //调用二级初始化函数
        if (method_exists($this, '_dayuanren')) {
            $this->_dayuanren();
        }
    }

    public function _empty()
    {
        return djson(1, '不存在的api！');
    }
}