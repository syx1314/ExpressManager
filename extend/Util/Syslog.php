<?php

// +----------------------------------------------------------------------
// | ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2014~2017 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: http://think.ctolog.com
// +----------------------------------------------------------------------
// | 开源协议 ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zoujingli/ThinkAdmin
// +----------------------------------------------------------------------

namespace Util;

use think\Db;
use think\db\Query;

/**
 * 操作日志服务
 * Class Syslog
 */
class Syslog
{

    /**
     * 写入操作日志
     * @param string $action
     * @param string $content
     * @return bool
     */
    public static function write($action = '行为', $content = "内容描述", $username = "无")
    {

        $request = request();
        $data = [
            'ip' => $request->ip(),
            'url' => $request->url(),
            'action' => $action,
            'content' => is_array($content) ? json_encode($content) : $content,
            'username' => $username,
            'create_time' => time()
        ];
        return M('system_log')->insert($data) !== false;
    }

}
