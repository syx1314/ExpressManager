<?php

namespace app\admin\controller;

use app\common\enum\ExpressOrderEnum;
use think\Db;
use Util\GoogleAuth;

class Index extends Admin
{
    public function index()
    {
        $data['total_price'] = M('porder')->where(['status' => ['in', '2,3,4,5']])->sum('total_price');
        $data['today_price'] = M('porder')->where(['pay_time' => ['egt', strtotime(date('Y-m-d'))], 'status' => ['in', '2,3,4,5']])->sum('total_price');
        $data['order_num'] = M('porder')->where(['status' => ['gt', 1]])->count();
        $data['today_order_num'] = M('porder')->where(['status' => ['gt', 1], 'pay_time' => ['egt', strtotime(date('Y-m-d'))]])->count();
        $data['cus_num'] = M('customer')->where(['type' => 1, 'is_del' => 0])->count();
        $data['agent_num'] = M('customer')->where(['type' => 2, 'is_del' => 0])->count();
        $data['cus_balance'] = M('customer')->where(['type' => 1, 'is_del' => 0])->sum('balance');
        $data['agent_balance'] = M('customer')->where(['type' => 2, 'is_del' => 0])->sum('balance');

        // 今日快递营业额   把今天所有的账单 支付 金额 加起来

        // 今日快递单数  快递总数
        $data['express_total_num'] = M('expressorder')->where(['create_time' => ['egt', strtotime(date('Y-m-d'))]])->count();
        // 今日快递单数  待取件总数
        $data['express_daiqu_num'] = M('expressorder')->where(['status' => ExpressOrderEnum::DAI_QU_JIAN,'create_time' => ['egt', strtotime(date('Y-m-d'))]])->count();
        $this->assign('data', $data);
        return view();
    }

    public function sysinfo()
    {
        $server_info = [
            'DThink版本' => "2.0",
            '操作系统' => PHP_OS,
            '运行环境' => $_SERVER["SERVER_SOFTWARE"],
            'PHP版本' => PHP_VERSION,
            'PHP运行方式' => php_sapi_name(),
            'ThinkPHP版本' => THINK_VERSION,
            '缓存方式' => C('cache.type'),
            'MYSQL版本' => Db::query('select version() as v')[0]['v'],
            '上传附件限制' => ini_get('upload_max_filesize'),
            '执行时间限制' => ini_get('max_execution_time') . '秒',
            '服务器时间' => date("Y年n月j日 H:i:s"),
            '服务器域名/IP地址' => $_SERVER['SERVER_NAME'] . ' [ ' . gethostbyname($_SERVER['SERVER_NAME']) . ' ]',
            '当前IP地址' => get_client_ip(0),
            '脚本运行占用最大内存' => get_cfg_var("memory_limit") ? get_cfg_var("memory_limit") : "无",
            '剩余空间' => round((disk_free_space(".") / (1024 * 1024)), 2) . 'M',
            'register_globals' => get_cfg_var("register_globals") == "1" ? "ON" : "OFF"
        ];
        $this->assign('server_info', $server_info);
        return view();
    }

    public function tongji()
    {
        return view();
    }

    /**
     * 数据统计
     */
    public function statistics()
    {
        $list = M()->query('select sum(total_price) as price,FROM_UNIXTIME(create_time,\'%Y年%m月%d日\') as time from dyr_porder where status in(2,3,4,5) GROUP BY time order by time asc');
        return djson(0, 'ok', $list);
    }

    public function bind_google_auth()
    {
        if ($this->adminuser['google_auth_secret']) {
            $this->redirect('admin/index');
        }
        $name = C('WEB_SITE_TITLE') . "-" . $this->adminuser['nickname'];
        $secret = GoogleAuth::createSecret();
        $qrCodeUrl = GoogleAuth::getQRCodeGoogleUrl($name, $secret);
        $this->assign('qrcode_url', $qrCodeUrl);
        $this->assign('secret', $secret);
        return view();
    }

    public function save_google_auth()
    {
        if ($this->adminuser['google_auth_secret']) {
            $this->redirect('admin/index');
        }
        M('Member')->where(['id' => $this->adminuser['id']])->setField(['google_auth_secret' => I('secret')]);
        $this->redirect('admin/index');
    }


}
