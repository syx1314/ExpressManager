<?php

namespace app\admin\controller;


use app\admin\model\WeixinUser;
use app\common\library\Email;

class Weixin extends Admin
{
    private $wechat;

    public function _init()
    {
        if ($appid = I('appid')) {
            S('admin_edit_appid_' . UID, I('appid'));
        } else {
            $appid = S('admin_edit_appid_' . UID);
        }
        $this->wxconfig = M('weixin')->where(['appid' => $appid])->find();
        if ($this->wxconfig) {
            $this->wechat = new \Util\Wechat($this->wxconfig);
        }
    }

    //配置首页
    public function index()
    {
        $data = M('weixin')->where(['type' => 1])->select();
        foreach ($data as &$item) {
            $item['apiurl'] = U('wxapi/index', ['id' => $item['appid']], true, true);
            $wechat = new \Util\Wechat($item);
            $res = $wechat->get_access_token();
            $item['status'] = $res['errno'] != 0 ? $res['errmsg'] : '正常';
        }
        $this->assign('list', $data);
        return view();
    }

    //编辑
    public function editw()
    {
        if (request()->isPost()) {
            $arr = I('post.');
            $arr['type'] = 1;
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

    //日志页面
    public function log()
    {
        $map = [];
        $map['weixin_appid'] = $this->wxconfig['appid'];
        if (I('key')) {
            $map['text|msg_type|openid'] = array('like', '%' . I('key') . '%');
        }
        $data = M('weixin_log')->where($map)->order('create_time desc')->paginate(C('LIST_ROWS'));
        $this->assign('list', $data);
        return view();
    }



    //添加模板
    public function add_api_template()
    {
        $res = $this->wechat->apiAddTemplate(I('short_id'));
        if ($res['errno'] != 0) {
            return rjson(1, $res['errmsg']);
        } else {
            return rjson(0, "添加成功", $res['data']['template_id']);
        }
    }

    /**
     
     * 已添加的模板
     */
    public function all_template()
    {
        $res = $this->wechat->getAllTemplate();
        if ($res['errno'] != 0) return $this->error($res['errmsg']);
        $this->assign('list', $res['data']['template_list']);
        return view();
    }


    /**
     * 自动回复规则列表
     */
    public function reply()
    {
        $map['weixin_appid'] = $this->wxconfig['appid'];
        $list = M('weixin_reply')->where($map)->order('id desc')->paginate(C('LIST_ROWS'));
        $this->assign("list", $list);
        return view();
    }

    /**
     * 修改回复规则状态
     */
    public function reply_status()
    {
        $ids = I('ids/a');
        if (!$ids) {
            return $this->error("请选择要操作的数据！");
        }
        $map['id'] = ['in', $ids];
        if (M('weixin_reply')->where($map)->setField('status', I('status') == 1 ? 1 : 0)) {
            return $this->success("操作成功！");
        } else {
            return $this->error("操作失败！");
        }

    }

    /**
     * 删除自动回复
     */
    public function reply_del()
    {
        $ids = I('ids/a');
        if (!$ids) {
            return $this->error("请选择要操作的数据！");
        }
        $map['id'] = ['in', $ids];
        if (M('weixin_reply')->where($map)->delete()) {
            return $this->success("操作成功！");
        } else {
            return $this->error("操作失败！");
        }

    }

    /**
     * 修改自动回复规则
     */
    public function edit_reply()
    {
        if (request()->isPost()) {
            $arr = I('post.');
            if (I('id')) {
                if (M('weixin_reply')->update($arr)) {
                    return $this->success("保存成功", U('reply'));
                } else {
                    return $this->error("保存失败");
                }
            } else {
                $arr['create_time'] = time();
                $arr['weixin_appid'] = $this->wxconfig['appid'];
                if (M('weixin_reply')->insert($arr)) {
                    return $this->success("新增成功", U('reply'));
                } else {
                    return $this->error("新增失败");
                }
            }
        } else {
            if (I('id')) {
                $info = M('weixin_reply')->find(I('id'));
            } else {
                $info = [
                    'id' => "",
                    'type' => 1,
                    'reply_style' => 1
                ];
            }
            $this->assign("info", $info);
            return view();
        }
    }


    public function edit_reply_init()
    {
        if (I('id')) {
            $info = M('weixin_reply')->find(I('id'));
        } else {
            $info = [
                'id' => "",
                'type' => 1,
                'reply_style' => 1
            ];
        }
        return djson(0, '', $info);
    }

    /**
     * @var array
     * 菜单类型
     */
    protected
        $menuType = [
        'view' => '跳转URL',
        'click' => '点击推事件',
        'scancode_push' => '扫码推事件',
        'scancode_waitmsg' => '扫码推事件且弹出“消息接收中”提示框',
        'pic_sysphoto' => '弹出系统拍照发图',
        'pic_photo_or_album' => '弹出拍照或者相册发图',
        'pic_weixin' => '弹出微信相册发图器',
        'location_select' => '弹出地理位置选择器',
    ];

    /**
     * 菜单管理
     */
    public function menu()
    {
        $map['weixin_appid'] = $this->wxconfig['appid'];
        $list = M('weixin_menu')->where($map)->select();
        $list = $this->wechat->arr2tree($list, 'index', 'pindex', 'sub');
        $this->assign("list", $list);
        return view();
    }

    /**
     * 微信菜单编辑
     */
    public function edit()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            !isset($post['data']) && $this->error('访问出错，请稍候再试！');
            // 删除菜单
            if (empty($post['data'])) {
                try {
                    M('weixin_menu')->where(['weixin_appid' => $this->wxconfig['appid']])->delete();
                    $this->wechat->deleteMenu();
                } catch (\Exception $e) {
                    return $this->error('删除取消微信菜单失败，请稍候再试！' . $e->getMessage());
                }
                return $this->success('删除并取消微信菜单成功！', '');
            }
            // 数据过滤处理
            foreach ($post['data'] as &$vo) {
                isset($vo['content']) && ($vo['content'] = str_replace('"', "'", $vo['content']));
                $vo['weixin_appid'] = $this->wxconfig['appid'];
            }
            M('weixin_menu')->where(['weixin_appid' => $this->wxconfig['appid']])->delete();
            M('weixin_menu')->insertAll($post['data']);
            $res = $this->menu_push();
            if ($res['errno'] == 0) {
                return $this->success('保存发布菜单成功！');
            } else {
                return $this->error('微信菜单发布失败，请稍候再试！' . $res['errmsg']);
            }
        }
    }

