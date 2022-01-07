<?php
/**
 * Created by PhpStorm.
 * User: 13788
 * Date: 2017/11/17
 * Time: 9:43
 */

namespace app\admin\controller;

use app\common\library\AgentGrade;


/**
 * 产品管理
 */
class Product extends Admin
{
    //产品列表
    public function index()
    {
        $map['is_del'] = 0;
        if (I('key')) {
            $map['name|desc'] = ['like', '%' . I('key') . '%'];
        }
        if (I('added')) {
            $map['added'] = intval(I('added')) - 1;
        }
        $type = 1;
        if (I('type')) {
            $type = I('type');
        }
        $map['type'] = $type;
        if (I('isp')) {
            $map['isp'] = I('isp');
        }
        if (I('cate_id')) {
            $map['cate_id'] = I('cate_id');
        }
        $lists = M('product')->where($map)->order("type,sort")->select();
        $grade = M('customer_grade')->order('grade_type asc,sort asc')->select();
        foreach ($lists as $key => $vo) {
            $gr = [];
            foreach ($grade as $k => $v) {
                $price = M('customer_grade_price')->where(['grade_id' => $v['id'], 'product_id' => $vo['id']])->find();
                array_push($gr, [
                    'id' => $v['id'],
                    'grade_name' => $v['grade_name'],
                    'price' => $vo['price'] + $price['ranges']
                ]);
            }
            $lists[$key]['grade'] = $gr;
            $lists[$key]['api_list'] = M('product_api')->where(['product_id' => $vo['id']])->order('sort asc')->select();
            $lists[$key]['cate_name'] = M('product_cate')->where(['id' => $vo['cate_id']])->value('cate');
        }
        $this->assign('_list', $lists);
        $this->assign('cates', M('product_cate')->where(['type' => $type])->order('sort asc')->select());
        $this->assign('types', C('PRODUCT_TYPE'));
        $this->assign('typeid', $type);
        return view();
    }

    //编辑
    public function edit()
    {
        if (request()->isPost()) {
            $isparr = I('isp/a');
            $arr = I('post.');
            if ($isparr) {
                $arr['isp'] = implode(',', $isparr);
            } else {
                $arr['isp'] = '';
            }
            unset($arr['id']);
            if (I('id')) {
                $data = M('product')->where(['id' => I('id')])->setField($arr);
                if ($data) {
                    return $this->success('保存成功');
                } else {
                    return $this->error('编辑失败');
                }
            } else {
                $aid = M('product')->insertGetId($arr);
                if ($aid) {
                    AgentGrade::initPrice();
                    return $this->success('新增成功');
                } else {
                    return $this->error('新增失败');
                }
            }
        } else {
            $info = M('product')->where(['id' => I('id')])->find();
            $this->assign('info', $info);
            if ($info) {
                $type = $info['type'];
            } else {
                $type = I('type');
            }
            $this->assign('cates', M('product_cate')->where(['type' => $type])->order('sort asc')->select());
            return view();
        }
    }


    //删除
    public function deletes()
    {
        if (M('product')->where(['id' => I('id')])->setField(['is_del' => 1])) {
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败');
        }
    }

    //上下架
    public function added()
    {
        $ids = I('id/a');
        $added = I('added') == 0 ? 0 : 1;
        $products = M('product')->where(['id' => ['in', $ids], 'added' => $added == 0 ? 1 : 0])->select();
        if (!$products) {
            return $this->error('未查询到产品');
        }
        $counts = 0;
        $errmsg = '';
        foreach ($products as $product) {
            $state = M('product')->where(['id' => $product['id']])->setField(['added' => $added]);
            if ($state) {
                $counts++;
            } else {
                $errmsg .= $product['name'] . '失败;';
            }
        }
        if ($counts == 0) {
            return $this->error('操作失败,' . $errmsg);
        }
        return $this->success("成功处理" . $counts . "条");
    }


    public function cates()
    {
        $map = [];
        if (I('type')) {
            $map['type'] = I('type');
        }
        $this->assign('_list', M('product_cate')->where($map)->order('sort asc')->select());
        return view();
    }

    //编辑
    public function cate_edit()
    {
        $info = M('product_cate')->where(['id' => I('id')])->find();
        $this->assign('info', $info);
        return view();
    }

