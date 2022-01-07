<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2018-03-23
 * Time: 21:42
 */

namespace app\common\library;

use app\common\model\Client;
use app\common\model\Customer as CustomerModel;

class Userlogin
{
    /**
     *生成登录数据
     */
    public static function create_login_data($customer_id)
    {
        //清除上次的access_token
        $customer = M('customer')->where(['id' => $customer_id])->field("id,status,username,mobile,headimg,type,f_id,create_time,balance,integral,is_mp_auth")->find();
        if ($customer['status'] != 1) {
            return ['errno' => 1, 'errmsg' => '账户被禁用', 'data' => ''];
        }
        $last_access_token = S('USERLOGINONE' . $customer['id']);//查询上一次的access_token
        if ($last_access_token) {
            S($last_access_token, null);//清除上一次登录的access_token 保存的数据
        }
        //生成本次access_token
        $access_token = dyr_encrypt(json_encode($customer['id']) . time());
        $adata['customer'] = $customer;
        $adata['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        S($access_token, $adata);//存储用户登录状态
        $retdata['access_token'] = $access_token;
        $retdata['customer'] = $customer;

        S('USERLOGINONE' . $customer['id'], $access_token);//用户当前登录状态的$access_token
        return ['errno' => 0, 'errmsg' => '登录成功', 'data' => $retdata];
    }

    public static function get_userinfo_by_token($access_token)
    {
        $adata = S($access_token);
        if (!$adata) {
            return ['errno' => -1, 'errmsg' => '请重新登录，您的登录已经过期了', 'data' => ''];
        }
        $customer = M('customer')->where(['id' => $adata['customer']['id'], 'is_del' => 0])->find();
        if (!$customer) {
            return ['errno' => -1, 'errmsg' => '请重新登录', 'data' => ''];
        }
        if ($customer['status'] != 1) {
            return ['errno' => 1, 'errmsg' => '账户被禁用', 'data' => ''];
        }
        return ['errno' => 0, 'errmsg' => 'ok', 'data' => $customer];
    }

    //微信H5注册方式
    public static function wxh5_user_reg($userinfo, $f_id, $weixin_appid, $client)
    {
        $customer = M('customer')->where(["wx_openid" => $userinfo['openid'], 'is_del' => 0])->find();
        if ($customer) {
            return rjson(1, '已经注册', $customer);
        }
        if ($f_id) {
            if ($fcus = M('customer')->where(['id' => $f_id, 'is_del' => 0])->field('id')->find()) {
                $arr['f_id'] = $fcus['id'];
            }
        }
        $arr['username'] = $userinfo['nickname'];
        $arr['headimg'] = $userinfo['headimgurl'];
        $arr['create_time'] = time();
        $arr['sex'] = $userinfo['sex'];
        $arr['weixin_appid'] = $weixin_appid;
        $arr['wx_openid'] = $userinfo['openid'];
        $arr['apikey'] = strtoupper(md5(time()));
        $arr['client'] = $client;
        $aid = M('customer')->insertGetId($arr);
        if ($aid) {
            Createlog::customerLog($aid, '注册成功', '系统');
            $customer = M('customer')->where(['id' => $aid])->find();
            if ($customer['f_id']) {
                CustomerModel::inviteSus($customer['f_id'], $customer['id']);
            }
            CustomerModel::checkShareImg($customer['id']);
            return rjson(0, '注册成功', $customer);
        } else {
            return rjson(1, '注册失败');
        }
    }

    //微信小程序注册
    public static function wxmp_user_reg($ap_openid, $f_id, $weixin_appid, $session_key)
    {
        $customer = M('customer')->where(["ap_openid" => $ap_openid, 'weixin_appid' => $weixin_appid, 'is_del' => 0])->find();
        if ($customer) {
            return rjson(1, '已经注册', $customer);
        }
        if ($f_id) {
            if ($fcus = M('customer')->where(['id' => $f_id, 'is_del' => 0])->field('id')->find()) {
                $arr['f_id'] = $fcus['id'];
            }
        }
        $arr['username'] = '';
        $arr['headimg'] = '';
        $arr['create_time'] = time();
        $arr['weixin_appid'] = $weixin_appid;
        $arr['ap_openid'] = $ap_openid;
        $arr['session_key'] = $session_key;
        $arr['apikey'] = strtoupper(md5(time()));
        $arr['client'] = Client::CLIENT_MP;
        $aid = M('customer')->insertGetId($arr);
        if ($aid) {
            Createlog::customerLog($aid, '注册成功', '小程序');
            $customer = M('customer')->where(['id' => $aid])->find();
            if ($customer['f_id']) {
                CustomerModel::inviteSus($customer['f_id'], $customer['id']);
            }
            return rjson(0, '注册成功', $customer);
        } else {
            return rjson(1, '注册失败');
        }
    }


    //代理商开户
    public static function aga_user_reg($username, $headimg, $mobile, $f_id)
    {
        if (M('Customer')->where(['username' => $username])->find()) {
            return rjson(1, '已有相同的用户名,请换一个名称');
        }
        $fuser = M('customer')->where(["id" => $f_id, 'is_del' => 0])->find();
        if ($fuser) {
            $arr['f_id'] = $f_id;
        }
        $arr['username'] = $username;
        $arr['headimg'] = $headimg;
        $arr['type'] = 2;
        $arr['mobile'] = $mobile;
        $arr['password'] = dyr_encrypt($mobile);
        $arr['grade_id'] = 3;
        $arr['create_time'] = time();
        $arr['apikey'] = strtoupper(md5(time()));
        $arr['client'] = Client::CLIENT_AGA;
        $aid = M('customer')->insertGetId($arr);
        if ($aid) {
            Createlog::customerLog($aid, '注册成功', '代理商');
            $customer = M('customer')->where(['id' => $aid])->find();
            return rjson(0, '注册成功', $customer);
        } else {
            return rjson(1, '注册失败');
        }
    }


    //代理商开户
    public static function h5_user_reg($username, $password, $f_id)
    {
        if (M('Customer')->where(['username' => $username])->find()) {
            return rjson(1, '已有相同用户名,请换一个名称');
        }
        $fuser = M('customer')->where(["id" => $f_id, 'is_del' => 0])->find();
        if ($fuser) {
            $arr['f_id'] = $f_id;
        }
        $arr['username'] = $username;
        $arr['headimg'] = C('DEFAULT_HEADIMG');
        $arr['type'] = 1;
        $arr['mobile'] = "";
        $arr['password'] = dyr_encrypt($password);
        $arr['grade_id'] = 1;
        $arr['create_time'] = time();
        $arr['apikey'] = strtoupper(md5(time()));
        $arr['client'] = Client::CLIENT_H5;
        $aid = M('customer')->insertGetId($arr);
        if ($aid) {
            Createlog::customerLog($aid, '注册成功', 'H5端');
            $customer = M('customer')->where(['id' => $aid])->find();
            return rjson(0, '注册成功', $customer);
        } else {
            return rjson(1, '注册失败');
        }
    }

}