<?php
/**
 * Created by PhpStorm.
 * User: 13788
 * Date: 2017/11/17
 * Time: 9:43
 */

namespace app\agent\controller;

use app\api\controller\Notify;
use app\common\library\Createlog;
use app\common\library\Notification;
use app\common\model\Balance;
use app\common\model\Client;
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
        $list = M('porder')->where($map)->order("id desc")->paginate(C('LIST_ROWS'));
        $this->assign('total_price', M('porder')->where($map)->sum("total_price"));
        $this->assign('_list', $list);
        $this->assign('_total', $list->total());
        return view();
    }

    private function create_map()
    {
        $map['is_del'] = 0;
        $map['customer_id'] = $this->user['id'];
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
                $map['order_number|title|product_name|mobile|out_trade_num|api_order_number'] = ['like', '%' . $key . '%'];
            }
        }
        if (I('status')) {
            $map['status'] = ['in', I('status')];
        } else {
            if (I('status2')) {
                $map['status'] = ['in', I('status2')];
            } else {
                $map['status'] = ['gt', 1];
            }
        }
        if (I('is_notification')) {
            $map['is_notification'] = intval(I('is_notification')) - 1;
        }
        if (I('type')) {
            $map['type'] = I('type');
        }
        if (I('isp')) {
            $map['isp'] = getISPText(I('isp'));
        }
        if (I('end_time') && I('begin_time')) {
            $map['create_time'] = array('between', [strtotime(I('begin_time')), strtotime(I('end_time'))]);
        }
        if (I('excel_id')) {
            $porderes = M('agent_proder_excel')->where(['customer_id' => $this->user['id'], 'excel_id' => I('excel_id')])->field('order_number')->select();
            $map['order_number'] = ['in', array_column($porderes, 'order_number')];
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
            array('title' => '系统单号', 'field' => 'order_number'),
            array('title' => '商户单号', 'field' => 'out_trade_num'),
            array('title' => '类型', 'field' => 'type'),
            array('title' => '产品ID', 'field' => 'product_id'),
            array('title' => '产品', 'field' => 'product_name'),
            array('title' => '手机', 'field' => 'mobile'),
            array('title' => '归属地', 'field' => 'guishu'),
            array('title' => '运营商', 'field' => 'isp'),
            array('title' => '状态', 'field' => 'status'),
            array('title' => '总金额', 'field' => 'total_price'),
            array('title' => '支付时间', 'field' => 'pay_time'),
            array('title' => '下单时间', 'field' => 'create_time'),
            array('title' => '完成时间', 'field' => 'finish_time'),
            array('title' => '回调地址', 'field' => 'notify_url'),
            array('title' => '回调时间', 'field' => 'notification_time'),
            array('title' => '凭证', 'field' => 'voucher'),
        );
        foreach ($ret as $key => $vo) {
            $ret[$key]['type'] = C('PRODUCT_TYPE')[$vo['type']];
            $ret[$key]['status'] = C('PORDER_STATUS')[$vo['status']];
            $ret[$key]['pay_time'] = time_format($vo['pay_time']);
            $ret[$key]['create_time'] = time_format($vo['create_time']);
            $ret[$key]['finish_time'] = time_format($vo['finish_time']);
            $ret[$key]['notification_time'] = time_format($vo['notification_time']);
            $ret[$key]['voucher'] = $vo['status'] == 4 ? C('WEB_URL') . "yrapi.php/open/voucher/id/" . $vo['id'] . '.html' : '';
        }
        exportToExcel('充值订单报表' . time(), $field_arr, $ret);
    }

    public function orders()
    {
        $areas = M('electricity_city')->order('initial asc,sort asc')->select();
        $this->assign('areas', $areas);
        return view();
    }

    public function get_product()
    {
        $map = ['p.is_del' => 0, 'p.added' => 1];
        $data = [];
        $types = C('PRODUCT_TYPE');
        foreach ($types as $key => $type) {
            $map['p.type'] = $key;
            $lists = ProductModel::getProducts($map, $this->user['id']);
            $data[] = [
                'name' => $type,
                'lists' => $lists
            ];
        };
        return djson(0, 'ok', ['product' => $data]);
    }

    //查询归属地
    public function get_guishu()
    {
        $mobile = I('mobile');
        $guishu = QCellCore($mobile);
        if ($guishu['errno'] != 0) {
            return djson($guishu['errno'], $guishu['errmsg']);
        }
        return djson(0, 'ok', $guishu['data']);
    }

    /**
     * 下单
     */
    public function create_order()
    {
        $mobile = I('mobile');
        $product_id = I('product_id');
        $area = I('area');
        $res = PorderModel::createOrder($mobile, $product_id, $area, $this->user['id'], Client::CLIENT_AGA, '代理商后台下单');
        if ($res['errno'] != 0) {
            return djson($res['errno'], $res['errmsg'], $res['data']);
        }
        $aid = $res['data'];
        PorderModel::compute_rebate($aid);
        Createlog::porderLog($aid, "代理下单成功");
        $porder = M('porder')->where(['id' => $aid])->field("id,order_number,mobile,product_id,total_price,create_time,guishu,title,out_trade_num")->find();
        $ret = Balance::expend($this->user['id'], $porder['total_price'], "代理商后台为号码：" . $porder['mobile'] . ",充值产品：" . $porder['title'] . "，单号" . $porder['order_number'], Balance::STYLE_ORDERS, '代理商_手工');
        if ($ret['errno'] != 0) {
            return djson($ret['errno'], $ret['errmsg']);
        }
        Createlog::porderLog($aid, "余额支付成功");
        $noticy = new Notify();
        $noticy->balance($porder['order_number']);
        return djson(0, "下单成功");
    }

    public function order_batch()
    {
        return view();
    }

    //导入记录
    public function excels()
    {
        $map['customer_id'] = $this->user['id'];
        if (I('key')) {
            $map['name'] = ['like', '%' . I('key') . '%'];
        }
        if (I('type')) {
            $map['type'] = I('type');
        }
        if (I('end_time') && I('begin_time')) {
            $map['create_time'] = array('between', [strtotime(I('begin_time')), strtotime(I('end_time'))]);
        }
        $list = M('agent_excel')
            ->where($map)->order('id desc')
            ->paginate(C('LIST_ROWS'))
            ->each(function ($item, $key) {
                $es = M('agent_proder_excel')->where(['excel_id' => $item['id']])->field('order_number')->select();
                $item['all_count'] = M('agent_proder_excel')->where(['excel_id' => $item['id']])->count();
                $item['weidao_count'] = M('agent_proder_excel')->where(['excel_id' => $item['id'], 'order_number' => null])->count();
                $porders = M('porder')->where(['customer_id' => $this->user['id'], 'order_number' => ['in', array_column($es, 'order_number')]])->select();
                $item['daoru_count'] = count($porders);
                $item['ing_count'] = M('porder')->where(['id' => ['in', array_column($porders, 'id')], 'status' => ['in', '2,3']])->count();
                $item['sus_count'] = M('porder')->where(['id' => ['in', array_column($porders, 'id')], 'status' => ['in', '4']])->count();
                $item['fail_count'] = M('porder')->where(['id' => ['in', array_column($porders, 'id')], 'status' => ['in', '5,6']])->count();
                $item['total_price'] = M('porder')->where(['id' => ['in', array_column($porders, 'id')], 'status' => ['in', '2,3,4,5,6']])->sum('total_price');
                $item['refund_price'] = M('porder')->where(['id' => ['in', array_column($porders, 'id')], 'status' => ['in', '6']])->sum('total_price');
                return $item;
            });
        $this->assign('_list', $list);
        return view();
    }

    //导入记录
    public function excels_out()
    {
        $exc = M('agent_excel')->where(['id' => I('id'), 'customer_id' => $this->user['id']])->find();
        if (!$exc) {
            return $this->error('没有该导入文件');
        }
        $porderes = M('agent_proder_excel')->where(['excel_id' => $exc['id']])->field('order_number')->select();
        $porders = M('porder')->where(['customer_id' => $this->user['id'], 'order_number' => ['in', array_column($porderes, 'order_number')]])->select();
        //查询需要导出数据
        $field_arr = array(
            array('title' => '平台单号', 'field' => 'order_number'),
            array('title' => '商户单号', 'field' => 'out_trade_num'),
            array('title' => '类型', 'field' => 'type'),
            array('title' => '产品ID', 'field' => 'product_id'),
            array('title' => '产品', 'field' => 'product_name'),
            array('title' => '手机', 'field' => 'mobile'),
            array('title' => '归属地', 'field' => 'guishu'),
            array('title' => '运营商', 'field' => 'isp'),
            array('title' => '电费地区', 'field' => 'area'),
            array('title' => '状态', 'field' => 'status'),
            array('title' => '总金额', 'field' => 'total_price'),
            array('title' => '支付时间', 'field' => 'pay_time'),
            array('title' => '下单时间', 'field' => 'create_time'),
            array('title' => '完成时间', 'field' => 'finish_time')
        );
        foreach ($porders as &$item) {
            $item['type'] = C('PRODUCT_TYPE')[$item['type']];
            $item['status'] = C('PORDER_STATUS')[$item['status']];
            $item['pay_time'] = time_format($item['pay_time']);
            $item['create_time'] = time_format($item['create_time']);
            $item['finish_time'] = time_format($item['finish_time']);
        }
        exportToExcel($exc['name'] . '-' . time_format($exc['create_time']), $field_arr, $porders);
    }


    //导入excel
    public function in_excel()
    {
        if (request()->isPost()) {
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
                $excel_id = M('agent_excel')->insertGetId([
                    'type' => 2,
                    'customer_id' => $this->user['id'],
                    'name' => $file->getInfo()['name'],
                    'create_time' => time()
                ]);
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
                    $product = ProductModel::getProduct($map, $this->user['id']);
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
                    $tirow[$k]['customer_id'] = $this->user['id'];
                    $tirow[$k]['excel_id'] = $excel_id;
                    $tirow[$k]['create_time'] = time();
                    $tirow[$k]['out_trade_num'] = $out_trade_num;
                }
                $sh = M('agent_proder_excel')->insertAll($tirow);
                return $this->success('成功导入' . $sh . '条数据,即将进入推送页面', U('porder_excel', ['status' => 1, 'excel_id' => $excel_id]));
            } else {
                return $this->error($file->getError());
            }
        } else {
            return view();
        }
    }

    public function batch_in()
    {
        $mobiles = I('mobiles/a');
        $product_id = I('product_id');
        $excel_id = M('agent_excel')->insertGetId([
            'type' => 1,
            'customer_id' => $this->user['id'],
            'name' => "批量下单" . date('Y-m-d H:i', time()),
            'create_time' => time()
        ]);
        $tirow = [];
        foreach ($mobiles as $k => $hang) {
            $mobile = $hang[0];
            $area = $hang[1];
            $hangstr = '  #第[' . ($k + 1) . '行]';
            $map['p.id'] = $product_id;
            $map['p.added'] = 1;
            $product = ProductModel::getProduct($map, $this->user['id']);
            if (!$product) {
                return $this->error('未找到匹配的充值产品，套餐id:' . $product_id . '，手机：' . $mobile . '，' . $hangstr, '', '', 20);
            }
            $tirow[$k]['product_id'] = $product_id;
            $tirow[$k]['mobile'] = $mobile;
            $tirow[$k]['remark'] = $product['name'];
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
            $tirow[$k]['customer_id'] = $this->user['id'];
            $tirow[$k]['excel_id'] = $excel_id;
            $tirow[$k]['create_time'] = time();
        }
        $sh = M('agent_proder_excel')->insertAll($tirow);
        return $this->success('成功生成' . $sh . '条数据,即将进入推送页面', U('porder_excel', ['status' => 1, 'excel_id' => $excel_id]));
    }

    public function porder_excel()
    {
        $map = $this->porder_excel_map();
        $list = M('agent_proder_excel p')
            ->join('agent_excel e', 'e.id=p.excel_id')
            ->where($map)->field('p.*,e.name as excel_name')
            ->order('p.id desc')->select();
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
        return view();
    }

    public function porder_excel_out()
    {
        $map = $this->porder_excel_map();
        $list = M('agent_proder_excel p')
            ->join('agent_excel e', 'e.id=p.excel_id')
            ->where($map)->field('p.*,e.name as excel_name')
            ->order('p.id desc')->select();
        //查询需要导出数据
        $field_arr = array(
            array('title' => '表格名称', 'field' => 'excel_name'),
            array('title' => '行', 'field' => 'hang'),
            array('title' => '商户单号', 'field' => 'out_trade_num'),
            array('title' => '类型', 'field' => 'type'),
            array('title' => '产品ID', 'field' => 'product_id'),
            array('title' => '产品', 'field' => 'product_name'),
            array('title' => '手机', 'field' => 'mobile'),
            array('title' => '归属地', 'field' => 'guishu'),
            array('title' => '运营商', 'field' => 'isp'),
            array('title' => '状态', 'field' => 'status'),
            array('title' => '系统单号', 'field' => 'order_number'),
            array('title' => '总金额', 'field' => 'total_price'),
            array('title' => '生成时间', 'field' => 'create_time'),
            array('title' => '提单结果', 'field' => 'resmsg')
        );
        foreach ($list as &$item) {
            $item['type'] = C('PRODUCT_TYPE')[$item['type']];
            $item['status'] = C('PORDER_EXCEL_STATUS')[$item['status']];
            $item['create_time'] = time_format($item['create_time']);
        }
        exportToExcel('导出表格-' . time_format(time()), $field_arr, $list);
    }

    public function zuofei_porder_excel()
    {
        if (M('agent_proder_excel')->where(['id' => I('id'), 'customer_id' => $this->user['id']])->setField(['status' => 5])) {
            return $this->success('作废成功');
        } else {
            return $this->error('作废失败');
        }
    }

    public function zuofei_all_porder_excel()
    {
        $map['customer_id'] = $this->user['id'];
        $map['status'] = 1;
        M('agent_proder_excel')->where($map)->setField(['status' => 5]);
        return $this->success('作废成功');
    }

    public function push_excel()
    {
        set_time_limit(0);
        $excel_id = I('excel_id');
        $map['p.status'] = 1;
        $map['p.excel_id'] = $excel_id;

        $list = M('agent_proder_excel p')
            ->join('agent_excel e', 'e.id=p.excel_id')
            ->where($map)->field('p.id')->select();
        if (!$list) {
            return $this->error('没有可推送的数据');
        }
        M('agent_proder_excel')->where(['id' => ['in', array_column($list, 'id')]])->setField(['status' => 2]);
        queue('app\queue\job\Work@agentPushExcel', $list);
        return $this->success('成功确认' . count($list) . '条！请刷新待推送列表查看', U('porder_excel', ['excel_id' => $excel_id]));
    }

    private function porder_excel_map()
    {
        $map['p.customer_id'] = $this->user['id'];
        if (I('status2')) {
            $map['p.status'] = ['in', I('status2')];
        }
        if (I('status')) {
            $map['p.status'] = I('status');
        }
        if (I('excel_id')) {
            $map['p.excel_id'] = I('excel_id');
        }
        return $map;
    }

    //回调通知
    public function notification()
    {
        $porder = M('porder')->where(['id' => I('id'), 'status' => ['in', '4,5,6']])->find();
        if (!$porder) {
            return $this->error('订单不可回调');
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


}