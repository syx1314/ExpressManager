<?php


namespace app\common\enum;

//pay_status:支付状态：1 待支付 2 支付完成
class ExpressOrderPayStatusEnum extends EnumBasics
{
  const NO_PAY = 1;
  const PAY_COMPLETE = 2;
}
