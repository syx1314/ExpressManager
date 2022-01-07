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
class CustomerHezuoPrice extends Model
{

    //初始化合作商价格
    public static function initPrice()
    {
        $grades = M('customer_grade')->where(['is_zdy_price' => 1])->select();

        $list = M('customer')->where(['grade_id' => ['in', array_column($grades, 'id')]])->select();

        foreach ($list as $k => $v) {
            $nolist = M()->query("select p.id from dyr_product as p left join dyr_customer_hezuo_price as gp on gp.product_id=p.id and p.is_del=0 and gp.customer_id=" . $v['id'] . " where gp.id is null");
            foreach ($nolist as $key => $vo) {
                $aid = M('customer_hezuo_price')->where([
                    'customer_id' => $v['id'],
                    'product_id' => $vo['id']
                ])->find();
                if ($aid) continue;
                $ga_ranges = M('customer_grade_price')->where(['grade_id' => $v['grade_id'], 'product_id' => $vo['id']])->value('ranges');
                $g1_ranges = M('customer_grade_price')->where(['grade_id' => 1, 'product_id' => $vo['id']])->value('ranges');
                $ranges = $g1_ranges - $ga_ranges;
                M('customer_hezuo_price')->insertGetId([
                    'customer_id' => $v['id'],
                    'product_id' => $vo['id'],
                    'ranges' => $ranges ? $ranges : 0
                ]);
            }
        }
    }


}