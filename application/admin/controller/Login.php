<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2017-11-14
 * Time: 9:50
 */

namespace app\admin\controller;
use app\common\library\RedisPackage;
use Recharge\InterfaceControl;
use think\Log;
use Util\Syslog;
use app\common\model\Member as MemberModel;

/**
 * 昵称：
 * 微信：1
 **/
class Login extends Base
{
    //登录
    public function login()
    {
        return view();
    }

    public function logindo()
    {
        $loginInfo='用户尝试登录用户名:'.I('nickname').'-----密码---'.I('password').'---验证码---'.I('verifycode').'---ip---'.get_client_ip();
        Log::error("登录参数".$loginInfo);
        //发送登陆通知
        $send=new InterfaceControl();
        $send->send($loginInfo);
        $res = MemberModel::pwdLogin(I('nickname'), I('password'), I('verifycode'));
        if ($res['errno'] != 0) {
            return djson(1, $res['errmsg'], $res['data']);
        }
        $member = $res['data'];
        $auth = array(
            'id' => $member['id'],
            'nickname' => $member['nickname'],
            'last_login_time' => $member['last_login_time'],
            'headimg' => $member['headimg'],
        );
        $auth['token'] = data_auth_sign($auth);
        $redis=new RedisPackage();
        $tk= $redis::set('adminToken-'.$auth['id'],data_auth_sign($auth));
       if ($tk) {
           session('user_auth', $auth);
           session('user_auth_sign', data_auth_sign($auth));
           Syslog::write("后台登录", $auth, $member['nickname']);

           return djson(0, "登录成功", ['member' => $member, 'url' => $member['google_auth_secret'] ? U('Admin/index') : U('Index/bind_google_auth')]);
       }else{
           return djson(0, "登录失败token出错",null);
       }
    }

    /**
     * 昵称：
 * 微信：1
     * 退出登录
     */
    public function logout()
    {
        session('user_auth', null);
        session('user_auth_sign', null);
        session('Auth_List', null);
        $this->redirect('Login/login');
    }
}