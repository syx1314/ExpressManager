<?php
/**
 * Created by PhpStorm.
 * User: 12048
 * Date: 2017-11-14
 * Time: 11:13
 */

namespace app\admin\model;

use think\Model;

/**
 
 **/
class MemberLog extends Model
{
    //记录访问、操作日志
    public static function addLog($member_id, $name, $url)
    {
        $mm = M('menu')->where(['url' => $url])->find();
        $title = self::getUrlSource($mm['id']);
        M('member_log')->insertGetId([
            'member_id' => $member_id,
            'name' => $name,
            'title' => $title,
            'url' => self::handleGetCurrentUrlAdmin(),
            'create_time' => time(),
            'ip' => get_client_ip(),
            'param' => json_encode(request()->param()),
            'method' => $_SERVER['REQUEST_METHOD']
        ]);
    }

    //获取
    private static function getUrlSource($id)
    {
        $mm = M('menu')->where(['id' => $id])->find();
        $title = '';
        if ($mm['pid']) {
            $title .= self::getUrlSource($mm['pid']) . '>';
        }
        $title .= $mm['title'];
        return $title;
    }

    private static function handleGetCurrentUrlAdmin()
    {
        //获取当前完整url,为了清晰，多定义几个变量,分几行写
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        $domain = $_SERVER['HTTP_HOST']; //域名/主机
        $requestUri = $_SERVER['REQUEST_URI']; //请求参数
        //将得到的各项拼接起来
        $currentUrl = $http_type . $domain . $requestUri;
        return $currentUrl; //传回当前url
    }

}