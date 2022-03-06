<?php

namespace app\api\controller;

use app\common\library\Createlog;
use app\common\model\Client;
use app\common\model\Expressorder as ExpressorderModel;
use Recharge\Qbd;
use think\Log;

class Expressorder extends Home
{
    /**
     * 下单
     */
    public function create_order()
    {
        Log::error("创建订单".file_get_contents("php://input"));
        $res=json_decode(file_get_contents("php://input"),true);
        $orderSendTime= $res['orderSendTime'];
        $userid= $res['userid'];
        $senderText= $res['senderText'];
        $receiveText= $res['receiveText'];
        $senderName= $res['senderName'];
        $senderProvince=$res['senderProvince'];
        $senderCity=$res['senderCity'];
        $senderCounty=$res['senderCounty'];
        $senderTown=$res['senderTown'];
        $senderAddress= $res['senderAddress'];
        $senderPhone= $res['senderPhone'];
        $receiveName= $res['receiveName'];
        $receiveAddress=$res['receiveAddress'];
        $receiveProvince= $res['receiveProvince'];
        $receiveCity= $res['receiveCity'];
        $receiveCounty= $res['receiveCounty'];
        $receiveTown= $res['receiveTown'];
        $receivePhone= $res['receivePhone'];
        $weight= $res['weight'];
        $goods= $res['goods'];
        $packageNum= $res['packageNum'];
        $insuredValue= $res['insuredValue'];
        $guaranteeValueAmount= $res['guaranteeValueAmount'];
        $remark= $res['remark'];;// 面单备注
        $sadd= $res['sadd'];
        $type= $res['type'];// 快递类型
        $qbd=new Qbd();

        // 查询价格
        $priceRes = $qbd->findPrice($weight,$senderText,$receiveText,$type);
        // 根据查到的价格 创建本地订单 跳起支付 支付完毕远程生单
        if ($priceRes['errno'] ==0) {
            $res= ExpressorderModel::createOrder($userid,$senderName,$senderPhone,$senderProvince,$senderCity,$senderCounty,$senderTown,
                $senderAddress,$receiveName,$receivePhone,$receiveProvince,$receiveCity,$receiveCounty,$receiveTown,$receiveAddress,null,$goods,
               $packageNum, $guaranteeValueAmount,$insuredValue,$orderSendTime,$remark,$type,$senderText,$receiveText,$weight,'',$priceRes['data']);
            if ($res['errno'] ==0) {
                return  rjson(0,'下单成功',$res['data']);
            }else{
                return  rjson(1,'下单失败',$res['data']);
            }
        }else{
            return djson(1, "价格获取失败",$priceRes['data']);
        }
    }
    //估算运费
    public function estimateprice() {

        $res=json_decode(file_get_contents("php://input"),true);
        $weight = $res['weight'];
        $sendAddress = $res['sendAddress'];
        $receiveAddress = $res['receiveAddress'];
        $type = $res['type'];
        $qbd= new Qbd();
        $priceRes = $qbd->findPrice($weight,$sendAddress,$receiveAddress,$type);
        if ($priceRes['errno']==0) {
            // 看折扣 还是看 首重续重 计算
            $data= $priceRes['data'];
            return   rjson(0,'费用获取成功',ExpressorderModel::culTotalPrice($weight,$data));
        }else {
            return   rjson(1,'费用获取失败',$priceRes['errmsg']);
        }
    }




    //创建支付
    public function topay()
    {
        Log::error("cline".$this->client);
        $res = ExpressorderModel::create_pay(I('order_id'), I('paytype'), $this->client);
        return djson($res['errno'], $res['errmsg'], $res['data']);
    }

    // 查询订单
    public function queryOrder() {
//        $map = ['customer_id' => $this->customer['id'], 'is_del' => 0, 'status' => ['gt', 1]];
//        if (I('type')) {
//            $map['type'] = I('type');
//        }
//        if (I('key')) {
//            $map['order_number|mobile'] = I('key');
//        }
        $map =[];
        $lists = ExpressorderModel::where($map)->order("create_time desc")->paginate(10);
        if ($lists) {
            return djson(0, "ok", $lists);
        } else {
            return djson(1, "暂无订单记录");
        }
    }
    // 查询订单详情
    public function queryOrderDetail() {
        $id= I('id');
        // 1. 查找本地
        $order =M('expressorder') -> where(['id'=>$id])->find();
        //2. 查询渠道订单详情
        if ($order['channel_order_id']) {
            $qbd = new Qbd();
            $channelOrderInfo= $qbd->checkOrder($order['channel_order_id'],$order['type']);
            // 返回的数组
            if ($channelOrderInfo) {
                $channelOrder= $channelOrderInfo['data']['order'];
                $order['rackingNum'] = $channelOrder['waybillNo'];
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
                $order['traceList'] = $channelOrderInfo['data']['traceList'];//轨迹
            }
        }

        return djson(0, "ok", $order);
    }
    // 地址解析
    public function nlpAddress() {
     $qbd= new Qbd();
      //  "data":{
//                "province":"浙江省",
//                "province_code":"330000",
//                "provinceId":330000,
//                "city":"杭州市",
//                "city_code":null,
//                "cityId":330100,
//                "county":"余杭区",
//                "county_code":null,
//                "countyId":330110,
//                "town":"闲林街道",
//                "townId":330110011,
//                "town_code":null,
//                "detail":"竹海水韵竹邻间12幢1单元302室",
//                "person":"陈佳佳",
//                "phonenum":"15658102016",
//                "mobile":null,
//                "telPhone":null
//                 }
    return $qbd->nlpaddress(I('address'));
    }



    /**
     * 充值记录
     */
    public function order_list()
    {
        $map = ['customer_id' => $this->customer['id'], 'is_del' => 0, 'status' => ['gt', 1]];
        if (I('type')) {
            $map['type'] = I('type');
        }
        if (I('key')) {
            $map['order_number|mobile'] = I('key');
        }
        $lists = PorderModel::where($map)->order("create_time desc")->paginate(10);
        if ($lists) {
            return djson(0, "ok", $lists);
        } else {
            return djson(1, "暂时还没有充值记录");
        }
    }

    /**
     * 充值记录详情
     */
    public function orderinfo()
    {
        $info = M('porder')->where(['customer_id' => $this->customer['id'], 'is_del' => 0, 'id' => I('id')])->find();
        if ($info) {
            return djson(0, "ok", $info);
        } else {
            return djson(1, "暂时没有记录信息");
        }
    }


}
