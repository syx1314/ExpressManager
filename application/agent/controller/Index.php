<?php

namespace app\agent\controller;


use Util\GoogleAuth;

class Index extends Admin
{
    public function index()
    {
        $data['order_num'] = M('porder')->where(['status' => ['gt', 1], 'customer_id' => $this->user['id']])->count();
        $data['today_order_all_num'] = M('porder')->where(['status' => ['gt', 1], 'customer_id' => $this->user['id'], 'pay_time' => ['egt', strtotime(date('Y-m-d'))]])->count();
        $data['order_ing_num'] = M('porder')->where(['status' => ['in', '2,3'], 'customer_id' => $this->user['id']])->count();
        $data['today_order_sus_num'] = M('porder')->where(['status' => ['in', '4'], 'customer_id' => $this->user['id'], 'finish_time' => ['egt', strtotime(date('Y-m-d'))]])->count();
        $data['today_order_fail_num'] = M('porder')->where(['status' => ['in', '5,6'], 'customer_id' => $this->user['id'], 'finish_time' => ['egt', strtotime(date('Y-m-d'))]])->count();

        $data['leiji_total_price'] = M('porder')->where(['status' => ['in', '2,3,4,5'], 'customer_id' => $this->user['id']])->sum('total_price');
        $data['today_total_price'] = M('porder')->where(['status' => ['in', '2,3,4,5'], 'customer_id' => $this->user['id'], 'pay_time' => ['egt', strtotime(date('Y-m-d'))]])->sum('total_price');

        $data['balance'] = $this->user['balance'];
        $this->assign('data', $data);
        return view();
    }

    /**
     * @return \think\response\Json
     * 数据统计
     */
    public function statistics()
    {
        $list = M()->query('select sum(total_price) as price,FROM_UNIXTIME(create_time,\'%Y年%m月%d日\') as time from dyr_porder where status in(2,3,4,5) and customer_id=' . $this->user['id'] . ' GROUP BY time order by time asc');
        return djson(0, 'ok', $list);
    }

    public function bind_google_auth()
    {
        if ($this->user['google_auth_secret']) {
            $this->redirect('admin/index');
        }
        $name = C('WEB_SITE_TITLE') . "-代理端" . "-" . $this->user['username'];
        $secret = GoogleAuth::createSecret();
        $qrCodeUrl = GoogleAuth::getQRCodeGoogleUrl($name, $secret);
        $this->assign('qrcode_url', $qrCodeUrl);
        $this->assign('secret', $secret);
        $this->assign('name', $name);
        return view();
    }

    public function save_google_auth()
    {
        if ($this->user['google_auth_secret']) {
            $this->redirect('admin/index');
        }
        M('customer')->where(['id' => $this->user['id']])->setField(['google_auth_secret' => I('secret')]);
        $this->redirect('admin/index');
    }

}
