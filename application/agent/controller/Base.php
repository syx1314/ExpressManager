<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2017-11-13
 * Time: 17:30
 */

namespace app\agent\controller;


/**
 
 **/
class Base extends \app\common\controller\Base
{
    /**
     
     * 初始化
     */
    public function _commonbase()
    {
        //你的初始化
        //调用二级初始化函数
        if (method_exists($this, '_dayuanren')) {
            $this->_dayuanren();
        }
    }

    /**
     
     * @return \think\response\View
     * 找不到方法
     */
    public function _empty()
    {
        return view('base/_empty');
    }

    protected function success($msg = '', $url = null, $data = '', $wait = 3, array $header = [])
    {
        return djson(0, $msg, ['url' => $url, 'wait' => $wait, 'data' => $data])->send();
    }

    protected function error($msg = '', $url = null, $data = '', $wait = 3, array $header = [])
    {
        return djson(1, $msg, ['url' => $url, 'wait' => $wait, 'data' => $data])->send();
    }
}