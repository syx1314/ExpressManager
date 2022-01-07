<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2017-11-13
 * Time: 11:25
 */

namespace app\api\controller;

/**
 
 **/
class Error extends Base
{
    /**
     
     * 找不到控制器
     */
    public function index()
    {
        return djson(1, '出错了！');
    }

}