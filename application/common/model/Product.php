<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2017-11-14
 * Time: 11:13
 */

namespace app\common\model;

use think\Model;

/**
 
 **/
class Product extends Model
{

    public static function getProducts($map, $user_id)
    {
        $user = M('customer')->where(['id' => $user_id, 'is_del' => 0])->find();
        if (!$user) {
            return false;
        }
        $cateids = M('product p')->where($map)->group('p.cate_id')->field('p.cate_id')->select();
        if(!$cateids) return false;
        $cates = M('product_cate')->where(['id' => ['in', array_column($cateids, 'cate_id')]])->order('sort asc')->select();
        if(!$cates) return false;
        $grades = M('customer_grade')->where(['is_zdy_price' => 1])->select();
        if (in_array($user['grade_id'] , [1, 3]) && $user['f_id'] && $fuser = M('Customer')->where(['id' => $user['f_id'], 'grade_id' => ['in', array_column($grades, 'id')]])->find()) {
            $price_sql = "(p.price+(select ranges from dyr_customer_grade_price where grade_id=" . $fuser['grade_id'] . " and product_id=p.id)+(select ranges from dyr_customer_hezuo_price where customer_id=" . $fuser['id'] . " and product_id=p.id)) as price";
            $ystag_sql = "(select ys_tag from dyr_customer_hezuo_price where customer_id=" . $fuser['id'] . " and product_id=p.id) as ys_tag";
        } else {
            $price_sql = "(p.price+(select ranges from dyr_customer_grade_price where grade_id=" . $user['grade_id']  . " and product_id=p.id)) as price";
            $ystag_sql = "p.ys_tag";
        }
        foreach ($cates as &$cate) {
            $map['p.cate_id'] = $cate['id'];
            $cate['products'] = M('product p')
                ->where($map)
                ->order("p.type,p.sort asc")
                ->field("p.id,p.name,p.desc,p.api_open,p.isp," . $ystag_sql . "," . $price_sql . ",(p.price+(select ranges from dyr_customer_grade_price where grade_id=1 and product_id=p.id)) as y_price,p.max_price,p.type,(select cate from dyr_product_cate where id=p.cate_id) as cate_name")
                ->select();
        }
        return $cates;
    }

    public static function getProduct($map, $user_id)
    {
        $user = M('customer')->where(['id' => $user_id, 'is_del' => 0])->find();
        if (!$user) {
            return false;
        }
        $grades = M('customer_grade')->where(['is_zdy_price' => 1])->select();
        if (in_array($user['grade_id'], [1, 3]) && $user['f_id'] && $fuser = M('Customer')->where(['id' => $user['f_id'], 'grade_id' => ['in', array_column($grades, 'id')]])->find()) {
            $price_sql = "(p.price+(select ranges from dyr_customer_grade_price where grade_id=" . $fuser['grade_id'] . " and product_id=p.id)+(select ranges from dyr_customer_hezuo_price where customer_id=" . $fuser['id'] . " and product_id=p.id)) as price";
            $ystag_sql = "(select ys_tag from dyr_customer_hezuo_price where customer_id=" . $fuser['id'] . " and product_id=p.id) as ys_tag";
        } else {
            $price_sql = "(p.price+(select ranges from dyr_customer_grade_price where grade_id=" . $user['grade_id'] . " and product_id=p.id)) as price";
            $ystag_sql = "p.ys_tag";
        }
        $info = M('product p')
            ->where($map)
            ->order("p.sort asc")
            ->field("p.id,p.name,p.desc,p.api_open,p.isp," . $ystag_sql . "," . $price_sql . ",(p.price+(select ranges from dyr_customer_grade_price where grade_id=1 and product_id=p.id)) as y_price,p.max_price,p.type,(select cate from dyr_product_cate where id=p.cate_id) as cate_name")
            ->find();
        if (!$info) {
            return false;
        }
        $apiarr = M('product_api')->where(['product_id' => $info['id'], 'status' => 1])->order('sort')->select();
        $info['api_open'] = ($info['api_open'] && count($apiarr) > 0) ? 1 : 0;
        $info['api_arr'] = json_encode($apiarr);
        return $info;
    }

}