    //编辑_保存
    public function cate_edit_save()
    {
        $arr = I('post.');
        unset($arr['id']);
        if (I('id')) {
            $data = M('product_cate')->where(['id' => I('id')])->setField($arr);
            if ($data) {
                return $this->success('保存成功');
            } else {
                return $this->error('编辑失败');
            }
        } else {
            $aid = M('product_cate')->insertGetId($arr);
            if ($aid) {
                return $this->success('新增成功');
            } else {
                return $this->error('新增失败');
            }
        }
    }

    public function cate_del()
    {
        if (M('product')->where(['cate_id' => I('id'), 'is_del' => 0])->find()) {
            return $this->error('分类下还有产品，请先移除产品或者删除产品');
        }
        if (M('product_cate')->where(['id' => I('id')])->delete()) {
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败');
        }
    }

    public function reapi()
    {
        return rjson(0, 'ok', M('reapi')->where(['is_del' => 0])->select());
    }

    public function reapi_param()
    {
        return rjson(0, 'ok', M('reapi_param')->where(['reapi_id' => I('reapi_id')])->select());
    }

    public function api()
    {
        $lists = M('product_api')->where(['product_id' => I('id')])->order('status desc , sort asc')->select();
        $this->assign('_list', $lists);
        return view();
    }

    public function edit_api()
    {
        if (request()->isPost()) {
            $arr = I('post.');
            if (I('id')) {
                $data = M('product_api')->where(['id' => I('id')])->setField($arr);
            } else {
                $data = M('product_api')->insertGetId($arr);
            }
            if ($data) {
                return rjson(0, '设置成功');
            } else {
                return rjson(1, '修改失败，您可能未做任何修改');
            }
        } else {
            $info = M('product_api')->where(['id' => I('id')])->find();
            $this->assign('info', $info ? $info : ['id' => 0, 'status' => 1, 'sort' => 0, 'reapi_id' => 0, 'param_id' => 0]);
            $this->assign('isps', C('ISP_TEXT'));
            return view();
        }
    }

    //上下架
    public function api_status_cg()
    {
        $data = M('product_api')->where(['id' => I('id')])->setField(['status' => I('status')]);
        if (!$data) {
            return $this->error('操作失败');
        }
        return $this->success("操作成功");
    }

    public function api_del()
    {
        if (M('product_api')->where(['id' => I('id')])->delete()) {
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败');
        }
    }


    public function phone()
    {
        $map = [];
        if (I('key')) {
            $map['prefix|phone|province|city|isp'] = I('key');
        }
        $list = M('phone')->where($map)->order('prefix asc')->paginate(C('LIST_ROWS'));
        $this->assign('_list', $list);
        return view();
    }

    //号码编辑
    public function phone_edit()
    {
        if (request()->isPost()) {
            $arr = I('post.');
            if (M('phone')->where(['phone' => I('phone')])->find()) {
                unset($arr['phone']);
                $data = M('phone')->where(['phone' => I('phone')])->update($arr);
                if ($data) {
                    return $this->success('更新成功');
                } else {
                    return $this->error('更新失败');
                }
            } else {
                $data = M('phone')->insert($arr);
                if ($data) {
                    return $this->success('新增成功');
                } else {
                    return $this->error('新增失败');
                }
            }
        } else {
            if (I('phone')) {
                $info = M('phone')->where(['phone' => I('phone')])->find();
                $this->assign("info", $info);
            }
        }
        return view();
    }

    public function phone_del()
    {
        if (M('phone')->where(['phone' => I('phone')])->delete()) {
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败');
        }
    }


    public function elecity()
    {
        $list = M('electricity_city')->order('initial asc,sort asc')->select();
        $this->assign('_list', $list);
        return view();
    }

    //号码编辑
    public function elecity_edit()
    {
        if (request()->isPost()) {
            $arr = I('post.');
            unset($arr['id']);
            $arr['initial'] = strtoupper($arr['initial']);
            if (I('id')) {
                $data = M('electricity_city')->where(['id' => I('id')])->update($arr);
                if ($data) {
                    return $this->success('更新成功');
                } else {
                    return $this->error('更新失败');
                }
            } else {
                $data = M('electricity_city')->insert($arr);
                if ($data) {
                    return $this->success('新增成功');
                } else {
                    return $this->error('新增失败');
                }
            }
        } else {
            $info = M('electricity_city')->where(['id' => I('id')])->find();
            $this->assign("info", $info);
        }
        return view();
    }

    public function elecity_del()
    {
        if (M('electricity_city')->where(['id' => I('id')])->delete()) {
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败');
        }
    }
}