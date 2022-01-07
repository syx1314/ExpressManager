<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2017-11-13
 * Time: 17:34
 */

namespace app\agent\controller;


/**
 
 **/
class Admin extends Base
{
    //初始化，验证是否登录，验证权限
    public function _dayuanren()
    {
        define('UID', is_login());
        if (!UID) {// 还没登录 跳转到登录页面
            $this->redirect('Login/login');
        }
        $this->user = M('customer')->where(['id' => UID])->find();

        $request = \think\Request::instance();
        define('MODULE_NAME', $request->module());
        define('ACTION_NAME', $request->action());
        define('CONTROLLER_NAME', $request->controller());

        //下级控制器初始化
        if (method_exists($this, '_init')) {
            $this->_init();
        }
    }

    /**
     
     * @return \think\response\View
     * 框架主页
     */
    public function index()
    {
        $mns = D('AgentMenu')->get_menu();
        $menu = $this->getTree($mns, 0);
        $menuhtml = $this->procHtml($menu);

        $this->assign('menuhtml', $menuhtml);
        $this->assign('user', session('user_auth_agent'));

        return view();
    }

    //菜单数组转树状图
    public function getTree($data, $pid)
    {
        $tree = [];
        foreach ($data as $k => $v) {
            if ($v['pid'] == $pid) {
                $v['child'] = $this->getTree($data, $v['id']);
                $tree[] = $v;
            }
        }
        return $tree;
    }

    //菜单转成html
    public function procHtml($tree, $lv = 0)
    {
        $clasarray = ['nav-second-level', 'nav-third-level', 'nav-four-level'];
        $html = '';
        foreach ($tree as $t) {
            $i = "";
            if ($t['pid'] == 0) {
                $i = "<i class=\"fa " . $t['icon'] . "\"></i>";
            }
            if ($t['child']) {
                $html .= " <li><a href=\"#\">" . $i . "<span class=\"nav-label\">" . $t['title'] . "</span><span class=\"fa arrow\"></span></a><ul class=\"nav " . $clasarray[$lv] . "\">";
                $html .= $this->procHtml($t['child'], $lv + 1);
                $html = $html . "</ul></li>";
            } else {
                $url = preg_match('/(http:\/\/)|(https:\/\/)/i', $t['url']) ? $t['url'] : url($t['url']);
                $html .= "<li><a class=\"J_menuItem\" href=\"" . $url . "\"> " . $i . "<span class=\"nav-label\">{$t['title']}</span></a></li>";
            }
        }
        return $html;
    }




    /**
     * 通用分页列表数据集获取方法
     *
     *  可以通过url参数传递where条件,例如:  index.html?name=asdfasdfasdfddds
     *  可以通过url空值排序字段和方式,例如: index.html?_field=id&_order=asc
     *  可以通过url参数r指定每页数据条数,例如: index.html?r=5
     *
     * @param sting|Model $model 模型名或模型实例
     * @param array $where where查询条件(优先级: $where>$_REQUEST>模型设定)
     * @param boolean $field 单表模型用不到该参数,要用在多表join时为field()方法指定参数
     * @return array|false
     * 返回数据集
     */
    protected function lists($model, $where = array(), $field = true)
    {
        return M($model)->where($where)->field($field)->paginate(C('LIST_ROWS'));
    }

    /**
     * 对数据表中的单行或多行记录执行修改 GET参数id为数字或逗号分隔的数字
     *
     * @param string $model 模型名称,供M函数使用的参数
     * @param array $data 修改的数据
     * @param array $where 查询时的where()方法的参数
     * @param array $msg 执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     */
    final protected function editRow($model, $data, $where, $msg)
    {
        $msg = array_merge(array('success' => '操作成功！', 'error' => '操作失败！', 'url' => ''), (array)$msg);
        if (M($model)->where($where)->update($data) !== false) {
            return $this->success($msg['success'], $msg['url']);
        } else {
            return $this->error($msg['error'], $msg['url']);
        }
    }

    /**
     * 禁用条目
     * @param string $model 模型名称,供D函数使用的参数
     * @param array $where 查询时的 where()方法的参数
     * @param array $msg 执行正确和错误的消息,可以设置四个元素 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     */
    protected function forbid($model, $where = array(), $msg = array('success' => '操作成功！', 'error' => '操作失败！'))
    {
        $data = array('status' => 0);
        $this->editRow($model, $data, $where, $msg);
    }

    /**
     * 恢复条目
     * @param string $model 模型名称,供D函数使用的参数
     * @param array $where 查询时的where()方法的参数
     * @param array $msg 执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     */
    protected function resume($model, $where = array(), $msg = array('success' => '操作成功！', 'error' => '操作失败！'))
    {
        $data = array('status' => 1);
        $this->editRow($model, $data, $where, $msg);
    }

    /**
     * 条目真删除
     * @param string $model 模型名称,供D函数使用的参数
     * @param array $where 查询时的where()方法的参数
     * @param array $msg 执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     */
    protected function delete($model, $where = array(), $msg = array('success' => '删除成功！', 'error' => '删除失败！'))
    {
        $ret = M($model)->where($where)->delete();
        if ($ret) {
            return $this->success($msg['success'], '');
        } else {
            return $this->error($msg['error'], '');
        }
    }

    /**
     * 条目假删除
     * @param string $model 模型名称,供D函数使用的参数
     * @param array $where 查询时的where()方法的参数
     * @param array $msg 执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     */
    protected function delete_false($model, $where = array(), $msg = array('success' => '删除成功！', 'error' => '删除失败！'))
    {
        $data = array('is_del' => 1);
        $this->editRow($model, $data, $where, $msg);
    }
}