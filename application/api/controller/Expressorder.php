<?php

namespace app\api\controller;

use app\common\library\Createlog;
use app\common\model\Client;
use app\common\model\Expressorder as ExpressorderModel;

use Recharge\Qbd;

class Expressorder
{
    /**
     * 下单
     */
    public function create_order()
    {
        $orderSendTime= I('orderSendTime');
        $senderText= I('senderText');
        $receiveText= I('receiveText');
        $senderName= I('senderName');
        $senderCity= I('senderCity');
        $senderAddress= I('senderAddress');
        $senderPhone= I('senderPhone');
        $receiveName= I('receiveName');
        $receiveAddress= I('receiveAddress');
        $receiveCity= I('receiveCity');
        $receivePhone= I('receivePhone');
        $weight= I('weight');
        $goods= I('goods');
        $insuredValue= I('insuredValue');
        $guaranteeValueAmount= I('guaranteeValueAmount');
        $remark= I('remark');// 面单备注
        $sadd= I('sadd');
        $type= I('type');// 快递类型
        $qbd=new Qbd();

        // 查询价格
        $priceRes = $qbd->findPrice($weight,$senderAddress,$receiveAddress,$type);
        // 根据查到的价格 创建本地订单 跳起支付 支付完毕远程生单
        if ($priceRes['errno'] ==0) {
            $res= ExpressorderModel::createOrder($senderName,$senderPhone,$senderCity,$senderAddress,$receiveName,$receivePhone,$receiveCity,$receiveAddress,null,$goods,
                $guaranteeValueAmount,$insuredValue,$orderSendTime,$remark,$type,$senderText,$receiveText,$weight,'',$priceRes);
            if ($res['errno'] ==0) {
                return  rjson(0,'下单成功',$res['data']);
            }else{
                return  rjson(1,'下单失败',$res['data']);
            }
        }else{
            return djson(1, "价格获取失败",'');
        }
    }
    //估算运费
    public function estimateprice() {
        $weight = I('weight');
        $qbd= new Qbd();
        $priceRes = $qbd->findPrice($weight,'河北省承德市隆化县隆化镇下洼子南胡同盛达水暖','广东省东莞市横沥镇新世纪华庭金岸7A','6');
        var_dump($priceRes);
        if ($priceRes['errno']==0) {
            // 看折扣 还是看 首重续重 计算
            $data= $priceRes['data'];
            return   ExpressorderModel::culTotalPrice($weight,$data);
        }else {
            return $priceRes['msg'];
        }

    }




    //创建支付
    public function topay()
    {
        $res = PorderModel::create_pay(I('order_id'), I('paytype'), $this->client);
        return djson($res['errno'], $res['errmsg'], $res['data']);
    }

    // 查询订单
    public function queryOrder() {

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
