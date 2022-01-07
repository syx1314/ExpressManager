<?php

// 应用公共文件
if (!function_exists('dyr_encrypt')) {
    /**
     * 系统加密方法
     * @param string $data 要加密的字符串
     * @param string $key 加密密钥
     * @param int $expire 过期时间 单位 秒
     * @return string
     */
    function dyr_encrypt($data, $key = '', $expire = 0)
    {
        $key = md5(empty($key) ? config('md5_prefix') : $key);
        $data = base64_encode($data);
        $x = 0;
        $len = strlen($data);
        $l = strlen($key);
        $char = '';

        for ($i = 0; $i < $len; $i++) {
            if ($x == $l) $x = 0;
            $char .= substr($key, $x, 1);
            $x++;
        }

        $str = sprintf('%010d', $expire ? $expire + time() : 0);

        for ($i = 0; $i < $len; $i++) {
            $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
        }
        return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($str));
    }
}
if (!function_exists('dyr_decrypt')) {
    /**
     * 系统解密方法
     * @param string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
     * @param string $key 加密密钥
     * @return string
     */
    function dyr_decrypt($data, $key = '')
    {
        $key = md5(empty($key) ? C('md5_prefix') : $key);
        $data = str_replace(array('-', '_'), array('+', '/'), $data);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        $data = base64_decode($data);
        $expire = substr($data, 0, 10);
        $data = substr($data, 10);

        if ($expire > 0 && $expire < time()) {
            return '';
        }
        $x = 0;
        $len = strlen($data);
        $l = strlen($key);
        $char = $str = '';

        for ($i = 0; $i < $len; $i++) {
            if ($x == $l) $x = 0;
            $char .= substr($key, $x, 1);
            $x++;
        }

        for ($i = 0; $i < $len; $i++) {
            if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
                $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
            } else {
                $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
            }
        }
        return base64_decode($str);
    }
}
if (!function_exists('get_client_ip')) {
    /**
     * 获取客户端IP地址
     * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @return mixed
     */
    function get_client_ip($type = 0)
    {
        $type = $type ? 1 : 0;
        static $ip = NULL;
        if ($ip !== NULL) return $ip[$type];
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) unset($arr[$pos]);
            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }
}
if (!function_exists('time_format')) {
    /**
     * 时间戳格式化
     * @param int $time
     * @return string 完整的时间显示
     * @author huajie <1204887277@qq.com>
     */
    function time_format($time = NULL, $format = 'Y-m-d H:i')
    {
        if (!$time) {
            return "";
        } else {
            return date($format, intval($time));
        }
    }
}
if (!function_exists('magic_time_format')) {
    /**
     * 高级时间戳格式化
     * @param int $time
     * @return string 完整的时间显示
     * @author da <1204887277@qq.com>
     */
    function magic_time_format($time)
    {
        //获取今天凌晨的时间戳
        $day = strtotime(date('Y-m-d', time()));
        //获取昨天凌晨的时间戳
        $pday = strtotime(date('Y-m-d', strtotime('-1 day')));
        //获取前天
        $ppday = strtotime(date('Y-m-d', strtotime('-2 day')));
        //获取现在的时间戳
        $nowtime = time();

        $tc = $nowtime - $time;
        if ($time < $ppday) {
            $str = date('Y-m-d H:i', $time);
        } elseif ($time < $day && $time > $pday) {
            $str = "昨天 " . date('H:i', $time);
        } elseif ($time < $pday && $time > $ppday) {
            $str = "前天 " . date('H:i', $time);
        } elseif ($tc > 60 * 60) {
            $str = floor($tc / (60 * 60)) . "小时前";
        } elseif ($tc > 60) {
            $str = floor($tc / 60) . "分钟前";
        } else {
            $str = "刚刚";
        }
        return $str;
    }
}
if (!function_exists('format_bytes')) {
    /**
     * 格式化字节大小
     * @param number $size 字节数
     * @param string $delimiter 数字和单位分隔符
     * @return string            格式化后的带单位的大小
     */
    function format_bytes($size, $delimiter = '')
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
        return round($size, 2) . $delimiter . $units[$i];
    }
}
if (!function_exists('is_weixin_browser')) {
    /**
     * @return bool
     * 判断微信浏览器
     */
    function is_weixin_browser()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('create_qrcode')) {
    /**
     
     * @param $txt
     * @param int $size
     * @return string
     * 生成二维码
     */
    function create_qrcode($txt, $size = 4)
    {
        $data = $txt;
        $level = 'L';
        $size = $size;
        $erweima = C('DOWNLOAD_UPLOAD.filePath') . "qr/" . time() . md5($txt) . ".png";
        \Phpqrcode\QRcode::png($data, $erweima, $level, $size);
        return $erweima;
    }
}

