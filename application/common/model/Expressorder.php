<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2017-11-14
 * Time: 11:13
 */

namespace app\common\model;

use app\common\library\Createlog;
use app\common\library\Notification;
use app\common\library\PayWay;
use app\common\library\Wxrefundapi;
use Recharge\Qbd;
use think\Log;
use think\Model;

/**
 **/
class Expressorder extends Model
{
    const PR = 'SAN';

    protected $append = ['status_text', 'status_text2', 'create_time_text'];

    public static function init()
    {
        self::event('after_insert', function ($expressorder) {
            $order_number = self::PR . date('ymd', time()) . $expressorder->id;
            $expressorder->where(['id' => $expressorder->id])->update(['out_trade_num' => $order_number]);
        });
    }

    public function Customer()
    {
        return $this->belongsTo('Customer', 'customer_id');
    }

    public function getStatusTextAttr($value, $data)
    {
        return C('PORDER_STATUS')[$data['status']];
    }

    public function getStatusText2Attr($value, $data)
    {
        return C('ORDER_STUTAS')[$data['status']];
    }

    public function getCreateTimeTextAttr($value, $data)
    {
        return time_format($data['create_time']);
    }

    public function getRebateStatusText($is_rebate, $status)
    {
        if ($is_rebate) {
            return "已返利";
        } else {
            if (in_array($status, [2, 3])) {
                return "待返利";
            } elseif (in_array($status, [5, 6])) {
                return "失败不返";
            } elseif (in_array($status, [4])) {
                return "待返利";
            } else {
                return "未知";
            }
        }
    }

    /**
     * 预估价格
     * @param $weight //重量
     * @param $priceA //首重
     * @param $priceB //续重
     * @param $discount //折扣
     * @param $totalFeeOri //折扣原价
     * @return float|int //计算出来的总价
     */
    public static function culTotalPrice($weight, $data)
    {
        // 查询价格 根据价格计算 保价费 重量  续重 或者 总价x折扣
        $weight = $weight;
        $priceA = $data['priceA'];
        $priceB = $data['priceB'];
        $discount = $data['discount'];
        $totalFeeOri = $data['fee'];
        if ($priceA) {
            if ($priceB < 2.5) {
                $priceB = 2.5;
            } elseif ($priceB < 3) {
                $priceB = 3;
            } else {
                $priceB = $priceB + 0.5;
            }
            // 首重 加 续重 算法
            if ($priceA < 6.5) {
                $priceA = 6.5;
            } else if ($priceA < 7) {
                $priceA = 7;
            } else if ($priceA < 8) {
                $priceA = 8;
            } else {
                $priceA = ($priceA + 0.5);
            }
            $sum = $priceA + ($weight - 1) * $priceB;
        } else {
            if ($discount <= 5) {
                $discount = 5;
            } else if ($discount < 7) {
                $discount = 7;
            } else if ($discount < 8) {
                $discount = 8;
            } else {
                $discount = $discount + 0.5;
            }
            $sum = $totalFeeOri * $discount * 0.1;
        }
        $sum += $data['serviceCharge'];

        $pr = [
            'priceA' => $priceA,
            'priceB' => $priceB,
            'totalFeeOri' => $totalFeeOri,
            'discount' => $discount,
            'serviceCharge' => $data['serviceCharge'],
            'totalPrice' => $sum,
            'remark'     =>$data['remark']
        ];
        return $pr;
    }

