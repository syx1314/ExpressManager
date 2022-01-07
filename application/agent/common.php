<?php
/**
 
 * 公司：
 **/
/**
 * User:廖强
 * Date: 2017-11-13
 * Time: 10:47
 */

function is_login()
{
    $user = session('user_auth_agent');
    if (empty($user)) {
        return 0;
    }
    $redis= new \app\common\library\RedisPackage();
    $tk=$redis::get('agentToken-'.$user['id']);
    if (data_auth_sign($user)!=$tk) {
        return 0;
    }
    //限制单点登陆
    $last_login_ip = M('customer')->where(['id' => $user['id']])->value('last_login_ip');
    if ($last_login_ip != get_client_ip()) {
        return 0;
    }
    return $user['id'];
}

/**
 * 数据签名认证
 * @param array $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data)
{
    //数据类型检测
    if (!is_array($data)) {
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

//数组转成树形无限级分类1
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
{
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}


// 分析枚举类型配置值 格式 a:名称1,b:名称2
function parse_config_attr($string)
{
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if (strpos($string, ':')) {
        $value = array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k] = $v;
        }
    } else {
        $value = $array;
    }
    return $value;
}


function return_material_img($text)
{
    $url = str_replace(".html", "", U('Weixin/get_path_by_url'));
    return str_replace("data-src=\"", "src=\"" . $url . "?url=", $text);
}

