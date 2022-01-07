<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2017-11-14
 * Time: 11:13
 */

namespace app\admin\model;


/**
 
 **/
class AuthGroup extends \app\common\model\AuthGroup
{
    public function get_auth($module)
    {
        $map['module'] = $module;
        $list = $this->where($map)->select();
        foreach ($list as $key => $vo) {
            if ($module == 'admin') {
                $list[$key]['mem_count'] = M('auth_group_access a')->join("member m", "m.id=a.user_id")->where(['a.group_id' => $vo['id'], 'm.is_del' => 0])->count();
            } else {
                $list[$key]['mem_count'] = M('auth_group_access a')->join("customer m", "m.id=a.user_id")->where(['a.group_id' => $vo['id'], 'm.is_del' => 0])->count();
            }
        }
        return $list;
    }

    public function get_user($group_id, $module)
    {
        $map['a.group_id'] = $group_id;
        $map['m.is_del'] = 0;
        if ($module == 'admin') {
            $list = M('auth_group_access a')->join("member m", "m.id=a.user_id")->where($map)->field("a.id,a.user_id,a.group_id,m.nickname as nickname,m.headimg,m.last_login_time,m.last_login_ip")->paginate(30);
        } else {
            $list = M('auth_group_access a')->join("customer m", "m.id=a.user_id")->where($map)->field("a.id,a.user_id,a.group_id,m.username as nickname,m.headimg,m.last_login_time,m.last_login_ip")->paginate(30);
        }
        return $list;
    }

    public function get_group($module)
    {
        $list = M('auth_group')->where(['module' => $module])->select();
        return $list;
    }


}