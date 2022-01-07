<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2018-01-16
 * Time: 16:20
 */

namespace app\api\controller;

/**
 * 作者：
 * 邮箱：
 **/
class Verify extends Base
{
    //控制器中 生成验证码
    public function img()
    {
        $id = I('?id') ? md5(I('id')) : 0;
        $captcha = new \Util\Captcha(82, 34, 4);
        $captcha->showImg();
        S('piccode' . $id, strtolower($captcha->getCaptcha()));
        return;
    }

    /**
     * 检查验证码石否正确
     */
    public function check($code)
    {
        $id = I('?id') ? md5(I('id')) : 0;
        if (S('piccode' . $id) == $code) {
            return djson(0, '正确');
        } else {
            return djson(1, '错误');
        }
    }
}