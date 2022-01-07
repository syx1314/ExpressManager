<?php

namespace app\admin\model;

use think\Model;

/**
 * 附件目录
 * Class SystemAttachmentCategory
 * @package app\admin\model\system
 */
class SystemAttachmentCategory extends Model
{
    /**
     * 数据表主键
     * @var string
     */
    protected $pk = 'id';

    /**
     * 模型名称
     * @var string
     */
    protected $name = 'system_attachment_category';
    protected $append = ['child'];

    /**
     * 添加分类
     * @param $name
     * @param $att_size
     * @param $att_type
     * @param $att_dir
     * @param string $satt_dir
     * @param int $pid
     * @return SystemAttachmentCategory|\think\Model
     */
    public static function Add($name, $att_size, $att_type, $att_dir, $satt_dir = '', $pid = 0)
    {
        $data['name'] = $name;
        $data['att_dir'] = $att_dir;
        $data['satt_dir'] = $satt_dir;
        $data['att_size'] = $att_size;
        $data['att_type'] = $att_type;
        $data['time'] = time();
        $data['pid'] = $pid;
        return self::create($data);
    }

//    public function child()
//    {
//        return $this->hasMany('SystemAttachmentCategory', 'pid');
//    }


    public function getChildAttr($value, $data)
    {
        return SystemAttachmentCategory::all(['pid' => $data['id']]);
    }

    /**
     * 将格式数组转换为树-菜单管理
     *
     * @param array $list
     * @param integer $level 进行递归时传递用的参数
     */
    private $formatTree; //用于树型数组完成递归格式的全局变量

    private function _toFormatTree($list, $level = 0, $title = 'name')
    {
        foreach ($list as $key => $val) {
            $tmp_str = str_repeat("&nbsp;", $level * 2);
            $tmp_str .= "└";

            $val['level'] = $level;
            $val['title_show'] = $level == 0 ? $val[$title] . "&nbsp;" : $tmp_str . $val[$title] . "&nbsp;";
            if (!array_key_exists('_child', $val)) {
                array_push($this->formatTree, $val);
            } else {
                $tmp_ary = $val['_child'];
                unset($val['_child']);
                array_push($this->formatTree, $val);
                $this->_toFormatTree($tmp_ary, $level + 1, $title); //进行下一层递归
            }
        }
        return;
    }

    public function toFormatTree($list, $title = 'name', $pk = 'id', $pid = 'pid', $root = 0)
    {
        $list = list_to_tree($list, $pk, $pid, '_child', $root);
        $this->formatTree = array();
        $this->_toFormatTree($list, 0, $title);
        return $this->formatTree;
    }
}