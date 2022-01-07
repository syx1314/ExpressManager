<?php

namespace app\api\controller;

use app\common\library\Userlogin;

/**
 * Class Weixin Applet
 */
class Applet extends Base
{
    public function _initbasechild()
    {
        $this->applet = new \Util\Applet($this->wxconfig);
    }

    private function authorization_code($code)
    {
        $rets = $this->applet->get_Openid_by_code($code);
        if (!$rets) {
            return ['errno' => 1, 'errmsg' => "失败，获取openid失败", 'data' => $this->applet->errMsg];
        }
        if (!$rets['openid']) {
            return ['errno' => 1, 'errmsg' => "失败,无openid！", 'data' => $rets];
        }
        if ($customer = M('customer')->where(["ap_openid" => $rets['openid'], 'is_del' => 0])->field("id")->find()) {
            M('customer')->where(["id" => $customer['id']])->setField(['session_key' => $rets['session_key']]);

        }
        return ['errno' => 0, 'errmsg' => 'ok', 'data' => $rets];
    }

    /**
     * 尝试性登录
     */
    public function login()
    {
        $authdata = $this->authorization_code(I('post.code'));
        if ($authdata['errno'] != 0) {
            return djson($authdata['errno'], $authdata['errmsg'], $authdata['data']);
        }
        $openid = $authdata['data']['openid'];
        $session_key = $authdata['data']['session_key'];
        if ($customer = M('customer')->where(["ap_openid" => $openid, 'weixin_appid' => $this->wxconfig['appid'], 'is_del' => 0])->field("id")->find()) {
            $data = Userlogin::create_login_data($customer['id']);
            return djson($data['errno'], $data['errmsg'], $data['data']);
        } else {
            $regret = Userlogin::wxmp_user_reg($openid, I('vi'), $this->wxconfig['appid'], $session_key);
            if ($regret['errno'] != 0) {
                return djson($regret['errno'], $regret['errmsg']);
            }
            $data = Userlogin::create_login_data($regret['data']['id']);
            return djson($data['errno'], $data['errmsg'], $data['data']);
        }
    }

    //获取昵称头像并更新
    public function save_user_info()
    {
        $authdata = $this->authorization_code(I('post.code'));
        if ($authdata['errno'] != 0) {
            return djson($authdata['errno'], $authdata['errmsg'], $authdata['data']);
        }
        if ($customer = M('customer')->where(["ap_openid" => $authdata['data']['openid'], 'weixin_appid' => $this->wxconfig['appid'], 'is_del' => 0])->field("id")->find()) {
            $encryptedData = I('encryptedData');
            $pc = new \Util\Wxbizdatacrypt($this->wxconfig['appid'], $authdata['data']['session_key']);
            $errCode = $pc->decryptData($encryptedData, I('iv'), $dataenc);
            if ($errCode != 0) {
                return djson(1, "解密失败！", $errCode);
            }
            M('customer')->where(['id' => $customer['id']])->setField([
                'username' => $dataenc->nickName,
                'headimg' => $dataenc->avatarUrl,
                'session_key' => $authdata['data']['session_key'],
                'sex' => $dataenc->gender == 1 ? 1 : 2,
                'is_mp_auth' => 1
            ]);
            return djson(0, "更新成功");
        } else {
            return djson(1, "失败，请删除小程序重新关注！");
        }
    }

    //解密手机
    public function encrypte_phone()
    {
        $session_key = M('Customer')->where(['id' => I('customer_id')])->value('session_key');
        $encryptedData = I('encryptedData');
        $pc = new \Util\Wxbizdatacrypt($this->wxconfig['appid'], $session_key);
        $errCode = $pc->decryptData($encryptedData, I('iv'), $dataenc);
        if ($errCode != 0) {
            return djson(1, "解密失败！", $errCode);
        }
        return djson(0, "ok", $dataenc->phoneNumber);
    }

    //获取模板消息id
    public function get_submsg_template_id()
    {
        $tm_names = I('tms');
        $namearr = explode(',', $tm_names);
        $templet = M('weixin_subscribe')->where(['weixin_appid' => $this->wxconfig['appid']])->find();
        if (!$templet) {
            return djson(1, '未找到模板配置');
        }
        $tmarr = [];
        foreach ($namearr as $tmp_clo) {
            if (isset($templet[$tmp_clo]) && $templet[$tmp_clo]) {
                $tmarr[] = $templet[$tmp_clo];
            }
        }
        if (count($tmarr) == 0) {
            return djson(1, '未配置模板id');
        }
        return djson(0, 'ok', $tmarr);
    }

}
