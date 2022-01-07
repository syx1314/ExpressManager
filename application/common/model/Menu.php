<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2017-11-14
 * Time: 11:13
 */

namespace app\common\model;

use think\Db;
use think\Model;

/**
 
 **/
class Menu extends Model
{

    protected static function init()
    {
        Menu::event('after_insert', function ($menu) {
            //自动给管理员组增加权限
            $groups = M('auth_group')->where(['module' => $menu->module, 'is_admin' => 1])->select();
            foreach ($groups as $k => $g) {
                M('auth_group')->where(['id' => $g['id']])->setField(['rules' => $g['rules'] . ',' . $menu['id']]);
            }
        });
    }

    public function getUrlAttr($value)
    {
        return strtolower($value);
    }

    public function getModuleAttr($value)
    {
        return strtolower($value);
    }

    public function setUrlAttr($value)
    {
        return strtolower($value);
    }

    public function setModuleAttr($value)
    {
        return strtolower($value);
    }

    /**
     
     * @return array
     * 获取菜单ids
     */
    public function get_menu_ids($module, $user_id)
    {
        $rules_group = Db::name('auth_group_access a')
            ->join("auth_group g", "a.group_id=g.id", 'LEFT')
            ->where(['g.module' => $module, "a.user_id" => $user_id, 'g.status' => 1])
            ->field('rules')->select();

        $ids = array();//保存用户所属用户组设置的所有权限规则id
        foreach ($rules_group as $g) {
            $ids = array_merge($ids, explode(',', trim($g['rules'], ',')));
        }
        $ids = array_unique($ids);
        return $ids;
    }


    /**
     
     * @return array
     * 获取权限列表
     */
    public function get_auth_list($module, $user_id)
    {
        $ids = $this->get_menu_ids($module, $user_id);
        $map = array(
            'id' => array('in', $ids),
            'status' => 1,
        );
        //读取用户组所有权限规则
        $rules = Db::name("menu")->where($map)->field('url')->select();
        //循环规则，判断结果。
        $authList = array();   //
        foreach ($rules as $rule) {
            //只要存在就记录
            $authList[] = strtolower($rule['url']);
        }
        S('auth_list_' . $module . '_' . $user_id, $authList, ['expire' => 60 * 10]);
        return array_unique($authList);
    }

    /**
     * 检查用户权限
     */
    public function check_rules($name, $module, $user_id)
    {
        if ($auth_list = S('auth_list_' . $module . '_' . $user_id)) {
        } else {
            $auth_list = $this->get_auth_list($module, $user_id);
        }
        $name = strtolower($name);
        if (in_array($name, $auth_list)) {
            return true;
        } else {
            return false;
        }
    }

    //自動生成菜單
    public function autoMenu($url, $module)
    {
        //查询自己有没有
        $mm = M('menu')->where(['url' => strtolower($url), 'module' => $module])->find();
        if ($mm) {
            return false;
        }
        //查询父级有没有
        $fm = M('menu')->where(['url' => ['like', strtolower(request()->controller()) . '/%'], 'module' => $module])->find();
        $this->save([
            'title' => $url,
            'module' => $module,
            'pid' => $fm ? $fm['id'] : 0,
            'sort' => 0,
            'url' => strtolower($url),
            'hide' => 0,
            'is_dev' => 0,
            'status' => 1,
            'type' => 1
        ]);
        return true;
    }
}