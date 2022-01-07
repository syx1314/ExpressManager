<?php
/**
 * Created by PhpStorm.
 * User: 13788
 * Date: 2017/11/17
 * Time: 9:43
 */

namespace app\admin\controller;


use Util\GoogleAuth;
use Util\Updatedt;

/**
 * 系统更新
 */
class Update extends Admin
{
    //系统更新
    public function index()
    {
        $up = new Updatedt();
        $res = $up->check();
        if ($res['errno'] == 0) {
            $this->assign('updata', $res['data']);
        }
        $this->assign('check_msg', $res['errmsg']);
        return view();
    }

    public function now_update()
    {
        set_time_limit(0);
        ini_set('max_execution_time', '0');
        $goret = GoogleAuth::verifyCode($this->adminuser['google_auth_secret'], I('verifycode'), 1);
        if (!$goret) {
            return $this->error("谷歌身份验证码错误！");
        }
        $up = new Updatedt1111111();
        $res = $up->check();
        if ($res['errno'] != 0) {
            return $this->error($res['errmsg']);
        }
        try {
            $up->start($res['data']['version'], $res['data']['path']);
            $up->executesql();
            M('sysupdate_log')->insertGetId(['version' => $res['data']['version'], 'zip_url' => $res['data']['path'], 'create_time' => time()]);
            return $this->success("更新完成");
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

}