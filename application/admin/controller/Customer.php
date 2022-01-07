<?php
/**
 * Created by PhpStorm.
 * User: 13788
 * Date: 2017/11/17
 * Time: 9:43
 */

namespace app\admin\controller;

use app\common\library\AgentGrade;
use app\common\library\Createlog;
use app\common\model\Balance;
use app\common\model\Client;
use app\common\model\CustomerHezuoPrice;
use app\common\model\Product as ProductModel;
use Util\GoogleAuth;
use Util\Random;


/**
 * Class Member
 * @package app\admin\controller
 
 *
 */
class Customer extends Admin
{
    //用户列表
    public function index()
    {
        $map = $this->create_map();
        $list = $this->getList($map);
        $this->assign('_list', $list);
        $this->assign('_count', $list->total());
        return view();
    }

    private function create_map()
    {
        $map['c.is_del'] = 0;
        if (I('key')) {
            if (I('query_name')) {
                $map[I('query_name')] = trim(I('key'));
            } else {
                $map['c.username|c.mobile'] = array('like', '%' . trim(I('key')) . '%');
            }
        }
        if (I('grade_id')) {
            $map['c.grade_id'] = I('grade_id');
        }
        if (I('is_subscribe')) {
            $map['c.is_subscribe'] = intval(I('is_subscribe')) - 1;
        }
        if (I('status')) {
            $map['c.status'] = I('status') - 1;
        }
        if (I('id')) {
            $map['c.id'] = I('id');
        }
        if (I('f_id')) {
            $map['c.f_id'] = I('f_id');
        }
        if (I('type')) {
            $map['c.type'] = I('type');
            $this->assign("grades", M('customer_grade')->where(['grade_type' => I('type')])->order("sort asc,grade_type asc")->select());
        } else {
            $this->assign("grades", []);
        }
        return $map;
    }


    private function getList($map, $page = true)
    {
        if ($page) {
            $list = M('Customer c')
                ->where($map)
                ->field('c.*,(select username from dyr_customer where id=c.f_id) as usernames,(select grade_name from dyr_customer_grade where id=c.grade_id) as grade_name,(select is_zdy_price from dyr_customer_grade where id=c.grade_id) as is_zdy_price,(select sum(total_price) from dyr_porder where customer_id=c.id and status>1) as total_price,(select count(*) from dyr_porder where customer_id=c.id and status>1) as porder_num,(select count(*) from dyr_customer where f_id=c.id and is_del=0) as child_num')
                ->order("c.create_time desc")
                ->paginate(C('LIST_ROWS'));
        } else {
            $list = M('Customer c')
                ->where($map)
                ->field('c.*,(select username from dyr_customer where id=c.f_id) as usernames,(select grade_name from dyr_customer_grade where id=c.grade_id) as grade_name,(select is_zdy_price from dyr_customer_grade where id=c.grade_id) as is_zdy_price,(select sum(total_price) from dyr_porder where customer_id=c.id and status>1) as total_price,(select count(*) from dyr_porder where customer_id=c.id and status>1) as porder_num,(select count(*) from dyr_customer where f_id=c.id and is_del=0) as child_num')
                ->order("c.create_time desc")
                ->select();
        }
        return $list;
    }

    public function customer_excel()
    {
        $map = $this->create_map();
        $ret = $this->getList($map, false);
        //查询需要导出数据
        $field_arr = array(
            ['title' => '昵称', 'field' => 'username'],
            ['title' => '手机', 'field' => 'mobile'],
            ['title' => '注册时间', 'field' => 'create_time'],
            ['title' => '会员类型', 'field' => 'type'],
            ['title' => '等级', 'field' => 'grade_name'],
            ['title' => 'appid', 'field' => 'weixin_appid'],
            ['title' => 'openid', 'field' => 'wx_openid'],
            ['title' => '余额', 'field' => 'balance'],
            ['title' => '订单金额', 'field' => 'total_price'],
            ['title' => '订单数', 'field' => 'porder_num'],
        );
        foreach ($ret as $key => $vo) {
            $ret[$key]['type'] = C('CUS_TYPE')[$vo['type']];
            $ret[$key]['create_time'] = time_format($vo['create_time']);
        }
        exportToExcel('会员列表' . time(), $field_arr, $ret);
    }

