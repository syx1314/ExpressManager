<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2017-11-14
 * Time: 11:13
 */

namespace app\common\model;

use app\common\enum\ExpressOrderEnum;
use app\common\library\Createlog;
use app\common\library\Notification;
use app\common\library\PayWay;
use app\common\library\RedisPackage;
use app\common\library\WxExpressrefundapi;
use app\common\library\Wxrefundapi;
use app\common\model\Expressorder as ExorderModel;
use Recharge\Qbd;
use think\Log;
use think\Model;

/**
 **/
class Expressorder extends Model
{
    const PR = 'SAN';



    public static function init()
    {
//        self::event('before_insert', function ($expressorder) {
//            $order_number = self::PR . date('ymd', time()) . $expressorder->id;
//            $expressorder->where(['id' => $expressorder->id])->update(['out_trade_num' => $order_number]);
//        });
    }

    public function Customer()
    {
        return $this->belongsTo('Customer', 'customer_id');
    }

    public function getStatusTextAttr($value, $data)
    {
        return '我擦';
//        return C('PORDER_STATUS')[$data['status']];
    }

//    public function getStatusText2Attr($value, $data)
//    {
//        return C('ORDER_STUTAS')[$data['status']];
//    }

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
        if ($priceA>0) {
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
            'totalPrice' => floor($sum*100)/100,
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
        $order_number = self::PR . date('ymd', time()) . time();
        $data['out_trade_num'] = $order_number;
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
        $data['channelTotalFeeOri'] = $priceRes['totalFeeOri'];;
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
        $pr = Expressorder::culTotalPrice($weight, $priceRes);
        $data['priceA'] = $pr['priceA'];
        $data['priceB'] = $pr['priceB'];
        $data['discount'] = $pr['discount'];
        $data['totalPrice'] = $pr['totalPrice'];
        $data['create_time'] = time();
        $model = new self();
        $model->save($data);
        if (!$aid = $model->id) {
            Createlog::expressOrderLog($data['out_trade_num'],"下单失败，请重试！");
            return rjson(1, '下单失败，请重试！');
        }
        // 创建支付账单
        $bill=ExpressorderBill::createBill($data['userid'], $data['out_trade_num'],1,$data['totalPrice']);
        if ($bill['errno'] == 0) {
            Createlog::expressOrderLog($data['out_trade_num'],"下单成功 | 创建支付账单成功");
            return rjson(0, '下单成功',$bill['data']);
        }
        Createlog::expressOrderLog($data['out_trade_num'],"下单成功 | 创建支付账单失败");
        return  $bill;
    }


