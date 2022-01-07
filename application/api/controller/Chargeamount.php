<?php

namespace app\api\controller;
use app\common\library\Createlog;
use app\common\model\Porder;


/**
 * 电费已经充值得金额查询
 */
class Chargeamount
{
  public function chargeamount() {
     $orderid =  I('orderid');
     echo self::recharge($orderid);
  }
    //充值
    public static function recharge($porder_id)
    {
        $res = Porder::getCurApi2($porder_id);
       if ($res['errno'] != 0) {
            return '错1'.$res['errmsg'];
        }
        $api = $res['data']['api'];

        $config = M('reapi')->where(['id' => $api['reapi_id']])->find();
        $param = M('reapi_param')->where(['id' => $api['param_id']])->find();
        if (!$config || !$param) {
            return  '接口未找到';
        }
        $porder=M('porder')->where(['order_number' => $porder_id])->find();
//        $porder['api_order_number'] = $api_order_number;
        //提交api
        $ret = self::callApi($porder, $config, $param);
        return $ret;
    }

    //提交接口
    private static function callApi($porder, $config, $param)
    {
        $classname = 'Recharge\\' . $config['callapi'];
        if (!class_exists($classname)) {
            return  '系统错误，接口类:' . $classname . '不存在';
        }
        $model = new $classname($config);
        if (!method_exists($model, 'check')) {
            return  '系统错误，接口类:' . $classname . '的充值方法（check）不存在';
        }
        return $model->check($porder['api_order_number']);
    }
}
