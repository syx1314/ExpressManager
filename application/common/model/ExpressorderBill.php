<?php

namespace app\common\model;

use app\common\enum\ExpressOrderPayStatusEnum;
use think\Log;
use think\Model;

class ExpressorderBill extends Model
{
    const PR = 'BIL';
    // 创建快递账单
    public static function createBill($userid,$order_number,$type,$total_price) {
        $data = [
            'bill_no'=>self::PR.time().$order_number,
            'userid'=>$userid,
            'order_number'=>$order_number,
            'type'=>$type,
            'pay_status'=>ExpressOrderPayStatusEnum::NO_PAY,// 待支付
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

    // 更新 账单异常   支付的 提示异常
    public static function updateBillError($userid,$bill_no,$total_price) {
        $data = [
            'error_price'=>$total_price,
            'error_msg'=>'账单重新生成',
            'update_time'=>time(),
        ];
        $res= self::update($data,['bill_no'=>$bill_no,'userid'=>$userid]);
        if ($res) {
           return rjson(0, '异常账单更新成功！');
        }
        return rjson(1, '异常账单更新失败', $res);
    }

    // 更新 账单  没有价格异常的  针对于 没有支付的
    public static function updateBill($userid,$bill_no,$total_price) {
        $data = [
            'total_price'=>$total_price,
            'update_time'=>time(),
        ];
        $res= self::update($data,['bill_no'=>$bill_no,'userid'=>$userid]);
        if ($res) {
            return rjson(0, '账单更新成功！');
        }
        return rjson(1, '账单更新失败', $res);
    }
}
