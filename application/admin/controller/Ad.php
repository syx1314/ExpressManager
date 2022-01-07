<?php

namespace app\admin\controller;
/**
 
 */

class Ad extends Admin
{
    //广告位列表
    public function pindex()
    {
        $list = M('ad')->select();
        $this->assign('list', $list);
        return view();
    }

    //广告位新增/编辑
    public function pedit()
    {
        if (request()->isPost()) {
            $arr = I('post.');
            if (I('post.id')) {
                $data = M('Ad')->update($arr);
                if ($data) {
                    return $this->success('更新成功', U('pindex'));
                } else {
                    return $this->error('更新失败');
                }
            } else {
                $data = M('Ad')->insert($arr);
                if ($data) {
                    return $this->success('新增成功', U('pindex'));
                } else {
                    return $this->error('新增失败');
                }
            }
        } else {
            if (I('id')) {
                $info = M('ad')->find(I('id'));
                $this->assign("info", $info);
            }
        }
        return view();
    }

    //删除广告位
    public function pdel()
    {
        $ret = M('ad')->where('id=' . I('id'))->delete();
        $ret1 = M('adc')->where('ad_id=' . I('id'))->delete();
        if ($ret) {
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败');
        }
    }


    //广告位详情列表
    public function cindex()
    {
        $list = M('adc')->where('ad_id', I('ad_id'))->order('sort')->select();
        $this->assign('list', $list);
        return view();
    }

    //广告位详情新增/编辑
    public function cedit()
    {
        if (request()->isPost()) {
            $arr = I('post.');
            if (I('post.id')) {
                $data = M('adc')->update($arr);
                if ($data) {
                    return $this->success('更新成功');
                } else {
                    return $this->error('更新失败');
                }
            } else {
                $arr['create_time'] = time();
                $data = M('adc')->insert($arr);
                if ($data) {
                    return $this->success('新增成功');
                } else {
                    return $this->error('新增失败');
                }
            }
        } else {
            if (I('id')) {
                $info = M('adc')->find(I('id'));
                $this->assign("info", $info);
            }
        }
        return view();
    }

    //删除广告位图片
    public function cdel()
    {
        $ret = M('adc')->where('id=' . I('id'))->delete();
        if ($ret) {
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败');
        }
    }

}
