<?php
/**
 * Created by PhpStorm.
 * User: 13788
 * Date: 2017/11/17
 * Time: 9:43
 */

namespace app\admin\controller;


/**
 * 凭证配置
 */
class Voucher extends Admin
{
    //列表
    public function index()
    {
        $list = M('voucher_config')->select();
        $this->assign('_list', $list);
        $this->assign('_prefix', HTTP_TYPE . $_SERVER['HTTP_HOST']);
        return view();
    }

    //删除
    public function deletes()
    {
        if (M('voucher_config')->where(['id' => I('id')])->delete()) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    //编辑
    public function edit()
    {
        if (request()->isPost()) {
            $arr = I('post.');
            if (I('id')) {
                $data = M('voucher_config')->where(['id' => I('id')])->setField($arr);
                if ($data) {
                    $this->success('保存成功');
                } else {
                    $this->error('编辑失败');
                }
            } else {
                $aid = M('voucher_config')->insertGetId($arr);
                if ($aid) {
                    $this->success('新增成功');
                } else {
                    $this->error('新增失败');
                }
            }
        } else {
            $info = M('voucher_config')->where(['id' => I('id')])->find();
            $this->assign('info', $info);
            return view();
        }
    }
}