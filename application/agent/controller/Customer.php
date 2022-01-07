<?php
/**
 * Created by PhpStorm.
 * User: 13788
 * Date: 2017/11/17
 * Time: 9:43
 */

namespace app\agent\controller;

use app\common\library\AgentGrade;
use app\common\library\Createlog;
use app\common\library\Userlogin;
use app\common\model\Balance;
use app\common\model\CustomerHezuoPrice;
use app\common\model\Porder as PorderModel;
use app\common\model\Product as ProductModel;
use app\common\model\Customer as CustomerModel;
use Util\GoogleAuth;


/**
 * Class Member
 * @package app\admin\controller
 
 *
 */
class Customer extends Admin
{
    public function _init()
    {
        if ($this->user['grade_id'] !== 4) {
            echo "您的账号没有权限";
            exit();
        }
    }

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
        $map['f_id'] = $this->user['id'];
        $map['type'] = 2;
        $map['c.is_del'] = 0;
        if (I('key')) {
            if (I('query_name')) {
                $map[I('query_name')] = trim(I('key'));
            } else {
                $map['c.username|c.mobile'] = array('like', '%' . trim(I('key')) . '%');
            }
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


    //编辑
    public function edit()
    {
        if (request()->isPost()) {
            $goret = GoogleAuth::verifyCode($this->user['google_auth_secret'], I('verifycode'), 1);
            if (!$goret) {
                return $this->error("谷歌身份验证码错误！");
            }
            $arr = I('post.');
            unset($arr['verifycode']);
            if (I('id')) {
                $data = M('Customer')->where(['id' => I('id'), 'f_id' => $this->user['id']])->setField($arr);
                if ($data) {
                    Createlog::customerLog(I('id'), '修改信息：' . json_encode($arr), '管理员：' . $this->user['username']);
                    return $this->success('保存成功');
                } else {
                    return $this->error('编辑失败');
                }
            } else {
                $username = I('username');
                $mobile = I('mobile');
                $headimg = C('DEFAULT_HEADIMG');
                if ($username == '' || $mobile == '') {
                    return $this->error('用户名和联系方式必须');
                }

                $res = Userlogin::aga_user_reg($username, $headimg, $mobile, $this->user['id']);
                if ($res['errno'] != 0) {
                    return $this->error($res['errmsg']);
                }
                $inid = $res['data']['id'];
                if ($inid) {
                    return $this->success('开户成功，密码是手机号码');
                } else {
                    return $this->error('新增失败');
                }
            }
        } else {
            $info = M('Customer')->where(['id' => I('id'), 'f_id' => $this->user['id']])->find();
            $this->assign('info', $info);
            return view();
        }
    }

    //代理商api
    public function edita()
    {
        $info = M('Customer')->where(['id' => I('id'), 'f_id' => $this->user['id']])->find();
        $this->assign('info', $info);
        return view();
    }


    //启用，禁用
    public function qi_jin()
    {
        if (I('status') == 0) {
            if (M('Customer')->where(['id' => I('id'), 'f_id' => $this->user['id']])->setField(['status' => 0])) {
                Createlog::customerLog(I('id'), '禁用账户', '代理商：' . $this->user['username']);
                return $this->success('禁用成功');
            } else {
                return $this->error('禁用失败');
            }
        } else {
            if (M('Customer')->where(['id' => I('id'), 'f_id' => $this->user['id']])->setField(['status' => 1])) {
                Createlog::customerLog(I('id'), '启用账户', '代理商：' . $this->user['username']);
                return $this->success('启用成功');
            } else {
                return $this->error('启用失败');
            }
        }
    }

    public function balance_log()
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
        $list = M('balance_log')->where($map)->order("create_time desc")->paginate(30);
        $this->assign('_list', $list);
        return view();
    }


    public function up_password()
    {
        if (!I('id') || !I('password')) {
            return $this->error("请输入密码！");
        }
        $goret = GoogleAuth::verifyCode($this->user['google_auth_secret'], I('verifycode'), 1);
        if (!$goret) {
            return $this->error("谷歌身份验证码错误！");
        }
        M('customer')->where(['id' => I('id'), 'f_id' => $this->user['id']])->setField(['password' => dyr_encrypt(I('password'))]);
        Createlog::customerLog(I('id'), '修改密码', '代理商：' . $this->user['username']);
        return $this->success('重置成功!');
    }

    public function balance_add()
    {
        $goret = GoogleAuth::verifyCode($this->user['google_auth_secret'], I('verifycode'), 1);
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
        $user = M('customer')->where(['id' => I('id'), 'f_id' => $this->user['id']])->find();
        if (!$user) {
            return $this->error("用户信息未找到！");
        }
        if ($money > 0) {
            $rets = Balance::expend($this->user['id'], abs($money), "代理商划拨余额给分销商，扣除，" . $remark, Balance::STYLE_RECHARGE, '代理商：' . $this->user['username']);
            if ($rets['errno'] != 0) {
                return $this->error($rets['errmsg']);
            }
            $ret = Balance::revenue(I('id'), $money, $remark, Balance::STYLE_RECHARGE, '代理商：' . $this->user['username']);
        } else {
            $rets = Balance::expend(I('id'), abs($money), $remark, Balance::STYLE_RECHARGE, '代理商：' . $this->user['username']);
            if ($rets['errno'] != 0) {
                return $this->error($rets['errmsg']);
            }
            $ret = Balance::revenue($this->user['id'], abs($money), "代理商扣除分销商余额，退回，" . $remark, Balance::STYLE_RECHARGE, '代理商：' . $this->user['username']);
        }
        if ($ret['errno'] != 0) {
            return $this->error($ret['errmsg']);
        }
        return $this->success($ret['errmsg']);
    }


    //订单列表
    public function porder()
    {
        $map = $this->porder_create_map();
        $list = $data['lists'] = PorderModel::where($map)
            ->field("order_number,out_trade_num,guishu,client,isp,pay_time,finish_time,type,customer_id,status,mobile,total_price,product_name,rebate_price,is_rebate,create_time,title,rebate_time,is_notification,notification_time,notification_num")
            ->order("create_time desc")
            ->paginate(20)->each(function ($item) {
                $item['rebate_status_text'] = $item->getRebateStatusText($item->is_rebate, $item->status);
            });
        $this->assign('total_price', M('porder')->where($map)->sum("total_price"));
        $this->assign('_list', $list);
        return view();
    }

    private function porder_create_map()
    {
        $user = M('customer')->where(['id' => I('customer_id'), 'f_id' => $this->user['id']])->find();
        if (!$user) {
            return $this->error("用户信息未找到！");
        }

        $map['is_del'] = 0;
        $map['customer_id'] = $user['id'];
        if (I('key')) {
            $map['order_number|title|product_name|mobile|out_trade_num'] = ['like', '%' . I('key') . '%'];
        }
        if (I('status')) {
            $map['status'] = intval(I('status'));
        } else {
            $map['status'] = ['gt', 1];
        }
        if (I('is_notification')) {
            $map['is_notification'] = intval(I('is_notification')) - 1;
        }
        if (I('type')) {
            $map['type'] = I('type');
        }
        if (I('end_time') && I('begin_time')) {
            $map['create_time'] = array('between', [strtotime(I('begin_time')), strtotime(I('end_time'))]);
        }
        return $map;
    }


}