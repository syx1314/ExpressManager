<?php
// +----------------------------------------------------------------------
// | DThink [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------

namespace app\common\library;
/**
 * Class Configapi
 * @package app\common\library
 * 系统配置参数获取
 */
class Configapi
{


    public static function getconfig()
    {
        $config = S('DB_CONFIG_DATA');
        if (!$config) {
            $config = self::lists();
            S('DB_CONFIG_DATA', $config);
        }
        return $config;
    }

    /**
     * 获取数据库中的配置列表
     * @return array 配置数组
     */
    private static function lists()
    {
        $map = [];
        $data = M('Config')->where($map)->field('type,name,value')->select();
        $config = array();
        if ($data && is_array($data)) {
            foreach ($data as $value) {
                $config[$value['name']] = self::parse($value['type'], $value['value']);
            }
        }
        return $config;
    }

    /**
     * 根据配置类型解析配置
     * @param integer $type 配置类型
     * @param string $value 配置值
     */
    private static function parse($type, $value)
    {
        switch ($type) {
            case 3: //解析数组
                $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
                if (strpos($value, ':')) {
                    $value = array();
                    foreach ($array as $val) {
                        $k = substr($val, 0, strpos($val, ':'));
                        $v = substr($val, strpos($val, ':') + 1);
//                        list($k, $v) = explode(':', $val);
                        $value[$k] = $v;
                    }
                } else {
                    $value = $array;
                }
                break;
        }
        return $value;
    }

    /**
     
     * 清楚配置缓存
     */
    public static function clear()
    {
        S('DB_CONFIG_DATA', null);
    }
}