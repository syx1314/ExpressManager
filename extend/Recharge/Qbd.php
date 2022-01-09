<?php


namespace Recharge;


use app\common\library\RedisPackage;
use think\Log;

/**
 * Q必达快递
 **/
class Qbd
{


    /**
     * @param $orderSendTime  //预约时间
     * @param $senderText  //寄件人文本
     * @param $receiveText // 收件人文本
     * @param $packageNum  // 包裹书
     * @param $senderName  //寄件人名字
     * @param $senderCity  //寄件人城市
     * @param $senderAddress //寄件人地址
     * @param $senderPhone   //寄件人手机号
     * @param $receiveName  // 收件人名字
     * @param $receiveAddress // 收件人地址
     * @param $receiveCity  // 收件人城市
     * @param $receivePhone // 收件人手机号
     * @param $weight // 重量
     * @param $goods  // 商品名字
     * @param $insuredValue
     * @param $guaranteeValueAmount
     * @param $remark
     * @param $channelName // 渠道名字
     * @param $priceA  // 首重
     * @param $priceB  // 续重
     * @param $discount // 折扣
     * @param $fee
     * @param $isThird
     * @param $isInsured
     * @param $fee1
     * @param $remark1
     * @param $type // 快递类型
     * @param $sadd
     * @param $agentCode
     */
    public function createOrder($orderSendTime,$senderText,$receiveText,$packageNum,$senderName,$senderCity,
                                $senderAddress,$senderPhone,$receiveName,$receiveAddress,$receiveCity,$receivePhone,
                                $weight,$goods,$insuredValue,$guaranteeValueAmount,$remark,$channelName,
                                $priceA,$priceB,$discount,$fee,$isThird,$isInsured,$fee1,$remark1,$type,$sadd,$agentCode){
        $data = [
                "t" => $this->timeram(),
                "orderSendTime" => "",
                "senderAddressCode"=>"",
                "senderText"=>"叶林(15850812532)\n地址：广东省江门市新会区新会经济开发区三和大道南9号,唐朝主题养生馆",
                "receiveText"=>"叶林(15850812532)\n地址：广东省江门市新会区新会经济开发区三和大道南9号,唐朝主题养生馆",
                "id"=>0,
                "packageNum"=>1,
                "senderName"=>"叶林",
                "senderCity"=>"广东省江门市新会区新会经济开发区",
                "senderAddress"=>"广东省江门市新会区新会经济开发区三和大道南9号,唐朝主题养生馆",
                "senderPhone"=>"15850812532",
                "receiveName"=>"叶林",
                "receiveAddress"=>"广东省江门市新会区新会经济开发区三和大道南9号,唐朝主题养生馆",
                "receiveCity"=>"广东省江门市新会区新会经济开发区",
                "receivePhone"=>"15850812532",
                "weight"=>1,
                "goods"=>"日用品",
                "insuredValue"=>0,
                "guaranteeValueAmount"=>0,
                "remark"=>"",
                "channelName"=>"QBD-京东-TKE-QGA",
                "priceA"=>6.17,
                "priceB"=>1.02,
                "discount"=>0,
                "fee"=>0,
                "isThird"=>true,
                "isInsured"=>true,
                "fee1"=>6.17,
                "remark1"=>"",
                "type"=>1,
                "sadd"=>"三和大道南9号,唐朝主题养生馆",
                "agentCode"=>"13102101195"
        ];
        // 查价格  计算总额 生成订单
        // 生成预生单
        $this->http('https://jdkd.ulifego.com/ht/jdkd/jdkdorder/advanceOrder',$data);

    }

    // 地址解析
    public function nlpaddress($text) {
//        {
//            "msg":"success",
//            "code":0,
//            "data":{
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
//        }
        $res= $this->http('https://jdkd.ulifego.com/ht/common/nlpaddress?t='.$this->timeram().'&text='.urlencode($text).'&type=1',null);
        if ($res['errno']== 0 && $res['data']) {
            return rjson(0,'解析地址成功',$res['data']);
        }else {
            return rjson(1,'解析地址出错',$res['data']);
        }

    }

    //快递黑名单
    public function expressblacklist() {
//        {"msg":"success","code":0,"isExist":false}
//        http://jdkd.ulifego.com/ht/express/expressblacklist/checkMobile/15658102016?t=1641571171922
    }
    //timeram
    public function timeram() {
        return time().'000';
    }

