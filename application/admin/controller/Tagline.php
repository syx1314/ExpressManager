<?php
/**
 * Created by PhpStorm.
 * User: 13788
 * Date: 2017/11/17
 * Time: 9:43
 */

namespace app\admin\controller;


/**
 * Class Member
 * @package app\admin\controller
 
 *
 */
class Tagline extends Admin
{
    //订单列表
    public function index()
    {
        $list = M('tagline_txt')->order("sort asc")->select();
        $this->assign('_list', $list);
        return view();
    }

    //删除
    public function deletes()
    {
        if (M('tagline_txt')->where(['id' => I('id')])->delete()) {
          return $this->success('删除成功');
        } else {
          return $this->error('删除失败');
        }
    }

    //编辑
    public function edit()
    {
        if (request()->isPost()) {
            $arr = I('post.');
            if (I('id')) {
                $data = M('tagline_txt')->where(['id' => I('id')])->setField($arr);
                if ($data) {
                  return $this->success('保存成功');
                } else {
                  return $this->error('编辑失败');
                }
            } else {
                $aid = M('tagline_txt')->insertGetId($arr);
                if ($aid) {
                  return $this->success('新增成功');
                } else {
                  return $this->error('新增失败');
                }
            }
        } else {
            $info = M('tagline_txt')->where(['id' => I('id')])->find();
            $this->assign('info', $info);
            return view();
        }
    }
}