    //拉取远程订单 by 订单id
    public static function fetchRemoteOrderById ($id) {
        $expressorder = M('expressorder')->where(['id'=>$id])->find();
        if ($expressorder&& $expressorder['channel_order_id'] && $expressorder['type']) {
            $res=self::fetchRemoteOrder($expressorder['channel_order_id'],$expressorder['type']);
            if ($res['errno'] == 0){
                // 加入任务队列 生成超重等账单
                $order =$res['data'];
                //如果 redis 的订单状态
                $expressOrderList = RedisPackage::get('expressOrderList');
                if ($expressOrderList) {
                    $expressOrderListArr = json_decode($expressOrderList, true);
                    for ($i = 0; $i < count($expressOrderListArr); $i++) {
                        if ($id == $expressOrderListArr[$i]['order_id']) {
                            // 拿到状态
                            $expressOrderListArr[$i]['order_status'] = $order['status'];
                        }
                    }
                    RedisPackage::set('expressOrderList', json_encode($expressOrderListArr));
                }
                Log::error('fetchRemoteOrderById  任务完毕' .$id.'----'.$order['status']);
                // 暂时不创建账单了
//                if ($order['overWeightStatus'] == 1) {
//                    queue('app\queue\job\Work@createOtherFeeBill', $id);
//                }
                return rjson(0,'拉取订单成功',null);
            }else{
                return rjson(0,'拉取远程单子失败',$res['errmsg']);
            }
        }else{
            return rjson(0,'找不到渠道单子');
        }
    }
    /**
     * @param $channel_order_id  渠道id
     * @param $type  快递类型
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public static function fetchRemoteOrder($channel_order_id,$type) {
        if ($channel_order_id && $type) {
            $qbd = new Qbd();
            $channelOrderInfo= $qbd->checkOrder($channel_order_id,$type);
            // 返回的数组
            if ($channelOrderInfo && $channelOrderInfo['errno']==0) {
                $channelOrder= $channelOrderInfo['data']['data'];
                $order['trackingNum'] = $channelOrder['trackingNum'];
                $order['volume'] = $channelOrder['volume'];
                $order['volumeWeight'] = $channelOrder['volumeWeight'];
                $order['weightActual'] = $channelOrder['weightActual'];// 实际重量
                $order['weightBill'] = $channelOrder['weightBill'];// 计费重量
                // $order['guaranteeValueAmount'] = $channelOrder['insuredValue'];// 保价价格
                $order['insuranceFee'] = $channelOrder['insuranceFee'];// 保价费
                $order['channelToatlPrice'] = $channelOrder['payFee'];// 渠道总价格
                $order['status'] = $channelOrder['status'];// 运单状态
                $order['statusName'] = $channelOrder['statusName'];// 运单状态
                $order['overWeightStatus'] = $channelOrder['overWeightStatus'];// 1 超重 2 超重/耗材/保价/转寄/加长已处理  3 超轻
                $order['otherFee'] = $channelOrder['otherFee'];// 其它费用
                $order['consumeFee'] = $channelOrder['consumeFee'];// 耗材费用
                $order['serviceCharge'] = $channelOrder['serviceCharge'];// 服务费
                $order['soliciter'] = $channelOrder['soliciter'];// 揽件员
                $order['update_time'] = time();// 更新时间
                self::where(['channel_order_id'=>$channel_order_id])->setField($order);
                $order['traceList'] = $channelOrder['traceList'];//轨迹

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
        // 查询账单  待支付
        $bill = M('expressorder_bill')->where(['id' => $aid, 'pay_status' => 1])->find();
        if (!$bill) {
            return rjson(1, '账单无需支付' . $aid);
        }
        $customer = M('customer')->where(['id' => $bill['userid']])->find();
        if (!$customer) {
            return rjson(1, '用户数据不存在');
        }
        $body= '';
        if ($bill['type']==1) {
            Createlog::expressOrderLog($bill['order_number'],"发起账单支付--快递费");
            $body = '为订单号'.$bill['order_number'].'支付快递费'.$bill['total_price'].'元';
        }else if ($bill['type']==2) {
            Createlog::expressOrderLog($bill['order_number'],"发起账单支付--超重转寄等费用");
            $body = '为订单号'.$bill['order_number'].'支付超重转寄等费用'.$bill['total_price'].'元';
        }
        return PayWay::create($payway, $client, [
            'openid' => $customer['wx_openid'] ? $customer['wx_openid'] : $customer['ap_openid'],
            'body' => $body,
            'order_number' => $bill['bill_no'], // 用账单id
            'total_price' => '0.01',
            'appid' => $customer['weixin_appid']
        ]);
    }

    // 判断是否超重 生成 超重 等其它费用账单
    public static function createOtherFeeBill($orderid) {

        $exOrder = self::where(['id'=>$orderid, 'overWeightStatus'=> 1])->find();
        $bill =M('expressorder_bill')->where(['order_number' =>$exOrder['out_trade_num'],'type'=> 2])->find();
        if ($bill) {
            return djson(0,'超重账单已经生成完毕',null);
        }
        if ($exOrder) {
            $sumPrice = 0;
            // 超重
            $weight = $exOrder['weightBill'] - $exOrder['weight'];
            if ($exOrder['discount'] >0 ) {
                $sumPrice += $exOrder['']*$exOrder['discount'];
            }
            // 保价
            if ($exOrder['insuredFee'] >0 ){
                $sumPrice+=$exOrder['insuredFee'];
            }
            // 耗材费
            if ($exOrder['consumeFee']) {
                $sumPrice+=$exOrder['consumeFee'];
            }
            // 其它费
            if ($exOrder['otherFee']) {
                $sumPrice+=$exOrder['otherFee'];
            }
            if ($sumPrice>0) {
                // 创建账单
                Createlog::expressOrderLog($exOrder['out_trade_num'],"创建账单--超重转寄等费用");
                ExpressorderBill::createBill($exOrder['userid'],$exOrder['out_trade_num'],2,$sumPrice);
                return djson(0,'创建账单成功',null);
            }
        }else {
            return djson(0,'没有需要额外收费订单',null);
        }
    }


    public static function notify($bill_no, $payway, $serial_number,$money)
    {
        // 查账单id
        $bill = M('expressorder_bill')->where(['bill_no' => $bill_no, 'pay_status' => 1])->find();
        Log::error('找到账单支付');
        if (!$bill) {
            return rjson(1, '账单不存在 或者已经支付过');
        }

        if ($bill['type'] ==1) {
            //支付快递费
            $porder = M('expressorder')->where(['out_trade_num' =>$bill['order_number'], 'status' => ExpressOrderEnum::CREATE])->find();
            Log::error("寻找快递单子".json_encode($porder));
            if (!$porder) {
                return rjson(1, '不存在订单');
            }
            Createlog::expressOrderLog($bill['order_number'],"快递用户支付回调成功");
            //TODO 判断支付了多钱 比较
            //-2创建订单 -1 支付完成 0渠道预下单1待取件2运输中5已签收6取消订单7终止揽收
            M('expressorder')->where(['id' => $porder['id'], 'status' => ExpressOrderEnum::CREATE])->setField(['status' => ExpressOrderEnum::PAY_COMPLETE, 'pay_time' => time(), 'pay_way' => $payway,'statusName'=>'支付完毕']);
            Log::error("修改快递状态代取件");
//         //api下单队列放到队列去远程生单
            queue('app\queue\job\Work@createChannelExpressApi', $porder['id']);
            Createlog::expressOrderLog($bill['order_number'],"开启队列 生成远程快递单子");

        }
        $bill_id = $bill['id'];
        M('expressorder_bill')->where(['id' => $bill_id])->setField(['pay_status'=> 2 , 'pay_money' => $money,'transaction_id' => $serial_number]);
        //发送支付成功通知
        Notification::payExpressSus($bill_id);
        return rjson(0, '回调处理完成');
    }

    //订单是否支付完成
    public static function  findPayCompleteStatus($order_id) {
        // 此单已经支付完成  //-2创建订单 -1 支付完成 0渠道预下单1待取件2运输中5已签收6取消订单7终止揽收
        return  M('expressorder')->where(['id' => $order_id, 'status' => ExpressOrderEnum::PAY_COMPLETE])->find();
    }
    // 创建远程渠道生单
    public static function createChannelExpress($order_id) {
        Log::error("创建渠道生单".$order_id);
        // 此单已经支付完成  //-2创建订单 -1 支付完成 0渠道预下单1待取件2运输中5已签收6取消订单7终止揽收
        $porder = self::findPayCompleteStatus($order_id);
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
        $res=$qbd->createAppOrder($data);
        Log::error("渠道生单结果".json_encode($res));
        //远程生单结果完成 讲 id  和渠道 id 放到redis中
        if ($res['errno'] == 0){
            Createlog::expressOrderLog($porder['out_trade_num'],"远程快递单生成成功");
            if ($res['data']) {
                $resOrder = $res['data'];
                self::where(['id'=>$order_id])->setField(['channel_order_id'=>$resOrder['id'],'status'=> ExpressOrderEnum::DAI_QU_JIAN]);
                if ($resOrder['id'] ) {
                    // 加入到定时任务队列 拉取订单信息
                    //放到redis理
                    Createlog::expressOrderLog($porder['out_trade_num'],"加入到定时任务队列 拉取订单信息");
                    $redisData1=[
                        'order_id'=>$order_id,
                        'order_status' => ExpressOrderEnum::DAI_QU_JIAN
                    ];
                    $expressOrderList =  RedisPackage::get('expressOrderList');
                    if ($expressOrderList) {
                        $expressOrderListArr = json_decode($expressOrderList,true);
                        array_push($expressOrderListArr,$redisData1);
                        RedisPackage::set('expressOrderList',json_encode($expressOrderListArr));
                    }else {
                        $expressOrderListArr = [];
                        array_push($expressOrderListArr,$redisData1);
                        RedisPackage::set('expressOrderList',json_encode($expressOrderListArr));
                    }
                }
                return  rjson(1, $resOrder['errmsg']);
            }

        }else {
            Createlog::expressOrderLog($porder['out_trade_num'],"远程生单失败原因:".$res['errmsg']);
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

            // 加入到任务队列退款
            self::where(['id'=>$order_id])->setField(['status'=>6]);
            return rjson(1, '找不到渠道订单,本地订单取消完毕');
        }
        $qbd= new Qbd();
        $res = $qbd->cancelOrder($porder['channel_order_id'],$porder['type']);
        if ($res['errno'] == 0) {
            // 取消成功
            return rjson(0, '取消成功订单');
        }else {
            return rjson(1, $res['data']);
        }
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
    //退款
    public static function refund($order_id, $remark, $operator)
    {
        //-2创建订单 -1 支付完成 0渠道预下单1待取件2运输中5已签收6取消订单7终止揽收
        // 没有到达运输中
        // 渠道查询运单状态
        $porder = M('expressorder')->where(['id' => $order_id])->find();
        if (!$porder) {
            return rjson(1, '未查询到退款订单');
        }
        //创建订单 直接取消订单
        if ($porder['status'] == ExpressOrderEnum::CREATE){
            //直接退款
            self::where(['id'=>$order_id])->setField(['status'=>ExpressOrderEnum::CANCEL_ORDER]);
            Createlog::porderLog($porder['id'], "退款成功|" . $remark);
            Notification::refundSus($porder['id']);
            return rjson(0, "订单无需退款 取消成功");
        }else if ($porder['status'] == ExpressOrderEnum::CANCEL_ORDER){
            return rjson(0, "订单无需退款");
        }else if ($porder['status'] == ExpressOrderEnum::DAI_QU_JIAN) {
            // 待取件  可以请求远程去 取消订单
            $qbd= new Qbd();
            //取消远程单子
            if ($porder['channel_order_id']) {
                $res=$qbd->cancelOrder($porder['channel_order_id'],$porder['type']);
                if ($res['errno']!=0) {
                    return rjson(1, "退款失败".$res['errmsg']);
                }
            }
        }

        switch ($porder['pay_way']) {
            case 1://公众号微信支付-退款
                $ret = WxExpressrefundapi::porder_wxpay_refund($porder['id']);
                break;
            case 2://余额
                $ret = Balance::revenue($porder['userid'], $porder['totalPrice'], "订单:" . $porder['out_trade_num'] . "充值失败退款", Balance::STYLE_REFUND, $operator);
                break;
            case 3://小程序微信支付
                $ret = WxExpressrefundapi::porder_wxpay_refund($porder['id']);
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