    /**
     * @param $sender_name
     * @param $sender_phone
     * @param $senderCity
     * @param $sender_address
     * @param $receiveName
     * @param $receivePhone
     * @param $receiveCity
     * @param $receiveAddress
     * @param $deliveryType //产品类型 6:特快零担 25 特快重货type=3必填
     * @param $goods // 商品名字
     * @param $guaranteeValueAmount // 保价金额
     * @param $insuranceFee // 保价费
     * @param $order_send_time //预约时间
     * @param $remark //面单备注
     * @param $type // 快递类型
     * @param $senderText // 寄件文本
     * @param $receiveText // 收件文本
     * @param $priceRes // 价格查询结果
     * @return array|\think\response\Json
     */
    public static function createOrder($userid,$sender_name, $sender_phone,$senderProvince, $senderCity,$senderCounty,$senderTown, $sender_address, $receiveName, $receivePhone,
                                       $receiveProvince, $receiveCity,$receiveCounty, $receiveTown,  $receiveAddress, $deliveryType, $goods,$packageNum, $guaranteeValueAmount,
                                       $insuranceFee, $order_send_time, $remark, $type, $senderText, $receiveText, $weight, $out_trade_num, $priceRes)
    {

        $data['out_trade_num'] = $out_trade_num;
        $data['userid'] = $userid;
        $data['sender_name'] = $sender_name;
        $data['sender_phone'] = $sender_phone;
        $data['senderProvince'] = $senderProvince;
        $data['senderCity'] = $senderCity;
        $data['senderCounty'] = $senderCounty;
        $data['senderTown'] = $senderTown;
        $data['sender_address'] = $sender_address;
        $data['receiveName'] = $receiveName;
        $data['receivePhone'] = $receivePhone;
        $data['receiveProvince'] = $receiveProvince;
        $data['receiveCity'] = $receiveCity;
        $data['receiveCounty'] = $receiveCounty;
        $data['receiveTown'] = $receiveTown;
        $data['receiveAddress'] = $receiveAddress;
        $data['deliveryType'] = $deliveryType;
        $data['goods'] = $goods;
        $data['packageNum'] = $packageNum;
        $data['guaranteeValueAmount'] = $guaranteeValueAmount;
        $data['insuranceFee'] = $insuranceFee;
        $data['order_send_time'] = $order_send_time;
        $data['remark'] = $remark;
        $data['type'] = $type;
        $data['senderText'] = $senderText;
        $data['receiveText'] = $receiveText;
        $data['weight'] = $weight;
        $data['channelPriceA'] = $priceRes['priceA'];
        $data['channelPriceB'] = $priceRes['priceB'];
        $data['channelDiscount'] = $priceRes['discount'];;
        $data['fee'] = $priceRes['fee'];;
        $data['fee1'] = $priceRes['fee1'];;
        $data['serviceCharge'] = $priceRes['serviceCharge'];;
        $remark= '';
        if (isset($priceRes['remark1'])){
            $remark = $priceRes['remark1'];
        }else if (isset($priceRes['remark'])) {
            $remark = $priceRes['remark'];
        }
        $data['remark1'] = $remark; //计泡比等描述
        $data['channelToatlPrice'] = $priceRes['totalFee'];;
        $data['channelName'] = $priceRes['name'];;
        // 计算自己的价格
        $data['totalPrice'] = Expressorder::culTotalPrice($weight, $priceRes)['totalPrice'];
        $data['create_time'] = time();
        $model = new self();
        $model->save($data);
        if (!$aid = $model->id) {
            return rjson(1, '下单失败，请重试！');
        }
        return rjson(0, '下单成功', $model->id);
    }

