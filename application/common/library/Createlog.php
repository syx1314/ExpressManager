<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2018-03-23
 * Time: 21:42
 */

namespace app\common\library;


class Createlog
{

    /**
     *生成充值订单日志
     */

    public static function porderLog($porderid, $log)
    {
        M('porder_log')->insertGetId([
            'porder_id' => $porderid,
            'log' => $log,
            'create_time' => time()
        ]);
    }

    /**
     *生成用户日志
     */
    public static function customerLog($customer_id, $log, $operator)
    {
        M('customer_log')->insertGetId([
            'customer_id' => $customer_id,
            'log' => $log,
            'operator' => $operator,
            'create_time' => time()
        ]);
    }


}