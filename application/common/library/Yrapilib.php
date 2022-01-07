<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2018-03-23
 * Time: 21:42
 */

namespace app\common\library;


class Yrapilib
{
    /**
     * 签名
     */
    public static function sign($signstr)
    {
        return strtoupper(md5($signstr));
    }


}