<?php

namespace app\common\model;

use think\Log;
use think\Model;

class ExpressorderBill extends Model
{
    const PR = 'BILL';
    // 创建快递账单
    public static function createBill($userid,$order_number,$type,$total_price) {
        $data = [
            'bill_no'=>self::PR.time().$order_number,
            'userid'=>$userid,
            'order_number'=>$order_number,
            'type'=>$type,
            'pay_status'=>1,// 待支付
            'total_price'=>$total_price,
            'create_time'=>time(),
        ];
        $bill = new self();
        $bill->save($data);
        Log::error("创建订单".json_encode($data));
        if (!$aid = $bill->id) {
            return rjson(1, '创建账单失败，请重试！');
        }
     return rjson(0, '创建账单成功', $bill->id);
    }
}