    /**
     * @param $channel_order_id  渠道id
     * @param $type  快递类型
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public static function fetchRemoteOrder($channel_order_id,$type) {
        Log::error("aaaaaaa");
        if ($channel_order_id && $type) {
            $qbd = new Qbd();
            $channelOrderInfo= $qbd->checkOrder($channel_order_id,$type);
            // 返回的数组
            if ($channelOrderInfo && $channelOrderInfo['errno']==0) {
                $channelOrder= $channelOrderInfo['data']['order'];
                $order['trackingNum'] = $channelOrder['waybillNo'];
                $order['volume'] = $channelOrder['volume'];
                $order['volumeWeight'] = $channelOrder['volumeWeight'];
                $order['weightActual'] = $channelOrder['weightFinal'];// 实际重量
                $order['weightBill'] = $channelOrder['weightFee'];// 计费重量
                $order['guaranteeValueAmount'] = $channelOrder['insuredValue'];// 保价价格
                $order['insuranceFee'] = $channelOrder['insuredFee'];// 保价费
                $order['channelToatlPrice'] = $channelOrder['total'];// 渠道总价格
                $order['status'] = $channelOrder['status'];// 运单状态
                $order['statusName'] = $channelOrder['orderStatus'];// 运单状态
                $order['overWeightStatus'] = $channelOrder['overweightStatus'];// 1 超重 2 超重/耗材/保价/转寄/加长已处理  3 超轻
                $order['otherFee'] = $channelOrder['otherFee'];// 其它费用
                $order['consumeFee'] = $channelOrder['consumables'];// 耗材费用
                $order['serviceCharge'] = $channelOrder['serviceCharge'];// 服务费
                $order['soliciter'] = $channelOrder['soliciter'];// 揽件员
                self::where(['channel_order_id'=>$channel_order_id])->setField($order);
                $order['traceList'] = $channelOrderInfo['data']['traceList'];//轨迹
                return rjson(0,'拉取订单成功',$order);
            }else{
                return rjson(1,$channelOrderInfo['errmsg'],null);
            }
        }else {
            return rjson(1,'参数 有误',null);
        }
    }
    //生成支付数据
    public static function create_pay($aid, $payway, $client)
    {
        //预下单 待支付
        $order = self::where(['id' => $aid, 'status' => 0])->find();
        if (!$order) {
            return rjson(1, '订单无需支付' . $aid);
        }
        $customer = M('customer')->where(['id' => $order['userid']])->find();
        if (!$customer) {
            return rjson(1, '用户数据不存在');
        }
        return PayWay::create($payway, $client, [
            'openid' => $customer['wx_openid'] ? $customer['wx_openid'] : $customer['ap_openid'],
            'body' => '快递支付订单号:'.$order['out_trade_num'],
            'order_number' => $order['out_trade_num'],
            'total_price' => '0.01',
            'appid' => $customer['weixin_appid']
        ]);
    }

    public static function notify($order_number, $payway, $serial_number)
    {
        //预下单 待支付
        $porder = M('expressorder')->where(['out_trade_num' => $order_number, 'status' => -2])->find();
        Log::error("寻找快递单子".json_encode($porder));
        if (!$porder) {
            return rjson(1, '不存在订单');
        }
        Createlog::porderLog($porder['id'], "快递用户支付回调成功");
        //TODO 判断支付了多钱 比较
        //-2创建订单 -1 支付完成 0渠道预下单1待取件2运输中5已签收6取消订单7终止揽收
        M('expressorder')->where(['id' => $porder['id'], 'status' => 0])->setField(['status' => -1, 'pay_time' => time(), 'pay_way' => $payway,'statusName'=>'支付完毕']);
        Log::error("修改快递状态代取件");
//        //api下单队列放到队列去远程生单
        queue('app\queue\job\Work@createChannelExpressApi', $porder['id']);
        //发送支付成功通知
        Notification::payExpressSus($porder['id']);
        return rjson(0, '回调处理完成');
    }

    // 创建远程渠道生单
    public static function createChannelExpress($order_id) {
        Log::error("创建渠道生单".$order_id);
        // 此单已经支付完成
        $porder = M('expressorder')->where(['id' => $order_id, 'status' => 1])->find();
        if (!$porder) {
            return rjson(1, '订单非已支付状态无法提交远程下单');
        }
        //提交远程渠道下快递
        $qbd=new Qbd();
        $data = [
            "orderSendTime" => $porder['order_send_time'],
            "senderAddressCode"=>"",
            "senderText"=>$porder['senderText'],
            "receiveText"=>$porder['receiveText'],
            "id"=>0,
            "packageNum"=>$porder['packageNum'],
            "senderName"=>$porder['sender_name'],
            "senderCity"=>$porder['senderProvince'].$porder['senderCity'].$porder['senderCounty'].$porder['senderTown'],
            "senderAddress"=>$porder['senderProvince'].$porder['senderCity'].$porder['senderCounty'].$porder['senderTown'].$porder['sender_address'],
            "senderPhone"=>$porder['sender_phone'],
            "receiveName"=>$porder['receiveName'],
            "receiveAddress"=>$porder['receiveProvince'].$porder['receiveCity'].$porder['receiveCounty'].$porder['receiveTown'].$porder['receiveAddress'],
            "receiveCity"=>$porder['receiveProvince'].$porder['receiveCity'].$porder['receiveCounty'].$porder['receiveTown'],
            "receivePhone"=>$porder['receivePhone'],
            "weight"=>$porder['weight'],
            "goods"=>$porder['goods'],
            "insuredValue"=>$porder['insuranceFee'],
            "guaranteeValueAmount"=>$porder['guaranteeValueAmount'],
            "remark"=>$porder['remark'],
            "channelName"=>$porder['channelName'],
            "priceA"=>$porder['channelPriceA'],
            "priceB"=>$porder['channelPriceB'],
            "discount"=>$porder['channelDiscount'],
            "fee"=>$porder['fee'],
            "isThird"=>true,
            "isInsured"=>true,
            "fee1"=>$porder['fee1'],
            "remark1"=>$porder['remark1'],
            "type"=>$porder['type'],
            "sadd"=>$porder['sender_address'],
        ];
        Log::error("渠道生单发起请求".$order_id);
        $res=$qbd->createOrder($data);
        Log::error("渠道生单结果".json_encode($res));
        //远程生单结果完成 讲 id  和渠道 id 放到redis中
        if ($res['errno'] == 0){
            if ($res['data']) {
             $resOrder = $res['data'];
                self::where(['id'=>$order_id])->setField(['channel_order_id'=>$resOrder['id'],'volume'=>$resOrder['volume'],'volumeWeight'=>$resOrder['volumeWeight'],'status'=> 0,'statusName'=>'渠道预下单']);
                $confirmRes=$qbd->checkOrder($resOrder['id'],$porder['type']);
                return  rjson(1, $confirmRes['errmsg']);
            }

        }else {
            return  rjson(1, $res['errmsg']);
        }
        return rjson(0, '提交接口工作完成');
    }

    // 取消订单
    public static function cancelOrder($order_id) {
        $porder = M('expressorder')->where(['id' => $order_id])->find();
        if (!$porder) {
            return rjson(1, '找不到订单');
        }
        if (!$porder['channel_order_id']){
            return rjson(1, '找不到渠道订单');
        }
       $qbd= new Qbd();
       return $qbd->cancelOrder($porder['channel_order_id'],$porder['type']);
    }

    //充值成功api
    public static function rechargeSusApi($api, $api_order_number, $data)
    {
        $flag = self::apinotify_log($api, $api_order_number, $data);
        if (!$flag) {
            return rjson(1, '接口已回调过了');
        }
        $porder = M('porder')->where(['api_order_number' => $api_order_number, 'status' => ['in', '2,3']])->find();
        if (!$porder) {
            return rjson(1, '订单未找到');
        }
        return self::rechargeSus($porder['order_number'], "充值成功|接口回调|" . var_export($data, true));
    }

    //充值成功
    public static function rechargeSus($order_number, $remark)
    {
        $porder = M('porder')->where(['order_number' => $order_number, 'status' => ['in', '2,3']])->find();
        if (!$porder) {
            return rjson(1, '订单未找到');
        }
        M('porder')->where(['id' => $porder['id']])->setField(['status' => 4, 'finish_time' => time()]);
        Createlog::porderLog($porder['id'], $remark);
        queue('app\queue\job\Work@callFunc', ['class' => '\app\common\library\Notification', 'func' => 'rechargeSus', 'param' => $porder['id']]);
        self::rebate($porder['id']);
        return rjson(0, '操作成功');
    }

    //充值失败api
    public static function rechargeFailApi($api, $api_order_number, $data)
    {
        $flag = self::apinotify_log($api, $api_order_number, $data);
        if (!$flag) {
            return rjson(1, '接口已回调过了');
        }
        $porder = M('porder')->where(['api_order_number' => $api_order_number, 'status' => ['in', '2,3']])->find();
        if (!$porder) {
            return rjson(1, '订单未找到');
        }
        return self::rechargeFail($porder['order_number'], "充值失败|接口回调|" . var_export($data, true));
    }

    /**
     * 充值失败
     */
    public static function rechargeFail($order_number, $remark)
    {
        $porder = M('porder')->where(['order_number' => $order_number, 'status' => ['in', '2,3']])->find();
        if (!$porder) {
            return rjson(1, '订单未找到');
        }
        //最后有一次api失败时间
        M('porder')->where(['id' => $porder['id']])->setField(['apifail_time' => time()]);

        if ($porder['api_open'] == 1) {
            //继续下一个接口
            $res = Porder::getCurApi($porder['id']);
            if ($res['errno'] == 0) {
                Createlog::porderLog($porder['id'], $remark);
                M('porder')->where(['id' => $porder['id']])->setField(['status' => 2]);
                queue('app\queue\job\Work@porderSubApi', $porder['id']);
                return rjson(0, '处理成功');
            }
        }
        if (C('ODAPI_FAIL_STYLE') == 2) {
            //回到充值中状态
            Createlog::porderLog($porder['id'], $remark);
            M('porder')->where(['id' => $porder['id']])->setField(['status' => 2]);
            Createlog::porderLog($porder['id'], "api失败,订单回到待充值状态");
            return rjson(0, '处理成功');
        }
        //直接订单失败
        return self::rechargeFailDo($order_number, $remark);
    }

