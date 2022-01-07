<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2017-11-14
 * Time: 9:50
 */

namespace app\agent\controller;
use app\common\library\RedisPackage;
use app\common\model\Customer as CustomerModel;

/**
 
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
        $res = CustomerModel::pwdLogin(I('username'), I('password'), I('verifycode'));
        if ($res['errno'] != 0) {
            return djson(1, $res['errmsg'], $res['data']);
        }
        $customer = $res['data'];
        $auth = array(
            'id' => $customer['id'],
            'username' => $customer['username'],
            'headimg' => $customer['headimg'],
            'mobile' => $customer['mobile']
        );
        $auth['token'] = data_auth_sign($auth);
        // 保存用户登录id
        $redis= new \app\common\library\RedisPackage();
        $tk= $redis::set('agentToken-'.$auth['id'],data_auth_sign($auth));
        if ($tk) {
            session('user_auth_agent', $auth);
            return djson(0, "登录成功", ['member' => $customer, 'url' => $customer['google_auth_secret'] ? U('Admin/index') : U('Index/bind_google_auth')]);
        }else{
            return djson(0, "登录失败token出错", '');
        }
    }

    /**
     
     * 退出登录
     */
    public function logout()
    {
        session('user_auth_agent', null);
        $this->redirect('Login/login');
    }
}