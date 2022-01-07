<?php


namespace Recharge;


use app\common\library\RedisPackage;
use think\Log;

/**
 * Q必达快递
 **/
class Qbd
{
    private $userid;
    private $apikey;
    private $notify;
    private $apiurl;//充值接口

    public function __construct($option)
    {
        $this->userid = isset($option['param1']) ? $option['param1'] : '';
        $this->apikey = isset($option['param2']) ? $option['param2'] : '';
        $this->notify = isset($option['param3']) ? $option['param3'] : '';
        $this->apiurl = isset($option['param4']) ? $option['param4'] : '';
    }

    /**
     * 创建订单
     */
    public function createOrder($data)
    {
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
        return $this->http('hhttps://jdkd.ulifego.com/ht/common/nlpaddress?t='.$this->timeram().'&text='.$text.'&type=1',null);
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
            'type'=>$type,
        ];

        return $this->http_get('https://jdkd.ulifego.com/ht/common/getChannelFreight',$data);
    }

    /**
     *查询订单详情
     */
    public function checkOrder($data)
    {
       return $this->http('https://jdkd.ulifego.com/ht/jdkd/jdkdorder/advanceOrder',$data);
    }

    /**
     * 登录存token
     */
    public function login($account,$pwd)
    {

        $data = [
            'account'=>$account,
            'password'=>$pwd
        ];
       $loginRes = $this->http('https://jdkd.ulifego.com/ht/web/login/loginNew',$data);
       if ($loginRes['code'] == 0) {
          $loginData= $loginRes['code']['"data": '];
           RedisPackage::set('qbdToken',$loginData['token']);
       }else{
           // 自动登录失败 发送通知消息
       }
        // 暂时存个qbdtoken
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
        }
        curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($oCurl, CURLOPT_TIMEOUT, 30);
        curl_setopt($oCurl, CURLOPT_HEADER, 0);
        $token = RedisPackage::get('qbdToken');
//        QBD/3.0(iPhone;iOS 10.3.1;Scale/3.00)
        if ($token) {
            curl_setopt($oCurl, CURLOPT_HTTPHEADER, ['token:'.$token]);
        }
        Log::error($strPOST);
        $sContent = curl_exec($oCurl);
        curl_close($oCurl);
        return json_decode($sContent, true);

    }
}