    /**
     * 充值失败
     */
    public static function rechargeFailDo($order_number, $remark)
    {
        $porder = M('porder')->where(['order_number' => $order_number, 'status' => ['in', '2,3']])->find();
        if (!$porder) {
            return rjson(1, '订单未找到');
        }
        Createlog::porderLog($porder['id'], $remark);
        M('porder')->where(['id' => $porder['id']])->setField(['status' => 5, 'finish_time' => time()]);
        queue('app\queue\job\Work@callFunc', ['class' => '\app\common\library\Notification', 'func' => 'rechargeFail', 'param' => $porder['id']]);
        C('AUTO_REFUND') == 1 && queue('app\queue\job\Work@porderRefund', ['id' => $porder['id'], 'remark' => $remark, 'operator' => '系统']);
        return rjson(0, '操作成功');
    }

    public static function getApiOrderNumber($order_number, $api_cur_index = 0, $api_cur_count = 0, $num = 1)
    {
        return $order_number . 'A' . $api_cur_count . ($api_cur_index + 1) . 'N' . $num;
    }

    //退款
    public static function refund($order_id, $remark, $operator)
    {
        $porder = M('porder')->where(['id' => $order_id, 'status' => 5])->find();
        if (!$porder) {
            return rjson(1, '未查询到退款订单');
        }
        switch ($porder['pay_way']) {
            case 1://公众号微信支付-退款
                $ret = Wxrefundapi::porder_wxpay_refund($porder['id']);
                break;
            case 2://余额
                $ret = Balance::revenue($porder['customer_id'], $porder['total_price'], "订单:" . $porder['order_number'] . "充值失败退款", Balance::STYLE_REFUND, $operator);
                break;
            case 3://小程序微信支付
                $ret = Wxrefundapi::porder_wxpay_refund($porder['id']);
                break;
            case 4://线下支付
                $ret = rjson(0, '线下支付无需退款');
                break;
            default:
                $ret = rjson(1, '不支持');
        }
        if ($ret['errno'] != 0) {
            Createlog::porderLog($porder['id'], "退款失败|" . $remark);
            return rjson(1, $ret['errmsg']);
        }
        M('porder')->where(['id' => $porder['id']])->setField(['status' => 6, 'refund_time' => time()]);
        Createlog::porderLog($porder['id'], "退款成功|" . $remark);
        Notification::refundSus($porder['id']);
        return rjson(0, "退款成功");
    }

