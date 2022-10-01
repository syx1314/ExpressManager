<?php
namespace app\admin\controller;

use app\common\library\Createlog;
use app\common\library\Notification;
use app\common\model\Expressorder as ExorderModel;
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

        $list = M('expressorder')
            ->alias('ex') ->where($map)->field('ex.*,(select username from dyr_customer where id=ex.userid) as username')->order($sort)->paginate(C('LIST_ROWS'));
        $dataList = $list->items();
        for ($i=0; $i < count($dataList); $i++) {
            $billList= M('expressorder_bill')->field('*')->where(['order_number'=>$dataList[$i]['out_trade_num']])->select();
            $dataList[$i]['billList'] = $billList;

        }

//        $testList =  Db::query("select * from dyr_expressorder as a left join dyr_expressorder_bill as b on a.out_trade_num = b.order_number;");
        $this->assign('total_price', M('expressorder')->where($map)->sum("totalPrice"));
        $this->assign('_list', $list);
        $this->assign('_dataList', $dataList);
        $this->assign('_total', $list->total());
        return view();
    }
    //远程更新渠道订单信息
    public function updateRemoteOrder() {
        if (I('channel_order_id') && I('type')) {
            $res=ExorderModel::fetchRemoteOrder(I('channel_order_id'),I('type'));
            if ($res['errno'] == 0){
                return $this->success('更新成功');
            }else{
                return $this->error('失败原因'.$res['errmsg']);
            }
        }else {
            return $this->error('参数有误');
        }
    }


    //物流轨迹
    public function express_trail() {
        $expressorder = M('expressorder')->where(['id'=>I('id')])->find();
        if ($expressorder&& $expressorder['channel_order_id'] && $expressorder['type']) {
            $res=ExorderModel::fetchRemoteOrder($expressorder['channel_order_id'],$expressorder['type']);
            if ($res['errno'] == 0){
                $this->assign('order', array_merge($expressorder,$res['data']));
                return view();
            }else{
                echo '拉取远程单子失败'.$res['errmsg'];
            }
        }else{
            echo '找不到渠道单子';
        }
    }
    //远程生单
    public function createChannelOrder() {
        if (I('id')) {
            $res=  ExorderModel::createChannelExpress(I('id'));
            if ($res['errno'] == 0) {
                return $this->success('远程生单成功');
            }else{
                return $this->success('远程生单失败:'.$res['errmsg']);
            }
        }else {
            return $this->error('参数有误');
        }
    }

    // 取消订单
    public function cancelOrder() {
        if (I('id') ) {
            $res=  ExorderModel::cancelOrder(I('id'));
            if ($res['errno'] == 0) {
                Createlog::expressOrderLog(I('out_trade_num'),"后台|取消订单成功" . session('user_auth')['nickname'], '管理员：' . session('user_auth')['nickname']);

                // TODO: 可以执行 退款了
                $ret = ExorderModel::refund(I('id'),"后台|" . session('user_auth')['nickname'], '管理员：' . session('user_auth')['nickname']);
                if ($ret['errno'] == 0) {
                    Createlog::expressOrderLog(I('out_trade_num'),"后台|退款成功" . session('user_auth')['nickname'], '管理员：' . session('user_auth')['nickname']);

                    return $this->success('取消远程订单成功 | 退款成功');
                }else {
                    Createlog::expressOrderLog(I('out_trade_num'),"后台|退款失败原因:".$ret['errmsg']. session('user_auth')['nickname'], '管理员：' . session('user_auth')['nickname']);

                    return $this->success('取消远程订单成功 | 退款失败原因:'.$ret['errmsg']);
                }
            }else{
                Createlog::expressOrderLog(I('out_trade_num'),"后台|取消订单失败原因:".$res['errmsg'] . session('user_auth')['nickname'], '管理员：' . session('user_auth')['nickname']);
                return $this->success('取消远程订单失败:'.$res['errmsg']);
            }
        }else {
            return $this->error('参数有误');
        }
    }
    public function log()
    {
        $list = M('expressorder_log')->where(['out_trade_num' => I('out_trade_num')])->order("id asc")->select();
        $this->assign('_list', $list);
        return view();
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
        if (I('userid')) {
            $map['userid'] = intval(I('userid'));
        }
        if (I('userid')) {
            $map['userid'] = intval(I('userid'));
        }
        if (I('out_trade_num')) {
            $map['out_trade_num'] = intval(I('out_trade_num'));
        }
        if (I('sender_phone')) {
            $map['sender_phone'] = intval(I('sender_phone'));
        }
        if (I('type')) {
            $map['type'] = I('type');
            $cates = M('product_cate')->where(['type' => I('type')])->select();
            $this->assign('cates', $cates);
        }
        return $map;
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
