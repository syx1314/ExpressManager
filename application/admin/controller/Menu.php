<?php

namespace app\admin\controller;

use app\admin\model\Menu as MenuModel;

/**
 * 菜单管理
 * 邮箱：
 **/
class Menu extends Admin
{
    public function index()
    {
        $module = I('module') ? I('module') : 'admin';
        $this->assign('module', $module);

        $mns = M('menu')->where(['module' => $module])->order('sort,id')->select();
        $menu = MenuModel::getTree($mns, 0);
        $menuhtml = $this->proc_menu_Html($menu);
        $this->assign('menu', $menuhtml);
        return view();
    }

    public function proc_menu_Html($tree, $lv = 0)
    {
        $module = I('module') ? I('module') : 'admin';
        $html = '';
        foreach ($tree as $t) {
            $edit = 'Menu/add?id=' . $t['id'] . '&module=' . $module;
            $del = 'Menu/del?id=' . $t['id'];
            $addnew = 'Menu/add?pid=' . $t['id'] . '&module=' . $module;
            if ($t['child']) {
                $html .= "<li class=\"list-group-item node-treeview1\" >";
                $m_html = '';
                for ($m = 0; $m < $lv; $m++) {
                    $m_html .= " <span class=\"indent\"></span>";
                }
                $html .= $m_html;
                $html .= " <span class=\"icon\"><i class=\"click-collapse glyphicon glyphicon-plus\"></i></span>";
                if ($t['icon']) {
                    $html .= "<span  class=\"icon\"><i class=\"fa " . $t['icon'] . "\"></i></span><a  class=\"open-window no-refresh\" title='编辑' href=\"" . url($edit) . "\"> " . $t['title'] . "</a>";
                } else {
                    $html .= "<span  class=\"icon\"><i class=\"glyphicon glyphicon-stop\"></i></span><a  class=\"open-window no-refresh\" title='编辑' href=\"" . url($edit) . "\">" . $t['title'] . "</a>";
                }
                $html .= "<span  class=\"badge badge-warning\">" . $t['sort'] . "</span>";
                $html .= "<a class=\"ajax-get confirm\" href=\"" . url($del) . "\"><span style=\"float: right;margin-right: 10px;color: #b31a2f;\" onclick=\"return del()\">删除</span></a>"
                    . "<a class='open-window no-refresh' title='添加下级' href=\"" . url($addnew) . "\"><span class=\"text-navy\" style=\"float: right\">添加下级&nbsp;</span></a>"
                    . " </li>"
                    . "<ul class=\"list-group\" style='display: none;'>";
                $html .= $this->proc_menu_Html($t['child'], $lv + 1);
                $html = $html . "</ul>";
            } else {
                $html .= "<li class=\"list-group-item node-treeview1\">";
                $m_html = '';
                for ($m = 0; $m < $lv; $m++) {
                    $m_html .= " <span class=\"indent\"></span>";
                }
                $html .= $m_html;
                $html .= " <span style=\"width:20px;display:inline-block\"></span>";
                if ($t['icon']) {
                    $html .= "<span  class=\"icon\"><i class=\"fa " . $t['icon'] . "\"></i></span><a class=\"open-window no-refresh\" title='编辑' href=\"" . url($edit) . "\">" . $t['title'] . "</a>";
                } else {
                    $html .= "<span  class=\"icon\"><i class=\"glyphicon glyphicon-stop\"></i></span><a  class=\"open-window no-refresh\" title='编辑' href=\"" . url($edit) . "\">" . $t['title'] . "</a>";
                }
                $html .= "<span  class=\"badge badge-warning\">" . $t['sort'] . "</span>";
                $html .= "<a class=\"ajax-get confirm\" url=\"" . url($del) . "\"><span style=\"float: right;margin-right: 10px;color: #b31a2f;\">删除</span></a>"
                    . "<a class='open-window no-refresh' title='添加下级' href=\"" . url($addnew) . "\"><span class=\"text-navy\" style=\"float: right\">添加下级&nbsp;</span></a>"
                    . " </li>";
            }
        }
        return $html;
    }


    public function add($id = 0)
    {
        if (request()->isPost()) {
            $menu_model = new MenuModel();
            $arr = I('post.');
            $arr['url'] = strtolower($arr['url']);
            if (I('post.id')) {
                $data = $menu_model->update($arr);
                if ($data) {
                    return $this->success('更新成功', U('index'));
                } else {
                    return $this->error('更新失败');
                }
            } else {
                $data = $menu_model->save($arr);
                if ($data) {
                    return $this->success('新增成功', U('index'));
                } else {
                    return $this->error('新增失败');
                }
            }
        } else {
            /* 获取数据 */
            $module = I('module') ? I('module') : 'admin';
            $info = M('Menu')->field(true)->find($id);
            $menus = M('Menu')->field(true)->where(['module' => $module])->order('sort asc')->select();
            $menus = D('Menu')->toFormatTree($menus);
            $menus = array_merge(array(0 => array('id' => 0, 'title_show' => '顶级菜单')), $menus);
            if (false === $info) {
                return $this->error('获取后台菜单信息错误');
            }
            $this->assign('info', $info);
            $this->assign('Menus', $menus);
            $this->meta_title = '新增菜单';
        }
        return view();
    }

    /**
     * 删除后台菜单
     */
    public function del()
    {
        $id = array_unique((array)I('id', 0));
        if (empty($id)) {
            return $this->error('请选择要操作的数据!');
        }
        $map = array('id' => array('in', $id));
        if (M('Menu')->delete($map)) {
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败！');
        }
    }
}