    //编辑
    public function edit()
    {
        if (request()->isPost()) {
            $goret = GoogleAuth::verifyCode($this->adminuser['google_auth_secret'], I('verifycode'), 1);
            if (!$goret) {
                return $this->error("谷歌身份验证码错误！");
            }
            $arr = I('post.');
            unset($arr['verifycode']);
            if (I('id')) {
                $data = M('Customer')->where(['id' => I('id')])->setField($arr);
                if ($data) {
                    Createlog::customerLog(I('id'), '修改信息：' . json_encode($arr), '管理员：' . $this->adminuser['nickname']);
                    return $this->success('保存成功');
                } else {
                    return $this->error('编辑失败');
                }
            } else {
                $username = I('username');
                $mobile = I('mobile');
                if ($username == '' || $mobile == '') {
                    return $this->error('用户名和联系方式必须');
                }
                if (M('Customer')->where(['type' => 2, 'username' => I('username')])->find()) {
                    return $this->error('已有相同用户名的代理商');
                }
                $arr['password'] = dyr_encrypt(I('password'));
                $arr['type'] = 2;
                $arr['client'] = Client::CLIENT_ADM;
                $arr['create_time'] = time();
                $arr['apikey'] = Random::alnum(32);
                $arr['headimg'] = $arr['headimg'] ? $arr['headimg'] : C('DEFAULT_HEADIMG');
                $inid = M('Customer')->insertGetId($arr);
                if ($inid) {
                    Createlog::customerLog($inid, '后台注册成功', $this->adminuser['nickname']);
                    return $this->success('新增成功');
                } else {
                    return $this->error('新增失败');
                }
            }
        } else {
            $this->assign("grades", M('customer_grade')->select());
            $info = M('Customer')->where(['id' => I('id')])->find();
            $this->assign('info', $info);
            return view();
        }
    }

    //代理商api
    public function edita()
    {
        $info = M('Customer')->where(['id' => I('id')])->find();
        $this->assign('info', $info);
        return view();
    }

    //查询等级
    public function get_grades()
    {
        $map = [];
        if (I('grade_type')) {
            $map['grade_type'] = I('grade_type');
        }
        $grades = M('customer_grade')->where($map)->order("sort asc,grade_type asc")->select();
        return djson(0, 'ok', $grades);
    }

    //删除
    public function deletes()
    {
        if (M('Customer')->where(['id' => I('id')])->setField(['is_del' => 1])) {
            Createlog::customerLog(I('id'), '删除账户', '管理员：' . $this->adminuser['nickname']);
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败');
        }
    }

    //删除所有海报
    public function del_poster()
    {
        $map['is_del'] = 0;
        if (I('id')) {
            $map['id'] = I('id');
        }
        M('Customer')->where($map)->setField(['qr_value' => '', 'qrurl' => '', 'share_img_time' => '0', 'mp_qrurl' => '', 'h5_qrurl' => '']);
        return $this->success('清空成功');
    }

    //启用，禁用
    public function qi_jin()
    {
        if (I('status') == 0) {
            if (M('Customer')->where(['id' => I('id')])->setField(['status' => 0])) {
                Createlog::customerLog(I('id'), '禁用账户', '管理员：' . $this->adminuser['nickname']);
                return $this->success('禁用成功');
            } else {
                return $this->error('禁用失败');
            }
        } else {
            if (M('Customer')->where(['id' => I('id')])->setField(['status' => 1])) {
                Createlog::customerLog(I('id'), '启用账户', '管理员：' . $this->adminuser['nickname']);
                return $this->success('启用成功');
            } else {
                return $this->error('启用失败');
            }
        }
    }

    public function balance_log()
    {
        $map = $this->balance_create_map();
        $list = M('balance_log b')->join('customer c', 'c.id=b.customer_id')->where($map)->field('b.*,c.username,c.headimg')->order("b.id desc")->paginate(30);
        $this->assign('_list', $list);
        return view();
    }

    private function balance_create_map()
    {
        $map = [];
        if (I('style')) {
            $map['b.style'] = I('style');
        }
        if (I('id')) {
            $map['b.customer_id'] = I('id');
        }
        if (I('key')) {
            $map['b.remark'] = array('like', '%' . I('key') . '%');
        }
        if (I('end_time') && I('begin_time')) {
            $map['b.create_time'] = array('between', [strtotime(I('begin_time')), strtotime(I('end_time'))]);
        }
        return $map;
    }


