<?php

namespace app\yrapi\controller;


class Open extends \app\common\controller\Base
{
    function _commonbase()
    {

    }

    //凭证
    public function voucher()
    {
        $porder = M('porder o')
            ->join('product p', 'p.id=o.product_id')
            ->where(['o.id' => I('id'), 'o.type' => ['in', [1, 2], 'o.status' => 4]])
            ->field('o.*,p.voucher_price,p.voucher_name')->find();
        if (!$porder || $porder['status'] != 4 || !in_array($porder['type'], [1, 2])) {
            echo "未获得凭证";
            exit();
        }
        $map['status'] = 1;
        $map['isp'] = ispstrtoint($porder['isp']);
        $voucher = M('voucher_config')->where($map)->find();
        if (!$voucher) {
            echo "订单没有凭证";
            exit();
        }
        $txtdata = [];
        if ($voucher['is_no']) {
            $txtdata[] = [
                'type' => 'txt',
                'left' => $voucher['no_left'],
                'top' => $voucher['no_top'],
                'size' => $voucher['no_size'],
                'color' => $voucher['no_color'],
                'text' => $porder['order_number']
            ];
        }
        if ($voucher['is_mobile']) {
            $txtdata[] = [
                'type' => 'txt',
                'left' => $voucher['mobile_left'],
                'top' => $voucher['mobile_top'],
                'size' => $voucher['mobile_size'],
                'color' => $voucher['mobile_color'],
                'text' => $porder['mobile']
            ];
        }
        if ($voucher['is_date']) {
            $txtdata[] = [
                'type' => 'txt',
                'left' => $voucher['date_left'],
                'top' => $voucher['date_top'],
                'size' => $voucher['date_size'],
                'color' => $voucher['date_color'],
                'text' => time_format($porder['finish_time'])
            ];
        }
        if ($voucher['is_price']) {
            $txtdata[] = [
                'type' => 'txt',
                'left' => $voucher['price_left'],
                'top' => $voucher['price_top'],
                'size' => $voucher['price_size'],
                'color' => $voucher['price_color'],
                'text' => $porder['voucher_price'] ? $porder['voucher_price'] : $porder['total_price']
            ];
        }
        if ($voucher['is_product']) {
            $txtdata[] = [
                'type' => 'txt',
                'left' => $voucher['product_left'],
                'top' => $voucher['product_top'],
                'size' => $voucher['product_size'],
                'color' => $voucher['product_color'],
                'text' => $porder['voucher_name'] ? $porder['voucher_name'] : $porder['product_name']
            ];
        }
        $this->assign('txtdata', json_encode($txtdata));
        $this->assign('bgpath', $voucher['path']);
        return view();
    }


}
