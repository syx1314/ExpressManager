<?php
/**
 * Created by PhpStorm.
 * User: 13788
 * Date: 2017/11/17
 * Time: 9:43
 */

namespace app\admin\controller;

use app\common\library\Createlog;
use app\common\library\Notification;
use app\common\model\Porder as PorderModel;
use app\common\model\Product as ProductModel;

/**
 * Class Member
 * @package app\admin\controller
 
 *
 */
class Porder extends Admin
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
        $list = M('porder')->where($map)->field("*,(select username from dyr_customer where id=customer_id) as username")->order($sort)->paginate(C('LIST_ROWS'));
        $this->assign('total_price', M('porder')->where($map)->sum("total_price"));
        $this->assign('cost', M('porder')->where($map)->sum("cost"));
        $this->assign('reapi', M('reapi')->where(['is_del' => 0])->select());
        $this->assign('_list', $list);
        $this->assign('_total', $list->total());
        return view();
    }

    //批量提交api
    public function batch_api()
    {
        if (request()->isPost()) {
            $ids = I('ids');
            $pnum = M('porder')->where(['id' => ['in', $ids]])->count();
            if ($ids == '' || $pnum == 0) {
                return $this->error('没有选择订单');
            }
            $param_ids = I('reapi_param_id/a');
            if (!$param_ids || count($param_ids) == 0) {
                return $this->error('没有选择api');
            }
            $api_arr = M('reapi_param rp')->join('reapi r', 'r.id=rp.reapi_id')->where(['rp.id' => ['in', $param_ids]])->field('rp.reapi_id,rp.id as param_id,1 as num')->orderRaw('find_in_set(param_id,"' . implode(',', $param_ids) . '")')->select();
            if (!$api_arr) {
                return $this->error('未找到对应的api', 'back');
            }
            queue('app\queue\job\Work@pordersSubApi', ['ids' => explode(',', $ids), 'api_arr' => $api_arr, 'op' => $this->adminuser['nickname']]);
            return $this->success('推送任务提交成功');
        } else {
            $map = $this->create_map();
            $list = M('porder')->where($map)->field("id")->select();
            if (count($list) == 0) {
                return $this->error('没有选择订单');
            }
            $ids = array_column($list, 'id');
            $this->assign('_total', count($ids));
            $this->assign('_ids', implode(',', $ids));
            $this->assign('reapi', M('reapi')->where(['is_del' => 0])->order('id desc')->select());
            return view();
        }
    }

    public function log()
    {
        $list = M('porder_log')->where(['porder_id' => I('id')])->order("id asc")->select();
        $this->assign('_list', $list);
        return view();
    }


    //删除
    public function deletes()
    {
        if (M('porder')->where(['id' => I('id')])->setField(['is_del' => 1])) {
            Createlog::porderLog(I('id'), "删除成功|后台|" . session('user_auth')['nickname']);
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败');
        }
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
        $map['is_del'] = 0;
        if ($key = trim(I('key'))) {
            $query_name = I('query_name');
            if ($query_name) {
                if (strpos($query_name, '.') !== false) {
                    $qu_arr = explode('.', $query_name);
                    $qu_rets = M($qu_arr[0])->where([$qu_arr[1] => $key])->field('id')->select();
                    $map[$qu_arr[2]] = ['in', array_column($qu_rets, 'id')];
                } else {
                    $map[$query_name] = $key;
                }
            } else {
                $map['order_number|title|product_name|mobile|out_trade_num'] = ['like', '%' . $key . '%'];
            }
        }
        if (I('status')) {
            $map['status'] = intval(I('status'));
        } else {
            $map['status'] = ['gt', 1];
        }
        if (I('type')) {
            $map['type'] = I('type');
            $cates = M('product_cate')->where(['type' => I('type')])->select();
            $this->assign('cates', $cates);
            if (I('cate')) {
                $product = M('product')->where(['cate_id' => I('cate'), 'is_del' => 0])->select();
                $this->assign('products', $product);
                if (I('product_id')) {
                    $map['product_id'] = I('product_id');
                }
            }
        }
        if (I('isp')) {
            $map['isp'] = getISPText(I('isp'));
        }
        if (I('client')) {
            $map['client'] = I('client');
        }
        if (I('reapi_id')) {
            $map['api_cur_id'] = I('reapi_id');
        }
        if (I('pay_way')) {
            $map['pay_way'] = I('pay_way');
        }
        if (I('customer_id')) {
            $map['customer_id'] = I('customer_id');
        }
        if (I('end_time') && I('begin_time')) {
            $time_style = I('time_style') ? I('time_style') : 'create_time';
            $map[$time_style] = array('between', [strtotime(I('begin_time')), strtotime(I('end_time'))]);
        }
        if (I('batch_mobile')) {
            $batch_order_number = str_replace(' ', '', str_replace(array("\r\n", "\r", "\n"), ",", I('batch_mobile')));
            $bt_mo = preg_grep('/\S+/', explode(',', $batch_order_number));
            $bt_mo && $map['mobile'] = array("in", $bt_mo);
        }
        return $map;
    }

    //导入excel
    public function in_excel()
    {
        if (request()->isPost()) {
            set_time_limit(0);
            vendor("phpexcel.PHPExcel");
            $file = request()->file('excel');
            if (empty($file)) {
                return $this->error('请选择上传文件');
            }
            $info = $file->validate(['size' => C('DOWNLOAD_UPLOAD.maxSize'), 'ext' => C('DOWNLOAD_UPLOAD.exts')])->move(C('DOWNLOAD_UPLOAD.movePath'));
            if ($info) {
                $exclePath = $info->getSaveName();  //获取文件名
                $file_name = C('DOWNLOAD_UPLOAD.movePath') . DS . $exclePath;//上传文件的地址
                $objReader = \PHPExcel_IOFactory::createReader("Excel2007");
                $obj_PHPExcel = $objReader->load($file_name, $encode = 'utf-8');  //加载文件内容,编码utf-8
                $excel_array = $obj_PHPExcel->getSheet(0)->toArray();   //转换为数组格式
                array_shift($excel_array);  //删除第一个数组(标题);
                //验证下单者
                $cus = M('customer')->where(['id' => C('PORDER_EXCEL_CUSID'), 'is_del' => 0])->find();
                if (!$cus) {
                    return $this->error('未找到正确的导入用户ID,点击导入设置配置用户ID');
                }
                $tirow = [];
                foreach ($excel_array as $k => $v) {
                    $product_id = $v[0];
                    $mobile = $v[1];
                    $remark = $v[2];
                    $out_trade_num = isset($v[3]) ? $v[3] : '';
                    $area = isset($v[4]) ? $v[4] : '';
                    $hangstr = '  #第[' . ($k + 2) . '行]';

                    $map['p.id'] = $product_id;
                    $map['p.added'] = 1;
                    $product = ProductModel::getProduct($map, $cus['id']);
                    if (!$product) {
                        return $this->error('未找到匹配的充值产品，套餐id:' . $product_id . '，手机：' . $mobile . '，' . $hangstr, '', '', 20);
                    }
                    $tirow[$k]['product_id'] = $product_id;
                    $tirow[$k]['mobile'] = $mobile;
                    $tirow[$k]['remark'] = $remark;
                    $tirow[$k]['area'] = $area;
                    $tirow[$k]['product_name'] = $product['name'];
                    $tirow[$k]['total_price'] = $product['price'];
                    $tirow[$k]['product_desc'] = $product['desc'];
                    $tirow[$k]['api_open'] = $product['api_open'];
                    $tirow[$k]['api_arr'] = $product['api_arr'];
                    $tirow[$k]['api_cur_index'] = -1;
                    $tirow[$k]['type'] = $product['type'];
                    $tirow[$k]['status'] = 1;
                    $tirow[$k]['hang'] = $k + 2;
                    $tirow[$k]['out_trade_num'] = $out_trade_num;
                    $tirow[$k]['create_time'] = time();
                }
                $sh = M('proder_excel')->insertAll($tirow);
                return $this->success('成功导入' . $sh . '条,即将刷新', U('porder_excel', ['status' => 1]));
            } else {
                return $this->error($file->getError());
            }
        } else {
            return $this->error('错误的请求方式');
        }
    }

    public function porder_excel()
    {
        $map = [];
        $status = I('?status') ? I('status') : 1;
        if ($status) {
            $map['status'] = $status;
        }
        $list = M('proder_excel')->where($map)->select();
        $alljy_pt = 0;
        $alljy_dr = 0;
        $total_price = 0;
        foreach ($list as &$item) {
            $item['ptjiaoyan'] = floatval($item['product_name']);
            $item['drjiaoyan'] = floatval($item['remark']);
            $item['jy_jg'] = $item['ptjiaoyan'] == $item['drjiaoyan'] ? 1 : 0;
            $alljy_dr += $item['drjiaoyan'];
            $alljy_pt += $item['ptjiaoyan'];
            $total_price += $item['total_price'];
        }
        $this->assign('alljy_pt', $alljy_pt);
        $this->assign('alljy_dr', $alljy_dr);
        $this->assign('alljy_jg', $alljy_pt == $alljy_dr ? 1 : 0);
        $this->assign('total_price', $total_price);
        $this->assign('reapi', M('reapi')->where(['is_del' => 0])->select());
        $this->assign('_list', $list);
        $this->assign('status', $status);
        return view();
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

    public function push_excel()
    {
        set_time_limit(0);
        $cus = M('customer')->where(['id' => C('PORDER_EXCEL_CUSID'), 'is_del' => 0])->find();
        if (!$cus) {
            return $this->error('未找到正确的导入用户ID,点击导入设置配置用户ID');
        }
        $list = M('proder_excel')->where(['status' => 1])->field('id')->select();
        if (!$list) {
            return $this->error('没有可推送的数据');
        }
        M('proder_excel')->where(['id' => ['in', array_column($list, 'id')]])->setField(['status' => 2]);
        queue('app\queue\job\Work@adminPushExcel', $list);
        return $this->success('成功确认' . count($list) . '条！请刷新待推送列表查看', U('porder_excel', ['status' => 2]));
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