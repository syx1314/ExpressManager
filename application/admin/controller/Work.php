<?php
/**
 * Created by PhpStorm.
 * User: 13788
 * Date: 2017/11/17
 * Time: 9:43
 */

namespace app\admin\controller;

use app\common\library\Createlog;
use app\common\library\Notification;
use app\common\library\RedisPackage;
use app\common\model\Porder as PorderModel;
use app\common\model\Product as ProductModel;
use think\console\command\make\Model;

/**
 * Class Member
 * @package app\admin\controller
 
 *
 */
class Work extends Admin
{
    //订单列表
    public function index()
    {
        $map = [];
        $expressOrderList = RedisPackage::get('expressOrderList');
        $list = [];
        if ($expressOrderList) {
            $list = json_decode($expressOrderList, true);
        }
        $this->assign('_list', $list);
        return view();
    }

    public function  add() {

    }

    // 删除任务
    public function deleteTask(){

    }
}