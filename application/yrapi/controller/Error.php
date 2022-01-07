<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2017-11-13
 * Time: 11:25
 */

namespace app\yrapi\controller;


class Error extends Base
{
    public function index()
    {
        return djson(1, '不存在的api!');
    }
}