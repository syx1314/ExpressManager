<?php
namespace app\admin\controller;

use app\common\library\Createlog;
use app\common\library\Notification;
use app\common\model\Porder as PorderModel;
use app\common\model\Product as ProductModel;
use Recharge\Qbd;
use think\Log;

/**
 *  快递订单
 * @package app\admin\controller
 *
 */
class Expressorder extends Admin
{
    //订单列表
    public function index()
    {
        $map = $this->create_map();
        if (I('sort')) {
            $sort = I('sort');
        } else {
            $sort = "id desc";
        }
        $list = M('expressorder')->where($map)->field('*')->order($sort)->paginate(C('LIST_ROWS'));
        $this->assign('total_price', M('expressorder')->where($map)->sum("totalPrice"));
        $this->assign('_list', $list);
        $this->assign('_total', $list->total());
        return view();
    }
    //远程更新渠道订单信息
    public function updateRemoteOrder() {
        if (I('channel_order_id') && I('type')) {
              $res=$this->fetchRemoteOrder(I('channel_order_id'),I('type'));
                if ($res['errno'] == 0){
                    return $this->success('更新成功');
                }else{
                    return $this->error('失败原因'.$res['errmsg']);
                }
        }else {
            return $this->error('参数有误');
        }
    }

    /**
     * @param $channel_order_id  渠道id
     * @param $type  快递类型
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    private function fetchRemoteOrder($channel_order_id,$type) {
        if ($channel_order_id && $type) {
            $qbd = new Qbd();
            $channelOrderInfo= $qbd->checkOrder($channel_order_id,$type);
            // 返回的数组
            if ($channelOrderInfo && $channelOrderInfo['errno']==0) {
                $channelOrder= $channelOrderInfo['data']['order'];
                $order['trackingNum'] = $channelOrder['waybillNo'];
                $order['volume'] = $channelOrder['volume'];
                $order['volumeWeight'] = $channelOrder['volumeWeight'];
                $order['weightActual'] = $channelOrder['weightFinal'];// 实际重量
                $order['weightBill'] = $channelOrder['weightFee'];// 计费重量
                $order['guaranteeValueAmount'] = $channelOrder['insuredValue'];// 保价价格
                $order['insuranceFee'] = $channelOrder['insuredFee'];// 保价费
                $order['channelToatlPrice'] = $channelOrder['total'];// 渠道总价格
                $order['status'] = $channelOrder['status'];// 运单状态
                $order['statusName'] = $channelOrder['orderStatus'];// 运单状态
                $order['overWeightStatus'] = $channelOrder['overweightStatus'];// 1 超重 2 超重/耗材/保价/转寄/加长已处理  3 超轻
                $order['otherFee'] = $channelOrder['otherFee'];// 其它费用
                $order['consumeFee'] = $channelOrder['consumables'];// 耗材费用
                $order['serviceCharge'] = $channelOrder['serviceCharge'];// 服务费
                $order['soliciter'] = $channelOrder['soliciter'];// 揽件员

                M('expressorder') ->where(['channel_order_id'=>I('channel_order_id')])->update($order);
                $order['traceList'] = $channelOrderInfo['data']['traceList'];//轨迹
                return rjson(0,'拉取订单成功',$order);
            }else{
                return rjson(1,channelOrderInfo['errmsg'],null);
            }
        }else {
            return rjson(1,'参数 有误',null);
        }
    }
    //物流轨迹
    public function express_trail() {
        $expressorder = M('expressorder')->where(['id'=>I('id')])->find();
        if ($expressorder&& $expressorder['channel_order_id'] && $expressorder['type']) {
            $res=$this->fetchRemoteOrder($expressorder['channel_order_id'],$expressorder['type']);
            echo  json_encode(  array_merge($expressorder,$res['data']));
            if ($res['errno'] == 0){
                $this->assign('order', array_merge($expressorder,$res['data']));
                return view();
            }else{
                echo '拉取远程单子失败'.res['errmsg'];
            }
        }else{
            echo '找不到渠道单子';
        }
    }


    public function log()
    {
        $list = M('porder_log')->where(['porder_id' => I('id')])->order("id asc")->select();
        $this->assign('_list', $list);
        return view();
    }



    //退款
    public function refund()
    {
        $ids = I('id/a');
        $porders = M('porder')->where(['id' => ['in', $ids], 'status' => ['in', '5']])->select();
        if (!$porders) {
            return $this->error('未查询到订单');
        }
        $counts = 0;
        $errmsg = '';
        foreach ($porders as $porder) {
            $ret = PorderModel::refund($porder['id'], "后台|" . session('user_auth')['nickname'], '管理员：' . session('user_auth')['nickname']);
            if ($ret['errno'] == 0) {
                $counts++;
            } else {
                $errmsg .= $ret['errmsg'] . ';';
            }
        }
        if ($counts == 0) {
            return $this->error('操作失败,' . $errmsg);
        }
        return $this->success("成功处理" . $counts . "条");
    }

    //设置充值成功
    public function set_chenggong()
    {
        $ids = I('id/a');
        $porders = M('porder')->where(['id' => ['in', $ids], 'status' => ['in', '2,3']])->select();
        if (!$porders) {
            return $this->error('未查询到订单');
        }
        $counts = 0;
        $errmsg = '';
        foreach ($porders as $porder) {
            $ret = PorderModel::rechargeSus($porder['order_number'], "充值成功|后台|" . session('user_auth')['nickname']);
            if ($ret['errno'] == 0) {
                $counts++;
            } else {
                $errmsg .= $ret['errmsg'] . ';';
            }
        }
        if ($counts == 0) {
            return $this->error('操作失败,' . $errmsg);
        }
        return $this->success("成功处理" . $counts . "条");
    }

    //设置充值中
    public function set_czing()
    {
        $ids = I('id/a');
        $porders = M('porder')->where(['id' => ['in', $ids], 'status' => ['in', '2']])->select();
        if (!$porders) {
            return $this->error('未查询到订单');
        }
        $counts = 0;
        $errmsg = '';
        foreach ($porders as $porder) {
            Createlog::porderLog($porder['id'], "将订单设置为充值中|后台|" . $this->adminuser['nickname']);
            M('porder')->where(['id' => $porder['id']])->setField(['status' => 3]);
            $counts++;
        }
        if ($counts == 0) {
            return $this->error('操作失败,' . $errmsg);
        }
        return $this->success("成功处理" . $counts . "条");
    }

    //设置充值失败
    public function set_shibai()
    {
        $ids = I('id/a');
        $porders = M('porder')->where(['id' => ['in', $ids], 'status' => ['in', '2,3']])->select();
        if (!$porders) {
            return $this->error('未查询到订单');
        }
        $counts = 0;
        $errmsg = '';
        foreach ($porders as $porder) {
            $ret = PorderModel::rechargeFailDo($porder['order_number'], "充值失败|后台|" . session('user_auth')['nickname']);
            if ($ret['errno'] == 0) {
                $counts++;
            } else {
                $errmsg .= $ret['errmsg'] . ';';
            }
        }
        if ($counts == 0) {
            return $this->error('操作失败,' . $errmsg);
        }
        return $this->success("成功处理" . $counts . "条");
    }

    //回调通知
    public function notification()
    {
        $porder = M('porder')->where(['id' => I('id'), 'status' => ['in', '4,5,6']])->find();
        if (!$porder) {
            return $this->error('未查询到可回调订单');
        }
        if ($porder['status'] == 4) {
            $ret = Notification::rechargeSus($porder['id']);
        } else {
            $ret = Notification::rechargeFail($porder['id']);
        }
        if ($ret['errno'] != 0) {
            return $this->error($ret['errmsg']);
        }
        return $this->success($ret['errmsg']);
    }

    private function create_map()
    {
        $map = [];
        if (I('status')) {
            $map['status'] = intval(I('status'));
        }
        if (I('type')) {
            $map['type'] = I('type');
            $cates = M('product_cate')->where(['type' => I('type')])->select();
            $this->assign('cates', $cates);
        }
        return $map;
    }


    public function delete_porder_excel()
    {
        if (M('proder_excel')->where(['id' => I('id')])->delete()) {
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败');
        }
    }

    public function empty_porder_excel()
    {
        $map['status'] = I('status');
        M('proder_excel')->where($map)->delete();
        return $this->success('清空成功');
    }

    /**
     * 导出记录
     */
    public function out_excel()
    {
        // 查询导出数据
        $map = $this->create_map();
        $ret = M('porder')->where($map)->order("create_time desc")->select();
        //查询需要导出数据
        $field_arr = array(
            array('title' => '单号', 'field' => 'order_number'),
            array('title' => '商户单号', 'field' => 'out_trade_num'),
            array('title' => '类型', 'field' => 'type'),
            array('title' => '产品ID', 'field' => 'product_id'),
            array('title' => '产品', 'field' => 'product_name'),
            array('title' => '手机', 'field' => 'mobile'),
            array('title' => '客户ID', 'field' => 'customer_id'),
            array('title' => '客户端', 'field' => 'client'),
            array('title' => '归属地', 'field' => 'guishu'),
            array('title' => '运营商', 'field' => 'isp'),
            array('title' => '状态', 'field' => 'status'),
            array('title' => '总金额', 'field' => 'total_price'),
            array('title' => '支付方式', 'field' => 'pay_way'),
            array('title' => '支付时间', 'field' => 'pay_time'),
            array('title' => '下单时间', 'field' => 'create_time'),
            array('title' => '回调地址', 'field' => 'notify_url'),
            array('title' => '回调时间', 'field' => 'notification_time'),
        );
        foreach ($ret as $key => $vo) {
            $ret[$key]['type'] = C('PRODUCT_TYPE')[$vo['type']];
            $ret[$key]['status'] = C('PORDER_STATUS')[$vo['status']];
            $ret[$key]['pay_way'] = C('PAYWAY')[$vo['pay_way']];
            $ret[$key]['client'] = C('CLIENT_TYPE')[$vo['client']];
            $ret[$key]['pay_time'] = time_format($vo['pay_time']);
            $ret[$key]['create_time'] = time_format($vo['create_time']);
            $ret[$key]['notification_time'] = time_format($vo['notification_time']);
        }
        exportToExcel('充值订单报表' . time(), $field_arr, $ret);
    }
}
