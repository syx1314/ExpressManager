<?php
/**
 * Created by PhpStorm.
 * User: 13788
 * Date: 2017/11/17
 * Time: 9:43
 */

namespace app\agent\controller;

use app\common\model\CustomerHezuoPrice;
use app\common\model\Product as ProductModel;

/**
 * Class Member
 * @package app\admin\controller
 
 *
 */
class Product extends Admin
{
    //产品列表
    public function index()
    {
        $map['p.is_del'] = 0;
        $map['p.added'] = 1;
        if (I('type')) {
            $map['p.type'] = I('type');
        }
        if (I('key')) {
            $map['p.name|p.desc'] = ['like', '%' . I('key') . '%'];;
        }
        CustomerHezuoPrice::initPrice();
        $lists = ProductModel::getProducts($map, $this->user['id']);
        $grade = M('customer_grade')->where(['id' => $this->user['grade_id']])->find();
        if ($lists && $grade['is_zdy_price'] == 1) {
            foreach ($lists as &$cate) {
                foreach ($cate['products'] as &$item) {
                    $hzprice = M('customer_hezuo_price')->where(['customer_id' => $this->user['id'], 'product_id' => $item['id']])->field('id as rangesid,ranges,ys_tag')->find();
                    if (!$hzprice) {
                        continue;
                    }
                    $item['ys_tag'] = $hzprice['ys_tag'];
                    $item['rangesid'] = $hzprice['rangesid'];
                    $item['ranges'] = $hzprice['ranges'];
                }
            }
        }
        $this->assign('_list', $lists);
        $this->assign('is_zdy_price', $grade['is_zdy_price']);
        return view();
    }

    //编辑
    public function hz_price_edit()
    {
        if (request()->isPost()) {
            $price = M('customer_hezuo_price')->where(['customer_id' => $this->user['id'], 'id' => I('id')])->field('id,product_id,customer_id')->find();
            $map['p.is_del'] = 0;
            $map['p.id'] = $price['product_id'];
            $customer = M('customer')->where(['id' => $price['customer_id']])->find();
            $product = ProductModel::getProduct($map, $customer['id']);

            $ranges = floatval(I('ranges'));
            if ($ranges < 0) {
                return $this->error('浮动金额不能小于0');
            }
            if (floatval($product['max_price']) > 0 && ($product['price'] + $ranges) > $product['max_price']) {
                return $this->error('不能设置高于封顶价格');
            }
            $data = M('customer_hezuo_price')->where(['customer_id' => $this->user['id'], 'id' => $price['id']])->setField(['ranges' => $ranges]);
            if ($data) {
                return $this->success('保存成功');
            } else {
                return $this->error('编辑失败');
            }
        } else {
            $info = M('customer_hezuo_price')->where(['id' => I('id')])->find();
            $this->assign('info', $info);
            return view();
        }
    }

    //编辑
    public function hz_price_ystag_edit()
    {
        $data = M('customer_hezuo_price')->where(['customer_id' => $this->user['id'], 'id' => I('id')])->setField(['ys_tag' => I('ys_tag')]);
        if ($data) {
            return $this->success('保存成功');
        } else {
            return $this->error('编辑失败');
        }
    }
}