    /**
     * 菜单创建
     */
    private function menu_push()
    {
        list($map, $field) = [['status' => '1', 'weixin_appid' => $this->wxconfig['appid']], 'id,index,pindex,name,type,content'];
        $result = M('weixin_menu')->field($field)->where($map)->order('sort ASC,id ASC')->select();
        foreach ($result as &$row) {
            empty($row['content']) && $row['content'] = uniqid();
            if ($row['type'] === 'miniprogram') {
                list($row['appid'], $row['url'], $row['pagepath']) = explode(',', "{$row['content']},,");
            } elseif ($row['type'] === 'view') {
                if (preg_match('#^(\w+:)?//#', $row['content'])) {
                    $row['url'] = $row['content'];
                } else {
                    $row['url'] = url($row['content'], '', true, true);
                }
            } elseif ($row['type'] === 'event') {
                if (isset($this->menuType[$row['content']])) {
                    list($row['type'], $row['key']) = [$row['content'], "wechat_menu#id#{$row['id']}"];
                }
            } elseif ($row['type'] === 'media_id') {
                $row['media_id'] = $row['content'];
            } elseif ($row['type'] === 'keys') {
                $row['type'] = 'click';
                $row['key'] = $row['content'];
            } else {
                $row['key'] = "wechat_menu#id#{$row['id']}";
                !in_array($row['type'], $this->menuType) && $row['type'] = 'click';
            }
            unset($row['content']);
        }
        $menus = $this->wechat->arr2tree($result, 'index', 'pindex', 'sub_button');
        //去除无效的字段
        foreach ($menus as &$menu) {
            unset($menu['index'], $menu['pindex'], $menu['id']);
            if (empty($menu['sub_button'])) {
                continue;
            }
            foreach ($menu['sub_button'] as &$submenu) {
                unset($submenu['index'], $submenu['pindex'], $submenu['id']);
            }
            unset($menu['type']);
        }
        $res = $this->wechat->createMenu(json_encode(['button' => $menus], JSON_UNESCAPED_UNICODE));
        return $res;
    }


    public function templetmsg()
    {
        if (request()->isPost()) {
            $arr = I('post.');
            M('weixin_templetmsg')->where(['weixin_appid' => $this->wxconfig['appid']])->setField($arr);
            return $this->success('保存成功');
        }
        if (!M('weixin_templetmsg')->where(['weixin_appid' => $this->wxconfig['appid']])->find()) {
            M('weixin_templetmsg')->insertGetId(['weixin_appid' => $this->wxconfig['appid']]);
        }
        $info = M('weixin_templetmsg')->where(['weixin_appid' => $this->wxconfig['appid']])->find();
        $this->assign('info', $info);
        return view();
    }
}
