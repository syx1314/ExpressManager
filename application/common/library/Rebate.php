<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2018-03-23
 * Time: 21:42
 */

namespace app\common\library;


use app\common\enum\BalanceStyle;

class Rebate
{
    /**
     * 代理返利计算
     * 下单时进行返利计算
     */
    public static function porder_create_rebate($porder_id)
    {
        $porder = M('porder')->where(['id' => $porder_id, 'status' => ['in', '1,2'], 'is_del' => 0])->find();
        if (!$porder) {
            return djson(1, '未找到订单');
        }
        $customer = M('customer')->where(['id' => $porder['customer_id'], 'is_del' => 0, 'status' => 1])->find();
        if (!$customer) {
            return djson(1, '用户未找到');
        }
        //自身等级价格
        $rebate_id = $customer['f_id'];
        if (!$rebate_id) {
            Createlog::porderLog($porder_id, '不返利,没有上级');
            return djson(1, '无上级，无需返利');
        }
        //查上级
        $fcus = M('customer')->where(['id' => $customer['f_id'], 'is_del' => 0, 'status' => 1])->find();
        if (!$fcus || $fcus['grade_id'] < $customer['grade_id']) {
            Createlog::porderLog($porder_id, '已经升级，无需给上级返利');
            return djson(1, '等级差异无需返利');
        }
        //上级等级价格
        $pricea = M('customer_grade_price')->where(['product_id' => $porder['product_id'], 'grade_id' => $fcus['grade_id']])->find();
        $rebate_price = floatval($pricea['rebate']);
        if ($rebate_price <= 0) {
            Createlog::porderLog($porder_id, '不返利,计算出金额：' . $pricea['rebate']);
            return djson(1, '不返利,计算出金额：' . $pricea['rebate']);
        }
        M('porder')->where(['id' => $porder_id])->setField(['rebate_id' => $rebate_id, 'rebate_price' => $rebate_price]);
        Createlog::porderLog($porder_id, '计算返利ID：' . $rebate_id . '，返利金额:￥' . $rebate_price);
        return rjson(0, '返利设置成功');
    }


    /**
     * 返利
     * 代理用户和普通用户
     */
    public static function porder_rebate($porder_id)
    {
        $porder = M('porder')->where(['id' => $porder_id, 'status' => ['in', '4'], 'rebate_id' => ['gt', 0], 'rebate_price' => ['gt', 0], 'is_del' => 0, 'is_rebate' => 0])->find();
        if ($porder) {
            M('porder')->where(['id' => $porder_id])->setField(['is_rebate' => 1]);
            D('Balance')->revenue($porder['rebate_id'], $porder['rebate_price'], '用户充值返利，单号' . $porder['order_number'], BalanceStyle::REWARDS);
        }
    }

    //送积分
    public static function add_integral($porder_id)
    {
        $porder = M('porder')->where(['id' => $porder_id, 'status' => ['in', '4'], 'is_del' => 0])->find();
        return M('customer')
            ->where(['id' => $porder['customer_id'], 'is_del' => 0, 'status' => 1])
            ->setInc("integral", intval($porder['total_price']));
    }

}