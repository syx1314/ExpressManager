<?php


namespace Recharge;


use app\common\enum\ExpressEnum;
use app\common\enum\ExpressOrderEnum;
use app\common\library\RedisPackage;
use think\Log;

/**
 * Q必达快递
 **/
class Qbd
{
    private $baseUrl='https://jdkd.ulifego.com';
    private $baseUrlApi='http://api.ulifego.com/';
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
    public function createOrder($data){
//        $data = [
//            "t" => $this->timeram(),
//            "orderSendTime" => "",
//            "senderAddressCode"=>"",
//            "senderText"=>"叶林(15850812532)\n地址：广东省江门市新会区新会经济开发区三和大道南9号,唐朝主题养生馆",
//            "receiveText"=>"叶林(15850812532)\n地址：广东省江门市新会区新会经济开发区三和大道南9号,唐朝主题养生馆",
//            "id"=>0,
//            "packageNum"=>1,
//            "senderName"=>"叶林",
//            "senderCity"=>"广东省江门市新会区新会经济开发区",
//            "senderAddress"=>"广东省江门市新会区新会经济开发区三和大道南9号,唐朝主题养生馆",
//            "senderPhone"=>"15850812532",
//            "receiveName"=>"叶林",
//            "receiveAddress"=>"广东省江门市新会区新会经济开发区三和大道南9号,唐朝主题养生馆",
//            "receiveCity"=>"广东省江门市新会区新会经济开发区",
//            "receivePhone"=>"15850812532",
//            "weight"=>1,
//            "goods"=>"日用品",
//            "insuredValue"=>0,
//            "guaranteeValueAmount"=>0,
//            "remark"=>"",
//            "channelName"=>"QBD-京东-TKE-QGA",
//            "priceA"=>6.17,
//            "priceB"=>1.02,
//            "discount"=>0,
//            "fee"=>0,
//            "isThird"=>true,
//            "isInsured"=>true,
//            "fee1"=>6.17,
//            "remark1"=>"",
//            "type"=>1,
//            "sadd"=>"三和大道南9号,唐朝主题养生馆",
//            "agentCode"=>"13102101195"
//        ];
        $data['t'] = $this->timeram();
        $data['agentCode'] = '13102101195';
        // 查价格  计算总额 生成订单
        // 生成预生单
        //根据type 请求不同的地址
        $url='';
        if ($data['type'] ==1) {
            //京东
            $url ='/ht/jdkd/jdkdorder/advanceOrder';
        }else if ($data['type'] ==2) {
            //德邦
            $url ='/ht/deppon/depponorder/advanceOrder';
        }else if ($data['type'] ==5) {
            //申通
            $url ='/ht/sto/stoorder/advanceOrder';
        }else if ($data['type'] ==6) {
            //圆通
            $url ='/ht/yto/ytoorder/advanceOrder';
        }else if ($data['type'] ==7) {
            //德邦航空
            $url ='/ht/yto/ytoorder/advanceOrder';
        }else if ($data['type'] ==13) {
            //兔子
            $url ='/ht/yto/ytoorder/advanceOrder';
        }
      return  $this->http($this->baseUrl.$url,$data);

    }


