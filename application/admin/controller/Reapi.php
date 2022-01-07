<?php
/**
 * Created by PhpStorm.
 * User: 13788
 * Date: 2017/11/17
 * Time: 9:43
 */

namespace app\admin\controller;


/**
 * 充值接口
 * @package app\admin\controller
 
 *
 */
class Reapi extends Admin
{
    //用户列表
    public function index()
    {
        $list = M('reapi')->where(['is_del' => 0])->select();
        $this->assign('_list', $list);
        return view();
    }

    //编辑
    public function edit()
    {
        if (request()->isPost()) {
            $arr = $_POST;
            if (I('id')) {
                $data = M('reapi')->where(['id' => I('id')])->setField($arr);
                if ($data) {
                    return $this->success('保存成功');
                } else {
                    return $this->error('编辑失败');
                }
            } else {
                $aid = M('reapi')->insertGetId($arr);
                if ($aid) {
                    return $this->success('新增成功');
                } else {
                    return $this->error('新增失败');
                }
            }
        } else {
            $info = M('reapi')->where(['id' => I('id')])->find();
            $this->assign('info', $info);
            return view();
        }
    }

    //套餐
    public function param()
    {
        $api = M('reapi')->where(['id' => I('id')])->find();
        if (!$api) {
            return $this->error('参数错误');
        }
        $this->assign('api', $api);
        $list = M('reapi_param')->where(['reapi_id' => I('id')])->select();
        $this->assign('_list', $list);
        return view();
    }

    //套餐编辑
    public function param_edit()
    {
        if (request()->isPost()) {
            $arr = $_POST;
            if (I('id')) {
                $data = M('reapi_param')->where(['id' => I('id')])->setField($arr);
                if ($data) {
                    return $this->success('保存成功');
                } else {
                    return $this->error('编辑失败');
                }
            } else {
                $data = M('reapi_param')->insertGetId($arr);
                if ($data) {
                    return $this->success('添加成功');
                } else {
                    return $this->error('添加失败');
                }
            }
        } else {
            $api = M('reapi')->where(['id' => I('reapi_id')])->find();
            if (!$api) {
                return $this->error('参数错误');
            }
            $info = M('reapi_param')->where(['id' => I('id')])->find();
            $this->assign('info', $info);
            return view();
        }
    }

    //删除
    public function deletes()
    {
        $reapi = M('reapi')->where(['id' => I('id')])->find();
        if (!$reapi) {
            return $this->error('未找到接口');
        }
        if (M('product_api')->where(['reapi_id' => $reapi['id']])->find()) {
            return $this->error('该接口还有产品在使用中，请先取消接口绑定的所有产品');
        }
        if (M('reapi')->where(['id' => $reapi['id']])->setField(['is_del' => 1])) {
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败');
        }
    }

    //删除
    public function deletes_param()
    {
        $param = M('reapi_param')->where(['id' => I('id')])->find();
        if (!$param) {
            return $this->error('未找到套餐');
        }
        if (M('product_api')->where(['param_id' => $param['id']])->find()) {
            return $this->error('该套餐还有产品在使用中，请先取消接口套餐绑定的所有产品');
        }
        if (M('reapi_param')->where(['id' => $param['id']])->delete()) {
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败');
        }
    }

    //查询接口套餐
    public function get_reapi_param()
    {
        $map = [];
        if (I('reapi_id')) {
            $map['reapi_id'] = I('reapi_id');
        }
        $lists = M('reapi_param')->where($map)->order("desc asc")->select();
        return djson(0, 'ok', $lists);
    }

}