    /**
     * 导出余额明细
     */
    public function balance_out_excel()
    {
        $map = $this->balance_create_map();
        // 查询导出数据
        $ret = M('balance_log b')->join('customer c', 'c.id=b.customer_id')->where($map)->field('b.*,c.username,c.headimg')->order("b.id desc")->select();
        //查询需要导出数据
        $field_arr = array(
            array('title' => '账号', 'field' => 'username'),
            array('title' => '账号ID', 'field' => 'customer_id'),
            array('title' => '交易时间', 'field' => 'create_time'),
            array('title' => '交易方式', 'field' => 'type'),
            array('title' => '交易金额', 'field' => 'money'),
            array('title' => '交易明细', 'field' => 'remark'),
            array('title' => '交易类型', 'field' => 'style'),
            array('title' => '余额', 'field' => 'balance')
        );
        foreach ($ret as $key => $vo) {
            $ret[$key]['type'] = $vo['type'] == 1 ? '收入' : '支出';
            $ret[$key]['money'] = $vo['type'] == 1 ? $vo['money'] : -$vo['money'];
            $ret[$key]['style'] = C('BALANCE_STYLE')[$vo['style']];
            $ret[$key]['create_time'] = time_format($vo['create_time']);
        }
        exportToExcel('余额明细' . time(), $field_arr, $ret);
    }

    public function integral_log()
    {
        $map = [];
        if (I('style')) {
            $map['style'] = I('style');
        }
        if (I('id')) {
            $map['customer_id'] = I('id');
        }
        if (I('key')) {
            $map['remark'] = array('like', '%' . I('key') . '%');
        }
        if (I('end_time') && I('begin_time')) {
            $map['create_time'] = array('between', [strtotime(I('begin_time')), strtotime(I('end_time'))]);
        }
        $list = M('integral_log')->where($map)->order("id desc")->paginate(30);
        $this->assign('_list', $list);
        return view();
    }

    public function customer_log()
    {
        $map = [];
        if (I('id')) {
            $map['customer_id'] = I('id');
        }
        if (I('key')) {
            $map['log'] = array('like', '%' . I('key') . '%');
        }
        $list = M('customer_log')->where($map)->order("create_time desc")->paginate(30);
        $this->assign('_list', $list);
        return view();
    }

    public function grade()
    {
        $list = M('customer_grade')->select();
        $this->assign('_list', $list);
        return view();
    }

    //编辑
    public function grade_edit()
    {
        if (request()->isPost()) {
            $arr = I('post.');
            if (I('id')) {
                $data = M('customer_grade')->where(['id' => I('id')])->setField($arr);
                if ($data) {
                    return $this->success('保存成功');
                } else {
                    return $this->error('编辑失败');
                }
            } else {
                $aid = M('customer_grade')->insertGetId($arr);
                if ($aid) {
                    return $this->success('新增成功');
                } else {
                    return $this->error('新增失败');
                }
            }
        } else {
            $info = M('customer_grade')->where(['id' => I('id')])->find();
            $this->assign('info', $info);
            return view();
        }
    }

    public function price()
    {
        AgentGrade::initPrice();
        if (I('grade_id')) {
            $map['gp.grade_id'] = I('grade_id');
        }
        if (I('product_id')) {
            $map['gp.product_id'] = I('product_id');
        }
        $map['p.is_del'] = 0;
        if (I('key')) {
            $map['p.name|p.desc'] = ['like', '%' . I('key') . '%'];
        }
        $list = M('customer_grade_price gp')
            ->join("product p", 'p.id=gp.product_id')
            ->where($map)
            ->field("gp.*,p.name,p.isp,p.desc,p.price,p.added,p.remark,(select grade_name from dyr_customer_grade where id=gp.grade_id) as grade_name")
            ->order("p.type asc,p.sort asc")
            ->select();
        $this->assign('_list', $list);
        return view();
    }

    //编辑
    public function price_edit()
    {
        if (request()->isPost()) {
            $arr = I('post.');
            $data = M('customer_grade_price')->where(['id' => I('id')])->setField($arr);
            if ($data) {
                return $this->success('保存成功');
            } else {
                return $this->error('编辑失败');
            }
        } else {
            $info = M('customer_grade_price')->where(['id' => I('id')])->find();
            $this->assign('info', $info);
            return view();
        }
    }

