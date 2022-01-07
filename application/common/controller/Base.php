<?php

namespace app\common\controller;

use app\common\model\Menu;
use think\Controller;
use app\common\library\Configapi;
use Util\Http;


/**
 
 **/
class Base extends Controller
{

    /**
     * 初始化
     */
    public function _initialize()
    {
        //读取系统动态配置
        C(Configapi::getconfig());
        //自动生成菜单
        $this->autoMenu();
        //调用二级初始化函数
        if (method_exists($this, '_commonbase')) {
            $this->_commonbase();
        }
    }

    //自動生成菜單
    private function autoMenu()
    {
        //查询自己有没有
        $url = request()->controller() . '/' . request()->action();
        $module = request()->module();
        if (in_array($module, C('MENU_MODULE')) && method_exists($this, request()->action())) {
            $menu = new Menu();
            $menu->autoMenu($url, $module);
        }
    }

    /**
     * 找不到方法
     */
    public function _empty()
    {
        return djson(1, '页面找到不到了！');
    }
}