    /**
     * 代理返利计算
     * 下单时进行返利计算
     */
    public static function compute_rebate($porder_id)
    {
        $porder = M('porder')->where(['id' => $porder_id, 'status' => ['in', '1,2'], 'is_del' => 0])->find();
        if (!$porder) {
            return rjson(1, '未找到订单');
        }
        $customer = M('customer')->where(['id' => $porder['customer_id'], 'is_del' => 0, 'status' => 1])->find();
        if (!$customer) {
            return rjson(1, '用户未找到');
        }
        //自身等级价格
        $rebate_id = $customer['f_id'];
        if (!$rebate_id) {
            Createlog::porderLog($porder_id, '不返利,没有上级');
            return rjson(1, '无上级，无需返利');
        }
        //查上级
        $fcus = M('customer')->where(['id' => $customer['f_id'], 'is_del' => 0, 'status' => 1])->find();
        if (!$fcus || $fcus['grade_id'] == $customer['grade_id']) {
            Createlog::porderLog($porder_id, '同级用户，无需给上级返利');
            return rjson(1, '等级无差异无需返利');
        }
        if (in_array($customer['grade_id'], [1, 3]) && M('customer_grade')->where(['is_zdy_price' => 1, 'id' => $fcus['grade_id']])->find()) {
            //如果上级是自定义价格
            $rebate_price = M('customer_hezuo_price')->where(['product_id' => $porder['product_id'], 'customer_id' => $fcus['id']])->value('ranges');
        } else {
            //非自定义价格
            $price_f = M('customer_grade_price')->where(['product_id' => $porder['product_id'], 'grade_id' => $fcus['grade_id']])->find();
            $price_m = M('customer_grade_price')->where(['product_id' => $porder['product_id'], 'grade_id' => $customer['grade_id']])->find();
            $rebate_price = floatval($price_m['ranges'] - $price_f['ranges']);
        }
        if ($rebate_price <= 0) {
            Createlog::porderLog($porder_id, '不返利,计算出金额：' . $rebate_price);
            return rjson(1, '不返利,计算出金额：' . $rebate_price);
        }
        M('porder')->where(['id' => $porder_id])->setField(['rebate_id' => $rebate_id, 'rebate_price' => $rebate_price]);
        Createlog::porderLog($porder_id, '计算返利ID：' . $rebate_id . '，返利金额:￥' . $rebate_price);
        return rjson(0, '返利设置成功');
    }


    /**
     * 返利
     * 代理用户和普通用户
     */
    public static function rebate($porder_id)
    {
        $porder = M('porder')->where(['id' => $porder_id, 'status' => ['in', '4'], 'rebate_id' => ['gt', 0], 'rebate_price' => ['gt', 0], 'is_del' => 0, 'is_rebate' => 0])->find();
        if ($porder) {
            M('porder')->where(['id' => $porder_id])->setField(['is_rebate' => 1, 'rebate_time' => time()]);
            Balance::revenue($porder['rebate_id'], $porder['rebate_price'], '用户充值返利，单号' . $porder['order_number'], Balance::STYLE_REWARDS, '系统');
        }
    }


    //存储日志,并检查是否可以执行回调操作
    public static function apinotify_log($api, $out_trade_no, $data)
    {
        if (!$out_trade_no) {
            return false;
        }
        $log = M('apinotify_log')->where(['api' => $api, 'out_trade_no' => $out_trade_no])->find();
        M('apinotify_log')->insertGetId([
            'api' => $api,
            'out_trade_no' => $out_trade_no,
            'data' => var_export($data, true),
            'create_time' => time()
        ]);
        if ($log) {
            return false;
        } else {
            return true;
        }
    }
}
