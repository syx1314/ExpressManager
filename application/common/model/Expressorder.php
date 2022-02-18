<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2017-11-14
 * Time: 11:13
 */

namespace app\common\model;

use app\api\controller\Notify;
use app\common\library\Createlog;
use app\common\library\Notification;
use app\common\library\PayWay;
use app\common\library\Rechargeapi;
use app\common\library\Wxrefundapi;
use app\common\model\Porder as PorderModel;
use think\Model;

/**
 **/
class Expressorder extends Model
{
    const PR = 'DASHAN';

    protected $append = ['status_text', 'status_text2', 'create_time_text'];

    public static function init()
    {
        self::event('after_insert', function ($expressorder) {
            $order_number = self::PR . date('ymd', time()) . $expressorder->id;
            $expressorder->where(['id' => $expressorder->id])->update(['out_trade_num' => $order_number]);
        });
    }

    public function Customer()
    {
        return $this->belongsTo('Customer', 'customer_id');
    }

    public function getStatusTextAttr($value, $data)
    {
        return C('PORDER_STATUS')[$data['status']];
    }

    public function getStatusText2Attr($value, $data)
    {
        return C('ORDER_STUTAS')[$data['status']];
    }

    public function getCreateTimeTextAttr($value, $data)
    {
        return time_format($data['create_time']);
    }

