<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2018-03-23
 * Time: 21:42
 */

namespace app\common\library;

use app\common\model\Porder;

class Rechargeapi
{
    //充值
    public static function recharge($porder_id)
    {
        $res = Porder::getCurApi($porder_id);
        if ($res['errno'] != 0) {
            return rjson($res['errno'], $res['errmsg']);
        }
        $api = $res['data']['api'];
        $cur_num = $res['data']['num'];
        $cur_index = $res['data']['index'];
        $porder = M('porder')->where(['id' => $porder_id, 'status' => 2, 'api_open' => 1])->find();
        $config = M('reapi')->where(['id' => $api['reapi_id']])->find();
        $param = M('reapi_param')->where(['id' => $api['param_id']])->find();
        if (!$config || !$param) {
            return rjson(1, '接口未找到');
        }
        //新单号
        $api_order_number = Porder::getApiOrderNumber($porder['order_number'], $porder['api_cur_index'], $porder['api_cur_count'], $cur_num);
        M('porder')->where(['id' => $porder_id])->setField(['api_order_number' => $api_order_number, 'apireq_time' => time(), 'api_cur_index' => $cur_index, 'api_cur_num' => $cur_num, 'api_cur_id' => $config['id'], 'api_cur_param_id' => $param['id']]);
        $porder['api_order_number'] = $api_order_number;

        //判断运营商
        if (in_array($porder['type'], [1, 2]) && isset($api['isp']) && $api['isp']) {
            $ispstr = getISPText($api['isp']);
            if (strpos($ispstr, $porder['isp']) === false) {
                $errmsg = "不在该接口的可充值运营商:" . $ispstr . "，当前：" . $porder['isp'];
                Porder::rechargeFail($porder['order_number'], "提交接口|[" . $cur_index . '][' . $cur_num . ']' . $config['name'] . '-' . $param['desc'] . "，" . $errmsg);
                return rjson(1, $errmsg);
            }
        }
        //提交api
        $ret = self::callApi($porder, $config, $param);
        if ($ret['errno'] != 0) {
            Porder::rechargeFail($porder['order_number'], "提交接口|[" . $cur_index . "][" . $cur_num . ']' . $config['name'] . '-' . $param['desc'] . "|错误|" . $ret['errmsg']);
            return rjson(1, $ret['errmsg']);
        }
        Createlog::porderLog($porder['id'], "提交接口|[" . $cur_index . "][" . $cur_num . ']' . $config['name'] . '-' . $param['desc'] . "|成功|平台返回：" . json_encode($ret['data']));
        M('porder')->where(['id' => $porder_id])->setField(['status' => 3, 'cost' => $param['cost']]);
        return rjson(0, '提交成功');
    }

    //提交接口
    private static function callApi($porder, $config, $param)
    {
        $classname = 'Recharge\\' . $config['callapi'];
        if (!class_exists($classname)) {
            return rjson(1, '系统错误，接口类:' . $classname . '不存在');
        }
        $model = new $classname($config);
        if (!method_exists($model, 'recharge')) {
            return rjson(1, '系统错误，接口类:' . $classname . '的充值方法（recharge）不存在');
        }
        return $model->recharge($porder['api_order_number'], $porder['mobile'], $param, $porder['isp']);
    }

}