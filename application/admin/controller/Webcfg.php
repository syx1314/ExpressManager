<?php

namespace app\admin\controller;

use  app\common\library\Configapi;
use think\Exception;
use Util\GoogleAuth;

/**
 * 邮箱：
 */
class Webcfg extends Admin
{
    public function index()
    {
        $typec = C('CONFIG_GROUP_LIST');
        $typel = [];
        foreach ($typec as $k => $v) {
            $typel[] = ['id' => $k, 'name' => $v];
        }
        if (I('name')) {
            $map['name'] = ['in', I('name')];
        }
        if (I('group')) {
            $griupstr = I('group');
            $grouparr = (explode(',', $griupstr));
            foreach ($typel as $k => $item) {
                if (!in_array($item['id'], $grouparr))
                    unset($typel[$k]);
            }
        }
        $list = [];
        foreach ($typel as $k => $tp) {
            $map['group'] = $tp['id'];
            $map['status'] = 1;
            $item = M('config')->where($map)->order('sort')->select();
            $item && array_push($list, ['type' => $tp['name'], 'item' => $item]);
        }
        $this->assign("typelist", $list);
        return view();
    }

    public function edit()
    {
        $config = I('post.');
        $ret = false;
        if ($config && is_array($config)) {
            foreach ($config as $name => $value) {
                $map = array('name' => $name);
                $data = M('config')->where($map)->find();
                if ($data['type'] == 3) {
                    try {
                        $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
                        if (strpos($value, ':')) {
                            foreach ($array as $val) {
                                list($k, $v) = explode(':', $val);
                            }
                        }
                    }//捕获异常
                    catch (Exception $e) {
                        continue;
                    }
                }
                if (M('config')->where($map)->update(['value' => $value])) {
                    $ret = true;
                }
            }
        }
        if ($ret) {
            Configapi::clear();
            return $this->success('保存成功！');
        } else {
            Configapi::clear();
            return $this->error('保存失败！');
        }
    }

    function curl_file_get_contents($durl)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $durl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 获取数据返回
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true); // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回   
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public function config()
    {
        /* 查询条件初始化 */
        $map = [];
        if (I('group_type') != -1 && I('group_type') != null) {
            $map['group'] = I('group_type');
        }
        if (I('key')) {
            $map['name|title'] = array('like', '%' . I('key') . '%');
        }
        $list = M('config')->where($map)->order('group,sort,id')->select();
        $this->assign('group', C('CONFIG_GROUP_LIST'));
        $this->assign('list', $list);
        return view();
    }

    /**
     * 新增配置
     */
    public function add($id = 0)
    {
        if (request()->isPost()) {
            $arr = I('post.');
            if (I('post.id')) {
                $arr['update_time'] = time();
                $data = M('config')->update($arr);
                if ($data) {
                    Configapi::clear();
                    return $this->success('更新成功');
                } else {
                    return $this->error('更新失败');
                }
            } else {
                $arr['status'] = 1;
                $arr['create_time'] = time();
                $arr['update_time'] = time();
                $arr['name'] = strtoupper($arr['name']);
                $data = M('config')->insert($arr);
                if ($data) {
                    Configapi::clear();
                    return $this->success('新增成功');
                } else {
                    return $this->error('新增失败');
                }
            }
        } else {
            /* 获取数据 */
            $info = M('Config')->field(true)->find($id);
//            if ($info['sys'] == 1) {
//              return $this->error('非法请求！');
//            }
            if (false === $info) {
                return $this->error('获取配置信息错误');
            }
            $this->meta_title = '新增配置';
            $this->assign('group_list', C('CONFIG_GROUP_LIST'));
            $this->assign('type_list', C('CONFIG_TYPE_LIST'));
            $this->assign('info', $info);
            return view();
        }
    }

    /**
     * 删除配置
     */
    public function del()
    {
        $id = array_unique((array)I('id', 0));
        if (empty($id)) {
            return $this->error('请选择要操作的数据!');
        }
        $map = array('id' => array('in', $id));
        $data = M('Config')->where($map)->find();
        if ($data['sys'] == 1) {
            return $this->error('删除失败！');
        }
        if (M('Config')->where($map)->delete()) {
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败！');
        }
    }

    /**
     * 删除配置
     */
    public function set_status()
    {
        $id = I('id');
        if (empty($id)) {
            return $this->error('请选择要操作的数据!');
        }
        if (M('Config')->where(['id' => $id])->setField(['status' => I('status')])) {
            return $this->success('操作成功');
        } else {
            return $this->error('操作失败！');
        }
    }

    /**
     * 配置排序
     */
    public function sort()
    {
        if (request()->isGet()) {
            $ids = I('get.ids');
            //获取排序的数据
            $map = array('status' => array('gt', -1));
            if (!empty($ids)) {
                $map['id'] = array('in', $ids);
            } elseif (I('group')) {
                $map['group'] = I('group');
            }
            $list = M('Config')->where($map)->field('id,title')->order('sort asc,id asc')->select();
            $this->assign('list', $list);
            $this->meta_title = '配置排序';
            return view();
        } elseif (request()->isPost()) {
            $ids = I('post.ids');
            $ids = explode(',', $ids);
            foreach ($ids as $key => $value) {
                $res = M('Config')->where(array('id' => $value))->setField('sort', $key + 1);
            }
            if ($res !== false) {
                return $this->success('排序成功！', U('config'));
            } else {
                $this->eorror('排序失败！');
            }
        } else {
            return $this->error('非法请求！');
        }
    }

    public function clear()
    {
        if (request()->isPost()) {
            set_time_limit(0);
            $goret = GoogleAuth::verifyCode($this->adminuser['google_auth_secret'], I('verifycode'), 1);
            if (!$goret) {
                return $this->error("谷歌身份验证码错误！");
            }
            $time = strtotime(I('time'));
            M('agent_excel')->where(['create_time' => ['lt', $time]])->delete();
            M('agent_proder_excel')->where(['create_time' => ['lt', $time]])->delete();
            M('apinotify_log')->where(['create_time' => ['lt', $time]])->delete();
            M('porder')->where(['create_time' => ['lt', $time]])->delete();
            M('porder_log')->where(['create_time' => ['lt', $time]])->delete();
            M('proder_excel')->where(['create_time' => ['lt', $time]])->delete();
            return $this->success('清除成功！');
        } else {
            return view();
        }
    }
}
