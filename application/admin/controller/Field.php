<?php

namespace app\admin\controller;

use think\Db;

/**
 
 */
class Field extends Admin
{
//    数据库列表
    public function index()
    {

        $list = Db::query("SHOW TABLE STATUS");
        $this->assign('list', $list);
        return view();
    }

//增加表
    public function add_table()
    {
        if (request()->isPost()) {
            $info = I('info/a', []);
            $column = I('column/a', []);
            if (!$info['tabename']) {
                return djson(1, '请填写表名！');
            }
            if (Db::query("SHOW TABLES LIKE '" . $info['tabename'] . "'")) {
                return djson(1, '已经存在' . $info['tabename'] . '表了');
            }
            $sql = "CREATE TABLE IF NOT EXISTS `" . $info['tabename'] . "`(";
            foreach ($column as $key => $vo) {
                if (!$vo['name']) {
                    return djson(1, '请填写' . ($key + 1) . '列名！');
                }
                if (!$vo['data_type']) {
                    return djson(1, '请选择' . ($key + 1) . '列数据类型！');
                }
                $auto_increment = "";
                $unsigned = "";
                $sql .= "`" . $vo['name'] . "` " . $vo['data_type'];
                switch ($vo['data_type']) {
                    case 'int':
                        $vo['length'] = $vo['length'] == 0 ? 11 : $vo['length'];
                    case 'tinyint':
                        $vo['length'] = $vo['length'] == 0 ? 11 : $vo['length'];
                    case 'smallint':
                        $vo['length'] = $vo['length'] == 0 ? 6 : $vo['length'];
                    case 'bigint':
                        $vo['length'] = $vo['length'] == 0 ? 20 : $vo['length'];
                        $auto_increment = $vo['auto_increment'] == 'true' ? 'AUTO_INCREMENT' : ''; //自动递增
                        $unsigned = $vo['unsigned'] == 'true' ? 'UNSIGNED' : '';  //无符号
                    case 'varchar':
                        $vo['length'] = $vo['length'] == 0 ? 255 : $vo['length'];
                    case 'char':
                        $vo['length'] = $vo['length'] == 0 ? 255 : $vo['length'];
                        $sql .= "(" . $vo['length'] . ")";
                        //数据长度只需要整数长度
                        break;
                    case 'decimal':
                        $vo['length'] = $vo['length'] == 0 ? 10 : $vo['length'];
                    case 'float':
                    case 'double':
                        if ($vo['length']) {
                            $sql .= "(" . $vo['length'] . "," . $vo['point'] . ")";
                        }
                        //数据整数长度、小数
                        break;
                    case 'text':
                    case 'timestamp':
                    case 'datetime':
                        //不需要长度、小数
                        break;
                    default:
                        //默认不需要长度、小数
                }
                $is_null = $vo['is_null'] == 'true' ? '' : 'NOT NULL';
                $default = $vo['default'] != '' ? "DEFAULT '" . $vo['default'] . "'" : '';
                $comment = $vo['comment'] != '' ? "COMMENT '" . $vo['comment'] . "'" : '';
                $sql .= " " . $unsigned . " " . $is_null . " " . $default . " " . $comment . " " . $auto_increment;
                if ($vo['is_key'] == 'true') {
                    $sql .= ", PRIMARY KEY ( `" . $vo['name'] . "` )";
                }
                if ($key < count($column) - 1) {
                    $sql .= " ,";
                }
            }
            $sql .= ")ENGINE=" . $info['engine'] . " DEFAULT CHARSET=" . $info['collation'] . " COMMENT='" . $info['comment'] . "'";
            try {
                Db::query($sql);
                return djson(0, '创建成功！');
            } catch (\Exception $e) {
                return djson(1, '创建失败,' . $e->getMessage());
            }
        } else {
            return view();
        }
    }

    //表
    public function table_data($name)
    {
        $column = (Db::query("select  column_name, column_comment  from Information_schema.columns  where table_Name = '" . $name . "'"));
        $list = M()->table($name)->paginate(C('LIST_ROWS'));
        $this->assign('column', $column);
        $this->assign('_list', $list);
        return view();
    }
}
