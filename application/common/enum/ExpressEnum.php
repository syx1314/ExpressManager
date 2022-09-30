<?php


namespace app\common\enum;

//-2创建订单 -1 支付完成 0渠道预下单1待取件2运输中5已签收6取消订单7终止揽收
class ExpressEnum extends EnumBasics
{
  const JD =[
        'id'=>1,
        'name' => '京东快递',
        'desc'=>'取件较快',
        'icon'=>'http://81.68.198.45/uploads/icon_jjfs_jingdong.png',
        'isEnable'=>true
  ];
    const DEBANG =[
        'id'=>2,
        'name' => '德邦快递',
        'desc'=>'取件快服务好',
        'icon'=>'http://81.68.198.45/uploads/icon_jjfs_debang.png',
        'isEnable'=>true

    ];
    const JDWL =[
        'id'=>3,
        'name' => '京东物流',
        'desc'=>'取件较快',
        'icon'=>'http://81.68.198.45/uploads/icon_jjfs_jdzhong.png',
        'isEnable'=>true
    ];
    const DEBANGWL =[
        'id'=>4,
        'name' => '德邦物流',
        'desc'=>'取件快服务好',
        'icon'=>'http://81.68.198.45/uploads/icon_jjfs_dbzhong1.png',
        'isEnable'=>true
    ];
    const SHENTONG =[
        'id'=>5,
        'name' => '申通快递',
        'desc'=>'取件慢,接单慢',
        'icon'=>'http://81.68.198.45/uploads/icon_jjfs_shentong.png',
        'isEnable'=>true
    ];
    const YUANTONG =[
        'id'=>6,
        'name' => '圆通快递',
        'desc'=>'取件揽收适中',
        'icon'=>'http://81.68.198.45/uploads/icon_jjfs_yuantong.png',
        'isEnable'=>true
    ];
    const DEBANGAIR =[
        'id'=>7,
        'name' => '德邦航空',
        'desc'=>'取件快服务好',
        'icon'=>'http://81.68.198.45/uploads/icon_jjfs_dbhang.png',
        'isEnable'=>true
    ];
    const SF =[
        'id'=>8,
        'name' => '顺丰快递',
        'desc'=>'取件快服务好',
        'icon'=>'http://81.68.198.45/uploads/icon_jjfs_shunfeng.png',
        'isEnable'=>true
    ];
    const JDDU =[
        'id'=>9,
        'name' => '京东得物',
        'desc'=>'取件较快',
        'icon'=>'http://81.68.198.45/uploads/icon_dwjj_jingdong.png',
        'isEnable'=>true
    ];
    const JDSHANGJIA =[
        'id'=>9,
        'name' => '京东商家',
        'desc'=>'取件快价格低',
        'icon'=>'http://81.68.198.45/uploads/icon_sjjj_jingdong.png',
        'isEnable'=>true
    ];
    const JT =[
        'id'=>13,
        'name' => '极兔快递',
        'desc'=>'揽收略快',
        'icon'=>'http://81.68.198.45/uploads/icon_jjfs_jitu.png',
        'isEnable'=>true
    ];

    public  static function getValueByKey($key)
    {
        return (parent::values())[$key];
    }
}