    /**
     * 查询价格
     */
    public function findPrice($weight,$sendAdd,$reviceAdd,$type)
    {
//        {"t":1641528302243,"weight":1,"sendAdd"=>"河北省承德市隆化县隆化镇下洼子南胡同盛达水暖","reviceAdd"=>"广东省东莞市横沥镇新世纪华庭金岸7A","type":6}
        $data= [
            't'=>$this->timeram(),
            'weight'=>$weight,
            'sendAdd'=>$sendAdd,
            'reviceAdd'=>$reviceAdd,
            'type'=>$type, // 快递
        ];
//        {
//            "msg":"success",
//            "billChannel":Object{...},
//            "code":0,
//            "data":{
//                "type":2,
//                "typeName":"德邦快递",
//                "name":"QBD-德邦-折扣-特快",
//                "fee":11,
//                "fee1":7.04,
//                "serviceCharge":0,
//                "priceA":0,
//                "priceB":0,
//                "discount":6.4,
//                "takeRate":0,
//                "isThird":false,
//                "isInsured":true,
//                "totalFeeOri":11,
//                "remark":"与官网价格一致，请按实际重量填写，系统才能匹配最低价渠道 （拒收，原价，长超150cm,长宽高超250cm,需20元加长费）特别声明：德邦2KG-4KG，有的地方4-3KG比2kg便宜，一切以德邦官网价格为准，抛比8000",
//                "takeFee":0,
//                "rate":null,
//                "insuredValue":null,
//                "guarantFee":0,
//                "totalFee":7.04,
//                "otherFee":0,
//                "otherRemark":null
//            }
//        }
        $res=$this->http('https://jdkd.ulifego.com/ht/common/getChannelFreight',$data);
        if ($res['data']) {
            return rjson(0,'费用预估成功',$res['data']);
        }
        return rjson(1,'费用预估失败',$res['data']);
    }

    /**
     *查询订单详情
     */
    public function checkOrder($data)
    {
       return $this->http('https://jdkd.ulifego.com/ht/jdkd/jdkdorder/advanceOrder',$data);
    }

    /**
     * 取消订单
     */
    public function cancelOrder() {

    }

    /**
     * 登录存token
     */
    public function login()
    {

        $data = [
            'account'=>'13102101195',
            'password'=>'123456'
        ];
       $loginRes = $this->http('https://jdkd.ulifego.com/ht/web/login/loginNew',$data);
       if ($loginRes['errno'] == 0) {
           $loginData= $loginRes['data'];
           var_dump($loginData);
           $redis=new RedisPackage();
           $redis::set('qbdToken',$loginData['token']);
           return rjson(0,'登录成功');
       }else{
           // 自动登录失败 发送通知消息
           return rjson(1,'登录失败',$loginRes['errmsg']);
       }
    }


    /**
     *  请求 p 空  get  否则post
     */
    private function http($url, $param)
    {
        $oCurl = curl_init();
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        if ($param) {
            $strPOST = json_encode($param);
            curl_setopt($oCurl, CURLOPT_POST, true);
            curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
            curl_setopt($oCurl, CURLOPT_HTTPHEADER, ["Content-Type:application/json;charset=utf-8"]);
            Log::error("请求参数".$strPOST);
        }
        curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($oCurl, CURLOPT_TIMEOUT, 30);
        curl_setopt($oCurl, CURLOPT_HEADER, 0);
        $redis=new RedisPackage();
        $token = $redis::get('qbdToken');
//        QBD/3.0(iPhone;iOS 10.3.1;Scale/3.00)
        if ($token) {
            $header= [
                'Content-Type'=>'application/json;charset=utf-8',
                'token' => $token
            ];
            curl_setopt($oCurl, CURLOPT_HTTPHEADER, $header);
        }else if (!strpos($url,'loginNew')){
            // 不是登录路径 而且没有登录成功 就去登录
            $this->login();
            return rjson(1,'系统繁忙,请重试','');
        }
        $sContent = curl_exec($oCurl);
        curl_close($oCurl);
        Log::error("快递接口请求地址".$url);
        Log::error("快递接口返回".$sContent);
        $res=json_decode($sContent, true);
        if ($res) {
            if ( $res['code']== 0) {
                return rjson(0,'接口请求成功',$res['data']);
            }else if ($res['code']== 401) {
                // token 失效 继续登录
                $this->login();
                return rjson(1,'系统繁忙,请重试',$res['msg']);
            }else{
                return rjson($res['code'],'请求失败原因:'.$res['msg'],$res['msg']);
            }
        }else{
            return rjson(1,'系统异常接口失败','');
        }

    }
}