if (!function_exists('save_web_image')) {
    /**
     * 保存网络图片
     */
    function save_web_image($imgurl)
    {
        $imgStr = file_get_contents($imgurl);
        $path = C('DOWNLOAD_UPLOAD.filePath') . "webimg/";
        if (!is_dir($path)) {
            mkdir(iconv("UTF-8", "GBK", $path), 0777, true);
        }
        $filename = md5($imgurl) . ".jpg";
        $fp = fopen($path . $filename, 'wb');
        fwrite($fp, $imgStr);
        return $path . $filename;
    }
}

if (!function_exists('filterEmoji')) {
    /**
     * @param $str
     * @return mixed
     * 过滤昵称特殊字符
     */
    function filterEmoji($str)
    {
        $str = preg_replace_callback(
            '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $str);
        return $str;
    }
}

if (!function_exists('exportToExcel')) {
    /**
     * $field_arr = array(
     * 'A' => array('title' => '字段1', 'field' => 'key1'),
     * 'B' => array('title' => '字段2', 'field' => 'key2'),
     * 'C' => array('title' => '字段3', 'field' => 'key3'),
     * 'D' => array('title' => '字段4', 'field' => 'key4'),
     * 'E' => array('title' => '字段5', 'field' => 'key5')
     * );
     * $ret = M('table')->select();
     * exportToExcel('订单报表", $field_arr, $ret);
     */
    /**
     * @creator liaoqiang
     * @desc 数据导出到excel(csv文件)
     * @param $filename 导出的csv文件名称 如date("Y年m月j日").'-test'
     * @param array $tileArray 所有列标题，对应数据字段的数据
     * @param array $dataArray 所有列数据
     */
    function exportToExcel($filename, $tileArray = [], $dataArray = [])
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0);
        ob_end_clean();
        ob_start();
        header("Content-Type: text/csv");
        header('Content-Type: application/vnd.ms-excel');
        $filename .= '.csv';
        header("Content-Disposition: attachment;filename=\"$filename\"");
        header('Cache-Control: max-age=0');
        $fp = fopen('php://output', 'w');
        fwrite($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));//转码 防止乱码(比如微信昵称(乱七八糟的))
        $title = [];
        foreach ($tileArray as $vo) {
            array_push($title, $vo['title']);
        }
        fputcsv($fp, $title);

        $index = 0;
        foreach ($dataArray as $item) {
            if ($index == 1000) {
                $index = 0;
                ob_flush();
                flush();
            }
            $index++;
            $row = [];
            foreach ($tileArray as $vo) {
                array_push($row, $item[$vo['field']]);
            }
            fputcsv($fp, $row);
        }
        ob_flush();
        flush();
        ob_end_clean();
    }
}


if (!function_exists('QCellCore')) {
    /**
     * 归属地查询
     */
    function QCellCore($mobile)
    {
        if (substr($mobile, 0, 1) == '1') {
            if ($phone = M('phone')->where(['phone' => substr($mobile, 0, 7)])->find()) {
                $data['type'] = 1;
                $data['ispstr'] = $phone['isp'];
                $data['prov'] = $phone['province'];
                $data['city'] = $phone['city'];
                $data['isp'] = ispstrtoint($phone['isp']);
                return rjson(0, 'ok', $data);
            } else {
                return rjson(1, '未找到该号码归属地');
            }
        } else {
            return rjson(1, '号码错误');
        }
    }
}

if (!function_exists('ispstrtoint')) {
    /**
     
     * @param $ispstr
     * @return int
     * 获取归运营商
     */
    function ispstrtoint($ispstr)
    {
        if ('移动' == $ispstr) {
            return 1;
        }
        if ('电信' == $ispstr) {
            return 2;
        }
        if ('联通' == $ispstr) {
            return 3;
        }
        if (strpos($ispstr, '虚拟') !== false) {
            return 4;
        }
        return 0;
    }
}


if (!function_exists('getISPText')) {

    function getISPText($ispidstr)
    {
        if (!$ispidstr) {
            return '';
        }
        $arr = explode(",", $ispidstr);
        foreach ($arr as $key => $vo) {
            $arr[$key] = C('ISP_TEXT')[$vo];
        }
        return implode(',', $arr);
    }

}


//通过id获取用户名
function get_name($id)
{
    return M('customer')->where('id=' . $id)->value('username');
}

//获取api名称
function getReapiName($id)
{
    return M('reapi')->where(['id' => $id])->value('name');
}

//获得API套餐名称
function getReapiParamName($id)
{
    return M('reapi_param')->where(['id' => $id])->value('desc');
}

//通过用户id获取等级名称
function get_user_grade_name($id)
{
    return M('customer c')->join('customer_grade g', 'g.id=c.grade_id')->where(['c.id' => $id])->value('grade_name');
}


