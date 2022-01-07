<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2017-11-14
 * Time: 14:41
 */

namespace app\admin\controller;

use think\Db;
use think\Request;

/**
 * 数据库备份
 
 **/
class Database extends Admin
{
    public function index()
    {
        $list = Db::query("SHOW TABLE STATUS");
        $list = array_map('array_change_key_case', $list);
        $this->assign('list', $list);
        return view();
    }

    /**
     * 修复表
     * @param  String $tables 表名
     */
    public function repair($tables = null)
    {
        if ($tables) {
            if (is_array($tables)) {
                $tables = implode('`,`', $tables);
                $list = Db::query("REPAIR TABLE `{$tables}`");

                if ($list) {
                  return $this->success("数据表修复完成！");
                } else {
                  return $this->error("数据表修复出错请重试！");
                }
            } else {
                $list = Db::query("REPAIR TABLE `{$tables}`");
                if ($list) {
                  return $this->success("数据表'{$tables}'修复完成！");
                } else {
                  return $this->error("数据表'{$tables}'修复出错请重试！");
                }
            }
        } else {
          return $this->error("请指定要修复的表！");
        }
    }

    /**
     * 优化表
     * @param  String $tables 表名
     */
    public function optimize()
    {
        $tables = I('tables/a');
        if ($tables) {
            if (is_array($tables)) {
                $tables = implode('`,`', $tables);
                $list = Db::query("OPTIMIZE TABLE `{$tables}`");

                if ($list) {
                  return $this->success("数据表优化完成！");
                } else {
                  return $this->error("数据表优化出错请重试！");
                }
            } else {
                $list = Db::query("OPTIMIZE TABLE `{$tables}`");
                if ($list) {
                  return $this->success("数据表'{$tables}'优化完成！");
                } else {
                  return $this->error("数据表'{$tables}'优化出错请重试！");
                }
            }
        } else {
          return $this->error("请指定要优化的表！");
        }
    }

    /**
     * 备份数据库
     * @param  String $tables 表名
     * @param  Integer $id 表ID
     * @param  Integer $start 起始行数
     */
    public function export($id = null, $start = null)
    {
        $tables = I('post.tables/a');
        if (Request::instance()->isPost() && !empty($tables) && is_array($tables)) { //初始化
            //读取备份配置
            $config = C('data_backup');
            $config['path'] = dirname(ROOT_PATH) . $config['path'];
            //检查是否有正在执行的任务
            $lock = "{$config['path']}backup.lock";
            if (is_file($lock)) {
              return $this->error('检测到有一个备份任务正在执行，请稍后再试！');
            } else {
                //创建锁文件
                file_put_contents($lock, time());
            }
            //检查备份目录是否可写
            is_writeable($config['path']) || $this->error('备份目录不存在或不可写，请检查后重试！');
            session('backup_config', $config);

            //生成备份文件信息
            $file = array(
                'name' => date('Ymd-His', time()),
                'part' => 1,
            );
            session('backup_file', $file);

            //创建备份文件
            $Database = new \Util\Database($file, $config);
            if (false !== $Database->create()) {
                foreach ($tables as $key => $table) {
                    $start = $Database->backup($table, 0);
                    if (false === $start) { //出错
                      return $this->error('备份出错！');
                    }
                }
                unlink($lock);
              return $this->success('备份完成！');
            } else {
              return $this->error('初始化失败，备份文件创建失败！');
            }
        } else { //出错
          return $this->error('参数错误！');
        }
    }
}