    //创建app 订单
    public function createAppOrder($data) {
//        {
//                "receiveAddress": "辽宁省 鞍山市 立山区沙河街道生产街30-4-401",
//      "mailType": 1,
//      "weight": 4,
//      "billWeight": 0,
//      "senderName": "湘湘",
//      "senderPhone": "18573341847",
//      "type": 2,
//      "senderAddress": "湖南省 长沙市 长沙县星沙街道蝴蝶谷7栋",
//      "receiveName": "温欣",
//      "length": 0,
//      "packageNum": 1,
//      "goods": "日用品",
//      "volumeWeight": 0,
//      "receivePhone": "15904926668",
//      "height": 0,
//      "width": 0,
//      "remark": "",
//      "incrementFee": 0}
       $order = [
              "receiveAddress" =>$data['receiveAddress'],
              "mailType"=> 1,
              "weight"=> $data['weight'],
              "billWeight"=>0,
              "senderName"=> $data['senderName'],
              "senderPhone"=>$data['senderPhone'],
              "type"=> $data['type'],
              "senderAddress"=> $data['senderAddress'],
              "receiveName"=> $data['receiveName'],
              "length"=> 0,
              "packageNum"=>1,
              "goods"=> $data['goods'],
              "volumeWeight"=> 0,
              "receivePhone"=> $data['receivePhone'],
              "height"=> 0,
              "width"=> 0,
              "remark"=> "",
              "incrementFee"=>0
       ];
        /**
         * {
        "msg": "success",
        "code": 0,
        "orderNo": "DBKD164810059554955",
        "kuaidi": 2,
        "typeName": "德邦快递",
        "id": 160911,
        "waybillNo": null
        }
         */
       return $this->http3($this->baseUrlApi.'/ht/web/createOrder',$order);


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
        $res= $this->http($this->baseUrlApi.'/ht/web/resolveAddress?&text='.urlencode($text).'&type=1',null);
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

    // 获取渠道快递描述
    public function getExpressDesc($type) {
//        {
//            "code": 0,
//  "msg": "成功",
//  "data": "<p>现有两条渠道，请如实填写重量体积，系统会匹配最低价渠道给寄件人，主要区别3KG以上续重差别，请知悉！</p ><p><br></p ><p>圆通快递，限重50kg,时效48小时，时效内揽收率80%，取件中规中矩，收费规则:(长*宽*高cm/6000与重量)取最大值！</p ><p><br></p ><p>建议体积大于0.35m³选择智能物流，以免超重原价！感谢理解！</p >"
//}
        return $this->http($this->baseUrlApi.'/ht/web/content?expressType='.$type.'&type=2','');
    }
    /**
     * 查询价格
     */
    public function findPrice($sendPhone,$weight,$sendAdd,$reviceAdd,$type)
    {
//        {"t":1641528302243,"weight":1,"sendAdd"=>"河北省承德市隆化县隆化镇下洼子南胡同盛达水暖","reviceAdd"=>"广东省东莞市横沥镇新世纪华庭金岸7A","type":6}
//        {
//            "type": 6,
//  "sendPhone": "18335336970",
//  "weight": 1,
//  "receiveAddress": "贵州省 贵阳市 观山湖区金岭社区服务中心金融城贵州银行大厦 韦译晗 18185009694",
//  "goodsValue": 0,
//  "sendAddress": "山西省 忻州市 偏关县鑫源美玲超市 池国玲 18335336970"
//}
        $data= [
            't'=>$this->timeram(),
            'weight'=>$weight,
            'sendPhone'=>$sendPhone,
            'sendAddress'=>$sendAdd,
            'receiveAddress'=>$reviceAdd,
            'goodsValue'=>0,
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
        $res=$this->http($this->baseUrl.'/ht/web/estimate',$data);
        if ($res['errno'] == 0) {
            // 对数据进行加工

//            {
//                "code": 0,
//                "msg": "成功",
//                "data": {
//                            "channelFee": 6,
//                "serviceCharge": 0,
//                "originalFee": 0,
//                "guarantFee": 0,
//                "type": 6,
//                "typeName": "圆通快递",
//                "isInsured": false,
//                "insuredMsg": "线上保价暂不支持，请线下与快递员进行保价",
//                "incrementFee": 0,
//                "volume": 0,
//                "volumeWeight": 0,
//                "billWeight": 1
//                    }
//            }

            $data = $res['data'];
            $data=$this->calPrice($data['type'],$data['billWeight'],$data['channelFee'],$data['originalFee'],$data['serviceCharge']);
            $desc = $this->getExpressDesc($type);
            if ($desc['errno'] ==0) {
                $data['remark'] =$desc['data'];
                $data['name'] =$res['data']['typeName'];
            }
            return rjson(0,'费用预估成功',$data);
        }
        return rjson(1,'费用预估失败',$res['data']);
    }


    public function calPrice($type,$weight,$channelFee,$originalFee,$serviceCharge) {

        $data =  [
            'priceA'=>0,
            'priceB'=>0,
            'discount'=>0,
            'fee'=>$channelFee,
            'fee1'=>$originalFee,
            'totalFeeOri'=>$originalFee,
            'totalFee'=>$originalFee,
            'serviceCharge'=>$serviceCharge
        ];
         switch ($type) {
             case ExpressEnum::JD['id']:
             case ExpressEnum::DEBANG['id']:
             case ExpressEnum::JDWL['id']:
             case ExpressEnum::DEBANGWL['id']:
             case ExpressEnum::DEBANGAIR['id']:
             case ExpressEnum::SF['id']:
             case ExpressEnum::JDDU['id']:
             case ExpressEnum::JDSHANGJIA['id']:
                  $discount = $channelFee*1.0/$originalFee;
                  $data['$discount'] =$discount;
             return $data;
             case ExpressEnum::SHENTONG['id']:
             case ExpressEnum::YUANTONG['id']:
                   $price = 6.5+($weight-1)*2.5;
                   if ($price>$channelFee) {
                      $data['priceA'] = 6;
                      $data['priceB'] = 2;
                   }else {
                       $price = 8+($weight-1)*3.5;
                       if ($price>$channelFee) {
                           $data['priceA'] = 8;
                           $data['priceB'] = 3.5;
                       }else {
                           return null;
                       }
                   }
             return $data;
             default:
                 return null;

         }
    }

    /**
     *查询订单详情
     * @param $channel_order_id 渠道订单id
     * @param $type 快递公司
     */
    public function checkOrder($channel_order_id,$type)
    {
        return $this->http2('http://api.ulifego.com/ht/web/orderDetail?orderId='.$channel_order_id.'&type='.$type,null);
    }

    /**
     * 取人生成快递公司订单
     */
    public function confirmOrder($channel_order_id,$type) {
        $data = [
            't' =>$this->timeram()
        ];
        $url='';
        if ($type ==1) {
            //京东
            $url ='/ht/jdkd/jdkdorder/confirm/';
        }else if ($type ==2) {
            //德邦
            $url ='/ht/deppon/depponorder/confirm/';
        }else if ($type==5) {
            //申通
            $url ='/ht/sto/stoorder/confirm/';
        }else if ($type ==6) {
            //圆通
            $url ='/ht/yto/ytoorder/confirm/';
        }else if ($type ==7) {
            //德邦航空
            $url ='/ht/yto/ytoorder/confirm/';
        }else if ($type ==13) {
            //兔子
            $url ='/ht/yto/ytoorder/confirm/';
        }
        return  $this->http($this->baseUrl.$url.$channel_order_id,$data);
    }
    /**
     * 取消订单
     */
    public function cancelOrder($channel_order_id,$type) {
      $data = [
          't' =>$this->timeram()
      ];
        $url='';
        if ($type ==1) {
            //京东
            $url ='/ht/jdkd/jdkdorder/cancelWaybill/';
        }else if ($type ==2) {
            //德邦
            $url ='/ht/deppon/depponorder/cancelWaybill/';
        }else if ($type==5) {
            //申通
            $url ='/ht/sto/stoorder/cancelWaybill/';
        }else if ($type ==6) {
            //圆通
            $url ='/ht/yto/ytoorder/cancelWaybill/';
        }else if ($type ==7) {
            //德邦航空
            $url ='/ht/yto/ytoorder/cancelWaybill/';
        }else if ($type ==13) {
            //兔子
            $url ='/ht/yto/ytoorder/cancelWaybill/';
        }
      return  $this->http($this->baseUrl.$url.$channel_order_id,$data);
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
        $loginRes = $this->http($this->baseUrl.'/ht/web/login/loginNew',$data);
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
            $strPOST = json_encode($param,JSON_UNESCAPED_UNICODE);
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
            Log::error("QBDtoken1".$token);
            $header= [
                'Content-Type:application/json;charset=utf-8',
                'Token:'.$token,
                'User-Agent:BD/3.0(iPhone;iOS 10.3.1;Scale/3.00)'
            ];
            curl_setopt($oCurl, CURLOPT_HTTPHEADER, $header);
        }else if (!strpos($url,'loginNew')){
            // 不是登录路径 而且没有登录成功 就去登录
            $this->login();
            return rjson(1,'系统繁忙,请重试','');
        }
        $sContent = curl_exec($oCurl);
        $a=curl_getinfo($oCurl);
        Log::error("头".http_build_query($a));
        curl_close($oCurl);
        Log::error("快递接口请求地址".$url);
        Log::error("快递接口返回".$sContent);
        $res=json_decode($sContent, true);
        if ($res) {
            if ( $res['code']== 0  && isset($res['data'])) {
                return rjson(0,'接口请求成功',$res['data']);
            }else if ($res['code']== 0  && !isset($res['data'])){
                Log::error("没有data执行");
                return rjson(0,'接口请求成功',$res['msg']);
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
    /**
     *  请求 p 空  get  否则post
     */
    private function http2($url, $param)
    {
        $oCurl = curl_init();
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        if ($param) {
            $strPOST = json_encode($param,JSON_UNESCAPED_UNICODE);
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
            Log::error("QBDtoken1".$token);
            $header= [
                'Content-Type:application/json;charset=utf-8',
                'token:'.$token
            ];
            curl_setopt($oCurl, CURLOPT_HTTPHEADER, $header);
        }else if (!strpos($url,'loginNew')){
            // 不是登录路径 而且没有登录成功 就去登录
            $this->login();
            return rjson(1,'系统繁忙,请重试','');
        }
        $sContent = curl_exec($oCurl);
        $a=curl_getinfo($oCurl);
        Log::error("头".http_build_query($a));
        curl_close($oCurl);
        Log::error("快递接口请求地址".$url);
        Log::error("快递接口返回".$sContent);
        $res=json_decode($sContent, true);
        if ($res) {
            if ( $res['code']== 0) {
                return rjson(0,'接口请求成功',$res);
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

    /**
     * @param $url
     * @param $param
     * @return array|\think\response\Json 结果原样返回 不包装
     */
    private function http3($url, $param)
    {
        $oCurl = curl_init();
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        if ($param) {
            $strPOST = json_encode($param,JSON_UNESCAPED_UNICODE);
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
            Log::error("QBDtoken1".$token);
            $header= [
                'Content-Type:application/json;charset=utf-8',
                'token:'.$token
            ];
            curl_setopt($oCurl, CURLOPT_HTTPHEADER, $header);
        }else if (!strpos($url,'loginNew')){
            // 不是登录路径 而且没有登录成功 就去登录
            $this->login();
            return rjson(1,'系统繁忙,请重试','');
        }
        $sContent = curl_exec($oCurl);
        $aStatus=curl_getinfo($oCurl);
        curl_close($oCurl);
        Log::error("快递接口请求地址".$url);
        Log::error("快递接口返回".$sContent);
        if (intval($aStatus["http_code"]) == 200) {
            $result = json_decode($sContent, true);
            if ($result['code'] == 0) {
                return rjson(0, $result['msg'], $result);
            } else {
                return rjson(1, $result['msg'], $result);
            }
        } else {
            return rjson(1, '接口访问失败，http错误码' . $aStatus["http_code"]);
        }
    }
}
