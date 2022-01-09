<?php

namespace app\api\controller;

use app\common\library\Createlog;
use app\common\model\Client;
use app\common\model\Porder as PorderModel;
use Recharge\Qbd;

class Expressorder
{
    /**
     * 下单
     */
    public function create_order()
    {
        $mobile = I('mobile');
        $product_id = I('product_id');
        $area = I('area');
        $res = PorderModel::createOrder($mobile, $product_id, $area, $this->customer['id'], $this->client, '下单');
        if ($res['errno'] != 0) {
            return djson($res['errno'], $res['errmsg'], $res['data']);
        }
        $aid = $res['data'];
        PorderModel::compute_rebate($aid);
        Createlog::porderLog($aid, "用户下单成功");
        return djson(0, "ok", ['id' => $aid]);
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
            return   $this->totalPrice($weight,$priceRes['data']['priceA'],$data['priceB'],$data['discount'],$data['totalFeeOri']);
        }else {
            return $priceRes['msg'];
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
    private function totalPrice($weight,$priceA,$priceB,$discount,$totalFeeOri) {
        // 查询价格 根据价格计算 保价费 重量  续重 或者 总价x折扣
        if ($priceA) {
            if ($priceB<2.5) {
                $priceB =2.5;
            }elseif ($priceB<3) {
                $priceB =3;
            }else {
                $priceB= $priceB+0.5;
            }
            // 首重 加 续重 算法
            if ($priceA<6.5) {
                return  6.5+($weight-1)*$priceB;
            }else if ($priceA<7) {
                return  7+($weight-1)*$priceB;
            }else if ($priceA<8) {
                return  8+($weight-1)*$priceB;
            }else {
                return  ($priceA+0.5)+($weight-1)*$priceB;
            }
        }else{
            if ($discount<7) {
                return $totalFeeOri*0.7;
            }else if ($discount<8){
                return $totalFeeOri*0.8;
            }else {
                return  $totalFeeOri*($discount+0.5)*0.1;
            }
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
