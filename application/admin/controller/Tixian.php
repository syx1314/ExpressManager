<?php
/**
 * Created by PhpStorm.
 * User: 13788
 * Date: 2017/11/17
 * Time: 9:43
 */

namespace app\admin\controller;

use app\common\library\SubscribeMessage;
use app\common\library\Templetmsg;
use app\common\library\Transfer;
use app\common\model\Balance;
use app\common\model\Client;

/**
 * @package app\admin\controller
 
 *
 */
class Tixian extends Admin
{
    //提现列表
    public function index()
    {
        $map = $this->create_map();
        $list = M('tixian a')
            ->join('customer c', 'c.id=a.customer_id')
            ->where($map)
            ->field('a.*,c.username')
            ->order("create_time desc")
            ->paginate(C('LIST_ROWS'));
        $total_money = M('tixian a')->join('customer c', 'c.id=a.customer_id')->where($map)->sum('a.money');
        $this->assign('_list', $list);
        $this->assign('_count', $list->total());
        $this->assign('total_money', $total_money);
        return view();
    }

    public function out_excel()
    {
        $map = $this->create_map();
        $ret = M('tixian a')
            ->join('customer c', 'c.id=a.customer_id')
            ->where($map)->field('a.*,c.username')->order("create_time desc")->select();
        //查询需要导出数据
        $field_arr = array(
            ['title' => '申请人', 'field' => 'username'],
            ['title' => '姓名', 'field' => 'name'],
            ['title' => '账号', 'field' => 'acount'],
            ['title' => '方式', 'field' => 'style'],
            ['title' => '金额', 'field' => 'money'],
            ['title' => '备注', 'field' => 'remark'],
            ['title' => '提交时间', 'field' => 'create_time'],
            ['title' => '状态', 'field' => 'status'],
            ['title' => '处理时间', 'field' => 'deal_time']
        );
        foreach ($ret as $key => $vo) {
            $ret[$key]['username'] = '[' . $vo['id'] . ']' . $vo['username'];
            $ret[$key]['style'] = C('TIXIAN_STYLE')[$vo['style']];
            $ret[$key]['create_time'] = time_format($vo['create_time']);
            $ret[$key]['deal_time'] = time_format($vo['deal_time']);
            $ret[$key]['status'] = C('TIXIAN_STATUS')[$vo['status']];
        }
        exportToExcel('提现报表' . time(), $field_arr, $ret);
    }

    private function create_map()
    {
        $map = [];
        if (I('key')) {
            $map['a.remark|a.customer_id|c.username'] = array('like', '%' . I('key') . '%');
        }
        if (I('status')) {
            $map['a.status'] = I('status');
        }
        if (I('end_time') && I('begin_time')) {
            $map['a.create_time'] = array('between', [strtotime(I('begin_time')), strtotime(I('end_time'))]);
        }
        return $map;
    }


    public function shenhe()
    {
        $status = I('status');
        $ids = I('id/a');
        $style = I('style');
        $tixians = M('tixian')->where(['id' => ['in', $ids], 'status' => ['in', '1']])->select();
        if (!$tixians) {
            return $this->error('未查询到需要操作的提现');
        }
        $counts = 0;
        $errmsg = '';
        foreach ($tixians as $tixian) {
            $cus = M('customer')->where(['id' => $tixian['customer_id']])->find();
            if ($status == 2) {
                if ($style == 1) {
                    //微信
                    $ret = Transfer::wx_transfers($cus['weixin_appid'], $cus['wx_openid'] ? $cus['wx_openid'] : $cus['ap_openid'], $tixian['order_number'], intval($tixian['money'] * 100));
                    if ($ret['errno'] != 0) {
                        $errmsg .= $ret['errmsg'] . ';';
                        continue;
                    }
                    $reason = "已转入您的微信钱包余额啦！";
                } elseif ($style == 3) {
                    //手动成功
                    $reason = "已转入您提交的" . C('TIXIAN_STYLE')[$tixian['style']] . "的账户中！";
                } else {
                    return $this->error('未知的提现渠道');
                }
                $cus['client'] == Client::CLIENT_WX && Templetmsg::tixianSh($tixian['customer_id'], "您好，申请的提现已到账啦～", $cus['username'], time_format($tixian['create_time']), $tixian['money'], $reason);
                $cus['client'] == Client::CLIENT_MP && SubscribeMessage::tixianSh($tixian['customer_id'], time_format(time()), '审核通过', $reason);
                M('tixian')->where(['id' => $tixian['id']])->setField(['status' => 2, 'deal_time' => time()]);
                $counts++;
            } else {
                $reason = I('prompt_remark');
                if (M('tixian')->where(['id' => $tixian['id']])->setField(['status' => 3, 'deal_time' => time()])) {
                    Balance::revenue($tixian['customer_id'], $tixian['money'], '提现失败退款', Balance::STYLE_REFUND, '管理员');
                    $cus['client'] == Client::CLIENT_WX && Templetmsg::tixianSh($tixian['customer_id'], "您好，申请的提现处理失败，已退回钱包余额～", $cus['username'], time_format($tixian['create_time']), $tixian['money'], $reason);
                    $cus['client'] == Client::CLIENT_MP && SubscribeMessage::tixianSh($tixian['customer_id'], time_format(time()), '审核不通过', $reason);
                }
                $counts++;
            }
        }
        if ($counts == 0) {
            return $this->error('操作失败,' . $errmsg);
        }
        return $this->success("成功处理" . $counts . "条");
    }

}