<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2017-11-13
 * Time: 17:34
 */

namespace app\admin\controller;
use app\common\library\RedisPackage;
use app\admin\model\MemberLog;
use app\admin\model\Menu;

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
        $this->adminuser = M('member')->where(['id' => UID])->find();
        define('IS_ROOT', is_administrator());
        $request = \think\Request::instance();
        define('MODULE_NAME', $request->module());
        define('ACTION_NAME', $request->action());
        define('CONTROLLER_NAME', $request->controller());

        if (!IS_ROOT && C('ADMIN_ALLOW_IP')) {
            // 检查IP地址访问
            if (!in_array(get_client_ip(), explode(',', C('ADMIN_ALLOW_IP')))) {
                return $this->error('403:禁止访问');
            }
        }
        //管理员无需授权
        if (!IS_ROOT) {
            $rule = strtolower(CONTROLLER_NAME . '/' . ACTION_NAME);
            $menu = new Menu();
            if (!$menu->check_rules($rule, $request->module(), UID)) {
                return $this->error("未授权访问", url('Index/index'));
            }
        }
        //写日志
        $this->memLog();
        //下级控制器初始化
        if (method_exists($this, '_init')) {
            $this->_init();
        }
    }


    //记录访问、操作日志
    private function memLog()
    {
        $url = strtolower(request()->controller() . '/' . request()->action());
        MemberLog::addLog($this->adminuser['id'], $this->adminuser['nickname'], $url);
    }


    /**
     
     * @return \think\response\View
     * 框架主页
     */
    public function index()
    {
        $menu = new Menu();
        $mns = $menu->get_menu($menu->get_menu_ids(request()->module(), UID));
        $menu = Menu::getTree($mns, 0);
        $menuhtml = Menu::procHtml($menu);
        $this->assign('menuhtml', $menuhtml);
        $this->assign('user', session('user_auth'));
        return view();
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