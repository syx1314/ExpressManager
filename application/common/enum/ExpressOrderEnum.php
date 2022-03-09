<?php


namespace app\common\enum;

//-2创建订单 -1 支付完成 0渠道预下单1待取件2运输中5已签收6取消订单7终止揽收
class ExpressOrderEnum extends EnumBasics
{
  const CREATE = -2;
  const PAY_COMPLETE = -1;
  const YU_CREATE_ORDER = 0;
  const DAI_QU_JIAN = 1;
  const YUN_SHU_ZHONG = 2;
  const YI_QIAN_SHOU = 5;
  const CANCEL_ORDER = 6;
  const ZUN_ZHI_LAN_SHOU = 7;
}