    public function getRebateStatusText($is_rebate, $status)
    {
        if ($is_rebate) {
            return "已返利";
        } else {
            if (in_array($status, [2, 3])) {
                return "待返利";
            } elseif (in_array($status, [5, 6])) {
                return "失败不返";
            } elseif (in_array($status, [4])) {
                return "待返利";
            } else {
                return "未知";
            }
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
    public static function culTotalPrice($weight, $data)
    {
        // 查询价格 根据价格计算 保价费 重量  续重 或者 总价x折扣
        $weight = $weight;
        $priceA = $data['priceA'];
        $priceB = $data['priceB'];
        $discount = $data['discount'];
        $totalFeeOri = $data['fee'];
        if ($priceA) {
            if ($priceB < 2.5) {
                $priceB = 2.5;
            } elseif ($priceB < 3) {
                $priceB = 3;
            } else {
                $priceB = $priceB + 0.5;
            }
            // 首重 加 续重 算法
            if ($priceA < 6.5) {
                $priceA = 6.5;
            } else if ($priceA < 7) {
                $priceA = 7;
            } else if ($priceA < 8) {
                $priceA = 8;
            } else {
                $priceA = ($priceA + 0.5);
            }
            $sum = $priceA + ($weight - 1) * $priceB;
        } else {
            if ($discount <= 5) {
                $discount = 5;
            } else if ($discount < 7) {
                $discount = 7;
            } else if ($discount < 8) {
                $discount = 8;
            } else {
                $discount = $discount + 0.5;
            }
            $sum = $totalFeeOri * $discount * 0.1;
        }
        $sum += $data['serviceCharge'];

        $pr = [
            'priceA' => $priceA,
            'priceB' => $priceB,
            'totalFeeOri' => $totalFeeOri,
            'discount' => $discount,
            'serviceCharge' => $data['serviceCharge'],
            'totalPrice' => $sum,
            'remark'     =>$data['remark']
        ];
        return $pr;
    }

    /**
     * @param $sender_name
     * @param $sender_phone
     * @param $senderCity
     * @param $sender_address
     * @param $receiveName
     * @param $receivePhone
     * @param $receiveCity
     * @param $receiveAddress
     * @param $deliveryType //产品类型 6:特快零担 25 特快重货type=3必填
     * @param $goods // 商品名字
     * @param $guaranteeValueAmount // 保价金额
     * @param $insuranceFee // 保价费
     * @param $order_send_time //预约时间
     * @param $remark //面单备注
     * @param $type // 快递类型
     * @param $senderText // 寄件文本
     * @param $receiveText // 收件文本
     * @param $priceRes // 价格查询结果
     * @return array|\think\response\Json
     */
    public static function createOrder($sender_name, $sender_phone, $senderCity, $sender_address, $receiveName, $receivePhone, $receiveCity, $receiveAddress,
                                       $deliveryType, $goods, $guaranteeValueAmount, $insuranceFee, $order_send_time, $remark, $type, $senderText, $receiveText, $weight, $out_trade_num, $priceRes)
    {

        $data['out_trade_num'] = $out_trade_num;
        $data['sender_name'] = $sender_name;
        $data['sender_phone'] = $sender_phone;
        $data['senderCity'] = $senderCity;
        $data['sender_address'] = $sender_address;
        $data['receiveName'] = $receiveName;
        $data['receivePhone'] = $receivePhone;
        $data['receiveCity'] = $receiveCity;
        $data['receiveAddress'] = $receiveAddress;
        $data['deliveryType'] = $deliveryType;
        $data['goods'] = $goods;
        $data['guaranteeValueAmount'] = $guaranteeValueAmount;
        $data['insuranceFee'] = $insuranceFee;
        $data['order_send_time'] = $order_send_time;
        $data['remark'] = $remark;
        $data['type'] = $type;
        $data['senderText'] = $senderText;
        $data['receiveText'] = $receiveText;
        $data['type'] = $type;
        $data['weight'] = $weight;
        $data['channelPriceA'] = $priceRes['priceA'];
        $data['channelPriceB'] = $priceRes['priceB'];
        $data['channelDiscount'] = $priceRes['discount'];;
        $data['fee'] = $priceRes['fee'];;
        $data['fee1'] = $priceRes['fee1'];;
        $data['serviceCharge'] = $priceRes['serviceCharge'];;
        $data['channelToatlPrice'] = $priceRes['totalFee'];;
        $data['channelName'] = $priceRes['name'];;
        // 计算自己的价格
        $data['totalPrice'] = Expressorder::culTotalPrice($weight, $priceRes)['totalPrice'];
        $data['create_time'] = time();
        $model = new self();
        $model->save($data);
        if (!$aid = $model->id) {
            return rjson(1, '下单失败，请重试！');
        }
        return rjson(0, '下单成功', $model->id);
    }


    //生成支付数据
    public static function create_pay($aid, $payway, $client)
    {
        $order = self::where(['id' => $aid, 'status' => 1])->find();
        if (!$order) {
            return rjson(1, '订单无需支付' . $aid);
        }
        $customer = M('customer')->where(['id' => $order['customer_id']])->find();
        if (!$customer) {
            return rjson(1, '用户数据不存在');
        }
        return PayWay::create($payway, $client, [
            'openid' => $customer['wx_openid'] ? $customer['wx_openid'] : $customer['ap_openid'],
            'body' => $order['body'],
            'order_number' => $order['order_number'],
            'total_price' => $order['total_price'],
            'appid' => $customer['weixin_appid']
        ]);
    }

    public static function notify($order_number, $payway, $serial_number)
    {
        $porder = M('porder')->where(['order_number' => $order_number, 'status' => 1])->find();
        if (!$porder) {
            return rjson(1, '不存在订单');
        }
        Createlog::porderLog($porder['id'], "用户支付回调成功");
        M('porder')->where(['id' => $porder['id'], 'status' => 1])->setField(['status' => 2, 'pay_time' => time(), 'pay_way' => $payway]);
        //api充值队列
        $porder['api_open'] == 1 && queue('app\queue\job\Work@porderSubApi', $porder['id']);
        //发送通知
        Notification::paySus($porder['id']);
        return rjson(0, '回调处理完成');
    }

    //提交接口充值
    public static function subApi($porder_id)
    {
        $porder = M('porder')->where(['id' => $porder_id, 'status' => 2, 'api_open' => 1])->find();
        if (!$porder) {
            return rjson(1, '订单无需提交接口充值');
        }
        //提交充值接口
        Rechargeapi::recharge($porder['id']);
        return rjson(0, '提交接口工作完成');
    }

    //获取当前当充值的API
    public static function getCurApi($porder_id)
    {
        $porder = M('porder')->where(['id' => $porder_id, 'status' => ['in', '2,3'], 'api_open' => 1])->find();
        if (!$porder) {
            return rjson(1, '自动充值订单无效');
        }
        $api_arr = json_decode($porder['api_arr'], true);
        if (count($api_arr) == 0) {
            return rjson(1, '自动充值接口为空');
        }
        if ($porder['api_cur_index'] >= count($api_arr) - 1 && $api_arr[$porder['api_cur_index']]['num'] <= $porder['api_cur_num']) {
            return rjson(1, '无可继续调用的API');
        }
        if ($porder['api_cur_index'] >= 0) {
            $num = $api_arr[$porder['api_cur_index']]['num'];
            $cur_num = $porder['api_cur_num'];
            if ($cur_num >= $num) {
                $index = $porder['api_cur_index'] + 1;
                $cnum = 1;
            } else {
                $index = $porder['api_cur_index'];
                $cnum = $porder['api_cur_num'] + 1;
            }
            return rjson(0, '请继续提交接口充值', ['api' => $api_arr[$index], 'index' => $index, 'num' => $cnum]);
        } else {
            $index = $porder['api_cur_index'] + 1;
            return rjson(0, '请继续提交接口充值', ['api' => $api_arr[$index], 'index' => $index, 'num' => 1]);
        }
    }

    public static function getCurApi2($porder_id)
    {
        $porder = M('porder')->where(['order_number' => $porder_id, 'api_open' => 1])->find();
        if (!$porder) {
            return rjson(1, '自动充值订单无效');
        }
        $api_arr = json_decode($porder['api_arr'], true);
        if (count($api_arr) == 0) {
            return rjson(1, '自动充值接口为空');
        }
        $index = $porder['api_cur_index'];
        return rjson(0, '请继续提交接口充值', ['api' => $api_arr[$index], 'index' => $index, 'num' => 1]);
    }

    //充值成功api
    public static function rechargeSusApi($api, $api_order_number, $data)
    {
        $flag = self::apinotify_log($api, $api_order_number, $data);
        if (!$flag) {
            return rjson(1, '接口已回调过了');
        }
        $porder = M('porder')->where(['api_order_number' => $api_order_number, 'status' => ['in', '2,3']])->find();
        if (!$porder) {
            return rjson(1, '订单未找到');
        }
        return self::rechargeSus($porder['order_number'], "充值成功|接口回调|" . var_export($data, true));
    }

    //充值成功
    public static function rechargeSus($order_number, $remark)
    {
        $porder = M('porder')->where(['order_number' => $order_number, 'status' => ['in', '2,3']])->find();
        if (!$porder) {
            return rjson(1, '订单未找到');
        }
        M('porder')->where(['id' => $porder['id']])->setField(['status' => 4, 'finish_time' => time()]);
        Createlog::porderLog($porder['id'], $remark);
        queue('app\queue\job\Work@callFunc', ['class' => '\app\common\library\Notification', 'func' => 'rechargeSus', 'param' => $porder['id']]);
        self::rebate($porder['id']);
        return rjson(0, '操作成功');
    }

    //充值失败api
    public static function rechargeFailApi($api, $api_order_number, $data)
    {
        $flag = self::apinotify_log($api, $api_order_number, $data);
        if (!$flag) {
            return rjson(1, '接口已回调过了');
        }
        $porder = M('porder')->where(['api_order_number' => $api_order_number, 'status' => ['in', '2,3']])->find();
        if (!$porder) {
            return rjson(1, '订单未找到');
        }
        return self::rechargeFail($porder['order_number'], "充值失败|接口回调|" . var_export($data, true));
    }

    /**
     * 充值失败
     */
    public static function rechargeFail($order_number, $remark)
    {
        $porder = M('porder')->where(['order_number' => $order_number, 'status' => ['in', '2,3']])->find();
        if (!$porder) {
            return rjson(1, '订单未找到');
        }
        //最后有一次api失败时间
        M('porder')->where(['id' => $porder['id']])->setField(['apifail_time' => time()]);

        if ($porder['api_open'] == 1) {
            //继续下一个接口
            $res = Porder::getCurApi($porder['id']);
            if ($res['errno'] == 0) {
                Createlog::porderLog($porder['id'], $remark);
                M('porder')->where(['id' => $porder['id']])->setField(['status' => 2]);
                queue('app\queue\job\Work@porderSubApi', $porder['id']);
                return rjson(0, '处理成功');
            }
        }
        if (C('ODAPI_FAIL_STYLE') == 2) {
            //回到充值中状态
            Createlog::porderLog($porder['id'], $remark);
            M('porder')->where(['id' => $porder['id']])->setField(['status' => 2]);
            Createlog::porderLog($porder['id'], "api失败,订单回到待充值状态");
            return rjson(0, '处理成功');
        }
        //直接订单失败
        return self::rechargeFailDo($order_number, $remark);
    }

    /**
     * 充值失败
     */
    public static function rechargeFailDo($order_number, $remark)
    {
        $porder = M('porder')->where(['order_number' => $order_number, 'status' => ['in', '2,3']])->find();
        if (!$porder) {
            return rjson(1, '订单未找到');
        }
        Createlog::porderLog($porder['id'], $remark);
        M('porder')->where(['id' => $porder['id']])->setField(['status' => 5, 'finish_time' => time()]);
        queue('app\queue\job\Work@callFunc', ['class' => '\app\common\library\Notification', 'func' => 'rechargeFail', 'param' => $porder['id']]);
        C('AUTO_REFUND') == 1 && queue('app\queue\job\Work@porderRefund', ['id' => $porder['id'], 'remark' => $remark, 'operator' => '系统']);
        return rjson(0, '操作成功');
    }

    public static function getApiOrderNumber($order_number, $api_cur_index = 0, $api_cur_count = 0, $num = 1)
    {
        return $order_number . 'A' . $api_cur_count . ($api_cur_index + 1) . 'N' . $num;
    }

    //退款
    public static function refund($order_id, $remark, $operator)
    {
        $porder = M('porder')->where(['id' => $order_id, 'status' => 5])->find();
        if (!$porder) {
            return rjson(1, '未查询到退款订单');
        }
        switch ($porder['pay_way']) {
            case 1://公众号微信支付-退款
                $ret = Wxrefundapi::porder_wxpay_refund($porder['id']);
                break;
            case 2://余额
                $ret = Balance::revenue($porder['customer_id'], $porder['total_price'], "订单:" . $porder['order_number'] . "充值失败退款", Balance::STYLE_REFUND, $operator);
                break;
            case 3://小程序微信支付
                $ret = Wxrefundapi::porder_wxpay_refund($porder['id']);
                break;
            case 4://线下支付
                $ret = rjson(0, '线下支付无需退款');
                break;
            default:
                $ret = rjson(1, '不支持');
        }
        if ($ret['errno'] != 0) {
            Createlog::porderLog($porder['id'], "退款失败|" . $remark);
            return rjson(1, $ret['errmsg']);
        }
        M('porder')->where(['id' => $porder['id']])->setField(['status' => 6, 'refund_time' => time()]);
        Createlog::porderLog($porder['id'], "退款成功|" . $remark);
        Notification::refundSus($porder['id']);
        return rjson(0, "退款成功");
    }

    /**
     * 代理返利计算
     * 下单时进行返利计算
     */
    public static function compute_rebate($porder_id)
    {
        $porder = M('porder')->where(['id' => $porder_id, 'status' => ['in', '1,2'], 'is_del' => 0])->find();
        if (!$porder) {
            return rjson(1, '未找到订单');
        }
        $customer = M('customer')->where(['id' => $porder['customer_id'], 'is_del' => 0, 'status' => 1])->find();
        if (!$customer) {
            return rjson(1, '用户未找到');
        }
        //自身等级价格
        $rebate_id = $customer['f_id'];
        if (!$rebate_id) {
            Createlog::porderLog($porder_id, '不返利,没有上级');
            return rjson(1, '无上级，无需返利');
        }
        //查上级
        $fcus = M('customer')->where(['id' => $customer['f_id'], 'is_del' => 0, 'status' => 1])->find();
        if (!$fcus || $fcus['grade_id'] == $customer['grade_id']) {
            Createlog::porderLog($porder_id, '同级用户，无需给上级返利');
            return rjson(1, '等级无差异无需返利');
        }
        if (in_array($customer['grade_id'], [1, 3]) && M('customer_grade')->where(['is_zdy_price' => 1, 'id' => $fcus['grade_id']])->find()) {
            //如果上级是自定义价格
            $rebate_price = M('customer_hezuo_price')->where(['product_id' => $porder['product_id'], 'customer_id' => $fcus['id']])->value('ranges');
        } else {
            //非自定义价格
            $price_f = M('customer_grade_price')->where(['product_id' => $porder['product_id'], 'grade_id' => $fcus['grade_id']])->find();
            $price_m = M('customer_grade_price')->where(['product_id' => $porder['product_id'], 'grade_id' => $customer['grade_id']])->find();
            $rebate_price = floatval($price_m['ranges'] - $price_f['ranges']);
        }
        if ($rebate_price <= 0) {
            Createlog::porderLog($porder_id, '不返利,计算出金额：' . $rebate_price);
            return rjson(1, '不返利,计算出金额：' . $rebate_price);
        }
        M('porder')->where(['id' => $porder_id])->setField(['rebate_id' => $rebate_id, 'rebate_price' => $rebate_price]);
        Createlog::porderLog($porder_id, '计算返利ID：' . $rebate_id . '，返利金额:￥' . $rebate_price);
        return rjson(0, '返利设置成功');
    }


    /**
     * 返利
     * 代理用户和普通用户
     */
    public static function rebate($porder_id)
    {
        $porder = M('porder')->where(['id' => $porder_id, 'status' => ['in', '4'], 'rebate_id' => ['gt', 0], 'rebate_price' => ['gt', 0], 'is_del' => 0, 'is_rebate' => 0])->find();
        if ($porder) {
            M('porder')->where(['id' => $porder_id])->setField(['is_rebate' => 1, 'rebate_time' => time()]);
            Balance::revenue($porder['rebate_id'], $porder['rebate_price'], '用户充值返利，单号' . $porder['order_number'], Balance::STYLE_REWARDS, '系统');
        }
    }


    //代理excel下单
    public static function agentExcelOrder($id)
    {
        $item = M('agent_proder_excel')->where(['status' => 2, 'id' => $id])->find();
        if (!$item) {
            return rjson(1, '订单不可推送');
        }
        M('agent_proder_excel')->where(['status' => 2, 'id' => $id])->setField(['status' => 3]);
        $res = PorderModel::createOrder($item['mobile'], $item['product_id'], $item['area'], $item['customer_id'], Client::CLIENT_AGA, '导入下单', $item['out_trade_num']);
        if ($res['errno'] != 0) {
            M('agent_proder_excel')->where(['id' => $item['id']])->setField(['status' => 5, 'resmsg' => $res['errmsg']]);
            return rjson(1, '下单失败,' . $res['errmsg']);
        }
        $aid = $res['data'];
        self::compute_rebate($aid);
        Createlog::porderLog($aid, "代理后台批量下单成功");
        $porder = M('porder')->where(['id' => $aid])->field("id,order_number,mobile,product_id,total_price,create_time,guishu,title,out_trade_num")->find();
        $ret = Balance::expend($item['customer_id'], $porder['total_price'], "代理商后台为号码：" . $porder['mobile'] . ",充值产品：" . $porder['title'] . "，单号" . $porder['order_number'], Balance::STYLE_ORDERS, '代理商_导入');
        if ($ret['errno'] != 0) {
            M('agent_proder_excel')->where(['id' => $item['id']])->setField(['status' => 5, 'resmsg' => $ret['errmsg']]);
            return rjson(1, '下单支付失败,' . $res['errmsg']);
        }
        Createlog::porderLog($aid, "余额支付成功");
        $porder = M('porder')->where(['id' => $aid])->field("id,order_number")->find();
        M('agent_proder_excel')->where(['id' => $item['id']])->setField(['status' => 4, 'order_number' => $porder['order_number']]);

        $noticy = new Notify();
        $noticy->balance($porder['order_number']);
        return rjson(1, '下单成功');
    }

    //代理api下单支付
    public static function agentApiPayPorder($porder_id, $customer_id, $notify_url)
    {
        self::where(['id' => $porder_id])->setField(['notify_url' => $notify_url]);
        self::compute_rebate($porder_id);
        Createlog::porderLog($porder_id, "用户下单成功");
        $porder = M('porder')->where(['id' => $porder_id])->field("id,order_number,remark,mobile,product_id,total_price,create_time,guishu,title,out_trade_num")->find();
        $ret = Balance::expend($customer_id, $porder['total_price'], "api为号码：" . $porder['mobile'] . ",充值产品：" . $porder['title'] . "，单号" . $porder['order_number'], Balance::STYLE_ORDERS, '用户自己api');
        if ($ret['errno'] != 0) {
            Createlog::porderLog($porder_id, $ret['errmsg']);
            M('porder')->where(['id' => $porder_id])->setField(['remark' => $porder['remark'] . '|' . $ret['errmsg']]);
            queue('app\queue\job\Work@callFunc', ['class' => '\app\common\library\Notification', 'func' => 'rechargeFail', 'param' => $porder['id']]);
            return rjson($ret['errno'], $ret['errmsg']);
        }
        Createlog::porderLog($porder_id, "余额支付成功");
        $noticy = new Notify();
        $noticy->balance($porder['order_number']);
        return rjson(0, '操作成功');
    }


    //后台excel导入订单
    public static function adminExcelOrder($id)
    {
        $cus = M('customer')->where(['id' => C('PORDER_EXCEL_CUSID'), 'is_del' => 0])->find();
        if (!$cus) {
            return rjson(1, '未找到正确的导入用户ID,点击导入设置配置用户ID');
        }
        $item = M('proder_excel')->where(['id' => $id, 'status' => 2])->find();
        if (!$item) {
            return rjson(1, '不可推送');
        }
        M('proder_excel')->where(['status' => 2, 'id' => $id])->setField(['status' => 3]);
        $res = PorderModel::createOrder($item['mobile'], $item['product_id'], $item['area'], $cus['id'], Client::CLIENT_ADM, '导入下单');
        if ($res['errno'] != 0) {
            M('proder_excel')->where(['id' => $item['id']])->setField(['status' => 5, 'resmsg' => $res['errmsg']]);
            return rjson(1, '下单失败,' . $res['errmsg']);
        }
        $porder = M('porder')->where(['id' => $res['data']])->field("id,order_number")->find();
        M('proder_excel')->where(['id' => $item['id']])->setField(['status' => 4, 'order_number' => $porder['order_number']]);
        $noticy = new Notify();
        $noticy->offline($porder['order_number']);
        return rjson('成功推送');
    }


    //存储日志,并检查是否可以执行回调操作
    public static function apinotify_log($api, $out_trade_no, $data)
    {
        if (!$out_trade_no) {
            return false;
        }
        $log = M('apinotify_log')->where(['api' => $api, 'out_trade_no' => $out_trade_no])->find();
        M('apinotify_log')->insertGetId([
            'api' => $api,
            'out_trade_no' => $out_trade_no,
            'data' => var_export($data, true),
            'create_time' => time()
        ]);
        if ($log) {
            return false;
        } else {
            return true;
        }
    }
}