    public function up_password()
    {
        if (!I('id') || !I('password')) {
            return $this->error("请输入密码！");
        }
        $goret = GoogleAuth::verifyCode($this->adminuser['google_auth_secret'], I('verifycode'), 1);
        if (!$goret) {
            return $this->error("谷歌身份验证码错误！");
        }
        M('customer')->where(['id' => I('id')])->setField(['password' => dyr_encrypt(I('password'))]);
        Createlog::customerLog(I('id'), '修改密码', '管理员：' . $this->adminuser['nickname']);
        return $this->success('重置成功!');
    }

    public function balance_add()
    {
        $goret = GoogleAuth::verifyCode($this->adminuser['google_auth_secret'], I('verifycode'), 1);
        if (!$goret) {
            return $this->error("谷歌身份验证码错误！");
        }
        $money = floatval(I('money'));
        if ($money == 0) {
            return $this->error("金额错误！");
        }
        $remark = I('remark');
        if ($remark == "") {
            return $this->error("备注必填！");
        }
        if ($money > 0) {
            $ret = Balance::revenue(I('id'), $money, $remark, Balance::STYLE_RECHARGE, '管理员：' . $this->adminuser['nickname']);
        } else {
            $ret = Balance::expend(I('id'), abs($money), $remark, Balance::STYLE_RECHARGE, '管理员：' . $this->adminuser['nickname']);
        }
        if ($ret['errno'] != 0) {
            return $this->error($ret['errmsg']);
        }
        return $this->success($ret['errmsg']);
    }

    public function hz_price()
    {
        CustomerHezuoPrice::initPrice();
        $customer = M('customer')->where(['id' => I('customer_id')])->find();
        if (!$customer) {
            return $this->error('未找到用户');
        }
        $map['p.is_del'] = 0;
        $key = trim(I('key'));
        if ($key) {
            if (I('query_name')) {
                $map[I('query_name')] = $key;
            } else {
                $map['p.name|p.desc'] = ['like', '%' . $key . '%'];
            }
        }
        if (I('product_id')) {
            $map['p.id'] = I('product_id');
        }
        $cates = ProductModel::getProducts($map, $customer['id']);
        foreach ($cates as &$cate) {
            foreach ($cate['products'] as &$item) {
                $hzprice = M('customer_hezuo_price')->where(['customer_id' => $customer['id'], 'product_id' => $item['id']])->field('id as rangesid,ranges,ys_tag')->find();
                if (!$hzprice) {
                    continue;
                }
                $item['ys_tag'] = $hzprice['ys_tag'];
                $item['rangesid'] = $hzprice['rangesid'];
                $item['ranges'] = $hzprice['ranges'];
            }
        }
        $this->assign('_list', $cates);
        return view();
    }

    //编辑
    public function hz_price_edit()
    {
        if (request()->isPost()) {
            $price = M('customer_hezuo_price')->where(['id' => I('id')])->field('id,product_id,customer_id')->find();
            $map['p.is_del'] = 0;
            $map['p.id'] = $price['product_id'];
            $customer = M('customer')->where(['id' => $price['customer_id']])->find();
            $product = ProductModel::getProduct($map, $customer['id']);

            $ranges = floatval(I('ranges'));
            if ($ranges < 0) {
                return $this->error('浮动金额不能小于0');
            }
            if (floatval($product['max_price']) > 0 && ($product['price'] + $ranges) > $product['max_price']) {
                return $this->error('不能设置高于封顶价格');
            }
            $data = M('customer_hezuo_price')->where(['id' => $price['id']])->setField(['ranges' => $ranges]);
            if ($data) {
                return $this->success('保存成功');
            } else {
                return $this->error('编辑失败');
            }
        } else {
            $info = M('customer_hezuo_price')->where(['id' => I('id')])->find();
            $this->assign('info', $info);
            return view();
        }
    }

    //编辑
    public function hz_price_ystag_edit()
    {
        $data = M('customer_hezuo_price')->where(['id' => I('id')])->setField(['ys_tag' => I('ys_tag')]);
        if ($data) {
            return $this->success('保存成功');
        } else {
            return $this->error('编辑失败');
        }
    }

}