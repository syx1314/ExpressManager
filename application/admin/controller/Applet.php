<?php

namespace app\admin\controller;


class Applet extends Admin
{
    private $applet;

    public function _init()
    {
        if ($appid = I('appid')) {
            S('admin_edit_mpappid_' . UID, I('appid'));
        } else {
            $appid = S('admin_edit_mpappid_' . UID);
        }
        $this->wxconfig = M('weixin')->where(['appid' => $appid])->find();
        if ($this->wxconfig) {
            $this->applet = new \Util\Applet($this->wxconfig);
        }
    }

    //配置首页
    public function index()
    {
        $data = M('weixin')->where(['type' => 2])->select();
        $this->assign('list', $data);
        return view();
    }

    //编辑
    public function editw()
    {
        if (request()->isPost()) {
            $arr = I('post.');
            $arr['type'] = 2;
            if (I('id')) {
                $data = M('weixin')->where(['id' => I('id')])->setField($arr);
                if ($data) {
                    return $this->success('保存成功');
                } else {
                    return $this->error('编辑失败');
                }
            } else {
                $data = M('weixin')->insertGetId($arr);
                if ($data) {
                    return $this->success('新增成功');
                } else {
                    return $this->error('新增失败');
                }
            }
        } else {
            $info = M('weixin')->where(['appid' => I('appid')])->find();
            $this->assign('info', $info);
            return view();
        }
    }

    //添加模板
    public function add_api_template()
    {
        $data = [
            "tid" => I('tid'),
            "kidList" => explode(',', I('kids')),
            "sceneDesc" => I('name')
        ];
        $res = $this->applet->addSubscribeMessage($data);
        if ($res['errno'] != 0) {
            return rjson(1, $res['errmsg']);
        } else {
            return rjson(0, "添加成功", $res['data']['priTmplId']);
        }
    }


    public function templetmsg()
    {
        if (request()->isPost()) {
            $arr = I('post.');
            M('weixin_subscribe')->where(['weixin_appid' => $this->wxconfig['appid']])->setField($arr);
            return $this->success('保存成功');
        }
        if (!M('weixin_subscribe')->where(['weixin_appid' => $this->wxconfig['appid']])->find()) {
            M('weixin_subscribe')->insertGetId(['weixin_appid' => $this->wxconfig['appid']]);
        }
        $info = M('weixin_subscribe')->where(['weixin_appid' => $this->wxconfig['appid']])->find();
        $this->assign('info', $info);
        return view();
    }
}
