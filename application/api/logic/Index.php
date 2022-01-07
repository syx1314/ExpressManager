<?php

namespace app\api\logic;

/**
 * Created by PhpStorm.
 * User: liaoqiang
 * Date: 2018/11/12
 * Time: 17:40
 * 该类和我们的controller类名和方法名是一一对应的
 */

use think\Logic;

class Index extends Logic
{
    /**
     * allowMethods  get|post
     * rules  array
     * message array
     * 验证规格 https://www.kancloud.cn/manual/thinkphp5/129320
     */
    public function get_product_mobile()
    {
        $this->allowMethods = 'post';
        $this->rules = [
            'mobile' => 'require|number|length:11'
        ];
        $this->message = [
            'mobile' => '手机格式错误',
        ];
    }

    public function test()
    {
        $this->allowMethods = 'get';
        $this->allowMethods = 'post';
        $this->rules = [
            'name' => 'require|max:25|min:2',//必须存在，最大长度25最小长度2
            'email' => 'require|email',//必须存在，邮箱
            'age' => 'number|between:1,120',//1-120的数字
            'numbt' => 'number|notBetween:1,10',//数字不能再1-10之间
            'status' => 'in:1,2,3',//值必须是1,2,3中的一个
            'status2' => 'notIn:1,2,3',//值不能是1,2,3中的一个
            'card' => 'number|length:16,18',//长度为16-18位的数字
            'scard' => 'number|length:18',//长度为18位的数字
            'float' => 'float',//浮点数
            'repassword' => 'require|confirm:password',//验证某个字段是否和另外一个字段的值一致
            'account' => 'require|different:name',//验证某个字段是否和另外一个字段的值不一致
            'boolean' => 'boolean',//bool型
            'array' => 'array',//数组型
            'score1' => 'eq:100',//验证是否等于某个值
            'score2' => 'egt:60',//验证是否大于等于某个值
            'score3' => 'elt:100',//验证是否小于等于某个值
            'score4' => 'lt:100',//验证是否小于某个值
            'price' => 'lt:market_price',//验证对比其他字段大小（数值大小对比）eq,egt,lt,elt
            'accepted' => 'accepted',// yes, on, 或是 1。常用于确认"服务条款"
            'date' => 'date',//有效日期
            'alpha' => 'alpha',//字母
            'alphaNum' => 'alphaNum',//字母和数字
            'alphaDash' => 'alphaDash',//字母数组下划线破折号
            'chs' => 'chs',//汉字
            'chsAlpha' => 'chsAlpha',//汉字、字母
            'chsAlphaNum' => 'chsAlphaNum',//汉字、字母和数字
            'chsDash' => 'chsDash',//汉字、字母、数字和下划线_及破折号-
            'activeUrl' => 'activeUrl',//有效的域名或者IP
            'url' => 'url',//有效的URL地址
            'ip' => 'ip',//有效的IP地址
            'create_time' => 'dateFormat:y-m-d',//指定格式的日期 y-m-d
            'begin_time' => 'after:2016-3-18',//字段的值是否在某个日期之后
            'end_time' => 'before:2016-10-01',//字段的值是否在某个日期之前
            'expire_time' => 'expire:2016-2-1,2016-10-01',//字段的值是否在某段日期之内
            'chepai1' => ['regex' => '/[京津沪渝冀豫云辽黑湘皖鲁新苏浙赣鄂桂甘晋蒙陕吉闽贵粤青藏川宁琼使领 A-Z]{1}[A-HJ-NP-Z]{1}(([0-9]{5}[DF])|([DF][A-HJ-NP-Z0-9][0-9]{4}))$/'],//正则：新能源车牌号
            'chepai2' => ['regex' => '/^[京津沪渝冀豫云辽黑湘皖鲁新苏浙赣鄂桂甘晋蒙陕吉闽贵粤青藏川宁琼使领 A-Z]{1}[A-HJ-NP-Z]{1}[A-Z0-9]{4}[A-Z0-9挂学警港澳]{1}$/'],//正则：非新能源车牌号
            'mobile' => ['regex' => '/^1((3[\d])|(4[5,6,7,9])|(5[0-3,5-9])|(6[5-7])|(7[0-8])|(8[\d])|(9[1,8,9]))\d{8}$/'],//正则：手机号(严谨), 根据工信部2019年最新公布的手机号段
            'tel' => ['regex' => '/^\d{8}(0\d|11|12)([0-2]\d|30|31)\d{3}$/'],//正则：国内座机电话,如: 0341-86091234
            'idcard' => ['regex' => '/^\d{6}(18|19|20)\d{2}(0\d|11|12)([0-2]\d|30|31)\d{3}(\d|X|x)$/'],//正则：二代身份证号(18位数字),最后一位是校验位,可能为数字或字符X
            'qq' => ['regex' => '/^[1-9][0-9]{4,10}$/'],//正则：qq号
            'post_code' => ['regex' => '/^(0[1-7]|1[0-356]|2[0-7]|3[0-6]|4[0-7]|5[1-7]|6[1-7]|7[0-5]|8[013-6])\d{4}$/']//正则：中国邮政编码
        ];
        $this->message = [
            'name.require' => '名称必须',
            'name.max' => '名称最多不能超过25个字符',
            'name.min' => '名称长度不能少于2个字符',
            'email' => '邮箱格式错误',
            'age' => '年龄格式错误'
        ];
    }
}